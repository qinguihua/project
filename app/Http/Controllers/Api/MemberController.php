<?php

namespace App\Http\Controllers\Api;

use App\Models\Member;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Mrgoon\AliSms\AliSms;

class MemberController extends Controller
{
    //用户注册
    public function reg( Request $request )
    {
        //接收参数
        $data=$request->all();
        //验证
        $validate=Validator::make($data,[
            "username"=>"required",
            "sms"=>"required|integer|min:1000|max:9999",
            "tel"=>[
                "required",
                "regex:/^0?(13|14|15|17|18|19)[0-9]{9}$/",
                "unique:members"
            ],
            "password"=>"required|min:6",
        ]);
        $code=Redis::get('tel_'.$data['tel']);
        //判断验证码是否正确
        if($data['sms']==$code){
            $data['password']=Hash::make($data['password']);

            if (Member::create($data)) {
                $data = [
                    'status' => "true",
                    'message' => "注册成功 请登录",
                ];
            } else {

                $data = [
                    'status' => "false",
                    'message' => "注册失败",
                ];
            }
        }
        return $data;
    }



    //手机验证码
        public function sms(Request $request)
        {
            //接收参数
            $tel = $request->get("tel");
            //随机生成4位验证码
            $code = mt_rand(1000, 9999);
            //把验证码存起来  五分钟有效
            Redis::setex("tel_" . $tel, 5 * 60, $code);
            //把验证码发给手机
            $config = [
                'access_key' => env("ALIYUNU_ACCESS_ID"),
                'access_secret' => env("ALIYUNU_ACCESS_KEY"),
                'sign_name' => '个人分享',
            ];

            $sms = new AliSms();
            $response = $sms->sendSms($tel, 'SMS_149417618', ['code' => $code], $config);
            //返回
//        dd($response);
            $data = [
                "status" => "true",
                "message" => "获取验证码成功" . $code
            ];

            return $data;

        }

        public function login(Request $request){
            //接收数据
            $name=$request->name;
            $password=$request->password;
            //判断用户名是否存在
            if ($member=Member::where("username",$name)->first()){
                //判断密码是否正确
               if(Hash::check($password,$member->password)) {
                   $data = [
                       "status" => "true",
                       "message" => "登录成功",
                       "user_id" => $member->id,
                       "username" => $name
                   ];
                 }
               }else{
                   $data = [
                       "status" => "false",
                       "message" => "登录失败"
                   ];
               }
            return $data;
        }

        //忘记密码
        public function reset(Request $request)
        {
            //接收数据
            $data = $request->post();
            $code = Redis::get('tel_' . $data['tel']);
                //判断验证码是否正确
                if ($data['sms'] == $code) {
                    //判断手机号是否存在
                     $member=Member::where("tel", $data["tel"])->first();
                    //密码加密
                    $data['password'] = Hash::make($data['password']);
                    //修改密码
                    if ($member->update($data)) {
                        $data = [
                            "status" => "true",
                            "message" => "密码修改成功",
                        ];
                    }else{
                        $data = [
                            "status" => "false",
                            "message" => "密码修改失败",
                        ];
                    }

                }
                return $data;
        }


        //修改密码
        public function alter(Request $request){

            //接收数据
            $data = $request->post();
            $oldPassword=$request->post("oldPassword");
            $newPassword=$request->post("newPassword");
            //加密
            $new = Hash::make($newPassword);
            $member = Member::where("id", $data['id'])->first();

            //判断旧密码是否正确
           if (Hash::check($oldPassword,$member->password)) {
               //修改新密码
               Member::where('id',$data['id'])->update(['password'=>$new]);


                   $data = [
                       "status" => "true",
                       "message" => "密码修改成功",
                   ];
               }else{
                   $data = [
                       "status" => "false",
                       "message" => "密码修改失败",
                   ];
               }

    return $data;

        }

        //显示余额与积分
        public function detail(Request $request){
            {
                return Member::find($request->get('user_id'));
            }
        }



}
