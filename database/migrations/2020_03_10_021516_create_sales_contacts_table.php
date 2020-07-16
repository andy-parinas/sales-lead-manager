<?php

use App\SalesContact;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('email2')->nullable();
            $table->string('contact_number');
            $table->string('street1');
            $table->string('street2')->nullable();
            $table->unsignedBigInteger('postcode_id');
            $table->string('customer_type');
            $table->string('status')->default(SalesContact::ACTIVE);
            $table->timestamps();

            $table->foreign('postcode_id')->references('id')->on('postcodes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_contacts');
    }
}
