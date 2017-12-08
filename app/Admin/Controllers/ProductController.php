<?php

namespace App\Admin\Controllers;

use App\Models\Category;
use App\Models\Product;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ProductController extends Controller
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
        return Admin::grid(Product::class, function (Grid $grid) {

            $grid->filter(function($filter){
                // Remove the default id filter

                // Add a column filter
                $filter->like('name', '名称');
                $filter->equal('type', '分类')->select([
                    0 => '普通产品',
                    1 => '主打产品',
                    2 => '特色产品'
                ]);
            });


            $grid->id('ID')->sortable();

            $grid->column('name', '产品名称');
            $grid->column('cat_id', '分类名称')->display(function($text) {

                switch ($text) {
                    case 0:
                        return '无分类';
                    case 1:
                        return '音响';
                    case 2:
                        return '耳机';
                    default:
                        return '无分类';
                }
            })->sortable();

            $grid->column('type', '首页位置')->display(function($text) {
                switch ($text) {
                    case 0:
                        return '普通产品';
                    case 1:
                        return '主打产品';
                    case 2:
                        return '特色产品';
                    default:
                        return '普通产品';
                }
            })->sortable();

            $options = [
                'on'  => ['value' => 1, 'text' => '启用', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '禁用', 'color' => 'default'],
            ];
            $grid->column('enabled','状态')->switch($options);

            $grid->column('cover','封面')->image('',60,60);
            
            $grid->column('intro_title','一句话简介')->display(function($intro_title) {
                return str_limit($intro_title,20);
            });
            $grid->column('intro','产品简介')->display(function($intro) {
                return str_limit($intro,20);
            });
            $grid->column('price','价格(元)')->sortable();


            //$grid->column('name', '产品名称');
            //$grid->column('name', '产品名称');
            //$grid->column('name', '产品名称');

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
        return Admin::form(Product::class, function ( Form $form) {
            $form->display('id', 'ID');
            // 添加text类型的input框
            $form->text('name', '产品名称');

            $form->select('cat_id', '产品分类')->options(function($id) {
                $categories = Category::where('enabled',1)->get(['name','id']);
                $arr = [];
                $categories->each(function ($item,$key) use (&$arr) {
                    $arr[$item->id] = $item->name;
                });
                return $arr;

            });

            $options = [
                '0'  => '普通产品',
                '1'  => '主打产品',
                '2' => '特色产品',
            ];
            $form->select('type', '产品在首页位置')->options($options);

            $options = [
                'on'  => ['value' => 1, 'text' => '启用', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '禁用', 'color' => 'default'],
            ];
            $form->switch('enabled', '状态(禁用后产品不显示)')->states($options);

            $form->image('cover','封面图');
            $form->text('intro_title', '封面图简介');
            $form->editor('intro','产品简介');
            $form->textarea('attr', '产品属性(每行一个属性)');



            $form->number('price', '价格(元)');

            $form->hasMany('product_info', function (Form\NestedForm $form) {
                $form->image('imgs','产品大图');
                $form->text('intro','每一张图片的产品描述');
            });

            $form->saving(function (Form $form) {
            });

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
