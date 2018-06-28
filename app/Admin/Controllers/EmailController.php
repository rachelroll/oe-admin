<?php

namespace App\Admin\Controllers;

use App\Models\Email;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class EmailController extends Controller
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

            $content->header('邮件管理');
            $content->description('收到的邮件');

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

            $content->header('新增收件人');
            $content->description('新增收件人');

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
        return Admin::grid(Email::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->column('receiver', '收件人');
            $grid->column('email', '邮箱');
            $options = [
                'on'  => ['value' => 1, 'text' => '启用', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '禁用', 'color' => 'default'],
            ];
            $grid->column('enabled','状态')->switch($options)->sortable();
            $grid->created_at('创建时间');
            $grid->updated_at('修改时间');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Email::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->text('receiver', '收件人')->rules('required|between:2,15');
            $form->text('email', '邮箱')->rules('required|email_address');
            $options = [
                'on'  => ['value' => 1, 'text' => '启用', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '禁用', 'color' => 'default'],
            ];
            $form->switch('enabled', '状态(禁用后产品不显示)')->states($options);
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
