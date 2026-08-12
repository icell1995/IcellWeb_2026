@php
    $_title = 'Commander Wish';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
@endpush

@section('content')   
    <div class="loaderbg" style="display:none"></div>

    <div class="box">
        <div class="box-header">
            <h3 class="fw-bold text-blue-dark"> Commander Wish</h3>
        </div>
        <div class="boxy-body mt-4 mt-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="fw-bold text-blue-dark mt-1">Filter</h4>    
                </div>
                <div class="card-body">
                    <form action="{{ route('commander-wish.index') }}" method="GET">
                        <div class="mb-3 mt-2">
                            <legend class="fw-bold text-blue-dark">Tanggal Kejadian</legend>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                    <span class="fw-bold">Dari Tanggal <span class="text-danger">*</span></span>
                                    <input class="form-control" id="startAccidentDate" name="startAccidentDate" placeholder="YYYY-MM-DD"
                                        autocomplete="off" value="{{ old('startAccidentDate', $urlParameters['startAccidentDate']) }}" data-provide="datepicker">
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                    <span class="fw-bold">Hingga Tanggal <span class="text-danger">*</span></span>
                                    <input class="form-control" id="endAccidentDate" name="endAccidentDate" placeholder="YYYY-MM-DD"
                                        autocomplete="off" value="{{ old('endAccidentDate', $urlParameters['endAccidentDate']) }}" data-provide="datepicker">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3 mt-2">
                            <legend class="fw-bold text-blue-dark">Satker</legend>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                    <span class="fw-bold">Polda <span class="text-danger">*</span></span>
                                    <select class="form-control select2" name="regionalPolice" id="regionalPolice">
                                        <option value="">--Pilih Polda--</option>
                                        @foreach ($regionalPolices as $regionalPolice)
                                            <option value="{{ $regionalPolice->id }}"
                                                {{ (old('regionalPolice', $urlParameters['regionalPoliceId']) == $regionalPolice->id) ? 'selected' : '' }}>
                                                {{ $regionalPolice->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                    <span class="fw-bold">Polres <span class="text-danger">*</span></span>
                                    <select class="form-control select2" name="resortPolice" id="resortPolice">
                                        <option value="">--Pilih Polres--</option>
                                        
                                    </select>
                                </div> --}}
                            </div>
                        </div>

                        <hr>

                        <div class="text-center">
                            <button type="submit" class="btn btn-dark-blue" id="filterSubmit">
                                <i class="bi bi-search"></i> {{ "Cari" }}
                            </button>
                            <button type="button" class="btn btn-success" id="generatePresentation">
                                <i class="bi bi-download"></i> {{ "Unduh PPT" }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-3 table-responsive">
                <table class="table table-striped table-bordered table-users dataTable table-signatory" name="dataTable" width="100%">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Satker</th>
                            <th class="text-center">P21</th>
                            <th class="text-center">SP3</th>
                            <th class="text-center">Diversi</th>
                            <th class="text-center">POM/TNI</th>
                            <th class="text-center">SP2LID</th>
                            <th class="text-center">Presentase Kelengkapan Doc</th>
                        </tr>
                    </thead>

                    <tbody
                        @php
                            $no = 1;
                        @endphp
                
                        @foreach($performances as $performance)
                            <tr>
                                <td class="text-center align-middle">{{ $no }}</td>
                                <td class="text-center align-middle">{{ $performance->nama_polda }}</td>
                                <td class="text-center align-middle">{{ $performance->p21 }}</td>
                                <td class="text-center align-middle">{{ $performance->sp3 }}</td>
                                <td class="text-center align-middle">{{ $performance->diversi }}</td>
                                <td class="text-center align-middle">{{ $performance->pom_tni }}</td>
                                <td class="text-center align-middle">{{ $performance->sp2lid }}</td>
                                <td class="text-center align-middle">{{ $performance->persentase_keberhasilan .  ' %'}}</td>
                            </tr>

                            @php
                                $no++;
                            @endphp
                        @endforeach
                    </tbody>
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

    <!-- Delete Button -->
    <script src="{{ asset('js/laravel.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('.dataTable').DataTable({
                responsive: true,
                pageLength: 505,
                dom: 'Bfrtip',
                buttons: [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5'
                ]
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
            $('#startAccidentDate').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: "true",
                orientation: 'auto bottom',
            }).on('changeDate', function(selected) {
                var startAccidentDate = new Date(selected.date.valueOf());
                $('#endAccidentDate').datepicker('setStartAccidentDate', startAccidentDate);
            });
            $('#startAccidentDate').keydown(function(e) {
                e.preventDefault();
                return false;
            })

            $('#endAccidentDate').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: "true",
                orientation: 'auto bottom',
            }).on('changeDate', function(selected) {
                var endAccidentDate = new Date(selected.date.valueOf());
                $('#startAccidentDate').datepicker('setEndAccidentDate', endAccidentDate);
            });
            $('#endAccidentDate').keydown(function(e) {
                e.preventDefault();
                return false;
            });
        });

        /*$(document).ready(function() {
            $('#regionalPolice').on('change', function() {
                var regionalPoliceId = $(this).val();
                if (regionalPoliceId) {
                    $.ajax({
                        url: "{{ route('commander-wish.api.resort-polices') }}",
                        type: "GET",
                        data: {
                            _token: "{{ csrf_token() }}",
                            regionalPoliceId: regionalPoliceId
                        },
                        dataType: "json",
                        success: function(response) {
                            var data = response.data;

                            $('#resortPolice').empty();
                            $('#resortPolice').append('<option value="">--Pilih Polda--</option>');
                            $.each(data, function(index, resortPolice) {
                                $('#resortPolice').append('<option value="' + resortPolice.id + '">' + resortPolice.name + '</option>');
                            });
                        }
                    });
                } else {
                    $('#resortPolice').empty();
                }
            });
        });*/

        $(document).ready(function() {
            $('#generatePresentation').on('click', function() {
                var startAccidentDate = $('#startAccidentDate').val();
                var endAccidentDate = $('#endAccidentDate').val();
                var regionalPoliceId = $('#regionalPolice').val();
                var resortPoliceId = null;

                //check is not empty
                if (startAccidentDate != '' && endAccidentDate != '') {
                    var url = "{{ route('commander-wish.generate-presentation') }}" + "?startAccidentDate=" + startAccidentDate + "&endAccidentDate=" + endAccidentDate + "&regionalPolice=" + regionalPoliceId + "&resortPolice=" + resortPoliceId;
                    window.open(url, '_blank');
                } else {
                    alert('Range Tanggal kejadian tidak boleh kosong');
                }
            });
        });
    </script>
@endpush
