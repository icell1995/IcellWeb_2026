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
        Schema::create('public.officer_career_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('officer_id')->nullable();

            $table->string('police_division_id')->nullable();
            $table->string('position_name')->nullable();
            $table->string('year')->nullable();

            $table->timestamps();

            $table->foreign('officer_id', 'fk_officer_career_histories_officer_id')->references('id')->on('public.officers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('police_division_id', 'fk_officer_career_histories_police_division_id')->references('id')->on('lib.police_divisions')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //drop foreign key constraints first
        Schema::table('public.officer_career_histories', function (Blueprint $table) {
            $table->dropForeign('fk_officer_career_histories_officer_id');
            $table->dropForeign('fk_officer_career_histories_police_division_id');
        });
        
        Schema::dropIfExists('public.officer_career_histories');
    }
};
