<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doc.surat_pemberitahuan_dimulainya_penyidikan_pusiknas_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id')->nullable();
            
            $table->uuid('surat_perintah_penyidikan_document_id')->nullable();
            $table->uuid('surat_perintah_tugas_document_id')->nullable();
            
            $table->string('document_number')->nullable();
            $table->date('document_date')->nullable();
            $table->string('document_classification_id')->nullable();
            $table->string('prosecutor_id')->nullable();
            $table->string('court_id')->nullable();
            
            $table->boolean('is_suspect_exists')->default(false);
            $table->text('description')->nullable();
            
            $table->bigInteger('appendix')->default(0);
            $table->json('carbon_copies')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->boolean('is_legacy')->default(false);
            $table->string('status_id')->nullable();
            $table->string('document_category_id')->nullable();
            $table->json('messages')->nullable();
            $table->json('timestamps_log')->nullable();
            $table->json('ip_addresses')->nullable();
            
            $table->dateTime('released_at')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('rejected_at')->nullable();
            $table->dateTime('last_synced_at')->nullable();
            
            $table->string('created_by_user_id')->nullable();
            $table->string('updated_by_user_id')->nullable();
            $table->string('deleted_by_user_id')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('accident_id', 'fk_spdp_pusiknas_docs_accident_id')->references('id')->on('public.accidents')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('surat_perintah_penyidikan_document_id', 'fk_spdp_pusiknas_docs_spsidik_id')->references('id')->on('doc.surat_perintah_penyidikan_documents')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('surat_perintah_tugas_document_id', 'fk_spdp_pusiknas_docs_sptugas_id')->references('id')->on('doc.surat_perintah_tugas_documents')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('document_classification_id', 'fk_spdp_pusiknas_docs_doc_class_id')->references('id')->on('lib.document_classifications')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('prosecutor_id', 'fk_spdp_pusiknas_docs_prosecutor_id')->references('id')->on('lib.prosecutors')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('court_id', 'fk_spdp_pusiknas_docs_court_id')->references('id')->on('lib.courts')->onDelete('set null')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('doc.surat_pemberitahuan_dimulainya_penyidikan_pusiknas_documents', function (Blueprint $table) {
            $table->dropForeign('fk_spdp_pusiknas_docs_accident_id');
            $table->dropForeign('fk_spdp_pusiknas_docs_spsidik_id');
            $table->dropForeign('fk_spdp_pusiknas_docs_sptugas_id');
            $table->dropForeign('fk_spdp_pusiknas_docs_doc_class_id');
            $table->dropForeign('fk_spdp_pusiknas_docs_prosecutor_id');
            $table->dropForeign('fk_spdp_pusiknas_docs_court_id');
        });
        Schema::dropIfExists('doc.surat_pemberitahuan_dimulainya_penyidikan_pusiknas_documents');
    }
};
