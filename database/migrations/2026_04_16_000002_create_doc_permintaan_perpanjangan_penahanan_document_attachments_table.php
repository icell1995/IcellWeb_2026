<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lampiran file untuk Surat Permintaan Perpanjangan Penahanan.
     * Bukan input di form create—baris ini dipakai saat upload PDF setelah persetujuan / tanda tangan (alur dokumen).
     * Pola sama dengan doc.*_document_attachments lain (SKET, SP penyidikan, dll.).
     */
    public function up(): void
    {
        Schema::create('doc.permintaan_perpanjangan_penahanan_document_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('permintaan_perpanjangan_penahanan_document_id');

            $table->string('name');
            $table->string('original_name')->nullable();
            $table->string('extension')->nullable();
            $table->string('mimetype')->nullable();
            $table->string('size')->nullable();
            $table->string('path')->nullable();

            $table->enum('type', ['DOCUMENT', 'IMAGE', 'VIDEO', 'AUDIO'])->nullable()->default('DOCUMENT');
            $table->timestamps();

            $table->foreign(
                'permintaan_perpanjangan_penahanan_document_id',
                'fk_ppp_doc_attachments_ppp_document_id'
            )
                ->references('id')
                ->on('doc.permintaan_perpanjangan_penahanan_documents')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('doc.permintaan_perpanjangan_penahanan_document_attachments', function (Blueprint $table) {
            $table->dropForeign('fk_ppp_doc_attachments_ppp_document_id');
        });

        Schema::dropIfExists('doc.permintaan_perpanjangan_penahanan_document_attachments');
    }
};
