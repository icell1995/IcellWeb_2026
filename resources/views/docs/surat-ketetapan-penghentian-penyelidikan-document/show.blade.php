<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Ketetapan Tentang Penghentian Penyelidikan</title>
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
                DAERAH {{ strtoupper($sketHenti->accident->polres->polda->full_name ?? '') }}<br>
                RESOR {{ strtoupper($sketHenti->accident->polres->full_name ?? '') }}<br>
                <span class="text-muted small">{{ ucwords($sketHenti->accident->polres->address ?? '') }}</span>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold">KOPSTUK</span>
                    <span class="badge bg-secondary fs-6">A.8.1</span>
                </div>
                <h4 class="mt-2"><u>SURAT KETETAPAN</u></h4>
                <h5>NOMOR: {{ $sketHenti->document_number }}</h5>
                <h6>Tentang</h6>
                <h5 class="fw-bold"><u>PENGHENTIAN PENYELIDIKAN</u></h5>
            </div>

            {{-- Identitas Dokumen & Rujukan --}}
            <table class="table table-bordered table-sm mb-4">
                <thead class="table-secondary">
                    <tr><th colspan="2">Identitas Dokumen & Rujukan</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold" width="35%">Nomor Surat Ketetapan</td>
                        <td>{{ $sketHenti->document_number }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Tanggal Penetapan</td>
                        <td>{{ $sketHenti->document_date ? \Carbon\Carbon::parse($sketHenti->document_date)->locale('id')->isoFormat('D MMMM Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Terhitung Mulai Tanggal</td>
                        <td>{{ $sketHenti->effective_date ? \Carbon\Carbon::parse($sketHenti->effective_date)->locale('id')->isoFormat('D MMMM Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Nomor Laporan Polisi</td>
                        <td>{{ $sketHenti->accident->no_lp }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Dikeluarkan di</td>
                        <td>{{ $sketHenti->document_location ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Dasar Surat Perintah Penghentian Penyelidikan</td>
                        <td>
                            @if ($sketHenti->suratPerintahPenghentianPenyelidikanDocument)
                                {{ $sketHenti->suratPerintahPenghentianPenyelidikanDocument->document_number }}
                                ({{ $sketHenti->suratPerintahPenghentianPenyelidikanDocument->document_date ? date('d/m/Y', strtotime($sketHenti->suratPerintahPenghentianPenyelidikanDocument->document_date)) : '-' }})
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Dasar Sprinlidik Terdahulu</td>
                        <td>
                            @if ($sketHenti->suratPerintahPenyelidikanDocument)
                                {{ $sketHenti->suratPerintahPenyelidikanDocument->document_number }}
                                ({{ $sketHenti->suratPerintahPenyelidikanDocument->document_date ? date('d/m/Y', strtotime($sketHenti->suratPerintahPenyelidikanDocument->document_date)) : '-' }})
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Dasar Laporan Hasil Gelar Perkara</td>
                        <td>
                            @if ($sketHenti->laporanHasilGelarPerkaraDocument)
                                {{ $sketHenti->laporanHasilGelarPerkaraDocument->document_number ?? 'LHGP' }}
                                ({{ $sketHenti->laporanHasilGelarPerkaraDocument->document_date ? date('d/m/Y', strtotime($sketHenti->laporanHasilGelarPerkaraDocument->document_date)) : '-' }})
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- Alasan Penghentian --}}
            @php
                $kodeAlasan = is_array($sketHenti->kode_alasan) ? $sketHenti->kode_alasan : (json_decode($sketHenti->kode_alasan, true) ?? []);
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
                    @if ($sketHenti->alasan_penghentian)
                    <tr>
                        <td class="fw-bold">Uraian Alasan</td>
                        <td>{{ $sketHenti->alasan_penghentian }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>

            {{-- Terlapor / Pihak Terkait yang Dihentikan --}}
            <table class="table table-bordered table-sm mb-4">
                <thead class="table-secondary">
                    <tr><th colspan="5">Pihak Terlapor / Terkait</th></tr>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Jenis Kelamin</th>
                        <th>Pekerjaan</th>
                        <th>Alamat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sketHenti->suspects as $idx => $suspect)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td><strong>{{ $suspect->name }}</strong><br><small class="text-muted">NIK: {{ $suspect->identity_number ?? ($suspect->nik ?? '-') }}</small></td>
                            <td>{{ $suspect->gender->name ?? ($suspect->gender_id == 1 ? 'Laki-laki' : ($suspect->gender_id == 2 ? 'Perempuan' : '-')) }}</td>
                            <td>{{ $suspect->job->name ?? '-' }}</td>
                            <td>{{ $suspect->address ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Belum ada pihak terlapor yang dipilih</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pejabat Penandatangan --}}
            @php
                $signatory = $sketHenti->suratKetetapanPenghentianPenyelidikanDocumentOfficers->firstWhere('class', 'SIGNATORY');
            @endphp
            <table class="table table-bordered table-sm mb-4">
                <thead class="table-secondary">
                    <tr><th colspan="2">Pejabat Penandatangan (Selaku Penyidik)</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold" width="35%">Nama Pejabat</td>
                        <td>
                            @if ($signatory)
                                {{ $signatory->first_title ? $signatory->first_title . ' ' : '' }}{{ $signatory->first_name }} {{ $signatory->last_name }}{{ $signatory->last_title ? ', ' . $signatory->last_title : '' }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">NRP</td>
                        <td>{{ $signatory->register_number ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>

            {{-- Tembusan --}}
            @php
                $carbonCopies = is_array($sketHenti->carbon_copies) ? $sketHenti->carbon_copies : (json_decode($sketHenti->carbon_copies, true) ?? []);
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
            @if ($sketHenti->suratKetetapanPenghentianPenyelidikanDocumentAttachment)
                <div class="alert alert-light border mb-4">
                    <i class="bi bi-paperclip"></i> <strong>Berkas Lampiran:</strong>
                    <a href="{{ asset($sketHenti->suratKetetapanPenghentianPenyelidikanDocumentAttachment->file_path) }}" target="_blank" class="ms-2">
                        {{ $sketHenti->suratKetetapanPenghentianPenyelidikanDocumentAttachment->name }}
                    </a>
                </div>
            @endif

            {{-- Tombol Aksi --}}
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <a href="{{ route('view_produktivitas_accident', ['accident_id' => $sketHenti->accident_id]) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <div>
                    <a href="{{ route('doc.surat-ketetapan-penghentian-penyelidikan-document.download', ['id' => $sketHenti->id]) }}" class="btn btn-success me-2">
                        <i class="bi bi-file-earmark-word"></i> Unduh Word (A.8.1)
                    </a>
                    <a href="{{ route('doc.surat-ketetapan-penghentian-penyelidikan-document.edit', ['id' => $sketHenti->id]) }}" class="btn btn-warning me-2">
                        <i class="bi bi-pencil-square"></i> Ubah
                    </a>
                    <form action="{{ route('doc.surat-ketetapan-penghentian-penyelidikan-document.delete', ['id' => $sketHenti->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat ketetapan ini?');">
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
