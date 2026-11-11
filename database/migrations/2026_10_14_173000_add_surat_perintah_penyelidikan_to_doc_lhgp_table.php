<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddSuratPerintahPenyelidikanToDocLhgpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::beginTransaction();
        try {
            Schema::table('doc.laporan_hasil_gelar_perkara_documents', function (Blueprint $table) {
                if (!Schema::hasColumn('doc.laporan_hasil_gelar_perkara_documents', 'surat_perintah_penyelidikan_document_id')) {
                    $table->uuid('surat_perintah_penyelidikan_document_id')->nullable()->after('accident_id')->comment('Id Sprinlidik');
                    $table->foreign('surat_perintah_penyelidikan_document_id', 'fk_lhgp_docs_sp_penyelidikan_document_id')
                        ->references('id')
                        ->on('doc.surat_perintah_penyelidikan_documents')
                        ->onDelete('restrict')
                        ->onUpdate('cascade');
                }
            });

            DB::commit();
        } catch (\Exception $e) {
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
        try {
            Schema::table('doc.laporan_hasil_gelar_perkara_documents', function (Blueprint $table) {
                if (Schema::hasColumn('doc.laporan_hasil_gelar_perkara_documents', 'surat_perintah_penyelidikan_document_id')) {
                    $table->dropForeign('fk_lhgp_docs_sp_penyelidikan_document_id');
                    $table->dropColumn('surat_perintah_penyelidikan_document_id');
                }
            });

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
