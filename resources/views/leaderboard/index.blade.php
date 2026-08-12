@php
    $_title = 'Leaderboard';
@endphp


@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('libs/bootstrap-duallistbox/bootstrap-duallistbox.css') }}" rel="stylesheet">

    <style>
        .regional-police-table-row-bg-color-01 { background-color: #FFEECC; }
        .regional-police-table-row-bg-color-02 { background-color: #CCFFEE; }
        .regional-police-table-row-bg-color-03 { background-color: #FFCCCC; }
        .regional-police-table-row-bg-color-04 { background-color: #CCFFCC; }
        .regional-police-table-row-bg-color-05 { background-color: #CCE5FF; }
        .regional-police-table-row-bg-color-06 { background-color: #FFF3CD; }
        .regional-police-table-row-bg-color-07 { background-color: #D1E7DD; }
        .regional-police-table-row-bg-color-08 { background-color: #F8D7DA; }
        .regional-police-table-row-bg-color-09 { background-color: #E2E3E5; }
        .regional-police-table-row-bg-color-10 { background-color: #CFE2FF; }
        .regional-police-table-row-bg-color-11 { background-color: #FFDDC1; }
        .regional-police-table-row-bg-color-12 { background-color: #E1FFE7; }
        .regional-police-table-row-bg-color-13 { background-color: #FFF9C4; }
        .regional-police-table-row-bg-color-14 { background-color: #FFCCFF; }
        .regional-police-table-row-bg-color-15 { background-color: #D0E1F9; }
        .regional-police-table-row-bg-color-16 { background-color: #FDE9D9; }
        .regional-police-table-row-bg-color-17 { background-color: #CCFFFF; }
        .regional-police-table-row-bg-color-18 { background-color: #FFFFCC; }
        .regional-police-table-row-bg-color-19 { background-color: #FFC1CC; }
        .regional-police-table-row-bg-color-20 { background-color: #E6FFCC; }
        .regional-police-table-row-bg-color-21 { background-color: #FFD1DC; }
        .regional-police-table-row-bg-color-22 { background-color: #FFEBCC; }
        .regional-police-table-row-bg-color-23 { background-color: #F1E4FF; }
        .regional-police-table-row-bg-color-24 { background-color: #FFEECC; }
        .regional-police-table-row-bg-color-25 { background-color: #CCE5FF; }
        .regional-police-table-row-bg-color-26 { background-color: #FFCCFF; }
        .regional-police-table-row-bg-color-27 { background-color: #FFCCCC; }
        .regional-police-table-row-bg-color-28 { background-color: #CCFFEE; }
        .regional-police-table-row-bg-color-29 { background-color: #FFFF99; }
        .regional-police-table-row-bg-color-30 { background-color: #FFDDC1; }
        .regional-police-table-row-bg-color-31 { background-color: #E1FFE7; }
        .regional-police-table-row-bg-color-32 { background-color: #FFF3CD; }
        .regional-police-table-row-bg-color-33 { background-color: #CCFFCC; }
        .regional-police-table-row-bg-color-34 { background-color: #FFD1DC; }
    </style>
@endpush

@section('content')
    <div class="box">
        <div class="box-header">
            <h4 class="fw-bold text-blue-dark mb-4">Rekapitulasi Hasil Sementara Lomba Kelengkapan Mindik Dalam Rangka HUT Lalu Lintas Bhayangkara Ke-69 2024</h4>
            <h6 class="fw-bold text-blue-dark mb-4">*( DITUTUP H-2 ACARA SYUKURAN HUT LANTAS ke-69 </h6>
        </div>

        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">Satker</th>
                                        <th scope="col">LP</th>
                                        <th scope="col">P21</th>
                                        <th scope="col">SP3</th>
                                        <th scope="col">SP2LID</th>
                                        <th scope="col">Diversi</th>
                                        <th scope="col">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                       
                                    @endphp
                                    @foreach ($leaderboardItems as $leaderboardItem)
                                    <tr class="{{ 'regional-police-table-row-bg-color-' . $leaderboardItem['polda'] }}">
                                        <th scope="row">{{$loop->iteration}}</th>
                                        <td>{{$leaderboardItem['polres_name'] ?? ''}} ({{$leaderboardItem['polda_name'] ?? ''}})</td>
                                        <td>{{$leaderboardItem['jumlah_laka'] ?? '-'}}</td>
                                        <td>{{
                                            (isset($leaderboardItem['percentage_p21'])) ? number_format($leaderboardItem['percentage_p21'], 2) . '%' : '-'
                                        }}</td>
                                        <td>{{
                                            (isset($leaderboardItem['percentage_sp3'])) ? number_format($leaderboardItem['percentage_sp3'], 2) . '%' : '-'
                                        }}</td>
                                        <td>{{
                                            (isset($leaderboardItem['percentage_sp2lid'])) ? number_format($leaderboardItem['percentage_sp2lid'], 2) . '%' : '-'
                                        }}</td>
                                        <td>{{
                                            (isset($leaderboardItem['percentage_diversi'])) ? number_format($leaderboardItem['percentage_diversi'], 2) . '%' : '-'
                                        }}</td>
                                        <td>{{
                                            (isset($leaderboardItem['percentage_total'])) ? number_format($leaderboardItem['percentage_total'], 2) . '%' : '-'
                                        }}</td>
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
@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js" defer></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" defer></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js" defer></script>
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script src="{{ asset('libs/bootstrap-duallistbox/jquery.bootstrap-duallistbox.js') }}"></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>
    {{-- Highcharts --}}
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    
    <script type="text/javascript">
        $(document).ready(function() {
            if ($(window).width() >= 768) {
                $('.sidemenu').removeClass('active');
                $('.main-content').removeClass('active');
            }
        });
    </script>
@endpush
