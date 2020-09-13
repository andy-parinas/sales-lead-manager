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
            $table->string('costing_correct');
            $table->date('date_costing_check');
            $table->integer('estimated_build_days');
            $table->string('trades_required');
            $table->string('building_supervisor');
            $table->unsignedBigInteger('roof_sheet_id');
            $table->unsignedBigInteger('roof_colour_id');
            $table->float('lineal_metres', 11,2);
            $table->string('franchise_authority');
            $table->date('authority_date');
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




