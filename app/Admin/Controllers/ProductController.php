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
use Illuminate\Support\Facades\Log;

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
                $filter->disableIdFilter();
                $catOptions = Category::where('enabled',1)->pluck('name','id');
                $filter->equal('cat_id','产品分类')->select($catOptions);
                $isNewOptions = [
                    0=>'否',
                    1=>'是',
                ];
                $filter->equal('is_new','是否新品')->select($isNewOptions);
            });




            $grid->id('ID')->sortable();

            $grid->column('name', '产品名称')->editable();
            $grid->column('model', '型号')->editable();
            $grid->column('sort', '排序')->sortable()->editable();

            $options = [
                'on'  => ['value' => 1, 'text' => '是', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '否', 'color' => 'default'],
            ];
            $grid->column('is_new','是否新品速递')->switch($options)->sortable();

            $grid->column('category.name', '分类名称')->sortable();

            $grid->column('rating', '评级')->display(function($text) {
                switch ($text) {
                    case 0:
                        return '暂无评级';
                    case 1:
                        return '★';
                    case 2:
                        return '★★';
                    case 3:
                        return '★★★';
                    case 4:
                        return '★★★★';
                    case 5:
                        return '★★★★★';
                    default:
                        return '暂无评级';
                }
            })->sortable();

            $options = [
                'on'  => ['value' => 1, 'text' => '启用', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '禁用', 'color' => 'default'],
            ];
            $grid->column('enabled','状态')->switch($options)->sortable();

            $grid->column('cover','封面')->image('',60,60);
            $grid->column('banner','产品Banner图')->image('',60,60);
            $grid->column('video_img','视频封面图')->image('',60,60);

            //$grid->column('intro_title','一句话简介')->display(function($intro_title) {
            //    return str_limit($intro_title,20);
            //});
            $grid->column('price','价格(元)')->sortable();



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

            $form->text('name', '产品名称');
            $form->text('model', '型号');
            $options = [
                'on'  => ['value' => 1, 'text' => '是', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '否', 'color' => 'default'],
            ];
            $form->switch('is_new', '是否新品速递')->states($options);
            $form->text('sort', '排序');
            $form->text('buy_url', '购买链接');
            $form->text('tm_url', '天猫链接');
            $options = [
                '0'  => '',
                '1'  => '★',
                '2' => '★★',
                '3' => '★★★',
                '4' => '★★★★',
                '5' => '★★★★★',
            ];
            $form->select('rating', '评级')->options($options);

            $form->select('cat_id', '产品分类')->options(function($id) {
                $categories = Category::where('enabled',1)->get(['name','id']);
                $arr = [];
                $categories->each(function ($item,$key) use (&$arr) {
                    $arr[$item->id] = $item->name;
                });
                return $arr;
            })->rules('required', [
                'required' => '分类必选',
            ]);

            $options = [
                '0'  => '随意',
                '1'  => '位置1',
                '2'  => '位置2',
            ];
            $form->select('position', '产品在首页位置')->options($options);

            $options = [
                'on'  => ['value' => 1, 'text' => '启用', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '禁用', 'color' => 'default'],
            ];
            $form->switch('enabled', '状态(禁用后产品不显示)')->states($options);

            $form->image('cover','封面图')->removable();
            $form->image('banner','Banner图')->removable();

            $form->image('video_img','视频封面图');
            $form->file('video_mp4','视频mp4');
            $form->file('video_ogv','视频ogv');
            $form->file('video_webm','视频webm');

            $form->text('intro_title', '封面图简介(必填)')->rules('required', [
                'required' => '封面图简介必填',
            ]);
            $form->editor('intro','产品详情');
            $form->editor('intro_en','Product Detail');
            //$form->textarea('attr', '产品属性(每行一个属性)');



            $form->number('price', '价格(元)');

            //$form->hasMany('product_info', function (Form\NestedForm $form) {
            //    $form->image('imgs','产品大图');
            //    $form->text('intro','每一张图片的产品描述');
            //});

            $form->saving(function (Form $form) {
                $form->buy_url = $form->buy_url?:'';
                $form->tm_url = $form->tm_url?:'';
                $form->sort = $form->sort?: 0;
            });


            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
