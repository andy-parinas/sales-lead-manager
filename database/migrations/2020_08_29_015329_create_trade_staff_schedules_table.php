<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradeStaffSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trade_staff_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('job_number');
            $table->date('anticipated_start')->nullable();
            $table->date('actual_start')->nullable();
            $table->date('anticipated_end')->nullable();
            $table->date('actual_end')->nullable();
            $table->unsignedBigInteger('trade_staff_id');
            $table->timestamps();

            $table->foreign('trade_staff_id')->references('id')->on('trade_staff')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trade_staff_schedules');
    }
}
