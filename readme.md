# ELE点餐平台

## 项目介绍

整个系统分为三个不同的网站，分别是

- 平台：网站管理者
- 商户：入住平台的餐馆
- 用户：订餐的用户

## Day01

### 开发任务

#### 平台端

- 商家分类管理
- 商家管理
- 商家审核

#### 商户端

- 商家注册

#### 要求

- 商家注册时，同步填写商家信息，商家账号和密码
- 商家注册后，需要平台审核通过，账号才能使用
- 平台可以直接添加商家信息和账户，默认已审核通过

### 实现步骤

1.安装

```php
composer create-project --prefer-dist laravel/laravel ele0620 "5.5.*" -vvv
```

2.设置虚拟主机并设置三个域名,将域名添加到hosts文件

```php
<VirtualHost *:80>
    DocumentRoot "D:\laravel\project\ele\public"
    ServerName www.ele.com
    ServerAlias shop.ele.com admin.ele.com
  <Directory "D:\laravel\project\ele\public">
      Options Indexes  FollowSymLinks ExecCGI
      AllowOverride All
      Order allow,deny
      Allow from all
      Require all granted
  </Directory>
</VirtualHost>
```

3.创建数据库ele

4.修改配置文件.env

```php
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ele
DB_USERNAME=root
DB_PASSWORD=root
```

5.安装语言包，设置中文

```php
# 安装 larave-ide-helper

composer require barryvdh/laravel-ide-helper

# 安装 doctrine/dbal 

composer require "doctrine/dbal: ~2.3"

# 导出配置文件

php artisan vendor:publish --provider="Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider" --tag=config

# 修改配置文件

 config/ide-helper.php 28行 找到include_fluent修改其值为true  

# 执行如下命令
    
#为 Facades 生成注释 
php artisan ide-helper:generate 
#为数据模型生成注释
php artisan ide-helper:models 
#生成 PhpStorm Meta file
php artisan ide-helper:meta 
```

6.布局模板，分admin和shop两个目录，分别在其目录下复制layouts

7.数据迁移

   修改app/Providers/AppServiceProvider.php 文件

```php
<?php

namespace App\Providers;

use Doctrine\DBAL\Schema\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

```

执行数据迁移

```PHP
php artisan migrate
```

8.将user放到Models文件

9.创建注册控制器

```php
php artisan make:controller User/RegController
```

10.生成基础控制器

```php
php artisan make:controller User/BaseController
```

> 所有控制器继承BaseController

# 注册

## 验证码

```php
1.执行 composer require mews/captcha -vvv    
2.配置 php artisan vendor:publish  选择7    
```

## 加密  安装Debug

```php
composer require barryvdh/laravel-debugbar -vvv
```

### 视图

```html
@extends("shop.layouts.main")
@section("title","商家注册")
@section("content")

    <form class="form-horizontal" method="post">

        {{csrf_field()}}

        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">用户名</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="inputEmail3" placeholder="用户名" name="name">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">密码</label>
            <div class="col-sm-10">
                <input type="password" class="form-control" id="inputPassword3" placeholder="密码" name="password">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">邮箱</label>
            <div class="col-sm-10">
                <input type="email" class="form-control" id="inputEmail3" placeholder="邮箱" name="email">
            </div>
        </div>
        <div class="form-group">
            <label  class="col-sm-2 control-label">验证码</label>
            <div class="col-sm-10">
                <input id="captcha" class="form-control" name="captcha" placeholder="验证码">
                <img class="thumbnail captcha" src="{{captcha_src('flat')}}" onclick="this.src='/captcha/flat?'+Math.random()" title="点击图片重新获取验证码">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-default">注册</button>
            </div>
        </div>
    </form>


@endsection
```

### 控制器

```php
 public function reg(Request $request){
        //判断提交方式
        if ($request->isMethod("post")){
            //验证
            $this->validate($request, [
                "name" => "required|unique:users",
                "password" => "required|min:6",
                "captcha" => "required|captcha"
                  ],[
                "captcha.required" => '验证码不能为空',
                "captcha.captcha" => '验证码有误',
            ]);
            //接收数据
            $data=$request->post();
            //密码加密
            $data['password'] = bcrypt($data['password']);
            //添加
            if (User::create($data)){
                //跳转
                return redirect()->route("shop.user.login")->with("success","注册成功");
            }

        }else{
            //显示视图
            return view("shop.user.reg");
        }
    }
```

