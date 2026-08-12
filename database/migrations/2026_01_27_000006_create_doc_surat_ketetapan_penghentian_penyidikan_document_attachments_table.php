<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocSuratKetetapanPenghentianPenyidikanDocumentAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doc.surat_ketetapan_penghentian_penyidikan_document_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('surat_ketetapan_penghentian_penyidikan_document_id');

            $table->string('name');
            $table->string('original_name')->nullable();
            $table->string('extension')->nullable();
            $table->string('mimetype')->nullable();
            $table->string('size')->nullable();
            $table->string('path')->nullable();

            $table->enum('type', ['DOCUMENT', 'IMAGE', 'VIDEO', 'AUDIO'])->nullable(true)->default('DOCUMENT');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('surat_ketetapan_penghentian_penyidikan_document_id', 'fk_skppy_doc_attachments_skppy_document_id')
                  ->references('id')
                  ->on('doc.surat_ketetapan_penghentian_penyidikan_documents')
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
        // Drop foreign key
        Schema::table('doc.surat_ketetapan_penghentian_penyidikan_document_attachments', function (Blueprint $table) {
            $table->dropForeign('fk_skppy_doc_attachments_skppy_document_id');
        });
        
        Schema::dropIfExists('doc.surat_ketetapan_penghentian_penyidikan_document_attachments');
    }
}
