<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finances', function (Blueprint $table) {
            $table->id();
            $table->float('project_price', 13, 2);
            $table->float('gst', 13,2);
            $table->float('contract_price', 13,2);
            $table->float('total_contract', 13,2);
            $table->float('deposit', 13, 2);
            $table->float('balance', 13, 2);
            $table->unsignedBigInteger('lead_id');
            $table->timestamps();

            $table->foreign('lead_id')->references('id')->on('leads')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('finances');
    }
}
