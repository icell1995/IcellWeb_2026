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
        Schema::create('doc.tahap_1_document_officers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tahap_1_document_id');
            $table->string('officer_id');
            $table->string('register_number')->nullable();
            $table->string('full_name')->nullable();
            $table->string('rank')->nullable();
            $table->string('position')->nullable();
            $table->string('police_name')->nullable();
            $table->string('class')->nullable()->comment('Contoh: SIGNATORY, INVESTIGATOR');
            $table->timestamps();

            $table->foreign('tahap_1_document_id', 'fk_thp1_officers_doc_id')->references('id')->on('doc.tahap_1_documents')->onDelete('cascade');
            $table->foreign('officer_id', 'fk_thp1_officers_officer_id')->references('id')->on('public.officers')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doc.tahap_1_document_officers', function (Blueprint $table) {
            $table->dropForeign('fk_thp1_officers_doc_id');
            $table->dropForeign('fk_thp1_officers_officer_id');
        });
        Schema::dropIfExists('doc.tahap_1_document_officers');
    }
};
