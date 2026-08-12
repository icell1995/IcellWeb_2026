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
        Schema::table('doc.surat_perintah_penyelidikan_documents', function (Blueprint $table) {
            $table->datetime('released_at')->nullable();
        });

        Schema::table('doc.surat_perintah_penyidikan_documents', function (Blueprint $table) {
            $table->datetime('released_at')->nullable();
        });

        Schema::table('doc.surat_perintah_tugas_documents', function (Blueprint $table) {
            $table->datetime('released_at')->nullable();
        });

        Schema::table('doc.laporan_hasil_gelar_perkara_documents', function (Blueprint $table) {
            $table->datetime('released_at')->nullable();
        });

        Schema::table('doc.surat_ketetapan_tentang_penetapan_tersangka_documents', function (Blueprint $table) {
            $table->datetime('released_at')->nullable();
        });

        Schema::table('doc.surat_pemberitahuan_dimulainya_penyidikan_documents', function (Blueprint $table) {
            $table->datetime('released_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doc.surat_perintah_penyelidikan_documents', function (Blueprint $table) {
            $table->dropColumn('released_at');
        });

        Schema::table('doc.surat_perintah_penyidikan_documents', function (Blueprint $table) {
            $table->dropColumn('released_at');
        });

        Schema::table('doc.surat_perintah_tugas_documents', function (Blueprint $table) {
            $table->dropColumn('released_at');
        });

        Schema::table('doc.laporan_hasil_gelar_perkara_documents', function (Blueprint $table) {
            $table->dropColumn('released_at');
        });

        Schema::table('doc.surat_ketetapan_tentang_penetapan_tersangka_documents', function (Blueprint $table) {
            $table->dropColumn('released_at');
        });

        Schema::table('doc.surat_pemberitahuan_dimulainya_penyidikan_documents', function (Blueprint $table) {
            $table->dropColumn('released_at');
        });
    }
};
