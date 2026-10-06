@php
    $_title = 'Surat Perintah Penangguhan Penahanan (S-18)';
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
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }
        .input-group > .btn {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
            white-space: nowrap;
        }
    </style>
@endpush

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}">
        <i class="bi bi-arrow-left"></i> Kembali ke Progress Perkara
    </a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Tambah Surat Perintah Penangguhan Penahanan (S-18 / SPRIN GUHAN)</h5>
            <small class="text-muted d-block mb-3">Kode Dokumen: <b>s18</b> │ Kode Proses SPP-TI: <b>DAT-3</b></small>

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
            <form action="{{ route('doc.surat-perintah-penangguhan-penahanan-document.store', ['accident_id' => $accidentId]) }}"
                method="POST" id="suratPerintahPenangguhanPenahananForm" novalidate>
                @csrf
                <input type="hidden" name="accident_id" id="accident_id" value="{{ $accidentId }}">

                <h5 class="fw-bold text-blue-dark">1. Identitas Dokumen</h5>

                {{-- Nomor LP --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="accidentNumber">Nomor LP</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="accidentNumber" type="text"
                            class="form-control font-weight-bold"
                            value="{{ $accident->no_lp }}" readonly style="background-color: #e9ecef;">
                    </div>
                </div>

                {{-- Nomor Dokumen S-18 --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nomor">Nomor Dokumen S-18<span class="text-danger fs-5">*</span>
                        <small class="text-muted d-block font-weight-normal">(Nomor Surat Perintah Penangguhan Penahanan)</small>
                    </label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="nomor" type="text"
                            class="form-control @error('nomor') is-invalid @enderror font-weight-bold"
                            name="nomor" value="{{ old('nomor') }}" required
                            placeholder="Contoh: SP.Guhan/01/X/2026/Reskrim">
                        @error('nomor')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Tanggal Dokumen S-18 --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggal">Tanggal S-18<span class="text-danger fs-5">*</span></label>
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

                {{-- Nomor SPDP (Read Only) --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nomor_spdp">Nomor SPDP<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="nomor_spdp" type="text"
                            class="form-control @error('nomor_spdp') is-invalid @enderror font-weight-bold"
                            name="nomor_spdp" value="{{ old('nomor_spdp', $nomorSpdp) }}" readonly
                            placeholder="Nomor SPDP otomatis dari sistem" style="background-color: #e9ecef;">
                        <small class="text-muted">(*Nomor SPDP bersifat tetap dan diambil otomatis dari dokumen SPDP perkara ini)</small>
                        @error('nomor_spdp')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Tanggal SPDP (Read Only) --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggal_spdp">Tanggal SPDP<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tanggal_spdp" type="text"
                            class="form-control @error('tanggal_spdp') is-invalid @enderror font-weight-bold"
                            name="tanggal_spdp"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('tanggal_spdp', $tanggalSpdp ? date('Y-m-d', strtotime($tanggalSpdp)) : date('Y-m-d')) }}"
                            readonly style="background-color: #e9ecef;">
                        <small class="text-muted">(*Tanggal SPDP bersifat tetap dan diambil otomatis dari dokumen SPDP perkara ini)</small>
                        @error('tanggal_spdp')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Kode Satker Penerbit SPDP (Read Only) --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="kode_satker_penerbit_spdp">Kode Satker Penerbit SPDP<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="kode_satker_penerbit_spdp" type="text"
                            class="form-control @error('kode_satker_penerbit_spdp') is-invalid @enderror font-weight-bold"
                            name="kode_satker_penerbit_spdp" value="{{ old('kode_satker_penerbit_spdp', $kodeSatkerDefault) }}" readonly
                            style="background-color: #e9ecef;">
                        <small class="text-muted">(*Kode Satker Kepolisian penerbit SPDP sesuai dengan Polres pada Nomor LP: {{ $accident->polres->full_name ?? $accident->polres->name ?? '-' }})</small>
                        @error('kode_satker_penerbit_spdp')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Nomor Surat Perintah Penahanan (S-17) (Read Only) --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nomor_surat_perintah_penahanan">Nomor Surat Perintah Penahanan (S-17)<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="nomor_surat_perintah_penahanan" type="text"
                            class="form-control @error('nomor_surat_perintah_penahanan') is-invalid @enderror font-weight-bold"
                            name="nomor_surat_perintah_penahanan"
                            value="{{ old('nomor_surat_perintah_penahanan', $nomorSuratPerintahPenahanan) }}" readonly
                            placeholder="Nomor S-17 otomatis dari sistem" style="background-color: #e9ecef;">
                        <small class="text-muted">(*Nomor Surat Perintah Penahanan diambil otomatis dari dokumen Surat Perintah Penahanan perkara ini)</small>
                        @error('nomor_surat_perintah_penahanan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Tanggal Surat Permohonan Penangguhan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggal_surat_permohonan">Tanggal Surat Permohonan Penangguhan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input class="form-control @error('tanggal_surat_permohonan') is-invalid @enderror" id="tanggal_surat_permohonan" name="tanggal_surat_permohonan"
                            placeholder="YYYY-MM-DD" autocomplete="off" value="{{ old('tanggal_surat_permohonan', date('Y-m-d')) }}"
                            data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-date-today-highlight="true" required>
                        <small class="text-muted">(*Tanggal Surat Permohonan Penangguhan Penahanan oleh tersangka/kuasa hukum/keluarga)</small>
                        @error('tanggal_surat_permohonan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                {{-- 2. KONDISI PERPANJANGAN PENAHANAN (POIN 9 & 10) --}}
                <h5 class="fw-bold text-blue-dark">2. Ketentuan Poin Dasar Tambahan (Poin 9, 10, 12, 13)</h5>
                <div class="alert alert-info py-2">
                    <i class="bi bi-info-circle"></i> Pada template surat, Poin 9, 10, 12, dan 13 merupakan ketentuan pilihan.
                    Tersedia checkbox untuk menandai jika poin tersebut <b>"Tidak ada ..."</b>. Ketika dicentang, field terkait tidak bisa diisi dan poin tersebut akan dihilangkan pada dokumen Word serta nomor urut poin selanjutnya otomatis disesuaikan secara berurutan.
                </div>

                {{-- Container Poin 9, 10, 12, 13 --}}
                <div id="perpanjanganSection" class="p-3 mb-3 border rounded bg-light">
                    {{-- ==================== BLOK POIN 9 ==================== --}}
                    <div class="p-3 mb-3 bg-white border rounded">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold text-primary mb-0">Poin 9: Surat Perpanjangan Penahanan (bila ada)</h6>
                            <span id="badgePoin9Status" class="badge bg-secondary" style="display: none;">
                                <i class="bi bi-slash-circle me-1"></i> Field Dinonaktifkan (Tidak Ada)
                            </span>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input cursor-pointer" type="checkbox"
                                id="tidak_ada_surat_perpanjangan_penahanan" name="tidak_ada_surat_perpanjangan_penahanan" value="1"
                                {{ old('tidak_ada_surat_perpanjangan_penahanan') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold cursor-pointer text-dark" for="tidak_ada_surat_perpanjangan_penahanan">
                                Tidak ada Surat Perpanjangan Penahanan (Poin 9)
                            </label>
                            <small class="d-block text-muted">Centang jika tidak ada Surat Perpanjangan Penahanan dari Kejaksaan.</small>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label" for="nomor_surat_perpanjangan_penahanan">Nomor Surat Perpanjangan Penahanan</label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <input id="nomor_surat_perpanjangan_penahanan" type="text"
                                    class="form-control poin9-input" name="nomor_surat_perpanjangan_penahanan"
                                    value="{{ old('nomor_surat_perpanjangan_penahanan') }}"
                                    placeholder="Contoh: B/123/X/2026/Kejaksaan">
                            </div>
                        </div>

                        <div class="input-group row mb-2 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label" for="tanggal_surat_perpanjangan_penahanan">Tanggal Surat Perpanjangan Penahanan</label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <input class="form-control poin9-input" id="tanggal_surat_perpanjangan_penahanan" name="tanggal_surat_perpanjangan_penahanan"
                                    placeholder="YYYY-MM-DD" autocomplete="off" value="{{ old('tanggal_surat_perpanjangan_penahanan') }}"
                                    data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-date-today-highlight="true">
                            </div>
                        </div>
                    </div>

                    {{-- ==================== BLOK POIN 10 ==================== --}}
                    <div class="p-3 mb-3 bg-white border rounded">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold text-primary mb-0">Poin 10: Surat Perintah Perpanjangan Penahanan (bila ada)</h6>
                            <span id="badgePoin10Status" class="badge bg-secondary" style="display: none;">
                                <i class="bi bi-slash-circle me-1"></i> Field Dinonaktifkan (Tidak Ada)
                            </span>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input cursor-pointer" type="checkbox"
                                id="tidak_ada_sprin_perpanjangan_penahanan" name="tidak_ada_sprin_perpanjangan_penahanan" value="1"
                                {{ old('tidak_ada_sprin_perpanjangan_penahanan') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold cursor-pointer text-dark" for="tidak_ada_sprin_perpanjangan_penahanan">
                                Tidak ada Surat Perintah Perpanjangan Penahanan (Poin 10)
                            </label>
                            <small class="d-block text-muted">Centang jika tidak ada Surat Perintah Perpanjangan Penahanan dari Kepolisian.</small>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label" for="nomor_sprin_perpanjangan_penahanan">Nomor Surat Perintah Perpanjangan Penahanan</label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <input id="nomor_sprin_perpanjangan_penahanan" type="text"
                                    class="form-control poin10-input" name="nomor_sprin_perpanjangan_penahanan"
                                    value="{{ old('nomor_sprin_perpanjangan_penahanan') }}"
                                    placeholder="Contoh: SP.JangHan/01/X/2026/Reskrim">
                            </div>
                        </div>

                        <div class="input-group row mb-2 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label" for="tanggal_sprin_perpanjangan_penahanan">Tanggal Surat Perintah Perpanjangan Penahanan</label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <input class="form-control poin10-input" id="tanggal_sprin_perpanjangan_penahanan" name="tanggal_sprin_perpanjangan_penahanan"
                                    placeholder="YYYY-MM-DD" autocomplete="off" value="{{ old('tanggal_sprin_perpanjangan_penahanan') }}"
                                    data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-date-today-highlight="true">
                            </div>
                        </div>
                    </div>

                    {{-- ==================== BLOK POIN 12 ==================== --}}
                    <div class="p-3 mb-3 bg-white border rounded">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold text-primary mb-0">Poin 12: Surat Ketetapan Penetapan Uang Jaminan Penangguhan Penahanan (Pilihan)</h6>
                            <span id="badgePoin12Status" class="badge bg-secondary" style="display: none;">
                                <i class="bi bi-slash-circle me-1"></i> Field Dinonaktifkan (Tidak Ada)
                            </span>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input cursor-pointer" type="checkbox"
                                id="tidak_ada_sket_uang_jaminan" name="tidak_ada_sket_uang_jaminan" value="1"
                                {{ old('tidak_ada_sket_uang_jaminan') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold cursor-pointer text-dark" for="tidak_ada_sket_uang_jaminan">
                                Tidak ada Surat Ketetapan Penetapan Uang Jaminan (Poin 12)
                            </label>
                            <small class="d-block text-muted">Centang jika dokumen penangguhan penahanan tidak menggunakan Surat Ketetapan Penetapan Uang Jaminan.</small>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label" for="nomor_sket_uang_jaminan">Nomor Surat Ketetapan Uang Jaminan</label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <input id="nomor_sket_uang_jaminan" type="text"
                                    class="form-control poin12-input" name="nomor_sket_uang_jaminan"
                                    value="{{ old('nomor_sket_uang_jaminan') }}"
                                    placeholder="Contoh: S.Tap/01/X/2026/Reskrim">
                            </div>
                        </div>

                        <div class="input-group row mb-2 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label" for="tanggal_sket_uang_jaminan">Tanggal Surat Ketetapan Uang Jaminan</label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <input class="form-control poin12-input" id="tanggal_sket_uang_jaminan" name="tanggal_sket_uang_jaminan"
                                    placeholder="YYYY-MM-DD" autocomplete="off" value="{{ old('tanggal_sket_uang_jaminan') }}"
                                    data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-date-today-highlight="true">
                            </div>
                        </div>
                    </div>

                    {{-- ==================== BLOK POIN 13 ==================== --}}
                    <div class="p-3 mb-1 bg-white border rounded">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold text-primary mb-0">Poin 13: Surat Ketetapan Penetapan Uang Tangguhan Penangguhan Penahanan (Pilihan)</h6>
                            <span id="badgePoin13Status" class="badge bg-secondary" style="display: none;">
                                <i class="bi bi-slash-circle me-1"></i> Field Dinonaktifkan (Tidak Ada)
                            </span>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input cursor-pointer" type="checkbox"
                                id="tidak_ada_sket_uang_tangguhan" name="tidak_ada_sket_uang_tangguhan" value="1"
                                {{ old('tidak_ada_sket_uang_tangguhan') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold cursor-pointer text-dark" for="tidak_ada_sket_uang_tangguhan">
                                Tidak ada Surat Ketetapan Penetapan Uang Tangguhan (Poin 13)
                            </label>
                            <small class="d-block text-muted">Centang jika dokumen penangguhan penahanan tidak menggunakan Surat Ketetapan Penetapan Uang Tangguhan.</small>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label" for="nomor_sket_uang_tangguhan">Nomor Surat Ketetapan Uang Tangguhan</label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <input id="nomor_sket_uang_tangguhan" type="text"
                                    class="form-control poin13-input" name="nomor_sket_uang_tangguhan"
                                    value="{{ old('nomor_sket_uang_tangguhan') }}"
                                    placeholder="Contoh: S.Tap/02/X/2026/Reskrim">
                            </div>
                        </div>

                        <div class="input-group row mb-2 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label" for="tanggal_sket_uang_tangguhan">Tanggal Surat Ketetapan Uang Tangguhan</label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <input class="form-control poin13-input" id="tanggal_sket_uang_tangguhan" name="tanggal_sket_uang_tangguhan"
                                    placeholder="YYYY-MM-DD" autocomplete="off" value="{{ old('tanggal_sket_uang_tangguhan') }}"
                                    data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-date-today-highlight="true">
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- 3. KONTEN DOKUMEN & JAMINAN --}}
                <h5 class="fw-bold text-blue-dark">3. Konten Jaminan Penangguhan Penahanan</h5>

                {{-- Jenis Jaminan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="jenis_jaminan">Jenis Jaminan
                        <small class="text-muted d-block font-weight-normal">(Opsional / Nullable)</small>
                    </label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="jenis_jaminan" id="jenis_jaminan">
                            <option value="">-- Pilih Jenis Jaminan (Bila Ada) --</option>
                            @foreach ($masterJenisJaminan as $val => $lbl)
                                <option value="{{ $val }}" {{ old('jenis_jaminan') == $val ? 'selected' : '' }}>
                                    {{ $lbl }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">(*Master Data SPPT-TI: 1 = Jaminan Uang, 2 = Jaminan Orang)</small>
                    </div>
                </div>

                {{-- Form fields Jaminan Uang (jenis_jaminan = 1) --}}
                <div id="jaminanUangSection" style="display: none;" class="p-3 mb-3 border rounded bg-light">
                    <h6 class="fw-bold text-success mb-3"><i class="bi bi-cash-stack"></i> Rincian Jaminan Uang</h6>

                    {{-- Besaran Uang Jaminan --}}
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-3 col-form-label" for="besaran_uang_jaminan">Besaran Uang Jaminan (Rp)</label>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                            <input id="besaran_uang_jaminan" type="number" step="any" min="0"
                                class="form-control font-weight-bold"
                                name="besaran_uang_jaminan"
                                value="{{ old('besaran_uang_jaminan') }}"
                                placeholder="Contoh: 50000000">
                            <small class="text-muted">(*Nilai besaran uang jaminan dalam mata uang Rupiah)</small>
                        </div>
                    </div>

                    {{-- Lokasi Penyimpanan Jaminan --}}
                    <div class="input-group row mb-2 ms-0">
                        <label class="fw-bold col-sm-3 col-form-label" for="lokasi_penyimpanan_jaminan">Lokasi Penyimpanan Jaminan</label>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                            <input id="lokasi_penyimpanan_jaminan" type="text"
                                class="form-control"
                                name="lokasi_penyimpanan_jaminan"
                                value="{{ old('lokasi_penyimpanan_jaminan', 'Panitera Pengadilan Negeri ' . ($accident->polres->name ?? '')) }}"
                                placeholder="Contoh: Panitera Pengadilan Negeri Jakarta Pusat">
                            <small class="text-muted">(*Tempat penyimpanan jaminan uang, contoh: Panitera Pengadilan Negeri ...)</small>
                        </div>
                    </div>
                </div>

                {{-- Form fields Jaminan Orang (jenis_jaminan = 2) --}}
                <div id="jaminanOrangSection" style="display: none;" class="p-3 mb-3 border rounded bg-light">
                    <h6 class="fw-bold text-info mb-3"><i class="bi bi-person-badge"></i> Rincian Jaminan Orang</h6>

                    {{-- Nomor Identitas Penjamin --}}
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-3 col-form-label" for="nomor_identitas_penjamin">Nomor Identitas Penjamin</label>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                            <input id="nomor_identitas_penjamin" type="text"
                                class="form-control"
                                name="nomor_identitas_penjamin"
                                value="{{ old('nomor_identitas_penjamin') }}"
                                placeholder="Contoh: 3171012345670001 / -">
                            <small class="text-muted">(*NIK KTP atau nomor identitas penjamin)</small>
                        </div>
                    </div>

                    {{-- Nama Penjamin --}}
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-3 col-form-label" for="nama_penjamin">Nama Penjamin</label>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                            <input id="nama_penjamin" type="text"
                                class="form-control font-weight-bold"
                                name="nama_penjamin"
                                value="{{ old('nama_penjamin') }}"
                                placeholder="Contoh: PT Asuransi ABC / Nama Penjamin">
                        </div>
                    </div>

                    {{-- Alamat Penjamin --}}
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-3 col-form-label" for="alamat_penjamin">Alamat Penjamin</label>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                            <textarea id="alamat_penjamin" class="form-control" name="alamat_penjamin" rows="2"
                                placeholder="Contoh: Jl. Sudirman No. 1, Jakarta">{{ old('alamat_penjamin') }}</textarea>
                        </div>
                    </div>

                    {{-- Besaran Uang Tanggungan (Bila ada pada jaminan orang) --}}
                    <div class="input-group row mb-2 ms-0">
                        <label class="fw-bold col-sm-3 col-form-label" for="besaran_uang_tanggungan">Besaran Uang Tanggungan (Opsional)</label>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                            <input id="besaran_uang_tanggungan" type="number" step="any" min="0"
                                class="form-control font-weight-bold"
                                name="besaran_uang_jaminan_orang"
                                value="{{ old('besaran_uang_jaminan') }}"
                                placeholder="Contoh: 50000000">
                            <small class="text-muted">(*Besaran uang tanggungan Pasal 110 KUHAP bila ditetapkan)</small>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- 4. DAFTAR TERSANGKA --}}
                <h5 class="fw-bold text-blue-dark">4. Tersangka yang Ditangguhkan Penahanannya<span class="text-danger fs-5">*</span></h5>

                @if ($suspects->count() == 0)
                    <div class="alert alert-warning" role="alert">
                        Belum ada Tersangka yang terdaftar pada perkara ini. Silahkan tambahkan Tersangka pada menu Progress Perkara terlebih dahulu sebelum membuat Surat Perintah Penangguhan Penahanan.
                    </div>
                @else
                    <div class="table-responsive my-2">
                        <table class="table table-bordered table-striped" id="suspectTable">
                            <thead class="table-danger">
                                <tr class="text-center">
                                    <th scope="col" width="5%">Pilih</th>
                                    <th scope="col">Nama Tersangka</th>
                                    <th scope="col">NIK / Identitas</th>
                                    <th scope="col">Tempat, Tgl Lahir</th>
                                    <th scope="col">Jenis Kelamin</th>
                                    <th scope="col">Pekerjaan</th>
                                    <th scope="col">Alamat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($suspects as $suspect)
                                    @php
                                        $age = $suspect->age ?? ($suspect->birth_date ? \Carbon\Carbon::parse($suspect->birth_date)->age : '-');
                                    @endphp
                                    <tr>
                                        <td class="text-center align-middle">
                                            <input type="checkbox" name="suspects[]" value="{{ $suspect->id }}"
                                                class="suspect-checkbox" id="suspect_{{ $suspect->id }}"
                                                {{ in_array($suspect->id, old('suspects', [])) || $loop->first ? 'checked' : '' }}>
                                        </td>
                                        <td class="align-middle fw-bold">
                                            <label for="suspect_{{ $suspect->id }}" class="mb-0 cursor-pointer">
                                                {{ $suspect->name }}
                                            </label>
                                        </td>
                                        <td class="align-middle">{{ $suspect->identity_number ?? '-' }}</td>
                                        <td class="align-middle">
                                            {{ $suspect->birth_place ?? '-' }},
                                            {{ $suspect->birth_date ? date('d-m-Y', strtotime($suspect->birth_date)) : '-' }}
                                            ({{ $age }} thn)
                                        </td>
                                        <td class="align-middle">{{ $suspect->gender->name ?? '-' }}</td>
                                        <td class="align-middle">{{ $suspect->job->name ?? '-' }}</td>
                                        <td class="align-middle">{{ $suspect->address ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @error('suspects')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                @endif

                <hr class="my-4">

                {{-- 5. PASAL YANG DISANGKAKAN (INHERITED AUTOMATICALLY) --}}
                <h5 class="fw-bold text-blue-dark">5. Pasal yang Disangkakan</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label">Daftar UU & Pasal</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <div class="p-3 border rounded bg-light">
                            @if (!empty($pasalList) && count($pasalList) > 0)
                                <ul class="mb-0 ps-3">
                                    @foreach ($pasalList as $pasal)
                                        <li><strong class="text-dark">{{ $pasal }}</strong></li>
                                        <input type="hidden" name="pasal_disangkakan[]" value="{{ $pasal }}">
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-muted fst-italic">Pasal akan diambil secara otomatis dari Surat Perintah Penyidikan terkait.</span>
                                <input type="hidden" name="pasal_disangkakan[]" value="Pasal 109 ayat (1) KUHAP">
                            @endif
                        </div>
                        <small class="text-muted">(*Pasal yang disangkakan diproses secara otomatis oleh sistem dari Surat Perintah Penyidikan)</small>
                    </div>
                </div>

                <hr class="my-4">

                {{-- 6. PETUGAS YANG DIPERINTAHKAN --}}
                <h5 class="fw-bold text-blue-dark">6. Petugas yang Diperintahkan</h5>

                {{-- Ketua Tim --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="officerLeader">Ketua Tim<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="officerLeader" id="officerLeader" required>
                            <option value="">--Pilih Ketua Tim--</option>
                            @foreach ($leaderOfficers as $data)
                                @php
                                    $fullName = \App\Helpers\PeopleNameHelper::getFullName($data->first_title, $data->first_name, $data->last_name, $data->last_title);
                                    $positionName = $data->position->name ?? '';
                                @endphp
                                <option value="{{ $data->id }}" {{ old('officerLeader') == $data->id ? 'selected' : '' }}
                                    data-register-number="{{ $data->register_number }}">
                                    {{ $data->register_number . ' - ' . $fullName . ' | ' . $positionName }}
                                </option>
                            @endforeach
                        </select>
                        @error('officerLeader')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="row col-12 my-2 ms-0">
                    <div id="internalOfficer">
                        <label class="fw-bold mb-2">Anggota Petugas yang Diperintahkan</label>
                        <div class="alert alert-primary my-2" role="alert">
                            Pilih personel lalu klik tombol 'Tambah' untuk menambahkan personel sebagai petugas yang diperintahkan.
                        </div>

                        <div class="row my-2">
                            <div class="col-md-7">
                                <div class="input-group">
                                    <select class="custom-select select2-input-group" id="officerInternalMemberOption"
                                        aria-describedby="officerInternalMemberOptionAddButtton">
                                        <option value="">--Pilih Petugas--</option>
                                        @foreach ($internalOfficers as $data)
                                            @php
                                                $fullName = \App\Helpers\PeopleNameHelper::getFullName($data->first_title, $data->first_name, $data->last_name, $data->last_title);
                                                $positionName = $data->position->name ?? '';
                                                $rankName = $data->rank->name ?? '';
                                                $policeName = $data->police->name ?? '';
                                            @endphp
                                            <option value="{{ $data->id }}"
                                                data-register-number="{{ $data->register_number }}"
                                                data-rank-name="{{ $rankName }}"
                                                data-name="{{ $fullName }}"
                                                data-position-name="{{ $positionName }}"
                                                data-police-name="{{ $policeName }}">
                                                {{ $data->register_number . ' - ' . $fullName . ' | ' . $positionName }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-primary" type="button"
                                        id="officerInternalMemberOptionAddButtton">
                                        <i class="bi bi-plus-circle"></i> Tambah
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive my-2">
                            <table class="table table-bordered" id="internalOfficerMemberTable">
                                <thead class="table-danger">
                                    <tr class="text-center">
                                        <th scope="col">Nama</th>
                                        <th scope="col">Pangkat</th>
                                        <th scope="col">NRP</th>
                                        <th scope="col">Jabatan</th>
                                        <th scope="col">Kesatuan</th>
                                        <th scope="col">Opsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- 7. PEJABAT PENANDATANGAN --}}
                <h5 class="fw-bold text-blue-dark">7. Pejabat Penandatangan</h5>

                <div class="input-group row mb-4 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="signatory">Pejabat Penandatangan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="signatory" id="signatory" required>
                            <option value="">--Pilih Pejabat Penandatangan--</option>
                            @foreach ($authorizedSignatories as $data)
                                @php
                                    $fullName = \App\Helpers\PeopleNameHelper::getFullName($data->first_title, $data->first_name, $data->last_name, $data->last_title);
                                    $positionName = $data->position->name ?? '';
                                    $rankName = $data->rank->name ?? '';
                                @endphp
                                <option value="{{ $data->id }}" {{ old('signatory') == $data->id ? 'selected' : '' }}>
                                    {{ $data->register_number . ' - ' . $fullName . ' | ' . $positionName . ' (' . $rankName . ')' }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">(*Pejabat yang berwenang menandatangani Surat Perintah Penangguhan Penahanan)</small>
                        @error('signatory')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="row col-12 my-4 ms-0">
                    <div class="col text-end">
                        <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-secondary me-2">
                            Batal
                        </a>
                        <button class="btn btn-primary" type="button" id="btnSubmitForm">
                            <i class="bi bi-save"></i> Simpan Dokumen
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // Select2 Init
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            $('.select2-input-group').select2({
                theme: 'bootstrap4'
            });

            // Datepicker Init
            $('#tanggal, #tanggal_surat_permohonan, #tanggal_surat_perpanjangan_penahanan, #tanggal_sprin_perpanjangan_penahanan, #tanggal_sket_uang_jaminan, #tanggal_sket_uang_tangguhan').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                orientation: 'auto bottom'
            }).on('changeDate', function() {
                $(this).trigger('change');
            });

            // Handler Checkbox Poin 9: Tidak ada Surat Perpanjangan Penahanan
            function updatePoin9State() {
                var isTidakAda = $('#tidak_ada_surat_perpanjangan_penahanan').is(':checked');
                if (isTidakAda) {
                    $('.poin9-input').prop('readonly', true).val('').css({
                        'background-color': '#e9ecef',
                        'cursor': 'not-allowed',
                        'pointer-events': 'none'
                    });
                    $('#badgePoin9Status').show();
                } else {
                    $('.poin9-input').prop('readonly', false).css({
                        'background-color': '',
                        'cursor': '',
                        'pointer-events': ''
                    });
                    $('#badgePoin9Status').hide();
                }
            }
            $('#tidak_ada_surat_perpanjangan_penahanan').on('change', updatePoin9State);
            updatePoin9State();

            // Handler Checkbox Poin 10: Tidak ada Surat Perintah Perpanjangan Penahanan
            function updatePoin10State() {
                var isTidakAda = $('#tidak_ada_sprin_perpanjangan_penahanan').is(':checked');
                if (isTidakAda) {
                    $('.poin10-input').prop('readonly', true).val('').css({
                        'background-color': '#e9ecef',
                        'cursor': 'not-allowed',
                        'pointer-events': 'none'
                    });
                    $('#badgePoin10Status').show();
                } else {
                    $('.poin10-input').prop('readonly', false).css({
                        'background-color': '',
                        'cursor': '',
                        'pointer-events': ''
                    });
                    $('#badgePoin10Status').hide();
                }
            }
            $('#tidak_ada_sprin_perpanjangan_penahanan').on('change', updatePoin10State);
            updatePoin10State();

            // Handler Checkbox Poin 12: Tidak ada Surat Ketetapan Penetapan Uang Jaminan
            function updatePoin12State() {
                var isTidakAda = $('#tidak_ada_sket_uang_jaminan').is(':checked');
                if (isTidakAda) {
                    $('.poin12-input').prop('readonly', true).val('').css({
                        'background-color': '#e9ecef',
                        'cursor': 'not-allowed',
                        'pointer-events': 'none'
                    });
                    $('#badgePoin12Status').show();
                } else {
                    $('.poin12-input').prop('readonly', false).css({
                        'background-color': '',
                        'cursor': '',
                        'pointer-events': ''
                    });
                    $('#badgePoin12Status').hide();
                }
            }
            $('#tidak_ada_sket_uang_jaminan').on('change', updatePoin12State);
            updatePoin12State();

            // Handler Checkbox Poin 13: Tidak ada Surat Ketetapan Penetapan Uang Tangguhan
            function updatePoin13State() {
                var isTidakAda = $('#tidak_ada_sket_uang_tangguhan').is(':checked');
                if (isTidakAda) {
                    $('.poin13-input').prop('readonly', true).val('').css({
                        'background-color': '#e9ecef',
                        'cursor': 'not-allowed',
                        'pointer-events': 'none'
                    });
                    $('#badgePoin13Status').show();
                } else {
                    $('.poin13-input').prop('readonly', false).css({
                        'background-color': '',
                        'cursor': '',
                        'pointer-events': ''
                    });
                    $('#badgePoin13Status').hide();
                }
            }
            $('#tidak_ada_sket_uang_tangguhan').on('change', updatePoin13State);
            updatePoin13State();

            // Toggle Jenis Jaminan
            function toggleJaminanSection() {
                var jenis = $('#jenis_jaminan').val();
                if (jenis == '1') {
                    $('#jaminanUangSection').slideDown();
                    $('#jaminanOrangSection').slideUp();
                } else if (jenis == '2') {
                    $('#jaminanOrangSection').slideDown();
                    $('#jaminanUangSection').slideUp();
                } else {
                    $('#jaminanUangSection').slideUp();
                    $('#jaminanOrangSection').slideUp();
                }
            }
            $('#jenis_jaminan').on('change', toggleJaminanSection);
            toggleJaminanSection();

            // Handle sync uang jaminan bila diisi di jaminan orang
            $('#besaran_uang_tanggungan').on('input', function() {
                $('#besaran_uang_jaminan').val($(this).val());
            });

            // Tambah Petugas yang Diperintahkan
            $('#officerInternalMemberOptionAddButtton').on('click', function() {
                var selectedOption = $('#officerInternalMemberOption').find('option:selected');
                var officerId = selectedOption.val();

                if (!officerId) {
                    return Swal.fire({
                        title: 'Perhatian',
                        text: 'Silahkan pilih petugas terlebih dahulu',
                        icon: 'warning',
                        confirmButtonText: 'Ok'
                    });
                }

                var registerNumber = selectedOption.data('register-number') || '-';
                var rankName = selectedOption.data('rank-name') || '-';
                var name = selectedOption.data('name') || '-';
                var positionName = selectedOption.data('position-name') || '-';
                var policeName = selectedOption.data('police-name') || '-';

                // Cek duplikasi
                var isAppended = false;
                $('#internalOfficerMemberTable tbody tr').each(function() {
                    var appendedRegisterNumber = $(this).find('.registerNumber').text().trim();
                    if (appendedRegisterNumber === registerNumber) {
                        isAppended = true;
                        return false;
                    }
                });

                if (isAppended) {
                    return Swal.fire({
                        title: 'Perhatian',
                        text: 'Petugas tersebut sudah ditambahkan dalam daftar!',
                        icon: 'warning',
                        confirmButtonText: 'Ok'
                    });
                }

                var rowHtml = '<tr>' +
                    '<td><input type="hidden" name="officers[]" value="' + officerId + '">' + name + '</td>' +
                    '<td>' + rankName + '</td>' +
                    '<td class="registerNumber">' + registerNumber + '</td>' +
                    '<td>' + positionName + '</td>' +
                    '<td>' + policeName + '</td>' +
                    '<td class="text-center">' +
                    '<button type="button" class="btn btn-danger btn-sm btn-delete-officer"><i class="bi bi-trash"></i></button>' +
                    '</td>' +
                    '</tr>';

                $('#internalOfficerMemberTable tbody').append(rowHtml);
                $('#officerInternalMemberOption').val('').trigger('change');
            });

            // Hapus Petugas dari tabel
            $(document).on('click', '.btn-delete-officer', function() {
                $(this).closest('tr').remove();
            });

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

            // Handle Submit Form with Inline markError & AJAX Validation
            $('#btnSubmitForm').on('click', function(e) {
                e.preventDefault();

                // Bersihkan error sebelumnya
                $('.frontend-error').remove();
                $('.is-invalid').removeClass('is-invalid');
                $('.border-danger').removeClass('border-danger');

                var errors = [];

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
                checkInput('#nomor', 'Nomor Dokumen S-18');
                checkInput('#tanggal', 'Tanggal S-18');
                checkInput('#nomor_spdp', 'Nomor SPDP');
                checkInput('#tanggal_spdp', 'Tanggal SPDP');
                checkInput('#kode_satker_penerbit_spdp', 'Kode Satker Penerbit SPDP');
                checkInput('#nomor_surat_perintah_penahanan', 'Nomor Surat Perintah Penahanan (S-17)');
                checkInput('#tanggal_surat_permohonan', 'Tanggal Surat Permohonan Penangguhan');

                // 2. Validasi Tersangka minimal 1
                if ($('.suspect-checkbox:checked').length === 0) {
                    markError('#suspectTable', 'Tersangka yang Ditangguhkan harus dipilih minimal 1 orang');
                }

                // 3. Validasi Ketua Tim & Pejabat Penandatangan
                checkSelect('#officerLeader', 'Ketua Tim');
                checkSelect('#signatory', 'Pejabat Penandatangan');

                // Jika terdapat error di sisi frontend, scroll ke elemen pertama dan batalkan submit
                if (errors.length > 0) {
                    scrollToFirstError();
                    return false;
                }

                // Validasi AJAX ke server
                $.ajax({
                    url: "{{ route('doc.surat-perintah-penangguhan-penahanan-document.api.validate-request-form', ['accident_id' => $accidentId]) }}",
                    type: 'POST',
                    dataType: 'json',
                    data: $('#suratPerintahPenangguhanPenahananForm').serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: response.message || 'Silahkan menunggu proses simpan data',
                                icon: 'success',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                $('#suratPerintahPenangguhanPenahananForm')[0].submit();
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
                                    } else if (key === 'officerLeader') {
                                        markError('#officerLeader', msg);
                                    } else if (key === 'signatory') {
                                        markError('#signatory', msg);
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
