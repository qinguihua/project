@extends("admin.layouts.main")
@section("title","添加活动")
@section("content")


    <form method="post" enctype="multipart/form-data" class="table table-striped">
        {{ csrf_field() }}
        <div class="form-group">
            <label>活动标题</label>
            <input type="text" class="form-control" placeholder="活动标题" name="title" value="{{old("title")}}">
        </div>

        <div class="form-group">
            <label>活动开始时间</label>
            <input type="datetime-local" class="form-control" placeholder="活动开始时间" name="start_time" value="{{old("start_time")}}">
        </div>

        <div class="form-group">
            <label>活动结束时间</label>
            <input type="datetime-local" class="form-control" placeholder="活动结束时间" name="end_time" value="{{old("end_time")}}">
        </div>

        <div class="form-group">
            <label>活动内容</label>
            <script id="container" name="content" type="text/plain"></script>
        </div>

        <button type="submit" class="btn btn-default">添加</button>
    </form>
@endsection

<!-- 实例化编辑器 -->
@section("js")
    <script type="text/javascript">
        var ue = UE.getEditor('container');
        ue.ready(function() {
            ue.execCommand('serverparam', '_token', '{{ csrf_token() }}'); // 设置 CSRF token.
        });
    </script>
@endsection
