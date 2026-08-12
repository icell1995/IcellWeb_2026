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
        Schema::create('doc.surat_perintah_penahanan_document_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('surat_perintah_penahanan_document_id');

            $table->string('name');
            $table->string('original_name')->nullable();
            $table->string('extension')->nullable();
            $table->string('mimetype')->nullable();
            $table->string('size')->nullable();
            $table->string('path')->nullable();

            $table->enum('type', ['DOCUMENT', 'IMAGE', 'VIDEO', 'AUDIO'])->nullable(true)->default('DOCUMENT');
            $table->timestamps();
            $table->string('flag')->nullable();

            $table->foreign('surat_perintah_penahanan_document_id', 'fk_sprin_penahanan_doc_attachments_sprin_penahanan_document_id')
                ->references('id')
                ->on('doc.surat_perintah_penahanan_documents')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doc.surat_perintah_penahanan_document_attachments', function (Blueprint $table) {
            $table->dropForeign('fk_sprin_penahanan_doc_attachments_sprin_penahanan_document_id');
        });
        Schema::dropIfExists('doc.surat_perintah_penahanan_document_attachments');
    }
};
