<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    //购物车列表
    public function index(Request $request)
    {
        //用户
        $userId=$request->input("user_id");
        //购物车列表
        $carts=Cart::where("user_id",$userId)->get();
        //声明一个数组
        $goodsList=[];
        //总价
        $totalCost=0;
        //循环购物车
        foreach ($carts as $k =>$v){
            $good=Menu::where("id",$v->goods_id)->first(["id as goods_id","goods_name","goods_img","goods_price"]);
            $good->amount=$v->amount;
            //算总价
            $totalCost=$totalCost+$good->amount*$good->goods_price;
            $goodsList[]=$good;

        }
        return [
            "goods_list"=>$goodsList,
            "totalCost"=>$totalCost
        ];

    }

    //添加列表
    public function add(Request $request)
    {
        //清空当前购物车
        Cart::where("user_id",$request->post("user_id"))->delete();
        //接收参数
        $goods=$request->post("goodsList");
        $counts=$request->post("goodsCount");
        foreach ($goods as  $k=>$good){
            $data=[
                "user_id"=>$request->post('user_id'),
                "goods_id"=>$good,
                "amount"=>$counts[$k]
            ];
          Cart::create($data);
        }
        return [
            "status"=>"true",
            "message"=>"添加成功"
        ];



    }


}
