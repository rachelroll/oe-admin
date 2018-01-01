<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    //$router->get('/', 'HomeController@index')->name('admin.index');
    $router->get('/', function() {
        return redirect(route('admin.product.index'));
    });
    $router->resource('users', 'UserController');
    //产品中心
    $router->resource('product', 'ProductController')->name('index','admin.product.index');
    //产品详情
    $router->resource('product-info', 'ProductInfoController');
    $router->resource('category', 'CategoryController');
    $router->resource('about', 'AboutController');
    $router->resource('carousel', 'CarouselController');

});
Route::post('upload', 'App\Admin\Controllers\AboutController@upload');
Route::get('qiniu-token', 'App\Admin\Controllers\QiniuController@token');
