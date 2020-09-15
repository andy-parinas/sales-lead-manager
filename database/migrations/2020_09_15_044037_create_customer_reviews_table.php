<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_reviews', function (Blueprint $table) {
            $table->id();
            $table->date('date_project_completed');
            $table->date('date_warranty_received');
            $table->string('home_addition_type')->nullable();
            $table->text('home_addition_description')->nullable();
            $table->string('service_received_rating')->nullable();
            $table->string('workmanship_rating')->nullable();
            $table->string('finished_product_rating')->nullable();
            $table->string('design_consultant_rating')->nullable();
            $table->text('comments')->nullable();
            $table->unsignedBigInteger('lead_id');
            $table->timestamps();

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
        Schema::dropIfExists('customer_reviews');
    }
}