### 路由

```php
Route::domain("www.ele.com")->namespace("Shop")->group(function (){

    //商户注册
    Route::any("user/reg","RegController@reg")->name("shop.user.reg");
   

});
```

# 登录

### 视图

```html
@extends("shop.layouts.main")
@section("title","商家登录")
@section("content")

    <form class="form-horizontal" method="post">

        {{csrf_field()}}

        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">用户名</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="inputEmail3" placeholder="用户名" name="name">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">密码</label>
            <div class="col-sm-10">
                <input type="password" class="form-control" id="inputPassword3" placeholder="密码" name="password">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <div class="checkbox">
                    <label>
                        <input type="checkbox">记住密码
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-default">登录</button>
            </div>
        </div>
    </form>


@endsection
```

### 控制器

```php
//登录

        public function login(Request $request){
            //判断提交方式
            if ($request->isMethod("post")){
                //验证
                $data=$this->validate($request, [
                    "name" => "required",
                    "password" => "required"
                ]);
                //验证账号和密码是否正确
                if(Auth::attempt($data,$request->has("remeber"))){
                    //登录成功
                    return redirect()->intended(route("shop.user.index"))->with("success","登录成功");
                }else{
                    //登录失败
                    return redirect()->back()->withInput()->with("danger","账号或密码错误");
                }
            }else{
                //显示视图
                return view("shop.user.login");
            }
    }
```

### 路由

```php
<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::domain("www.ele.com")->namespace("Shop")->group(function (){

    //商户注册
    Route::any("user/reg","RegController@reg")->name("shop.user.reg");
    //商户登录
    Route::any("user/login","RegController@login")->name("shop.user.login");
    //后台首页
    Route::any("user/index","RegController@index")->name("shop.user.index");

});
```

## 商家信息

创建模型

```php
php artisan make:model Models/ShopInformation -m
```

创建表与字段，并数据迁移

```php
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_informations', function (Blueprint $table) {
            $table->increments('id');

            $table->integer("shop_category_id")->comment("店铺分类ID");
            $table->string("shop_name")->comment("店铺名称");
            $table->string("shop_img")->comment("店铺图片");
            $table->float("shop_rating")->comment("评分");
            $table->boolean("brand")->comment("是否是品牌");
            $table->boolean("on_time")->comment("是否准时送达");
            $table->boolean("fengniao")->comment("是否蜂鸟配送");
            $table->boolean("bao")->comment("是否保标记");
            $table->boolean("piao")->comment("是否票标记");
            $table->boolean("zhun")->comment("是否准标记");
            $table->decimal("start_send")->comment("起送金额");
            $table->decimal("send_cost")->comment("配送费");
            $table->string("notice")->comment("店公告");
            $table->string("discount")->comment("优惠信息");
            $table->integer("status")->comment("状态：1 正常 0待审核 -1禁用");
            $table->integer("user_id")->comment("商家id");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_informations');
    }
}

```

数据迁移

```
php artisan migrate
```

创建控制器

```
php artisan make:controller shop/ShopInformationController
```

### 添加

### 图片上传

修改配置文件 config/filesystems.php

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'image' => [
            'driver' => 'local',
            'root' => public_path(),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
        ],

    ],

];

```

> 前端视图 input类型file 提交方式post enctype为form格式

#### 控制器

```php
<?php

namespace App\Http\Controllers\shop;

use App\Models\ShopCategory;
use App\Models\ShopInformation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShopInformationController extends Controller
{
    //显示所有商家信息
    public function index(){
        $informations=ShopInformation::all();
        $results=ShopCategory::all();
        //显示视图并传递数据
        return view("shop.information.index",compact("informations","results"));
    }

    //添加
    public function add(Request $request){

        if($request->isMethod("post")){

            //验证，如果没有通过验证，就返回添加页面
            $this->validate($request,[
                "shop_category_id"=>"required",
                "shop_name"=>"required",
                "shop_img"=>"required",
                "start_send"=>"required",
                "send_cost"=>"required",
                "notice"=>"required",
                "discount"=>"required",
            ]);

            //接收数据
            $data=$request->post();

            $data['on_time']=$request->has('on_time')?'1':'0';
            $data['brand']=$request->has('brand')?'1':'0';
            $data['fengniao']=$request->has('fengniao')?'1':'0';
            $data['bao']=$request->has('bao')?'1':'0';
            $data['piao']=$request->has('piao')?'1':'0';
            $data['zhun']=$request->has('zhun')?'1':'0';


//            dd($data);
            //上传图片
            $data['shop_img']=$request->file("shop_img")->store("images","image");

            //将数据入库
            if(ShopInformation::create($data)){
                //跳转
                return redirect()->intended(route("shop.information.index"))->with("success","添加成功");
            }

        }else{

            $results=ShopCategory::all();
            //显示视图并传递数据
            return view("shop.information.add",compact("results"));

        }
    }

    //删除
    public function del($id){

        $information=ShopInformation::find($id);

        if($information->delete()){
            //跳转
            return redirect()->intended(route("shop.information.index"))->with("success","删除成功");
        }

    }

}
```

#### 视图(index)

```php
@extends("shop.layouts.main")
@section("title","店铺信息")
@section("content")

