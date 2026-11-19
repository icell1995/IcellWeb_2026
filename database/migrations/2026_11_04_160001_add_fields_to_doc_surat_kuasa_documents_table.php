<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('doc.surat_kuasa_documents')) {
            Schema::table('doc.surat_kuasa_documents', function (Blueprint $table) {
                // Kontak & medsos pemberi kuasa
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'pemberi_telp')) {
                    $table->string('pemberi_telp')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'pemberi_email')) {
                    $table->string('pemberi_email')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'pemberi_medsos_platform')) {
                    $table->string('pemberi_medsos_platform')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'pemberi_medsos_akun')) {
                    $table->string('pemberi_medsos_akun')->nullable();
                }

                // Penerima kuasa tambahan
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'penerima_kuasa_kewarganegaraan')) {
                    $table->string('penerima_kuasa_kewarganegaraan')->nullable()->default('Indonesia');
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'penerima_telp')) {
                    $table->string('penerima_telp')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'penerima_email')) {
                    $table->string('penerima_email')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'penerima_medsos_platform')) {
                    $table->string('penerima_medsos_platform')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'penerima_medsos_akun')) {
                    $table->string('penerima_medsos_akun')->nullable();
                }

                // Khusus & Perkara
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'tindak_pidana')) {
                    $table->string('tindak_pidana')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'pasal_pidana')) {
                    $table->string('pasal_pidana')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'uu_nomor')) {
                    $table->string('uu_nomor')->nullable()->default('22');
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'uu_tahun')) {
                    $table->string('uu_tahun')->nullable()->default('2009');
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'uu_tentang')) {
                    $table->string('uu_tentang')->nullable()->default('Lalu Lintas dan Angkutan Jalan');
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'tkp')) {
                    $table->text('tkp')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'waktu_kejadian')) {
                    $table->string('waktu_kejadian')->nullable();
                }

                // Rujukan
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'dasar_lp_nomor')) {
                    $table->string('dasar_lp_nomor')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'dasar_lp_tanggal')) {
                    $table->string('dasar_lp_tanggal')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'dasar_sp_sidik_nomor')) {
                    $table->string('dasar_sp_sidik_nomor')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'dasar_sp_sidik_tanggal')) {
                    $table->string('dasar_sp_sidik_tanggal')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'dasar_sp_gas_sidik_nomor')) {
                    $table->string('dasar_sp_gas_sidik_nomor')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'dasar_sp_gas_sidik_tanggal')) {
                    $table->string('dasar_sp_gas_sidik_tanggal')->nullable();
                }

                // Surat Penetapan Penunjukan Penasehat Hukum
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'surat_penetapan_nomor')) {
                    $table->string('surat_penetapan_nomor')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'surat_penetapan_tanggal')) {
                    $table->string('surat_penetapan_tanggal')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

