<?php

namespace App\Http\Controllers\admin;

use App\Models\Activity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActivityController extends BaseController
{
    public function index(Request $request){

        $url=$request->query();

        //接收数据
        $time=$request->get("time");
        $title=$request->get("title");

        //按活动时间搜索
        $time =$request->get("time");
        //有效期内
        //$date = date('Y-m-d',time());
        $query = Activity::orderBy("id");
        //得到当前时间
        $date=date('Y-m-d H:i:s', time());
        //判断时间  1 进行 2 结束 3 未开始
        if( $time == 1 ){
            $query->where("start_time","<=",$date)->where("end_time",">",$date);
        }
        if($time == 2){
            $query->where("end_time","<",$date);
        }
        if($time == 3){
            $query->where("start_time",">",$date);
        }

        $activitys = $query->paginate(2);
//        dd($date);
//        $activitys=Activity::all();
        //显示视图并传递数据
        return view("admin.activity.index",compact("activitys","url"));

    }

    public function add(Request $request){

        //判断提交方式
        if ($request->isMethod("post")){

            //验证
            $this->validate($request,[
//                'title'=>'required',
//                'content'=>'required',
//                'start_time'=>'required',
//                'end_time'=>'required',
            ]);

            //接收数据

            $data=$request->post();

            //数据入库
            if (Activity::create($data)){
                //跳转
                return redirect()->route("admin.activity.index")->with("success","添加成功");
            }

        }else{

            //显示视图
            return view("admin.activity.add");

        }
    }

    //修改
    public function edit(Request $request,$id)
    {

        //通过id得到对象
        $activity = Activity::find($id);

        $activity->start_time = str_replace(" ", "T", $activity->start_time);
        $activity->end_time = str_replace(" ", "T", $activity->end_time);

        //判断提交方式
        if ($request->isMethod("post")) {
            $data = $this->validate($request, [
                "title" => "required",
                "start_time" => "required",
                "end_time" => "required",
                "content" => "required"
            ]);
//            $data=$request->post();
//           dd($data);
            $data['start_time'] = str_replace("T", " ", $data['start_time']);
            $data['end_time'] = str_replace("T", " ", $data['end_time']);

//            dd($data);
            $data->update($data);
            return redirect()->intended(route("admin.activity.index"))->with("success", "修改成功");
        }else{

            return view("admin.activity.edit",compact("activity"));
        }
    }


        //删除
    public function del($id)
    {

        //通过id得到对象
        $activity = Activity::find($id);

        if ($activity->delete()) {

            //跳转
            return redirect()->route('admin.activity.index')->with('success', "删除成功");
        }
    }


}
