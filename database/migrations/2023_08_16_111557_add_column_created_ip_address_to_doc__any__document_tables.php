<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCreatedIpAddressToDocAnyDocumentTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('doc.surat_perintah_penyelidikan_documents', function (Blueprint $table) {
            $table->json('ip_addresses')->nullable();
        });

        Schema::table('doc.surat_perintah_penyidikan_documents', function (Blueprint $table) {
            $table->json('ip_addresses')->nullable();
        });

        Schema::table('doc.surat_perintah_tugas_documents', function (Blueprint $table) {
            $table->json('ip_addresses')->nullable();
        });

        Schema::table('doc.laporan_hasil_gelar_perkara_documents', function (Blueprint $table) {
            $table->json('ip_addresses')->nullable();
        });

        Schema::table('doc.surat_ketetapan_tentang_penetapan_tersangka_documents', function (Blueprint $table) {
            $table->json('ip_addresses')->nullable();
        });

        Schema::table('doc.surat_pemberitahuan_dimulainya_penyidikan_documents', function (Blueprint $table) {
            $table->json('ip_addresses')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('doc.surat_perintah_penyelidikan_documents', function (Blueprint $table) {
            $table->dropColumn('ip_addresses');
        });

        Schema::table('doc.surat_perintah_penyidikan_documents', function (Blueprint $table) {
            $table->dropColumn('ip_addresses');
        });

        Schema::table('doc.surat_perintah_tugas_documents', function (Blueprint $table) {
            $table->dropColumn('ip_addresses');
        });

        Schema::table('doc.laporan_hasil_gelar_perkara_documents', function (Blueprint $table) {
            $table->dropColumn('ip_addresses');
        });

        Schema::table('doc.surat_ketetapan_tentang_penetapan_tersangka_documents', function (Blueprint $table) {
            $table->dropColumn('ip_addresses');
        });

        Schema::table('doc.surat_pemberitahuan_dimulainya_penyidikan_documents', function (Blueprint $table) {
            $table->dropColumn('ip_addresses');
        });
    }
}