<a href="{{route("shop.information.add")}}" class="btn btn-info">添加</a>
    <table class="table table-hover">
        <tr>
            <th>id</th>
            <th>分类</th>
            <th>名称</th>
            <th>图片</th>
            <th>评分</th>
            <th>是否品牌</th>
            <th>准时送达</th>
            <th>是否蜂鸟配送</th>
            <th>保标记</th>
            <th>票标记</th>
            <th>准标记</th>
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
            <td>{{$information->shop_category_id}}</td>
            <td>{{$information->shop_name}}</td>
            <td><img src="/{{$information->shop_img}}" alt="" width="100px"></td>
            <td>{{$information->shop_rating}}</td>
            <td>{{$information->brand}}</td>
            <td>{{$information->on_time}}</td>
            <td>{{$information->fengniao}}</td>
            <td>{{$information->bao}}</td>
            <td>{{$information->piao}}</td>
            <td>{{$information->zhun}}</td>
            <td>{{$information->start_send}}</td>
            <td>{{$information->send_cost}}</td>
            <td>{{$information->notice}}</td>
            <td>{{$information->discount}}</td>
            <td>{{$information->user_id}}</td>
            <td>
                <a href="{{route("shop.information.check",$information->id)}}" class="btn btn-success">审核</a>
                <a href="{{route("shop.information.del",$information->id)}}" class="btn btn-danger">删除</a>
            </td>
        </tr>
       @endforeach
    </table>

@endsection
```

#### add

```php
@extends("layouts.main")
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
```

### 其他表同上的增删改查

## 后台登录

#### 登录

```php
//登录

    public function login(Request $request){
        //判断提交方式
        if ($request->isMethod("post")){
            //验证
            $data=$this->validate($request, [
                "name" => "required",
                "password" => "required"
            ]);
//            dd($data);
            //验证账号和密码是否正确
            if (Auth::guard("admin")->attempt($data,$request->has("remember"))){
                //登录成功
                return redirect()->intended(route("admin.information.index"))->with("success","登录成功");
            }else{
                //登录失败
                return redirect()->back()->withInput()->with("danger","账号或密码错误");
            }
        }else{
            //显示视图
            return view("admin.admin.login");
        }
    }
```

#### 视图

```html
@extends("admin.layouts.main")
@section("title","管理员登录")
@section("content")

    <form class="form-horizontal" method="post">

        {{csrf_field()}}

        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">用户名</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="inputEmail3" placeholder="用户名" name="name">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">密码</label>
            <div class="col-sm-10">
                <input type="password" class="form-control" id="inputPassword3" placeholder="密码" name="password">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <div class="checkbox" >
                    <label>
                        <input type="checkbox">记住密码
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-default">登录</button>
            </div>
        </div>
    </form>


@endsection
```

#### 导航条

```php
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
                <li class="active"><a href="#">首页<span class="sr-only">(current)</span></a></li>
                <li><a href="{{route('admin.category.index')}}">商家分类</a></li>
                <li><a href="{{route('shop.information.index')}}">商家信息</a></li>
                <li><a href="{{route('shop.user.home')}}">商家管理</a></li>
                <li><a href="{{route('admin.admin.index')}}">管理员管理</a></li>
            </ul>
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
```



#### 注销

```php
//注销
    public function logout(){
        Auth::guard("admin")->logout();
        //跳转
        return redirect()->route("admin.admin.login")->with("success","退出成功");
    }
