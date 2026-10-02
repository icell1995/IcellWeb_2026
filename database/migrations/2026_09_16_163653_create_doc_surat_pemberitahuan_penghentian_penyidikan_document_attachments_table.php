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
        Schema::create('doc.surat_pemberitahuan_penghentian_penyidikan_document_attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('surat_pemberitahuan_penghentian_penyidikan_document_id')->nullable();
            
            $table->string('name')->nullable();
            $table->string('url')->nullable();
            $table->string('status_id')->nullable();
            $table->string('remark')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('surat_pemberitahuan_penghentian_penyidikan_document_id', 'fk_sp3_docs_att_doc_id')
                ->references('id')
                ->on('doc.surat_pemberitahuan_penghentian_penyidikan_documents')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doc.surat_pemberitahuan_penghentian_penyidikan_document_attachments', function (Blueprint $table) {
            $table->dropForeign('fk_sp3_docs_att_doc_id');
        });
        Schema::dropIfExists('doc.surat_pemberitahuan_penghentian_penyidikan_document_attachments');
    }
};
