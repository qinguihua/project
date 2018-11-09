<?php

namespace App\Http\Controllers\Api;

use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\ShopInformation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{

        public function index(Request $request){

            //接收数据
            $keyword=$request->get("keyword");
//            $keyword ="素";
//dd($keyword);
            if($keyword!==null){
                //dd($keyword);
                $shops=ShopInformation::where("status",1)->where('shop_name','like','%'.$keyword.'%')->get();

        //  dd($shops);
           }else{
                //得到所有的店铺，设置状态为1
                $shops=ShopInformation::where("status",1)->get();
            }

            //追加时间和距离
            foreach ($shops as $k =>$v){
                $shops[$k]->shop_img=env("ALIYUN_OSS_URL").$v->shop_img;
                $shops[$k]->distance=rand(1000,5000);
                $shops[$k]->estimate_time=ceil($shops[$k]['distance'] / rand(100, 150));
            }

            return $shops;
        }

        public function detail()
        {
            $id=\request()->get('id');
            $shop=ShopInformation::find($id);
            $shop->shop_img=env("ALIYUN_OSS_URL").$shop->shop_img;
            $shop->service_code = 4.6;


            $shop->evaluate = [
                [
                    "user_id" => 12344,
                    "username" => "w******k",
                    "user_img" => "http=>//www.homework.com/images/slider-pic4.jpeg",
                    "time" => "2017-2-22",
                    "evaluate_code" => 1,
                    "send_time" => 30,
                    "evaluate_details" => "不怎么好吃"],
                ["user_id" => 12344,
                    "username" => "w******k",
                    "user_img" => "http=>//www.homework.com/images/slider-pic4.jpeg",
                    "time" => "2017-2-22",
                    "evaluate_code" => 4.5,
                    "send_time" => 30,
                    "evaluate_details" => "很好吃"]
            ];

            $cates=MenuCategory::where("information_id",$id)->get();

            //当前分类有哪些商品
            foreach ($cates as $k=>$cate){
                $goods=$cate->menus;

//                foreach ($goods as $v=>$good){
//                    $goods[$v]->goods_img=env("ALIYUN_OSS_URL").$good->goods_img;
//                }

                $cates[$k]->goods_list=$goods;

            }

            $shop->commodity=$cates;
            return $shop;
        }
}

