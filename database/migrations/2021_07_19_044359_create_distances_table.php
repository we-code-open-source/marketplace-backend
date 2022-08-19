<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('from_latitude', 8, 5);
            $table->decimal('from_longitude', 8, 5);
            $table->decimal('to_latitude', 8, 5);
            $table->decimal('to_longitude', 8, 5);
            $table->decimal('distance_value', 9, 2);
            $table->integer('duration_value');
            $table->string('distance_text', 20);
            $table->string('duration_text', 20);
            $table->text('response');
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
        Schema::dropIfExists('distances');
    }
}
