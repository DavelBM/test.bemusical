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
            $table->string('views')->nullable();
            // views: {
            //     listDay: { buttonText: 'ToDos Today' },
            //     listWeek: { buttonText: 'ToDos Week' },
            //     month: { buttonText: 'Tmi mes' }
            // }
            $table->string('businessHours')->nullable();
            // businessHours: {
            //     // days of week. an array of zero-based day of week integers (0=Sunday)
            //     dow: [ 1, 2, 3, 4, 5 ], // Monday - Thursday

            //     start: '10:00', // a start time (10am in this example)
            //     end: '18:00', // an end time (6pm in this example)
            // }
            $table->string('time_before_event');
            $table->string('time_after_event');
            $table->string('weekNumbers')->nullable();
            // weekNumbers: true,
            // weekNumbersWithinDays: true,
            // weekNumberCalculation: 'ISO'
            $table->enum('defaultView', ['listDay', 'listWeek', 'month'])->default('month');
            // defaultView: 'month'
            $table->string('defaultDate')->nullable();
            // defaultDate: '2017-01-12',
            $table->string('eventColor')->nullable();
            // eventColor: '#A78036',

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
