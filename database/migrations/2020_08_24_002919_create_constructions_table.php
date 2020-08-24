<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConstructionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('constructions', function (Blueprint $table) {
            $table->id();
            $table->string('site_address');
            $table->unsignedBigInteger('postcode_id');
            $table->text('material_list')->nullable();
            $table->date('date_materials_received')->nullable();
            $table->date('date_assembly_completed')->nullable();
            $table->date('date_anticipated_delivery')->nullable();
            $table->date('date_finished_product_delivery')->nullable();
            $table->string('coil_number')->nullable();
            $table->unsignedInteger('trade_staff_id');
            $table->date('anticipated_construction_start')->nullable();
            $table->date('anticipated_construction_complete')->nullable();
            $table->date('actual_construction_start')->nullable();
            $table->date('actual_construction_complete')->nullable();
            $table->text('comments')->nullable();
            $table->date('final_inspection_date')->nullable();
            $table->unsignedBigInteger('lead_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('constructions');
    }
}
