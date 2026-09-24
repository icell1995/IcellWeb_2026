<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Cetak Surat Permintaan Izin Penyitaan (S-12) - {{ $document->document_number }}</title>

    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        .radius-card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 900px;
            margin: 20px auto;
        }
        .border-bot {
            border-bottom: 2px solid #000000;
            display: inline-block;
            padding-bottom: 2px;
        }
        .kop-surat {
            border-bottom: 3px double #000000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .table-custom th, .table-custom td {
            padding: 8px 12px;
            vertical-align: middle;
        }
        @media print {
            body {
                background: #ffffff !important;
            }
            .radius-card {
                box-shadow: none;
                padding: 0;
                margin: 0;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body style="background-color: #eeeeee">
    <div class="d-flex justify-content-center">
        <div class="radius-card w-100">
            <!-- Kop Surat -->
            <div class="kop-surat">
                <div class="row align-items-center">
                    <div class="col-2 text-center">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo Polri" style="max-height: 85px;">
                    </div>
                    <div class="col-10">
                        <h6 class="mb-0 font-weight-bold">KEPOLISIAN NEGARA REPUBLIK INDONESIA</h6>
                        <h6 class="mb-0 font-weight-bold">DAERAH {{ $accident->polres->polda->full_name ?? 'DAERAH' }}</h6>
                        <h6 class="mb-0 font-weight-bold">RESOR {{ $accident->polres->full_name ?? 'RESOR' }}</h6>
                        <small class="text-muted">
                            {{ ucwords(($accident->polres->address ?? '') . ', ' . ($accident->polres->polres_district ?? '') . ', ' . ($accident->polres->polres_zipcode ?? '')) }}
                        </small>
                    </div>
                </div>
            </div>

            <!-- Judul Dokumen -->
            <div class="text-center my-4">
                <h4 class="font-weight-bolder mb-1"><span class="border-bot">SURAT PERMINTAAN IZIN PENYITAAN</span></h4>
                <h5 class="font-weight-bold">NOMOR: {{ $document->document_number }}</h5>
            </div>

            <!-- Rincian Dokumen -->
            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Perihal</div>
                <div class="col-md-9">: Permintaan Izin Penyitaan Barang Bukti</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Kepada Yth.</div>
                <div class="col-md-9">: Ketua Pengadilan Negeri <b>{{ $court->name ?? ($document->court->name ?? '-') }}</b></div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Tanggal Dokumen</div>
                <div class="col-md-9">
                    : {{ $document->document_date ? \Carbon\Carbon::parse($document->document_date)->locale('id')->translatedFormat('d F Y') : '-' }}
                </div>
            </div>

            <!-- Dasar Rujukan -->
            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Dasar Rujukan</div>
                <div class="col-md-9">
                    <div class="card bg-light border-0">
                        <div class="card-body py-2 px-3">
                            <ol class="mb-0 ps-3">
                                <li>Pasal 38 ayat (1) Kitab Undang-Undang Hukum Acara Pidana (KUHAP);</li>
                                <li>Undang-Undang Republik Indonesia Nomor 2 Tahun 2002 tentang Kepolisian Negara Republik Indonesia;</li>
                                <li>Undang-Undang Republik Indonesia Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan;</li>
                                <li>Laporan Polisi Nomor: <b>{{ $document->laporan_pengaduan_number ?? $accident->no_lp }}</b>, tanggal {{ $document->laporan_pengaduan_date ? \Carbon\Carbon::parse($document->laporan_pengaduan_date)->locale('id')->translatedFormat('d F Y') : '-' }};</li>
                                <li>Surat Perintah Penyidikan Nomor: <b>{{ $document->sprindik_number }}</b>, tanggal {{ $document->sprindik_date ? \Carbon\Carbon::parse($document->sprindik_date)->locale('id')->translatedFormat('d F Y') : '-' }};</li>
                                @if(!empty($document->surat_perintah_penyitaan_number))
                                    <li>Surat Perintah Penyitaan Nomor: <b>{{ $document->surat_perintah_penyitaan_number }}</b>, tanggal {{ $document->surat_perintah_penyitaan_date ? \Carbon\Carbon::parse($document->surat_perintah_penyitaan_date)->locale('id')->translatedFormat('d F Y') : '-' }};</li>
                                @endif
                                @if(!empty($document->spdp_number))
                                    <li>Surat Pemberitahuan Dimulainya Penyidikan (SPDP) Nomor: <b>{{ $document->spdp_number }}</b>, tanggal {{ $document->spdp_date ? \Carbon\Carbon::parse($document->spdp_date)->locale('id')->translatedFormat('d F Y') : '-' }};</li>
                                @endif
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pasal / Tindak Pidana -->
            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Pasal yang Disangkakan</div>
                <div class="col-md-9">
                    <div class="card bg-light border-0">
                        <div class="card-body py-2 px-3">
                            @php
                                $mainLawsShow = isset($laws) ? $laws->where('flag', 'MAIN')->values() : collect();
                                $additionalLawsShow = isset($laws) ? $laws->where('flag', 'ADDT')->values() : collect();
                            @endphp
                            @if($mainLawsShow->count() > 0)
                                <ul class="mb-0 ps-3">
                                    @foreach($mainLawsShow as $law)
                                        <li>
                                            @if(!empty($law->crimeType))
                                                <b>{{ $law->constitution_chapter ?? $law->pasal }}</b>
                                                — {{ $law->crimeType->name ?? '' }}
                                                @if(!empty($law->crimeConstitution->name))
                                                    ({{ $law->crimeConstitution->name }})
                                                @elseif(!empty($law->constitution))
                                                    ({{ $law->constitution }})
                                                @endif
                                            @else
                                                <b>{{ $law->constitution_chapter ?? $law->pasal }}</b>
                                                @if(!empty($law->constitution))
                                                    ({{ $law->constitution }})
                                                @elseif(!empty($law->crimeConstitution->name))
                                                    ({{ $law->crimeConstitution->name }})
                                                @endif
                                                @if(!empty($law->description))
                                                    - <i>{{ $law->description }}</i>
                                                @endif
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @elseif($laws && count($laws) > 0)
                                {{-- Legacy: no flag differentiation --}}
                                <ul class="mb-0 ps-3">
                                    @foreach($laws as $law)
                                        <li>
                                            <b>{{ $law->constitution_chapter ?? $law->pasal }}</b>
                                            @if(!empty($law->constitution))
                                                ({{ $law->constitution }})
                                            @elseif(!empty($law->crimeConstitution->name))
                                                ({{ $law->crimeConstitution->name }})
                                            @endif
                                            @if(!empty($law->description))
                                                - <i>{{ $law->description }}</i>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>

                    @if($additionalLawsShow->count() > 0)
                        <div class="card bg-light border-0 mt-2">
                            <div class="card-body py-2 px-3">
                                <small class="fw-bold text-secondary">Undang-Undang Khusus Tambahan:</small>
                                <ul class="mb-0 ps-3">
                                    @foreach($additionalLawsShow as $addLaw)
                                        <li>{{ $addLaw->constitution ?? $addLaw->description ?? '-' }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tersangka -->
            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Tersangka Terkait</div>
                <div class="col-md-9">
                    <div class="card bg-light border-0">
                        <div class="card-body py-2 px-3">
                            @if($documentSuspects && count($documentSuspects) > 0)
                                <ol class="mb-0 ps-3">
                                    @foreach($documentSuspects as $docSuspect)
                                        @php
                                            $s = $docSuspect->suspect;
                                        @endphp
                                        @if($s)
                                            <li>
                                                <b>{{ $s->name }}</b>
                                                @if(!empty($s->id_card_number)) | NIK: {{ $s->id_card_number }} @endif
                                                @if(!empty($s->occupation)) | Pekerjaan: {{ $s->occupation }} @endif
                                                @if(!empty($s->address)) | Alamat: {{ $s->address }} @endif
                                            </li>
                                        @endif
                                    @endforeach
                                </ol>
                            @else
                                <span class="text-muted">Dalam proses lidik / belum ada tersangka spesifik</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Barang Sitaan -->
            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Barang yang Dimintakan Izin Sita</div>
                <div class="col-md-9">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm table-custom bg-white">
                            <thead class="table-danger text-center">
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th>Nama Barang / Benda Sitaan</th>
                                    <th>Jenis</th>
                                    <th>Jumlah</th>
                                    <th>Satuan</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($seizedItems as $index => $item)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="font-weight-bold">{{ $item->nama }}</td>
                                        <td>{{ $item->jenis ?? '-' }}</td>
                                        <td class="text-center">{{ $item->jumlah }}</td>
                                        <td class="text-center">{{ $item->satuan ?? '-' }}</td>
                                        <td>{{ $item->keterangan ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Belum ada daftar barang sitaan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Ketua Tim Penyidik -->
            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Ketua Tim Penyidik</div>
                <div class="col-md-9">
                    <div class="card bg-light border-0">
                        <div class="card-body py-2 px-3">
                            @if(isset($leaderOfficer) && $leaderOfficer)
                                @php
                                    $leaderRankName = $leaderOfficer->rank->name ?? ($leaderOfficer->rank->full_name ?? '');
                                    $leaderPosName = $leaderOfficer->position->name ?? '';
                                    $leaderFullName = \App\Helpers\PeopleNameHelper::getFullName($leaderOfficer->first_title, $leaderOfficer->first_name, $leaderOfficer->last_name, $leaderOfficer->last_title);
                                @endphp
                                <b>{{ $leaderFullName }}</b><br>
                                <span class="text-muted">Pangkat/NRP: {{ $leaderRankName }} / {{ $leaderOfficer->register_number }} | Jabatan: {{ $leaderPosName }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pejabat Penandatangan -->
            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Pejabat Penandatangan</div>
                <div class="col-md-9">
                    <div class="card bg-light border-0">
                        <div class="card-body py-2 px-3">
                            @php
                                $displaySignatories = isset($signatories) && $signatories->isNotEmpty() ? $signatories : $officers->where('class', 'SIGNATORY');
                                if ($displaySignatories->isEmpty()) {
                                    $displaySignatories = $officers->where('class', '!=', 'LEADER');
                                }
                            @endphp
                            @forelse($displaySignatories as $no => $officer)
                                @php
                                    $rankName = $officer->rank->name ?? ($officer->rank->full_name ?? '');
                                    $posName = $officer->position->name ?? '';
                                    $fullName = \App\Helpers\PeopleNameHelper::getFullName($officer->first_title, $officer->first_name, $officer->last_name, $officer->last_title);
                                @endphp
                                <div class="mb-2">
                                    {{ $no + 1 }}. <b>{{ $fullName }}</b><br>
                                    <span class="text-muted ms-3">Pangkat/NRP: {{ $rankName }} / {{ $officer->register_number }} | Jabatan: {{ $posName }}</span>
                                </div>
                            @empty
                                <span class="text-muted">-</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tembusan -->
            @if(!empty($document->carbon_copies) && count($document->carbon_copies) > 0)
                <div class="row mb-3">
                    <div class="col-md-3 font-weight-bold">Tembusan</div>
                    <div class="col-md-9">
                        <div class="card bg-light border-0">
                            <div class="card-body py-2 px-3">
                                <ol class="mb-0 ps-3">
                                    @foreach($document->carbon_copies as $carbonCopy)
                                        <li>{{ $carbonCopy }}</li>
                                    @endforeach
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Berkas Digital Lampiran -->
            @if($attachments && count($attachments) > 0)
                <div class="row mb-3">
                    <div class="col-md-3 font-weight-bold">Berkas Digital Terlampir</div>
                    <div class="col-md-9">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm table-custom bg-white">
                                <thead class="table-secondary text-center">
                                    <tr>
                                        <th>Nama File</th>
                                        <th>Ukuran</th>
                                        <th class="no-print">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attachments as $att)
                                        <tr>

                                            <td>{{ $att->original_name ?? $att->name }}</td>
                                            <td class="text-center">{{ round(((float) ($att->size ?? 0)) / 1024, 1) }} KB</td>
                                            <td class="text-center no-print">
                                                @if(!empty($att->path))
                                                    <a href="{{ asset('storage/' . $att->path) }}" target="_blank" class="btn btn-xs btn-outline-primary">
                                                        <i class="bi bi-eye"></i> Buka File
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Metadata Pembuat -->
            <div class="border-top pt-3 mt-4 text-muted small">
                <div class="row">
                    <div class="col-md-6">
                        Dibuat oleh: <b>{{ $createdByUser->full_name ?? ($createdByUser->name ?? '-') }}</b>
                    </div>
                    <div class="col-md-6 text-md-end">
                        Tanggal Dibuat: {{ $document->created_at ? \Carbon\Carbon::parse($document->created_at)->locale('id')->translatedFormat('d F Y H:i') : '-' }} WIB
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi (Download & Print) -->
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top no-print">
                <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Progres Perkara
                </a>

                <div>
                    <button type="button" class="btn btn-outline-dark me-2" id="print-cetak">
                        <i class="bi bi-printer me-1"></i> Cetak Tampilan
                    </button>

                    <a href="{{ route('doc.surat-permintaan-izin-penyitaan-document.download', ['id' => $document->id, 'accident_id' => $accident->id]) }}"
                        class="btn btn-primary">
                        <i class="bi bi-file-earmark-word me-1"></i> Unduh Dokumen (Word)
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#print-cetak').on('click', function () {
                window.print();
            });
        });
    </script>
</body>

</html>
