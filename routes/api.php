<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get("shop/index","Api\ShopController@index");
Route::get("shop/detail","Api\ShopController@detail");
Route::get("member/sms","Api\MemberController@sms");
Route::any("member/reg","Api\MemberController@reg");
Route::any("member/login","Api\MemberController@login");
Route::any("member/reset","Api\MemberController@reset");
Route::any("member/alter","Api\MemberController@alter");
Route::any("member/detail","Api\MemberController@detail");
Route::any("cart/index","Api\CartController@index");
Route::any("cart/add","Api\CartController@add");
Route::any("address/index","Api\AddressController@index");
Route::any("address/add","Api\AddressController@add");
Route::any("order/index","Api\OrderController@index");
Route::any("order/add","Api\OrderController@add");
Route::get("order/detail","Api\OrderController@detail");
Route::any("order/pay","Api\OrderController@pay");
