@php
    $_title = 'Lihat Surat Permintaan Pemblokiran Rekening Bank';
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
            font-size: 13.5px;
        }
        .kop-surat {
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .kop-surat h6 {
            margin: 0;
            font-size: 13px;
            letter-spacing: 0.5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop-surat p {
            margin: 2px 0 0 0;
            font-size: 11px;
        }
        .text-justify {
            text-align: justify;
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
            <a href="{{ route('doc.surat-permintaan-blokir-rekening-bank-document.download', ['id' => $document->id, 'accident_id' => $accident->id]) }}" class="btn btn-success me-2">
                <i class="bi bi-download me-1"></i> Unduh Word
            </a>
            <a href="{{ route('doc.surat-permintaan-blokir-rekening-bank-document.edit', ['id' => $document->id, 'accident_id' => $accident->id]) }}" class="btn btn-warning me-2">
                <i class="bi bi-pencil-square me-1"></i> Edit Dokumen
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer me-1"></i> Cetak Dokumen
            </button>
        </div>
    </div>

    <div class="doc-paper">
        <!-- KOP SURAT -->
        <div class="kop-surat">
            <h6>{{ $kopstuk_1 ?? 'KEPOLISIAN NEGARA REPUBLIK INDONESIA' }}</h6>
            <h6>{{ $kopstuk_2 ?? ('DAERAH ' . strtoupper($accident->polres->polda->name ?? 'JAWA TIMUR')) }}</h6>
            <h6>{{ $kopstuk_3 ?? ('RESOR ' . strtoupper($accident->polres->name ?? 'PASURUAN')) }}</h6>
            <p>{{ $kopstuk_4 ?? ($accident->polres->address ?? '') }}</p>
        </div>

        <p class="mb-3 font-weight-bold" style="letter-spacing: 1px;">"PRO JUSTITIA"</p>

        <!-- HEADER SURAT -->
        <div class="row mb-3">
            <div class="col-6">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 80px;">Nomor</td>
                        <td style="width: 15px;">:</td>
                        <td><strong>{{ $nomor_surat ?? ($document->nomor ?: 'B/...../XI/2026/Satlantas') }}</strong></td>
                    </tr>
                    <tr>
                        <td>Klasifikasi</td>
                        <td>:</td>
                        <td>{{ $klasifikasi ?? ($document->sifat ?? 'Biasa') }}</td>
                    </tr>
                    <tr>
                        <td>Lampiran</td>
                        <td>:</td>
                        <td>{{ $lampiran ?? ($document->lampiran ?? '-') }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">Perihal</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;"><strong><u>{{ $perihal ?? 'Permintaan Pemblokiran Rekening Bank' }}</u></strong></td>
                    </tr>
                </table>
            </div>
            <div class="col-6 text-end">
                <p class="mb-3">{{ $dikeluarkan_di ?? ($document->tempat_surat ?? ($accident->polres->name ?? 'Pasuruan')) }}, {{ $tanggal_surat ?? $document->formatted_document_date }}</p>
                <div class="text-start ps-5">
                    <p class="mb-1"><strong><u>Kepada</u></strong></p>
                    <p class="mb-1">Yth. {!! nl2br(e($bank_tujuan_raw ?? $document->bank_tujuan)) !!}</p>
                    <p class="mb-0">di</p>
                    <p class="ps-3 mb-0"><u>{{ $tempat_tujuan ?? 'Tempat' }}</u></p>
                </div>
            </div>
        </div>

        <!-- ISI SURAT -->
        <div class="doc-body text-justify">
            <!-- POIN 1: RUJUKAN -->
            <div class="d-flex mb-2">
                <div style="min-width: 25px;">1.</div>
                <div>
                    <div>Rujukan:</div>
                    <table style="width: 100%; border: none;">
                        <tr>
                            <td style="vertical-align: top; width: 22px;">a.</td>
                            <td style="text-align: justify;">{{ $dasar_kuhp ?? 'Kitab Undang-Undang Hukum Pidana (KUHP);' }}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">b.</td>
                            <td style="text-align: justify;">{{ $dasar_kuhap ?? 'Pasal 5 ayat (1) huruf b angka 1, Pasal 7 ayat (1) huruf d dan j, Pasal 11, Pasal 38 ayat (1), Pasal 39 ayat (1) dan Pasal 44 Undang-Undang Nomor 8 Tahun 1981 tentang Hukum Acara Pidana;' }}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">c.</td>
                            <td style="text-align: justify;">{{ $dasar_polri ?? 'Undang-Undang Nomor 2 Tahun 2002 tentang Kepolisian Negara Republik Indonesia;' }}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">d.</td>
                            <td style="text-align: justify;">{{ $dasar_uu_pidana ?? 'Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan;' }}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">e.</td>
                            <td style="text-align: justify;">Laporan Polisi Nomor: {{ $dasar_lp_nomor ?? '-' }} tanggal {{ $dasar_lp_tanggal ?? '-' }};</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">f.</td>
                            <td style="text-align: justify;">Surat Perintah Penyidikan Nomor: {{ $dasar_sp_sidik_nomor ?? '-' }} tanggal {{ $dasar_sp_sidik_tanggal ?? '-' }}.</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- POIN 2: PEMBERITAHUAN PERKARA & IDENTITAS TERSANGKA -->
            <div class="d-flex mb-2">
                <div style="min-width: 25px;">2.</div>
                <div style="text-align: justify;">
                    <div>Sehubungan dengan rujukan di atas, diinformasikan kepada Dir bahwa Penyidik {{ $satker ?? 'Satuan Lalu Lintas' }} sedang melakukan penyidikan perkara dugaan Tindak Pidana {{ $tindak_pidana ?? 'Kecelakaan Lalu Lintas' }} sebagaimana dimaksud dalam Pasal {{ $pasal_pidana ?? '310 ayat (4)' }} UU Nomor {{ $uu_nomor ?? '22' }} Tahun {{ $uu_tahun ?? '2009' }} tentang {{ $uu_tentang ?? 'Lalu Lintas dan Angkutan Jalan' }}, yang terjadi di {{ $tkp ?? 'wilayah hukum' }}, {{ $waktu_kejadian ?? '-' }}, dengan identitas Tersangka sebagai berikut:</div>
                    <table style="width: 100%; margin-top: 5px;">
                        <tr>
                            <td style="width: 22px; vertical-align: top;">a.</td>
                            <td style="width: 180px; vertical-align: top;">nama</td>
                            <td style="width: 15px; vertical-align: top;">:</td>
                            <td style="vertical-align: top;"><strong>{{ $tersangka_nama ?? '-' }}</strong>;</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">b.</td>
                            <td style="vertical-align: top;">nomor identitas</td>
                            <td style="vertical-align: top;">:</td>
                            <td style="vertical-align: top;">{{ $tersangka_no_identitas ?? '-' }};</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">c.</td>
                            <td style="vertical-align: top;">kewarganegaraan</td>
                            <td style="vertical-align: top;">:</td>
                            <td style="vertical-align: top;">{{ $tersangka_kewarganegaraan ?? 'WNI' }};</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">d.</td>
                            <td style="vertical-align: top;">jenis kelamin</td>
                            <td style="vertical-align: top;">:</td>
                            <td style="vertical-align: top;">{{ $tersangka_jenis_kelamin ?? '-' }};</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">e.</td>
                            <td style="vertical-align: top;">tempat/tanggal lahir</td>
                            <td style="vertical-align: top;">:</td>
                            <td style="vertical-align: top;">{{ $tersangka_ttl ?? '-' }};</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">f.</td>
                            <td style="vertical-align: top;">pekerjaan</td>
                            <td style="vertical-align: top;">:</td>
                            <td style="vertical-align: top;">{{ $tersangka_pekerjaan ?? '-' }};</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">g.</td>
                            <td style="vertical-align: top;">agama</td>
                            <td style="vertical-align: top;">:</td>
                            <td style="vertical-align: top;">{{ $tersangka_agama ?? '-' }};</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">h.</td>
                            <td style="vertical-align: top;">alamat</td>
                            <td style="vertical-align: top;">:</td>
                            <td style="vertical-align: top;">{{ $tersangka_alamat ?? '-' }}.</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- POIN 3: OBJEK & JADWAL PEMBLOKIRAN -->
            <div class="d-flex mb-2">
                <div style="min-width: 25px;">3.</div>
                <div style="text-align: justify;">
                    <div>Guna kepentingan penyidikan dimohon kepada Direktur menunjuk staf yang berkompeten untuk memberikan data, keterangan dan melakukan pemblokiran {{ $objek_blokir ?? '-' }} serta untuk hadir pada:</div>
                    <table style="width: 100%; margin-top: 5px;">
                        <tr>
                            <td style="width: 22px; vertical-align: top;">a.</td>
                            <td style="width: 130px; vertical-align: top;">hari/tanggal</td>
                            <td style="width: 15px; vertical-align: top;">:</td>
                            <td style="vertical-align: top;"><strong>{{ $blokir_hari_tanggal ?? '-' }}</strong>;</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">b.</td>
                            <td style="vertical-align: top;">pukul</td>
                            <td style="vertical-align: top;">:</td>
                            <td style="vertical-align: top;">{{ $blokir_waktu ?? '09:00 WIB' }};</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">c.</td>
                            <td style="vertical-align: top;">tempat</td>
                            <td style="vertical-align: top;">:</td>
                            <td style="vertical-align: top;">{{ $blokir_tempat ?? '-' }}.</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- POIN 4: KOORDINASI / NARAHUBUNG -->
            <div class="d-flex mb-3">
                <div style="min-width: 25px;">4.</div>
                <div style="text-align: justify;">
                    Untuk memudahkan dalam berkoordinasi dan berkomunikasi dapat menghubungi {{ $penyidik_sebutan ?? 'Penyidik Pembantu' }} atas nama {{ $penyidik_nama ?? '-' }} dengan nomor Hp. {{ $penyidik_nohp ?? '-' }}.
                </div>
            </div>

            <!-- POIN 5: PENUTUP -->
            <div class="d-flex mb-4">
                <div style="min-width: 25px;">5.</div>
                <div>Demikian untuk menjadi maklum.</div>
            </div>
        </div>

        <!-- TANDA TANGAN & TEMBUSAN -->
        <div class="row mt-4">
            <div class="col-6">
                <div style="font-size: 13px;">
                    <p class="mb-1"><strong><u>Tembusan:</u></strong></p>
                    <div>1. {{ $tembusan_1 ?? '-' }}</div>
                    <div>2. {{ $tembusan_2 ?? '-' }}</div>
                    <div>3. {{ $tembusan_3 ?? '-' }}</div>
                    <div>4. {{ $tembusan_4 ?? '-' }}</div>
                    <div>5. {{ $tembusan_5 ?? '-' }}</div>
                </div>
            </div>
            <div class="col-6 text-center">
                <div class="font-weight-bold text-uppercase" style="white-space: pre-line;">{!! nl2br(e($atasan_jabatan_raw ?? ($document->signatory_position ?? 'KEPALA SATUAN LALU LINTAS'))) !!}</div>
                <div style="height: 70px;"></div>
                <p class="mb-0 font-weight-bold"><u>{{ $atasan_nama ?? ($document->signatory_name ?? '-') }}</u></p>
                <p class="mb-0">{{ $atasan_pangkat_nrp ?? ($document->signatory_rank . ' NRP ' . $document->signatory_nrp) }}</p>
            </div>
        </div>
    </div>
@endsection

