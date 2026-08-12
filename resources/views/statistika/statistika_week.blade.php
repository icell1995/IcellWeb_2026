@php
    $_title = 'Laporan Mingguan';
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
            <h3 class="text-blue-dark fw-semibold mb-2">Laporan Mingguan</h3>

            <fieldset class="border rounded-3 p-3">
                <form class="form_statistik_hari row mt-2">
                    @csrf
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-3">
                        <label for="polda" class="fw-semibold mb-1">Polda</label>
                        <select id="polda_id" name="polda_id" class="form-select select2 @error('polda_id') is-invalid @enderror">
                            @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                                <option value="-">Semua Polda</option>
                            @endif
                            @foreach ($polda as $poldas)
                                @if(in_array($poldas->name, ['POLDA XE', 'PJR INDUK KORLANTAS', 'PUSDIKLANTAS', 'DIT GAKKUM KORLANTAS']))
                                    @continue
                                @endif
                                <option value="{{ $poldas->id }}" {{ old('polda_id') == $poldas->id ? 'selected' : '' }}>
                                    {{ $poldas->name }}</option>
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
                        <label for="week" class="fw-semibold mb-1">Tanggal (Berdasarkan Tanggal Kejadian)</label>
                        {{-- <input data-date-format="mm/yyyy" data-provide="datepicker" type="text" id="bulan"
                            class="form-control datepicker" placeholder="4/2021" name="bulan"> --}}
                        <input class="form-control date" type="text" id="week" name="week"
                            placeholder="Pilih Minggu" autocomplete="off">
                    </div>
                    <div class="text-center mb-3">
                        <button type="submit" class="btn btn-dark-blue btn-weeks">Cek Hasil</button>
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
                            <h2 class="fw-bold">Laporan Mingguan</h2>
                            <span class="fw-bold" id="text-datetitle"></span>
                            <span class="fw-bold">Polda : <span id="text-poldaName"></span></span>
                            <span class="fw-bold">Polres : <span id="text-polresName"></span></span>
                        </div>
                        <img class="img-header" src="{{ asset('images/logo2.png') }}" alt="profile_image"
                            style="width: 12%!important; height: auto!important;">
                    </div>

                    <div id="hasil_weeks" hiden>
                        <div id="selratabel" name="selratabel" class="selratabel" style="display: none;">
                        </div>

                        <div class="row" id="chart_week" name="chart_week" style="display: none;" height="100%"
                            width="100%" position="absolute">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-12" id="chart_week_1" height="100%"
                                width="100%" position="absolute">
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
            if (get_pol == null || get_pol == '-') {
                $('#polres_id').prop('disabled', true);
            } else {
                $('#polres_id').prop('disabled', false);
            }
            $("#hasil_weeks").hide()
            $("#btn-export").hide()
        });

        $(".btn-export").click(function(e) {
            e.preventDefault();
            var selra_id = $("#selra_id").val();
            var week = $('#week').val();
            var polres_id = $('#polres_id').val();
            var polda_id = $('#polda_id').val();
            window.location = "{{ route('ExportWeeks') }}" + '?selra_id=' + selra_id + '&week=' + week +
                '&polres_id=' + polres_id + '&polda_id=' + polda_id;

        });

        $(".btn-weeks").click(function(e) {
            e.preventDefault();
            var _token = $("input[name='_token']").val();
            var selra_id = $("#selra_id").val();
            var week = $('#week').val();
            var polda_id = $('#polda_id').val();
            var polres_id = $('#polres_id').val();
            if (week == '') {
                return alert('mohon isi Tanggal terlebih dahulu')
            }
            $("#content-report").hide();
            $("#loader").fadeIn();
            $(".loaderbg").fadeIn();

            $.ajax({
                url: "{{ route('chartcalculationWeek') }}",
                type: 'GET',
                data: {
                    _token: _token,
                    selra_id: selra_id,
                    week: week,
                    polda_id: polda_id,
                    polres_id: polres_id
                },
                success: function(response) {
                    $("#loader").fadeOut();
                    $(".loaderbg").fadeOut();
                    $("#chart_week_1").remove();
                    $("#chart_week").show();
                    $("#hasil_weeks ").show();
                    $("#selratabel").show();
                    $("#content-report").show();
                    $("#selra").remove();
                    $("#btn-export").show();

                    var data = response.data;
                    $('#text-poldaName').html(data.poldaName);
                    $('#text-polresName').html(data.polresName);
                    $('#text-datetitle').html(data.datetitle);
                    var summary = data.summary
                    var summary_selra = data.summary_selra;
                    var tr =
                        '<div id="selra" class="selra" name="selra">' +
                        '<table class="table">' +
                        '<tr class="fw-bold">' +
                        '<td class="text-center" wdith="50%"> Jumlah Total Kejadian Laka</td>' +
                        '<td class="text-center" wdith="50%"><span id="summary_laka" > ' + summary
                        .total_laka + ' (0%) </span> </td>' +
                        '</tr>' +

                        '<tr class="fw-bold">' +
                        '<td class="text-center" wdith="50%"> Jumlah Total Korban MD</td>' +
                        '<td class="text-center" wdith="50%"><span id="summary_laka" > ' + summary.md +
                        ' (0%) </span> </td>' +
                        '</tr>' +

                        '<tr class="fw-bold">' +
                        '<td class="text-center" wdith="50%"> Jumlah Total Korban LB</td>' +
                        '<td class="text-center" wdith="50%"><span id="summary_laka" > ' + summary.lb +
                        ' (0%) </span> </td>' +
                        '</tr>' +

                        '<tr class="fw-bold">' +
                        '<td class="text-center" wdith="50%"> Jumlah Total Korban LR</td>' +
                        '<td class="text-center" wdith="50%"><span id="summary_laka" > ' + summary.lr +
                        ' (0%) </span> </td>' +
                        '</tr>' +

                        '<tr class="fw-bold">' +
                        '<td class="text-center" wdith="50%"> Jumlah Selra P21</td>' +
                        '<td class="text-center" wdith="50%"><span id="summary_laka" > ' + summary_selra
                        .p21 + ' </span> </td>' +
                        '</tr>' +

                        '<tr class="fw-bold">' +
                        '<td class="text-center" wdith="50%"> Jumlah Selra SP3</td>' +
                        '<td class="text-center" wdith="50%"><span id="summary_laka" > ' + summary_selra
                        .sp3 + ' </span> </td>' +
                        '</tr>' +

                        '<tr class="fw-bold">' +
                        '<td class="text-center" wdith="50%"> Jumlah Selra DIVERSI</td>' +
                        '<td class="text-center" wdith="50%"><span id="summary_laka" > ' + summary_selra
                        .diversi + ' </span> </td>' +
                        '</tr>' +

                        '<tr class="fw-bold">' +
                        '<td class="text-center" wdith="50%"> Jumlah Selra POM/TNI</td>' +
                        '<td class="text-center" wdith="50%"><span id="summary_laka" > ' + summary_selra
                        .pom_tni + ' </span> </td>' +
                        '</tr>' +

                        '<tr class="fw-bold">' +
                        '<td class="text-center" wdith="50%"> Jumlah Selra RJ</td>' +
                        '<td class="text-center" wdith="50%"><span id="summary_laka" > ' + summary_selra
                        .adat_rj + ' </span> </td>' +
                        '</tr>' +

                        '<tr class="fw-bold">' +
                        '<td class="text-center" wdith="50%"> Jumlah SP2LID</td>' +
                        '<td class="text-center" wdith="50%"><span id="summary_laka" > ' + summary_selra
                        .sp2lid + ' </span> </td>' +
                        '</tr>' +

                        '<tr class="fw-bold">' +
                        '<td class="text-center" wdith="50%"> Jumlah Selra DALAM PROSES</td>' +
                        '<td class="text-center" wdith="50%"><span id="summary_laka" > ' + summary_selra
                        .dalam_proses +
                        ' </span> </td>' +
                        '</tr>' +
                        '</table>' +
                        '</div>'

                    // $("#chart_week").append('<div class="col-md-12" id="chart_week_1" name="chart_week_1">'+
                    //                         '<canvas id="canvas" height="280" width="600"></canvas>'+
                    //                         '</div>')

                    $("#chart_week").append(
                        '<div class="col-md-12" id="chart_week_1" height="100%" width="100%" position="absolute">' +
                        '<div class="col-md-12 resizechart">' +
                        '<canvas id="canvas" name="canvas" ></canvas>' +
                        '</div>' +
                        '</div>')
                    var chartData = data.cart.chartSelra;

                    // var data_md = dataWeeks[1];
                    // var data_lr = dataWeeks[3];
                    // var data_lb = dataWeeks[2];
                    // var data_mdPast = dataWeeks[4];
                    // var data_lrPast = dataWeeks[6];
                    // var data_lbPast = dataWeeks[5];

                    var barChartData = {
                        // labels: [''+dataWeeks[7]+'  Sampai  '+dataWeeks[9]+'',''+dataWeeks[7]+'  Sampai  '+dataWeeks[8]+''],
                        labels: chartData.labels,

                        datasets: [{
                            // label: "Meninggal Dunia",
                            // backgroundColor: "rgb(255,0,0)",
                            // data: [''+data_mdPast+'',''+data_md+'']
                            label: chartData.datasets[0].label,
                            backgroundColor: '#00FF00',
                            borderColor: 'rgb(255, 99, 132)',
                            data: chartData.datasets[0].data,
                        }, {
                            // label: "Luka Berat",
                            // backgroundColor: "rgb(220,20,60)",
                            // data: [''+data_lbPast+'',''+data_lb+'']
                            label: chartData.datasets[1].label,
                            backgroundColor: '#00FFFF',
                            borderColor: 'rgb(255, 99, 132)',
                            data: chartData.datasets[1].data,
                        }, {
                            // label: "Luka Ringan",
                            // backgroundColor: "rgb(240,128,128)",
                            // data: [''+data_lrPast+'',,''+data_lr+'']
                            label: chartData.datasets[2].label,
                            backgroundColor: '#0000FF',
                            borderColor: 'rgb(255, 99, 132)',
                            data: chartData.datasets[2].data,
                        }, {
                            label: chartData.datasets[3].label,
                            backgroundColor: '#FF00FF',
                            borderColor: 'rgb(255, 99, 132)',
                            data: chartData.datasets[3].data,
                        }, {
                            label: chartData.datasets[4].label,
                            backgroundColor: '#FFFF00',
                            borderColor: 'rgb(255, 99, 132)',
                            data: chartData.datasets[4].data,
                        }, {
                            label: chartData.datasets[5].label,
                            backgroundColor: '#ff9900',
                            borderColor: 'rgb(255, 99, 132)',
                            data: chartData.datasets[5].data,
                        }, {
                            label: chartData.datasets[6].label,
                            backgroundColor: '#ee354f',
                            borderColor: 'rgb(255, 99, 132)',
                            data: chartData.datasets[6].data,
                        }, ]
                        // {
                        //     label: chartData.datasets[6].label,
                        //     backgroundColor: '#ee354f',
                        //     borderColor: 'rgb(255, 99, 132)',
                        //     data: chartData.datasets[6].data,
                        // }]
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

                    $('#selratabel').prepend(tr);
                }

            });
        });


        $('.date').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: "true",
            orientation: 'auto bottom'
        });


        $('.form_statistik_minggu').submit(function(event) {
            var check = $('#minggu').val();
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

            $.get('{{ url("pengguna/polres_list") }}/' + poldaId, function(data) {
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
