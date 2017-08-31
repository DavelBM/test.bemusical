<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ensemble_id')->unsigned();   
            $table->integer('user_id')->unsigned();        
            $table->string('name')->nullable();
            $table->string('instrument');
            $table->string('slug');
            $table->string('token');
            $table->string('email')->nullable();
            $table->boolean('confirmation');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('members');
    }
}
