<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doc.surat_pemberitahuan_penghentian_penyidikan_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id')->nullable();
            
            $table->string('document_number')->nullable();
            $table->date('document_date')->nullable();
            
            $table->string('document_classification_id')->nullable();
            
            // Custom fields for SP3
            $table->string('no_spdp')->nullable();
            $table->string('no_sk_penghentian')->nullable();
            $table->date('tanggal_sk_penghentian')->nullable();
            $table->string('no_sp_penghentian')->nullable();
            $table->date('tanggal_sp_penghentian')->nullable();
            $table->json('kode_alasan')->nullable();
            $table->json('suspect_ids')->nullable();
            
            $table->bigInteger('appendix')->default(0);
            $table->json('carbon_copies')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->string('status_id')->nullable();
            $table->string('document_category_id')->nullable();
            $table->json('messages')->nullable();
            $table->json('ip_addresses')->nullable();
            
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->unsignedBigInteger('updated_by_user_id')->nullable();
            $table->unsignedBigInteger('deleted_by_user_id')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            $table->dateTime('last_synced_at')->nullable();
            
            $table->foreign('accident_id', 'fk_sp3_docs_accident_id')->references('id')->on('public.accidents')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('document_classification_id', 'fk_sp3_docs_document_classification_id')->references('id')->on('lib.document_classifications')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doc.surat_pemberitahuan_penghentian_penyidikan_documents', function (Blueprint $table) {
            $table->dropForeign('fk_sp3_docs_accident_id');
            $table->dropForeign('fk_sp3_docs_document_classification_id');
        });
        Schema::dropIfExists('doc.surat_pemberitahuan_penghentian_penyidikan_documents');
    }
};
