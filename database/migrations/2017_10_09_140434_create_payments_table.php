<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('request_id')->unsigned();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('_billing_address')->nullable();
            $table->string('_billing_zip')->nullable();
            $table->string('_id_costumer')->nullable();
            $table->string('_id_card')->nullable();
            $table->string('_id_token')->nullable();
            $table->string('_id_charge');
            $table->decimal('amount', 5, 2);
            $table->boolean('payed'); // 1 = payed && 0 = not payed
            $table->enum('type', ['stripe', 'paypal', 'transfer', 'cash']);

            $table->foreign('request_id')->references('id')->on('requests')->onDelete('cascade');
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
        Schema::dropIfExists('payments');
    }
}
