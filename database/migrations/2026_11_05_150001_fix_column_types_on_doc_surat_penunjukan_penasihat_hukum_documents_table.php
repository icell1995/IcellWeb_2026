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
        DB::statement('ALTER TABLE "doc"."surat_penunjukan_penasihat_hukum_documents" ALTER COLUMN "created_by_user_id" TYPE bigint USING NULL;');
        DB::statement('ALTER TABLE "doc"."surat_penunjukan_penasihat_hukum_documents" ALTER COLUMN "updated_by_user_id" TYPE bigint USING NULL;');
        DB::statement('ALTER TABLE "doc"."surat_penunjukan_penasihat_hukum_documents" ALTER COLUMN "contact_officer_id" TYPE character varying USING contact_officer_id::text;');
        DB::statement('ALTER TABLE "doc"."surat_penunjukan_penasihat_hukum_documents" ALTER COLUMN "signatory_id" TYPE character varying USING signatory_id::text;');
        DB::statement('ALTER TABLE "doc"."surat_penunjukan_penasihat_hukum_documents" ALTER COLUMN "suspect_id" TYPE character varying USING suspect_id::text;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};

