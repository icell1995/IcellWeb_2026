@extends('cms.layouts.app')

@section('_title', 'Monitor Integrasi - CMS ICELL')

@push('style')
<!-- DataTables Buttons CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
<!-- Bootstrap Datepicker CSS -->
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
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-blue-dark m-0"><i class="bi bi-activity me-2"></i> Monitor Integrasi Log</h3>
    </div>
    
    <!-- Filter Card -->
    <div class="card shadow-sm border-0 mb-4 filter-card">
        <div class="card-body">
            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-funnel"></i> Filter Periode Waktu</h6>
            <form id="filter-form" class="row align-items-center">
                <div class="col-md-5">
                    <div class="btn-group w-100 shadow-sm" role="group" aria-label="Filter Periode">
                        <input type="radio" class="btn-check" name="filter_type" id="filterDaily" value="daily" checked>
                        <label class="btn btn-outline-primary fw-semibold" for="filterDaily">Harian</label>

                        <input type="radio" class="btn-check" name="filter_type" id="filterWeekly" value="weekly">
                        <label class="btn btn-outline-primary fw-semibold" for="filterWeekly">Mingguan</label>

                        <input type="radio" class="btn-check" name="filter_type" id="filterMonthly" value="monthly">
                        <label class="btn btn-outline-primary fw-semibold" for="filterMonthly">Bulanan</label>

                        <input type="radio" class="btn-check" name="filter_type" id="filterCustom" value="custom">
                        <label class="btn btn-outline-primary fw-semibold" for="filterCustom">Custom</label>
                    </div>
                </div>
                
                <div class="col-md-5" id="custom-date-container" style="display: none;">
                    <div class="input-daterange input-group" id="datepicker">
                        <span class="input-group-text bg-white"><i class="bi bi-calendar3"></i></span>
                        <input type="text" class="input-sm form-control" name="start_date" id="start_date" placeholder="Tanggal Mulai" autocomplete="off" />
                        <span class="input-group-text">s/d</span>
                        <input type="text" class="input-sm form-control" name="end_date" id="end_date" placeholder="Tanggal Akhir" autocomplete="off" />
                    </div>
                </div>

                <div class="col-md-2 text-end mt-md-0 mt-3 align-self-end">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm"><i class="bi bi-search"></i> Terapkan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Tabs & Table -->
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
                <h5 class="fw-bold text-dark m-0" id="table-title">Data Log Integrasi TAR Korlantas</h5>
            </div>
            
            <div class="table-responsive">
                <table id="integrationTable" class="table table-hover table-bordered w-100 align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Waktu</th>
                            <th width="10%">Status</th>
                            <th width="50%">Detail / Pesan</th>
                            <th width="20%">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data dimuat via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<!-- Bootstrap Datepicker JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
<!-- DataTables Buttons JS -->
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

    // Initialize Datepicker
    $('.input-daterange').datepicker({
        format: "dd-mm-yyyy",
        todayBtn: "linked",
        clearBtn: true,
        autoclose: true,
        todayHighlight: true
    });

    // Toggle Custom Date Filter
    $('input[name="filter_type"]').change(function() {
        if ($(this).val() === 'custom') {
            $('#custom-date-container').fadeIn();
        } else {
            $('#custom-date-container').fadeOut();
            // Optional: reset datepicker values when hiding
            // $('#start_date').val('');
            // $('#end_date').val('');
        }
    });

    // Handle Tab Click
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        currentApp = $(e.target).data('app');
        $('#table-title').text('Data Log Integrasi ' + $(e.target).text().trim());
        table.ajax.reload();
    });

    // Initialize DataTable
    var table = $('#integrationTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4 text-center'B><'col-sm-12 col-md-4'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        buttons: [
            { extend: 'excel', text: '<i class="bi bi-file-earmark-excel"></i> Excel', className: 'btn btn-success btn-sm px-3' },
            { extend: 'pdf', text: '<i class="bi bi-file-earmark-pdf"></i> PDF', className: 'btn btn-danger btn-sm px-3', orientation: 'landscape' },
            { extend: 'print', text: '<i class="bi bi-printer"></i> Print', className: 'btn btn-info btn-sm text-white px-3' }
        ],
        ajax: {
            url: "{{ route('cms.integration-monitor.data') }}",
            type: "GET",
            data: function (d) {
                d.app_type = currentApp;
                d.filter = $('input[name="filter_type"]:checked').val();
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
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
            { data: 'created_at', className: 'text-center' },
            { data: 'status', className: 'text-center' },
            { data: 'detail' },
            { data: 'ip_address', className: 'text-center' }
        ],
        order: [[1, 'desc']],
        language: {
            emptyTable: "Tidak ada data log pada periode ini",
            search: "Cari Data:",
            lengthMenu: "Tampil _MENU_ baris",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ log",
            paginate: {
                first: "Awal",
                last: "Akhir",
                next: "Maju",
                previous: "Mundur"
            }
        }
    });

    // Handle Form Submit (Filter)
    $('#filter-form').submit(function(e) {
        e.preventDefault();
        
        // Validation for custom date
        if($('input[name="filter_type"]:checked').val() === 'custom') {
            if(!$('#start_date').val() || !$('#end_date').val()) {
                alert('Silakan pilih rentang tanggal (Mulai s/d Akhir) terlebih dahulu.');
                return;
            }
        }
        
        table.ajax.reload();
    });
});
</script>
@endpush
