@extends("shop.layouts.main")
@section("title","商家列表")
@section("content")


    <a href="{{route("shop.user.add")}}" class="btn btn-info">添加</a>
    <br>
    <br>
    <table class="table table-striped">
        <tr>
            <th>Id</th>
            <th>名称</th>
            <th>邮箱</th>
            <th>操作</th>
        </tr>
        @foreach($users as $user)
            <tr>
                <td>{{$user->id}}</td>
                <td>{{$user->name}}</td>
                <td>{{$user->email}}</td>
                <td>
                    <a href="{{route("shop.user.edit",$user->id)}}" class="btn btn-success">编辑</a>
                    <a href="{{route("shop.user.del",$user->id)}}" class="btn btn-danger">删除</a>
                </td>
            </tr>
        @endforeach
    </table>

@endsection