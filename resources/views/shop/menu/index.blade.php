@extends("shop.layouts.main")
@section("title","菜品分类")
@section("content")

<div>

    <a href="{{route("shop.menu.add")}}" class="btn btn-info">添加</a>

    <form class="navbar-form navbar-right">
        <div class="form-group">
            <select name="category_id" class="form-control">
                <option value="">请选择分类</option>
                @foreach($results as $result)
                    <option value="{{$result->id}}">{{$result->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="exampleInputName2">价格</label>
            <input type="text" class="form-control" id="exampleInputName2" placeholder="最高价" name="maxPrice">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail2">-</label>
            <input type="text" class="form-control" id="exampleInputEmail2" placeholder="最低价" name="minPrice">
        </div>
        <div class="form-group">
            <input type="text" class="form-control" id="exampleInputEmail2" placeholder="请输入菜品名称" name="goods_name">
            <button type="submit" class="btn btn-default">搜索</button>
        </div>
    </form>

</div>



    <table class="table table-hover">
        <tr>
            <th>id</th>
            <th>名称</th>
            <th>所属商家</th>
            <th>所属分类</th>
            <th>价格</th>
            <th>简介</th>
            <th>月销量</th>
            <th>商品图片</th>
            <th>操作</th>
        </tr>
        @foreach($menus as $menu)
        <tr>
            <td>{{$menu->id}}</td>
            <td>{{$menu->goods_name}}</td>
            <td>{{$menu->shop_information->shop_name}}</td>
            <td>{{$menu->menu_category->name}}</td>
            <td>{{$menu->goods_price}}</td>
            <td>{{$menu->description}}</td>
            <td>{{$menu->month_sales}}</td>
            {{--<td><img src="/{{$menu->goods_img}}" alt="" width="100">--}}
            <td><img src="{{$menu->goods_img}}?x-oss-process=image/resize,m_fill,w_80,h_80"></td>
            </td>
            <td>
                <a href="{{route("shop.menu.edit",$menu->id)}}" class="btn btn-success">编辑</a>
                <a href="{{route("shop.menu.del",$menu->id)}}" class="btn btn-danger">删除</a>
            </td>
        </tr>
       @endforeach
    </table>

{{$menus->appends($url)->links()}}

@endsection