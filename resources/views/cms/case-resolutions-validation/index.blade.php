@extends('cms.layouts.app')

@section('_title', 'Validasi Selra')

@section('content')
    {{-- Define $isLengthAware at the top before use --}}
    @php
        use Illuminate\Pagination\Paginator;
        use Illuminate\Pagination\SimplePaginator;
        use Illuminate\Pagination\LengthAwarePaginator;

        $isPaginator =
            $accidents instanceof Paginator ||
            $accidents instanceof SimplePaginator ||
            $accidents instanceof LengthAwarePaginator;
        $isLengthAware = $accidents instanceof LengthAwarePaginator;
    @endphp

    <div class="loaderbg" style="display:none"></div>

    <div class="box">
        <div class="card">
            <div class="card rounded-2">
                {{-- NAVBAR 2 BUTTON / ROUTE SWITCH --}}
                @php
                    $isMindik = request()->routeIs('cms.case-document-validation.*');
                    $isSelra = request()->routeIs('cms.case-resolutions-validations.*');

                    $valFrom = optional($from)->format('d-m-Y');
                    $valTo = optional($to)->format('d-m-Y');
                    $today = now()->format('d-m-Y');
                @endphp

                <div class="p-2 bg-white">
                    <div class="d-flex gap-2">
                        <a href="{{ route('cms.case-document-validation.index') }}"
                            class="flex-fill text-center py-3 rounded-3 border d-flex align-items-center justify-content-center gap-2 text-decoration-none
                      {{ $isMindik ? 'bg-primary text-white border-primary' : 'bg-white text-primary border-primary' }}">
                            <i class="bi bi-file-earmark"></i>
                            <span class="fw-semibold">Review Mindik</span>
                        </a>
                        <a href="{{ route('cms.case-resolutions-validations.index') }}"
                            class="flex-fill text-center py-3 rounded-3 border d-flex align-items-center justify-content-center gap-2 text-decoration-none
                      {{ $isSelra ? 'bg-primary text-white border-primary' : 'bg-white text-primary border-primary' }}">
                            <i class="bi bi-people"></i>
                            <span class="fw-semibold">Review Selra</span>
                        </a>

                    </div>
                </div>
            </div>

            <div class="card-body">
                <h3 class="fw-bold text-center text-primary mb-2">
                    REVIEW PENYELESAIAN PERKARA (SELRA)
                </h3>
                <p class="text-center text-muted mb-3">
                    Validasi dokumen Selra yang diunggah oleh petugas.
                </p>

                {{-- Badge counter ringkas --}}
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge text-dark px-3 py-2 rounded-3 bg-info-subtle border">
                        <h3>Total Antrian: <b>{{ number_format($pendingCount ?? 0) }}</b></h3>
                    </span>
                </div>

                <div class="mt-2 mb-3">
                    <form method="get" class="row g-2 align-items-end">
                        {{-- Jenis Selra --}}
                        <div class="col-xl-4 col-lg-3 col-md-4">
                            <label class="form-label fw-semibold text-dark mb-2 d-flex align-items-center gap-2">Jenis
                                Selra</label>
                            <select name="type" class="form-select" onchange="this.form.submit()">
                                <option value="">— Semua Jenis —</option>
                                @foreach ($types ?? collect() as $opt)
                                    <option value="{{ $opt }}"
                                        {{ ($type ?? request('type')) === $opt ? 'selected' : '' }}>
                                        {{ $opt }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Status (default Pending) --}}
                        {{-- <div class="col-xl-4 col-lg-3 col-md-4">
              <label class="form-label fw-semibold text-dark mb-2 d-flex align-items-center gap-2">Status</label>
              @php $currentStatus = $status ?? request('status', 'pending'); @endphp
              <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="pending"  {{ $currentStatus === 'pending'  ? 'selected' : '' }}>
                  Belum Divalidasi ({{ number_format($pendingCount ?? 0) }})
                </option>
                <option value="approved" {{ $currentStatus === 'approved' ? 'selected' : '' }}>
                  Disetujui ({{ number_format($approvedCount ?? 0) }})
                </option>
              </select>
            </div> --}}

                        <div class="col-xl-4 col-lg-3 col-md-4">
                            <label class="form-label fw-semibold text-dark mb-2 d-flex align-items-center gap-2">
                                Status
                            </label>
                            @php
                                $currentStatus = $status ?? request('status', 'pending');
                            @endphp
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="pending" {{ $currentStatus === 'pending' ? 'selected' : '' }}>
                                    Belum Divalidasi ({{ number_format($pendingCount ?? 0) }})
                                </option>
                                <option value="returned" {{ $currentStatus === 'returned' ? 'selected' : '' }}>
                                    Pernah Dikembalikan ({{ number_format($returnedCount ?? 0) }})
                                </option>
                                <option value="approved" {{ $currentStatus === 'approved' ? 'selected' : '' }}>
                                    Disetujui ({{ number_format($approvedCount ?? 0) }})
                                </option>
                            </select>
                        </div>

                        {{-- Spacer --}}

                        {{-- Cari Nomor LP / Satker / Polres / Polda --}}
                        <div class="col-xl-4 col-lg-3 col-md-4">
                            <label class="form-label fw-semibold text-dark mb-2 d-flex align-items-center gap-2">Cari (LP /
                                Satker / Polres / Polda)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" name="q" class="form-control"
                                    placeholder="contoh: polres rembang" value="{{ $q ?? request('q') }}">
                            </div>
                        </div>

                        {{-- Rentang Tanggal --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark mb-2 d-flex align-items-center gap-2">
                                <i class="bi bi-calendar-event"></i> Rentang Tanggal
                            </label>

                            <div class="d-flex flex-column flex-lg-row gap-2">
                                <div class="flex-grow-1">
                                    <div class="d-flex flex-column flex-md-row gap-2">
                                        <div class="input-group flex-grow-1">
                                            <span class="input-group-text bg-white">
                                                <i class="bi bi-calendar2-week"></i>&nbsp;Dari
                                            </span>
                                            <input class="form-control" type="text" id="filter_from" name="from"
                                                placeholder="DD - MM - YYYY" autocomplete="off" readonly
                                                value="{{ $valFrom }}">
                                            <span class="input-group-text bg-white border-start">
                                                <i class="bi bi-arrow-right"></i>
                                            </span>
                                            <span class="input-group-text bg-white">
                                                <i class="bi bi-calendar2-event"></i>&nbsp;Sampai
                                            </span>
                                            <input class="form-control" type="text" id="filter_to" name="to"
                                                placeholder="DD - MM - YYYY" autocomplete="off" readonly
                                                value="{{ $valTo }}">
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-primary flex-grow-1">Terapkan</button>
                                            <a href="{{ route('cms.case-resolutions-validations.index', ['status' => 'pending']) }}"
                                                class="btn btn-outline-secondary flex-grow-1 align-items-center d-flex justify-content-center">
                                                Reset
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Pertahankan per_page saat filter --}}
                        @if (request()->has('per_page'))
                            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                        @endif
                    </form>
                </div>

                {{-- Tabel --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        @if ($accidents->count() > 0)
                            <span class="text-muted">
                                Menampilkan <b>{{ $accidents->count() }}</b> dari
                                @if ($isLengthAware)
                                    <b>{{ $accidents->total() }}</b>
                                @else
                                    <b>{{ $accidents->count() }}</b>
                                @endif
                                data
                            </span>
                        @endif
                    </div>

                    <div class="d-flex gap-2">
                        {{-- Button Buka Semua di Tab Baru --}}
                        @if ($accidents->count() > 0)
                            <button type="button" class="btn btn-outline-primary btn-sm" id="openAllTabsBtn"
                                data-bs-toggle="tooltip"
                                data-bs-title="Buka semua {{ $accidents->count() }} data dalam tab baru">
                                <i class="bi bi-box-arrow-up-right"></i>
                                Buka Semua Tab ({{ $accidents->count() }})
                            </button>

                            {{-- Button Buka yang Belum Divalidasi Saja --}}
                            {{-- @php
                                $pendingInCurrentPage = $accidents->filter(function ($a) {
                                    return $a->accidentResolution && empty($a->accidentResolution->approved_at);
                                });
                            @endphp

                            @if ($pendingInCurrentPage->count() > 0)
                                <button type="button" class="btn btn-warning btn-sm" id="openPendingTabsBtn"
                                    data-bs-toggle="tooltip"
                                    data-bs-title="Buka hanya yang belum divalidasi ({{ $pendingInCurrentPage->count() }})">
                                    <i class="bi bi-clock-history"></i>
                                    Buka Pending ({{ $pendingInCurrentPage->count() }})
                                </button>
                            @endif --}}
                        @endif
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered" width="100%">
                        <thead class="table-light">
                            <tr>

                                <th class="text-center" style="width:70%">Laporan Polisi</th>
                                <th class="text-center" style="width:20%">Ringkasan SELRA</th>
                                <th class="text-center" style="width:10%">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($accidents as $accident)
                                @php
                                    $r = $accident->accidentResolution ?? null;
                                    $isApproved = !empty($r?->approved_at);
                                @endphp
                                <tr data-resolution-id="{{ $r?->id }}"
                                    data-is-approved="{{ $isApproved ? 'true' : 'false' }}"
                                    data-detail-url="{{ $r ? route('cms.case-resolutions-validations.show', $r->id) : '' }}">

                                    {{-- LP --}}
                                    <td class="align-middle text-center">
                                        {{-- Nomor LP --}}
                                        <div class="fw-semibold">{{ $accident->no_lp ?? '-' }}</div>
                                        <div class="small mt-1">
                                            <span class="badge bg-danger-subtle text-danger border me-1">
                                                Tanggal Kejadian:
                                                {{ $accident->accident_date ? \Carbon\Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y') : '-' }}
                                            </span>
                                            <span class="badge bg-primary-subtle text-primary border">
                                                Tanggal Dilaporkan:
                                                {{ $accident->report_date ? \Carbon\Carbon::parse($accident->report_date)->locale('id')->translatedFormat('d F Y') : '-' }}
                                            </span>
                                            <span class="badge bg-warning-subtle text-black border">
                                                Tanggal Tindak Lanjut:
                                                {{ $accident->created_at ? \Carbon\Carbon::parse($accident->created_at)->locale('id')->translatedFormat('d F Y') : '-' }}
                                            </span>
                                        </div>
                                        <div class="small text-muted mt-1">
                                            Satker: {{ $accident->police->full_name ?? '-' }}
                                        </div>
                                    </td>

                                    {{-- Ringkasan SELRA --}}
                                    <td class="align-middle">
                                        @if ($r)
                                            <div class="mb-1 d-flex align-items-center flex-wrap gap-1">
                                                <b>Status:</b>
                                                <span
                                                    class="badge {{ $isApproved ? 'bg-success' : 'bg-warning text-dark' }}">
                                                    {{ $isApproved ? 'Disetujui' : 'Belum Divalidasi' }}
                                                </span>
                                                @php
                                                    $lr = $accident->last_reject_at
                                                        ? \Carbon\Carbon::parse($accident->last_reject_at)
                                                        : null;
                                                    $ls = $accident->last_res_created_at
                                                        ? \Carbon\Carbon::parse($accident->last_res_created_at)
                                                        : null;
                                                    // Pernah dikembalikan jika ada REJECT sebelum SELRA terakhir
                                                    $wasRejectedBefore = $lr && $ls && $lr->lt($ls);
                                                @endphp

                                                @if ($wasRejectedBefore)
                                                    <span class="badge bg-danger-subtle text-danger border ms-1"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-title="Pernah dikembalikan pada {{ $lr->format('d-m-Y H:i') }}. Alasan: {{ $accident->last_reject_reason ?: '-' }}">
                                                        Pernah dikembalikan
                                                    </span>
                                                @endif
                                            </div>
                                            <div><b>Jenis Selra:</b> <b>{{ $r->type_name ?? '-' }}</b></div>
                                            <div><b>Upload:</b> {{ optional($r->uploaded_at)->format('d-m-Y H:i') ?? '-' }}
                                            </div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td class="text-center align-middle">
                                        @if ($r)
                                            <a href="{{ route('cms.case-resolutions-validations.show', $r->id) }}"
                                                class="btn btn-primary btn-sm">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        @else
                                            <span class="text-muted">Tidak ada SELRA</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <i class="bi bi-inboxes"></i> Tidak ada data untuk kriteria ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination + Per halaman --}}
                @if ($isPaginator)
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <form method="get" class="d-flex align-items-center gap-2">
                            {{-- pertahankan filter saat ubah per_page --}}
                            <input type="hidden" name="status" value="{{ $currentStatus }}">
                            <input type="hidden" name="type" value="{{ $type ?? request('type') }}">
                            <span class="text-muted">Per halaman:</span>
                            <select name="per_page" class="form-select form-select-sm" style="width:auto"
                                onchange="this.form.submit()">
                                @foreach ([5, 10, 25, 50, 100] as $n)
                                    <option value="{{ $n }}"
                                        {{ (int) request('per_page', 10) === $n ? 'selected' : '' }}>{{ $n }}
                                    </option>
                                @endforeach
                            </select>
                        </form>

                        <div class="text-end">
                            @if ($accidents instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                <div class="small text-muted">
                                    Halaman <b>{{ $accidents->currentPage() }}</b> dari
                                    <b>{{ $accidents->lastPage() }}</b>
                                </div>
                            @endif
                            {{ $accidents->withQueryString()->links() }}
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection

@push('script')
<script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>
{{-- Datepicker Initialization --}}
<script>
    function initDatepickersSimple() {
        const opts = {
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true,
            orientation: 'bottom auto',
            container: 'body',
            endDate: new Date()
        };

        $('#filter_from, #filter_to').datepicker(opts);

        $('#filter_from, #filter_to').prev('.input-group-text').on('click', function() {
            const $target = $(this).next('input.form-control');
            if ($target.length) $target.datepicker('show');
        });

        $('#filter_from, #filter_to').on('click', function() {
            $(this).datepicker('show');
        });
    }

    // Jalankan saat DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDatepickersSimple);
    } else {
        initDatepickersSimple();
    }
</script>

{{-- Open Multiple Tabs Functionality --}}
<script>
(function() {
    'use strict';

    // Konfigurasi
    const CONFIG = {
        delayBetweenTabs: 300,
        maxTabsWarning: 10,
        maxTabsLimit: 50,
        confirmationThreshold: 5
    };

    /**
     * Buka multiple URLs dalam tab baru dengan delay
     */
    function openMultipleTabs(urls, options = {}) {
        const {
            onProgress = null,
            onComplete = null,
            onError = null
        } = options;

        if (!urls || urls.length === 0) {
            console.warn('No URLs to open');
            return;
        }

        // Validasi jumlah tabs
        if (urls.length > CONFIG.maxTabsLimit) {
            alert(`Terlalu banyak tab! Maksimal ${CONFIG.maxTabsLimit} tab sekaligus. Anda memilih ${urls.length} tab.`);
            return;
        }

        // Konfirmasi jika banyak
        if (urls.length > CONFIG.confirmationThreshold) {
            const confirmMsg = `Anda akan membuka ${urls.length} tab baru. Lanjutkan?\n\nTips: Nonaktifkan popup blocker untuk hasil terbaik.`;
            if (!confirm(confirmMsg)) {
                return;
            }
        }

        // Show progress indicator
        const $progressContainer = showProgressIndicator(urls.length);

        let openedCount = 0;
        let errorCount = 0;

        urls.forEach((url, index) => {
            setTimeout(() => {
                try {
                    const newTab = window.open(url, '_blank');

                    if (newTab) {
                        openedCount++;
                        console.log(`✓ Opened tab ${openedCount}/${urls.length}: ${url}`);
                    } else {
                        errorCount++;
                        console.error(`✗ Failed to open tab ${index + 1}: ${url} (Popup blocked?)`);
                    }

                    // Update progress
                    if (onProgress) {
                        onProgress(openedCount, urls.length);
                    }
                    updateProgressIndicator($progressContainer, openedCount, urls.length, errorCount);

                    // Complete callback
                    if (index === urls.length - 1) {
                        setTimeout(() => {
                            hideProgressIndicator($progressContainer);

                            if (onComplete) {
                                onComplete(openedCount, errorCount);
                            }

                            // Show summary
                            showSummary(openedCount, errorCount, urls.length);
                        }, 500);
                    }
                } catch (error) {
                    errorCount++;
                    console.error(`Error opening tab ${index + 1}:`, error);

                    if (onError) {
                        onError(error, url);
                    }
                }
            }, index * CONFIG.delayBetweenTabs);
        });
    }

    /**
     * Show progress indicator
     */
    function showProgressIndicator(total) {
        const $existing = document.getElementById('tabProgressIndicator');
        if ($existing) {
            $existing.remove();
        }

        const $indicator = document.createElement('div');
        $indicator.id = 'tabProgressIndicator';
        $indicator.className = 'position-fixed bottom-0 end-0 m-4 p-4 bg-white shadow-lg rounded-3 border border-primary';
        $indicator.style.cssText = 'z-index: 9999; min-width: 300px;';
        $indicator.innerHTML = `
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-0 fw-bold">Membuka Tab...</h6>
                    <small class="text-muted">Mohon tunggu</small>
                </div>
            </div>
            <div class="progress" style="height: 8px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                     role="progressbar" 
                     style="width: 0%"
                     id="tabProgressBar"></div>
            </div>
            <div class="mt-2 text-center">
                <small class="text-muted" id="tabProgressText">0 dari ${total}</small>
            </div>
        `;

        document.body.appendChild($indicator);
        return $indicator;
    }

    /**
     * Update progress indicator
     */
    function updateProgressIndicator($container, current, total, errors) {
        if (!$container) return;

        const percentage = Math.round((current / total) * 100);
        const $progressBar = $container.querySelector('#tabProgressBar');
        const $progressText = $container.querySelector('#tabProgressText');

        if ($progressBar) {
            $progressBar.style.width = percentage + '%';
            $progressBar.setAttribute('aria-valuenow', percentage);

            if (errors > 0) {
                $progressBar.classList.remove('bg-primary');
                $progressBar.classList.add('bg-warning');
            }
        }

        if ($progressText) {
            $progressText.textContent = `${current} dari ${total}${errors > 0 ? ` (${errors} error)` : ''}`;
        }
    }

    /**
     * Hide progress indicator
     */
    function hideProgressIndicator($container) {
        if ($container) {
            setTimeout(() => {
                $container.style.opacity = '0';
                setTimeout(() => $container.remove(), 300);
            }, 1000);
        }
    }

    /**
     * Show summary notification
     */
    function showSummary(opened, errors, total) {
        const message = errors > 0 
            ? `✓ Berhasil membuka ${opened} dari ${total} tab.\n✗ ${errors} tab gagal (mungkin diblokir popup blocker).`
            : `✓ Berhasil membuka semua ${opened} tab!`;

        alert(message);
    }

    /**
     * Get all URLs from table
     */
    function getAllUrls() {
        const $rows = document.querySelectorAll('tbody tr[data-detail-url]');
        const urls = [];

        $rows.forEach($row => {
            const url = $row.getAttribute('data-detail-url');
            if (url && url.trim() && url !== '') {
                urls.push(url);
            }
        });

        console.log('getAllUrls found:', urls.length, 'URLs');
        return urls;
    }

    /**
     * Get only pending (not approved) URLs
     */
    function getPendingUrls() {
        const $rows = document.querySelectorAll('tbody tr[data-detail-url][data-is-approved="false"]');
        const urls = [];

        $rows.forEach($row => {
            const url = $row.getAttribute('data-detail-url');
            if (url && url.trim() && url !== '') {
                urls.push(url);
            }
        });

        console.log('getPendingUrls found:', urls.length, 'pending URLs');
        return urls;
    }

    /**
     * Initialize button event listeners
     */
    function initOpenTabsButtons() {
        console.log('Initializing Open Tabs buttons...');

        // Button: Open All Tabs
        const $openAllBtn = document.getElementById('openAllTabsBtn');
        if ($openAllBtn) {
            console.log('✓ Found openAllTabsBtn');
            $openAllBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Open All Tabs clicked');
                
                const urls = getAllUrls();

                if (urls.length === 0) {
                    alert('Tidak ada data untuk dibuka.');
                    return;
                }

                openMultipleTabs(urls);
            });
        } else {
            console.warn('✗ openAllTabsBtn not found');
        }

        // Button: Open Pending Tabs Only
        const $openPendingBtn = document.getElementById('openPendingTabsBtn');
        if ($openPendingBtn) {
            console.log('✓ Found openPendingTabsBtn');
            $openPendingBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Open Pending Tabs clicked');
                
                const urls = getPendingUrls();

                if (urls.length === 0) {
                    alert('Tidak ada data pending untuk dibuka.');
                    return;
                }

                openMultipleTabs(urls);
            });
        } else {
            console.warn('✗ openPendingTabsBtn not found');
        }
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initOpenTabsButtons();
            console.log('✓ Open Multiple Tabs functionality initialized (DOMContentLoaded)');
        });
    } else {
        initOpenTabsButtons();
        console.log('✓ Open Multiple Tabs functionality initialized (immediate)');
    }

})();
</script>
@endpush

@push('styles')
{{-- Ubah juga ini menjadi 'style' --}}
@endpush

@push('style')
<style>
    #tabProgressIndicator {
        transition: opacity 0.3s ease;
    }

    #openAllTabsBtn:hover,
    #openPendingTabsBtn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    #openAllTabsBtn,
    #openPendingTabsBtn {
        transition: all 0.2s ease;
    }
</style>
@endpush
