@extends("shop.layouts.main")

@section("title","添加菜品")

@section("content")

    <form method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="form-group">
            <label>商品名称</label>
            <input type="text" class="form-control" placeholder="菜品名称" name="goods_name" value="{{"$menu->goods_name"}}">
        </div>

        {{--<div class="form-group">--}}
            {{--<label>商品评分</label>--}}
            {{--<input type="text" class="form-control" placeholder="评分" name="rating" value="{{"$menu->rating"}}">--}}
        {{--</div>--}}

        {{--<div class="form-group">--}}
            {{--<label>商品所属商家</label>--}}
            {{--<input type="text" class="form-control" placeholder="所属商家" name="information_id" value="{{"$menu->information_id"}}">--}}
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
            <input type="text" class="form-control" placeholder="价格" name="goods_price" value="{{"$menu->goods_price"}}">
        </div>

        <div class="form-group">
            <label>商品描述</label>
            <textarea name="description" cols="30" rows="5" class="form-control">{{"$menu->description"}}</textarea>

            {{--<div class="form-group">--}}
                {{--<label>商品月销量</label>--}}
                {{--<input type="text" class="form-control" placeholder="月销量" name="month_sales" value="{{"$menu->month_sales"}}">--}}
            {{--</div>--}}

            {{--<div class="form-group">--}}
                {{--<label>商品评分数量</label>--}}
                {{--<input type="text" class="form-control" placeholder="评分数量" name="rating_count" value="{{"$menu->rating_count"}}">--}}
            {{--</div>--}}

            <div class="form-group">
                <label>提示信息</label>
                <input type="text" class="form-control" placeholder="提示信息" name="tips" value="{{"$menu->tips"}}">
            </div>

            {{--<div class="form-group">--}}
                {{--<label>满意度数量</label>--}}
                {{--<input type="text" class="form-control" placeholder="满意度数量" name="satisfy_count" value="{{"$menu->satisfy_count"}}">--}}
            {{--</div>--}}

            {{--<div class="form-group">--}}
                {{--<label>满意度评分</label>--}}
                {{--<input type="text" class="form-control" placeholder="满意度评分" name="satisfy_rate" value="{{"$menu->satisfy_rate"}}">--}}
            {{--</div>--}}

            {{--<div class="form-group">--}}
                {{--<label>商品图片</label>--}}
                {{--<input type="file" name="goods_img">--}}
                {{--<p class="help-block">请选择你的商品图片</p>--}}
            {{--</div>--}}

            <div class="form-group">
                <label>商品图片</label>

                <input type="text" name="goods_img" value="" id="goods_img">
                <!--dom结构部分-->
                <div id="uploader-demo">
                    <!--用来存放item-->
                    <div id="fileList" class="uploader-list"></div>
                    <div id="filePicker">选择图片</div>
                </div>
            </div>

            <div class="checkbox">
                <label>
                    <input type="radio" name="status"> 上架
                    <input type="radio" name="status"> 下架
                </label>
            </div>

            <button type="submit" class="btn btn-default">修改</button>
    </form>
@endsection

@section("js")
    <script>
        // 图片上传demo
        jQuery(function () {
            var $ = jQuery,
                $list = $('#fileList'),
                // 优化retina, 在retina下这个值是2
                ratio = window.devicePixelRatio || 1,

                // 缩略图大小
                thumbnailWidth = 100 * ratio,
                thumbnailHeight = 100 * ratio,

                // Web Uploader实例
                uploader;

            // 初始化Web Uploader
            uploader = WebUploader.create({

                // 自动上传。
                auto: true,

                formData: {
                    // 这里的token是外部生成的长期有效的，如果把token写死，是可以上传的。
                    _token:'{{csrf_token()}}'
                },


                // swf文件路径
                swf: '/webuploader/Uploader.swf',

                // 文件接收服务端。
                server: '{{route("shop.menu.upload")}}',

                // 选择文件的按钮。可选。
                // 内部根据当前运行是创建，可能是input元素，也可能是flash.
                pick: '#filePicker',

                // 只允许选择文件，可选。
                accept: {
                    title: 'Images',
                    extensions: 'gif,jpg,jpeg,bmp,png',
                    mimeTypes: 'image/*'
                }
            });

            // 当有文件添加进来的时候
            uploader.on('fileQueued', function (file) {
                var $li = $(
                    '<div id="' + file.id + '" class="file-item thumbnail">' +
                    '<img>' +
                    '<div class="info">' + file.name + '</div>' +
                    '</div>'
                    ),
                    $img = $li.find('img');

                $list.html($li);

                // 创建缩略图
                uploader.makeThumb(file, function (error, src) {
                    if (error) {
                        $img.replaceWith('<span>不能预览</span>');
                        return;
                    }

                    $img.attr('src', src);
                }, thumbnailWidth, thumbnailHeight);
            });

            // 文件上传过程中创建进度条实时显示。
            uploader.on('uploadProgress', function (file, percentage) {
                var $li = $('#' + file.id),
                    $percent = $li.find('.progress span');

                // 避免重复创建
                if (!$percent.length) {
                    $percent = $('<p class="progress"><span></span></p>')
                        .appendTo($li)
                        .find('span');
                }

                $percent.css('width', percentage * 100 + '%');
            });

            // 文件上传成功，给item添加成功class, 用样式标记上传成功。
            uploader.on('uploadSuccess', function (file,data) {
                $('#' + file.id).addClass('upload-state-done');

                $("#goods_img").val(data.url);
            });

            // 文件上传失败，现实上传出错。
            uploader.on('uploadError', function (file) {
                var $li = $('#' + file.id),
                    $error = $li.find('div.error');

                // 避免重复创建
                if (!$error.length) {
                    $error = $('<div class="error"></div>').appendTo($li);
                }

                $error.text('上传失败');
            });

            // 完成上传完了，成功或者失败，先删除进度条。
            uploader.on('uploadComplete', function (file) {
                $('#' + file.id).find('.progress').remove();
            });
        });
    </script>
@stop