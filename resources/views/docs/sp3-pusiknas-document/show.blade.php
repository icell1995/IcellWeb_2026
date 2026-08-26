<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>SP3 — Pusiknas Bareskrim</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex justify-content-center" style="background-color:#eeeeee; padding:20px 0;">
        <div class="radius-card mt-4" style="background:#fff; padding:30px; border-radius:8px; max-width:900px; width:100%;">

            {{-- Kop Surat --}}
            <div class="text-center mb-4">
                <img src="{{ asset('images/logo.png') }}" style="height:60px;" alt="Logo Polri"><br>
                <strong>KEPOLISIAN NEGARA REPUBLIK INDONESIA</strong><br>
                DAERAH {{ strtoupper($accident->polres->polda->full_name ?? '') }}<br>
                RESOR {{ strtoupper($accident->polres->full_name ?? '') }}<br>
                <span class="text-muted small">{{ ucwords($accident->polres->address ?? '') }}</span>
                <hr>
                <h4><u>SURAT PEMBERITAHUAN PENGHENTIAN PENYIDIKAN</u></h4>
                <h5>NOMOR: {{ $sp3->no_sp3 }}</h5>
                <span class="badge badge-warning text-dark">KODE PROSES SPPT-TI: DIK-40</span>
            </div>

            {{-- identitas_dokumen --}}
            <table class="table table-bordered table-sm mb-4">
                <thead class="table-secondary"><tr><th colspan="2">identitas_dokumen</th></tr></thead>
                <tbody>
                    <tr><td class="fw-bold" width="40%">kode_jenis_dokumen</td><td><code>sp3</code></td></tr>
                    <tr><td class="fw-bold">nomor</td><td>{{ $sp3->no_sp3 }}</td></tr>
                    <tr><td class="fw-bold">tanggal</td>
                        <td>{{ $sp3->tanggal_berlaku ? date('Y-m-d', strtotime($sp3->tanggal_berlaku)) : '-' }}</td></tr>
                    <tr><td class="fw-bold">nomor_spdp</td><td>{{ $sp3->no_spdp ?? '-' }}</td></tr>
                </tbody>
            </table>

            {{-- konten_dokumen --}}
            <table class="table table-bordered table-sm mb-4">
                <thead class="table-info"><tr><th colspan="2">konten_dokumen</th></tr></thead>
                <tbody>
                    {{-- kode_alasan --}}
                    <tr>
                        <td class="fw-bold" width="40%">kode_alasan</td>
                        <td>
                            @if (!empty($kodeAlasan))
                                <ol class="mb-0">
                                    @foreach($kodeAlasan as $kode)
                                        <li>
                                            <strong>Kode {{ $kode }}:</strong>
                                            {{ $masterAlasan[$kode] ?? 'Tidak Diketahui' }}
                                        </li>
                                    @endforeach
                                </ol>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>

                    {{-- pejabat_penandatangan --}}
                    <tr>
                        <td class="fw-bold">pejabat_penandatangan</td>
                        <td>
                            @php
                                $signatoryId = $extraData['signatory_id'] ?? null;
                                $signatoryOfficer = $signatoryId
                                    ? \App\Models\Officer::with(['rank', 'position'])->find($signatoryId)
                                    : null;
                            @endphp
                            @if ($signatoryOfficer)
                                <strong>nama:</strong> {{ $signatoryOfficer->first_name . ' ' . $signatoryOfficer->last_name }}<br>
                                <strong>nomor_induk:</strong> {{ $signatoryOfficer->register_number }}<br>
                                <strong>jabatan:</strong> {{ $signatoryOfficer->position->name ?? '-' }}<br>
                                <strong>pangkat:</strong> {{ $signatoryOfficer->rank->name ?? '-' }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>

                    {{-- daftar_terlapor_atau_tersangka --}}
                    <tr>
                        <td class="fw-bold">daftar_terlapor_atau_tersangka</td>
                        <td>
                            @php
                                $suspectIds = $extraData['suspect_ids'] ?? [];
                                $suspectList = $suspectIds
                                    ? \App\Models\Suspect::whereIn('id', $suspectIds)->get()
                                    : collect();
                            @endphp
                            @forelse ($suspectList as $s)
                                <div>• {{ $s->name }}</div>
                            @empty
                                <span class="text-muted">-</span>
                            @endforelse
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="text-center mt-4">
                <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"
                   class="btn btn-secondary">← Kembali</a>
            </div>

        </div>
    </div>
</body>
</html>
