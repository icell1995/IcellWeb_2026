<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard ICELL</title>
    <link rel="shortcut icon" href="{{ asset('images/logo1x.png') }}" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap1x.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modern-dashboard.css') }}">

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <script src="{{ asset('js/bootstrap1x.js') }}"></script>

    <!-- Highcharts -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
</head>

<body>
    <!-- Dark Mode Toggle Button -->
    {{-- <button class="btn-dark-toggle" id="darkModeToggle">
        <i class="bi bi-moon-stars"></i>
    </button> --}}

    <div class="container-fluid vh-100 p-0">
        <!-- Header -->
        <div class="dashboard-header">
            <div class="header-content">
                <div class="d-flex justify-content-between align-items-start align-items-lg-center gap-4">
                    <div class="d-flex align-items-start gap-4">
                        <div class="dashboard-icon">
                            <div class="logo-container">
                                <img 
                                    src="{{ asset('images/logo1.png') }}" 
                                    alt="Logo Polri" 
                                    class="dashboard-logo logo-polri"
                                    onerror="this.style.display='none';"
                                >
                                <div class="logo-divider"></div>
                                <img 
                                    src="{{ asset('images/logo2x.png') }}" 
                                    alt="ICELL Logo" 
                                    class="dashboard-logo logo-korlantas"
                                    onerror="this.style.display='none';"
                                >
                            </div>
                            <i class="bi bi-speedometer2 text-white fallback-icon" style="font-size: 2.5rem; display: none;"></i>
                        </div>
                        <div>
                            <h1 class="dashboard-title h3 fw-bold mb-2">
                                Dashboard System ICELL
                            </h1>
                            <div class="d-flex flex-wrap align-items-center gap-4 header-info">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-calendar3 fs-5"></i>
                                    <span class="fw-medium">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2" style="width: 100px">
                                    <i class="bi bi-clock fs-5"></i>
                                    <span class="fw-medium" id="currentTime"></span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-activity fs-5"></i>
                                    <span class="fw-medium">Informasi Cepat Penyidikan Lalu Lintas {{ now()->format('Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center gap-3">
                        <div class="header-badge d-flex align-items-center gap-2">
                            <i class="bi bi-calendar-week"></i>
                            <span class="fw-semibold">Periode {{ \Carbon\Carbon::now()->startOfYear()->locale('id')->isoFormat('D MMMM Y') }} - {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}</span>
                        </div>
                        <button class="btn-refresh" id="refreshData">
                            <i class="bi bi-arrow-clockwise fs-5"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-body">
            <!-- Status Cards Row -->
            <div class="row g-4 mb-4">
                <!-- Laka Masuk DORS Card -->
                <div class="col-lg-3 col-md-3 col-sm-6 col-12">
                    <div class="stats-card primary h-100">
                        <div class="p-4 position-relative h-100">
                            <div class="position-absolute top-0 end-0 mt-3 me-3">
                                <div class="stat-icon" style="background: rgba(37, 99, 235, 0.1);">
                                    <i class="bi bi-box-arrow-in-down fs-4" style="color: var(--blue-600);"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column justify-content-between h-100">
                                <div>
                                    <span class="text-uppercase fw-semibold text-muted small">Laka Masuk Dari DORS</span>
                                    <span class="text-base fw-bold d-block">Hari ini</span>
                                </div>
                                <h2 class="stats-number text-center my-auto display-2 fw-bold text-primary" id="dorsCount">
                                    {{ number_format($countDORS, 0, '.', '.') }}
                                </h2>
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted small fw-medium">Update terakhir</span>
                                        <span class="badge-custom badge-primary">
                                            <i class="bi bi-clock-history me-1"></i>
                                            {{ now()->format('d M Y') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LP Ditindaklanjuti Card -->
                <div class="col-lg-3 col-md-3 col-sm-6 col-12">
                    <div class="stats-card warning h-100">
                        <div class="p-4 position-relative h-100">
                            <div class="position-absolute top-0 end-0 mt-3 me-3">
                                <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1);">
                                    <i class="bi bi-hourglass-split fs-4" style="color: var(--amber-600);"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column justify-content-between h-100">
                                <div>
                                    <span class="text-uppercase fw-semibold text-muted small">LP Ditindaklanjuti</span>
                                    <span class="text-base fw-bold d-block">Hari ini</span>
                                </div>
                                <h2 class="stats-number text-center my-auto display-2 fw-bold text-warning" id="totalCard">
                                    {{ number_format($total[0]->totalall, 0, '.', '.') }}
                                </h2>
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted small fw-medium">Update terakhir</span>
                                        <span class="badge-custom badge-warning">
                                            <i class="bi bi-clock-history me-1"></i>
                                            {{ now()->format('d M Y') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total SELRA Card -->
                <div class="col-lg-3 col-md-3 col-sm-6 col-12">
                    <div class="stats-card success h-100">
                        <div class="p-4 position-relative h-100">
                            <div class="position-absolute top-0 end-0 mt-3 me-3">
                                <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1);">
                                    <i class="bi bi-file-earmark-check fs-4" style="color: var(--emerald-600);"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column justify-content-between h-100">
                                <div>
                                    <span class="text-uppercase fw-semibold text-muted small">Total SELRA</span>
                                    <span class="text-base fw-bold d-block">Sampai hari ini</span>
                                </div>
                                <h2 class="stats-number text-center my-auto display-2 fw-bold text-success" id="selraCard">
                                    {{ number_format($total_selra, 0, '.', '.') }}
                                </h2>
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted small fw-medium">Update terakhir</span>
                                        <span class="badge-custom badge-success">
                                            <i class="bi bi-clock-history me-1"></i>
                                            {{ now()->format('d M Y') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Pejabat TTE Card -->
                <div class="col-lg-3 col-md-3 col-sm-6 col-12">
                    <div class="stats-card info h-100">
                        <div class="p-4 position-relative h-100">
                            <div class="position-absolute top-0 end-0 mt-3 me-3">
                                <div class="stat-icon" style="background: rgba(6, 182, 212, 0.1);">
                                    <i class="bi bi-person-badge fs-4" style="color: var(--cyan-600);"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column justify-content-between h-100">
                                <div>
                                    <span class="text-uppercase fw-semibold text-muted small">Total Polres TTE Aktif</span>
                                    <span class="text-base fw-bold d-block">Sampai hari ini</span>
                                </div>
                                <div class="text-center my-auto">
                                    <h2 class="stats-number display-2 fw-bold text-info mb-1" id="pejabatCard">
                                        {{ $active_tte_units }}
                                    </h2>
                                    <p class="text-muted mb-0 fs-6 fw-medium">dari <strong class="text-info">{{ $total_polres }} Polres</strong></p>
                                    <small class="text-muted">telah menggunakan TTE</small>
                                </div>
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted small fw-medium">Persentase Aktif</span>
                                        <span class="badge-custom badge-info" id="pejabatPercentage">
                                            {{ $persentase_polres_tte }}%
                                        </span>
                                    </div>
                                    <div class="progress-custom">
                                        <div class="progress-bar-custom" id="pejabatProgress" style="width: 79.6%; background: linear-gradient(135deg, var(--cyan-500) 0%, var(--cyan-600) 100%);">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Table Row -->
            <div class="row g-4">
                <!-- Left Column - Charts -->
                <div class="col-lg-8 col-md-12 col-12">
                    <!-- Bar Chart -->
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title fw-bold mb-0">
                                    <i class="bi bi-bar-chart-fill me-2 text-primary"></i>
                                    Jumlah LP Ditindaklanjuti Per-Bulan
                                </h5>
                                <button id="refreshBarChart" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </div>
                            <div id="dashBar" class="chart-container" style="height: 350px;"></div>
                        </div>
                    </div>

                    <!-- Pie Charts Row -->
                    <div class="row g-4">
                        <!-- Last Year Pie Chart -->
                        <div class="col-lg-6 col-md-6 col-12">
                            <div class="card shadow">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="card-title fw-bold mb-0">
                                            <i class="bi bi-pie-chart-fill me-2 text-warning"></i>
                                            SELRA Tahun Lalu
                                        </h5>
                                    </div>
                                    <div id="dashPie2" class="chart-container" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Current Year Pie Chart -->
                        <div class="col-lg-6 col-md-6 col-12">
                            <div class="card shadow">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="card-title fw-bold mb-0">
                                            <i class="bi bi-pie-chart-fill me-2 text-success"></i>
                                            SELRA Tahun {{ now()->format('Y') }}
                                        </h5>
                                    </div>
                                    <div id="dashPie" class="chart-container" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Table -->
                <div class="col-lg-4 col-md-12 col-12">
                    <div class="card shadow h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex flex-column justify-content-between align-items-start mb-3">
                                <h5 class="card-title fw-bold mb-0">
                                    <i class="bi bi-table me-2 mt-4 text-primary"></i>
                                    Jumlah Selra Per-Polda
                                </h5>
                                <small class="d-block" style="margin-left: 2rem">
                                    Berdasarkan input selra sampai dengan hari ini
                                </small>
                            </div>
                            <div class="table-responsive flex-grow-1">
                                <table class="table table-bordered table-hover custom-table">
                                    <thead>
                                        <tr class="bg-light">
                                            <th class="text-center">Polda</th>
                                            <th class="text-center">P21</th>
                                            <th class="text-center">SP3</th>
                                            <th class="text-center">DIV</th>
                                            <th class="text-center">SP2LID</th>
                                            <th class="text-center">POM</th>
                                        </tr>
                                    </thead>
                                    <tbody class="selra-table-body">
                                        @foreach ($selra as $row)
                                            <tr>
                                                <td class="fw-medium">{{ $row->name }}</td>
                                                <td class="text-center">{{ number_format($row->p21 ?? 0, 0, '.', '.') }}</td>
                                                <td class="text-center">{{ number_format($row->sp3 ?? 0, 0, '.', '.') }}</td>
                                                <td class="text-center">{{ number_format($row->diversi ?? 0, 0, '.', '.') }}</td>
                                                <td class="text-center">{{ number_format($row->sp2lid ?? 0, 0, '.', '.') }}</td>
                                                <td class="text-center">{{ number_format($row->pom_tni ?? 0, 0, '.', '.') }}</td>
                                            </tr>
                                        @endforeach
                                        <tr class="table-active fw-bold">
                                            <td>Total</td>
                                            <td class="text-center">{{ number_format($total_p21, 0, '.', '.') }}</td>
                                            <td class="text-center">{{ number_format($total_sp3, 0, '.', '.') }}</td>
                                            <td class="text-center">{{ number_format($total_diversi, 0, '.', '.') }}</td>
					    <td class="text-center">{{ number_format($total_sp2lid, 0, '.', '.') }}</td>
                                            <td class="text-center">{{ number_format($total_pom_tni, 0, '.', '.') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="mt-4 mb-3 text-center">
                <div class="py-2">
                    <small class="text-muted fw-medium">
                        &copy; {{ date('Y') }} ICELL Dashboard | Informasi Cepat Penyidikan Lalu Lintas
                    </small>
                </div>
            </footer>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay d-none" id="loadingOverlay">
        <div class="loading-content">
            <div class="spinner"></div>
            <span class="fw-medium">Memperbarui data...</span>
        </div>
    </div>

    <style>
        /* Custom styles for TTE card */
        .stats-card.info .stats-number {
            text-shadow: 0 2px 4px rgba(6, 182, 212, 0.1);
        }
        
        .stats-card.info .text-info {
            color: var(--cyan-600) !important;
        }
        
        .stats-card.info small {
            font-size: 0.75rem;
            opacity: 0.8;
        }
        
        .badge-custom.badge-info {
            background: linear-gradient(135deg, var(--cyan-500) 0%, var(--cyan-600) 100%);
            color: white;
            font-weight: 600;
            padding: 0.375rem 0.75rem;
            border-radius: 0.5rem;
        }
        
        .progress-custom {
            height: 6px;
            background-color: rgba(6, 182, 212, 0.1);
            border-radius: 3px;
            overflow: hidden;
        }
        
        .progress-bar-custom {
            height: 100%;
            border-radius: 3px;
            transition: width 0.6s ease;
        }
    </style>

    <!-- Dashboard Scripts -->
    <script src="{{ asset('js/modern-dashboard01.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        // Auto-reload timer update for time display
        setInterval(updateTime, 1000);

        // Create auto reload indicator UI
        function createAutoReloadIndicator() {
            try {
                // Remove existing indicator if any
                const existingIndicator = document.getElementById('autoReloadIndicator');
                if (existingIndicator) {
                    existingIndicator.remove();
                }

                const indicator = document.createElement('div');
                indicator.id = 'autoReloadIndicator';
                indicator.className = 'auto-reload-indicator';
                indicator.innerHTML = `
                    <div class="d-flex align-items-center gap-2">
                        <div class="auto-reload-dot"></div>
                        <span class="auto-reload-text">Memperbarui data dalam <span id="countdown">${formatTime(remainingTime)}</span></span>
                        <button class="btn-toggle-auto-reload" id="toggleAutoReload" title="Pause auto reload">
                            <i class="bi bi-pause-fill"></i>
                        </button>
                    </div>
                `;
                document.body.appendChild(indicator);

                // Add event listener to toggle button
                const toggleBtn = document.getElementById('toggleAutoReload');
                if (toggleBtn) {
                    toggleBtn.addEventListener('click', toggleAutoReload);
                }
            } catch (error) {
                console.error('Error creating auto reload indicator:', error);
            }
        }

        // Add CSS styles for auto reload indicator
        function addAutoReloadStyles() {
            if (document.getElementById('autoReloadStyles')) return;

            const autoReloadStyles = document.createElement('style');
            autoReloadStyles.id = 'autoReloadStyles';
            autoReloadStyles.textContent = `
                .auto-reload-indicator {
                    font-family: 'Inter', sans-serif;
                }
                
                .auto-reload-indicator .d-flex {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }
                
                #countdown {
                    font-family: 'Courier New', monospace;
                    font-weight: 600;
                    color: inherit;
                }
            `;
            document.head.appendChild(autoReloadStyles);
        }

        // Function to load bar chart
        function loadDashBarChart() {
            if(dashBarChart){
                dashBarChart.destroy();
                dashBarChart = null;
            }
            
            $('#loadingOverlay').removeClass('d-none');
            
            $.ajax({
                url: "{{ route('getDashBar') }}",
                type: 'get',
                success: function(data) {
                    var get_date = [];
                    var get_count = [];
                    
                    // Parse data
                    for (let x = 0; x < data.length; x++) {
                        get_date.push(data[x].date);
                        get_count.push(parseInt(data[x].count));
                    }
                    
                    // Create modern bar chart with enhanced styling
                    dashBarChart = Highcharts.chart('dashBar', {
                        chart: {
                            type: 'column',
                            style: {
                                fontFamily: 'Inter, sans-serif'
                            },
                            backgroundColor: {
                                linearGradient: [0, 0, 0, 300],
                                stops: [
                                    [0, 'rgba(255, 255, 255, 0.95)'],
                                    [1, 'rgba(248, 250, 252, 0.95)']
                                ]
                            },
                            borderRadius: 12,
                            plotBorderWidth: 0,
                            spacingTop: 20,
                            spacingRight: 20,
                            spacingBottom: 20,
                            spacingLeft: 20
                        },
                        colors: [
                            '#3b82f6', '#10b981', '#f59e0b', '#ef4444', 
                            '#8b5cf6', '#ec4899', '#14b8a6', '#f97316',
                            '#6366f1', '#84cc16', '#06b6d4', '#d946ef'
                        ],
                        title: {
                            text: null
                        },
                        xAxis: {
                            categories: get_date,
                            crosshair: {
                                width: 2,
                                color: 'rgba(59, 130, 246, 0.2)',
                                dashStyle: 'dash'
                            },
                            labels: {
                                style: {
                                    fontSize: '12px',
                                    fontWeight: '500',
                                    color: '#64748b'
                                },
                                rotation: -45
                            },
                            lineColor: 'transparent',
                            tickColor: 'transparent',
                            gridLineWidth: 0
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'Jumlah LP',
                                style: {
                                    fontSize: '14px',
                                    fontWeight: '600',
                                    color: '#374151'
                                }
                            },
                            gridLineColor: 'rgba(148, 163, 184, 0.2)',
                            gridLineDashStyle: 'dash',
                            lineColor: 'transparent',
                            tickColor: 'transparent',
                            labels: {
                                style: {
                                    fontSize: '11px',
                                    color: '#64748b'
                                },
                                formatter: function() {
                                    return this.value.toLocaleString();
                                }
                            }
                        },
                        tooltip: {
                            headerFormat: '<div style="text-align:center; margin-bottom:8px;"><span style="font-size:14px; font-weight:700; color:#1f2937;">{point.key}</span></div>',
                            pointFormat: '<div style="display:flex; align-items:center; justify-content:space-between; min-width:150px;">' +
                                '<span style="color:{point.color}; font-size:16px;">?</span>' +
                                '<span style="font-weight:500; color:#374151;">{series.name}</span>' +
                                '<span style="font-weight:700; color:#1f2937; font-size:16px;">{point.y:,.0f}</span>' +
                                '</div>',
                            footerFormat: '',
                            shared: false,
                            useHTML: true,
                            backgroundColor: 'rgba(255, 255, 255, 0.98)',
                            borderWidth: 0,
                            borderRadius: 12,
                            shadow: {
                                color: 'rgba(0, 0, 0, 0.1)',
                                width: 8,
                                offsetX: 0,
                                offsetY: 4
                            },
                            style: {
                                padding: '12px'
                            }
                        },
                        plotOptions: {
                            column: {
                                borderRadius: {
                                    radius: 6,
                                    scope: 'point'
                                },
                                borderWidth: 0,
                                pointPadding: 0.15,
                                groupPadding: 0.1,
                                shadow: {
                                    color: 'rgba(0, 0, 0, 0.15)',
                                    width: 4,
                                    offsetX: 0,
                                    offsetY: 2
                                },
                                dataLabels: {
                                    enabled: true,
                                    crop: false,
                                    overflow: 'allow',
                                    format: '{point.y:,.0f}',
                                    style: {
                                        fontWeight: '600',
                                        fontSize: '11px',
                                        color: '#374151',
                                        textOutline: 'none'
                                    },
                                    y: -8
                                },
                                states: {
                                    hover: {
                                        brightness: 0.1,
                                        shadow: {
                                            color: 'rgba(0, 0, 0, 0.25)',
                                            width: 6,
                                            offsetX: 0,
                                            offsetY: 4
                                        }
                                    }
                                }
                            },
                            series: {
                                animation: {
                                    duration: 1200,
                                    easing: 'easeOutBounce'
                                },
                                colorByPoint: true,
                                colors: [
                                    {
                                        linearGradient: [0, 0, 0, 300],
                                        stops: [
                                            [0, '#3b82f6'],
                                            [1, '#1d4ed8']
                                        ]
                                    },
                                    {
                                        linearGradient: [0, 0, 0, 300],
                                        stops: [
                                            [0, '#10b981'],
                                            [1, '#059669']
                                        ]
                                    },
                                    {
                                        linearGradient: [0, 0, 0, 300],
                                        stops: [
                                            [0, '#f59e0b'],
                                            [1, '#d97706']
                                        ]
                                    },
                                    {
                                        linearGradient: [0, 0, 0, 300],
                                        stops: [
                                            [0, '#ef4444'],
                                            [1, '#dc2626']
                                        ]
                                    },
                                    {
                                        linearGradient: [0, 0, 0, 300],
                                        stops: [
                                            [0, '#8b5cf6'],
                                            [1, '#7c3aed']
                                        ]
                                    },
                                    {
                                        linearGradient: [0, 0, 0, 300],
                                        stops: [
                                            [0, '#ec4899'],
                                            [1, '#db2777']
                                        ]
                                    }
                                ]
                            }
                        },
                        legend: {
                            enabled: false
                        },
                        credits: {
                            enabled: false
                        },
                        series: [{
                            name: 'Jumlah LP',
                            data: get_count,
                            colorByPoint: true,
                            showInLegend: false
                        }]
                    });
                    
                    $('#loadingOverlay').addClass('d-none');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX Error:', textStatus, errorThrown);
                    $('#loadingOverlay').addClass('d-none');
                }
            });
        }

        // Function to load pie charts
        function loadPieChart() {
            if(dashPieChart){
                dashPieChart.destroy();
                dashPie2Chart.destroy();
                dashPieChart = null;
                dashPie2Chart = null;
            }
            
            $('#loadingOverlay').removeClass('d-none');

            $.ajax({
                url: "{{ route('getDashPie') }}",
                type: 'get',
                success: function(data) {
                    // Parse data for current year
                    var get_year = data[0].date_year;
                    var get_jumlah = data[0].jumlah_laka;
                    var pieData = [];
                    
                    for (let x = 0; x < data[0].jumlah_selra.length; x++) {
                        pieData.push({
                            name: data[0].jumlah_selra[x].name,
                            y: parseFloat(data[0].jumlah_selra[x].percentage)
                        });
                    }
                    
                    // Parse data for last year
                    var get_lastYear = data[0].lastYear;
                    var get_jumlah_lastYear = data[0].jumlah_laka_lastYear;
                    var pieData2 = [];
                    
                    for (let x = 0; x < data[0].jumlah_selra_lastYear.length; x++) {
                        pieData2.push({
                            name: data[0].jumlah_selra_lastYear[x].name,
                            y: parseFloat(data[0].jumlah_selra_lastYear[x].percentage_lastyear)
                        });
                    }
                    
                    // Create enhanced current year pie chart
                    dashPieChart = Highcharts.chart('dashPie', {
                        chart: {
                            type: 'pie',
                            style: {
                                fontFamily: 'Inter, sans-serif'
                            },
                            backgroundColor: {
                                linearGradient: [0, 0, 0, 300],
                                stops: [
                                    [0, 'rgba(255, 255, 255, 0.95)'],
                                    [1, 'rgba(248, 250, 252, 0.95)']
                                ]
                            },
                            borderRadius: 12,
                            plotBorderWidth: 0,
                            spacingTop: 15,
                            spacingRight: 15,
                            spacingBottom: 15,
                            spacingLeft: 15
                        },
                        colors: [
                            {
                                radialGradient: { cx: 0.5, cy: 0.3, r: 0.7 },
                                stops: [
                                    [0, '#3b82f6'],
                                    [1, '#1e40af']
                                ]
                            },
                            {
                                radialGradient: { cx: 0.5, cy: 0.3, r: 0.7 },
                                stops: [
                                    [0, '#10b981'],
                                    [1, '#065f46']
                                ]
                            },
                            {
                                radialGradient: { cx: 0.5, cy: 0.3, r: 0.7 },
                                stops: [
                                    [0, '#f59e0b'],
                                    [1, '#b45309']
                                ]
                            },
                            {
                                radialGradient: { cx: 0.5, cy: 0.3, r: 0.7 },
                                stops: [
                                    [0, '#ef4444'],
                                    [1, '#b91c1c']
                                ]
                            },
                            {
                                radialGradient: { cx: 0.5, cy: 0.3, r: 0.7 },
                                stops: [
                                    [0, '#8b5cf6'],
                                    [1, '#6d28d9']
                                ]
                            }
                        ],
                        title: {
                            text: null
                        },
                        subtitle: {
                            text: null
                        },
                        plotOptions: {
                            pie: {
                                allowPointSelect: true,
                                cursor: 'pointer',
                                innerSize: '60%',
                                borderRadius: 6,
                                borderWidth: 2,
                                borderColor: 'rgba(255, 255, 255, 0.8)',
                                dataLabels: {
                                    enabled: true,
                                    format: '<b>{point.name}</b>: {point.percentage:.1f}%',
                                    style: {
                                        fontWeight: '600',
                                        fontSize: '12px',
                                        textOutline: '1px contrast'
                                    },
                                    connectorShape: 'crookedLine',
                                    crookDistance: '70%'
                                },
                                showInLegend: false,
                                states: {
                                    hover: {
                                        halo: {
                                            size: 5,
                                            opacity: 0.25
                                        },
                                        brightness: 0.1
                                    }
                                }
                            },
                            series: {
                                animation: {
                                    duration: 1200,
                                    easing: 'easeOutBounce'
                                },
                                states: {
                                    inactive: {
                                        opacity: 0.6
                                    }
                                }
                            }
                        },
                        tooltip: {
                            pointFormat: '<span style="color:{point.color}; font-weight: 600;">{series.name}</span>: <b>{point.percentage:.1f}%</b><br/><span style="font-size: 12px; color: #64748b;">Total: {point.y} LP</span>',
                            backgroundColor: 'rgba(255, 255, 255, 0.95)',
                            borderWidth: 0,
                            borderRadius: 12,
                            shadow: {
                                color: 'rgba(0, 0, 0, 0.1)',
                                offsetX: 0,
                                offsetY: 4,
                                opacity: 0.3,
                                width: 8
                            },
                            style: {
                                fontFamily: 'Inter, sans-serif',
                                fontSize: '13px',
                                padding: '12px'
                            },
                            useHTML: true
                        },
                        credits: {
                            enabled: false
                        },
                        series: [{
                            name: 'Persentase',
                            colorByPoint: true,
                            data: pieData
                        }],
                        caption: {
                            text: `<span style="font-size: 14px; font-weight: 500;">Total SELRA: <b>${formatNumber(get_jumlah)} LP</b></span>`,
                            align: 'center',
                            style: {
                                fontFamily: 'Inter, sans-serif'
                            }
                        }
                    });
                    
                    // Create enhanced last year pie chart
                    dashPie2Chart = Highcharts.chart('dashPie2', {
                        chart: {
                            type: 'pie',
                            style: {
                                fontFamily: 'Inter, sans-serif'
                            },
                            backgroundColor: {
                                linearGradient: [0, 0, 0, 300],
                                stops: [
                                    [0, 'rgba(255, 255, 255, 0.95)'],
                                    [1, 'rgba(248, 250, 252, 0.95)']
                                ]
                            },
                            borderRadius: 12,
                            plotBorderWidth: 0,
                            spacingTop: 15,
                            spacingRight: 15,
                            spacingBottom: 15,
                            spacingLeft: 15
                        },
                        colors: [
                            {
                                radialGradient: { cx: 0.5, cy: 0.3, r: 0.7 },
                                stops: [
                                    [0, '#f59e0b'],
                                    [1, '#b45309']
                                ]
                            },
                            {
                                radialGradient: { cx: 0.5, cy: 0.3, r: 0.7 },
                                stops: [
                                    [0, '#ef4444'],
                                    [1, '#b91c1c']
                                ]
                            },
                            {
                                radialGradient: { cx: 0.5, cy: 0.3, r: 0.7 },
                                stops: [
                                    [0, '#8b5cf6'],
                                    [1, '#6d28d9']
                                ]
                            },
                            {
                                radialGradient: { cx: 0.5, cy: 0.3, r: 0.7 },
                                stops: [
                                    [0, '#10b981'],
                                    [1, '#065f46']
                                ]
                            },
                            {
                                radialGradient: { cx: 0.5, cy: 0.3, r: 0.7 },
                                stops: [
                                    [0, '#3b82f6'],
                                    [1, '#1e40af']
                                ]
                            }
                        ],
                        title: {
                            text: null
                        },
                        subtitle: {
                            text: null
                        },
                        plotOptions: {
                            pie: {
                                allowPointSelect: true,
                                cursor: 'pointer',
                                innerSize: '60%',
                                borderRadius: 6,
                                borderWidth: 2,
                                borderColor: 'rgba(255, 255, 255, 0.8)',
                                dataLabels: {
                                    enabled: true,
                                    format: '<b>{point.name}</b>: {point.percentage:.1f}%',
                                    style: {
                                        fontWeight: '600',
                                        fontSize: '12px',
                                        textOutline: '1px contrast'
                                    },
                                    connectorShape: 'crookedLine',
                                    crookDistance: '70%'
                                },
                                showInLegend: false,
                                states: {
                                    hover: {
                                        halo: {
                                            size: 5,
                                            opacity: 0.25
                                        },
                                        brightness: 0.1
                                    }
                                }
                            },
                            series: {
                                animation: {
                                    duration: 1200,
                                    easing: 'easeOutBounce'
                                },
                                states: {
                                    inactive: {
                                        opacity: 0.6
                                    }
                                }
                            }
                        },
                        tooltip: {
                            pointFormat: '<span style="color:{point.color}; font-weight: 600;">{series.name}</span>: <b>{point.percentage:.1f}%</b><br/><span style="font-size: 12px; color: #64748b;">Total: {point.y} LP</span>',
                            backgroundColor: 'rgba(255, 255, 255, 0.95)',
                            borderWidth: 0,
                            borderRadius: 12,
                            shadow: {
                                color: 'rgba(0, 0, 0, 0.1)',
                                offsetX: 0,
                                offsetY: 4,
                                opacity: 0.3,
                                width: 8
                            },
                            style: {
                                fontFamily: 'Inter, sans-serif',
                                fontSize: '13px',
                                padding: '12px'
                            },
                            useHTML: true
                        },
                        credits: {
                            enabled: false
                        },
                        series: [{
                            name: 'Persentase',
                            colorByPoint: true,
                            data: pieData2
                        }],
                        caption: {
                            text: `<span style="font-size: 14px; font-weight: 500;">Total SELRA: <b>${formatNumber(get_jumlah_lastYear)} LP</b></span>`,
                            align: 'center',
                            style: {
                                fontFamily: 'Inter, sans-serif'
                            }
                        }
                    });
                    
                    $('#loadingOverlay').addClass('d-none');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX Error:', textStatus, errorThrown);
                    $('#loadingOverlay').addClass('d-none');
                }
            });
        }

        // Function to update card counts
        function updateContent() {
            $.ajax({
                url: "{{ route('updateContent') }}",
                type: 'get',
                success: function(data) {
                    // Update card counters with animation
                    animateCounter('dorsCount', parseFormattedNumber($('#dorsCount').text()), data.countDORS);
                    animateCounter('totalCard', parseFormattedNumber($('#totalCard').text()), data.total[0].totalall);
                    animateCounter('selraCard', parseFormattedNumber($('#selraCard').text()), data.total_selra);
                    
                    // Update Polres TTE Aktif card
                    if (data.polresTTE || data.active_tte_units !== undefined) {
                        const activePolres = data.active_tte_units || 0; // Default value jika tidak ada data
                        const totalPolres = data.total_polres || 0; // Default value jika tidak ada data
                        const persentase = totalPolres > 0 ? Math.round((activePolres / totalPolres) * 100) : 0;
                        
                        // Update angka utama dengan animasi khusus (tanpa formatting)
                        animateCounterSimple('pejabatCard', parseInt($('#pejabatCard').text()) || 0, activePolres);
                        
                        // Update persentase dan progress bar
                        $('#pejabatPercentage').text(`${persentase}%`);
                        $('#pejabatProgress').css('width', `${persentase}%`);
                    }
                    
                    console.log('Content updated successfully');
                },
                error: function(error) {
                    console.error('Error updating content', error);
                }
            });
        }

        // Helper function to animate counter without formatting (for simple numbers)
        function animateCounterSimple(id, start, end) {
            let current = start;
            const increment = end > start ? 1 : -1;
            const step = Math.abs(end - start) / 50;
            const timer = setInterval(function() {
                current += increment * step;
                if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                    clearInterval(timer);
                    current = end;
                }
                $('#' + id).text(Math.round(current));
            }, 10);
        }
        
        // Initialize dashboard
        // Note: Auto-reload, styling, dan utility functions telah dipindahkan ke modern-dashboard.js
        loadDashBarChart();
        loadPieChart();
        updateTime();
        
        // Format existing numbers on page load
        function formatExistingNumbers() {
            // Format numbers in cards (if they're not already formatted)
            // Note: pejabatCard dikecualikan karena menggunakan format khusus
            const elements = ['#dorsCount', '#totalCard', '#selraCard'];
            elements.forEach(function(selector) {
                const element = $(selector);
                const currentValue = parseFormattedNumber(element.text());
                if (currentValue > 0) {
                    element.text(formatNumber(currentValue));
                }
            });
        }
        
        // Initialize number formatting
        formatExistingNumbers();
        
        // Initialize auto reload system
        addAutoReloadStyles();
        createAutoReloadIndicator();
        startCountdown();
        
        // Setup event listeners
        $('#refreshBarChart').on('click', function() {
            loadDashBarChart();
        });
        
        $('#refreshData').on('click', function() {
            // Reset countdown when manually refreshing
            resetCountdown();
            loadDashBarChart();
            loadPieChart();
            updateContent();
            // Restart countdown if auto reload is enabled
            if (isAutoReloadEnabled) {
                startCountdown();
            }
        });
        
        // Dark Mode Toggle
        $('#darkModeToggle').on('click', function() {
            // For now, just show refresh functionality
            loadDashBarChart();
            loadPieChart();
            updateContent();
            
            // Optional: Add dark mode functionality here
            const icon = $(this).find('i');
            if (icon.hasClass('bi-moon-stars')) {
                icon.removeClass('bi-moon-stars').addClass('bi-sun');
            } else {
                icon.removeClass('bi-sun').addClass('bi-moon-stars');
            }
        });
        
        // Update time display
        function updateTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            $('#currentTime').text(`${hours}:${minutes}:${seconds}`);
            
            setTimeout(updateTime, 1000);
        }

        // Apply custom styling for the selra table
        $('.custom-table').addClass('table-sm');
        
        // Adjust table height to fill card
        function adjustTableHeight() {
            const cardBody = $('.table-responsive').closest('.card-body');
            const cardHeaderHeight = cardBody.find('.d-flex.justify-content-between').outerHeight(true);
            const cardPadding = parseInt(cardBody.css('padding-top')) + parseInt(cardBody.css('padding-bottom'));
            const tableContainerHeight = cardBody.height() - cardHeaderHeight - cardPadding;
            
            $('.table-responsive').css({
                'height': tableContainerHeight + 'px'
            });
        }
        
        // Run on page load and window resize
        adjustTableHeight();
        $(window).on('resize', function() {
            adjustTableHeight();
        });
    });
</script>

</html>