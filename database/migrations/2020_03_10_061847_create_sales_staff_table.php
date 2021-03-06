<?php

use App\SalesStaff;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_staff', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('legacy_name')->nullable();
            $table->string('sales_code')->nullable();
            $table->string('email');
            $table->string('email2')->nullable();
            $table->string('contact_number');
            $table->string('sales_phone')->nullable();
            $table->string('status')->default(SalesStaff::ACTIVE);
            //$table->unsignedBigInteger('franchise_id');
            $table->timestamps();

            //$table->foreign('franchise_id')->references('id')->on('franchises');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_staff');
    }
}
