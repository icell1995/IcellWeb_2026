<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotLaporanHasilGelarPerkaraDocumentSuspectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pivot.laporan_hasil_gelar_perkara_document_suspect', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->uuid('laporan_hasil_gelar_perkara_document_id');
            $table->uuid('suspect_id');

            $table->timestamps();

            $table->foreign('laporan_hasil_gelar_perkara_document_id', 'fk_lhgp_doc_suspect_lhgp_document_id')->references('id')->on('doc.laporan_hasil_gelar_perkara_documents')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('suspect_id', 'fk_lhgp_doc_suspect_suspect_id')->references('id')->on('public.suspects')->onDelete('restrict')->onUpdate('cascade');
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
        Schema::table('pivot.laporan_hasil_gelar_perkara_document_suspect', function (Blueprint $table) {
            $table->dropForeign('fk_lhgp_doc_suspect_lhgp_document_id');
            $table->dropForeign('fk_lhgp_doc_suspect_suspect_id');
        });
        Schema::dropIfExists('pivot.laporan_hasil_gelar_perkara_document_suspect');
    }
}
