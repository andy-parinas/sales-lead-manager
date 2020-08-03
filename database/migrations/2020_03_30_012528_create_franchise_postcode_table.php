<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFranchisePostcodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('franchise_postcode', function (Blueprint $table) {
            $table->unsignedBigInteger('franchise_id');
            $table->unsignedBigInteger('postcode_id');
            $table->primary(['franchise_id', 'postcode_id']);
            $table->foreign('franchise_id')->references('id')->on('franchises')->cascadeOnDelete();
            $table->foreign('postcode_id')->references('id')->on('postcodes')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('franchise_postcode');
    }
}
