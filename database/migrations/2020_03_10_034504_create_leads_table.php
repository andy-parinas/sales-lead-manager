<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->unsignedBigInteger('franchise_id');
            $table->unsignedBigInteger('sales_contact_id');
            $table->unsignedBigInteger('lead_source_id');
            $table->date('lead_date');
            $table->string('postcode_status');
            $table->timestamps();

            $table->foreign('franchise_id')->references('id')->on('franchises');
            $table->foreign('sales_contact_id')->references('id')->on('sales_contacts');
            $table->foreign('lead_source_id')->references('id')->on('lead_sources');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leads');
    }
}
