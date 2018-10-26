<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::domain("shop.ele.com")->namespace("Shop")->group(function (){

    //商户注册
    Route::any("user/reg","RegController@reg")->name("shop.user.reg");
    //商户登录
    Route::any("user/login","RegController@login")->name("shop.user.login");
    //后台首页
    Route::any("user/index","RegController@index")->name("shop.user.index");
    //商家店铺信息
    Route::get("information/index","ShopInformationController@index")->name("shop.information.index");
    Route::any("information/add","ShopInformationController@add")->name("shop.information.add");
    Route::any("information/check/{id}","ShopInformationController@check")->name("shop.information.check");
    Route::any("information/edit{id}","ShopInformationController@edit")->name("shop.information.edit");
    Route::get("information/del{id}","ShopInformationController@del")->name("shop.information.del");

    //商家基本信息的管理
    Route::get("user/home","UserController@home")->name("shop.user.home");
    Route::any("user/add","UserController@add")->name("shop.user.add");
    Route::any("user/edit{id}","UserController@edit")->name("shop.user.edit");
    Route::get("user/del{id}","UserController@del")->name("shop.user.del");

});


Route::domain("admin.ele.com")->namespace("Admin")->group(function (){

    //管理员登录
    Route::any("admin/login","AdminController@login")->name("admin.admin.login");

    //管理员注销登录
    Route::any("admin/logout","AdminController@logout")->name("admin.admin.logout");

    //更改密码
    Route::any("admin/changepwd","AdminController@changepwd")->name("admin.admin.changepwd");

    //商家店铺信息
//    Route::get("information/index","ShopInformationController@index")->name("admin.information.index");
//    Route::any("information/add","ShopInformationController@add")->name("admin.information.add");
//    Route::any("information/check","ShopInformationController@check")->name("admin.information.check");
//    Route::any("information/edit{id}","ShopInformationController@edit")->name("admin.information.edit");
//    Route::get("information/del{id}","ShopInformationController@del")->name("admin.information.del");


    //商家店铺的分类
    Route::get("category/index","ShopCategoryController@index")->name("admin.category.index");
    Route::any("category/add","ShopCategoryController@add")->name("admin.category.add");
    Route::any("category/edit{id}","ShopCategoryController@edit")->name("admin.category.edit");
    Route::get("category/del{id}","ShopCategoryController@del")->name("admin.category.del");

    //管理员信息的管理
    Route::get("admin/index","AdminController@index")->name("admin.admin.index");
    Route::any("admin/add","AdminController@add")->name("admin.admin.add");
    Route::any("admin/edit{id}","AdminController@edit")->name("admin.admin.edit");
    Route::get("admin/del{id}","AdminController@del")->name("admin.admin.del");

});




