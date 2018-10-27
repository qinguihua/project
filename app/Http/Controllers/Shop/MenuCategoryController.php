<?php

namespace App\Http\Controllers\shop;

use App\Models\Menu;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MenuCategoryController extends BaseController
{
    //
    public function index(){

        $menu_categorys=MenuCategory::all();
        //显示视图并传递数据
        return view("shop.menu_category.index",compact("menu_categorys"));

    }

    public function add(Request $request){

        //判断提交方式
        if ($request->isMethod("post")){

            //验证
            $this->validate($request,[
                'name'=>'required|unique',
                'description'=>'required',
                'is_selected'=>'required',
            ]);

            //接收数据

            $data=$request->post();

            $shopId = Auth::user()->information->id;

            $data["information_id"]=$shopId;
//            dd( $data["information_id"]);

            //判断
            if($request->post("is_selected")){
                //把所有的is_selected设置为0
                MenuCategory::where("is_selected",1)->where("information_id",$shopId)->update(["is_selected"=>0]);
            }

            //数据入库
            if (MenuCategory::create($data)){
                //跳转
                return redirect()->route("shop.menu_category.index")->with("success","添加成功");
            }

        }else{

            //显示视图
            return view("shop.menu_category.add");

        }
    }

    //修改
    public function edit(Request $request,$id){

        //通过id得到对象
        $menu_category=MenuCategory::find($id);
        //判断提交方式
        if ($request->isMethod("post")){

            //接收数据
            $data=$request->post();

            $shopId = Auth::user()->information->id;

            $data["information_id"]=$shopId;

            //判断
            if($request->post("is_selected")){
                //把所有的is_selected设置为0
                MenuCategory::where("is_selected",1)->where("information_id",$shopId)->update(["is_selected"=>0]);
            }

            if ($menu_category->update($data)){
                return redirect()->route("shop.menu_category.index")->with("success","修改成功");
            }

        }else{
            //显示视图并传数据
            return view("shop.menu_category.edit",compact("menu_category"));
        }


    }


    //删除
    public function del($id){


        //得到当前分类
        $cate=MenuCategory::findOrFail($id);
        //得到当前分类对应的店铺数
        $shopCount=Menu::where('category_id',$cate->id)->count();
        //判断当前分类店铺数
        if ($shopCount){
            //回跳
            return  back()->with("danger","当前分类下有菜品，不能删除");
        }
        //否则删除
        $cate->delete();
        //跳转
        return redirect()->route('shop.menu_category.index')->with('success',"删除成功");
    }


    //查看
    public function check($id){

        $lists = DB::table("menus")->where("category_id",$id)->get();

//        $menu_categorys=MenuCategory::all();

        return view("shop.menu_category.check",compact("lists"));

    }


}
