<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersInstrumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instrument_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('instrument_id')->unsigned();
        
            $table->foreign('instrument_id')->references('id')->on('instruments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('ensemble_instrument', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ensemble_id')->unsigned();
            $table->integer('instrument_id')->unsigned();
        
            $table->foreign('instrument_id')->references('id')->on('instruments')->onDelete('cascade');
            $table->foreign('ensemble_id')->references('id')->on('ensembles')->onDelete('cascade');
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
        Schema::dropIfExists('users_instruments');
    }
}
