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
        $tables = [
            'doc.daftar_pencarian_barang_documents',
            'doc.surat_penunjukan_penasihat_hukum_documents',
            'doc.surat_permintaan_buka_blokir_rekening_bank_documents',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $tableBlueprint) use ($table) {
                    if (!Schema::hasColumn($table, 'deleted_by_user_id')) {
                        $tableBlueprint->unsignedBigInteger('deleted_by_user_id')->nullable()->after('updated_by_user_id');
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'doc.daftar_pencarian_barang_documents',
            'doc.surat_penunjukan_penasihat_hukum_documents',
            'doc.surat_permintaan_buka_blokir_rekening_bank_documents',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $tableBlueprint) use ($table) {
                    if (Schema::hasColumn($table, 'deleted_by_user_id')) {
                        $tableBlueprint->dropColumn('deleted_by_user_id');
                    }
                });
            }
        }
    }
};

