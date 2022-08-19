<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ExtrasFoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extra_foods', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('extra_id')->unsigned();
            $table->integer('food_id')->unsigned();
            $table->foreign('extra_id')->references('id')->on('extras')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('food_id')->references('id')->on('foods')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::drop('extra_foods');
    }
}
