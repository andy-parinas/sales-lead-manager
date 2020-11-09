<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFranchiseSalesStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('franchise_sales_staff', function (Blueprint $table) {
            $table->unsignedBigInteger('franchise_id');
            $table->unsignedBigInteger('sales_staff_id');
            $table->primary(['franchise_id', 'sales_staff_id']);
            $table->foreign('franchise_id')->references('id')->on('franchises')->cascadeOnDelete();
            $table->foreign('sales_staff_id')->references('id')->on('sales_staff')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('franchise_sales_staff');
    }
}
