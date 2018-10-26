@extends("admin.layouts.main")
@section("content")

    <form class="form-horizontal" method="post" enctype="multipart/form-data">

        {{csrf_field()}}

        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">店铺分类</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="inputEmail3" name="shop_category_id	">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">店铺名称</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="inputEmail3" name="shop_name	">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">店铺图片</label>
            <div class="col-sm-10">
                <input type="file" class="form-control" id="inputEmail3" name="shop_img">
            </div>
        </div>

        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">起送金额</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="inputEmail3" name="start_send">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">配送费</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="inputEmail3" name="send_cost">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">店铺公告</label>
            <div class="col-sm-10">
                <textarea name="notice" id="" cols="50" rows="5"></textarea>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">优惠信息</label>
            <div class="col-sm-10">
                <textarea name="discount" id="" cols="50" rows="5"></textarea>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="brand">品牌连锁店
                    </label>
                    <label>
                        <input type="checkbox" name="on_time">准时送达
                    </label>
                    <label>
                        <input type="checkbox" name="fengniao">蜂鸟配送
                    </label>
                    <label>
                        <input type="checkbox" name="bao">保
                    </label>
                    <label>
                        <input type="checkbox" name="piao">票
                    </label>
                    <label>
                        <input type="checkbox" name="zhun">准
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-default">添加</button>
            </div>
        </div>
    </form>


@endsection