```

#### 修改密码

```php
//更改密码
    public function changepwd(Request $request){

        //得到当前用户
        $admin=Auth::guard("admin")->user();

//        dd($admin);
        //判断提交方式
        if ($request->isMethod("post")){

            //验证
            $this->validate($request,[
                "old_password"=>"required",
                "password"=>"required|confirmed"
            ]);


            $oldpassword=$request->post("old_password");
            //判断旧密码是否正确
            if(Hash::check($oldpassword,$admin->password)){
                //设置新密码
                $admin->password=Hash::make($request->post("password"));
                //保存修改
                $admin->save();
                //跳转
                return redirect()->route("admin.admin.index")->with("success","修改成功");
            }
            //旧密码不正确
            return back()->with("danger", "旧密码不正确");

        }else{

            //显示视图
            return view("admin.admin.changepwd",compact("admin"));

        }
    }
```

#### 修改密码视图

```html
@extends("admin.layouts.main")
@section("title","修改密码")
@section("content")



    <form method="post"  class="table table-striped">
        {{ csrf_field() }}
        <div class="form-group">
            <label>用户名</label>
            <input class="form-control" type="text"  name="name" value="{{$admin->name}}" readonly>
        </div>
        <div class="form-group">
            <label>原密码</label>
            <input type="password" class="form-control" name="old_password">
        </div>
        <div class="form-group">
            <label>新密码</label>
            <input type="password" class="form-control" name="password">
        </div>
        <div class="form-group">
            <label>确认密码</label>
            <input type="password" class="form-control" name="password_confirmation">
        </div>
        <button type="submit" class="btn btn-default">修改</button>
    </form>
@endsection
```

### 商家审核

#### 视图

```html
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
```

#### 控制器

```php
//审核
    public function check($id){
//        $id =Auth::id();
//        dd($id);
       $information=ShopInformation::findOrFail($id);
//       dd($information);
       $information->status=1;
       $information->save();
       return back()->with("success","通过审核");
    }
