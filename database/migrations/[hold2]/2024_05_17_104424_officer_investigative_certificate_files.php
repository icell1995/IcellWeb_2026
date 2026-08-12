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
        Schema::create('officer_investigative_certificate_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('officer_investigative_certificate_id');

            $table->string('name');
            $table->string('original_name')->nullable();
            $table->string('extension')->nullable();
            $table->string('mimetype')->nullable();
            $table->string('size')->nullable();

            $table->timestamps();

            $table->foreign('officer_investigative_certificate_id', 'fk_officer_inv_cert_files_officer_inv_cert_id')->references('id')->on('officer_investigative_certificates')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('officer_investigative_certificate_files', function (Blueprint $table) {
            $table->dropForeign('fk_officer_inv_cert_files_officer_inv_cert_id');
        });
        Schema::dropIfExists('officer_investigative_certificate_files');
    }
};
