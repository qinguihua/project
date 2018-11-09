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

## Day04

### 开发任务

优化 - 将网站图片上传到阿里云OSS对象存储服务，以减轻服务器压力(<https://github.com/jacobcyl/Aliyun-oss-storage>) - 使用webuploder图片上传插件，提升用户上传图片体验

平台 - 平台活动管理（活动列表可按条件筛选 未开始/进行中/已结束 的活动） - 活动内容使用ueditor内容编辑器(<https://github.com/overtrue/laravel-ueditor>)

商户端 - 查看平台活动（活动列表和活动详情） - 活动列表不显示已结束的活动

# 实现步骤

## 阿里云OSS

1. 登录阿里云网站

2. 开通oss(实名认证之后申请半年免费)

3. 进入控制器 OSS操作面板

4. 新建 bucket 取名 域名 标准存储 公共读

5. 点击用户图像---》accesskeys--->继续使用accsskeys--->添加accesskeys--->拿到access_id和access_key

6. 执行 命令 安装 ali-oss插件(在phpstorm中执行)

   ```php
   composer require jacobcyl/ali-oss-storage -vvv
   ```

7. 修改 app/filesystems.php 添加如何代码

   ```php
   <?php
   
   return [
   
       ...此处省略N个代码
       'disks' => [
   
   
           'oss' => [
               'driver'        => 'oss',
               'access_id'     => 'LTAIN4ntRZ61ncKi',//账号
               'access_key'    => 't8ER2mqST23gATgmx9w8u1v1jLMmWu',//密钥
               'bucket'        => 'project-ele',//空间名称
               'endpoint'      => 'oss-cn-shenzhen.aliyuncs.com', // OSS 外网节点或自定义外部域名
   
           ],
   
       ],
   
   ];
   ```

8. 修改 .env配置文件 设置文件上传驱动为oss

   ```php
   FILESYSTEM_DRIVER=oss
   ALIYUN_OSS_URL=http://project-ele.oss-cn-shenzhen.aliyuncs.com/
   ALIYUNU_ACCESS_ID=LTAIN4ntRZ61ncKi
   ALIYUNU_ACCESS_KEY=t8ER2mqST23gATgmx9w8u1v1jLMmWu
   ALIYUNU_OSS_BUCKET=project-ele
   ALIYUNU_OSS_ENDPOINT=oss-cn-shenzhen.aliyuncs.com
   ```

9. 获取图片 及 缩略图

   ```php
               <td><img src="{{env("ALIYUN_OSS_URL").$menu->goods_img}}?x-oss-process=image/resize,m_fill,w_80,h_80"></td>
   ```

### webuploader

#### 下载并解压

```php
 https://github.com/fex-team/webuploader/releases/download/0.1.5/webuploader-0.1.5.zip
```

#### 将解压文件复制到public文件夹下面

#### 分别引用css和js修改layouts里的main模板

```php
<!--引入CSS-->
    <link rel="stylesheet" type="text/css" href="/webuploader/webuploader.css">
    
 <body>
    
    <!--引入JS-->
<script type="text/javascript" src="/webuploader/webuploader.js"></script>
    
@yield("js")
    
</body>
</html>
```

### 在添加视图中

```html
<div class="form-group">
                <label>商品图片</label>

                <input type="hidden" name="goods_img" value="" id="goods_img">
                <!--dom结构部分-->
                <div id="uploader-demo">
                    <!--用来存放item-->
                    <div id="fileList" class="uploader-list"></div>
                    <div id="filePicker">选择图片</div>
                </div>
            </div>
```

### js

```js
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
```

### css

```css
#picker {
    display: inline-block;
    line-height: 1.428571429;
    vertical-align: middle;
    margin: 0 12px 0 0;
}
#picker .webuploader-pick {
    padding: 6px 12px;
    display: block;
}


#uploader-demo .thumbnail {
    width: 110px;
    height: 110px;
}
#uploader-demo .thumbnail img {
    width: 100%;
}
.uploader-list {
    width: 100%;
    overflow: hidden;
}
.file-item {
    float: left;
    position: relative;
    margin: 0 20px 20px 0;
    padding: 4px;
}
.file-item .error {
    position: absolute;
    top: 4px;
    left: 4px;
    right: 4px;
    background: red;
    color: white;
    text-align: center;
    height: 20px;
    font-size: 14px;
    line-height: 23px;
}
.file-item .info {
    position: absolute;
    left: 4px;
    bottom: 4px;
    right: 4px;
    height: 20px;
    line-height: 20px;
    text-indent: 5px;
    background: rgba(0, 0, 0, 0.6);
    color: white;
    overflow: hidden;
    white-space: nowrap;
    text-overflow : ellipsis;
    font-size: 12px;
    z-index: 10;
}
.upload-state-done:after {
    content:"\f00c";
    font-family: FontAwesome;
    font-style: normal;
    font-weight: normal;
    line-height: 1;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    font-size: 32px;
    position: absolute;
    bottom: 0;
    right: 4px;
    color: #4cae4c;
    z-index: 99;
}
.file-item .progress {
    position: absolute;
    right: 4px;
    bottom: 4px;
    height: 3px;
    left: 4px;
    height: 4px;
    overflow: hidden;
    z-index: 15;
    margin:0;
    padding: 0;
    border-radius: 0;
    background: transparent;
}
.file-item .progress span {
    display: block;
    overflow: hidden;
    width: 0;
    height: 100%;
    background: #d14 url(../images/progress.png) repeat-x;
    -webit-transition: width 200ms linear;
    -moz-transition: width 200ms linear;
    -o-transition: width 200ms linear;
    -ms-transition: width 200ms linear;
    transition: width 200ms linear;
    -webkit-animation: progressmove 2s linear infinite;
    -moz-animation: progressmove 2s linear infinite;
    -o-animation: progressmove 2s linear infinite;
    -ms-animation: progressmove 2s linear infinite;
    animation: progressmove 2s linear infinite;
    -webkit-transform: translateZ(0);
}
@-webkit-keyframes progressmove {
    0% {
        background-position: 0 0;
    }
    100% {
        background-position: 17px 0;
    }
}
@-moz-keyframes progressmove {
    0% {
        background-position: 0 0;
    }
    100% {
        background-position: 17px 0;
    }
}
@keyframes progressmove {
    0% {
        background-position: 0 0;
    }
    100% {
        background-position: 17px 0;
    }
}

a.travis {
  position: relative;
  top: -4px;
  right: 15px;
}
```

## 修改同上

```php
  //修改
    public function edit(Request $request,$id){

        //通过id得到对象
        $menu=Menu::find($id);
        //判断提交方式
        if ($request->isMethod("post")){

            //接收数据
            $data=$request->post();
            $data['status']=$request->has('status')?'1':'0';

//            //判断是否重新上传图片
//            if($request->file("goods_img")!==null){
//                $data['goods_img']=$request->file("goods_img")->store("images");
//            }else{
//                $data['goods_img']=$menu->goods_img;
//            }

            if ($menu->update($data)){
                return redirect()->route("shop.menu.index")->with("success","修改成功");
            }

        }else{
            //显示视图并传数据
            $results=MenuCategory::all();
            return view("shop.menu.edit",compact("menu","results"));
        }

    }
```

## ueditor内容编辑器

### 安装

```php
composer require "overtrue/laravel-ueditor:~1.0"
```

### 配置

添加下面一行到 `config/app.php` 中 `providers` 部分：

```php
Overtrue\LaravelUEditor\UEditorServiceProvider::class,
```

### 发布配置文件与资源

```php
php artisan vendor:publish 
    
然后选择 ：
  Provider: Overtrue\LaravelUEditor\UEditorServiceProvider对应的数字

```

### 在config/ueditor.php中修改 

```php
return [
    // 存储引擎: config/filesystem.php 中 disks， public 或 qiniu
    'disk' => 'oss',
    'route' => [
        'name' => '/ueditor/server',
        'options' => [
            // middleware => 'auth',
        ],
    ],
```



### 模板引入编辑器

这行的作用是引入编辑器需要的 css,js 等文件，所以你不需要再手动去引入它们

```php
@include('vendor.ueditor.assets')
```

### 编辑器初始化

```php
<!-- 实例化编辑器 -->
<script type="text/javascript">
    var ue = UE.getEditor('container');
    ue.ready(function() {
        ue.execCommand('serverparam', '_token', '{{ csrf_token() }}'); // 设置 CSRF token.
    });
</script>

<!-- 编辑器容器 -->
<script id="container" name="content" type="text/plain"></script>
```

## 活动列表

```php
public function index(){

        $activitys=Activity::all();
        //显示视图并传递数据
        return view("admin.activity.index",compact("activitys"));

    }
```

### 视图

```html
@extends("admin.layouts.main")
@section("title","活动列表")
@section("content")


    <a href="{{route("admin.activity.add")}}" class="btn btn-info">添加</a>
    <br>
    <br>
    <table class="table table-striped">
        <tr>
            <th>Id</th>
            <th>活动标题</th>
            <th>活动内容</th>
            <th>开始时间</th>
            <th>结束时间</th>
            <th>操作</th>
        </tr>
        @foreach($activitys as $activity)
            <tr>
                <td>{{$activity->id}}</td>
                <td>{{$activity->title}}</td>
                <td>{{$activity->content}}</td>
                <td>{{$activity->start_time}}</td>
                <td>{{$activity->end_time}}</td>

                <td>
                    <a href="{{route("admin.activity.edit",$activity->id)}}" class="btn btn-success">编辑</a>
                    <a href="{{route("admin.activity.del",$activity->id)}}" class="btn btn-danger">删除</a>
                </td>
            </tr>
        @endforeach
    </table>

@endsection
```

### 添加，内容用编辑器

```php
 public function add(Request $request){

        //判断提交方式
        if ($request->isMethod("post")){

            //验证
            $this->validate($request,[
                'title'=>'required',
                'content'=>'required',
                'start_time'=>'required',
                'end_time'=>'required',
            ]);

            //接收数据

            $data=$request->post();

            //数据入库
            if (Activity::create($data)){
                //跳转
                return redirect()->route("admin.activity.index")->with("success","添加成功");
            }

        }else{

            //显示视图
            return view("admin.activity.add");

        }
    }
```

### main

```php
@yield("js")
    
</body>
```

### 视图

```html
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
```

### 编辑

```php
//修改
    public function edit(Request $request,$id)
    {

        //通过id得到对象
        $activity = Activity::find($id);

        $activity->start_time = str_replace(" ", "T", $activity->start_time);
        $activity->end_time = str_replace(" ", "T", $activity->end_time);

        //判断提交方式
        if ($request->isMethod("post")) {
            $data = $this->validate($request, [
                "title" => "required",
                "start_time" => "required",
                "end_time" => "required",
                "content" => "required"
            ]);
//            $data=$request->post();
//           dd($data);
            $data['start_time'] = str_replace("T", " ", $data['start_time']);
            $data['end_time'] = str_replace("T", " ", $data['end_time']);

//            dd($data);
            $data->update($data);
            return redirect()->intended(route("admin.activity.index"))->with("success", "修改成功");
        }else{

            return view("admin.activity.edit",compact("activity"));
        }
    }

```

### 视图

```html
@extends("admin.layouts.main")
@section("title","修改活动")
@section("content")


    <form method="post" enctype="multipart/form-data" class="table table-striped">
        {{ csrf_field() }}
        <div class="form-group">
            <label>活动标题</label>
            <input type="text" class="form-control" placeholder="活动标题" name="title" value="{{$activity->title}}">
        </div>

        <div class="form-group">
            <label>活动开始时间</label>
            <input type="datetime-local" class="form-control" placeholder="活动开始时间" name="start_time" value="{{$activity->start_time}}">
        </div>

        <div class="form-group">
            <label>活动结束时间</label>
            <input type="datetime-local" class="form-control" placeholder="活动结束时间" name="end_time" value="{{$activity->end_time}}">
        </div>

        <div class="form-group">
            <label>活动内容</label>
            <script id="container" name="content" type="text/plain">{{$activity->content}}</script>
        </div>

        <button type="submit" class="btn btn-default">修改</button>
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

```

### 在前台显示未结束的活动列表

```php
<?php

namespace App\Http\Controllers\shop;

use App\Models\Activity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActivityController extends BaseController
{
    //
    public function show(){

        $activitys=Activity::where("end_time",">=",date('Y-m-d H:i:s', time()))->get();

        return view("shop.user.show",compact("activitys"));

    }
}
```

### 视图

```html
@extends("admin.layouts.main")
@section("title","活动列表")
@section("content")

        <a href="{{route("admin.activity.add")}}" class="btn btn-info">添加</a>

    <br>
    <br>
    <table class="table table-striped">
        <tr>
            <th>活动标题</th>
            <th>活动内容</th>
            <th>开始时间</th>
            <th>结束时间</th>
        </tr>
        @foreach($activitys as $activity)
            <tr>
                <td>{{$activity->title}}</td>
                <td>{{$activity->content}}</td>
                <td>{{$activity->start_time}}</td>
                <td>{{$activity->end_time}}</td>
            </tr>
        @endforeach
    </table>
@endsection
```

# Day05

## 开发任务

接口开发

- 商家列表接口(支持商家搜索)
- 获取指定商家接口

## 实现步骤

#### 1.解压dist.zip,并复制到public文件下

#### 2.将dist中static与api.js移到public下

#### 3.在视图view文件下创建index，并将dist中的index中的代码复制在index中

```php
<!DOCTYPE html>
<html>
<head>
    <meta charset=utf-8>
    <meta name=format-detection content="telphone=no, email=no">
    <meta name=viewport
          content="width=device-width,initial-scale=0.3333333333333333,maximum-scale=0.3333333333333333,minimum-scale=0.3333333333333333,user-scalable=no">
    <title>vue</title>
    <link href=./static/css/app.d40081f78a711e3486e27f787eed3c1f.css rel=stylesheet>
</head>
<body>
<svg xmlns=http://www.w3.org/2000/svg xmlns:xlink=http://www.w3.org/1999/xlink
     style=position:absolute;width:0;height:0;visibility:hidden>
    <defs>
        <symbol viewbox="0 0 26 31" id=location>
            <path fill=#FFF fill-rule=evenodd
                  d="M22.116 22.601c-2.329 2.804-7.669 7.827-7.669 7.827-.799.762-2.094.763-2.897-.008 0 0-5.26-4.97-7.643-7.796C1.524 19.8 0 16.89 0 13.194 0 5.908 5.82 0 13 0s13 5.907 13 13.195c0 3.682-1.554 6.602-3.884 9.406zM18 13a5 5 0 1 0-10 0 5 5 0 0 0 10 0z"></path>
        </symbol>
        <symbol viewbox="0 0 14 8" id=arrow>
            <path fill=#FFF fill-rule=evenodd
                  d="M5.588 6.588c.78.78 2.04.784 2.824 0l5.176-5.176c.78-.78.517-1.412-.582-1.412H.994C-.107 0-.372.628.412 1.412l5.176 5.176z"></path>
        </symbol>
        <symbol viewbox="0 0 60 60" id=eleme>
            <path fill=#3CABFF fill-rule=evenodd
                  d="M0 9.375A9.374 9.374 0 0 1 9.375 0h41.25A9.374 9.374 0 0 1 60 9.375v41.25A9.374 9.374 0 0 1 50.625 60H9.375A9.374 9.374 0 0 1 0 50.625V9.375zm35.94 30.204c-5.601 3.147-12.645 1.256-15.834-4.217-3.206-5.501-1.303-12.537 4.25-15.713 4.7-2.689 10.51-1.749 14.127 1.941L27.526 27.89a2.81 2.81 0 0 0-1.037 3.854 2.862 2.862 0 0 0 3.887 1.035l15.988-9.166a17.238 17.238 0 0 0-1.222-2.571c-4.777-8.198-15.358-11.007-23.632-6.275-8.275 4.734-11.11 15.217-6.332 23.415 4.77 8.184 15.322 10.997 23.59 6.297.877-.5 1.654-1.037 2.376-1.623l-1.31-2.248a2.868 2.868 0 0 0-3.893-1.028zm10.824-7.39l-1.418-2.425-4.911 2.798 2.835 4.846 2.454-1.399h.002a2.779 2.779 0 0 0 1.038-3.82z"></path>
        </symbol>
        <symbol viewbox="0 0 32 31" id=shop>
            <g fill-rule=evenodd>
                <path d="M28.232 1.822C27.905.728 26.97.152 25.759.152H5.588c-1.252 0-1.867.411-2.397 1.415l-.101.243-.443 1.434-.975 3.154-.002.007C.837 9.101.294 10.854.26 10.956l-.059.259c-.231 1.787.337 3.349 1.59 4.448 1.159 1.017 2.545 1.384 3.865 1.384.07 0 .07 0 .132-.002-.01.001-.01.001.061.002 1.32 0 2.706-.367 3.865-1.384a4.96 4.96 0 0 0 .413-.407l-1.043-.946-1.056.931c1.033 1.171 2.51 1.792 4.21 1.801.04.002.088.004.173.004 1.32 0 2.706-.367 3.865-1.384.148-.13.287-.267.418-.411l-1.044-.944-1.057.93c1.033 1.174 2.511 1.796 4.213 1.806.04.002.088.004.173.004 1.32 0 2.706-.367 3.865-1.384.15-.131.29-.27.422-.416l-1.046-.943-1.058.929c1.033 1.177 2.513 1.801 4.218 1.811.04.002.088.004.173.004 1.32 0 2.706-.367 3.865-1.384 1.206-1.058 1.858-2.812 1.676-4.426-.069-.61-.535-2.207-1.354-4.785l-.109-.342a327.554 327.554 0 0 0-1.295-3.966l-.122-.366.014.043h.004zm-2.684.85l.12.361.318.962c.329.999.658 2.011.965 2.973l.108.338c.719 2.262 1.203 3.92 1.24 4.249.08.711-.233 1.553-.735 1.993-.553.485-1.308.685-2.008.685l-.098-.002c-.987-.007-1.695-.306-2.177-.854l-1.044-1.189-1.06 1.175a2.192 2.192 0 0 1-.188.185c-.553.485-1.308.685-2.008.685l-.098-.002c-.985-.007-1.693-.305-2.174-.852l-1.043-1.185-1.059 1.171c-.058.064-.12.125-.186.183-.553.485-1.308.685-2.008.685l-.098-.002c-.984-.007-1.692-.304-2.173-.85L9.101 12.2l-1.058 1.166a2.248 2.248 0 0 1-.184.181c-.553.485-1.307.685-2.008.685l-.061-.001-.131.001c-.701 0-1.455-.2-2.008-.685-.538-.472-.767-1.102-.654-1.971l-1.396-.18 1.338.44c.043-.13.552-1.775 1.425-4.599l.002-.007.975-3.155.443-1.434-1.345-.415 1.245.658c.054-.102.042-.085-.083-.001-.122.082-.143.086-.009.086H25.763c.053 0-.164-.133-.225-.339l.014.043-.004-.001zM5.528 19.48c.778 0 1.408.63 1.408 1.408v7.424a1.408 1.408 0 1 1-2.816 0v-7.424c0-.778.63-1.408 1.408-1.408z"></path>
                <path d="M.28 29.72c0-.707.58-1.28 1.277-1.28h28.155a1.28 1.28 0 0 1 .007 2.56H1.561A1.278 1.278 0 0 1 .28 29.72z"></path>
                <path d="M26.008 19.48c.778 0 1.408.63 1.408 1.408v7.424a1.408 1.408 0 1 1-2.816 0v-7.424c0-.778.63-1.408 1.408-1.408z"></path>
            </g>
        </symbol>
        <symbol viewbox="0 0 28 33" id=arrow-left>
            <path fill-rule=evenodd
                  d="M17.655 1.853L15.961.159.033 16.072 15.961 32l1.694-1.694L3.429 16.08 17.655 1.854z"
                  class=path1></path>
        </symbol>
    </defs>
</svg>
<svg xmlns=http://www.w3.org/2000/svg xmlns:xlink=http://www.w3.org/1999/xlink style=position:absolute;width:0;height:0>
    <defs>
        <symbol viewbox="0 0 1024 1024" id=res-bad>
            <path fill=#D0021B fill-rule=evenodd
                  d="M512 0C230.326 0 0 230.326 0 512s230.573 512 512 512 512-230.326 512-512S793.674 0 512 0zM240.694 373.755l158.735-56.285 15.306 46.164L256 419.919l-15.306-46.164zm440.409 384.123c-10.122 0-20.49-10.122-25.674-20.49-10.122-10.122-61.47-25.674-148.366-25.674-86.896 0-138.245 15.306-148.366 25.674 0 10.122-10.122 20.49-25.674 20.49s-25.674-10.122-25.674-25.674c0-71.591 174.041-71.591 194.53-71.591 20.489 0 194.53 0 194.53 71.591 10.122 10.368 0 25.674-15.306 25.674zM768 419.919l-163.672-61.47 15.306-46.164 158.735 56.285-10.368 51.348-.001.001z"></path>
        </symbol>
        <symbol viewbox="0 0 1146 885" id=choose>
            <path d="M1001.309 14.473c18.618-18.618 46.545-18.618 65.164 0l65.164 65.164c18.618 18.618 18.618 46.545 0 65.164L410.182 870.91c-18.618 18.618-46.545 18.618-65.164 0L14.545 545.092c-18.618-18.618-18.618-46.545 0-65.164l65.164-65.164c18.618-18.618 46.545-18.618 65.164 0L377.6 647.491l623.709-633.018z"></path>
        </symbol>
        <symbol viewbox="0 0 982 854" id=notice>
            <path d="M461.467 21.667c-12.8 0-29.867 4.267-51.2 25.6L214 256.334H73.2c-38.4 0-72.533 34.133-72.533 76.8v217.6c0 38.4 34.133 72.533 72.533 72.533H214l192 192c17.067 17.067 38.4 21.333 46.933 21.333 25.6 0 55.467-21.333 55.467-68.267V85.666c8.533-46.933-21.333-64-46.933-64v.001zm-29.867 691.2l-179.2-179.2H86v-192h166.4l174.933-192 4.267 563.2zM649.2.333v102.4C794.267 145.4 888.133 273.4 888.133 427S790 708.6 649.2 751.267v102.4C845.467 811 982 636.067 982 427 982 217.933 841.2 43 649.2.333z"></path>
            <path d="M772.933 427c0-85.333-46.933-162.133-123.733-192v388.267C726 589.134 772.933 512.334 772.933 427z"></path>
        </symbol>
        <symbol viewbox="0 0 547 987" id=arrow-right>
            <path d="M0 931.973l51.2 54.613 494.933-494.933L51.2.133 0 51.333l440.32 440.32L0 931.973z"></path>
        </symbol>
        <symbol viewbox="0 0 188 163" id=res-collection>
            <path fill=#272636 fill-rule=evenodd
                  d="M94.25 26.5C85.75 10.75 69.125 0 50.125 0 22.625 0 .375 22.375.375 50c0 13.125 5 25 13.25 34L90 160.75c1.25 1.125 2.75 1.75 4.25 1.75s3-.625 4.25-1.75L174.875 84C183 75.125 188 63.125 188 50c0-27.625-22.25-50-49.75-50-18.875 0-35.375 10.75-44 26.5zm71.125 49.375l-71.125 72.25-71.125-72.25C16.75 69.125 12.875 60 12.875 50c0-20.75 16.75-37.5 37.25-37.5 16.625 0 31 11 36 26.125 1.25 3.25 4.5 5.625 8.125 5.625 3.75 0 6.875-2.25 8.25-5.5 4.875-15.25 19.125-26.25 35.75-26.25 20.625 0 37.25 16.75 37.25 37.5.125 10-3.75 19.125-10.125 25.875z"></path>
        </symbol>
        <symbol viewbox="0 0 1024 1024" id=res-well>
            <path fill=#7ED321 fill-rule=evenodd
                  d="M512 0C229.376 0 0 229.376 0 512s229.376 512 512 512 512-229.376 512-512S794.624 0 512 0zM247.808 402.432c0-36.864 39.936-93.184 93.184-93.184s93.184 56.32 93.184 93.184c0 11.264-9.216 20.48-20.48 20.48-11.264 0-20.48-9.216-20.48-20.48 0-16.384-24.576-52.224-52.224-52.224-27.648 0-52.224 35.84-52.224 52.224 0 11.264-9.216 20.48-20.48 20.48-11.264 0-20.48-9.216-20.48-20.48zM512 800.768c-132.096 0-239.616-96.256-239.616-215.04 0-11.264 9.216-20.48 20.48-20.48 11.264 0 20.48 9.216 20.48 20.48 0 96.256 89.088 174.08 198.656 174.08 109.568 0 198.656-77.824 198.656-174.08 0-11.264 9.216-20.48 20.48-20.48 11.264 0 20.48 9.216 20.48 20.48 0 117.76-107.52 215.04-239.616 215.04zm243.712-377.856c-11.264 0-20.48-9.216-20.48-20.48 0-17.408-24.576-52.224-52.224-52.224-28.672 0-52.224 34.816-52.224 52.224 0 11.264-9.216 20.48-20.48 20.48-11.264 0-20.48-9.216-20.48-20.48 0-36.864 39.936-93.184 93.184-93.184s93.184 56.32 93.184 93.184c0 11.264-9.216 20.48-20.48 20.48z"></path>
        </symbol>
        <symbol viewbox="0 0 1024 1024" id=res-ordinary>
            <path fill=#febb00 fill-rule=evenodd
                  d="M670.476 454.548c33.663 0 60.952-27.019 60.952-60.349s-27.289-60.349-60.952-60.349-60.952 27.019-60.952 60.349 27.289 60.349 60.952 60.349zm-316.952 0c33.663 0 60.952-27.019 60.952-60.349s-27.289-60.349-60.952-60.349-60.952 27.019-60.952 60.349 27.289 60.349 60.952 60.349zM0 508.862C0 228.892 226.941 1.931 506.938 1.931h10.125c279.974 0 506.938 226.899 506.938 506.931 0 279.97-226.941 506.931-506.938 506.931h-10.125C226.964 1015.793 0 788.894 0 508.862zm292.571 187.081c0 13.425 10.844 24.14 24.22 24.14h390.417c13.372 0 24.22-10.808 24.22-24.14 0-13.425-10.844-24.14-24.22-24.14H316.791c-13.372 0-24.22 10.808-24.22 24.14z"
                  class="path1 fill-color2"></path>
        </symbol>
        <symbol viewbox="0 0 1024 1024" id=res-x>
            <path fill-rule=evenodd
                  d="M480.518 512L8.377 984.141c-8.853 8.853-8.777 22.871-.083 31.565 8.754 8.754 22.825 8.656 31.565-.083L512 543.482l472.141 472.141c8.853 8.853 22.871 8.777 31.565.083 8.754-8.754 8.656-22.825-.083-31.565L543.482 512l472.141-472.141c8.853-8.853 8.777-22.871.083-31.565-8.754-8.754-22.825-8.656-31.565.083L512 480.518 39.859 8.377C31.006-.476 16.988-.4 8.294 8.294c-8.754 8.754-8.656 22.825.083 31.565L480.518 512z"
                  class="path1 fill-color3"></path>
        </symbol>
        <symbol viewbox="0 0 12 6" id=activity-more>
            <path fill=#999 fill-rule=evenodd
                  d="M4.577 5.423c.79.77 2.073.767 2.857 0l4.12-4.026C12.345.625 12.09 0 10.985 0H1.027C-.077 0-.33.63.457 1.397l4.12 4.026z"></path>
        </symbol>
        <symbol viewbox="0 0 22 22" id=rating-star>
            <path fill-rule=evenodd
                  d="M10.986 17.325l-5.438 3.323c-1.175.718-1.868.208-1.55-1.126l1.48-6.202-4.84-4.15c-1.046-.895-.775-1.71.59-1.82l6.353-.51L10.03.95c.53-1.272 1.39-1.266 1.915 0l2.445 5.89 6.353.51c1.372.11 1.632.93.592 1.82l-4.84 4.15 1.478 6.202c.32 1.34-.38 1.84-1.55 1.126l-5.437-3.323z"></path>
        </symbol>
    </defs>
</svg>
<svg xmlns=http://www.w3.org/2000/svg xmlns:xlink=http://www.w3.org/1999/xlink style=position:absolute;width:0;height:0>
    <defs>
        <symbol viewbox="0 0 32 32" id=back-top.fac75cb>
            <path fill=#999
                  d="M16 31.767c8.708 0 15.767-7.06 15.767-15.767S24.707.233 16 .233C7.292.233.233 7.293.233 16S7.293 31.767 16 31.767zm0-.26C7.436 31.506.494 24.563.494 16S7.436.494 16 .494 31.506 7.436 31.506 16 24.564 31.506 16 31.506z"></path>
            <path fill=#999 d="M15.74 18.893c0 .56.78.56.78 0v-8.878c0-.56-.78-.56-.78 0v8.878z"></path>
            <path fill=#999
                  d="M11.33 14.646l4.704-4.503c.09-.095.258-.282.25-.272-.018.02-.047.04-.166.04s-.148-.02-.165-.04c-.01-.01.157.178.236.26l4.768 4.517.538-.568-4.755-4.504a17.8 17.8 0 0 1-.214-.238c-.132-.143-.225-.21-.408-.21s-.275.068-.408.21c0 0-.16.18-.23.252l-4.69 4.49.54.565zm-.212-5.808h10v-.782h-10v.782zm2.066 12.959v.364h1.1c-.015.188-.035.37-.067.546h-.883v2.473h.364v-2.126h1.574v2.114h.37v-2.462h-1.045a6.24 6.24 0 0 0 .068-.545h1.148v-.363h-2.63zM14.91 25.2l-.256.24c.395.32.7.622.93.903l.27-.275a6.815 6.815 0 0 0-.945-.868zm-.63-1.84v.926c-.016.442-.125.8-.332 1.075-.218.266-.603.48-1.158.64l.203.317c.577-.177.992-.416 1.247-.717.26-.322.395-.764.41-1.314v-.925h-.37zm-2.348 2.91c.296 0 .447-.156.447-.462V22.16H13v-.363h-1.808v.364h.8v3.554c0 .145-.068.223-.192.223-.17 0-.353-.01-.54-.02l.083.352h.587zm5.15-3.636l-.344.125c.13.228.24.477.332.747h-.7v.364h2.602v-.363h-.63c.136-.234.256-.5.365-.795l-.348-.12c-.105.323-.235.63-.385.915h-.545a5.06 5.06 0 0 0-.348-.873zm2.128-.842v4.535h.364v-4.192h.93c-.166.56-.338 1.05-.51 1.46.406.5.614.9.62 1.205-.006.187-.032.312-.084.38-.052.06-.17.098-.358.108-.1 0-.218-.01-.364-.02l.12.394c.425 0 .71-.068.856-.192.135-.125.203-.348.203-.67-.005-.312-.203-.717-.592-1.205.187-.468.358-.977.52-1.522v-.28H19.21zm-2.742.286v.364h2.468v-.364h-1.06a8.404 8.404 0 0 0-.17-.535l-.385.068c.07.14.13.297.193.47H16.47zm2.264 2.265h-2.068V26.3h.37v-.29h1.33v.29h.368v-1.957zm-1.698 1.314v-.96h1.33v.96h-1.33z"></path>
        </symbol>
        <symbol viewbox="0 0 20 32" id=arrow-left.6f6409e>
            <path fill=#fff d="M16.552 5.633L14.508 3.59 2.243 15.853 14.508 28.41l2.044-2.043-10.22-10.513z"></path>
        </symbol>
        <symbol viewbox="0 0 40 40" id=index-regular.b245d60>
            <g fill=none fill-rule=evenodd stroke=#666 stroke-width=2>
                <path d="M31.426 23.095l2.678 5.742 2.943-1.372a3.173 3.173 0 0 0 1.537-4.212l-1.339-2.871-5.819 2.713z"></path>
                <path d="M29.074 31.161c-1.224-.49-2.404-.32-3.49.185-6.383 2.977-13.938.286-16.875-6.01-2.936-6.297-.14-13.815 6.243-16.792 5.211-2.43 11.203-1.083 14.825 2.919l-12.263 5.718c-1.596.745-2.295 2.624-1.561 4.198.734 1.574 2.625 2.246 4.22 1.503l8.422-3.928 9.953-4.641a18.78 18.78 0 0 0-.941-2.453C33.202 2.416 21.869-1.62 12.294 2.844 2.718 7.309-1.474 18.586 2.93 28.03c4.404 9.445 15.737 13.482 25.313 9.017 1.069-.499 2.067-.879 3.438-1.744 0 0-1.382-3.651-2.607-4.142z"></path>
            </g>
        </symbol>
        <symbol xmlns:xlink=http://www.w3.org/1999/xlink viewbox="0 0 40 40" id=index.18edf5a>
            <defs>
                <lineargradient id=index.18edf5a_c x1=50% x2=50% y1=100% y2=0%>
                    <stop offset=0% stop-color=#2BAEFF></stop>
                    <stop offset=100% stop-color=#0095FF></stop>
                </lineargradient>
                <lineargradient id=index.18edf5a_d x1=50% x2=50% y1=100% y2=0%>
                    <stop offset=0% stop-color=#29ADFF></stop>
                    <stop offset=100% stop-color=#0095FF></stop>
                </lineargradient>
                <path id=index.18edf5a_a
                      d="M30.426 22.095l2.678 5.742 2.943-1.372a3.173 3.173 0 0 0 1.537-4.212l-1.339-2.871-5.819 2.713z"></path>
                <mask id=index.18edf5a_e width=9.455 height=10.456 x=-1 y=-1>
                    <path fill=#fff d="M29.426 18.382h9.455v10.456h-9.455z"></path>
                    <use xlink:href=#index.18edf5a_a></use>
                </mask>
                <path id=index.18edf5a_b
                      d="M28.074 30.161c-1.224-.49-2.404-.32-3.49.185-6.383 2.977-13.938.286-16.875-6.01-2.936-6.297-.14-13.815 6.243-16.792 5.211-2.43 11.203-1.083 14.825 2.919l-12.263 5.718c-1.596.745-2.295 2.624-1.561 4.198.734 1.574 2.625 2.246 4.22 1.503l8.422-3.928 9.953-4.641a18.78 18.78 0 0 0-.941-2.453C32.202 1.416 20.869-2.62 11.294 1.844 1.718 6.309-2.474 17.586 1.93 27.03c4.404 9.445 15.737 13.482 25.313 9.017 1.069-.499 2.067-.879 3.438-1.744 0 0-1.382-3.651-2.607-4.142z"></path>
                <mask id=index.18edf5a_f width=38.769 height=39.241 x=-.7 y=-.7>
                    <path fill=#fff d=M-.521-.675h38.769v39.241H-.521z></path>
                    <use xlink:href=#index.18edf5a_b></use>
                </mask>
            </defs>
            <g fill=none fill-rule=evenodd>
                <g transform="translate(1 1)">
                    <use fill=url(#index.18edf5a_c) xlink:href=#index.18edf5a_a></use>
                    <use stroke=url(#index.18edf5a_d) stroke-width=2 mask=url(#index.18edf5a_e)
                         xlink:href=#index.18edf5a_a></use>
                </g>
                <g transform="translate(1 1)">
                    <use fill=url(#index.18edf5a_c) xlink:href=#index.18edf5a_b></use>
                    <use stroke=url(#index.18edf5a_d) stroke-width=1.4 mask=url(#index.18edf5a_f)
                         xlink:href=#index.18edf5a_b></use>
                </g>
            </g>
        </symbol>
        <symbol xmlns:xlink=http://www.w3.org/1999/xlink viewbox="0 0 40 40" id=discover-regular.8ef537f>
            <defs>
                <path id=discover-regular.8ef537f_a
                      d="M20 40c11.046 0 20-8.954 20-20S31.046 0 20 0 0 8.954 0 20s8.954 20 20 20z"></path>
                <mask id=discover-regular.8ef537f_b width=40 height=40 x=0 y=0 fill=#fff>
                    <use xlink:href=#discover-regular.8ef537f_a></use>
                </mask>
            </defs>
            <g fill=none fill-rule=evenodd>
                <use stroke=#666 stroke-width=4 mask=url(#discover-regular.8ef537f_b)
                     xlink:href=#discover-regular.8ef537f_a></use>
                <path stroke=#666 stroke-width=2
                      d="M12.79 28.126c-1.515.68-2.169.016-1.462-1.484l3.905-8.284c.47-.999 1.665-2.198 2.66-2.675l8.484-4.064c1.497-.717 2.153-.08 1.46 1.435l-3.953 8.64c-.46 1.006-1.647 2.186-2.655 2.64l-8.44 3.792z"></path>
                <path fill=#666
                      d="M15.693 24.636c-.692.276-1.02-.06-.747-.746l2.21-4.946c.225-.505.721-.602 1.122-.202l2.563 2.563c.394.394.31.893-.203 1.122l-4.945 2.209z"></path>
            </g>
        </symbol>
        <symbol viewbox="0 0 40 40" id=discover.5811137>
            <defs>
                <lineargradient id=discover.5811137_a x1=50% x2=50% y1=100% y2=0%>
                    <stop offset=0% stop-color=#2BAEFF></stop>
                    <stop offset=100% stop-color=#0095FF></stop>
                </lineargradient>
            </defs>
            <g fill=none fill-rule=evenodd>
                <path fill=url(#discover.5811137_a)
                      d="M20 40c11.046 0 20-8.954 20-20S31.046 0 20 0 0 8.954 0 20s8.954 20 20 20z"></path>
                <path fill=#FFF
                      d="M12.79 28.126c-1.515.68-2.169.016-1.462-1.484l3.905-8.284c.47-.999 1.665-2.198 2.66-2.675l8.484-4.064c1.497-.717 2.153-.08 1.46 1.435l-3.953 8.64c-.46 1.006-1.647 2.186-2.655 2.64l-8.44 3.792z"></path>
                <path fill=url(#discover.5811137_a)
                      d="M6.482 5.44c-.684-.294-.678-.764 0-1.055L11.54 2.45c.517-.198.936.085.936.65v3.625c0 .558-.412.852-.936.65L6.48 5.44z"
                      transform="rotate(-45 34.258 3.92)"></path>
            </g>
        </symbol>
        <symbol xmlns:xlink=http://www.w3.org/1999/xlink viewbox="0 0 38 38" id=order-regular.41c17f8>
            <defs>
                <rect id=order-regular.41c17f8_a width=38 height=38 rx=2></rect>
                <mask id=order-regular.41c17f8_b width=38 height=38 x=0 y=0 fill=#fff>
                    <use xlink:href=#order-regular.41c17f8_a></use>
                </mask>
            </defs>
            <g fill=none fill-rule=evenodd>
                <use stroke=#666 stroke-width=4 mask=url(#order-regular.41c17f8_b)
                     xlink:href=#order-regular.41c17f8_a></use>
                <rect width=24 height=2 x=7 y=8 fill=#666 rx=1></rect>
                <rect width=20 height=2 x=7 y=17 fill=#666 rx=1></rect>
                <rect width=8 height=2 x=7 y=26 fill=#666 rx=1></rect>
            </g>
        </symbol>
        <symbol viewbox="0 0 38 38" id=order.070ae2a>
            <defs>
                <lineargradient id=order.070ae2a_a x1=50% x2=50% y1=100% y2=0%>
                    <stop offset=0% stop-color=#2BAEFF></stop>
                    <stop offset=100% stop-color=#0095FF></stop>
                </lineargradient>
            </defs>
            <g fill=none fill-rule=evenodd>
                <rect width=38 height=38 fill=url(#order.070ae2a_a) rx=2></rect>
                <rect width=24 height=2 x=7 y=8 fill=#FFF rx=1></rect>
                <rect width=20 height=2 x=7 y=17 fill=#FFF rx=1></rect>
                <rect width=8 height=2 x=7 y=26 fill=#FFF rx=1></rect>
            </g>
        </symbol>
        <symbol xmlns:xlink=http://www.w3.org/1999/xlink viewbox="0 0 38 38" id=profile-regular.c151d62>
            <defs>
                <path id=profile-regular.c151d62_a
                      d="M10 11.833V8.999A8.999 8.999 0 0 1 19 0c4.97 0 9 4.04 9 8.999v2.834l-.013.191C27.657 16.981 23.367 21 19 21c-4.616 0-8.64-4.02-8.987-8.976L10 11.833z"></path>
                <mask id=profile-regular.c151d62_c width=18 height=21 x=0 y=0 fill=#fff>
                    <use xlink:href=#profile-regular.c151d62_a></use>
                </mask>
                <path id=profile-regular.c151d62_b
                      d="M0 32.675C0 26.763 10.139 22 19.027 22 27.916 22 38 26.763 38 32.757v3.312C38 37.136 37.098 38 35.997 38H2.003C.897 38 0 37.137 0 36.037v-3.362z"></path>
                <mask id=profile-regular.c151d62_d width=38 height=16 x=0 y=0 fill=#fff>
                    <use xlink:href=#profile-regular.c151d62_b></use>
                </mask>
            </defs>
            <g fill=none fill-rule=evenodd stroke=#666 stroke-width=4>
                <use mask=url(#profile-regular.c151d62_c) xlink:href=#profile-regular.c151d62_a></use>
                <use mask=url(#profile-regular.c151d62_d) xlink:href=#profile-regular.c151d62_b></use>
            </g>
        </symbol>
        <symbol viewbox="0 0 38 38" id=profile.dbc5ebf>
            <defs>
                <lineargradient id=profile.dbc5ebf_a x1=50% x2=50% y1=100% y2=0%>
                    <stop offset=0% stop-color=#2BAEFF></stop>
                    <stop offset=100% stop-color=#0095FF></stop>
                </lineargradient>
            </defs>
            <path fill=url(#profile.dbc5ebf_a) fill-rule=evenodd
                  d="M10 11.833V8.999A8.999 8.999 0 0 1 19 0c4.97 0 9 4.04 9 8.999v2.834l-.013.191C27.657 16.981 23.367 21 19 21c-4.616 0-8.64-4.02-8.987-8.976L10 11.833zM0 32.675C0 26.763 10.139 22 19.027 22 27.916 22 38 26.763 38 32.757v3.312C38 37.136 37.098 38 35.997 38H2.003C.897 38 0 37.137 0 36.037v-3.362z"></path>
        </symbol>
        <symbol viewbox="0 0 126 126" id=expired.1331b14>
            <path fill=#9B9B9B fill-rule=evenodd
                  d="M63 125.5c34.518 0 62.5-27.982 62.5-62.5S97.518.5 63 .5.5 28.482.5 63s27.982 62.5 62.5 62.5zM15.156 66.678l-3.073-1.258 2.868-1.674.248-3.31 2.478 2.21 3.225-.79-1.335 3.04 1.746 2.825-3.302-.33-2.147 2.533-.704-3.245zm4.07-24.55l-2.03-2.625 3.32-.015 1.87-2.744 1.04 3.153 3.187.93-2.677 1.964.1 3.32-2.695-1.94-3.124 1.122 1.01-3.163zm15.8-19.223l-.446-3.29 2.883 1.646 2.99-1.44-.674 3.25 2.294 2.4-3.3.363-1.573 2.924-1.363-3.027-3.267-.592 2.457-2.233zm23.296-8.75l1.258-3.072 1.674 2.868 3.31.248-2.21 2.478.79 3.225-3.04-1.335-2.825 1.746.33-3.302-2.533-2.147 3.245-.704zm24.55 4.072l2.625-2.032.015 3.32 2.744 1.87-3.153 1.04-.93 3.188-1.964-2.677-3.32.1 1.94-2.695-1.122-3.124 3.163 1.01zm27.972 39.095l3.073 1.258-2.868 1.674-.248 3.31-2.478-2.21-3.225.79 1.335-3.04-1.746-2.825 3.302.33 2.147-2.533.704 3.245zm-4.07 24.55l2.03 2.625-3.32.015-1.87 2.744-1.04-3.153-3.187-.93 2.677-1.964-.1-3.32 2.695 1.94 3.124-1.122-1.01 3.163zm-15.8 19.223l.446 3.29-2.883-1.646-2.99 1.44.674-3.25-2.294-2.4 3.3-.363 1.573-2.924 1.363 3.027 3.267.592-2.457 2.233zm-23.296 8.75l-1.258 3.072-1.674-2.868-3.31-.248 2.21-2.478-.79-3.225 3.04 1.335 2.825-1.746-.33 3.302 2.533 2.147-3.245.704zm-24.55-4.072l-2.625 2.032-.015-3.32-2.744-1.87 3.153-1.04.93-3.188 1.964 2.677 3.32-.1-1.94 2.695 1.122 3.124-3.163-1.01zM74.257 41.7a23.764 23.764 0 0 0-22.17.092 23.767 23.767 0 0 0-12.508 18.646l.995.1a22.767 22.767 0 0 1 11.983-17.863 22.764 22.764 0 0 1 21.238-.088l.462-.887zm11.387 22.436A22.764 22.764 0 0 1 74.313 82.1a22.767 22.767 0 0 1-21.5.696l-.44.897a23.767 23.767 0 0 0 22.44-.727A23.764 23.764 0 0 0 86.64 64.214l-.997-.078zM63 122.5C30.14 122.5 3.5 95.86 3.5 63S30.14 3.5 63 3.5s59.5 26.64 59.5 59.5-26.64 59.5-59.5 59.5zm14.127-71.148l1.14 1.975 3.388-1.956-1.14-1.974-3.388 1.956zm2.704-3.14l-1.055-1.83-3.388 1.956 1.056 1.83 3.388-1.957zm.237 8.232l3.388-1.956-1.14-1.974-3.388 1.956 1.14 1.974zm-6.89-8.715a24.73 24.73 0 0 0-.892-1.453 7.288 7.288 0 0 0-.79-.985c.31-.104.617-.227.924-.367a6.52 6.52 0 0 0 .842-.46c.13-.093.226-.12.285-.08.06.04.066.128.017.267a.653.653 0 0 0-.032.378c.03.113.09.253.187.42l.85 1.475 3.39-1.956a39.962 39.962 0 0 0-1.01-1.677c-.25-.383-.472-.665-.67-.847a13.33 13.33 0 0 0 1.857-.767c.19-.09.313-.107.374-.05.062.057.064.148.007.273-.09.2-.128.356-.117.47.01.114.06.247.147.4l.792 1.37c.24-.157.48-.318.718-.483a9.91 9.91 0 0 0 .673-.513l1.02 1.766c-.26.095-.52.204-.78.327-.262.123-.525.243-.79.36l4.655 8.063c.234-.17.46-.333.675-.486.217-.153.43-.318.643-.496l.912 1.58c-.21.085-.434.177-.672.278-.238.1-.534.243-.888.43-.354.185-.79.423-1.307.712a205.733 205.733 0 0 0-3.876 2.238c-.516.307-.943.567-1.28.78-.34.215-.615.402-.828.562-.212.16-.408.31-.586.45l-.912-1.58c.638-.24 1.29-.533 1.958-.882l-4.668-8.085a20.893 20.893 0 0 0-1.67 1.186l-1.02-1.767a21.623 21.623 0 0 0 1.862-.854zm14.762 2.285l3.387-1.956-2.124-3.68-3.388 1.956 2.124 3.68zm-1.45-10.332l-3.387 1.956 1.956 3.387 3.387-1.956-1.956-3.387zm2.11 11.67c.274.634.514 1.305.717 2.01.204.704.36 1.408.47 2.11.11.704.167 1.4.17 2.093a10.19 10.19 0 0 1-.17 1.94c-.51-.15-1.18-.14-2.008.024.213-.974.312-1.88.298-2.723a10.595 10.595 0 0 0-.37-2.558c-.23-.865-.573-1.77-1.028-2.72a48.398 48.398 0 0 0-1.714-3.208l-2.7-4.676a25.767 25.767 0 0 0-.875-1.42 21.753 21.753 0 0 0-.85-1.186c.525-.21 1.043-.45 1.554-.717.51-.267 1.112-.6 1.805-1a60.923 60.923 0 0 0 1.893-1.136 17.45 17.45 0 0 0 1.502-1.047c.137.364.325.787.565 1.267.24.48.517.99.83 1.53l7.535 13.054a6.1 6.1 0 0 1 .46.94.97.97 0 0 1-.036.756c-.115.25-.347.527-.698.832-.35.304-.864.688-1.54 1.15a3.186 3.186 0 0 0-.647-.858 4.97 4.97 0 0 0-1.038-.717 13.81 13.81 0 0 0 1.096-.55c.264-.152.45-.295.555-.43a.502.502 0 0 0 .108-.437 2.097 2.097 0 0 0-.243-.566l-2.172-3.762-3.47 2.004zm-1.954 7.223a6.16 6.16 0 0 0-1.466-.69 6.537 6.537 0 0 0-1.563-.332l.69-1.59a14.604 14.604 0 0 1 3.05.817l-.71 1.794zm-4.033-.027a2.137 2.137 0 0 0-.287.51 6.12 6.12 0 0 0-.26.872 23.78 23.78 0 0 0-.283 1.452c-.1.594-.225 1.34-.37 2.237a3.37 3.37 0 0 0-.92-.078 5.34 5.34 0 0 0-1.096.19 8.492 8.492 0 0 0 .812-2.41c.15-.843.175-1.782.077-2.816.39.034.75.034 1.08 0a8.61 8.61 0 0 0 1.06-.182c.14-.044.227-.04.26.017.03.056.007.126-.074.21zm-17.506-5.745c.68-.392 1.22-.72 1.624-.98.405-.26.798-.538 1.182-.834l1.044 1.81c-.426.19-.86.4-1.3.626a40.64 40.64 0 0 0-1.66.917l5.015 8.688c.21.36.354.684.435.97.082.285.043.584-.118.9-.16.313-.468.676-.924 1.086-.455.41-1.11.918-1.962 1.52a10.17 10.17 0 0 0-.84-.83 7.863 7.863 0 0 0-1.12-.836 20.7 20.7 0 0 0 1.457-.813c.36-.226.625-.43.797-.612.172-.183.262-.346.27-.49a.783.783 0 0 0-.117-.444l-4.68-8.105-4.448 2.568c-.846.488-1.512.886-2 1.195-.485.31-.936.6-1.35.877l-1.03-1.788c.236-.1.472-.204.706-.31.234-.108.484-.234.75-.38a93.69 93.69 0 0 0 2.035-1.132l4.45-2.568a106.39 106.39 0 0 0-1.3-2.202c-.33-.54-.576-.92-.74-1.138.35-.13.72-.29 1.105-.486.387-.194.696-.378.93-.55.192-.147.346-.176.462-.086.117.09.133.205.048.346a.79.79 0 0 0-.08.56c.044.186.098.335.162.446l1.2 2.08zm-1.79 11.537a25.633 25.633 0 0 0-1.934-1.475 35.97 35.97 0 0 0-2.03-1.31l1.267-1.644a38.25 38.25 0 0 1 2.034 1.195c.68.428 1.346.9 1.993 1.412l-1.33 1.822zm-12.53-7.01c.706.293 1.41.608 2.11.942.702.334 1.376.693 2.022 1.078l-1.13 2.12a56.81 56.81 0 0 0-2.01-1.152 41.097 41.097 0 0 0-2.06-1.044l1.067-1.945zM63 118.25c30.514 0 55.25-24.736 55.25-55.25S93.514 7.75 63 7.75 7.75 32.486 7.75 63 32.486 118.25 63 118.25zm-2.237-47.53c.262-.058.562-.097.9-.118.34-.02.753-.04 1.24-.063.52-.025 1.176-.163 1.964-.415.788-.25 1.72-.646 2.794-1.184 1.077-.536 2.303-1.235 3.682-2.096a87.9 87.9 0 0 0 4.634-3.133 10.2 10.2 0 0 0 .24 1.4c.098.378.23.74.394 1.09a321.96 321.96 0 0 1-4.068 2.362 69.403 69.403 0 0 1-3.052 1.65c-.88.445-1.643.802-2.29 1.074s-1.236.483-1.768.633c-.533.15-1.03.256-1.492.32-.462.063-.954.107-1.476.13-.62.046-1.087.126-1.4.24-.31.117-.536.344-.674.682-.123.33-.22.74-.286 1.232a18.89 18.89 0 0 0-.144 1.62 7.14 7.14 0 0 0-1.164-.31 9.118 9.118 0 0 0-1.23-.136c.132-.575.256-1.07.374-1.49.118-.42.23-.785.338-1.096.106-.31.212-.575.318-.793.105-.22.214-.407.326-.564l-3.66-6.34c-.582.337-1.08.634-1.495.892-.415.257-.75.498-1.01.722l-.972-1.684c.293-.132.648-.3 1.066-.505.42-.203.83-.42 1.23-.653a31.8 31.8 0 0 0 1.27-.775c.433-.277.775-.516 1.028-.718.14.4.292.778.46 1.134.17.355.413.81.733 1.364l3.193 5.53zm-15.907-.43l-2.712-4.7-5.425 3.133c-1.456.84-2.783 1.63-3.983 2.368-1.2.74-2.125 1.344-2.778 1.813l-1.237-2.14c.307-.14.708-.335 1.202-.583.494-.25 1.055-.54 1.684-.876a143.593 143.593 0 0 0 4.375-2.429 153.71 153.71 0 0 0 4.442-2.648c1.175-.734 2.054-1.315 2.638-1.745.15.357.367.813.652 1.37a42.88 42.88 0 0 0 1.05 1.915l1.848 3.2a32.46 32.46 0 0 0 1.93 2.96l-2.057 1.188-.72-1.247-9.395 5.424 3.072 5.32c.224.39.415.68.574.875.158.195.345.304.562.327.216.023.5-.045.853-.202.353-.157.838-.405 1.455-.743.876-.47 1.734-.942 2.577-1.42a68.054 68.054 0 0 0 2.465-1.465c.754-.453 1.335-.84 1.743-1.158.407-.318.686-.66.836-1.023.15-.364.185-.81.104-1.334a26.6 26.6 0 0 0-.45-2.124c.843.437 1.734.523 2.67.26.206 1.026.324 1.854.354 2.483.03.628-.083 1.184-.34 1.665-.258.48-.698.943-1.32 1.386-.623.443-1.495.988-2.617 1.636l-2.545 1.47c-.908.524-1.758.996-2.55 1.417-1.063.558-1.902.97-2.517 1.23-.615.264-1.123.368-1.524.313-.402-.055-.75-.274-1.045-.657-.297-.385-.652-.937-1.068-1.658l-3.444-5.965a27.726 27.726 0 0 0-1.155-1.855c-.337-.49-.602-.835-.793-1.04.37-.157.762-.342 1.176-.553.414-.212.79-.425 1.13-.64.185-.125.32-.144.41-.056.087.088.085.214-.005.377a.624.624 0 0 0-.105.394c.015.12.082.286.202.494l.384.665 9.396-5.424zM10.402 63c0-29.05 23.55-52.598 52.598-52.598 29.05 0 52.598 23.55 52.598 52.598 0 29.05-23.55 52.598-52.598 52.598-29.05 0-52.598-23.55-52.598-52.598z"></path>
        </symbol>
        <symbol viewbox="0 0 127 127" id=failure.8cb323d>
            <path fill=#9B9B9B fill-rule=evenodd
                  d="M15.273 67.207l-3.097-1.268 2.89-1.688.25-3.337 2.497 2.227 3.252-.794-1.348 3.064 1.76 2.846-3.33-.334-2.163 2.554-.71-3.27zm4.104-24.745l-2.05-2.647 3.348-.015 1.885-2.766 1.05 3.178 3.212.938-2.7 1.98.102 3.345-2.716-1.955-3.15 1.13 1.02-3.188zm15.926-19.378l-.45-3.316 2.906 1.66 3.014-1.454-.68 3.277 2.313 2.42-3.327.365-1.585 2.948-1.376-3.05-3.294-.598 2.477-2.25zm23.482-8.82l1.268-3.096 1.687 2.89 3.337.25-2.227 2.497.794 3.252-3.064-1.348-2.846 1.76.334-3.33-2.554-2.164 3.27-.71zM83.53 18.37l2.647-2.05.015 3.347 2.766 1.885-3.178 1.05-.938 3.212-1.98-2.7-3.345.102 1.955-2.716-1.13-3.15 3.188 1.02zm28.197 39.407l3.097 1.268-2.89 1.687-.25 3.337-2.497-2.228-3.25.794 1.346-3.064-1.76-2.846 3.33.334 2.163-2.554.71 3.27zm-4.104 24.745l2.05 2.647-3.348.014-1.885 2.766-1.05-3.178-3.212-.938 2.7-1.98-.102-3.345 2.716 1.954 3.15-1.13-1.02 3.188zM91.697 101.9l.45 3.316-2.906-1.66-3.014 1.454.68-3.277-2.313-2.42 3.327-.364L89.505 96l1.376 3.052 3.294.597-2.477 2.25zm-23.482 8.82l-1.268 3.096-1.687-2.89-3.337-.25 2.227-2.497-.794-3.253 3.064 1.348 2.846-1.76-.334 3.33 2.554 2.163-3.27.71zm-24.745-4.105l-2.647 2.05-.015-3.348-2.766-1.885 3.178-1.05.938-3.212 1.98 2.7 3.345-.102-1.955 2.716 1.13 3.15-3.188-1.02zM74.846 42.03a23.954 23.954 0 0 0-22.347.093 23.957 23.957 0 0 0-12.61 18.795l1.004.1a22.95 22.95 0 0 1 12.078-18.005 22.946 22.946 0 0 1 21.41-.09l.464-.894zm11.478 22.615a22.946 22.946 0 0 1-11.42 18.108 22.95 22.95 0 0 1-21.67.7l-.447.905a23.957 23.957 0 0 0 22.62-.732A23.954 23.954 0 0 0 87.33 64.724l-1.006-.08zm-37.113 5.24l-4.8-8.314-15.505 8.953.84 1.455 13.988-8.076 3.132 5.424-11.37 6.564-1.727-2.993-1.496.864 6.324 10.955c.936 1.62 2.185 2.01 3.764 1.097l11.474-6.624c.807-.522 1.298-1.11 1.504-1.81.145-.806-.41-2.536-1.69-5.233l-1.72.383c1.217 2.346 1.735 3.82 1.577 4.41-.147.418-.47.77-.927 1.035l-10.642 6.144c-.852.492-1.52.295-2.012-.557l-3.6-6.235 12.887-7.44zm3.442-13.96c.673 3.326.564 6.354-.346 9.096l1.904.37c.413-1.346.624-2.854.664-4.512l4.968-2.868.78 1.35c.534 1.023.99 2.006 1.33 2.975l-8.045 4.644.828 1.433 7.732-4.464c.3 1.24.416 2.447.355 3.59-.292 2.47-1.775 5.182-4.393 8.135l1.542.8c2.672-2.956 4.168-5.788 4.51-8.507.152-1.418.03-2.926-.368-4.526 3.066 2.72 7.417 3.727 13.076 3.064l.075-1.79c-5.303.846-9.33.066-12.075-2.34l7.732-4.463-.828-1.434-8.584 4.955c-.36-.957-.816-1.94-1.35-2.962l-.78-1.35 6.963-4.02-.84-1.456-6.963 4.02-2.1-3.637-1.538.888 2.1 3.637-4.2 2.424a30.786 30.786 0 0 0-.445-3.318l-1.705.264zm21.876-7.086c.215 2.34.11 4.508-.3 6.49l1.71.176c.37-2.097.46-4.34.25-6.767l-1.66.1zm7.698.708l.4-1.56c-1.87-.695-3.4-1.14-4.616-1.326l-.4 1.422c1.44.333 2.964.81 4.616 1.464zM77.396 54l-.323 1.6c1.28.202 2.63.476 4.008.845-.134 2.6-.86 4.987-2.182 7.163l1.682.802c1.336-2.295 2.057-4.79 2.218-7.487 1.138.34 2.354.718 3.62 1.18l.375-1.797a49.185 49.185 0 0 0-4.018-1.2 22.76 22.76 0 0 0-.65-4.39l-1.602.203a22.94 22.94 0 0 1 .538 3.763 45.295 45.295 0 0 0-3.664-.683zM73.85 42.912l-1.416 1.15c.746.427 1.508.93 2.252 1.498l-4.26 2.46.827 1.434 9.623-5.556-.828-1.434-3.907 2.256a39.916 39.916 0 0 0-2.29-1.808zm10.454.587l3.096-1.79c1.44 2.69 2.224 5.34 2.403 7.954-1.702-1.124-3.415-2.602-5.137-4.434-.098-.553-.24-1.136-.362-1.73zm-20.804 83c34.794 0 63-28.206 63-63S98.294.5 63.5.5s-63 28.206-63 63 28.206 63 63 63zm0-3.024c-33.124 0-59.976-26.852-59.976-59.976 0-33.124 26.852-59.976 59.976-59.976 33.124 0 59.976 26.852 59.976 59.976 0 33.124-26.852 59.976-59.976 59.976zm0-4.284c30.758 0 55.692-24.934 55.692-55.692S94.258 7.808 63.5 7.808 7.808 32.742 7.808 63.5s24.934 55.692 55.692 55.692zM10.48 63.5c0-29.28 23.74-53.02 53.02-53.02 29.28 0 53.02 23.74 53.02 53.02 0 29.28-23.74 53.02-53.02 53.02-29.28 0-53.02-23.74-53.02-53.02zm79.33-11.955c-.158 2.558-1.02 5.05-2.55 7.486l1.63.86c1.396-2.385 2.236-4.865 2.514-7.408 2.244 1.198 4.51 1.858 6.784 1.958l.117-1.814c-2.25-.058-4.537-.706-6.826-1.934-.017-3.15-.92-6.396-2.705-9.773l1.767-1.02-.84-1.456-5.842 3.372a44.97 44.97 0 0 0-1.257-3.57l-1.64.615c1.746 4.176 2.524 7.828 2.39 10.954l1.615.592c.056-.864.088-1.77.03-2.733 1.576 1.53 3.18 2.82 4.813 3.872z"></path>
        </symbol>
        <symbol viewbox="0 0 126 126" id=used.032eb77>
            <path fill=#9B9B9B fill-rule=evenodd
                  d="M15.156 66.678l-3.073-1.258 2.868-1.674.248-3.31 2.478 2.21 3.225-.79-1.335 3.04 1.746 2.825-3.302-.33-2.147 2.533-.704-3.245zm4.07-24.55l-2.03-2.625 3.32-.015 1.87-2.744 1.04 3.153 3.187.93-2.677 1.964.1 3.32-2.695-1.94-3.124 1.122 1.01-3.163zm15.8-19.223l-.446-3.29 2.883 1.646 2.99-1.44-.674 3.25 2.294 2.4-3.3.363-1.573 2.924-1.363-3.027-3.267-.592 2.457-2.233zm23.296-8.75l1.258-3.072 1.674 2.868 3.31.248-2.21 2.478.79 3.225-3.04-1.335-2.825 1.746.33-3.302-2.533-2.147 3.245-.704zm24.55 4.072l2.625-2.032.015 3.32 2.744 1.87-3.153 1.04-.93 3.188-1.964-2.677-3.32.1 1.94-2.695-1.122-3.124 3.163 1.01zm27.972 39.095l3.073 1.258-2.868 1.674-.248 3.31-2.478-2.21-3.225.79 1.335-3.04-1.746-2.825 3.302.33 2.147-2.533.704 3.245zm-4.07 24.55l2.03 2.625-3.32.015-1.87 2.744-1.04-3.153-3.187-.93 2.677-1.964-.1-3.32 2.695 1.94 3.124-1.122-1.01 3.163zm-15.8 19.223l.446 3.29-2.883-1.646-2.99 1.44.674-3.25-2.294-2.4 3.3-.363 1.573-2.924 1.363 3.027 3.267.592-2.457 2.233zm-23.296 8.75l-1.258 3.072-1.674-2.868-3.31-.248 2.21-2.478-.79-3.225 3.04 1.335 2.825-1.746-.33 3.302 2.533 2.147-3.245.704zm-24.55-4.072l-2.625 2.032-.015-3.32-2.744-1.87 3.153-1.04.93-3.188 1.964 2.677 3.32-.1-1.94 2.695 1.122 3.124-3.163-1.01zM74.257 41.7a23.764 23.764 0 0 0-22.17.092 23.767 23.767 0 0 0-12.508 18.646l.995.1a22.767 22.767 0 0 1 11.983-17.863 22.764 22.764 0 0 1 21.238-.088l.462-.887zm11.387 22.436A22.764 22.764 0 0 1 74.313 82.1a22.767 22.767 0 0 1-21.5.696l-.44.897a23.767 23.767 0 0 0 22.44-.727A23.764 23.764 0 0 0 86.64 64.214l-.997-.078zM47.52 72.318l-5.088-8.14-15.183 9.487.89 1.425 13.697-8.56 3.32 5.312L34.022 78.8l-1.83-2.932-1.467.916L37.43 87.51c.99 1.588 2.252 1.93 3.8.965l11.234-7.02c.79-.55 1.26-1.155 1.44-1.863.117-.81-.498-2.518-1.872-5.17l-1.704.443c1.297 2.303 1.867 3.758 1.73 4.353-.133.422-.443.786-.89 1.066l-10.422 6.512c-.834.52-1.51.348-2.03-.486L34.9 80.204l12.62-7.886zM53.414 58.7l.878 1.405 5.332-3.332 1.208 1.934-4.64 2.9 3.6 5.76 4.558-2.85c.77 1.502 1.21 2.84 1.342 4.002a17.179 17.179 0 0 1-4.674-.958l-.636 1.473a18.18 18.18 0 0 0 5.15 1.085c-.377 1.48-1.548 3.004-3.484 4.525l1.47.95c2.145-1.822 3.417-3.636 3.817-5.442 2.946-.086 5.894-.938 8.858-2.536l-.51-1.633c-2.756 1.524-5.51 2.368-8.246 2.52-.087-1.36-.618-2.98-1.6-4.915l4.844-3.028-3.598-5.76-4.763 2.976-1.21-1.933 5.598-3.498-.877-1.404-5.596 3.497-1.298-2.076-1.486.93 1.298 2.075-5.333 3.33zm15.055 1.404l-3.4 2.124c-.1-.163-.182-.338-.283-.5l-1.654-2.647 3.38-2.11 1.957 3.134zm-4.884 3.052L60.35 65.18l-1.96-3.136 3.257-2.035 1.654 2.645c.103.163.184.34.286.5zm-10.6 3.144l7.095 11.357 1.467-.916-8.56-13.696a31.668 31.668 0 0 0-.917-5.68l-1.78.233c1.074 3.8 1.33 7.604.763 11.41l1.455 1.24c.252-1.317.398-2.624.477-3.947zm21.298-13.65l5.17-3.23 2.226 3.562-5.17 3.23-2.226-3.56zm2.984 4.957l5.25-3.282 3.727 5.964 1.506-.942-3.725-5.964 5.536-3.46 2.214 3.542c.534.855.415 1.524-.318 1.982-.692.433-1.47.863-2.31 1.33l1.29 1.204 2.34-1.463c1.425-.89 1.692-2.048.802-3.473L84.053 37.8 68.89 47.275l6.104 9.77c1.7 2.814 2.467 5.533 2.296 8.16l1.743.296c.234-2.523-.36-5.15-1.765-7.896zm11.454-9.025l-5.536 3.46-2.226-3.563 5.536-3.46 2.226 3.562zm-3.078-4.926l-5.536 3.46-2.188-3.5 5.536-3.46 2.188 3.5zM63 125.5c34.518 0 62.5-27.982 62.5-62.5S97.518.5 63 .5.5 28.482.5 63s27.982 62.5 62.5 62.5zm0-3C30.14 122.5 3.5 95.86 3.5 63S30.14 3.5 63 3.5s59.5 26.64 59.5 59.5-26.64 59.5-59.5 59.5zm0-4.25c30.514 0 55.25-24.736 55.25-55.25S93.514 7.75 63 7.75 7.75 32.486 7.75 63 32.486 118.25 63 118.25zM10.402 63c0-29.05 23.55-52.598 52.598-52.598 29.05 0 52.598 23.55 52.598 52.598 0 29.05-23.55 52.598-52.598 52.598-29.05 0-52.598-23.55-52.598-52.598zm66.012-18.444l2.188 3.5-5.17 3.23-2.187-3.5 5.17-3.23z"></path>
        </symbol>
        <symbol viewbox="0 0 120 120" id=select.482ce59>
            <circle cx=60 cy=60 r=60></circle>
            <path fill=#FFF
                  d="M63.84 84.678a1.976 1.976 0 0 1-.387.545L55.478 93.2a1.996 1.996 0 0 1-2.83-.006L24.173 64.716a2.005 2.005 0 0 1-.005-2.828l7.976-7.976a1.996 1.996 0 0 1 2.828.005l19.016 19.015 37.51-37.512a1.99 1.99 0 0 1 2.823 0l7.977 7.977c.784.784.78 2.043 0 2.823L63.84 84.678z"></path>
        </symbol>
        <symbol viewbox="0 0 547 987" id=arrow-right.c6f18a9>
            <path fill=#999 fill-rule=evenodd
                  d="M0 931.973l51.2 54.613 494.933-494.933L51.2.133 0 51.333l440.32 440.32L0 931.973z"></path>
        </symbol>
    </defs>
</svg>
<svg xmlns=http://www.w3.org/2000/svg xmlns:xlink=http://www.w3.org/1999/xlink
     style=position:absolute;width:0;height:0;visibility:hidden>
    <defs>
        <symbol viewbox="0 0 44 44" id=cart-add>
            <path fill-rule=evenodd
                  d="M22 0C9.8 0 0 9.8 0 22s9.8 22 22 22 22-9.8 22-22S34.2 0 22 0zm0 42C11 42 2 33 2 22S11 2 22 2s20 9 20 20-9 20-20 20z"
                  clip-rule=evenodd></path>
            <path fill-rule=evenodd d="M32 20c1.1 0 2 .9 2 2s-.9 2-2 2H12c-1.1 0-2-.9-2-2s.9-2 2-2h20z"
                  clip-rule=evenodd></path>
        </symbol>
        <symbol viewbox="0 0 44 44" id=cart-minus>
            <path fill=none d="M0 0h44v44H0z"></path>
            <path fill-rule=evenodd
                  d="M22 0C9.8 0 0 9.8 0 22s9.8 22 22 22 22-9.8 22-22S34.2 0 22 0zm10 24h-8v8c0 1.1-.9 2-2 2s-2-.9-2-2v-8h-8c-1.1 0-2-.9-2-2s.9-2 2-2h8v-8c0-1.1.9-2 2-2s2 .9 2 2v8h8c1.1 0 2 .9 2 2s-.9 2-2 2z"
                  clip-rule=evenodd></path>
        </symbol>
        <symbol viewbox="0 0 24 32" id=cart-remove>
            <path fill=#bbb fill-rule=evenodd
                  d="M21.5 10h-19c-1.1 0-1.918.896-1.819 1.992l1.638 18.016C2.419 31.104 3.4 32 4.5 32h15c1.1 0 2.081-.896 2.182-1.992l1.637-18.016A1.798 1.798 0 0 0 21.5 10zM8 28H5L4 14h4v14zm6 0h-4V14h4v14zm5 0h-3V14h4l-1 14zm2-24h-2.941l-.353-2.514C17.592.669 16.823 0 15.998 0H8c-.825 0-1.593.668-1.708 1.486L5.94 4H3a3 3 0 0 0-3 3v1h24V7a3 3 0 0 0-3-3zM8.24 2h7.52l.279 2H7.96l.28-2z"></path>
        </symbol>
        <symbol viewbox="0 0 14 16" id=cart>
            <path fill=#FFF fill-rule=evenodd
                  d="M12.364 2.998H2.088L1.816.687a.455.455 0 0 0-.478-.431L.431.303A.454.454 0 0 0 0 .78l1.256 10.893c.006.293.011 1.325.933 1.325h9.546a.455.455 0 0 0 .455-.454v-.881a.454.454 0 0 0-.455-.455H3.05l-.11-.937h8.606c.998 0 1.889-.724 1.989-1.616l.455-4.04c.1-.893-.628-1.617-1.626-1.617zm-.45 4.245c-.075.669-.317 1.212-1.066 1.212H2.727L2.3 4.812h8.821c.749 0 1.065.543.99 1.212l-.197 1.219zM2.416 15.79a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm9.092 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"></path>
        </symbol>
    </defs>
</svg>
<div id=app></div>
<script type=text/javascript src=../api.js></script>
<script type=text/javascript src=./static/js/manifest.59bf1f05749e37c381b0.js></script>
<script type=text/javascript src=./static/js/vendor.5ae9090e4744f6e68d24.js></script>
<script type=text/javascript src=./static/js/app.4bc07750056465c05616.js></script>
</body>
</html>
```

#### 创建控制器

```php
php artisan make:controller Api/ShopController
```

#### 得到所有数据

```php
<?php

namespace App\Http\Controllers\Api;

use App\Models\ShopInformation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShopController extends Controller
{

        public function index(){

            //得到所有的店铺，设置状态为1
            $shops=ShopInformation::where("status",1)->get();

            return $shops;
        }
}
```

#### 修改public/api.js

```php
  // 获得商家列表接口
  businessList: '/api/shop/index',
```

#### 追加时间和距离

```php

public function index(){

            //得到所有的店铺，设置状态为1
            $shops=ShopInformation::where("status",1)->get();
            //追加时间和距离
            foreach ($shops as $k =>$v){
                $shops[$k]->shop_img=env("ALIYUN_OSS_URL").$v->shop_img;
                $shops[$k]->distance=rand(1000,5000);
                $shops[$k]->estimate_time=ceil($shops[$k]['distance'] / rand(100, 150));
            }
            return $shops;

        }
```

#### 修改public/api.js

```php
  // 获得指定商家接口
  business: '/api/shop/detail',
```

### 路由routes/api.php

```php
Route::get("shop/index","Api\ShopController@index");
Route::get("shop/detail","Api\ShopController@detail");
```

### 店铺及商品接口Http/Controllers/Api/ShopController.php

```php
  public function detail()
        {
            $id=\request()->get('id');
            $shop=ShopInformation::find($id);
            $shop->shop_img=env("ALIYUN_OSS_URL").$shop->shop_img;
            $shop->service_code = 4.6;


            $shop->evaluate = [
                [
                    "user_id" => 12344,
                    "username" => "w******k",
                    "user_img" => "http=>//www.homework.com/images/slider-pic4.jpeg",
                    "time" => "2017-2-22",
                    "evaluate_code" => 1,
                    "send_time" => 30,
                    "evaluate_details" => "不怎么好吃"],
                ["user_id" => 12344,
                    "username" => "w******k",
                    "user_img" => "http=>//www.homework.com/images/slider-pic4.jpeg",
                    "time" => "2017-2-22",
                    "evaluate_code" => 4.5,
                    "send_time" => 30,
                    "evaluate_details" => "很好吃"]
            ];

            $cates=MenuCategory::where("information_id",$id)->get();

            //当前分类有哪些商品
            foreach ($cates as $k=>$cate){
                $goods=$cate->menus;

                foreach ($goods as $v=>$good){
                    $goods[$v]->goods_img=env("ALIYUN_OSS_URL").$good->goods_img;
                }

                $cates[$k]->goods_list=$goods;

            }

            $shop->commodity=$cates;
            return $shop;
        }
```

### 搜索 Http/Controllers/Api/ShopController.php

```php
public function index(){

            //接收数据
            $keyword=\request()->get('keyword');
            if($keyword!=null){
                $shops=ShopInformation::where("status",1)->where('shop_name','like','%{$keyword}%')->get();
           }else{
                //得到所有的店铺，设置状态为1
                $shops=ShopInformation::where("status",1)->get();
            }
}
```

# Day06

## 开发任务

接口开发

- 用户注册
- 用户登录
- 忘记密码
- 发送短信 要求
- 创建会员表
- 短信验证码发送成功后,保存到redis,并设置有效期5分钟
- 用户注册时,从redis取出验证码进行验证

## 实现步骤

### 安装https://packagist.org/packages/mrgoon/aliyun-sms

```php
composer require mrgoon/aliyun-sms -vvv
```

### 安装 https://laravel-china.org/docs/laravel/5.5/redis/1331

```php
composer require predis/predis -vvv
```

#### 在阿里云上的短信服务中添加签名管理和模板管理

![1540975153853](C:\Users\ADMINI~1\AppData\Local\Temp\1540975153853.png)

![1540975221575](C:\Users\ADMINI~1\AppData\Local\Temp\1540975221575.png)

### 建立控制器member

```php
//手机验证码
    public function sms(Request $request){
        //接收参数
        $tel=$request->get("tel");
        //随机生成4位验证码
        $code=mt_rand(1000,9999);
        //把验证码存起来  五分钟有效
        Redis::setex("tel_".$tel,5*60,$code);
        //把验证码发给手机
        $config = [
            'access_key' => env("ALIYUNU_ACCESS_ID"),
            'access_secret' => env("ALIYUNU_ACCESS_KEY"),
            'sign_name' => '个人分享',
        ];

        $aliSms = new AliSms();
        $response = $aliSms->sendSms($tel, 'SMS_149417618', ['code'=> $code], $config);
        //返回
//        dd($response);
        $data=[
            "status"=>true,
            "message"=>"获取验证码成功".$code
        ];

        return $data;

    }
```

### 路由routes/api.php

```php
Route::get("member/sms","Api\MemberController@sms");
```

### 配置public/api.js

```php
  // 获取短信验证码接口
  sms: '/api/member/sms',
```

### 注册

```php
  //用户注册
    public function reg( Request $request )
    {
        //验证
        $data=$request->post();
        $code=Redis::get('tel_'.$data['tel']);
        //判断验证码是否正确
        if($data['sms']==$code){
            $data['password']=Hash::make($data['password']);

            if (Member::create($data)) {
                $data = [
                    'status' => "true",
                    'message' => "注册成功 请登录",
                ];
            } else {

                $data = [
                    'status' => "false",
                    'message' => "注册失败",
                ];
            }
        }
        return $data;
    }
```

### 注册接口

```php
  // 注册接口
  regist: '/api/member/reg',
```

### 路由

```php
Route::any("member/reg","Api\MemberController@reg");
```

### Models/member.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable=["username","password","tel"];
}
```

### 登录

```php
 public function login(Request $request){
            //接收数据
            $name=$request->name;
            $password=$request->password;
            //判断用户名是否存在
            if ($member=Member::where("username",$name)->first()){
                //判断密码是否正确
               if(Hash::check($password,$member->password)) {
                   $data = [
                       "status" => "true",
                       "message" => "登录成功",
                       "user_id" => $member->id,
                       "username" => $name
                   ];
                 }
               }else{
                   $data = [
                       "status" => "false",
                       "message" => "登录失败"
                   ];
               }
            return $data;
        }
```

### 接口

```php
// 登录验证接口
  loginCheck: '/api/member/login',
```

### 路由

```php
Route::any("member/login","Api\MemberController@login");
```

# Day07

开发任务

接口开发

- 用户地址管理相关接口
- 购物车相关接口

### 实现步骤

### 忘记密码

```php
//忘记密码
        public function reset(Request $request)
        {
            //接收数据
            $data = $request->post();
            $code = Redis::get('tel_' . $data['tel']);
                //判断验证码是否正确
                if ($data['sms'] == $code) {
                    //判断手机号是否存在
                     $member=Member::where("tel", $data["tel"])->first();
                    //密码加密
                    $data['password'] = Hash::make($data['password']);
                    //修改密码
                    if ($member->update($data)) {
                        $data = [
                            "status" => "true",
                            "message" => "密码修改成功",
                        ];
                    }else{
                        $data = [
                            "status" => "false",
                            "message" => "密码修改失败",
                        ];
                    }

                }
        }
```

### 加入购物车

```php
<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    //购物车列表
    public function index(Request $request)
    {
        //用户
        $userId=$request->input("user_id");
        //购物车列表
        $carts=Cart::where("user_id",$userId)->get();
        //声明一个数组
        $goodsList=[];
        //总价
        $totalCost=0;
        //循环购物车
        foreach ($carts as $k =>$v){
            $good=Menu::where("id",$v->goods_id)->first(["id as goods_id","goods_name","goods_img","goods_price"]);
            $good->amount=$v->amount;
            //算总价
            $totalCost=$totalCost+$good->amount*$good->goods_price;
            $goodsList[]=$good;

        }
        return [
            "goods_list"=>$goodsList,
            "totalCost"=>$totalCost
        ];

    }

    //添加列表
    public function add(Request $request)
    {
        //清空当前购物车
        Cart::where("user_id",$request->post("user_id"))->delete();
        //接收参数
        $goods=$request->post("goodsList");
        $counts=$request->post("goodsCount");
        foreach ($goods as  $k=>$good){
            $data=[
                "user_id"=>$request->post('user_id'),
                "goods_id"=>$good,
                "amount"=>$counts[$k]
            ];
          Cart::create($data);
        }
        return [
            "status"=>"true",
            "message"=>"添加成功"
        ];



    }


}

