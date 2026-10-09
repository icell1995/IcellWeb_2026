<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Ketetapan Penghentian Penyidikan</title>
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
                <h4><u>SURAT KETETAPAN</u></h4>
                <h5>NOMOR: {{ $sketHenti->document_number }}</h5>
                <h6 class="fw-bold text-uppercase">tentang<br>PENGHENTIAN PENYIDIKAN</h6>
            </div>

            {{-- Identitas Dokumen & Dasar --}}
            <table class="table table-bordered table-sm mb-4">
                <thead class="table-secondary">
                    <tr><th colspan="2">Identitas Dokumen & Dasar Hukum</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold" width="35%">Nomor Surat Ketetapan</td>
                        <td>{{ $sketHenti->document_number }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Tanggal Surat Ketetapan</td>
                        <td>{{ $sketHenti->document_date ? \Carbon\Carbon::parse($sketHenti->document_date)->locale('id')->isoFormat('D MMMM Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Terhitung Mulai Tanggal (TMT)</td>
                        <td>{{ $sketHenti->effective_date ? \Carbon\Carbon::parse($sketHenti->effective_date)->locale('id')->isoFormat('D MMMM Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Ditetapkan di</td>
                        <td>{{ $sketHenti->document_location ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Laporan Polisi</td>
                        <td>{{ $accident->no_lp }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Dasar Sprindik</td>
                        <td>
                            @if ($sketHenti->suratPerintahPenyidikanDocument)
                                {{ $sketHenti->suratPerintahPenyidikanDocument->document_number }} 
                                ({{ $sketHenti->suratPerintahPenyidikanDocument->document_date ? date('d/m/Y', strtotime($sketHenti->suratPerintahPenyidikanDocument->document_date)) : '-' }})
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Dasar SPDP</td>
                        <td>
                            @if ($sketHenti->suratPemberitahuanDimulainyaPenyidikanDocument)
                                {{ $sketHenti->suratPemberitahuanDimulainyaPenyidikanDocument->document_number }} 
                                ({{ $sketHenti->suratPemberitahuanDimulainyaPenyidikanDocument->document_date ? date('d/m/Y', strtotime($sketHenti->suratPemberitahuanDimulainyaPenyidikanDocument->document_date)) : '-' }})
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Dasar Penetapan Tersangka</td>
                        <td>
                            @if ($sketHenti->suratKetetapanTentangPenetapanTersangkaDocument)
                                {{ $sketHenti->suratKetetapanTentangPenetapanTersangkaDocument->document_number }} 
                                ({{ $sketHenti->suratKetetapanTentangPenetapanTersangkaDocument->document_date ? date('d/m/Y', strtotime($sketHenti->suratKetetapanTentangPenetapanTersangkaDocument->document_date)) : '-' }})
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Dasar Surat Perintah Penghentian Penyidikan</td>
                        <td>
                            @if ($sketHenti->suratPerintahPenghentianPenyidikanDocument)
                                {{ $sketHenti->suratPerintahPenghentianPenyidikanDocument->document_number }} 
                                ({{ $sketHenti->suratPerintahPenghentianPenyidikanDocument->document_date ? date('d/m/Y', strtotime($sketHenti->suratPerintahPenghentianPenyidikanDocument->document_date)) : '-' }})
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Dasar Gelar Perkara (LHGP)</td>
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
            <table class="table table-bordered table-sm mb-4">
                <thead class="table-info">
                    <tr><th colspan="2">Alasan Penghentian Penyidikan</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold" width="35%">Alasan Yuridis</td>
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
                        <td class="fw-bold">Keterangan / Uraian Tambahan</td>
                        <td>{{ $sketHenti->alasan_penghentian }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="fw-bold">Dugaan Tindak Pidana</td>
                        <td>{{ $sketHenti->dugaan_tindak_pidana ?? 'Kecelakaan Lalu Lintas' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Pasal yang Disangkakan</td>
                        <td>{{ $sketHenti->pasal_list ?? 'Pasal 310 UU No. 22 Tahun 2009' }}</td>
                    </tr>
                </tbody>
            </table>

            {{-- Tersangka Terkait --}}
            <table class="table table-bordered table-sm mb-4">
                <thead class="table-warning">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Tersangka</th>
                        <th>NIK</th>
                        <th>Alamat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sketHenti->suspects as $idx => $s)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $s->name }}</strong></td>
                            <td>{{ $s->identity_number ?? ($s->nik ?? '-') }}</td>
                            <td>{{ $s->address ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted">Belum ada data tersangka yang dipilih</td></tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pejabat Penandatangan --}}
            <table class="table table-bordered table-sm mb-4">
                <thead class="table-secondary">
                    <tr><th colspan="2">Pejabat Penandatangan</th></tr>
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
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $sketHenti->accident_id]) }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Kasus
                    </a>
                </div>

                <div>
                    <a href="{{ route('doc.surat-ketetapan-penghentian-penyidikan-document.download', $sketHenti->id) }}" class="btn btn-success me-2">
                        <i class="fas fa-file-word me-1"></i> Unduh Word (.docx)
                    </a>

                    @if ($sketHenti->isEditable())
                        <a href="{{ route('doc.surat-ketetapan-penghentian-penyidikan-document.edit', $sketHenti->id) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <form action="{{ route('doc.surat-ketetapan-penghentian-penyidikan-document.delete', $sketHenti->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat ketetapan ini?');">
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
