<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gigs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('request_id')->unsigned();
            $table->string('title');
            $table->string('start');
            // $dt->addMinutes(61); 
            $table->string('end');
            $table->string('url');
            $table->boolean('allDay')->default(0);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('request_id')->references('id')->on('requests')->onDelete('cascade');

            $table->timestamps();
        });

        Schema::create('gig_options', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('listDay')->nullable();
            $table->string('listWeek')->nullable();
            $table->string('month')->nullable();
            $table->boolean('monday')->default(1);
            $table->boolean('tuesday')->default(1);
            $table->boolean('wednesday')->default(1);
            $table->boolean('thursday')->default(1);
            $table->boolean('friday')->default(1);
            $table->boolean('saturday')->default(1);
            $table->boolean('sunday')->default(1);
            $table->string('start')->nullable();
            $table->string('end')->nullable();
            $table->integer('time_before_event')->default(30);
            $table->integer('time_after_event')->default(30);
            $table->enum('defaultView', ['listDay', 'listWeek', 'month'])->default('month');
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
        Schema::dropIfExists('events');
    }
}
