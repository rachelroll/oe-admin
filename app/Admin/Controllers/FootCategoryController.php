<?php

namespace App\Admin\Controllers;

use App\Models\FootCategory;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class FootCategoryController extends Controller
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

            $content->header('header');
            $content->description('description');

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
            if (count(FootCategory::get()) >= 3) {
                $body = '<h1>最多只能添加 三个分类</h1>';
            } else {
                $body = $this->form();
            }
            $content->body($body);
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(FootCategory::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->column('name', '分类名称')->editable();
            $grid->column('sort', '排序')->editable()->sortable();


            $options = [
                'on'  => ['value' => 1, 'text' => '启用', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '禁用', 'color' => 'default'],
            ];
            $grid->column('enabled','状态')->switch($options);


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
        return Admin::form(FootCategory::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->text('name', '分类名称');

            $form->text('sort','排序');

            $options = [
                'on'  => ['value' => 1, 'text' => '启用', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '禁用', 'color' => 'default'],
            ];
            $form->switch('enabled', '状态(启用禁用)')->states($options);


            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
