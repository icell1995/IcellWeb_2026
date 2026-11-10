<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Surat Permintaan Pemblokiran Rekening Bank
     * Kode Kategori Dokumen: 0707 (DCT-0707)
     */
    public function up(): void
    {
        DB::beginTransaction();
        try {
            DB::statement('CREATE SCHEMA IF NOT EXISTS doc;');

            if (!Schema::hasTable('doc.surat_permintaan_blokir_rekening_bank_documents')) {
                Schema::create('doc.surat_permintaan_blokir_rekening_bank_documents', function (Blueprint $table) {
                    $table->uuid('id')->primary();
                    $table->uuid('accident_id')->nullable();
                    $table->uuid('surat_perintah_penyidikan_document_id')->nullable();
                    $table->string('suspect_id')->nullable();

                    // Identitas Dokumen
                    $table->string('nomor')->nullable()->comment('Nomor Surat');
                    $table->string('sifat')->nullable()->default('Biasa')->comment('Sifat/Klasifikasi: Biasa / Segera / Rahasia');
                    $table->string('lampiran')->nullable()->default('-')->comment('Lampiran');
                    $table->string('perihal')->nullable()->default('Permintaan Pemblokiran Rekening Bank')->comment('Perihal Surat');
                    $table->date('tanggal')->nullable()->comment('Tanggal Surat');
                    $table->string('tempat_surat')->nullable()->comment('Tempat penerbitan surat');

                    // Tujuan / Pihak Bank
                    $table->string('bank_tujuan')->nullable()->comment('Nama & Pimpinan Bank Tujuan');
                    $table->string('tempat_tujuan')->nullable()->default('Tempat')->comment('Kota / Tempat Bank');

                    // Dasar Hukum / Rujukan
                    $table->text('dasar_kuhp')->nullable();
                    $table->text('dasar_kuhap')->nullable();
                    $table->text('dasar_polri')->nullable();
                    $table->text('dasar_uu_pidana')->nullable();
                    $table->string('dasar_lp_nomor')->nullable();
                    $table->string('dasar_lp_tanggal')->nullable();
                    $table->string('dasar_sp_sidik_nomor')->nullable();
                    $table->string('dasar_sp_sidik_tanggal')->nullable();

                    // Informasi Perkara & Dugaan Tindak Pidana
                    $table->string('satker')->nullable();
                    $table->string('tindak_pidana')->nullable();
                    $table->string('pasal_pidana')->nullable();
                    $table->string('uu_nomor')->nullable();
                    $table->string('uu_tahun')->nullable();
                    $table->string('uu_tentang')->nullable();
                    $table->text('tkp')->nullable();
                    $table->string('waktu_kejadian')->nullable();

                    // Identitas Tersangka
                    $table->string('tersangka_nama')->nullable();
                    $table->string('tersangka_no_identitas')->nullable();
                    $table->string('tersangka_kewarganegaraan')->nullable()->default('WNI');
                    $table->string('tersangka_jenis_kelamin')->nullable();
                    $table->string('tersangka_ttl')->nullable();
                    $table->string('tersangka_pekerjaan')->nullable();
                    $table->string('tersangka_agama')->nullable();
                    $table->text('tersangka_alamat')->nullable();

                    // Objek Blokir & Pelaksanaan Kehadiran
                    $table->text('objek_blokir')->nullable()->comment('Nomor Rekening, Bank & Atas Nama Objek Pemblokiran');
                    $table->string('blokir_hari_tanggal')->nullable();
                    $table->string('blokir_waktu')->nullable();
                    $table->string('blokir_tempat')->nullable();

                    // Penyidik / Narahubung
                    $table->string('penyidik_id')->nullable();
                    $table->string('penyidik_sebutan')->nullable()->default('Penyidik Pembantu');
                    $table->string('penyidik_nama')->nullable();
                    $table->string('penyidik_nohp')->nullable();

                    // Tembusan
                    $table->string('tembusan_1')->nullable();
                    $table->string('tembusan_2')->nullable();
                    $table->string('tembusan_3')->nullable();
                    $table->string('tembusan_4')->nullable();
                    $table->string('tembusan_5')->nullable();
                    $table->json('tembusan')->nullable();
                    $table->text('catatan')->nullable();

                    // Pejabat Penandatangan (Signatory)
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
                    $table->string('document_category_id')->nullable()->default('0707');
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

                    $table->foreign('accident_id', 'fk_sp_blokir_bank_accident_id')
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
        Schema::dropIfExists('doc.surat_permintaan_blokir_rekening_bank_documents');
    }
};

