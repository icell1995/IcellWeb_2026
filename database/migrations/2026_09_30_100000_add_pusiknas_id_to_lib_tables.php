<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom pusiknas_id ke tabel lib yang terintegrasi dengan Pusiknas.
     * Kolom ini menyimpan nilai Kode dari Kode Perorangan Pusiknas (IdPuskarda).
     * Menggunakan DB::statement karena tabel berada di schema 'lib'.
     */
    public function up(): void
    {
        $tables = [
            'lib.genders',
            'lib.educations',
            'lib.religions',
            'lib.marital_statuses',
            'lib.identity_types',
            'lib.jobs',
        ];

        foreach ($tables as $qualifiedTable) {
            $exists = DB::select("
                SELECT column_name FROM information_schema.columns
                WHERE table_schema = ? AND table_name = ? AND column_name = 'pusiknas_id'
            ", explode('.', $qualifiedTable));

            if (empty($exists)) {
                DB::statement("ALTER TABLE {$qualifiedTable} ADD COLUMN pusiknas_id INTEGER DEFAULT NULL");
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'lib.genders',
            'lib.educations',
            'lib.religions',
            'lib.marital_statuses',
            'lib.identity_types',
            'lib.jobs',
        ];

        foreach ($tables as $qualifiedTable) {
            $exists = DB::select("
                SELECT column_name FROM information_schema.columns
                WHERE table_schema = ? AND table_name = ? AND column_name = 'pusiknas_id'
            ", explode('.', $qualifiedTable));

            if (!empty($exists)) {
                DB::statement("ALTER TABLE {$qualifiedTable} DROP COLUMN IF EXISTS pusiknas_id");
            }
        }
    }
};
