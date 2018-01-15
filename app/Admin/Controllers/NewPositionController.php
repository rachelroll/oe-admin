<?php

namespace App\Admin\Controllers;

use App\Models\NewPosition;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class NewPositionController extends Controller
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
        return Admin::grid(NewPosition::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->column('layout', '新品速递布局')->editable();

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
        return Admin::form(NewPosition::class, function (Form $form) {

            $form->display('id', 'ID');

            $options = [
                'on'  => ['value' => 1, 'text' => '启用', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '禁用', 'color' => 'default'],
            ];
            $form->switch('enabled', '状态')->states($options);

            $form->text('layout', '新品速递布局: 例如 要显示3行,第一行2个图片,第二个3个图片,第三行4个图片,那么 输入"2,3,4"即可, 引号不输入,如果创建多个新品速递位置,只有第一个有用');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
