<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDocLaporanHasilGelarPerkaraDocumentsTable extends Migration
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
            Schema::create('doc.laporan_hasil_gelar_perkara_documents', function (Blueprint $table) {
                $table->uuid('id')->primary()->comment('Id SDocument'); 
                $table->uuid('accident_id')->nullable()->comment('Id Kecelakaan'); 
                $table->uuid('surat_perintah_penyidikan_document_id')->nullable()->comment('Id Sprindik'); 

                $table->date('document_date');
                $table->enum('document_type', ['BIASA', 'KHUSUS']);
                $table->string('case_degree_type_id')->nullable();

                $table->string('case_degree_invite_reference')->nullable();
                $table->date('date');
                $table->string('time');
                $table->string('timezone_id');
                $table->string('place')->nullable();
                $table->string('case_degree_leader')->nullable();
                $table->bigInteger('attendees')->default(0);
                
                $table->longText('discussion')->nullable();
                $table->longText('conclusion')->nullable();
                $table->longText('closing')->nullable();

                $table->boolean('is_active')->default(true)->after('id');

                $table->string('created_by')->comment('Diisi data user');
                $table->string('updated_by')->nullable()->comment('Diisi data user');
                $table->string('deleted_by')->nullable()->after('deleted_at')->comment('Diisi data user');
            
                $table->timestamps();
                $table->softDeletes()->after('updated_at');
                $table->dateTime('last_synced_at')->nullable()->comment('Waktu terakhir ditarik dengan EMP');
                
                $table->foreign('accident_id', 'fk_lhgp_docs_accident_id')->references('id')->on('public.accidents')->onDelete('set null')->onUpdate('cascade');
                $table->foreign('surat_perintah_penyidikan_document_id', 'fk_lhgp_docs_sp_penyidikan_document_id')->references('id')->on('doc.surat_perintah_penyidikan_documents')->onDelete('restrict')->onUpdate('cascade');
                $table->foreign('case_degree_type_id', 'fk_lhgp_docs_case_degree_type_id')->references('id')->on('lib.case_degree_types')->onDelete('restrict')->onUpdate('cascade');
                $table->foreign('timezone_id', 'fk_lhgp_docs_timezone_id')->references('id')->on('lib.timezones')->onDelete('restrict')->onUpdate('cascade');
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
        try{
            // Drop foreign keys
            Schema::table('doc.laporan_hasil_gelar_perkara_documents', function (Blueprint $table) {
                $table->dropForeign('fk_lhgp_docs_accident_id');
                $table->dropForeign('fk_lhgp_docs_sp_penyidikan_document_id');
                $table->dropForeign('fk_lhgp_docs_case_degree_type_id');
                $table->dropForeign('fk_lhgp_docs_timezone_id');
            });
            Schema::dropIfExists('doc.laporan_hasil_gelar_perkara_documents');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
