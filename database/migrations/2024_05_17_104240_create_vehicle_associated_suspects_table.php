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
        Schema::create('vehicle_associated_suspects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('suspect_id');
            $table->uuid('accident_id');

            $table->unsignedBigInteger('accident_type_id')->nullable();
            $table->unsignedBigInteger('vehicle_type_id')->nullable();
            $table->string('identity_type_id')->nullable();
            $table->unsignedBigInteger('accident_cause_id')->nullable();

            $table->string('identity_number')->nullable();
            $table->string('identity_type')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('accident_number')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->string('plate_number')->nullable();
            $table->text('accident_location')->nullable();
            $table->date('accident_date')->nullable();
            $table->string('accident_type')->nullable();
            $table->string('accident_cause')->nullable();
            $table->string('total_victim')->nullable();
            $table->string('total_material_loss')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();

            $table->timestamps();

            $table->foreign('suspect_id', 'fk_vehicle_associated_suspects_suspect_id')->references('id')->on('suspects')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('accident_id', 'fk_vehicle_associated_suspects_accident_id')->references('id')->on('accidents')->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('accident_type_id', 'fk_vehicle_associated_suspects_accident_type_id')->references('id')->on('lib.accident_types')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('vehicle_type_id', 'fk_vehicle_associated_suspects_vehicle_type_id')->references('id')->on('lib.vehicle_types')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('identity_type_id', 'fk_vehicle_associated_suspects_identity_type_id')->references('id')->on('lib.identity_types')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('accident_cause_id', 'fk_vehicle_associated_suspects_accident_cause_id')->references('id')->on('lib.accident_causes')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //

        Schema::table('vehicle_associated_suspects', function (Blueprint $table) {
            $table->dropForeign('fk_vehicle_associated_suspects_suspect_id');
            $table->dropForeign('fk_vehicle_associated_suspects_accident_id');
            $table->dropForeign('fk_vehicle_associated_suspects_accident_type_id');
            $table->dropForeign('fk_vehicle_associated_suspects_vehicle_type_id');
            $table->dropForeign('fk_vehicle_associated_suspects_identity_type_id');
            $table->dropForeign('fk_vehicle_associated_suspects_accident_cause_id');
        });

        Schema::dropIfExists('vehicle_associated_suspects');
    }
};
