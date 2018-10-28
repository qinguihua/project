@extends("admin.layouts.main")
@section("title","活动列表")
@section("content")

    <div>

        <a href="{{route("admin.activity.add")}}" class="btn btn-info">添加</a>

        <form class="navbar-form navbar-right">
            <div class="form-group">
                <select name="time" class="form-control">
                    <option>请选择分类</option>
                        <option value="3">活动未开始</option>
                        <option value="1">活动进行中</option>
                        <option value="2">活动已结束</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-default">搜索</button>
            </div>
        </form>

    </div>



    <br>
    <br>
    <table class="table table-striped">
        <tr>
            <th>Id</th>
            <th>活动标题</th>
            <th>活动内容</th>
            <th>开始时间</th>
            <th>结束时间</th>
            <th>操作</th>
        </tr>
        @foreach($activitys as $activity)
            <tr>
                <td>{{$activity->id}}</td>
                <td>{{$activity->title}}</td>
                <td>{{$activity->content}}</td>
                <td>{{$activity->start_time}}</td>
                <td>{{$activity->end_time}}</td>

                <td>
                    <a href="{{route("admin.activity.edit",$activity->id)}}" class="btn btn-success">编辑</a>
                    <a href="{{route("admin.activity.del",$activity->id)}}" class="btn btn-danger">删除</a>
                </td>
            </tr>
        @endforeach
    </table>

    {{$activitys->appends($url)->links()}}

@endsection