<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCouponsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('delivery_coupon_id')->unsigned()->nullable();
            $table->foreign('delivery_coupon_id')->references('id')->on('coupons');
            $table->double('delivery_coupon_value', 5, 2)->nullable()->default(0);
            $table->integer('restaurant_coupon_id')->unsigned()->nullable();
            $table->foreign('restaurant_coupon_id')->references('id')->on('coupons');
            $table->double('restaurant_coupon_value', 5, 2)->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_coupon_id', 'delivery_coupon_value', 'restaurant_coupon_id', 'restaurant_coupon_value']);
        });
    }
}
