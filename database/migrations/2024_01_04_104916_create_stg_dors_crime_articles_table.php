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
            $table->uuid('id')->primary();

            $table->uuid('dors_accident_id')->nullable();
            $table->string('id_uu')->nullable();
            $table->string('dors_id')->nullable();
            $table->string('pasal')->nullable();

            $table->timestamps();

            $table->foreign('dors_accident_id', 'fk_stg_dors_crime_articles_dors_accident_id')->references('id')->on('public.stg_dors_accidents')->onUpdate('CASCADE')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('public.stg_dors_crime_articles', function (Blueprint $table) {
            $table->dropForeign('fk_stg_dors_crime_articles_dors_accident_id');
        });
        Schema::dropIfExists('public.stg_dors_crime_articles');
    }
};
