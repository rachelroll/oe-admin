<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.index');
    $router->resource('users', 'UserController');
    //产品中心
    $router->resource('product', 'ProductController');
    //产品详情
    $router->resource('product-info', 'ProductInfoController');
    $router->resource('category', 'CategoryController');
    $router->resource('about', 'AboutController');

});
Route::post('upload', 'App\Admin\Controllers\AboutController@upload');
Route::get('qiniu-token', 'App\Admin\Controllers\QiniuController@token');
