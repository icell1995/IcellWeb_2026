<div class="min-vh-100 p-2">
    <div class="text-center px-2 py-3 align-self-center">
        <a href="/" class="brandlogo text-decoration-none">
            <span class="fw-bold text-white">CMS ICELL</span>
        </a>
    </div>
    <ul class="nav nav-pills flex-column mt-2" id="menu">
        <li class="nav-item mb-2">
            <a href="{{ route('cms.home.index') }}" class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('cms.home.index') ? 'active' : '' }}">
                <i class="bi bi-house text-white fs-4"></i>
                <span class="ms-3 text-white fw-semibold">Home</span>
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="{{ route('cms.validation-dashboard-irsms-icell') }}"
                class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('cms.validation-dashboard-irsms-icell') ? 'active' : '' }}"
                target="_blank">
                <i class="bi bi-shield-check text-white fs-4"></i>
                <span class="ms-3 text-white fw-semibold">Dashboard IRSMS & ICELL</span>
                <i class="bi bi-box-arrow-up-right text-white ms-2 small"></i>
                <!-- Ikon untuk menunjukkan link eksternal -->
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="{{ route('cms.check-officer-data.index') }}"
                class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('cms.check-officer-data.index') ? 'active' : '' }}">
                <i class="bi bi-person-badge text-white fs-4"></i>
                <span class="ms-3 text-white fw-semibold">Check Officer Data</span>
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="{{ route('cms.case-document-validation.index') }}"
                class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('cms.case-document-validation.index') ? 'active' : '' }}">
                <i class="bi bi-patch-check text-white fs-4"></i>
                <span class="ms-3 text-white fw-semibold">Review</span>
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="{{ route('cms.case-document-validation-report.index') }}"
                class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('cms.case-document-validation-report.index') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check text-white fs-4"></i>
                <span class="ms-3 text-white fw-semibold">Review Report</span>
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="{{ route('cms.document-return.index') }}"
                class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('cms.document-return.index') ? 'active' : '' }}">
                <i class="bi bi-arrow-return-left text-white fs-4"></i>
                <span class="ms-3 text-white fw-semibold">Pengembalian Dokumen</span>
            </a>
        </li>

        {{-- <li class="nav-item mb-2">
            <a href="{{ route('cms.case-resolution-validation-report.index') }}"
                class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('cms.case-resolution-validation-report.index') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check text-white fs-4"></i>
                <span class="ms-3 text-white fw-semibold">Review Selra Report</span>
            </a>
        </li> --}}

        {{-- <li class="nav-item mb-2">
            <a href="{{ route('cms.case-resolutions-validations.index') }}"
                class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('cms.case-resolutions-validations.*') ? 'active' : '' }}"
                aria-current="{{ request()->routeIs('cms.case-resolutions-validations.*') ? 'page' : '' }}">
                <i class="bi bi-patch-check text-white fs-4"></i>
                <span class="ms-3 text-white fw-semibold">Review Selra</span>
            </a>
        </li> --}}

        <li class="nav-item mb-2">
            <a href="#ticketing" class="nav-link parents-menu d-flex align-items-center" data-bs-toggle="collapse">
                <i class="bi bi-ticket-perforated text-white fs-4 {{ request()->routeIs('ticketing.*') ? 'active' : '' }}"></i>
                <span class="ms-3 text-white fw-semibold">Ticketing</span>
                <i class="bi bi-chevron-right text-white dropdown-arrow"></i>
            </a>
            <ul class="nav flex-column flex-nowrap collapse {{ request()->routeIs('ticketing.*') ? 'show' : '' }}"
                id="ticketing" data-bs-parent="#menu">
                <li class="nav-item items-child my-1">
                    <a href="{{ route('ticketing.open') }}"
                        class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('ticketing.open') ? 'active' : '' }}">Open
                        (New)</a>
                </li>
                <li class="nav-item items-child my-1">
                    <a href="{{ route('ticketing.pending') }}"
                        class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('ticketing.pending') ? 'active' : '' }}">Pending
                        (&gt;3 days)</a>
                </li>
                <li class="nav-item items-child my-1">
                    <a href="{{ route('ticketing.solved') }}"
                        class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('ticketing.solved') ? 'active' : '' }}">Solved</a>
                </li>
            </ul>
        </li>
        
        <li class="nav-item mb-2">
            <a href="{{ route('request-data.index') }}"
                class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('request-data.*') ? 'active' : '' }}">
                <i class="bi bi-inbox text-white fs-4"></i>
                <span class="ms-3 text-white fw-semibold">Request Data</span>
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="#databases" class="nav-link parents-menu d-flex align-items-center" data-bs-toggle="collapse"
                aria-current="page">
                <i class="bi bi-database text-white fs-4"></i>
                <span class="ms-3 text-white fw-semibold">Databases</span>
                <i class="bi bi-chevron-right text-white dropdown-arrow"></i>
            </a>
            <ul class="nav flex-column flex-nowrap collapse" id="databases" data-bs-parent="#menu">
                <li class="nav-item items-child my-1">
                    <a href="{{ route('cms.db.postgresql.query.index') }}"
                        class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('cms.db.postgresql.query.index') ? 'active' : '' }}">Postgresql - Query</a>
                </li>
            </ul>
        </li>

        <li class="nav-item mb-2">
            <a href="#tools" class="nav-link parents-menu d-flex align-items-center" data-bs-toggle="collapse"
                aria-current="page">
                <i class="bi bi-tools text-white fs-4"></i>
                <span class="ms-3 text-white fw-semibold">Tools</span>
                <i class="bi bi-chevron-right text-white dropdown-arrow"></i>
            </a>
            <ul class="nav flex-column flex-nowrap collapse" id="tools" data-bs-parent="#menu">
                <li class="nav-item items-child my-1">
                    <a href="{{ url('/log-viewer') }}"
                        class="nav-link child-menu text-decoration-none text-white ps-5">Log Viewer</a>
                </li>
                <li class="nav-item items-child my-1">
                    <a href="{{ url('/pulse') }}"
                        class="nav-link child-menu text-decoration-none text-white ps-5">Pulse</a>
                </li>
                <li class="nav-item items-child my-1">
                    <a href="{{ route('cms.check-officer-digital-signature.index') }}"
                        class="nav-link child-menu text-decoration-none text-white ps-5">Check TTE</a>
                </li>
                <li class="nav-item items-child my-1">
                    <a href="{{ route('cms.maintenance-mode.index') }}"
                        class="nav-link child-menu text-decoration-none text-white ps-5 {{ request()->routeIs('cms.maintenance-mode.*') ? 'active' : '' }}">
                        <i class="bi bi-gear-wide-connected me-1"></i>Maintenance Mode
                    </a>
                </li>
            </ul>
        </li>

        <li class="nav-item mb-2">
            <a href="#officer" class="nav-link parents-menu d-flex align-items-center" data-bs-toggle="collapse"
                aria-current="page">
                <i class="bi bi-people text-white text-white fs-4"></i>
                <span class="ms-3 text-white fw-semibold">Anggota</span>
                <i class="bi bi-chevron-right text-white dropdown-arrow"></i>
            </a>
            <ul class="nav flex-column flex-nowrap collapse" id="officer" data-bs-parent="#menu">
                <li class="nav-item items-child my-1">
                    <a href="" class="nav-link child-menu text-decoration-none text-white ps-5">Personnel</a>
                </li>
                <li class="nav-item items-child my-1">
                    <a href="" class="nav-link child-menu text-decoration-none text-white ps-5">Admin Satker</a>
                </li>
                <li class="nav-item items-child my-1">
                    <a href="" class="nav-link child-menu text-decoration-none text-white ps-5">Penandatangan</a>
                </li>
            </ul>
        </li>

        <li class="nav-item mb-2">
            <a href="#library" class="nav-link parents-menu d-flex align-items-center" data-bs-toggle="collapse"
                aria-current="page">
                <i class="bi bi-file-earmark text-white fs-4"></i>
                <span class="ms-3 text-white fw-semibold">Library</span>
                <i class="bi bi-chevron-right text-white dropdown-arrow"></i>
            </a>
            <ul class="nav flex-column flex-nowrap collapse" id="library" data-bs-parent="#menu">
                <li class="nav-item items-child my-1">
                    <a href="" class="nav-link child-menu text-decoration-none text-white ps-5">Polisi</a>
                </li>
                <li class="nav-item items-child my-1">
                    <a href="" class="nav-link child-menu text-decoration-none text-white ps-5">Kejaksaan</a>
                </li>
                <li class="nav-item items-child my-1">
                    <a href="" class="nav-link child-menu text-decoration-none text-white ps-5">Pengadilan</a>
                </li>
                <li class="nav-item items-child my-1">
                    <a href="{{ route('cms.libs.position.index') }}"
                        class="nav-link child-menu text-decoration-none text-white ps-5">Jabatan</a>
                </li>
                <li class="nav-item items-child my-1">
                    <a href="{{ route('cms.libs.position-cluster.index') }}"
                        class="nav-link child-menu text-decoration-none text-white ps-5">Cluster Jabatan</a>
                </li>
            </ul>
        </li>

        <li class="nav-item mb-2">
            <a href="{{ route('cms.integration-monitor.index') }}" class="nav-link parents-menu d-flex align-items-center {{ request()->routeIs('cms.integration-monitor.*') ? 'active' : '' }}">
                <i class="bi bi-activity text-white fs-4"></i>
                <span class="ms-3 text-white fw-semibold">Monitor Integrasi</span>
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="{{ route('cms.event-gallery.index') }}" class="nav-link parents-menu d-flex align-items-center">
                <i class="bi bi-images text-white fs-4"></i>
                <span class="ms-3 text-white fw-semibold">Event Gallery</span>
            </a>
        </li>

        <li class="nav-item mb-2 bg-danger">
            <a href="{{ route('home') }}" class="nav-link parents-menu d-flex align-items-center">
                <i class="bi bi-arrow-down-left text-white fs-4"></i>
                <span class="ms-3 text-white fw-semibold">Back To ICELL</span>
            </a>
        </li>
    </ul>
</div>
