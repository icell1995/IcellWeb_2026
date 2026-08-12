@php
    $userPolice = Auth::user()->police;
@endphp

<style>
.dropdown-arrow {
    transition: transform 0.3s ease;
    transform: rotate(0deg);
    display: inline-block;
    position: absolute;
    right: 15px;
}

/* Rotate down when menu is active or expanded */
.rotate-down {
    transform: rotate(90deg);
}

/* Adding smooth transition */
.collapse {
    transition: all 0.3s ease;
}

.collapse .parents-menu.active, 
[aria-expanded="true"] .dropdown-arrow {
    transform: rotate(0deg);
}

/* Parent menu hover effect */
.parents-menu:hover .dropdown-arrow {
    transform: translateX(3px);
}

.parents-menu.active .dropdown-arrow,
[aria-expanded="true"] .dropdown-arrow {
    transform: rotate(90deg);
}
</style>
<div class="min-vh-100 p-2">
    <div class="text-center px-2 py-3 align-self-center">
        <a href="/" class="brandlogo text-decoration-none">
            <span class="fw-bold text-white">ICELL</span>
        </a>
    </div>
    <ul class="nav nav-pills flex-column mt-2" id="menu">
        <li class="nav-item mb-2">
            <a href="{{ route('home') }}"
                class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('home') ? 'active' : '' }}">
                <i class="bi bi-house text-white fs-4"></i>
                <span class="ms-3 text-white fw-semibold">Beranda</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            @if (Auth::user()->hasPermission('personnel.R') || Auth::user()->hasPermission('signatories.R') || Auth::user()->hasPermission('certification.R'))
                <!-- Anggota Menu -->
                <a href="#anggota"
                    class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('personnel.*') ? 'active' : '' }}"
                    data-bs-toggle="collapse" aria-current="page">
                    <i class="bi bi-people text-white fs-4"></i>
                    <span class="ms-3 text-white fw-semibold">Anggota</span>
                    <i
                        class="bi bi-chevron-right text-white dropdown-arrow {{ request()->routeIs('personnel.*') ? 'rotate-down' : '' }}"></i>
                </a>
                <ul class="nav flex-column flex-nowrap collapse {{ request()->routeIs('personnel.*') ? 'show' : '' }}"
                    id="anggota" data-bs-parent="#menu">
                    {{-- <li class="nav-item items-child my-1">
                        <a href="{{ route('petugas') }}"
                            class="nav-link child-menu text-decoration-none text-white ps-5">Daftar Penyidik</a>
                    </li> --}}
                    {{-- <li class="nav-item items-child my-1">
                        <a href="{{ route('pengguna') }}"
                            class="nav-link child-menu text-decoration-none text-white ps-5">Pengguna
                            Aplikasi</a>
                    </li> --}}
                    @php
                        $policeId = !empty(Auth::user()->police_id) ? Auth::user()->police_id : null;
                    @endphp
                    @if (Auth::user()->hasPermission('personnel.R'))
                    <li class="nav-item items-child my-1">
                        <a href="{{ route('personnel.index', ['policeId' => $policeId]) }}"
                            class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('personnel.index') ? 'active' : '' }}">Personel</a>
                    </li>
                    @endif
                    @if (Auth::user()->hasPermission('signatories.R'))
                    <li class="nav-item items-child my-1">
                        <a href="{{ route('personnel.signatory', ['policeId' => $policeId]) }}"
                            class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('personnel.signatory') ? 'active' : '' }}">Pejabat
                            TTE</a>
                    </li>
                    @endif
                    @if (Auth::user()->hasPermission('certification.R'))
                    <li class="nav-item items-child my-1">
                        <a href="{{ route('personnel.certification', ['policeId' => $policeId]) }}"
                            class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('personnel.certification') ? 'active' : '' }}">Sertifikasi
                            Personel</a>
                    </li>
                    @endif
                    {{-- <li class="nav-item items-child my-1">
                        <a href="{{ route('signatories') }}"
                            class="nav-link child-menu text-decoration-none text-white ps-5">Pejabat
                            TTE</a>
                    </li> --}}
                </ul>
            @endif
        </li>
        <li class="nav-item mb-2">
            @if (Auth::user()->hasPermission('role.R') || Auth::user()->hasPermission('permission.R'))
                <!-- Manajemen Akses Menu -->
                <a href="#akses"
                    class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('permission') || request()->routeIs('role') || request()->routeIs('role-new') ? 'active' : '' }}"
                    data-bs-toggle="collapse" aria-current="page">
                    <i class="bi bi-card-list text-white fs-4"></i>
                    <span class="ms-3 text-white fw-semibold">Manajemen Akses</span>
                    <i
                        class="bi bi-chevron-right text-white dropdown-arrow {{ request()->routeIs('permission') || request()->routeIs('role') || request()->routeIs('role-new') ? 'rotate-down' : '' }}"></i>
                </a>
                <ul class="nav flex-column flex-nowrap collapse {{ request()->routeIs('permission') || request()->routeIs('role') || request()->routeIs('role-new') ? 'show' : '' }}"
                    id="akses" data-bs-parent="#menu">
                    @if(Auth::user()->hasPermission('permission.R'))
                    <li class="nav-item items-child my-1">
                        <a href="{{ route('permission') }}"
                            class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('permission') ? 'active' : '' }}">Hak
                            Akses</a>
                    </li>
                    @endif
                    {{-- Role Lama Disembunyikan Sesuai Request
                    <li class="nav-item items-child my-1">
                        <a href="{{ route('role') }}"
                            class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('role') ? 'active' : '' }}">Role</a>
                    </li>
                    --}}
                    @if(Auth::user()->hasPermission('role.R'))
                    <li class="nav-item items-child my-1">
                        <a href="{{ url('role-new') }}"
                            class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->is('role-new') ? 'active' : '' }}">Role</a>
                    </li>
                    @endif
                </ul>
            @endif
        </li>
        @if (Auth::user()->hasPermission('accident.R') || Auth::user()->hasPermission('case.R') || Auth::user()->hasPermission('productivity.R') || Auth::user()->hasPermission('productivity-lp.R') || Auth::user()->hasPermission('recap.R'))
            <li class="nav-item mb-2">
                <!-- Laporan Menu -->
                <a href="#laporan"
                    class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('accident') || request()->routeIs('case.index') || request()->routeIs('produktivitas') || request()->routeIs('rekap') ? 'active' : '' }}"
                    data-bs-toggle="collapse" aria-current="page">
                    <i class="bi bi-file-earmark text-white fs-4"></i>
                    <span class="ms-3 text-white fw-semibold">Laporan</span>
                    <i
                        class="bi bi-chevron-right text-white dropdown-arrow {{ request()->routeIs('accident') || request()->routeIs('case.index') || request()->routeIs('produktivitas') || request()->routeIs('rekap') ? 'rotate-down' : '' }}"></i>
                </a>
                <ul class="nav flex-column flex-nowrap collapse {{ request()->routeIs('accident') || request()->routeIs('case.index') || request()->routeIs('produktivitas') || request()->routeIs('rekap') ? 'show' : '' }}"
                    id="laporan" data-bs-parent="#menu">
                    @if (Auth::user()->hasPermission('accident.R'))
                        <li class="nav-item items-child my-1">
                            <a href="{{ route('accident') }}"
                                class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('accident') ? 'active' : '' }}">Register
                                Perkara Laka</a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('case.R'))
                        <li class="nav-item items-child my-1">
                            <a href="{{ route('case.index') }}"
                                class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('case.index') ? 'active' : '' }}">Register
                                Perkara Jatanlin</a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('productivity.R') || Auth::user()->hasPermission('productivity-lp.R'))
                    <li class="nav-item items-child my-1">
                        <a href="{{ route('produktivitas') }}"
                            class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('produktivitas') ? 'active' : '' }}">Perkara
                            Ditangani</a>
                    </li>
                    @endif
                    @if (Auth::user()->hasPermission('recap.R'))
                    <li class="nav-item items-child my-1">
                        <a href="{{ route('rekap') }}"
                            class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('rekap') ? 'active' : '' }}">Rekap</a>
                    </li>
                    @endif
                </ul>
            </li>
        @endif
        @if (Auth::user()->hasPermission('document-signature-verif.R'))
            <li class="nav-item mb-2">
                <a href="{{ route('document-signature.verification.index') }}"
                    class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('document-signature.verification.*') ? 'active' : '' }}">
                    <i class="bi bi-patch-check text-white fs-4"></i>
                    <span class="ms-3 text-white fw-semibold">Tanda Tangan Dokumen TTE (Verifikasi)</span>
                </a>
            </li>
        @endif
        @if (Auth::user()->hasPermission('document-signature.R'))
            <li class="nav-item mb-2">
                <a href="{{ route('document-signature.index') }}"
                    class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('document-signature.*') ? 'active' : '' }}">
                    <i class="bi bi-patch-check text-white fs-4"></i>
                    <span class="ms-3 text-white fw-semibold">Tanda Tangan Dokumen TTE</span>
                </a>
            </li>
        @endif
        @if (Auth::user()->hasPermission('document-approval.R'))
            <li class="nav-item mb-2">
                <a href="{{ route('document-approval.index') }}"
                    class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('document-approval.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-check text-white fs-4"></i>
                    <span class="ms-3 text-white fw-semibold">Persetujuan Dokumen</span>
                </a>
            </li>
        @endif
        @if (Auth::user()->hasPermission('document-approval-upload.R'))
            <li class="nav-item mb-2">
                <a href="{{ route('document-approval.upload.index') }}"
                    class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('document-approval.upload.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-arrow-up text-white fs-4"></i>
                    <span class="ms-3 text-white fw-semibold">Persetujuan Dokumen (Upload)</span>
                </a>
            </li>
        @endif
        @if (Auth::user()->hasPermission('statistics.R') || Auth::user()->hasPermission('report-individu.R') || Auth::user()->hasPermission('anev.R'))
            <li class="nav-item mb-2">
                <!-- Statistika Menu -->
                <a href="#statistika"
                    class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('index_month') || request()->routeIs('index_week') || request()->routeIs('index_day') || request()->routeIs('report_individu') || request()->routeIs('index_anev') ? 'active' : '' }}"
                    data-bs-toggle="collapse" aria-current="page">
                    <i class="bi bi-bar-chart-line text-white fs-4"></i>
                    <span class="ms-3 text-white fw-semibold">Statistika / Anev</span>
                    <i
                        class="bi bi-chevron-right text-white dropdown-arrow {{ request()->routeIs('index_month') || request()->routeIs('index_week') || request()->routeIs('index_day') || request()->routeIs('report_individu') || request()->routeIs('index_anev') ? 'rotate-down' : '' }}"></i>
                </a>
                <ul class="nav flex-column flex-nowrap collapse {{ request()->routeIs('index_month') || request()->routeIs('index_week') || request()->routeIs('index_day') || request()->routeIs('report_individu') || request()->routeIs('index_anev') ? 'show' : '' }}"
                    id="statistika" data-bs-parent="#menu">
                    @if (Auth::user()->hasPermission('statistics.R'))
                    <li class="nav-item items-child my-1">
                        <a href="{{ route('index_month') }}"
                            class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('index_month') ? 'active' : '' }}">Laporan
                            Bulanan</a>
                    </li>
                    <li class="nav-item items-child my-1">
                        <a href="{{ route('index_week') }}"
                            class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('index_week') ? 'active' : '' }}">Laporan
                            Mingguan</a>
                    </li>
                    <li class="nav-item items-child my-1">
                        <a href="{{ route('index_day') }}"
                            class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('index_day') ? 'active' : '' }}">Laporan
                            Harian</a>
                    </li>
                    @endif
                    @if (Auth::user()->hasPermission('report-individu.R'))
                    <li class="nav-item items-child my-1">
                        <a href="{{ route('report_individu') }}"
                            class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('report_individu') ? 'active' : '' }}">Laporan
                            Individu</a>
                    </li>
                    @endif
                    @if (Auth::user()->hasPermission('anev.R'))
                    <li class="nav-item items-child my-1">
                        <a href="{{ route('index_anev') }}"
                            class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('index_anev') ? 'active' : '' }}">Anev</a>
                    </li>
                    @endif
                </ul>
            </li>
        @endif
        @if (Auth::user()->hasPermission('dpo.R') || Auth::user()->hasPermission('dpb.R'))
            <li class="nav-item mb-2">
                <!-- DPO/DPB Menu -->
                <a href="#dpo/dpb"
                    class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('index_dpo') || request()->routeIs('index_dpb') ? 'active' : '' }}"
                    data-bs-toggle="collapse" aria-current="page">
                    <i class="bi bi-exclamation-circle text-white fs-4"></i>
                    <span class="ms-3 text-white fw-semibold">DPO / DPB</span>
                    <i
                        class="bi bi-chevron-right text-white dropdown-arrow {{ request()->routeIs('index_dpo') || request()->routeIs('index_dpb') ? 'rotate-down' : '' }}"></i>
                </a>
                <ul class="nav flex-column flex-nowrap collapse {{ request()->routeIs('index_dpo') || request()->routeIs('index_dpb') ? 'show' : '' }}"
                    id="dpo/dpb" data-bs-parent="#menu">
                    @if (Auth::user()->hasPermission('dpo.R'))
                    <li class="nav-item items-child my-1">
                        <a href="{{ route('index_dpo') }}"
                            class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('index_dpo') ? 'active' : '' }}">DPO</a>
                    </li>
                    @endif
                    @if (Auth::user()->hasPermission('dpb.R'))
                    <li class="nav-item items-child my-1">
                        <a href="{{ route('index_dpb') }}"
                            class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('index_dpb') ? 'active' : '' }}">DPB</a>
                    </li>
                    @endif
                </ul>
            </li>
        @endif
        @if (Auth::user()->hasPermission('category-info.R') || Auth::user()->hasPermission('territory.R') || Auth::user()->hasPermission('organization.R') || Auth::user()->hasPermission('organization.D'))
            <li class="nav-item mb-2">
                <!-- Kategori Menu -->
                <a href="#kategori"
                    class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('wilayah') || request()->routeIs('organisasi') ? 'active' : '' }}"
                    data-bs-toggle="collapse" aria-current="page">
                    <i class="bi bi-clipboard  text-white fs-4"></i>
                    <span class="ms-3 text-white fw-semibold">Kategori</span>
                    <i
                        class="bi bi-chevron-right text-white dropdown-arrow {{ request()->routeIs('wilayah') || request()->routeIs('organisasi') ? 'rotate-down' : '' }}"></i>
                </a>
                <ul class="nav flex-column flex-nowrap collapse {{ request()->routeIs('wilayah') || request()->routeIs('organisasi') ? 'show' : '' }}"
                    id="kategori" data-bs-parent="#menu">
                    @if (Auth::user()->hasPermission('category-info.R'))
                        <li class="nav-item items-child my-1">
                            <a href=""
                                class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('layanan-info') ? 'active' : '' }}">Layanan
                                Info</a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('territory.R'))
                    <li class="nav-item items-child my-1">
                        <a href="/wilayah"
                            class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->is('wilayah') ? 'active' : '' }}">Daftar
                            Wilayah</a>
                    </li>
                    @endif
                    @if (Auth::user()->hasPermission('organization.R') || Auth::user()->hasPermission('organization.D'))
                    <li class="nav-item items-child my-1">
                        <a href="/organisasi"
                            class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->is('organisasi') ? 'active' : '' }}">Struktur
                            Organisasi</a>
                    </li>
                    @endif
                </ul>
            </li>
        @endif
        @if (Auth::user()->hasPermission('tutorial.R'))
            <li class="nav-item mb-2 bg-danger">
                <a href="{{ asset('pdf-manual-book/Tutorial_ICELL_PDF.pdf') }}" target="_blank"
                    class="nav-link parents-menu d-flex align-items-center">
                    <i class="bi bi-journal-album text-white fs-4"></i>
                    <span class="ms-3 text-white fw-semibold">Tutorial ICELL</span>
                </a>
            </li>
        @endif

        {{-- <li class="nav-item mb-2">
            <a href="{{ route('leaderboard.index') }}"
                class="nav-link parents-menu d-flex align-items-center">
                <i class="bi bi-bar-chart-steps text-white fs-4"></i>
                <span class="ms-3 text-white fw-semibold">Leaderboard</span>
            </a>
        </li> --}}

        @if (Auth::user()->hasPermission('commander-wish.R'))
            <li class="nav-item mb-2">
                <a href="{{ route('commander-wish.index') }}"
                    class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('commander-wish.*') ? 'active' : '' }}">
                    <i class="bi bi-broadcast text-white fs-4"></i>
                    <span class="ms-3 text-white fw-semibold">Commander Wish</span>
                </a>
            </li>
        @endif

        @php
            $hasKatalogDaftar = Auth::user()->hasPermission('catalog-pangkat.R') || Auth::user()->hasPermission('catalog-kerusakan.R') || Auth::user()->hasPermission('catalog-kondisi-cahaya.R') || Auth::user()->hasPermission('catalog-pendidikan.R') || Auth::user()->hasPermission('catalog-pengaturan-simpang.R') || Auth::user()->hasPermission('catalog-tipe-kecelakaan.R') || Auth::user()->hasPermission('catalog-titik-acuan.R');
            
            $hasKatalogDokumen = Auth::user()->hasPermission('catalog-saksi.R') || Auth::user()->hasPermission('catalog-tersangka.R') || Auth::user()->hasPermission('catalog-penahanan.R') || Auth::user()->hasPermission('catalog-penggeledahan.R') || Auth::user()->hasPermission('catalog-penyitaan.R') || Auth::user()->hasPermission('catalog-penyegelan.R') || Auth::user()->hasPermission('catalog-labfor.R') || Auth::user()->hasPermission('catalog-rekening-bank.R') || Auth::user()->hasPermission('catalog-dpo-dpb.R');

            $hasKatalogPolda = Auth::user()->hasPermission('catalog-polda.R');
            $hasKatalogPolres = Auth::user()->hasPermission('catalog-polres.R');
        @endphp

        @if ($hasKatalogDaftar || $hasKatalogDokumen || $hasKatalogPolda || $hasKatalogPolres)
            <li class="nav-item mb-2">
                <!-- Katalog Menu -->
                <a href="#katalog"
                    class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('katalog.*') ? 'active' : '' }}"
                    data-bs-toggle="collapse" aria-current="page">
                    <i class="bi bi-book text-white fs-4"></i>
                    <span class="ms-3 text-white fw-semibold">Katalog</span>
                    <i
                        class="bi bi-chevron-right text-white dropdown-arrow {{ request()->routeIs('katalog.*') ? 'rotate-down' : '' }}"></i>
                </a>
                <ul class="nav flex-column flex-nowrap collapse" id="katalog" data-bs-parent="#menu">
                    @if ($hasKatalogDaftar)
                    <li class="nav-item items-child my-1">
                        <a href="" class="nav-link child-menu text-decoration-none text-white ps-5"
                            data-bs-toggle="collapse" data-bs-target="#daftar">
                            Daftar <i class="bi bi-caret-down-fill float-end text-white"></i>
                        </a>
                        <ul class="nav flex-column flex-nowrap collapse" id="daftar" data-bs-parent="#katalog">
                            @if (Auth::user()->hasPermission('catalog-pangkat.R'))
                            <li class="nav-item">
                                <a href="/pangkat" class="nav-link sub-menus text-white {{ request()->is('pangkat', 'pangkat/*') ? 'fw-bold' : '' }}">Pangkat</a>
                            </li>
                            @endif
                            @if (Auth::user()->hasPermission('catalog-kerusakan.R'))
                            <li class="nav-item">
                                <a href="/kerusakan" class="nav-link sub-menus text-white {{ request()->is('kerusakan', 'kerusakan/*') ? 'fw-bold' : '' }}">Kerusakan</a>
                            </li>
                            @endif
                            @if (Auth::user()->hasPermission('catalog-kondisi-cahaya.R'))
                            <li class="nav-item">
                                <a href="/kondisi-cahaya" class="nav-link sub-menus text-white {{ request()->is('kondisi-cahaya', 'kondisi-cahaya/*') ? 'fw-bold' : '' }}">Kondisi Cahaya</a>
                            </li>
                            @endif
                            @if (Auth::user()->hasPermission('catalog-pendidikan.R'))
                            <li class="nav-item">
                                <a href="/pendidikan" class="nav-link sub-menus text-white {{ request()->is('pendidikan', 'pendidikan/*') ? 'fw-bold' : '' }}">Pendidikan</a>
                            </li>
                            @endif
                            @if (Auth::user()->hasPermission('catalog-pengaturan-simpang.R'))
                            <li class="nav-item">
                                <a href="/pengaturan-simpang" class="nav-link sub-menus text-white {{ request()->is('pengaturan-simpang', 'pengaturan-simpang/*') ? 'fw-bold' : '' }}">Pengaturan Simpang</a>
                            </li>
                            @endif
                            @if (Auth::user()->hasPermission('catalog-tipe-kecelakaan.R'))
                            <li class="nav-item">
                                <a href="/tipe-kecelakaan" class="nav-link sub-menus text-white {{ request()->is('tipe-kecelakaan', 'tipe-kecelakaan/*') ? 'fw-bold' : '' }}">Tipe Kecelakaan</a>
                            </li>
                            @endif
                            @if (Auth::user()->hasPermission('catalog-titik-acuan.R'))
                            <li class="nav-item">
                                <a href="/titik-acuan" class="nav-link sub-menus text-white {{ request()->is('titik-acuan', 'titik-acuan/*') ? 'fw-bold' : '' }}">Titik Acuan</a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if ($hasKatalogDokumen)
                    <li class="nav-item items-child my-1">
                        <a href="" class="nav-link child-menu text-decoration-none text-white ps-5"
                            data-bs-toggle="collapse" data-bs-target="#dokumen">
                            Dokumen <i class="bi bi-caret-down-fill float-end text-white"></i>
                        </a>
                        <ul class="nav flex-column flex-nowrap collapse" id="dokumen" data-bs-parent="#katalog">
                            @if (Auth::user()->hasPermission('catalog-saksi.R'))
                            <li class="nav-item">
                                <a href="/saksi" class="nav-link sub-menus text-white {{ request()->is('saksi', 'saksi/*') ? 'fw-bold' : '' }}">Saksi</a>
                            </li>
                            @endif
                            @if (Auth::user()->hasPermission('catalog-tersangka.R'))
                            <li class="nav-item">
                                <a href="/tersangka" class="nav-link sub-menus text-white {{ request()->is('tersangka', 'tersangka/*') ? 'fw-bold' : '' }}">Tersangka</a>
                            </li>
                            @endif
                            @if (Auth::user()->hasPermission('catalog-penahanan.R'))
                            <li class="nav-item">
                                <a href="/penahanan" class="nav-link sub-menus text-white {{ request()->is('penahanan', 'penahanan/*') ? 'fw-bold' : '' }}">Penahanan</a>
                            </li>
                            @endif
                            @if (Auth::user()->hasPermission('catalog-penggeledahan.R'))
                            <li class="nav-item">
                                <a href="/penggeledahan" class="nav-link sub-menus text-white {{ request()->is('penggeledahan', 'penggeledahan/*') ? 'fw-bold' : '' }}">Penggeledahan</a>
                            </li>
                            @endif
                            @if (Auth::user()->hasPermission('catalog-penyitaan.R'))
                            <li class="nav-item">
                                <a href="/penyitaan" class="nav-link sub-menus text-white {{ request()->is('penyitaan', 'penyitaan/*') ? 'fw-bold' : '' }}">Penyitaan</a>
                            </li>
                            @endif
                            @if (Auth::user()->hasPermission('catalog-penyegelan.R'))
                            <li class="nav-item">
                                <a href="/penyegelan" class="nav-link sub-menus text-white {{ request()->is('penyegelan', 'penyegelan/*') ? 'fw-bold' : '' }}">Penyegelan</a>
                            </li>
                            @endif
                            @if (Auth::user()->hasPermission('catalog-labfor.R'))
                            <li class="nav-item">
                                <a href="/labfor" class="nav-link sub-menus text-white {{ request()->is('labfor', 'labfor/*') ? 'fw-bold' : '' }}">Labfor</a>
                            </li>
                            @endif
                            @if (Auth::user()->hasPermission('catalog-rekening-bank.R'))
                            <li class="nav-item">
                                <a href="/rekening-bank" class="nav-link sub-menus text-white {{ request()->is('rekening-bank', 'rekening-bank/*') ? 'fw-bold' : '' }}">Rekening Bank</a>
                            </li>
                            @endif
                            @if (Auth::user()->hasPermission('catalog-dpo-dpb.R'))
                            <li class="nav-item">
                                <a href="/dpo-dpb" class="nav-link sub-menus text-white {{ request()->is('dpo-dpb', 'dpo-dpb/*') ? 'fw-bold' : '' }}">DPO / DPB</a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if ($hasKatalogPolda)
                    <li class="nav-item items-child my-1">
                        <a href="/polda" class="nav-link child-menu text-decoration-none text-white ps-5">Polda</a>
                    </li>
                    @endif
                    @if ($hasKatalogPolres)
                    <li class="nav-item items-child my-1">
                        <a href="/polres" class="nav-link child-menu text-decoration-none text-white ps-5">Polres</a>
                    </li>
                    @endif
                </ul>
            </li>
        @endif

        @if (Auth::user()->hasPermission('cms.R'))
            <li class="nav-item mb-2 bg-danger">
                <a href="{{ route('cms.home.index') }}" class="nav-link parents-menu d-flex align-items-center">
                    <i class="bi bi-window-sidebar text-white fs-4"></i>
                    <span class="ms-3 text-white fw-semibold">CMS</span>
                </a>
            </li>
        @endif

        @if (Auth::user()->hasPermission('irsms.R'))
            <li class="nav-item mb-2">
                <a href="javascript:void(0);" onclick="openIrsmsSSO()"
                    class="nav-link parents-menu d-flex align-items-center">
                    <img src="{{ asset('images/logo-irsms.png') }}" alt="logoIRSMS" style="width: 20%;">
                    <span class="ms-3 text-white fw-semibold" style="margin-left: 2% !important">IRSMS</span>
                </a>
            </li>
        @endif
    </ul>
