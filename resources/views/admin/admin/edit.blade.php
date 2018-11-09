@extends("admin.layouts.main")
@section("title","管理员管理")
@section("content")



    <form method="post" enctype="multipart/form-data" class="table table-striped">
        {{ csrf_field() }}
        <div class="form-group">
            <label>名称</label>
            <input type="text" class="form-control" name="name" value="{{old("name",$admin->name)}}">
        </div>
        <div class="form-group">
            <label>密码</label>
            <input type="password" class="form-control" name="password">
        </div>
        <div class="form-group">
            <label>邮箱</label>
            <input type="email" class="form-control" name="email" value="{{old("name",$admin->email)}}">
        </div>
        <div class="form-group">
            <label>角色</label>
            @foreach($roles as $role)
                <input type="checkbox" name="role[]" value="{{$role->id}}" {{in_array($role->name,$ro)?"checked":''}}>{{$role->name}}
            @endforeach
        </div>
        <button type="submit" class="btn btn-default">修改</button>
    </form>
@endsection