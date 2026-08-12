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
        Schema::create('pivot.tahap_1_document_suspect', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tahap_1_document_id');
            $table->uuid('suspect_id');
            $table->timestamps();

            $table->foreign('tahap_1_document_id', 'fk_thp1_suspect_doc_id')->references('id')->on('doc.tahap_1_documents')->onDelete('cascade');
            $table->foreign('suspect_id', 'fk_thp1_suspect_suspect_id')->references('id')->on('public.suspects')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pivot.tahap_1_document_suspect', function (Blueprint $table) {
            $table->dropForeign('fk_thp1_suspect_doc_id');
            $table->dropForeign('fk_thp1_suspect_suspect_id');
        });
        Schema::dropIfExists('pivot.tahap_1_document_suspect');
    }
};
