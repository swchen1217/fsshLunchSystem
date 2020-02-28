<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('dish_id');
            $table->unsignedInteger('rating');
            $table->date('created_at');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
            $table->foreign('dish_id')->references('id')->on('dishes')->onDelete('CASCADE');

            $table->unique(['user_id','dish_id','created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ratings');
    }
}