</div>

<script>
    function openIrsmsSSO() {
        const loadingModal = document.getElementById("loadingModal");
        const failedModal = document.getElementById("failedModal");

        loadingModal.style.display = "flex";

        fetch("{{ route('sso.redirect', ['target' => 'irsms']) }}")
            .then(res => res.json().then(data => ({ ok: res.ok, status: res.status, data })))
            .then(({ ok, status, data }) => {
                loadingModal.style.display = "none";

                if (ok && data.status === 'success' && data.redirect_url) {
                    window.location.href = data.redirect_url;
                    return;
                }
                console.error('SSO failed:', data.message);
                showFailedModal(data.message || (status === 403 ? 'Akses IRSMS tidak diizinkan' : 'Gagal membuka IRSMS'));
            })
    .catch(err => {
        console.error('SSO Error:', err);
        loadingModal.style.display = "none";
        showFailedModal();
    });
    }

    function showFailedModal(message = "Gagal membuka IRSMS") {
        document.getElementById("failedModal").style.display = "flex";
        document.getElementById("failedModalMessage").innerText = message;
    }

    function closeFailedModal() {
        document.getElementById("failedModal").style.display = "none";
    }

    document.addEventListener('DOMContentLoaded', function() {
    // Get all parent menu items with dropdown
    const parentMenus = document.querySelectorAll('.parents-menu[data-bs-toggle="collapse"]');
    
    // Add click event listener to each parent menu
    parentMenus.forEach(menu => {
        // Get the target element id
        const targetId = menu.getAttribute('href').substring(1);
        const targetElement = document.getElementById(targetId);
        
        // Get the dropdown arrow element
        const dropdownArrow = menu.querySelector('.dropdown-arrow');
        
        // Create a Bootstrap collapse instance
        const bsCollapse = new bootstrap.Collapse(targetElement, {
            toggle: false
        });
        
        // Add click event listener
        menu.addEventListener('click', function(e) {
            // Toggle the rotate class on dropdown arrow
            if(dropdownArrow) {
                if(targetElement.classList.contains('show')) {
                    dropdownArrow.classList.remove('rotate-down');
                } else {
                    dropdownArrow.classList.add('rotate-down');
                }
            }
        });
        
        // Listen for bootstrap collapse events
        targetElement.addEventListener('shown.bs.collapse', function() {
            if(dropdownArrow) {
                dropdownArrow.classList.add('rotate-down');
            }
        });
        
        targetElement.addEventListener('hidden.bs.collapse', function() {
            if(dropdownArrow) {
                dropdownArrow.classList.remove('rotate-down');
            }
        });
    });
});
</script>


<!-- Modal Loading -->
<div id="loadingModal"
    style="display: none; position: fixed; z-index: 9999; top: 0; left: 0; width: 100%; height: 100%; 
            background: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
    <div style="background: white; padding: 20px; border-radius: 10px; text-align: center;">
        <img src="{{ asset('images/loading.gif') }}" width="80" alt="Loading...">
        <p>Membuka IRSMS...</p>
    </div>
</div>

<!-- Modal Gagal -->
<div id="failedModal"
    style="display: none; position: fixed; z-index: 9999; top: 0; left: 0; width: 100%; height: 100%; 
            background: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
    <div style="background: white; padding: 20px; border-radius: 10px; text-align: center;">
        <img src="{{ asset('images/failed.gif') }}" width="80" alt="Gagal">
        <p id="failedModalMessage">Gagal membuka IRSMS</p>
        <button class="btn btn-warning mt-2" onclick="closeFailedModal()">Tutup</button>
    </div>
</div>
