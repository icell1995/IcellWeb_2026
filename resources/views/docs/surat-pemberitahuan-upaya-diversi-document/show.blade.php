<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Surat Pemberitahuan Upaya Diversi (SPUD) - {{ $spudDocument->document_number }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
    <style>
        .spud-sheet {
            background-color: #fff;
            max-width: 800px;
            margin: 20px auto;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            font-family: "Times New Roman", Times, serif;
            font-size: 14px;
            line-height: 1.6;
        }
        .header-kop {
            text-align: left;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 20px;
            line-height: 1.2;
        }
        .header-title {
            text-align: center;
            margin-bottom: 25px;
        }
        .header-title h4 {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 4px;
        }
    </style>
</head>
<body style="background-color: #f4f6f9;">
    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Kembali ke Berkas Perkara
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fa fa-print"></i> Cetak / Print
            </button>
        </div>

        <div class="spud-sheet">
            <div class="header-kop">
                <div>KEPOLISIAN NEGARA REPUBLIK INDONESIA</div>
                <div>DAERAH {{ strtoupper($accident->polres->polda->name ?? '') }}</div>
                <div>RESOR {{ strtoupper($accident->polres->name ?? '') }}</div>
            </div>

            <div class="header-title">
                <h4>SURAT PEMBERITAHUAN UPAYA DIVERSI</h4>
                <div>Nomor: {{ $spudDocument->document_number }}</div>
            </div>

            <table class="table table-borderless mb-3" style="width: 100%;">
                <tr>
                    <td style="width: 25%;">Klasifikasi</td>
                    <td style="width: 2%;">:</td>
                    <td>{{ $spudDocument->documentClassification->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Lampiran</td>
                    <td>:</td>
                    <td>{{ $spudDocument->appendix }} Lembar</td>
                </tr>
                <tr>
                    <td>Perihal</td>
                    <td>:</td>
                    <td><strong>Pemberitahuan Upaya Diversi terhadap Tersangka Anak</strong></td>
                </tr>
                <tr>
                    <td>Kepada Yth.</td>
                    <td>:</td>
                    <td><strong>{{ $spudDocument->prosecutor->name ?? '-' }}</strong></td>
                </tr>
                <tr>
                    <td>Tembusan</td>
                    <td>:</td>
                    <td>
                        @if (!empty($spudDocument->carbon_copies) && count($spudDocument->carbon_copies) > 0)
                            <ol class="mb-0 ps-3">
                                @foreach ($spudDocument->carbon_copies as $cc)
                                    <li>{{ $cc }}</li>
                                @endforeach
                            </ol>
                        @elseif ($spudDocument->court)
                            {{ $spudDocument->court->name }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </table>

            <hr>

            <div class="mb-3">
                <p>Bersama ini diberitahukan bahwa terhadap perkara kecelakaan lalu lintas:</p>
                <table class="table table-sm table-bordered">
                    <tr>
                        <th style="width: 30%;">Nomor LP</th>
                        <td>{{ $accident->no_lp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Surat Perintah Penyidikan</th>
                        <td>{{ $spudDocument->suratPerintahPenyidikanDocument->document_number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>SPDP</th>
                        <td>{{ $spudDocument->suratPemberitahuanDimulainyaPenyidikanDocument->document_number ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            <div class="mb-3">
                <p>Telah dilakukan upaya Diversi terhadap Tersangka Anak sebagai berikut:</p>
                @if($spudDocument->suspect)
                    @php
                        $suspect = $spudDocument->suspect;
                        $ageStr = '-';
                        if (!empty($spudDocument->messages['suspect_age'])) {
                            $ageStr = $spudDocument->messages['suspect_age'];
                        } elseif (!empty($suspect->birth_date)) {
                            try {
                                $target = !empty($spudDocument->document_date) ? \Carbon\Carbon::parse($spudDocument->document_date) : \Carbon\Carbon::now();
                                $diff = \Carbon\Carbon::parse($suspect->birth_date)->diff($target);
                                $ageStr = "{$diff->y} tahun {$diff->m} bulan {$diff->d} hari";
                            } catch (\Exception $e) {}
                        } elseif (!empty($suspect->age)) {
                            $ageStr = $suspect->age . ' tahun';
                        }
                    @endphp
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th style="width: 30%;">Nama Lengkap</th>
                            <td><strong>{{ $suspect->name }}</strong> (Anak)</td>
                        </tr>
                        <tr>
                            <th>NIK / No Identitas</th>
                            <td>{{ $suspect->identity_number ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Lahir / Usia</th>
                            <td>{{ $suspect->birth_date ? \Carbon\Carbon::parse($suspect->birth_date)->locale('id')->translatedFormat('d F Y') : '-' }} ({{ $ageStr }})</td>
                        </tr>
                        <tr>
                            <th>Jenis Kelamin</th>
                            <td>{{ $suspect->gender->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>{{ $suspect->address ?? '-' }}</td>
                        </tr>
                    </table>
                @else
                    <p class="text-muted">Data tersangka tidak ditemukan.</p>
                @endif
            </div>

            <div class="mt-4 row">
                <div class="col-6"></div>
                <div class="col-6 text-center">
                    <div>{{ $accident->polres->name ?? 'Tempat' }}, {{ \Carbon\Carbon::parse($spudDocument->document_date)->locale('id')->translatedFormat('d F Y') }}</div>
                    <div class="fw-bold mt-1">PENYIDIK / PENANDATANGAN</div>
                    <br><br><br><br>
                    @php
                        $signatory = $spudDocument->suratPemberitahuanUpayaDiversiDocumentOfficers->first();
                    @endphp
                    <div class="fw-bold text-decoration-underline">{{ $signatory ? $signatory->first_name . ' ' . $signatory->last_name : '-' }}</div>
                    <div>{{ $signatory ? ($signatory->rank->name ?? '') . ' NRP. ' . $signatory->register_number : '' }}</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
