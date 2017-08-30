<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEnsembleRepertoiresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ensemble_repertoires', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ensemble_id')->unsigned(); 
            $table->string('repertoire_example');
            $table->boolean('visible');
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
        Schema::dropIfExists('ensemble_repertoires');
    }
}
