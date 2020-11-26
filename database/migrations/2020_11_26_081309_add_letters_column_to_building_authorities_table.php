<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLettersColumnToBuildingAuthoritiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('building_authorities', function (Blueprint $table) {
            $table->dateTime('intro_council_letter_sent')->nullable();
            $table->dateTime('out_of_council_letter_sent')->nullable();
            $table->dateTime('no_council_letter_sent')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('building_authorities', function (Blueprint $table) {
            $table->dropColumn('intro_council_letter_sent');
            $table->dropColumn('out_of_council_letter_sent');
            $table->dropColumn('no_council_letter_sent');
        });
    }
}
