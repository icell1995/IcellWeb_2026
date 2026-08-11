<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifySuratPerintahTugasDocumentsAndRelationshipTables extends Migration
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
            Schema::table('document.surat_perintah_tugas_documents', function (Blueprint $table) {
                $table->renameColumn('no_surat', 'document_number');
                $table->renameColumn('tanggal_springas', 'document_date');
                $table->renameColumn('tanggal_dimulai', 'start_date');
                $table->renameColumn('tanggal_berakhir', 'end_date');
                $table->renameColumn('pejabat_penandatangan', 'signatory_id');

            });
            Schema::table('document.surat_perintah_tugas_documents', function (Blueprint $table) {
                $table->string('no_lp')->nullable()->change();
                $table->uuid('signatory_id')->nullable()->change();
                $table->string('ketua_tim')->nullable()->change();
                $table->string('no_sprindik')->nullable()->change();
                $table->date('end_date')->nullable()->change();
    
                $table->uuid('surat_perintah_penyidikan_document_id')->nullable();
                $table->foreign('surat_perintah_penyidikan_document_id')->references('id')->on('document.investigation_order_letters')->onUpdate('cascade')->onDelete('set null')->constraint('fk_surat_perintah_tugas_surat_perintah_penyidikan_id');
                $table->foreign('signatory_id')->references('id')->on('authorized_signatories')->onUpdate('cascade')->onDelete('set null')->constraint('fk_surat_perintah_tugas_signatory_id');
            });

            Schema::table('document.surat_perintah_tugas_document_officer', function (Blueprint $table) {
                $table->renameColumn('sprint_gas_id', 'surat_perintah_tugas_document_id');
            });

            DB::commit();
        } catch(\Exception $e){
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
            Schema::table('document.surat_perintah_tugas_documents', function (Blueprint $table) {
                // $table->dropForeign('fk_surat_perintah_tugas_surat_perintah_penyidikan_id');
                // $table->dropForeign('fk_surat_perintah_tugas_signatory_id');

                $table->string('no_lp')->nullable(false)->change();
                $table->uuid('signatory_id')->nullable(false)->change();
                $table->string('ketua_tim')->nullable(false)->change();
                $table->string('no_sprindik')->nullable(false)->change();
                $table->date('end_date')->nullable(false)->change();
                
                $table->dropColumn('surat_perintah_penyidikan_document_id');
            });
            Schema::table('document.surat_perintah_tugas_documents', function (Blueprint $table) {
                    $table->renameColumn('document_number', 'no_surat');
                    $table->renameColumn('document_date', 'tanggal_springas');
                    $table->renameColumn('start_date', 'tanggal_dimulai');
                    $table->renameColumn('end_date', 'tanggal_berakhir');
                    $table->renameColumn('signatory_id', 'pejabat_penandatangan');
            });

            Schema::table('document.surat_perintah_tugas_document_officer', function (Blueprint $table) {
                $table->renameColumn('surat_perintah_tugas_document_id', 'sprint_gas_id');
            });

            DB::commit();
        } catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }
}
