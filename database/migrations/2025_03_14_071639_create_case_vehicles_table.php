<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('case_vehicles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('accident_id')->nullable();

            $table->string('stnk_number')->nullable();
            $table->string('stnk_province')->nullable();
            $table->string('stnk_regency')->nullable();
            $table->string('stnk_district')->nullable();
            $table->string('stnk_village')->nullable();
            $table->string('stnk_road_name')->nullable();
            $table->string('stnk_rt')->nullable();
            $table->string('stnk_rw')->nullable();
            
            $table->string('owner_name')->nullable();
            $table->string('owner_first_name')->nullable();
            $table->string('owner_last_name')->nullable();
            $table->string('owner_address')->nullable();
            $table->string('owner_gender')->nullable();
            $table->string('owner_religion')->nullable();
            $table->date('owner_birth_date')->nullable();
            $table->string('owner_age')->nullable();
            $table->string('owner_identity_type')->nullable();
            $table->string('owner_identity_number')->nullable();
            $table->string('owner_license_class')->nullable();
            $table->string('owner_license_number')->nullable();
            
            $table->string('vehicle_type')->nullable();
            $table->string('vehicle_design')->nullable();
            $table->string('vehicle_color')->nullable();
            
            $table->string('plate_number')->nullable();
            $table->string('frame_number')->nullable();
            $table->string('engine_number')->nullable();
            $table->string('engine_capacity')->nullable();
            $table->string('production_year')->nullable();
            $table->string('cargo_load')->nullable();
            $table->string('axis')->nullable();

            $table->text('temporary_deductive')->nullable();
            $table->text('accident_description')->nullable();

            $table->timestamps();

            $table->foreign('accident_id', 'fk_case_vehicles_accident_id')->references('id')->on('accidents')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_vehicles', function (Blueprint $table) {
            $table->dropForeign('fk_case_vehicles_accident_id');
        });
        Schema::dropIfExists('case_vehicles');
    }
};
