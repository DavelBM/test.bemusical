<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersStylesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('style_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('style_id')->unsigned();
        
            $table->foreign('style_id')->references('id')->on('styles')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('ensemble_style', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ensemble_id')->unsigned();
            $table->integer('style_id')->unsigned();
        
            $table->foreign('style_id')->references('id')->on('styles')->onDelete('cascade');
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
        Schema::dropIfExists('users_styles');
    }
}
