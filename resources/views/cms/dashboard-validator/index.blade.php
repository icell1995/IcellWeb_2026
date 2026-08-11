@extends('cms.layouts.app')

@section('_title', 'Dashboard Validasi Dokumen')

@push('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard-validator.css') }}">
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
                            Dashboard Validasi Dokumen
                            {{-- <span class="badge rounded-pill bg-success bg-opacity-10 text-success fs-6 ms-2 px-3 py-1">
                                <i class="bi bi-check-circle me-1"></i> {{ $totalValidationWeek }} Validasi
                            </span> --}}
                        </h3>
                        {{-- <span class="text-blue-dark fs-4 fw-bold">({{ $rangeDisplay }})</span> --}}
                    </div>
                    <div class="text-muted d-flex align-items-center">
                        <i class="bi bi-calendar3 me-2"></i>
                        <span>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                        <span class="mx-2">|</span>
                        <span class="d-flex align-items-center w-25">
                            <i class="bi bi-clock me-1"></i>
                            <span id="currentTime"></span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Filter Buttons -->
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-end gap-2">
                <div class="filter-container bg-white shadow-sm rounded-4 p-2 d-inline-flex align-items-center">
                    <a href="{{ route('cms.validation-dashboard', ['range' => 'today']) }}"
                        class="filter-btn btn px-4 py-2 {{ $range == 'today' ? 'active' : '' }}">
                        <i class="bi bi-calendar-day me-2"></i> Hari Ini
                    </a>
                    <a href="{{ route('cms.validation-dashboard', ['range' => 'this-week']) }}"
                        class="filter-btn btn px-4 py-2 {{ $range == 'this-week' ? 'active' : '' }}">
                        <i class="bi bi-calendar-week me-2"></i> Minggu Ini
                    </a>
                    <a href="#" class="filter-btn btn px-4 py-2 {{ $range == 'custom' ? 'active' : '' }}"
                        data-bs-toggle="modal" data-bs-target="#customDateModal">
                        <i class="bi bi-calendar-range me-2"></i> Custom
                    </a>
                </div>
                <div class="d-flex">
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
            <!-- Total Validasi Card -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card stats-card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body position-relative p-4">
                        <div class="position-absolute top-0 end-0 mt-3 me-4">
                            <div
                                class="stat-icon-container p-3 rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10">
                                <i class="bi bi-check-circle fs-4 text-primary"></i>
                            </div>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="text-uppercase fw-semibold text-secondary fs-7">Total Validasi</span>
                            <span class="text-dark fs-6 fw-bold">{{ $rangeDisplay }}</span>
                            <h2 class="stats-number my-3 display-5 fw-bold">{{ number_format($totalValidationWeek) }}</h2>
                            <div class="d-flex align-items-center mt-1">
                                <div
                                    class="trend-badge rounded-pill px-2 py-1 d-inline-flex align-items-center gap-1 
                                    {{ $weekTrend > 0
                                        ? 'bg-success bg-opacity-10 text-success'
                                        : ($weekTrend < 0
                                            ? 'bg-danger bg-opacity-10 text-danger'
                                            : 'bg-secondary bg-opacity-10 text-secondary') }}">
                                    @if ($weekTrend > 0)
                                        <i class="bi bi-graph-up-arrow"></i>
                                    @elseif($weekTrend < 0)
                                        <i class="bi bi-graph-down-arrow"></i>
                                    @else
                                        <i class="bi bi-dash"></i>
                                    @endif
                                    <span class="fw-semibold">{{ abs($weekTrend) }}%</span>
                                </div>
                                <span class="text-secondary ms-2 small">dibanding
                                    {{ $range == 'today' ? 'kemarin' : 'minggu lalu' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Validasi Hari Ini Card -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card stats-card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body position-relative p-4">
                        <div class="position-absolute top-0 end-0 mt-3 me-4">
                            <div
                                class="stat-icon-container p-3 rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10">
                                <i class="bi bi-calendar-check fs-4 text-success"></i>
                            </div>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="text-uppercase fw-semibold text-secondary fs-7">Validasi</span>
                            <span class="text-dark fs-6 fw-bold">Hari Ini</span>
                            <h2 class="stats-number my-3 display-5 fw-bold">{{ number_format($totalValidationToday) }}</h2>
                            <div class="d-flex align-items-center mt-1">
                                <div
                                    class="trend-badge rounded-pill px-2 py-1 d-inline-flex align-items-center gap-1 
                                    {{ $todayTrend > 0
                                        ? 'bg-success bg-opacity-10 text-success'
                                        : ($todayTrend < 0
                                            ? 'bg-danger bg-opacity-10 text-danger'
                                            : 'bg-secondary bg-opacity-10 text-secondary') }}">
                                    @if ($todayTrend > 0)
                                        <i class="bi bi-graph-up-arrow"></i>
                                    @elseif($todayTrend < 0)
                                        <i class="bi bi-graph-down-arrow"></i>
                                    @else
                                        <i class="bi bi-dash"></i>
                                    @endif
                                    <span class="fw-semibold">{{ abs($todayTrend) }}%</span>
                                </div>
                                <span class="text-secondary ms-2 small">dibanding kemarin</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Menunggu Validasi Card -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card stats-card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body position-relative p-4">
                        <div class="position-absolute top-0 end-0 mt-3 me-4">
                            <div
                                class="stat-icon-container p-3 rounded-circle d-flex align-items-center justify-content-center bg-danger bg-opacity-10">
                                <i class="bi bi-x-circle fs-4 text-danger"></i>
                            </div>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="text-uppercase fw-semibold text-secondary fs-7">Dokumen Dikembalikan</span>
                            <span class="text-dark fs-6 fw-bold">{{ $rangeDisplay }}</span>
                            <h2 class="stats-number my-3 display-5 fw-bold">{{ number_format($rejectedValidation) }}
                            </h2>
                            <div class="mt-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-secondary small">Persentase</span>
                                    <span
                                        class="badge bg-danger rounded-pill">{{ $rejectedValidationPercentage }}%</span>
                                </div>
                                <div class="progress progress-validation rounded-pill" style="height: 8px">
                                    <div class="progress-bar bg-danger" role="progressbar"
                                        style="width: {{ $rejectedValidationPercentage }}%"
                                        aria-valuenow="{{ $rejectedValidationPercentage }}" aria-valuemin="0"
                                        aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Menunggu Validasi Hari Ini Card -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card stats-card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body position-relative p-4">
                        <div class="position-absolute top-0 end-0 mt-3 me-4">
                            <div
                                class="stat-icon-container p-3 rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10">
                                <i class="bi bi-hourglass fs-4 text-warning"></i>
                            </div>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="text-uppercase fw-semibold text-secondary fs-7">Menunggu Validasi</span>
                            <span class="text-dark fs-6 fw-bold">Hari Ini</span>
                            <h2 class="stats-number my-3 display-5 fw-bold">{{ number_format($pendingValidationToday) }}
                            </h2>
                            <div class="mt-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-secondary small">Persentase</span>
                                    <span
                                        class="badge bg-warning rounded-pill">{{ $pendingValidationTodayPercentage }}%</span>
                                </div>
                                <div class="progress progress-validation rounded-pill" style="height: 8px">
                                    <div class="progress-bar bg-warning" role="progressbar"
                                        style="width: {{ $pendingValidationTodayPercentage }}%"
                                        aria-valuenow="{{ $pendingValidationTodayPercentage }}" aria-valuemin="0"
                                        aria-valuemax="100">
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
            <!-- Validation Trend Chart -->
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-0 p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title fw-bold m-0">Tren Validasi {{ $rangeDisplay }}</h5>
                            <div class="chart-legend d-flex gap-4">
                                <div class="d-flex align-items-center">
                                    <div class="legend-indicator bg-primary rounded-circle me-2"
                                        style="width:10px;height:10px;"></div>
                                    <span class="small text-secondary">Dokumen Divalidasi</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="legend-indicator bg-warning rounded-circle me-2"
                                        style="width:10px;height:10px;"></div>
                                    <span class="small text-secondary">Menunggu Validasi</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-4 pt-0 pb-4">
                        <div class="chart-container" style="position: relative; height:320px;">
                            <canvas id="validationTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leaderboard -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-0 p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title fw-bold m-0">Top Validator</h5>
                            <div class="btn-group btn-group-sm filter-btns p-1 bg-light rounded-pill">
                                <button type="button"
                                    class="filter-leaderboard btn btn-sm rounded-pill px-3 {{ $range == 'today' ? 'active' : '' }}"
                                    data-range="today">
                                    Hari Ini
                                </button>
                                <button type="button"
                                    class="filter-leaderboard btn btn-sm rounded-pill px-3 {{ $range == 'week' ? 'active' : '' }}"
                                    data-range="week">
                                    Minggu Ini
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush" id="validatorLeaderboard">
                            @forelse ($topValidators as $index => $validator)
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

                                                <div
                                                    class="avatar-circle d-flex justify-content-center align-items-center">
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
                                                    <small
                                                        class="text-muted">{{ $validator->role ?? 'Validator' }}</small>
                                                    <div class="progress ms-2" style="height: 5px; width: 60px;">
                                                        <div class="progress-bar {{ $index < 3 ? 'bg-success' : 'bg-primary' }}"
                                                            role="progressbar"
                                                            style="width: {{ $topValidators->count() > 0 ? ($validator->validation_count / $topValidators->first()->validation_count) * 100 : 0 }}%"
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
                    <div class="card-footer bg-white p-3 text-center">
                        <a href="{{ route('cms.case-document-validation-report.index') }}"
                            class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-list-check me-1"></i> Lihat Laporan Lengkap
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Type Stats -->
        {{-- <div class="row g-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-0 p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title fw-bold m-0">Statistik Jenis Dokumen</h5>
                            <div class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3 py-2">
                                {{ $rangeDisplay }}
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="row">
                            <div class="col-12 col-lg-8">
                                <div class="chart-container position-relative" style="height: 300px;">
                                    <canvas id="documentTypeChart"></canvas>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="table-responsive doc-type-table">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="border-0">Jenis Dokumen</th>
                                                <th class="text-center border-0">Jumlah</th>
                                                <th class="text-end border-0">Persentase</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $colors = ['#0d6efd', '#6f42c1', '#d63384', '#dc3545', '#fd7e14', '#ffc107', '#198754', '#20c997', '#0dcaf0', '#6c757d']; @endphp

                                            @foreach ($documentTypeStats as $index => $docType)
                                                <tr class="doc-type-row">
                                                    <td class="align-middle">
                                                        <div class="d-flex align-items-center">
                                                            <div class="color-indicator me-2"
                                                                style="width: 12px; height: 12px; border-radius: 50%; background-color: {{ $colors[$index % count($colors)] }}">
                                                            </div>
                                                            <span
                                                                class="doc-name">{{ $docType->document_category_name }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-center align-middle fw-semibold">
                                                        {{ number_format($docType->count) }}</td>
                                                    <td class="text-end align-middle">
                                                        <div class="d-flex align-items-center justify-content-end">
                                                            <div class="progress me-2" style="width: 40px; height: 6px;">
                                                                <div class="progress-bar"
                                                                    style="width: {{ round(($docType->count / max(1, $totalValidationWeek)) * 100, 1) }}%; background-color: {{ $colors[$index % count($colors)] }}"
                                                                    role="progressbar" aria-valuemin="0"
                                                                    aria-valuemax="100">
                                                                </div>
                                                            </div>
                                                            <span class="badge rounded-pill bg-light text-dark">
                                                                {{ round(($docType->count / max(1, $totalValidationWeek)) * 100, 1) }}%
                                                            </span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
    </div>

    <!-- Custom Date Range Modal -->
    <div class="modal fade" id="customDateModal" tabindex="-1" aria-labelledby="customDateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                <div class="modal-header border-0 bg-primary bg-opacity-10 p-4">
                    <div class="d-flex align-items-center">
                        <div class="modal-icon me-3 bg-primary bg-opacity-10 rounded-circle p-2">
                            <i class="bi bi-calendar-range text-primary fs-4"></i>
                        </div>
                        <h5 class="modal-title fw-bold" id="customDateModalLabel">Pilih Rentang Tanggal</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('cms.validation-dashboard') }}" method="GET">
                    <div class="modal-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control rounded-3" id="startDate" name="startDate"
                                        required>
                                    <label for="startDate">Tanggal Mulai</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control rounded-3" id="endDate" name="endDate"
                                        required>
                                    <label for="endDate">Tanggal Akhir</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-3 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                            <i class="bi bi-x me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-check2 me-1"></i> Terapkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Variabel yang dibutuhkan oleh file JS eksternal
        var leaderboardUrl = "{{ route('cms.validation-dashboard.leaderboard') }}";
        var validationTrendData = @json($validationTrend);
        var documentTypeData = @json($documentTypeStats);

        // Debug informasi
        console.log('Validation Trend Data:', validationTrendData);
        console.log('Document Type Data:', documentTypeData);

        // Pastikan struktur data sesuai dengan yang diharapkan
        if (!validationTrendData || !validationTrendData.labels || !validationTrendData.validated || !validationTrendData
            .pending) {
            console.warn('validationTrendData tidak memiliki struktur yang benar');
            validationTrendData = {
                labels: [],
                validated: [],
                pending: []
            };
        }

        if (!documentTypeData || !Array.isArray(documentTypeData) || documentTypeData.length === 0) {
            console.warn('documentTypeData tidak memiliki struktur yang benar');
            documentTypeData = [];
        }
    </script>
    <script src="{{ asset('js/dashboard-validator.js') }}"></script>
@endpush
