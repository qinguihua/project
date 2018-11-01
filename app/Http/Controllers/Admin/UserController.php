<?php

namespace App\Http\Controllers\admin;

use App\Models\ShopInformation;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class UserController extends BaseController
{
    //
    //显示所有用户
    public function index()
    {
        $users =User::all();
        return view('admin.user.index', compact('users'));
    }

    //添加
    public function reg(Request $request)
    {
        if ($request->isMethod("post")) {
            //1. 验证
            $this->validate($request, [
                "name" => "required|unique:users",
                "password" => "required",
                "email" => "required"
            ]);
            //2. 接收数据
            $data = $request->post();
            //2.1密码加密
            $data['password'] = bcrypt($data['password']);
            //3. 入库

            User::create($data);
            //4. 跳转
            return redirect()->route("admin.user.index")->with("success", "注册成功");
        }
        return view("admin.user.reg");
    }

    //删除
    public function del($id)
    {
        DB::transaction(function () use ($id){
            //1. 删除用户
            User::findOrFail($id)->delete();
            //2. 删除用户对应店铺
            ShopInformation::where("user_id", $id)->delete();
        });
        return back()->with("success", "删除成功");
    }

}
