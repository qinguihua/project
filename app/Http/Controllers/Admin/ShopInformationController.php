<?php

namespace App\Http\Controllers\admin;

use App\Models\ShopCategory;
use App\Models\ShopInformation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShopInformationController extends BaseController
{
    //显示所有商家信息
    public function index(){
        $informations=ShopInformation::all();
//        $results=ShopCategory::all();
        //显示视图并传递数据
        return view("admin.information.index",compact("informations"));
    }


//申请店铺
    public function apply(Request $request,$id)
    {

//        $id=User::find($id);
        //判断数据提交方式
        if ($request->isMethod("post")) {
            //1. 验证
            $this->validate($request, [
//                'shop_cate_id' => 'required|integer',
//                'shop_name' => 'required|max:100|unique:shops',
                'shop_img' => 'required',
                'start_send' => 'required|numeric',
                'send_cost' => 'required|numeric',
                'notice' => 'string',
                'discount' => 'string',
            ]);
            //接收数
            $data = $request->post();
            $data['on_time']=$request->has('on_time')?'1':'0';
            $data['brand']=$request->has('brand')?'1':'0';
            $data['fengniao']=$request->has('fengniao')?'1':'0';
            $data['bao']=$request->has('bao')?'1':'0';
            $data['piao']=$request->has('piao')?'1':'0';
            $data['zhun']=$request->has('zhun')?'1':'0';

            $data['user_id']=$id;
            $data['status']=1;

//             dd($data);
//            $data['shop_img'] = $request->file("shop_img")->store("images", "image");
//             dd($data);
            ShopInformation::create($data);
            session()->flash("success", "申请店铺成功。");
            return redirect()->route("admin.user.index");
        }
        //得到所有商家分类
        $cates = ShopCategory::all();
        return view("admin.information.apply",compact("cates"));
    }


    //审核
    public function check($id){
//        $id =Auth::id();
//        dd($id);
        $information=ShopInformation::findOrFail($id);
//       dd($information);
        $information->status=1;
        $information->save();

        $shopName=$information->user->name;

        $to = $information->user->email;//收件人

        $subject = $shopName.' 审核通知';//邮件标题
        \Illuminate\Support\Facades\Mail::send(
            'email.test',
            compact("shopName"),
            function ($message) use($to, $subject) {
                $message->to($to)->subject($subject);
            }
        );

        return back()->with("success","通过审核");
    }



    //删除
    public function del($id){

        $information=ShopInformation::findOrFail($id);

        if($information->delete()){
            //跳转
            return redirect()->route("admin.information.index")->with("success","删除成功");
        }

    }

    public function upload(Request $request){

        //处理上传
        $file=$request->file("file");

        if ($file){
            $url=$file->store("menu_cate");
            //得到真实的地址
//            $url=Storage::url($url);
            $data["url"]=$url;
            return $data;
        }
    }



}
