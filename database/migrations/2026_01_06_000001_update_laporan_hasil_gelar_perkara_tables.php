<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateLaporanHasilGelarPerkaraTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::beginTransaction();
        try {
            // Add new columns to doc.laporan_hasil_gelar_perkara_documents
            Schema::table('doc.laporan_hasil_gelar_perkara_documents', function (Blueprint $table) {
                $table->string('implementer')->nullable()->after('attendees')->comment('Pelaksana');
                $table->string('litigated')->nullable()->after('implementer')->comment('Pihak yang berperkara');
                $table->text('attendance_list')->nullable()->after('litigated')->comment('Daftar peserta gelar');
            });

            // Update check constraint on doc.laporan_hasil_gelar_perkara_document_officers to include NOTULEN
            DB::statement('ALTER TABLE doc.laporan_hasil_gelar_perkara_document_officers DROP CONSTRAINT IF EXISTS laporan_hasil_gelar_perkara_document_officers_class_check');
            DB::statement("ALTER TABLE doc.laporan_hasil_gelar_perkara_document_officers ADD CONSTRAINT laporan_hasil_gelar_perkara_document_officers_class_check CHECK (class::text = ANY (ARRAY['MEMBER'::character varying, 'LEADER'::character varying, 'SIGNATORY'::character varying, 'NOTULEN'::character varying]::text[]))");

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::beginTransaction();
        try {
            // Drop columns from doc.laporan_hasil_gelar_perkara_documents
            Schema::table('doc.laporan_hasil_gelar_perkara_documents', function (Blueprint $table) {
                $table->dropColumn(['implementer', 'litigated', 'attendance_list']);
            });

            // Revert check constraint on doc.laporan_hasil_gelar_perkara_document_officers (remove NOTULEN)
            DB::statement('ALTER TABLE doc.laporan_hasil_gelar_perkara_document_officers DROP CONSTRAINT IF EXISTS laporan_hasil_gelar_perkara_document_officers_class_check');
            DB::statement("ALTER TABLE doc.laporan_hasil_gelar_perkara_document_officers ADD CONSTRAINT laporan_hasil_gelar_perkara_document_officers_class_check CHECK (class::text = ANY (ARRAY['MEMBER'::character varying, 'LEADER'::character varying, 'SIGNATORY'::character varying]::text[]))");

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
