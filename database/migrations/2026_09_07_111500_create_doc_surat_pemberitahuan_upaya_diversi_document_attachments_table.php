<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocSuratPemberitahuanUpayaDiversiDocumentAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doc.surat_pemberitahuan_upaya_diversi_document_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('surat_pemberitahuan_upaya_diversi_document_id');

            $table->string('name');
            $table->string('original_name')->nullable();
            $table->string('extension')->nullable();
            $table->string('mimetype')->nullable();
            $table->string('size')->nullable();
            $table->string('path')->nullable();
            $table->string('flag')->nullable();

            $table->enum('type', ['DOCUMENT', 'IMAGE', 'VIDEO', 'AUDIO'])->nullable(true)->default('DOCUMENT');
            $table->timestamps();

            $table->foreign('surat_pemberitahuan_upaya_diversi_document_id', 'fk_spud_doc_attachments_spud_document_id')
                ->references('id')
                ->on('doc.surat_pemberitahuan_upaya_diversi_documents')
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
        Schema::table('doc.surat_pemberitahuan_upaya_diversi_document_attachments', function (Blueprint $table) {
            $table->dropForeign('fk_spud_doc_attachments_spud_document_id');
        });
        Schema::dropIfExists('doc.surat_pemberitahuan_upaya_diversi_document_attachments');
    }
}
