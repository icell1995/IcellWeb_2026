<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDocSuratKetetapanTentangPenetapanTersangkaDocumentOfficersTable extends Migration
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
            Schema::create('doc.surat_ketetapan_tentang_penetapan_tersangka_document_officers', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->uuid('surat_ketetapan_tentang_penetapan_tersangka_document_id');
                
                $table->bigInteger('sort')->default(0);
                $table->string('register_number');
                $table->string('first_title')->nullable();
                $table->string('first_name');
                $table->string('last_name')->nullable();
                $table->string('last_title')->nullable();
                
                $table->json('rank')->nullable();
                $table->json('position')->nullable();
                $table->json('role')->nullable();
                
                $table->string('phone_number')->nullable();
                $table->string('email')->nullable();
                $table->text('information')->nullable();
                
                $table->string('police_id')->nullable();
                
                $table->enum('status', ['PRESENT', 'PAST', 'EXTERNAL'])->default('PRESENT')->nullable(true);
                $table->enum('class', ['MEMBER', 'LEADER', 'SIGNATORY'])->default('MEMBER')->nullable(true);
                $table->enum('flag', ['INTERNAL', 'MOVED', 'EXTERNAL'])->default('INTERNAL')->nullable(true);
                $table->enum('insert_method', ['MANUAL', 'IMPORT'])->default('IMPORT')->nullable(true);
                
                $table->timestamps();
                
                $table->foreign('surat_ketetapan_tentang_penetapan_tersangka_document_id', 'fk_sket_tp_tersangka_doc_officers_sket_tp_tersangka_document_id')->references('id')->on('doc.surat_ketetapan_tentang_penetapan_tersangka_documents')->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('police_id', 'fk_sket_tp_tersangka_doc_officers_police_id')->references('id')->on('lib.polices')->onDelete('restrict')->onUpdate('cascade');
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
        // Drop Foreign Key
        Schema::table('doc.surat_ketetapan_tentang_penetapan_tersangka_document_officers', function (Blueprint $table) {
            $table->dropForeign('fk_sket_tp_tersangka_doc_officers_sket_tp_tersangka_document_id');
            $table->dropForeign('fk_sket_tp_tersangka_doc_officers_police_id');
        });
        Schema::dropIfExists('doc.surat_ketetapan_tentang_penetapan_tersangka_document_officers');
    }
}
