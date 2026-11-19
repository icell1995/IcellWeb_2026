<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Perintah Penghentian Penyidikan</title>
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
                <h5 class="fw-bold">“PRO JUSTITIA”</h5>
                <h4><u>SURAT PERINTAH PENGHENTIAN PENYIDIKAN</u></h4>
                <h5>NOMOR: {{ $sprintHenti->document_number }}</h5>
            </div>

            {{-- Identitas Dokumen & Dasar --}}
            <table class="table table-bordered table-sm mb-4">
                <thead class="table-secondary">
                    <tr><th colspan="2">Identitas Dokumen</th></tr>
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
                        <td>{{ $accident->no_lp }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Dasar Sprindik Terdahulu</td>
                        <td>
                            @if ($sprintHenti->suratPerintahPenyidikanDocument)
                                {{ $sprintHenti->suratPerintahPenyidikanDocument->document_number }} 
                                ({{ $sprintHenti->suratPerintahPenyidikanDocument->document_date ? date('d/m/Y', strtotime($sprintHenti->suratPerintahPenyidikanDocument->document_date)) : '-' }})
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Dasar Laporan Gelar Perkara</td>
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
            <table class="table table-bordered table-sm mb-4">
                <thead class="table-info">
                    <tr><th colspan="2">Alasan Penghentian Penyidikan</th></tr>
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
                        <td class="fw-bold">Uraian / Keterangan</td>
                        <td>{{ $sprintHenti->alasan_penghentian }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>

            {{-- Pertimbangan, Dasar, Untuk --}}
            <div class="card mb-4">
                <div class="card-header bg-light fw-bold">Isi Surat Perintah</div>
                <div class="card-body">
                    <p class="mb-2"><strong>Pertimbangan:</strong></p>
                    <p class="text-muted ps-3 mb-3">{!! nl2br(e($sprintHenti->pertimbangan)) !!}</p>

                    <p class="mb-2"><strong>Dasar:</strong></p>
                    <p class="text-muted ps-3 mb-3">{!! nl2br(e($sprintHenti->dasar)) !!}</p>

                    <p class="mb-2"><strong>Untuk:</strong></p>
                    <p class="text-muted ps-3 mb-0">{!! nl2br(e($sprintHenti->untuk)) !!}</p>
                </div>
            </div>

            {{-- Penyidik Penerima Perintah --}}
            <table class="table table-bordered table-sm mb-4">
                <thead class="table-warning">
                    <tr>
                        <th width="5%">No</th>
                        <th>Penyidik / Penyidik Pembantu Penerima Perintah</th>
                        <th>NRP</th>
                        <th>Jabatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($memberOfficers as $idx => $officer)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $officer->first_name }} {{ $officer->last_name }}</td>
                            <td>{{ $officer->register_number }}</td>
                            <td>{{ optional($officer->position)->name ?? ($officer->position_id ?? '-') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted">Belum ada penyidik penerima perintah yang dipilih</td></tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pejabat Penandatangan --}}
            <table class="table table-bordered table-sm mb-4">
                <thead class="table-secondary">
                    <tr><th colspan="2">Pejabat Pemberi Perintah (Penandatangan)</th></tr>
                </thead>
                <tbody>
                    @if ($signatoryOfficer)
                        <tr>
                            <td class="fw-bold" width="35%">Nama</td>
                            <td>{{ $signatoryOfficer->first_name }} {{ $signatoryOfficer->last_name }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">NRP</td>
                            <td>{{ $signatoryOfficer->register_number }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Jabatan</td>
                            <td>{{ optional($signatoryOfficer->position)->name ?? '-' }}</td>
                        </tr>
                    @else
                        <tr><td colspan="2" class="text-muted">Belum dipilih</td></tr>
                    @endif
                </tbody>
            </table>

            {{-- Tombol Aksi --}}
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $sprintHenti->accident_id]) }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Kasus
                    </a>
                </div>

                <div>
                    <a href="{{ route('doc.surat-perintah-penghentian-penyidikan-document.download', $sprintHenti->id) }}" class="btn btn-success me-2">
                        <i class="fas fa-file-word me-1"></i> Unduh Word (.docx)
                    </a>

                    @if ($sprintHenti->isEditable())
                        <a href="{{ route('doc.surat-perintah-penghentian-penyidikan-document.edit', $sprintHenti->id) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <form action="{{ route('doc.surat-perintah-penghentian-penyidikan-document.delete', $sprintHenti->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat perintah ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-1"></i> Hapus
                            </button>
                        </form>
                    @endif
                </div>
            </div>

        </div>
    </div>
</body>
</html>
