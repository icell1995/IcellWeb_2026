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
        Schema::table('doc.surat_permintaan_izin_penyitaan_documents', function (Blueprint $table) {
            $table->string('surat_perintah_penyitaan_file')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doc.surat_permintaan_izin_penyitaan_documents', function (Blueprint $table) {
            $table->dropColumn('surat_perintah_penyitaan_file');
        });
    }
};
