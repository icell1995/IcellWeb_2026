<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doc.surat_pemberitahuan_dimulainya_penyidikan_pusiknas_document_attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('surat_pemberitahuan_dimulainya_penyidikan_pusiknas_document_id');

            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_size')->nullable();
            $table->string('file_type')->nullable();
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('surat_pemberitahuan_dimulainya_penyidikan_pusiknas_document_id', 'fk_spdp_pusiknas_attach_doc_id')
                ->references('id')->on('doc.surat_pemberitahuan_dimulainya_penyidikan_pusiknas_documents')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('doc.surat_pemberitahuan_dimulainya_penyidikan_pusiknas_document_attachments', function (Blueprint $table) {
            $table->dropForeign('fk_spdp_pusiknas_attach_doc_id');
        });
        Schema::dropIfExists('doc.surat_pemberitahuan_dimulainya_penyidikan_pusiknas_document_attachments');
    }
};
