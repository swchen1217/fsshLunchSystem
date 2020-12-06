<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDishesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dishes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('manufacturer_id');
            $table->unsignedInteger('nutrition_id');
            $table->unsignedTinyInteger('price');
            $table->string('photo');
            $table->timestamps();

            $table->foreign('manufacturer_id')->references('id')->on('manufacturers')->onDelete('CASCADE');
            $table->foreign('nutrition_id')->references('id')->on('nutrition')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dishes');
    }
}
