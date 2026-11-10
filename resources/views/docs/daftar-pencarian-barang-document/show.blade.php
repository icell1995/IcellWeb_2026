<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Pencarian Barang (DPB) — {{ $documentNumber ?? ($document->document_number ?? 'Dokumen') }}</title>
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
            font-size: 8.5pt;
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
            font-size: 8.5pt;
            line-height: 1.2;
            color: #000000;
        }

        /* Kop Surat Polri di Kiri Atas */
        .kop-header {
            width: 280pt;
            text-align: center;
            margin-bottom: 2pt;
        }

        .kop-header-text {
            font-family: "Arial Narrow", Arial, sans-serif;
            font-size: 9pt;
            font-weight: bold;
            line-height: 1.15;
            text-transform: uppercase;
        }

        .kop-address {
            font-weight: normal;
            font-size: 8pt;
            text-transform: none;
            line-height: 1.15;
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
            margin-top: 4pt;
            margin-bottom: 6pt;
            letter-spacing: 0.5px;
        }

        /* Judul Dokumen & Logo */
        .doc-center-title {
            text-align: center;
            margin-top: 2pt;
            margin-bottom: 6pt;
        }

        .doc-logo {
            width: 48pt;
            height: auto;
            display: block;
            margin: 0 auto 3pt auto;
        }

        .doc-title-main {
            font-size: 10pt;
            font-weight: bold;
            text-decoration: underline;
            letter-spacing: 0.5px;
        }

        .doc-title-sub {
            font-size: 9pt;
            margin-top: 1pt;
        }

        /* Tabel Keterangan Barang (Bordered) */
        table.dpb-table {
            width: 100%;
            border-collapse: collapse;
            font-family: "Arial Narrow", Arial, sans-serif;
            font-size: 8.5pt;
            line-height: 1.2;
            color: #000;
            border: 1px solid #000000;
            margin-top: 2pt;
            margin-bottom: 6pt;
        }

        table.dpb-table th, table.dpb-table td {
            border: 1px solid #000000;
            padding: 1.5pt 3pt;
            vertical-align: top;
        }

        /* Tabel Layout Bebas Border */
        table.word-table {
            width: 100%;
            border-collapse: collapse;
            font-family: "Arial Narrow", Arial, sans-serif;
            font-size: 8.5pt;
            line-height: 1.2;
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
                <span class="text-muted small">Daftar Pencarian Barang / DPB (Format Word Resmi)</span>
            </div>
            <div class="d-flex align-items-center">
                <a href="{{ route('doc.daftar-pencarian-barang-document.download', ['id' => $document->id, 'accident_id' => $document->accident_id]) }}" class="btn btn-sm btn-success me-2 fw-bold shadow-sm">
                    <i class="bi bi-file-earmark-word me-1"></i> Unduh File Word (.docx)
                </a>
                <a href="{{ route('doc.daftar-pencarian-barang-document.edit', ['id' => $document->id, 'accident_id' => $document->accident_id]) }}" class="btn btn-sm btn-warning me-2 text-dark">
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
                            DAERAH {{ preg_replace('/^(DAERAH|POLDA)\s+/i', '', $daerahPoliceFullName) }}<br>
                            {{ $resorPoliceFullName }}<br>
                            <span class="kop-address">{{ $resorPoliceAddress }}</span>
                        </div>
                        <div class="kop-line"></div>
                    </div>

                    {{-- 2. "PRO JUSTITIA" --}}
                    <div class="pro-justitia">
                        "PRO JUSTITIA"
                    </div>

                    {{-- 3. LOGO TRIBRATA & JUDUL RESMI DPB --}}
                    <div class="doc-center-title">
                        <img src="{{ asset('images/doc/logo-tribrata-polri.png') }}" alt="Tribrata Polri" class="doc-logo">
                        <div class="doc-title-main">DAFTAR PENCARIAN BARANG</div>
                        <div class="doc-title-sub">Nomor: {{ $documentNumber }}</div>
                    </div>

                    {{-- 4. DASAR RUJUKAN & NARASI PENGANTAR --}}
                    <div style="margin-bottom: 2pt;">Berdasarkan:</div>
                    <table class="word-table" style="margin-bottom: 4pt;">
                        <tr>
                            <td style="width: 14pt;">1.</td>
                            <td>Laporan Polisi Nomor: {{ $accidentNumber }} tanggal {{ $accidentDate }};</td>
                        </tr>
                        <tr>
                            <td>2.</td>
                            <td>{{ $courtPermitText }}</td>
                        </tr>
                    </table>

                    <div class="justify-text" style="line-height: 1.25; margin-bottom: 6pt;">
                        Bahwa barang bukti tersebut di bawah ini dicari karena diduga dipergunakan/dibuat khusus untuk tindak pidana, atau hasil dari tindak pidana, atau untuk menghalang-halangi penyidikan, atau ada hubungan langsung dengan dugaan Tindak Pidana {{ $dugaanTindakPidana }} sebagaimana dimaksud dalam Pasal {{ $pasalDiduga }} UU Nomor {{ $uuNomor }} Tahun {{ $uuTahun }} tentang {{ $uuTentang }}, yang terjadi di {{ $tempatKejadian }}, {{ $waktuKejadian }} dan apabila ditemukan agar diserahkan kepada {{ $contactOfficerRole }} atas nama {{ $contactOfficerName }} dengan menghubungi nomor Hp. {{ $contactOfficerPhone }} atau dapat menghubungi/mendatangi kantor Kepolisian tersebut di atas.
                    </div>

                    {{-- 5. TABEL KETERANGAN BARANG (12 BUTIR RESMI) --}}
                    <div style="font-weight: bold; text-align: center; margin-bottom: 2pt; font-size: 9pt;">KETERANGAN</div>
                    <table class="dpb-table">
                        <tr>
                            <td style="width: 22pt; text-align: center;">1.</td>
                            <td style="width: 145pt;">Nama Barang</td>
                            <td style="width: 8pt; text-align: center;">:</td>
                            <td>{{ $itemName }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">2.</td>
                            <td>Jumlah</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $quantity }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">3.</td>
                            <td>Tanda-tanda khusus</td>
                            <td style="text-align: center;">:</td>
                            <td>
                                <div>a.&nbsp;&nbsp;Nomor registrasi : {{ $registrationNumber }} ;</div>
                                <div>b.&nbsp;&nbsp;Isi silinder : {{ $cylinderCapacity }} ;</div>
                                <div>c.&nbsp;&nbsp;Bahan bakar : {{ $fuelType }} ;</div>
                                <div>d.&nbsp;&nbsp;Warna TNKB : {{ $tnkbColor }} ;</div>
                                <div>e.&nbsp;&nbsp;Tahun registrasi : {{ $registrationYear }} ;</div>
                                <div>f.&nbsp;&nbsp;Nomor BPKB : {{ $bpkbNumber }} ;</div>
                                <div>g.&nbsp;&nbsp;{{ $specialSignsOther }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">4.</td>
                            <td>Ciri-ciri (Tipe, Ukuran, dll)</td>
                            <td style="text-align: center;">:</td>
                            <td>
                                <div>a.&nbsp;&nbsp;Tipe : {{ $itemType }} ;</div>
                                <div>b.&nbsp;&nbsp;Jenis : {{ $itemCategory }} ;</div>
                                <div>c.&nbsp;&nbsp;Model : {{ $itemModel }} ;</div>
                                <div>d.&nbsp;&nbsp;{{ $physicalFeaturesOther }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">5.</td>
                            <td>Warna</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $itemColor }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">6.</td>
                            <td>Tahun pembuatan</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $manufactureYear }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">7.</td>
                            <td>Taksiran harga</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $estimatedPrice }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">8.</td>
                            <td>Merek dagang</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $tradeMark }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">9.</td>
                            <td>Nomor pembuatan</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $engineFrameNumber }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">10.</td>
                            <td>Tempat pembuatan</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $manufacturePlace }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">11.</td>
                            <td>Cara hilangnya</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $lossChronology }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">12.</td>
                            <td>Keterangan lain yang penting</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $importantNotes }}</td>
                        </tr>
                    </table>

                    {{-- 6. BAGIAN BAWAH: KEPADA & PENANDATANGAN (2 Kolom Presisi Sesuai Template Word) --}}
                    <table class="word-table" style="width: 100%; margin-top: 8pt; table-layout: fixed;">
                        <colgroup>
                            <col style="width: 259.2pt;">
                            <col style="width: 272.4pt;">
                        </colgroup>
                        <tr>
                            {{-- Kepada (Kiri, Lebar Penuh 259.2pt) --}}
                            <td style="vertical-align: top; padding: 0;">
                                <div style="font-weight: bold; margin-bottom: 3pt;">Kepada:</div>
                                <table class="word-table" style="width: 100%; table-layout: fixed;">
                                    <colgroup>
                                        <col style="width: 16pt;">
                                        <col style="width: 243.2pt;">
                                    </colgroup>
                                    <tr>
                                        <td style="vertical-align: top; padding: 0.5pt 0;">1.</td>
                                        <td style="vertical-align: top; padding: 0.5pt 0; text-align: left;">Direskrimum/Sus/Narkoba jajaran.</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top; padding: 0.5pt 0;">2.</td>
                                        <td style="vertical-align: top; padding: 0.5pt 0; text-align: left;">Kapolres jajaran.</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top; padding: 0.5pt 0;">3.</td>
                                        <td style="vertical-align: top; padding: 0.5pt 0; text-align: left;">{{ $kepada_3 }}</td>
                                    </tr>
                                </table>
                            </td>

                            {{-- Penandatangan (Kanan, Lebar Penuh 272.4pt) --}}
                            <td style="vertical-align: top; text-align: center; padding: 0;">
                                <div>{{ $documentLocation }}, {{ $documentDate }}</div>
                                <div style="font-weight: bold; white-space: pre-line;">{{ $signatoryHeadText }}</div>
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
