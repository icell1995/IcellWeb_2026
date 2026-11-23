<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. public.accidents
        if (Schema::hasTable('accidents')) {
            if (Schema::hasColumns('accidents', ['polres_id', 'accident_date', 'selra_flag'])) {
                DB::statement('CREATE INDEX IF NOT EXISTS idx_accidents_polres_date_selra ON public.accidents (polres_id, accident_date, selra_flag)');
            }
            if (Schema::hasColumns('accidents', ['polda_id', 'accident_date', 'selra_flag'])) {
                DB::statement('CREATE INDEX IF NOT EXISTS idx_accidents_polda_date_selra ON public.accidents (polda_id, accident_date, selra_flag)');
            }
            if (Schema::hasColumns('accidents', ['created_at'])) {
                DB::statement('CREATE INDEX IF NOT EXISTS idx_accidents_created_at_desc ON public.accidents (created_at DESC)');
            }
        }

        // 2. public.officers
        if (Schema::hasTable('officers')) {
            if (Schema::hasColumns('officers', ['polda_id', 'polres_id', 'state'])) {
                DB::statement('CREATE INDEX IF NOT EXISTS idx_officers_polda_polres_state ON public.officers (polda_id, polres_id, state)');
            }
            if (Schema::hasColumns('officers', ['user_id'])) {
                DB::statement('CREATE INDEX IF NOT EXISTS idx_officers_user_id ON public.officers (user_id)');
            }
        }

        // 3. public.users
        if (Schema::hasTable('users')) {
            if (Schema::hasColumns('users', ['police_id', 'role_id', 'is_active'])) {
                DB::statement('CREATE INDEX IF NOT EXISTS idx_users_police_role_active ON public.users (police_id, role_id, is_active)');
            }
            if (Schema::hasColumns('users', ['polda_id', 'polres_id', 'state'])) {
                DB::statement('CREATE INDEX IF NOT EXISTS idx_users_polda_polres_state ON public.users (polda_id, polres_id, state)');
            }
        }

        // 4. public.log_case_document_validations
        if (Schema::hasTable('log_case_document_validations')) {
            if (Schema::hasColumns('log_case_document_validations', ['document_category_id', 'updated_status_id', 'approved_at'])) {
                DB::statement('CREATE INDEX IF NOT EXISTS idx_log_case_doc_val_cat_status_approved ON public.log_case_document_validations (document_category_id, updated_status_id, approved_at)');
            }
        }

        // 5. public.dpb & public.dpo
        if (Schema::hasTable('dpb') && Schema::hasColumns('dpb', ['accident_id', 'state'])) {
            DB::statement('CREATE INDEX IF NOT EXISTS idx_dpb_accident_state ON public.dpb (accident_id, state)');
        }
        if (Schema::hasTable('dpo') && Schema::hasColumns('dpo', ['accident_id', 'state'])) {
            DB::statement('CREATE INDEX IF NOT EXISTS idx_dpo_accident_state ON public.dpo (accident_id, state)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS public.idx_accidents_polres_date_selra');
        DB::statement('DROP INDEX IF EXISTS public.idx_accidents_polda_date_selra');
        DB::statement('DROP INDEX IF EXISTS public.idx_accidents_created_at_desc');
        DB::statement('DROP INDEX IF EXISTS public.idx_officers_polda_polres_state');
        DB::statement('DROP INDEX IF EXISTS public.idx_officers_user_id');
        DB::statement('DROP INDEX IF EXISTS public.idx_users_police_role_active');
        DB::statement('DROP INDEX IF EXISTS public.idx_users_polda_polres_state');
        DB::statement('DROP INDEX IF EXISTS public.idx_log_case_doc_val_cat_status_approved');
        DB::statement('DROP INDEX IF EXISTS public.idx_dpb_accident_state');
        DB::statement('DROP INDEX IF EXISTS public.idx_dpo_accident_state');
    }
};

