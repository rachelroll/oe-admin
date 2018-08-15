<?php

namespace App\Admin\Controllers;

use App\Models\About;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AboutController extends Controller
{
    use ModelForm;
    private $header = '关于';

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header($this->header);
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

            $content->header($this->header);
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
            $content->header($this->header);
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
        return Admin::grid(About::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->column('title', '导航名称');
            $grid->column('title_en', 'Name_en');

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
        return Admin::form(About::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->text('title', '导航名称');
            $form->text('title_en', 'English Name');

            $form->editor('content', '具体内容');
            $form->editor('content_en', 'Content');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    public function upload(Request $request)
    {
        if ($request->hasFile('wang-editor-file') && $request->file('wang-editor-file')->isValid() ) {
            $storage = Storage::disk('local');
            $url = $storage->url($storage->put('public',$request->file('wang-editor-file')));
            return response()->json(['url'=>$url]);
        }

    }
}
