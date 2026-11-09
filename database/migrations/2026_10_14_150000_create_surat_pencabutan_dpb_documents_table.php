<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doc.surat_pencabutan_dpb_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id')->index();
            $table->string('document_category_id')->nullable()->default('0715');
            $table->string('status_id')->nullable()->default('2');

            // Header & Identitas Surat Pencabutan DPB
            $table->string('nomor_surat')->nullable();
            $table->date('tanggal_surat')->nullable();
            $table->string('dikeluarkan_di')->nullable();

            // Rujukan LP & Satker
            $table->string('nomor_lp')->nullable();
            $table->date('tanggal_lp')->nullable();
            $table->string('satker_penyidik')->nullable();

            // Dasar Pencabutan (Berdasarkan Poin 1 & 2)
            $table->string('nama_instansi_dasar')->nullable();
            $table->string('nomor_surat_dasar')->nullable();
            $table->date('tanggal_surat_dasar')->nullable();
            $table->string('nomor_dpb')->nullable();
            $table->date('tanggal_dpb')->nullable();

            // 12 Butir Keterangan Barang Bukti (Spesimen S-24.6.2)
            $table->string('nama_barang')->nullable();
            $table->string('jumlah_barang')->nullable();

            // Poin 3: Tanda-tanda khusus
            $table->string('no_registrasi')->nullable();
            $table->string('isi_silinder')->nullable();
            $table->string('bahan_bakar')->nullable();
            $table->string('warna_tnkb')->nullable();
            $table->string('tahun_registrasi')->nullable();
            $table->string('no_bpkb')->nullable();
            $table->text('tanda_khusus_lain')->nullable();

            // Poin 4: Ciri-ciri (Tipe, Ukuran, dll)
            $table->string('tipe_barang')->nullable();
            $table->string('jenis_barang')->nullable();
            $table->string('model_barang')->nullable();
            $table->text('ciri_lain')->nullable();

            // Poin 5 s/d 12
            $table->string('warna_barang')->nullable();
            $table->string('tahun_pembuatan')->nullable();
            $table->string('taksiran_harga')->nullable();
            $table->string('merek_dagang')->nullable();
            $table->string('nomor_pembuatan')->nullable(); // No. Seri/ No. IMEI/ No. Rangka/ No. Mesin
            $table->string('tempat_pembuatan')->nullable();
            $table->text('cara_hilangnya')->nullable();
            $table->text('keterangan_lain')->nullable();

            // Tembusan / Kepada (Distribusi Surat)
            $table->string('kepada_1')->nullable();
            $table->string('kepada_2')->nullable();
            $table->string('kepada_3')->nullable();

            // Pejabat Penandatangan (Atasan Penyidik)
            $table->string('nama_penandatangan')->nullable();
            $table->string('pangkat_penandatangan')->nullable();
            $table->string('nrp_penandatangan')->nullable();
            $table->string('jabatan_penandatangan')->nullable()->default('ATASAN PENYIDIK');

            // Tracking & Audit Trail
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->unsignedBigInteger('updated_by_user_id')->nullable();
            $table->unsignedBigInteger('deleted_by_user_id')->nullable();
            $table->jsonb('ip_addresses')->nullable();
            $table->jsonb('timestamps')->nullable();
            $table->jsonb('messages')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('doc.surat_pencabutan_dpb_documents');
    }
};
