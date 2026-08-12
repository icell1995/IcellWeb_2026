<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lampiran PDF/Word Surat Perintah Penangkapan setelah alur persetujuan.
     */
    public function up(): void
    {
        Schema::create('doc.surat_perintah_penangkapan_document_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('surat_perintah_penangkapan_document_id');

            $table->string('name');
            $table->string('original_name')->nullable();
            $table->string('extension')->nullable();
            $table->string('mimetype')->nullable();
            $table->string('size')->nullable();
            $table->string('path')->nullable();

            $table->enum('type', ['DOCUMENT', 'IMAGE', 'VIDEO', 'AUDIO'])->nullable()->default('DOCUMENT');
            $table->timestamps();

            $table->foreign(
                'surat_perintah_penangkapan_document_id',
                'fk_spp_doc_attachments_spp_document_id'
            )
                ->references('id')
                ->on('doc.surat_perintah_penangkapan_documents')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('doc.surat_perintah_penangkapan_document_attachments', function (Blueprint $table) {
            $table->dropForeign('fk_spp_doc_attachments_spp_document_id');
        });

        Schema::dropIfExists('doc.surat_perintah_penangkapan_document_attachments');
    }
};
