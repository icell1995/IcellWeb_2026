
@php
    $userPolice = Auth::user()->police;
@endphp
<div class="min-vh-100 p-2">
    <div class="text-center px-2 py-3 align-self-center">
        <a href="/" class="brandlogo text-decoration-none">
            <span class="fw-bold text-white">ICELL</span>
        </a>
    </div>
    <ul class="nav nav-pills flex-column mt-2" id="menu">
        <li class="nav-item mb-2">
            <a href="{{ route('home') }}" class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('home') ? 'active' : '' }}">
                <i class="bi bi-house text-white fs-4"></i>
                <span class="ms-3 text-white fw-semibold">Beranda</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            @if (in_array(Auth::getUser()->role_id, [1,3,5]))
                <a href="#anggota" class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('personnel.*') ? 'active' : '' }}" data-bs-toggle="collapse"
                    aria-current="page">
                    <i class="bi bi-people text-white fs-4"></i>
                    <span class="ms-3 text-white fw-semibold">Anggota</span>
                    <i class="bi bi-chevron-right text-white dropdown-arrow"></i>
                </a>
                <ul class="nav flex-column flex-nowrap collapse {{ request()->routeIs('personnel.*') ? 'show' : '' }}" id="anggota" data-bs-parent="#menu">
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
                        $policeId = (Auth::user()->role_id != 1) ? Auth::user()->police_id : null;
                    @endphp
                    <li class="nav-item items-child my-1">
                        <a href="{{ route('personnel.index', ['policeId' => $policeId]) }}"
                            class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('personnel.index') ? 'active' : '' }}">Personel</a>
                    </li>
                    <li class="nav-item items-child my-1">
                        <a href="{{ route('personnel.signatory', ['policeId' => $policeId]) }}"
                            class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('personnel.signatory') ? 'active' : '' }}">Pejabat TTE</a>
                    </li>
                    <li class="nav-item items-child my-1">
                        <a href="{{ route('personnel.certification', ['policeId' => $policeId]) }}"
                            class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('personnel.certification') ? 'active' : '' }}">Sertifikasi Personel</a>
                    </li>
                    {{-- <li class="nav-item items-child my-1">
                        <a href="{{ route('signatories') }}"
                            class="nav-link child-menu text-decoration-none text-white ps-5">Pejabat
                            TTE</a>
                    </li> --}}
                </ul>
            @endif
        </li>
        <li class="nav-item mb-2">
            @if (Auth::getUser()->role_id == 1)
                <a href="#akses" class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('permission') || request()->routeIs('role') ? 'active' : '' }}" data-bs-toggle="collapse"
                    aria-current="page">
                    <i class="bi bi-card-list text-white fs-4"></i>
                    <span class="ms-3 text-white fw-semibold">Manajemen Akses</span>
                    <i class="bi bi-chevron-right text-white dropdown-arrow"></i>
                </a>
                <ul class="nav flex-column flex-nowrap collapse {{ request()->routeIs('permission') || request()->routeIs('role') ? 'show' : '' }}" id="akses" data-bs-parent="#menu">
                    <li class="nav-item items-child my-1">
                        <a href="{{ route('permission') }}"
                        class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('permission') ? 'active' : '' }}">Hak Akses</a>
                    </li>
                    <li class="nav-item items-child my-1">
                        <a href="{{ route('role') }}"
                        class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('role') ? 'active' : '' }}">Role</a>
                    </li>
                </ul>
            @endif
        </li>
        <li class="nav-item mb-2">
            <a href="#laporan" class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('accident') || request()->routeIs('case.index') || request()->routeIs('produktivitas') || request()->routeIs('rekap') ? 'active' : '' }}" data-bs-toggle="collapse" aria-current="page">
                <i class="bi bi-file-earmark text-white fs-4"></i>
                <span class="ms-3 text-white fw-semibold">Laporan</span>
                <i class="bi bi-chevron-right text-white dropdown-arrow"></i>
            </a>
            <ul class="nav flex-column flex-nowrap collapse {{ request()->routeIs('accident') || request()->routeIs('case.index') || request()->routeIs('produktivitas') || request()->routeIs('rekap') ? 'show' : '' }}" id="laporan" data-bs-parent="#menu">
                @if(Auth::getUser()->role_id == 1 || Auth::getUser()->role_id == 3)
                    <li class="nav-item items-child my-1">
                        <a href="{{ route('accident') }}"
                        class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('accident') ? 'active' : '' }}">Register Perkara Laka</a>
                    </li>
                    <li class="nav-item items-child my-1">
                        <a href="{{ route('case.index') }}"
                        class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('case.index') ? 'active' : '' }}">Register Perkara Jatanlin</a>
                    </li>
                @endif
                <li class="nav-item items-child my-1">
                    <a href="{{ route('produktivitas') }}"
                    class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('produktivitas') ? 'active' : '' }}">Perkara Ditangani</a>
                </li>
                <li class="nav-item items-child my-1">
                    <a href="{{ route('rekap') }}"
                    class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('rekap') ? 'active' : '' }}">Rekap</a>
                </li>
            </ul>
        </li>
        @if (Auth::getUser()->role_id == 3)
            <li class="nav-item mb-2">
                <a href="{{ route('document-signature.verification.index') }}"
                class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('document-signature.verification.*') ? 'active' : '' }}">
                    <i class="bi bi-patch-check text-white fs-4"></i>
                    <span class="ms-3 text-white fw-semibold">Tanda Tangan Dokumen TTE (Verifikasi)</span>
                </a>
            </li>
        @endif
        @if (Auth::getUser()->role_id == 5)
            <li class="nav-item mb-2">
                <a href="{{ route('document-signature.index') }}"
                class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('document-signature.*') ? 'active' : '' }}">
                    <i class="bi bi-patch-check text-white fs-4"></i>
                    <span class="ms-3 text-white fw-semibold">Tanda Tangan Dokumen TTE</span>
                </a>
            </li>
        @endif
        @if (Auth::getUser()->role_id == 5 || Auth::getUser()->role_id == 3)
            <li class="nav-item mb-2">
                <a href="{{ route('document-approval.index') }}"
                class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('document-approval.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-check text-white fs-4"></i>
                    <span class="ms-3 text-white fw-semibold">Persetujuan Dokumen</span>
                </a>
            </li>
        @endif
        @if (Auth::getUser()->role_id == 3)
            <li class="nav-item mb-2">
                <a href="{{ route('document-approval.upload.index') }}"
                class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('document-approval.upload.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-arrow-up text-white fs-4"></i>
                    <span class="ms-3 text-white fw-semibold">Persetujuan Dokumen (Upload)</span>
                </a>
            </li>
        @endif
        <li class="nav-item mb-2">
            <a href="#statistika" class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('index_month') || request()->routeIs('index_week') || request()->routeIs('index_day') || request()->routeIs('report_individu') || request()->routeIs('index_anev') ? 'active' : '' }}" data-bs-toggle="collapse" aria-current="page">
                <i class="bi bi-bar-chart-line text-white fs-4"></i>
                <span class="ms-3 text-white fw-semibold">Statistika / Anev</span>
                <i class="bi bi-chevron-right text-white dropdown-arrow"></i>
            </a>
            <ul class="nav flex-column flex-nowrap collapse {{ request()->routeIs('index_month') || request()->routeIs('index_week') || request()->routeIs('index_day') || request()->routeIs('report_individu') || request()->routeIs('index_anev') ? 'show' : '' }}" id="statistika" data-bs-parent="#menu">
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
                <li class="nav-item items-child my-1">
                    <a href="{{ route('report_individu') }}"
                    class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('report_individu') ? 'active' : '' }}">Laporan
                        Individu</a>
                </li>
                <li class="nav-item items-child my-1">
                    <a href="{{ route('index_anev') }}"
                    class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('index_anev') ? 'active' : '' }}">Anev</a>
                </li>
            </ul>
        </li>
        <li class="nav-item mb-2">
            <a href="#dpo/dpb" class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('index_dpo') || request()->routeIs('index_dpb') ? 'active' : '' }}" data-bs-toggle="collapse" aria-current="page">
                <i class="bi bi-exclamation-circle text-white fs-4"></i>
                <span class="ms-3 text-white fw-semibold">DPO / DPB</span>
                <i class="bi bi-chevron-right text-white dropdown-arrow"></i>
            </a>
            <ul class="nav flex-column flex-nowrap collapse {{ request()->routeIs('index_dpo') || request()->routeIs('index_dpb') ? 'show' : '' }}" id="dpo/dpb" data-bs-parent="#menu">
                <li class="nav-item items-child my-1">
                    <a href="{{route('index_dpo')}}" class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('index_dpo') ? 'active' : ''}}">DPO</a>
                </li>
                <li class="nav-item items-child my-1">
                    <a href="{{route('index_dpb')}}" class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()-> routeIs('index_dpb') ? 'active' : '' }}">DPB</a>
                </li>
            </ul>
        </li>
        <li class="nav-item mb-2">
            <a href="#kategori" class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('wilayah') || request()->routeIs('organisasi') ? 'active' : '' }}" data-bs-toggle="collapse" aria-current="page">
                <i class="bi bi-clipboard  text-white fs-4"></i>
                <span class="ms-3 text-white fw-semibold">Kategori</span>
                <i class="bi bi-chevron-right text-white dropdown-arrow"></i>
            </a>
            <ul class="nav flex-column flex-nowrap collapse {{ request()->routeIs('wilayah') || request()->routeIs('organisasi') ? 'show' : '' }}" id="kategori" data-bs-parent="#menu">
                @if (Auth::getUser()->role_id == 1)
                    <li class="nav-item items-child my-1">
                        <a href="" class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('layanan-info') ? 'active' : '' }}">Layanan
                            Info</a>
                    </li>
                @endif
                <li class="nav-item items-child my-1">
                    <a href="/wilayah" class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->is('wilayah') ? 'active' : '' }}">Daftar
                        Wilayah</a>
                </li>
                <li class="nav-item items-child my-1">
                    <a href="/organisasi" class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->is('organisasi') ? 'active' : '' }}">Struktur
                        Organisasi</a>
                </li>
            </ul>
        </li>
        <li class="nav-item mb-2 bg-danger">
            <a href="{{ asset('pdf-manual-book/Tutorial_ICELL_PDF.pdf') }}" target="_blank"
                    class="nav-link parents-menu d-flex align-items-center">
                    <i class="bi bi-journal-album text-white fs-4"></i>
                    <span class="ms-3 text-white fw-semibold">Tutorial ICELL</span>
            </a>
            {{-- <a href="#manual_book" class="nav-link parents-menu d-flex align-items-center" data-bs-toggle="collapse"
                aria-current="page">
                <i class="bi bi-journal-album text-white fs-4"></i>
                <span class="ms-3 text-white fw-semibold">Manual Book</span>
                <i class="bi bi-chevron-right text-white dropdown-arrow"></i>
                <a href="{{ asset('pdf-manual-book/Tutorial_ICELL_PDF.pdf') }}" target="_blank"
                        class="nav-link child-menu text-decoration-none text-white ps-5">Tutorial ICELL</a>
            </a> --}}
            {{-- <ul class="nav flex-column flex-nowrap collapse" id="manual_book" data-bs-parent="#menu">
                <li class="nav-item items-child my-1">
                    <a href="{{ asset('pdf-manual-book/SPRIN-LIDIK.pdf') }}" target="_blank"
                        class="nav-link child-menu text-decoration-none text-white ps-5">Surat Perintah
                        Penyelidikan</a>
                </li>
                <li class="nav-item items-child my-1">
                    <a href="{{ asset('pdf-manual-book/SPRIN-SIDIK.pdf') }}" target="_blank"
                        class="nav-link child-menu text-decoration-none text-white ps-5">Surat Perintah Penyidikan</a>
                </li>
                <li class="nav-item items-child my-1">
                    <a href="{{ asset('pdf-manual-book/SPRIN-TUGAS.pdf') }}" target="_blank"
                        class="nav-link child-menu text-decoration-none text-white ps-5">Surat Perintah Tugas</a>
                </li>
                <li class="nav-item items-child my-1">
                    <a href="{{ asset('pdf-manual-book/LHGP.pdf') }}" target="_blank"
                        class="nav-link child-menu text-decoration-none text-white ps-5">Laporan Hasil Gelar
                        Perkara</a>
                </li>
                <li class="nav-item items-child my-1">
                    <a href="{{ asset('pdf-manual-book/STAP-PENETAPAN-TERSANGKA.pdf') }}" target="_blank"
                        class="nav-link child-menu text-decoration-none text-white ps-5">Surat Ketetapan Tentang
                        Penetapan Tersangka</a>
                </li>
                <li class="nav-item items-child my-1">
                    <a href="{{ asset('pdf-manual-book/SPDP.pdf') }}" target="_blank"
                        class="nav-link child-menu text-decoration-none text-white ps-5">SPDP</a>
                </li>
            </ul> --}}

        </li>

        {{-- <li class="nav-item mb-2">
            <a href="{{ route('leaderboard.index') }}"
                class="nav-link parents-menu d-flex align-items-center">
                <i class="bi bi-bar-chart-steps text-white fs-4"></i>
                <span class="ms-3 text-white fw-semibold">Leaderboard</span>
            </a>
        </li> --}}

        @if (Auth::getUser()->role_id == 1)
        <li class="nav-item mb-2">
            <a href="{{ route('commander-wish.index') }}"
                class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('commander-wish.*') ? 'active' : '' }}">
                <i class="bi bi-broadcast text-white fs-4"></i>
                <span class="ms-3 text-white fw-semibold">Commander Wish</span>
            </a>
        </li>
        @endif

        @if (Auth::getUser()->role_id == 1)
            <li class="nav-item mb-2">
                <a href="#katalog" class="nav-link parents-menu d-flex align-items-center" data-bs-toggle="collapse"
                    aria-current="page">
                    <i class="bi bi-book text-white fs-4"></i>
                    <span class="ms-3 text-white fw-semibold">Katalog</span>
                    <i class="bi bi-chevron-right text-white dropdown-arrow"></i>
                </a>
                <ul class="nav flex-column flex-nowrap collapse" id="katalog" data-bs-parent="#menu">
                    <li class="nav-item items-child my-1">
                        <a href="" class="nav-link child-menu text-decoration-none text-white ps-5"
                            data-bs-toggle="collapse" data-bs-target="#daftar">
                            Daftar <i class="bi bi-caret-down-fill float-end text-white"></i>
                        </a>
                        <ul class="nav flex-column flex-nowrap collapse" id="daftar" data-bs-parent="#katalog">
                            <li class="nav-item">
                                <a href="/pangkat" class="nav-link sub-menus text-white">Pangkat</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link sub-menus text-white">Kerusakan</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link sub-menus text-white">Kondisi Cahaya</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link sub-menus text-white">Pendidikan</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link sub-menus text-white">Pengaturan Simpang</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link sub-menus text-white">Tipe Kecelakaan</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link sub-menus text-white">Titik Acuan</a>
                            </li>
                            <!-- Tambahkan lebih banyak submenu di sini jika diperlukan -->
                        </ul>
                    </li>
                    <li class="nav-item items-child my-1">
                        <a href="" class="nav-link child-menu text-decoration-none text-white ps-5"
                            data-bs-toggle="collapse" data-bs-target="#dokumen">
                            Dokumen <i class="bi bi-caret-down-fill float-end text-white"></i>
                        </a>
                        <ul class="nav flex-column flex-nowrap collapse" id="dokumen" data-bs-parent="#katalog">
                            <li class="nav-item">
                                <a href="#" class="nav-link sub-menus text-white">Saksi</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link sub-menus text-white">Tersangka</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link sub-menus text-white">Penahanan</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link sub-menus text-white">Penggeledahan</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link sub-menus text-white">Penyitaan</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link sub-menus text-white">Penyegelan</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link sub-menus text-white">Labfor</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link sub-menus text-white">Rekening Bank</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link sub-menus text-white">DPO / DPB</a>
                            </li>
                            <!-- Tambahkan lebih banyak submenu di sini jika diperlukan -->
                        </ul>
                    </li>
                    <li class="nav-item items-child my-1">
                        <a href="/polda" class="nav-link child-menu text-decoration-none text-white ps-5">Polda</a>
                    </li>
                    <li class="nav-item items-child my-1">
                        <a href="/polres" class="nav-link child-menu text-decoration-none text-white ps-5">Polres</a>
                    </li>
                </ul>
            </li>

            @if (Auth::check() && stripos(Auth::user()->username, 'Helpdesk') === 0)
                <li class="nav-item mb-2 bg-danger">
                    <a href="{{ route('cms.home.index') }}" class="nav-link parents-menu d-flex align-items-center">
                        <i class="bi bi-window-sidebar text-white fs-4"></i>
                        <span class="ms-3 text-white fw-semibold">CMS</span>
                    </a>
                </li>
            @endif

            @if (Auth::check() && Auth::user()->role_id == 1)
                <li class="nav-item mb-2">
                    <a href="javascript:void(0);" onclick="openIrsmsSSO()" class="nav-link parents-menu d-flex align-items-center">
                        <img src="{{ asset('images/logo-irsms.png') }}" alt="logoIRSMS" style="width: 20%;">
                        <span class="ms-3 text-white fw-semibold" style="margin-left: 2% !important">IRSMS</span>
                    </a>
                </li>
            @endif

        @endif
    </ul>
</div>

<script>
    function openIrsmsSSO() {
        const loadingModal = document.getElementById("loadingModal");
        const failedModal = document.getElementById("failedModal");

        loadingModal.style.display = "flex";

        fetch("{{ route('sso.redirect', ['target' => 'irsms']) }}")
            .then(res => {
                if (!res.ok) {
                    throw new Error('Network response was not ok');
                }
                return res.json();
            })
            .then(data => {
                loadingModal.style.display = "none";

                if (data.status === 'success' && data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    console.error('SSO failed:', data.message);
                    showFailedModal(data.message);
                }
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