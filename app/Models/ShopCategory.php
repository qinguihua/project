<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopCategory extends Model
{
    //
    protected $fillable=["name","img","status"];

    public function information(){
        return $this->hasOne(ShopInformation::class,"shop_category_id");
    }

    public function user(){
        return $this->hasOne(ShopInformation::class,"user_id");
    }
}
