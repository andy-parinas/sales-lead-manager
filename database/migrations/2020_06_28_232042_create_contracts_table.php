<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->date('contract_date');
            $table->string('contract_number');
            $table->float('contract_price', 13,2);
            $table->float('deposit_amount', 13,2)->default(0.0);
            $table->float('total_variation', 13,2)->default(0.0);
            $table->date('date_deposit_received')->nullable();
            $table->float('total_contract',13,2);
            $table->string('warranty_required');
            $table->date('date_warranty_sent')->nullable();
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
        Schema::dropIfExists('contracts');
    }
}
