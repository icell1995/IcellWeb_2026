<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDocSuratPerintahPenyidikanDocumentsTable extends Migration
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
            Schema::create('doc.surat_perintah_penyidikan_documents', function (Blueprint $table) {
                $table->uuid('id')->primary()->comment('Id Surat'); 
                $table->uuid('accident_id')->nullable()->comment('Id Kecelakaan'); 
        
                $table->string('document_number')->comment('No Surat Sprindik');
                $table->dateTime('document_date')->comment('Tanggal Sprindik');
                $table->dateTime('start_date')->comment('Tanggal Mulai Sprindik');
                $table->dateTime('end_date')->comment('Tanggal Berakhir Sprindik');
        
                $table->string('case_classification')->nullable()->after('document_date');
        
                $table->boolean('is_active')->default(true)->after('id');
        
                $table->string('created_by')->comment('Diisi data user');
                $table->string('updated_by')->nullable()->comment('Diisi data user');
                $table->string('deleted_by')->nullable()->after('deleted_at')->comment('Diisi data user');
        
                $table->timestamps();
                $table->softDeletes()->after('updated_at');
                $table->dateTime('last_synced_at')->nullable()->comment('Waktu terakhir ditarik dengan EMP');
        
                $table->foreign('accident_id', 'fk_spsidik_docs_accident_id')->references('id')->on('public.accidents')->onDelete('set null')->onUpdate('cascade');
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
            //Drop Foreign Key
            Schema::table('doc.surat_perintah_penyidikan_documents', function (Blueprint $table) {
                $table->dropForeign('fk_spsidik_docs_accident_id');
            });

            Schema::dropIfExists('doc.surat_perintah_penyidikan_documents');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
