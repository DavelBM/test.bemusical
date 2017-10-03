<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterGeneralRequests3Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('general_requests', function (Blueprint $table) {
            $table->string('address', 250)->change();
            $table->text('tags')->change();
            $table->text('instruments')->change();
            $table->text('styles')->change();
            $table->text('original_array')->change();
            $table->text('array_per_date')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_requests', function (Blueprint $table) {
            //
        });
    }
}
