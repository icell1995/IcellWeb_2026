<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocSuratKetetapanTentangPenetapanTersangkaDocumentAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doc.surat_ketetapan_tentang_penetapan_tersangka_document_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('surat_ketetapan_tentang_penetapan_tersangka_document_id');

            $table->string('name');
            $table->string('original_name')->nullable();
            $table->string('extension')->nullable();
            $table->string('mimetype')->nullable();
            $table->string('size')->nullable();
            $table->string('path')->nullable();

            $table->enum('type', ['DOCUMENT', 'IMAGE', 'VIDEO', 'AUDIO'])->nullable(true)->default('DOCUMENT');
            $table->timestamps();

            $table->foreign('surat_ketetapan_tentang_penetapan_tersangka_document_id', 'fk_sket_tp_tsk_doc_attachments_sket_tp_tsk_document_id')
                ->references('id')
                ->on('doc.surat_ketetapan_tentang_penetapan_tersangka_documents')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop foreign key
        Schema::table('doc.surat_ketetapan_tentang_penetapan_tersangka_document_attachments', function (Blueprint $table) {
            $table->dropForeign('fk_sket_tp_tsk_doc_attachments_sket_tp_tsk_document_id');
        });
        Schema::dropIfExists('doc.surat_ketetapan_tentang_penetapan_tersangka_document_attachments');
    }
}
