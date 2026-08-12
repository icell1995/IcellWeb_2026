<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocSuratPemberitahuanDimulainyaPenyidikanDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doc.surat_pemberitahuan_dimulainya_penyidikan_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id')->nullable();
            $table->uuid('surat_perintah_penyidikan_document_id')->nullable();
            $table->uuid('surat_perintah_tugas_document_id')->nullable();

            $table->string('document_number');
            $table->date('document_date');
            
            $table->string('document_classification_id')->nullable();
            $table->boolean('is_suspect_exists')->nullable();

            $table->string('prosecutor_id')->nullable();
            $table->string('court_id')->nullable();
            
            $table->bigInteger('appendix')->default(0);

            $table->json('carbon_copies')->nullable();

            $table->boolean('is_active')->default(true);

            $table->string('created_by')->comment('Diisi data user');
            $table->string('updated_by')->nullable()->comment('Diisi data user');
            $table->string('deleted_by')->nullable()->after('deleted_at')->comment('Diisi data user');
        
            $table->timestamps();
            $table->softDeletes()->after('updated_at');
            $table->dateTime('last_synced_at')->nullable()->comment('Waktu terakhir ditarik dengan EMP');

            $table->foreign('accident_id', 'fk_spdp_docs_accident_id')->references('id')->on('public.accidents')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('surat_perintah_penyidikan_document_id', 'fk_spdp_docs_sp_penyidikan_document_id')->references('id')->on('doc.surat_perintah_penyidikan_documents')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('surat_perintah_tugas_document_id', 'fk_spdp_docs_sp_tugas_document_id')->references('id')->on('doc.surat_perintah_tugas_documents')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('prosecutor_id', 'fk_spdp_docs_prosecutor_id')->references('id')->on('lib.prosecutors')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('court_id', 'fk_spdp_docs_court_id')->references('id')->on('lib.courts')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('document_classification_id', 'fk_spdp_docs_document_classification_id')->references('id')->on('lib.document_classifications')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop Foreign Key
        Schema::table('doc.surat_pemberitahuan_dimulainya_penyidikan_documents', function (Blueprint $table) {
            $table->dropForeign('fk_spdp_docs_accident_id');
            $table->dropForeign('fk_spdp_docs_sp_penyidikan_document_id');
            $table->dropForeign('fk_spdp_docs_sp_tugas_document_id');
            $table->dropForeign('fk_spdp_docs_prosecutor_id');
            $table->dropForeign('fk_spdp_docs_court_id');
            $table->dropForeign('fk_spdp_docs_document_classification_id');
        });
        Schema::dropIfExists('doc.surat_pemberitahuan_dimulainya_penyidikan_documents');
    }
}
