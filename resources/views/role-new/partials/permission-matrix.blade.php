@php
    // Konfigurasi modul — disesuaikan dengan kebutuhan nyata project iCell
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

        // ── STATISTIKA / ANEV ────────────────────────────────────────────────
        ['code' => 'statistics',       'name' => 'Statistika/Anev → Lap Bulanan/Mingguan/Harian', 'perms' => ['R', 'E']],
        ['code' => 'report-individu',  'name' => 'Statistika/Anev → Lap Individu',            'perms' => ['R']],
        ['code' => 'anev',             'name' => 'Statistika/Anev → Anev',                    'perms' => ['R', 'E']],

        // ── DPO / DPB ────────────────────────────────────────────────────────
        ['code' => 'dpo',              'name' => 'DPO / DPB → DPO',                           'perms' => ['R']],
        ['code' => 'dpb',              'name' => 'DPO / DPB → DPB',                           'perms' => ['R']],

        // ── KATEGORI ─────────────────────────────────────────────────────────
        ['code' => 'category-info',    'name' => 'Kategori → Layanan Info',                    'perms' => ['R']],
        ['code' => 'territory',        'name' => 'Kategori → Daftar Wilayah',                  'perms' => ['R']],
        ['code' => 'organization',     'name' => 'Kategori → Struktur Organisasi',             'perms' => ['R', 'D']],

        // ── MENU KHUSUS ──────────────────────────────────────────────────────
        ['code' => 'tutorial',         'name' => 'Tutorial ICELL',                             'perms' => ['R']],
        ['code' => 'commander-wish',   'name' => 'Commander Wish',                             'perms' => ['R', 'E', 'DN']],

        // ── KATALOG → DAFTAR ─────────────────────────────────────────────────
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

        // ── CMS & INTEGRASI ──────────────────────────────────────────────────
        ['code' => 'cms',                  'name' => 'CMS',                                   'perms' => ['R', 'C', 'U', 'D', 'I', 'E', 'DN', 'UP']],
        ['code' => 'irsms',                'name' => 'IRSMS',                                 'perms' => ['R']],
    ];

    $allPerms    = ['R', 'C', 'U', 'D', 'I', 'E', 'DN', 'UP'];
    $isReadOnly   = isset($isReadOnly) && $isReadOnly;
    $disabledAttr = $isReadOnly ? 'disabled' : '';
@endphp

