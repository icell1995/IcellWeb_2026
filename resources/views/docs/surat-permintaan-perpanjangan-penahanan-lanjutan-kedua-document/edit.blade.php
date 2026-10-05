@php
    $_title = 'Ubah Surat Permintaan Perpanjangan Penahanan Ke Ketua Pengadilan Negeri (Kedua 30 Hari) (S-22)';
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
            <h5 class="fw-bold text-blue-dark">Ubah Surat Permintaan Perpanjangan Penahanan Ke Ketua Pengadilan Negeri (Kedua 30 Hari) (S-22)</h5>

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
            <form action="{{ route('doc.surat-permintaan-perpanjangan-penahanan-lanjutan-kedua-document.update', ['id' => $document->id, 'accident_id' => $accidentId]) }}"
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
                    <label class="fw-bold col-sm-2 col-form-label" for="nomor_surat">Nomor Surat S-22<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nomor_surat" type="text" class="form-control @error('nomor_surat') is-invalid @enderror" name="nomor_surat"
                            value="{{ old('nomor_surat', $document->nomor_surat) }}" required placeholder="Contoh: B/124/V/2026/Lantas">
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
                            value="{{ old('tanggal_surat', $document->tanggal_surat ? Carbon\Carbon::parse($document->tanggal_surat)->format('Y-m-d') : '') }}" data-provide="datepicker" required>
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
                            <option value="BIASA" {{ old('klasifikasi_surat_id', $document->klasifikasi_surat_id) == 'BIASA' ? 'selected' : '' }}>Biasa</option>
                            <option value="RAHASIA" {{ old('klasifikasi_surat_id', $document->klasifikasi_surat_id) == 'RAHASIA' ? 'selected' : '' }}>Rahasia</option>
                            <option value="SANGAT RAHASIA" {{ old('klasifikasi_surat_id', $document->klasifikasi_surat_id) == 'SANGAT RAHASIA' ? 'selected' : '' }}>Sangat Rahasia</option>
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
                                <option value="{{ $prosecutor->id }}" data-name="{{ $prosecutor->name }}" {{ old('kejaksaan_id', $document->kejaksaan_id) == $prosecutor->id ? 'selected' : '' }}>
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
                            value="{{ old('nama_pengadilan_negeri', $document->nama_pengadilan_negeri ?: $defaultNamaPengadilanNegeri) }}" placeholder="Contoh: PENGADILAN NEGERI SIMALUNGUN" required>
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
                    <span class="fw-bold text-dark mb-2">Surat Perintah Penyidikan</span>
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="nomor_surat_perintah_penyidikan">Nomor Sprint Sidik<span class="text-danger fs-5">*</span></label>
                            <input id="nomor_surat_perintah_penyidikan" type="text" class="form-control @error('nomor_surat_perintah_penyidikan') is-invalid @enderror" name="nomor_surat_perintah_penyidikan"
                                value="{{ old('nomor_surat_perintah_penyidikan', $document->nomor_surat_perintah_penyidikan) }}" placeholder="Nomor Surat Perintah Penyidikan" required>
                            @error('nomor_surat_perintah_penyidikan')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="tanggal_surat_perintah_penyidikan">Tanggal Sprint Sidik<span class="text-danger fs-5">*</span></label>
                            <input id="tanggal_surat_perintah_penyidikan" type="text" class="form-control @error('tanggal_surat_perintah_penyidikan') is-invalid @enderror" name="tanggal_surat_perintah_penyidikan"
                                placeholder="YYYY-MM-DD" autocomplete="off"
                                value="{{ old('tanggal_surat_perintah_penyidikan', $document->tanggal_surat_perintah_penyidikan ? Carbon\Carbon::parse($document->tanggal_surat_perintah_penyidikan)->format('Y-m-d') : '') }}" data-provide="datepicker" required>
                            @error('tanggal_surat_perintah_penyidikan')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Poin 1.g - SPDP -->
                <div class="card bg-light border-0 mb-3 p-3">
                    <span class="fw-bold text-dark mb-2">Surat Pemberitahuan Dimulainya Penyidikan (SPDP)</span>
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="nomor_spdp">Nomor SPDP<span class="text-danger fs-5">*</span></label>
                            <input id="nomor_spdp" type="text" class="form-control @error('nomor_spdp') is-invalid @enderror" name="nomor_spdp"
                                value="{{ old('nomor_spdp', $document->nomor_spdp) }}" placeholder="Nomor SPDP" required>
                            @error('nomor_spdp')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="tanggal_spdp">Tanggal SPDP<span class="text-danger fs-5">*</span></label>
                            <input id="tanggal_spdp" type="text" class="form-control @error('tanggal_spdp') is-invalid @enderror" name="tanggal_spdp"
                                placeholder="YYYY-MM-DD" autocomplete="off"
                                value="{{ old('tanggal_spdp', $document->tanggal_spdp ? Carbon\Carbon::parse($document->tanggal_spdp)->format('Y-m-d') : '') }}" data-provide="datepicker" required>
                            @error('tanggal_spdp')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Poin 1.h - SKET Tersangka -->
                <div class="card bg-light border-0 mb-3 p-3">
                    <span class="fw-bold text-dark mb-2">Surat Ketetapan tentang Penetapan Tersangka</span>
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="nomor_sket_tersangka">Nomor S.Ket Penetapan Tersangka<span class="text-danger fs-5">*</span></label>
                            <input id="nomor_sket_tersangka" type="text" class="form-control @error('nomor_sket_tersangka') is-invalid @enderror" name="nomor_sket_tersangka"
                                value="{{ old('nomor_sket_tersangka', $document->nomor_sket_tersangka) }}" placeholder="Nomor S.Ket Penetapan Tersangka" required>
                            @error('nomor_sket_tersangka')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="tanggal_sket_tersangka">Tanggal S.Ket Penetapan Tersangka<span class="text-danger fs-5">*</span></label>
                            <input id="tanggal_sket_tersangka" type="text" class="form-control @error('tanggal_sket_tersangka') is-invalid @enderror" name="tanggal_sket_tersangka"
                                placeholder="YYYY-MM-DD" autocomplete="off"
                                value="{{ old('tanggal_sket_tersangka', $document->tanggal_sket_tersangka ? Carbon\Carbon::parse($document->tanggal_sket_tersangka)->format('Y-m-d') : '') }}" data-provide="datepicker" required>
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
                            <label class="fw-bold" for="nomor_surat_perintah_penahanan">Nomor Sprint Penahanan<span class="text-danger fs-5">*</span></label>
                            <input id="nomor_surat_perintah_penahanan" type="text" class="form-control @error('nomor_surat_perintah_penahanan') is-invalid @enderror" name="nomor_surat_perintah_penahanan"
                                value="{{ old('nomor_surat_perintah_penahanan', $document->nomor_surat_perintah_penahanan) }}" placeholder="Contoh: Sp.Han/12/IV/2026/Lantas" required>
                            @error('nomor_surat_perintah_penahanan')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="tanggal_surat_perintah_penahanan">Tanggal Sprint Penahanan<span class="text-danger fs-5">*</span></label>
                            <input id="tanggal_surat_perintah_penahanan" type="text" class="form-control @error('tanggal_surat_perintah_penahanan') is-invalid @enderror" name="tanggal_surat_perintah_penahanan"
                                placeholder="YYYY-MM-DD" autocomplete="off"
                                value="{{ old('tanggal_surat_perintah_penahanan', $document->tanggal_surat_perintah_penahanan ? Carbon\Carbon::parse($document->tanggal_surat_perintah_penahanan)->format('Y-m-d') : '') }}" data-provide="datepicker" required>
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
                            <label class="fw-bold" for="nomor_surat_perpanjangan_kejaksaan">Nomor Perpanjangan Kejaksaan<span class="text-danger fs-5">*</span></label>
                            <input id="nomor_surat_perpanjangan_kejaksaan" type="text" class="form-control @error('nomor_surat_perpanjangan_kejaksaan') is-invalid @enderror" name="nomor_surat_perpanjangan_kejaksaan"
                                value="{{ old('nomor_surat_perpanjangan_kejaksaan', $document->nomor_surat_perpanjangan_kejaksaan) }}" placeholder="Nomor Surat Perpanjangan Kejaksaan" required>
                            @error('nomor_surat_perpanjangan_kejaksaan')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="tanggal_surat_perpanjangan_kejaksaan">Tanggal Perpanjangan Kejaksaan<span class="text-danger fs-5">*</span></label>
                            <input id="tanggal_surat_perpanjangan_kejaksaan" type="text" class="form-control @error('tanggal_surat_perpanjangan_kejaksaan') is-invalid @enderror" name="tanggal_surat_perpanjangan_kejaksaan"
                                placeholder="YYYY-MM-DD" autocomplete="off"
                                value="{{ old('tanggal_surat_perpanjangan_kejaksaan', $document->tanggal_surat_perpanjangan_kejaksaan ? Carbon\Carbon::parse($document->tanggal_surat_perpanjangan_kejaksaan)->format('Y-m-d') : '') }}" data-provide="datepicker" required>
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
                                value="{{ old('nomor_surat_perintah_perpanjangan_penahanan', $document->nomor_surat_perintah_perpanjangan_penahanan) }}" placeholder="Contoh: Sp.Jang.Han/05/V/2026/Lantas" required>
                            @error('nomor_surat_perintah_perpanjangan_penahanan')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="tanggal_surat_perintah_perpanjangan_penahanan">Tanggal Sprint Perpanjangan Penahanan (JPU)<span class="text-danger fs-5">*</span></label>
                            <input id="tanggal_surat_perintah_perpanjangan_penahanan" type="text" class="form-control @error('tanggal_surat_perintah_perpanjangan_penahanan') is-invalid @enderror" name="tanggal_surat_perintah_perpanjangan_penahanan"
                                placeholder="YYYY-MM-DD" autocomplete="off"
                                value="{{ old('tanggal_surat_perintah_perpanjangan_penahanan', $document->tanggal_surat_perintah_perpanjangan_penahanan ? Carbon\Carbon::parse($document->tanggal_surat_perintah_penahanan)->format('Y-m-d') : '') }}" data-provide="datepicker" required>
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
                            <label class="fw-bold" for="nomor_sket_perpanjangan_kpn_pertama">Nomor S.Ket Perpanjangan KPN1<span class="text-danger fs-5">*</span></label>
                            <input id="nomor_sket_perpanjangan_kpn_pertama" type="text" class="form-control @error('nomor_sket_perpanjangan_kpn_pertama') is-invalid @enderror" name="nomor_sket_perpanjangan_kpn_pertama"
                                value="{{ old('nomor_sket_perpanjangan_kpn_pertama', $document->nomor_sket_perpanjangan_kpn_pertama) }}" placeholder="Nomor Surat Penetapan / Izin KPN Pertama" required>
                            @error('nomor_sket_perpanjangan_kpn_pertama')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="tanggal_sket_perpanjangan_kpn_pertama">Tanggal S.Ket Perpanjangan KPN1<span class="text-danger fs-5">*</span></label>
                            <input id="tanggal_sket_perpanjangan_kpn_pertama" type="text" class="form-control @error('tanggal_sket_perpanjangan_kpn_pertama') is-invalid @enderror" name="tanggal_sket_perpanjangan_kpn_pertama"
                                placeholder="YYYY-MM-DD" autocomplete="off"
                                value="{{ old('tanggal_sket_perpanjangan_kpn_pertama', $document->tanggal_sket_perpanjangan_kpn_pertama ? Carbon\Carbon::parse($document->tanggal_sket_perpanjangan_kpn_pertama)->format('Y-m-d') : '') }}" data-provide="datepicker" required>
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
                                value="{{ old('nomor_surat_perintah_perpanjangan_penahanan_kpn_pertama', $document->nomor_surat_perintah_perpanjangan_penahanan_kpn_pertama) }}" placeholder="Nomor Sprint Perpanjangan Penahanan (KPN1)" required>
                            @error('nomor_surat_perintah_perpanjangan_penahanan_kpn_pertama')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label class="fw-bold" for="tanggal_surat_perintah_perpanjangan_penahanan_kpn_pertama">Tanggal Sprint Perpanjangan Penahanan (KPN1)<span class="text-danger fs-5">*</span></label>
                            <input id="tanggal_surat_perintah_perpanjangan_penahanan_kpn_pertama" type="text" class="form-control @error('tanggal_surat_perintah_perpanjangan_penahanan_kpn_pertama') is-invalid @enderror" name="tanggal_surat_perintah_perpanjangan_penahanan_kpn_pertama"
                                placeholder="YYYY-MM-DD" autocomplete="off"
                                value="{{ old('tanggal_surat_perintah_perpanjangan_penahanan_kpn_pertama', $document->tanggal_surat_perintah_perpanjangan_penahanan_kpn_pertama ? Carbon\Carbon::parse($document->tanggal_surat_perintah_perpanjangan_penahanan_kpn_pertama)->format('Y-m-d') : '') }}" data-provide="datepicker" required>
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
                            value="{{ old('pengadilan_negeri_akhir_tanggal', $document->pengadilan_negeri_akhir_tanggal ? Carbon\Carbon::parse($document->pengadilan_negeri_akhir_tanggal)->format('Y-m-d') : '') }}" data-provide="datepicker" required>
                        <small class="text-muted">Tanggal berakhirnya masa perpanjangan penahanan dari Ketua Pengadilan Negeri (Pertama 30 Hari) sebelumnya</small>
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
                            value="{{ old('waktu_penahanan_hari', $document->waktu_penahanan_hari ?: 30) }}" required min="1" max="60">
                        <small class="text-muted">Perpanjangan penahanan lanjutan kedua ke Pengadilan Negeri berlaku maksimal 30 hari.</small>
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
                            value="{{ old('tanggal_mulai_perpanjangan_penahanan', $document->tanggal_mulai_perpanjangan_penahanan ? Carbon\Carbon::parse($document->tanggal_mulai_perpanjangan_penahanan)->format('Y-m-d') : '') }}" data-provide="datepicker" required>
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
                            value="{{ old('tanggal_akhir_perpanjangan_penahanan', $document->tanggal_akhir_perpanjangan_penahanan ? Carbon\Carbon::parse($document->tanggal_akhir_perpanjangan_penahanan)->format('Y-m-d') : '') }}" readonly required>
                        <small class="text-muted fst-italic">Tanggal akhir dihitung otomatis berdasarkan tanggal mulai dan lama penahanan.</small>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="prison_id">Tempat Penahanan / Rutan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select id="prison_id" name="prison_id" class="form-select select2 @error('rutan_name') is-invalid @enderror" required>
                            <option value="polres" data-name="{{ $defaultRutanName }}" {{ old('prison_id', $document->prison_id) == 'polres' || (empty(old('prison_id', $document->prison_id)) && (old('rutan_name', $document->rutan_name) == $defaultRutanName || empty(old('rutan_name', $document->rutan_name)))) ? 'selected' : '' }}>
                                {{ $defaultRutanName }} (Internal Kepolisian)
                            </option>
                            @if(isset($prisonsGrouped) && $prisonsGrouped->isNotEmpty())
                                @foreach ($prisonsGrouped as $province => $prisons)
                                    <optgroup label="Provinsi {{ $province }}">
                                        @foreach ($prisons as $prison)
                                            <option value="{{ $prison->id }}" data-name="{{ $prison->name }}"
                                                {{ (string) old('prison_id', $document->prison_id) === (string) $prison->id || old('rutan_name', $document->rutan_name) === $prison->name ? 'selected' : '' }}>
                                                {{ $prison->name }}{{ $prison->branch ? ' ('.$prison->branch.')' : '' }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            @endif
                        </select>
                        <input type="hidden" id="rutan_name" name="rutan_name" value="{{ old('rutan_name', $document->rutan_name) }}">
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
                            placeholder="Masukkan alasan mengapa masa penahanan perlu diperpanjang..." required>{{ old('alasan_perpanjangan', $document->alasan_perpanjangan) }}</textarea>
                        @error('alasan_perpanjangan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>
                <h5 class="fw-bold text-blue-dark">Undang-Undang yang Dikenakan<span class="text-danger fs-5">*</span></h5>

                <div class="row col-12 my-2 ms-0">
                    <div id="law">
                        <div class="row mb-2">
                            <div class="col">
                                <button class="btn btn-primary float-right" id="addLawButton" type="button"
                                    data-bs-toggle="modal" data-bs-target="#addLawModal"><i class="bi bi-plus-circle"></i>
                                    Tambah</button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="lawTable">
                                <thead class="table-danger">
                                    <tr class="text-center">
                                        <th scope="col">Jenis Kejahatan</th>
                                        <th scope="col">Golongan Kejahatan</th>
                                        <th scope="col">Undang-Undang</th>
                                        <th scope="col">Pasal</th>
                                        <th scope="col">Opsi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Modal Tambah UU -->
                    <div class="modal fade" id="addLawModal" tabindex="-1" role="dialog" aria-labelledby="addLawModalTitle"
                        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addLawModalTitle">Tambah Undang-Undang</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group mb-3">
                                        <label for="crime_type_id" class="form-label fw-bold">Jenis Kejahatan</label>
                                        <select class="form-control select2" id="crime_type_id" style="width: 100%;">
                                            <option value="">-- Pilih Jenis Kejahatan --</option>
                                            @foreach ($crimeTypes as $crimeType)
                                                <option value="{{ $crimeType->id }}">{{ $crimeType->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="crime_class_id" class="form-label fw-bold">Golongan Kejahatan</label>
                                        <select class="form-control select2" id="crime_class_id" style="width: 100%;">
                                            <option value="">-- Pilih Golongan Kejahatan --</option>
                                            @foreach ($crimeClasses as $crimeClass)
                                                <option value="{{ $crimeClass->id }}">{{ $crimeClass->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="crime_constitution_id" class="form-label fw-bold">Undang-Undang</label>
                                        <select class="form-control select2" id="crime_constitution_id" style="width: 100%;">
                                            <option value="">-- Pilih Undang-Undang --</option>
                                            @foreach ($crimeConstitutions as $crimeConstitution)
                                                <option value="{{ $crimeConstitution->id }}">{{ $crimeConstitution->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="constitution_chapter" class="form-label fw-bold">Pasal</label>
                                        <input type="text" class="form-control" id="constitution_chapter"
                                            placeholder="Contoh: Pasal 310 ayat (4)">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    <button type="button" class="btn btn-primary" id="saveLawButton">Tambahkan</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-primary my-3" role="alert">
                        *Jika ada yang berkaitan dengan tindak pidana yang dipersangkakan <br />
                        Contoh: Undang-Undang nomor 22 tahun 2009 LLAJ tentang Pengemudi mabuk
                    </div>

                    <div class="input-group mt-3">
                        <table class="table table-bordered table-responsive-md" id="additionalLawTable">
                            <thead class="table-danger">
                                <tr class="text-center">
                                    <th scope="col">Nama</th>
                                    <th scope="col">Opsi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="pasal_diduga">Pasal yang Dipersangkakan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <textarea id="pasal_diduga" class="form-control @error('pasal_diduga') is-invalid @enderror" name="pasal_diduga" rows="2"
                            placeholder="Contoh: Pasal 310 ayat (4) Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan" required>{{ old('pasal_diduga', $document->pasal_diduga ?: $defaultPasalDiduga) }}</textarea>
                        <small class="text-muted">Diambil otomatis dari dokumen pendahulu dan dapat disesuaikan jika diperlukan (Poin 2 Surat).</small>
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
                            placeholder="Uraian dugaan tindak pidana..." required>{{ old('dugaan_tindak_pidana', $document->dugaan_tindak_pidana ?: $defaultDugaanTindakPidana) }}</textarea>
                        <small class="text-muted">Uraian ringkas peristiwa dugaan tindak pidana kecelakaan lalu lintas (Poin 2 Surat).</small>
                        @error('dugaan_tindak_pidana')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>
                <h5 class="fw-bold text-blue-dark">Pihak Terlibat</h5>

                @php
                    $selectedSuspectIds = $document->suspects->pluck('id')->toArray();
                    $selectedSignatory = $document->officers->first();
                @endphp

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
                                <option value="{{ $signatory->id }}" {{ old('signatory', $selectedSignatory->officer_id ?? '') == $signatory->id ? 'selected' : '' }}>
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
                                <option value="{{ $officer->id }}" {{ old('contact_officer_id', $document->contact_officer_id) == $officer->id ? 'selected' : '' }}>
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
                                $existingTembusan = old('tembusan', $document->tembusan ?? ['Kepala Kepolisian Resor ' . ($accident->polres->full_name ?? '')]);
                            @endphp
                            @if(!empty($existingTembusan) && is_array($existingTembusan))
                                @foreach($existingTembusan as $cc)
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
                        <i class="bi bi-save"></i> {{ __('Simpan Perubahan') }}
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

            // Datepicker calculation: tanggal mulai + durasi hari -> tanggal akhir
            function updateTanggalAkhir() {
                var mulaiVal = $('#tanggal_mulai_perpanjangan_penahanan').val();
                var durasiVal = parseInt($('#waktu_penahanan_hari').val()) || 30;

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

            $('#tanggal_mulai_perpanjangan_penahanan, #waktu_penahanan_hari').on('change input', function() {
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

            // Setup Master Laws
            var existingMainLaws = @json($existingMainLaws ?? []);
            var existingAdditionalLaws = @json($existingAdditionalLaws ?? []);

            function addMainLawRow(law) {
                var row = '<tr>' +
                    '<td>' + (law.crime_type_name || '-') + 
                        '<input type="hidden" name="lawCrimeTypeIds[]" value="' + law.crime_type_id + '">' +
                    '</td>' +
                    '<td>' + (law.crime_class_name || '-') + 
                        '<input type="hidden" name="lawCrimeClassIds[]" value="' + law.crime_class_id + '">' +
                    '</td>' +
                    '<td>' + (law.crime_constitution_name || '-') + 
                        '<input type="hidden" name="lawCrimeConstitutionIds[]" value="' + law.crime_constitution_id + '">' +
                    '</td>' +
                    '<td>' + (law.constitution_chapter || '-') + 
                        '<input type="hidden" name="lawCrimeConstitutionChapters[]" value="' + (law.constitution_chapter || '') + '">' +
                    '</td>' +
                    '<td class="text-center">' +
                        '<button type="button" class="btn btn-sm btn-outline-danger removeLawRowBtn"><i class="bi bi-trash"></i> Hapus</button>' +
                    '</td>' +
                '</tr>';
                $('#lawTable tbody').append(row);
            }

            function addAdditionalLawRow(law) {
                var row = '<tr>' +
                    '<td>' + law.constitution + 
                        '<input type="hidden" name="lawAdditionalNames[]" value="' + law.constitution + '">' +
                    '</td>' +
                    '<td class="text-center">' +
                        '<button type="button" class="btn btn-sm btn-outline-danger removeAdditionalLawRowBtn"><i class="bi bi-trash"></i> Hapus</button>' +
                    '</td>' +
                '</tr>';
                $('#additionalLawTable tbody').append(row);
            }

            existingMainLaws.forEach(function(l) {
                addMainLawRow(l);
            });

            existingAdditionalLaws.forEach(function(l) {
                addAdditionalLawRow(l);
            });

            $('#saveLawButton').click(function() {
                var typeId = $('#crime_type_id').val();
                var typeName = $('#crime_type_id option:selected').text();
                var classId = $('#crime_class_id').val();
                var className = $('#crime_class_id option:selected').text();
                var constId = $('#crime_constitution_id').val();
                var constName = $('#crime_constitution_id option:selected').text();
                var chapter = $('#constitution_chapter').val();

                if (!typeId || !classId || !constId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Silakan lengkapi pilihan Jenis, Golongan, dan Undang-Undang terlebih dahulu.'
                    });
                    return;
                }

                addMainLawRow({
                    crime_type_id: typeId,
                    crime_type_name: typeName,
                    crime_class_id: classId,
                    crime_class_name: className,
                    crime_constitution_id: constId,
                    crime_constitution_name: constName,
                    constitution_chapter: chapter
                });

                $('#addLawModal').modal('hide');
                $('#crime_type_id').val('').trigger('change');
                $('#crime_class_id').val('').trigger('change');
                $('#crime_constitution_id').val('').trigger('change');
                $('#constitution_chapter').val('');
            });

            $(document).on('click', '.removeLawRowBtn', function() {
                $(this).closest('tr').remove();
            });

            $(document).on('click', '.removeAdditionalLawRowBtn', function() {
                $(this).closest('tr').remove();
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

            // AJAX Validation handling on form submit with SweetAlert2
            $('#s22Form').on('submit', function(e) {
                e.preventDefault();
                
                var $btn = $('#s22FormSubmit');
                var originalHtml = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');

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
                                text: response.message || 'Data valid, menyimpan perubahan...',
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
                                var errorMessages = '';
                                $.each(res.errors, function(key, value) {
                                    var msg = Array.isArray(value) ? value[0] : value;
                                    errorMessages += '- ' + msg + '<br>';
                                });

                                return Swal.fire({
                                    icon: 'error',
                                    title: 'Mohon Periksa Kembali Isian Anda',
                                    html: errorMessages,
                                    confirmButtonText: 'OK',
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
