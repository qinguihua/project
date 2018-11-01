@extends("admin.layouts.main")
@section("title","商户管理")
@section("content")


    <a href="{{route("admin.user.reg")}}" class="btn btn-primary">添加</a>
    <table class="table table-bordered">
        <tr>
            <th>Id</th>
            <th>用户名</th>
            <th>Email</th>
            <th>店铺</th>

            <th>操作</th>
        </tr>
        @foreach($users as $user)
            <tr>
                <td>{{$user->id}}</td>
                <td>{{$user->name}}</td>
                <td>{{$user->email}}</td>
                <td>@if($user->information) {{$user->information->shop_name}} @endif</td>

                <td>

                    {{--<a href="#" class="btn btn-info">编辑</a>--}}
                    <a href="{{route("admin.user.del",[$user->id])}}" class="btn btn-danger">删除</a>
                    @if(!$user->information)
                        <a href="{{route('admin.information.apply',[$user->id])}}" class="btn btn-success">添加店铺</a> @endif

                </td>
            </tr>
        @endforeach
    </table>

@endsection

