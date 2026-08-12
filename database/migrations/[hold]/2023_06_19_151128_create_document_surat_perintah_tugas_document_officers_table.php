<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDocumentSuratPerintahTugasDocumentOfficersTable extends Migration
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
            Schema::create('doc.surat_perintah_tugas_document_officers', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->uuid('surat_perintah_tugas_document_id');
                
                $table->bigInteger('sort')->default(0);
                $table->string('register_number');
                $table->string('first_title')->nullable();
                $table->string('first_name');
                $table->string('last_name')->nullable();
                $table->string('last_title')->nullable();
                $table->string('rank');
                $table->string('position');
                $table->string('phone_number')->nullable();
                $table->string('email')->nullable();
                $table->text('information')->nullable();

                $table->string('headquarter_police')->nullable();
                $table->string('regional_police')->nullable();
                $table->string('resort_police')->nullable();

                $table->enum('status', ['CURRENT', 'PAST'])->default('CURRENT');
                $table->enum('role', ['MEMBER', 'LEADER', 'SIGNATORY'])->default('MEMBER');
                $table->enum('flag', ['INTERNAL', 'MOVED', 'EXTERNAL'])->default('INTERNAL');

                $table->timestamps();

                $table->foreign('surat_perintah_tugas_document_id', 'fk_spt_doc_officers_surat_perintah_tugas_document_id')->references('id')->on('doc.surat_perintah_tugas_documents')->onDelete('cascade')->onUpdate('cascade');
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
            Schema::table('doc.surat_perintah_tugas_document_officers', function (Blueprint $table) {
                $table->dropForeign('fk_spt_doc_officers_surat_perintah_tugas_document_id');
            });
            Schema::dropIfExists('doc.surat_perintah_tugas_document_officers');
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