```

## Day02

### 开发任务

- 完善day1的功能，要用事务保证同时删除用户和店铺，删除图片
- 平台：平台管理员账号管理
- 平台：管理员登录和注销功能，修改个人密码(参考微信修改密码功能)
- 平台：商户账号管理，重置商户密码
- 商户端：商户登录和注销功能，修改个人密码
- 修改个人密码需要用到验证密码功能,[参考文档](https://laravel-china.org/docs/laravel/5.5/hashing)
- 商户登录正常登录，登录之后判断店铺状态是否为1，不为1不能做任何操作

### 实现步骤

1. 在商户端口和平台端都要创建BaseController 以后都要继承自己的BaseController

2. 商户的登录和以前一样

3. 平台的登录，模型中必需继承 use Illuminate\Foundation\Auth\User as Authenticatable

4. 设置配置文件config/auth.php

   ## 同时删除用户和店铺

   ### 控制器

   ```php
    //删除
       public function del($id){
   
           $information=ShopInformation::findOrFail($id);
   
           if($information->delete()){
               //跳转
               return redirect()->route("shop.information.index")->with("success","删除成功");
           }
   
       }
   
   ```

   ### 视图

   ```html
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
   ```

   ### 完善登录和修改个人密码

   ```php
   <?php
   
   namespace App\Http\Controllers\Shop;
   
   use App\Models\User;
   use Illuminate\Http\Request;
   use App\Http\Controllers\Controller;
   use Illuminate\Support\Facades\Auth;
   use Illuminate\Support\Facades\Hash;
   
   class RegController extends BaseController
   {
       //
       public function reg(Request $request){
           //判断提交方式
           if ($request->isMethod("post")){
               //验证
               $this->validate($request, [
                   "name" => "required|unique:users",
                   "password" => "required|min:6",
                   "captcha" => "required|captcha"
                     ],[
                   "captcha.required" => '验证码不能为空',
                   "captcha.captcha" => '验证码有误',
               ]);
               //接收数据
               $data=$request->post();
               //密码加密
               $data['password'] = bcrypt($data['password']);
               //添加
               if (User::create($data)){
                   //跳转
                   return redirect()->route("shop.user.login")->with("success","注册成功");
               }
   
           }else{
               //显示视图
               return view("shop.user.reg");
           }
       }
   
       //登录
   
           public function login(Request $request){
               //判断提交方式
               if ($request->isMethod("post")){
                   //验证
                   $data=$this->validate($request, [
                       "name" => "required",
                       "password" => "required"
                   ]);
                   //验证账号和密码是否正确
                   if(Auth::attempt($data,$request->has("remeber"))){
   
                       //当前登录用户的id
                       $user=Auth::user();
                       $shop=$user->information;
                       //通过用户找到店铺
                       if ($shop){
                           //如果有店铺
                           switch ($shop->status){
                               //禁用
                               case -1:
                                   Auth::logout();
                                   return back()->withInput()->with("danger","店铺已被禁用！");
                                   break;
                               //未审核
                               case 0:
                                   Auth::logout();
                                   return back()->withInput()->with("danger","店铺等待审核中!");
                                   break;
                           }
                       }
   
                       //登录成功
                       if (Auth::user()->information==null) {
                           return redirect()->intended(route("shop.information.add"))->with("success","登录成功,欢迎申请店铺");
                       }else{
                           return redirect()->intended(route("shop.user.index"))->with("success","登录成功");
                       }
   
                   }else{
                       //登录失败
                       return redirect()->back()->withInput()->with("danger","账号或密码错误");
                   }
               }else{
                   //显示视图
                   return view("shop.user.login");
               }
       }
   
   
       //更改密码
       public function change_pwd(Request $request){
   
           //得到当前用户
           $user=Auth::guard("web")->user();
   
   //        dd($user);
           //判断提交方式
           if ($request->isMethod("post")){
   
               //验证
               $this->validate($request,[
                   "old_password"=>"required",
                   "password"=>"required|confirmed"
               ]);
   
   
               $oldpassword=$request->post("old_password");
               //判断旧密码是否正确
               if(Hash::check($oldpassword,$user->password)){
                   //设置新密码
                   $user->password=Hash::make($request->post("password"));
   //                $user->password=Hash::make($request->post("password"));
                   //保存修改
                   $user->save();
                   //跳转
                   return redirect()->route("admin.admin.index")->with("success","修改成功");
               }
               //旧密码不正确
               return back()->with("danger", "旧密码不正确");
   
           }else{
   
               //显示视图
               return view("shop.user.change_pwd",compact("user"));
   
           }
       }
   
   
       //注销
       public function logout(){
           Auth::guard("web")->logout();
           //跳转
           return redirect()->route("shop.user.login")->with("success","退出成功");
       }
   
   
       //后台首页
       public function index(){
   //        if (Auth::user()->information===null) {
   ////            return redirect()->route("shop.information.add")->with("danger","你还没有商铺呢！快点加入我们吧！！");
   ////        }
           //显示视图
           return view("shop.user.index");
   
       }
   
   
   
   }
   
   ```

   ```html
   @extends("shop.layouts.main")
   @section("title","修改密码")
   @section("content")
   
   
   
       <form method="post"  class="table table-striped">
           {{ csrf_field() }}
           <div class="form-group">
               <label>用户名</label>
               <input class="form-control" type="text"  name="name" value="{{$user->name}}" readonly>
           </div>
           <div class="form-group">
               <label>原密码</label>
               <input type="password" class="form-control" name="old_password">
           </div>
           <div class="form-group">
               <label>新密码</label>
               <input type="password" class="form-control" name="password">
           </div>
           <div class="form-group">
               <label>确认密码</label>
               <input type="password" class="form-control" name="password_confirmation">
           </div>
           <button type="submit" class="btn btn-default">修改</button>
       </form>
   @endsection
   ```

   ### 导航条

   ```php
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
                   <li class="active"><a href="{{route('shop.user.index')}}">首页<span class="sr-only">(current)</span></a></li>
                   <li><a href="#">商家分类</a></li>
                   <li><a href="#">商家信息</a></li>
                   <li><a href="#">商家管理</a></li>
                   <li><a href="#">管理员管理</a></li>
               </ul>
               <ul class="nav navbar-nav navbar-right">
                   @auth("web")
                       <li class="dropdown">
                           <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                               欢迎{{\Illuminate\Support\Facades\Auth::guard("web")->user()->name}} <span class="caret"></span></a>
                           <ul class="dropdown-menu">
                               <li><a href="{{route("shop.user.change_pwd")}}">修改密码</a></li>
   
                               <li role="separator" class="divider"></li>
                               <li><a href="{{route("shop.user.logout")}}">注销</a></li>
                           </ul>
                       </li>
                   @endauth
                   @guest("web")
                       <li><a href="{{route("shop.user.login")}}">登录</a></li>
                   @endguest
   
               </ul>
           </div><!-- /.navbar-collapse -->
       </div><!-- /.container-fluid -->
   </nav>
   ```

   ## DAY03

   ### 开发任务

   #### 商户端 

   - 菜品分类管理 
   - 菜品管理 

   #### 要求

   - 一个商户只能有且仅有一个默认菜品分类 
   - 只能删除空菜品分类 
   - 必须登录才能管理商户后台（使用中间件实现） 
   - 可以按菜品分类显示该分类下的菜品列表 
   - 可以根据条件（按菜品名称和价格区间）搜索菜品

## 菜品分类的增删改查

```php
<?php

