<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocSuratPerintahPenyelidikanDocumentCaseKeywordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doc.surat_perintah_penyelidikan_document_case_keywords', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('surat_perintah_penyelidikan_document_id');

            $table->string('keyword_id')->nullable();
            $table->string('keyword');

            $table->timestamps();

            $table->foreign('surat_perintah_penyelidikan_document_id', 'fk_splidik_doc_case_keywords_sp_penyelidikan_document_id')->references('id')->on('doc.surat_perintah_penyelidikan_documents')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('doc.surat_perintah_penyelidikan_document_case_keywords', function (Blueprint $table) {
            $table->dropForeign('fk_splidik_doc_case_keywords_sp_penyelidikan_document_id');
        });
        Schema::dropIfExists('doc.surat_perintah_penyelidikan_document_case_keywords');
    }
}
