@extends('layouts.app')

@section('content')
@php
    $modules = [
        // ── ANGGOTA ─────────────────────────────────────────────────────────
        ['code' => 'personnel',        'name' => 'Anggota → Personel',                        'perms' => ['R', 'C', 'U']],
        ['code' => 'signatories',      'name' => 'Anggota → Pejabat TTE',                     'perms' => ['R']],
        ['code' => 'certification',    'name' => 'Anggota → Sertifikasi Personel',             'perms' => ['R']],

        // ── MANAJEMEN AKSES ──────────────────────────────────────────────────
        ['code' => 'permission',       'name' => 'Manajemen Akses → Hak Akses',                'perms' => ['R']],
        ['code' => 'role',             'name' => 'Manajemen Akses → Role',                     'perms' => ['R', 'C', 'U']],

        // ── LAPORAN ──────────────────────────────────────────────────────────
        ['code' => 'accident',         'name' => 'Laporan → Register Perkara Laka',            'perms' => ['R', 'U']],
        ['code' => 'case',             'name' => 'Laporan → Register Perkara Jatanlin',        'perms' => ['R', 'U', 'E']],
        ['code' => 'productivity',     'name' => 'Laporan → Perkara Ditangani',                'perms' => ['R']],
        ['code' => 'productivity-lp',  'name' => 'Laporan → Perkara Ditangani → LP',          'perms' => ['R', 'C', 'U', 'D']],
        ['code' => 'recap',            'name' => 'Laporan → Rekap',                            'perms' => ['R']],

        // ── DOKUMEN TTE & PERSETUJUAN ───────────────────────────────────────
        ['code' => 'document-signature-verif', 'name' => 'Dokumen → Tanda Tangan TTE (Verifikasi)', 'perms' => ['R', 'U']],
        ['code' => 'document-signature',       'name' => 'Dokumen → Tanda Tangan TTE',             'perms' => ['R', 'U']],
        ['code' => 'document-approval',        'name' => 'Dokumen → Persetujuan Dokumen',          'perms' => ['R', 'U']],
        ['code' => 'document-approval-upload', 'name' => 'Dokumen → Persetujuan Dokumen (Upload)', 'perms' => ['R', 'U', 'UP']],
        ['code' => 'document-action',          'name' => 'Dokumen → Tindakan Dokumen (Approval/Upload Request)', 'perms' => ['R', 'U']],

        // ── STATISTIKA / ANEV ────────────────────────────────────────────────
        ['code' => 'statistics',       'name' => 'Statistika/Anev → Lap Bulanan/Mingguan/Harian', 'perms' => ['R', 'E']],
        ['code' => 'report-individu',  'name' => 'Statistika/Anev → Lap Individu',            'perms' => ['R']],
        ['code' => 'anev',             'name' => 'Statistika/Anev → Anev',                    'perms' => ['R', 'E']],

        // ── DPO / DPB ────────────────────────────────────────────────────────
        ['code' => 'dpo',              'name' => 'DPO / DPB → DPO',                           'perms' => ['R']],
        ['code' => 'dpb',              'name' => 'DPO / DPB → DPB',                           'perms' => ['R']],

        // ── KATEGORI ─────────────────────────────────────────────────────────
        /*
        ['code' => 'category-info',    'name' => 'Kategori → Layanan Info',                    'perms' => ['R']],
        ['code' => 'territory',        'name' => 'Kategori → Daftar Wilayah',                  'perms' => ['R']],
        ['code' => 'organization',     'name' => 'Kategori → Struktur Organisasi',             'perms' => ['R', 'D']],
        */

        // ── MENU KHUSUS ──────────────────────────────────────────────────────
        ['code' => 'tutorial',         'name' => 'Tutorial ICELL',                             'perms' => ['R']],
        ['code' => 'commander-wish',   'name' => 'Commander Wish',                             'perms' => ['R', 'E', 'DN']],

        // ── KATALOG → DAFTAR ─────────────────────────────────────────────────
        /*
        ['code' => 'catalog-pangkat',           'name' => 'Katalog → Daftar → Pangkat',              'perms' => ['R']],
        ['code' => 'catalog-kerusakan',         'name' => 'Katalog → Daftar → Kerusakan',            'perms' => ['R']],
        ['code' => 'catalog-kondisi-cahaya',    'name' => 'Katalog → Daftar → Kondisi Cahaya',       'perms' => ['R']],
        ['code' => 'catalog-pendidikan',        'name' => 'Katalog → Daftar → Pendidikan',           'perms' => ['R']],
        ['code' => 'catalog-pengaturan-simpang','name' => 'Katalog → Daftar → Pengaturan Simpang',   'perms' => ['R']],
        ['code' => 'catalog-tipe-kecelakaan',   'name' => 'Katalog → Daftar → Tipe Kecelakaan',      'perms' => ['R']],
        ['code' => 'catalog-titik-acuan',       'name' => 'Katalog → Daftar → Titik Acuan',          'perms' => ['R']],

        // ── KATALOG → DOKUMEN ────────────────────────────────────────────────
        ['code' => 'catalog-saksi',         'name' => 'Katalog → Dokumen → Saksi',            'perms' => ['R']],
        ['code' => 'catalog-tersangka',     'name' => 'Katalog → Dokumen → Tersangka',        'perms' => ['R']],
        ['code' => 'catalog-penahanan',     'name' => 'Katalog → Dokumen → Penahanan',        'perms' => ['R']],
        ['code' => 'catalog-penggeledahan', 'name' => 'Katalog → Dokumen → Penggeledahan',    'perms' => ['R']],
        ['code' => 'catalog-penyitaan',     'name' => 'Katalog → Dokumen → Penyitaan',        'perms' => ['R']],
        ['code' => 'catalog-penyegelan',    'name' => 'Katalog → Dokumen → Penyegelan',       'perms' => ['R']],
        ['code' => 'catalog-labfor',        'name' => 'Katalog → Dokumen → Labfor',           'perms' => ['R']],
        ['code' => 'catalog-rekening-bank', 'name' => 'Katalog → Dokumen → Rekening Bank',    'perms' => ['R']],
        ['code' => 'catalog-dpo-dpb',       'name' => 'Katalog → Dokumen → DPO / DPB',       'perms' => ['R']],

        // ── KATALOG LAINNYA ──────────────────────────────────────────────────
        ['code' => 'catalog-polda',        'name' => 'Katalog → Polda',                       'perms' => ['R']],
        ['code' => 'catalog-polres',       'name' => 'Katalog → Polres',                      'perms' => ['R']],
        */

        // ── CMS & INTEGRASI ──────────────────────────────────────────────────
        ['code' => 'cms',                  'name' => 'CMS',                                   'perms' => ['R', 'C', 'U', 'D', 'I', 'E', 'DN', 'UP']],
        ['code' => 'irsms',                'name' => 'IRSMS',                                 'perms' => ['R']],
        ['code' => 'carousel',             'name' => 'CMS → Carousel Slider',                 'perms' => ['R', 'C', 'U', 'D']],
        ['code' => 'pulse',                'name' => 'CMS → Dashboard Pulse',                 'perms' => ['R']],
    ];

    $labelMap = [
        'R'  => 'Read',
        'C'  => 'Create',
        'U'  => 'Update',
        'D'  => 'Delete',
        'I'  => 'Import',
        'E'  => 'Export',
        'DN' => 'Download',
        'UP' => 'Upload'
    ];

    $colorMap = [
        'R'  => 'primary',
        'C'  => 'success',
        'U'  => 'warning text-dark',
        'D'  => 'danger',
        'I'  => 'info text-dark',
        'E'  => 'dark',
        'DN' => 'secondary',
        'UP' => 'secondary'
    ];