namespace App\Http\Controllers\shop;

use App\Models\Menu_category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MenuCategoryController extends BaseController
{
    //
    public function index(){

        $menu_categorys=Menu_category::all();
        //显示视图并传递数据
        return view("shop.menu_category.index",compact("menu_categorys"));

    }

    public function add(Request $request){
        //判断提交方式
        if ($request->isMethod("post")){

            //验证
            $this->validate($request,[
                'name'=>'required|unique',
                'description'=>'required',
                'is_selected'=>'required',
            ]);

            //接收数据
            $data=$request->post();
            $shopId = Auth::user()->shop_information->id;
            $shopId=$data["information_id"];
            //判断
            if($request->post("is_selected")){
                //把所有的is_selected设置为0
                Menu_category::where("is_selected",1)->where("information_id",$shopId)->update(["is_selected"=>0]);
            }
            //数据入库
            if (Menu_category::create($data)){
                //跳转
                return redirect()->route("shop.menu_category.index")->with("success","添加成功");
            }

        }else{

            //显示视图
            return view("shop.menu_category.add");

        }
    }

    //修改
    public function edit(Request $request,$id){

        //通过id得到对象
        $menu_category=Menu_category::find($id);
        //判断提交方式
        if ($request->isMethod("post")){

            //接收数据
            $data=$request->post();

            if ($menu_category->update($data)){
                return redirect()->route("shop.menu_category.index")->with("success","修改成功");
            }

        }else{
            //显示视图并传数据
            return view("shop.menu_category.edit",compact("menu_category"));
        }


    }


    //删除
    public function del($id){

        $menu_category=Menu_category::find($id);

        if ($menu_category->delete()){

            return redirect()->route("shop.menu_category.index")->with("success","删除成功");
        }

    }

}
```

### 视图

```html
@extends("shop.layouts.main")
@section("title","菜品分类")
@section("content")

<a href="{{route("shop.menu_category.add")}}" class="btn btn-info">添加</a>
    <table class="table table-hover">
        <tr>
            <th>id</th>
            <th>名称</th>
            <th>所属商家</th>
            <th>简介</th>
            <th>操作</th>
        </tr>
        @foreach($menu_categorys as $menu_category)
        <tr>
            <td>{{$menu_category->id}}</td>
            <td>{{$menu_category->name}}</td>
            <td>{{$menu_category->information_id}}</td>
            <td>{{$menu_category->description}}</td>
            <td>
                <a href="{{route("shop.menu_category.edit",$menu_category->id)}}" class="btn btn-success">编辑</a>
                <a href="{{route("shop.menu_category.del",$menu_category->id)}}" class="btn btn-danger">删除</a>
            </td>
        </tr>
       @endforeach
    </table>

@endsection
```

### 菜品的增删改查

```php
<?php

namespace App\Http\Controllers\shop;

use App\Models\Menu;
use App\Models\Menu_category;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MenuController extends BaseController
{
    //
    public function index(){

        $menus=Menu::all();
        //显示视图并传递数据
        return view("shop.menu.index",compact("menus"));

    }

    public function add(Request $request){
        //判断提交方式
        if ($request->isMethod("post")){

            //接收数据
            $data=$request->post();
//            $shopId = Auth::user()->shop_information->id;
            $shopId=$data["information_id"];

            $data['status']=$request->has('status')?'1':'0';
            //上传图片
            $data['goods_img']=$request->file("goods_img")->store("images","image");
            //数据入库
            if (Menu::create($data)){
                //跳转
                return redirect()->route("shop.menu.index")->with("success","添加成功");
            }

        }else{
            $results=MenuCategory::all();
            //dd($results);
            //显示视图
            return view("shop.menu.add",compact("results"));

        }
    }

    //修改
    public function edit(Request $request,$id){

        //通过id得到对象
        $menu=Menu::find($id);
        //判断提交方式
        if ($request->isMethod("post")){

            //接收数据
            $data=$request->post();
            $data['status']=$request->has('status')?'1':'0';

            //判断是否重新上传图片
            if($request->file("goods_img")!==null){
                $data['goods_img']=$request->file("goods_img")->store("images","image");
            }else{
                $data['goods_img']=$menu->goods_img;
            }

            if ($menu->update($data)){
                return redirect()->route("shop.menu.index")->with("success","修改成功");
            }

        }else{
            //显示视图并传数据
            $results=MenuCategory::all();
            return view("shop.menu.edit",compact("menu","results"));
        }


    }


    //删除
    public function del($id){

        $menu=Menu::find($id);

        if ($menu->delete()){

            return redirect()->route("shop.menu.index")->with("success","删除成功");
        }

    }

}

