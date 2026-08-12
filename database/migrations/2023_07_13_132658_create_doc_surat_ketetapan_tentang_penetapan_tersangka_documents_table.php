<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocSuratKetetapanTentangPenetapanTersangkaDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doc.surat_ketetapan_tentang_penetapan_tersangka_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id')->nullable();
            $table->uuid('surat_perintah_penyidikan_document_id')->nullable();
            
            $table->string('document_number');
            $table->date('document_date');
            
            $table->string('suspect_source_id')->nullable();
            $table->uuid('laporan_hasil_gelar_perkara_document_id')->nullable();

            $table->date('resume_suspect_determination_date')->nullable();

            $table->string('prosecutor_id')->nullable();

            $table->boolean('is_active')->default(true)->after('id');

            $table->string('created_by')->comment('Diisi data user');
            $table->string('updated_by')->nullable()->comment('Diisi data user');
            $table->string('deleted_by')->nullable()->after('deleted_at')->comment('Diisi data user');
        
            $table->timestamps();
            $table->softDeletes()->after('updated_at');
            $table->dateTime('last_synced_at')->nullable()->comment('Waktu terakhir ditarik dengan EMP');

            $table->foreign('accident_id', 'fk_sket_tp_tersangka_docs_accident_id')->references('id')->on('public.accidents')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('surat_perintah_penyidikan_document_id', 'fk_sket_tp_tersangka_docs_sp_penyidikan_document_id')->references('id')->on('doc.surat_perintah_penyidikan_documents')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('laporan_hasil_gelar_perkara_document_id', 'fk_sket_tp_tersangka_docs_lhgp_document_id')->references('id')->on('doc.laporan_hasil_gelar_perkara_documents')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('prosecutor_id', 'fk_sket_tp_tersangka_docs_prosecutor_id')->references('id')->on('lib.prosecutors')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('suspect_source_id', 'fk_sket_tp_tersangka_docs_suspect_source_id')->references('id')->on('lib.suspect_sources')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations. SuratKetetapanTentangPenetapanTersangkaDocument
     *
     * @return void
     */
    public function down()
    {
        // Drop Foreign Key
        Schema::table('doc.surat_ketetapan_tentang_penetapan_tersangka_documents', function (Blueprint $table) {
            $table->dropForeign('fk_sket_tp_tersangka_docs_accident_id');
            $table->dropForeign('fk_sket_tp_tersangka_docs_sp_penyidikan_document_id');
            $table->dropForeign('fk_sket_tp_tersangka_docs_lhgp_document_id');
            $table->dropForeign('fk_sket_tp_tersangka_docs_prosecutor_id');
            $table->dropForeign('fk_sket_tp_tersangka_docs_suspect_source_id');
        });
        Schema::dropIfExists('doc.surat_ketetapan_tentang_penetapan_tersangka_documents');
    }
}
