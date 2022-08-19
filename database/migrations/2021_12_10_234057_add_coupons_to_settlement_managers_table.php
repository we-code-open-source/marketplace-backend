<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCouponsToSettlementManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settlement_managers', function (Blueprint $table) {
            $table->integer('delivery_coupons_count')->default(0);
            $table->double('delivery_coupons_amount', 9, 2)->default(0);
            $table->integer('restaurant_coupons_count')->default(0);
            $table->double('restaurant_coupons_amount', 9, 2)->default(0);
            $table->double('restaurant_coupons_on_company_count', 9, 2)->default(0);
            $table->integer('restaurant_coupons_on_company_amount')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settlement_managers', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_coupons_count',
                'delivery_coupons_amount',
                'restaurant_coupons_count',
                'restaurant_coupons_amount',
                'restaurant_coupons_on_company_count',
                'restaurant_coupons_on_company_amount'
            ]);
        });
    }
}
