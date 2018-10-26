@extends("admin.layouts.main")
@section("title","店铺申请列表")
@section("content")



    <form method="post" enctype="multipart/form-data" class="table table-striped">
        {{ csrf_field() }}
        <div class="form-group">
            <label>商铺分类名</label>
            <input type="text" class="form-control" name="name" value="{{old("name",$category->name)}}">
        </div>

        <div class="form-group">
            <label>商铺分类图片</label>
            <input type="file" name="img">
            <p class="help-block">请选择你的商铺分类图片</p>
            <img src="/{{$category->img}}" width="100">
        </div>

        <div class="form-group">
            <label>状态</label>
            <div>
                <input type="radio" name="status" value="1" <?php if($category->status==1) echo 'checked' ?> >启用
                <input type="radio" name="status" value="0" <?php if($category->status==0) echo 'checked' ?>>禁用
            </div>
        </div>

        <button type="submit" class="btn btn-default">确认修改</button>
    </form>
@endsection