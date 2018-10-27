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
    //商户注销
    Route::any("user/logout","RegController@logout")->name("shop.user.logout");
    //商户更改密码
    Route::any("user/change_pwd","RegController@change_pwd")->name("shop.user.change_pwd");
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


    //菜品分类
    Route::get("menu_category/index","MenuCategoryController@index")->name("shop.menu_category.index");
    Route::any("menu_category/add","MenuCategoryController@add")->name("shop.menu_category.add");
    Route::any("menu_category/edit{id}","MenuCategoryController@edit")->name("shop.menu_category.edit");
    Route::get("menu_category/del{id}","MenuCategoryController@del")->name("shop.menu_category.del");
    Route::get("menu_category/check{id}","MenuCategoryController@check")->name("shop.menu_category.check");


    //菜品
    Route::get("menu/index","MenuController@index")->name("shop.menu.index");
    Route::any("menu/add","MenuController@add")->name("shop.menu.add");
    Route::any("menu/edit{id}","MenuController@edit")->name("shop.menu.edit");
    Route::get("menu/del{id}","MenuController@del")->name("shop.menu.del");

});


Route::domain("admin.ele.com")->namespace("Admin")->group(function (){

    //管理员登录
    Route::any("admin/login","AdminController@login")->name("admin.admin.login");

    //管理员注销登录
    Route::any("admin/logout","AdminController@logout")->name("admin.admin.logout");

    //更改密码
    Route::any("admin/changepwd","AdminController@changepwd")->name("admin.admin.changepwd");

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




