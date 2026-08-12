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
        Schema::create('public.stg_dors_crime_articles', function (Blueprint $table) {
            $table->uuid('id');

            $table->string('dors_id');//
            $table->integer('id_uu')->nullable();
            $table->string('pasal')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public.stg_dors_crime_articles');
    }
};
