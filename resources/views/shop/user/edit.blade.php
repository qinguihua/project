@extends("shop.layouts.main")
@section("title","管理员管理")
@section("content")



    <form method="post" enctype="multipart/form-data" class="table table-striped">
        {{ csrf_field() }}
        <div class="form-group">
            <label>名称</label>
            <input type="text" class="form-control" name="name" value="{{old("name",$user->name)}}">
        </div>
        <div class="form-group">
            <label>邮箱</label>
            <input type="email" class="form-control" name="email" value="{{old("name",$user->email)}}">
        </div>
        <div class="form-group">
            <label>密码</label>
            <input type="password" class="form-control" name="password">
        </div>
        <button type="submit" class="btn btn-default">修改</button>
    </form>
@endsection