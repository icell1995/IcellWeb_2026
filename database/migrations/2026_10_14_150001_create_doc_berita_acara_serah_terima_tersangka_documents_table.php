<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Berita Acara Serah Terima Tersangka Dan Barang Bukti
     * Kode Kategori Dokumen: 0602
     */
    public function up(): void
    {
        DB::beginTransaction();
        try {
            DB::statement('CREATE SCHEMA IF NOT EXISTS doc;');

            if (!Schema::hasTable('doc.berita_acara_serah_terima_tersangka_documents')) {
                Schema::create('doc.berita_acara_serah_terima_tersangka_documents', function (Blueprint $table) {
                    $table->uuid('id')->primary();
                    $table->uuid('accident_id')->nullable();
                    $table->uuid('surat_perintah_penyidikan_document_id')->nullable();
                    $table->uuid('surat_perintah_penahanan_document_id')->nullable();
                    $table->string('prosecutor_id')->nullable();

                    // Identitas Dokumen & Tempat/Waktu
                    $table->string('nomor')->nullable()->comment('Nomor Berita Acara');
                    $table->date('tanggal')->nullable()->comment('Tanggal Berita Acara');
                    $table->string('hari')->nullable()->comment('Hari pembuatan (misal: Senin)');
                    $table->string('jam')->nullable()->comment('Pukul / Jam pembuatan (misal: 10.00 WIB)');
                    $table->string('tempat')->nullable()->comment('Tempat / Ruangan pembuatan');

                    // Dokumen Terkait / Dasar Serah Terima
                    $table->string('nomor_spdp')->nullable();
                    $table->date('tanggal_spdp')->nullable();
                    $table->string('kode_satker_penerbit_spdp')->nullable();
                    $table->string('nomor_sprindik')->nullable();
                    $table->date('tanggal_sprindik')->nullable();
                    $table->string('nomor_surat_perintah_penahanan')->nullable();
                    $table->date('tanggal_surat_perintah_penahanan')->nullable();
                    $table->string('nomor_p21')->nullable()->comment('Nomor Surat P21 dari Kejaksaan / Dasar Penyerahan');
                    $table->date('tanggal_p21')->nullable();

                    // Pihak Kedua (Yang Menerima)
                    $table->string('nama_kejaksaan')->nullable();
                    $table->string('lokasi_kejaksaan')->nullable();
                    $table->string('pihak_kedua_nama')->nullable()->comment('Nama Petugas / JPU Penerima');
                    $table->string('pihak_kedua_nip')->nullable()->comment('NIP / NRP Petugas Penerima');
                    $table->string('pihak_kedua_pangkat')->nullable()->comment('Pangkat Petugas Penerima');
                    $table->string('pihak_kedua_jabatan')->nullable()->comment('Jabatan Petugas Penerima');
                    $table->string('pihak_kedua_instansi')->nullable()->comment('Instansi Petugas Penerima');

                    // Barang Bukti & Keterangan
                    $table->text('daftar_barang_bukti')->nullable()->comment('Rincian barang bukti yang diserahkan');
                    $table->text('catatan')->nullable()->comment('Catatan tambahan atau kondisi serah terima');

                    // Signatory / Atasan
                    $table->string('signatory_id')->nullable();
                    $table->string('signatory_head_text')->nullable();
                    $table->string('signatory_position')->nullable();
                    $table->string('signatory_name')->nullable();
                    $table->string('signatory_rank')->nullable();
                    $table->string('signatory_nrp')->nullable();

                    // Kompatibilitas Sistem Dokumen Internal
                    $table->string('document_number')->nullable();
                    $table->date('document_date')->nullable();
                    $table->string('status_id')->nullable()->default('2');
                    $table->string('document_category_id')->nullable()->default('0602');
                    $table->boolean('is_active')->default(true);
                    $table->boolean('is_legacy')->default(false);
                    $table->dateTime('last_synced_at')->nullable();
                    $table->dateTime('released_at')->nullable();
                    $table->dateTime('approved_at')->nullable();
                    $table->dateTime('rejected_at')->nullable();
                    $table->json('messages')->nullable();
                    $table->json('ip_addresses')->nullable();
                    $table->json('payload')->nullable();

                    $table->unsignedBigInteger('created_by_user_id')->nullable();
                    $table->unsignedBigInteger('updated_by_user_id')->nullable();
                    $table->unsignedBigInteger('deleted_by_user_id')->nullable();

                    $table->timestamps();
                    $table->softDeletes();

                    $table->foreign('accident_id', 'fk_ba_serah_terima_tsk_accident_id')
                        ->references('id')->on('public.accidents')
                        ->onDelete('set null')->onUpdate('cascade');
                });
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doc.berita_acara_serah_terima_tersangka_documents');
    }
};
