<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Surat Permintaan Izin Penggeledahan — {{ $document->document_number }}</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #eeeeee;
            color: #000000;
        }

        .document-wrapper {
            background-color: #ffffff;
            max-width: 850px;
            width: 100%;
            margin: 25px auto;
            padding: 40px 50px 50px 50px;
            border-radius: 6px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.12);
            font-size: 14px;
            line-height: 1.5;
            color: #000;
        }

        .kop-box {
            display: inline-block;
            text-align: center;
            line-height: 1.25;
            font-weight: bold;
            font-size: 13px;
        }

        .kop-line {
            border-bottom: 2px solid #000000;
            margin-top: 4px;
            margin-bottom: 8px;
            width: 100%;
        }

        .text-pro-justitia {
            font-weight: bold;
            text-decoration: underline;
            font-size: 14px;
        }

        .table-meta td {
            padding: 1px 4px;
            vertical-align: top;
            border: none !important;
            font-size: 14px;
        }

        .content-body {
            text-align: justify;
            margin-top: 15px;
        }

        .ol-main {
            padding-left: 20px;
            margin-bottom: 0;
        }

        .ol-main > li {
            margin-bottom: 12px;
            padding-left: 4px;
        }

        .ol-sub {
            list-style-type: lower-alpha;
            padding-left: 22px;
            margin-top: 4px;
            margin-bottom: 4px;
        }

        .ol-sub > li {
            margin-bottom: 4px;
            padding-left: 2px;
        }

        .table-suspect {
            margin: 8px 0 8px 15px;
            width: calc(100% - 15px);
        }

        .table-suspect td {
            padding: 2px 4px;
            vertical-align: top;
            border: none !important;
            font-size: 14px;
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .document-wrapper {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 15mm 20mm !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    @php
        $signatoryOfficer = $document->authorizedSignatory;
        $investigatorOfficer = $document->officers->where('class', 'MEMBER')->first() 
            ?? $document->officers->where('class', 'LEADER')->first()
            ?? $signatoryOfficer;

        $sprindik = $document->suratPerintahPenyidikanDocument;
        $pasalFormatted = $sprindik->pasal_formatted ?? '';
        $tindakPidanaFormatted = $sprindik->tindak_pidana_formatted ?? '';

        $suspectList = $document->suspects ?? collect();
        $suspectNames = $suspectList->pluck('name')->implode(', ');
    @endphp

    {{-- Toolbar Aksi --}}
    <div class="no-print d-flex justify-content-between align-items-center" style="max-width: 850px; margin: 15px auto 0 auto; padding: 0 10px;">
        <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId ?? $accident->id]) }}" class="btn btn-secondary btn-sm">
            <i class="fa fa-arrow-left"></i> Kembali ke Progres Perkara
        </a>
        <div class="d-flex gap-2">
            <a href="{{ route('doc.surat-permintaan-penggeledahan-document.download', ['id' => $document->id, 'accident_id' => $accidentId ?? $accident->id]) }}" class="btn btn-success btn-sm mr-2">
                <i class="fa fa-file-word"></i> Unduh Word (.docx)
            </a>
            <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="fa fa-print"></i> Cetak / PDF
            </button>
        </div>
    </div>

    {{-- Lembar Surat --}}
    <div class="document-wrapper">

        {{-- Kode Dokumen Pojok Kanan Atas --}}
        <div class="text-right" style="font-weight: bold; font-size: 15px; margin-bottom: -15px;">
            S-1
        </div>

        {{-- KOP KESATUAN & LOGO --}}
        <div class="row align-items-start">
            <div class="col-7">
                <div class="kop-box text-center">
                    KEPOLISIAN NEGARA REPUBLIK INDONESIA<br>
                    DAERAH {{ strtoupper($accident->polres->polda->full_name ?? '') }}<br>
                    RESOR {{ strtoupper($accident->polres->full_name ?? '') }}<br>
                    <span style="font-weight: normal; font-size: 12px; text-transform: capitalize;">
                        {{ ucwords($accident->polres->address . ', ' . $accident->polres->polres_district . ', ' . $accident->polres->polres_zipcode) }}
                    </span>
                    <div class="kop-line"></div>
                </div>
            </div>
            <div class="col-5 text-right pt-2">
                <div>
                    {{ $accident->polres->polres_district ?? ($accident->polres->full_name ?? 'Kota') }}, 
                    {{ $document->document_date ? Carbon\Carbon::parse($document->document_date)->locale('id')->translatedFormat('d F Y') : date('d F Y') }}
                </div>
            </div>
        </div>

        {{-- PRO JUSTITIA --}}
        <div class="mt-2 mb-3">
            <span class="text-pro-justitia">"PRO JUSTITIA"</span>
        </div>

        {{-- META SURAT (Nomor, Klasifikasi, Lampiran, Hal & Tujuan Kepada) --}}
        <div class="row mb-3">
            <div class="col-7">
                <table class="table-meta">
                    <tr>
                        <td style="width: 85px;">Nomor</td>
                        <td style="width: 15px;">:</td>
                        <td>{{ $document->document_number }}</td>
                    </tr>
                    <tr>
                        <td>Klasifikasi</td>
                        <td>:</td>
                        <td>Biasa</td>
                    </tr>
                    <tr>
                        <td>Lampiran</td>
                        <td>:</td>
                        <td>satu berkas</td>
                    </tr>
                    <tr>
                        <td>Hal</td>
                        <td>:</td>
                        <td><strong>permintaan izin penggeledahan.</strong></td>
                    </tr>
                </table>
            </div>
            <div class="col-5 pl-3">
                <div style="line-height: 1.4;">
                    Kepada<br>
                    Yth. <strong>KETUA PENGADILAN NEGERI {{ strtoupper($accident->polres->polres_district ?? ($accident->polres->full_name ?? '......')) }}</strong><br>
                    di<br>
                    <span class="pl-3">{{ ucwords(strtolower($accident->polres->polres_district ?? ($accident->polres->full_name ?? 'Kota/Kab'))) }}</span>
                </div>
            </div>
        </div>

        {{-- ISI SURAT (POIN 1 - 4) --}}
        <div class="content-body">
            <ol class="ol-main">
                {{-- 1. Rujukan --}}
                <li>
                    <strong>Rujukan:</strong>
                    <ol class="ol-sub">
                        <li>Undang-Undang Nomor 2 Tahun 2002 tentang Kepolisian Negara Republik Indonesia;</li>
                        <li>Pasal 3 dan Pasal 618 Undang-Undang Nomor 1 Tahun 2023 tentang Kitab Undang-Undang Hukum Pidana;</li>
                        <li>Pasal 1 angka 14 dan angka 34, Pasal 5 ayat (2) huruf a, Pasal 7 ayat (1) huruf f, Pasal 41, Pasal 42, Pasal 43, Pasal 47, Pasal 89 huruf d, Pasal 112 huruf a, Pasal 113 ayat (4), ayat (5), ayat (6), ayat (7), ayat (8) dan ayat (9), Pasal 114, Pasal 115, Pasal 116, Pasal 156 ayat (1) huruf d dan Pasal 361 Undang-Undang Nomor 20 Tahun 2025 tentang Kitab Undang-Undang Hukum Acara Pidana;</li>
                        <li>{{ $pasalFormatted ?: 'Undang-Undang Republik Indonesia terkait tindak pidana yang dipersangkakan' }};</li>
                        <li>Laporan Polisi Nomor: {{ $accident->no_lp ?? '..................' }} tanggal {{ $accident->report_date ? Carbon\Carbon::parse($accident->report_date)->locale('id')->translatedFormat('d F Y') : ($accident->created_at ? Carbon\Carbon::parse($accident->created_at)->locale('id')->translatedFormat('d F Y') : '..................') }};</li>
                        <li>Surat Perintah Penyidikan Nomor: {{ $sprindik->document_number ?? '..................' }} tanggal {{ $sprindik && $sprindik->document_date ? Carbon\Carbon::parse($sprindik->document_date)->locale('id')->translatedFormat('d F Y') : '..................' }};</li>
                        <li>Laporan kemajuan singkat tanggal {{ $document->document_date ? Carbon\Carbon::parse($document->document_date)->locale('id')->translatedFormat('d F Y') : '..................' }}.</li>
                    </ol>
                </li>

                {{-- 2. Sehubungan dengan rujukan --}}
                <li>
                    Sehubungan dengan rujukan di atas, diberitahukan kepada Ketua bahwa penyidik {{ $accident->polres->full_name ?? 'Kepolisian' }} sedang menangani perkara dugaan tindak pidana {{ $tindakPidanaFormatted ?: 'tindak pidana yang dipersangkakan' }}, yang terjadi pada hari {{ $accident->accident_date ? Carbon\Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('l, d F Y') : '..................' }} atau dalam kurun waktu sekitar pukul {{ $accident->accident_time ? Carbon\Carbon::parse($accident->accident_time)->format('H:i') . ' WIB' : '..................' }}, sebagaimana dimaksud dalam {{ $pasalFormatted ?: 'Pasal yang dipersangkakan' }}.
                </li>

                {{-- 3. Permintaan Izin Penggeledahan --}}
                <li>
                    Berkaitan dengan hal tersebut, diajukan kepada Ketua permintaan izin penggeledahan {{ strtolower($document->jenis_penggeledahan ?? 'rumah/tempat tertutup lainnya atau alat angkut') }} milik atau yang dihuni/digunakan oleh {{ $suspectNames ?: '....................................' }} yang terletak di {{ $document->alamat_penggeledahan ?? '....................................' }}, yang diduga sebagai tempat kejadian perkara/tempat persembunyian tersangka/tempat disembunyikan barang-barang bukti*), berdasarkan hasil pemeriksaan terhadap tersangka/saksi:

                    @if ($suspectList->isNotEmpty())
                        @foreach ($suspectList as $suspect)
                            <table class="table-suspect">
                                <tr>
                                    <td style="width: 25px;">a.</td>
                                    <td style="width: 190px;">nama</td>
                                    <td style="width: 15px;">:</td>
                                    <td><strong>{{ $suspect->name }}</strong></td>
                                </tr>
                                <tr>
                                    <td>b.</td>
                                    <td>tempat/tanggal lahir</td>
                                    <td>:</td>
                                    <td>{{ ($suspect->birth_place ?? '-') . ', ' . ($suspect->birth_date ? Carbon\Carbon::parse($suspect->birth_date)->locale('id')->translatedFormat('d F Y') : '-') }}</td>
                                </tr>
                                <tr>
                                    <td>c.</td>
                                    <td>nomor identitas</td>
                                    <td>:</td>
                                    <td>{{ $suspect->nik ?? ($suspect->identity_number ?? '-') }}</td>
                                </tr>
                                <tr>
                                    <td>d.</td>
                                    <td>jenis kelamin</td>
                                    <td>:</td>
                                    <td>{{ $suspect->gender ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>e.</td>
                                    <td>pekerjaan</td>
                                    <td>:</td>
                                    <td>{{ $suspect->job ?? ($suspect->occupation->name ?? '-') }}</td>
                                </tr>
                                <tr>
                                    <td>f.</td>
                                    <td>kewarganegaraan</td>
                                    <td>:</td>
                                    <td>{{ $suspect->citizenship ?? 'WNI' }}</td>
                                </tr>
                                <tr>
                                    <td>g.</td>
                                    <td>agama</td>
                                    <td>:</td>
                                    <td>{{ $suspect->religion ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>h.</td>
                                    <td>tempat tinggal/kediaman</td>
                                    <td>:</td>
                                    <td>{{ $suspect->address ?? ($suspect->full_address ?? '-') }}</td>
                                </tr>
                            </table>
                        @endforeach
                    @else
                        <table class="table-suspect">
                            <tr><td style="width: 25px;">a.</td><td style="width: 190px;">nama</td><td style="width: 15px;">:</td><td>...........................................................................</td></tr>
                            <tr><td>b.</td><td>tempat/tanggal lahir</td><td>:</td><td>...........................................................................</td></tr>
                            <tr><td>c.</td><td>nomor identitas</td><td>:</td><td>...........................................................................</td></tr>
                            <tr><td>d.</td><td>jenis kelamin</td><td>:</td><td>...........................................................................</td></tr>
                            <tr><td>e.</td><td>pekerjaan</td><td>:</td><td>...........................................................................</td></tr>
                            <tr><td>f.</td><td>kewarganegaraan</td><td>:</td><td>...........................................................................</td></tr>
                            <tr><td>g.</td><td>agama</td><td>:</td><td>...........................................................................</td></tr>
                            <tr><td>h.</td><td>tempat tinggal/kediaman</td><td>:</td><td>...........................................................................</td></tr>
                        </table>
                    @endif

                    <div class="mt-2">
                        Untuk lebih memudahkan dalam berkoordinasi dan berkomunikasi ditunjuk penyidik/penyidik pembantu 
                        <strong>{{ $investigatorOfficer ? ($investigatorOfficer->full_name ?? ($investigatorOfficer->first_name . ' ' . $investigatorOfficer->last_name)) : '....................................' }}</strong> 
                        ({{ $investigatorOfficer ? ($investigatorOfficer->rank->name ?? '') : '....................' }}), 
                        dengan nomor <em>handphone</em> <strong>{{ $investigatorOfficer->phone_number ?? ($signatoryOfficer->phone_number ?? '....................') }}</strong>.
                    </div>
                </li>

                {{-- 4. Penutup --}}
                <li>
                    Demikian untuk menjadi maklum, atas perhatian dan kerjasamanya diucapkan terima kasih.
                </li>
            </ol>
        </div>

        {{-- TANDA TANGAN & TEMBUSAN --}}
        <div class="row mt-4 pt-3">
            <div class="col-6">
                <div style="font-size: 13px;">
                    <strong>Tembusan:</strong>
                    <ol class="pl-3 mb-0" style="font-size: 13px; line-height: 1.4;">
                        <li>Kepala Kepolisian Daerah {{ $accident->polres->polda->full_name ?? '..................' }};</li>
                        <li>Kepala Kepolisian Resor {{ $accident->polres->full_name ?? '..................' }};</li>
                        <li>Pengawas Penyidikan / Arsip.</li>
                    </ol>
                </div>
            </div>
            <div class="col-6 text-center">
                <div style="font-size: 13px; line-height: 1.35;">
                    <p class="mb-0 font-weight-bold" style="text-transform: uppercase;">
                        {{ $signatoryOfficer->position->name ?? 'KASAT LANTAS / SELAKU PENYIDIK' }}
                    </p>
                    <p class="mb-0">Selaku Penyidik</p>
                    
                    <div style="height: 65px;"></div>
                    
                    <p class="mb-0 font-weight-bold" style="text-transform: uppercase;">
                        <u>{{ $signatoryOfficer ? ($signatoryOfficer->full_name ?? ($signatoryOfficer->first_name . ' ' . $signatoryOfficer->last_name)) : '................................................' }}</u>
                    </p>
                    <p class="mb-0">
                        {{ $signatoryOfficer ? (($signatoryOfficer->rank->name ?? '') . ' NRP. ' . $signatoryOfficer->register_number) : 'PANGKAT NRP. ....................' }}
                    </p>
                </div>
            </div>
        </div>

    </div>
</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>

</html>
