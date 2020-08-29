<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('build_logs', function (Blueprint $table) {
            $table->id();
            $table->date('work_date');
            $table->float('time_spent', 6, 2);
            $table->float('hourly_rate', 6, 2);
            $table->float('total_cost', 11, 2);
            $table->text('comments')->nullable();
            $table->unsignedBigInteger('construction_id');
            $table->unsignedBigInteger('trade_staff_id');
            $table->timestamps();

            $table->foreign('construction_id')->references('id')->on('constructions')->cascadeOnDelete();
            $table->foreign('trade_staff_id')->references('id')->on('trade_staff');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('build_logs');
    }
}
