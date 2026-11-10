<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Surat Penunjukan Penasihat Hukum
     * Kode Kategori Dokumen: 0704 (DCT-0704)
     */
    public function up(): void
    {
        DB::beginTransaction();
        try {
            DB::statement('CREATE SCHEMA IF NOT EXISTS doc;');

            if (!Schema::hasTable('doc.surat_penunjukan_penasihat_hukum_documents')) {
                Schema::create('doc.surat_penunjukan_penasihat_hukum_documents', function (Blueprint $table) {
                    $table->uuid('id')->primary();
                    $table->uuid('accident_id')->nullable();

                    // Status & Kategori Dokumen
                    $table->string('status_id')->nullable()->default('2');
                    $table->string('document_category_id')->nullable()->default('0704');

                    // 1. Kop Surat Kepolisian
                    $table->string('daerah_police_full_name')->nullable();
                    $table->string('resor_police_full_name')->nullable();
                    $table->string('resor_police_address')->nullable();

                    // 2. Metadata & Alamat Tujuan Surat
                    $table->string('document_number')->nullable();
                    $table->string('document_location')->nullable();
                    $table->string('document_date')->nullable();
                    $table->string('document_classification_name')->nullable()->default('Biasa');
                    $table->string('appendix')->nullable()->default('-');
                    $table->string('target_legal_aid_name')->nullable();
                    $table->string('target_legal_aid_location')->nullable();

                    // 3. Dasar Rujukan (Poin 1)
                    $table->text('dasar_kuhap')->nullable();
                    $table->text('undang_undang_lain')->nullable();
                    $table->string('accident_number')->nullable();
                    $table->string('accident_date')->nullable();
                    $table->string('surat_perintah_penyidikan_document_number')->nullable();
                    $table->string('surat_perintah_penyidikan_document_date')->nullable();
                    $table->string('surat_keterangan_tidak_mampu_instansi')->nullable();
                    $table->string('surat_keterangan_tidak_mampu_number')->nullable();
                    $table->string('surat_keterangan_tidak_mampu_date')->nullable();
                    $table->string('surat_keterangan_tidak_mampu_person_name')->nullable();

                    // 4. Identitas Tersangka (Poin 2)
                    $table->uuid('suspect_id')->nullable();
                    $table->string('suspect_name')->nullable();
                    $table->string('suspect_identity_number')->nullable();
                    $table->string('suspect_nationality')->nullable()->default('Indonesia');
                    $table->string('suspect_gender_name')->nullable();
                    $table->string('suspect_birth_place')->nullable();
                    $table->string('suspect_birth_date')->nullable();
                    $table->string('suspect_job_name')->nullable();
                    $table->string('suspect_religion_name')->nullable();
                    $table->text('suspect_full_address')->nullable();

                    // 5. Perkara Pidana, TKP, Waktu Kejadian
                    $table->string('dugaan_tindak_pidana')->nullable();
                    $table->string('pasal_diduga')->nullable();
                    $table->string('uu_nomor')->nullable()->default('22');
                    $table->string('uu_tahun')->nullable()->default('2009');
                    $table->string('uu_tentang')->nullable()->default('Lalu Lintas dan Angkutan Jalan');
                    $table->text('tempat_kejadian')->nullable();
                    $table->string('waktu_kejadian')->nullable();
                    $table->string('kurun_waktu')->nullable()->default('-');

                    // 6. Jadwal Pemeriksaan & Narahubung (Poin 3)
                    $table->string('examination_day')->nullable();
                    $table->string('examination_date')->nullable();
                    $table->string('examination_time')->nullable();
                    $table->string('examination_office_name')->nullable();
                    $table->text('examination_office_address')->nullable();
                    $table->uuid('contact_officer_id')->nullable();
                    $table->string('contact_officer_role')->nullable()->default('Penyidik Pembantu');
                    $table->string('contact_officer_name')->nullable();
                    $table->string('contact_officer_phone')->nullable();

                    // 7. Tembusan
                    $table->string('carbon_copy_1')->nullable();
                    $table->string('carbon_copy_2')->nullable();
                    $table->string('carbon_copy_3')->nullable();

                    // 8. Tanda Tangan Pejabat
                    $table->uuid('signatory_id')->nullable();
                    $table->string('signatory_head_text')->nullable();
                    $table->string('signatory_position_name')->nullable();
                    $table->string('signatory_name')->nullable();
                    $table->string('signatory_rank_name')->nullable();
                    $table->string('signatory_register_number')->nullable();

                    // Audit Columns
                    $table->uuid('created_by_user_id')->nullable();
                    $table->uuid('updated_by_user_id')->nullable();
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
        Schema::dropIfExists('doc.surat_penunjukan_penasihat_hukum_documents');
    }
};

