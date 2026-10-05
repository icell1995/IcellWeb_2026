<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Surat Permohonan Perpanjangan Penahanan Kejaksaan — SPPT-TI Pusiknas</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .doc-container {
            max-width: 960px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 40px;
        }
        .header-logo {
            height: 70px;
            margin-bottom: 10px;
        }
        .header-title {
            font-weight: 700;
            color: #1e293b;
        }
        .header-sub {
            font-size: 0.95rem;
            color: #475569;
        }
        .table-section-title {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.95rem;
        }
        .badge-spp {
            background-color: #0284c7;
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
        }
        pre.json-view {
            background-color: #1e293b;
            color: #38bdf8;
            padding: 20px;
            border-radius: 8px;
            font-family: "Courier New", Courier, monospace;
            font-size: 0.85rem;
            max-height: 480px;
            overflow-y: auto;
        }
        .nav-tabs .nav-link {
            font-weight: 600;
            color: #64748b;
        }
        .nav-tabs .nav-link.active {
            color: #1e3a8a;
            border-bottom: 3px solid #1e3a8a;
        }
    </style>
</head>
<body>

    <div class="container py-4">
        <div class="doc-container">

            {{-- Kop Surat Resmi Polri --}}
            <div class="text-center mb-4 pb-3 border-bottom">
                <img src="{{ asset('images/logo.png') }}" class="header-logo" alt="Logo Polri"><br>
                <strong class="header-title fs-5">KEPOLISIAN NEGARA REPUBLIK INDONESIA</strong><br>
                <span class="fw-bold">DAERAH {{ strtoupper($accident->polres->polda->name ?? 'JAWA TIMUR') }}</span><br>
                <span class="fw-bold">RESOR {{ strtoupper($accident->polres->full_name ?? ($accident->polres->name ?? 'PASURUAN')) }}</span><br>
                <span class="header-sub small">{{ ucwords($accident->polres->address ?? '') }}</span>
                <div class="my-3">
                    <span class="badge badge-spp">KODE DOKUMEN: S-21 │ PERMOHONAN PERPANJANGAN PENAHANAN KEJAKSAAN</span>
                </div>
                <h4 class="fw-bold mt-2"><u>SURAT PERMOHONAN PERPANJANGAN PENAHANAN KEJAKSAAN</u></h4>
                <h5 class="text-muted">NOMOR: {{ $document->nomor ?? $document->document_number ?? '-' }}</h5>
            </div>

            {{-- Navigasi Tab: Preview Dokumen vs JSON Payload --}}
            <ul class="nav nav-tabs mb-4" id="docTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="preview-tab" data-bs-toggle="tab" data-bs-target="#preview" type="button" role="tab" aria-selected="true">
                        <i class="bi bi-file-text me-1"></i> Preview Dokumen
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="json-tab" data-bs-toggle="tab" data-bs-target="#jsonView" type="button" role="tab" aria-selected="false">
                        <i class="bi bi-code-slash me-1"></i> Format JSON (SPPT-TI)
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="docTabsContent">
                
                {{-- TAB 1: PREVIEW DOKUMEN --}}
                <div class="tab-pane fade show active" id="preview" role="tabpanel">

                    {{-- Tabel Identitas Dokumen --}}
                    <table class="table table-bordered table-sm mb-4">
                        <thead>
                            <tr class="table-section-title">
                                <th colspan="2"><i class="bi bi-info-circle me-1"></i> 1. IDENTITAS DOKUMEN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold" width="35%">Nomor LP</td>
                                <td>{{ $accident->no_lp ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Nomor Surat Permohonan</td>
                                <td><span class="fw-bold text-primary">{{ $document->nomor ?? $document->document_number ?? '-' }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Tanggal Surat</td>
                                <td>{{ $document->tanggal ? \Carbon\Carbon::parse($document->tanggal)->locale('id')->translatedFormat('d F Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Klasifikasi / Lampiran</td>
                                <td>{{ $document->klasifikasi ?? 'BIASA' }} │ {{ $document->lampiran ?? '1 (satu) Berkas' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Tempat Terbit</td>
                                <td>{{ $document->tempat_surat ?? ($accident->polres->name ?? 'Pasuruan') }}</td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Tabel Tujuan Kejaksaan --}}
                    <table class="table table-bordered table-sm mb-4">
                        <thead>
                            <tr class="table-section-title">
                                <th colspan="2"><i class="bi bi-bank me-1"></i> 2. TUJUAN KEJAKSAAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold" width="35%">Nama Kejaksaan Penerima</td>
                                <td><strong>Kepada Yth. KEPALA {{ $document->nama_kejaksaan ?? ($document->prosecutor->name ?? '-') }}</strong></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Lokasi / Tempat (di ...)</td>
                                <td>di {{ $document->lokasi_kejaksaan ?? ($document->prosecutor->address ?? '-') }}</td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Tabel Rujukan Poin 1 --}}
                    <table class="table table-bordered table-sm mb-4">
                        <thead>
                            <tr class="table-section-title">
                                <th colspan="2"><i class="bi bi-journal-text me-1"></i> 3. RUJUKAN DOKUMEN TERKAIT (POIN 1)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold" width="35%">Surat Perintah Penyidikan (Sprindik)</td>
                                <td>{{ $document->nomor_sprindik ?? '-' }} (Tanggal: {{ $document->tanggal_sprindik ? \Carbon\Carbon::parse($document->tanggal_sprindik)->locale('id')->translatedFormat('d F Y') : '-' }})</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">SPDP</td>
                                <td>{{ $document->nomor_spdp ?? '-' }} (Tanggal: {{ $document->tanggal_spdp ? \Carbon\Carbon::parse($document->tanggal_spdp)->locale('id')->translatedFormat('d F Y') : '-' }})</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">SKET Penetapan Tersangka</td>
                                <td>{{ $document->nomor_penetapan_tersangka ?? '-' }} (Tanggal: {{ $document->tanggal_penetapan_tersangka ? \Carbon\Carbon::parse($document->tanggal_penetapan_tersangka)->locale('id')->translatedFormat('d F Y') : '-' }})</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Surat Perintah Penahanan (S-17)</td>
                                <td>{{ $document->nomor_surat_perintah_penahanan ?? '-' }} (Tanggal: {{ $document->tanggal_surat_perintah_penahanan ? \Carbon\Carbon::parse($document->tanggal_surat_perintah_penahanan)->locale('id')->translatedFormat('d F Y') : '-' }})</td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Tabel Uraian Perkara Poin 2 --}}
                    <table class="table table-bordered table-sm mb-4">
                        <thead>
                            <tr class="table-section-title">
                                <th colspan="2"><i class="bi bi-file-earmark-ruled me-1"></i> 4. URAIAN PERKARA (POIN 2)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold" width="35%">Satker Penyidik</td>
                                <td>{{ $document->satker_penyidik ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Dugaan Tindak Pidana</td>
                                <td>{{ $document->dugaan_tindak_pidana ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Pasal yang Disangkakan</td>
                                <td>{{ $document->pasal_diduga ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Tempat Kejadian Perkara (TKP)</td>
                                <td>{{ $document->tempat_kejadian ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Kurun Waktu Kejadian</td>
                                <td>{{ $document->kurun_waktu ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Tabel Perpanjangan Penahanan Poin 3 --}}
                    <table class="table table-bordered table-sm mb-4">
                        <thead>
                            <tr class="table-section-title">
                                <th colspan="2"><i class="bi bi-clock-history me-1"></i> 5. MASA PERPANJANGAN PENAHANAN (POIN 3)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold" width="35%">Akhir Penahanan Penyidik (20 Hari)</td>
                                <td>{{ $document->tanggal_akhir_penahanan_lama ? \Carbon\Carbon::parse($document->tanggal_akhir_penahanan_lama)->locale('id')->translatedFormat('d F Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Tempat Penahanan / Rutan</td>
                                <td>{{ $document->nama_rutan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Masa Perpanjangan</td>
                                <td>
                                    <strong>40 (empat puluh) hari</strong>
                                    (Dari {{ $document->tanggal_mulai_perpanjangan ? \Carbon\Carbon::parse($document->tanggal_mulai_perpanjangan)->locale('id')->translatedFormat('d F Y') : '-' }}
                                    s.d. {{ $document->tanggal_akhir_perpanjangan ? \Carbon\Carbon::parse($document->tanggal_akhir_perpanjangan)->locale('id')->translatedFormat('d F Y') : '-' }})
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Penyidik Penghubung (Kontak)</td>
                                <td>
                                    {{ $document->contact_officer_name ?? '-' }}
                                    @if($document->contact_officer_phone)
                                        <span class="badge bg-secondary ms-2"><i class="bi bi-telephone me-1"></i>{{ $document->contact_officer_phone }}</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Tabel Data Tersangka --}}
                    <table class="table table-bordered table-sm mb-4">
                        <thead>
                            <tr class="table-section-title">
                                <th colspan="4"><i class="bi bi-people me-1"></i> 6. DATA TERSANGKA</th>
                            </tr>
                            <tr class="table-light">
                                <th width="5%">No.</th>
                                <th width="35%">Nama Lengkap</th>
                                <th width="30%">No. Identitas</th>
                                <th width="30%">TTL / Pekerjaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($document->suspects as $idx => $suspect)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="fw-bold">{{ strtoupper($suspect->name) }}</td>
                                    <td>{{ $suspect->identity_number ?? '-' }}</td>
                                    <td>
                                        {{ $suspect->birth_place ?? '-' }}, {{ $suspect->birth_date ? \Carbon\Carbon::parse($suspect->birth_date)->locale('id')->translatedFormat('d F Y') : '-' }}<br>
                                        <small class="text-muted">{{ $suspect->job->name ?? '-' }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-2">Tidak ada data tersangka.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Tabel Tembusan --}}
                    <table class="table table-bordered table-sm mb-4">
                        <thead>
                            <tr class="table-section-title">
                                <th colspan="2"><i class="bi bi-send me-1"></i> 7. TEMBUSAN SURAT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $cc = $document->carbon_copies ?? [];
                            @endphp
                            @if(!empty($cc) && count($cc) > 0)
                                @foreach($cc as $index => $c)
                                    <tr>
                                        <td width="5%" class="text-center">{{ $loop->iteration }}.</td>
                                        <td>{{ $c }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="2" class="text-muted text-center py-2">Tidak ada tembusan surat.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                    {{-- Tabel Pejabat Penandatangan --}}
                    <table class="table table-bordered table-sm mb-4">
                        <thead>
                            <tr class="table-section-title">
                                <th colspan="2"><i class="bi bi-pen me-1"></i> 8. PEJABAT PENANDATANGAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold" width="35%">Teks Header</td>
                                <td>{{ $document->signatory_head_text ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Jabatan</td>
                                <td>{{ $document->signatory_position ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Nama Pejabat</td>
                                <td><strong>{{ $document->signatory_name ?? '-' }}</strong></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Pangkat / NRP</td>
                                <td>{{ $document->signatory_rank ?? '-' }} NRP {{ $document->signatory_nrp ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>

                </div>

                {{-- TAB 2: FORMAT JSON SPPT-TI --}}
                <div class="tab-pane fade" id="jsonView" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">JSON Payload untuk Integrasi SPPT-TI Pusiknas Bareskrim:</span>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="copyJson()">
                                <i class="bi bi-clipboard me-1"></i> Salin JSON
                            </button>
                            <a href="{{ route('doc.surat-permohonan-perpanjangan-penahanan-kejaksaan-document.show', ['id' => $document->id, 'accident_id' => $accident->id, 'json' => 1]) }}"
                               target="_blank" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Buka Raw JSON
                            </a>
                        </div>
                    </div>
                    <pre class="json-view"><code id="jsonContent">{{ json_encode($jsonPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>

            </div>

            {{-- Tombol Aksi Bawah --}}
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accident->id]) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Progress Perkara
                </a>

                <div class="d-flex gap-2">
                    <a href="{{ route('doc.surat-permohonan-perpanjangan-penahanan-kejaksaan-document.download', ['id' => $document->id, 'accident_id' => $accident->id]) }}"
                       class="btn btn-success me-2">
                        <i class="bi bi-file-earmark-word me-1"></i> Unduh Word
                    </a>

                    <a href="{{ route('doc.surat-permohonan-perpanjangan-penahanan-kejaksaan-document.edit', ['id' => $document->id, 'accident_id' => $accident->id]) }}"
                       class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i> Edit Dokumen
                    </a>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyJson() {
            var text = document.getElementById('jsonContent').innerText;
            navigator.clipboard.writeText(text).then(function() {
                alert('JSON berhasil disalin ke clipboard!');
            });
        }
    </script>
</body>
</html>
