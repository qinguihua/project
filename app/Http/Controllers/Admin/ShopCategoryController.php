<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\ShopCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShopCategoryController extends BaseController
{
    //
    public function index(){
        $categorys=ShopCategory::all();
        //显示视图并传递数据
        return view("admin.category.index",compact("categorys"));
    }

    public function add(Request $request){
        if ($request->isMethod("post")){
            //接收数据
            $data=$request->post();

            //上传图片
//            $data['img']=$request->file("img")->store("images","image");

            //将数据入库
            if(ShopCategory::create($data)){
                //跳转
                return redirect()->intended(route("admin.category.index"))->with("success","添加成功");
            }

        }else{
            //显示视图
            return view("admin.category.add");
        }
    }

    //修改
    public function edit(Request $request,$id){
        $category=ShopCategory::find($id);
        if ($request->isMethod("post")){

            //接收数据
            $data=$request->post();
            //dd($data);
            //判断图片是否重新上传
//            if($request->file("img")!==null){
//                $data['img']=$request->file("img")->store("images","image");
//            }else{
//                $data['img']=$category->img;
//            }

            //将数据入库
            if($category->update($data)){
                //跳转
                return redirect()->intended(route("admin.category.index"))->with("success","修改成功");
            }

        }else{
            //显示视图
            return view("admin.category.edit",compact("category"));
        }
    }

    //删除
    public function del($id){
        $category=ShopCategory::find($id);
        if($category->delete()){
            //跳转
            return redirect()->intended(route("admin.category.index"))->with("success","删除成功");
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
