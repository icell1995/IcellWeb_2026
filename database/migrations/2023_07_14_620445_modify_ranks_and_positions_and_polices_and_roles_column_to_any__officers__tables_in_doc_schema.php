<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyRanksAndPositionsAndPolicesAndRolesColumnToAnyOfficersTablesInDocSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::beginTransaction();
        try{
            Schema::table('doc.surat_perintah_penyelidikan_document_officers', function (Blueprint $table) {
                $table->dropColumn('rank');
                $table->dropColumn('position');
                $table->dropColumn('role');
                $table->dropColumn('headquarter_police');
                $table->dropColumn('regional_police');
                $table->dropColumn('resort_police');
                
                $table->string('position_id', 255)->nullable();
                $table->string('rank_id', 255)->nullable();

                $table->foreign('position_id', 'fk_splidik_doc_officers_position_id')->references('id')->on('lib.positions')->onDelete('restrict')->onUpdate('cascade');
                $table->foreign('rank_id', 'fk_splidik_doc_officers_rank_id')->references('id')->on('lib.ranks')->onDelete('restrict')->onUpdate('cascade');
            });
        
            Schema::table('doc.surat_perintah_penyidikan_document_officers', function (Blueprint $table) {
                $table->dropColumn('rank');
                $table->dropColumn('position');
                $table->dropColumn('role');
                $table->dropColumn('headquarter_police');
                $table->dropColumn('regional_police');
                $table->dropColumn('resort_police');
                
                $table->string('position_id', 255)->nullable();
                $table->string('rank_id', 255)->nullable();

                $table->foreign('position_id', 'fk_spsidik_doc_officers_position_id')->references('id')->on('lib.positions')->onDelete('restrict')->onUpdate('cascade');
                $table->foreign('rank_id', 'fk_spsidik_doc_officers_rank_id')->references('id')->on('lib.ranks')->onDelete('restrict')->onUpdate('cascade');
            });

            Schema::table('doc.surat_perintah_tugas_document_officers', function (Blueprint $table) {
                $table->dropColumn('rank');
                $table->dropColumn('position');
                $table->dropColumn('role');
                $table->dropColumn('headquarter_police');
                $table->dropColumn('regional_police');
                $table->dropColumn('resort_police');
                
                $table->string('position_id', 255)->nullable();
                $table->string('rank_id', 255)->nullable();

                $table->foreign('position_id', 'fk_sptugas_doc_officers_position_id')->references('id')->on('lib.positions')->onDelete('restrict')->onUpdate('cascade');
                $table->foreign('rank_id', 'fk_sptugas_doc_officers_rank_id')->references('id')->on('lib.ranks')->onDelete('restrict')->onUpdate('cascade');
            });
            
            Schema::table('doc.laporan_hasil_gelar_perkara_document_officers', function (Blueprint $table) {
                $table->dropColumn('rank');
                $table->dropColumn('position');
                $table->dropColumn('role');
                
                $table->string('position_id', 255)->nullable();
                $table->string('rank_id', 255)->nullable();

                $table->foreign('position_id', 'fk_lhgp_doc_officers_position_id')->references('id')->on('lib.positions')->onDelete('restrict')->onUpdate('cascade');
                $table->foreign('rank_id', 'fk_lhgp_doc_officers_rank_id')->references('id')->on('lib.ranks')->onDelete('restrict')->onUpdate('cascade');
            });
        
            Schema::table('doc.surat_ketetapan_tentang_penetapan_tersangka_document_officers', function (Blueprint $table) {
                $table->dropColumn('rank');
                $table->dropColumn('position');
                $table->dropColumn('role');
                
                $table->string('position_id', 255)->nullable();
                $table->string('rank_id', 255)->nullable();

                $table->foreign('position_id', 'fk_sket_tp_tersangka_doc_officers_position_id')->references('id')->on('lib.positions')->onDelete('restrict')->onUpdate('cascade');
                $table->foreign('rank_id', 'fk_sket_tp_tersangka_doc_officers_rank_id')->references('id')->on('lib.ranks')->onDelete('restrict')->onUpdate('cascade');
            });
        
            Schema::table('doc.surat_pemberitahuan_dimulainya_penyidikan_document_officers', function (Blueprint $table) {
                $table->dropColumn('rank');
                $table->dropColumn('position');
                $table->dropColumn('role');
                
                $table->string('position_id', 255)->nullable();
                $table->string('rank_id', 255)->nullable();

                $table->foreign('position_id', 'fk_spdp_doc_officers_position_id')->references('id')->on('lib.positions')->onDelete('restrict')->onUpdate('cascade');
                $table->foreign('rank_id', 'fk_spdp_doc_officers_rank_id')->references('id')->on('lib.ranks')->onDelete('restrict')->onUpdate('cascade');
            });

            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::beginTransaction();
        try{
            Schema::table('doc.surat_perintah_penyelidikan_document_officers', function (Blueprint $table) {
                //drop fk
                $table->dropForeign('fk_splidik_doc_officers_position_id');
                $table->dropForeign('fk_splidik_doc_officers_rank_id');
            });
            Schema::table('doc.surat_perintah_penyelidikan_document_officers', function (Blueprint $table) {
                $table->dropColumn('position_id');
                $table->dropColumn('rank_id');
                
                $table->json('rank')->nullable();
                $table->json('position')->nullable();
                $table->json('role')->nullable();
                $table->json('headquarter_police')->nullable();
                $table->json('regional_police')->nullable();
                $table->json('resort_police')->nullable();
            });
            
            Schema::table('doc.surat_perintah_penyidikan_document_officers', function (Blueprint $table) {
                //drop fk
                $table->dropForeign('fk_spsidik_doc_officers_position_id');
                $table->dropForeign('fk_spsidik_doc_officers_rank_id');
            });
            Schema::table('doc.surat_perintah_penyidikan_document_officers', function (Blueprint $table) {
                $table->dropColumn('position_id');
                $table->dropColumn('rank_id');
                
                $table->json('rank')->nullable();
                $table->json('position')->nullable();
                $table->json('role')->nullable();
                $table->json('headquarter_police')->nullable();
                $table->json('regional_police')->nullable();
                $table->json('resort_police')->nullable();
            });
           
            Schema::table('doc.surat_perintah_tugas_document_officers', function (Blueprint $table) {
                //drop fk
                $table->dropForeign('fk_sptugas_doc_officers_position_id');
                $table->dropForeign('fk_sptugas_doc_officers_rank_id');
            });
            Schema::table('doc.surat_perintah_tugas_document_officers', function (Blueprint $table) {
                $table->dropColumn('position_id');
                $table->dropColumn('rank_id');
                
                $table->json('rank')->nullable();
                $table->json('position')->nullable();
                $table->json('role')->nullable();
                $table->json('headquarter_police')->nullable();
                $table->json('regional_police')->nullable();
                $table->json('resort_police')->nullable();
            });

            Schema::table('doc.laporan_hasil_gelar_perkara_document_officers', function (Blueprint $table) {
                //drop fk
                $table->dropForeign('fk_lhgp_doc_officers_position_id');
                $table->dropForeign('fk_lhgp_doc_officers_rank_id');
            });
            Schema::table('doc.laporan_hasil_gelar_perkara_document_officers', function (Blueprint $table) {
                $table->dropColumn('position_id');
                $table->dropColumn('rank_id');
                
                $table->json('rank')->nullable();
                $table->json('position')->nullable();
                $table->json('role')->nullable();
            });

            Schema::table('doc.surat_ketetapan_tentang_penetapan_tersangka_document_officers', function (Blueprint $table) {
                //drop fk
                $table->dropForeign('fk_sket_tp_tersangka_doc_officers_position_id');
                $table->dropForeign('fk_sket_tp_tersangka_doc_officers_rank_id');
            });
            Schema::table('doc.surat_ketetapan_tentang_penetapan_tersangka_document_officers', function (Blueprint $table) {
                $table->dropColumn('position_id');
                $table->dropColumn('rank_id');
                
                $table->json('rank')->nullable();
                $table->json('position')->nullable();
                $table->json('role')->nullable();
            });

            Schema::table('doc.surat_pemberitahuan_dimulainya_penyidikan_document_officers', function (Blueprint $table) {
                //drop fk
                $table->dropForeign('fk_spdp_doc_officers_position_id');
                $table->dropForeign('fk_spdp_doc_officers_rank_id');
            });
            Schema::table('doc.surat_pemberitahuan_dimulainya_penyidikan_document_officers', function (Blueprint $table) {
                $table->dropColumn('position_id');
                $table->dropColumn('rank_id');
                
                $table->json('rank')->nullable();
                $table->json('position')->nullable();
                $table->json('role')->nullable();
            });

            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }
}
