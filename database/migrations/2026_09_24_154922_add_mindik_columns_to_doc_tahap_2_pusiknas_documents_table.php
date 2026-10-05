<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom Mindik ke tabel utama Tahap 2
        Schema::table('doc.tahap_2_documents', function (Blueprint $table) {
            // Referensi dokumen rujukan
            $table->uuid('surat_perintah_penyidikan_id')->nullable()->after('no_spdp')->comment('FK ke Surat Perintah Penyidikan');
            $table->uuid('surat_pemberitahuan_dimulainya_penyidikan_id')->nullable()->after('surat_perintah_penyidikan_id')->comment('FK ke SPDP');
            $table->uuid('surat_ketetapan_penetapan_tersangka_id')->nullable()->after('surat_pemberitahuan_dimulainya_penyidikan_id')->comment('FK ke S.Tap Tersangka');

            // Berkas Perkara
            $table->string('berkas_perkara_number')->nullable()->after('surat_ketetapan_penetapan_tersangka_id');
            $table->date('berkas_perkara_date')->nullable()->after('berkas_perkara_number');
            $table->integer('berkas_perkara_rangkap')->default(1)->after('berkas_perkara_date');

            // Klasifikasi & Lampiran
            $table->string('klasifikasi')->nullable()->after('berkas_perkara_rangkap')->comment('BIASA / RAHASIA / dsb');
            $table->string('lampiran')->nullable()->after('klasifikasi');

            // Pasal
            $table->text('pasal_disangkakan')->nullable()->after('lampiran');

            // Penahanan
            $table->string('penahanan_status')->nullable()->after('pasal_disangkakan')->comment('DITAHAN / DITANGGUHKAN / TIDAK_DITAHAN');
            $table->string('penahanan_rutan')->nullable()->after('penahanan_status');
            $table->string('penahanan_cabang')->nullable()->after('penahanan_rutan');
            $table->date('penahanan_start_date')->nullable()->after('penahanan_cabang');
            $table->date('penahanan_end_date')->nullable()->after('penahanan_start_date');
            $table->string('surat_perintah_penahanan_number')->nullable()->after('penahanan_end_date');
            $table->date('surat_perintah_penahanan_date')->nullable()->after('surat_perintah_penahanan_number');
            $table->string('surat_perpanjangan_penahanan_number')->nullable()->after('surat_perintah_penahanan_date');
            $table->date('surat_perpanjangan_penahanan_date')->nullable()->after('surat_perpanjangan_penahanan_number');
            $table->string('surat_perpanjangan_penahanan_court_number')->nullable()->after('surat_perpanjangan_penahanan_date');
            $table->date('surat_perpanjangan_penahanan_court_date')->nullable()->after('surat_perpanjangan_penahanan_court_number');
            $table->string('surat_penangguhan_penahanan_number')->nullable()->after('surat_perpanjangan_penahanan_court_date');
            $table->date('surat_penangguhan_penahanan_date')->nullable()->after('surat_penangguhan_penahanan_number');

            // Barang Bukti
            $table->string('barang_bukti_storage')->nullable()->after('surat_penangguhan_penahanan_date')->comment('Tempat penyimpanan BB');
            $table->json('barang_bukti')->nullable()->after('barang_bukti_storage')->comment('Daftar BB dari form');
            $table->integer('jumlah_bb')->default(0)->after('barang_bukti');

            // Investigator
            $table->string('investigator_pangkat_nama')->nullable()->after('jumlah_bb');
            $table->string('investigator_hp')->nullable()->after('investigator_pangkat_nama');

            // Tembusan (namai sama dengan Tahap1)
            $table->json('tembusan')->nullable()->after('investigator_hp')->comment('Tembusan surat');
        });

        // 2. Buat tabel attachment Tahap 2
        Schema::create('doc.tahap_2_document_attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tahap_2_document_id');

            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->bigInteger('file_size')->nullable()->comment('bytes');
            $table->string('file_type')->nullable()->comment('mime type');
            $table->string('description')->nullable();

            $table->string('created_by_user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tahap_2_document_id', 'fk_bpt2_attach_doc_id')
                ->references('id')->on('doc.tahap_2_documents')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        // Drop tabel attachment
        Schema::table('doc.tahap_2_document_attachments', function (Blueprint $table) {
            $table->dropForeign('fk_bpt2_attach_doc_id');
        });
        Schema::dropIfExists('doc.tahap_2_document_attachments');

        // Hapus kolom yang ditambahkan
        Schema::table('doc.tahap_2_documents', function (Blueprint $table) {
            $table->dropColumn([
                'surat_perintah_penyidikan_id',
                'surat_pemberitahuan_dimulainya_penyidikan_id',
                'surat_ketetapan_penetapan_tersangka_id',
                'berkas_perkara_number',
                'berkas_perkara_date',
                'berkas_perkara_rangkap',
                'klasifikasi',
                'lampiran',
                'pasal_disangkakan',
                'penahanan_status',
                'penahanan_rutan',
                'penahanan_cabang',
                'penahanan_start_date',
                'penahanan_end_date',
                'surat_perintah_penahanan_number',
                'surat_perintah_penahanan_date',
                'surat_perpanjangan_penahanan_number',
                'surat_perpanjangan_penahanan_date',
                'surat_perpanjangan_penahanan_court_number',
                'surat_perpanjangan_penahanan_court_date',
                'surat_penangguhan_penahanan_number',
                'surat_penangguhan_penahanan_date',
                'barang_bukti_storage',
                'barang_bukti',
                'jumlah_bb',
                'investigator_pangkat_nama',
                'investigator_hp',
                'tembusan',
            ]);
        });
    }
};
