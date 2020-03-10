<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_types', function (Blueprint $table) {
            $table->id();
            $table->string('taken_by');
            $table->dateTime('date_allocated');
            $table->text('description');
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('design_assessor_id');
            $table->timestamps();

            $table->foreign('lead_id')->references('id')->on('leads');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('design_assessor_id')->references('id')->on('design_assessors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_types');
    }
}
