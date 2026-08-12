<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocSuratPerintahPenyidikanDocumentLawsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doc.surat_perintah_penyidikan_document_laws', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('surat_perintah_penyidikan_document_id');

            $table->json('crime_type')->nullable();
            $table->json('crime_class')->nullable();
            $table->json('constitution')->nullable();
            $table->string('constitution_chapter')->nullable();
            $table->text('description')->nullable();
            
            $table->enum('flag', ['MAIN','ADDITIONAL'])->default('MAIN');

            $table->timestamps();

            $table->foreign('surat_perintah_penyidikan_document_id', 'fk_spsidik_doc_laws_sp_penyidikan_document_id')->references('id')->on('doc.surat_perintah_penyidikan_documents')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop foreign key
        Schema::table('doc.surat_perintah_penyidikan_document_laws', function (Blueprint $table) {
            $table->dropForeign('fk_spsidik_doc_laws_sp_penyidikan_document_id');
        });
        Schema::dropIfExists('doc.surat_perintah_penyidikan_document_laws');
    }
}
