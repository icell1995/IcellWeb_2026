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
        Schema::create('pivot.surat_ketetapan_penghentian_penyidikan_document_suspect', function (Blueprint $table) {
            $table->uuid('surat_ketetapan_penghentian_penyidikan_document_id')->index('sp3_doc_id_idx');
            $table->uuid('suspect_id')->index('sp3_suspect_id_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pivot.surat_ketetapan_penghentian_penyidikan_document_suspect');
    }
};
