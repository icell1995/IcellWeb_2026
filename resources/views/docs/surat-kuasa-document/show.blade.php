@php
    $_title = 'Lihat Surat Kuasa Penasehat Hukum';
@endphp

@extends('layouts.app')

@push('style')
    <style>
        .doc-paper {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            padding: 50px 60px;
            max-width: 850px;
            margin: 20px auto 40px auto;
            font-family: 'Bookman Old Style', 'Times New Roman', Times, serif;
            color: #000;
            line-height: 1.5;
            font-size: 14px;
        }
        .doc-title {
            text-align: center;
            margin-bottom: 25px;
        }
        .doc-title h4 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .text-justify {
            text-align: justify;
        }
        .table-data {
            width: 100%;
            margin-bottom: 6px;
        }
        .table-data td {
            vertical-align: top;
            padding: 2px 0;
        }
        .table-data td.lbl {
            width: 180px;
        }
        .table-data td.sep {
            width: 20px;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            .doc-paper, .doc-paper * {
                visibility: visible;
            }
            .doc-paper {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 20px;
                box-shadow: none;
                border: none;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accident->id]) }}">
            <i class="bi bi-arrow-left"></i> Kembali ke Progress Perkara
        </a>
        <div>
            <a href="{{ route('doc.surat-kuasa-document.download', ['id' => $document->id, 'accident_id' => $accident->id]) }}" class="btn btn-primary me-2">
                <i class="bi bi-download me-1"></i> Unduh File Word (.docx)
            </a>
            <a href="{{ route('doc.surat-kuasa-document.edit', ['id' => $document->id, 'accident_id' => $accident->id]) }}" class="btn btn-warning me-2">
                <i class="bi bi-pencil-square me-1"></i> Edit Dokumen
            </a>
            <button onclick="window.print()" class="btn btn-secondary">
                <i class="bi bi-printer me-1"></i> Cetak Dokumen
            </button>
        </div>
    </div>

    <div class="doc-paper">
        <!-- JUDUL SURAT -->
        <div class="doc-title">
            <h4>SURAT KUASA</h4>
        </div>

        <!-- PEMBUKA PEMBERI KUASA -->
        <p class="mb-2">Yang bertanda tangan di bawah ini:</p>

        <!-- DATA PEMBERI KUASA -->
        <table class="table-data" style="margin-left: 20px;">
            <tr>
                <td class="lbl">nama lengkap</td>
                <td class="sep">:</td>
                <td>{{ $pemberi_nama }};</td>
            </tr>
            <tr>
                <td class="lbl">nomor identitas</td>
                <td class="sep">:</td>
                <td>{{ $pemberi_no_identitas }};</td>
            </tr>
            <tr>
                <td class="lbl">kewarganegaraan</td>
                <td class="sep">:</td>
                <td>{{ $pemberi_kewarganegaraan }};</td>
            </tr>
            <tr>
                <td class="lbl">jenis kelamin</td>
                <td class="sep">:</td>
                <td>{{ $pemberi_jenis_kelamin }};</td>
            </tr>
            <tr>
                <td class="lbl">tempat/tanggal lahir</td>
                <td class="sep">:</td>
                <td>{{ $pemberi_ttl }};</td>
            </tr>
            <tr>
                <td class="lbl">umur</td>
                <td class="sep">:</td>
                <td>{{ $pemberi_umur }};</td>
            </tr>
            <tr>
                <td class="lbl">pekerjaan</td>
                <td class="sep">:</td>
                <td>{{ $pemberi_pekerjaan }};</td>
            </tr>
            <tr>
                <td class="lbl">agama</td>
                <td class="sep">:</td>
                <td>{{ $pemberi_agama }};</td>
            </tr>
            <tr>
                <td class="lbl">alamat</td>
                <td class="sep">:</td>
                <td>{{ $pemberi_alamat }};</td>
            </tr>
        </table>
        <p class="mb-4" style="margin-left: 20px;">
            nomor Hp. {{ $pemberi_nohp }} / telp. {{ $pemberi_telp }} / email: {{ $pemberi_email }} / akun media sosial: ({{ $pemberi_medsos_platform }}): {{ $pemberi_medsos_akun }}.
        </p>

        <!-- PEMBUKA PENERIMA KUASA -->
        <p class="mb-2">dengan ini memberikan kuasa hukum kepada:</p>

        <!-- DATA PENERIMA KUASA -->
        <table class="table-data" style="margin-left: 20px;">
            <tr>
                <td class="lbl">nama lengkap</td>
                <td class="sep">:</td>
                <td>{{ $penerima_nama }};</td>
            </tr>
            <tr>
                <td class="lbl">nomor identitas</td>
                <td class="sep">:</td>
                <td>{{ $penerima_no_identitas }};</td>
            </tr>
            <tr>
                <td class="lbl">kewarganegaraan</td>
                <td class="sep">:</td>
                <td>{{ $penerima_kewarganegaraan }};</td>
            </tr>
            <tr>
                <td class="lbl">jenis kelamin</td>
                <td class="sep">:</td>
                <td>{{ $penerima_jenis_kelamin }};</td>
            </tr>
            <tr>
                <td class="lbl">tempat/tanggal lahir</td>
                <td class="sep">:</td>
                <td>{{ $penerima_ttl }};</td>
            </tr>
            <tr>
                <td class="lbl">umur</td>
                <td class="sep">:</td>
                <td>{{ $penerima_umur }};</td>
            </tr>
            <tr>
                <td class="lbl">pekerjaan</td>
                <td class="sep">:</td>
                <td>{{ $penerima_pekerjaan }};</td>
            </tr>
            <tr>
                <td class="lbl">agama</td>
                <td class="sep">:</td>
                <td>{{ $penerima_agama }};</td>
            </tr>
            <tr>
                <td class="lbl">alamat</td>
                <td class="sep">:</td>
                <td>{{ $penerima_alamat }};</td>
            </tr>
        </table>
        <p class="mb-4" style="margin-left: 20px;">
            nomor Hp. {{ $penerima_nohp }} / telp. {{ $penerima_telp }} / email: {{ $penerima_email }} / akun media sosial: ({{ $penerima_medsos_platform }}): {{ $penerima_medsos_akun }}.
        </p>

        <!-- BAGIAN KHUSUS -->
        <div style="text-align: center; margin: 20px 0 10px 0;">
            <strong style="font-size: 15px;">Khusus:</strong>
        </div>

        <p class="text-justify mb-2">
            Untuk memberikan bantuan hukum di dalam proses penyidikan, kepada pemberi kuasa (tersangka) yang dipersangkakan telah melakukan Tindak Pidana {{ $tindak_pidana }} sebagaimana dimaksud dalam Pasal {{ $pasal_pidana }} UU No. {{ $uu_nomor }} Tahun {{ $uu_tahun }} tentang {{ $uu_tentang }}, yang terjadi di {{ $tkp }}, {{ $waktu_kejadian }}, berdasarkan:
        </p>
        <ol class="mb-3" style="padding-left: 25px;">
            <li>Laporan Polisi Nomor: {{ $dasar_lp_nomor }} tanggal {{ $dasar_lp_tanggal }};</li>
            <li>Surat Perintah Penyidikan Nomor: {{ $dasar_sp_sidik_nomor }} tanggal {{ $dasar_sp_sidik_tanggal }};</li>
            <li>Surat Perintah Tugas Penyidikan Nomor: {{ $dasar_sp_gas_sidik_nomor }} tanggal {{ $dasar_sp_gas_sidik_tanggal }}.</li>
        </ol>

        <p class="text-justify mb-3">
            Kuasa tidak diberikan hak kepada penerima kuasa untuk mengalihkannya kepada orang lain (tanpa hak substitusi), kecuali atas persetujuan pemberi kuasa dan/atau persetujuan Penyidik/Penyidik Pembantu yang telah menunjuk penerima kuasa sebagai Penasehat Hukum berdasarkan Surat Penetapan Penunjukan Penasehat Hukum Nomor: {{ $surat_penetapan_nomor }} tanggal {{ $surat_penetapan_tanggal }}.
        </p>

        <p class="text-justify mb-4">
            Demikian surat kuasa ini dibuat dengan sebenarnya tanpa ada tekanan dan paksaan dari pihak manapun dan dapat digunakan untuk kepentingan penyelidikan dan penyidikan.
        </p>

        <!-- TEMPAT & TANGGAL -->
        <div class="row mb-3">
            <div class="col-6"></div>
            <div class="col-6 text-end">
                <span>{{ $dikeluarkan_di }}, {{ $tanggal_surat }}</span>
            </div>
        </div>

        <!-- TANDA TANGAN -->
        <div class="row text-center mt-3">
            <div class="col-6">
                <p class="mb-1">Yang menerima kuasa</p>
                <div style="height: 85px;"></div>
                <p class="mb-0 fw-bold">( {{ $penerima_nama }} )</p>
            </div>
            <div class="col-6">
                <p class="mb-1">Yang memberi kuasa/Tersangka</p>
                <div style="height: 85px;"></div>
                <p class="mb-0 fw-bold">( {{ $pemberi_nama }} )</p>
            </div>
        </div>
    </div>
@endsection
