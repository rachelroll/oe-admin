<?php

namespace App\Admin\Controllers;

use App\Models\Carousel;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class CarouselController extends Controller
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
        return Admin::grid(Carousel::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->column('sort', '排序(越小越前)')->editable();
            $grid->column('title', '轮播图名称')->editable();
            $grid->column('img', '图片')->image('',60,60);
            $grid->column('intro', '简单描述')->editable();
            $grid->column('url', '跳转地址')->editable();
            $grid->column('target', '打开方式')->display(function($target) {
                switch ($target) {
                    case 0:
                        return '新窗口打开';
                    case 1:
                        return '当前窗口';
                    default:
                        return '其他方式';
                }
            });;
            $grid->column('remark', '备注')->editable();
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
        return Admin::form(Carousel::class, function (Form $form) {

            $form->display('id', 'ID');


            $form->text('title','轮播图名称');

            $options = [
                'on'  => ['value' => 1, 'text' => '启用', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '禁用', 'color' => 'default'],
            ];
            $form->switch('enabled', '状态(禁用后产品不显示)')->states($options);

            $form->text('intro','简单描述');
            $form->text('url','跳转地址(写产品ID)');
            $form->number('sort', '排序');
            $options = [
                '0'  => '新窗口打开',
                '1'  => '当前页打开',
            ];
            $form->select('target','打开方式')->options($options);

            $form->image('img','轮播图片');

            $form->text('remark','备注');


            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
