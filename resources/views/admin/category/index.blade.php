@extends("admin.layouts.main")
@section("title","店铺申请列表")
@section("content")


    <a href="{{route("admin.category.add")}}" class="btn btn-info">添加</a>
    <br>
    <br>
    <table class="table table-striped">
        <tr>
            <th>Id</th>
            <th>商铺分类</th>
            <th>商铺分类图片</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        @foreach($categorys as $category)
            <tr>
                <td>{{$category->id}}</td>
                <td>{{$category->name}}</td>
                <td><img src="{{env("ALIYUN_OSS_URL").$category->img}}?x-oss-process=image/resize,m_fill,w_80,h_80"></td>
                <td>{{$category->status}}</td>

                <td>
                    <a href="{{route("admin.category.edit",$category->id)}}" class="btn btn-success">编辑</a>
                    <a href="{{route("admin.category.del",$category->id)}}" class="btn btn-danger">删除</a>
                </td>
            </tr>
        @endforeach
    </table>

@endsection