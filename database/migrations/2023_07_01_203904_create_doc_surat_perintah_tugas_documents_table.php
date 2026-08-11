<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocSuratPerintahTugasDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doc.surat_perintah_tugas_documents', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Id Surat'); 
            $table->uuid('accident_id')->nullable()->comment('Id Kecelakaan'); 
    
            $table->uuid('related_id')->nullable()->comment('Id Surat terkait');
            $table->string('related_type')->nullable()->comment('Tipe Surat terkait');
            $table->json('related_property')->nullable()->comment('Properti Surat terkait');

            $table->string('document_number')->comment('No Surat Springas');
            $table->dateTime('document_date')->comment('Tanggal Springas');
            $table->dateTime('start_date')->comment('Tanggal Mulai Springas');
            $table->dateTime('end_date')->comment('Tanggal Berakhir Springas')->nullable();
    
            $table->string('case_classification')->nullable()->after('letter_date');

            $table->longText('task_description')->nullable();
    
            $table->boolean('is_active')->default(true)->after('id');
    
            $table->string('created_by')->comment('Diisi data user');
            $table->string('updated_by')->nullable()->comment('Diisi data user');
            $table->string('deleted_by')->nullable()->after('deleted_at')->comment('Diisi data user');
    
            $table->timestamps();
            $table->softDeletes()->after('updated_at');
            $table->dateTime('last_synced_at')->nullable()->comment('Waktu terakhir ditarik dengan EMP');
    
            $table->foreign('accident_id', 'fk_sptugas_docs_accident_id')->references('id')->on('public.accidents')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Drop Foreign Key
        Schema::table('doc.surat_perintah_tugas_documents', function (Blueprint $table) {
            $table->dropForeign('fk_sptugas_docs_accident_id');
        });

        //Drop Table
        Schema::dropIfExists('doc.surat_perintah_tugas_documents');
    }
}
