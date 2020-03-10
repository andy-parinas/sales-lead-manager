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
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('sales_contact_id');
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('sales_contact_id')->references('id')->on('sales_contacts');
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
