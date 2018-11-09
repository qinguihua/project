<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    //
    protected $fillable=["goods_name","rating","information_id",
        "category_id","goods_price","description","month_sales",
        "rating_count","tips","satisfy_count","satisfy_rate","goods_img","status"];


    public function menu_category(){
        return $this->belongsTo(MenuCategory::class,"category_id");
    }

    public function shop_information(){
        return $this->belongsTo(ShopInformation::class,"information_id");
    }

    public function getGoodsImgAttribute($value){
        return env("ALIYUN_OSS_URL").$value;
    }

}

