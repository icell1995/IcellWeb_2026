@extends('cms.layouts.app')

@section('_title', 'Selra SP2LID Korban MD')

@push('style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
    .sp2lid-card {
        border-radius: 14px;
        border: none;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06);
    }
    .badge-md {
        background-color: #dc3545;
        color: #fff;
        font-size: 0.95rem;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .badge-selra {
        background-color: #ff9900;
        color: #fff;
        font-size: 0.95rem;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 20px;
    }
    .table-sp2lid th {
        background-color: #f1f5f9;
        color: #334155;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        padding: 14px 16px;
        vertical-align: middle;
    }
    .table-sp2lid td {
        padding: 14px 16px;
        vertical-align: middle;
    }
    .table-sp2lid tbody tr:hover {
        background-color: #f8fafc;
    }
    .alert-banner {
        border-left: 5px solid #dc3545;
        background: #fff5f5;
        border-radius: 10px;
        padding: 16px 20px;
    }
    .datepicker.datepicker-dropdown {
        z-index: 1060 !important;
        font-family: inherit;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        border: 1px solid #e2e8f0;
        padding: 8px;
    }
    .datepicker table tr td.active.active,
    .datepicker table tr td.active:hover {
        background-color: #0d6efd !important;
        border-radius: 4px;
    }
    .datepicker table tr td.today {
        background-color: #e0f2fe !important;
        border-color: #38bdf8 !important;
    }
    /* Sempurnakan form-control & input-group agar sejajar sempurna */
    .form-control, .input-group-text, .form-select {
        height: 38px !important;
        min-height: 38px !important;
        font-size: 0.875rem;
        margin: 0 !important;
    }
    .input-group {
        flex-wrap: nowrap;
        align-items: stretch;
    }
    .input-group-text {
        background-color: #f8fafc !important;
        border-color: #ced4da !important;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 0.75rem !important;
    }
    /* Pastikan input readonly datepicker memiliki background putih & teks rapi */
    .datepicker-input,
    input.datepicker-input[readonly] {
        background-color: #ffffff !important;
        cursor: pointer !important;
        font-weight: 500;
        color: #1e293b !important;
        border-color: #ced4da !important;
        padding: 0.375rem 0.75rem !important;
        box-shadow: none !important;
    }
    .datepicker-input:focus {
        background-color: #ffffff !important;
        border-color: #86b7fe !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15) !important;
    }
    .select2-container--bootstrap-5 .select2-selection--single {
        border-radius: 0.375rem;
        min-height: 38px !important;
        height: 38px !important;
        border-color: #ced4da;
        display: flex !important;
        align-items: center !important;
        padding: 0 2.25rem 0 0.75rem !important;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        line-height: 1.5 !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        font-size: 0.875rem;
        color: #1e293b;
    }
</style>
@endpush

@section('content')
<div class="loaderbg" style="display:none"></div>

<div class="box p-3 p-md-4">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-danger p-2 rounded-circle">
                    <i class="bi bi-exclamation-triangle-fill text-white fs-5"></i>
                </span>
                <h3 class="fw-bold text-dark m-0">Selra SP2LID Korban MD</h3>
            </div>
            <p class="text-muted m-0">
                Daftar Laporan Polisi (LP) dengan status penyelesaian perkara <strong>SP2LID</strong> namun memiliki korban <strong>Meninggal Dunia (MD)</strong>.
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="badge bg-primary text-white fs-6 px-3 py-2 rounded-pill shadow-sm" id="badgePeriodDisplay">
                <i class="bi bi-calendar-range me-1"></i> <span id="textPeriodDisplay">{{ $periodDisplay ?? 'Tahun Lalu & Tahun Ini' }}</span>
            </span>
            <span class="badge bg-danger text-white fs-6 px-3 py-2 rounded-pill shadow-sm">
                <i class="bi bi-bell-fill me-1"></i> <span id="textTotalAll">{{ number_format($totalAll) }}</span> Kasus Terdeteksi
            </span>
        </div>
    </div>

    <!-- Alert Info Penjelasan Aturan -->
    <div class="alert-banner mb-4 shadow-sm">
        <div class="d-flex align-items-start gap-3">
            <i class="bi bi-info-circle-fill text-danger fs-3"></i>
            <div>
                <h6 class="fw-bold text-danger mb-1">Perhatian Khusus Validasi SP2LID:</h6>
                <p class="small text-secondary mb-0">
                    Sesuai ketentuan standar penyidikan laka lantas, LP yang memiliki korban <strong>Meninggal Dunia (MD)</strong> pada dasarnya <strong>tidak memenuhi syarat</strong> untuk diselesaikan melalui <strong>SP2LID (Surat Penetapan Penghentian Penyelidikan)</strong> kecuali ada rekomendasi khusus gelar perkara. Silakan periksa Surat Ketetapan yang diupload oleh satker terkait melalui tombol <em>Preview</em> di tabel bawah.
                </p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card sp2lid-card h-100 p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="p-3 bg-danger bg-opacity-10 text-danger rounded-4 me-3">
                        <i class="bi bi-file-earmark-excel-fill fs-2"></i>
                    </div>
                    <div>
                        <span class="text-secondary small fw-semibold">Total LP SP2LID & MD</span>
                        <h3 class="fw-bolder text-dark mb-0" id="cardTotalAll">{{ number_format($totalAll) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card sp2lid-card h-100 p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4 me-3">
                        <i class="bi bi-person-x-fill fs-2 text-danger"></i>
                    </div>
                    <div>
                        <span class="text-secondary small fw-semibold">Total Korban Meninggal Dunia</span>
                        <h3 class="fw-bolder text-danger mb-0">
                            <span id="cardTotalMd">{{ number_format($totalMdCount) }}</span> <small class="fs-6 text-muted">Jiwa</small>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card sp2lid-card h-100 p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-4 me-3">
                        <i class="bi bi-file-earmark-check-fill fs-2"></i>
                    </div>
                    <div>
                        <span class="text-secondary small fw-semibold">Kategori Selra</span>
                        <h3 class="fw-bolder text-primary mb-0">SP2LID <small class="fs-6 text-muted">(S0108)</small></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Box -->
    <div class="card sp2lid-card bg-white mb-4">
        <div class="card-body p-3">
            <!-- Preset Tombol Periode Cepat -->
            <div class="d-flex flex-wrap align-items-center gap-2 pb-3 mb-3 border-bottom">
                <span class="small fw-bold text-secondary me-1"><i class="bi bi-clock-history me-1"></i>Periode Cepat:</span>
                <button type="button" class="btn btn-sm rounded-pill btn-period {{ ($period ?? 'this_year') === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}" data-period="all">
                    Tahun {{ $lastYear ?? (date('Y') - 1) }} & {{ $thisYear ?? date('Y') }}
                </button>
                <button type="button" class="btn btn-sm rounded-pill btn-period {{ ($period ?? 'this_year') === 'this_year' ? 'btn-primary' : 'btn-outline-secondary' }}" data-period="this_year">
                    Tahun Ini ({{ $thisYear ?? date('Y') }})
                </button>
                <button type="button" class="btn btn-sm rounded-pill btn-period btn-outline-secondary" data-period="last_year">
                    Tahun Lalu ({{ $lastYear ?? (date('Y') - 1) }})
                </button>
                <button type="button" class="btn btn-sm rounded-pill btn-period btn-outline-secondary" data-period="this_month">
                    Bulan Ini
                </button>
                <button type="button" class="btn btn-sm rounded-pill btn-period btn-outline-secondary" data-period="this_week">
                    Minggu Ini
                </button>
                <button type="button" class="btn btn-sm rounded-pill btn-period btn-outline-secondary" data-period="today">
                    Hari Ini
                </button>
            </div>

            <form id="filterForm">
                <input type="hidden" id="filter_period" name="period" value="{{ $period ?? 'this_year' }}">

                <!-- Form Filter Fields -->
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label small fw-bold text-secondary">Kategori Laka</label>
                        <select id="filter_category_laka" name="category_laka" class="form-select select2">
                            <option value="all">Semua Laka</option>
                            <option value="Kontra">Kontra</option>
                            <option value="Tunggal">Tunggal</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label small fw-bold text-secondary">Status Dokumen</label>
                        <select id="filter_file_status" name="file_status" class="form-select select2">
                            <option value="all">Semua File</option>
                            <option value="uploaded">Ada File Ketetapan</option>
                            <option value="not_uploaded">Belum Ada File</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-4 col-lg-2">
                        <label class="form-label small fw-bold text-secondary">Acuan Tanggal</label>
                        <select id="filter_date_type" name="date_type" class="form-select select2">
                            <option value="report_date">Tgl Lapor</option>
                            <option value="accident_date">Tgl Kejadian</option>
                            <option value="selra_date">Tgl Ketetapan</option>
                        </select>
                    </div>

                    <!-- Rentang Waktu Datepicker (Dari & Sampai) -->
                    <div class="col-12 col-md-4 col-lg-2">
                        <label class="form-label small fw-bold text-secondary">
                            <i class="bi bi-calendar-event me-1"></i>Dari Tanggal
                        </label>
                        <div class="input-group">
                            <span class="input-group-text" role="button"><i class="bi bi-calendar3"></i></span>
                            <input type="text" id="filter_from" name="from" 
                                   class="form-control datepicker-input text-center" 
                                   placeholder="YYYY-MM-DD" 
                                   value="{{ $fromDate ?? $minDateFormatted }}" 
                                   autocomplete="off" 
                                   readonly>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 col-lg-2">
                        <label class="form-label small fw-bold text-secondary">
                            <i class="bi bi-calendar-event me-1"></i>Sampai Tanggal
                        </label>
                        <div class="input-group">
                            <span class="input-group-text" role="button"><i class="bi bi-calendar3"></i></span>
                            <input type="text" id="filter_to" name="to" 
                                   class="form-control datepicker-input text-center" 
                                   placeholder="YYYY-MM-DD" 
                                   value="{{ $toDate ?? '' }}" 
                                   autocomplete="off" 
                                   readonly>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-12 d-flex justify-content-end gap-2 mt-3 pt-2 border-top">
                        <button type="button" id="btnResetFilter" class="btn btn-light border px-4">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                        </button>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                            <i class="bi bi-filter me-1"></i> Terapkan Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Table DataTables -->
    <div class="card sp2lid-card bg-white overflow-hidden">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark m-0">
                <i class="bi bi-list-ul text-primary me-2"></i>Daftar Laporan Polisi (SP2LID & MD)
            </h5>
            <small class="text-muted">DataTables Server-side</small>
        </div>
        <div class="table-responsive p-3">
            <table class="table table-sp2lid table-striped table-bordered align-middle w-100" id="tableSp2lidMd">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th>Nomor LP</th>
                        <th class="text-center" style="width: 140px;">Kategori Laka</th>
                        <th class="text-center" style="width: 130px;">Jumlah MD</th>
                        <th class="text-center" style="width: 130px;">Selra</th>
                        <th class="text-center" style="width: 250px;">Preview Surat Ketetapan</th>
                        <th class="text-center" style="width: 90px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Diisi otomatis oleh DataTables Server-Side -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Preview Dokumen PDF -->
<div class="modal fade" id="previewSuratModal" tabindex="-1" aria-labelledby="previewSuratModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light py-3 px-4">
                <div>
                    <h5 class="modal-title fw-bold text-primary mb-0" id="previewSuratModalLabel">
                        <i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i>Preview Dokumen Surat Ketetapan
                    </h5>
                    <small class="text-muted" id="previewModalSubtitle">Nomor LP: -</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="#" id="previewOpenTabBtn" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Buka di Tab Baru
                    </a>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0 position-relative" style="height: 75vh; min-height: 520px; background-color: #525659;">
                <div id="previewSpinner" class="position-absolute top-50 start-50 translate-middle text-center text-white">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Memuat...</span>
                    </div>
                    <p class="small mt-2 mb-0">Memuat Dokumen PDF...</p>
                </div>
                <iframe id="previewIframe" src="" class="w-100 h-100 border-0" style="display: none;"></iframe>
            </div>
            <div class="modal-footer bg-light py-2 px-4">
                <span class="text-muted small me-auto" id="previewModalFilename"></span>
                <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        const minDate = '{{ $minDateFormatted ?? "2026-01-01" }}';

        // 1. Inisialisasi Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            minimumResultsForSearch: Infinity
        });

        // 2. Inisialisasi Bootstrap Datepicker
        $('.datepicker-input').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            startDate: minDate,
            clearBtn: true,
            orientation: 'bottom auto'
        });

        // Klik icon calendar untuk membuka datepicker
        $('.input-group-text[role="button"]').on('click', function() {
            $(this).closest('.input-group').find('.datepicker-input').focus().datepicker('show');
        });

        // 3. Inisialisasi DataTables Server-Side
        const table = $('#tableSp2lidMd').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            order: [[1, 'desc']],
            pageLength: 15,
            lengthMenu: [[10, 15, 25, 50, 100], [10, 15, 25, 50, 100]],
            ajax: {
                url: "{{ route('cms.sp2lid-md.index') }}",
                data: function (d) {
                    d.period = $('#filter_period').val();
                    d.category_laka = $('#filter_category_laka').val();
                    d.file_status = $('#filter_file_status').val();
                    d.date_type = $('#filter_date_type').val();
                    d.from = $('#filter_from').val();
                    d.to = $('#filter_to').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'no_lp', name: 'accidents.no_lp' },
                { data: 'category_laka', name: 'category_laka', className: 'text-center' },
                { data: 'md', name: 'accidents.md', className: 'text-center' },
                { data: 'selra', name: 'selra', className: 'text-center', orderable: false, searchable: false },
                { data: 'preview_surat', name: 'preview_surat', className: 'text-center', orderable: false, searchable: false },
                { data: 'aksi', name: 'aksi', className: 'text-center', orderable: false, searchable: false }
            ],
            language: {
                processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Memuat data...</span></div>',
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: '<div class="text-center py-4"><i class="bi bi-check-circle-fill text-success fs-1"></i><h6 class="mt-2">Tidak Ada Data</h6><p class="text-muted small mb-0">Tidak ditemukan Laporan Polisi dengan status SP2LID & MD sesuai filter.</p></div>',
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                search: "Cari LP / Satker:",
                searchPlaceholder: "Nomor LP / Polres / Polda...",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "Berikutnya",
                    previous: "Sebelumnya"
                }
            },
            drawCallback: function(settings) {
                // Update badge & card info jika ada dari response
                const json = settings.json;
                if (json) {
                    if (json.totalAll !== undefined) {
                        const formattedTotal = Number(json.totalAll).toLocaleString('id-ID');
                        $('#textTotalAll').text(formattedTotal);
                        $('#cardTotalAll').text(formattedTotal);
                    }
                    if (json.totalMdCount !== undefined) {
                        $('#cardTotalMd').text(Number(json.totalMdCount).toLocaleString('id-ID'));
                    }
                    if (json.periodDisplay !== undefined) {
                        $('#textPeriodDisplay').text(json.periodDisplay);
                    }
                }
            }
        });

        // 4. Handle Submit Filter
        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            $('#filter_period').val('custom');
            updatePeriodButtonsState('custom');
            table.draw();
        });

        // 5. Handle Klik Preset Tombol Periode Cepat
        $('.btn-period').on('click', function() {
            const period = $(this).data('period');
            $('#filter_period').val(period);
            updatePeriodButtonsState(period);

            const thisYear = '{{ $thisYear ?? date("Y") }}';
            const lastYear = '{{ $lastYear ?? (date("Y") - 1) }}';

            if (period === 'all') {
                $('#filter_from').datepicker('update', minDate);
                $('#filter_to').datepicker('update', '');
            } else if (period === 'this_year') {
                $('#filter_from').datepicker('update', thisYear + '-01-01');
                $('#filter_to').datepicker('update', thisYear + '-12-31');
            } else if (period === 'last_year') {
                $('#filter_from').datepicker('update', lastYear + '-01-01');
                $('#filter_to').datepicker('update', lastYear + '-12-31');
            } else if (period === 'today') {
                const today = new Date().toISOString().slice(0, 10);
                $('#filter_from').datepicker('update', today);
                $('#filter_to').datepicker('update', today);
            } else {
                $('#filter_from').datepicker('update', '');
                $('#filter_to').datepicker('update', '');
            }

            table.draw();
        });

        function updatePeriodButtonsState(activePeriod) {
            $('.btn-period').each(function() {
                if ($(this).data('period') === activePeriod) {
                    $(this).removeClass('btn-outline-secondary').addClass('btn-primary');
                } else {
                    $(this).removeClass('btn-primary').addClass('btn-outline-secondary');
                }
            });
        }

        // 6. Handle Reset Filter
        $('#btnResetFilter').on('click', function() {
            const thisYear = '{{ $thisYear ?? date("Y") }}';
            $('#filter_period').val('this_year');
            $('#filter_category_laka').val('all').trigger('change');
            $('#filter_file_status').val('all').trigger('change');
            $('#filter_date_type').val('report_date').trigger('change');
            $('#filter_from').datepicker('update', thisYear + '-01-01');
            $('#filter_to').datepicker('update', thisYear + '-12-31');
            table.search('');
            updatePeriodButtonsState('this_year');
            table.draw();
        });

        // 7. Handle Klik Tombol Preview Modal (delegated untuk DataTables)
        $(document).on('click', '.btn-preview-modal', function() {
            const url = $(this).data('url');
            const noLp = $(this).data('no-lp');
            const fileName = $(this).data('filename');

            $('#previewModalSubtitle').text('LP: ' + noLp);
            $('#previewModalFilename').text('File: ' + fileName);
            $('#previewOpenTabBtn').attr('href', url);

            $('#previewSpinner').show();
            $('#previewIframe').hide().attr('src', url);

            $('#previewIframe').off('load').on('load', function() {
                $('#previewSpinner').hide();
                $(this).fadeIn(200);
            });

            const modal = new bootstrap.Modal(document.getElementById('previewSuratModal'));
            modal.show();
        });

        // Reset iframe saat modal ditutup agar memory dilepas
        $('#previewSuratModal').on('hidden.bs.modal', function () {
            $('#previewIframe').attr('src', '').hide();
            $('#previewSpinner').show();
        });
    });
</script>
@endpush
