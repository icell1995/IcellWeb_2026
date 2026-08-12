<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Lomba ICELL</title>
    <link rel="shortcut icon" href="{{ asset('images/logo2x.png') }}" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap1x.min.css') }}">
    
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .fixed-top-section {
            position: sticky;
            top: 0;
            z-index: 1000;
            background-color: #f4f7f6;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            margin-bottom: 10px !important;
        }

        .dashboard-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 15px 30px;
        }

        .btn-back {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 50px;
            padding: 6px 18px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            font-size: 0.9rem;
        }

        .btn-back:hover {
            background: white;
            color: #1e3a8a;
        }

        .marquee-container {
            background: #ef4444;
            color: white;
            padding: 8px 0;
            overflow: hidden;
            white-space: nowrap;
        }

        .marquee-text {
            display: inline-block;
            animation: marquee 25s linear infinite;
            font-weight: 700;
            font-size: 1rem;
        }

        @keyframes marquee {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }

        .nav-section {
            padding: 15px 30px 5px 30px;
            background: #f4f7f6;
        }

        .nav-tabs {
            border: none;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #64748b;
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 8px;
            margin-right: 10px;
            transition: all 0.3s;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .nav-tabs .nav-link.active {
            background-color: #3b82f6;
            color: white;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
        }

        .content-container {
            padding: 0 30px 30px 30px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            background: white;
        }

        .table-responsive {
            overflow: visible !important; /* No vertical scroll in card */
            border-radius: 10px;
        }

        .header-sticky {
            position: sticky;
            top: 215px; /* Adjust based on fixed-top-section height */
            z-index: 999;
            background: white;
        }

        /* Polda Row Colors */
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
</head>

<body>
    <div class="fixed-top-section">
        <div class="dashboard-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="{{ asset('images/logo2x.png') }}" alt="Logo Korlantas" style="height: 40px; margin-right: 15px;">
                <h3 class="mb-0 fw-bold">PENILAIAN PENYELESAIAN PERKARA ICELL 2025</h3>
            </div>
            <a href="{{ route('home') }}" class="btn-back">
                <i class="bi bi-house-door-fill me-1"></i> Beranda
            </a>
        </div>

        <div class="marquee-container">
            <div class="marquee-text">
                (AKAN DIUMUMKAN PADA SAAT RAKERNIS FUNGSI LALU LINTAS 2026) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                (AKAN DIUMUMKAN PADA SAAT RAKERNIS FUNGSI LALU LINTAS 2026) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                (AKAN DIUMUMKAN PADA SAAT RAKERNIS FUNGSI LALU LINTAS 2026)
            </div>
        </div>

        <div class="nav-section">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-white text-primary p-2 border me-2 shadow-sm">
                            <i class="bi bi-calendar-range me-1"></i> Periode LP: {{ App\Helpers\FormatDateHelper::formatDateRange($recapLombaBeginDate, $recapLombaLimitDate) }}
                        </span>
                        <span class="badge bg-white text-danger p-2 border shadow-sm">
                            <i class="bi bi-exclamation-triangle me-1"></i> Penilaian Ditutup H-1 Rakernis
                        </span>
                    </div>
                    <ul class="nav nav-tabs" id="lombaTab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="kategori1-tab" data-bs-toggle="tab" data-bs-target="#kategori1" type="button">Kategori 1</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="kategori2-tab" data-bs-toggle="tab" data-bs-target="#kategori2" type="button">Kategori 2</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="kategori3-tab" data-bs-toggle="tab" data-bs-target="#kategori3" type="button">Kategori 3</button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="content-container">
        <div class="tab-content card p-4 rounded-top-0" style="margin-top: -1px;">
            @php
                $categories = [
                    ['id' => 'kategori1', 'label' => 'Kategori 1: Jumlah Laka Lebih dari 5000 Pertahun', 'filter' => function($i){ return $i['accident_total'] > 5000; }],
                    ['id' => 'kategori2', 'label' => 'Kategori 2: Jumlah Laka 1500 s/d 5000 Pertahun', 'filter' => function($i){ return $i['accident_total'] >= 1500 && $i['accident_total'] <= 5000; }],
                    ['id' => 'kategori3', 'label' => 'Kategori 3: Jumlah Laka Kurang dari 1500 Pertahun', 'filter' => function($i){ return $i['accident_total'] < 1500; }]
                ];
            @endphp

            @foreach($categories as $index => $cat)
            <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="{{ $cat['id'] }}" role="tabpanel">
                <h4 class="fw-bold text-blue-dark mb-4">{{ $cat['label'] }}</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="header-sticky text-center align-middle shadow-sm">
                            <tr class="text-white">
                                <th rowspan="3" style="background-color: #fd7e14">No</th>
                                <th rowspan="3" class="bg-warning">Polda</th>
                                <th rowspan="3" style="background-color: #0dcaf0">Jumlah LP <br/> (LP - Limpah)</th>
                                <th colspan="8" style="background-color:rgb(35, 3, 110)">Selra</th>
                                <th rowspan="3" class="bg-primary">Jumlah <br/> %Bobot</th>
                                <th rowspan="3" class="bg-secondary">Limpah POM/TNI</th>
                            </tr>
                            <tr class="text-white">
                                <th colspan="2" style="background-color: #d63384">P21 (Bobot : 6)</th>
                                <th colspan="2" style="background-color: #6610f2">SP3 (Bobot : 2)</th>
                                <th colspan="2" class="bg-secondary">Diversi (Bobot : 2)</th>
                                <th colspan="2" style="background-color: #6f42c1">SP2LID (Bobot : 1)</th>
                            </tr>
                            <tr class="text-white small">
                                <th style="background-color: #d63384">Jml</th><th style="background-color: #d63384">%</th>
                                <th style="background-color: #6610f2">Jml</th><th style="background-color: #6610f2">%</th>
                                <th class="bg-secondary">Jml</th><th class="bg-secondary">%</th>
                                <th style="background-color: #6f42c1">Jml</th><th style="background-color: #6f42c1">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $items = collect($recapLombaLeaderboardItems)->filter($cat['filter'])->sortByDesc('selra_total_weight_percentage');
                            @endphp
                            @foreach ($items as $item)
                                <tr class="text-center align-middle {{'regional-police-table-row-bg-color-' . str_pad($item['polda'], 2, '0', STR_PAD_LEFT)}}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="text-start fw-bold">{{ $item['polda_name'] }}</td>
                                    <td>{{ number_format($item['accident_new_total'], 0, ',', '.') }}</td>
                                    <td>{{ number_format($item['p21_total'], 0, ',', '.') }}</td>
                                    <td>{{ number_format($item['p21_total_weight_percentage'], 2) }}%</td>
                                    <td>{{ number_format($item['sp3_total'], 0, ',', '.') }}</td>
                                    <td>{{ number_format($item['sp3_total_weight_percentage'], 2) }}%</td>
                                    <td>{{ number_format($item['diversi_total'], 0, ',', '.') }}</td>
                                    <td>{{ number_format($item['diversi_total_weight_percentage'], 2) }}%</td>
                                    <td>{{ number_format($item['sp2lid_total'], 0, ',', '.') }}</td>
                                    <td>{{ number_format($item['sp2lid_total_weight_percentage'], 2) }}%</td>
                                    <td class="fw-bold">{{ number_format($item['selra_total_weight_percentage'], 2) }}%</td>
                                    <td>{{ number_format($item['pom_tni_total'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="{{ asset('js/bootstrap1x.js') }}"></script>
</body>

</html>
