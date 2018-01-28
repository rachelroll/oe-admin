<?php

namespace App\Admin\Controllers;

use App\Models\Foot;

use App\Models\FootCategory;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class FootController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('页脚分类');
            $content->description('注意,这里页脚分类最多只能增加4个分类');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Foot::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->filter(function($filter){
                // Remove the default id filter

                // Add a column filter
                $filter->like('name', '名称');
                $filter->disableIdFilter();
                $catOptions = FootCategory::where('enabled',1)->pluck('name','id');
                $filter->equal('cat_id','Foot分类')->select($catOptions);
            });
            $grid->column('name', 'Foot名称')->editable();
            $grid->column('sort', '排序')->sortable()->editable();
            $options = [
                'on'  => ['value' => 1, 'text' => '启用', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '禁用', 'color' => 'default'],
            ];
            $grid->column('enabled','状态')->switch($options)->sortable();


            $grid->created_at();
            $grid->updated_at();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Foot::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->select('cat_id', 'Foot分类')->options(function($id) {
                $categories = FootCategory::where('enabled',1)->get(['name','id']);
                $arr = [];
                $categories->each(function ($item,$key) use (&$arr) {
                    $arr[$item->id] = $item->name;
                });
                return $arr;
            })->rules('required', [
                'required' => '分类必选',
            ]);
            $form->text('name', 'Foot名称');
            $form->text('sort', '排序');


            $options = [
                'on'  => ['value' => 1, 'text' => '启用', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '禁用', 'color' => 'default'],
            ];

            $form->switch('enabled', '状态(禁用后产品不显示)')->states($options);
            $form->editor('info','详情');


            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
