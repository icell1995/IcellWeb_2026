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
        Schema::create('doc.resume_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id')->index();
            $table->string('document_category_id')->nullable()->default('0801');
            $table->string('status_id')->nullable()->default('2');

            // Header & Identitas Dokumen
            $table->string('nomor_surat')->nullable();
            $table->date('tanggal_surat')->nullable();
            $table->string('dikeluarkan_di')->nullable();
            $table->string('satker_penyidik')->nullable();

            // Rujukan Kasus
            $table->string('nomor_lp')->nullable();
            $table->date('tanggal_lp')->nullable();

            // I. Dasar
            $table->jsonb('dasar')->nullable();

            // II. Perkara
            $table->jsonb('perkara')->nullable();

            // III. Fakta-Fakta Penyidikan
            $table->jsonb('fakta_tkp')->nullable();
            $table->jsonb('fakta_korban')->nullable();
            $table->jsonb('fakta_pemanggilan')->nullable();
            $table->jsonb('fakta_perintah_membawa')->nullable();
            $table->jsonb('fakta_penangkapan')->nullable();
            $table->jsonb('fakta_penahanan')->nullable();
            $table->jsonb('fakta_penggeledahan')->nullable();
            $table->jsonb('fakta_penyitaan')->nullable();

            // Keterangan Saksi, Ahli, Tersangka (Repeater)
            $table->jsonb('keterangan_saksi')->nullable();
            $table->jsonb('keterangan_ahli')->nullable();
            $table->jsonb('keterangan_tersangka')->nullable();

            // Fakta Tambahan
            $table->text('fakta_konfrontasi')->nullable();
            $table->text('fakta_rekonstruksi')->nullable();
            $table->text('fakta_dpo')->nullable();
            $table->text('fakta_dpb')->nullable();
            $table->text('fakta_red_notice')->nullable();
            $table->text('fakta_pencegahan')->nullable();
            $table->text('fakta_penangkalan')->nullable();

            // Barang Bukti (Repeater)
            $table->jsonb('barang_bukti')->nullable();

            // Penghentian & Lanjutan
            $table->jsonb('fakta_penghentian_penyidikan')->nullable();
            $table->jsonb('fakta_penyidikan_lanjutan')->nullable();

            // IV. Pembahasan
            $table->jsonb('analisa_kasus')->nullable();
            $table->jsonb('analisa_yuridis')->nullable();

            // V. Kesimpulan
            $table->jsonb('kesimpulan')->nullable();

            // Penandatangan Kiri: Atasan Penyidik Selaku Penyidik
            $table->string('atasan_penyidik_nama')->nullable();
            $table->string('atasan_penyidik_pangkat')->nullable();
            $table->string('atasan_penyidik_nrp')->nullable();
            $table->string('atasan_penyidik_jabatan')->nullable()->default('ATASAN PENYIDIK Selaku Penyidik');

            // Penandatangan Kanan: Yang Membuat (Penyidik / Penyidik Pembantu)
            $table->string('pembuat_nama')->nullable();
            $table->string('pembuat_pangkat')->nullable();
            $table->string('pembuat_nrp')->nullable();
            $table->string('pembuat_jabatan')->nullable()->default('Penyidik / Penyidik Pembantu');

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
        Schema::dropIfExists('doc.resume_documents');
    }
};
