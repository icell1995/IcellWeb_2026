<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Surat Permintaan Buka Blokir Rekening Bank
     * Kode Kategori Dokumen: 0708 (DCT-0708)
     */
    public function up(): void
    {
        DB::beginTransaction();
        try {
            DB::statement('CREATE SCHEMA IF NOT EXISTS doc;');

            if (!Schema::hasTable('doc.surat_permintaan_buka_blokir_rekening_bank_documents')) {
                Schema::create('doc.surat_permintaan_buka_blokir_rekening_bank_documents', function (Blueprint $table) {
                    $table->uuid('id')->primary();
                    $table->uuid('accident_id')->nullable();

                    // Status & Kategori Dokumen
                    $table->string('status_id')->nullable()->default('2');
                    $table->string('document_category_id')->nullable()->default('0708');

                    // 1. Kop Surat Kepolisian
                    $table->string('daerah_police_full_name')->nullable();
                    $table->string('resor_police_full_name')->nullable();
                    $table->string('resor_police_address')->nullable();

                    // 2. Metadata & Bank Tujuan
                    $table->string('document_number')->nullable();
                    $table->string('document_location')->nullable();
                    $table->string('document_date')->nullable();
                    $table->string('document_classification_name')->nullable()->default('Biasa');
                    $table->string('appendix')->nullable()->default('-');
                    $table->string('target_bank_name')->nullable();
                    $table->string('target_bank_location')->nullable();

                    // 3. Dasar Rujukan (Poin 1)
                    $table->text('dasar_kuhap')->nullable();
                    $table->text('undang_undang_lain')->nullable();
                    $table->string('accident_number')->nullable();
                    $table->string('accident_date')->nullable();
                    $table->string('surat_perintah_penyidikan_document_number')->nullable();
                    $table->string('surat_perintah_penyidikan_document_date')->nullable();
                    $table->string('surat_pemblokiran_nomor')->nullable();
                    $table->string('surat_pemblokiran_tanggal')->nullable();
                    $table->string('nama_pengadilan_negeri')->nullable();
                    $table->string('penetapan_court_number')->nullable();
                    $table->string('penetapan_court_date')->nullable();
                    $table->string('surat_perintah_kepala_instansi')->nullable();
                    $table->string('surat_perintah_kepala_nomor')->nullable();

                    // 4. Informasi Perkara & Pihak Bank (Poin 2)
                    $table->string('target_bank_leader_title')->nullable()->default('Pemimpin Cabang');
                    $table->string('satker_penyidik')->nullable();
                    $table->string('dugaan_tindak_pidana')->nullable();
                    $table->string('pasal_diduga')->nullable();
                    $table->string('uu_nomor')->nullable()->default('22');
                    $table->string('uu_tahun')->nullable()->default('2009');
                    $table->string('uu_tentang')->nullable()->default('Lalu Lintas dan Angkutan Jalan');
                    $table->text('tempat_kejadian')->nullable();
                    $table->string('waktu_kejadian')->nullable();

                    // 5. Identitas Tersangka (Poin 2)
                    $table->string('suspect_id')->nullable();
                    $table->string('suspect_name')->nullable();
                    $table->string('suspect_identity_number')->nullable();
                    $table->string('suspect_nationality')->nullable()->default('Indonesia');
                    $table->string('suspect_gender_name')->nullable();
                    $table->string('suspect_birth_place')->nullable();
                    $table->string('suspect_birth_date')->nullable();
                    $table->string('suspect_job_name')->nullable();
                    $table->string('suspect_religion_name')->nullable();
                    $table->text('suspect_full_address')->nullable();

                    // 6. Rekening Buka Blokir & Rekening Penampungan Sitaan (Poin 3)
                    $table->string('account_number_1')->nullable();
                    $table->string('account_holder_name_1')->nullable();
                    $table->string('account_owner_type_1')->nullable()->default('Tersangka');
                    $table->string('account_number_2_line')->nullable()->default('-');
                    $table->string('seizure_amount')->nullable();
                    $table->string('escrow_bank_name')->nullable();
                    $table->string('escrow_account_number')->nullable();
                    $table->string('escrow_account_holder_name')->nullable();
                    $table->string('escrow_account_owner_type')->nullable()->default('Rekening Penampungan Barang Bukti');

                    // 7. Petugas Narahubung (Poin 4)
                    $table->string('contact_officer_id')->nullable();
                    $table->string('contact_officer_role')->nullable()->default('Penyidik Pembantu');
                    $table->string('contact_officer_name')->nullable();
                    $table->string('contact_officer_phone')->nullable();

                    // 8. Tembusan
                    $table->string('carbon_copy_2')->nullable();

                    // 9. Pejabat Penandatangan
                    $table->string('signatory_id')->nullable();
                    $table->string('signatory_head_text')->nullable();
                    $table->string('signatory_position_name')->nullable();
                    $table->string('signatory_name')->nullable();
                    $table->string('signatory_rank_name')->nullable();
                    $table->string('signatory_register_number')->nullable();

                    // Audit Columns
                    $table->unsignedBigInteger('created_by_user_id')->nullable();
                    $table->unsignedBigInteger('updated_by_user_id')->nullable();
                    $table->unsignedBigInteger('deleted_by_user_id')->nullable();
                    $table->boolean('is_active')->default(true);
                    $table->softDeletes();
                    $table->timestamps();
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
        Schema::dropIfExists('doc.surat_permintaan_buka_blokir_rekening_bank_documents');
    }
};

