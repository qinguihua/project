@extends("shop.layouts.main")
@section("title","菜品分类")
@section("content")

    <table class="table table-hover">
        <tr>
            <th>名称</th>
            <th>价格</th>
            <th>简介</th>
            <th>图片</th>
        </tr>
        @foreach($lists as $list)
        <tr>
            <td>{{$list->goods_name}}</td>
            <td>{{$list->goods_price}}</td>
            <td>{{$list->description}}</td>
            <td><img src="/{{$list->goods_img}}" alt="" width="100"></td>

        </tr>
       @endforeach
    </table>

@endsection