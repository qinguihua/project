<nav class="navbar navbar-inverse">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">ELE点餐系统</a>
        </div>


        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">


            <ul class="nav navbar-nav">
                <li class="active"><a href="{{route("admin.nav.index")}}">首页 <span class="sr-only">(current)</span></a></li>
                @foreach(\App\Models\Nav::where("pid",0)->get() as $k1=>$v1)
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                           aria-expanded="false">{{$v1->name}} <span class="caret"></span></a>
                        <ul class="dropdown-menu">

                            @foreach(\App\Models\Nav::where("pid",$v1->id)->get() as $k2=>$v2)
                                <li><a href="{{route($v2->url)}}">{{$v2->name}}</a></li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>



            {{--<ul class="nav navbar-nav navbar-left">--}}
                {{--<li class="dropdown">--}}
                    {{--<a href="{{route('admin.category.index')}}" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">店铺分类<span class="caret"></span></a>--}}
                    {{--<ul class="dropdown-menu">--}}
                        {{--<li><a href="{{route('admin.category.add')}}">添加分类</a></li>--}}
                        {{--<li role="separator" class="divider"></li>--}}
                        {{--<li><a href="{{route('admin.category.index')}}">分类列表</a></li>--}}
                    {{--</ul>--}}
                {{--</li>--}}
            {{--</ul>--}}

            {{--<ul class="nav navbar-nav navbar-left">--}}
                {{--<li class="dropdown">--}}
                    {{--<a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">店铺信息管理<span class="caret"></span></a>--}}
                    {{--<ul class="dropdown-menu">--}}
                        {{--<li><a href="{{route('admin.information.add')}}">添加商家信息</a></li>--}}
                        {{--<li role="separator" class="divider"></li>--}}
                        {{--<li><a href="{{route('admin.information.index')}}">店铺信息列表</a></li>--}}
                    {{--</ul>--}}
                {{--</li>--}}
            {{--</ul>--}}

            {{--<ul class="nav navbar-nav navbar-left">--}}
                {{--<li class="dropdown">--}}
                    {{--<a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">商户管理<span class="caret"></span></a>--}}
                    {{--<ul class="dropdown-menu">--}}
                        {{--<li><a href="{{route('admin.user.reg')}}">添加商户</a></li>--}}
                        {{--<li role="separator" class="divider"></li>--}}
                        {{--<li><a href="{{route('admin.user.index')}}">商户列表</a></li>--}}
                    {{--</ul>--}}
                {{--</li>--}}
            {{--</ul>--}}

            {{--<ul class="nav navbar-nav navbar-left">--}}
                {{--<li class="dropdown">--}}
                    {{--<a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">管理员<span class="caret"></span></a>--}}
                    {{--<ul class="dropdown-menu">--}}
                        {{--<li><a href="{{route('admin.admin.index')}}">管理员列表</a></li>--}}
                    {{--</ul>--}}
                {{--</li>--}}
            {{--</ul>--}}

            <ul class="nav navbar-nav navbar-right">
             @auth("admin")
                <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            欢迎{{\Illuminate\Support\Facades\Auth::guard("admin")->user()->name}} <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{route("admin.admin.changepwd")}}">修改密码</a></li>

                            <li role="separator" class="divider"></li>
                            <li><a href="{{route("admin.admin.logout")}}">注销</a></li>
                        </ul>
                    </li>
                @endauth
                @guest("admin")
                    <li><a href="{{route("admin.admin.login")}}">登录</a></li>
                @endguest

            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>