<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adress extends Model
{
    protected $fillable=["name","user_id","tel","provence","city","area","detail_address","is_default"];
}
