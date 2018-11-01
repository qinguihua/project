<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="/shop/dist/img/user1-128x128.jpg" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">

                <a href="#"><i class="fa fa-circle text-success"></i>Online</a>
            </div>
        </div>
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">菜单管理</li>
            {{--<li><a href="http://shop.ele.com/menu_caegory/index"t><i class="fa fa-book"></i> <span>菜品分类管理</span></a></li>--}}

            <li class="treeview">
                <a href="">
                    <i class="fa fa-dashboard"></i><span>菜品分类管理</span>
                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{route('shop.menu_category.index')}}"><i class="fa fa-circle-o"></i>菜品分类列表</a></li>
                </ul>
            </li>

            <li class="treeview">
                <a href="{{route('shop.menu.index')}}">
                    <i class="fa fa-dashboard"></i><span>菜品</span>
                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{route('shop.menu.add')}}"><i class="fa fa-circle-o"></i>添加菜品</a></li>
                    <li><a href="{{route('shop.menu.index')}}"><i class="fa fa-circle-o">查看菜品</i></a></li>
                </ul>
            </li>

            <li class="header">活动</li>
            <li><a href="{{route('shop.user.show')}}"><i class="fa fa-circle-o text-red"></i> <span>查看活动</span></a></li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>