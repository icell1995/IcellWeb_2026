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
        Schema::create('log_api_irsms_korlantas_post_stg_dors_accidents', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('code');
            $table->string('status');
            $table->string('method');
            $table->string('endpoint');
            $table->string('ip_address')->nullable();
            $table->string('class_model')->nullable();
            $table->text('message')->nullable();
            $table->json('data')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_api_irsms_korlantas_post_stg_dors_accidents');
    }
};
