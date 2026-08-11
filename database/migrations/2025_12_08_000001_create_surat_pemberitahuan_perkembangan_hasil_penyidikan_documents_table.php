<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Create SP2HP Documents Table
        Schema::create('doc.surat_pemberitahuan_perkembangan_hasil_penyidikan_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id');
            $table->string('document_category_id')->nullable();
            $table->uuid('reporting_person_id')->nullable(); // Reference to reporting_persons table for penerima
            
            // Informasi Surat
            $table->string('nomor_lp', 255)->nullable();
            $table->date('tanggal_lp')->nullable();
            $table->string('nomor_surat', 255)->nullable();
            $table->date('tanggal_surat')->nullable();
            $table->string('tempat_surat', 255)->nullable();
            
            $table->enum('tipe_sp2hp', ['A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7'])->nullable();
            $table->enum('tingkat_kasus', ['RINGAN', 'SEDANG', 'BERAT'])->nullable();
            
            // Data Pelapor (kept in SP2HP table)
            $table->string('pelapor_nama', 255)->nullable();
            $table->text('pelapor_alamat')->nullable();

            // Data Kendaraan (JSON) - kept for additional vehicle info
            $table->json('kendaraan_data')->nullable();
            
            // Type-specific data (JSON) for different SP2HP types
            $table->json('type_specific_data')->nullable();
            
            // Tindakan data (JSON) for A4 tindakan list
            $table->jsonb('a4_tindakan_list')->nullable();
            $table->jsonb('tindakan_dilakukan')->nullable();
            
            // Pasal dan Barang Bukti
            $table->text('pasal_diduga')->nullable();
            $table->text('barang_bukti')->nullable();
            $table->text('catatan')->nullable();
            
            // Status & Approval
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes with explicit short names
            $table->index('accident_id', 'sp2hp_docs_accident_id_idx');
            $table->index('document_category_id', 'sp2hp_docs_document_category_id_idx');
            $table->index('reporting_person_id', 'sp2hp_docs_reporting_person_id_idx');
            $table->index('nomor_surat', 'sp2hp_docs_nomor_surat_idx');
            $table->index('tipe_sp2hp', 'sp2hp_docs_tipe_sp2hp_idx');
            $table->index('status', 'sp2hp_docs_status_idx');
            $table->index('created_by', 'sp2hp_docs_created_by_idx');
            
            // Foreign keys with explicit short names
            $table->foreign('accident_id', 'sp2hp_docs_accident_id_fk')->references('id')->on('accidents')->onDelete('cascade');
            $table->foreign('document_category_id', 'fk_sp2hp_docs_document_category_id')->references('id')->on('lib.document_categories')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('reporting_person_id', 'sp2hp_docs_reporting_person_id_fk')->references('id')->on('public.reporting_persons')->onDelete('set null');
            $table->foreign('created_by', 'sp2hp_docs_created_by_fk')->references('id')->on('users');
            $table->foreign('updated_by', 'sp2hp_docs_updated_by_fk')->references('id')->on('users');
            $table->foreign('approved_by', 'sp2hp_docs_approved_by_fk')->references('id')->on('users');
        });

        // Clean duplicate signatory entries if officers table exists
        if (Schema::hasTable('doc.surat_pemberitahuan_perkembangan_hasil_penyidikan_officers')) {
            DB::statement("
                DELETE FROM doc.surat_pemberitahuan_perkembangan_hasil_penyidikan_officers a
                USING doc.surat_pemberitahuan_perkembangan_hasil_penyidikan_officers b
                WHERE a.id > b.id
                AND a.sp2hp_document_id = b.sp2hp_document_id
                AND a.class = 'SIGNATORY'
                AND b.class = 'SIGNATORY'
                AND a.officer_id = b.officer_id
                AND a.register_number = b.register_number
            ");
            
            // Standardize data format - update officer_id, rank_id, position_id, police_id to match master data
            DB::statement("
                UPDATE doc.surat_pemberitahuan_perkembangan_hasil_penyidikan_officers AS sp2hp_off
                SET 
                    officer_id = CAST(o.id AS VARCHAR),
                    rank_id = CAST(o.rank_id AS VARCHAR),
                    position_id = CAST(o.position_id AS VARCHAR),
                    police_id = CAST(o.police_id AS VARCHAR),
                    email = COALESCE(sp2hp_off.email, o.email),
                    insert_method = 'IMPORT'
                FROM public.officers AS o
                WHERE sp2hp_off.register_number = o.register_number
                AND sp2hp_off.officer_id IS NULL
            ");
            
            // For entries that already have officer_id but inconsistent data, update to match master
            DB::statement("
                UPDATE doc.surat_pemberitahuan_perkembangan_hasil_penyidikan_officers AS sp2hp_off
                SET 
                    rank_id = CAST(o.rank_id AS VARCHAR),
                    position_id = CAST(o.position_id AS VARCHAR),
                    police_id = CAST(o.police_id AS VARCHAR),
                    email = COALESCE(sp2hp_off.email, o.email)
                FROM public.officers AS o
                WHERE sp2hp_off.officer_id = CAST(o.id AS VARCHAR)
                AND (
                    sp2hp_off.rank_id != CAST(o.rank_id AS VARCHAR)
                    OR sp2hp_off.position_id != CAST(o.position_id AS VARCHAR)
                    OR sp2hp_off.police_id != CAST(o.police_id AS VARCHAR)
                )
            ");
        }
    }

    public function down()
    {
        Schema::dropIfExists('doc.surat_pemberitahuan_perkembangan_hasil_penyidikan_documents');
    }
};