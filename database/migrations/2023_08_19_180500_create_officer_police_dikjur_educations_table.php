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
        Schema::create('public.officer_police_dikjur_educations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('officer_id')->nullable();

            $table->string('police_dikjur_education_place_id')->nullable();
            $table->string('police_dikjur_education_material_id')->nullable();

            $table->string('graduate_year')->nullable();

            $table->timestamps();

            $table->foreign('officer_id', 'fk_officer_police_dikjur_edus_officer_id')->references('id')->on('public.officers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('police_dikjur_education_place_id', 'fk_officer_police_dikjur_edus_police_dikjur_edu_place_id')->references('id')->on('lib.police_dikjur_education_places')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('police_dikjur_education_material_id', 'fk_officer_police_dikjur_edus_police_dikjur_edu_material_id')->references('id')->on('lib.police_dikjur_education_materials')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //drop foreign key constraints first
        Schema::table('public.officer_police_dikjur_educations', function (Blueprint $table) {
            $table->dropForeign('fk_officer_police_dikjur_edus_officer_id');
            $table->dropForeign('fk_officer_police_dikjur_edus_police_dikjur_edu_place_id');
            $table->dropForeign('fk_officer_police_dikjur_edus_police_dikjur_edu_material_id');
        });

        Schema::dropIfExists('public.officer_police_dikjur_educations');
    }
};
