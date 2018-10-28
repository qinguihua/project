@extends("shop.layouts.main")
@section("title","活动列表")
@section("content")

        <a href="{{route("admin.activity.add")}}" class="btn btn-info">添加</a>

    <br>
    <br>
    <table class="table table-striped">
        <tr>
            <th>活动标题</th>
            <th>活动内容</th>
            <th>开始时间</th>
            <th>结束时间</th>
        </tr>
        @foreach($activitys as $activity)
            <tr>
                <td>{{$activity->title}}</td>
                <td>{{$activity->content}}</td>
                <td>{{$activity->start_time}}</td>
                <td>{{$activity->end_time}}</td>
            </tr>
        @endforeach
    </table>
@endsection