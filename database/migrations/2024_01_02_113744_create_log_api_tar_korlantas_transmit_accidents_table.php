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
        Schema::create('public.log_api_tar_korlantas_transmit_accidents', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->uuid('accident_id')->nullable();
            $table->string('class_model')->nullable();
            $table->string('ip_address')->nullable();
            
            $table->timestamps();

            $table->foreign('accident_id', 'fk_log_api_tar_korlantas_transmit_accidents_accident_id')
                ->references('id')
                ->on('public.accidents')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('public.log_api_tar_korlantas_transmit_accidents', function (Blueprint $table) {
            $table->dropForeign('fk_log_api_tar_korlantas_transmit_accidents_accident_id');
        });
        
        Schema::dropIfExists('public.log_api_tar_korlantas_transmit_accidents');
    }
};
