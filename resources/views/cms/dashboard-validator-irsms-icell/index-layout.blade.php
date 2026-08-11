@extends('cms.layouts.app')

@section('_title', 'Dashboard Validasi Dokumen IRSMS & ICELL')

@push('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard-validator-irsms-icell.css') }}">
@endpush

@section('content')
    <div class="box">
        <div
            class="box-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
            <div class="d-flex align-items-start">
                <div
                    class="dashboard-icon-wrapper bg-primary bg-opacity-10 rounded-circle px-3 py-2 align-items-center me-3 shadow-sm">
                    <i class="bi bi-shield-check text-primary fs-3"></i>
                </div>
                <div>
                    <div class="d-flex gap-2 align-items-center mb-1">
                        <h3 class="fw-bold text-blue-dark mb-1 d-flex align-items-center">
                            Dashboard Validasi Dokumen IRSMS & ICELL
                        </h3>
                    </div>
                    <div class="text-muted d-flex align-items-center">
                        <i class="bi bi-calendar3 me-2"></i>
                        <span>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                        <span class="mx-2">|</span>
                        <span class="d-flex align-items-center w-25">
                            <i class="bi bi-clock me-1"></i>
                            <span id="currentTime"></span>
                        </span>
                        @if(isset($irsmsApiData) && isset($irsmsApiData['lastUpdate']))
                            <span class="mx-2">|</span>
                            <span class="d-flex align-items-center">
                                <i class="bi bi-arrow-repeat me-1"></i>
                                <span>IRSMS Last Update: {{ \Carbon\Carbon::parse($irsmsApiData['lastUpdate'])->format('H:i') }}</span>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Refresh Button -->
            <div class="d-flex flex-wrap align-items-start justify-content-center justify-content-md-end gap-2">
                <div class="d-flex gap-2">
                    <span class="fs-6 badge rounded-5 bg-primary bg-opacity-10 text-primary p-3">
                        <i class="bi bi-calendar3 me-2"></i>
                        {{ $rangeDisplay }}
                    </span>
                    <button id="refreshData"
                        class="btn btn-light border-0 shadow-sm rounded-circle d-flex align-items-center justify-content-center pulse-button"
                        style="width: 40px; height: 40px;">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="box-body mt-3">
        <!-- Stats Cards Row -->
        <div class="row g-4 mb-4">
            <!-- Menunggu Validasi Hari Ini Card - IRSMS -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card stats-card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body position-relative p-4">
                        <div class="position-absolute top-0 end-0 mt-3 me-4">
                            <div
                                class="stat-icon-container p-3 rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10">
                                <i class="bi bi-hourglass-split fs-4 text-warning"></i>
                            </div>
                        </div>
                        <div class="d-flex flex-column justify-content-between h-100">
                            <div>
                                <span class="text-uppercase fw-semibold text-secondary fs-7">Menunggu Validasi IRSMS
                                </span>
                                <span class="text-dark fs-6 fw-bold d-block">Hari Ini</span>
                            </div>
                            <h2 class="stats-number text-center my-auto display-6 fw-bold text-warning">
                                {{ number_format($pendingValidationTodayIrsms) }}
                            </h2>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-secondary small">Persentase</span>
                                    <span
                                        class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2">{{ $pendingValidationTodayPercentageIrsms }}%</span>
                                </div>
                                <div class="progress progress-validation rounded-pill" style="height: 8px">
                                    <div class="progress-bar bg-warning" role="progressbar"
                                        style="width: {{ $pendingValidationTodayPercentageIrsms }}%"
                                        aria-valuenow="{{ $pendingValidationTodayPercentageIrsms }}" aria-valuemin="0"
                                        aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Total Validasi & Dokumen Dikembalikan IRSMS -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="row g-4">
                    <!-- Total Validasi Card IRSMS -->
                    <div class="col-12">
                        <div class="card stats-card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-body position-relative p-4">
                                <div class="position-absolute top-0 end-0 mt-3 me-4">
                                    <div
                                        class="stat-icon-container p-3 rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10">
                                        <i class="bi bi-check-circle fs-4 text-primary"></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="text-uppercase fw-semibold text-secondary fs-7">Total Validasi IRSMS</span>
                                    <span class="text-dark fs-7 fw-bold">{{ $rangeDisplay }}</span>
                                    <h2 class="stats-number my-3 fs-5 fw-bold">{{ number_format($totalValidationIrsms) }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dokumen Dikembalikan IRSMS -->
                    <div class="col-12">
                        <div class="card stats-card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-body position-relative p-4">
                                <div class="position-absolute top-0 end-0 mt-3 me-4">
                                    <div
                                        class="stat-icon-container p-3 rounded-circle d-flex align-items-center justify-content-center bg-danger bg-opacity-10">
                                        <i class="bi bi-x-circle fs-4 text-danger"></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="text-uppercase fw-semibold text-secondary fs-7">Dokumen Dikembalikan IRSMS</span>
                                    <span class="text-dark fs-7 fw-bold">{{ $rangeDisplay }}</span>
                                    <h2 class="stats-number my-3 fs-5 fw-bold">{{ number_format($rejectedValidationIrsms) }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Menunggu Validasi ICELL Card -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card stats-card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body position-relative p-4">
                        <div class="position-absolute top-0 end-0 mt-3 me-4">
                            <div
                                class="stat-icon-container p-3 rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10">
                                <i class="bi bi-hourglass-split fs-4 text-warning"></i>
                            </div>
                        </div>
                        <div class="d-flex flex-column justify-content-between h-100">
                            <div>
                                <span class="text-uppercase fw-semibold text-secondary fs-7">Menunggu Validasi ICELL</span>
                                <span class="text-dark fs-6 fw-bold d-block">Hari ini</span>
                            </div>
                            <h2 class="stats-number text-center my-auto display-6 fw-bold text-warning">
                                {{ number_format($pendingValidationTodayIcell) }}
                            </h2>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-secondary small">Persentase</span>
                                    <span
                                        class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2">{{ $pendingValidationTodayPercentageIcell }}%</span>
                                </div>
                                <div class="progress progress-validation rounded-pill" style="height: 8px">
                                    <div class="progress-bar bg-warning" role="progressbar"
                                        style="width: {{ $pendingValidationTodayPercentageIcell }}%"
                                        aria-valuenow="{{ $pendingValidationTodayPercentageIcell }}" aria-valuemin="0"
                                        aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Total Validasi & Dokumen Dikembalikan ICELL -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="row g-4">
                    <!-- Total Validasi ICELL Card -->
                    <div class="col-12">
                        <div class="card stats-card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-body position-relative p-4">
                                <div class="position-absolute top-0 end-0 mt-3 me-4">
                                    <div
                                        class="stat-icon-container p-3 rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10">
                                        <i class="bi bi-check-circle fs-4 text-primary"></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="text-uppercase fw-semibold text-secondary fs-7">Total Validasi ICELL</span>
                                    <span class="text-dark fs-7 fw-bold">{{ $rangeDisplay }}</span>
                                    <h2 class="stats-number my-3 fs-5 fw-bold">{{ number_format($totalValidationIcell) }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dokumen Dikembalikan ICELL -->
                    <div class="col-12">
                        <div class="card stats-card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-body position-relative p-4">
                                <div class="position-absolute top-0 end-0 mt-3 me-4">
                                    <div
                                        class="stat-icon-container p-3 rounded-circle d-flex align-items-center justify-content-center bg-danger bg-opacity-10">
                                        <i class="bi bi-x-circle fs-4 text-danger"></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="text-uppercase fw-semibold text-secondary fs-7">Dokumen
                                        Dikembalikan ICELL</span>
                                    <span class="text-dark fs-7 fw-bold">{{ $rangeDisplay }}</span>
                                    <h2 class="stats-number my-3 fs-5 fw-bold">{{ number_format($rejectedValidationIcell) }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Leaderboard Row -->
    <div class="row g-4 mb-4">
        <!-- Leaderboard IRSMS -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title fw-bold m-0">Top Validator IRSMS</h5>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                            <i class="bi bi-calendar3 me-1"></i> {{ $rangeDisplay }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush" id="validatorLeaderboardIrsms">
                        @forelse ($topValidatorsIrsms as $index => $validator)
                            <li class="list-group-item leader-card border-0 px-4 py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3 position-relative">
                                            @if ($index < 3)
                                                <div
                                                    class="validator-rank position-absolute top-0 start-0 translate-middle d-flex align-items-center justify-content-center 
                                                            {{ $index < 3 ? 'top-rank rank-' . ($index + 1) : '' }}">
                                                    {{ $index + 1 }}
                                                </div>
                                            @else
                                                <div
                                                    class="validator-rank position-absolute top-0 start-0 translate-middle d-flex align-items-center justify-content-center">
                                                    {{ $index + 1 }}
                                                </div>
                                            @endif

                                            <div class="avatar-circle d-flex justify-content-center align-items-center">
                                                <span
                                                    class="avatar-initials">{{ strtoupper(substr($validator->name, 0, 1)) }}</span>
                                                @if ($index < 3)
                                                    <div class="position-absolute top-0 end-0 translate-middle">
                                                        <i class="bi bi-trophy-fill text-warning"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-semibold">{{ $validator->name }}</h6>
                                            <div class="d-flex align-items-center">
                                                <small class="text-muted">{{ $validator->role ?? 'Validator' }}</small>
                                                <div class="progress ms-2" style="height: 5px; width: 60px;">
                                                    <div class="progress-bar {{ $index < 3 ? 'bg-success' : 'bg-primary' }}"
                                                        role="progressbar"
                                                        style="width: {{ $topValidatorsIrsms->count() > 0 ? ($validator->validation_count / $topValidatorsIrsms->first()->validation_count) * 100 : 0 }}%"
                                                        aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="badge rounded-pill {{ $index < 3 ? 'bg-success' : 'bg-primary' }} px-3 py-2">
                                        {{ $validator->validation_count }}
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-center py-5">
                                <i class="bi bi-people text-muted opacity-25" style="font-size: 3rem;"></i>
                                <p class="mt-3 text-muted">Belum ada data validator</p>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Leaderboard ICELL -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title fw-bold m-0">Top Validator ICELL</h5>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                            <i class="bi bi-calendar3 me-1"></i> {{ $rangeDisplay }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush" id="validatorLeaderboardIcell">
                        @forelse ($topValidatorsIcell as $index => $validator)
                            <li class="list-group-item leader-card border-0 px-4 py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3 position-relative">
                                            @if ($index < 3)
                                                <div
                                                    class="validator-rank position-absolute top-0 start-0 translate-middle d-flex align-items-center justify-content-center 
                                                            {{ $index < 3 ? 'top-rank rank-' . ($index + 1) : '' }}">
                                                    {{ $index + 1 }}
                                                </div>
                                            @else
                                                <div
                                                    class="validator-rank position-absolute top-0 start-0 translate-middle d-flex align-items-center justify-content-center">
                                                    {{ $index + 1 }}
                                                </div>
                                            @endif

                                            <div class="avatar-circle d-flex justify-content-center align-items-center">
                                                <span
                                                    class="avatar-initials">{{ strtoupper(substr($validator->name, 0, 1)) }}</span>
                                                @if ($index < 3)
                                                    <div class="position-absolute top-0 end-0 translate-middle">
                                                        <i class="bi bi-trophy-fill text-warning"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-semibold">{{ $validator->name }}</h6>
                                            <div class="d-flex align-items-center">
                                                <small class="text-muted">{{ $validator->role ?? 'Validator' }}</small>
                                                <div class="progress ms-2" style="height: 5px; width: 60px;">
                                                    <div class="progress-bar {{ $index < 3 ? 'bg-success' : 'bg-primary' }}"
                                                        role="progressbar"
                                                        style="width: {{ $topValidatorsIcell->count() > 0 ? ($validator->validation_count / $topValidatorsIcell->first()->validation_count) * 100 : 0 }}%"
                                                        aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="badge rounded-pill {{ $index < 3 ? 'bg-success' : 'bg-primary' }} px-3 py-2">
                                        {{ $validator->validation_count }}
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-center py-5">
                                <i class="bi bi-people text-muted opacity-25" style="font-size: 3rem;"></i>
                                <p class="mt-3 text-muted">Belum ada data validator</p>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Variabel yang dibutuhkan oleh file JS eksternal
        var leaderboardUrl = "{{ route('cms.validation-dashboard-irsms-icell.leaderboard') }}";
        
        // Tampilkan tahun saat ini untuk header
        var currentYear = {{ Carbon\Carbon::now()->year }};

        // Inisialisasi data dashboard dari controller
        var dashboardData = {
            totalValidationIrsms: {{ $totalValidationIrsms }},
            totalValidationTodayIrsms: {{ $totalValidationTodayIrsms }},
            rejectedValidationIrsms: {{ $rejectedValidationIrsms }},
            pendingValidationTodayIrsms: {{ $pendingValidationTodayIrsms }},
            pendingValidationTodayPercentageIrsms: {{ $pendingValidationTodayPercentageIrsms }},

            totalValidationIcell: {{ $totalValidationIcell }},
            totalValidationTodayIcell: {{ $totalValidationTodayIcell }},
            rejectedValidationIcell: {{ $rejectedValidationIcell }},
            pendingValidationTodayIcell: {{ $pendingValidationTodayIcell }},
            pendingValidationTodayPercentageIcell: {{ $pendingValidationTodayPercentageIcell }},

            irsmsApiStatus: '{{ isset($irsmsApiData) ? "connected" : "api_error" }}',
            rangeDisplay: '{{ $rangeDisplay }}'
        };

        // Tambahkan data top validators jika tersedia
        @if(isset($topValidatorsIrsms) && count($topValidatorsIrsms) > 0)
            dashboardData.topValidatorsIrsms = {!! json_encode($topValidatorsIrsms) !!};
        @endif

        @if(isset($topValidatorsIcell) && count($topValidatorsIcell) > 0)
            dashboardData.topValidatorsIcell = {!! json_encode($topValidatorsIcell) !!};
        @endif
    </script>
    <script src="{{ asset('js/dashboard-validator-irsms-icell.js') }}"></script>
@endpush
