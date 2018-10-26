@extends("shop.layouts.main")

@section("title","添加管理员")

@section("content")

    <form method="post">
        {{ csrf_field() }}
        <div class="form-group">
            <label>商家名称</label>
            <input type="text" class="form-control" placeholder="商家名称" name="name" value="{{old("name")}}">
        </div>

        <div class="form-group">
            <label>商家邮箱</label>
            <input type="email" class="form-control" placeholder="商家邮箱" name="email" value="{{old("email")}}">
        </div>

        <div class="form-group">
            <label>商家密码</label>
            <input type="password" class="form-control" placeholder="商家密码" name="password">
        </div>


        <button type="submit" class="btn btn-default">添加</button>
    </form>
@endsection