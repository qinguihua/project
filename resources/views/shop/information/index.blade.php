@extends("admin.layouts.main")
@section("title","店铺信息")
@section("content")

<a href="{{route("shop.information.add")}}" class="btn btn-info">添加</a>
    <table class="table table-hover">
        <tr>
            <th>id</th>
            <th>分类</th>
            <th>名称</th>
            <th>图片</th>
            {{--<th>评分</th>--}}
            <th>是否品牌</th>
            {{--<th>准时送达</th>--}}
            <th>是否蜂鸟配送</th>
            {{--<th>保标记</th>--}}
            {{--<th>票标记</th>--}}
            {{--<th>准标记</th>--}}
            <th>起送金额</th>
            <th>配送费</th>
            <th>店公告</th>
            <th>优惠信息</th>
            <th>商家</th>
            <th>操作</th>
        </tr>
        @foreach($informations as $information)
        <tr>
            <td>{{$information->id}}</td>
            <td>{{$information->category->name}}</td>
            <td>{{$information->shop_name}}</td>
            <td><img src="/{{$information->shop_img}}" alt="" width="100px"></td>
            {{--<td>{{$information->shop_rating}}</td>--}}
            <td>{{$information->brand}}</td>
            {{--<td>{{$information->on_time}}</td>--}}
            <td>{{$information->fengniao}}</td>
            {{--<td>{{$information->bao}}</td>--}}
            {{--<td>{{$information->piao}}</td>--}}
            {{--<td>{{$information->zhun}}</td>--}}
            <td>{{$information->start_send}}</td>
            <td>{{$information->send_cost}}</td>
            <td>{{$information->notice}}</td>
            <td>{{$information->discount}}</td>
            <td>{{$information->user->name}}</td>
            <td>
                @if($information->status===0)
                <a href="{{route("shop.information.check",$information->id)}}" class="btn btn-success">审核</a>
                @endif
                <a href="{{route("shop.information.del",$information->id)}}" class="btn btn-danger" onclick="return confirm('删除会一并删除用户,确认吗？')">删除</a>
            </td>
        </tr>
       @endforeach
    </table>

@endsection