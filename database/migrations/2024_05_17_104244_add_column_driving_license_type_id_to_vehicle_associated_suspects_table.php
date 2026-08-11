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
        Schema::table('vehicle_associated_suspects', function (Blueprint $table) {
            $table->unsignedBigInteger('driving_license_type_id')->nullable();

            $table->foreign('driving_license_type_id', 'fk_vehicle_associated_suspects_driving_license_type_id')->references('id')->on('lib.driving_license_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_associated_suspects', function (Blueprint $table) {
            $table->dropForeign('fk_vehicle_associated_suspects_driving_license_type_id');
        });

        Schema::table('vehicle_associated_suspects', function (Blueprint $table) {
            $table->dropColumn('driving_license_type_id');
        });
    }
};
