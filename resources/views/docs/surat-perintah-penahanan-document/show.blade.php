<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Surat Perintah Penahanan (S-17) — SPPT-TI Pusiknas</title>
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
        .table-subsection {
            background-color: #f1f5f9;
            font-weight: 600;
        }
        .badge-s17 {
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
                <span class="fw-bold">DAERAH {{ strtoupper($accident->polres->polda->full_name ?? '') }}</span><br>
                <span class="fw-bold">RESOR {{ strtoupper($accident->polres->full_name ?? '') }}</span><br>
                <span class="header-sub small">{{ ucwords($accident->polres->address ?? '') }}</span>
                <div class="my-3">
                    <span class="badge badge-s17">KODE DOKUMEN SPPT-TI: S-17</span>
                </div>
                <h4 class="fw-bold mt-2"><u>SURAT PERINTAH PENAHANAN</u></h4>
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
                                <th colspan="2"><i class="bi bi-info-circle me-1"></i> IDENTITAS DOKUMEN (identitas_dokumen)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold" width="35%">kode_jenis_dokumen</td>
                                <td><code>s17</code> (Surat Perintah Penahanan)</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">nomor</td>
                                <td><span class="badge bg-light text-dark border">{{ $document->nomor ?? $document->document_number ?? '-' }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">tanggal</td>
                                <td>{{ $document->tanggal ? date('d-m-Y', strtotime($document->tanggal)) : ($document->document_date ? date('d-m-Y', strtotime($document->document_date)) : '-') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">nomor_spdp</td>
                                <td>{{ $document->nomor_spdp ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">tanggal_spdp</td>
                                <td>{{ $document->tanggal_spdp ? date('d-m-Y', strtotime($document->tanggal_spdp)) : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">kode_satker_penerbit_spdp</td>
                                <td><code>{{ $document->kode_satker_penerbit_spdp ?? '-' }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">nomor_surat_perintah_penangkapan</td>
                                <td>{{ $document->nomor_surat_perintah_penangkapan ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Tabel Konten Dokumen --}}
                    <table class="table table-bordered table-sm mb-4">
                        <thead>
                            <tr class="table-section-title">
                                <th colspan="2"><i class="bi bi-card-checklist me-1"></i> KONTEN DOKUMEN (konten_dokumen)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $jenisPenahananMap = [1 => 'RUTAN', 2 => 'RUMAH', 3 => 'KOTA'];
                                $jenisPenahananText = $jenisPenahananMap[$document->kode_jenis_penahanan] ?? 'RUTAN';
                            @endphp
                            <tr>
                                <td class="fw-bold" width="35%">kode_jenis_penahanan</td>
                                <td>
                                    <span class="badge bg-primary">{{ $document->kode_jenis_penahanan }} ({{ $jenisPenahananText }})</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">kode_satker_tempat_penahanan</td>
                                <td><code>{{ $document->kode_satker_tempat_penahanan ?? '-' }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">keterangan_tempat_penahanan</td>
                                <td>{{ $document->lokasi_penahanan ?? ($document->messages['tempat_penahanan_nama'] ?? '-') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">tanggal_mulai</td>
                                <td>{{ $document->tanggal_mulai ? date('d-m-Y', strtotime($document->tanggal_mulai)) : ($document->start_date ? date('d-m-Y', strtotime($document->start_date)) : '-') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">tanggal_akhir</td>
                                <td>
                                    {{ $document->tanggal_akhir ? date('d-m-Y', strtotime($document->tanggal_akhir)) : ($document->end_date ? date('d-m-Y', strtotime($document->end_date)) : '-') }}
                                    @if($document->tanggal_mulai && $document->tanggal_akhir)
                                        @php
                                            $tglM = \Carbon\Carbon::parse($document->tanggal_mulai);
                                            $tglA = \Carbon\Carbon::parse($document->tanggal_akhir);
                                            $durasi = $tglM->diffInDays($tglA) + 1;
                                        @endphp
                                        <span class="badge bg-secondary ms-2">Durasi: {{ $durasi }} Hari</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Daftar Tersangka --}}
                    <div class="card mb-4 border">
                        <div class="card-header bg-light fw-bold">
                            <i class="bi bi-people me-1"></i> DAFTAR TERSANGKA YANG DITAHAN (tersangka)
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm mb-0">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th>Nama</th>
                                            <th>NIK</th>
                                            <th>TTL / Umur</th>
                                            <th>L/P</th>
                                            <th>Pekerjaan</th>
                                            <th>Alamat</th>
                                            <th>Pasal Disangkakan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($document->suspects as $s)
                                            @php
                                                $umur = $s->age ?? ($s->birth_date ? \Carbon\Carbon::parse($s->birth_date)->age : '-');
                                                $pasals = $document->messages['daftar_uu_pasal'] ?? ['Pasal 310 ayat (4) UU RI No. 22 Tahun 2009'];
                                            @endphp
                                            <tr>
                                                <td class="fw-bold">{{ $s->name }}</td>
                                                <td>{{ $s->identity_number ?? '-' }}</td>
                                                <td>{{ $s->birth_place ?? '-' }}, {{ $s->birth_date ? date('d-m-Y', strtotime($s->birth_date)) : '-' }} ({{ $umur }} thn)</td>
                                                <td>{{ $s->gender->name ?? '-' }}</td>
                                                <td>{{ $s->job->name ?? '-' }}</td>
                                                <td>{{ $s->address ?? '-' }}</td>
                                                <td>
                                                    @if(is_array($pasals))
                                                        <ul class="mb-0 ps-3">
                                                            @foreach($pasals as $p)<li>{{ $p }}</li>@endforeach
                                                        </ul>
                                                    @else
                                                        {{ $pasals }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-3">Tidak ada data tersangka yang terhubung.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Petugas yang Diperintahkan --}}
                    <div class="card mb-4 border">
                        <div class="card-header bg-light fw-bold">
                            <i class="bi bi-person-badge me-1"></i> PETUGAS YANG DIPERINTAHKAN
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm mb-0">
                                    <thead class="table-secondary">
                                        <tr class="text-center">
                                            <th>Nama</th>
                                            <th>Pangkat</th>
                                            <th>NRP</th>
                                            <th>Jabatan</th>
                                            <th>Kesatuan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $members = $document->suratPerintahPenahananDocumentOfficers->where('class', 'MEMBER');
                                        @endphp
                                        @forelse($members as $m)
                                            @php
                                                $fullName = \App\Helpers\PeopleNameHelper::getFullName($m->first_title, $m->first_name, $m->last_name, $m->last_title);
                                            @endphp
                                            <tr class="text-center">
                                                <td class="fw-bold">{{ $fullName }}</td>
                                                <td>{{ $m->rank->name ?? '-' }}</td>
                                                <td>{{ $m->register_number ?? '-' }}</td>
                                                <td>{{ $m->position->name ?? '-' }}</td>
                                                <td>{{ $m->police->name ?? ($accident->polres->full_name ?? '-') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-2">Tidak ada petugas yang ditugaskan.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Pejabat Penandatangan --}}
                    <table class="table table-bordered table-sm mb-4">
                        <thead>
                            <tr class="table-section-title">
                                <th colspan="2"><i class="bi bi-pen me-1"></i> PEJABAT PENANDATANGAN (pejabat_penandatangan)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $signatory = $document->suratPerintahPenahananDocumentOfficers->where('class', 'SIGNATORY')->first();
                            @endphp
                            @if($signatory)
                                <tr>
                                    <td class="fw-bold" width="35%">nama</td>
                                    <td>{{ \App\Helpers\PeopleNameHelper::getFullName($signatory->first_title, $signatory->first_name, $signatory->last_name, $signatory->last_title) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">nomor_induk (NRP)</td>
                                    <td>{{ $signatory->register_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">pangkat</td>
                                    <td>{{ $signatory->rank->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">jabatan</td>
                                    <td>{{ $signatory->position->name ?? '-' }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="2" class="text-muted text-center py-2">Belum ada pejabat penandatangan.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                </div>

                {{-- TAB 2: FORMAT JSON SPPT-TI --}}
                <div class="tab-pane fade" id="jsonView" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">JSON Payload S-17 untuk Integrasi SPPT-TI Pusiknas Bareskrim:</span>
                        <a href="{{ route('doc.surat-perintah-penahanan-document.show', ['id' => $document->id, 'accident_id' => $accident->id, 'json' => 1]) }}"
                           target="_blank" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Buka Raw JSON
                        </a>
                    </div>
                    <pre class="json-view"><code>{{ json_encode($jsonPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>

            </div>

            {{-- Tombol Aksi Bawah --}}
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accident->id]) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Progress Perkara
                </a>

                <div class="d-flex gap-2">
                    <a href="{{ route('doc.surat-perintah-penahanan-document.download', ['id' => $document->id, 'accident_id' => $accident->id]) }}"
                       class="btn btn-success me-2">
                        <i class="bi bi-file-earmark-word me-1"></i> Unduh Word (S-17)
                    </a>

                    @if(in_array($document->status_id, ['2', '4']))
                        <a href="{{ route('doc.surat-perintah-penahanan-document.edit', ['id' => $document->id, 'accident_id' => $accident->id]) }}"
                           class="btn btn-warning">
                            <i class="bi bi-pencil me-1"></i> Edit Dokumen
                        </a>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
