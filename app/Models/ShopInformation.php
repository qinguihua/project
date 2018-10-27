<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopInformation extends Model
{
    //
    protected $fillable=["shop_category_id","shop_name","shop_img","brand",
        "on_time","fengniao","bao","piao","zhun","start_send","send_cost","notice","discount","user_id"];


    public function user(){
        return $this->belongsTo(User::class,"user_id");
    }

    public function category(){
        return $this->belongsTo(ShopCategory::class,"shop_category_id");
    }

    public function menus(){
        return $this->hasOne(Menu::class,"information_id");
    }

    public function menuCategory(){
        return $this->hasOne(MenuCategory::class,"information_id");
    }

}



