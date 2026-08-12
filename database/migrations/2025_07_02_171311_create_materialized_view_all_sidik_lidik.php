<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE MATERIALIZED VIEW IF NOT EXISTS mv_all_sidik AS
            SELECT accident_id FROM doc.surat_perintah_penyidikan_documents WHERE deleted_at IS NULL
            UNION
            SELECT accident_id FROM legacy.investigation_warrants
            UNION
            SELECT accident_id FROM public.surat_penyidikan
        ");

        DB::statement("CREATE INDEX IF NOT EXISTS idx_mv_all_sidik_accident_id ON mv_all_sidik(accident_id)");

        // Membuat mv_all_lidik
        DB::statement("
            CREATE MATERIALIZED VIEW IF NOT EXISTS mv_all_lidik AS
            SELECT accident_id FROM doc.surat_perintah_penyelidikan_documents WHERE deleted_at IS NULL
            UNION
            SELECT accident_id FROM legacy.investigation_order_letters
            UNION
            SELECT accident_id FROM public.surat_penyelidikan
        ");

        DB::statement("CREATE INDEX IF NOT EXISTS idx_mv_all_lidik_accident_id ON mv_all_lidik(accident_id)");
    }

    public function down(): void
    {
        DB::statement("DROP MATERIALIZED VIEW IF EXISTS mv_all_sidik");
        DB::statement("DROP MATERIALIZED VIEW IF EXISTS mv_all_lidik");
    }
};
