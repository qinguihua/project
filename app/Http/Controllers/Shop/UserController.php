<?php

namespace App\Http\Controllers\Shop;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class UserController extends BaseController
{
    //
    public function home()
    {
        $users =User::all();
        //显示视图并传递数据
        return view("shop.user.home", compact("users"));

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

                //将数据入库
                if(User::create($data)){
                    //跳转
                    return redirect()->intended(route("shop.user.home"))->with("success","添加成功");
                }

            }else{
                //显示视图
                return view("shop.user.add");
            }
        }

        //修改
        public function edit(Request $request,$id){
            $user=User::find($id);
            if ($request->isMethod("post")){

                //接收数据
                $data=$request->post();

                //将数据入库
                if($user->update($data)){
                    //跳转
                    return redirect()->intended(route("shop.user.home"))->with("success","修改成功");
                }

            }else{
                //显示视图
                return view("shop.user.edit",compact("user"));
            }
        }

        //删除
        public function del($id){
            $user=User::find($id);

            DB::transaction(function (){

                DB::table("user")->delete();

            });

            if($user->delete()){
                //跳转

                return redirect()->intended(route("shop.user.home"))->with("success","删除成功");
            }
        }


}
