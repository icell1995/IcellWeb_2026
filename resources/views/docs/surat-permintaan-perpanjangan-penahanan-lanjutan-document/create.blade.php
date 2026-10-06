@php
    $_title = 'Surat Permintaan Perpanjangan Penahanan Ke Ketua Pengadilan Negeri (Pertama 30 Hari) (S-22)';
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
            <h5 class="fw-bold text-blue-dark">Tambah Surat Permintaan Perpanjangan Penahanan PN (Pertama 30 Hari)</h5>

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
            <form action="{{ route('doc.surat-permintaan-perpanjangan-penahanan-lanjutan-document.store', ['accident_id' => $accidentId]) }}"
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
                            value="{{ old('nomor_surat') }}" required placeholder="Contoh: B/123/IV/2026/Lantas">
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
                <h5 class="fw-bold text-blue-dark">Data SPDP & Kejaksaan Tujuan</h5>

                @if($spdpDocument)
                    <div class="alert alert-info py-2">
                        <i class="bi bi-info-circle-fill me-1"></i> SPDP Terdeteksi: Dokumen SPDP <b>{{ $spdpDocument->document_number }}</b> tanggal <b>{{ date('d-m-Y', strtotime($spdpDocument->document_date)) }}</b> telah dihubungkan otomatis.
                    </div>
                @endif

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="kejaksaan_id">Kejaksaan Tujuan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2 @error('kejaksaan_id') is-invalid @enderror" name="kejaksaan_id" id="kejaksaan_id" required>
                            <option value="">-- Pilih Kejaksaan --</option>
                            @foreach ($prosecutors as $prosecutor)
                                <option value="{{ $prosecutor->id }}" {{ old('kejaksaan_id', $defaultKejaksaanId) == $prosecutor->id ? 'selected' : '' }}>
                                    {{ $prosecutor->full_name ?? $prosecutor->name }}
                                </option>
                            @endforeach
                        </select>
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
                            value="{{ old('nama_pengadilan_negeri', $defaultNamaPengadilanNegeri) }}" placeholder="Contoh: PENGADILAN NEGERI CIBINONG" required>
                        <small class="text-muted">Secara otomatis disesuaikan dari Kejaksaan Tujuan, dan dapat diedit kembali jika diperlukan.</small>
                        @error('nama_pengadilan_negeri')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nomor_spdp">Nomor SPDP<span class="text-danger fs-5">*</span>
                        <small class="text-muted d-block font-weight-normal">(Otomatis dari Sistem)</small>
                    </label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nomor_spdp" type="text" class="form-control @error('nomor_spdp') is-invalid @enderror" name="nomor_spdp"
                            value="{{ old('nomor_spdp', $defaultNomorSpdp) }}" placeholder="Nomor SPDP" required readonly style="background-color: #e9ecef;">
                        @error('nomor_spdp')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="tanggal_spdp">Tanggal SPDP<span class="text-danger fs-5">*</span>
                        <small class="text-muted d-block font-weight-normal">(Otomatis dari Sistem)</small>
                    </label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input id="tanggal_spdp" type="text" class="form-control @error('tanggal_spdp') is-invalid @enderror" name="tanggal_spdp"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('tanggal_spdp', $defaultTanggalSpdp ? Carbon\Carbon::parse($defaultTanggalSpdp)->format('Y-m-d') : '') }}" required readonly style="background-color: #e9ecef; pointer-events: none;">
                        @error('tanggal_spdp')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>
                <h5 class="fw-bold text-blue-dark">Riwayat Surat Perintah & Penetapan Tersangka</h5>

                @if($sketTersangkaDocument)
                    <div class="alert alert-info py-2">
                        <i class="bi bi-info-circle-fill me-1"></i> Penetapan Tersangka Terdeteksi: S.Ket Penetapan Tersangka <b>{{ $sketTersangkaDocument->document_number }}</b> tanggal <b>{{ date('d-m-Y', strtotime($sketTersangkaDocument->document_date)) }}</b> telah dihubungkan otomatis.
                    </div>
                @endif

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nomor_sket_tersangka">No S.Ket Tersangka<span class="text-danger fs-5">*</span>
                        <small class="text-muted d-block font-weight-normal">(Otomatis dari Sistem)</small>
                    </label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nomor_sket_tersangka" type="text" class="form-control @error('nomor_sket_tersangka') is-invalid @enderror" name="nomor_sket_tersangka"
                            value="{{ old('nomor_sket_tersangka', $defaultNomorSketTersangka) }}" placeholder="Nomor Surat Ketetapan Tersangka" required readonly style="background-color: #e9ecef;">
                        @error('nomor_sket_tersangka')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="tanggal_sket_tersangka">Tgl S.Ket Tersangka<span class="text-danger fs-5">*</span>
                        <small class="text-muted d-block font-weight-normal">(Otomatis dari Sistem)</small>
                    </label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input id="tanggal_sket_tersangka" type="text" class="form-control @error('tanggal_sket_tersangka') is-invalid @enderror" name="tanggal_sket_tersangka"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('tanggal_sket_tersangka', $defaultTanggalSketTersangka ? Carbon\Carbon::parse($defaultTanggalSketTersangka)->format('Y-m-d') : '') }}" required readonly style="background-color: #e9ecef; pointer-events: none;">
                        @error('tanggal_sket_tersangka')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="surat_perintah_penahanan_document_id">Dokumen S-17 Terkait</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2 @error('surat_perintah_penahanan_document_id') is-invalid @enderror" name="surat_perintah_penahanan_document_id" id="surat_perintah_penahanan_document_id">
                            <option value="">-- Pilih Dokumen S-17 (Otomatis Isi / Tertaut) --</option>
                            @if(isset($s17Documents))
                                @foreach($s17Documents as $s17)
                                    <option value="{{ $s17->id }}" 
                                        data-nomor="{{ $s17->nomor ?? $s17->document_number }}" 
                                        data-tanggal="{{ $s17->tanggal ?? $s17->document_date }}"
                                        {{ (old('surat_perintah_penahanan_document_id', $defaultS17Id ?? null) == $s17->id) ? 'selected' : '' }}>
                                        {{ ($s17->nomor ?? $s17->document_number ?? 'S-17') . ' (' . ($s17->tanggal ? Carbon\Carbon::parse($s17->tanggal)->format('d/m/Y') : ($s17->document_date ? Carbon\Carbon::parse($s17->document_date)->format('d/m/Y') : '-')) . ')' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <small class="text-muted">Pilih surat perintah penahanan (S-17) untuk menautkan relasi dan mengisi otomatis nomor & tanggal di bawah.</small>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nomor_surat_perintah_penahanan">No Sprint Penahanan (S-17)<span class="text-danger fs-5">*</span>
                        <small class="text-muted d-block font-weight-normal">(Otomatis dari S-17)</small>
                    </label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nomor_surat_perintah_penahanan" type="text" class="form-control @error('nomor_surat_perintah_penahanan') is-invalid @enderror" name="nomor_surat_perintah_penahanan"
                            value="{{ old('nomor_surat_perintah_penahanan', $defaultNomorSprintPenahanan ?? '') }}" placeholder="Contoh: Sp.Han/12/IV/2026/Lantas" required readonly style="background-color: #e9ecef;">
                        @error('nomor_surat_perintah_penahanan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="tanggal_surat_perintah_penahanan">Tgl Sprint Penahanan<span class="text-danger fs-5">*</span>
                        <small class="text-muted d-block font-weight-normal">(Otomatis dari S-17)</small>
                    </label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input id="tanggal_surat_perintah_penahanan" type="text" class="form-control @error('tanggal_surat_perintah_penahanan') is-invalid @enderror" name="tanggal_surat_perintah_penahanan"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('tanggal_surat_perintah_penahanan', $defaultTanggalSprintPenahanan ? Carbon\Carbon::parse($defaultTanggalSprintPenahanan)->format('Y-m-d') : '') }}" required readonly style="background-color: #e9ecef; pointer-events: none;">
                        @error('tanggal_surat_perintah_penahanan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="surat_permohonan_perpanjangan_penahanan_kejaksaan_document_id">Dokumen S-21 Terkait</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2 @error('surat_permohonan_perpanjangan_penahanan_kejaksaan_document_id') is-invalid @enderror" name="surat_permohonan_perpanjangan_penahanan_kejaksaan_document_id" id="surat_permohonan_perpanjangan_penahanan_kejaksaan_document_id">
                            <option value="">-- Pilih Dokumen S-21 (Otomatis Isi / Tertaut) --</option>
                            @if(isset($s21Documents))
                                @foreach($s21Documents as $s21)
                                    <option value="{{ $s21->id }}" 
                                        data-nomor="{{ $s21->nomor ?? $s21->document_number }}" 
                                        data-tanggal="{{ $s21->tanggal ?? $s21->document_date }}"
                                        {{ (old('surat_permohonan_perpanjangan_penahanan_kejaksaan_document_id', $defaultS21Id ?? null) == $s21->id) ? 'selected' : '' }}>
                                        {{ ($s21->nomor ?? $s21->document_number ?? 'S-21') . ' (' . ($s21->tanggal ? Carbon\Carbon::parse($s21->tanggal)->format('d/m/Y') : ($s21->document_date ? Carbon\Carbon::parse($s21->document_date)->format('d/m/Y') : '-')) . ')' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <small class="text-muted">Pilih surat permohonan perpanjangan kejaksaan (S-21) untuk menautkan relasi dan mengisi otomatis nomor & tanggal di bawah.</small>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nomor_surat_perpanjangan_kejaksaan">No Perpanjangan Kejaksaan<span class="text-danger fs-5">*</span>
                        <small class="text-muted d-block font-weight-normal">(Otomatis dari S-21)</small>
                    </label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nomor_surat_perpanjangan_kejaksaan" type="text" class="form-control @error('nomor_surat_perpanjangan_kejaksaan') is-invalid @enderror" name="nomor_surat_perpanjangan_kejaksaan"
                            value="{{ old('nomor_surat_perpanjangan_kejaksaan', $defaultNomorPerpanjanganKejaksaan ?? '') }}" placeholder="Nomor Surat Perpanjangan dari Kejaksaan sebelumnya" required readonly style="background-color: #e9ecef;">
                        @error('nomor_surat_perpanjangan_kejaksaan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="tanggal_surat_perpanjangan_kejaksaan">Tgl Perpanjangan Kejaksaan<span class="text-danger fs-5">*</span>
                        <small class="text-muted d-block font-weight-normal">(Otomatis dari S-21)</small>
                    </label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input id="tanggal_surat_perpanjangan_kejaksaan" type="text" class="form-control @error('tanggal_surat_perpanjangan_kejaksaan') is-invalid @enderror" name="tanggal_surat_perpanjangan_kejaksaan"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('tanggal_surat_perpanjangan_kejaksaan', $defaultTanggalPerpanjanganKejaksaan ? Carbon\Carbon::parse($defaultTanggalPerpanjanganKejaksaan)->format('Y-m-d') : '') }}" required readonly style="background-color: #e9ecef; pointer-events: none;">
                        @error('tanggal_surat_perpanjangan_kejaksaan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nomor_surat_perintah_perpanjangan_penahanan">No Sprint Perpanjangan Penahanan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nomor_surat_perintah_perpanjangan_penahanan" type="text" class="form-control @error('nomor_surat_perintah_perpanjangan_penahanan') is-invalid @enderror" name="nomor_surat_perintah_perpanjangan_penahanan"
                            value="{{ old('nomor_surat_perintah_perpanjangan_penahanan') }}" placeholder="Contoh: Sp.Jang.Han/05/V/2026/Lantas" required>
                        <small class="text-muted">Surat Perintah Perpanjangan Penahanan dari Penyidik</small>
                        @error('nomor_surat_perintah_perpanjangan_penahanan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="tanggal_surat_perintah_perpanjangan_penahanan">Tgl Sprint Perpanjangan Penahanan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input id="tanggal_surat_perintah_perpanjangan_penahanan" type="text" class="form-control @error('tanggal_surat_perintah_perpanjangan_penahanan') is-invalid @enderror" name="tanggal_surat_perintah_perpanjangan_penahanan"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('tanggal_surat_perintah_perpanjangan_penahanan') }}" data-provide="datepicker" required>
                        @error('tanggal_surat_perintah_perpanjangan_penahanan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="kejaksaan_akhir_tanggal">Tgl Berakhir Penahanan Kejaksaan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input id="kejaksaan_akhir_tanggal" type="text" class="form-control @error('kejaksaan_akhir_tanggal') is-invalid @enderror" name="kejaksaan_akhir_tanggal"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('kejaksaan_akhir_tanggal') }}" data-provide="datepicker" required>
                        <small class="text-muted">Tanggal berakhirnya masa penahanan oleh Kejaksaan / Penuntut Umum sebelumnya</small>
                        @error('kejaksaan_akhir_tanggal')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>
                <h5 class="fw-bold text-blue-dark">Detail Masa Perpanjangan Lanjutan</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="waktu_penahanan_hari">Lama Penahanan (Hari)<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input id="waktu_penahanan_hari" type="number" class="form-control @error('waktu_penahanan_hari') is-invalid @enderror" name="waktu_penahanan_hari"
                            value="30" readonly style="background-color: #e9ecef;" required>
                        <small class="text-muted"><i class="bi bi-info-circle"></i> Lama perpanjangan penahanan lanjutan ke Pengadilan Negeri telah ditetapkan 30 hari.</small>
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
                            value="{{ old('tanggal_mulai_perpanjangan_penahanan') }}" data-provide="datepicker" required>
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
                            value="{{ old('tanggal_akhir_perpanjangan_penahanan') }}" readonly required>
                        <small class="text-muted fst-italic"><i class=""></i> Tanggal akhir dihitung otomatis berdasarkan tanggal mulai dan lama penahanan.</small>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="prison_id">Tempat Penahanan / Rutan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select id="prison_id" name="prison_id" class="form-select select2 @error('rutan_name') is-invalid @enderror" required>
                            <option value="polres" data-name="{{ $defaultRutanName }}" {{ old('prison_id') == 'polres' || (!old('prison_id') && (!old('rutan_name') || old('rutan_name') == $defaultRutanName)) ? 'selected' : '' }}>
                                {{ $defaultRutanName }} (Internal Kepolisian)
                            </option>
                            @if(isset($prisonsGrouped) && $prisonsGrouped->isNotEmpty())
                                @foreach ($prisonsGrouped as $province => $prisons)
                                    <optgroup label="Provinsi {{ $province }}">
                                        @foreach ($prisons as $prison)
                                            <option value="{{ $prison->id }}" data-name="{{ $prison->name }}"
                                                {{ (string) old('prison_id') === (string) $prison->id || old('rutan_name') === $prison->name ? 'selected' : '' }}>
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
                            placeholder="Masukkan alasan mengapa masa penahanan perlu diperpanjang..." required>{{ old('alasan_perpanjangan') }}</textarea>
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
                            placeholder="Uraian dugaan tindak pidana..." required>{{ old('dugaan_tindak_pidana', $defaultDugaanTindakPidana) }}</textarea>
                        <small class="text-muted"><i class="bi bi-info-circle"></i> Diambil otomatis dari Laporan Polisi (damage_lose_desc) dan dapat disesuaikan jika diperlukan (Poin 2 Surat).</small>
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

            function calculateEndDate() {
                var startDateStr = $('#tanggal_mulai_perpanjangan_penahanan').val();
                var days = 30; // Lama perpanjangan penahanan ke PN paten 30 hari
                if (startDateStr) {
                    var parts = startDateStr.split('-');
                    if (parts.length === 3) {
                        var year = parseInt(parts[0], 10);
                        var month = parseInt(parts[1], 10) - 1;
                        var day = parseInt(parts[2], 10);
                        var startDate = new Date(year, month, day);
                        startDate.setDate(startDate.getDate() + days - 1);
                        var yyyy = startDate.getFullYear();
                        var mm = String(startDate.getMonth() + 1).padStart(2, '0');
                        var dd = String(startDate.getDate()).padStart(2, '0');
                        $('#tanggal_akhir_perpanjangan_penahanan').val(yyyy + '-' + mm + '-' + dd);
                    }
                }
            }
            $('#tanggal_mulai_perpanjangan_penahanan').on('change changeDate input', calculateEndDate);
            if ($('#tanggal_mulai_perpanjangan_penahanan').val() && !$('#tanggal_akhir_perpanjangan_penahanan').val()) {
                calculateEndDate();
            }

            var userEditedPN = false;
            $('#nama_pengadilan_negeri').on('input', function() {
                userEditedPN = true;
            });

            $('#kejaksaan_id').on('change select2:select', function() {
                var selectedText = $(this).find('option:selected').text().trim();
                if (selectedText && !selectedText.includes('-- Pilih Kejaksaan --')) {
                    if (!userEditedPN || !$('#nama_pengadilan_negeri').val()) {
                        var pnVal = selectedText.replace(/kejaksaan/gi, 'PENGADILAN');
                        $('#nama_pengadilan_negeri').val(pnVal);
                    }
                }
            });

            $('#prison_id').on('change select2:select', function() {
                var selectedOption = $(this).find('option:selected');
                var selectedName = selectedOption.data('name') || selectedOption.text().trim();
                $('#rutan_name').val(selectedName);
            });

            // Tembusan dinamis
            $(".addCarbonCopiesButton").click(function() {
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

            // Auto-fill saat memilih dokumen relasi S-17 & S-21
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

            // AJAX Validation handling on form submit with SweetAlert2
            $('#s22Form').on('submit', function(e) {
                e.preventDefault();
                
                var $btn = $('#s22FormSubmit');
                var originalHtml = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');

                var formData = $(this).serialize();
                var url = "{{ route('doc.surat-permintaan-perpanjangan-penahanan-lanjutan-document.api.validate-request-form', ['accident_id' => $accidentId]) }}";
                
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
                                confirmButtonText: 'Ok'
                            }).then((result) => {
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
                            var res = JSON.parse(xhr.responseText);
                            if (xhr.status === 422 && res.errors) {
                                var errorMessages = '';
                                $.each(res.errors, function(key, value) {
                                    var msg = Array.isArray(value) ? value[0] : value;
                                    errorMessages += '- ' + msg + '<br>';
                                });

                                return Swal.fire({
                                    icon: 'error',
                                    title: 'Mohon Periksa Kembali Isian Anda',
                                    html: errorMessages,
                                });
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
