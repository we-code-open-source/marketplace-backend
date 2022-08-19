<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEnableNotificationsToUserRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_restaurants', function (Blueprint $table) {
            $table->boolean('enable_notifications')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_restaurants', function (Blueprint $table) {
            $table->dropColumn('enable_notifications');
        });
    }
}
