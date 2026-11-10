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
                // Pemberi Kuasa exact template columns
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'pemberi_nama')) {
                    $table->string('pemberi_nama')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'pemberi_no_identitas')) {
                    $table->string('pemberi_no_identitas')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'pemberi_kewarganegaraan')) {
                    $table->string('pemberi_kewarganegaraan')->nullable()->default('Indonesia');
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'pemberi_jenis_kelamin')) {
                    $table->string('pemberi_jenis_kelamin')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'pemberi_ttl')) {
                    $table->string('pemberi_ttl')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'pemberi_umur')) {
                    $table->string('pemberi_umur')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'pemberi_pekerjaan')) {
                    $table->string('pemberi_pekerjaan')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'pemberi_agama')) {
                    $table->string('pemberi_agama')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'pemberi_alamat')) {
                    $table->text('pemberi_alamat')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'pemberi_nohp')) {
                    $table->string('pemberi_nohp')->nullable();
                }

                // Penerima Kuasa exact template columns
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'penerima_nama')) {
                    $table->string('penerima_nama')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'penerima_no_identitas')) {
                    $table->string('penerima_no_identitas')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'penerima_kewarganegaraan')) {
                    $table->string('penerima_kewarganegaraan')->nullable()->default('Indonesia');
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'penerima_jenis_kelamin')) {
                    $table->string('penerima_jenis_kelamin')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'penerima_ttl')) {
                    $table->string('penerima_ttl')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'penerima_umur')) {
                    $table->string('penerima_umur')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'penerima_pekerjaan')) {
                    $table->string('penerima_pekerjaan')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'penerima_agama')) {
                    $table->string('penerima_agama')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'penerima_alamat')) {
                    $table->text('penerima_alamat')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'penerima_nohp')) {
                    $table->string('penerima_nohp')->nullable();
                }

                // Tanggal & Tempat
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'dikeluarkan_di')) {
                    $table->string('dikeluarkan_di')->nullable();
                }
                if (!Schema::hasColumn('doc.surat_kuasa_documents', 'tanggal_surat')) {
                    $table->string('tanggal_surat')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('doc.surat_kuasa_documents')) {
            Schema::table('doc.surat_kuasa_documents', function (Blueprint $table) {
                $columns = [
                    'pemberi_nama', 'pemberi_no_identitas', 'pemberi_kewarganegaraan', 'pemberi_jenis_kelamin',
                    'pemberi_ttl', 'pemberi_umur', 'pemberi_pekerjaan', 'pemberi_agama', 'pemberi_alamat', 'pemberi_nohp',
                    'penerima_nama', 'penerima_no_identitas', 'penerima_kewarganegaraan', 'penerima_jenis_kelamin',
                    'penerima_ttl', 'penerima_umur', 'penerima_pekerjaan', 'penerima_agama', 'penerima_alamat', 'penerima_nohp',
                    'dikeluarkan_di', 'tanggal_surat',
                ];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('doc.surat_kuasa_documents', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};

