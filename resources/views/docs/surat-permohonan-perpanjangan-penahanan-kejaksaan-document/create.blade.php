@php
    $_title = 'Surat Permohonan Perpanjangan Penahanan Kejaksaan';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
    <style>
        .input-group > .select2-container--bootstrap4 {
            flex: 1 1 auto;
            width: 1% !important;
        }
        .input-group > .select2-container--bootstrap4 .select2-selection--single {
            height: calc(1.5em + .75rem + 2px) !important;
            display: flex;
            align-items: center;
        }
        .carbon-copy-item {
            background: #fdfdfe;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 8px;
        }
    </style>
@endpush

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}">
        <i class="bi bi-arrow-left"></i> Kembali ke Progress Perkara
    </a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Surat Permohonan Perpanjangan Penahanan Kejaksaan</h5>
            <small class="text-muted d-block mb-3">Kode Dokumen: <b>DCT-0605 / DCT-0903</b> │ Kategori: <b>0605 / 0903</b></small>

            <div class="alert alert-info">
                <i class="bi bi-info-circle me-1"></i>
                Permohonan perpanjangan penahanan diajukan kepada Kepala Kejaksaan Negeri untuk memperpanjang masa penahanan tersangka selama <b>40 (empat puluh) hari</b> setelah berakhirnya penahanan 20 hari pertama oleh Penyidik.
            </div>

            <!-- Error alert -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <div class="box-body">
            <form action="{{ route('doc.surat-permohonan-perpanjangan-penahanan-kejaksaan-document.store', ['accident_id' => $accidentId]) }}"
                method="POST" id="suratPermohonanPerpanjanganPenahananKejaksaanForm" novalidate>
                @csrf
                <input type="hidden" name="accident_id" id="accident_id" value="{{ $accidentId }}">

                {{-- 1. IDENTITAS DOKUMEN --}}
                <h5 class="fw-bold text-blue-dark border-bottom pb-2 mb-3">1. Identitas Dokumen</h5>

                {{-- Nomor LP --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="accidentNumber">Nomor LP</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="accidentNumber" type="text"
                            class="form-control font-weight-bold"
                            value="{{ $accident->no_lp }}" readonly style="background-color: #e9ecef;">
                    </div>
                </div>

                {{-- Tanggal LP --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="accidentDate">Tanggal LP</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="accidentDate" type="text"
                            class="form-control"
                            value="{{ $accident->accident_date ? \Carbon\Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y') : '-' }}" readonly style="background-color: #e9ecef;">
                    </div>
                </div>

                {{-- Nomor Surat Permohonan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nomor">Nomor Surat Permohonan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="nomor" type="text"
                            class="form-control @error('nomor') is-invalid @enderror font-weight-bold"
                            name="nomor" value="{{ old('nomor') }}" required
                            placeholder="Contoh: B/123/X/2026/Satlantas">
                        @error('nomor')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Tanggal Surat Permohonan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggal">Tanggal Surat Permohonan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal"
                            placeholder="YYYY-MM-DD" autocomplete="off" value="{{ old('tanggal', date('Y-m-d')) }}"
                            data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-date-today-highlight="true" required>
                        @error('tanggal')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Klasifikasi & Lampiran --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="klasifikasi">Klasifikasi Surat</label>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-2">
                        <select class="form-select" id="klasifikasi" name="klasifikasi">
                            <option value="BIASA" {{ old('klasifikasi') == 'BIASA' ? 'selected' : '' }}>BIASA</option>
                            <option value="RAHASIA" {{ old('klasifikasi') == 'RAHASIA' ? 'selected' : '' }}>RAHASIA</option>
                            <option value="SEGERA" {{ old('klasifikasi') == 'SEGERA' ? 'selected' : '' }}>SEGERA</option>
                        </select>
                    </div>
                    <label class="fw-bold col-sm-2 col-form-label text-md-end" for="lampiran">Lampiran</label>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input id="lampiran" type="text" class="form-control" name="lampiran"
                            value="{{ old('lampiran', '1 (satu) Berkas') }}" placeholder="1 (satu) Berkas">
                    </div>
                </div>

                {{-- Tempat Terbit Surat --}}
                <div class="input-group row mb-4 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tempat_surat">Kota / Tempat Terbit Surat</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tempat_surat" type="text" class="form-control" name="tempat_surat"
                            value="{{ old('tempat_surat', $accident->polres->name ?? 'Pasuruan') }}" placeholder="Contoh: Pasuruan / Bangil">
                    </div>
                </div>

                {{-- 2. TUJUAN KEJAKSAAN --}}
                <h5 class="fw-bold text-blue-dark border-bottom pb-2 mb-3">2. Tujuan Kejaksaan</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="prosecutor_id">Kejaksaan Penerima<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2 @error('prosecutor_id') is-invalid @enderror"
                            id="prosecutor_id" name="prosecutor_id" required style="width: 100%;">
                            <option value="">-- Pilih Kejaksaan --</option>
                            @foreach ($prosecutors as $prosecutor)
                                <option value="{{ $prosecutor->id }}"
                                    data-location="{{ $prosecutor->address ?? $prosecutor->name }}"
                                    {{ old('prosecutor_id', $defaultProsecutorId) == $prosecutor->id ? 'selected' : '' }}>
                                    {{ $prosecutor->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('prosecutor_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-4 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="lokasi_kejaksaan">Tempat / Lokasi Kejaksaan (di ...)</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="lokasi_kejaksaan" type="text" class="form-control" name="lokasi_kejaksaan"
                            value="{{ old('lokasi_kejaksaan', $defaultProsecutorLocation) }}"
                            placeholder="Contoh: BANGIL / PASURUAN">
                        <small class="text-muted">Akan dicetak pada format: <i>Kepada Yth. KEPALA [Nama Kejaksaan] di [Lokasi Kejaksaan]</i></small>
                    </div>
                </div>

                {{-- 3. RUJUKAN DASAR HUKUM & SURAT --}}
                <h5 class="fw-bold text-blue-dark border-bottom pb-2 mb-3">3. Rujukan Dokumen Terkait</h5>

                {{-- Sprindik --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nomor_sprindik">Surat Perintah Penyidikan
                        <small class="text-muted d-block font-weight-normal">(Otomatis dari Sistem)</small>
                    </label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12 mb-2">
                        <input type="text" class="form-control font-weight-bold" id="nomor_sprindik" name="nomor_sprindik"
                            value="{{ old('nomor_sprindik', $nomorSprindik) }}" placeholder="Nomor Sprindik"
                            readonly style="background-color: #e9ecef;">
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <input class="form-control" id="tanggal_sprindik" name="tanggal_sprindik"
                            placeholder="Tanggal Sprindik (YYYY-MM-DD)" autocomplete="off"
                            value="{{ old('tanggal_sprindik', $tanggalSprindik) }}"
                            readonly style="background-color: #e9ecef; pointer-events: none;">
                    </div>
                </div>

                {{-- SPDP --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nomor_spdp">SPDP
                        <small class="text-muted d-block font-weight-normal">(Otomatis dari Sistem)</small>
                    </label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12 mb-2">
                        <input type="text" class="form-control font-weight-bold" id="nomor_spdp" name="nomor_spdp"
                            value="{{ old('nomor_spdp', $nomorSpdp) }}" placeholder="Nomor SPDP"
                            readonly style="background-color: #e9ecef;">
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <input class="form-control" id="tanggal_spdp" name="tanggal_spdp"
                            placeholder="Tanggal SPDP (YYYY-MM-DD)" autocomplete="off"
                            value="{{ old('tanggal_spdp', $tanggalSpdp) }}"
                            readonly style="background-color: #e9ecef; pointer-events: none;">
                    </div>
                    <input type="hidden" name="kode_satker_penerbit_spdp" value="{{ $kodeSatkerDefault }}">
                </div>

                {{-- Penetapan Tersangka --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nomor_penetapan_tersangka">SKET Penetapan Tersangka
                        <small class="text-muted d-block font-weight-normal">(Otomatis dari Sistem)</small>
                    </label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12 mb-2">
                        <input type="text" class="form-control font-weight-bold" id="nomor_penetapan_tersangka" name="nomor_penetapan_tersangka"
                            value="{{ old('nomor_penetapan_tersangka', $nomorSket) }}" placeholder="Nomor Penetapan Tersangka"
                            readonly style="background-color: #e9ecef;">
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <input class="form-control" id="tanggal_penetapan_tersangka" name="tanggal_penetapan_tersangka"
                            placeholder="Tanggal SKET (YYYY-MM-DD)" autocomplete="off"
                            value="{{ old('tanggal_penetapan_tersangka', $tanggalSket) }}"
                            readonly style="background-color: #e9ecef; pointer-events: none;">
                    </div>
                </div>

                {{-- Surat Perintah Penahanan --}}
                <div class="input-group row mb-4 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nomor_surat_perintah_penahanan">Surat Perintah Penahanan (S-17)
                        <small class="text-muted d-block font-weight-normal">(Otomatis dari Sistem)</small>
                    </label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12 mb-2">
                        <input type="text" class="form-control font-weight-bold" id="nomor_surat_perintah_penahanan" name="nomor_surat_perintah_penahanan"
                            value="{{ old('nomor_surat_perintah_penahanan', $nomorS17) }}" placeholder="Nomor Surat Perintah Penahanan"
                            readonly style="background-color: #e9ecef;">
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <input class="form-control" id="tanggal_surat_perintah_penahanan" name="tanggal_surat_perintah_penahanan"
                            placeholder="Tanggal S-17 (YYYY-MM-DD)" autocomplete="off"
                            value="{{ old('tanggal_surat_perintah_penahanan', $tanggalS17) }}"
                            readonly style="background-color: #e9ecef; pointer-events: none;">
                    </div>
                </div>

                {{-- 4. URAIAN PERKARA (POIN 2) --}}
                <h5 class="fw-bold text-blue-dark border-bottom pb-2 mb-3">4. Uraian Perkara (Poin 2)</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="satker_penyidik">Satker Penyidik
                        <small class="text-muted d-block font-weight-normal">(Otomatis dari Sistem)</small>
                    </label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="satker_penyidik" type="text" class="form-control" name="satker_penyidik"
                            value="{{ old('satker_penyidik', $satkerPenyidikDefault) }}" placeholder="Satker Penyidik"
                            readonly style="background-color: #e9ecef;">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="dugaan_tindak_pidana">Dugaan Tindak Pidana</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <textarea id="dugaan_tindak_pidana" class="form-control" name="dugaan_tindak_pidana" rows="2">{{ old('dugaan_tindak_pidana', 'Kecelakaan Lalu Lintas yang mengakibatkan orang lain meninggal dunia dan/atau luka berat') }}</textarea>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="pasal_diduga">Pasal yang Disangkakan
                        <small class="text-muted d-block font-weight-normal">(Otomatis dari Sprindik)</small>
                    </label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="pasal_diduga" type="text" class="form-control" name="pasal_diduga"
                            value="{{ old('pasal_diduga', $defaultPasal) }}"
                            readonly style="background-color: #e9ecef;">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tempat_kejadian">Tempat Kejadian Perkara (TKP)
                        <small class="text-muted d-block font-weight-normal">(Otomatis dari Laporan Polisi)</small>
                    </label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <textarea id="tempat_kejadian" class="form-control" name="tempat_kejadian" rows="2"
                            readonly style="background-color: #e9ecef;">{{ old('tempat_kejadian', $tempatKejadianDefault) }}</textarea>
                    </div>
                </div>

                <div class="input-group row mb-4 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="kurun_waktu">Kurun Waktu Kejadian
                        <small class="text-muted d-block font-weight-normal">(Otomatis dari Waktu Kejadian LP)</small>
                    </label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="kurun_waktu" type="text" class="form-control" name="kurun_waktu"
                            value="{{ old('kurun_waktu', $kurunWaktuDefault) }}"
                            readonly style="background-color: #e9ecef;">
                    </div>
                </div>

                {{-- 5. MASA PENAHANAN & PERPANJANGAN (POIN 3) --}}
                <h5 class="fw-bold text-blue-dark border-bottom pb-2 mb-3">5. Masa Penahanan & Perpanjangan (Poin 3)</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggal_akhir_penahanan_lama">
                        Akhir Masa Penahanan Penyidik<span class="text-danger fs-5">*</span>
                        <small class="text-muted d-block font-weight-normal">(Otomatis dari Akhir Masa Penahanan S-17)</small>
                    </label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input class="form-control @error('tanggal_akhir_penahanan_lama') is-invalid @enderror"
                            id="tanggal_akhir_penahanan_lama" name="tanggal_akhir_penahanan_lama"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('tanggal_akhir_penahanan_lama', $tanggalAkhirPenahananLama) }}"
                            readonly style="background-color: #e9ecef; pointer-events: none;" required>
                        @error('tanggal_akhir_penahanan_lama')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nama_rutan">Tempat Penahanan / Nama Rutan<span class="text-danger fs-5">*</span>
                        <small class="text-muted d-block font-weight-normal">(Otomatis dari Lokasi Penahanan S-17)</small>
                    </label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="nama_rutan" type="text"
                            class="form-control @error('nama_rutan') is-invalid @enderror"
                            name="nama_rutan" value="{{ old('nama_rutan', $namaRutanDefault) }}" required
                            placeholder="Contoh: Rutan Kepolisian Resor Pasuruan"
                            readonly style="background-color: #e9ecef;">
                        @error('nama_rutan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggal_mulai_perpanjangan">
                        Masa Perpanjangan (40 Hari)<span class="text-danger fs-5">*</span>
                        <small class="text-muted d-block font-weight-normal">(Otomatis dihitung dari S-17)</small>
                    </label>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-2">
                        <small class="text-muted d-block">Tanggal Mulai:</small>
                        <input class="form-control @error('tanggal_mulai_perpanjangan') is-invalid @enderror"
                            id="tanggal_mulai_perpanjangan" name="tanggal_mulai_perpanjangan"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('tanggal_mulai_perpanjangan', $tanggalMulaiPerpanjangan) }}"
                            readonly style="background-color: #e9ecef; pointer-events: none;" required>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-12 col-12 text-center align-self-center mb-2">
                        <span class="fw-bold">s.d.</span>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <small class="text-muted d-block">Tanggal Berakhir (40 Hari):</small>
                        <input class="form-control @error('tanggal_akhir_perpanjangan') is-invalid @enderror"
                            id="tanggal_akhir_perpanjangan" name="tanggal_akhir_perpanjangan"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('tanggal_akhir_perpanjangan', $tanggalAkhirPerpanjangan) }}"
                            readonly style="background-color: #e9ecef; pointer-events: none;" required>
                    </div>
                    <input type="hidden" name="jumlah_hari" value="40">
                </div>

                {{-- Kontak Penyidik / Penyidik Pembantu Penghubung --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="contact_officer_id">
                        Penyidik Penghubung<span class="text-danger fs-5">*</span>
                        <small class="text-muted d-block font-weight-normal">(Penyidik/Penyidik Pembantu untuk koordinasi)</small>
                    </label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12 mb-2">
                        <select class="form-control select2 @error('contact_officer_id') is-invalid @enderror"
                            id="contact_officer_id" name="contact_officer_id" required style="width: 100%;">
                            <option value="">-- Pilih Penyidik / Petugas Penghubung --</option>
                            @foreach ($internalOfficers as $officer)
                                <option value="{{ $officer->id }}"
                                    data-phone="{{ $officer->phone_number ?? ($officer->user->phone_number ?? '') }}"
                                    {{ old('contact_officer_id') == $officer->id ? 'selected' : '' }}>
                                    {{ $officer->full_name }} - {{ $officer->rank->name ?? '' }} (NRP {{ $officer->register_number }})
                                </option>
                            @endforeach
                        </select>
                        @error('contact_officer_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <input id="contact_officer_phone" type="text"
                            class="form-control" name="contact_officer_phone"
                            value="{{ old('contact_officer_phone') }}"
                            placeholder="No. Handphone (Contoh: 081234567890)">
                        <small class="text-muted">Nomor kontak yang dapat dihubungi Kejaksaan</small>
                    </div>
                </div>

                {{-- 6. DATA TERSANGKA --}}
                <h5 class="fw-bold text-blue-dark border-bottom pb-2 mb-3 mt-4">6. Tersangka yang Dimohonkan Perpanjangan Penahanan</h5>

                <div class="mb-4">
                    @if ($suspects->isEmpty())
                        <div class="alert alert-warning">
                            Belum ada data tersangka dalam berkas perkara ini. Silakan input tersangka terlebih dahulu.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="suspectTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40px;" class="text-center">Pilih</th>
                                        <th>Nama Lengkap</th>
                                        <th>No. Identitas</th>
                                        <th>Tempat, Tanggal Lahir</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Pekerjaan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($suspects as $idx => $s)
                                        <tr>
                                            <td class="text-center align-middle">
                                                <div class="icheck-primary d-inline">
                                                    <input type="checkbox" id="suspect_{{ $s->id }}" name="suspects[]"
                                                        class="suspect-checkbox"
                                                        value="{{ $s->id }}"
                                                        {{ (is_array(old('suspects')) && in_array($s->id, old('suspects'))) || $idx === 0 ? 'checked' : '' }}>
                                                    <label for="suspect_{{ $s->id }}"></label>
                                                </div>
                                            </td>
                                            <td class="align-middle fw-bold">{{ $s->name }}</td>
                                            <td class="align-middle">{{ $s->identity_number ?? '-' }}</td>
                                            <td class="align-middle">
                                                {{ $s->birth_place ?? '-' }}, {{ $s->birth_date ? \Carbon\Carbon::parse($s->birth_date)->locale('id')->translatedFormat('d F Y') : '-' }}
                                            </td>
                                            <td class="align-middle">{{ $s->gender->name ?? '-' }}</td>
                                            <td class="align-middle">{{ $s->job->name ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- 7. TEMBUSAN (CARBON COPIES) --}}
                <h5 class="fw-bold text-blue-dark border-bottom pb-2 mb-3">7. Tembusan Surat</h5>

                <div class="mb-4">
                    <div id="carbonCopiesContainer">
                        @php
                            $ccList = old('carbon_copies', $defaultCarbonCopies);
                        @endphp
                        @foreach ($ccList as $ccIndex => $ccVal)
                            <div class="carbon-copy-item d-flex align-items-center gap-2">
                                <span class="fw-bold cc-num" style="width: 25px;">{{ $loop->iteration }}.</span>
                                <input type="text" class="form-control" name="carbon_copies[]" value="{{ $ccVal }}" placeholder="Nama penerima tembusan">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-cc-btn" title="Hapus baris">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addCcBtn">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Tembusan
                    </button>
                </div>

                {{-- 8. PEJABAT PENANDATANGAN (SIGNATORY) --}}
                <h5 class="fw-bold text-blue-dark border-bottom pb-2 mb-3">8. Pejabat Penandatangan</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="signatory">Pejabat Penandatangan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2 @error('signatory') is-invalid @enderror"
                            id="signatory" name="signatory" required style="width: 100%;">
                            <option value="">-- Pilih Pejabat Penandatangan --</option>
                            @foreach ($authorizedSignatories as $signatory)
                                <option value="{{ $signatory->id }}"
                                    data-position="{{ $signatory->position->name ?? ($signatory->position_id ?? 'KASAT LANTAS') }}"
                                    {{ old('signatory') == $signatory->id ? 'selected' : '' }}>
                                    {{ $signatory->full_name }} - {{ $signatory->rank->name ?? '' }} ({{ $signatory->position->name ?? 'Kasat Lantas' }})
                                </option>
                            @endforeach
                        </select>
                        @error('signatory')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-4 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="signatory_head_text">Teks Header Tanda Tangan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <textarea id="signatory_head_text" class="form-control" name="signatory_head_text" rows="2">{{ old('signatory_head_text', "a.n. KEPALA KEPOLISIAN RESOR " . strtoupper($accident->polres->name ?? 'PASURUAN')) }}</textarea>
                        <small class="text-muted">Gunakan jika ditandatangani atas nama (a.n.), atau kosongkan jika langsung oleh Kapolres.</small>
                    </div>
                </div>

                <div class="box-footer text-end mt-4">
                    <button type="button" class="btn btn-primary px-4 py-2 fw-bold" id="btnSubmitForm">
                        <i class="bi bi-save me-1"></i> Simpan Dokumen
                    </button>
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-secondary px-4 py-2 ms-2">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Auto-update lokasi kejaksaan saat kejaksaan dipilih
            $('#prosecutor_id').on('change', function() {
                var selectedOpt = $(this).find(':selected');
                var loc = selectedOpt.data('location');
                if (loc && !$('#lokasi_kejaksaan').val()) {
                    $('#lokasi_kejaksaan').val(loc);
                }
            });

            // Auto-update phone saat penyidik penghubung dipilih
            $('#contact_officer_id').on('change', function() {
                var selectedOpt = $(this).find(':selected');
                var phone = selectedOpt.data('phone');
                if (phone) {
                    $('#contact_officer_phone').val(phone);
                }
            });

            // Auto calculate 40 hari perpanjangan saat tanggal mulai berubah
            $('#tanggal_mulai_perpanjangan').on('change', function() {
                var startVal = $(this).val();
                if (startVal) {
                    var startDate = new Date(startVal);
                    if (!isNaN(startDate.getTime())) {
                        startDate.setDate(startDate.getDate() + 39); // 40 hari inklusif
                        var yyyy = startDate.getFullYear();
                        var mm = String(startDate.getMonth() + 1).padStart(2, '0');
                        var dd = String(startDate.getDate()).padStart(2, '0');
                        $('#tanggal_akhir_perpanjangan').val(yyyy + '-' + mm + '-' + dd);
                    }
                }
            });

            // Auto set tanggal mulai perpanjangan saat akhir penahanan lama berubah
            $('#tanggal_akhir_penahanan_lama').on('change', function() {
                var lamaVal = $(this).val();
                if (lamaVal) {
                    var lamaDate = new Date(lamaVal);
                    if (!isNaN(lamaDate.getTime())) {
                        lamaDate.setDate(lamaDate.getDate() + 1);
                        var yyyy = lamaDate.getFullYear();
                        var mm = String(lamaDate.getMonth() + 1).padStart(2, '0');
                        var dd = String(lamaDate.getDate()).padStart(2, '0');
                        $('#tanggal_mulai_perpanjangan').val(yyyy + '-' + mm + '-' + dd).trigger('change');
                    }
                }
            });

            // Dynamic Tembusan (Carbon copies)
            $('#addCcBtn').on('click', function() {
                var count = $('#carbonCopiesContainer .carbon-copy-item').length + 1;
                var html = `
                    <div class="carbon-copy-item d-flex align-items-center gap-2">
                        <span class="fw-bold cc-num" style="width: 25px;">${count}.</span>
                        <input type="text" class="form-control" name="carbon_copies[]" placeholder="Nama penerima tembusan">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-cc-btn" title="Hapus baris">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                `;
                $('#carbonCopiesContainer').append(html);
            });

            $(document).on('click', '.remove-cc-btn', function() {
                if ($('#carbonCopiesContainer .carbon-copy-item').length > 1) {
                    $(this).closest('.carbon-copy-item').remove();
                    // Reindex numbers
                    $('#carbonCopiesContainer .carbon-copy-item').each(function(idx) {
                        $(this).find('.cc-num').text((idx + 1) + '.');
                    });
                } else {
                    $(this).closest('.carbon-copy-item').find('input').val('');
                }
            });

            // Helper scroll ke error pertama
            function scrollToFirstError() {
                var $firstError = $('.frontend-error, .invalid-feedback.d-block').first();
                if (!$firstError.length) {
                    $firstError = $('.is-invalid, .border-danger').first();
                }
                if ($firstError && $firstError.length) {
                    var el = $firstError[0];
                    if (el && typeof el.scrollIntoView === 'function') {
                        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    var topPos = $firstError.offset() ? $firstError.offset().top : 0;
                    $('html, body, .content-wrapper, .wrapper, main').stop().animate({
                        scrollTop: Math.max(0, topPos - 140)
                    }, 400);
                } else {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }

            // Real-time pembersihan error saat user mengetik atau memilih opsi
            $(document).on('input change', 'input, select, textarea', function() {
                $(this).removeClass('is-invalid');
                if ($(this).next('.select2-container').length) {
                    $(this).next('.select2-container').find('.select2-selection').removeClass('border border-danger is-invalid');
                }
                $(this).siblings('.frontend-error, .invalid-feedback').remove();
                $(this).next('.frontend-error, .invalid-feedback').remove();
                var $container = $(this).closest('.table-responsive, .input-group');
                if ($container.length) {
                    $container.siblings('.frontend-error, .invalid-feedback').remove();
                    $container.next('.frontend-error, .invalid-feedback').remove();
                }
            });

            // Pembersihan error checkbox tersangka saat dicentang
            $(document).on('change', '.suspect-checkbox', function() {
                if ($('.suspect-checkbox:checked').length > 0) {
                    $('#suspectTable').removeClass('border border-danger is-invalid');
                    var $wrapper = $('#suspectTable').closest('.table-responsive');
                    var $container = $wrapper.length ? $wrapper : $('#suspectTable');
                    $container.siblings('.frontend-error, .invalid-feedback').remove();
                    $container.next('.frontend-error, .invalid-feedback').remove();
                }
            });

            // Form Submit validation dengan pesan error di bawah masing-masing field
            $('#btnSubmitForm, #btnSubmit').on('click', function(e) {
                e.preventDefault();

                // Bersihkan pesan error sebelumnya
                $('.is-invalid').removeClass('is-invalid');
                $('.border.border-danger').removeClass('border border-danger');
                $('.select2-selection').removeClass('border border-danger is-invalid');
                $('.frontend-error').remove();
                $('.invalid-feedback').remove();

                var errors = [];

                function markError(fieldSelector, message) {
                    var $field = $(fieldSelector);
                    if (!$field.length) return;

                    if ($field.is('table')) {
                        $field.addClass('border border-danger is-invalid');
                        var $wrapper = $field.closest('.table-responsive, .input-group');
                        var $container = $wrapper.length ? $wrapper : $field;
                        $container.siblings('.frontend-error, .invalid-feedback').remove();
                        $container.next('.frontend-error, .invalid-feedback').remove();
                        $container.after('<div class="invalid-feedback d-block frontend-error">' + message + '</div>');
                        errors.push(message);
                        return;
                    } else if ($field.is(':checkbox')) {
                        $field.addClass('is-invalid');
                        var $container = $field.closest('.table-responsive, .input-group');
                        $container.siblings('.frontend-error, .invalid-feedback').remove();
                        $container.next('.frontend-error, .invalid-feedback').remove();
                        $container.after('<div class="invalid-feedback d-block frontend-error">' + message + '</div>');
                        errors.push(message);
                        return;
                    } else {
                        $field.addClass('is-invalid');
                    }
                    if ($field.next('.select2-container').length) {
                        $field.next('.select2-container').find('.select2-selection').addClass('border border-danger is-invalid');
                    }
                    var $target = $field.next('.select2-container').length ? $field.next('.select2-container') : $field;
                    $target.siblings('.frontend-error, .invalid-feedback').remove();
                    $target.next('.frontend-error, .invalid-feedback').remove();
                    $target.after('<div class="invalid-feedback d-block frontend-error">' + message + '</div>');
                    errors.push(message);
                }

                function checkInput(fieldSelector, label) {
                    var $field = $(fieldSelector);
                    if ($field.is(':disabled') || !$field.is(':visible')) return;
                    var raw = $field.val();
                    var val = (raw !== null && raw !== undefined) ? String(raw).trim() : '';
                    if (!val || val === '') {
                        markError(fieldSelector, label + ' harus diisi');
                    }
                }

                function checkSelect(fieldSelector, label) {
                    var $field = $(fieldSelector);
                    if ($field.is(':disabled') || (!$field.is(':visible') && !$field.next('.select2-container:visible').length)) return;
                    var raw = $field.val();
                    var hasVal = Array.isArray(raw) ? raw.length > 0 : (raw && String(raw).trim() !== '' && String(raw).trim() !== '0');
                    if (!hasVal) {
                        markError(fieldSelector, label + ' harus dipilih');
                    }
                }

                // 1. Validasi Identitas Dokumen
                checkInput('#nomor', 'Nomor Surat Permohonan');
                checkInput('#tanggal', 'Tanggal Surat Permohonan');

                // 2. Validasi Tujuan Kejaksaan
                checkSelect('#prosecutor_id', 'Kejaksaan Penerima');

                // 3. Validasi Masa Penahanan & Rutan
                checkInput('#tanggal_akhir_penahanan_lama', 'Akhir Masa Penahanan Penyidik');
                checkInput('#nama_rutan', 'Tempat Penahanan / Nama Rutan');
                checkInput('#tanggal_mulai_perpanjangan', 'Tanggal Mulai Perpanjangan');
                checkInput('#tanggal_akhir_perpanjangan', 'Tanggal Berakhir Perpanjangan');

                var tglMulai = $('#tanggal_mulai_perpanjangan').val();
                var tglAkhir = $('#tanggal_akhir_perpanjangan').val();
                if (tglMulai && tglAkhir) {
                    var d1 = new Date(tglMulai);
                    var d2 = new Date(tglAkhir);
                    if (d2 < d1) {
                        markError('#tanggal_akhir_perpanjangan', 'Tanggal Berakhir Perpanjangan harus setelah atau sama dengan Tanggal Mulai');
                    }
                }

                // 4. Validasi Kontak Penyidik Penghubung
                checkSelect('#contact_officer_id', 'Penyidik Penghubung');

                // 5. Validasi Tersangka minimal 1 orang
                if ($('.suspect-checkbox:checked').length === 0) {
                    markError('#suspectTable', 'Minimal 1 (satu) tersangka harus dipilih');
                }

                // 6. Validasi Pejabat Penandatangan
                checkSelect('#signatory', 'Pejabat Penandatangan');

                // Jika terdapat error di sisi frontend, scroll ke elemen pertama dan batalkan submit
                if (errors.length > 0) {
                    scrollToFirstError();
                    return false;
                }

                // Validasi AJAX ke server
                $.ajax({
                    url: "{{ route('doc.surat-permohonan-perpanjangan-penahanan-kejaksaan-document.api.validate-request-form', ['accident_id' => $accidentId]) }}",
                    type: 'POST',
                    dataType: 'json',
                    data: $('#suratPermohonanPerpanjanganPenahananKejaksaanForm').serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: response.message || 'Silahkan menunggu proses simpan data',
                                icon: 'success',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                $('#suratPermohonanPerpanjanganPenahananKejaksaanForm')[0].submit();
                            });
                        }
                    },
                    error: function(xhr) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.code == 422 && response.errors) {
                                $.each(response.errors, function(key, messages) {
                                    var msg = Array.isArray(messages) ? messages[0] : messages;
                                    var $target = $('#' + key + ', [name="' + key + '"]');
                                    if ($target.length) {
                                        markError($target, msg);
                                    } else if (key === 'suspects') {
                                        markError('#suspectTable', msg);
                                    } else if (key === 'signatory') {
                                        markError('#signatory', msg);
                                    } else if (key === 'contact_officer_id') {
                                        markError('#contact_officer_id', msg);
                                    }
                                });
                                scrollToFirstError();
                            } else {
                                var message = response.message || response.errors || 'Terjadi kesalahan saat memproses data.';
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Perhatian',
                                    text: typeof message === 'string' ? message : JSON.stringify(message)
                                });
                            }
                        } catch (e) {
                            console.error(e);
                        }
                    }
                });
            });
        });
    </script>
@endpush
