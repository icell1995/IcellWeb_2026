<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar modul permission — sinkron dengan permission-matrix.blade.php
        $modules = [
            // ── ANGGOTA ─────────────────────────────────────────────────────
            ['code' => 'personnel',        'perms' => ['R', 'C', 'U']],
            ['code' => 'signatories',      'perms' => ['R']],
            ['code' => 'certification',    'perms' => ['R']],

            // ── MANAJEMEN AKSES ──────────────────────────────────────────────
            ['code' => 'permission',       'perms' => ['R']],
            ['code' => 'role',             'perms' => ['R', 'C', 'U']],

            // ── LAPORAN ──────────────────────────────────────────────────────
            ['code' => 'accident',         'perms' => ['R', 'U']],
            ['code' => 'case',             'perms' => ['R', 'U', 'E']],
            ['code' => 'productivity',     'perms' => ['R']],
            ['code' => 'productivity-lp',  'perms' => ['R', 'C', 'U', 'D']],
            ['code' => 'recap',            'perms' => ['R']],

            // ── DOKUMEN TTE & PERSETUJUAN ───────────────────────────────────
            ['code' => 'document-signature-verif', 'perms' => ['R', 'U']],
            ['code' => 'document-signature',       'perms' => ['R', 'U']],
            ['code' => 'document-approval',        'perms' => ['R', 'U']],
            ['code' => 'document-approval-upload', 'perms' => ['R', 'U', 'UP']],

            // ── STATISTIKA / ANEV ────────────────────────────────────────────
            ['code' => 'statistics',       'perms' => ['R', 'E']],
            ['code' => 'report-individu',  'perms' => ['R']],
            ['code' => 'anev',             'perms' => ['R', 'E']],

            // ── DPO / DPB ────────────────────────────────────────────────────
            ['code' => 'dpo',              'perms' => ['R']],
            ['code' => 'dpb',              'perms' => ['R']],

            // ── KATEGORI ─────────────────────────────────────────────────────
            ['code' => 'category-info',    'perms' => ['R']],
            ['code' => 'territory',        'perms' => ['R']],
            ['code' => 'organization',     'perms' => ['R', 'D']],

            // ── MENU KHUSUS ──────────────────────────────────────────────────
            ['code' => 'tutorial',         'perms' => ['R']],
            ['code' => 'commander-wish',   'perms' => ['R', 'E', 'DN']],

            // ── KATALOG → DAFTAR ─────────────────────────────────────────────
            ['code' => 'catalog-pangkat',            'perms' => ['R']],
            ['code' => 'catalog-kerusakan',          'perms' => ['R']],
            ['code' => 'catalog-kondisi-cahaya',     'perms' => ['R']],
            ['code' => 'catalog-pendidikan',         'perms' => ['R']],
            ['code' => 'catalog-pengaturan-simpang', 'perms' => ['R']],
            ['code' => 'catalog-tipe-kecelakaan',    'perms' => ['R']],
            ['code' => 'catalog-titik-acuan',        'perms' => ['R']],

            // ── KATALOG → DOKUMEN ────────────────────────────────────────────
            ['code' => 'catalog-saksi',          'perms' => ['R']],
            ['code' => 'catalog-tersangka',      'perms' => ['R']],
            ['code' => 'catalog-penahanan',      'perms' => ['R']],
            ['code' => 'catalog-penggeledahan',  'perms' => ['R']],
            ['code' => 'catalog-penyitaan',      'perms' => ['R']],
            ['code' => 'catalog-penyegelan',     'perms' => ['R']],
            ['code' => 'catalog-labfor',         'perms' => ['R']],
            ['code' => 'catalog-rekening-bank',  'perms' => ['R']],
            ['code' => 'catalog-dpo-dpb',        'perms' => ['R']],

            // ── KATALOG LAINNYA ──────────────────────────────────────────────
            ['code' => 'catalog-polda',  'perms' => ['R']],
            ['code' => 'catalog-polres', 'perms' => ['R']],

            // ── CMS & INTEGRASI ──────────────────────────────────────────────
            ['code' => 'cms',    'perms' => ['R', 'C', 'U', 'D', 'I', 'E', 'DN', 'UP']],
            ['code' => 'irsms',  'perms' => ['R']],
            ['code' => 'carousel',         'perms' => ['R', 'C', 'U', 'D']],
            ['code' => 'pulse',            'perms' => ['R']],
            ['code' => 'document-action',  'perms' => ['R', 'U']],
        ];

        // RESET TOTAL TABLE PERMISSIONS & PIVOT
        // Karena pakai PostgreSQL, gunakan TRUNCATE dengan RESTART IDENTITY dan CASCADE
        \Illuminate\Support\Facades\DB::statement('TRUNCATE TABLE permission_role, permissions RESTART IDENTITY CASCADE;');

        $insertData = [];
        foreach ($modules as $mod) {
            foreach ($mod['perms'] as $perm) {
                $insertData[] = [
                    'name' => $mod['code'] . '.' . $perm,
                    'state' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }

        // Insert sekaligus agar lebih cepat dan ID berurutan cantik (1, 2, 3...)
        \Illuminate\Support\Facades\DB::table('permissions')->insert($insertData);

        // ─────────────────────────────────────────────────────────────────────
        // AUTO-CREATE & ASSIGN PERMISSION UNTUK ROLE LEVEL 2 (BUILT-IN SYSTEM)
        // Role ini bersifat mutlak — dibuat otomatis setiap kali seeder dijalankan.
        // Mendapat semua permission KECUALI 4 modul dokumen TTE/Persetujuan.
        // ─────────────────────────────────────────────────────────────────────
        $excludedPrefixes = [
            'document-signature-verif',
            'document-signature',
            'document-approval',
            'document-approval-upload',
        ];

        // ── ROLE LEVEL 2 — ID MUTLAK = 2 ────────────────────────────────────
        // Selalu pastikan role ini ada dengan ID=2 dan data sesuai seeder.
        // Jika ID=2 sudah ada → UPDATE semua field agar sinkron dengan seeder.
        // Jika ID=2 belum ada → INSERT baru dengan ID=2.
        // ─────────────────────────────────────────────────────────────────────
        \Illuminate\Support\Facades\DB::table('lib.roles')->updateOrInsert(
            ['id' => 2], // ← kunci: ID selalu 2
            [
                'name'        => 'HELPDESK',
                'level'       => 2,
                'description' => 'Helpdesk — akses penuh kecuali modul dokumen TTE & Persetujuan. (Built-in, tidak dapat dimodifikasi)',
                'state'       => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        );

        $roleLevel2 = \Illuminate\Support\Facades\DB::table('lib.roles')->where('id', 2)->first();

        // Assign semua permission kecuali yang dikecualikan
        \Illuminate\Support\Facades\DB::table('permission_role')->where('role_id', $roleLevel2->id)->delete();

        $permissionsForLevel2 = \Illuminate\Support\Facades\DB::table('permissions')
            ->get()
            ->filter(function ($perm) use ($excludedPrefixes) {
                foreach ($excludedPrefixes as $prefix) {
                    if (str_starts_with($perm->name, $prefix . '.')) {
                        return false;
                    }
                }
                return true;
            });

        $pivotData = $permissionsForLevel2->map(fn($p) => [
            'role_id'       => $roleLevel2->id,
            'permission_id' => $p->id,
        ])->values()->toArray();

        if (!empty($pivotData)) {
            \Illuminate\Support\Facades\DB::table('permission_role')->insert($pivotData);
        }
    }
}
