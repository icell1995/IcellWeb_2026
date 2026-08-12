<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pindahkan tabel ke schema doc lalu perbaiki nama tabelnya
        DB::statement('ALTER TABLE "doc_tahap_1_documents" SET SCHEMA "doc"');
        DB::statement('ALTER TABLE "doc"."doc_tahap_1_documents" RENAME TO "tahap_1_documents"');

        // Menambahkan fields/kolom tambahan dan standarisasi (Harmonisasi dengan SP3)
        Schema::table('doc.tahap_1_documents', function (Blueprint $table) {
            // Harmonisasi Tipe Data Relasi User (Legacy BigInt -> UUID)
            // Kita drop dulu foreign key lama jika ada (Postgres)
            DB::statement('ALTER TABLE "doc"."tahap_1_documents" DROP CONSTRAINT IF EXISTS fk_tahap_1_docs_created_by_user_id');
            DB::statement('ALTER TABLE "doc"."tahap_1_documents" DROP CONSTRAINT IF EXISTS fk_tahap_1_docs_updated_by_user_id');
            DB::statement('ALTER TABLE "doc"."tahap_1_documents" DROP CONSTRAINT IF EXISTS fk_tahap_1_docs_deleted_by_user_id');

            // Ubah tipe data kolom ke UUID
            DB::statement('ALTER TABLE "doc"."tahap_1_documents" ALTER COLUMN "created_by_user_id" TYPE uuid USING created_by_user_id::text::uuid');
            DB::statement('ALTER TABLE "doc"."tahap_1_documents" ALTER COLUMN "updated_by_user_id" TYPE uuid USING updated_by_user_id::text::uuid');
            DB::statement('ALTER TABLE "doc"."tahap_1_documents" ALTER COLUMN "deleted_by_user_id" TYPE uuid USING deleted_by_user_id::text::uuid');

            // Tambahkan missing workflow fields
            $table->dateTime('submitted_at')->nullable()->after('messages')->comment('Waktu submit berkas');
            
            // Set default values sesuai konsep global
            DB::statement('ALTER TABLE "doc"."tahap_1_documents" ALTER COLUMN "document_category_id" SET DEFAULT \'0805\'');
            DB::statement('ALTER TABLE "doc"."tahap_1_documents" ALTER COLUMN "status_id" SET DEFAULT \'1\'');

            // Field Bisnis Tambahan
            $table->string('klasifikasi')->nullable()->comment('Klasifikasi');
            $table->string('lampiran')->nullable()->comment('Lampiran');

            // Relasi Referensi Dokumen Lain
            $table->uuid('surat_perintah_penyidikan_id')->nullable()->comment('Ref SP_SIDIK');
            $table->uuid('surat_pemberitahuan_dimulainya_penyidikan_id')->nullable()->comment('Ref SPDP');
            $table->uuid('surat_ketetapan_penetapan_tersangka_id')->nullable()->comment('Ref Penetapan Tersangka');
            
            // Detail Berkas
            $table->string('berkas_perkara_number')->nullable()->comment('No Berkas Perkara');
            $table->date('berkas_perkara_date')->nullable()->comment('Tanggal Berkas');
            $table->integer('berkas_perkara_rangkap')->nullable()->default(1);
            $table->text('pasal_disangkakan')->nullable()->comment('Pasal yang dipersangkakan');
            
            // Data Penahanan
            $table->string('penahanan_rutan')->nullable()->comment('Nama RUTAN');
            $table->string('penahanan_cabang')->nullable()->comment('Cabang RUTAN');
            $table->date('penahanan_start_date')->nullable();
            $table->date('penahanan_end_date')->nullable();
            
            $table->string('surat_perintah_penahanan_number')->nullable();
            $table->date('surat_perintah_penahanan_date')->nullable();
            
            $table->string('surat_perpanjangan_penahanan_number')->nullable();
            $table->date('surat_perpanjangan_penahanan_date')->nullable();
            $table->string('surat_perpanjangan_penahanan_court_number')->nullable()->comment('Nomor perpanjangan dari Pengadilan');
            $table->date('surat_perpanjangan_penahanan_court_date')->nullable();
            
            $table->string('penahanan_status', 50)->default('TIDAK_DITAHAN')->comment('Status: DITAHAN, DITANGGUHKAN, TIDAK_DITAHAN');
            $table->string('surat_penangguhan_penahanan_number')->nullable();
            $table->date('surat_penangguhan_penahanan_date')->nullable();

            // Barang Bukti
            $table->string('barang_bukti_storage')->nullable()->comment('Tempat penyimpanan BB');
            $table->json('barang_bukti')->nullable()->comment('Array bukti JSON');
            $table->integer('jumlah_bb')->nullable();
            
            // Investigator
            $table->string('investigator_pangkat_nama')->nullable();
            $table->string('investigator_hp')->nullable();

            // Tembusan
            $table->json('tembusan')->nullable();

            // Re-connect Foreign Keys (Tanpa User FK sesuai konsep SP3)
            // Catatan: SP3 menggunakan UUID untuk audit trail tapi tidak memasang hard FK ke tabel users (Bigint) 
            // untuk menghindari mismatch. Kita ikuti pola tersebut di sini.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doc.tahap_1_documents', function (Blueprint $table) {
            $table->dropColumn([
                'klasifikasi', 'lampiran', 'surat_perintah_penyidikan_id', 'surat_pemberitahuan_dimulainya_penyidikan_id',
                'surat_ketetapan_penetapan_tersangka_id', 'berkas_perkara_number', 'berkas_perkara_date', 'berkas_perkara_rangkap',
                'pasal_disangkakan', 'penahanan_rutan', 'penahanan_cabang', 'penahanan_start_date', 'penahanan_end_date',
                'surat_perintah_penahanan_number', 'surat_perintah_penahanan_date', 'surat_perpanjangan_penahanan_number',
                'surat_perpanjangan_penahanan_date', 'surat_perpanjangan_penahanan_court_number', 'surat_perpanjangan_penahanan_court_date',
                'penahanan_status', 'surat_penangguhan_penahanan_number', 'surat_penangguhan_penahanan_date', 'barang_bukti_storage',
                'barang_bukti', 'jumlah_bb', 'investigator_pangkat_nama', 'investigator_hp', 'tembusan'
            ]);
        });

        // Restore naming
        DB::statement('ALTER TABLE "doc"."tahap_1_documents" RENAME TO "doc_tahap_1_documents"');
        // Restore to public schema
        DB::statement('ALTER TABLE "doc"."doc_tahap_1_documents" SET SCHEMA "public"');
    }
};