@endphp

    <div class="box">
        <div class="box-header">
            <h3 class="text-blue-dark fw-semibold mb-2">Informasi Hak Akses</h3>
        </div>
        <div class="box-body">
            <div class="table-responsive mt-3" style="max-height: 70vh; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 5px;">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead style="background-color: #2F4288; color:#fff; position: sticky; top: 0; z-index: 10;">
                        <tr>
                            <th class="text-center" width="5%" style="background-color: #2F4288;">No</th>
                            <th width="35%" style="background-color: #2F4288;">Module Name</th>
                            <th width="60%" style="background-color: #2F4288;">Hak Akses Tersedia (Have Access)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($modules as $index => $mod)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="fw-semibold text-dark">{{ $mod['name'] }}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($mod['perms'] as $perm)
                                            <span class="badge bg-{{ $colorMap[$perm] ?? 'secondary' }} p-2" style="font-size: 0.85rem; font-weight: 500;">
                                                <i class="bi bi-check2-circle me-1"></i> {{ $labelMap[$perm] ?? $perm }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- 
    ========================================================
    KODE LAMA (DISIMPAN AGAR TIDAK HILANG)
    ========================================================
    <div class="box">
        <div class="box-header">
            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif
            <h3 class="text-blue-dark fw-semibold mb-2">Daftar Hak Akses</h3>
        </div>
        <div class="box-body">
            <div class="table-responsive mt-3">
                <table class="table table-bordered table-officer" width="100%" id="dataTable" name="dataTable">
                    <thead>
                        <tr>
                            <th class="text-center align-middle" width="5%">No</th>
                            <th class="text-center align-middle">Hak Akses</th>
                            <th class="text-center align-middle">Action</th>
                        </tr>
                    </thead>
                    <?php $no = 0; ?>

                    @foreach ($permission as $permissions)
                        <?php $no++; ?>
                        <tbody>
                            <tr>
                                <td class="text-center" scope="row">{{ $no }}</td>
                                <td>{{ $permissions->name }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-secondary btn-edit" data-bs-toggle="modal"
                                        data-bs-target="#edit-data" data-id="{{ $permissions->id }}">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    @endforeach
                </table>
            </div>
            <div class="text-end">
                <button type="button" class="material-icons floating-btn" data-bs-toggle="modal"
                    data-bs-target="#add-data">add</button>
            </div>
        </div>

        @include('permission.modal.permission-modal-add')
        @include('permission.modal.permission-modal-edit')
    </div>
    --}}
@endsection
