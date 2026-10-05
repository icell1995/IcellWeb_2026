<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pivot.surat_pemberitahuan_dimulainya_penyidikan_pusiknas_document_suspect', function (Blueprint $table) {
            $table->uuid('surat_pemberitahuan_dimulainya_penyidikan_pusiknas_document_id');
            $table->uuid('suspect_id');

            $table->timestamps();

            $table->foreign('surat_pemberitahuan_dimulainya_penyidikan_pusiknas_document_id', 'fk_spdp_pusiknas_susp_doc_id')
                ->references('id')->on('doc.surat_pemberitahuan_dimulainya_penyidikan_pusiknas_documents')
                ->onDelete('cascade')->onUpdate('cascade');
                
            $table->foreign('suspect_id', 'fk_spdp_pusiknas_susp_suspect_id')
                ->references('id')->on('public.suspects')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->primary(['surat_pemberitahuan_dimulainya_penyidikan_pusiknas_document_id', 'suspect_id'], 'pk_spdp_pusiknas_suspect');
        });
    }

    public function down(): void
    {
        Schema::table('pivot.surat_pemberitahuan_dimulainya_penyidikan_pusiknas_document_suspect', function (Blueprint $table) {
            $table->dropForeign('fk_spdp_pusiknas_susp_doc_id');
            $table->dropForeign('fk_spdp_pusiknas_susp_suspect_id');
        });
        Schema::dropIfExists('pivot.surat_pemberitahuan_dimulainya_penyidikan_pusiknas_document_suspect');
    }
};
