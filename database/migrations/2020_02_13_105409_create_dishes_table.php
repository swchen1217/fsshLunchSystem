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
            $table->bigIncrements('id');
            $table->date('saleDate');
            $table->string('name');
            $table->integer('price');
            $table->string('status');
            $table->string('contents');
            $table->double('calories');
            $table->double('protein');
            $table->double('fat');
            $table->double('carbohydrate');
            $table->double('stars');
            $table->text('note');
            $table->string('photo');
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
        Schema::dropIfExists('dishes');
    }
}
