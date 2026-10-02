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
        Schema::table('doc.tahap_1_documents', function (Blueprint $table) {
            // Field Bisnis Tambahan
            $table->string('klasifikasi')->nullable()->comment('Klasifikasi');
            $table->string('lampiran')->nullable()->comment('Lampiran');

            // Relasi Referensi Dokumen Lain
            $table->uuid('surat_perintah_penyidikan_id')->nullable()->comment('Ref SP_SIDIK');
            $table->uuid('surat_pemberitahuan_dimulainya_penyidikan_id')->nullable()->comment('Ref SPDP');
            $table->uuid('surat_ketetapan_penetapan_tersangka_id')->nullable()->comment('Ref Penetapan Tersangka');
            
            // Detail Berkas
            $table->string('berkas_perkara_number')->nullable()->comment('No Berkas Perkara');
            $table->date('berkas_perkara_date')->nullable()->comment('Tanggal Berkas');
            $table->integer('berkas_perkara_rangkap')->nullable()->default(1);
            $table->text('pasal_disangkakan')->nullable()->comment('Pasal yang dipersangkakan');
            
            // Data Penahanan
            $table->string('penahanan_rutan')->nullable()->comment('Nama RUTAN');
            $table->string('penahanan_cabang')->nullable()->comment('Cabang RUTAN');
            $table->date('penahanan_start_date')->nullable();
            $table->date('penahanan_end_date')->nullable();
            
            $table->string('surat_perintah_penahanan_number')->nullable();
            $table->date('surat_perintah_penahanan_date')->nullable();
            
            $table->string('surat_perpanjangan_penahanan_number')->nullable();
            $table->date('surat_perpanjangan_penahanan_date')->nullable();
            $table->string('surat_perpanjangan_penahanan_court_number')->nullable()->comment('Nomor perpanjangan dari Pengadilan');
            $table->date('surat_perpanjangan_penahanan_court_date')->nullable();
            
            $table->string('penahanan_status', 50)->default('TIDAK_DITAHAN')->comment('Status: DITAHAN, DITANGGUHKAN, TIDAK_DITAHAN');
            $table->string('surat_penangguhan_penahanan_number')->nullable();
            $table->date('surat_penangguhan_penahanan_date')->nullable();

            // Barang Bukti
            $table->string('barang_bukti_storage')->nullable()->comment('Tempat penyimpanan BB');
            $table->json('barang_bukti')->nullable()->comment('Array bukti JSON');
            $table->integer('jumlah_bb')->nullable();
            
            // Investigator
            $table->string('investigator_pangkat_nama')->nullable();
            $table->string('investigator_hp')->nullable();

            // Tembusan
            $table->json('tembusan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doc.tahap_1_documents', function (Blueprint $table) {
            $table->dropColumn([
                'klasifikasi', 'lampiran', 'surat_perintah_penyidikan_id', 'surat_pemberitahuan_dimulainya_penyidikan_id',
                'surat_ketetapan_penetapan_tersangka_id', 'berkas_perkara_number', 'berkas_perkara_date', 'berkas_perkara_rangkap',
                'pasal_disangkakan', 'penahanan_rutan', 'penahanan_cabang', 'penahanan_start_date', 'penahanan_end_date',
                'surat_perintah_penahanan_number', 'surat_perintah_penahanan_date', 'surat_perpanjangan_penahanan_number',
                'surat_perpanjangan_penahanan_date', 'surat_perpanjangan_penahanan_court_number', 'surat_perpanjangan_penahanan_court_date',
                'penahanan_status', 'surat_penangguhan_penahanan_number', 'surat_penangguhan_penahanan_date', 'barang_bukti_storage',
                'barang_bukti', 'jumlah_bb', 'investigator_pangkat_nama', 'investigator_hp', 'tembusan'
            ]);
        });
    }
};
