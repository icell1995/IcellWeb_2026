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
        Schema::create('public.officer_certificate_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('officer_id')->nullable();

            $table->string('certificate_number')->nullable();
            $table->date('begin_date')->nullable();
            $table->date('expired_date')->nullable();

            $table->timestamps();

            $table->foreign('officer_id', 'fk_officer_certificate_histories_officer_id')->references('id')->on('public.officers')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //drop foreign key constraints first
        Schema::table('public.officer_certificate_histories', function (Blueprint $table) {
            $table->dropForeign('fk_officer_certificate_histories_officer_id');
        });
        
        Schema::dropIfExists('public.officer_certificate_histories');
    }
};
