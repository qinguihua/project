@extends("shop.layouts.main")
@section("title","菜品分类")
@section("content")

<a href="{{route("shop.menu_category.add")}}" class="btn btn-info">添加</a>

    <table class="table table-hover">
        <tr>
            <th>id</th>
            <th>名称</th>
            <th>所属商家</th>
            <th>简介</th>
            <th>选中</th>
            <th>操作</th>
        </tr>
        @foreach($menu_categorys as $menu_category)
        <tr>
            <td>{{$menu_category->id}}</td>
            <td>{{$menu_category->name}}</td>
            <td>{{$menu_category->shop_information->shop_name}}</td>
            <td>{{$menu_category->description}}</td>
            <td>{{$menu_category->is_selected}}</td>
            <td>
                <a href="{{route("shop.menu_category.edit",$menu_category->id)}}" class="btn btn-success">编辑</a>
                <a href="{{route("shop.menu_category.check",$menu_category->id)}}" class="btn btn-success">查看</a>
                <a href="{{route("shop.menu_category.del",$menu_category->id)}}" class="btn btn-danger">删除</a>
            </td>
        </tr>
       @endforeach
    </table>

@endsection