<hr class="mt-5 mb-4">
<h5 class="fw-semibold text-blue-dark mb-3">Permission Matrix</h5>
<p class="text-muted small mb-2 fw-semibold text-primary">R: Read, C: Create, U: Update, D: Delete, I: Import, E: Export, DN: Download, UP: Upload</p>
<div class="table-responsive" style="max-height: 60vh; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 5px;">
    <table class="table table-bordered table-hover align-middle mb-0">
        <thead style="position: sticky; top: 0; z-index: 10; background-color: #2F4288; color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <tr>
                <th class="text-center align-middle" rowspan="2" width="5%" style="background-color: #2F4288;">No</th>
                <th class="align-middle" rowspan="2" width="15%" style="background-color: #2F4288;">Code</th>
                <th class="align-middle" rowspan="2" width="25%" style="background-color: #2F4288;">Module Name</th>
                <th class="text-center align-middle" rowspan="2" width="5%" style="background-color: #2F4288;">
                    Have<br>Access<br>
                    <input class="form-check-input mt-2" type="checkbox" id="check-all-access" {{ $disabledAttr }} title="Check All">
                </th>
                <th class="text-center border-bottom-0" colspan="8" style="background-color: #2F4288;">Permissions</th>
            </tr>
            <tr>
                @foreach($allPerms as $p)
                <th class="text-center border-top-0" width="5%" style="background-color: #2F4288;">{{ $p }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($modules as $index => $mod)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><code class="text-primary">{{ $mod['code'] }}</code></td>
                <td class="fw-semibold text-dark">{{ $mod['name'] }}</td>
                <td class="text-center">
                    <input class="form-check-input dummy-input matrix-access" type="checkbox" {{ $disabledAttr }}>
                </td>
                @foreach($allPerms as $p)
                <td class="text-center">
                    @if(in_array($p, $mod['perms']))
                        @php
                            $permString = $mod['code'] . '.' . $p;
                            // Checked hanya berdasarkan data database ($rolePermissions)
                            $isChecked = (isset($rolePermissions) && in_array($permString, $rolePermissions)) ? 'checked' : '';
                        @endphp
                        <input name="permissions[]" value="{{ $permString }}" class="form-check-input matrix-perm" type="checkbox" {{ $isChecked }} {{ $disabledAttr }}>
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const accessCheckboxes = document.querySelectorAll('.matrix-access');
        const permCheckboxes = document.querySelectorAll('.matrix-perm');

        // === INISIALISASI AWAL: Sinkronisasi "Have Access" berdasarkan permission yang sudah tercentang ===
        accessCheckboxes.forEach(function(accessCb) {
            const tr = accessCb.closest('tr');
            if (!tr) return;

            // Cek semua matrix-perm di baris ini (termasuk yang disabled)
            const permsInRow = tr.querySelectorAll('.matrix-perm');
            let anyChecked = false;
            permsInRow.forEach(function(cb) {
                if (cb.checked) anyChecked = true;
            });
            accessCb.checked = anyChecked;
        });

        // Master toggle logic (Have Access clicked)
        accessCheckboxes.forEach(function(accessCb) {
            accessCb.addEventListener('change', function(e) {
                if(e.detail && e.detail.programmatic) return;

                const tr = this.closest('tr');
                if (!tr) return;

                const permsInRow = tr.querySelectorAll('.matrix-perm:not(:disabled)');
                permsInRow.forEach(function(permCb) {
                    permCb.checked = accessCb.checked;
                    permCb.dispatchEvent(new CustomEvent('change', { detail: { programmatic: true } }));
                });
            });
        });

        // Child toggle logic (Granular clicked)
        permCheckboxes.forEach(function(permCb) {
            permCb.addEventListener('change', function(e) {
                if(e.detail && e.detail.programmatic) return;

                const tr = this.closest('tr');
                if (!tr) return;

                const allPermsInRow = tr.querySelectorAll('.matrix-perm:not(:disabled)');
                const accessCb = tr.querySelector('.matrix-access');
                
                if (accessCb && allPermsInRow.length > 0) {
                    let anyChecked = false;
                    allPermsInRow.forEach(function(cb) {
                        if (cb.checked) anyChecked = true;
                    });
                    accessCb.checked = anyChecked;
                }
            });
        });

        // Master Check All / Uncheck All via icon checkbox
        const checkAllAccess = document.getElementById('check-all-access');
        if (checkAllAccess && !checkAllAccess.disabled) {
            checkAllAccess.addEventListener('change', function() {
                const isCheckingAll = this.checked;

                // Apply to children items
                permCheckboxes.forEach(function(cb) {
                    if (!cb.disabled) {
                        cb.checked = isCheckingAll;
                    }
                });
                
                // Apply to "Have Access" parent item
                accessCheckboxes.forEach(function(cb) {
                    if (!cb.disabled) {
                        cb.checked = isCheckingAll;
                    }
                });
            });

            // Sinkronisasi otomatis ke checkAllAccess jika semua accessCheckboxes tercentang manual
            function syncMasterCheckbox() {
                let allChecked = true;
                let anyUnchecked = false;
                accessCheckboxes.forEach(function(cb) {
                    if (!cb.disabled) {
                        if (!cb.checked) {
                            allChecked = false;
                            anyUnchecked = true;
                        }
                    }
                });
                
                if (anyUnchecked) {
                    checkAllAccess.checked = false;
                } else if (allChecked && accessCheckboxes.length > 0) {
                    checkAllAccess.checked = true;
                }
            }

            // Bind ini saat granular ada perubahan
            permCheckboxes.forEach(function(permCb) {
                permCb.addEventListener('change', syncMasterCheckbox);
            });
            accessCheckboxes.forEach(function(accessCb) {
                accessCb.addEventListener('change', syncMasterCheckbox);
            });
            
            // Initial sync
            syncMasterCheckbox();
        }
    });
</script>
