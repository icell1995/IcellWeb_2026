<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Berita Acara Serah Terima Tersangka Dan Barang Bukti — {{ $deliveryLetterNumber ?? ($document->nomor ?? 'Dokumen') }}</title>
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
            margin-bottom: 6pt;
            letter-spacing: 0.5px;
        }

        /* Judul Dokumen */
        .title-doc {
            text-align: center;
            font-family: "Arial Narrow", Arial, sans-serif;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 4pt;
            margin-bottom: 12pt;
            font-size: 9.5pt;
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

        .dash-line {
            letter-spacing: -1px;
            color: #111;
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
                <span class="text-muted small">Berita Acara Serah Terima Tersangka Dan Barang Bukti (Format Word Resmi)</span>
            </div>
            <div class="d-flex align-items-center">
                <a href="{{ route('doc.berita-acara-serah-terima-tersangka-dan-barang-bukti-document.download', ['id' => $document->id]) }}" class="btn btn-sm btn-success me-2 fw-bold shadow-sm">
                    <i class="bi bi-file-earmark-word me-1"></i> Unduh File Word (.docx)
                </a>
                <a href="{{ route('doc.berita-acara-serah-terima-tersangka-dan-barang-bukti-document.edit', ['id' => $document->id, 'accident_id' => $document->accident_id]) }}" class="btn btn-sm btn-warning me-2 text-dark">
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

            {{-- 3. JUDUL DOKUMEN --}}
            <div class="title-doc">
                BERITA ACARA SERAH TERIMA TERSANGKA DAN BARANG BUKTI
            </div>

            {{-- PARAGRAF 1: WAKTU & PENYIDIK --}}
            <div class="justify-text" style="margin-bottom: 4pt;">
                <span class="dash-line">-----</span> Pada hari ini <b>{{ $documentDayName }}</b> tanggal <b>{{ $documentDayDate }}</b> bulan <b>{{ $documentMonth }}</b> tahun <b>{{ $documentYear }}</b> ({{ $documentDateText }}), pukul <b>{{ $documentTime }}</b> WIB, saya: <span class="dash-line">--------------------------------------------------------------------------------</span>
            </div>

            <div class="justify-text" style="margin-bottom: 4pt;">
                <b>{{ $investigatorName }}</b>, pangkat <b>{{ $investigatorRankName }}</b>, NRP <b>{{ $investigatorRegisterNumber }}</b>, jabatan selaku <b>{{ $investigatorPositionName }}</b> pada kantor Kepolisian tersebut di atas, bersama-sama dengan: <span class="dash-line">------------------------------------------------------------------</span>
            </div>

            {{-- ANGGOTA PENDAMPING (DINAMIS) --}}
            @php
                $renderOfficers = !empty($accompanyingOfficerList) ? $accompanyingOfficerList : [
                    ['name' => $officer1Name, 'rank_name' => $officer1RankName, 'register_number' => $officer1RegisterNumber, 'position_name' => $officer1PositionName]
                ];
            @endphp
            <table class="word-table" style="margin-bottom: 4pt;">
                @foreach ($renderOfficers as $oIdx => $off)
                    <tr>
                        <td style="width: 18pt;">{{ $oIdx + 1 }}.</td>
                        <td style="width: 80pt;">nama</td>
                        <td style="width: 12pt;">:</td>
                        <td style="width: 240pt;"><b>{{ $off['name'] ?? '-' }}</b>;</td>
                        <td style="text-align: right;">( .............................. )</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>pangkat/NRP</td>
                        <td>:</td>
                        <td colspan="2">{{ $off['rank_name'] ?? '-' }} NRP {{ $off['register_number'] ?? '-' }};</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>jabatan</td>
                        <td>:</td>
                        <td colspan="2">{{ $off['position_name'] ?? '-' }};</td>
                    </tr>
                @endforeach
            </table>

            {{-- PARAGRAF 2: RUJUKAN P21 & SURAT PENGANTAR --}}
            <div class="justify-text" style="margin-bottom: 4pt;">
                masing-masing dari kantor yang sama, berdasarkan: <span class="dash-line">----------------------------------------------------------------------------------------------------</span>
            </div>

            <table class="word-table" style="margin-bottom: 4pt;">
                <tr>
                    <td style="width: 18pt;">1.</td>
                    <td class="justify-text">
                        Surat <b>{{ $prosecutorInstitutionName }}</b> Nomor: <b>{{ $p21DocumentNumber }}</b> tanggal <b>{{ $p21DocumentDate }}</b> perihal pemberitahuan berkas perkara Tersangka atas nama <b>{{ $suspectName }}</b> sudah lengkap (P.21); <span class="dash-line">-----------------------------------------------------------------------------------</span>
                    </td>
                </tr>
                <tr>
                    <td>2.</td>
                    <td class="justify-text">
                        Surat <b>{{ $delivererHeadTitle }}</b> Nomor: <b>{{ $deliveryLetterNumber }}</b> tanggal <b>{{ $deliveryLetterDate }}</b> perihal pengiriman tersangka dan barang bukti atas nama <b>{{ $suspectName }}</b>. <span class="dash-line">-----------------------------------------------------------------------------------------------------------------</span>
                    </td>
                </tr>
            </table>

            {{-- PARAGRAF 3: PENYERAHAN TERSANGKA & BERKAS --}}
            <div class="justify-text" style="margin-bottom: 4pt;">
                Telah menyerahkan Tersangka atas nama <b>{{ $suspectName }}</b> sebagai berikut: <span class="dash-line">------------------------------------------------------------------------------</span>
            </div>

            <table class="word-table" style="margin-bottom: 4pt;">
                <tr>
                    <td style="width: 18pt;">1.</td>
                    <td class="justify-text">
                        Tersangka atas nama <b>{{ $suspectName }}</b> sebagaimana terlampir dalam daftar tersangka pada berkas perkara Nomor: <b>{{ $berkasNumber }}</b> tanggal <b>{{ $berkasDate }}</b>; <span class="dash-line">---------------------------------------------------------------------------------------------------------------------------</span>
                    </td>
                </tr>
                @if ($suspect2Line && $suspect2Line != '-')
                    <tr>
                        <td>2.</td>
                        <td class="justify-text">{{ $suspect2Line }}</td>
                    </tr>
                @endif
            </table>

            <div class="justify-text" style="margin-bottom: 4pt;">
                dan barang bukti dalam keadaan lengkap dan baik sebagaimana terlampir dalam daftar barang bukti pada berkas perkara Nomor: <b>{{ $berkasNumber }}</b> tanggal <b>{{ $berkasDate }}</b>, kepada Penuntut Umum yang diterima oleh: <span class="dash-line">--------------------------------------------------------------------</span>
            </div>

            {{-- PARAGRAF 4: PENUNTUT UMUM --}}
            <table class="word-table" style="margin-bottom: 4pt;">
                <tr>
                    <td style="width: 18pt;"></td>
                    <td style="width: 80pt;">nama</td>
                    <td style="width: 12pt;">:</td>
                    <td style="width: 240pt;"><b>{{ $prosecutorOfficerName }}</b>;</td>
                    <td style="text-align: right;">( .............................. )</td>
                </tr>
                <tr>
                    <td></td>
                    <td>pangkat/NIP</td>
                    <td>:</td>
                    <td colspan="2">{{ $prosecutorOfficerRankNip }};</td>
                </tr>
                <tr>
                    <td></td>
                    <td>jabatan</td>
                    <td>:</td>
                    <td colspan="2">{{ $prosecutorOfficerPositionName }};</td>
                </tr>
            </table>

            {{-- PARAGRAF 5: PERKARA & TKP --}}
            <div class="justify-text" style="margin-bottom: 4pt;">
                dalam perkara dugaan Tindak Pidana <b>{{ $dugaanTindakPidana }}</b> sebagaimana dimaksud dalam Pasal <b>{{ $pasalDiduga }}</b> UU Nomor <b>{{ $uuNomor }}</b> Tahun <b>{{ $uuTahun }}</b> tentang <b>{{ $uuTentang }}</b>, yang terjadi di <b>{{ $tempatKejadian }}</b>, <b>{{ $waktuKejadian }}</b>, dengan Tersangka atas nama <b>{{ $suspectName }}</b>. <span class="dash-line">-------------------</span>
            </div>

            <div class="justify-text" style="margin-bottom: 4pt;">
                Serah terima dilakukan pada hari <b>{{ $handoverDay }}</b> tanggal <b>{{ $handoverDate }}</b> di <b>{{ $handoverPlace }}</b>, dengan disaksikan oleh: <span class="dash-line">---------------------------------</span>
            </div>

            {{-- SAKSI-SAKSI DINAMIS --}}
            <table class="word-table" style="margin-bottom: 4pt;">
                @foreach ($witnessList as $wIdx => $wit)
                    <tr>
                        <td style="width: 18pt;">{{ $wIdx + 1 }}.</td>
                        <td style="width: 80pt;">nama</td>
                        <td style="width: 12pt;">:</td>
                        <td style="width: 240pt;"><b>{{ $wit['name'] ?: '..............................' }}</b>;</td>
                        <td style="text-align: right;">( .............................. )</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>pangkat/NIP</td>
                        <td>:</td>
                        <td colspan="2">{{ $wit['rank_nip'] ?: '-' }};</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>jabatan</td>
                        <td>:</td>
                        <td colspan="2">{{ $wit['position_name'] ?: '-' }};</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>alamat</td>
                        <td>:</td>
                        <td colspan="2">{{ $wit['address'] ?: '-' }};</td>
                    </tr>
                @endforeach
            </table>

            {{-- PARAGRAF 7: PELAKSANAAN --}}
            <div class="justify-text" style="margin-bottom: 4pt;">
                Adapun pelaksanaannya sebagai berikut: {{ $executionDescription }} <span class="dash-line">---------------------------------------------------------------------------------------</span>
            </div>

            {{-- PARAGRAF 8: PENUTUP --}}
            <div class="justify-text" style="margin-bottom: 15pt;">
                <span class="dash-line">-----</span> Demikian Berita Acara Serah Terima Tersangka dan Barang Bukti ini dibuat dengan sebenarnya atas kekuatan sumpah jabatan, kemudian ditutup dan ditandatangani di <b>{{ $documentLocation }}</b> pada hari, tanggal, bulan dan tahun tersebut di atas. <span class="dash-line">----------------------------------------</span>
            </div>

            {{-- TANDA TANGAN (YANG MENERIMA & YANG MENYERAHKAN) --}}
            <table class="word-table" style="width: 100%; text-align: center; margin-bottom: 12pt;">
                <tr>
                    <td style="width: 50%; vertical-align: top;">
                        Yang menerima,<br><br><br><br>
                        <b><u>{{ $prosecutorOfficerName }}</u></b><br>
                        <span>{{ $prosecutorOfficerRankNip }}</span>
                    </td>
                    <td style="width: 50%; vertical-align: top;">
                        Yang menyerahkan,<br>
                        <b>{{ $investigatorRole }}</b><br><br><br>
                        <b><u>{{ $investigatorName }}</u></b><br>
                        <span>{{ $investigatorRankName }} NRP {{ $investigatorRegisterNumber }}</span>
                    </td>
                </tr>
            </table>

            {{-- TANDA TANGAN SAKSI-SAKSI --}}
            <div style="text-align: center; margin-bottom: 4pt;">
                Saksi-saksi:
            </div>

            <table class="word-table" style="width: 250pt; margin: 0 auto; text-align: left;">
                @foreach ($witnessList as $wIdx => $wit)
                    <tr>
                        <td style="width: 18pt;">{{ $wIdx + 1 }}.</td>
                        <td>{{ $wit['name'] ?: '-' }}</td>
                        <td style="text-align: right;">( .............................. )</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</body>
</html>
