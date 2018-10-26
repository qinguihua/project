@extends("admin.layouts.main")
@section("title","修改密码")
@section("content")



    <form method="post"  class="table table-striped">
        {{ csrf_field() }}
        <div class="form-group">
            <label>用户名</label>
            <input class="form-control" type="text"  name="name" value="{{$admin->name}}" readonly>
        </div>
        <div class="form-group">
            <label>原密码</label>
            <input type="password" class="form-control" name="old_password">
        </div>
        <div class="form-group">
            <label>新密码</label>
            <input type="password" class="form-control" name="password">
        </div>
        <div class="form-group">
            <label>确认密码</label>
            <input type="password" class="form-control" name="password_confirmation">
        </div>
        <button type="submit" class="btn btn-default">修改</button>
    </form>
@endsection