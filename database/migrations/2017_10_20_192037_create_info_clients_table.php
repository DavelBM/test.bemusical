<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInfoClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('info_clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_costumer')->nullable();
            $table->integer('client_id')->unsigned();
            $table->string('name');
            $table->string('address', 250);
            $table->string('company');
            $table->string('phone');
            $table->string('zip');
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });

        Schema::table('clients', function(Blueprint $table){
            $table->dropColumn('name');
            $table->dropColumn('address');
            $table->dropColumn('company');
            $table->dropColumn('phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('info_clients');
    }
}
