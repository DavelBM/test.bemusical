<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEnsemblesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ensembles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();           
            $table->string('slug')->nullable();
            $table->string('name');
            $table->string('manager_name');
            $table->string('type');
            $table->string('email')->unique();
            $table->string('profile_picture');
            $table->text('about');
            $table->string('summary');
            $table->string('address');
            $table->string('phone', 10);
            $table->string('location');
            $table->integer('mile_radious');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('ensembles');
    }
}
