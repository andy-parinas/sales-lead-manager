<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildingAuthoritiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('building_authorities', function (Blueprint $table) {
            $table->id();
            $table->string('approval_required');
            $table->string('building_authority_name')->nullable();
            $table->date('date_plans_sent_to_draftsman')->nullable();
            $table->date('date_plans_completed')->nullable();
            $table->date('date_plans_sent_to_authority')->nullable();
            $table->text('building_authority_comments')->nullable();
            $table->date('date_anticipated_approval')->nullable();
            $table->date('date_received_from_authority')->nullable();
            $table->string('permit_number')->nullable();
            $table->string('security_deposit_required')->nullable();
            $table->string('building_insurance_name')->nullable();
            $table->string('building_insurance_number')->nullable();
            $table->string('date_insurance_request_sent')->nullable();
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
        Schema::dropIfExists('building_authorities');
    }
}