```

### 视图

```html
@extends("shop.layouts.main")
@section("title","菜品分类")
@section("content")

<a href="{{route("shop.menu.add")}}" class="btn btn-info">添加</a>
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
            <td><img src="/{{$menu->goods_img}}" alt="" width="100"></td>
            <td>
                <a href="{{route("shop.menu.edit",$menu->id)}}" class="btn btn-success">编辑</a>
                <a href="{{route("shop.menu.del",$menu->id)}}" class="btn btn-danger">删除</a>
            </td>
        </tr>
       @endforeach
    </table>

@endsection
```

### 不能删除有菜品的分类

```php
 //删除
    public function del($id){


        //得到当前分类
        $cate=MenuCategory::findOrFail($id);
        //得到当前分类对应的菜品数
        $shopCount=Menu::where('category_id',$cate->id)->count();
        //判断当前分类菜品数
        if ($shopCount){
            //回跳
            return  back()->with("danger","当前分类下有菜品，不能删除");
        }
        //否则删除
        $cate->delete();
        //跳转
        return redirect()->route('shop.menu_category.index')->with('success',"删除成功");
    }
```

### 搜索并分页

```php
 public function index(Request $request){
        
        $url=$request->query();

        // 接收数据
        $categoryId = $request->get("category_id");
        $goods_name=$request->get("goods_name");
        $maxPrice=$request->get("maxPrice");
        $minPrice=$request->get("minPrice");

        //得到所有并分页
        $query = Menu::orderBy("id");
        if ($categoryId!==null) {
            $query->where("category_id",$categoryId);
        }

        //按菜品名搜索
        if ($goods_name!==null){

            $query->where("title","like","%{$goods_name}%");
        }
        //按价格区间搜索
        if ($maxPrice!=0 && $minPrice!=0){
            $query->where("goods_price",">=","$minPrice");
            $query->where("goods_price","<=","$maxPrice");
        }

        $menus=$query->paginate(2);


        //显示视图并传递数据
        $results=MenuCategory::all();
        return view("shop.menu.index",compact("menus","results","url"));

    }

```

### 视图

```html
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
            <td><img src="/{{$menu->goods_img}}" alt="" width="100"></td>
            <td>
                <a href="{{route("shop.menu.edit",$menu->id)}}" class="btn btn-success">编辑</a>
                <a href="{{route("shop.menu.del",$menu->id)}}" class="btn btn-danger">删除</a>
            </td>
        </tr>
       @endforeach
    </table>

{{$menus->appends($url)->links()}}

@endsection
```

### 根据菜品分类查看菜品

```php
//查看
    public function check($id){

        $lists = DB::table("menus")->where("category_id",$id)->get();

//        $menu_categorys=MenuCategory::all();

        return view("shop.menu_category.check",compact("lists"));

    }

```

### 视图

```html
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
```

### 商户登录权限

```html

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <title>商户登录</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim 和 Respond.js 是为了让 IE8 支持 HTML5 元素和媒体查询（media queries）功能 -->
    <!-- 警告：通过 file:// 协议（就是直接将 html 页面拖拽到浏览器中）访问页面时 Respond.js 不起作用 -->
    <!--[if lt IE 9]>
    <script src="https://cdn.jsdelivr.net/npm/html5shiv@3.7.3/dist/html5shiv.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/respond.js@1.4.2/dest/respond.min.js"></script>
    <![endif]-->
</head>
<body>


<form class="form-horizontal" method="post">
    @include("shop.layouts._error")
    @include("shop.layouts._msg")

    {{csrf_field()}}
    <br><br><br><br>
