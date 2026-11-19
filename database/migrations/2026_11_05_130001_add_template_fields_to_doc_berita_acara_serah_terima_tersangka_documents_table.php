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
        if (Schema::hasTable('doc.berita_acara_serah_terima_tersangka_documents')) {
            Schema::table('doc.berita_acara_serah_terima_tersangka_documents', function (Blueprint $table) {
                // Kop
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'daerah_police_full_name')) {
                    $table->string('daerah_police_full_name')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'resor_police_full_name')) {
                    $table->string('resor_police_full_name')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'resor_police_address')) {
                    $table->string('resor_police_address')->nullable();
                }

                // Waktu & Pembukaan
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'document_day_name')) {
                    $table->string('document_day_name')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'document_day_date')) {
                    $table->string('document_day_date')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'document_month')) {
                    $table->string('document_month')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'document_year')) {
                    $table->string('document_year')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'document_date_text')) {
                    $table->string('document_date_text')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'document_time')) {
                    $table->string('document_time')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'document_location')) {
                    $table->string('document_location')->nullable();
                }

                // Penyidik (Yang Menyerahkan)
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'investigator_name')) {
                    $table->string('investigator_name')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'investigator_rank_name')) {
                    $table->string('investigator_rank_name')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'investigator_register_number')) {
                    $table->string('investigator_register_number')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'investigator_position_name')) {
                    $table->string('investigator_position_name')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'investigator_role')) {
                    $table->string('investigator_role')->nullable()->default('PENYIDIK / PENYIDIK PEMBANTU');
                }

                // 2 Petugas Pendamping
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'officer1_name')) {
                    $table->string('officer1_name')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'officer1_rank_name')) {
                    $table->string('officer1_rank_name')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'officer1_register_number')) {
                    $table->string('officer1_register_number')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'officer1_position_name')) {
                    $table->string('officer1_position_name')->nullable();
                }

                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'officer2_name')) {
                    $table->string('officer2_name')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'officer2_rank_name')) {
                    $table->string('officer2_rank_name')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'officer2_register_number')) {
                    $table->string('officer2_register_number')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'officer2_position_name')) {
                    $table->string('officer2_position_name')->nullable();
                }

                // Rujukan Serah Terima
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'prosecutor_institution_name')) {
                    $table->string('prosecutor_institution_name')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'p21_document_number')) {
                    $table->string('p21_document_number')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'p21_document_date')) {
                    $table->string('p21_document_date')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'deliverer_head_title')) {
                    $table->string('deliverer_head_title')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'delivery_letter_number')) {
                    $table->string('delivery_letter_number')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'delivery_letter_date')) {
                    $table->string('delivery_letter_date')->nullable();
                }

                // Tersangka & Berkas Perkara
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'suspect_name')) {
                    $table->string('suspect_name')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'suspect2_line')) {
                    $table->text('suspect2_line')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'berkas_number')) {
                    $table->string('berkas_number')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'berkas_date')) {
                    $table->string('berkas_date')->nullable();
                }

                // Penuntut Umum (Penerima)
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'prosecutor_officer_name')) {
                    $table->string('prosecutor_officer_name')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'prosecutor_officer_rank_nip')) {
                    $table->string('prosecutor_officer_rank_nip')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'prosecutor_officer_position_name')) {
                    $table->string('prosecutor_officer_position_name')->nullable();
                }

                // Kasus & Perkara
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'dugaan_tindak_pidana')) {
                    $table->string('dugaan_tindak_pidana')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'pasal_diduga')) {
                    $table->string('pasal_diduga')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'uu_nomor')) {
                    $table->string('uu_nomor')->nullable()->default('22');
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'uu_tahun')) {
                    $table->string('uu_tahun')->nullable()->default('2009');
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'uu_tentang')) {
                    $table->string('uu_tentang')->nullable()->default('Lalu Lintas dan Angkutan Jalan');
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'tempat_kejadian')) {
                    $table->text('tempat_kejadian')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'waktu_kejadian')) {
                    $table->string('waktu_kejadian')->nullable();
                }

                // Pelaksanaan & Tempat
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'handover_day')) {
                    $table->string('handover_day')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'handover_date')) {
                    $table->string('handover_date')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'handover_place')) {
                    $table->string('handover_place')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'execution_description')) {
                    $table->text('execution_description')->nullable();
                }

                // Saksi 1 & Saksi 2
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'witness1_name')) {
                    $table->string('witness1_name')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'witness1_rank_nip')) {
                    $table->string('witness1_rank_nip')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'witness1_position_name')) {
                    $table->string('witness1_position_name')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'witness1_address')) {
                    $table->text('witness1_address')->nullable();
                }

                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'witness2_name')) {
                    $table->string('witness2_name')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'witness2_rank_nip')) {
                    $table->string('witness2_rank_nip')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'witness2_position_name')) {
                    $table->string('witness2_position_name')->nullable();
                }
                if (!Schema::hasColumn('doc.berita_acara_serah_terima_tersangka_documents', 'witness2_address')) {
                    $table->text('witness2_address')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // down logic optional
    }
};

