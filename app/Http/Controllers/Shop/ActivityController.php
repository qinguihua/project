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
