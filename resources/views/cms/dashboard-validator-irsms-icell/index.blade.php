<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Validasi Dokumen IRSMS & ICELL</title>
    <link rel="shortcut icon" href="{{ asset('images/logo2x.png') }}" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard-validator-irsms-icell.css') }}">
</head>

<body>
    <!-- Dark Mode Toggle -->
    <button class="btn-dark-toggle" id="darkModeToggle">
        <i class="bi bi-moon-stars"></i>
    </button>

    <div class="main-content">
        <!-- Full Screen Dashboard Header -->
        <div class="dashboard-header">
            <div class="header-content">
                <div
                    class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-4">
                    <div class="d-flex align-items-start gap-4">
                        <div class="dashboard-icon">
                            <div class="logo-container">
                                <img src="{{ asset('images/logo-irsms.png') }}" alt="IRSMS Logo"
                                    class="dashboard-logo logo-irsms" onerror="this.style.display='none';">
                                <div class="logo-divider"></div>
                                <img src="{{ asset('images/logoICELLTransparent.png') }}" alt="ICELL Logo"
                                    class="dashboard-logo logo-icell" onerror="this.style.display='none';">
                            </div>
                            <i class="bi bi-shield-check text-white fallback-icon"
                                style="font-size: 2.5rem; display: none;"></i>
                        </div>
                        <div>
                            <h1 class="dashboard-title h3 fw-bold mb-2">
                                Dashboard Validasi Dokumen IRSMS & ICELL
                            </h1>
                            <div class="d-flex flex-wrap align-items-center gap-4 header-info">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-calendar3 fs-5"></i>
                                    <span
                                        class="fw-medium">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2" style="width: 100px">
                                    <i class="bi bi-clock fs-5"></i>
                                    <span class="fw-medium" id="currentTime"></span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-building fs-5"></i>
                                    <span class="fw-medium">Sistem Validasi Terintegrasi</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="header-badge d-flex align-items-center gap-2">
                            <i class="bi bi-calendar-week"></i>
                            <span class="fw-semibold">{{ $rangeDisplay ?? 'Sampai hari ini' }}</span>
                        </div>
                        <button class="btn-refresh" id="refreshData">
                            <i class="bi bi-arrow-clockwise fs-5"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <!-- IRSMS Pending -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div
                        class="stats-card warning h-100 {{ ($pendingValidationTodayIrsms ?? 0) > 100 ? 'pulse-alert' : '' }}">
                        <div class="p-4 position-relative h-100">
                            <div class="position-absolute top-0 end-0 mt-3 me-3">
                                <div class="stat-icon"
                                    style="background: {{ ($pendingValidationTodayIrsms ?? 0) > 100 ? 'rgba(249, 115, 22, 0.1)' : 'rgba(245, 158, 11, 0.1)' }};">
                                    <i class="bi bi-hourglass-split fs-4"
                                        style="color: {{ ($pendingValidationTodayIrsms ?? 0) > 100 ? 'var(--orange-600)' : 'var(--amber-600)' }};"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column justify-content-between h-100">
                                <div>
                                    <span class="text-uppercase fw-semibold text-muted small">Menunggu Validasi
                                        IRSMS</span>
                                    <span class="text-base fw-bold d-block">Sampai hari ini</span>
                                </div>
                                <h2
                                    class="stats-number-warning text-center my-auto display-2 fw-bold {{ ($pendingValidationTodayIrsms ?? 0) > 100 ? 'high-alert-number' : '' }}">
                                    {{ number_format($pendingValidationTodayIrsms ?? 0) }}
                                </h2>
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted small fw-medium">Persentase</span>
                                        <span
                                            class="badge-custom badge-warning {{ ($pendingValidationTodayIrsms ?? 0) > 100 ? 'high-alert-badge' : '' }}">
                                            {{ $percentageValidate ?? 0 }}%
                                        </span>
                                    </div>
                                    <div class="progress-custom">
                                        <div class="progress-bar-custom"
                                            style="width: {{ $percentageValidate ?? 0 }}%; background: {{ ($pendingValidationTodayIrsms ?? 0) > 100 ? 'linear-gradient(135deg, var(--orange-500) 0%, var(--orange-600) 100%)' : 'linear-gradient(135deg, var(--amber-500) 0%, var(--amber-600) 100%)' }};">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- IRSMS Stats -->
                <div class="col-12 col-md-6 col-lg-3 row g-3">
                    <!-- Total Validasi IRSMS -->
                    <div class="col-12">
                        <div class="stats-card success h-100">
                            <div class="p-4 position-relative h-100">
                                <div class="position-absolute top-0 end-0 mt-3 me-3">
                                    <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1);">
                                        <i class="bi bi-check-circle fs-4" style="color: var(--emerald-600);"></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column justify-content-between h-100">
                                    <div>
                                        <span class="text-uppercase fw-semibold text-muted small">Total Validasi
                                            IRSMS</span>
                                        <span
                                            class="text-base fw-bold d-block">{{ $rangeDisplay ?? 'Sampai hari ini' }}</span>
                                    </div>
                                    <h1 class="stats-number-irsms text-center my-auto h1 fw-bold">
                                        {{ number_format($totalValidationIrsms ?? 0) }}</h1>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dokumen Dikembalikan IRSMS -->
                    <div class="col-12">
                        <div class="stats-card danger h-100">
                            <div class="p-4 position-relative h-100">
                                <div class="position-absolute top-0 end-0 mt-3 me-3">
                                    <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1);">
                                        <i class="bi bi-arrow-left-right fs-4" style="color: var(--red-600);"></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column justify-content-between h-100">
                                    <div>
                                        <span class="text-uppercase fw-semibold text-muted small">Dokumen
                                            Dikembalikan
                                            IRSMS</span>
                                        <span
                                            class="text-base fw-bold d-block">{{ $rangeDisplay ?? 'Sampai hari ini' }}</span>
                                    </div>
                                    <h1 class="stats-number-irsms text-center my-auto h1 fw-bold">
                                        {{ number_format($rejectedValidationIrsms ?? 0) }}</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ICELL Pending -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div
                        class="stats-card warning h-100 {{ ($pendingValidationTodayIcell ?? 0) > 50 ? 'pulse-alert' : '' }}">
                        <div class="p-4 position-relative h-100">
                            <div class="position-absolute top-0 end-0 mt-3 me-3">
                                <div class="stat-icon"
                                    style="background: {{ ($pendingValidationTodayIcell ?? 0) > 50 ? 'rgba(249, 115, 22, 0.1)' : 'rgba(245, 158, 11, 0.1)' }};">
                                    <i class="bi bi-hourglass-split fs-4"
                                        style="color: {{ ($pendingValidationTodayIcell ?? 0) > 50 ? 'var(--orange-600)' : 'var(--amber-600)' }};"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column justify-content-between h-100">
                                <div>
                                    <span class="text-uppercase fw-semibold text-muted small">Menunggu Validasi
                                        ICELL</span>
                                    <span class="text-base fw-bold d-block">Sampai hari ini</span>
                                </div>
                                <h2
                                    class="stats-number-warning text-center my-auto display-2 fw-bold {{ ($pendingValidationTodayIcell ?? 0) > 50 ? 'high-alert-number' : '' }}">
                                    {{ number_format($pendingValidationTodayIcell ?? 0) }}
                                </h2>
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted small fw-medium">Persentase</span>
                                        <span
                                            class="badge-custom badge-warning {{ ($pendingValidationTodayIcell ?? 0) > 50 ? 'high-alert-badge' : '' }}">
                                            {{ $pendingValidationTodayPercentageIcell ?? 0 }}%
                                        </span>
                                    </div>
                                    <div class="progress-custom">
                                        <div class="progress-bar-custom"
                                            style="width: {{ $pendingValidationTodayPercentageIcell ?? 0 }}%; background: {{ ($pendingValidationTodayIcell ?? 0) > 50 ? 'linear-gradient(135deg, var(--orange-500) 0%, var(--orange-600) 100%)' : 'linear-gradient(135deg, var(--amber-500) 0%, var(--amber-600) 100%)' }};">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ICELL Stats -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="row g-3">
                        <!-- Total Validasi ICELL -->
                        <div class="col-12">
                            <div class="stats-card success h-100">
                                <div class="p-4 position-relative h-100">
                                    <div class="position-absolute top-0 end-0 mt-3 me-3">
                                        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1);">
                                            <i class="bi bi-check-circle fs-4" style="color: var(--emerald-600);"></i>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-uppercase fw-semibold text-muted small">Total Validasi
                                            ICELL</span>
                                        <span class="fw-bold small">{{ $rangeDisplay ?? 'Sampai hari ini' }}</span>
                                        <h3 class="stats-number my-3 h4 fw-bold">
                                            {{ number_format($totalValidationIcell ?? 0) }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dokumen Dikembalikan ICELL -->
                        <div class="col-12">
                            <div class="stats-card danger">
                                <div class="p-4 position-relative">
                                    <div class="position-absolute top-0 end-0 mt-3 me-3">
                                        <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1);">
                                            <i class="bi bi-arrow-left-right fs-4" style="color: var(--red-600);"></i>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-uppercase fw-semibold text-muted small">Dokumen Dikembalikan
                                            ICELL</span>
                                        <span class="fw-bold small">{{ $rangeDisplay ?? 'Sampai hari ini' }}</span>
                                        <h3 class="stats-number my-3 h4 fw-bold">
                                            {{ number_format($rejectedValidationIcell ?? 0) }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Validasi Selra -->
                        <div class="col-12">
                            <div class="stats-card warning">
                                <div class="p-4 position-relative">
                                    <div class="position-absolute top-0 end-0 mt-3 me-3">
                                        <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1);">
                                            <i class="bi bi-file-earmark-check fs-4"
                                                style="color: rgb(245, 158, 11);"></i>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-uppercase fw-semibold text-muted small">Total Pending
                                            Validasi
                                            SELRA</span>
                                        <span class="text-base fw-bold d-block">Sampai hari ini</span>
                                        <h3 class="stats-number-warning fw-bold my-3">
                                            {{ number_format($totalPendingSelra ?? 0) }}
                                        </h3>

                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div class="text-center p-2 rounded"
                                                    style="background: rgba(245, 158, 11,0.1);">
                                                    <h6 class="my-1 fw-bold">
                                                        {{ number_format($pendingSelraCount ?? 0) }}</h6>
                                                    <small class="text-muted" style="font-size: 0.7rem;">
                                                        <i class="bi bi-hourglass"></i> Belum divalidasi
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-center p-2 rounded"
                                                    style="background: rgba(245, 158, 11,0.1);">
                                                    <h6 class="my-1 fw-bold">
                                                        {{ number_format($returnedSelraCount ?? 0) }}</h6>
                                                    <small class="text-muted" style="font-size: 0.7rem;">
                                                        <i class="bi bi-arrow-left-right"></i> Pernah Dikembalikan
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Leaderboards -->
            <div class="row g-4 mb-4">
                <!-- Leaderboard IRSMS -->
                <div class="col-12 col-lg-6">
                    <div class="leaderboard-card h-100" id="leaderboardIrsms">
                        <div class="p-4 border-bottom border-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold m-0 d-flex align-items-center">
                                    <div class="stat-icon me-2" style="background: rgba(245, 158, 11, 0.1);">
                                        <i class="bi bi-trophy-fill fs-4" style="color: var(--amber-500);"></i>
                                    </div>
                                    Top Validator IRSMS
                                </h5>

                                <!-- DROPDOWN FILTER - GANTI button group dengan dropdown -->
                                <div class="dropdown">
                                    <button
                                        class="btn btn-sm btn-outline-primary dropdown-toggle rounded-pill px-3 d-flex align-items-center gap-2"
                                        type="button" id="dropdownFilterIrsms" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bi bi-funnel"></i>
                                        <span id="filterLabelIrsms">Sampai hari ini</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm"
                                        aria-labelledby="dropdownFilterIrsms">
                                        <li>
                                            <a class="dropdown-item filter-leaderboard-irsms" href="#"
                                                data-range="today" data-system="irsms">
                                                <i class="bi bi-calendar-day me-2"></i>
                                                Hari Ini
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item filter-leaderboard-irsms" href="#"
                                                data-range="7days" data-system="irsms">
                                                <i class="bi bi-calendar-week me-2"></i>
                                                7 Hari Terakhir
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item filter-leaderboard-irsms" href="#"
                                                data-range="this_month" data-system="irsms">
                                                <i class="bi bi-calendar-week me-2"></i>
                                                Bulanan (Tanggal 1 {{ \Carbon\Carbon::now()->locale('id')->isoFormat('MMM') }} s/d {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMM') }})
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <a class="dropdown-item filter-leaderboard-irsms active" href="#"
                                                data-range="all" data-system="irsms">
                                                <i class="bi bi-calendar-range me-2"></i>
                                                Sampai hari ini
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="p-2" id="leaderboardIrsmsContent">
                            @if (($topValidatorsIrsms ?? collect())->count() > 0)
                                <!-- Top 5 IRSMS Validators -->
                                @foreach ($topValidatorsIrsms->take(5) as $index => $validator)
                                    <div class="leader-item" data-index="{{ $index }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3 position-relative">
                                                    <div class="rank-badge {{ $index < 3 ? 'rank-' . ($index + 1) : '' }}"
                                                        style="z-index: 10; position: absolute;">
                                                        {{ $index + 1 }}
                                                    </div>
                                                    <div class="avatar">
                                                        @if (!empty($validator->image))
                                                            <img src="{{ $validator->image }}"
                                                                alt="{{ $validator->name ?? 'Unknown' }}"
                                                                class="rounded-circle"
                                                                style="width: 45px; height: 45px; object-fit: cover;"
                                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                            <span class="fw-semibold" style="display: none;">
                                                                {{ strtoupper(substr($validator->name ?? 'U', 0, 1)) }}
                                                            </span>
                                                        @else
                                                            <span
                                                                class="fw-semibold">{{ strtoupper(substr($validator->name ?? 'U', 0, 1)) }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 fw-semibold">{{ $validator->name ?? 'Unknown' }}
                                                    </h6>
                                                    <div class="d-flex align-items-center">
                                                        <small
                                                            class="text-muted me-2">{{ $validator->role ?? 'Validator' }}</small>
                                                        <div class="progress-custom"
                                                            style="width: 80px; height: 4px;">
                                                            <div class="progress-bar-custom {{ $index < 3 ? 'bg-success' : 'bg-primary' }}"
                                                                style="width: {{ isset($topValidatorsIrsms[0]) ? ($validator->validation_count / $topValidatorsIrsms[0]->validation_count) * 100 : 0 }}%; background: {{ $index < 3 ? 'var(--emerald-500)' : 'var(--primary-500)' }};">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                class="badge-custom {{ $index < 3 ? 'badge-success' : 'badge-primary' }}">
                                                {{ number_format($validator->validation_count ?? 0) }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Hidden IRSMS Validators (beyond top 5) -->
                                @php
                                    $hiddenValidators = $topValidatorsIrsms->slice(5)->values();
                                @endphp

                                @foreach ($hiddenValidators as $loopIndex => $validator)
                                    @php
                                        $actualRank = $loopIndex + 6;
                                    @endphp
                                    <div class="leader-item leader-item-hidden" data-index="{{ $loopIndex + 5 }}"
                                        style="display: none;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3 position-relative">
                                                    <div class="rank-badge" style="z-index: 10; position: absolute;">
                                                        {{ $actualRank }}
                                                    </div>
                                                    <div class="avatar">
                                                        @if (!empty($validator->image))
                                                            <img src="{{ $validator->image }}"
                                                                alt="{{ $validator->name ?? 'Unknown' }}"
                                                                class="rounded-circle"
                                                                style="width: 45px; height: 45px; object-fit: cover;"
                                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                            <span class="fw-semibold" style="display: none;">
                                                                {{ strtoupper(substr($validator->name ?? 'U', 0, 1)) }}
                                                            </span>
                                                        @else
                                                            <span
                                                                class="fw-semibold">{{ strtoupper(substr($validator->name ?? 'U', 0, 1)) }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 fw-semibold">{{ $validator->name ?? 'Unknown' }}
                                                    </h6>
                                                    <div class="d-flex align-items-center">
                                                        <small
                                                            class="text-muted me-2">{{ $validator->role ?? 'Validator' }}</small>
                                                        <div class="progress-custom"
                                                            style="width: 80px; height: 4px;">
                                                            <div class="progress-bar-custom bg-primary"
                                                                style="width: {{ isset($topValidatorsIrsms[0]) ? ($validator->validation_count / $topValidatorsIrsms[0]->validation_count) * 100 : 0 }}%; background: var(--primary-500);">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="badge-custom badge-primary">
                                                {{ number_format($validator->validation_count ?? 0) }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Expand/Collapse Button -->
                                @if ($topValidatorsIrsms->count() > 5)
                                    <div class="text-center mt-3">
                                        <button
                                            class="btn btn-sm btn-outline-primary rounded-pill px-4 btn-expand-leaderboard"
                                            data-target="leaderboardIrsms" data-expanded="false">
                                            <i class="bi bi-chevron-down me-1"></i>
                                            <span class="expand-text">Lihat Semua
                                                ({{ $topValidatorsIrsms->count() - 5 }} lainnya)</span>
                                        </button>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-people text-muted opacity-25" style="font-size: 4rem;"></i>
                                    <p class="mt-3 text-muted fw-medium">Belum ada data validator</p>
                                    <p class="text-muted small">Data akan muncul setelah ada aktivitas validasi</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Leaderboard ICELL -->
                <div class="col-12 col-lg-6">
                    <div class="leaderboard-card h-100" id="leaderboardIcell">
                        <div class="p-4 border-bottom border-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold m-0 d-flex align-items-center">
                                    <div class="stat-icon me-2" style="background: rgba(245, 158, 11, 0.1);">
                                        <i class="bi bi-trophy-fill fs-4" style="color: var(--amber-500);"></i>
                                    </div>
                                    Top Validator ICELL
                                </h5>

                                <!-- DROPDOWN FILTER -->
                                <div class="dropdown">
                                    <button
                                        class="btn btn-sm btn-outline-primary dropdown-toggle rounded-pill px-3 d-flex align-items-center gap-2"
                                        type="button" id="dropdownFilterIcell" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bi bi-funnel"></i>
                                        <span id="filterLabelIcell">Sampai hari ini</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm"
                                        aria-labelledby="dropdownFilterIcell">
                                        <li>
                                            <a class="dropdown-item filter-leaderboard-icell" href="#"
                                                data-range="today" data-system="icell">
                                                <i class="bi bi-calendar-day me-2"></i>
                                                Hari Ini
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item filter-leaderboard-icell" href="#"
                                                data-range="7days" data-system="icell">
                                                <i class="bi bi-calendar-week me-2"></i>
                                                7 Hari Terakhir
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item filter-leaderboard-icell" href="#"
                                                data-range="this_month" data-system="icell">
                                                <i class="bi bi-calendar-week me-2"></i>
                                                Bulanan (Tanggal 1 {{ \Carbon\Carbon::now()->locale('id')->isoFormat('MMM') }} s/d {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMM') }})
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <a class="dropdown-item filter-leaderboard-icell active" href="#"
                                                data-range="all" data-system="icell">
                                                <i class="bi bi-calendar-range me-2"></i>
                                                Sampai hari ini
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="p-2" id="leaderboardIcellContent">
                            @if (($topValidatorsIcell ?? collect())->count() > 0)
                                @foreach ($topValidatorsIcell->take(5) as $index => $validator)
                                    <div class="leader-item" data-index="{{ $index }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3 position-relative">
                                                    <div class="rank-badge {{ $index < 3 ? 'rank-' . ($index + 1) : '' }}"
                                                        style="z-index: 10; position: absolute;">
                                                        {{ $index + 1 }}
                                                    </div>
                                                    <div class="avatar">
                                                        <span
                                                            class="fw-semibold">{{ strtoupper(substr($validator->name ?? 'U', 0, 1)) }}</span>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 fw-semibold">{{ $validator->name ?? 'Unknown' }}
                                                    </h6>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <small
                                                            class="text-muted">{{ $validator->role ?? 'Validator' }}</small>
                                                        <div class="progress-custom"
                                                            style="width: 80px; height: 4px;">
                                                            <div class="progress-bar-custom {{ $index < 3 ? 'bg-success' : 'bg-primary' }}"
                                                                style="width: {{ isset($topValidatorsIcell[0]) ? ($validator->validation_count / $topValidatorsIcell[0]->validation_count) * 100 : 0 }}%; background: {{ $index < 3 ? 'var(--emerald-500)' : 'var(--primary-500)' }};">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                class="badge-custom {{ $index < 3 ? 'badge-success' : 'badge-primary' }}">
                                                {{ number_format($validator->validation_count ?? 0) }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Hidden items (beyond top 5) -->
                                @php
                                    $hiddenValidators = $topValidatorsIcell->slice(5)->values(); // RE-INDEX!
                                @endphp

                                @foreach ($hiddenValidators as $loopIndex => $validator)
                                    @php
                                        $actualRank = $loopIndex + 6; // Loop index 0 = rank 6
                                    @endphp
                                    <div class="leader-item leader-item-hidden" data-index="{{ $loopIndex + 5 }}"
                                        style="display: none;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3 position-relative">
                                                    <div class="rank-badge" style="z-index: 10; position: absolute;">
                                                        {{ $actualRank }}
                                                    </div>
                                                    <div class="avatar">
                                                        <span
                                                            class="fw-semibold">{{ strtoupper(substr($validator->name ?? 'U', 0, 1)) }}</span>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 fw-semibold">{{ $validator->name ?? 'Unknown' }}
                                                    </h6>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <small
                                                            class="text-muted">{{ $validator->role ?? 'Validator' }}</small>
                                                        <div class="progress-custom"
                                                            style="width: 80px; height: 4px;">
                                                            <div class="progress-bar-custom bg-primary"
                                                                style="width: {{ isset($topValidatorsIcell[0]) ? ($validator->validation_count / $topValidatorsIcell[0]->validation_count) * 100 : 0 }}%; background: var(--primary-500);">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="badge-custom badge-primary">
                                                {{ number_format($validator->validation_count ?? 0) }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Expand/Collapse Button -->
                                @if ($topValidatorsIcell->count() > 5)
                                    <div class="text-center mt-3">
                                        <button
                                            class="btn btn-sm btn-outline-primary rounded-pill px-4 btn-expand-leaderboard"
                                            data-target="leaderboardIcell" data-expanded="false">
                                            <i class="bi bi-chevron-down me-1"></i>
                                            <span class="expand-text">Lihat Semua
                                                ({{ $topValidatorsIcell->count() - 5 }} lainnya)</span>
                                        </button>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-people text-muted opacity-25" style="font-size: 4rem;"></i>
                                    <p class="mt-3 text-muted fw-medium">Belum ada data validator</p>
                                    <p class="text-muted small">Data akan muncul setelah ada aktivitas validasi</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="mt-5 mb-3 text-center">
                <div class="py-3">
                    <small class="text-muted fw-medium">
                        &copy; {{ date('Y') }} IRSMS & ICELL Dashboard
                    </small>
                </div>
            </footer>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay d-none" id="loadingOverlay">
        <div class="loading-content">
            <div class="spinner"></div>
            <span class="fw-medium">Memperbarui data...</span>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Dashboard Script Variables -->
    <script>
        // Global variables for dashboard data
        @if (isset(
                $pendingValidationTodayIrsms,
                $totalValidationIrsms,
                $rejectedValidationIrsms,
                $pendingValidationTodayIcell,
                $totalValidationIcell,
                $rejectedValidationIcell,
                $pendingValidationTodayPercentageIrsms,
                $pendingValidationTodayPercentageIcell,
                $rangeDisplay,
                $irsmsApiStatus,
                $topValidatorsIrsms,
                $topValidatorsIcell))

            const dashboardData = {
                pendingValidationTodayIrsms: {{ $pendingValidationTodayIrsms ?? 0 }},
                totalValidationIrsms: {{ $totalValidationIrsms ?? 0 }},
                rejectedValidationIrsms: {{ $rejectedValidationIrsms ?? 0 }},
                pendingValidationTodayIcell: {{ $pendingValidationTodayIcell ?? 0 }},
                totalValidationIcell: {{ $totalValidationIcell ?? 0 }},
                rejectedValidationIcell: {{ $rejectedValidationIcell ?? 0 }},
                pendingValidationTodayPercentageIrsms: {{ $pendingValidationTodayPercentageIrsms ?? 0 }},
                pendingValidationTodayPercentageIcell: {{ $pendingValidationTodayPercentageIcell ?? 0 }},
                rangeDisplay: '{{ $rangeDisplay ?? 'Sampai hari ini' }}',
                irsmsApiStatus: '{{ $irsmsApiStatus ?? 'disconnected' }}',
                topValidatorsIrsms: {!! json_encode($topValidatorsIrsms ?? []) !!},
                topValidatorsIcell: {!! json_encode($topValidatorsIcell ?? []) !!}
            };
        @endif

        // Set leaderboard URL for AJAX requests
        const leaderboardUrl = '{{ url('/cms/validation-dashboard-irsms-icell/leaderboard') }}';
    </script>

    <!-- jQuery for compatibility -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Main Dashboard Script -->
    <script src="{{ asset('js/dashboard-validator-irsms-icell.js') }}"></script>
</body>

</html>
