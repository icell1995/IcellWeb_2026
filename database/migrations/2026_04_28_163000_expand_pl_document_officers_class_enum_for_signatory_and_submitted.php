<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Surat Perintah Penahanan Lanjutan: simpan snapshot officer tambahan (SIGNATORY, SUBMITTED) ke tabel document_officers.
 *
 * Catatan:
 * - Di beberapa DB, kolom `class` dibuat sebagai enum (MySQL) atau CHECK constraint (PostgreSQL).
 * - Migrasi ini memperluas value yang diperbolehkan agar insert tidak gagal.
 */
return new class extends Migration
{
    private string $table = 'doc.perpanjangan_lanjutan_document_officers';
    private array $allowed = ['MEMBER', 'LEADER', 'SIGNATORY', 'SUBMITTED'];

    public function up(): void
    {
        if (! Schema::hasTable($this->table) || ! Schema::hasColumn($this->table, 'class')) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            $vals = implode("','", $this->allowed);
            DB::statement("ALTER TABLE `{$this->table}` MODIFY `class` ENUM('{$vals}') NULL DEFAULT 'MEMBER'");
            return;
        }

        if ($driver === 'pgsql') {
            // Laravel enum on pgsql biasanya jadi CHECK constraint bernama "<table>_<column>_check".
            DB::statement('ALTER TABLE '.$this->table.' DROP CONSTRAINT IF EXISTS perpanjangan_lanjutan_document_officers_class_check');
            DB::statement('ALTER TABLE '.$this->table.' DROP CONSTRAINT IF EXISTS doc_perpanjangan_lanjutan_document_officers_class_check');
            DB::statement('ALTER TABLE '.$this->table.' ALTER COLUMN class TYPE varchar(50)');

            $in = implode("','", $this->allowed);
            DB::statement(
                "ALTER TABLE {$this->table} ADD CONSTRAINT chk_pl_doc_officers_class CHECK (class IN ('{$in}'))"
            );
            return;
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable($this->table) || ! Schema::hasColumn($this->table, 'class')) {
            return;
        }

        $driver = DB::getDriverName();
        $allowed = ['MEMBER', 'LEADER'];

        if ($driver === 'mysql') {
            $vals = implode("','", $allowed);
            DB::statement("ALTER TABLE `{$this->table}` MODIFY `class` ENUM('{$vals}') NULL DEFAULT 'MEMBER'");
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE '.$this->table.' DROP CONSTRAINT IF EXISTS chk_pl_doc_officers_class');
            $in = implode("','", $allowed);
            DB::statement(
                "ALTER TABLE {$this->table} ADD CONSTRAINT chk_pl_doc_officers_class CHECK (class IN ('{$in}'))"
            );
            return;
        }
    }
};

