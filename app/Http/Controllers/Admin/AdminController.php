<?php

namespace App\Http\Controllers\Admin;


use App\Models\Admin;
use Illuminate\Auth\Authenticatable;
//use App\Models\ShopCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends BaseController
{
    //

    //登录

    public function login(Request $request){
        //判断提交方式
        if ($request->isMethod("post")){
            //验证
            $data=$this->validate($request, [
                "name" => "required",
                "password" => "required"
            ]);
//            dd($data);
            //验证账号和密码是否正确
            if (Auth::guard("admin")->attempt($data,$request->has("remember"))){
                //登录成功
                return redirect()->intended(route("shop.information.index"))->with("success","登录成功");
            }else{
                //登录失败
                return redirect()->back()->withInput()->with("danger","账号或密码错误");
            }
        }else{
            //显示视图
            return view("admin.admin.login");
        }
    }

    //注销
    public function logout(){
        Auth::guard("admin")->logout();
        //跳转
        return redirect()->route("admin.admin.login")->with("success","退出成功");
    }



    public function index(){
        $admins=Admin::all();
        //显示视图并传递数据
        return view("admin.admin.index",compact("admins"));
    }

    public function add(Request $request){
        if ($request->isMethod("post")){
            //验证
            $this->validate($request,[
                "name"=>"required",
                "password"=>"required|min:6",
                "email"=>"required"
            ]);

            //接收数据
            $data=$request->post();

            //密码加密
            $data['password'] = bcrypt($data['password']);

            //将数据入库
            if(Admin::create($data)){
                //跳转
                return redirect()->intended(route("admin.admin.index"))->with("success","添加成功");
            }

        }else{
            //显示视图
            return view("admin.admin.add");
        }
    }

    //更改密码
    public function changepwd(Request $request){

        //得到当前用户
        $admin=Auth::guard("admin")->user();

//        dd($admin);
        //判断提交方式
        if ($request->isMethod("post")){

            //验证
            $this->validate($request,[
                "old_password"=>"required",
                "password"=>"required|confirmed"
            ]);


            $oldpassword=$request->post("old_password");
            //判断旧密码是否正确
            if(Hash::check($oldpassword,$admin->password)){
                //设置新密码
                $admin->password=Hash::make($request->post("password"));
                //保存修改
                $admin->save();
                //跳转
                return redirect()->route("admin.admin.index")->with("success","修改成功");
            }
            //旧密码不正确
            return back()->with("danger", "旧密码不正确");

        }else{

            //显示视图
            return view("admin.admin.changepwd",compact("admin"));

        }
    }

    //修改
    public function edit(Request $request,$id){
        $admin=Admin::find($id);
        if ($request->isMethod("post")){

            //接收数据
            $data=$request->post();

            //将数据入库
            if($admin->update($data)){
                //跳转
                return redirect()->intended(route("admin.admin.index"))->with("success","修改成功");
            }

        }else{
            //显示视图
            return view("admin.admin.edit",compact("admin"));
        }
    }

    //删除
    public function del($id){
        $admin=Admin::find($id);
        if($admin->delete()){
            //跳转

            return redirect()->intended(route("admin.admin.index"))->with("success","删除成功");
        }
    }



}
