<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Surat Perintah Penangguhan Penahanan (S-18) — SPPT-TI Pusiknas</title>
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
        .badge-s18 {
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
                <span class="fw-bold">DAERAH {{ strtoupper($accident->polres->polda->name ?? '') }}</span><br>
                <span class="fw-bold">RESOR {{ strtoupper($accident->polres->full_name ?? ($accident->polres->name ?? '')) }}</span><br>
                <span class="header-sub small">{{ ucwords($accident->polres->address ?? '') }}</span>
                <div class="my-3">
                    <span class="badge badge-s18">KODE DOKUMEN SPPT-TI: S-18 │ PROSES: DAT-3</span>
                </div>
                <h4 class="fw-bold mt-2"><u>SURAT PERINTAH PENANGGUHAN PENAHANAN</u></h4>
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
                                <td><span class="badge bg-primary">s18</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Nomor S-18 (nomor)</td>
                                <td class="fw-bold text-primary">{{ $document->nomor ?? $document->document_number ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Tanggal S-18 (tanggal)</td>
                                <td>{{ $document->tanggal ? \Carbon\Carbon::parse($document->tanggal)->isoFormat('D MMMM Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Nomor SPDP (nomor_spdp)</td>
                                <td>{{ $document->nomor_spdp ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Tanggal SPDP (tanggal_spdp)</td>
                                <td>{{ $document->tanggal_spdp ? \Carbon\Carbon::parse($document->tanggal_spdp)->isoFormat('D MMMM Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Kode Satker Penerbit SPDP</td>
                                <td><code>{{ $document->kode_satker_penerbit_spdp ?? '-' }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Nomor Surat Perintah Penahanan (S-17)</td>
                                <td>{{ $document->nomor_surat_perintah_penahanan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Tanggal Surat Permohonan Penangguhan</td>
                                <td>{{ $document->tanggal_surat_permohonan ? \Carbon\Carbon::parse($document->tanggal_surat_permohonan)->isoFormat('D MMMM Y') : '-' }}</td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Ketentuan Poin 9 & 10 (bila ada) --}}
                    <table class="table table-bordered table-sm mb-4">
                        <thead>
                            <tr class="table-section-title">
                                <th colspan="2"><i class="bi bi-check2-square me-1"></i> KETENTUAN PERPANJANGAN PENAHANAN (POIN 9 & 10 BILA ADA)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold" width="35%">Poin 9: Surat Perpanjangan Penahanan</td>
                                <td>
                                    @if ($document->has_surat_perpanjangan_penahanan && $document->nomor_surat_perpanjangan_penahanan)
                                        <span class="badge bg-success mb-1"><i class="bi bi-check-circle"></i> Ada</span><br>
                                        Nomor: <b>{{ $document->nomor_surat_perpanjangan_penahanan }}</b>, Tanggal: {{ $document->tanggal_surat_perpanjangan_penahanan ? \Carbon\Carbon::parse($document->tanggal_surat_perpanjangan_penahanan)->isoFormat('D MMMM Y') : '-' }}
                                    @else
                                        <span class="badge bg-secondary"><i class="bi bi-dash-circle"></i> Tidak Ada (Poin 9 dihilangkan dari Word)</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Poin 10: Surat Perintah Perpanjangan Penahanan</td>
                                <td>
                                    @if ($document->has_sprin_perpanjangan_penahanan && $document->nomor_sprin_perpanjangan_penahanan)
                                        <span class="badge bg-success mb-1"><i class="bi bi-check-circle"></i> Ada</span><br>
                                        Nomor: <b>{{ $document->nomor_sprin_perpanjangan_penahanan }}</b>, Tanggal: {{ $document->tanggal_sprin_perpanjangan_penahanan ? \Carbon\Carbon::parse($document->tanggal_sprin_perpanjangan_penahanan)->isoFormat('D MMMM Y') : '-' }}
                                    @else
                                        <span class="badge bg-secondary"><i class="bi bi-dash-circle"></i> Tidak Ada (Poin 10 dihilangkan dari Word)</span>
                                    @endif
                                </td>
                            </tr>
                            @php
                                $messages = is_array($document->messages) ? $document->messages : (json_decode($document->messages, true) ?? []);
                            @endphp
                            <tr>
                                <td class="fw-bold">Poin 12: SKET Uang Jaminan Penangguhan</td>
                                <td>
                                    @if (!empty($messages['nomor_sket_uang_jaminan']))
                                        <span class="badge bg-success mb-1"><i class="bi bi-check-circle"></i> Ada</span><br>
                                        Nomor: <b>{{ $messages['nomor_sket_uang_jaminan'] }}</b>, Tanggal: {{ !empty($messages['tanggal_sket_uang_jaminan']) ? \Carbon\Carbon::parse($messages['tanggal_sket_uang_jaminan'])->isoFormat('D MMMM Y') : '-' }}
                                    @else
                                        <span class="badge bg-secondary"><i class="bi bi-dash-circle"></i> Tidak Ada (Poin 12 dihilangkan dari Word)</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Poin 13: SKET Uang Tangguhan Penangguhan</td>
                                <td>
                                    @if (!empty($messages['nomor_sket_uang_tangguhan']))
                                        <span class="badge bg-success mb-1"><i class="bi bi-check-circle"></i> Ada</span><br>
                                        Nomor: <b>{{ $messages['nomor_sket_uang_tangguhan'] }}</b>, Tanggal: {{ !empty($messages['tanggal_sket_uang_tangguhan']) ? \Carbon\Carbon::parse($messages['tanggal_sket_uang_tangguhan'])->isoFormat('D MMMM Y') : '-' }}
                                    @else
                                        <span class="badge bg-secondary"><i class="bi bi-dash-circle"></i> Tidak Ada (Poin 13 dihilangkan dari Word)</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Tabel Jaminan Penangguhan --}}
                    <table class="table table-bordered table-sm mb-4">
                        <thead>
                            <tr class="table-section-title">
                                <th colspan="2"><i class="bi bi-shield-check me-1"></i> KONTEN DOKUMEN — JAMINAN (konten_dokumen)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold" width="35%">Jenis Jaminan (jenis_jaminan)</td>
                                <td>
                                    @if ($document->jenis_jaminan == 1)
                                        <span class="badge bg-success">1 - Jaminan Uang</span>
                                    @elseif ($document->jenis_jaminan == 2)
                                        <span class="badge bg-info">2 - Jaminan Orang</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak ada jaminan</span>
                                    @endif
                                </td>
                            </tr>
                            @if ($document->besaran_uang_jaminan)
                                <tr>
                                    <td class="fw-bold">Besaran Uang Jaminan / Tanggungan</td>
                                    <td class="fw-bold text-success">Rp {{ number_format($document->besaran_uang_jaminan, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            @if ($document->lokasi_penyimpanan_jaminan)
                                <tr>
                                    <td class="fw-bold">Lokasi Penyimpanan Jaminan</td>
                                    <td>{{ $document->lokasi_penyimpanan_jaminan }}</td>
                                </tr>
                            @endif
                            @if ($document->jenis_jaminan == 2 || $document->nama_penjamin)
                                <tr>
                                    <td class="fw-bold">Nomor Identitas Penjamin</td>
                                    <td>{{ $document->nomor_identitas_penjamin ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Nama Penjamin</td>
                                    <td><strong>{{ $document->nama_penjamin ?? '-' }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Alamat Penjamin</td>
                                    <td>{{ $document->alamat_penjamin ?? '-' }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                    {{-- Tersangka --}}
                    <table class="table table-bordered table-sm mb-4">
                        <thead>
                            <tr class="table-section-title">
                                <th colspan="7"><i class="bi bi-people me-1"></i> TERSANGKA YANG DITANGGUHKAN PENAHANANNYA (tersangka[])</th>
                            </tr>
                            <tr class="table-subsection text-center small">
                                <th>No</th>
                                <th>Nama Lengkap</th>
                                <th>NIK</th>
                                <th>TTL / Umur</th>
                                <th>Jenis Kelamin</th>
                                <th>Pekerjaan</th>
                                <th>Alamat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($document->suspects as $idx => $s)
                                @php
                                    $age = $s->age ?? ($s->birth_date ? \Carbon\Carbon::parse($s->birth_date)->age : '-');
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $idx + 1 }}</td>
                                    <td class="fw-bold">{{ $s->name }}</td>
                                    <td>{{ $s->identity_number ?? '-' }}</td>
                                    <td>{{ $s->birth_place ?? '-' }}, {{ $s->birth_date ? date('d-m-Y', strtotime($s->birth_date)) : '-' }} ({{ $age }} thn)</td>
                                    <td>{{ $s->gender->name ?? '-' }}</td>
                                    <td>{{ $s->job->name ?? '-' }}</td>
                                    <td>{{ $s->address ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted fst-italic">Belum ada data tersangka</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Petugas yang Diperintahkan --}}
                    @php
                        $leader = $document->leaderOfficer->first() ?? $document->suratPerintahPenangguhanPenahananDocumentOfficers->where('class', 'LEADER')->first();
                        $members = $document->memberOfficers;
                    @endphp
                    <table class="table table-bordered table-sm mb-4">
                        <thead>
                            <tr class="table-section-title">
                                <th colspan="5"><i class="bi bi-people-fill me-1"></i> PETUGAS YANG DIPERINTAHKAN</th>
                            </tr>
                            <tr class="table-subsection text-center small">
                                <th width="15%">Peran</th>
                                <th>Nama Lengkap</th>
                                <th>Pangkat / NRP</th>
                                <th>Jabatan</th>
                                <th>Kesatuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($leader)
                                <tr class="table-info">
                                    <td class="text-center fw-bold"><span class="badge bg-primary">Ketua Tim</span></td>
                                    <td class="fw-bold">{{ \App\Helpers\PeopleNameHelper::getFullName($leader->first_title, $leader->first_name, $leader->last_name, $leader->last_title) }}</td>
                                    <td>{{ ($leader->rank->name ?? $leader->rank_id ?? '-') . ' / ' . ($leader->register_number ?? '-') }}</td>
                                    <td>{{ $leader->position->name ?? ($leader->position_id ?? '-') }}</td>
                                    <td>{{ $leader->police->name ?? '-' }}</td>
                                </tr>
                            @endif
                            @forelse ($members as $m)
                                <tr>
                                    <td class="text-center"><span class="badge bg-secondary">Anggota</span></td>
                                    <td class="fw-bold">{{ \App\Helpers\PeopleNameHelper::getFullName($m->first_title, $m->first_name, $m->last_name, $m->last_title) }}</td>
                                    <td>{{ ($m->rank->name ?? $m->rank_id ?? '-') . ' / ' . ($m->register_number ?? '-') }}</td>
                                    <td>{{ $m->position->name ?? ($m->position_id ?? '-') }}</td>
                                    <td>{{ $m->police->name ?? '-' }}</td>
                                </tr>
                            @empty
                                @if (!$leader)
                                    <tr>
                                        <td colspan="5" class="text-center text-muted fst-italic">Belum ada petugas yang dipilih</td>
                                    </tr>
                                @endif
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Pejabat Penandatangan --}}
                    @php
                        $signatory = $document->suratPerintahPenangguhanPenahananDocumentOfficers->where('class', 'SIGNATORY')->first();
                    @endphp
                    @if ($signatory)
                        <table class="table table-bordered table-sm mb-4">
                            <thead>
                                <tr class="table-section-title">
                                    <th colspan="2"><i class="bi bi-pen me-1"></i> PEJABAT PENANDATANGAN (pejabat_penandatangan[])</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-bold" width="35%">Nama Pejabat</td>
                                    <td><strong>{{ \App\Helpers\PeopleNameHelper::getFullName($signatory->first_title, $signatory->first_name, $signatory->last_name, $signatory->last_title) }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">NRP / NIP (nomor_induk)</td>
                                    <td>{{ $signatory->register_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Pangkat (pangkat)</td>
                                    <td>{{ $signatory->rank->name ?? ($signatory->rank_id ?? '-') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Jabatan (jabatan)</td>
                                    <td>{{ $signatory->position->name ?? ($signatory->position_id ?? '-') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    @endif

                </div>

                {{-- TAB 2: FORMAT JSON SPPT-TI --}}
                <div class="tab-pane fade" id="jsonView" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-muted">Struktur Payload JSON Pertukaran Data SPPT-TI (S-18):</span>
                        <div>
                            <button class="btn btn-sm btn-outline-secondary me-2" onclick="copyJson()">
                                <i class="bi bi-clipboard"></i> Copy JSON
                            </button>
                            <a href="{{ route('doc.surat-perintah-penangguhan-penahanan-document.show', ['id' => $document->id, 'accident_id' => $accident->id, 'json' => 1]) }}"
                               target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-box-arrow-up-right"></i> Buka Raw JSON
                            </a>
                        </div>
                    </div>
                    <pre class="json-view" id="jsonText">{{ json_encode($jsonPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>

            </div>

            {{-- Action Buttons --}}
            <div class="d-flex justify-content-between align-items-center pt-3 mt-4 border-top">
                <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accident->id]) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <div>
                    <a href="{{ route('doc.surat-perintah-penangguhan-penahanan-document.download', ['id' => $document->id, 'accident_id' => $accident->id]) }}"
                       class="btn btn-success me-2">
                        <i class="bi bi-download"></i> Unduh Word (.docx)
                    </a>
                    @if (in_array($document->status_id, ['1', '2', '4', 1, 2, 4]))
                        <a href="{{ route('doc.surat-perintah-penangguhan-penahanan-document.edit', ['id' => $document->id, 'accident_id' => $accident->id]) }}"
                           class="btn btn-primary">
                            <i class="bi bi-pencil-square"></i> Ubah Dokumen
                        </a>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyJson() {
            var text = document.getElementById('jsonText').innerText;
            navigator.clipboard.writeText(text).then(function() {
                alert('JSON berhasil disalin ke clipboard!');
            });
        }
    </script>
</body>
</html>
