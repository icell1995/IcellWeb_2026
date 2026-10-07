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
            if (!Schema::hasColumn('doc.surat_laporan_persetujuan_penyitaan_document_persons', 'bap_file')) {
                $table->string('bap_file')->nullable()->after('bap_date');
            }
            if (Schema::hasColumn('doc.surat_laporan_persetujuan_penyitaan_document_persons', 'bap_number')) {
                $table->dropColumn('bap_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doc.surat_laporan_persetujuan_penyitaan_document_persons', function (Blueprint $table) {
            if (Schema::hasColumn('doc.surat_laporan_persetujuan_penyitaan_document_persons', 'bap_file')) {
                $table->dropColumn('bap_file');
            }
            if (!Schema::hasColumn('doc.surat_laporan_persetujuan_penyitaan_document_persons', 'bap_number')) {
                $table->string('bap_number')->nullable()->after('reported_person_id');
            }
        });
    }
};
