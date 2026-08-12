<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotSuratKetetapanTentangPenetapanTersangkaDocumentSuspectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pivot.surat_ketetapan_tentang_penetapan_tersangka_document_suspect', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->uuid('surat_ketetapan_tentang_penetapan_tersangka_document_id');
            $table->uuid('suspect_id');

            $table->timestamps();

            $table->foreign('surat_ketetapan_tentang_penetapan_tersangka_document_id', 'fk_sket_tp_tersangka_doc_suspect_sket_tp_tersangka_document_id')->references('id')->on('doc.surat_ketetapan_tentang_penetapan_tersangka_documents')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('suspect_id', 'fk_sket_tp_tersangka_doc_suspect_suspect_id')->references('id')->on('public.suspects')->onDelete('restrict')->onUpdate('cascade');
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
        Schema::table('pivot.surat_ketetapan_tentang_penetapan_tersangka_document_suspect', function (Blueprint $table) {
            $table->dropForeign('fk_sket_tp_tersangka_doc_suspect_sket_tp_tersangka_document_id');
            $table->dropForeign('fk_sket_tp_tersangka_doc_suspect_suspect_id');
        });
        Schema::dropIfExists('pivot.surat_ketetapan_tentang_penetapan_tersangka_document_suspect');
    }
}
