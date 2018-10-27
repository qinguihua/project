@extends("shop.layouts.main")
@section("content")

    <form class="form-horizontal" method="post">

        {{csrf_field()}}

        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">名称</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="inputEmail3" name="name" value="{{$menu_category->name}}">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">菜品编号</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="inputEmail3" name="type_accumulation" value="{{$menu_category->type_accumulation}}">
            </div>
        </div>
        {{--<div class="form-group">--}}
            {{--<label for="inputEmail3" class="col-sm-2 control-label">所属商家</label>--}}
            {{--<div class="col-sm-10">--}}
                {{--<input type="text" class="form-control" id="inputEmail3" name="information_id" value="{{$menu_category->information_id}}">--}}
            {{--</div>--}}
        {{--</div>--}}
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">简介</label>
            <div class="col-sm-10">
                <textarea name="description" id="" cols="50" rows="5">{{$menu_category->description}}</textarea>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">默认分类</label>
            <div class="col-sm-10">
                <input type="radio"name="is_selected" value="1">是
                <input type="radio"name="is_selected" value="0">否
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-default">修改</button>
            </div>
        </div>
    </form>


@endsection