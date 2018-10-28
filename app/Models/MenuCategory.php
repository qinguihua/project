<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    //

    protected $fillable=["name","type_accumulation","information_id","description","is_selected"];

    public function menus(){
        return $this->hasMany(Menu::class,"category_id");
    }

/*    public function shop_information(){
        return $this->belongsTo(Menu::class,"information_id");
    }*/

    public function shop_information(){
        return $this->belongsTo(ShopInformation::class,"information_id");
    }

}
