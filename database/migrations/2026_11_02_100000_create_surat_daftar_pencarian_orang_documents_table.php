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
        Schema::create('doc.surat_daftar_pencarian_orang_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id')->index();
            $table->string('document_category_id')->nullable()->default('0302');
            $table->string('status_id')->nullable()->default('2');

            // Header & Identitas Surat
            $table->string('kopstuk_1')->nullable();
            $table->string('kopstuk_2')->nullable();
            $table->string('kopstuk_3')->nullable();
            $table->string('kopstuk_4')->nullable();
            $table->string('nomor_surat')->nullable();
            $table->date('tanggal_surat')->nullable();
            $table->string('dikeluarkan_di')->nullable();
            $table->string('nomor_hp_penyidik')->nullable();

            // Rujukan Surat Permintaan (Kotak Atas Kanan)
            $table->text('rujukan_instansi')->nullable();
            $table->string('rujukan_nomor')->nullable();
            $table->date('rujukan_tanggal')->nullable();

            // Identitas Tersangka (Poin 1 s/d 8)
            $table->uuid('tersangka_id')->nullable();
            $table->string('tersangka_nama')->nullable();
            $table->string('tersangka_jenis_identitas')->nullable()->default('KTP');
            $table->string('tersangka_nomor_identitas')->nullable();
            $table->string('tersangka_kewarganegaraan')->nullable()->default('Indonesia');
            $table->string('tersangka_jenis_kelamin')->nullable();
            $table->string('tersangka_tempat_lahir')->nullable();
            $table->date('tersangka_tanggal_lahir')->nullable();
            $table->string('tersangka_pekerjaan')->nullable();
            $table->text('tersangka_alamat')->nullable();
            $table->string('tersangka_kontak')->nullable(); // No Telp / HP / Email / Medsos
            $table->string('foto_tersangka_path')->nullable(); // Path Pasfoto 4x6

            // Catatan Hukuman (Poin 9 a-d)
            $table->string('pernah_dihukum')->nullable()->default('Tidak Pernah');
            $table->string('hukuman_tempat')->nullable();
            $table->string('hukuman_lama')->nullable();
            $table->text('hukuman_keterangan')->nullable();

            // Rumus Daktiloskopi (Poin 10)
            $table->string('rumus_daktiloskopi')->nullable();

            // Ciri-Ciri Khusus Fisik (Poin 11 a-h)
            $table->string('tinggi_badan')->nullable(); // cm
            $table->string('berat_badan')->nullable();  // kg
            $table->string('rambut')->nullable();
            $table->string('bentuk_tubuh')->nullable();
            $table->string('warna_kulit')->nullable();
            $table->string('mata')->nullable();
            $table->string('hidung')->nullable();
            $table->string('bibir')->nullable();
            $table->text('ciri_khusus_lainnya')->nullable();

            // Uraian Kasus & Tindak Pidana (Poin 12 s/d 15)
            $table->text('uraian_tindak_pidana')->nullable();
            $table->string('pasal_pidana')->nullable();
            $table->string('uu_pidana_nomor')->nullable();
            $table->string('uu_pidana_tahun')->nullable();
            $table->string('uu_pidana_tentang')->nullable();
            $table->text('tkp')->nullable();
            $table->text('waktu_kejadian')->nullable();
            $table->string('no_reg_kejahatan')->nullable();
            $table->text('keterangan_lain_penting')->nullable();

            // Distribusi / Kepada (Bawah Kiri)
            $table->text('distribusi_kepada')->nullable();

            // Penandatangan Atasan Penyidik (Bawah Kanan)
            $table->string('atasan_nama')->nullable();
            $table->string('atasan_pangkat')->nullable();
            $table->string('atasan_nrp')->nullable();
            $table->string('atasan_jabatan')->nullable()->default('ATASAN PENYIDIK');

            // Audit Trail
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
        Schema::dropIfExists('doc.surat_daftar_pencarian_orang_documents');
    }
};

