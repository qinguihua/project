<?php

namespace App\Http\Controllers\Shop;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegController extends BaseController
{
    //
    public function reg(Request $request){
        //判断提交方式
        if ($request->isMethod("post")){
            //验证
            $this->validate($request, [
                "name" => "required|unique:users",
                "password" => "required|min:6",
                "captcha" => "required|captcha"
                  ],[
                "captcha.required" => '验证码不能为空',
                "captcha.captcha" => '验证码有误',
            ]);
            //接收数据
            $data=$request->post();
            //密码加密
            $data['password'] = bcrypt($data['password']);
            //添加
            if (User::create($data)){
                //跳转
                return redirect()->route("shop.user.login")->with("success","注册成功");
            }

        }else{
            //显示视图
            return view("shop.user.reg");
        }
    }

    //登录

        public function login(Request $request){
            //判断提交方式
            if ($request->isMethod("post")){
                //验证
                $data=$this->validate($request, [
                    "name" => "required",
                    "password" => "required"
                ]);
                //验证账号和密码是否正确
                if(Auth::attempt($data,$request->has("remeber"))){

                    //当前登录用户的id
                    $user=Auth::user();
                    $shop=$user->information;
                    //通过用户找到店铺
                    if ($shop){
                        //如果有店铺
                        switch ($shop->status){
                            //禁用
                            case -1:
                                Auth::logout();
                                return back()->withInput()->with("danger","店铺已被禁用！");
                                break;
                            //未审核
                            case 0:
                                Auth::logout();
                                return back()->withInput()->with("danger","店铺等待审核中!");
                                break;
                        }
                    }

                    //登录成功
                    if (Auth::user()->information==null) {
                        return redirect()->intended(route("shop.information.add"))->with("success","登录成功,欢迎申请店铺");
                    }else{
                        return redirect()->intended(route("shop.user.index"))->with("success","登录成功");
                    }

                }else{
                    //登录失败
                    return redirect()->back()->withInput()->with("danger","账号或密码错误");
                }
            }else{
                //显示视图
                return view("shop.user.login");
            }
    }


    //更改密码
    public function change_pwd(Request $request){

        //得到当前用户
        $user=Auth::guard("web")->user();

//        dd($user);
        //判断提交方式
        if ($request->isMethod("post")){

            //验证
            $this->validate($request,[
                "old_password"=>"required",
                "password"=>"required|confirmed"
            ]);


            $oldpassword=$request->post("old_password");
            //判断旧密码是否正确
            if(Hash::check($oldpassword,$user->password)){
                //设置新密码
                $user->password=Hash::make($request->post("password"));
//                $user->password=Hash::make($request->post("password"));
                //保存修改
                $user->save();
                //跳转
                return redirect()->route("admin.admin.index")->with("success","修改成功");
            }
            //旧密码不正确
            return back()->with("danger", "旧密码不正确");

        }else{

            //显示视图
            return view("shop.user.change_pwd",compact("user"));

        }
    }


    //注销
    public function logout(){
        Auth::guard("web")->logout();
        //跳转
        return redirect()->route("shop.user.login")->with("success","退出成功");
    }


    //后台首页
    public function index(){
//        if (Auth::user()->information===null) {
////            return redirect()->route("shop.information.add")->with("danger","你还没有商铺呢！快点加入我们吧！！");
////        }
        //显示视图
        return view("shop.user.index");

    }



}
