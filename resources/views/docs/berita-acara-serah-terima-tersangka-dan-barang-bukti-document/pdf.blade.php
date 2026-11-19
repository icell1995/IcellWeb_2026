<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Berita Acara Serah Terima Tersangka Dan Barang Bukti - {{ $document->nomor ?? 'DOKUMEN' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.5cm 2cm 2cm 2cm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10.5pt;
            line-height: 1.4;
            color: #000;
        }
        .header-kop {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 6px;
            margin-bottom: 16px;
        }
        .header-kop h4 {
            margin: 0;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header-kop h5 {
            margin: 2px 0 0 0;
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header-kop p {
            margin: 3px 0 0 0;
            font-size: 8.5pt;
        }
        .doc-title {
            text-align: center;
            margin-bottom: 16px;
        }
        .doc-title h4 {
            margin: 0;
            font-size: 11.5pt;
            text-decoration: underline;
            font-weight: bold;
        }
        .doc-title p {
            margin: 3px 0 0 0;
            font-size: 10.5pt;
        }
        .intro {
            text-align: justify;
            margin-bottom: 12px;
        }
        .section-header {
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 4px;
            font-size: 10.5pt;
        }
        table.meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        table.meta-table td {
            vertical-align: top;
            padding: 2px 0;
            font-size: 10.5pt;
        }
        ol {
            margin: 4px 0;
            padding-left: 20px;
        }
        ol li {
            margin-bottom: 3px;
            font-size: 10.5pt;
        }
        .signature-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .signature-table td {
            vertical-align: top;
            text-align: center;
            width: 50%;
            font-size: 10.5pt;
        }
        .signature-space {
            height: 60px;
        }
    </style>
</head>
<body>
    @php
        $payload = is_array($document->payload) ? $document->payload : (json_decode($document->payload ?? '[]', true) ?: []);
        $polda = $payload['kop_daerah'] ?? ($accident->polres->polda->name ?? 'JAWA TIMUR');
        $resor = $payload['kop_resor'] ?? ($accident->polres->name ?? 'PASURUAN');
        $alamat = $payload['kop_alamat'] ?? ($accident->polres->address ?? '');
        $deliverer = ($document->officers ? $document->officers->where('class', 'DELIVERER')->first() : null) ?? ($document->delivererOfficer ?? null);
        $signatory = $document->authorizedSignatory ?? ($document->signatory ?? null);
    @endphp

    {{-- KOP SURAT --}}
    <div class="header-kop">
        <h4>KEPOLISIAN NEGARA REPUBLIK INDONESIA</h4>
        <h5>DAERAH {{ strtoupper($polda) }}</h5>
        <h5>RESOR {{ strtoupper($resor) }}</h5>
        @if ($alamat)
            <p>{{ $alamat }}</p>
        @endif
    </div>

    {{-- JUDUL --}}
    <div class="doc-title">
        <h4>BERITA ACARA SERAH TERIMA TERSANGKA DAN BARANG BUKTI</h4>
        <p>Nomor: {{ $document->nomor ?: ($document->document_number ?: '—') }}</p>
    </div>

    {{-- PEMBUKA --}}
    <div class="intro">
        Pada hari ini, <b>{{ $document->hari ?: ($document->tanggal ? \Carbon\Carbon::parse($document->tanggal)->locale('id')->translatedFormat('l') : '—') }}</b>, 
        tanggal <b>{{ $document->tanggal ? \Carbon\Carbon::parse($document->tanggal)->locale('id')->translatedFormat('d F Y') : ($document->document_date ? \Carbon\Carbon::parse($document->document_date)->locale('id')->translatedFormat('d F Y') : '—') }}</b>, 
        sekitar pukul <b>{{ $document->jam ?: '—' }}</b>, bertempat di <b>{{ $document->tempat ?: ('Kantor Kepolisian Resor ' . $resor) }}</b>, kami masing-masing:
    </div>

    {{-- PIHAK PERTAMA --}}
    <div class="section-header">I. PIHAK PERTAMA (YANG MENYERAHKAN):</div>
    <table class="meta-table">
        <tr>
            <td style="width: 150px;">Nama</td>
            <td style="width: 15px;">:</td>
            <td><b>{{ strtoupper($deliverer ? $deliverer->full_name : ($document->signatory_name ?? '—')) }}</b></td>
        </tr>
        <tr>
            <td>Pangkat / NRP</td>
            <td>:</td>
            <td>{{ $deliverer->rank->name ?? '-' }} / NRP: {{ $deliverer->register_number ?? '-' }}</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>:</td>
            <td>{{ $deliverer->position->name ?? 'Penyidik Pembantu' }}</td>
        </tr>
        <tr>
            <td>Kesatuan</td>
            <td>:</td>
            <td>Kepolisian Resor {{ $resor }}</td>
        </tr>
    </table>

    {{-- PIHAK KEDUA --}}
    <div class="section-header">II. PIHAK KEDUA (YANG MENERIMA):</div>
    <table class="meta-table">
        <tr>
            <td style="width: 150px;">Nama</td>
            <td style="width: 15px;">:</td>
            <td><b>{{ strtoupper($document->pihak_kedua_nama ?? '—') }}</b></td>
        </tr>
        <tr>
            <td>Pangkat / Golongan</td>
            <td>:</td>
            <td>{{ $document->pihak_kedua_pangkat ?: '-' }}</td>
        </tr>
        <tr>
            <td>NIP</td>
            <td>:</td>
            <td>{{ $document->pihak_kedua_nip ?: '-' }}</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>:</td>
            <td>{{ $document->pihak_kedua_jabatan ?: 'Jaksa Penuntut Umum' }}</td>
        </tr>
        <tr>
            <td>Instansi</td>
            <td>:</td>
            <td>{{ $document->pihak_kedua_instansi ?: ($document->nama_kejaksaan ?: 'Kejaksaan Negeri') }}</td>
        </tr>
    </table>

    {{-- DASAR SERAH TERIMA --}}
    <div class="section-header">III. DASAR SERAH TERIMA:</div>
    <ol>
        <li>Laporan Polisi Nomor: <b>{{ $accident->no_lp ?: '-' }}</b>, tanggal: {{ $accident->accident_date ? \Carbon\Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y') : '-' }};</li>
        @if ($document->nomor_spdp)
            <li>Surat Pemberitahuan Dimulainya Penyidikan (SPDP) Nomor: <b>{{ $document->nomor_spdp }}</b>{{ $document->tanggal_spdp ? ', tanggal: ' . \Carbon\Carbon::parse($document->tanggal_spdp)->locale('id')->translatedFormat('d F Y') : '' }};</li>
        @endif
        @if ($document->nomor_sprindik)
            <li>Surat Perintah Penyidikan Nomor: <b>{{ $document->nomor_sprindik }}</b>{{ $document->tanggal_sprindik ? ', tanggal: ' . \Carbon\Carbon::parse($document->tanggal_sprindik)->locale('id')->translatedFormat('d F Y') : '' }};</li>
        @endif
        @if ($document->nomor_surat_perintah_penahanan)
            <li>Surat Perintah Penahanan Nomor: <b>{{ $document->nomor_surat_perintah_penahanan }}</b>{{ $document->tanggal_surat_perintah_penahanan ? ', tanggal: ' . \Carbon\Carbon::parse($document->tanggal_surat_perintah_penahanan)->locale('id')->translatedFormat('d F Y') : '' }};</li>
        @endif
        @if ($document->nomor_p21)
            <li>Surat Kepala Kejaksaan Negeri perihal Hasil Penyidikan Perkara Pidana Sudah Lengkap (P-21) Nomor: <b>{{ $document->nomor_p21 }}</b>{{ $document->tanggal_p21 ? ', tanggal: ' . \Carbon\Carbon::parse($document->tanggal_p21)->locale('id')->translatedFormat('d F Y') : '' }}.</li>
        @endif
    </ol>

    {{-- TERSANGKA --}}
    <div class="section-header">IV. TERSANGKA YANG DISERAHKAN:</div>
    @if ($document->suspects && $document->suspects->isNotEmpty())
        @foreach ($document->suspects as $idx => $s)
            <div style="margin-left: 15px; margin-bottom: 6px;">
                <b>{{ $idx + 1 }}. {{ strtoupper($s->name) }}</b><br>
                <span style="font-size: 10pt;">
                    NIK: {{ $s->nik ?: '-' }} | Tempat/Tgl Lahir: {{ $s->birth_place ?: '-' }}, {{ $s->birth_date ? \Carbon\Carbon::parse($s->birth_date)->locale('id')->translatedFormat('d F Y') : '-' }}<br>
                    Jenis Kelamin: {{ $s->gender->name ?? ($s->gender_id == 1 ? 'Laki-laki' : 'Perempuan') }} | Agama: {{ $s->religion->name ?? '-' }} | Pekerjaan: {{ $s->job->name ?? '-' }}<br>
                    Alamat: {{ $s->address ?: '-' }}
                </span>
            </div>
        @endforeach
    @elseif (!empty($payload['nama_tersangka_manual']))
        <div style="margin-left: 15px; margin-bottom: 6px;">
            <b>1. {{ strtoupper($payload['nama_tersangka_manual']) }}</b><br>
            @if (!empty($payload['rincian_tersangka_manual']))
                <span style="font-size: 10pt; white-space: pre-line;">{{ $payload['rincian_tersangka_manual'] }}</span>
            @endif
        </div>
    @else
        <p style="margin-left: 15px; font-style: italic;">Sesuai Berkas Perkara.</p>
    @endif

    {{-- BARANG BUKTI --}}
    <div class="section-header">V. BARANG BUKTI YANG DISERAHKAN:</div>
    <div style="margin-left: 15px; white-space: pre-line; margin-bottom: 8px;">
        {{ $document->daftar_barang_bukti ?: 'Nihil / Sesuai Berkas Perkara.' }}
    </div>

    {{-- CATATAN --}}
    <div class="section-header">VI. CATATAN / KONDISI SERAH TERIMA:</div>
    <div style="margin-left: 15px; margin-bottom: 12px;">
        {{ $document->catatan ?: 'Tersangka dan barang bukti diserahkan dalam keadaan lengkap dan baik. Tersangka dalam kondisi sehat jasmani dan rohani.' }}
    </div>

    {{-- PENUTUP --}}
    <div class="intro">
        Demikian Berita Acara Serah Terima Tersangka dan Barang Bukti ini dibuat dengan sebenarnya atas kekuatan sumpah jabatan, kemudian ditutup dan ditandatangani di {{ $document->tempat ?: ('Kepolisian Resor ' . $resor) }} pada hari dan tanggal tersebut di atas.
    </div>

    {{-- TANDA TANGAN --}}
    <table class="signature-table">
        <tr>
            <td>
                Yang Menerima,<br>
                <b>PIHAK KEDUA</b>
                <div class="signature-space"></div>
                <b><u>{{ strtoupper($document->pihak_kedua_nama ?: '—') }}</u></b><br>
                <span>{{ $document->pihak_kedua_pangkat ? $document->pihak_kedua_pangkat . ' / ' : '' }}NIP: {{ $document->pihak_kedua_nip ?: '—' }}</span>
            </td>
            <td>
                Yang Menyerahkan,<br>
                <b>PIHAK PERTAMA</b>
                <div class="signature-space"></div>
                <b><u>{{ strtoupper($deliverer ? $deliverer->full_name : ($document->signatory_name ?: '—')) }}</u></b><br>
                <span>{{ $deliverer->rank->name ?? '-' }} / NRP: {{ $deliverer->register_number ?? '-' }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding-top: 25px;">
                {!! nl2br(e($document->signatory_head_text ?: ("Mengetahui,\na.n. KEPALA KEPOLISIAN RESOR " . strtoupper($resor)))) !!}<br>
                <b>{{ strtoupper($document->signatory_position ?: ($signatory->position->name ?? 'KASAT LANTAS')) }}</b>
                <div class="signature-space"></div>
                <b><u>{{ strtoupper($document->signatory_name ?: ($signatory->full_name ?? ($document->signatory ? PeopleNameHelper::getFullName($document->signatory->first_title, $document->signatory->first_name, $document->signatory->last_name, $document->signatory->last_title) : '—'))) }}</u></b><br>
                <span>{{ strtoupper($document->signatory_rank ?: ($signatory->rank->name ?? '-')) }} / NRP: {{ $document->signatory_nrp ?: ($signatory->register_number ?? '-') }}</span>
            </td>
        </tr>
    </table>
</body>
</html>

