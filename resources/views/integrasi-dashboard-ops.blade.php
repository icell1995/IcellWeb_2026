<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Data Laka Ops Lilin 2025</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-gradient-start: #0f172a;
            --bg-gradient-end: #1e3a8a;
            --card-bg: rgba(255, 255, 255, 0.95);
            --text-primary: #1e293b;
            --chart-line-2024: #000000;
            --chart-line-2025: #22c55e;
            --header-text: #ffffff;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
            min-height: 100vh;
            color: var(--text-primary);
            padding-bottom: 2rem;
        }

        .dashboard-header {
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            color: white;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .logo-img {
            height: 60px;
            object-fit: contain;
        }

        .dashboard-title {
            font-size: 1.75rem;
            font-weight: 800;
            text-transform: uppercase;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            letter-spacing: 0.5px;
        }

        .dashboard-subtitle {
            font-size: 1rem;
            opacity: 0.9;
            font-weight: 500;
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            height: 100%;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-label {
            font-size: 0.875rem;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            margin: 0.5rem 0;
            color: #0f172a;
        }

        .stat-comparison {
            font-size: 0.875rem;
            font-weight: 600;
        }

        .trend-up { color: #ef4444; }
        .trend-down { color: #22c55e; }

        .main-chart-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .side-table-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .card-header-styled {
            background: #f8fafc;
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 700;
            color: #334155;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .custom-table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.025em;
            background-color: #f1f5f9;
            padding-left: 16px;
            padding-right: 16px;
        }
        
        .custom-table td {
            vertical-align: middle;
            padding-left: 16px;
            padding-right: 16px;
        }

        .text-year-2024 { color: black; font-weight: 700; }
        .text-year-2025 { color: #22c55e; font-weight: 700; }
        
        /* Highcharts formatting */
        .highcharts-title {
            font-family: 'Inter', sans-serif !important;
            font-weight: 700 !important;
        }
    </style>
</head>
<body>

    <div class="container-fluid px-5 py-2">
        <!-- Header -->
        <div class="dashboard-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-4">
                <img src="{{ asset('images/logo1.png') }}" alt="Logo Polri" class="logo-img" onerror="this.style.display='none'">
                <div>
                    <h1 class="dashboard-title mb-0">Dashboard Data Laka Ops Lilin 2025</h1>
                    <div class="dashboard-subtitle mt-1">
                        <i class="bi bi-calendar3 me-2"></i>Periode 18 Desember 2025 s.d {{date('d F Y')}}
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <img src="{{ asset('images/logo2x.png') }}" alt="Logo IRSMS" class="logo-img" onerror="this.style.display='none'">
                <div class="text-end">
                    <div class="fw-bold fs-5" id="clock">00:00:00</div>
                    <small class="opacity-75">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</small>
                </div>
            </div>
        </div>

        <!-- Akumulasi Title -->
        <div class="row mb-3">
             <div class="col-12">
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded-pill me-2" style="width: 4px; height: 24px;"></div>
                    <h5 class="text-white fw-bold mb-0 text-uppercase" style="font-size: 0.9rem; letter-spacing: 1px;">Akumulasi Data Operasi</h5>
                </div>
            </div>
        </div>

        <!-- Top Stats Row (Existing) -->
        <div class="row g-4 mb-4">
            <!-- Total Laka -->
            <div class="col-xl-3 col-md-6">
                <div class="stat-card border-start border-4 border-primary">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stat-label">Jumlah Laka</div>
                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-bicycle text-primary fs-5"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-end mb-2">
                        <div>
                            <div class="text-muted small fw-semibold">2024</div>
                            <div class="fs-4 fw-bold text-dark opacity-75">3,910</div>
                        </div>
                        <div class="text-end">
                            <div class="text-muted small fw-semibold">2025</div>
                            <div class="fs-2 fw-bold text-primary">3,421</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center pt-2 border-top">
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 me-2">
                            <i class="bi bi-arrow-down-short"></i> 12.5%
                        </span>
                        <span class="text-muted small">dari tahun lalu</span>
                    </div>
                </div>
            </div>
            <!-- MD -->
            <div class="col-xl-3 col-md-6">
                <div class="stat-card border-start border-4 border-danger">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stat-label">Meninggal Dunia (MD)</div>
                        <div class="bg-danger bg-opacity-10 p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                             <i class="bi bi-heart-pulse-fill text-danger fs-5"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-end mb-2">
                        <div>
                            <div class="text-muted small fw-semibold">2024</div>
                            <div class="fs-4 fw-bold text-dark opacity-75">486</div>
                        </div>
                        <div class="text-end">
                            <div class="text-muted small fw-semibold">2025</div>
                            <div class="fs-2 fw-bold text-danger">412</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center pt-2 border-top">
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 me-2">
                            <i class="bi bi-arrow-down-short"></i> 15.2%
                        </span>
                        <span class="text-muted small">dari tahun lalu</span>
                    </div>
                </div>
            </div>
            <!-- LB -->
            <div class="col-xl-3 col-md-6">
                <div class="stat-card border-start border-4 border-warning">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stat-label">Luka Berat (LB)</div>
                        <div class="bg-warning bg-opacity-10 p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                             <i class="bi bi-bandaid-fill text-warning fs-5"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-end mb-2">
                         <div>
                            <div class="text-muted small fw-semibold">2024</div>
                            <div class="fs-4 fw-bold text-dark opacity-75">619</div>
                        </div>
                        <div class="text-end">
                            <div class="text-muted small fw-semibold">2025</div>
                            <div class="fs-2 fw-bold text-warning">567</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center pt-2 border-top">
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 me-2">
                            <i class="bi bi-arrow-down-short"></i> 8.4%
                        </span>
                         <span class="text-muted small">dari tahun lalu</span>
                    </div>
                </div>
            </div>
            <!-- LR -->
            <div class="col-xl-3 col-md-6">
                <div class="stat-card border-start border-4 border-success">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stat-label">Luka Ringan (LR)</div>
                        <div class="bg-success bg-opacity-10 p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                             <i class="bi bi-person-check-fill text-success fs-5"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-end mb-2">
                        <div>
                            <div class="text-muted small fw-semibold">2024</div>
                            <div class="fs-4 fw-bold text-dark opacity-75">4,583</div>
                        </div>
                        <div class="text-end">
                            <div class="text-muted small fw-semibold">2025</div>
                            <div class="fs-2 fw-bold text-success">4,120</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center pt-2 border-top">
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 me-2">
                             <i class="bi bi-arrow-down-short"></i> 10.1%
                        </span>
                        <span class="text-muted small">dari tahun lalu</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="row g-4">
            <!-- Left Column: Main Chart -->
            <div class="col-lg-8">
                <!-- Daily Stats Row (New) -->
                <div class="row mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-warning rounded-pill me-2" style="width: 4px; height: 24px;"></div>
                            <h5 class="text-white fw-bold mb-0 text-uppercase ls-1" style="font-size: 0.9rem; letter-spacing: 1px;">Data Kejadian Hari Ini</h5>
                        </div>
                    <!-- Laka Hari Ini -->
                    <div class="col-xl-3 col-md-6 mb-0">
                        <div class="card rounded-3 border-0 shadow-sm overflow-hidden" style="background: linear-gradient(to right, #ffffff, #f8fafc);">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-uppercase text-muted fw-bold small mb-1" style="font-size: 0.7rem;">Jumlah Laka</div>
                                        <div class="fs-4 fw-bold text-dark">145</div>
                                    </div>
                                    <div class="bg-primary bg-opacity-10 text-primary rounded px-2 py-1 small fw-bold">
                                        <i class="bi bi-bicycle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- MD Hari Ini -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card rounded-3 border-0 shadow-sm overflow-hidden" style="background: linear-gradient(to right, #ffffff, #f8fafc);">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-uppercase text-muted fw-bold small mb-1" style="font-size: 0.7rem;">Meninggal Dunia (MD)</div>
                                        <div class="fs-4 fw-bold text-danger">12</div>
                                    </div>
                                    <div class="bg-danger bg-opacity-10 text-danger rounded px-2 py-1 small fw-bold">
                                        <i class="bi bi-heart-pulse-fill"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- LB Hari Ini -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card rounded-3 border-0 shadow-sm overflow-hidden" style="background: linear-gradient(to right, #ffffff, #f8fafc);">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-uppercase text-muted fw-bold small mb-1" style="font-size: 0.7rem;">Luka Berat (LB)</div>
                                        <div class="fs-4 fw-bold text-warning">24</div>
                                    </div>
                                    <div class="bg-warning bg-opacity-10 text-warning rounded px-2 py-1 small fw-bold">
                                        <i class="bi bi-bandaid-fill"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- LR Hari Ini -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card rounded-3 border-0 shadow-sm overflow-hidden" style="background: linear-gradient(to right, #ffffff, #f8fafc);">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-uppercase text-muted fw-bold small mb-1" style="font-size: 0.7rem;">Luka Ringan (LR)</div>
                                        <div class="fs-4 fw-bold text-success">156</div>
                                    </div>
                                    <div class="bg-success bg-opacity-10 text-success rounded px-2 py-1 small fw-bold">
                                        <i class="bi bi-person-check-fill"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="main-chart-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold mb-0">Tren Kejadian Laka (Harian)</h4>
                        <div class="d-flex gap-3">
                            <span class="d-flex align-items-center gap-2 small fw-bold">
                                <span style="width: 12px; height: 12px; background: black; border-radius: 50%;"></span>
                                Ops Lilin 2024
                            </span>
                            <span class="d-flex align-items-center gap-2 small fw-bold">
                                <span style="width: 12px; height: 12px; background: #22c55e; border-radius: 50%;"></span>
                                Ops Lilin 2025
                            </span>
                        </div>
                    </div>
                    <div id="mainChart" style="height: 500px; width: 100%;"></div>
                </div>
            </div>

            <!-- Right Column: Tables -->
            <div class="col-lg-4">
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="d-flex align-items-center">
                            <div class="bg-success rounded-pill me-2" style="width: 4px; height: 24px;"></div>
                            <h5 class="text-white fw-bold mb-0 text-uppercase ls-1" style="font-size: 0.9rem; letter-spacing: 1px;">Akumulasi Data Perbandingan 2024 vs 2025</h5>
                        </div>
                    </div>
                </div>
                <!-- Usia -->
                <div class="side-table-card pb-2">
                    <div class="card-header-styled">
                        <span><i class="bi bi-people-fill me-2 text-primary"></i>Klasifikasi Usia Korban</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover custom-table mb-0">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th class="text-center text-year-2024 ">2024</th>
                                    <th class="text-center text-year-2025">2025</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>15 - 19 Tahun</td>
                                    <td class="text-center">452</td>
                                    <td class="text-center">380</td>
                                </tr>
                                <tr>
                                    <td>20 - 24 Tahun</td>
                                    <td class="text-center">612</td>
                                    <td class="text-center">540</td>
                                </tr>
                                <tr>
                                    <td>25 - 29 Tahun</td>
                                    <td class="text-center">390</td>
                                    <td class="text-center">350</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Kendaraan -->
                <div class="side-table-card pb-2">
                    <div class="card-header-styled">
                        <span><i class="bi bi-car-front-fill me-2 text-warning"></i>Jenis Kendaraan</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover custom-table mb-0">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th class="text-center text-year-2024">2024</th>
                                    <th class="text-center text-year-2025">2025</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Sepeda Motor</td>
                                    <td class="text-center">2,100</td>
                                    <td class="text-center">1,850</td>
                                </tr>
                                <tr>
                                    <td>Mobil Penumpang</td>
                                    <td class="text-center">850</td>
                                    <td class="text-center">820</td>
                                </tr>
                                <tr>
                                    <td>Bus</td>
                                    <td class="text-center">120</td>
                                    <td class="text-center">110</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                 <!-- Jalan -->
                 <div class="side-table-card pb-2">
                    <div class="card-header-styled">
                        <span><i class="bi bi-signpost-split-fill me-2 text-info"></i>Fungsi Jalan</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover custom-table mb-0">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th class="text-center text-year-2024">2024</th>
                                    <th class="text-center text-year-2025">2025</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Arteri</td>
                                    <td class="text-center">1,200</td>
                                    <td class="text-center">1,050</td>
                                </tr>
                                <tr>
                                    <td>Kolektor</td>
                                    <td class="text-center">800</td>
                                    <td class="text-center">720</td>
                                </tr>
                                <tr>
                                    <td>Tol</td>
                                    <td class="text-center">150</td>
                                    <td class="text-center">180</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conclusion Section (Static) -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-header bg-white p-3 border-bottom d-flex align-items-center">
                        <i class="bi bi-file-bar-graph-fill me-2 text-primary fs-5"></i>
                        <h5 class="fw-bold mb-0 text-dark">Kesimpulan Data Ops Lilin 2025</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-info border-0 bg-info bg-opacity-10 d-flex align-items-center mb-4">
                            <i class="bi bi-info-circle-fill text-info fs-4 me-3"></i>
                            <div>
                                <strong>Status Umum:</strong> Terjadi penurunan angka kecelakaan lalu lintas secara signifikan dibandingkan dengan Ops Lilin 2024.
                            </div>
                        </div>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-3 text-secondary text-uppercase small ls-1">Poin Penting Analisa Data</h6>
                                <ul class="list-group list-group-flush mb-4">
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span>1. Penurunan total kejadian kecelakaan sebesar <strong>12.5%</strong>.</span>
                                        <span class="badge bg-success rounded-pill"><i class="bi bi-arrow-down"></i> 12.5%</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span>2. Jumlah korban Meninggal Dunia (MD) menurun sebesar <strong>15.2%</strong>.</span>
                                        <span class="badge bg-success rounded-pill"><i class="bi bi-arrow-down"></i> 15.2%</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span>3. Penurunan tingkat fatalitas pada usia produktif (20-24 Tahun).</span>
                                        <span class="badge bg-primary rounded-pill">Positif</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-3 text-secondary text-uppercase small ls-1">Rekomendasi Tindak Lanjut</h6>
                                <div class="card bg-light border-0">
                                    <div class="card-body d-flex align-items-center">
                                        <p class="mb-0 text-muted">
                                            Berdasarkan data di atas, disarankan untuk tetap mempertahankan konsentrasi pengamanan di jalur arteri dan meningkatkan sosialisasi keselamatan berkendara bagi pengguna sepeda motor, yang masih mendominasi angka kecelakaan (1,850 kejadian).
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    <script>
        // Clock functionality
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            document.getElementById('clock').innerText = timeString.replace(/\./g, ':');
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Main Chart Configuration
        document.addEventListener('DOMContentLoaded', function () {
            // Generate dummy dates
            const days = [];
            for(let i=1; i<=22; i++) {
                days.push('Hari ' + i);
            }

            Highcharts.chart('mainChart', {
                chart: {
                    type: 'spline',
                    style: { fontFamily: 'Inter, sans-serif' }
                },
                title: { text: '' },
                xAxis: {
                    categories: days,
                    gridLineWidth: 1,
                    gridLineColor: '#f1f5f9',
                    labels: { style: { color: '#64748b' } }
                },
                yAxis: {
                    title: { text: 'Jumlah Kejadian' },
                    gridLineColor: '#e2e8f0',
                    gridLineDashStyle: 'LongDash'
                },
                legend: { enabled: false },
                plotOptions: {
                    spline: {
                        lineWidth: 3,
                        marker: {
                            radius: 4,
                            lineColor: '#ffffff',
                            lineWidth: 2
                        }
                    }
                },
                series: [{
                    name: '2024',
                    data: [120, 132, 101, 134, 90, 230, 210, 240, 250, 220, 180, 150, 160, 140, 130, 120, 110, 100, 90, 80, 70, 60],
                    color: '#000000',
                    dashStyle: 'ShortDash'
                }, {
                    name: '2025',
                    data: [100, 110, 90, 110, 80, 190, 180, 200, 210, 190, 150, 130, 140, 120, 110, 100, 90, 80, 70, 60, 50, 40],
                    color: '#22c55e'
                }],
                credits: { enabled: false },
                tooltip: {
                    shared: true,
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    borderColor: '#e2e8f0',
                    borderRadius: 8,
                    shadow: true
                }
            });
        });
    </script>
</body>
</html>
