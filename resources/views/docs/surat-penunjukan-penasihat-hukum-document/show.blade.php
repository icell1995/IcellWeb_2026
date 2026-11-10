<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Surat Penunjukan Penasihat Hukum — {{ $documentNumber ?? ($document->document_number ?? 'Dokumen') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* Tampilan Background Layar Kerja Word */
        body {
            background-color: #525659;
            color: #000;
            margin: 0;
            padding: 0;
            font-family: "Arial Narrow", Arial, "Liberation Sans", sans-serif;
            font-size: 9pt;
            -webkit-font-smoothing: antialiased;
        }

        /* Lembar Kertas Dokumen Persis Folio / F4 Word */
        .doc-page-container {
            padding: 20px 0 40px 0;
            display: flex;
            justify-content: center;
        }

        .doc-page {
            width: 215mm;
            min-height: 330mm;
            background: #ffffff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.35);
            padding: 25.2pt 57.6pt;
            box-sizing: border-box;
            position: relative;
            font-family: "Arial Narrow", Arial, "Liberation Sans", sans-serif;
            font-size: 9pt;
            line-height: 1.25;
            color: #000000;
        }

        /* Kop Surat Polri di Kiri Atas */
        .kop-header {
            width: 275pt;
            text-align: center;
            margin-bottom: 2pt;
        }

        .kop-header-text {
            font-family: "Arial Narrow", Arial, sans-serif;
            font-size: 9.5pt;
            font-weight: bold;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .kop-address {
            font-weight: normal;
            font-size: 8.5pt;
            text-transform: none;
            line-height: 1.2;
        }

        .kop-line {
            border-bottom: 1.5px solid #000000;
            margin-top: 2pt;
            width: 100%;
        }

        /* Pro Justitia */
        .pro-justitia {
            font-family: "Arial Narrow", Arial, sans-serif;
            font-size: 9pt;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 4pt;
            margin-bottom: 8pt;
            letter-spacing: 0.5px;
        }

        /* Tabel Layout Bebas Border */
        table.word-table {
            width: 100%;
            border-collapse: collapse;
            font-family: "Arial Narrow", Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.25;
            color: #000;
            border: none;
            margin: 0;
            padding: 0;
        }

        table.word-table td {
            vertical-align: top;
            padding: 0.5pt 0;
            border: none;
        }

        .justify-text {
            text-align: justify;
            text-justify: inter-word;
        }

        /* Pengaturan Print / Cetak Langsung */
        @media print {
            body {
                background: #fff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .doc-page-container {
                padding: 0 !important;
                display: block !important;
            }
            .doc-page {
                box-shadow: none !important;
                border: none !important;
                padding: 25.2pt 57.6pt !important;
                margin: 0 !important;
                width: 100% !important;
                min-height: auto !important;
            }
        }
    </style>
</head>
<body>
    {{-- Bar Navigasi Atas --}}
    <div class="bg-white border-bottom py-2 px-4 shadow-sm no-print sticky-top">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="{{ route('view_produktivitas_accident', ['accident_id' => $document->accident_id]) }}" class="btn btn-sm btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Perkara
                </a>
                <span class="fw-bold text-dark me-2">Pratinjau Dokumen:</span>
                <span class="text-muted small">Surat Penunjukan Penasihat Hukum (Format Word Resmi)</span>
            </div>
            <div class="d-flex align-items-center">
                <a href="{{ route('doc.surat-penunjukan-penasihat-hukum-document.download', ['id' => $document->id]) }}" class="btn btn-sm btn-success me-2 fw-bold shadow-sm">
                    <i class="bi bi-file-earmark-word me-1"></i> Unduh File Word (.docx)
                </a>
                <a href="{{ route('doc.surat-penunjukan-penasihat-hukum-document.edit', ['id' => $document->id, 'accident_id' => $document->accident_id]) }}" class="btn btn-sm btn-warning me-2 text-dark">
                    <i class="bi bi-pencil-square me-1"></i> Edit Data
                </a>
                <button onclick="window.print()" class="btn btn-sm btn-primary">
                    <i class="bi bi-printer me-1"></i> Cetak Dokumen
                </button>
            </div>
        </div>
    </div>

    {{-- Tampilan Dokumen Persis Word F4 --}}
    <div class="doc-page-container">
                <div class="doc-page">

                    {{-- 1. KOP SURAT KEPOLISIAN (Header Kiri Atas) --}}
                    <div class="kop-header">
                        <div class="kop-header-text">
                            KEPOLISIAN NEGARA REPUBLIK INDONESIA<br>
                            DAERAH {{ $daerahPoliceFullName }}<br>
                            {{ $resorPoliceFullName }}<br>
                            <span class="kop-address">{{ $resorPoliceAddress }}</span>
                        </div>
                        <div class="kop-line"></div>
                    </div>

                    {{-- 2. "PRO JUSTITIA" --}}
                    <div class="pro-justitia">
                        "PRO JUSTITIA"
                    </div>

                    {{-- 3. TABEL 0: METADATA & ALAMAT TUJUAN (2 Kolom Word) --}}
                    <table class="word-table" style="margin-bottom: 6pt;">
                        <tr>
                            {{-- Kolom Kiri: Metadata Surat --}}
                            <td style="width: 259.2pt;">
                                <table class="word-table">
                                    <tr>
                                        <td style="width: 58pt;">Nomor</td>
                                        <td style="width: 8pt; text-align: center;">:</td>
                                        <td>{{ $documentNumber }}</td>
                                    </tr>
                                    <tr>
                                        <td>Klasifikasi</td>
                                        <td style="text-align: center;">:</td>
                                        <td>{{ $documentClassificationName }}</td>
                                    </tr>
                                    <tr>
                                        <td>Lampiran</td>
                                        <td style="text-align: center;">:</td>
                                        <td>{{ $appendix }}</td>
                                    </tr>
                                    <tr>
                                        <td>Perihal</td>
                                        <td style="text-align: center;">:</td>
                                        <td style="text-decoration: underline;">penunjukan penasehat hukum.</td>
                                    </tr>
                                </table>
                            </td>

                            {{-- Kolom Kanan: Tempat, Tanggal & Tujuan Surat --}}
                            <td style="width: 272.4pt; padding-left: 10pt;">
                                <div>{{ $documentLocation }}, {{ $documentDate }}</div>
                                <div style="margin-top: 8pt;">Kepada</div>
                                <div style="margin-top: 6pt;">Yth.&nbsp;&nbsp;&nbsp;{{ $targetLegalAidName }}</div>
                                <div style="margin-top: 6pt;">di</div>
                                <div style="margin-top: 6pt; text-decoration: underline;">{{ $targetLegalAidLocation }}</div>
                            </td>
                        </tr>
                    </table>

                    {{-- 4. TABEL 1: ISI SURAT RESMI (Persis Susunan Word Poin 1 s/d 4) --}}
                    <table class="word-table" style="width: 100%; table-layout: fixed;">
                        <colgroup>
                            <col style="width: 21.6pt;">
                            <col style="width: 25.2pt;">
                            <col style="width: 130pt;">
                            <col style="width: 10pt;">
                            <col style="width: 344.8pt;">
                        </colgroup>

                        {{-- POIN 1: RUJUKAN --}}
                        <tr>
                            <td style="width: 21.6pt;">1.</td>
                            <td colspan="4">Rujukan:</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="width: 25.2pt;">a.</td>
                            <td colspan="3" class="justify-text">{{ $dasarKuhap }};</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>b.</td>
                            <td colspan="3" class="justify-text">Undang-Undang Nomor 2 Tahun 2002 tentang Kepolisian Negara Republik Indonesia;</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>c.</td>
                            <td colspan="3" class="justify-text">{{ $undangUndangLain }};</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>d.</td>
                            <td colspan="3" class="justify-text">Laporan Polisi Nomor: {{ $accidentNumber }} tanggal {{ $accidentDate }};</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>e.</td>
                            <td colspan="3" class="justify-text">Surat Perintah Penyidikan Nomor: {{ $suratPerintahPenyidikanDocumentNumber }} tanggal {{ $suratPerintahPenyidikanDocumentDate }};</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>f.</td>
                            <td colspan="3" class="justify-text">Surat Kepala {{ $suratKeteranganTidakMampuInstansi }} Nomor: {{ $suratKeteranganTidakMampuNumber }} tanggal {{ $suratKeteranganTidakMampuDate }} perihal Keterangan Tidak mampu atas nama {{ $suratKeteranganTidakMampuPersonName }}.</td>
                        </tr>

                        {{-- POIN 2: PENGANTAR PENUNJUKAN & IDENTITAS TERSANGKA --}}
                        <tr>
                            <td style="padding-top: 4pt;">2.</td>
                            <td colspan="4" class="justify-text" style="padding-top: 4pt;">
                                Sehubungan dengan rujukan di atas, untuk kepentingan pemeriksaan dan selama dalam pemeriksaan, diminta Ketua dapat menunjuk seorang Penasehat Hukum untuk mendampingi Tersangka dengan identitas sebagai berikut:
                            </td>
                        </tr>

                        {{-- Sub-poin a s/d h Tersangka --}}
                        <tr>
                            <td></td>
                            <td>a.</td>
                            <td>nama</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $suspectName }};</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>b.</td>
                            <td>nomor identitas</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $suspectIdentityNumber }};</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>c.</td>
                            <td>kewarganegaraan</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $suspectNationality }};</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>d.</td>
                            <td>jenis kelamin</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $suspectGenderName }};</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>e.</td>
                            <td>tempat/tanggal lahir</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $suspectBirthPlace }}, {{ $suspectBirthDate }};</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>f.</td>
                            <td>pekerjaan</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $suspectJobName }};</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>g.</td>
                            <td>agama</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $suspectReligionName }};</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>h.</td>
                            <td>alamat</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $suspectFullAddress }}.</td>
                        </tr>

                        {{-- Paragraf Perkara --}}
                        <tr>
                            <td></td>
                            <td colspan="4" class="justify-text" style="padding-top: 2pt;">
                                dalam perkara dugaan Tindak Pidana {{ $dugaanTindakPidana }} sebagaimana dimaksud dalam Pasal {{ $pasalDiduga }} UU Nomor {{ $uuNomor }} Tahun {{ $uuTahun }} tentang {{ $uuTentang }}, yang terjadi di {{ $tempatKejadian }}, {{ $waktuKejadian }}{{ ($kurunWaktu && $kurunWaktu != '-') ? ' / dalam kurun waktu ' . $kurunWaktu : '' }}, yang diduga dilakukan oleh Tersangka.
                            </td>
                        </tr>

                        {{-- POIN 3: JADWAL & NARAHUBUNG --}}
                        <tr>
                            <td style="padding-top: 4pt;">3.</td>
                            <td colspan="4" class="justify-text" style="padding-top: 4pt;">
                                Pemeriksaan akan dilakukan pada hari {{ $examinationDay }} tanggal {{ $examinationDate }} pukul {{ $examinationTime }} WIB pada kantor {{ $examinationOfficeName }} yang beralamat di {{ $examinationOfficeAddress }}. Untuk memudahkan koordinasi dapat menghubungi {{ $contactOfficerRole }} atas nama {{ $contactOfficerName }} dengan nomor Hp. {{ $contactOfficerPhone }}.
                            </td>
                        </tr>

                        {{-- POIN 4: PENUTUP --}}
                        <tr>
                            <td style="padding-top: 4pt; padding-bottom: 4pt;">4.</td>
                            <td colspan="4" style="padding-top: 4pt; padding-bottom: 4pt;">
                                Demikian untuk menjadi maklum.
                            </td>
                        </tr>
                    </table>

                    {{-- 5. BAGIAN BAWAH: TEMBUSAN & PENANDATANGAN (2 Kolom Presisi Sesuai Template Word) --}}
                    <table class="word-table" style="width: 100%; margin-top: 10pt; table-layout: fixed;">
                        <colgroup>
                            <col style="width: 259.2pt;">
                            <col style="width: 272.4pt;">
                        </colgroup>
                        <tr>
                            {{-- Tembusan (Kiri, Lebar Penuh 259.2pt) --}}
                            <td style="vertical-align: top; padding: 0;">
                                <div style="text-decoration: underline; margin-bottom: 3pt;">Tembusan:</div>
                                <table class="word-table" style="width: 100%; table-layout: fixed;">
                                    <colgroup>
                                        <col style="width: 16pt;">
                                        <col style="width: 243.2pt;">
                                    </colgroup>
                                    <tr>
                                        <td style="vertical-align: top; padding: 0.5pt 0;">1.</td>
                                        <td style="vertical-align: top; padding: 0.5pt 0; text-align: left;">{{ $carbon_copy_1 }}</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top; padding: 0.5pt 0;">2.</td>
                                        <td style="vertical-align: top; padding: 0.5pt 0; text-align: left;">{{ $carbon_copy_2 }}</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top; padding: 0.5pt 0;">3.</td>
                                        <td style="vertical-align: top; padding: 0.5pt 0; text-align: left;">{{ $carbon_copy_3 }}</td>
                                    </tr>
                                </table>
                            </td>

                            {{-- Tanda Tangan Pejabat (Kanan, Lebar Penuh 272.4pt) --}}
                            <td style="vertical-align: top; text-align: center; padding: 0;">
                                <div>{{ $signatoryHeadText }}</div>
                                <div>{{ $signatoryPositionName }}</div>
                                <div style="height: 50pt;"></div>
                                <div style="text-decoration: underline; font-weight: bold;">{{ $signatoryName }}</div>
                                <div>{{ $signatoryRankName }} NRP {{ $signatoryRegisterNumber }}</div>
                            </td>
                        </tr>
                    </table>

                </div>
            </div>
</body>
</html>
