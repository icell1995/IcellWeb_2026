@php
    $_title = 'Laporan Bulanan';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <style>
        .select2-container--default .select2-selection--single {
            height: 38px !important;
            line-height: 38px !important;
            border: 1px solid #ced4da !important;
            border-radius: .25rem !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
            padding-left: 12px !important;
            color: #495057 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
    </style>
@endpush

@section('title', $_title)

@section('content')
    <div class="box">
        <div class="box-header">
            <h3 class="text-blue-dark fw-semibold mb-2">Laporan Bulanan</h3>
            <fieldset class="border rounded-3 p-3">
                <form class="form_statistik_hari row mt-2">
                    @csrf
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-3">
                        <label for="polda" class="fw-semibold mb-1">Polda</label>
                        {{-- <span id="get_user" style="">{{ Auth::user()->role_id }}</span>
                                <span id="get_polda" style="">{{ Auth::user()->polda_id }}</span> --}}
                        <select id="polda_id" name="polda_id" class="form-select select2 @error('polda_id') is-invalid @enderror">
                            @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                                <option value="-">Semua Polda</option>
                            @endif
                            @foreach ($polda as $poldas)
                                @if(in_array($poldas->name, ['POLDA XE', 'PJR INDUK KORLANTAS', 'PUSDIKLANTAS', 'DIT GAKKUM KORLANTAS']))
                                    @continue
                                @endif
                                <option value="{{ $poldas->id }}" {{ old('polda_id') == $poldas->id ? 'selected' : '' }}>
                                    {{ $poldas->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-3">
                        <label for="polres" class="fw-semibold mb-1">Polres</label>
                        <select id="polres_id" name="polres_id"
                            class="form-select select2 @error('polres_id') is-invalid @enderror">
                            @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                                <option value="-">Pilih Polres</option>
                            @endif
                            @foreach ($polres as $polress)
                                <option value="{{ $polress->id }}"
                                    {{ old('polres_id') == $polress->id ? 'selected' : '' }}>
                                    {{ $polress->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-3">
                        <label for="Bulan" class="fw-semibold mb-1">Bulan (Berdasarkan Bulan Kejadian)</label>
                        {{-- <input data-date-format="mm/yyyy" data-provide="datepicker" type="text" id="bulan"
                            class="form-control datepicker" placeholder="4/2021" name="bulan"> --}}
                        <input class="form-control @error('bulan') is-invalid @enderror " type="text" id="bulan"
                            name="bulan" placeholder="Pilih Bulan" autocomplete="off">
                        @error('bulan')
                            <div class="alert alert-danger"> cek kembali bulan nya</div>
                        @enderror
                    </div>
                    <div class="text-center mb-3">
                        <button type="submit" class="btn btn-dark-blue btn-months">Cek Hasil</button>
                    </div>
                    @if(Auth::user()->hasPermission('statistics.E'))
                    <div class="text-end">
                        <button id="btn-export" class="btn btn-primary btn-short btn-export" type="submit"
                            style="display: none;"><span><i class="bi bi-download"></i></span> Export
                            data</button>
                    </div>
                    @endif
                </form>
            </fieldset>
        </div>
        <div class="box-body mt-4">
            <div class="card" id="content-report" style="display: none">
                <div class="card-body">
                    <div class="d-flex justify-content-between p-2 m-3" style="border-bottom: 3px solid rgb(34, 37, 119);">
                        <img class="img-header" src="{{ asset('images/logo1.png') }}" alt="profile_image"
                            style="width: 10%!important; height: auto!important;">
                        <div class="d-flex flex-column text-center">
                            <h2 class="fw-bold">Laporan Bulanan</h2>
                            <span class="fw-bold" id="text-datetitle"></span>
                            <span class="fw-bold">Polda : <span id="text-poldaName"></span></span>
                            <span class="fw-bold">Polres : <span id="text-polresName"></span></span>
                        </div>
                        <img class="img-header" src="{{ asset('images/logo2.png') }}" alt="profile_image"
                            style="width: 12%!important; height: auto!important;">
                    </div>

                    <div id="hasil_bulan" class="hasil_bulan" hiden>
                        <div id="selratabel" name="selratabel" class="selratabel" style="display: none;">
                        </div>

                        <div class="row" id="chart_month" name="chart_month" style="display: none;" height="100%"
                            width="100%" position="absolute">
                            <div class="col-md-12" id="chart_month_1" height="100%" width="100%" position="absolute">
                                <div class="resizechart" position="absolute">
                                    <canvas id="canvas" name="canvas"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script type="text/javascript">
        $(document).ready(function() {
            var get_pol = $('#polda_id').val();
            if (get_pol == null || get_pol == '-' || get_pol == "") {
                $('#polres_id').prop('disabled', true);
            } else {
                $('#polres_id').prop('disabled', false);
            }
            $("#hasil_bulan").hide()
            $("#btn-export").hide()
        });

        $(".btn-export").click(function(e) {
            e.preventDefault();
            var selra_id = $("#selra_id").val();
            var bulan = $('#bulan').val();
            var polres_id = $('#polres_id').val();
            var polda_id = $('#polda_id').val();
            var polres_id = $('#polres_id').val();
            window.location = "{{ route('ExportMonth') }}" + '?selra_id=' + selra_id + '&bulan=' + bulan +
                '&polres_id=' + polres_id + '&polda_id=' + polda_id;

        });


        $(".btn-months").click(function(e) {
            e.preventDefault();
            var _token = $("input[name='_token']").val();
            var polda = $("#get_polda").val();
            var bulan = $('#bulan').val();
            var polda_id = $('#polda_id').val();
            var polres_id = $('#polres_id').val();
            if (bulan == '') {
                return alert('Mohon isi bulan terlebih dahulu');
            }
            // $("#content-report").hide();
            // $("#loader").fadeIn();
            // $(".loaderbg").fadeIn();

            $("#btn-export").show();
            $.ajax({
                url: "{{ route('chartcalculationMonth') }}",
                type: 'GET',
                data: {
                    _token: _token,
                    bulan: bulan,
                    polres_id: polres_id,
                    polda_id: polda_id
                },
                success: function(dataMonth) {
                    $("#loader").fadeOut();
                    $(".loaderbg").fadeOut();
                    $("#hasil_bulan").show();
                    $("#content-report").show();
                    $("#selratabel").show();
                    $("#selra").remove();
                    $("#chart_month_1").remove();
                    $("#chart_month").show();
                    var checkPolda = dataMonth.poldaName;
                    var checkPolres = dataMonth.polresName;

                    $('#text-poldaName').html(checkPolda);
                    $('#text-polresName').html(checkPolres);
                    $('#text-datetitle').html(dataMonth.textMonth);

                    // console.log(dataMonth);

                    var perhitunganBiasa =
                        '<div id="selra" class="selra" name="selra">' +
                        '<table class="table">' +

                        '<tr class="fw-bold">' +
                        '<td class="text-center" width="50%"> Jumlah Total Kejadian Laka</td>' +
                        '<td class="text-center" width="50%"><span id="summary_laka" > ' + dataMonth
                        .countdata + ' (0%) </span> </td>' +
                        '</tr>' +

                        '<tr class="fw-bold">' +
                        '<td class="text-center" width="50%"> Jumlah Total Korban MD</td>' +
                        '<td class="text-center" width="50%"><span id="summary_laka" > ' + dataMonth
                        .md + ' (0%) </span> </td>' +
                        '</tr>' +

                        '<tr class="fw-bold">' +
                        '<td class="text-center" width="50%"> Jumlah Total Korban LB</td>' +
                        '<td class="text-center" width="50%"><span id="summary_laka" > ' + dataMonth
                        .lb + ' (0%) </span> </td>' +
                        '</tr>' +

                        '<tr class="fw-bold">' +
                        '<td class="text-center" width="50%"> Jumlah Total Korban LR</td>' +
                        '<td class="text-center" width="50%"><span id="summary_laka" > ' + dataMonth
                        .lr + ' (0%) </span> </td>' +
                        '</tr>' +

                        '<tr class="fw-bold">' +
                        '<td class="text-center" width="50%"> Jumlah Selra P21</td>' +
                        '<td class="text-center" width="50%"><span id="summary_laka" > ' + dataMonth
                        .p21 + ' </span> </td>' +
                        '</tr>' +

                        '<tr class="fw-bold">' +
                        '<td class="text-center" width="50%"> Jumlah Selra SP3</td>' +
                        '<td class="text-center" width="50%"><span id="summary_laka" > ' + dataMonth
                        .sp3 + ' </span> </td>' +
                        '</tr>' +

                        '<tr class="fw-bold">' +
                        '<td class="text-center" width="50%"> Jumlah Selra DIVERSI</td>' +
                        '<td class="text-center" width="50%"><span id="summary_laka" > ' + dataMonth
                        .diversi + ' </span> </td>' +
                        '</tr>' +

                        '<tr class="fw-bold">' +
                        '<td class="text-center" width="50%"> Jumlah Selra POM/TNI</td>' +
                        '<td class="text-center" width="50%"><span id="summary_laka" > ' + dataMonth
                        .tni + ' </span> </td>' +
                        '</tr>' +

                        '<tr class="fw-bold">' +
                        '<td class="text-center" width="50%"> Jumlah Selra RJ</td>' +
                        '<td class="text-center" width="50%"><span id="summary_laka" > ' + dataMonth
                        .adat_rj + ' </span> </td>' +
                        '</tr>' +


                        '<tr class="fw-bold">' +
                        '<td class="text-center" width="50%"> Jumlah Selra SP2LID</td>' +
                        '<td class="text-center" width="50%"><span id="summary_laka" > ' + dataMonth
                        .sp2led + ' </span> </td>' +
                        '</tr>' +

                        '<tr class="fw-bold">' +
                        '<td class="text-center" width="50%"> Jumlah Selra DALAM PROSES</td>' +
                        '<td class="text-center" width="50%"><span id="summary_laka" > ' + dataMonth
                        .dalamProses + ' </span> </td>' +
                        '</tr>' +

                        '</table>' +
                        '</div>'

                    $("#chart_month").append(
                        '<div class="col-md-12" id="chart_month_1" height="100%" width="100%" position="absolute">' +
                        '<div class="col-md-12 resizechart">' +
                        '<canvas id="canvas" name="canvas" ></canvas>' +
                        '</div>' +
                        '</div>')
                    // width="100%" height="30%"
                    var bulan = [dataMonth.textMonthPast, dataMonth.textMonth];

                    var barChartData = {

                        labels: bulan,

                        datasets: [{
                            label: "P21",
                            backgroundColor: "#00FF00",
                            data: ['' + dataMonth.p21Past + '', '' + dataMonth.p21 + '']
                        }, {
                            label: "SP3",
                            backgroundColor: "#00FFFF",
                            data: ['' + dataMonth.sp3Past + '', '' + dataMonth.sp3 + '']
                        }, {
                            label: "DIVERSI",
                            backgroundColor: "#0000FF",
                            data: ['' + dataMonth.diversiPast + '', '' + dataMonth.diversi +
                                ''
                            ]
                        }, {
                            label: "POM/TNI",
                            backgroundColor: "#FF00FF",
                            data: ['' + dataMonth.tniPast + '', '' + dataMonth.tni + '']
                        }, {
                            label: "RJ",
                            backgroundColor: "#FFFF00",
                            data: ['' + dataMonth.adat_rjPast + '', '' + dataMonth.adat_rj +
                                ''
                            ]
                        }, {
                            label: "SP2LID",
                            backgroundColor: "#ff9900",
                            data: ['' + dataMonth.sp2ledPast + '', '' + dataMonth.sp2led +
                                ''
                            ]
                        }, {
                            label: "DALAM PROSES",
                            backgroundColor: "#ee354f",
                            data: ['' + dataMonth.dalamProsesPast + '', '' + dataMonth
                                .dalamProses + ''
                            ]
                        }]

                    };

                    var ctx = document.getElementById("canvas").getContext("2d");

                    window.myBar = new Chart(ctx, {
                        type: "bar",
                        data: barChartData,
                        options: {
                            elements: {
                                rectangle: {
                                    borderWidth: 1
                                }
                            },
                            responsive: true,
                            maintainAspectRatio: false,
                            title: {
                                display: true,
                                text: "Dashboard"
                            }
                        }
                    });
                    $('#selratabel').prepend(perhitunganBiasa);
                }

            });
        });

        $('#bulan').datepicker({
            format: 'MM yyyy',
            startView: "months",
            minViewMode: "months",
            autoclose: "true",
            orientation: 'auto bottom'
        });

        $('.form_statistik_bulan').submit(function(event) {
            var check = $('#bulan').val();

        });

        $('#polda_id').on('change', function(event) {
            event.preventDefault();
            var poldaId = $(this).val();
            $('#polres_id').prop('disabled', true);
            $('#polres_id').empty();
            $('#polres_id').append('<option value="">Pilih Polres</option>');
            $('#polres_id').trigger('change');
            if (!poldaId) {
                return;
            }

            $.get('{{ url('pengguna/polres_list') }}/' + poldaId, function(data) {

                $('#polres_id').empty()
                var option = '<option value="">Pilih Polres</option>';
                $('#polres_id').append(option);
                $.each(data, function(key, polres) {

                    var id = polres.id;
                    var name = polres.name;
                    var option = '<option value="' + id + '">' + name + '</option>';

                    $('#polres_id').append(option);
                });
                $('#polres_id').prop('disabled', false);
                $('#polres_id').trigger('change');
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%'
            });
        });
    </script>
@endpush
