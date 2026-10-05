<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Surat Perintah Pencabutan Penangguhan Penahanan (S-19 / SPRIN CABUT GUHAN)
     * Kode Dokumen SPP-TI: s19 | Kode Kategori Dokumen: 0604
     */
    public function up(): void
    {
        DB::beginTransaction();
        try {
            DB::statement('CREATE SCHEMA IF NOT EXISTS doc;');

            if (!Schema::hasTable('doc.surat_perintah_pencabutan_penangguhan_penahanan_documents')) {
                Schema::create('doc.surat_perintah_pencabutan_penangguhan_penahanan_documents', function (Blueprint $table) {
                    $table->uuid('id')->primary();
                    $table->uuid('accident_id')->nullable();
                    $table->uuid('surat_perintah_penyidikan_document_id')->nullable();
                    $table->uuid('surat_ketetapan_penetapan_tersangka_id')->nullable();
                    $table->uuid('surat_perintah_penahanan_document_id')->nullable();
                    $table->uuid('surat_perintah_penangguhan_penahanan_document_id')->nullable();

                    // Identitas Dokumen
                    $table->string('nomor')->nullable()->comment('WAJIB. Nomor S-19');
                    $table->date('tanggal')->nullable()->comment('WAJIB. Tanggal S-19');
                    $table->string('nomor_spdp')->nullable()->comment('WAJIB. Nomor SPDP');
                    $table->date('tanggal_spdp')->nullable()->comment('WAJIB. Tanggal SPDP');
                    $table->string('kode_satker_penerbit_spdp')->nullable()->comment('WAJIB. Kode Satker Penerbit SPDP');
                    $table->string('nomor_surat_perintah_penahanan')->nullable()->comment('Nomor S-17');
                    $table->date('tanggal_surat_perintah_penahanan')->nullable()->comment('Tanggal S-17');
                    $table->string('nomor_surat_perintah_penangguhan')->nullable()->comment('Nomor S-18');
                    $table->date('tanggal_surat_perintah_penangguhan')->nullable()->comment('Tanggal S-18');

                    // Konten Penahanan Kembali
                    $table->integer('kode_jenis_penahanan')->nullable()->default(1)->comment('1=Rutan, 2=Rumah, 3=Kota');
                    $table->string('kode_satker_tempat_penahanan')->nullable()->comment('Kode / Nama Satker / Rutan Tempat Penahanan');
                    $table->string('tempat_penahanan')->nullable()->comment('Teks Tempat Penahanan');
                    $table->integer('jumlah_hari')->nullable()->default(20)->comment('Sisa waktu masa penahanan dalam hari');
                    $table->date('tanggal_mulai')->nullable()->comment('Tanggal mulai penahanan kembali');
                    $table->date('tanggal_akhir')->nullable()->comment('Tanggal akhir penahanan kembali');
                    $table->text('alasan_pencabutan')->nullable()->comment('Alasan pencabutan penangguhan');

                    // Kompatibilitas Sistem Dokumen Internal
                    $table->string('document_number')->nullable();
                    $table->date('document_date')->nullable();
                    $table->string('status_id')->nullable()->default('2');
                    $table->string('document_category_id')->nullable()->default('0604');
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

                    $table->foreign('accident_id', 'fk_spp_cabut_guhan_docs_accident_id')
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
        Schema::dropIfExists('doc.surat_perintah_pencabutan_penangguhan_penahanan_documents');
    }
};
