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
        Schema::create('doc.tahap_1_document_attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tahap_1_document_id');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type')->nullable();
            $table->integer('file_size')->nullable();
            $table->string('category')->nullable()->comment('Contoh: SCAN_DOKUMEN, LAMPIRAN');
            $table->timestamps();

            $table->foreign('tahap_1_document_id', 'fk_thp1_attach_doc_id')->references('id')->on('doc.tahap_1_documents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doc.tahap_1_document_attachments', function (Blueprint $table) {
            $table->dropForeign('fk_thp1_attach_doc_id');
        });
        Schema::dropIfExists('doc.tahap_1_document_attachments');
    }
};
