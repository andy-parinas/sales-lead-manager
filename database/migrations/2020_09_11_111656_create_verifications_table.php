<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verifications', function (Blueprint $table) {
            $table->id();
            $table->string('design_correct');
            $table->date('date_design_check');
            $table->string('costing_correct')->nullable();
            $table->date('date_costing_check')->nullable();
            $table->integer('estimated_build_days')->nullable();
            $table->string('trades_required')->nullable();
            $table->string('building_supervisor')->nullable();
            $table->unsignedBigInteger('roof_sheet_id')->nullable();
            $table->unsignedBigInteger('roof_colour_id')->nullable();
            $table->string('lineal_metres')->nullable();
            $table->string('franchise_authority')->nullable();
            $table->date('authority_date')->nullable();
            $table->unsignedBigInteger('lead_id');
            $table->timestamps();

            $table->foreign('roof_sheet_id')->references('id')->on('roof_sheets');
            $table->foreign('roof_colour_id')->references('id')->on('roof_colours');
            $table->foreign('lead_id')->references('id')->on('leads');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('verifications');
    }
}




