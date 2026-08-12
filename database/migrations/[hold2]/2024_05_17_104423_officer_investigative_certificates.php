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
        Schema::create('officer_investigative_certificates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('officer_id');

            $table->boolean('is_certificate_penyidik_exists');
            $table->string('certificate_penyidik_number')->nullable();
            $table->date('certificate_penyidik_start_date')->nullable();
            $table->date('certificate_penyidik_end_date')->nullable();

            $table->timestamps();

            $table->foreign('officer_id', 'fk_officer_investigative_certificates_officer_id')
                ->references('id')
                ->on('public.officers')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         // Drop foreign key fk_officer_investigative_certificates_officer_id
         Schema::table('public.officer_investigative_certificates', function (Blueprint $table) {
            $table->dropForeign('fk_officer_investigative_certificates_officer_id');
        });
        
        Schema::dropIfExists('officer_investigative_certificates');
    }
};
