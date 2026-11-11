<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddAttendancesToDocLhgpTable extends Migration
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
            Schema::table('doc.laporan_hasil_gelar_perkara_documents', function (Blueprint $table) {
                if (!Schema::hasColumn('doc.laporan_hasil_gelar_perkara_documents', 'attendances')) {
                    $table->json('attendances')->nullable()->after('attendees')->comment('Daftar nama dan jabatan peserta gelar');
                }
            });

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
            Schema::table('doc.laporan_hasil_gelar_perkara_documents', function (Blueprint $table) {
                if (Schema::hasColumn('doc.laporan_hasil_gelar_perkara_documents', 'attendances')) {
                    $table->dropColumn('attendances');
                }
            });

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
