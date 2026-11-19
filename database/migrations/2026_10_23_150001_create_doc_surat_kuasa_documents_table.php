<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Surat Kuasa
     * Kode Kategori Dokumen: 0703 (DCT-0703)
     */
    public function up(): void
    {
        DB::beginTransaction();
        try {
            DB::statement('CREATE SCHEMA IF NOT EXISTS doc;');

            if (!Schema::hasTable('doc.surat_kuasa_documents')) {
                Schema::create('doc.surat_kuasa_documents', function (Blueprint $table) {
                    $table->uuid('id')->primary();
                    $table->uuid('accident_id')->nullable();

                    // Informasi Dokumen Surat Kuasa
                    $table->string('nomor')->nullable()->comment('Nomor Surat Kuasa (opsional)');
                    $table->date('tanggal')->nullable()->comment('Tanggal Surat Kuasa');
                    $table->string('tempat_surat')->nullable()->comment('Tempat/Kota Pembuatan Surat');
                    $table->string('judul_surat')->nullable()->default('SURAT KUASA KHUSUS')->comment('Judul Dokumen');

                    // Identitas Pemberi Kuasa
                    $table->string('pemberi_kuasa_nama')->nullable();
                    $table->string('pemberi_kuasa_nik')->nullable();
                    $table->string('pemberi_kuasa_tempat_lahir')->nullable();
                    $table->date('pemberi_kuasa_tanggal_lahir')->nullable();
                    $table->string('pemberi_kuasa_umur')->nullable();
                    $table->string('pemberi_kuasa_jenis_kelamin')->nullable();
                    $table->string('pemberi_kuasa_agama')->nullable();
                    $table->string('pemberi_kuasa_pekerjaan')->nullable();
                    $table->string('pemberi_kuasa_kewarganegaraan')->nullable()->default('Indonesia');
                    $table->text('pemberi_kuasa_alamat')->nullable();
                    $table->string('pemberi_kuasa_telepon')->nullable();
                    $table->string('pemberi_kuasa_kedudukan')->nullable()->comment('Selaku: Tersangka / Korban / Saksi / Keluarga dsb');

                    // Identitas Penerima Kuasa
                    $table->string('penerima_kuasa_nama')->nullable();
                    $table->string('penerima_kuasa_nik')->nullable()->comment('NIK / No KTP / No Anggota Advokat');
                    $table->string('penerima_kuasa_tempat_lahir')->nullable();
                    $table->date('penerima_kuasa_tanggal_lahir')->nullable();
                    $table->string('penerima_kuasa_umur')->nullable();
                    $table->string('penerima_kuasa_jenis_kelamin')->nullable();
                    $table->string('penerima_kuasa_agama')->nullable();
                    $table->string('penerima_kuasa_pekerjaan')->nullable()->default('Advokat / Penasihat Hukum');
                    $table->string('penerima_kuasa_kantor')->nullable()->comment('Kantor Hukum / Law Firm');
                    $table->text('penerima_kuasa_alamat')->nullable();
                    $table->string('penerima_kuasa_telepon')->nullable();

                    // Pokok Keperluan Kuasa (Khusus)
                    $table->string('nomor_lp')->nullable();
                    $table->date('tanggal_lp')->nullable();
                    $table->text('pokok_perkara')->nullable()->comment('Perkara kecelakaan lalu lintas');
                    $table->text('isi_kuasa')->nullable()->comment('Rincian kewenangan/hak kuasa khusus');
                    $table->text('catatan')->nullable();

                    // Pejabat Mengetahui (Opsional)
                    $table->string('mengetahui_signatory_id')->nullable();
                    $table->string('mengetahui_nama')->nullable();
                    $table->string('mengetahui_pangkat')->nullable();
                    $table->string('mengetahui_nrp')->nullable();
                    $table->string('mengetahui_jabatan')->nullable();

                    // Kompatibilitas Sistem Dokumen Internal
                    $table->string('document_number')->nullable();
                    $table->date('document_date')->nullable();
                    $table->string('status_id')->nullable()->default('2');
                    $table->string('document_category_id')->nullable()->default('0703');
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

                    $table->foreign('accident_id', 'fk_surat_kuasa_accident_id')
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
        Schema::dropIfExists('doc.surat_kuasa_documents');
    }
};
