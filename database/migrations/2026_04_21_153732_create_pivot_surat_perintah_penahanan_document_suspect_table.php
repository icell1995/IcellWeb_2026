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
        //
        Schema::create('pivot.surat_perintah_penahanan_document_suspect', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->uuid('surat_perintah_penahanan_document_id');
            $table->uuid('suspect_id');

            $table->timestamps();

            $table->foreign('surat_perintah_penahanan_document_id', 'fk_sprin_penahanan_doc_suspect_sprin_penahanan_document_id')->references('id')->on('doc.surat_perintah_penahanan_documents')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('suspect_id', 'fk_sprin_penahanan_doc_suspect_suspect_id')->references('id')->on('public.suspects')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('pivot.surat_perintah_penahanan_document_suspect', function (Blueprint $table) {
            $table->dropForeign('fk_sprin_penahanan_doc_suspect_sprin_penahanan_document_id');
            $table->dropForeign('fk_sprin_penahanan_doc_suspect_suspect_id');
        });
        Schema::dropIfExists('pivot.surat_perintah_penahanan_document_suspect');
    }
};
