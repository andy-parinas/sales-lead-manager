<?php

use App\TradeStaff;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradeStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trade_staff', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('contact_number');
            $table->unsignedBigInteger('trade_type_id');
            $table->string('company')->nullable();
            $table->string('abn')->nullable();
            $table->string('builders_license')->nullable();
            $table->string('status')->default(TradeStaff::ACTIVE);
            $table->unsignedBigInteger('franchise_id');
            $table->timestamps();

            $table->foreign('trade_type_id')->references('id')->on('trade_types');
            $table->foreign('franchise_id')->references('id')->on('franchises');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trade_staff');
    }
}
