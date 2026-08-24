<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>SPDP — Pusiknas Bareskrim</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex justify-content-center" style="background-color:#eeeeee; padding: 20px 0;">
        <div class="radius-card mt-4 w-60" style="background:#fff; padding:30px; border-radius:8px; max-width:900px; width:100%;">

            {{-- Kop Surat --}}
            <div class="text-center mb-4">
                <img src="{{ asset('images/logo.png') }}" style="height:60px;" alt="Logo Polri"><br>
                <strong>KEPOLISIAN NEGARA REPUBLIK INDONESIA</strong><br>
                DAERAH {{ strtoupper($accident->polres->polda->full_name ?? '') }}<br>
                RESOR {{ strtoupper($accident->polres->full_name ?? '') }}<br>
                <span class="text-muted small">{{ ucwords($accident->polres->address ?? '') }}</span>
                <hr>
                <h4><u>SURAT PEMBERITAHUAN DIMULAINYA PENYIDIKAN</u></h4>
                <h5>NOMOR: {{ $document->document_number }}</h5>
                <span class="badge badge-info">KODE PROSES SPPT-TI: DIK-10</span>
            </div>

            {{-- identitas_dokumen --}}
            <table class="table table-bordered table-sm mb-4">
                <thead class="table-secondary"><tr><th colspan="2">identitas_dokumen</th></tr></thead>
                <tbody>
                    <tr><td class="fw-bold" width="40%">kode_jenis_dokumen</td><td><code>spdp</code></td></tr>
                    <tr><td class="fw-bold">nomor</td><td>{{ $document->document_number }}</td></tr>
                    <tr><td class="fw-bold">tanggal</td><td>{{ $document->document_date ? date('Y-m-d', strtotime($document->document_date)) : '-' }}</td></tr>
                </tbody>
            </table>

            {{-- konten_dokumen --}}
            <table class="table table-bordered table-sm mb-4">
                <thead class="table-info"><tr><th colspan="2">konten_dokumen</th></tr></thead>
                <tbody>
                    <tr><td class="fw-bold" width="40%">nomor_sprindik</td>
                        <td>{{ $document->suratPerintahPenyidikanDocument->document_number ?? '-' }}</td></tr>
                    <tr><td class="fw-bold">tanggal_sprindik</td>
                        <td>{{ $document->suratPerintahPenyidikanDocument->document_date
                            ? date('Y-m-d', strtotime($document->suratPerintahPenyidikanDocument->document_date))
                            : '-' }}</td></tr>
                    <tr><td class="fw-bold">uraian_singkat_perkara</td>
                        <td>{{ $document->description ?? '-' }}</td></tr>

                    {{-- daftar_laporan --}}
                    <tr>
                        <td class="fw-bold">daftar_laporan</td>
                        <td>
                            <table class="table table-sm mb-0">
                                <tr><td>nomor LP</td><td>{{ $accident->no_lp ?? '-' }}</td></tr>
                                <tr><td>tanggal</td><td>{{ $accident->report_date ? date('Y-m-d', strtotime($accident->report_date)) : '-' }}</td></tr>
                                <tr><td>kode satker penerbit</td><td>{{ $accident->polres->emp_id ?? '-' }}</td></tr>
                            </table>
                        </td>
                    </tr>

                    {{-- daftar_uu_pasal --}}
                    @php
                        $messages = $document->messages ?? [];
                        $pasals   = $messages['daftar_uu_pasal'] ?? [];
                        $lokasi   = $messages['lokasi_kejadian'] ?? $accident->accident_location ?? '-';
                        $kodeWil  = $messages['kode_wilayah'] ?? '-';
                        $waktu    = $messages['waktu_kejadian'] ?? '-';
                        $tahun    = $messages['tahun_kejadian'] ?? '-';
                        $bulan    = $messages['bulan_kejadian'] ?? '-';
                        $tanggal  = $messages['tanggal_kejadian'] ?? '-';
                    @endphp
                    <tr>
                        <td class="fw-bold">daftar_uu_pasal</td>
                        <td>
                            @if (!empty($pasals))
                                <ol class="mb-0">@foreach($pasals as $p)<li>{{ $p }}</li>@endforeach</ol>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>

                    {{-- daftar_kejadian_perkara --}}
                    <tr>
                        <td class="fw-bold">daftar_kejadian_perkara</td>
                        <td>
                            <table class="table table-sm mb-0">
                                <tr><td>lokasi</td><td>{{ $lokasi }}</td></tr>
                                <tr><td>kode_wilayah</td><td>{{ $kodeWil }}</td></tr>
                                <tr><td>waktu</td><td>{{ $waktu }}</td></tr>
                                <tr><td>tahun</td><td>{{ $tahun }}</td></tr>
                                <tr><td>bulan</td><td>{{ $bulan }}</td></tr>
                                <tr><td>tanggal</td><td>{{ $tanggal }}</td></tr>
                            </table>
                        </td>
                    </tr>

                    {{-- daftar_terlapor_atau_tersangka --}}
                    <tr>
                        <td class="fw-bold">daftar_terlapor_atau_tersangka</td>
                        <td>
                            @forelse($document->suspects as $s)
                                <div>• {{ $s->name }} ({{ $s->flag }})</div>
                            @empty
                                @forelse($document->reportedPersons as $rp)
                                    <div>• {{ $rp->name }} (Terlapor)</div>
                                @empty
                                    <span class="text-muted">-</span>
                                @endforelse
                            @endforelse
                        </td>
                    </tr>

                    {{-- pejabat_penandatangan --}}
                    <tr>
                        <td class="fw-bold">pejabat_penandatangan</td>
                        <td>
                            @php
                                $signatory = $document->suratPemberitahuanDimulainyaPenyidikanDocumentOfficers
                                    ->where('class', 'SIGNATORY')->first();
                            @endphp
                            @if($signatory)
                                <strong>nama:</strong> {{ $signatory->first_name . ' ' . $signatory->last_name }}<br>
                                <strong>nomor_induk:</strong> {{ $signatory->register_number }}<br>
                                <strong>jabatan:</strong> {{ $signatory->position->name ?? '-' }}<br>
                                <strong>pangkat:</strong> {{ $signatory->rank->name ?? '-' }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="text-center mt-4">
                <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"
                   class="btn btn-secondary">
                    ← Kembali
                </a>
            </div>

        </div>
    </div>
</body>
</html>
