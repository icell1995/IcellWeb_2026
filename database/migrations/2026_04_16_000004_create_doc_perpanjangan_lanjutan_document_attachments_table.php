<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lampiran file untuk Surat Perintah Penahanan Lanjutan.
     * Diisi setelah dokumen disetujui / ditandatangani (bukan dari form create)—sama pola dengan surat permintaan & dokumen lain.
     */
    public function up(): void
    {
        Schema::create('doc.perpanjangan_lanjutan_document_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('perpanjangan_lanjutan_document_id');

            $table->string('name');
            $table->string('original_name')->nullable();
            $table->string('extension')->nullable();
            $table->string('mimetype')->nullable();
            $table->string('size')->nullable();
            $table->string('path')->nullable();

            $table->enum('type', ['DOCUMENT', 'IMAGE', 'VIDEO', 'AUDIO'])->nullable()->default('DOCUMENT');
            $table->timestamps();

            $table->foreign(
                'perpanjangan_lanjutan_document_id',
                'fk_pl_doc_attachments_pl_document_id'
            )
                ->references('id')
                ->on('doc.perpanjangan_lanjutan_documents')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('doc.perpanjangan_lanjutan_document_attachments', function (Blueprint $table) {
            $table->dropForeign('fk_pl_doc_attachments_pl_document_id');
        });

        Schema::dropIfExists('doc.perpanjangan_lanjutan_document_attachments');
    }
};
