<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Mockery\Matcher\Closure;

class BaseController extends Controller
{
    //

    public function __construct()
    {
        //中间件
        $this->middleware("auth:admin",[
            "except"=>["login"]
        ]);

        //设置权限
        $this->middleware(function ($request,\Closure $next){
            //得到当前访问地址的路由
            $route=Route::currentRouteName();
            //设置一个白名单
            $allow=[
                "admin.admin.login",
                "admin.admin.logout"
            ];
            //判断当前登录用户有没有权限
            if (!in_array($route,$allow) && !Auth::guard("admin")->user()->
                   can($route) && Auth::guard("admin")->id() != 1){
                exit(view("admin.admin.out"));
            }
            return $next($request);

        });


    }

}
