<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocLaporanHasilGelarPerkaraDocumentFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doc.laporan_hasil_gelar_perkara_document_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('laporan_hasil_gelar_perkara_document_id');

            $table->string('name');
            $table->string('original_name')->nullable();
            $table->string('extension')->nullable();
            $table->string('mimetype')->nullable();
            $table->string('size')->nullable();

            $table->enum('type', ['DOCUMENT', 'IMAGE', 'VIDEO', 'AUDIO'])->default('DOCUMENT');

            $table->timestamps();

            $table->foreign('laporan_hasil_gelar_perkara_document_id', 'fk_lhgp_doc_files_lh_gelar_perkara_document_id')->references('id')->on('doc.laporan_hasil_gelar_perkara_documents')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::table('doc.laporan_hasil_gelar_perkara_document_files', function (Blueprint $table) {
            $table->dropForeign('fk_lhgp_doc_files_lh_gelar_perkara_document_id');
        });
        Schema::dropIfExists('doc.laporan_hasil_gelar_perkara_document_files');
    }
}
