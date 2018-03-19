<?php

namespace App\Admin\Controllers;

use App\Models\FileManage;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class FileManageController extends Controller
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
        return Admin::grid(FileManage::class, function (Grid $grid) {

            $grid->filter(function($filter){
                // Remove the default id filter

                // Add a column filter
                $filter->like('name', '名称');
                $filter->disableIdFilter();
            });

            $grid->id('ID')->sortable();
            $grid->column('name', '文件名称')->sortable();
            $grid->column('url', '文件地址')->display(function($url) {
                if ($url) {
                    return 'http://' . config('admin.qiniu.qiniu_host') . $url;
                }
                return '';
            });

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
        return Admin::form(FileManage::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('name', '文件名称');
            $form->file('url','文件')->removable();
            $form->display('url','文件地址')->with(function($url){
                if ($url) {
                    return  'http://' . config('admin.qiniu.qiniu_host') . $url;
                }
                return '';
            });
            $form->text('comment', '文件备注');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
