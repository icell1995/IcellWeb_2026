@extends('layouts.app')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/home-dashboard.css') }}">
@endpush

@section('content')
    <div class="box">

        <div class="row mb-3">
            <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-2">
                <div class="card shadow rounded-4 border-0 bg-white">
                    <div class="d-flex flex-wrap p-3">
                        <div
                            class="count-total text-center border-end border-3 border-blue col-lg-5 col-md-5 col-sm-12 col-12">
                            <h2 class="text-danger fw-bolder">{{ $accident }}</h2>
                        </div>
                        <div class="ms-2 col-lg-6 col-md-6 col-sm-12 col-12">
                            <span class="fw-bold">Jumlah Laka Lantas Ditindaklanjuti</span>
                        </div>
                    </div>
                    <div class="card-footer d-grid gap-2 border-0">
                        <button class="viewdata">
                            <a class="view-data" href="{{ route('produktivitas') }}">Lihat</a>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-2">
                <div class="card shadow rounded-4 border-0 bg-white">
                    <div class="d-flex flex-wrap p-3">
                        <div
                            class="count-total text-center border-end border-3 border-blue col-lg-5 col-md-5 col-sm-12 col-12">
                            <h2 class="text-danger fw-bolder">{{ $dpo }}</h2>
                        </div>
                        <div class="ms-2 col-lg-6 col-md-6 col-sm-12 col-12">
                            <span class="fw-bold">Daftar Pencarian Orang (DPO)</span>
                        </div>
                    </div>
                    <div class="card-footer d-grid gap-2 border-0">
                        <button class="viewdata">
                            <a class="view-data" href="{{ route('index_dpo') }}">Lihat</a>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-2">
                <div class="card shadow rounded-4 border-0 bg-white">
                    <div class="d-flex flex-wrap p-3">
                        <div
                            class="count-total text-center border-end border-3 border-blue col-lg-5 col-md-5 col-sm-12 col-12">
                            <h2 class="text-danger fw-bolder">{{ $dpb }}</h2>
                        </div>
                        <div class="ms-2 col-lg-6 col-md-6 col-sm-12 col-12">
                            <span class="fw-bold">Daftar Pencarian Barang (DPB)</span>
                        </div>
                    </div>
                    <div class="card-footer d-grid gap-2 border-0">
                        <button class="viewdata">
                            <a class="view-data" href="{{ route('index_dpb') }}">Lihat</a>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7 col-md-7 col-sm-12 col-12 mb-2">
                <div class="card shadow rounded-4 p-3 border-0 bg-white">
                    <div id="modernChartBar" class="modern-chart-container"></div>
                </div>
            </div>
            <div class="col-lg-5 col-md-5 col-sm-12 col-12 mb-2">
                <div class="card shadow rounded-4 p-3 border-0 bg-white">
                    <div id="modernChartPie" class="modern-chart-container"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Polda 2026 -->
    <div class="modal fade" id="leaderboard2026Polda" data-bs-backdrop="static" data-bs-keyboard="false"aria-hidden="true"
        aria-labelledby="leaderboard2026PoldaLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="leaderboard2026PoldaLabel">Rekapitulasi Selra Laka 2026</h1>
                </div>
                <div class="modal-body">
                    <h3 class="fw-bold text-blue-dark mb-4">Rekapitulasi Selra Laka
                        {{ Carbon\Carbon::parse($recap2026BeginDate)->locale('id')->translatedFormat('Y') }}</h3>
                    {{-- <h5 class="fw-bold text-blue-dark mb-4 mt-4">*( DITUTUP H-2 ACARA SYUKURAN HUT LANTAS ke-69 </h5> --}}
                    <h6 class="fw-bold marked-text text-blue-dark mb-4 mt-4"><i class="bi bi-pin-angle-fill"></i> Dihitung
                        Periode LP dari
                        {{ App\Helpers\FormatDateHelper::formatDateRange($recap2026BeginDate, $recap2026LimitDate) }}</h6>
                    {{-- <h6 class="fw-bold text-blue-dark mb-4 mt-4">*( Untuk LP dengan tercantum selra RJ mohon untuk di update agar masuk ke dalam perhitungan </h6> --}}

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="header">
                                                <tr>
                                                    <th scope="col" rowspan="2" class="text-center"
                                                        style="background-color: #fd7e14">No</th>
                                                    <th scope="col" rowspan="2" class="text-center bg-warning">Polda
                                                    </th>
                                                    {{-- <th scope="col" rowspan="2" class="text-center text-white" style="background-color:rgb(240, 13, 145)">Tunggakan 2024</th> --}}
                                                    <th scope="col" rowspan="2" class="text-center"
                                                        style="background-color: #0dcaf0">Jumlah LP <br /> (LP - Limpah)
                                                    </th>
                                                    <th scope="col" colspan="2" class="text-center text-white"
                                                        style="background-color: #d63384">P21</th>
                                                    <th scope="col" colspan="2" class="text-center text-white"
                                                        style="background-color: #6610f2">SP3</th>
                                                    <th scope="col" colspan="2" class="text-center text-white"
                                                        style="background-color: #6f42c1">SP2LID</th>
                                                    <th scope="col" colspan="2"
                                                        class="text-center text-white bg-secondary">Diversi</th>
                                                    <th scope="col" colspan="2"
                                                        class="text-center bg-primary text-white">Total</th>
                                                    <th scope="col" colspan="4"
                                                        class="text-center bg-danger marked-column text-white">Tunggakan
                                                    </th>
                                                    <th scope="col" rowspan="2"
                                                        class="text-center bg-secondary text-white">Limpah POM/TNI <br />
                                                        (Mengurangi Kasus)</th>
                                                </tr>
                                                <tr>
                                                    <th scope="col" class="text-center text-white"
                                                        style="background-color: #d63384">Jumlah</th>
                                                    <th scope="col" class="text-center text-white"
                                                        style="background-color: #d63384">%</th>

                                                    <th scope="col" class="text-center text-white"
                                                        style="background-color: #6610f2">Jumlah</th>
                                                    <th scope="col" class="text-center text-white"
                                                        style="background-color: #6610f2">%</th>

                                                    <th scope="col" class="text-center text-white"
                                                        style="background-color: #6f42c1">Jumlah</th>
                                                    <th scope="col" class="text-center text-white"
                                                        style="background-color: #6f42c1">%</th>

                                                    <th scope="col" class="text-center text-white bg-secondary">Jumlah
                                                    </th>
                                                    <th scope="col" class="text-center text-white bg-secondary">%</th>

                                                    <th scope="col" class="text-center bg-primary text-white">Jumlah
                                                    </th>
                                                    <th scope="col" class="text-center bg-primary text-white">%</th>

                                                    <th scope="col"
                                                        class="text-center bg-danger marked-column text-white">Dalam Proses
                                                    </th>
                                                    <th scope="col"
                                                        class="text-center bg-danger marked-column text-white">Tabrak Lari
                                                    </th>
                                                    <th scope="col"
                                                        class="text-center bg-danger marked-column text-white">Jumlah</th>
                                                    <th scope="col"
                                                        class="text-center bg-danger marked-column text-white">%</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    // Move the item with polres = 1114 to the end of the array
                                                    $recap2026LeaderboardItemsPolda = collect(
                                                        $recap2026LeaderboardItems,
                                                    );
                                                    $recap2026LeaderboardItemsPolda = $recap2026LeaderboardItemsPolda
                                                        ->sortBy('total_total_percentage')
                                                        ->reverse();
                                                    /*$specialItem = $leaderboardItems->where('polres', '1114')->first();

                                                    if ($specialItem) {
                                                        // Remove the special item from the original position
                                                        $leaderboardItems = $leaderboardItems->reject(function ($item) {
                                                            return $item['polres'] === '1114';
                                                        });

                                                        // Append the special item to the end
                                                        $leaderboardItems->push($specialItem);
                                                    }*/

                                                    $accidentTotal = 0;
                                                    $p21Total = 0;
                                                    $sp3Total = 0;
                                                    $sp2lidTotal = 0;
                                                    $diversiTotal = 0;
                                                    $totalTotal = 0;
                                                    $onProgressTotal = 0;
                                                    $inTheProcessTotal = 0;
                                                    $hitAndRunTotal = 0;
                                                    $newEntryCrimeClearanceTotal = 0;
                                                    $pomTniTotal = 0;

                                                    $accidentTotalPercentage = 0;
                                                    $p21TotalPercentage = 0;
                                                    $sp3TotalPercentage = 0;
                                                    $sp2lidTotalPercentage = 0;
                                                    $diversiTotalPercentage = 0;
                                                    $totalTotalPercentage = 0;
                                                    $onProgressTotalPercentage = 0;
                                                @endphp

                                                @foreach ($recap2026LeaderboardItemsPolda as $leaderboardItemPolda)
                                                    @php
                                                        $accidentTotal =
                                                            $accidentTotal +
                                                            ($leaderboardItemPolda['accident_total'] ?? 0);
                                                        $p21Total =
                                                            $p21Total + ($leaderboardItemPolda['p21_total'] ?? 0);
                                                        $sp3Total =
                                                            $sp3Total + ($leaderboardItemPolda['sp3_total'] ?? 0);
                                                        $sp2lidTotal =
                                                            $sp2lidTotal + ($leaderboardItemPolda['sp2lid_total'] ?? 0);
                                                        $diversiTotal =
                                                            $diversiTotal +
                                                            ($leaderboardItemPolda['diversi_total'] ?? 0);
                                                        $totalTotal =
                                                            $totalTotal + ($leaderboardItemPolda['total_total'] ?? 0);
                                                        $onProgressTotal =
                                                            $onProgressTotal +
                                                            ($leaderboardItemPolda['on_progress_total'] ?? 0);
                                                        $inTheProcessTotal =
                                                            $inTheProcessTotal +
                                                            ($leaderboardItemPolda['in_the_process_total'] ?? 0);
                                                        $hitAndRunTotal =
                                                            $hitAndRunTotal +
                                                            ($leaderboardItemPolda['hit_and_run_total'] ?? 0);
                                                        $newEntryCrimeClearanceTotal =
                                                            $newEntryCrimeClearanceTotal +
                                                            ($leaderboardItemPolda['new_entry_crime_clearance_total'] ??
                                                                0);
                                                        $pomTniTotal =
                                                            $pomTniTotal +
                                                            ($leaderboardItemPolda['pom_tni_total'] ?? 0);
                                                        $cellColor =
                                                            'regional-police-table-row-bg-color-' .
                                                            $leaderboardItemPolda['polda'];
                                                    @endphp

                                                    <tr
                                                        class="{{ 'regional-police-table-row-bg-color-' . $leaderboardItemPolda['polda'] }}">
                                                        <th scope="row" class="text-center">
                                                            {{ $loop->iteration }}
                                                        </th>
                                                        <th scope="row" class="text-center">
                                                            <button type="button"
                                                                class="btn btn-sm btn-primary float-start regional-police-2026-table-row-button"
                                                                id="{{ 'polda-' . $leaderboardItemPolda['polda'] }}">
                                                                <i class="bi bi-chevron-expand"></i>
                                                            </button>

                                                            {{ $leaderboardItemPolda['polda_name'] }}

                                                            @if ($leaderboardItemPolda['new_entry_crime_clearance_total'] > 0)
                                                                <button type="button"
                                                                    class="btn btn-sm btn-success float-end rounded-pill">
                                                                    {{ $leaderboardItemPolda['new_entry_crime_clearance_total'] }}
                                                                </button>
                                                            @endif
                                                        </th>
                                                        {{-- <td class="text-center">-</td> --}}
                                                        <td class="text-center">
                                                            {{ $leaderboardItemPolda['accident_total'] }}</td>
                                                        <td class="text-center">{{ $leaderboardItemPolda['p21_total'] }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ number_format($leaderboardItemPolda['p21_total_percentage'], 2) . '%' }}
                                                        </td>
                                                        <td class="text-center">{{ $leaderboardItemPolda['sp3_total'] }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ number_format($leaderboardItemPolda['sp3_total_percentage'], 2) . '%' }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ $leaderboardItemPolda['sp2lid_total'] }}</td>
                                                        <td class="text-center">
                                                            {{ number_format($leaderboardItemPolda['sp2lid_total_percentage'], 2) . '%' }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ $leaderboardItemPolda['diversi_total'] }}</td>
                                                        <td class="text-center">
                                                            {{ number_format($leaderboardItemPolda['diversi_total_percentage'], 2) . '%' }}
                                                        </td>
                                                        <td class="text-center">{{ $leaderboardItemPolda['total_total'] }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ number_format($leaderboardItemPolda['total_total_percentage'], 2) . '%' }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ $leaderboardItemPolda['in_the_process_total'] }}</td>
                                                        <td class="text-center">
                                                            {{ $leaderboardItemPolda['hit_and_run_total'] }}</td>
                                                        <td class="text-center">
                                                            {{ $leaderboardItemPolda['on_progress_total'] }}</td>
                                                        <td class="text-center">
                                                            {{ number_format($leaderboardItemPolda['on_progress_total_percentage'], 2) . '%' }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ $leaderboardItemPolda['pom_tni_total'] }}</td>
                                                    </tr>

                                                    @php
                                                        $leaderboardItemPolres = collect(
                                                            $leaderboardItemPolda['polres'],
                                                        );
                                                        $leaderboardItemPolres = $leaderboardItemPolres
                                                            ->sortBy('percentage_total')
                                                            ->reverse();
                                                    @endphp

                                                    @foreach ($leaderboardItemPolres as $leaderboardItem)
                                                        @if (isset($leaderboardItem['polres']))
                                                            @if (!in_array($leaderboardItem['polres'], ['1605']))
                                                                @php
                                                                    $cellColor =
                                                                        'regional-police-table-row-bg-color-' .
                                                                        $leaderboardItem['polda'];
                                                                @endphp

                                                                <tr class="{{ $cellColor }} {{ 'resort-police-2025-table-row-polda-' . $leaderboardItem['polda'] }}"
                                                                    style="display:none;">
                                                                    <th scope="row" class="text-center">
                                                                        {{ $loop->iteration }}</th>
                                                                    <td>
                                                                        {{ $leaderboardItem['polres_name'] ?? '' }}
                                                                        ({{ $leaderboardItem['polda_name'] ?? '' }})

                                                                        @if ($leaderboardItem['new_entry_crime_clearance'] > 0)
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-success float-end rounded-pill">
                                                                                {{ $leaderboardItem['new_entry_crime_clearance'] }}
                                                                            </button>
                                                                        @endif
                                                                    </td>
                                                                    {{-- <td class="text-center">-</td> --}}
                                                                    <td class="text-center">
                                                                        {{ $leaderboardItem['jumlah_laka'] ?? '-' }}</td>
                                                                    <td class="text-center">
                                                                        {{ $leaderboardItem['p21'] ?? '-' }}</td>
                                                                    <td class="text-center">
                                                                        {{ isset($leaderboardItem['percentage_p21']) ? number_format($leaderboardItem['percentage_p21'], 2) . '%' : '-' }}
                                                                    </td>
                                                                    <td class="text-center">
                                                                        {{ $leaderboardItem['sp3'] ?? '-' }}</td>
                                                                    <td class="text-center">
                                                                        {{ isset($leaderboardItem['percentage_sp3']) ? number_format($leaderboardItem['percentage_sp3'], 2) . '%' : '-' }}
                                                                    </td>
                                                                    <td class="text-center">
                                                                        {{ $leaderboardItem['sp2lid'] ?? '-' }}</td>
                                                                    <td class="text-center">
                                                                        {{ isset($leaderboardItem['percentage_sp2lid']) ? number_format($leaderboardItem['percentage_sp2lid'], 2) . '%' : '-' }}
                                                                    </td>
                                                                    <td class="text-center">
                                                                        {{ $leaderboardItem['diversi'] ?? '-' }}</td>
                                                                    <td class="text-center">
                                                                        {{ isset($leaderboardItem['percentage_diversi']) ? number_format($leaderboardItem['percentage_diversi'], 2) . '%' : '-' }}
                                                                    </td>
                                                                    <td class="text-center">
                                                                        {{ $leaderboardItem['total'] ?? '-' }}</td>
                                                                    <td class="text-center">
                                                                        {{ isset($leaderboardItem['percentage_total']) ? number_format($leaderboardItem['percentage_total'], 2) . '%' : '-' }}
                                                                    </td>
                                                                    <td class="text-center">
                                                                        {{ $leaderboardItem['in_the_process'] ?? '-' }}
                                                                    </td>
                                                                    <td class="text-center">
                                                                        {{ $leaderboardItem['hit_and_run'] ?? '-' }}</td>
                                                                    <td class="text-center">
                                                                        {{ $leaderboardItem['on_progress'] ?? '-' }}</td>
                                                                    <td class="text-center">
                                                                        {{ isset($leaderboardItem['percentage_total']) ? number_format($leaderboardItem['percentage_on_progress'], 2) . '%' : '-' }}
                                                                    </td>
                                                                    <td class="text-center">
                                                                        {{ $leaderboardItem['pom_tni'] ?? '-' }}</td>
                                                                </tr>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                                @php
                                                    $p21TotalPercentage =
                                                        $accidentTotal != 0 ? ($p21Total / $accidentTotal) * 100 : 0;
                                                    $sp3TotalPercentage =
                                                        $accidentTotal != 0 ? ($sp3Total / $accidentTotal) * 100 : 0;
                                                    $sp2lidTotalPercentage =
                                                        $accidentTotal != 0 ? ($sp2lidTotal / $accidentTotal) * 100 : 0;
                                                    $diversiTotalPercentage =
                                                        $accidentTotal != 0
                                                            ? ($diversiTotal / $accidentTotal) * 100
                                                            : 0;
                                                    $totalTotalPercentage =
                                                        $accidentTotal != 0 ? ($totalTotal / $accidentTotal) * 100 : 0;
                                                    $onProgressTotalPercentage =
                                                        $accidentTotal != 0
                                                            ? ($onProgressTotal / $accidentTotal) * 100
                                                            : 0;
                                                @endphp
                                                <tr>
                                                    <th scope="row" colspan="2"
                                                        class="text-center bg-secondary text-white">
                                                        Total

                                                        @if ($newEntryCrimeClearanceTotal > 0)
                                                            <button type="button"
                                                                class="btn btn-sm btn-warning float-end rounded-pill">
                                                                {{ $newEntryCrimeClearanceTotal }}
                                                            </button>
                                                        @endif
                                                    </th>
                                                    {{-- <td class="text-center bg-secondary text-white">-</td> --}}
                                                    <td class="text-center bg-secondary text-white">{{ $accidentTotal }}
                                                    </td>
                                                    <td class="text-center bg-secondary text-white">{{ $p21Total }}
                                                    </td>
                                                    <td class="text-center bg-secondary text-white">
                                                        {{ number_format($p21TotalPercentage, 2) . '%' }}</td>
                                                    <td class="text-center bg-secondary text-white">{{ $sp3Total }}
                                                    </td>
                                                    <td class="text-center bg-secondary text-white">
                                                        {{ number_format($sp3TotalPercentage, 2) . '%' }}</td>
                                                    <td class="text-center bg-secondary text-white">{{ $sp2lidTotal }}
                                                    </td>
                                                    <td class="text-center bg-secondary text-white">
                                                        {{ number_format($sp2lidTotalPercentage, 2) . '%' }}</td>
                                                    <td class="text-center bg-secondary text-white">{{ $diversiTotal }}
                                                    </td>
                                                    <td class="text-center bg-secondary text-white">
                                                        {{ number_format($diversiTotalPercentage, 2) . '%' }}</td>
                                                    <td class="text-center bg-primary text-white">{{ $totalTotal }}</td>
                                                    <td class="text-center bg-primary text-white">
                                                        {{ number_format($totalTotalPercentage, 2) . '%' }}</td>
                                                    <td class="text-center bg-danger marked-column text-white">
                                                        {{ $inTheProcessTotal }}</td>
                                                    <td class="text-center bg-danger marked-column text-white">
                                                        {{ $hitAndRunTotal }}</td>
                                                    <td class="text-center bg-danger marked-column text-white">
                                                        {{ $onProgressTotal }}</td>
                                                    <td class="text-center bg-danger marked-column text-white">
                                                        {{ number_format($onProgressTotalPercentage, 2) . '%' }}</td>
                                                    <td class="text-center bg-secondary text-white">{{ $pomTniTotal }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" data-bs-target="#leaderboard2025Polda" data-bs-toggle="modal">
                        Lanjut <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Polda 2025 -->
    <div class="modal fade" id="leaderboard2025Polda" data-bs-backdrop="static"
        data-bs-keyboard="false"aria-hidden="true" aria-labelledby="leaderboard2025PoldaLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="leaderboard2025PoldaLabel">Rekapitulasi Selra Laka 2025</h1>
                </div>
                <div class="modal-body">
                    <h3 class="fw-bold text-blue-dark mb-4">Rekapitulasi Selra Laka
                        {{ Carbon\Carbon::parse($recap2025BeginDate)->locale('id')->translatedFormat('Y') }}</h3>
                    {{-- <h5 class="fw-bold text-blue-dark mb-4 mt-4">*( DITUTUP H-2 ACARA SYUKURAN HUT LANTAS ke-69 </h5> --}}
                    <h6 class="fw-bold marked-text text-blue-dark mb-4 mt-4"><i class="bi bi-pin-angle-fill"></i> Dihitung
                        Periode LP dari
                        {{ App\Helpers\FormatDateHelper::formatDateRange($recap2025BeginDate, $recap2025LimitDate) }}</h6>
                    {{-- <h6 class="fw-bold text-blue-dark mb-4 mt-4">*( Untuk LP dengan tercantum selra RJ mohon untuk di update agar masuk ke dalam perhitungan </h6> --}}

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="header">
                                                <tr>
                                                    <th scope="col" rowspan="2" class="text-center"
                                                        style="background-color: #fd7e14">No</th>
                                                    <th scope="col" rowspan="2" class="text-center bg-warning">
                                                        Polda</th>
                                                    {{-- <th scope="col" rowspan="2" class="text-center text-white" style="background-color:rgb(240, 13, 145)">Tunggakan 2024</th> --}}
                                                    <th scope="col" rowspan="2" class="text-center"
                                                        style="background-color: #0dcaf0">Jumlah LP <br /> (LP - Limpah)
                                                    </th>
                                                    <th scope="col" colspan="2" class="text-center text-white"
                                                        style="background-color: #d63384">P21</th>
                                                    <th scope="col" colspan="2" class="text-center text-white"
                                                        style="background-color: #6610f2">SP3</th>
                                                    <th scope="col" colspan="2" class="text-center text-white"
                                                        style="background-color: #6f42c1">SP2LID</th>
                                                    <th scope="col" colspan="2"
                                                        class="text-center text-white bg-secondary">Diversi</th>
                                                    <th scope="col" colspan="2"
                                                        class="text-center bg-primary text-white">Total</th>
                                                    <th scope="col" colspan="4"
                                                        class="text-center bg-danger marked-column text-white">Tunggakan
                                                    </th>
                                                    <th scope="col" rowspan="2"
                                                        class="text-center bg-secondary text-white">Limpah POM/TNI <br />
                                                        (Mengurangi Kasus)</th>
                                                </tr>
                                                <tr>
                                                    <th scope="col" class="text-center text-white"
                                                        style="background-color: #d63384">Jumlah</th>
                                                    <th scope="col" class="text-center text-white"
                                                        style="background-color: #d63384">%</th>

                                                    <th scope="col" class="text-center text-white"
                                                        style="background-color: #6610f2">Jumlah</th>
                                                    <th scope="col" class="text-center text-white"
                                                        style="background-color: #6610f2">%</th>

                                                    <th scope="col" class="text-center text-white"
                                                        style="background-color: #6f42c1">Jumlah</th>
                                                    <th scope="col" class="text-center text-white"
                                                        style="background-color: #6f42c1">%</th>

                                                    <th scope="col" class="text-center text-white bg-secondary">Jumlah
                                                    </th>
                                                    <th scope="col" class="text-center text-white bg-secondary">%</th>

                                                    <th scope="col" class="text-center bg-primary text-white">Jumlah
                                                    </th>
                                                    <th scope="col" class="text-center bg-primary text-white">%</th>

                                                    <th scope="col"
                                                        class="text-center bg-danger marked-column text-white">Dalam Proses
                                                    </th>
                                                    <th scope="col"
                                                        class="text-center bg-danger marked-column text-white">Tabrak Lari
                                                    </th>
                                                    <th scope="col"
                                                        class="text-center bg-danger marked-column text-white">Jumlah</th>
                                                    <th scope="col"
                                                        class="text-center bg-danger marked-column text-white">%</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    // Move the item with polres = 1114 to the end of the array
                                                    $recap2025LeaderboardItemsPolda = collect(
                                                        $recap2025LeaderboardItems,
                                                    );
                                                    $recap2025LeaderboardItemsPolda = $recap2025LeaderboardItemsPolda
                                                        ->sortBy('total_total_percentage')
                                                        ->reverse();
                                                    /*$specialItem = $leaderboardItems->where('polres', '1114')->first();

                                                    if ($specialItem) {
                                                        // Remove the special item from the original position
                                                        $leaderboardItems = $leaderboardItems->reject(function ($item) {
                                                            return $item['polres'] === '1114';
                                                        });

                                                        // Append the special item to the end
                                                        $leaderboardItems->push($specialItem);
                                                    }*/

                                                    $accidentTotal = 0;
                                                    $p21Total = 0;
                                                    $sp3Total = 0;
                                                    $sp2lidTotal = 0;
                                                    $diversiTotal = 0;
                                                    $totalTotal = 0;
                                                    $onProgressTotal = 0;
                                                    $inTheProcessTotal = 0;
                                                    $hitAndRunTotal = 0;
                                                    $newEntryCrimeClearanceTotal = 0;
                                                    $pomTniTotal = 0;

                                                    $accidentTotalPercentage = 0;
                                                    $p21TotalPercentage = 0;
                                                    $sp3TotalPercentage = 0;
                                                    $sp2lidTotalPercentage = 0;
                                                    $diversiTotalPercentage = 0;
                                                    $totalTotalPercentage = 0;
                                                    $onProgressTotalPercentage = 0;
                                                @endphp

                                                @foreach ($recap2025LeaderboardItemsPolda as $leaderboardItemPolda)
                                                    @php
                                                        $accidentTotal =
                                                            $accidentTotal +
                                                            ($leaderboardItemPolda['accident_total'] ?? 0);
                                                        $p21Total =
                                                            $p21Total + ($leaderboardItemPolda['p21_total'] ?? 0);
                                                        $sp3Total =
                                                            $sp3Total + ($leaderboardItemPolda['sp3_total'] ?? 0);
                                                        $sp2lidTotal =
                                                            $sp2lidTotal + ($leaderboardItemPolda['sp2lid_total'] ?? 0);
                                                        $diversiTotal =
                                                            $diversiTotal +
                                                            ($leaderboardItemPolda['diversi_total'] ?? 0);
                                                        $totalTotal =
                                                            $totalTotal + ($leaderboardItemPolda['total_total'] ?? 0);
                                                        $onProgressTotal =
                                                            $onProgressTotal +
                                                            ($leaderboardItemPolda['on_progress_total'] ?? 0);
                                                        $inTheProcessTotal =
                                                            $inTheProcessTotal +
                                                            ($leaderboardItemPolda['in_the_process_total'] ?? 0);
                                                        $hitAndRunTotal =
                                                            $hitAndRunTotal +
                                                            ($leaderboardItemPolda['hit_and_run_total'] ?? 0);
                                                        $newEntryCrimeClearanceTotal =
                                                            $newEntryCrimeClearanceTotal +
                                                            ($leaderboardItemPolda['new_entry_crime_clearance_total'] ??
                                                                0);
                                                        $pomTniTotal =
                                                            $pomTniTotal +
                                                            ($leaderboardItemPolda['pom_tni_total'] ?? 0);
                                                        $cellColor =
                                                            'regional-police-table-row-bg-color-' .
                                                            $leaderboardItemPolda['polda'];
                                                    @endphp

                                                    <tr
                                                        class="{{ 'regional-police-table-row-bg-color-' . $leaderboardItemPolda['polda'] }}">
                                                        <th scope="row" class="text-center">
                                                            {{ $loop->iteration }}
                                                        </th>
                                                        <th scope="row" class="text-center">
                                                            <button type="button"
                                                                class="btn btn-sm btn-primary float-start regional-police-2025-table-row-button"
                                                                id="{{ 'polda-' . $leaderboardItemPolda['polda'] }}">
                                                                <i class="bi bi-chevron-expand"></i>
                                                            </button>

                                                            {{ $leaderboardItemPolda['polda_name'] }}

                                                            @if ($leaderboardItemPolda['new_entry_crime_clearance_total'] > 0)
                                                                <button type="button"
                                                                    class="btn btn-sm btn-success float-end rounded-pill">
                                                                    {{ $leaderboardItemPolda['new_entry_crime_clearance_total'] }}
                                                                </button>
                                                            @endif
                                                        </th>
                                                        {{-- <td class="text-center">-</td> --}}
                                                        <td class="text-center">
                                                            {{ $leaderboardItemPolda['accident_total'] }}</td>
                                                        <td class="text-center">{{ $leaderboardItemPolda['p21_total'] }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ number_format($leaderboardItemPolda['p21_total_percentage'], 2) . '%' }}
                                                        </td>
                                                        <td class="text-center">{{ $leaderboardItemPolda['sp3_total'] }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ number_format($leaderboardItemPolda['sp3_total_percentage'], 2) . '%' }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ $leaderboardItemPolda['sp2lid_total'] }}</td>
                                                        <td class="text-center">
                                                            {{ number_format($leaderboardItemPolda['sp2lid_total_percentage'], 2) . '%' }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ $leaderboardItemPolda['diversi_total'] }}</td>
                                                        <td class="text-center">
                                                            {{ number_format($leaderboardItemPolda['diversi_total_percentage'], 2) . '%' }}
                                                        </td>
                                                        <td class="text-center">{{ $leaderboardItemPolda['total_total'] }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ number_format($leaderboardItemPolda['total_total_percentage'], 2) . '%' }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ $leaderboardItemPolda['in_the_process_total'] }}</td>
                                                        <td class="text-center">
                                                            {{ $leaderboardItemPolda['hit_and_run_total'] }}</td>
                                                        <td class="text-center">
                                                            {{ $leaderboardItemPolda['on_progress_total'] }}</td>
                                                        <td class="text-center">
                                                            {{ number_format($leaderboardItemPolda['on_progress_total_percentage'], 2) . '%' }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ $leaderboardItemPolda['pom_tni_total'] }}</td>
                                                    </tr>

                                                    @php
                                                        $leaderboardItemPolres = collect(
                                                            $leaderboardItemPolda['polres'],
                                                        );
                                                        $leaderboardItemPolres = $leaderboardItemPolres
                                                            ->sortBy('percentage_total')
                                                            ->reverse();
                                                    @endphp

                                                    @foreach ($leaderboardItemPolres as $leaderboardItem)
                                                        @if (isset($leaderboardItem['polres']))
                                                            @if (!in_array($leaderboardItem['polres'], ['1605']))
                                                                @php
                                                                    $cellColor =
                                                                        'regional-police-table-row-bg-color-' .
                                                                        $leaderboardItem['polda'];
                                                                @endphp

                                                                <tr class="{{ $cellColor }} {{ 'resort-police-2025-table-row-polda-' . $leaderboardItem['polda'] }}"
                                                                    style="display:none;">
                                                                    <th scope="row" class="text-center">
                                                                        {{ $loop->iteration }}</th>
                                                                    <td>
                                                                        {{ $leaderboardItem['polres_name'] ?? '' }}
                                                                        ({{ $leaderboardItem['polda_name'] ?? '' }})

                                                                        @if ($leaderboardItem['new_entry_crime_clearance'] > 0)
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-success float-end rounded-pill">
                                                                                {{ $leaderboardItem['new_entry_crime_clearance'] }}
                                                                            </button>
                                                                        @endif
                                                                    </td>
                                                                    {{-- <td class="text-center">-</td> --}}
                                                                    <td class="text-center">
                                                                        {{ $leaderboardItem['jumlah_laka'] ?? '-' }}</td>
                                                                    <td class="text-center">
                                                                        {{ $leaderboardItem['p21'] ?? '-' }}</td>
                                                                    <td class="text-center">
                                                                        {{ isset($leaderboardItem['percentage_p21']) ? number_format($leaderboardItem['percentage_p21'], 2) . '%' : '-' }}
                                                                    </td>
                                                                    <td class="text-center">
                                                                        {{ $leaderboardItem['sp3'] ?? '-' }}</td>
                                                                    <td class="text-center">
                                                                        {{ isset($leaderboardItem['percentage_sp3']) ? number_format($leaderboardItem['percentage_sp3'], 2) . '%' : '-' }}
                                                                    </td>
                                                                    <td class="text-center">
                                                                        {{ $leaderboardItem['sp2lid'] ?? '-' }}</td>
                                                                    <td class="text-center">
                                                                        {{ isset($leaderboardItem['percentage_sp2lid']) ? number_format($leaderboardItem['percentage_sp2lid'], 2) . '%' : '-' }}
                                                                    </td>
                                                                    <td class="text-center">
                                                                        {{ $leaderboardItem['diversi'] ?? '-' }}</td>
                                                                    <td class="text-center">
                                                                        {{ isset($leaderboardItem['percentage_diversi']) ? number_format($leaderboardItem['percentage_diversi'], 2) . '%' : '-' }}
                                                                    </td>
                                                                    <td class="text-center">
                                                                        {{ $leaderboardItem['total'] ?? '-' }}</td>
                                                                    <td class="text-center">
                                                                        {{ isset($leaderboardItem['percentage_total']) ? number_format($leaderboardItem['percentage_total'], 2) . '%' : '-' }}
                                                                    </td>
                                                                    <td class="text-center">
                                                                        {{ $leaderboardItem['in_the_process'] ?? '-' }}
                                                                    </td>
                                                                    <td class="text-center">
                                                                        {{ $leaderboardItem['hit_and_run'] ?? '-' }}</td>
                                                                    <td class="text-center">
                                                                        {{ $leaderboardItem['on_progress'] ?? '-' }}</td>
                                                                    <td class="text-center">
                                                                        {{ isset($leaderboardItem['percentage_total']) ? number_format($leaderboardItem['percentage_on_progress'], 2) . '%' : '-' }}
                                                                    </td>
                                                                    <td class="text-center">
                                                                        {{ $leaderboardItem['pom_tni'] ?? '-' }}</td>
                                                                </tr>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                                @php
                                                    $p21TotalPercentage =
                                                        $accidentTotal != 0 ? ($p21Total / $accidentTotal) * 100 : 0;
                                                    $sp3TotalPercentage =
                                                        $accidentTotal != 0 ? ($sp3Total / $accidentTotal) * 100 : 0;
                                                    $sp2lidTotalPercentage =
                                                        $accidentTotal != 0 ? ($sp2lidTotal / $accidentTotal) * 100 : 0;
                                                    $diversiTotalPercentage =
                                                        $accidentTotal != 0
                                                            ? ($diversiTotal / $accidentTotal) * 100
                                                            : 0;
                                                    $totalTotalPercentage =
                                                        $accidentTotal != 0 ? ($totalTotal / $accidentTotal) * 100 : 0;
                                                    $onProgressTotalPercentage =
                                                        $accidentTotal != 0
                                                            ? ($onProgressTotal / $accidentTotal) * 100
                                                            : 0;
                                                @endphp
                                                <tr>
                                                    <th scope="row" colspan="2"
                                                        class="text-center bg-secondary text-white">
                                                        Total

                                                        @if ($newEntryCrimeClearanceTotal > 0)
                                                            <button type="button"
                                                                class="btn btn-sm btn-warning float-end rounded-pill">
                                                                {{ $newEntryCrimeClearanceTotal }}
                                                            </button>
                                                        @endif
                                                    </th>
                                                    {{-- <td class="text-center bg-secondary text-white">-</td> --}}
                                                    <td class="text-center bg-secondary text-white">{{ $accidentTotal }}
                                                    </td>
                                                    <td class="text-center bg-secondary text-white">{{ $p21Total }}
                                                    </td>
                                                    <td class="text-center bg-secondary text-white">
                                                        {{ number_format($p21TotalPercentage, 2) . '%' }}</td>
                                                    <td class="text-center bg-secondary text-white">{{ $sp3Total }}
                                                    </td>
                                                    <td class="text-center bg-secondary text-white">
                                                        {{ number_format($sp3TotalPercentage, 2) . '%' }}</td>
                                                    <td class="text-center bg-secondary text-white">{{ $sp2lidTotal }}
                                                    </td>
                                                    <td class="text-center bg-secondary text-white">
                                                        {{ number_format($sp2lidTotalPercentage, 2) . '%' }}</td>
                                                    <td class="text-center bg-secondary text-white">{{ $diversiTotal }}
                                                    </td>
                                                    <td class="text-center bg-secondary text-white">
                                                        {{ number_format($diversiTotalPercentage, 2) . '%' }}</td>
                                                    <td class="text-center bg-primary text-white">{{ $totalTotal }}
                                                    </td>
                                                    <td class="text-center bg-primary text-white">
                                                        {{ number_format($totalTotalPercentage, 2) . '%' }}</td>
                                                    <td class="text-center bg-danger marked-column text-white">
                                                        {{ $inTheProcessTotal }}</td>
                                                    <td class="text-center bg-danger marked-column text-white">
                                                        {{ $hitAndRunTotal }}</td>
                                                    <td class="text-center bg-danger marked-column text-white">
                                                        {{ $onProgressTotal }}</td>
                                                    <td class="text-center bg-danger marked-column text-white">
                                                        {{ number_format($onProgressTotalPercentage, 2) . '%' }}</td>
                                                    <td class="text-center bg-secondary text-white">{{ $pomTniTotal }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" data-bs-target="#leaderboard2026Polda" data-bs-toggle="modal">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </button>
                    <button class="btn btn-primary" data-bs-target="#infoBoard" data-bs-toggle="modal">
                        Lanjut <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="infoBoard" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="infoBoardLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="infoBoardLabel">Papan Informasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-start">
                        <div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist"
                            aria-orientation="vertical">
                            {{-- <button class="nav-link active" id="info-4-tab" data-bs-toggle="pill" data-bs-target="#info-4-content" type="button" role="tab" aria-controls="info-4-content" aria-selected="true">Gangguan Layanan PDN</button> --}}
                            <button class="nav-link active" id="info-1-tab" data-bs-toggle="pill"
                                data-bs-target="#info-1-content" type="button" role="tab"
                                aria-controls="info-1-content" aria-selected="true">Alur Pendaftaran TTE</button>
                            <button class="nav-link" id="info-2-tab" data-bs-toggle="pill"
                                data-bs-target="#info-2-content" type="button" role="tab"
                                aria-controls="info-2-content" aria-selected="false">Aktivasi BSrE</button>
                            <button class="nav-link" id="info-3-tab" data-bs-toggle="pill"
                                data-bs-target="#info-3-content" type="button" role="tab"
                                aria-controls="info-3-content" aria-selected="false">TR Syarat Daftar TTE</button>
                        </div>

                        <div class="tab-content" id="v-pills-tabContent">
  

                            <div class="tab-pane fade show active" id="info-1-content" role="tabpanel"
                                aria-labelledby="info-1-tab">
                                <h4 class="fw-bold text-blue-dark mb-4 mt-4">Alur Pendaftaran Akun BSrE Untuk TTE</h4>

                                <a href="{{ asset('images/AlurPendaftaranTTE.png') }}">
                                    <img src="{{ asset('images/AlurPendaftaranTTE.png') }}" class="img-fluid"
                                        alt="Responsive image">
                                </a>
                            </div>

                            <div class="tab-pane fade" id="info-2-content" role="tabpanel" aria-labelledby="info-2-tab">
                                <h4 class="fw-bold text-blue-dark mb-4 mt-4">Prosedur Aktivasi Akun BSrE (Jika Sudah
                                    Mendaftarakan Ke Robinops Bareskrim)</h4>

                                <a href="{{ asset('file/PPT_PENERBITAN_TTE_BSrE.pdf') }}"
                                    class="btn btn-primary mb-4 btn-lg" role="button" target="_blank">
                                    <i class="bi bi-download"></i> Download
                                </a>
                                <div class="embed-responsive embed-responsive-16by1">
                                    <iframe style="top:0;left:0;width:100%;height:1024px;" class="embed-responsive-item"
                                        src="{{ asset('file/PPT_PENERBITAN_TTE_BSrE.pdf') }}" allowfullscreen></iframe>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="info-3-content" role="tabpanel" aria-labelledby="info-3-tab">
                                <h4 class="fw-bold text-blue-dark mb-4 mt-4">TR Syarat Pendaftaran TTE</h4>

                                <a href="{{ asset('file/TR_SYARAT_DAFTAR_TTE.pdf') }}"
                                    class="btn btn-primary mb-4 btn-lg" role="button" target="_blank">
                                    <i class="bi bi-download"></i> Download
                                </a>
                                <div class="embed-responsive embed-responsive-16by1">
                                    <iframe style="top:0; left:0; width:900px; height:1024px;"
                                        class="embed-responsive-item" src="{{ asset('file/TR_SYARAT_DAFTAR_TTE.pdf') }}"
                                        allowfullscreen></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        <i class="bi bi-x"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    @if (session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 5000
            });
        </script>
    @endif
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts-gl/dist/echarts-gl.min.js"></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#leaderboard2026Polda').modal('show');
        });

        setInterval(function() {
            $('.marked-column').toggleClass('bg-white bg-danger text-white text-black');
        }, 2000);

        setInterval(function() {
            $('.marked-text').toggleClass('text-danger text-success');
        }, 1500);

        $('.regional-police-2025-table-row-button').click(function() {
            var id = $(this).attr('id');
            console.log(id);
            $('.resort-police-2025-table-row-' + id).toggle();
        });

        $('.regional-police-2024-table-row-button').click(function() {
            var id = $(this).attr('id');
            console.log(id);
            $('.resort-police-2024-table-row-' + id).toggle();
        });
    </script>

    <script type="text/javascript">
        window.modernChartData = {
            dates: [],
            counts: []
        };

        function drawECharts2D() {
            var dates = window.modernChartData.dates;
            var counts = window.modernChartData.counts;
            if (!dates || !dates.length) return;

            echarts.dispose(document.getElementById('modernChartBar'));

            window.modernBarChart = echarts.init(document.getElementById('modernChartBar'));
            var barOption = {
                color: ['#222577', '#198754', '#ca8a04', '#6f42c1', '#0891b2', '#c2410c', '#1d4ed8', '#15803d', '#d97706', '#5b21b6', '#0e7490', '#991b1b'],
                title: {
                    text: 'Jumlah LP Ditindaklanjuti Per-Bulan',
                    textStyle: {
                        fontSize: 16,
                        fontWeight: 'bold',
                        color: '#222577'
                    },
                    left: 10,
                    top: 10
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: { type: 'shadow' }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '15%',
                    top: '18%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    data: dates,
                    axisLine: { lineStyle: { color: '#cbd5e1' } },
                    axisLabel: { color: '#64748b', fontSize: 11, interval: 0, rotate: 45 }
                },
                yAxis: {
                    type: 'value',
                    axisLine: { show: false },
                    splitLine: { lineStyle: { color: '#f1f5f9' } },
                    axisLabel: { color: '#64748b', fontSize: 11 }
                },
                series: [{
                    name: 'Jumlah Laka Lantas Sedang Ditindak Lanjuti',
                    type: 'bar',
                    barWidth: '50%',
                    colorBy: 'data',
                    label: {
                        show: true,
                        position: 'top',
                        color: '#64748b',
                        fontSize: 10,
                        fontWeight: 'bold'
                    },
                    itemStyle: {
                        borderRadius: [4, 4, 0, 0]
                    },
                    data: counts
                }]
            };
            window.modernBarChart.setOption(barOption);
        }

        // window.onload = function(){

        $.ajax({
            url: "{{ route('getChartBulan') }}",
            type: 'get',
            success: function(data) {
                var get_date = new Array();
                var get_count = new Array();
                a = data.length;
                // alert(a);
                for (x = 0; x < a; x++) {
                    get_date.push([data[x].date]);
                    get_count.push([data[x].count]);
                }

                // --- Simpan data ke global state untuk toggle
                window.modernChartData.dates = data.map(function(item) { return item.date; });
                window.modernChartData.counts = data.map(function(item) { return Number(item.count); });

                // Render default 2D
                drawECharts2D();
            }
        });


        $.ajax({
            url: "{{ route('getPieBulan') }}",
            type: 'get',
            success: function(data) {
                // var get_date = new Array();
                // var get_percentage = new Array();
                var get_month = new Array();
                var get_year = new Array();
                var get_jumlah = new Array();
                var get_selra = new Array();
                var get_percentage = new Array();

                a = data[0].jumlah_selra.length;
                for (x = 0; x < a; x++) {
                    get_selra.push([data[0].jumlah_selra[x].name]);
                    get_percentage.push([data[0].jumlah_selra[x].percentage]);
                }

                // --- Map data untuk ECharts Donut dengan palet warna harmonis & berbobot gelap-terang seimbang dengan #222577 (Navy Base)
                var echartsPieData = data[0].jumlah_selra.map(function(item) {
                    var color = '#ca8a04';
                    var nameUpper = item.name.toUpperCase();
                    if (nameUpper.indexOf('SP2LID') !== -1) {
                        color = '#222577';
                    } else if (nameUpper.indexOf('P21') !== -1) {
                        color = '#198754';
                    } else if (nameUpper.indexOf('SP3') !== -1) {
                        color = '#0891b2';
                    } else if (nameUpper.indexOf('DIVERSI') !== -1) {
                        color = '#6f42c1';
                    } else if (nameUpper.indexOf('POM') !== -1 || nameUpper.indexOf('TNI') !== -1) {
                        color = '#c2410c';
                    } else {
                        color = '#ca8a04';
                    }
                    return { 
                        name: item.name, 
                        value: Number(item.percentage),
                        itemStyle: { color: color }
                    };
                });

                window.modernPieChart = echarts.init(document.getElementById('modernChartPie'));
                var pieOption = {
                    title: {
                        text: 'Jumlah SELRA Tahun ' + data[0].date_year + ' sebanyak : ' + data[0].jumlah_laka + ' LP',
                        subtext: '( Berdasarkan Tanggal Tindak Lanjut Tahun ' + data[0].date_year + ' )',
                        textStyle: {
                            fontSize: 16,
                            fontWeight: 'bold',
                            color: '#222577'
                        },
                        subtextStyle: {
                            fontWeight: 'bold',
                            fontSize: 11,
                            color: '#64748b'
                        },
                        left: 10,
                        top: 10
                    },
                    tooltip: {
                        trigger: 'item',
                        formatter: '{b}: {c} LP ({d}%)'
                    },
                    legend: {
                        bottom: '5%',
                        left: 'center',
                        textStyle: { fontSize: 11, color: '#64748b' }
                    },
                    series: [
                        {
                            name: 'Persentase',
                            type: 'pie',
                            radius: ['35%', '60%'],
                            avoidLabelOverlap: true,
                            minAngle: 5,
                            itemStyle: {
                                borderRadius: 10,
                                borderColor: '#fff',
                                borderWidth: 2
                            },
                            label: {
                                show: true,
                                position: 'outer',
                                alignTo: 'edge',
                                margin: 15,
                                formatter: '{b}\n{d}%',
                                fontSize: 10,
                                color: '#475569'
                            },
                            labelLine: {
                                length: 15,
                                length2: 10
                            },
                            labelLayout: {
                                moveOverlap: 'shiftY'
                              },
                              emphasis: {
                                  label: {
                                      show: true,
                                      fontSize: 14,
                                      fontWeight: 'bold'
                                  }
                              },
                              data: echartsPieData
                          }
                      ]
                  };
                  window.modernPieChart.setOption(pieOption);
              }
          });
  
          // Resize handler untuk menjaga responsiveness ECharts saat viewport berubah
          window.addEventListener('resize', function() {
              if (window.modernBarChart) window.modernBarChart.resize();
              if (window.modernPieChart) window.modernPieChart.resize();
          });
          // Build the chart
      </script>
@endpush
