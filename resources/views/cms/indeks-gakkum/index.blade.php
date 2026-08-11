@extends('cms.layouts.app')

@section('_title', 'Indeks Gakkum - CMS ICELL')

@section('content')
    <div class="box">
        <div class="box-header d-flex justify-content-between align-items-center flex-wrap">
            <h3 class="fw-bold text-blue-dark mb-0">Indeks Gakkum (Clearance Rate)</h3>
            <form action="{{ route('cms.indeks-gakkum.index') }}" method="GET" class="d-flex align-items-center flex-wrap mt-2 mt-md-0">
                <label for="year" class="me-2 fw-bold">Tahun:</label>
                <select name="year" id="year" class="form-select me-2" style="width: 120px;">
                    @for($y = date('Y'); $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>

                <label for="quarter" class="me-2 ms-3 fw-bold">Triwulan:</label>
                <select name="quarter" id="quarter" class="form-select me-2" style="width: 150px;">
                    <option value="all" {{ $quarter == 'all' ? 'selected' : '' }}>Setahun Penuh</option>
                    <option value="1" {{ $quarter == '1' ? 'selected' : '' }}>TW-I (Jan-Mar)</option>
                    <option value="2" {{ $quarter == '2' ? 'selected' : '' }}>TW-II (Jan-Jun)</option>
                    <option value="3" {{ $quarter == '3' ? 'selected' : '' }}>TW-III (Jan-Sep)</option>
                    <option value="4" {{ $quarter == '4' ? 'selected' : '' }}>TW-IV (Jan-Des)</option>
                </select>

                <button type="submit" class="btn btn-primary ms-2">Filter</button>
            </form>
        </div>
        <div class="box-body mt-4">
            
            <div class="alert alert-info mb-4">
                <strong>Formula Clearance Rate:</strong> C = (KB + TS) / (TB + TS) × 100%
            </div>

            {{-- Kartu Metrik Utama --}}
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card bg-primary text-white text-center shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Laka Tahun Berjalan (TB)</h5>
                            <h2 class="display-5 fw-bold mb-0">{{ number_format($tb, 0, ',', '.') }}</h2>
                            <p class="mb-0 mt-2"><small>Sumber: API IRSMS getTotalLaka</small></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-success text-white text-center shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Selra Tahun Berjalan (KB)</h5>
                            <h2 class="display-5 fw-bold mb-0">{{ number_format($kb, 0, ',', '.') }}</h2>
                            <p class="mb-0 mt-2"><small>Laka tahun {{ $year }} diselesaikan tahun {{ $year }}</small></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-warning text-dark text-center shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Tunggakan Selra (TS)</h5>
                            <h2 class="display-5 fw-bold mb-0">{{ number_format($ts, 0, ',', '.') }}</h2>
                            <p class="mb-0 mt-2"><small>Laka tahun {{ $year - 1 }} diselesaikan tahun {{ $year }}</small></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Rincian KB per jenis Selra --}}
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-success text-white fw-bold">
                            <i class="bi bi-list-check me-2"></i>Rincian Selra Tahun Berjalan (KB)
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Jenis Selra</th>
                                        <th class="text-center">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>P21</td>
                                        <td class="text-center fw-bold">{{ number_format($kbDetail->p21 ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>SP3</td>
                                        <td class="text-center fw-bold">{{ number_format($kbDetail->sp3 ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Diversi</td>
                                        <td class="text-center fw-bold">{{ number_format($kbDetail->diversi ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>POM / TNI</td>
                                        <td class="text-center fw-bold">{{ number_format($kbDetail->pom_tni ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>SP2LID</td>
                                        <td class="text-center fw-bold">{{ number_format($kbDetail->sp2lid ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                                <tfoot class="table-success">
                                    <tr>
                                        <td class="fw-bold">Total KB</td>
                                        <td class="text-center fw-bold">{{ number_format($kb, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Rincian TS per jenis Selra --}}
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-warning text-dark fw-bold">
                            <i class="bi bi-list-check me-2"></i>Rincian Tunggakan Selra (TS)
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Jenis Selra</th>
                                        <th class="text-center">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>P21</td>
                                        <td class="text-center fw-bold">{{ number_format($tsDetail->p21 ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>SP3</td>
                                        <td class="text-center fw-bold">{{ number_format($tsDetail->sp3 ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Diversi</td>
                                        <td class="text-center fw-bold">{{ number_format($tsDetail->diversi ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>POM / TNI</td>
                                        <td class="text-center fw-bold">{{ number_format($tsDetail->pom_tni ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>SP2LID</td>
                                        <td class="text-center fw-bold">{{ number_format($tsDetail->sp2lid ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                                <tfoot class="table-warning">
                                    <tr>
                                        <td class="fw-bold">Total TS</td>
                                        <td class="text-center fw-bold">{{ number_format($ts, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Hasil Clearance Rate --}}
            <div class="card border-0 shadow-sm mt-2">
                <div class="card-body text-center p-5">
                    <h3 class="fw-bold mb-4">
                        Hasil Clearance Rate (Indeks Gakkum) Tahun {{ $year }} 
                        @if($quarter != 'all') 
                            - Triwulan {{ $quarter }} 
                        @endif
                    </h3>
                    
                    <div class="d-flex justify-content-center align-items-center mb-3">
                        <div style="font-size: 5rem; font-weight: 800; color: {{ $clearanceRate >= 55 ? '#28a745' : '#dc3545' }}">
                            {{ $clearanceRate }}%
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <h4 class="fw-bold">
                            Kinerja Level: <span class="badge bg-{{ $kinerjaLevel <= 2 ? 'success' : ($kinerjaLevel == 3 ? 'warning text-dark' : 'danger') }} fs-4">Level {{ $kinerjaLevel }}</span>
                        </h4>
                        <p class="text-muted mt-2">
                            (Level 1: 86-100%, Level 2: 71-85%, Level 3: 55-70%, Level 4: ≤54%)
                        </p>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <h5 class="fw-bold text-muted">Perhitungan:</h5>
                        <p class="fs-5">
                            C = ({{ number_format($kb, 0, ',', '.') }} + {{ number_format($ts, 0, ',', '.') }}) / 
                            ({{ number_format($tb, 0, ',', '.') }} + {{ number_format($ts, 0, ',', '.') }}) × 100%
                        </p>
                        <p class="fs-5">
                            C = {{ number_format($kb + $ts, 0, ',', '.') }} / {{ number_format($tb + $ts, 0, ',', '.') }} × 100% = <strong>{{ $clearanceRate }}%</strong>
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
