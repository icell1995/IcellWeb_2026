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
        Schema::table('doc.surat_perintah_penyelidikan_document_attachments', function (Blueprint $table) {
            $table->string('flag')->nullable();
        });

        Schema::table('doc.surat_perintah_penyidikan_document_attachments', function (Blueprint $table) {
            $table->string('flag')->nullable();
        });

        Schema::table('doc.surat_perintah_tugas_document_attachments', function (Blueprint $table) {
            $table->string('flag')->nullable();
        });

        Schema::table('doc.laporan_hasil_gelar_perkara_document_attachments', function (Blueprint $table) {
            $table->string('flag')->nullable();
        });

        Schema::table('doc.surat_ketetapan_tentang_penetapan_tersangka_document_attachments', function (Blueprint $table) {
            $table->string('flag')->nullable();
        });

        Schema::table('doc.surat_pemberitahuan_dimulainya_penyidikan_document_attachments', function (Blueprint $table) {
            $table->string('flag')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doc.surat_perintah_penyelidikan_document_attachments', function (Blueprint $table) {
            $table->dropColumn('flag');
        });

        Schema::table('doc.surat_perintah_penyidikan_document_attachments', function (Blueprint $table) {
            $table->dropColumn('flag');
        });

        Schema::table('doc.surat_perintah_tugas_document_attachments', function (Blueprint $table) {
            $table->dropColumn('flag');
        });

        Schema::table('doc.laporan_hasil_gelar_perkara_document_attachments', function (Blueprint $table) {
            $table->dropColumn('flag');
        });

        Schema::table('doc.surat_ketetapan_tentang_penetapan_tersangka_document_attachments', function (Blueprint $table) {
            $table->dropColumn('flag');
        });

        Schema::table('doc.surat_pemberitahuan_dimulainya_penyidikan_document_attachments', function (Blueprint $table) {
            $table->dropColumn('flag');
        });
    }
};
