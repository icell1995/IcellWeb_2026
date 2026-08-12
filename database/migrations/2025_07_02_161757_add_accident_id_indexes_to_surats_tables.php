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
        DB::statement('CREATE INDEX IF NOT EXISTS idx_sppd_accident_id ON doc.surat_perintah_penyidikan_documents(accident_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_iw_accident_id ON legacy.investigation_warrants(accident_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_sp_accident_id ON public.surat_penyidikan(accident_id)');
        
        DB::statement('CREATE INDEX IF NOT EXISTS idx_spp_accident_id ON doc.surat_perintah_penyelidikan_documents(accident_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_iol_accident_id ON legacy.investigation_order_letters(accident_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_spy_accident_id ON public.surat_penyelidikan(accident_id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS idx_sppd_accident_id');
        DB::statement('DROP INDEX IF EXISTS idx_iw_accident_id');
        DB::statement('DROP INDEX IF EXISTS idx_sp_accident_id');
        
        DB::statement('DROP INDEX IF EXISTS idx_spp_accident_id');
        DB::statement('DROP INDEX IF EXISTS idx_iol_accident_id');
        DB::statement('DROP INDEX IF EXISTS idx_spy_accident_id');
    }
};
