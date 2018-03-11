<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', function() {
        return redirect(route('admin.product.index'));
    });
    $router->get('/', 'HomeController@index')->name('admin.index');
    $router->resource('users', 'UserController');
    //产品中心
    $router->resource('product', 'ProductController')->name('index','admin.product.index');
    //产品详情
    $router->resource('product-info', 'ProductInfoController');

    $router->resource('category', 'CategoryController');
    //页脚分类
    $router->resource('foot-category', 'FootCategoryController');
    //页脚
    $router->resource('foot', 'FootController');

    $router->resource('about', 'AboutController');
    $router->resource('carousel', 'CarouselController');

    //新品推荐
    $router->resource('new-position', 'NewPositionController');
    $router->resource('file-manage', 'FileManageController');

    //留言管理
    $router->resource('message', 'MessageController');

    // instruction management
    $router->resource('instruction', 'InstructionController');

});
Route::post('upload', 'App\Admin\Controllers\AboutController@upload');
Route::get('qiniu-token', 'App\Admin\Controllers\QiniuController@token');
