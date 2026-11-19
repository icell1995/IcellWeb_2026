<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocSuratPermohonanPenetapanDiversiDocumentAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doc.surat_permohonan_penetapan_diversi_document_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('surat_permohonan_penetapan_diversi_document_id');

            $table->string('name')->nullable();
            $table->string('original_name')->nullable();
            $table->string('extension')->nullable();
            $table->string('mimetype')->nullable();
            $table->bigInteger('size')->nullable();
            $table->string('type')->nullable();

            $table->timestamps();

            $table->foreign('surat_permohonan_penetapan_diversi_document_id', 'fk_sppd_doc_attachments_sppd_document_id')
                ->references('id')->on('doc.surat_permohonan_penetapan_diversi_documents')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('doc.surat_permohonan_penetapan_diversi_document_attachments');
    }
}
