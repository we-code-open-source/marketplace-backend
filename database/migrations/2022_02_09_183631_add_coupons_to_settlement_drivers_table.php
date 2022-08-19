<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCouponsToSettlementDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settlement_drivers', function (Blueprint $table) {
            $table->integer('count_delivery_coupons');
            $table->integer('count_restaurant_coupons');
            $table->decimal('amount_restaurant_coupons');
            $table->decimal('amount_delivery_coupons');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settlement_drivers', function (Blueprint $table) {
            $table->dropColumn([
                'count_delivery_coupons',
                'count_restaurant_coupons',
                'amount_restaurant_coupons',
                'amount_delivery_coupons',
            ]);
        });
    }
}
