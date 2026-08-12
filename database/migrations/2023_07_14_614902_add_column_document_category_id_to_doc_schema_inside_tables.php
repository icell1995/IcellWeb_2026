<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnDocumentCategoryIdToDocSchemaInsideTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('doc.surat_perintah_penyelidikan_documents', function (Blueprint $table) {
            $table->string('document_category_id')->nullable();

            $table->foreign('document_category_id', 'fk_splidik_docs_document_category_id')
                ->references('id')
                ->on('lib.document_categories')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
       
        Schema::table('doc.surat_perintah_penyidikan_documents', function (Blueprint $table) {
            $table->string('document_category_id')->nullable();

            $table->foreign('document_category_id', 'fk_spsidik_docs_document_category_id')
                ->references('id')
                ->on('lib.document_categories')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });

        Schema::table('doc.surat_perintah_tugas_documents', function (Blueprint $table) {
            $table->string('document_category_id')->nullable();

            $table->foreign('document_category_id', 'fk_sptugas_docs_document_category_id')
                ->references('id')
                ->on('lib.document_categories')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
      
        Schema::table('doc.laporan_hasil_gelar_perkara_documents', function (Blueprint $table) {
            $table->string('document_category_id')->nullable();

            $table->foreign('document_category_id', 'fk_lhgp_docs_document_category_id')
                ->references('id')
                ->on('lib.document_categories')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
        
        Schema::table('doc.surat_ketetapan_tentang_penetapan_tersangka_documents', function (Blueprint $table) {
            $table->string('document_category_id')->nullable();

            $table->foreign('document_category_id', 'fk_sket_tp_tersangka_docs_document_category_id')
                ->references('id')
                ->on('lib.document_categories')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
        
        Schema::table('doc.surat_pemberitahuan_dimulainya_penyidikan_documents', function (Blueprint $table) {
            $table->string('document_category_id')->nullable();

            $table->foreign('document_category_id', 'fk_spdp_docs_document_category_id')
                ->references('id')
                ->on('lib.document_categories')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         //drop fk
        Schema::table('doc.surat_perintah_penyelidikan_documents', function (Blueprint $table) {
            $table->dropForeign('fk_splidik_docs_document_category_id');
        });

        Schema::table('doc.surat_perintah_penyidikan_documents', function (Blueprint $table) {
            $table->dropForeign('fk_spsidik_docs_document_category_id');
        });

        Schema::table('doc.surat_perintah_tugas_documents', function (Blueprint $table) {
            $table->dropForeign('fk_sptugas_docs_document_category_id');
        });

        Schema::table('doc.laporan_hasil_gelar_perkara_documents', function (Blueprint $table) {
            $table->dropForeign('fk_lhgp_docs_document_category_id');
        });

        Schema::table('doc.surat_ketetapan_tentang_penetapan_tersangka_documents', function (Blueprint $table) {
            $table->dropForeign('fk_sket_tp_tersangka_docs_document_category_id');
        });

        Schema::table('doc.surat_pemberitahuan_dimulainya_penyidikan_documents', function (Blueprint $table) {
            $table->dropForeign('fk_spdp_docs_document_category_id');
        });

        //drop column
        Schema::table('doc.surat_perintah_penyelidikan_documents', function (Blueprint $table) {
            $table->dropColumn('document_category_id');
        });
      
        Schema::table('doc.surat_perintah_penyidikan_documents', function (Blueprint $table) {
            $table->dropColumn('document_category_id');
        });
       
        Schema::table('doc.surat_perintah_tugas_documents', function (Blueprint $table) {
            $table->dropColumn('document_category_id');
        });

        Schema::table('doc.laporan_hasil_gelar_perkara_documents', function (Blueprint $table) {
            $table->dropColumn('document_category_id');
        });

        Schema::table('doc.surat_ketetapan_tentang_penetapan_tersangka_documents', function (Blueprint $table) {
            $table->dropColumn('document_category_id');
        });

        Schema::table('doc.surat_pemberitahuan_dimulainya_penyidikan_documents', function (Blueprint $table) {
            $table->dropColumn('document_category_id');
        });
    }
}
