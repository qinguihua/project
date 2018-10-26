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









