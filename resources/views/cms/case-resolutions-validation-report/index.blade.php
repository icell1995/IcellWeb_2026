@extends('cms.layouts.app')

@section('_title', 'Report Riview Selra ' . $rangeDate)

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
    {{-- Datepicker CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker3.min.css"/>
@endpush

@section('content')   
    <div class="loaderbg" style="display:none"></div>

    <div class="box">
        <div class="card rounded-2">
            {{-- NAVBAR 2 BUTTON / ROUTE SWITCH --}}
            @php
            $isMindik = request()->routeIs('cms.case-document-validation-report.*');    
            $isSelra  = request()->routeIs('cms.case-resolution-validation-report.*');
            @endphp
            <div class="p-2 bg-white">
                <div class="d-flex gap-2">
                    <a href="{{ route('cms.case-document-validation-report.index') }}"
                    class="flex-fill text-center py-3 rounded-3 border d-flex align-items-center justify-content-center gap-2 text-decoration-none
                            {{ $isMindik ? 'bg-primary text-white border-primary' : 'bg-white text-primary border-primary' }}">
                    <i class="bi bi-file-earmark"></i>
                    <span class="fw-semibold">Report Riview Mindik</span>
                    </a>
                    <a href="{{ route('cms.case-resolution-validation-report.index') }}"
                    class="flex-fill text-center py-3 rounded-3 border d-flex align-items-center justify-content-center gap-2 text-decoration-none
                            {{ $isSelra ? 'bg-primary text-white border-primary' : 'bg-white text-primary border-primary' }}">
                    <i class="bi bi-people"></i>
                    <span class="fw-semibold">Report Riview Selra</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="box-header">
            <h3 class="fw-bold text-blue-dark">Report Review Selra ({{ $rangeDate }})</h3>
        </div>
        <div class="boxy-body mt-4 mt-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="fw-bold text-blue-dark mt-1">Filter</h4>    
                </div>
                <div class="card-body">
                    {{-- Ganti ke route yang baru --}}
                    <form action="{{ route('cms.case-resolution-validation-report.index') }}" method="GET">
                        <div class="mb-3 mt-2">
                            <legend class="fw-bold text-blue-dark">Tanggal</legend>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                    <span class="fw-bold">Dari Tanggal <span class="text-danger">*</span></span>
                                    <input class="form-control" id="startApprovedDate" name="startApprovedDate" placeholder="YYYY-MM-DD"
                                        autocomplete="off" value="{{ old('startApprovedDate', $urlParameters['startApprovedDate']) }}" data-provide="datepicker">
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                    <span class="fw-bold">Hingga Tanggal <span class="text-danger">*</span></span>
                                    <input class="form-control" id="endApprovedDate" name="endApprovedDate" placeholder="YYYY-MM-DD"
                                        autocomplete="off" value="{{ old('endApprovedDate', $urlParameters['endApprovedDate']) }}" data-provide="datepicker">
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-dark-blue" id="filterSubmit">
                                <i class="bi bi-search"></i> Cari
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-3 table-responsive">
                <h5>Tanggal : {{ $rangeDate }}</h5>
                <h5>Total : {{ $reports->count() }}</h5>

                <table class="table table-striped table-bordered table-users dataTable table-signatory" name="dataTable" width="100%">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">No LP</th>
                            <th class="text-center">Jenis Selra</th>
                            <th class="text-center">No Dokumen</th>
                            <th class="text-center">Tanggal Disetujui</th>
                            <th class="text-center">Reviewer</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php $no = 1; @endphp
                        @forelse($reports as $report)
                            <tr>
                                <td class="text-center align-middle">{{ $no }}</td>
                                <td class="text-center align-middle">{{ $report->accident_number }}</td>
                                <td class="text-center align-middle">{{ $report->type_name }}</td>
                                <td class="text-center align-middle">{{ $report->document_number }}</td>
                                <td class="text-center align-middle">
                                    {{ \Carbon\Carbon::parse($report->approved_at)->locale('id')->translatedFormat('d F Y H:i:s') . ' WIB' }}
                                </td>
                                <td class="text-center align-middle">{{ $report->approved_by_name }}</td>
                            </tr>
                            @php $no++; @endphp
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Tidak ada data pada rentang tanggal ini.</td>
                            </tr>
                        @endforelse
                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-center">Total</th>
                            <th colspan="3" class="text-center">{{ $reports->count() }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>

@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" defer></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>

    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.js"></script>

    {{-- Datepicker JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>

    <!-- Delete Button -->
    <script src="{{ asset('js/laravel.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('.dataTable').DataTable({
                responsive: true,
                pageLength: 100,
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'copyHtml5',  text: 'Copy',  footer: true },
                    { extend: 'excelHtml5', text: 'Excel', footer: true },
                    { extend: 'csvHtml5',   text: 'Csv',   footer: true }
                ],
                fixedHeader: { footer: true }
            });
        });

        // Select2 with Bootstrap4 theme
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        });

        // Datepicker
        $(document).ready(function() {
            $('#startApprovedDate').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                orientation: 'auto bottom',
            }).on('changeDate', function(selected) {
                var startApprovedDate = new Date(selected.date.valueOf());
                $('#endApprovedDate').datepicker('setStartDate', startApprovedDate);
            }).on('keydown', function(e){ e.preventDefault(); });

            $('#endApprovedDate').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                orientation: 'auto bottom',
            }).on('changeDate', function(selected) {
                var endApprovedDate = new Date(selected.date.valueOf());
                $('#startApprovedDate').datepicker('setEndDate', endApprovedDate);
            }).on('keydown', function(e){ e.preventDefault(); });
        });
    </script>
@endpush
