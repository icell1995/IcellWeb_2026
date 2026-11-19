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
        Schema::table('doc.surat_laporan_persetujuan_penyitaan_document_persons', function (Blueprint $table) {
            $table->boolean('is_seized_at_work_unit')->default(true);
            $table->text('seized_location')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doc.surat_laporan_persetujuan_penyitaan_document_persons', function (Blueprint $table) {
            $table->dropColumn(['is_seized_at_work_unit', 'seized_location']);
        });
    }
};
