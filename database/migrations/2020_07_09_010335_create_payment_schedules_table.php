<?php

use App\PaymentSchedule;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_schedules', function (Blueprint $table) {
            $table->id();
            $table->date('due_date');
            $table->date('payment_date')->nullable();
            $table->string('description');
            $table->float('amount', 13,2)->default(0.0);
            $table->string('status')->default(PaymentSchedule::NOT_PAID);
            $table->unsignedBigInteger('finance_id');
            $table->timestamps();

            $table->foreign('finance_id')->references('id')->on('finances')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_schedules');
    }
}
