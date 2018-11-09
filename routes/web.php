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
    return view('index');
});

Route::domain("shop.ele.com")->namespace("Shop")->group(function (){

    //@regtion 商户注册
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
//    Route::get("information/index","ShopInformationController@index")->name("shop.information.index");
    Route::any("information/add","ShopInformationController@add")->name("shop.information.add");
//    Route::any("information/check/{id}","ShopInformationController@check")->name("shop.information.check");
//    Route::any("information/edit{id}","ShopInformationController@edit")->name("shop.information.edit");
//    Route::get("information/del{id}","ShopInformationController@del")->name("shop.information.del");

    //图片自动上传
    Route::any("information/upload","ShopInformationController@upload")->name("shop.information.upload");


    //商家基本信息的管理
    Route::get("user/home","UserController@home")->name("shop.user.home");
    Route::any("user/add","UserController@add")->name("shop.user.add");
    Route::any("user/edit{id}","UserController@edit")->name("shop.user.edit");
    Route::get("user/del{id}","UserController@del")->name("shop.user.del");

    //前台显示活动列表
    Route::any("user/show","ActivityController@show")->name("shop.user.show");


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
    //图片自动上传
    Route::any("menu/upload","MenuController@upload")->name("shop.menu.upload");


    //订单详情
    Route::get("order/index","OrderController@index")->name("shop.order.index");
    //代付款
    Route::any("order/status/{id}/{status}","OrderController@status")->name("order.status");
    //查看
    Route::any("order/check{id}","OrderController@check")->name("shop.order.check");
    //按天统计
    Route::any("order/day","OrderController@day")->name("shop.order.day");
    //按月统计
    Route::any("order/month","OrderController@month")->name("shop.order.month");
    //订单销量
    Route::any("order/order","OrderController@order")->name("shop.order.order");
    //菜品销量
    Route::any("order/menu","OrderController@menu")->name("shop.order.menu");
    //按天统计
    Route::any("order/day1","OrderController@day1")->name("shop.order.day1");
    //按天统计
    Route::any("order/month1","OrderController@month1")->name("shop.order.month1");


    //抽奖活动
    Route::get("event/index","EventController@index")->name("shop.event.index");
    Route::any("event/sign/{id}","EventController@sign")->name("shop.event.sign");
    Route::any("event/check/{id}","EventController@check")->name("shop.event.check");

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

    //图片上传
    Route::any("category/upload","ShopCategoryController@upload")->name("admin.category.upload");

    //商家店铺信息
    Route::get("information/index","ShopInformationController@index")->name("admin.information.index");
    Route::any("information/add","ShopInformationController@add")->name("admin.information.add");
    Route::any("information/check/{id}","ShopInformationController@check")->name("admin.information.check");
    Route::any("information/edit{id}","ShopInformationController@edit")->name("admin.information.edit");
    Route::get("information/del{id}","ShopInformationController@del")->name("admin.information.del");
    //图片上传
    Route::any("information/upload","ShopInformationController@upload")->name("admin.information.upload");
    //管理员信息的管理
    Route::get("admin/index","AdminController@index")->name("admin.admin.index");
    Route::any("admin/add","AdminController@add")->name("admin.admin.add");
    Route::any("admin/edit{id}","AdminController@edit")->name("admin.admin.edit");
    Route::get("admin/del{id}","AdminController@del")->name("admin.admin.del");

    //商家管理
    Route::get("user/index","UserController@index")->name("admin.user.index");
    Route::any("user/reg","UserController@reg")->name("admin.user.reg");
    Route::any("user/del{id}","UserController@del")->name("admin.user.del");
    Route::any("information/apply/{id}","ShopInformationController@apply")->name("admin.information.apply");

    //活动列表
    Route::get("activity/index","ActivityController@index")->name("admin.activity.index");
    Route::any("activity/add","ActivityController@add")->name("admin.activity.add");
    Route::any("activity/edit{id}","ActivityController@edit")->name("admin.activity.edit");
    Route::get("activity/del{id}","ActivityController@del")->name("admin.activity.del");


    //订单销量
    Route::any("order/order","OrderController@order")->name("admin.order.order");
    //按天统计
    Route::any("order/day","OrderController@day")->name("admin.order.day");
    //按月统计
    Route::any("order/month","OrderController@month")->name("admin.order.month");

    //各个商家的销量
    //月
    Route::any("order/shopmonth","OrderController@shopmonth")->name("admin.order.shopmonth");
    //天
    Route::any("order/shopday","OrderController@shopday")->name("admin.order.shopday");
    //总
    Route::any("order/shopall","OrderController@shopall")->name("admin.order.shopall");


    //会员管理
    Route::get("member/index","MemberController@index")->name("admin.member.index");
    Route::any("member/check/{id}","MemberController@check")->name("admin.member.check");


    //权限列表
    Route::get("permission/index","PermissionController@index")->name("admin.permission.index");
    Route::any("permission/add","PermissionController@add")->name("admin.permission.add");
    Route::any("permission/edit/{id}","PermissionController@edit")->name("admin.permission.edit");
    Route::get("permission/del/{id}","PermissionController@del")->name("admin.permission.del");

    //角色列表
    Route::get("role/index","RoleController@index")->name("admin.role.index");
    Route::any("role/add","RoleController@add")->name("admin.role.add");
    Route::any("role/edit/{id}","RoleController@edit")->name("admin.role.edit");
    Route::get("role/del/{id}","RoleController@del")->name("admin.role.del");


    //导航条列表
    Route::get("nav/index","NavController@index")->name("admin.nav.index");
    Route::any("nav/add","NavController@add")->name("admin.nav.add");
    Route::any("nav/edit/{id}","NavController@edit")->name("admin.nav.edit");
    Route::get("nav/del/{id}","NavController@del")->name("admin.nav.del");



    //抽奖活动
    Route::get("event/index","EventController@index")->name("admin.event.index");
    Route::any("event/add","EventController@add")->name("admin.event.add");
    Route::any("event/edit/{id}","EventController@edit")->name("admin.event.edit");
    Route::get("event/del/{id}","EventController@del")->name("admin.event.del");

    //抽奖
    Route::get("event/cj/{id}","EventController@cj")->name("admin.event.cj");


    //抽奖活动奖品
    Route::get("eventprize/index","EventPrizeController@index")->name("admin.eventprize.index");
    Route::any("eventprize/add","EventPrizeController@add")->name("admin.eventprize.add");
    Route::any("eventprize/edit/{id}","EventPrizeController@edit")->name("admin.eventprize.edit");
    Route::get("eventprize/del/{id}","EventPrizeController@del")->name("admin.eventprize.del");


});




