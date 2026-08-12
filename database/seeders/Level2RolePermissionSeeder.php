<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder khusus untuk role level 2.
 *
 * Role level 2 mendapat SEMUA permission KECUALI 4 modul dokumen:
 *  - document-signature-verif
 *  - document-signature
 *  - document-approval
 *  - document-approval-upload
 *
 * Seeder ini AMAN dijalankan berulang kali (idempotent) karena
 * ia hanya menghapus & mengganti permission untuk role level 2 saja,
 * tanpa menyentuh role lain.
 *
 * Cara menjalankan:
 *   php artisan db:seed --class=Level2RolePermissionSeeder
 */
class Level2RolePermissionSeeder extends Seeder
{
    // Permission yang TIDAK boleh dimiliki role level 2
    private array $excludedPrefixes = [
        'document-signature-verif',
        'document-signature',
        'document-approval',
        'document-approval-upload',
    ];

    public function run(): void
    {
        $roleLevel2 = DB::table('lib.roles')->where('level', 2)->first();

        if (!$roleLevel2) {
            $this->command->warn('Role dengan level 2 tidak ditemukan. Seeder dibatalkan.');
            return;
        }

        $this->command->info("Memproses role: [{$roleLevel2->id}] {$roleLevel2->name} (Level 2)");

        // Hapus semua permission lama untuk role ini
        DB::table('permission_role')->where('role_id', $roleLevel2->id)->delete();

        // Ambil semua permission dari database kecuali yang dikecualikan
        $allPermissions = DB::table('permissions')->get();

        $filtered = $allPermissions->filter(function ($perm) {
            foreach ($this->excludedPrefixes as $prefix) {
                if (str_starts_with($perm->name, $prefix . '.')) {
                    return false; // Eksklusikan
                }
            }
            return true;
        });

        $pivotData = $filtered->map(fn($p) => [
            'role_id'       => $roleLevel2->id,
            'permission_id' => $p->id,
        ])->values()->toArray();

        if (empty($pivotData)) {
            $this->command->warn('Tidak ada permission yang diassign.');
            return;
        }

        DB::table('permission_role')->insert($pivotData);

        $this->command->info("✅ Berhasil assign {$filtered->count()} permission ke role level 2.");
        $this->command->line("   Dikecualikan: " . implode(', ', $this->excludedPrefixes));
    }
}
