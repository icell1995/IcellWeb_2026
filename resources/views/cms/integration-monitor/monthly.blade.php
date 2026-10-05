@extends('cms.layouts.app')

@section('_title', 'Monitor Integrasi Bulanan - CMS ICELL')

@push('style')
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css">

<style>
    .gradient-header {
        background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
        color: white;
        border-radius: .5rem .5rem 0 0;
    }
    .shadow-hover {
        transition: box-shadow 0.3s ease-in-out;
    }
    .shadow-hover:hover {
        box-shadow: 0 10px 20px rgba(0,0,0,.1)!important;
    }
    .nav-tabs .nav-link {
        font-weight: 600;
        color: #6c757d;
        border: none;
        padding: 1rem 1.5rem;
        transition: all 0.2s;
    }
    .nav-tabs .nav-link:hover {
        color: #0d6efd;
        background-color: #f8f9fa;
        border-radius: .5rem .5rem 0 0;
    }
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        background-color: #fff;
        border-bottom: 3px solid #0d6efd;
    }
    .filter-card {
        background-color: #f8f9fa;
        border-left: 5px solid #0d6efd;
    }
    /* Loading overlay */
    #loading-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255,255,255,0.8);
        z-index: 10;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: .25rem;
    }
    .summary-card {
        border: none;
        border-radius: .75rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,.1)!important;
    }
    .summary-card .card-body {
        padding: 1rem 1.25rem;
    }
    .summary-card .summary-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }
    .summary-card .summary-value {
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1.2;
    }
    .summary-card .summary-label {
        font-size: .8rem;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .5px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-blue-dark m-0"><i class="bi bi-calendar2-check me-2"></i> Monitor Integrasi Bulanan</h3>
    </div>

    <div class="card shadow-sm border-0 mb-4 filter-card">
        <div class="card-body">
            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-funnel"></i> Filter Periode Waktu</h6>
            <form id="filter-form" class="row align-items-center">
                <div class="col-md-8">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white"><i class="bi bi-calendar3"></i></span>
                        <input type="text" class="form-control" name="month_picker" id="monthPicker" placeholder="Pilih Bulan" autocomplete="off" readonly />
                    </div>
                </div>

                <div class="col-md-2 text-end mt-md-0 mt-3 align-self-end">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm"><i class="bi bi-search"></i> Terapkan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4" id="summary-cards">
        <div class="col-md col-sm-6">
            <div class="card summary-card shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="summary-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-calendar3"></i>
                    </div>
                    <div>
                        <div class="summary-value" id="summaryTotalHari">-</div>
                        <div class="summary-label">Total Hari</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md col-sm-6">
            <div class="card summary-card shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="summary-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-journal-text"></i>
                    </div>
                    <div>
                        <div class="summary-value" id="summaryTotalLog">-</div>
                        <div class="summary-label">Total Log</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md col-sm-6">
            <div class="card summary-card shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="summary-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <div class="summary-value" id="summarySuccess">-</div>
                        <div class="summary-label">Success</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md col-sm-6">
            <div class="card summary-card shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="summary-icon bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-x-circle"></i>
                    </div>
                    <div>
                        <div class="summary-value" id="summaryFailed">-</div>
                        <div class="summary-label">Failed</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md col-sm-6">
            <div class="card summary-card shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="summary-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-percent"></i>
                    </div>
                    <div>
                        <div class="summary-value" id="summarySuccessRate">-</div>
                        <div class="summary-label">Success Rate</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-hover border-0 position-relative">
        <div id="loading-overlay" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <span class="ms-2 fw-bold text-primary fs-5">Memuat Data...</span>
        </div>

        <div class="card-header bg-white p-0 border-bottom-0">
            <ul class="nav nav-tabs px-3 pt-2" id="integrationTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tar-tab" data-bs-toggle="tab" data-app="tar" type="button" role="tab"><i class="bi bi-cloud-arrow-up me-2"></i>TAR Korlantas</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="irsms-tab" data-bs-toggle="tab" data-app="irsms" type="button" role="tab"><i class="bi bi-cloud-arrow-down me-2"></i>IRSMS Korlantas</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="divtik-tab" data-bs-toggle="tab" data-app="divtik" type="button" role="tab"><i class="bi bi-shield-lock me-2"></i>Divtik Polri</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="emp-tab" data-bs-toggle="tab" data-app="emp" type="button" role="tab"><i class="bi bi-folder-symlink me-2"></i>EMP Bareskrim</button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark m-0" id="table-title">Data Monitoring Bulanan - TAR Korlantas</h5>
            </div>

            <div class="table-responsive">
                <table id="monthlyTable" class="table table-hover table-bordered w-100 align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Tanggal</th>
                            <th width="15%">Total Log</th>
                            <th width="15%">Success</th>
                            <th width="15%">Failed</th>
                            <th width="15%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data dimuat via AJAX -->
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <th colspan="2" class="text-center">TOTAL</th>
                            <th class="text-center" id="footTotalLog">0</th>
                            <th class="text-center" id="footSuccess">0</th>
                            <th class="text-center" id="footFailed">0</th>
                            <th class="text-center">-</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>
    $(document).ready(function() {
        let currentApp = 'tar';
        const now = new Date();
        const currentMonth = now.getMonth(); // 0-11
        const currentYear = now.getFullYear();

        const appNames = {
            'tar': 'TAR Korlantas',
            'irsms': 'IRSMS Korlantas',
            'divtik': 'Divtik Polri',
            'emp': 'EMP Bareskrim'
        };

        $('#monthPicker').datepicker({
            format: "mm-yyyy",
            startView: "months",
            minViewMode: "months",
            autoclose: true,
            endDate: new Date(currentYear, currentMonth, 1),
            todayHighlight: true,
            container: 'body',
            orientation: 'auto bottom'
        });

        var defaultMonth = ('0' + (currentMonth + 1)).slice(-2) + '-' + currentYear;
        $('#monthPicker').datepicker('setDate', new Date(currentYear, currentMonth, 1));

        function getSelectedMonth() {
            var val = $('#monthPicker').val();
            if (!val) return { month: currentMonth + 1, year: currentYear };
            var parts = val.split('-');
            return { month: parseInt(parts[0], 10), year: parseInt(parts[1], 10) };
        }

        function getMonthName(m) {
            const names = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            return names[m - 1] || '';
        }

        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function updateSummary(summary) {
            if (summary) {
                $('#summaryTotalHari').text(summary.total_hari + ' Hari');
                $('#summaryTotalLog').text(formatNumber(summary.total_log));
                $('#summarySuccess').text(formatNumber(summary.total_success));
                $('#summaryFailed').text(formatNumber(summary.total_failed));
                $('#summarySuccessRate').text(summary.success_rate + '%');
            } else {
                $('#summaryTotalHari, #summaryTotalLog, #summarySuccess, #summaryFailed, #summarySuccessRate').text('-');
            }
        }

        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            currentApp = $(e.target).data('app');
            $('#table-title').text('Data Monitoring Bulanan - ' + appNames[currentApp]);
            table.ajax.reload();
        });

        var table = $('#monthlyTable').DataTable({
            dom: "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4 text-center'B><'col-sm-12 col-md-4'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                    className: 'btn btn-success btn-sm px-3',
                    title: function() {
                        var sel = getSelectedMonth();
                        return 'Monitor Integrasi Bulanan - ' + appNames[currentApp] + ' - ' + getMonthName(sel.month) + ' ' + sel.year;
                    },
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5],
                        format: {
                            body: function(data, row, column, node) {
                                return (data !== null && data !== undefined ? data.toString() : '').replace(/<[^>]*>/g, '').trim();
                            }
                        }
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm px-3',
                    orientation: 'landscape',
                    title: function() {
                        var sel = getSelectedMonth();
                        return 'Monitor Integrasi Bulanan - ' + appNames[currentApp] + ' - ' + getMonthName(sel.month) + ' ' + sel.year;
                    },
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5],
                        format: {
                            body: function(data, row, column, node) {
                                return (data !== null && data !== undefined ? data.toString() : '').replace(/<[^>]*>/g, '').trim();
                            }
                        }
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="bi bi-printer"></i> Print',
                    className: 'btn btn-info btn-sm text-white px-3',
                    title: function() {
                        var sel = getSelectedMonth();
                        return 'Monitor Integrasi Bulanan - ' + appNames[currentApp] + ' - ' + getMonthName(sel.month) + ' ' + sel.year;
                    },
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5],
                        format: {
                            body: function(data, row, column, node) {
                                return (data !== null && data !== undefined ? data.toString() : '').replace(/<[^>]*>/g, '').trim();
                            }
                        }
                    }
                }
            ],
            ajax: {
                url: "{{ route('cms.integration-monitor.monthly.data') }}",
                type: "GET",
                data: function (d) {
                    var sel = getSelectedMonth();
                    d.app_type = currentApp;
                    d.month = sel.month;
                    d.year = sel.year;
                },
                dataSrc: function(json) {
                    updateSummary(json.summary);
                    return json.data;
                },
                beforeSend: function() {
                    $('#loading-overlay').fadeIn(150);
                },
                complete: function() {
                    $('#loading-overlay').fadeOut(150);
                }
            },
            columns: [
                { data: 'no', className: 'text-center' },
                { data: 'tanggal', className: 'text-center' },
                { data: 'total_log', className: 'text-center', render: function(data) { return formatNumber(data); } },
                { data: 'success', className: 'text-center', render: function(data) { return formatNumber(data); } },
                { data: 'failed', className: 'text-center', render: function(data) { return formatNumber(data); } },
                { data: 'status', className: 'text-center' }
            ],
            ordering: false,
            paging: false,
            searching: false,
            info: true,
            language: {
                emptyTable: "Tidak terdapat data log integrasi pada periode yang dipilih.",
                info: "Menampilkan _TOTAL_ hari",
            },
            footerCallback: function(row, data, start, end, display) {
                var totalLog = 0, totalSuccess = 0, totalFailed = 0;
                data.forEach(function(d) {
                    totalLog += d.total_log;
                    totalSuccess += d.success;
                    totalFailed += d.failed;
                });
                $('#footTotalLog').text(formatNumber(totalLog));
                $('#footSuccess').text(formatNumber(totalSuccess));
                $('#footFailed').text(formatNumber(totalFailed));
            }
        });

        $('#filter-form').submit(function(e) {
            e.preventDefault();
            if (!$('#monthPicker').val()) {
                alert('Silakan pilih bulan terlebih dahulu.');
                return;
            }
            table.ajax.reload();
        });

        function updateUrl() {
            var sel = getSelectedMonth();
            const params = new URLSearchParams({
                month: sel.month,
                year: sel.year,
                application: currentApp
            });
            window.history.replaceState({}, '', '{{ route("cms.integration-monitor.monthly.index") }}?' + params.toString());
        }

        $('#monthPicker').on('changeDate', updateUrl);
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', updateUrl);
        $('#filter-form').on('submit', function() {
            setTimeout(updateUrl, 100);
        });

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('month') && urlParams.has('year')) {
            var m = parseInt(urlParams.get('month'), 10);
            var y = parseInt(urlParams.get('year'), 10);
            $('#monthPicker').datepicker('setDate', new Date(y, m - 1, 1));
        }
        if (urlParams.has('application')) {
            const app = urlParams.get('application');
            currentApp = app;
            $('button[data-app="' + app + '"]').tab('show');
        }
    });
    </script>
@endpush
