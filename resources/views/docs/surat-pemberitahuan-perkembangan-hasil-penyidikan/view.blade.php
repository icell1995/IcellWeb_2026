@php
    $_title = 'Detail SP2HP - ' . ($sp2hp->nomor_surat ?? 'Draft');
@endphp

@extends('layouts.app')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        .paper-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
        .paper-header {
            background: linear-gradient(135deg, #1e293b 0%, #3b82f6 100%);
            color: white;
            padding: 30px 35px;
            border-bottom: 1px solid #e2e8f0;
        }
        .paper-body {
            padding: 40px;
        }
        .section-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            letter-spacing: -0.025em;
        }
        .section-title i {
            margin-right: 12px;
            color: #3b82f6;
            background: rgba(59, 130, 246, 0.1);
            padding: 10px;
            border-radius: 8px;
            font-size: 1rem;
        }
        .info-label {
            font-size: 0.75rem;
            color: #64748b;
            margin-bottom: 0.35rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .info-value {
            font-size: 1.1rem;
            color: #0f172a;
            font-weight: 600;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        .badge-custom {
            padding: 0.7em 1.4em;
            font-weight: 900;
            font-size: 1.05rem;
            border-radius: 6px;
            letter-spacing: 0.04em;
            box-shadow: 0 2px 4px rgba(0,0,0,.15);
        }

        .table-custom thead th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 800;
            border-bottom: 2px solid #e2e8f0;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 12px 16px;
        }
        .table-custom tbody td {
            padding: 12px 16px;
            vertical-align: middle;
            color: #334155;
            font-weight: 500;
        }
        .sub-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
        }
        .sub-card-title {
            font-weight: 800;
            color: #334155;
            margin-bottom: 18px;
            font-size: 1.05rem;
            letter-spacing: -0.01em;
        }
        @media print {
            .paper-card {
                box-shadow: none;
                border: none;
                border-radius: 0;
            }
            .paper-header {
                background: none !important;
                color: black !important;
                border-bottom: 3px solid black;
                padding: 0;
                margin-bottom: 30px;
            }
            .paper-header h4 {
                color: black !important;
                font-size: 24pt;
            }
            .paper-body {
                padding: 0;
            }
            .section-title {
                border-bottom: 2px solid black;
                color: black;
            }
            .section-title i {
                display: none;
            }
            .d-print-none {
                display: none !important;
            }
            .info-label {
                color: #000;
            }
            .info-value {
                color: #000;
            }
        }
    </style>

    <div class="container-fluid py-4">
        <!-- Action Buttons -->
        <div class="row mb-4 d-print-none justify-content-center">
            <div class="col-lg-10">
                <div class="d-flex justify-content-between align-items-center">
                    <a class="btn btn-outline-secondary rounded-pill px-4" href="{{ route('view_produktivitas_accident', ['accident_id' => $sp2hp->accident_id]) }}">
                        <i class="bi bi-arrow-left me-2"></i> Kembali ke Perkara
                    </a>
                    {{-- <button class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="window.print()">
                        <i class="bi bi-printer me-2"></i> Cetak Dokumen
                    </button> --}}
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="paper-card">
                    <!-- Header -->
                    <div class="paper-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1 fw-bold">
                                    <i class="fas fa-file-alt me-2"></i> SP2HP
                                </h4>
                                <p class="mb-0 opacity-75">Surat Pemberitahuan Perkembangan Hasil Penyidikan</p>
                            </div>
                            <div class="text-end">
                                @if(!empty($sp2hp->tipe_sp2hp))
                                    <span class="badge bg-white text-primary badge-custom mb-1">Tipe {{ strtoupper($sp2hp->tipe_sp2hp) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="paper-body">
                        <!-- Informasi Surat -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="section-title"><i class="fas fa-info-circle"></i> Informasi Surat</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Nomor LP</div>
                                <div class="info-value">{{ $sp2hp->nomor_lp ?? '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Tanggal LP</div>
                                <div class="info-value">{{ $sp2hp->tanggal_lp ? \Carbon\Carbon::parse($sp2hp->tanggal_lp)->format('d-m-Y') : '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Nomor Surat</div>
                                <div class="info-value">
                                    @if($sp2hp->nomor_surat)
                                        {{ $sp2hp->nomor_surat }}
                                    @elseif(isset($rujukanSurat) && $rujukanSurat)
                                        {{ $rujukanSurat->nomor_surat ?? '-' }}
                                        <small class="text-muted ms-1">(dari {{ $rujukanSurat->tipe_sp2hp }})</small>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Tanggal Surat</div>
                                <div class="info-value">
                                    @if($sp2hp->tanggal_surat)
                                        {{ \Carbon\Carbon::parse($sp2hp->tanggal_surat)->format('d-m-Y') }}
                                    @elseif(isset($rujukanSurat) && $rujukanSurat && $rujukanSurat->tanggal_surat)
                                        {{ \Carbon\Carbon::parse($rujukanSurat->tanggal_surat)->format('d-m-Y') }}
                                        <small class="text-muted ms-1">(dari {{ $rujukanSurat->tipe_sp2hp }})</small>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Tempat Surat</div>
                                <div class="info-value">{{ $sp2hp->tempat_surat ?? '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Tingkat Kasus</div>
                                <div class="info-value"><span class="badge bg-warning text-dark">{{ strtoupper($sp2hp->tingkat_kasus ?? '-') }}</span></div>
                            </div>
                        </div>

                        <!-- Data Pelapor -->
                        @if(!empty($sp2hp->pelapor_nama))
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="section-title"><i class="fas fa-user"></i> Data Pelapor</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Nama Pelapor</div>
                                <div class="info-value">{{ $sp2hp->pelapor_nama }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Alamat</div>
                                <div class="info-value">{{ $sp2hp->pelapor_alamat ?? '-' }}</div>
                            </div>
                        </div>
                        @endif

                        <!-- Data Penerima -->
                        @if(isset($allPenerima) && $allPenerima->count() > 0)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="section-title"><i class="fas fa-user-tie"></i> Penerima SP2HP</h5>
                                <div class="table-responsive">
                                    <table class="table table-hover table-custom align-middle">
                                        <thead>
                                            <tr>
                                                <th width="5%" class="text-center">No</th>
                                                <th width="20%">Nama</th>
                                                <th width="15%">Identitas</th>
                                                <th width="15%">No. Identitas</th>
                                                <th width="15%">Telepon</th>
                                                <th width="30%">Alamat</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($allPenerima as $index => $penerima)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td class="fw-bold">{{ $penerima->name }}</td>
                                                <td>{{ $penerima->identityType->name ?? '-' }}</td>
                                                <td>{{ $penerima->identity_number ?? '-' }}</td>
                                                <td>{{ $penerima->phone_number ?? '-' }}</td>
                                                <td><small class="text-muted">{{ \Illuminate\Support\Str::limit($penerima->address ?? '-', 60) }}</small></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Data Penyidik -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="section-title"><i class="fas fa-user-shield"></i> Data Penyidik</h5>
                                @if(isset($allPenyidik) && count($allPenyidik) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover table-custom align-middle">
                                        <thead>
                                            <tr>
                                                <th width="5%" class="text-center">No</th>
                                                <th width="15%">NRP</th>
                                                <th width="15%">Pangkat</th>
                                                <th width="25%">Nama</th>
                                                <th width="15%">Telepon</th>
                                                <th width="25%">Unit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($allPenyidik as $index => $penyidik)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>{{ $penyidik['nrp'] ?? '-' }}</td>
                                                <td>{{ $penyidik['pangkat'] ?? '-' }}</td>
                                                <td class="fw-bold">{{ $penyidik['nama'] ?? '-' }}</td>
                                                <td>{{ $penyidik['telp'] ?? '-' }}</td>
                                                <td>{{ \Illuminate\Support\Str::limit($penyidik['unit'] ?? '-', 40) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="alert alert-light text-center text-muted border-0">
                                    <i class="fas fa-info-circle me-2"></i> Tidak ada data penyidik
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Pejabat Penandatangan -->
                        @if(isset($signatory))
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="section-title"><i class="fas fa-signature"></i> Pejabat Penandatangan</h5>
                                <div class="sub-card">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="info-label">Nama</div>
                                            <div class="info-value">{{ $signatory['nama'] ?? '-' }}</div>
                                            <div class="info-label">NRP</div>
                                            <div class="info-value">{{ $signatory['nrp'] ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label">Pangkat</div>
                                            <div class="info-value">{{ $signatory['pangkat'] ?? '-' }}</div>
                                            <div class="info-label">Jabatan</div>
                                            <div class="info-value">{{ $signatory['jabatan'] ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label">Kontak</div>
                                            <div class="info-value">
                                                <div><i class="fas fa-phone-alt me-2 text-muted"></i> {{ $signatory['telp'] ?? '-' }}</div>
                                                <div><i class="fas fa-envelope me-2 text-muted"></i> {{ $signatory['email'] ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Data Kendaraan (A4-A7) -->
                        @if(isset($kendaraanDetail) && in_array($sp2hp->tipe_sp2hp, ['A4','A5','A6','A7']))
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="section-title"><i class="fas fa-car"></i> Kendaraan Barang Bukti</h5>
                                <div class="sub-card">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="info-label">Plat Nomor</div>
                                            <div class="info-value">{{ $kendaraanDetail['plat_nomor'] ?? '-' }}</div>
                                            <div class="info-label">Jenis</div>
                                            <div class="info-value">{{ $kendaraanDetail['jenis'] ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label">Merk</div>
                                            <div class="info-value">{{ $kendaraanDetail['merk'] ?? '-' }}</div>
                                            <div class="info-label">Warna</div>
                                            <div class="info-value">{{ $kendaraanDetail['warna'] ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label">Nomor Rangka</div>
                                            <div class="info-value">{{ $kendaraanDetail['nomor_rangka'] ?? '-' }}</div>
                                            <div class="info-label">Nomor Mesin</div>
                                            <div class="info-value">{{ $kendaraanDetail['nomor_mesin'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Data Tersangka (A3-A7) -->
                        @if(
                            isset($accident) &&
                            $accident->suspects &&
                            count($accident->suspects) > 0 &&
                            in_array($sp2hp->tipe_sp2hp, ['A4','A5','A6','A7'])
                        )
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="section-title"><i class="fas fa-user-tag"></i> Data Tersangka</h5>
                                <div class="table-responsive">
                                    <table class="table table-hover table-custom align-middle">
                                        <thead>
                                            <tr>
                                                <th width="5%" class="text-center">No</th>
                                                <th width="20%">Nama</th>
                                                <th width="10%">Identitas</th>
                                                <th width="15%">No. Identitas</th>
                                                <th width="15%">TTL</th>
                                                <th width="10%">JK</th>
                                                <th width="25%">Alamat</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($accident->suspects as $index => $suspect)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td class="fw-bold">{{ $suspect->name }}</td>
                                                <td>{{ $suspect->identityType->name ?? '-' }}</td>
                                                <td>{{ $suspect->identity_number ?? '-' }}</td>
                                                <td>
                                                    {{ $suspect->birth_place ?? '-' }},
                                                    {{ $suspect->birth_date 
                                                        ? \Carbon\Carbon::parse($suspect->birth_date)->format('d-m-Y') 
                                                        : '-' 
                                                    }}
                                                </td>
                                                <td>{{ $suspect->gender->name ?? '-' }}</td>
                                                <td><small class="text-muted">{{ \Illuminate\Support\Str::limit($suspect->address ?? '-', 50) }}</small></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif

                        @php
                            $typeSpecificData = is_string($sp2hp->type_specific_data) 
                                ? json_decode($sp2hp->type_specific_data, true) 
                                : $sp2hp->type_specific_data;
                            if (!is_array($typeSpecificData)) {
                                $typeSpecificData = [];
                            }
                        @endphp

                        <!-- Data Khusus Tipe A2 -->
                        @if($sp2hp->tipe_sp2hp == 'A2')
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-warning border-0 shadow-sm">
                                    <h5 class="alert-heading fw-bold"><i class="fas fa-exclamation-triangle me-2"></i> Data Khusus Tipe A2</h5>
                                    <p class="mb-0">Belum Dapat Ditingkatkan ke Penyidikan</p>
                                </div>
                                
                                <div class="row g-3">
                                    @if(!empty($typeSpecificData['a2_rujukan_a1']))
                                    <div class="col-12">
                                        <div class="sub-card">
                                            <div class="sub-card-title"><i class="fas fa-link me-2"></i> Nomor Surat A1 yang Dirujuk</div>
                                            <p class="mb-0">{{ $typeSpecificData['a2_rujukan_a1'] }}</p>
                                        </div>
                                    </div>
                                    @endif

                                    @if(!empty($typeSpecificData['a2_fakta_lidik']))
                                    <div class="col-12">
                                        <div class="sub-card">
                                            <div class="sub-card-title"><i class="fas fa-clipboard-list me-2"></i> Fakta-fakta Hasil Penyelidikan</div>
                                            <div class="text-dark">{!! nl2br(e($typeSpecificData['a2_fakta_lidik'])) !!}</div>
                                        </div>
                                    </div>
                                    @endif

                                    @if(!empty($typeSpecificData['a2_kendala']))
                                    <div class="col-12">
                                        <div class="sub-card">
                                            <div class="sub-card-title"><i class="fas fa-ban me-2"></i> Kendala Penyelidikan</div>
                                            <div class="text-dark">{!! nl2br(e($typeSpecificData['a2_kendala'])) !!}</div>
                                        </div>
                                    </div>
                                    @endif

                                    @if(!empty($typeSpecificData['a2_rencana_lanjut']))
                                    <div class="col-12">
                                        <div class="sub-card">
                                            <div class="sub-card-title"><i class="fas fa-forward me-2"></i> Rencana Tindak Lanjut</div>
                                            <div class="text-dark">{!! nl2br(e($typeSpecificData['a2_rencana_lanjut'])) !!}</div>
                                        </div>
                                    </div>
                                    @endif

                                    @if(!empty($typeSpecificData['a2_alasan']))
                                    <div class="col-12">
                                        <div class="sub-card">
                                            <div class="sub-card-title"><i class="fas fa-comment-dots me-2"></i> Kesimpulan / Alasan Belum Naik Sidik</div>
                                            <div class="text-dark">{!! nl2br(e($typeSpecificData['a2_alasan'])) !!}</div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Data Khusus Tipe A3 -->
                        @if($sp2hp->tipe_sp2hp == 'A3')
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-success border-0 shadow-sm">
                                    <h5 class="alert-heading fw-bold"><i class="fas fa-check-circle me-2"></i> Data Khusus Tipe A3</h5>
                                    <p class="mb-0">Perkembangan Hasil Penyidikan</p>
                                </div>

                                <div class="row g-3">
                                    @if(!empty($typeSpecificData['a3_rujukan_a1']))
                                    <div class="col-md-4">
                                        <div class="sub-card h-100">
                                            <div class="info-label">Rujukan A1</div>
                                            <div class="info-value">{{ $typeSpecificData['a3_rujukan_a1'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                    @endif

                                    @if(!empty($typeSpecificData['a3_tanggal_a1']))
                                    <div class="col-md-4">
                                        <div class="sub-card h-100">
                                            <div class="info-label">Tanggal A1</div>
                                            <div class="info-value">{{ $typeSpecificData['a3_tanggal_a1'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                    @endif

                                    @if(!empty($typeSpecificData['a3_sprin_sidik']))
                                    <div class="col-md-4">
                                        <div class="sub-card h-100">
                                            <div class="info-label">SPRIN Sidik</div>
                                            <div class="info-value">{{ $typeSpecificData['a3_sprin_sidik'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                    @endif

                                    @if(!empty($typeSpecificData['a3_tanggal_sprin']))
                                    <div class="col-md-4">
                                        <div class="sub-card h-100">
                                            <div class="info-label">Tanggal SPRIN</div>
                                            <div class="info-value">{{ $typeSpecificData['a3_tanggal_sprin'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                    @endif

                                    @if(!empty($typeSpecificData['a3_nomor_spdp']))
                                    <div class="col-md-4">
                                        <div class="sub-card h-100">
                                            <div class="info-label">Nomor SPDP</div>
                                            <div class="info-value">{{ $typeSpecificData['a3_nomor_spdp'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                    @endif

                                    @if(!empty($typeSpecificData['a3_tanggal_spdp']))
                                    <div class="col-md-4">
                                        <div class="sub-card h-100">
                                            <div class="info-label">Tanggal SPDP</div>
                                            <div class="info-value">{{ $typeSpecificData['a3_tanggal_spdp'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                    @endif

                                    @if(!empty($typeSpecificData['a3_tahap_penyidikan']))
                                    <div class="col-12">
                                        <div class="sub-card">
                                            <div class="sub-card-title"><i class="fas fa-layer-group me-2"></i> Tahap Penyidikan</div>
                                            <div class="text-dark">{!! nl2br(e($typeSpecificData['a3_tahap_penyidikan'])) !!}</div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Data Khusus Tipe A4 -->
                        @if($sp2hp->tipe_sp2hp == 'A4')
                        @php
                            $tindakanList = is_string($sp2hp->a4_tindakan_list) 
                                ? json_decode($sp2hp->a4_tindakan_list, true) 
                                : $sp2hp->a4_tindakan_list;
                            if (!is_array($tindakanList)) {
                                $tindakanList = [];
                            }
                        @endphp
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info border-0 shadow-sm">
                                    <h5 class="alert-heading fw-bold"><i class="fas fa-info-circle me-2"></i> Data Khusus Tipe A4</h5>
                                    <p class="mb-0">Hambatan / Kendala Penyidikan</p>
                                </div>

                                <div class="row g-3">
                                    <div class="col-12">
                                        <h6 class="section-title"><i class="fas fa-tasks"></i> Tindakan yang Telah Dilakukan</h6>
                                        @if(count($tindakanList) > 0)
                                        <div class="table-responsive mb-3">
                                            <table class="table table-hover table-custom align-middle">
                                                <thead>
                                                    <tr>
                                                        <th width="5%" class="text-center">No</th>
                                                        <th width="40%">Tindakan</th>
                                                        <th width="55%">Keterangan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($tindakanList as $index => $tindakan)
                                                    <tr>
                                                        <td class="text-center">{{ $index + 1 }}</td>
                                                        <td><i class="fas fa-check-circle text-success me-2"></i>{{ $tindakan['nama'] ?? '-' }}</td>
                                                        <td>{{ $tindakan['keterangan'] ?? '-' }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @else
                                        <div class="alert alert-light text-center text-muted border-0">
                                            <i class="fas fa-info-circle me-2"></i> Tidak ada tindakan yang tercatat
                                        </div>
                                        @endif
                                    </div>

                                    @if(!empty($typeSpecificData['a4_hambatan']))
                                    <div class="col-12">
                                        <div class="sub-card">
                                            <div class="sub-card-title"><i class="fas fa-exclamation-circle me-2"></i> Hambatan</div>
                                            <div class="text-dark">{!! nl2br(e($typeSpecificData['a4_hambatan'])) !!}</div>
                                        </div>
                                    </div>
                                    @endif

                                    @if(!empty($typeSpecificData['a4_rencana']))
                                    <div class="col-12">
                                        <div class="sub-card">
                                            <div class="sub-card-title"><i class="fas fa-tasks me-2"></i> Rencana Tindak Lanjut</div>
                                            <div class="text-dark">{!! nl2br(e($typeSpecificData['a4_rencana'])) !!}</div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Data Khusus Tipe A5 -->
                        @if($sp2hp->tipe_sp2hp == 'A5')
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-danger border-0 shadow-sm">
                                    <h5 class="alert-heading fw-bold"><i class="fas fa-stop-circle me-2"></i> Data Khusus Tipe A5</h5>
                                    <p class="mb-0">Penghentian Penyidikan (SP3)</p>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="sub-card h-100">
                                            <div class="info-label">SPRIN Sidik</div>
                                            <div class="info-value">{{ $typeSpecificData['a5_sprin_sidik'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="sub-card h-100">
                                            <div class="info-label">SP2HP Terakhir</div>
                                            <div class="info-value">{{ $typeSpecificData['a5_sp2hp_terakhir'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="sub-card h-100">
                                            <div class="info-label">Alasan SP3</div>
                                            <div class="info-value">{{ $typeSpecificData['a5_alasan_sp3'] ?? '-' }}</div>
                                        </div>
                                    </div>

                                    @if(!empty($typeSpecificData['a5_keterangan_sp3']))
                                    <div class="col-12">
                                        <div class="sub-card">
                                            <div class="sub-card-title"><i class="fas fa-comment-dots me-2"></i> Keterangan SP3</div>
                                            <div class="text-dark">{!! nl2br(e($typeSpecificData['a5_keterangan_sp3'])) !!}</div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Data Khusus Tipe A6 -->
                        @if($sp2hp->tipe_sp2hp == 'A6')
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info border-0 shadow-sm">
                                    <h5 class="alert-heading fw-bold"><i class="fas fa-share me-2"></i> Data Khusus Tipe A6</h5>
                                    <p class="mb-0">Pelimpahan Berkas Perkara Tahap 1</p>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="sub-card h-100">
                                            <div class="info-label">SP2HP Terakhir</div>
                                            <div class="info-value">{{ $typeSpecificData['a6_sp2hp_terakhir'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="sub-card h-100">
                                            <div class="info-label">Nama Tersangka</div>
                                            <div class="info-value">{{ $typeSpecificData['a6_nama_tersangka'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="sub-card h-100">
                                            <div class="info-label">Nomor Kirim Berkas</div>
                                            <div class="info-value">{{ $typeSpecificData['a6_nomor_kirim_berkas'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="sub-card h-100">
                                            <div class="info-label">Tanggal Kirim</div>
                                            <div class="info-value">{{ $typeSpecificData['a6_tanggal_kirim'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="sub-card h-100">
                                            <div class="info-label">Tujuan Kejaksaan</div>
                                            <div class="info-value">
                                                {{ $typeSpecificData['a6_tujuan_kejaksaan_name']
                                                    ?? $typeSpecificData['a6_tujuan_kejaksaan']
                                                    ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Data Khusus Tipe A7 -->
                        @if($sp2hp->tipe_sp2hp == 'A7')
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-primary border-0 shadow-sm">
                                    <h5 class="alert-heading fw-bold"><i class="fas fa-flag-checkered me-2"></i> Data Khusus Tipe A7</h5>
                                    <p class="mb-0">Pelimpahan ke Kejaksaan (Tahap 2)</p>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="sub-card h-100">
                                            <div class="info-label">Nama Tersangka</div>
                                            <div class="info-value">{{ $typeSpecificData['a7_nama_tersangka'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="sub-card h-100">
                                            <div class="info-label">Rujukan Tahap 1</div>
                                            <div class="info-value">{{ $typeSpecificData['a7_rujukan_tahap1'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="sub-card h-100">
                                            <div class="info-label">Nomor P21</div>
                                            <div class="info-value">{{ $typeSpecificData['a7_nomor_p21'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="sub-card h-100">
                                            <div class="info-label">Tanggal P21</div>
                                            <div class="info-value">{{ $typeSpecificData['a7_tanggal_p21'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="sub-card h-100">
                                            <div class="info-label">Nomor Kirim Tahap 2</div>
                                            <div class="info-value">{{ $typeSpecificData['a7_nomor_kirim_tahap2'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="sub-card h-100">
                                            <div class="info-label">Tanggal Serah Tahap 2</div>
                                            <div class="info-value">{{ $typeSpecificData['a7_tanggal_serah_tahap2'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="sub-card">
                                            <div class="info-label">Tujuan Kejaksaan</div>
                                            <div class="info-value">
                                                {{ $typeSpecificData['a7_tujuan_kejaksaan_name']
                                                    ?? $typeSpecificData['a7_tujuan_kejaksaan']
                                                    ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Pasal Diduga -->
                        @if(!empty($sp2hp->pasal_diduga))
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="section-title"><i class="fas fa-gavel"></i> Pasal yang Diduga</h5>
                                <div class="sub-card">
                                    <div class="text-dark fw-medium">
                                        {!! nl2br(e($sp2hp->pasal_diduga)) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Tembusan -->
                        @if(isset($tembusanList) && count($tembusanList) > 0)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="section-title"><i class="fas fa-copy"></i> Tembusan</h5>
                                <div class="sub-card">
                                    <ol class="mb-0 fw-medium">
                                        @foreach($tembusanList as $tembusan)
                                        <li>{{ $tembusan }}</li>
                                        @endforeach
                                    </ol>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Barang Bukti -->
                        @if(!empty($sp2hp->barang_bukti))
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="section-title"><i class="fas fa-box"></i> Barang Bukti</h5>
                                <div class="sub-card">
                                    <div class="text-dark">
                                        {!! nl2br(e($sp2hp->barang_bukti)) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Catatan -->
                        @if(!empty($sp2hp->catatan))
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="section-title"><i class="fas fa-sticky-note"></i> Catatan</h5>
                                <div class="sub-card bg-warning bg-opacity-10 border-warning">
                                    <div class="text-dark">
                                        {!! nl2br(e($sp2hp->catatan)) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Metadata -->
                        <div class="row mt-5 pt-4 border-top">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center text-muted small">
                                    <i class="fas fa-user-edit me-2"></i>
                                    <div>
                                        @if($sp2hp->createdByUser && $sp2hp->createdByUser->officer)
                                            @php
                                                $creator = $sp2hp->createdByUser->officer;
                                                $creatorRank = $creator->rank ? $creator->rank->name : '-';
                                                $creatorName = trim(($creator->first_name ?? '') . ' ' . ($creator->last_name ?? '')) ?: '-';
                                                $creatorNRP = $creator->register_number ?? ($creator->officer_id ?? '-');
                                            @endphp
                                            <strong>Dibuat oleh:</strong><br>
                                            <span class="opacity-75">{{ $creatorRank }} {{ $creatorName }}</span><br>
                                            <span class="opacity-75">NRP: {{ $creatorNRP }}</span><br>
                                            <span class="opacity-75">{{ $sp2hp->created_at ? $sp2hp->created_at->format('d F Y H:i') : '-' }}</span>
                                        @else
                                            <strong>Dibuat oleh:</strong> {{ $sp2hp->created_by ?? '-' }}<br>
                                            <span class="opacity-75">{{ $sp2hp->created_at ? $sp2hp->created_at->format('d F Y H:i') : '-' }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                <div class="d-flex align-items-center justify-content-md-end text-muted small">
                                    <div class="text-end">
                                        @if($sp2hp->updatedByUser && $sp2hp->updatedByUser->officer)
                                            @php
                                                $updater = $sp2hp->updatedByUser->officer;
                                                $updaterRank = $updater->rank ? $updater->rank->name : '-';
                                                $updaterName = trim(($updater->first_name ?? '') . ' ' . ($updater->last_name ?? '')) ?: '-';
                                                $updaterNRP = $updater->register_number ?? ($updater->officer_id ?? '-');
                                            @endphp
                                            <strong>Terakhir diupdate oleh:</strong><br>
                                            <span class="opacity-75">{{ $updaterRank }} {{ $updaterName }}</span><br>
                                            <span class="opacity-75">NRP: {{ $updaterNRP }}</span><br>
                                            <span class="opacity-75">{{ $sp2hp->updated_at ? $sp2hp->updated_at->format('d F Y H:i') : '-' }}</span>
                                        @else
                                            <strong>Terakhir diupdate:</strong><br>
                                            <span class="opacity-75">{{ $sp2hp->updated_at ? $sp2hp->updated_at->format('d F Y H:i') : '-' }}</span>
                                        @endif
                                    </div>
                                    <i class="fas fa-history ms-2"></i>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
