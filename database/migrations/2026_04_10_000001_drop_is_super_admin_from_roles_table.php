<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hapus kolom is_super_admin dari tabel lib.roles.
     * Super Admin tidak lagi digunakan dalam sistem — akses dikontrol penuh oleh RBAC permission.
     */
    public function up(): void
    {
        Schema::table('lib.roles', function (Blueprint $table) {
            if (Schema::hasColumn('lib.roles', 'is_super_admin')) {
                $table->dropColumn('is_super_admin');
            }
        });
    }

    /**
     * Kembalikan kolom jika rollback diperlukan.
     */
    public function down(): void
    {
        Schema::table('lib.roles', function (Blueprint $table) {
            $table->boolean('is_super_admin')->default(false)->after('level');
        });
    }
};
