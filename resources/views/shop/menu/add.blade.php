@extends("shop.layouts.main")

@section("title","添加菜品")

@section("content")

    <form method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="form-group">
            <label>商品名称</label>
            <input type="text" class="form-control" placeholder="菜品名称" name="goods_name" value="{{old("goods_name")}}">
        </div>

        <div class="form-group">
            <label>商品评分</label>
            <input type="text" class="form-control" placeholder="评分" name="rating" value="{{old("rating")}}">
        </div>

        {{--<div class="form-group">--}}
            {{--<label>商品所属商家</label>--}}
            {{--<input type="text" class="form-control" placeholder="所属商家" name="information_id" value="{{old("shop_id")}}">--}}
        {{--</div>--}}

        <div class="form-group">
            <label>商品分类</label>
            <select name="category_id" id="">
                @foreach($results as $result)
                <option value="{{$result->information_id}}">{{$result->name}}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>商品价格</label>
            <input type="text" class="form-control" placeholder="价格" name="goods_price" value="{{old("goods_price")}}">
        </div>

        <div class="form-group">
            <label>商品描述</label>
            <textarea name="description" cols="30" rows="5" class="form-control"></textarea>

            <div class="form-group">
                <label>商品月销量</label>
                <input type="text" class="form-control" placeholder="月销量" name="month_sales" value="{{old("month_sales")}}">
            </div>

            <div class="form-group">
                <label>商品评分数量</label>
                <input type="text" class="form-control" placeholder="评分数量" name="rating_count" value="{{old("rating_count")}}">
            </div>

            <div class="form-group">
                <label>提示信息</label>
                <input type="text" class="form-control" placeholder="提示信息" name="tips" value="{{old("tips")}}">
            </div>

            <div class="form-group">
                <label>满意度数量</label>
                <input type="text" class="form-control" placeholder="满意度数量" name="satisfy_count" value="{{old("satisfy_count")}}">
            </div>

            <div class="form-group">
                <label>满意度评分</label>
                <input type="text" class="form-control" placeholder="满意度评分" name="satisfy_rate" value="{{old("satisfy_rate")}}">
            </div>

            <div class="form-group">
                <label>商品图片</label>
                <input type="file" name="goods_img">
                <p class="help-block">请选择你的商品图片</p>
            </div>

            <div class="checkbox">
                <label>
                    <input type="radio" name="status"> 上架
                    <input type="radio" name="status"> 下架
                </label>
            </div>

            <button type="submit" class="btn btn-default">确认申请</button>
    </form>
@endsection