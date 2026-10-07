@php
    $_title = 'Surat Permintaan Perpanjangan Penahanan Ke Ketua Pengadilan Negeri (Kedua 30 Hari) (S-22)';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
@endpush

@section('content')

    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"><i
            class="bi bi-arrow-left"></i> Kembali ke Progress Perkara</a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Tambah Surat Permintaan Perpanjangan Penahanan PN (Kedua 30 Hari)</h5>

            <div class="alert alert-danger" id="attentionBox">
                <div class="text-center">
                    <b>
                        PERHATIAN !<br />
                        <br />
                        DATA INI WAJIB DIISI DENGAN DETAIL DAN LENGKAP KARENA AKAN DIPERTUKARKAN DENGAN APARAT PENEGAK HUKUM
                        LAINNYA DALAM KERANGKA SISTEM PENANGANAN PERKARA TERPADU BERBASIS TEKNOLOGI INFORMASI (SPPT-TI).
                    </b>
                </div>
            </div>
            
            @if ($errors->any())
                <div class="card-body">
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="card-body">
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                </div>
            @endif
        </div>

        <div class="box-body">
            <form action="{{ route('doc.surat-permintaan-perpanjangan-penahanan-lanjutan-kedua-document.store', ['accident_id' => $accidentId]) }}"
                method="POST" id="s22Form" novalidate>
                @csrf
                <input type="hidden" name="accident_id" id="accident_id" value="{{ $accidentId }}">

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="accidentNumber">Nomor LP</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="accidentNumber" type="text"
                            class="form-control font-weight-bold"
                            name="accidentNumber" value="{{ $accident->no_lp }}" required placeholder="" readonly>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nomor_surat">Nomor Dokumen<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nomor_surat" type="text" class="form-control @error('nomor_surat') is-invalid @enderror" name="nomor_surat"
                            value="{{ old('nomor_surat') }}" required placeholder="Contoh: B/124/V/2026/Lantas">
                        @error('nomor_surat')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="tanggal_surat">Tanggal Surat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input id="tanggal_surat" type="text" class="form-control @error('tanggal_surat') is-invalid @enderror" name="tanggal_surat"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('tanggal_surat', date('Y-m-d')) }}" data-provide="datepicker" required>
                        @error('tanggal_surat')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="klasifikasi_surat_id">Klasifikasi Surat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2 @error('klasifikasi_surat_id') is-invalid @enderror" name="klasifikasi_surat_id" id="klasifikasi_surat_id" required>
                            <option value="">-- Pilih Klasifikasi --</option>
                            <option value="BIASA" {{ old('klasifikasi_surat_id', 'BIASA') == 'BIASA' ? 'selected' : '' }}>Biasa</option>
                            <option value="RAHASIA" {{ old('klasifikasi_surat_id') == 'RAHASIA' ? 'selected' : '' }}>Rahasia</option>
                            <option value="SANGAT RAHASIA" {{ old('klasifikasi_surat_id') == 'SANGAT RAHASIA' ? 'selected' : '' }}>Sangat Rahasia</option>
                        </select>
                        @error('klasifikasi_surat_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>


                <hr>
                <h5 class="fw-bold text-blue-dark">Instansi Pengadilan & Kejaksaan Tujuan</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="kejaksaan_id">Kejaksaan Negeri Tujuan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2 @error('kejaksaan_id') is-invalid @enderror" name="kejaksaan_id" id="kejaksaan_id" required>
                            <option value="">-- Pilih Kejaksaan Negeri Tujuan --</option>
                            @foreach ($prosecutors as $prosecutor)
                                <option value="{{ $prosecutor->id }}" data-name="{{ $prosecutor->name }}" {{ old('kejaksaan_id', $defaultKejaksaanId) == $prosecutor->id ? 'selected' : '' }}>
                                    {{ $prosecutor->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Kejaksaan Negeri yang menangani wilayah hukum perkara ini</small>
                        @error('kejaksaan_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nama_pengadilan_negeri">Pengadilan Negeri Tujuan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nama_pengadilan_negeri" type="text" class="form-control @error('nama_pengadilan_negeri') is-invalid @enderror" name="nama_pengadilan_negeri"
                            value="{{ old('nama_pengadilan_negeri', $defaultNamaPengadilanNegeri) }}" placeholder="Contoh: PENGADILAN NEGERI SIMALUNGUN" required>
                        <small class="text-muted">Ketua Pengadilan Negeri yang dituju untuk permohonan izin perpanjangan penahanan</small>
                        @error('nama_pengadilan_negeri')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>
                <h5 class="fw-bold text-blue-dark">Rujukan Dokumen Pendahulu</h5>

                <!-- Poin 1.f - Sprint Sidik -->
                <div class="card bg-light border-0 mb-3 p-3">
                    <span class="fw-bold text-dark mb-2">Surat Perintah Penyidikan <small class="text-muted font-weight-normal">(Otomatis dari Sistem)</small></span>
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="nomor_surat_perintah_penyidikan">Nomor Sprint Sidik<span class="text-danger fs-5">*</span></label>
                            <input id="nomor_surat_perintah_penyidikan" type="text" class="form-control @error('nomor_surat_perintah_penyidikan') is-invalid @enderror" name="nomor_surat_perintah_penyidikan"
                                value="{{ old('nomor_surat_perintah_penyidikan', $defaultNomorSprintSidik) }}" placeholder="Nomor Surat Perintah Penyidikan" required readonly style="background-color: #e9ecef;">
                            @error('nomor_surat_perintah_penyidikan')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="tanggal_surat_perintah_penyidikan">Tanggal Sprint Sidik<span class="text-danger fs-5">*</span></label>
                            <input id="tanggal_surat_perintah_penyidikan" type="text" class="form-control @error('tanggal_surat_perintah_penyidikan') is-invalid @enderror" name="tanggal_surat_perintah_penyidikan"
                                placeholder="YYYY-MM-DD" autocomplete="off"
                                value="{{ old('tanggal_surat_perintah_penyidikan', $defaultTanggalSprintSidik) }}" required readonly style="background-color: #e9ecef; pointer-events: none;">
                            @error('tanggal_surat_perintah_penyidikan')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Poin 1.g - SPDP -->
                <div class="card bg-light border-0 mb-3 p-3">
                    <span class="fw-bold text-dark mb-2">Surat Pemberitahuan Dimulainya Penyidikan (SPDP) <small class="text-muted font-weight-normal">(Otomatis dari Sistem)</small></span>
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="nomor_spdp">Nomor SPDP<span class="text-danger fs-5">*</span></label>
                            <input id="nomor_spdp" type="text" class="form-control @error('nomor_spdp') is-invalid @enderror" name="nomor_spdp"
                                value="{{ old('nomor_spdp', $defaultNomorSpdp) }}" placeholder="Nomor SPDP" required readonly style="background-color: #e9ecef;">
                            @error('nomor_spdp')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="tanggal_spdp">Tanggal SPDP<span class="text-danger fs-5">*</span></label>
                            <input id="tanggal_spdp" type="text" class="form-control @error('tanggal_spdp') is-invalid @enderror" name="tanggal_spdp"
                                placeholder="YYYY-MM-DD" autocomplete="off"
                                value="{{ old('tanggal_spdp', $defaultTanggalSpdp) }}" required readonly style="background-color: #e9ecef; pointer-events: none;">
                            @error('tanggal_spdp')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Poin 1.h - SKET Tersangka -->
                <div class="card bg-light border-0 mb-3 p-3">
                    <span class="fw-bold text-dark mb-2">Surat Ketetapan tentang Penetapan Tersangka <small class="text-muted font-weight-normal">(Otomatis dari Sistem)</small></span>
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="nomor_sket_tersangka">Nomor S.Ket Penetapan Tersangka<span class="text-danger fs-5">*</span></label>
                            <input id="nomor_sket_tersangka" type="text" class="form-control @error('nomor_sket_tersangka') is-invalid @enderror" name="nomor_sket_tersangka"
                                value="{{ old('nomor_sket_tersangka', $defaultNomorSketTersangka) }}" placeholder="Nomor S.Ket Penetapan Tersangka" required readonly style="background-color: #e9ecef;">
                            @error('nomor_sket_tersangka')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="tanggal_sket_tersangka">Tanggal S.Ket Penetapan Tersangka<span class="text-danger fs-5">*</span></label>
                            <input id="tanggal_sket_tersangka" type="text" class="form-control @error('tanggal_sket_tersangka') is-invalid @enderror" name="tanggal_sket_tersangka"
                                placeholder="YYYY-MM-DD" autocomplete="off"
                                value="{{ old('tanggal_sket_tersangka', $defaultTanggalSketTersangka) }}" required readonly style="background-color: #e9ecef; pointer-events: none;">
                            @error('tanggal_sket_tersangka')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Poin 1.i - Sprint Penahanan Penyidik S-17 -->
                <div class="card bg-light border-0 mb-3 p-3">
                    <span class="fw-bold text-dark mb-2">Surat Perintah Penahanan Penyidik (S-17)</span>
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="surat_perintah_penahanan_document_id">Pilih Dokumen S-17 Terkait (Opsional)</label>
                            <select class="form-control select2" name="surat_perintah_penahanan_document_id" id="surat_perintah_penahanan_document_id">
                                <option value="">-- Hubungkan Dokumen S-17 --</option>
                                @if(isset($s17Documents))
                                    @foreach($s17Documents as $s17)
                                        <option value="{{ $s17->id }}" data-nomor="{{ $s17->nomor ?? $s17->document_number }}" data-tanggal="{{ $s17->tanggal ?? $s17->document_date }}" {{ (old('surat_perintah_penahanan_document_id', $defaultS17Id ?? null) == $s17->id) ? 'selected' : '' }}>
                                            {{ ($s17->nomor ?? $s17->document_number ?? 'S-17') . ' (' . ($s17->tanggal ? Carbon\Carbon::parse($s17->tanggal)->format('d/m/Y') : ($s17->document_date ? Carbon\Carbon::parse($s17->document_date)->format('d/m/Y') : '-')) . ')' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <small class="text-muted">Pilih dokumen S-17 untuk menautkan relasi dan mengisi otomatis nomor & tanggal di bawah.</small>
                        </div>
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="nomor_surat_perintah_penahanan">Nomor Sprint Penahanan<span class="text-danger fs-5">*</span></label>
                            <input id="nomor_surat_perintah_penahanan" type="text" class="form-control @error('nomor_surat_perintah_penahanan') is-invalid @enderror" name="nomor_surat_perintah_penahanan"
                                value="{{ old('nomor_surat_perintah_penahanan', $defaultNomorSprintPenahanan) }}" placeholder="Contoh: Sp.Han/12/IV/2026/Lantas" required readonly style="background-color: #e9ecef;">
                            @error('nomor_surat_perintah_penahanan')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="tanggal_surat_perintah_penahanan">Tanggal Sprint Penahanan<span class="text-danger fs-5">*</span></label>
                            <input id="tanggal_surat_perintah_penahanan" type="text" class="form-control @error('tanggal_surat_perintah_penahanan') is-invalid @enderror" name="tanggal_surat_perintah_penahanan"
                                placeholder="YYYY-MM-DD" autocomplete="off"
                                value="{{ old('tanggal_surat_perintah_penahanan', $defaultTanggalSprintPenahanan) }}" required readonly style="background-color: #e9ecef; pointer-events: none;">
                            @error('tanggal_surat_perintah_penahanan')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Poin 1.j - Surat Perpanjangan Kejaksaan -->
                <div class="card bg-light border-0 mb-3 p-3">
                    <span class="fw-bold text-dark mb-2">Surat Perpanjangan Penahanan dari Kejaksaan</span>
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="surat_permohonan_perpanjangan_penahanan_kejaksaan_document_id">Pilih Dokumen S-21 Terkait (Opsional)</label>
                            <select class="form-control select2" name="surat_permohonan_perpanjangan_penahanan_kejaksaan_document_id" id="surat_permohonan_perpanjangan_penahanan_kejaksaan_document_id">
                                <option value="">-- Hubungkan Dokumen S-21 --</option>
                                @if(isset($s21Documents))
                                    @foreach($s21Documents as $s21)
                                        <option value="{{ $s21->id }}" data-nomor="{{ $s21->nomor ?? $s21->document_number }}" data-tanggal="{{ $s21->tanggal ?? $s21->document_date }}" {{ (old('surat_permohonan_perpanjangan_penahanan_kejaksaan_document_id', $defaultS21Id ?? null) == $s21->id) ? 'selected' : '' }}>
                                            {{ ($s21->nomor ?? $s21->document_number ?? 'S-21') . ' (' . ($s21->tanggal ? Carbon\Carbon::parse($s21->tanggal)->format('d/m/Y') : ($s21->document_date ? Carbon\Carbon::parse($s21->document_date)->format('d/m/Y') : '-')) . ')' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <small class="text-muted">Pilih dokumen S-21 untuk menautkan relasi dan mengisi otomatis nomor & tanggal di bawah.</small>
                        </div>
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="nomor_surat_perpanjangan_kejaksaan">Nomor Perpanjangan Kejaksaan<span class="text-danger fs-5">*</span></label>
                            <input id="nomor_surat_perpanjangan_kejaksaan" type="text" class="form-control @error('nomor_surat_perpanjangan_kejaksaan') is-invalid @enderror" name="nomor_surat_perpanjangan_kejaksaan"
                                value="{{ old('nomor_surat_perpanjangan_kejaksaan', $defaultNomorPerpanjanganKejaksaan) }}" placeholder="Nomor Surat Perpanjangan Kejaksaan" required readonly style="background-color: #e9ecef;">
                            @error('nomor_surat_perpanjangan_kejaksaan')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="tanggal_surat_perpanjangan_kejaksaan">Tanggal Perpanjangan Kejaksaan<span class="text-danger fs-5">*</span></label>
                            <input id="tanggal_surat_perpanjangan_kejaksaan" type="text" class="form-control @error('tanggal_surat_perpanjangan_kejaksaan') is-invalid @enderror" name="tanggal_surat_perpanjangan_kejaksaan"
                                placeholder="YYYY-MM-DD" autocomplete="off"
                                value="{{ old('tanggal_surat_perpanjangan_kejaksaan', $defaultTanggalPerpanjanganKejaksaan) }}" required readonly style="background-color: #e9ecef; pointer-events: none;">
                            @error('tanggal_surat_perpanjangan_kejaksaan')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Poin 1.k - Sprint Perpanjangan Penahanan (JPU) -->
                <div class="card bg-light border-0 mb-3 p-3">
                    <span class="fw-bold text-dark mb-2">Surat Perintah Perpanjangan Penahanan (JPU)</span>
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="nomor_surat_perintah_perpanjangan_penahanan">Nomor Sprint Perpanjangan Penahanan (JPU)<span class="text-danger fs-5">*</span></label>
                            <input id="nomor_surat_perintah_perpanjangan_penahanan" type="text" class="form-control @error('nomor_surat_perintah_perpanjangan_penahanan') is-invalid @enderror" name="nomor_surat_perintah_perpanjangan_penahanan"
                                value="{{ old('nomor_surat_perintah_perpanjangan_penahanan', $defaultNomorSprintPerpanjanganJpu) }}" placeholder="Contoh: Sp.Jang.Han/05/V/2026/Lantas" required readonly style="background-color: #e9ecef;">
                            <small class="text-muted"><i class="bi bi-info-circle"></i> Nomor Sprint Perpanjangan Penahanan dari JPU diambil otomatis dari dokumen relasi sebelumnya.</small>
                            @error('nomor_surat_perintah_perpanjangan_penahanan')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="tanggal_surat_perintah_perpanjangan_penahanan">Tanggal Sprint Perpanjangan Penahanan (JPU)<span class="text-danger fs-5">*</span></label>
                            <input id="tanggal_surat_perintah_perpanjangan_penahanan" type="text" class="form-control @error('tanggal_surat_perintah_perpanjangan_penahanan') is-invalid @enderror" name="tanggal_surat_perintah_perpanjangan_penahanan"
                                placeholder="YYYY-MM-DD" autocomplete="off"
                                value="{{ old('tanggal_surat_perintah_perpanjangan_penahanan', $defaultTanggalSprintPerpanjanganJpu) }}" required readonly style="background-color: #e9ecef; pointer-events: none;">
                            <small class="text-muted"><i class="bi bi-info-circle"></i> Tanggal Sprint Perpanjangan Penahanan dari JPU diambil otomatis dari dokumen relasi sebelumnya.</small>
                            @error('tanggal_surat_perintah_perpanjangan_penahanan')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Poin 1.l - S.Ket Perpanjangan KPN Pertama (KPN1) -->
                <div class="card bg-light border-0 mb-3 p-3">
                    <span class="fw-bold text-dark mb-2">Surat Ketetapan Perpanjangan Penahanan Ketua Pengadilan Negeri (KPN1)</span>
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="surat_permintaan_perpanjangan_penahanan_lanjutan_document_id">Pilih Dokumen S-22 Pertama Terkait (Opsional)</label>
                            <select class="form-control select2" name="surat_permintaan_perpanjangan_penahanan_lanjutan_document_id" id="surat_permintaan_perpanjangan_penahanan_lanjutan_document_id">
                                <option value="">-- Hubungkan Dokumen S-22 Pertama --</option>
                                @if(isset($s22PertamaDocuments))
                                    @foreach($s22PertamaDocuments as $s22p)
                                        <option value="{{ $s22p->id }}" data-nomor="{{ $s22p->nomor_surat ?? $s22p->document_number }}" data-tanggal="{{ $s22p->tanggal_surat ?? $s22p->document_date }}" data-akhir="{{ $s22p->tanggal_akhir_perpanjangan_penahanan }}" data-sprint-jpu-nomor="{{ $s22p->nomor_surat_perintah_perpanjangan_penahanan }}" data-sprint-jpu-tanggal="{{ $s22p->tanggal_surat_perintah_perpanjangan_penahanan ? Carbon\Carbon::parse($s22p->tanggal_surat_perintah_perpanjangan_penahanan)->format('Y-m-d') : '' }}" {{ (old('surat_permintaan_perpanjangan_penahanan_lanjutan_document_id', $defaultS22PertamaId ?? null) == $s22p->id) ? 'selected' : '' }}>
                                            {{ ($s22p->nomor_surat ?? $s22p->document_number ?? 'S-22 Pertama') . ' (' . ($s22p->tanggal_surat ? Carbon\Carbon::parse($s22p->tanggal_surat)->format('d/m/Y') : ($s22p->document_date ? Carbon\Carbon::parse($s22p->document_date)->format('d/m/Y') : '-')) . ')' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <small class="text-muted">Pilih permohonan S-22 pertama untuk menautkan relasi dan mengisi otomatis rujukan KPN1.</small>
                        </div>
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="nomor_sket_perpanjangan_kpn_pertama">Nomor S.Ket Perpanjangan KPN1<span class="text-danger fs-5">*</span></label>
                            <input id="nomor_sket_perpanjangan_kpn_pertama" type="text" class="form-control @error('nomor_sket_perpanjangan_kpn_pertama') is-invalid @enderror" name="nomor_sket_perpanjangan_kpn_pertama"
                                value="{{ old('nomor_sket_perpanjangan_kpn_pertama', $defaultNomorSketKpn1) }}" placeholder="Nomor Surat Penetapan / Izin KPN Pertama" required readonly style="background-color: #e9ecef;">
                            <small class="text-muted"><i class="bi bi-info-circle"></i> Nomor Surat Ketetapan KPN1 diambil otomatis dari penetapan S-22 Pertama.</small>
                            @error('nomor_sket_perpanjangan_kpn_pertama')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="tanggal_sket_perpanjangan_kpn_pertama">Tanggal S.Ket Perpanjangan KPN1<span class="text-danger fs-5">*</span></label>
                            <input id="tanggal_sket_perpanjangan_kpn_pertama" type="text" class="form-control @error('tanggal_sket_perpanjangan_kpn_pertama') is-invalid @enderror" name="tanggal_sket_perpanjangan_kpn_pertama"
                                placeholder="YYYY-MM-DD" autocomplete="off"
                                value="{{ old('tanggal_sket_perpanjangan_kpn_pertama', $defaultTanggalSketKpn1) }}" required readonly style="background-color: #e9ecef; pointer-events: none;">
                            <small class="text-muted"><i class="bi bi-info-circle"></i> Tanggal Surat Ketetapan KPN1 diambil otomatis dari penetapan S-22 Pertama.</small>
                            @error('tanggal_sket_perpanjangan_kpn_pertama')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Poin 1.m - Sprint Perpanjangan Penahanan Penyidik (KPN1) -->
                <div class="card bg-light border-0 mb-3 p-3">
                    <span class="fw-bold text-dark mb-2">Surat Perintah Perpanjangan Penahanan Penyidik (KPN1)</span>
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="nomor_surat_perintah_perpanjangan_penahanan_kpn_pertama">Nomor Sprint Perpanjangan Penahanan (KPN1)<span class="text-danger fs-5">*</span></label>
                            <input id="nomor_surat_perintah_perpanjangan_penahanan_kpn_pertama" type="text" class="form-control @error('nomor_surat_perintah_perpanjangan_penahanan_kpn_pertama') is-invalid @enderror" name="nomor_surat_perintah_perpanjangan_penahanan_kpn_pertama"
                                value="{{ old('nomor_surat_perintah_perpanjangan_penahanan_kpn_pertama', $defaultNomorSprintKpn1) }}" placeholder="Nomor Sprint Perpanjangan Penahanan (KPN1)" required>
                            @error('nomor_surat_perintah_perpanjangan_penahanan_kpn_pertama')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="tanggal_surat_perintah_perpanjangan_penahanan_kpn_pertama">Tanggal Sprint Perpanjangan Penahanan (KPN1)<span class="text-danger fs-5">*</span></label>
                            <input id="tanggal_surat_perintah_perpanjangan_penahanan_kpn_pertama" type="text" class="form-control @error('tanggal_surat_perintah_perpanjangan_penahanan_kpn_pertama') is-invalid @enderror" name="tanggal_surat_perintah_perpanjangan_penahanan_kpn_pertama"
                                placeholder="YYYY-MM-DD" autocomplete="off"
                                value="{{ old('tanggal_surat_perintah_perpanjangan_penahanan_kpn_pertama', $defaultTanggalSprintKpn1) }}" data-provide="datepicker" required>
                            @error('tanggal_surat_perintah_perpanjangan_penahanan_kpn_pertama')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr>
                <h5 class="fw-bold text-blue-dark">Detail Masa Perpanjangan Kedua</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="pengadilan_negeri_akhir_tanggal">Tgl Berakhir Penahanan PN<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input id="pengadilan_negeri_akhir_tanggal" type="text" class="form-control @error('pengadilan_negeri_akhir_tanggal') is-invalid @enderror" name="pengadilan_negeri_akhir_tanggal"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('pengadilan_negeri_akhir_tanggal', $defaultPengadilanNegeriAkhirTanggal) }}" readonly style="background-color: #e9ecef; pointer-events: none;" required>
                        <small class="text-muted"><i class="bi bi-info-circle"></i> Tanggal berakhirnya masa perpanjangan penahanan dari Ketua Pengadilan Negeri (Pertama 30 Hari) diambil otomatis dari dokumen S-22 Pertama.</small>
                        @error('pengadilan_negeri_akhir_tanggal')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="waktu_penahanan_hari">Lama Penahanan (Hari)<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input id="waktu_penahanan_hari" type="number" class="form-control @error('waktu_penahanan_hari') is-invalid @enderror" name="waktu_penahanan_hari"
                            value="30" readonly style="background-color: #e9ecef;" required>
                        <small class="text-muted"><i class="bi bi-info-circle"></i> Lama perpanjangan penahanan lanjutan kedua ke Pengadilan Negeri telah ditetapkan 30 hari.</small>
                        @error('waktu_penahanan_hari')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="tanggal_mulai_perpanjangan_penahanan">Tanggal Mulai<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input id="tanggal_mulai_perpanjangan_penahanan" type="text" class="form-control @error('tanggal_mulai_perpanjangan_penahanan') is-invalid @enderror" name="tanggal_mulai_perpanjangan_penahanan"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('tanggal_mulai_perpanjangan_penahanan', $defaultTanggalMulai) }}" data-provide="datepicker" required>
                        @error('tanggal_mulai_perpanjangan_penahanan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="tanggal_akhir_perpanjangan_penahanan">Tanggal Akhir<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input id="tanggal_akhir_perpanjangan_penahanan" type="text" class="form-control" name="tanggal_akhir_perpanjangan_penahanan"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('tanggal_akhir_perpanjangan_penahanan', $defaultTanggalAkhir) }}" readonly required>
                        <small class="text-muted fst-italic">Tanggal akhir dihitung otomatis berdasarkan tanggal mulai dan lama penahanan.</small>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="prison_id">Tempat Penahanan / Rutan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select id="prison_id" name="prison_id" class="form-select select2 @error('rutan_name') is-invalid @enderror" required>
                            <option value="polres" data-name="{{ $defaultRutanName }}" {{ old('prison_id', $defaultPrisonId) == 'polres' || (empty(old('prison_id', $defaultPrisonId)) && (!old('rutan_name') || old('rutan_name') == $defaultRutanName)) ? 'selected' : '' }}>
                                {{ $defaultRutanName }} (Internal Kepolisian)
                            </option>
                            @if(isset($prisonsGrouped) && $prisonsGrouped->isNotEmpty())
                                @foreach ($prisonsGrouped as $province => $prisons)
                                    <optgroup label="Provinsi {{ $province }}">
                                        @foreach ($prisons as $prison)
                                            <option value="{{ $prison->id }}" data-name="{{ $prison->name }}"
                                                {{ (string) old('prison_id', $defaultPrisonId) === (string) $prison->id || old('rutan_name') === $prison->name ? 'selected' : '' }}>
                                                {{ $prison->name }}{{ $prison->branch ? ' ('.$prison->branch.')' : '' }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            @endif
                        </select>
                        <input type="hidden" id="rutan_name" name="rutan_name" value="{{ old('rutan_name', $defaultRutanName) }}">
                        <small class="text-muted fst-italic">Pilih Rutan dari master data atau opsi Rutan internal kepolisian setempat.</small>
                        @error('rutan_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="alasan_perpanjangan">Alasan Perpanjangan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <textarea id="alasan_perpanjangan" class="form-control @error('alasan_perpanjangan') is-invalid @enderror" name="alasan_perpanjangan" rows="3"
                            placeholder="Masukkan alasan mengapa masa penahanan perlu diperpanjang..." required>{{ old('alasan_perpanjangan', $defaultAlasan) }}</textarea>
                        @error('alasan_perpanjangan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>
                <h5 class="fw-bold text-blue-dark">Undang-Undang yang Dikenakan<span class="text-danger fs-5">*</span></h5>

                <div class="alert alert-info py-2 my-2" role="alert">
                    <i class="bi bi-info-circle-fill"></i> Data Undang-Undang dan Pasal diambil otomatis dari Surat Perintah Penyidikan terkait.
                </div>

                <div class="row col-12 my-2 ms-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-3" id="lawTable">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th scope="col">Jenis Kejahatan</th>
                                    <th scope="col">Golongan Kejahatan</th>
                                    <th scope="col">Undang-Undang</th>
                                    <th scope="col">Pasal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($initialMainLaws as $law)
                                    <tr class="text-center">
                                        <td>{{ $law['crime_type_name'] ?? '-' }}</td>
                                        <td>{{ $law['crime_class_name'] ?? '-' }}</td>
                                        <td>{{ $law['crime_constitution_name'] ?? '-' }}</td>
                                        <td><span class="badge bg-primary fs-6">{{ $law['constitution_chapter'] ?? '-' }}</span></td>
                                    </tr>
                                    <input type="hidden" name="lawCrimeTypeIds[]" value="{{ $law['crime_type_id'] }}">
                                    <input type="hidden" name="lawCrimeClassIds[]" value="{{ $law['crime_class_id'] }}">
                                    <input type="hidden" name="lawCrimeConstitutionIds[]" value="{{ $law['crime_constitution_id'] }}">
                                    <input type="hidden" name="lawCrimeConstitutionChapters[]" value="{{ $law['constitution_chapter'] }}">
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted fst-italic">Tidak ada data undang-undang dari Surat Perintah Penyidikan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(!empty($initialAdditionalLaws))
                        <div class="mb-3">
                            <label class="fw-bold">Undang-Undang Khusus Tambahan:</label>
                            <ul class="list-group">
                                @foreach($initialAdditionalLaws as $addLaw)
                                    <li class="list-group-item">{{ $addLaw['constitution'] ?? '-' }}</li>
                                    <input type="hidden" name="lawAdditionalNames[]" value="{{ $addLaw['constitution'] }}">
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="pasal_diduga">Pasal yang Dipersangkakan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <textarea id="pasal_diduga" class="form-control @error('pasal_diduga') is-invalid @enderror" name="pasal_diduga" rows="2"
                            placeholder="Contoh: Pasal 310 ayat (4) Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan" readonly style="background-color: #e9ecef;" required>{{ old('pasal_diduga', $defaultPasalDiduga) }}</textarea>
                        <small class="text-muted"><i class="bi bi-info-circle"></i> Diambil otomatis dari Surat Perintah Penyidikan (Poin 2 Surat).</small>
                        @error('pasal_diduga')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="dugaan_tindak_pidana">Uraian Dugaan Tindak Pidana<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <textarea id="dugaan_tindak_pidana" class="form-control @error('dugaan_tindak_pidana') is-invalid @enderror" name="dugaan_tindak_pidana" rows="3"
                            placeholder="Uraian dugaan tindak pidana..." readonly style="background-color: #e9ecef;" required>{{ old('dugaan_tindak_pidana', $defaultDugaanTindakPidana) }}</textarea>
                        <small class="text-muted"><i class="bi bi-info-circle"></i> Diambil otomatis dari data Laporan Polisi (Uraian Kejadian / Kerusakan).</small>
                        @error('dugaan_tindak_pidana')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>
                <h5 class="fw-bold text-blue-dark">Pihak Terlibat</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="suspects">Pilih Tersangka<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2 @error('suspects') is-invalid @enderror" name="suspects[]" id="suspects" multiple="multiple" data-placeholder="Pilih Tersangka..." required>
                            @foreach ($suspects as $suspect)
                                <option value="{{ $suspect->id }}" {{ in_array($suspect->id, old('suspects', $selectedSuspectIds)) ? 'selected' : '' }}>
                                    {{ $suspect->name }} (NIK: {{ $suspect->identity_number ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        @if($suspects->isEmpty())
                            <small class="text-muted fst-italic mt-1 d-block"><i class="bi bi-info-circle"></i> Belum ada data Tersangka yang terdaftar pada Laporan Polisi ini.</small>
                        @endif
                        @error('suspects')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="signatory">Pejabat Penandatangan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2 @error('signatory') is-invalid @enderror" name="signatory" id="signatory" required>
                            <option value="">-- Pilih Pejabat Penandatangan --</option>
                            @foreach ($authorizedSignatories as $signatory)
                                <option value="{{ $signatory->id }}" {{ old('signatory') == $signatory->id ? 'selected' : '' }}>
                                    {{ $signatory->full_name }} ({{ $signatory->register_number }}) - {{ $signatory->position->name ?? '' }}
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

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="contact_officer_id">Penyidik Pembantu<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2 @error('contact_officer_id') is-invalid @enderror" name="contact_officer_id" id="contact_officer_id" required>
                            <option value="">-- Pilih Personil Kontak --</option>
                            @foreach ($memberOfficers as $officer)
                                <option value="{{ $officer->id }}" {{ old('contact_officer_id') == $officer->id ? 'selected' : '' }}>
                                    {{ $officer->full_name }} ({{ $officer->register_number }})
                                </option>
                            @endforeach
                        </select>
                        @error('contact_officer_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="tembusan">Tembusan Lainnya<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-12">
                        <div id="carbonCopiesContainer">
                            @php
                                $initialTembusan = old('tembusan', ['Kepala Kepolisian Resor ' . ($accident->polres->full_name ?? '')]);
                            @endphp
                            @if(!empty($initialTembusan) && is_array($initialTembusan))
                                @foreach($initialTembusan as $cc)
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="tembusan[]" value="{{ $cc }}" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-danger removeCarbonCopiesButton" type="button">Hapus</button>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <button class="btn btn-primary mb-2 addCarbonCopiesButton" type="button">Tambah</button>

                        @error('tembusan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>

                @if(strtotime($accident->report_date) < strtotime('2024-01-01') || ($accident->police && $accident->police->is_whitelisted_document_legacy == true && strtotime($accident->police->start_date_whitelisted_document_legacy) <= strtotime($accident->report_date) && strtotime($accident->report_date) <= strtotime($accident->police->end_date_whitelisted_document_legacy)))
                    @include('docs.components.form.checkbox.is-legacy')
                @endif

                <div class="text-center">
                    <button type="submit" class="btn btn-dark-blue" id="s22FormSubmit">
                        <i class="bi bi-save"></i> {{ __('Simpan') }}
                    </button>
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"
                        class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> {{ __('Batal') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

    @if(strtotime($accident->report_date) < strtotime('2024-01-01') || ($accident->police && $accident->police->is_whitelisted_document_legacy == true && strtotime($accident->police->start_date_whitelisted_document_legacy) <= strtotime($accident->report_date) && strtotime($accident->report_date) <= strtotime($accident->police->end_date_whitelisted_document_legacy)))
        @include('docs.components.form.checkbox.is-legacy-js')
    @endif

    <script type="text/javascript">
        $(document).ready(function() {
            setInterval(function() {
                $('#attentionBox').toggleClass('alert-danger alert-warning');
            }, 1000);

            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            $('[data-provide="datepicker"]').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });

            $('[data-provide="datepicker"]').keydown(function(e) {
                e.preventDefault();
                return false;
            });

            // Datepicker calculation: tanggal mulai + durasi hari -> tanggal akhir (paten 30 hari)
            function updateTanggalAkhir() {
                var mulaiVal = $('#tanggal_mulai_perpanjangan_penahanan').val();
                var durasiVal = 30;

                if (mulaiVal) {
                    var mulaiDate = new Date(mulaiVal);
                    if (!isNaN(mulaiDate.getTime())) {
                        mulaiDate.setDate(mulaiDate.getDate() + (durasiVal - 1));
                        var yyyy = mulaiDate.getFullYear();
                        var mm = String(mulaiDate.getMonth() + 1).padStart(2, '0');
                        var dd = String(mulaiDate.getDate()).padStart(2, '0');
                        $('#tanggal_akhir_perpanjangan_penahanan').val(yyyy + '-' + mm + '-' + dd);
                    }
                }
            }

            $('#tanggal_mulai_perpanjangan_penahanan').on('change input', function() {
                updateTanggalAkhir();
            });

            // Auto uppercase & sync Pengadilan Negeri Tujuan berdasarkan Kejaksaan
            $('#kejaksaan_id').on('change', function() {
                var selectedOption = $(this).find('option:selected');
                var kejaksaanName = selectedOption.data('name') || selectedOption.text() || '';
                
                if (kejaksaanName && kejaksaanName !== '-- Pilih Kejaksaan Negeri Tujuan --') {
                    var cleaned = kejaksaanName.replace(/^(KEJAKSAAN\s+NEGERI\s+|KEJARI\s+)/i, '').trim();
                    if (cleaned) {
                        $('#nama_pengadilan_negeri').val(('PENGADILAN NEGERI ' + cleaned).toUpperCase());
                    }
                }
            });

            $('#nama_pengadilan_negeri').on('input', function() {
                var val = $(this).val();
                $(this).val(val.toUpperCase());
            });

            $('#prison_id').on('change select2:select', function() {
                var selectedOption = $(this).find('option:selected');
                var selectedName = selectedOption.data('name') || selectedOption.text().trim();
                $('#rutan_name').val(selectedName);
            });

            // Tembusan dinamis
            $(document).on("click", ".addCarbonCopiesButton", function() {
                var inputGroup = '<div class="input-group mb-2">' +
                    '<input type="text" class="form-control" name="tembusan[]" value="" required>' +
                    '<div class="input-group-append">' +
                    '<button class="btn btn-outline-danger removeCarbonCopiesButton" type="button">Hapus</button>' +
                    '</div>' +
                    '</div>';

                $("#carbonCopiesContainer").append(inputGroup);
            });

            $(document).on("click", ".removeCarbonCopiesButton", function() {
                $(this).closest(".input-group").remove();
            });

            // Auto-fill saat memilih dokumen relasi S-17, S-21, S-22 Pertama
            $('#surat_perintah_penahanan_document_id').on('change', function() {
                var $opt = $(this).find('option:selected');
                var nomor = $opt.data('nomor');
                var tanggal = $opt.data('tanggal');
                if (nomor) {
                    $('#nomor_surat_perintah_penahanan').val(nomor);
                }
                if (tanggal) {
                    $('#tanggal_surat_perintah_penahanan').val(tanggal.toString().substring(0, 10));
                }
            });

            $('#surat_permohonan_perpanjangan_penahanan_kejaksaan_document_id').on('change', function() {
                var $opt = $(this).find('option:selected');
                var nomor = $opt.data('nomor');
                var tanggal = $opt.data('tanggal');
                if (nomor) {
                    $('#nomor_surat_perpanjangan_kejaksaan').val(nomor);
                }
                if (tanggal) {
                    $('#tanggal_surat_perpanjangan_kejaksaan').val(tanggal.toString().substring(0, 10));
                }
            });

            $('#surat_permintaan_perpanjangan_penahanan_lanjutan_document_id').on('change', function() {
                var $opt = $(this).find('option:selected');
                var nomor = $opt.data('nomor');
                var tanggal = $opt.data('tanggal');
                var akhir = $opt.data('akhir');
                var sprintJpuNomor = $opt.data('sprint-jpu-nomor');
                var sprintJpuTanggal = $opt.data('sprint-jpu-tanggal');

                if (nomor) {
                    $('#nomor_sket_perpanjangan_kpn_pertama').val(nomor);
                }
                if (tanggal) {
                    $('#tanggal_sket_perpanjangan_kpn_pertama').val(tanggal.toString().substring(0, 10));
                }
                if (sprintJpuNomor) {
                    $('#nomor_surat_perintah_perpanjangan_penahanan').val(sprintJpuNomor);
                }
                if (sprintJpuTanggal) {
                    $('#tanggal_surat_perintah_perpanjangan_penahanan').val(sprintJpuTanggal.toString().substring(0, 10));
                }
                if (akhir) {
                    var akhirStr = akhir.toString().substring(0, 10);
                    $('#pengadilan_negeri_akhir_tanggal').val(akhirStr);
                    var akhirDate = new Date(akhirStr);
                    if (!isNaN(akhirDate.getTime())) {
                        akhirDate.setDate(akhirDate.getDate() + 1);
                        var yyyy = akhirDate.getFullYear();
                        var mm = String(akhirDate.getMonth() + 1).padStart(2, '0');
                        var dd = String(akhirDate.getDate()).padStart(2, '0');
                        $('#tanggal_mulai_perpanjangan_penahanan').val(yyyy + '-' + mm + '-' + dd).trigger('change');
                    }
                }
            });

            // =========================================================================
            // VALIDASI FORM & AUTO-FOCUS KE FIELD KOSONG (KONSISTEN DENGAN BA-HAN)
            // =========================================================================
            function hasFieldValue($field) {
                if (typeof $field === 'string') $field = $($field);
                if (!$field.length || $field.is(':disabled')) return true;
                if ($field.is('select')) {
                    var val = $field.val();
                    if (Array.isArray(val)) return val.length > 0;
                    return val && val !== '' && val !== '0' && val !== null;
                }
                if ($field.is('input[type="radio"]')) {
                    var name = $field.attr('name');
                    return $('input[name="' + name + '"]:checked').length > 0;
                }
                var raw = $field.val();
                return raw !== null && raw !== undefined && String(raw).trim() !== '';
            }

            function clearFieldError($field) {
                if (typeof $field === 'string') $field = $($field);
                if (!$field.length) return;
                $field.removeClass('is-invalid');
                if ($field.next('.select2-container').length) {
                    $field.next('.select2-container').find('.select2-selection').removeClass('border border-danger is-invalid');
                }
                $field.siblings('.frontend-error, .invalid-feedback').remove();
                $field.next('.frontend-error, .invalid-feedback').remove();
                var $container = $field.closest('.table-responsive, .input-group');
                if ($container.length) {
                    $container.siblings('.frontend-error, .invalid-feedback').remove();
                    $container.next('.frontend-error, .invalid-feedback').remove();
                }
            }

            function markError(fieldSelector, message, errorList) {
                var $field = $(fieldSelector);
                if (!$field.length) return;

                if ($field.is('table')) {
                    $field.addClass('border border-danger is-invalid');
                    var $wrapper = $field.closest('.table-responsive, .input-group');
                    var $container = $wrapper.length ? $wrapper : $field;
                    $container.siblings('.frontend-error, .invalid-feedback').remove();
                    $container.next('.frontend-error, .invalid-feedback').remove();
                    $container.after('<div class="invalid-feedback d-block frontend-error">' + message + '</div>');
                    if (Array.isArray(errorList)) errorList.push(message);
                    return;
                }

                $field.addClass('is-invalid');
                if ($field.next('.select2-container').length) {
                    $field.next('.select2-container').find('.select2-selection').addClass('border border-danger is-invalid');
                }
                var $target = $field.next('.select2-container').length ? $field.next('.select2-container') : $field;
                $target.siblings('.frontend-error, .invalid-feedback').remove();
                $target.next('.frontend-error, .invalid-feedback').remove();
                $target.after('<div class="invalid-feedback d-block frontend-error">' + message + '</div>');
                if (Array.isArray(errorList)) {
                    errorList.push(message);
                }
            }

            function scrollToFirstError() {
                setTimeout(function() {
                    var $firstError = $('.is-invalid:visible, .border-danger:visible, .frontend-error:visible').first();
                    if (!$firstError.length) {
                        $firstError = $('.is-invalid, .border-danger').first();
                    }
                    if ($firstError && $firstError.length) {
                        var $visibleTarget = $firstError;
                        if (!$firstError.is(':visible')) {
                            var $s2 = $firstError.next('.select2-container');
                            if ($s2.length && $s2.is(':visible')) {
                                $visibleTarget = $s2;
                            } else {
                                var $visParent = $firstError.closest('.input-group, .row, div:visible');
                                if ($visParent.length) {
                                    $visibleTarget = $visParent;
                                }
                            }
                        }

                        var domEl = $visibleTarget[0];
                        if (domEl && typeof domEl.scrollIntoView === 'function') {
                            try {
                                domEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            } catch (e) {
                                domEl.scrollIntoView(true);
                            }
                        }

                        var topPos = $visibleTarget.offset() ? $visibleTarget.offset().top : 0;
                        $('html, body, .content-wrapper, .wrapper, main').stop().animate({
                            scrollTop: Math.max(0, topPos - 120)
                        }, 400);

                        setTimeout(function() {
                            if ($firstError.is('input:not([type="hidden"]), textarea') && $firstError.is(':visible')) {
                                $firstError.trigger('focus');
                            } else if ($firstError.is('select') || $visibleTarget.hasClass('select2-container')) {
                                try {
                                    $firstError.select2('open');
                                } catch (e) {
                                    $visibleTarget.find('.select2-selection').trigger('focus');
                                }
                            }
                        }, 450);
                    } else {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                }, 100);
            }

            function checkInput(fieldSelector, label, errorList) {
                var $field = $(fieldSelector);
                if (!$field.length || $field.is(':disabled')) return;
                if (!hasFieldValue($field)) {
                    markError(fieldSelector, label + ' harus diisi', errorList);
                }
            }

            function checkSelect(fieldSelector, label, errorList) {
                var $field = $(fieldSelector);
                if (!$field.length || $field.is(':disabled')) return;
                if (!hasFieldValue($field)) {
                    markError(fieldSelector, label + ' harus dipilih', errorList);
                }
            }

            // Real-time pembersihan error saat user mengetik atau memilih opsi
            $(document).on('input change changeDate dp.change select2:select select2:clear select2:unselect blur', 'input, textarea, select', function() {
                var $el = $(this);
                if (hasFieldValue($el)) {
                    clearFieldError($el);
                }
            });

            // Continuous watcher untuk input yang diupdate oleh plugin popover / datepicker
            setInterval(function() {
                $('input.is-invalid, textarea.is-invalid, select.is-invalid').each(function() {
                    var $field = $(this);
                    if (hasFieldValue($field)) {
                        clearFieldError($field);
                    }
                });
            }, 200);

            // Validasi Submit Form (Konsisten dengan BA-HAN: tanpa SweetAlert saat error)
            $('#s22Form').on('submit', function(e) {
                e.preventDefault();
                
                var $btn = $('#s22FormSubmit');
                var originalHtml = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memvalidasi...');

                // Bersihkan pesan error sebelumnya
                $('.is-invalid').removeClass('is-invalid');
                $('.border.border-danger').removeClass('border border-danger');
                $('.select2-selection').removeClass('border border-danger is-invalid');
                $('.frontend-error').remove();
                $('.invalid-feedback').remove();

                var errors = [];

                // Validasi Frontend Field Wajib
                checkInput('#nomor_surat', 'Nomor Dokumen', errors);
                checkInput('#tanggal_surat', 'Tanggal Dokumen', errors);
                checkSelect('#klasifikasi_surat_id', 'Klasifikasi Dokumen', errors);
                checkSelect('#kejaksaan_id', 'Kejaksaan Negeri Tujuan', errors);
                checkInput('#nama_pengadilan_negeri', 'Pengadilan Negeri Tujuan', errors);
                checkInput('#nomor_surat_perintah_penyidikan', 'Nomor Sprint Sidik', errors);
                checkInput('#tanggal_surat_perintah_penyidikan', 'Tanggal Sprint Sidik', errors);
                checkInput('#nomor_spdp', 'Nomor SPDP', errors);
                checkInput('#tanggal_spdp', 'Tanggal SPDP', errors);
                checkInput('#nomor_sket_tersangka', 'Nomor S.Ket Penetapan Tersangka', errors);
                checkInput('#tanggal_sket_tersangka', 'Tanggal S.Ket Penetapan Tersangka', errors);
                checkInput('#nomor_surat_perintah_penahanan', 'Nomor Sprint Penahanan (S-17)', errors);
                checkInput('#tanggal_surat_perintah_penahanan', 'Tanggal Sprint Penahanan (S-17)', errors);
                checkInput('#nomor_surat_perpanjangan_kejaksaan', 'Nomor Perpanjangan Kejaksaan (S-21)', errors);
                checkInput('#tanggal_surat_perpanjangan_kejaksaan', 'Tanggal Perpanjangan Kejaksaan (S-21)', errors);
                checkInput('#nomor_surat_perintah_perpanjangan_penahanan', 'Nomor Sprint Perpanjangan Penahanan JPU', errors);
                checkInput('#tanggal_surat_perintah_perpanjangan_penahanan', 'Tanggal Sprint Perpanjangan Penahanan JPU', errors);
                checkInput('#nomor_sket_perpanjangan_kpn_pertama', 'Nomor S.Ket Perpanjangan KPN1', errors);
                checkInput('#tanggal_sket_perpanjangan_kpn_pertama', 'Tanggal S.Ket Perpanjangan KPN1', errors);
                checkInput('#nomor_surat_perintah_perpanjangan_penahanan_kpn_pertama', 'Nomor Sprint Perpanjangan Penahanan KPN1', errors);
                checkInput('#tanggal_surat_perintah_perpanjangan_penahanan_kpn_pertama', 'Tanggal Sprint Perpanjangan Penahanan KPN1', errors);
                checkInput('#pengadilan_negeri_akhir_tanggal', 'Tgl Berakhir Penahanan PN', errors);
                checkInput('#tanggal_mulai_perpanjangan_penahanan', 'Tanggal Mulai Perpanjangan', errors);
                checkInput('#tanggal_akhir_perpanjangan_penahanan', 'Tanggal Akhir Perpanjangan', errors);
                checkSelect('#prison_id', 'Tempat Penahanan / Rutan', errors);
                checkInput('#alasan_perpanjangan', 'Alasan Perpanjangan', errors);
                checkInput('#pasal_diduga', 'Pasal yang Dipersangkakan', errors);
                checkInput('#dugaan_tindak_pidana', 'Uraian Dugaan Tindak Pidana', errors);
                checkSelect('#suspects', 'Tersangka', errors);
                checkSelect('#signatory', 'Pejabat Penandatangan', errors);
                checkSelect('#contact_officer_id', 'Penyidik Pembantu', errors);

                $('input[name="tembusan[]"]').each(function(idx) {
                    if (!hasFieldValue($(this))) {
                        markError($(this), 'Tembusan ' + (idx + 1) + ' harus diisi', errors);
                    }
                });

                // Jika ada field kosong di frontend, langsung scroll & fokus ke field pertama (tanpa SweetAlert)
                if (errors.length > 0) {
                    $btn.prop('disabled', false).html(originalHtml);
                    scrollToFirstError();
                    return false;
                }

                $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
                var formData = $(this).serialize();
                var url = "{{ route('doc.surat-permintaan-perpanjangan-penahanan-lanjutan-kedua-document.api.validate-request-form', ['accident_id' => $accidentId]) }}";
                
                $.ajax({
                    url: url,
                    type: "POST",
                    dataType: 'json',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: response.message || 'Data valid, menyimpan dokumen...',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
                                $('#s22Form')[0].submit();
                            });
                        } else {
                            $btn.prop('disabled', false).html(originalHtml);
                            Swal.fire({
                                icon: 'error',
                                title: 'Perhatian',
                                text: response.message || 'Gagal memvalidasi form.'
                            });
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html(originalHtml);
                        try {
                            var res = xhr.responseJSON || (xhr.responseText ? JSON.parse(xhr.responseText) : null);
                            if (xhr.status === 422 && res && res.errors) {
                                var serverErrors = [];
                                $.each(res.errors, function(key, messages) {
                                    var msg = Array.isArray(messages) ? messages[0] : messages;
                                    var targetKey = key;
                                    if (key === 'suspect_id') targetKey = 'suspects';
                                    if (key === 'signatory_officer_id') targetKey = 'signatory';

                                    var $target = $('#' + targetKey + ', [name="' + targetKey + '"], [name="' + targetKey + '[]"], #' + key + ', [name="' + key + '"]');
                                    if ($target.length) {
                                        markError($target, msg, serverErrors);
                                    } else {
                                        serverErrors.push(msg);
                                    }
                                });

                                scrollToFirstError();
                                return false;
                            }
                        } catch (err) {}

                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Gagal memvalidasi form dokumen S-22 (Kode: ' + xhr.status + ').'
                        });
                    }
                });
            });
        });
    </script>
@endpush
