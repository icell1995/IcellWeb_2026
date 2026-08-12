<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDocumentSuratPerintahTugasDocumentsTable extends Migration
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
            Schema::create('doc.surat_perintah_tugas_documents', function (Blueprint $table) {
                $table->uuid('id')->primary()->comment('Id Surat');
                $table->uuid('accident_id')->nullable()->comment('Id Kecelakaan'); 
                $table->json('investigation')->nullable()->comment('Penyelidikan atau Penyidikan'); 
                
                $table->string('letter_number')->comment('No Surat Perintah Tugas');
                $table->dateTime('letter_date')->comment('Tanggal Surat Perintah Tugas')->default(now());

                $table->dateTime('start_date')->comment('Tanggal Mulai Surat Perintah Tugas');
                $table->dateTime('end_date')->nullable()->comment('Tanggal Berakhir Surat Perintah Tugas');
                $table->longText('task_description')->nullable()->comment('Deskripsi Tugas');

                $table->json('created_by')->comment('Diisi data user pembuat');
                $table->json('updated_by')->nullable()->comment('Diisi data user pembuat');
                
                $table->timestamps();
                $table->softDeletes();
                $table->dateTime('last_synced_at')->nullable()->comment('tanggal terakhir sync ke EMP');

                $table->foreign('accident_id', 'fk_spt_docs_accident_id')->references('id')->on('public.accidents')->onDelete('set null')->onUpdate('cascade');
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
            // Drop foreign key
            Schema::table('doc.surat_perintah_tugas_documents', function (Blueprint $table) {
                $table->dropForeign('fk_spt_docs_accident_id');
            });
            Schema::dropIfExists('doc.surat_perintah_tugas_documents');
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
