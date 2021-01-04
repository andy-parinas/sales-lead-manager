<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSmsEmailColumnToJobTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_types', function (Blueprint $table) {
            $table->dateTime('email_sent_to_design_advisor')->nullable();
            $table->dateTime('sms_sent_to_design_advisor')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_types', function (Blueprint $table) {
            $table->dropColumn('email_sent_to_design_advisor');
            $table->dropColumn('sms_sent_to_design_advisor');
        });
    }
}