```

### 地址接口

```php
<?php

namespace App\Http\Controllers\Api;

use App\Models\Adress;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AdressController extends Controller
{
    //
    public function index(Request $request){
        //得到当前用户ID
        $memberID=$request->input("user_id");
        //得到当前用户所有地址
        $addresses=Adress::all();
        //返回地址
        return $addresses;
    }

    public function add(Request $request){
        //验证
        $validate=Validator::make($request->all(),[
               "name"=>"required",
               "tel"=>[
                   "required",
                   "regex:/^0?(13|14|15|17|18|19)[0-9]{9}$/",
               ]
            ]);
        //判断验证
        if ($validate->fails()){
            //返回错误
            return [
                "status"=>"false",
                "message"=>$validate->errors()->first(),
            ];
        }
        $data=$request->all();
//        dd($data);
        $data["is_default"]=0;
        //数据入库
        if (Adress::create($data)) {
            //返回数据
            $data= [
                "status"=>"true",
                "message"=>"添加成功"
            ];
        }else{
            $data= [
                "status"=>"false",
                "message"=>"添加失败"
            ];
        }

    }

}
```

## 实现步骤

# Day08

## 开发任务

接口开发

- 订单接口(使用事务保证订单和订单商品表同时写入成功)

- 密码修改和重置密码接口

  ### 添加订单

  ```php
  //添加订单
      public function add(Request $request)
      {
          //当前收货地址
          $address =Address::find($request->post('address_id'));
         // dd($address);
          //判断地址是否有误
          if ($address === null) {
              return [
                  "status" => "false",
                  "message" => "地址的选择不正确"
              ];
          }
          //用户id
          $data["user_id"] = $request->post('user_id');
          //店铺id
          $carts = Cart::where("user_id", $request->post('user_id'))->get();
          //购物车里第一条数据的id，通过商品id找出菜品中的shop_id
          $shopId = Menu::find($carts[0]->goods_id)->information_id;
          //dd($shopId);
          $data['shop_id'] = $shopId;
          //生成订单号
          $data["order_code"] = date("ymdHis") . rand(1000, 9999);
          //地址
          $data["province"] = $address->provence;
          $data["city"] = $address->city;
          $data["county"] = $address->area;
          $data["address"] = $address->detail_address;
          $data["tel"] = $address->tel;
          $data["name"] = $address->name;
          //总价
          $total = 0;
          foreach ($carts as $k => $v) {
              //dd($v->toarrAy());
              $good = Menu::where("id", $v->goods_id)->first();
              $total += $v->amount * $good->goods_price;
          }
          $data['total'] = $total;
          //状态
          $data['status'] = 0;
  
          //启动事物
          //dd($data);
  
          DB::beginTransaction();
  
          try {
  
              //订单入库
              $order = Order::create($data);
              //订单商品
              foreach($carts as $s=>$cart){
                  //得到当前菜品
                  $menu=Menu::find($cart->goods_id);
                  //判断库存是否充足
                  if ($cart->amount>$menu->stock){
                      //抛出异常
                      throw new \Exception($menu->goods_name."库存不足");
                  }
                  //减去库存
                  $menu->stock=$menu->stock-$cart->amount;
                  //保存
                  $menu->save();
                  OrderDetail::insert([
                     "order_id"=>$order->id,
                     "goods_id"=>$cart->goods_id,
                     "amount"=>$cart->amount,
                     "goods_name"=>$menu->goods_name,
                     "goods_img"=>$menu->goods_img,
                      "goods_price"=>$menu->goods_price,
                  ]);
              }
              //清空购物车
              Cart::where("user_id",$request->post("user_id"))->delete();
              //提交事务
              DB::commit();
          }catch (\Exception $exception){
              //回滚
              DB::rollBack();
              return [
                  "status"=>"false",
                  "message"=>$exception->getMessage(),
              ];
          }
  
          return [
              "status"=>"true",
              "message"=>"添加成功",
              "order_id"=>$order->id
          ];
  
  }
  ```

  ### 模型Models/Order.php

  ```php
  <?php
  
  namespace App\Models;
  
  use Illuminate\Database\Eloquent\Model;
  
  class Order extends Model
  {
      //
  
      protected $fillable=["user_id","shop_id","order_code","province","city","county","address","tel","name","total","status"];
  
      public function shop(){
          return $this->belongsTo(ShopInformation::class,'shop_id');
      }
      public function goods(){
          return $this->hasMany(OrderDetail::class,"order_id");
      }
  
  }
  ```

  ### 订单详情

  ```php
  //订单详情
      public function detail(Request $request){
          $order=Order::find($request->input('id'));
          //构造状态数组
          $stats=[0=>'代付款',1=>'待发货',2=>'待收货',3=>'待完成',-1=>'取消'];
          $data['id']=$order->id;
          $data['order_code']=$order->order_code;
          $data['order_birth_time']=(string)$order->created_at;
          $data['order_status']=$stats[$order->status];
          $data['shop_id']=$order->shop_id;
          $data['shop_name']=$order->shop->shop_name;
          $data['shop_img']=$order->shop->shop_img;
          $data['order_price']=$order->total;
          $data['order_address']=$order->provence.$order->city.$order->area.$order->detail_address;
          $data['goods_list']=$order->goods;
         return $data;
          //dd($data);
  
      }
  ```

  ### Models/OrderDetail.php

  ```php
  <?php
  
  namespace App\Models;
  
  use Illuminate\Database\Eloquent\Model;
  
  class OrderDetail extends Model
  {
      //
      protected $fillable=["goods_id","order_id","amount","goods_name","goods_img","goods_price"];
  }
  
  ```



  ### 订单支付

  ```php
   //订单支付
         public function pay(Request $request){
          //得到订单
             $order=Order::find($request->post("id"));
             //得到用户
             $member=Member::find($order->user_id);
             //判断金钱是否足够
             if ($order->total>$member->money){
                 return [
                   "status"=>"false",
                   "message"=>"余额不足，请充值"
                 ];
             }
             //足够
             $member->money=$member->money-$order->total;
             $member->save();
             //更改订单状态
             $order->status=1;
             $order->save();
             return [
                 "status"=>"true",
                 "message"=>"支付成功"
             ];
         }
  ```

  ### Models/Menu.php

  ```php
      public function getGoodsImgAttribute($value){
          return env("ALIYUN_OSS_URL").$value;
      }
  ```

  ### 订单列表

  ```php
   //订单
      public function index(Request $request){
          $orders=Order::where("user_id",$request->input("user_id"))->get();
  
  
          //dd($orders);
  
        // $datas=[];
          foreach ($orders as $order){
              //查询当前订单货物
              //$goods = OrderDetail::where('order_id',$order->id)->get();
              //当前店铺
             $shop = ShopInformation::where('id',$order->shop_id)->first();
             // dd($order->shop_id);
              $stats=[0=>'代付款',1=>'待发货',2=>'待收货',3=>'待完成',-1=>'取消'];
              $data['id']=$order->id;
              $data['name']=$order->name;
              $data['order_code']=$order->order_code;
              $data['order_birth_time']=(string)$order->created_at;
              $data['order_status']=$stats[$order->status];
              $data['shop_id']=$order->shop_id;
              $data['shop_name']=$shop->shop_name;
              $data['shop_img']=$shop->shop_img;
              $data['order_price']=$order->total;
              $data['order_address']=$order->province . $order->city . $order->county . $order->address;
  
              $data['goods_list']=$order->goods;
              $x[]=$data;
          }
          //$data['goods_list'] = $order->goods;
         // dd($data);
          return  $x;
          //dd( $data['goods_list']);
      }
  ```

  # Day09

  ### 开发任务

  商户端

  - 订单管理[订单列表,查看订单,取消订单,发货]

  - 订单量统计[按日统计,按月统计,累计]（每日、每月、总计）

  - 菜品销量统计[按日统计,按月统计,累计]（每日、每月、总计）

    平台

  - 订单量统计[按商家分别统计和整体统计]（每日、每月、总计）

  - 菜品销量统计[按商家分别统计和整体统计]（每日、每月、总计）

  - 会员管理[会员列表,查询会员,查看会员信息,禁用会员账号]

  ### 实现步骤

  ### 订单管理

  ```php
    //订单管理
      public function index(){
  //
          $orders=Order::all();
  
          return view("shop.order.index",compact("orders"));
      }
  
      //查看订单
      public function check($id){
          $lists = DB::table("orders")->where("id",$id)->get();
          return view("shop.order.check",compact("lists"));
      }
  
      //取消订单
      public function status($id,$status){
          $result=DB::table("orders")->where("id",$id)->update(["status"=>$status]);
          if($result){
              return redirect()->route("shop.order.index");
          }
      }
  ```

  ### 订单销量统计

  ```php
    //订单销量
      public function order(){
              $shopId=Auth::user()->information->id;
  //        dd($shopId);
              $data=Order::where("shop_id",$shopId)
                  ->select(DB::raw("COUNT(*) as nums,SUM(total) as money"))
                  ->get();
              //显示视图
              return view("shop.order.order",compact("data"));
      }
  
      //按天统计
      public function day(Request $request){
          $shopId=Auth::user()->information->id;
  //        dd($shopId);
          $data=Order::where("shop_id",$shopId)
              ->select(DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d')
                as date,COUNT(*) as nums,SUM(total) as money"))
              ->groupBy("date")
              ->get();
  
          //接收数据
          $start=$request->input("start");
          $end=$request->input("end");
          //如果有起始时间
          if ($start!==null){
              $data->whereDate("create_at",">=",$start);
          }
          if ($end!==null){
              $data->whereDate("create_at","<=",$end);
          }
          //显示视图
          return view("shop.order.day",compact("data"));
  
      }
  
      //按月统计
      public function month(Request $request){
          $shopId=Auth::user()->information->id;
  //        dd($shopId);
          $data=Order::where("shop_id",$shopId)
              ->select(DB::raw("DATE_FORMAT(created_at,'%Y-%m')
                as date,COUNT(*) as nums,SUM(total) as money"))
              ->groupBy("date")
              ->get();
  
          //接收数据
          $start=$request->input("start");
          $end=$request->input("end");
          //如果有起始时间
          if ($start!==null){
              $data->whereDate("create_at",">=",$start);
          }
          if ($end!==null){
              $data->whereDate("create_at","<=",$end);
          }
          //显示视图
          return view("shop.order.day",compact("data"));
  
      }
  ```

  ### 菜品销量统计

  ```php
   //菜品销量
      public function menu(){
      //找到当前店铺所有订单
      $ids=Order::where("shop_id",Auth::user()->information->id)->pluck("id");
      $data=OrderDetail::select(DB::raw('SUM(amount) as nums,SUM(goods_price) as money'))->whereIn("order_id",$ids)->get();
      //显示视图
      return view("shop.order.menu",compact("data"));
  }
  
  //按天统计
      public function day1(Request $request){
          //找到当前店铺所有订单
          $ids=Order::where("shop_id",Auth::user()->information->id)->pluck("id");
  //        dd($shopId);
          $data=OrderDetail::where("order_id",$ids)
              ->select(DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d')
                as date,SUM(amount) as nums,SUM(goods_price) as money"))
              ->groupBy("date")
              ->get();
  
          //接收数据
          $start=$request->input("start");
          $end=$request->input("end");
          //如果有起始时间
          if ($start!==null){
              $data->whereDate("create_at",">=",$start);
          }
          if ($end!==null){
              $data->whereDate("create_at","<=",$end);
          }
          //显示视图
          return view("shop.order.day1",compact("data"));
  
      }
  
  //按月统计
      public function month1(Request $request){
          //找到当前店铺所有订单
          $ids=Order::where("shop_id",Auth::user()->information->id)->pluck("id");
  //        dd($shopId);
          $data=OrderDetail::where("order_id",$ids)
              ->select(DB::raw("DATE_FORMAT(created_at,'%Y-%m')
                as date,SUM(amount) as nums,SUM(goods_price) as money"))
              ->groupBy("date")
              ->get();
  
          //接收数据
          $start=$request->input("start");
          $end=$request->input("end");
          //如果有起始时间
          if ($start!==null){
              $data->whereDate("create_at",">=",$start);
          }
          if ($end!==null){
              $data->whereDate("create_at","<=",$end);
          }
          //显示视图
          return view("shop.order.month1",compact("data"));
  
      }
  ```

  ## 平台

  ### 按整体统计

  ```php
  class OrderController extends BaseController
  {
      //按整体分类
      //订单销量
      public function order(){
          $data=Order::select(DB::raw("COUNT(*) as nums,SUM(total) as money"))->get();
          //显示视图
          return view("admin.order.order",compact("data"));
      }
  
      //按天统计
      public function day(Request $request){
          $data=Order::select(DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d')
                as date,COUNT(*) as nums,SUM(total) as money"))
              ->groupBy("date")
              ->get();
  
          //接收数据
          $start=$request->input("start");
          $end=$request->input("end");
          //如果有起始时间
          if ($start!==null){
              $data->whereDate("create_at",">=",$start);
          }
          if ($end!==null){
              $data->whereDate("create_at","<=",$end);
          }
          //显示视图
          return view("admin.order.day",compact("data"));
  
      }
  
      //按月统计
      public function month(Request $request){
          $data=Order::select(DB::raw("DATE_FORMAT(created_at,'%Y-%m')
                as date,COUNT(*) as nums,SUM(total) as money"))
              ->groupBy("date")
              ->get();
  
          //接收数据
          $start=$request->input("start");
          $end=$request->input("end");
          //如果有起始时间
          if ($start!==null){
              $data->whereDate("create_at",">=",$start);
          }
          if ($end!==null){
              $data->whereDate("create_at","<=",$end);
          }
          //显示视图
          return view("admin.order.day",compact("data"));
  
      }
  ```

  ### 按商家统计

  ```php
   //按商家分类 按月
      public function shopmonth(){
  
          $data=Order::select(DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d')
                as date,COUNT(*) as nums,SUM(total) as money,shop_id"))
              ->groupBy("shop_id","date")
              ->get();
          //显示视图
          return view("admin.order.shopmonth",compact("data"));
  
      }
  
      //按天
      public function shopday(){
  
          $data=Order::select(DB::raw("DATE_FORMAT(created_at,'%Y-%m')
                as date,COUNT(*) as nums,SUM(total) as money,shop_id"))
              ->groupBy("shop_id","date")
              ->get();
          //显示视图
          return view("admin.order.shopday",compact("data"));
  
      }
  
      //总
      public function shopall(){
  
          $data=Order::select(DB::raw("COUNT(*) as nums,SUM(total) as money,shop_id"))
              ->groupBy("shop_id")
              ->get();
          //显示视图
          return view("admin.order.shopall",compact("data"));
  
      }
  ```













