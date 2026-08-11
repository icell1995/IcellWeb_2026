<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotSuratPemberitahuanDimulainyaPenyidikanDocumentInformantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pivot.surat_pemberitahuan_dimulainya_penyidikan_document_informant', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->uuid('surat_pemberitahuan_dimulainya_penyidikan_document_id');
            $table->uuid('informant_id');

            $table->timestamps();

            $table->foreign('surat_pemberitahuan_dimulainya_penyidikan_document_id', 'fk_spdp_doc_informant_spdp_document_id')->references('id')->on('doc.surat_pemberitahuan_dimulainya_penyidikan_documents')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('informant_id', 'fk_spdp_doc_informant_informant_id')->references('id')->on('public.informants')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Drop fk
        Schema::table('pivot.surat_pemberitahuan_dimulainya_penyidikan_document_informant', function (Blueprint $table) {
            $table->dropForeign('fk_spdp_doc_informant_spdp_document_id');
            $table->dropForeign('fk_spdp_doc_informant_informant_id');
        });
        Schema::dropIfExists('pivot.surat_pemberitahuan_dimulainya_penyidikan_document_informant');
    }
}
