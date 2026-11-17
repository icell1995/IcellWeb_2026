@extends('cms.layouts.app')

@section('_title', 'Manajemen Surat (Feature Flag) - CMS ICELL')

@push('style')
<style>
    /* Modern Dashboard Styling */
    .feature-card {
        border-radius: 14px;
        transition: all 0.25s ease-in-out;
        border: 1px solid rgba(0, 0, 0, 0.06);
        background: #ffffff;
    }
    .feature-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08) !important;
    }

    .stat-icon-wrapper {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
    }

    /* Live Search & Filter Bar */
    .filter-pill {
        border-radius: 50rem;
        padding: 0.45rem 1rem;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        border: 1px solid #dee2e6;
        color: #495057;
        background: #fff;
    }
    .filter-pill:hover {
        background: #f1f3f5;
        color: #212529;
    }
    .filter-pill.active {
        background: #222577;
        border-color: #222577;
        color: #fff !important;
        box-shadow: 0 4px 10px rgba(34, 37, 119, 0.25);
    }

    /* Custom Modern Switch */
    .ios-switch {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 26px;
    }
    .ios-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .ios-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #e9ecef;
        transition: 0.3s;
        border-radius: 34px;
        border: 2px solid #ced4da;
    }
    .ios-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 2px;
        bottom: 2px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    input:checked + .ios-slider {
        background-color: #198754;
        border-color: #198754;
    }
    input:focus + .ios-slider {
        box-shadow: 0 0 1px #198754;
    }
    input:checked + .ios-slider:before {
        transform: translateX(22px);
    }
    input:disabled + .ios-slider {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Status Glow Badges */
    .badge-status-active {
        background-color: #d1e7dd;
        color: #0f5132;
        border: 1px solid #badbcc;
        font-weight: 600;
        padding: 0.4rem 0.75rem;
        border-radius: 50rem;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }
    .badge-status-inactive {
        background-color: #f8d7da;
        color: #842029;
        border: 1px solid #f5c2c7;
        font-weight: 600;
        padding: 0.4rem 0.75rem;
        border-radius: 50rem;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }
    .pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }
    .pulse-dot-green {
        background-color: #198754;
        box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7);
        animation: pulse-green 2s infinite;
    }
    .pulse-dot-red {
        background-color: #dc3545;
    }
    @keyframes pulse-green {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(25, 135, 84, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); }
    }

    /* Table & Card Polishing */
    .doc-table th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 1rem 0.75rem;
        border-bottom: 2px solid #e2e8f0;
    }
    .doc-table td {
        padding: 0.9rem 0.75rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .doc-table tr:hover td {
        background-color: #f8fafc;
    }

    .code-chip {
        background-color: #f1f5f9;
        color: #334155;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        font-family: monospace;
        font-size: 0.78rem;
        cursor: pointer;
        transition: all 0.15s ease;
        border: 1px solid #e2e8f0;
    }
    .code-chip:hover {
        background-color: #e2e8f0;
        color: #0f172a;
    }

    .avatar-initial {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #222577;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* Timeline Stepper for History */
    .timeline {
        position: relative;
        padding-left: 1.5rem;
        margin: 0;
        list-style: none;
    }
    .timeline:before {
        content: "";
        position: absolute;
        top: 0;
        bottom: 0;
        left: 7px;
        width: 2px;
        background: #e2e8f0;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 1.25rem;
    }
    .timeline-item:last-child {
        margin-bottom: 0;
    }
    .timeline-badge {
        position: absolute;
        left: -1.5rem;
        top: 2px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #fff;
        border: 3px solid #222577;
    }
    .timeline-badge.badge-active {
        border-color: #198754;
    }
    .timeline-badge.badge-inactive {
        border-color: #dc3545;
    }
</style>
@endpush

@section('content')
<div class="box p-3">
    {{-- Header Banner --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(135deg, #1e2269 0%, #2a3194 100%);">
        <div class="card-body p-4 text-white">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h2 class="fw-bold text-white mb-1">Manajemen Jenis Surat & Regulasi</h2>
                    <p class="text-white-50 mb-0 small" style="max-width: 680px;">
                        Aktifkan atau nonaktifkan jenis dokumen perkara secara instan. Sistem otomatis melindungi rute berkas perkara di level backend (HTTP 404 Guard) dan menyinkronkan UI pembuatan surat tanpa perlu deployment ulang.
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-light fw-semibold shadow-sm d-flex align-items-center gap-2" id="btn-sync-enum">
                        <i class="bi bi-arrow-repeat" id="sync-icon"></i>
                        <span>Sync dari Enum</span>
                    </button>
                    <a href="{{ route('cms.document-management.index') }}" class="btn btn-outline-light d-flex align-items-center gap-1" title="Refresh Halaman">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card feature-card shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="stat-icon-wrapper bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-file-earmark-text-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total Dokumen</div>
                        <div class="fs-4 fw-bold text-dark">{{ $totalAll }} <span class="fs-6 fw-normal text-muted">jenis</span></div>
                        <div class="small text-muted">{{ count($stages) }} tahapan perkara</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card feature-card shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="stat-icon-wrapper bg-success bg-opacity-10 text-success">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="text-muted small fw-semibold text-uppercase">Status Aktif</div>
                        <div class="d-flex align-items-baseline gap-2">
                            <span class="fs-4 fw-bold text-success" id="stat-total-active">{{ $totalActive }}</span>
                            <span class="small text-muted">({{ $totalAll > 0 ? round(($totalActive / $totalAll) * 100) : 0 }}%)</span>
                        </div>
                        <div class="progress mt-1" style="height: 4px;">
                            <div class="progress-bar bg-success" style="width: {{ $totalAll > 0 ? ($totalActive / $totalAll) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card feature-card shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="stat-icon-wrapper bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="text-muted small fw-semibold text-uppercase">Status Nonaktif</div>
                        <div class="d-flex align-items-baseline gap-2">
                            <span class="fs-4 fw-bold text-danger" id="stat-total-inactive">{{ $totalInactive }}</span>
                            <span class="small text-muted">({{ $totalAll > 0 ? round(($totalInactive / $totalAll) * 100) : 0 }}%)</span>
                        </div>
                        <div class="progress mt-1" style="height: 4px;">
                            <div class="progress-bar bg-danger" style="width: {{ $totalAll > 0 ? ($totalInactive / $totalAll) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card feature-card shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="stat-icon-wrapper bg-info bg-opacity-10 text-info">
                        <i class="bi bi-cpu-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Form / Route Siap</div>
                        <div class="fs-4 fw-bold text-dark">{{ $totalAvailableRoutes }} <span class="fs-6 fw-normal text-muted">route</span></div>
                        <div class="small text-muted">Terkoneksi ke Controller</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stage / Category Control Section --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom" 
             style="cursor: pointer;" 
             data-bs-toggle="collapse" 
             data-bs-target="#stageManagementCollapse">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-folder-check text-primary fs-5"></i>
                <div>
                    <h6 class="fw-bold text-blue-dark mb-0">Kontrol Kategori / Tahapan Perkara (Stage Toggle)</h6>
                    <span class="text-muted small" style="font-size: 0.78rem;">
                        Jika kategori dinonaktifkan, kategori dan seluruh surat di dalamnya otomatis <strong>tidak akan tampil</strong> di pilihan pembuatan berkas pengguna.
                    </span>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 small">
                    {{ $stages->where('is_active', true)->count() }} / {{ $stages->count() }} Kategori Aktif
                </span>
                <i class="bi bi-chevron-down text-muted"></i>
            </div>
        </div>
        <div class="collapse show" id="stageManagementCollapse">
            <div class="card-body p-3">
                <div class="row g-3">
                    @foreach ($stages as $stage)
                        @php
                            $stat = $stageStats[$stage->id] ?? ['total' => 0, 'active' => 0, 'inactive' => 0];
                            $isStageActive = (bool) $stage->is_active;
                        @endphp
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <div class="card border h-100 rounded-3 shadow-none p-3" style="background-color: {{ $isStageActive ? '#ffffff' : '#fafafa' }}; border-color: {{ $isStageActive ? '#e2e8f0' : '#f1f5f9' }} !important;">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="code-chip font-monospace fw-bold">{{ $stage->id }}</span>
                                        <span class="{{ $isStageActive ? 'badge-status-active' : 'badge-status-inactive' }} py-1 px-2" style="font-size: 0.72rem;" id="badge-stage-status-{{ $stage->id }}">
                                            <span class="pulse-dot {{ $isStageActive ? 'pulse-dot-green' : 'pulse-dot-red' }}"></span>
                                            <span>{{ $isStageActive ? 'Kategori Aktif' : 'Kategori Nonaktif' }}</span>
                                        </span>
                                    </div>
                                    <label class="ios-switch">
                                        <input type="checkbox" 
                                               class="toggle-stage-switch" 
                                               id="stage-switch-{{ $stage->id }}" 
                                               data-id="{{ $stage->id }}" 
                                               data-name="{{ $stage->name }}" 
                                               data-count="{{ $stat['total'] }}" 
                                               {{ $isStageActive ? 'checked' : '' }}>
                                        <span class="ios-slider"></span>
                                    </label>
                                </div>
                                <div class="fw-bold text-dark mb-1 text-truncate" title="{{ $stage->name }}" style="font-size: 0.88rem;">
                                    {{ $stage->name }}
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                                    <span class="small text-muted" style="font-size: 0.75rem;">
                                        <i class="bi bi-files me-1"></i> {{ $stat['active'] }}/{{ $stat['total'] }} surat aktif
                                    </span>
                                    <a href="{{ route('cms.document-management.index', ['stage' => $stage->id]) }}" class="small text-primary text-decoration-none fw-semibold" style="font-size: 0.75rem;">
                                        Filter Surat <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Toolbar Section --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                {{-- Left: Status Pills --}}
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <a href="{{ route('cms.document-management.index', ['stage' => $selectedStage, 'search' => $search]) }}" 
                       class="filter-pill {{ $selectedStatus === null || $selectedStatus === '' ? 'active' : '' }}">
                        <i class="bi bi-grid-fill"></i> Semua ({{ $totalAll }})
                    </a>
                    <a href="{{ route('cms.document-management.index', ['status' => '1', 'stage' => $selectedStage, 'search' => $search]) }}" 
                       class="filter-pill {{ $selectedStatus === '1' ? 'active' : '' }}">
                        <span class="pulse-dot pulse-dot-green"></span> Aktif ({{ $totalActive }})
                    </a>
                    <a href="{{ route('cms.document-management.index', ['status' => '0', 'stage' => $selectedStage, 'search' => $search]) }}" 
                       class="filter-pill {{ $selectedStatus === '0' ? 'active' : '' }}">
                        <span class="pulse-dot pulse-dot-red"></span> Nonaktif ({{ $totalInactive }})
                    </a>
                </div>

                {{-- Right: Stage Dropdown & Realtime Search Input (Bersebelahan) --}}
                <div class="d-flex align-items-center gap-2 flex-nowrap ms-auto">
                    <form method="GET" action="{{ route('cms.document-management.index') }}" class="m-0 d-inline-block">
                        @if($selectedStatus !== null && $selectedStatus !== '')
                            <input type="hidden" name="status" value="{{ $selectedStatus }}">
                        @endif
                        <select name="stage" class="form-select form-select-sm" onchange="this.form.submit()" style="width: 240px; border-radius: 8px;">
                            <option value="">-- Seluruh Tahapan Perkara --</option>
                            @foreach ($stages as $stage)
                                @php
                                    $stat = $stageStats[$stage->id] ?? ['total' => 0, 'active' => 0];
                                @endphp
                                <option value="{{ $stage->id }}" {{ $selectedStage == $stage->id ? 'selected' : '' }}>
                                    {{ $stage->id }} - {{ $stage->name }} ({{ $stat['active'] }}/{{ $stat['total'] }} Aktif)
                                </option>
                            @endforeach
                        </select>
                    </form>

                    {{-- Live Client-side Filter Search Input --}}
                    <div class="input-group input-group-sm m-0" style="width: 220px;">
                        <span class="input-group-text bg-light border-end-0" style="border-radius: 8px 0 0 8px;">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" id="live-filter-input" class="form-control border-start-0" placeholder="Ketik untuk filter instan..." value="{{ $search }}" style="border-radius: 0 8px 8px 0;">
                    </div>

                    @if(!empty($selectedStage))
                        {{-- Batch Action Dropdown for Selected Stage --}}
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle rounded-3 text-nowrap" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-lightning-charge me-1"></i> Aksi Massal
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li>
                                    <a class="dropdown-item text-success batch-toggle-btn" href="#" data-status="1" data-stage="{{ $selectedStage }}">
                                        <i class="bi bi-check-circle me-2"></i> Aktifkan Semua Surat Tahap {{ $selectedStage }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger batch-toggle-btn" href="#" data-status="0" data-stage="{{ $selectedStage }}">
                                        <i class="bi bi-x-circle me-2"></i> Nonaktifkan Semua Surat Tahap {{ $selectedStage }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Main Document Table Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-card-checklist text-primary fs-5"></i>
                <h5 class="fw-bold text-blue-dark mb-0">Daftar Dokumen Terdaftar</h5>
                <span class="badge bg-light text-dark border ms-2" id="filtered-counter">{{ $documents->count() }} Dokumen Ditampilkan</span>
            </div>
            <div class="small text-muted">
                <i class="bi bi-info-circle me-1"></i> Klik toggle untuk mengubah status izin pembuatan berkas
            </div>
        </div>

        <div class="table-responsive">
            <table class="table doc-table align-middle mb-0" id="documents-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 75px;">ID</th>
                        <th style="width: 170px;">Tahapan</th>
                        <th>Nama Surat / Dokumen</th>
                        <th style="width: 250px;">Base Route / Route Name</th>
                        <th class="text-center" style="width: 130px;">Status</th>
                        <th class="text-center" style="width: 100px;">Toggle</th>
                        <th style="width: 170px;">Terakhir Diubah</th>
                        <th class="text-center" style="width: 90px;">Riwayat</th>
                    </tr>
                </thead>
                <tbody id="documents-table-body">
                    @forelse ($documents as $doc)
                        <tr class="document-row" 
                            id="row-{{ $doc->id }}" 
                            data-id="{{ $doc->id }}" 
                            data-name="{{ strtolower($doc->name) }}" 
                            data-stage="{{ $doc->parent_id }}" 
                            data-status="{{ $doc->is_active ? '1' : '0' }}">
                            
                            {{-- ID --}}
                            <td class="text-center">
                                <span class="code-chip font-monospace fw-bold" title="Salin ID" onclick="copyToClipboard('{{ $doc->id }}', 'ID Surat')">
                                    {{ $doc->id }}
                                </span>
                            </td>

                            {{-- Tahapan (Stage) --}}
                            <td>
                                @if($doc->parent)
                                    <span class="badge bg-light text-secondary border px-2 py-1 small fw-semibold">
                                        {{ $doc->parent->id }} - {{ $doc->parent->name }}
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>

                            {{-- Nama Dokumen --}}
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div>
                                        <div class="fw-bold text-dark doc-name" style="font-size: 0.92rem;">
                                            {{ $doc->name }}
                                        </div>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            @if(!empty($doc->code))
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary font-monospace" style="font-size: 0.72rem;">
                                                    {{ $doc->code }}
                                                </span>
                                            @endif
                                            @if(empty($doc->route) && empty($doc->base_route))
                                                <span class="badge bg-warning bg-opacity-25 text-dark border border-warning" style="font-size: 0.7rem;">
                                                    <i class="bi bi-clock me-1"></i> Form Belum Tersedia
                                                </span>
                                            @else
                                                <span class="badge bg-info bg-opacity-10 text-primary border border-info border-opacity-25" style="font-size: 0.7rem;">
                                                    <i class="bi bi-check2 me-1"></i> Form Ready
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Base Route with Click to Copy --}}
                            <td>
                                @if(!empty($doc->base_route) || !empty($doc->route))
                                    @php
                                        $displayRoute = $doc->base_route ?: $doc->route;
                                    @endphp
                                    <span class="code-chip text-primary d-inline-block text-truncate" 
                                          style="max-width: 230px;" 
                                          title="Klik untuk salin route" 
                                          onclick="copyToClipboard('{{ $displayRoute }}', 'Route Name')">
                                        <i class="bi bi-clipboard me-1 small"></i>{{ $displayRoute }}
                                    </span>
                                @else
                                    <span class="text-muted small fst-italic">Route null</span>
                                @endif
                            </td>

                            {{-- Status Badge (with Pulse Dot) --}}
                            <td class="text-center">
                                <span class="{{ $doc->is_active ? 'badge-status-active' : 'badge-status-inactive' }}" id="badge-status-{{ $doc->id }}">
                                    <span class="pulse-dot {{ $doc->is_active ? 'pulse-dot-green' : 'pulse-dot-red' }}"></span>
                                    <span class="badge-label">{{ $doc->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                </span>
                            </td>

                            {{-- Realtime Switch Toggle --}}
                            <td class="text-center">
                                <label class="ios-switch">
                                    <input type="checkbox" 
                                           class="toggle-document-switch" 
                                           id="switch-{{ $doc->id }}" 
                                           data-id="{{ $doc->id }}" 
                                           data-name="{{ $doc->name }}" 
                                           {{ $doc->is_active ? 'checked' : '' }}>
                                    <span class="ios-slider"></span>
                                </label>
                            </td>

                            {{-- Audit Metadata --}}
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-initial" title="{{ $doc->updatedByUser ? $doc->updatedByUser->first_name : 'System' }}">
                                        {{ $doc->updatedByUser ? strtoupper(substr($doc->updatedByUser->first_name, 0, 1)) : 'S' }}
                                    </div>
                                    <div class="small line-height-sm">
                                        <div class="fw-semibold text-dark text-truncate" style="max-width: 120px;" id="updated-by-{{ $doc->id }}">
                                            {{ $doc->updatedByUser ? $doc->updatedByUser->first_name . ' ' . $doc->updatedByUser->last_name : 'System' }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.75rem;" id="updated-at-{{ $doc->id }}">
                                            {{ $doc->updated_at ? $doc->updated_at->format('d/m/Y H:i') : '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- View History Button --}}
                            <td class="text-center">
                                <button type="button" 
                                        class="btn btn-sm btn-light border rounded-pill px-2 py-1 view-history-btn" 
                                        data-id="{{ $doc->id }}" 
                                        data-name="{{ $doc->name }}" 
                                        title="Buka riwayat audit perubahan">
                                    <i class="bi bi-clock-history text-primary"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr id="empty-state-row">
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-3 text-secondary text-opacity-50"></i>
                                    <h6 class="fw-bold text-dark">Tidak ada jenis surat ditemukan</h6>
                                    <p class="small text-muted mb-0">Coba atur ulang kata kunci pencarian atau filter tahapan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    <tr id="no-search-results-row" style="display: none;">
                        <td colspan="8" class="text-center py-5 text-muted">
                            <div class="py-4">
                                <i class="bi bi-search fs-1 d-block mb-3 text-secondary text-opacity-50"></i>
                                <h6 class="fw-bold text-dark">Tidak ada surat yang sesuai dengan kata kunci</h6>
                                <p class="small text-muted mb-0">Silakan bersihkan filter pencarian untuk melihat seluruh surat.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Collapsible System Audit Trail Feed --}}
    <div class="card border-0 shadow-sm rounded-4" id="audit-trail-card">
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center" 
             style="cursor: pointer;" 
             data-bs-toggle="collapse" 
             data-bs-target="#auditFeedCollapse">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-activity text-secondary fs-5"></i>
                <h6 class="fw-bold text-blue-dark mb-0">Aktivitas & Riwayat Audit Terakhir</h6>
                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill ms-2">{{ $logs->total() }} Catatan</span>
            </div>
            <i class="bi bi-chevron-down text-muted"></i>
        </div>
        <div class="collapse show" id="auditFeedCollapse">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 170px;">Waktu Perubahan</th>
                                <th style="width: 90px;">ID Surat</th>
                                <th>Jenis Surat</th>
                                <th style="width: 170px;">Perubahan Status</th>
                                <th>Alasan Regulasi / Catatan</th>
                                <th style="width: 180px;">Eksekutor (Admin)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                                <tr>
                                    <td class="text-muted">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '-' }}
                                    </td>
                                    <td>
                                        <span class="code-chip font-monospace">{{ $log->document_category_id }}</span>
                                    </td>
                                    <td class="fw-semibold text-dark">
                                        {{ $log->documentCategory ? $log->documentCategory->name : '-' }}
                                    </td>
                                    <td>
                                        <span class="badge {{ $log->previous_status ? 'bg-success' : 'bg-danger' }} px-2 py-1">
                                            {{ $log->previous_status ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                        <i class="bi bi-arrow-right mx-1 text-muted"></i>
                                        <span class="badge {{ $log->new_status ? 'bg-success' : 'bg-danger' }} px-2 py-1">
                                            {{ $log->new_status ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-secondary">{{ $log->reason ?: '-' }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-initial" style="width: 24px; height: 24px; font-size: 0.65rem;">
                                                {{ $log->changedByUser ? strtoupper(substr($log->changedByUser->first_name, 0, 1)) : 'S' }}
                                            </div>
                                            <span class="fw-semibold text-dark">
                                                {{ $log->changedByUser ? $log->changedByUser->first_name . ' ' . $log->changedByUser->last_name : 'System' }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        Belum ada catatan riwayat perubahan regulasi surat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                    <div class="card-footer bg-white py-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 border-top">
                        <div class="small text-muted">
                            Menampilkan <strong>{{ $logs->firstItem() ?? 0 }}</strong> - <strong>{{ $logs->lastItem() ?? 0 }}</strong> dari <strong>{{ $logs->total() }}</strong> riwayat audit
                        </div>
                        <div>
                            {{ $logs->fragment('audit-trail-card')->withQueryString()->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Slide-Over / Modal Riwayat Detail per Dokumen --}}
<div class="modal fade" id="historyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #1e2269 0%, #2a3194 100%);">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-0" id="historyModalTitle">Riwayat Status Dokumen</h5>
                    <p class="text-white-50 small mb-0" id="historyModalSubtitle">Audit log regulasi & perubahan status</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="max-height: 520px; overflow-y: auto;">
                <div id="historyLoading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <div class="text-muted small mt-2">Memuat riwayat audit...</div>
                </div>
                <div id="historyTimeline" style="display: none;">
                    <ul class="timeline" id="timelineList"></ul>
                </div>
                <div id="historyEmpty" class="text-center py-4 text-muted" style="display: none;">
                    <i class="bi bi-clock-history fs-1 d-block mb-2 text-secondary opacity-50"></i>
                    Belum ada riwayat perubahan status yang tercatat untuk dokumen ini.
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>
<script>
$(document).ready(function() {
    const csrfToken = '{{ csrf_token() }}';

    // 1. Live Client-side Search Filter
    $('#live-filter-input').on('keyup', function() {
        const query = $(this).val().toLowerCase().trim();
        let visibleCount = 0;

        $('.document-row').each(function() {
            const row = $(this);
            const id = row.data('id').toString();
            const name = row.data('name');
            const route = row.find('code, .code-chip').text().toLowerCase();

            if (id.includes(query) || name.includes(query) || route.includes(query)) {
                row.show();
                visibleCount++;
            } else {
                row.hide();
            }
        });

        $('#filtered-counter').text(`${visibleCount} Dokumen Ditampilkan`);

        if (visibleCount === 0) {
            $('#no-search-results-row').show();
        } else {
            $('#no-search-results-row').hide();
        }
    });

    // 2. Realtime Stage / Category Toggle Switch Handler
    $('.toggle-stage-switch').on('change', function(e) {
        const checkbox = $(this);
        const stageId = checkbox.data('id');
        const stageName = checkbox.data('name');
        const count = checkbox.data('count');
        const targetStatus = checkbox.is(':checked');
        const statusLabel = targetStatus ? 'MENGAKTIFKAN' : 'MENONAKTIFKAN';

        let htmlMessage = '';
        if (targetStatus) {
            htmlMessage = `Apakah Anda yakin ingin <strong>MENGAKTIFKAN</strong> Kategori <strong>${stageName}</strong>?<br><span class="text-muted small">Kategori ini akan kembali tampil di pilihan berkas pengguna. Seluruh surat (${count} surat) di dalamnya juga akan diaktifkan.</span>`;
        } else {
            htmlMessage = `Apakah Anda yakin ingin <strong>MENONAKTIFKAN</strong> Kategori <strong>${stageName}</strong>?<br><div class="alert alert-warning text-start small mt-2 mb-0"><i class="bi bi-exclamation-triangle-fill me-1"></i> <strong>Perhatian:</strong> Kategori ini dan seluruh <strong>${count} surat</strong> di dalamnya otomatis tidak akan tampil sama sekali bagi user, dan akses rute mutasinya diblokir.</div>`;
        }

        Swal.fire({
            title: `Konfirmasi ${statusLabel} Kategori`,
            html: htmlMessage,
            icon: targetStatus ? 'question' : 'warning',
            input: 'text',
            inputLabel: 'Catatan / Alasan (Opsional):',
            inputPlaceholder: 'Contoh: Regulasi tahapan belum diberlakukan...',
            showCancelButton: true,
            confirmButtonColor: targetStatus ? '#198754' : '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Ya, ${statusLabel} Kategori`,
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.showLoading();
                $.ajax({
                    url: `{{ url('/cms/document-management/stage') }}/${stageId}/toggle`,
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        is_active: targetStatus ? 1 : 0,
                        cascade: 1,
                        reason: result.value || ''
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            checkbox.prop('checked', !targetStatus);
                            Swal.fire('Gagal', response.message || 'Terjadi kesalahan sistem', 'error');
                        }
                    },
                    error: function(xhr) {
                        checkbox.prop('checked', !targetStatus);
                        Swal.fire('Error', xhr.responseJSON?.message || 'Gagal menghubungi server.', 'error');
                    }
                });
            } else {
                checkbox.prop('checked', !targetStatus);
            }
        });
    });

    // 3. Realtime Toggle Switch Handler
    $('.toggle-document-switch').on('change', function(e) {
        const checkbox = $(this);
        const docId = checkbox.data('id');
        const docName = checkbox.data('name');
        const targetStatus = checkbox.is(':checked');
        const statusLabel = targetStatus ? 'AKTIF' : 'NONAKTIF';

        Swal.fire({
            title: `Konfirmasi ${statusLabel}`,
            html: `Ubah status berkas <strong>${docName}</strong> menjadi <strong class="${targetStatus ? 'text-success' : 'text-danger'}">${statusLabel}</strong>?<br><span class="text-muted small">${targetStatus ? 'User dapat membuat surat ini kembali.' : 'User tidak dapat membuat surat ini dan route mutasi akan diblokir.'}</span>`,
            icon: targetStatus ? 'question' : 'warning',
            input: 'text',
            inputLabel: 'Catatan / Alasan Perubahan (Opsional):',
            inputPlaceholder: 'Contoh: Menunggu petunjuk teknis resmi...',
            showCancelButton: true,
            confirmButtonColor: targetStatus ? '#198754' : '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Ubah Status',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const reason = result.value || '';

                $.ajax({
                    url: `{{ url('/cms/document-management') }}/${docId}/toggle`,
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        is_active: targetStatus ? 1 : 0,
                        reason: reason
                    },
                    beforeSend: function() {
                        checkbox.prop('disabled', true);
                    },
                    success: function(response) {
                        checkbox.prop('disabled', false);

                        if (response.success) {
                            // Update Status Badge with Pulse Dot
                            const badge = $(`#badge-status-${docId}`);
                            if (response.is_active) {
                                badge.removeClass('badge-status-inactive').addClass('badge-status-active');
                                badge.html('<span class="pulse-dot pulse-dot-green"></span><span class="badge-label">Aktif</span>');
                            } else {
                                badge.removeClass('badge-status-active').addClass('badge-status-inactive');
                                badge.html('<span class="pulse-dot pulse-dot-red"></span><span class="badge-label">Nonaktif</span>');
                            }

                            // Update Row Data Attribute
                            $(`#row-${docId}`).attr('data-status', response.is_active ? '1' : '0');

                            // Update Audit Meta info
                            $(`#updated-by-${docId}`).text(response.updated_by);
                            $(`#updated-at-${docId}`).text(response.updated_at);

                            // Update Global KPI Counters
                            let activeCount = parseInt($('#stat-total-active').text()) || 0;
                            let inactiveCount = parseInt($('#stat-total-inactive').text()) || 0;
                            if (response.is_active) {
                                $('#stat-total-active').text(activeCount + 1);
                                $('#stat-total-inactive').text(Math.max(0, inactiveCount - 1));
                            } else {
                                $('#stat-total-active').text(Math.max(0, activeCount - 1));
                                $('#stat-total-inactive').text(inactiveCount + 1);
                            }

                            // Toast notification
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                            Toast.fire({
                                icon: 'success',
                                title: response.message
                            });
                        } else {
                            checkbox.prop('checked', !targetStatus);
                            Swal.fire('Gagal', response.message || 'Terjadi kesalahan sistem', 'error');
                        }
                    },
                    error: function(xhr) {
                        checkbox.prop('disabled', false);
                        checkbox.prop('checked', !targetStatus);
                        const msg = xhr.responseJSON?.message || 'Gagal menghubungi server.';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            } else {
                checkbox.prop('checked', !targetStatus);
            }
        });
    });

    // 3. Batch Toggle Action per Stage
    $('.batch-toggle-btn').on('click', function(e) {
        e.preventDefault();
        const stageId = $(this).data('stage');
        const targetStatus = $(this).data('status');
        const statusLabel = targetStatus == 1 ? 'MENGAKTIFKAN' : 'MENONAKTIFKAN';

        Swal.fire({
            title: `Aksi Massal: ${statusLabel}`,
            text: `Apakah Anda yakin ingin ${statusLabel.toLowerCase()} SELURUH surat pada Tahap ${stageId}?`,
            icon: 'warning',
            input: 'text',
            inputLabel: 'Alasan perubahan massal:',
            inputPlaceholder: 'Contoh: Penyesuaian regulasi perkap...',
            showCancelButton: true,
            confirmButtonColor: targetStatus == 1 ? '#198754' : '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Jalankan Aksi Massal',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.showLoading();
                $.ajax({
                    url: '{{ route("cms.document-management.batch-toggle") }}',
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        stage_id: stageId,
                        is_active: targetStatus,
                        reason: result.value || ''
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 2000
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Gagal', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Gagal memperbarui data.', 'error');
                    }
                });
            }
        });
    });

    // 4. Sync from Enum Action
    $('#btn-sync-enum').on('click', function() {
        const btn = $(this);
        const icon = $('#sync-icon');

        Swal.fire({
            title: 'Sinkronisasi Enum ke Database',
            text: 'Proses ini akan membaca DocumentTypeEnum dan memastikan seluruh jenis surat terdaftar di database. Status aktif/nonaktif yang sudah diatur Admin TIDAK AKAN ditimpa.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#222577',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Mulai Sinkronisasi',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.prop('disabled', true);
                icon.addClass('spinner-border spinner-border-sm bi-arrow-repeat').removeClass('bi bi-arrow-repeat');

                $.ajax({
                    url: '{{ route("cms.document-management.sync") }}',
                    type: 'POST',
                    data: { _token: csrfToken },
                    success: function(response) {
                        btn.prop('disabled', false);
                        icon.removeClass('spinner-border spinner-border-sm bi-arrow-repeat').addClass('bi bi-arrow-repeat');

                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sinkronisasi Selesai!',
                                html: `<pre class="text-start bg-light p-3 small font-monospace rounded" style="max-height: 250px; overflow-y: auto;">${response.output || response.message}</pre>`,
                                confirmButtonText: 'Segarkan Halaman'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Gagal', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false);
                        icon.removeClass('spinner-border spinner-border-sm bi-arrow-repeat').addClass('bi bi-arrow-repeat');
                        Swal.fire('Error', xhr.responseJSON?.message || 'Gagal menjalankan sinkronisasi.', 'error');
                    }
                });
            }
        });
    });

    // 5. History Modal Handler
    $('.view-history-btn').on('click', function() {
        const docId = $(this).data('id');
        const docName = $(this).data('name');

        $('#historyModalTitle').text(`Riwayat Dokumen: ${docName}`);
        $('#historyModalSubtitle').text(`Kode Dokumen: ${docId}`);
        $('#historyLoading').show();
        $('#historyTimeline').hide();
        $('#historyEmpty').hide();
        $('#historyModal').modal('show');

        $.ajax({
            url: `{{ url('/cms/document-management') }}/${docId}/history`,
            type: 'GET',
            success: function(response) {
                $('#historyLoading').hide();

                if (response.success && response.logs.length > 0) {
                    let html = '';
                    response.logs.forEach(log => {
                        const isNowActive = log.new_status;
                        const badgeClass = isNowActive ? 'badge-active' : 'badge-inactive';
                        const textClass = isNowActive ? 'text-success' : 'text-danger';

                        html += `
                            <li class="timeline-item">
                                <span class="timeline-badge ${badgeClass}"></span>
                                <div class="bg-light p-3 rounded-3 border">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <div class="fw-bold ${textClass}">
                                            ${isNowActive ? '<i class="bi bi-check-circle me-1"></i> Diaktifkan' : '<i class="bi bi-x-circle me-1"></i> Dinonaktifkan'}
                                        </div>
                                        <div class="text-muted small">
                                            <i class="bi bi-clock me-1"></i> ${log.created_at}
                                        </div>
                                    </div>
                                    <div class="text-secondary small mb-2">
                                        <em>"${log.reason || 'Tidak ada catatan alasan.'}"</em>
                                    </div>
                                    <div class="d-flex align-items-center gap-1 text-muted" style="font-size: 0.78rem;">
                                        <i class="bi bi-person-fill"></i> Diubah oleh: <strong class="text-dark">${log.changed_by}</strong>
                                    </div>
                                </div>
                            </li>
                        `;
                    });

                    $('#timelineList').html(html);
                    $('#historyTimeline').show();
                } else {
                    $('#historyEmpty').show();
                }
            },
            error: function() {
                $('#historyLoading').hide();
                $('#historyEmpty').html('<span class="text-danger">Gagal memuat catatan riwayat perubahan.</span>').show();
            }
        });
    });
});

// Helper Function: Copy to Clipboard
function copyToClipboard(text, label) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
            Toast.fire({
                icon: 'info',
                title: `${label} disalin: ${text}`
            });
        });
    }
}
</script>
@endpush
