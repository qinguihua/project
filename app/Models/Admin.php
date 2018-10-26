<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
class Admin extends Authenticatable
{
    //加上下面这一句，相当于把$rememberTokenName清空，
//    protected $rememberTokenName = '';
    protected $fillable=["name","password","email"];
}
