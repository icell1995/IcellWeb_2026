<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('accident_resolutions', function (Blueprint $table) {
            if (!Schema::hasColumn('accident_resolutions','approved_at')) {
                $table->dateTime('approved_at')->nullable()->index();
            }
            // 1 LP = 1 SELRA
            $table->unique('accident_id', 'uniq_accident_resolutions_accident_id');
        });
    }

    public function down(): void
    {
        Schema::table('accident_resolutions', function (Blueprint $table) {
            if (Schema::hasColumn('accident_resolutions','approved_at')) {
                $table->dropColumn('approved_at');
            }
            $table->dropUnique('uniq_accident_resolutions_accident_id');
        });
    }
};

