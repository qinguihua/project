<?php

namespace App\Http\Controllers\Shop;

use App\Models\ShopCategory;
use App\Models\ShopInformation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ShopInformationController extends Controller
{
    //显示所有商家信息
    public function index(){
        $informations=ShopInformation::all();
//        $results=ShopCategory::all();
        //显示视图并传递数据
        return view("shop.information.index",compact("informations"));
    }

    //添加
    public function add(Request $request){

        //判断提交方式
        if($request->isMethod("post")){

            //验证，如果没有通过验证，就返回添加页面
            $this->validate($request,[
//                "shop_category_id"=>"required",
//                "shop_name"=>"required",
                "shop_img"=>"required",
                "start_send"=>"required",
                "send_cost"=>"required",
                "notice"=>"required",
                "discount"=>"required",
            ]);

            //接收数据
            $data=$request->post();

            $data['on_time']=$request->has('on_time')?'1':'0';
            $data['brand']=$request->has('brand')?'1':'0';
            $data['fengniao']=$request->has('fengniao')?'1':'0';
            $data['bao']=$request->has('bao')?'1':'0';
            $data['piao']=$request->has('piao')?'1':'0';
            $data['zhun']=$request->has('zhun')?'1':'0';


//            dd($data);
            //上传图片
            $data['shop_img']=$request->file("shop_img")->store("images","image");

            //将数据入库
            if(ShopInformation::create($data)){
                //跳转
                return redirect()->intended(route("shop.user.index"))->with("success","申请成功，等待审核");
            }

        }else{

            $results=ShopCategory::all();
            //显示视图并传递数据
            return view("shop.information.add",compact("results"));

        }
    }

    //审核
    public function check($id){
//        $id =Auth::id();
//        dd($id);
       $information=ShopInformation::findOrFail($id);
//       dd($information);
       $information->status=1;
       $information->save();
       return back()->with("success","通过审核");
    }



    //删除
    public function del($id){

        $information=ShopInformation::findOrFail($id);

        if($information->delete()){
            //跳转
            return redirect()->route("shop.information.index")->with("success","删除成功");
        }

    }



}
