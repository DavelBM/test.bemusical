<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterGeneralRequests2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('general_requests', function (Blueprint $table) {
            $table->integer('times')->after('type');
            $table->dropColumn('company');
            $table->dropColumn('type');
            $table->string('type_of')->after('comment');
            $table->string('tags')->after('type_of');
            $table->string('instruments')->after('tags');
            $table->string('styles')->after('instruments');
            $table->string('original_array')->after('assined');
            $table->string('array_per_date')->after('original_array');
            $table->string('sended_at')->after('array_per_date');
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
