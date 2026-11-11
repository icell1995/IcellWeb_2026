<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Perintah Penghentian Penyelidikan</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <div class="d-flex justify-content-center" style="background-color:#eeeeee; padding:20px 0;">
        <div class="radius-card mt-4" style="background:#fff; padding:30px; border-radius:8px; max-width:900px; width:100%;">

            {{-- Kop Surat --}}
            <div class="text-center mb-4">
                <img src="{{ asset('images/logo.png') }}" style="height:60px;" alt="Logo Polri"><br>
                <strong>KEPOLISIAN NEGARA REPUBLIK INDONESIA</strong><br>
                DAERAH {{ strtoupper($sprintHenti->accident->polres->polda->full_name ?? '') }}<br>
                RESOR {{ strtoupper($sprintHenti->accident->polres->full_name ?? '') }}<br>
                <span class="text-muted small">{{ ucwords($sprintHenti->accident->polres->address ?? '') }}</span>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold">KOPSTUK</span>
                    <span class="badge bg-secondary fs-6">A.8</span>
                </div>
                <h5 class="fw-bold mt-2">“PRO JUSTITIA”</h5>
                <h4><u>SURAT PERINTAH PENGHENTIAN PENYELIDIKAN</u></h4>
                <h5>NOMOR: {{ $sprintHenti->document_number }}</h5>
            </div>

            {{-- Identitas Dokumen & Dasar --}}
            <table class="table table-bordered table-sm mb-4">
                <thead class="table-secondary">
                    <tr><th colspan="2">Identitas Dokumen & Rujukan</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold" width="35%">Nomor Surat Perintah</td>
                        <td>{{ $sprintHenti->document_number }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Tanggal Surat Perintah</td>
                        <td>{{ $sprintHenti->document_date ? \Carbon\Carbon::parse($sprintHenti->document_date)->locale('id')->isoFormat('D MMMM Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Nomor Laporan Polisi</td>
                        <td>{{ $sprintHenti->accident->no_lp }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Dikeluarkan di</td>
                        <td>{{ $sprintHenti->document_location ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Dasar Sprinlidik Terdahulu</td>
                        <td>
                            @if ($sprintHenti->suratPerintahPenyelidikanDocument)
                                {{ $sprintHenti->suratPerintahPenyelidikanDocument->document_number }} 
                                ({{ $sprintHenti->suratPerintahPenyelidikanDocument->document_date ? date('d/m/Y', strtotime($sprintHenti->suratPerintahPenyelidikanDocument->document_date)) : '-' }})
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Dasar Laporan Hasil Gelar Perkara</td>
                        <td>
                            @if ($sprintHenti->laporanHasilGelarPerkaraDocument)
                                {{ $sprintHenti->laporanHasilGelarPerkaraDocument->document_number ?? 'LHGP' }} 
                                ({{ $sprintHenti->laporanHasilGelarPerkaraDocument->document_date ? date('d/m/Y', strtotime($sprintHenti->laporanHasilGelarPerkaraDocument->document_date)) : '-' }})
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- Alasan Penghentian --}}
            @php
                $kodeAlasan = is_array($sprintHenti->kode_alasan) ? $sprintHenti->kode_alasan : (json_decode($sprintHenti->kode_alasan, true) ?? []);
            @endphp
            <table class="table table-bordered table-sm mb-4">
                <thead class="table-info">
                    <tr><th colspan="2">Alasan Penghentian Penyelidikan</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold" width="35%">Kode & Alasan</td>
                        <td>
                            @if (!empty($kodeAlasan))
                                <ol class="mb-0 ps-3">
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
                    @if ($sprintHenti->alasan_penghentian)
                    <tr>
                        <td class="fw-bold">Uraian Alasan</td>
                        <td>{{ $sprintHenti->alasan_penghentian }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>

            {{-- Terlapor / Pihak Terkait --}}
            <table class="table table-bordered table-sm mb-4">
                <thead class="table-secondary">
                    <tr><th colspan="4">Pihak Terkait / Terlapor</th></tr>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>Alamat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sprintHenti->suspects as $idx => $suspect)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>{{ $suspect->name }}</td>
                            <td>{{ $suspect->identity_number ?? ($suspect->nik ?? '-') }}</td>
                            <td>{{ $suspect->address ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Belum ada pihak terlapor yang dipilih</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pejabat & Penyelidik --}}
            @php
                $signatory = $sprintHenti->suratPerintahPenghentianPenyelidikanDocumentOfficers->firstWhere('class', 'SIGNATORY');
                $members   = $sprintHenti->suratPerintahPenghentianPenyelidikanDocumentOfficers->where('class', 'MEMBER');
            @endphp
            <table class="table table-bordered table-sm mb-4">
                <thead class="table-secondary">
                    <tr><th colspan="2">Pejabat & Penyelidik</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold" width="35%">Pemberi Perintah (Selaku Penyidik)</td>
                        <td>
                            @if ($signatory)
                                {{ $signatory->first_title ? $signatory->first_title . ' ' : '' }}{{ $signatory->first_name }} {{ $signatory->last_name }}{{ $signatory->last_title ? ', ' . $signatory->last_title : '' }}
                                (NRP: {{ $signatory->register_number }})
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Penyelidik Penerima Perintah</td>
                        <td>
                            @if ($members->count() > 0)
                                <ol class="mb-0 ps-3">
                                    @foreach($members as $mem)
                                        <li>
                                            {{ $mem->first_title ? $mem->first_title . ' ' : '' }}{{ $mem->first_name }} {{ $mem->last_name }}{{ $mem->last_title ? ', ' . $mem->last_title : '' }}
                                            (NRP: {{ $mem->register_number }})
                                        </li>
                                    @endforeach
                                </ol>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- Tembusan --}}
            @php
                $carbonCopies = is_array($sprintHenti->carbon_copies) ? $sprintHenti->carbon_copies : (json_decode($sprintHenti->carbon_copies, true) ?? []);
            @endphp
            @if (!empty($carbonCopies))
            <table class="table table-bordered table-sm mb-4">
                <thead class="table-secondary">
                    <tr><th>Tembusan</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <ol class="mb-0 ps-3">
                                @foreach($carbonCopies as $cc)
                                    <li>{{ $cc }}</li>
                                @endforeach
                            </ol>
                        </td>
                    </tr>
                </tbody>
            </table>
            @endif

            {{-- Dokumen Digital --}}
            @if ($sprintHenti->suratPerintahPenghentianPenyelidikanDocumentAttachment)
                <div class="alert alert-light border mb-4">
                    <i class="bi bi-paperclip"></i> <strong>Berkas Lampiran:</strong>
                    <a href="{{ asset($sprintHenti->suratPerintahPenghentianPenyelidikanDocumentAttachment->file_path) }}" target="_blank" class="ms-2">
                        {{ $sprintHenti->suratPerintahPenghentianPenyelidikanDocumentAttachment->name }}
                    </a>
                </div>
            @endif

            {{-- Tombol Aksi --}}
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <a href="{{ route('view_produktivitas_accident', ['accident_id' => $sprintHenti->accident_id]) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <div>
                    <a href="{{ route('doc.surat-perintah-penghentian-penyelidikan-document.download', ['id' => $sprintHenti->id]) }}" class="btn btn-success me-2">
                        <i class="bi bi-file-earmark-word"></i> Unduh Word (A.8)
                    </a>
                    <a href="{{ route('doc.surat-perintah-penghentian-penyelidikan-document.edit', ['id' => $sprintHenti->id]) }}" class="btn btn-warning me-2">
                        <i class="bi bi-pencil-square"></i> Ubah
                    </a>
                    <form action="{{ route('doc.surat-perintah-penghentian-penyelidikan-document.delete', ['id' => $sprintHenti->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat perintah ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</body>
</html>
