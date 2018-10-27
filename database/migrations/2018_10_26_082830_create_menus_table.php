<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id');

            $table->string("goods_name");
            $table->float("rating");
            $table->integer("information_id");
            $table->integer("category_id");
            $table->decimal("goods_price");
            $table->string("description");
            $table->integer("month_sales");
            $table->integer("rating_count");
            $table->string("tips");
            $table->integer("satisfy_count");
            $table->float("satisfy_rate");
            $table->string("goods_img");
            $table->integer("status");

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
        Schema::dropIfExists('menus');
    }
}