<div class="form-group">
    <label for="inputEmail3" class="col-sm-2 control-label">用户名</label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="inputEmail3" placeholder="用户名" name="name">
    </div>
</div>
<div class="form-group">
    <label for="inputPassword3" class="col-sm-2 control-label">密码</label>
    <div class="col-sm-10">
        <input type="password" class="form-control" id="inputPassword3" placeholder="密码" name="password">
    </div>
</div>
<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
        <div class="checkbox">
            <label>
                <input type="checkbox">记住密码
            </label>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-default">登录</button>
    </div>
</div>
    </form>


<!-- jQuery (Bootstrap 的所有 JavaScript 插件都依赖 jQuery，所以必须放在前边) -->
<script src="https://cdn.jsdelivr.net/npm/jquery@1.12.4/dist/jquery.min.js"></script>
<!-- 加载 Bootstrap 的所有 JavaScript 插件。你也可以根据需要只加载单个插件。 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js"></script>
</body>
</html>
```

### 异常处理

```php
<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);



    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        //return $request->expectsJson()
        //            ? response()->json(['message' => $exception->getMessage()], 401)
        //            : redirect()->guest(route('login'));
        if ($request->expectsJson()) {
            return response()->json(['message' => $exception->getMessage()], 401);
        } else {
            session()->flash("danger","没有权限,请登录！！！");
            return in_array('web', $exception->guards()) ? redirect()->guest(route('admin.admin.login')) :
                redirect()->guest(route('shop.user.login'));
        }
    }

}

```

### 控制器

```php
<?php

namespace App\Http\Controllers\Shop;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware("auth:web",[
            "except"=>["login","reg"]
        ]);
    }

}

```

### 一个商户只能有且仅有一个默认菜品分类 

```php
<?php

namespace App\Http\Controllers\shop;

use App\Models\Menu;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MenuCategoryController extends BaseController
{
    //
    public function index(){

        $menu_categorys=MenuCategory::all();
        //显示视图并传递数据
        return view("shop.menu_category.index",compact("menu_categorys"));

    }

    public function add(Request $request){

        //判断提交方式
        if ($request->isMethod("post")){

            //验证
            $this->validate($request,[
                'name'=>'required|unique',
                'description'=>'required',
                'is_selected'=>'required',
            ]);

            //接收数据

            $data=$request->post();

            $shopId = Auth::user()->information->id;

            $data["information_id"]=$shopId;
//            dd( $data["information_id"]);

            //判断,只能只有一个默认值
            if($request->post("is_selected")){
                //把所有的is_selected设置为0
                MenuCategory::where("is_selected",1)->where("information_id",$shopId)->update(["is_selected"=>0]);
            }

            //数据入库
            if (MenuCategory::create($data)){
                //跳转
                return redirect()->route("shop.menu_category.index")->with("success","添加成功");
            }

        }else{

            //显示视图
            return view("shop.menu_category.add");

        }
    }

    //修改
    public function edit(Request $request,$id){

        //通过id得到对象
        $menu_category=MenuCategory::find($id);
        //判断提交方式
        if ($request->isMethod("post")){

            //接收数据
            $data=$request->post();

            $shopId = Auth::user()->information->id;

            $data["information_id"]=$shopId;

            //判断,只能只有一个默认值
            if($request->post("is_selected")){
                //把所有的is_selected设置为0
                MenuCategory::where("is_selected",1)->where("information_id",$shopId)->update(["is_selected"=>0]);
            }

            if ($menu_category->update($data)){
                return redirect()->route("shop.menu_category.index")->with("success","修改成功");
            }

        }else{
            //显示视图并传数据
            return view("shop.menu_category.edit",compact("menu_category"));
        }


    }


    //删除
    public function del($id){


        //得到当前分类
        $cate=MenuCategory::findOrFail($id);
        //得到当前分类对应的店铺数
        $shopCount=Menu::where('category_id',$cate->id)->count();
        //判断当前分类店铺数
        if ($shopCount){
            //回跳
            return  back()->with("danger","当前分类下有菜品，不能删除");
        }
        //否则删除
        $cate->delete();
        //跳转
        return redirect()->route('shop.menu_category.index')->with('success',"删除成功");
    }


    //查看
    public function check($id){

        $lists = DB::table("menus")->where("category_id",$id)->get();

//        $menu_categorys=MenuCategory::all();

        return view("shop.menu_category.check",compact("lists"));

    }


}

```






