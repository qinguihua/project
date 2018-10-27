<?php

namespace App\Http\Controllers\shop;

use App\Models\Menu;
use App\Models\Menu_category;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MenuController extends BaseController
{
    //
    public function index(Request $request){

        $url=$request->query();

        // 接收数据
        $categoryId = $request->get("category_id");
        $goods_name=$request->get("goods_name");
        $maxPrice=$request->get("maxPrice");
        $minPrice=$request->get("minPrice");

        //得到所有并分页
        $query = Menu::orderBy("id");
        if ($categoryId!==null) {
            $query->where("category_id",$categoryId);
        }

        //按菜品名搜索
        if ($goods_name!==null){

            $query->where("title","like","%{$goods_name}%");
        }
        //按价格区间搜索
        if ($maxPrice!=0 && $minPrice!=0){
            $query->where("goods_price",">=","$minPrice");
            $query->where("goods_price","<=","$maxPrice");
        }

        $menus=$query->paginate(2);


        //显示视图并传递数据
        $results=MenuCategory::all();
        return view("shop.menu.index",compact("menus","results","url"));

    }



    public function add(Request $request){
        //判断提交方式
        if ($request->isMethod("post")){

            //接收数据
            $data=$request->post();
//            $shopId = Auth::user()->shop_information->id;
            $shopId=$data["information_id"];

            $data['status']=$request->has('status')?'1':'0';
            //上传图片
            $data['goods_img']=$request->file("goods_img")->store("images","image");
            //数据入库
            if (Menu::create($data)){
                //跳转
                return redirect()->route("shop.menu.index")->with("success","添加成功");
            }

        }else{
            $results=MenuCategory::all();
            //dd($results);
            //显示视图
            return view("shop.menu.add",compact("results"));

        }
    }

    //修改
    public function edit(Request $request,$id){

        //通过id得到对象
        $menu=Menu::find($id);
        //判断提交方式
        if ($request->isMethod("post")){

            //接收数据
            $data=$request->post();
            $data['status']=$request->has('status')?'1':'0';

            //判断是否重新上传图片
            if($request->file("goods_img")!==null){
                $data['goods_img']=$request->file("goods_img")->store("images","image");
            }else{
                $data['goods_img']=$menu->goods_img;
            }

            if ($menu->update($data)){
                return redirect()->route("shop.menu.index")->with("success","修改成功");
            }

        }else{
            //显示视图并传数据
            $results=MenuCategory::all();
            return view("shop.menu.edit",compact("menu","results"));
        }


    }


    //删除
    public function del($id){

        $menu=Menu::find($id);

        if ($menu->delete()){

            return redirect()->route("shop.menu.index")->with("success","删除成功");
        }

    }

}
