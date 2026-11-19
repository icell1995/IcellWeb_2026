@php
    $_title = 'Edit Surat Pengiriman Berkas Perkara (Tahap I)';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <style>
        .select2-container .select2-selection.border-danger,
        .select2-container .select2-selection.is-invalid {
            border-color: #dc3545 !important;
        }
        .invalid-feedback.frontend-error {
            display: block;
            font-size: 80%;
            color: #dc3545;
            margin-top: 0.25rem;
        }
    </style>
@endpush

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"><i class="bi bi-arrow-left"></i>
        Kembali ke Progress Perkara</a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">
                Edit Surat Pengiriman Berkas Perkara (Tahap I)
                <span class="badge bg-info text-white ms-2">SPPT-TI / Pusiknas Bareskrim</span>
            </h5>

            <div class="alert alert-danger mt-3" id="attentionBox">
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
                        <ul>
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
            <form action="{{ route('doc.tahap-1-document.update', ['accident_id' => $accidentId, 'id' => $document->id]) }}"
                method="POST" enctype="multipart/form-data" id="tahap1Form" novalidate>
                @csrf
                
                <input type="hidden" name="accident_id" value="{{ $accidentId }}">

                <h5 class="fw-bold text-blue-dark">INFORMASI DASAR DOKUMEN</h5>

                {{-- Nomor LP --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="no_lp">Nomor LP</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="no_lp" type="text" class="form-control bg-light" value="{{ $accident->no_lp }}" readonly>
                    </div>
                </div>

                {{-- Nomor Dokumen --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="document_number">Nomor Dokumen (S-50)<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="document_number" type="text"
                            class="form-control @error('document_number') is-invalid @enderror"
                            name="document_number" value="{{ old('document_number', $document->document_number) }}" required
                            placeholder="Contoh: B/001/I/RES.0.0.1/2026/Satker">

                        @error('document_number')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Tanggal Surat --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="document_date">Tanggal Dokumen<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input class="form-control @error('document_date') is-invalid @enderror" id="document_date" name="document_date"
                            placeholder="YYYY-MM-DD" autocomplete="off" value="{{ old('document_date', $document->document_date ? $document->document_date->format('Y-m-d') : '') }}"
                            data-provide="datepicker" required>

                        @error('document_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Klasifikasi --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="klasifikasi">Klasifikasi<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="klasifikasi" id="klasifikasi" required>
                            <option value="Biasa" {{ old('klasifikasi', $document->klasifikasi) == 'Biasa' ? 'selected' : '' }}>Biasa</option>
                            <option value="Rahasia" {{ old('klasifikasi', $document->klasifikasi) == 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
                            <option value="Sangat Rahasia" {{ old('klasifikasi', $document->klasifikasi) == 'Sangat Rahasia' ? 'selected' : '' }}>Sangat Rahasia</option>
                        </select>
                    </div>
                </div>

                {{-- Lampiran --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="lampiran">Lampiran</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="lampiran" type="text" class="form-control" name="lampiran" value="{{ old('lampiran', $document->lampiran) }}" placeholder="Contoh: 1 (satu) berkas">
                    </div>
                </div>


                <hr>

                <h5 class="fw-bold text-blue-dark">REFERENSI DOKUMEN & BERKAS PERKARA</h5>

                {{-- Surat Perintah Penyidikan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="surat_perintah_penyidikan_id">Nomor Surat Perintah Penyidikan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="surat_perintah_penyidikan_id" id="surat_perintah_penyidikan_id" required>
                            <option value="">--Pilih No Surat Perintah Penyidikan--</option>
                            @foreach ($suratPerintahPenyidikanDocuments as $sp)
                                <option value="{{ $sp->id }}" {{ old('surat_perintah_penyidikan_id', $document->surat_perintah_penyidikan_id) == $sp->id ? 'selected' : '' }}>
                                    {{ $sp->document_number }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih Surat Perintah Penyidikan yang terkait</small>
                    </div>
                </div>

                {{-- SPDP --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="surat_pemberitahuan_dimulainya_penyidikan_id">Nomor SPDP<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="surat_pemberitahuan_dimulainya_penyidikan_id" id="surat_pemberitahuan_dimulainya_penyidikan_id" required>
                            <option value="">--Pilih No SPDP--</option>
                            @foreach ($suratPemberitahuanDimulainyaPenyidikanDocuments as $spdp)
                                <option value="{{ $spdp->id }}" {{ old('surat_pemberitahuan_dimulainya_penyidikan_id', $document->surat_pemberitahuan_dimulainya_penyidikan_id) == $spdp->id ? 'selected' : '' }}>
                                    {{ $spdp->document_number }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- S.Tap Penetapan Tersangka --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="surat_ketetapan_penetapan_tersangka_id">Nomor S.Tap Penetapan Tersangka</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="surat_ketetapan_penetapan_tersangka_id" id="surat_ketetapan_penetapan_tersangka_id">
                            <option value="">--Pilih No S.Tap Penetapan Tersangka--</option>
                            @foreach ($suratKetetapanTentangPenetapanTersangkaDocuments as $st)
                                <option value="{{ $st->id }}" {{ old('surat_ketetapan_penetapan_tersangka_id', $document->surat_ketetapan_penetapan_tersangka_id) == $st->id ? 'selected' : '' }}>
                                    {{ $st->document_number }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                {{-- Berkas Perkara Number --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="berkas_perkara_number">Nomor Berkas Perkara<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="berkas_perkara_number" type="text" class="form-control" name="berkas_perkara_number" value="{{ old('berkas_perkara_number', $document->berkas_perkara_number) }}" required>
                    </div>
                </div>

                {{-- Berkas Perkara Date --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="berkas_perkara_date">Tanggal Dokumen Ditandatangani<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input class="form-control" id="berkas_perkara_date" name="berkas_perkara_date" placeholder="YYYY-MM-DD" value="{{ old('berkas_perkara_date', $document->berkas_perkara_date ? $document->berkas_perkara_date->format('Y-m-d') : '') }}" data-provide="datepicker" required>
                    </div>
                </div>

                {{-- Jumlah Rangkap --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="berkas_perkara_rangkap">Jumlah Rangkap<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="berkas_perkara_rangkap" type="number" class="form-control" name="berkas_perkara_rangkap" value="{{ old('berkas_perkara_rangkap', $document->berkas_perkara_rangkap) }}" min="1" required>
                    </div>
                </div>

                <hr>

                <h5 class="fw-bold text-blue-dark">DATA TERSANGKA & TINDAK PIDANA</h5>

                
                <hr>

                {{-- Uraian Singkat Perkara --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="uraianPerkara">Uraian Singkat Perkara <span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <textarea id="uraianPerkara" class="form-control" name="uraianPerkara" rows="4" placeholder="Uraikan secara singkat dugaan tindak pidana yang terjadi..." required>{{ old('uraianPerkara', $document->messages['uraian_singkat_perkara'] ?? ($accident->damage_lose_desc ?? '')) }}</textarea>
                    </div>
                </div>

                {{-- Lokasi Kejadian --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="lokasiKejadian">Lokasi Kejadian <span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="lokasiKejadian" type="text" class="form-control" name="lokasiKejadian" value="{{ old('lokasiKejadian', $document->messages['lokasi_kejadian'] ?? $accident->road_name ?? '') }}" placeholder="Jalan/Tempat kejadian" required>
                    </div>
                </div>

                {{-- Kode Wilayah --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="kodeWilayah">Kode Wilayah</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="kodeWilayah" id="kodeWilayah" required>
                            <option value="">-- Pilih Kecamatan --</option>
                            @foreach ($districts as $district)
                                <option value="{{ $district['KodePuskarda'] }}" {{ old('kodeWilayah', $defaultKodeWilayah ?? '') == $district['KodePuskarda'] ? 'selected' : '' }}>
                                    {{ $district['KodePuskarda'] }} &mdash; {{ $district['Nama'] }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-info mt-1"><i class="bi bi-info-circle"></i> Opsi menyesuaikan wilayah polres saat ini.</small>
                    </div>
                </div>

                {{-- Waktu Kejadian (Hidden) --}}
                <input type="hidden" name="waktuKejadian" value="{{ old('waktuKejadian', $document->messages['waktu_kejadian'] ?? ('Sekitar pukul ' . ($accident->accident_time ? \Carbon\Carbon::parse($accident->accident_time)->format('H:i') : '-') . ' WIB')) }}">

                {{-- Tanggal Kejadian (Hidden) --}}
                <input type="hidden" name="tahunKejadian" value="{{ old('tahunKejadian', $document->messages['tahun_kejadian'] ?? ($accident->accident_date ? date('Y', strtotime($accident->accident_date)) : '')) }}">
                <input type="hidden" name="bulanKejadian" value="{{ old('bulanKejadian', $document->messages['bulan_kejadian'] ?? ($accident->accident_date ? date('n', strtotime($accident->accident_date)) : '')) }}">
                <input type="hidden" name="tanggalKejadian" value="{{ old('tanggalKejadian', $document->messages['tanggal_kejadian'] ?? ($accident->accident_date ? date('j', strtotime($accident->accident_date)) : '')) }}">

                {{-- Daftar Tersangka --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="suspects">Daftar Tersangka<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        @php
                            $selectedSuspects = is_array(old('suspects')) ? old('suspects') : $document->suspects->pluck('id')->toArray();
                        @endphp
                        <select class="form-control select2 @error('suspects') is-invalid @enderror" 
                                name="suspects[]" id="suspects" multiple="multiple" required>
                            @foreach ($suspects as $suspect)
                                <option value="{{ $suspect->id }}" {{ in_array($suspect->id, $selectedSuspects) ? 'selected' : '' }}>
                                    {{ $suspect->name }} - {{ $suspect->identity_number }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih Tersangka dalam Berkas Ini</small>
                    </div>
                </div>


                {{-- Pasal Disangkakan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label">Pasal yang Disangkakan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <div class="p-2 bg-light border rounded text-muted" id="pasal_disangkakan_display" style="min-height: 80px;">{{ old('pasal_disangkakan', $document->pasal_disangkakan ?: 'Akan terisi otomatis berdasarkan Sprindik terpilih') }}</div>
                        <input type="hidden" id="pasal_disangkakan" name="pasal_disangkakan" value="{{ old('pasal_disangkakan', $document->pasal_disangkakan) }}">
                    </div>
                </div>

                <hr>

                

                <hr>

                <h5 class="fw-bold text-blue-dark">INFORMASI PENAHANAN</h5>

                {{-- Status Penahanan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label">Status Penahanan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12 d-flex align-items-center">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input penahanan-status-radio" type="radio" name="penahanan_status" id="status_ditahan" value="DITAHAN" {{ old('penahanan_status', $document->penahanan_status ?? 'DITAHAN') == 'DITAHAN' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="status_ditahan">Ditahan</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input penahanan-status-radio" type="radio" name="penahanan_status" id="status_ditangguhkan" value="DITANGGUHKAN" {{ old('penahanan_status', $document->penahanan_status) == 'DITANGGUHKAN' ? 'checked' : '' }}>
                            <label class="form-check-label" for="status_ditangguhkan">Ditangguhkan</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input penahanan-status-radio" type="radio" name="penahanan_status" id="status_tidak_ditahan" value="TIDAK_DITAHAN" {{ old('penahanan_status', $document->penahanan_status) == 'TIDAK_DITAHAN' ? 'checked' : '' }}>
                            <label class="form-check-label" for="status_tidak_ditahan">Tidak Ditahan</label>
                        </div>
                    </div>
                </div>

                <div id="detentionFieldsContainer" style="{{ old('penahanan_status', $document->penahanan_status ?? 'DITAHAN') == 'TIDAK_DITAHAN' ? 'display:none;' : '' }}">
                    <div class="input-group row mb-3 ms-0">
                        <div class="col-sm-3"></div>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-12 row pe-0">
                            <div class="col-md-6 mb-2">
                                <label class="fw-bold d-block mb-1" for="penahanan_rutan">Nama Rutan <span class="text-danger required-penahanan">*</span></label>
                                <select id="penahanan_rutan" name="penahanan_rutan" class="form-control select2">
                                    <option value="">--Pilih Rutan--</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2 pe-0">
                                <label class="fw-bold d-block mb-1" for="penahanan_cabang">Cabang Rutan</label>
                                <select id="penahanan_cabang" name="penahanan_cabang" class="form-control select2">
                                    <option value="">--Pilih Cabang--</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="input-group row mb-3 ms-0">
                        <div class="col-sm-3"></div>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-12 row pe-0">
                            <div class="col-md-6 mb-2">
                                <label class="fw-bold d-block mb-1" for="penahanan_start_date">Tgl Mulai Penahanan <span class="text-danger required-penahanan">*</span></label>
                                <input class="form-control" id="penahanan_start_date" name="penahanan_start_date" placeholder="YYYY-MM-DD" value="{{ old('penahanan_start_date', $document->penahanan_start_date ? $document->penahanan_start_date->format('Y-m-d') : '') }}" data-provide="datepicker">
                            </div>
                            <div class="col-md-6 mb-2 pe-0">
                                <label class="fw-bold d-block mb-1" for="penahanan_end_date">Tgl Selesai Penahanan <span class="text-danger required-penahanan">*</span></label>
                                <input class="form-control" id="penahanan_end_date" name="penahanan_end_date" placeholder="YYYY-MM-DD" value="{{ old('penahanan_end_date', $document->penahanan_end_date ? $document->penahanan_end_date->format('Y-m-d') : '') }}" data-provide="datepicker">
                            </div>
                        </div>
                    </div>
                    <div class="input-group row mb-3 ms-0">
                        <div class="col-sm-3"></div>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-12 row pe-0">
                            <div class="col-md-6 mb-2">
                                <label class="fw-bold d-block mb-1" for="surat_perintah_penahanan_number">No. Surat Penahanan <span class="text-danger required-penahanan">*</span></label>
                                <select id="surat_perintah_penahanan_number" name="surat_perintah_penahanan_number" class="form-control select2"
                                    data-placeholder="-- Pilih Surat Perintah Penahanan --">
                                    <option value="">-- Pilih Surat Perintah Penahanan --</option>
                                    @if($document->surat_perintah_penahanan_number && !$suratPerintahPenahananDocuments->contains('document_number', $document->surat_perintah_penahanan_number))
                                        <option value="{{ $document->surat_perintah_penahanan_number }}" selected>
                                            {{ $document->surat_perintah_penahanan_number }} (Tersimpan sebelumnya)
                                        </option>
                                    @endif
                                    @foreach($suratPerintahPenahananDocuments as $spp)
                                        @php
                                            $sppSuspects = ($spp->suspects && $spp->suspects->count() > 0) ? $spp->suspects->pluck('name')->implode(', ') : '';
                                        @endphp
                                        <option value="{{ $spp->document_number }}"
                                            data-date="{{ $spp->document_date ? \Carbon\Carbon::parse($spp->document_date)->format('Y-m-d') : '' }}"
                                            data-rutan="{{ $spp->lokasi_penahanan }}"
                                            data-cabang="{{ $spp->cabang_penahanan }}"
                                            data-start-date="{{ $spp->tanggal_mulai ? \Carbon\Carbon::parse($spp->tanggal_mulai)->format('Y-m-d') : '' }}"
                                            data-end-date="{{ $spp->tanggal_akhir ? \Carbon\Carbon::parse($spp->tanggal_akhir)->format('Y-m-d') : '' }}"
                                            {{ old('surat_perintah_penahanan_number', $document->surat_perintah_penahanan_number) == $spp->document_number ? 'selected' : '' }}>
                                            {{ $spp->document_number }}
                                            @if($sppSuspects) &mdash; {{ $sppSuspects }} @endif
                                        </option>
                                    @endforeach
                                </select>
                                @if($suratPerintahPenahananDocuments->isEmpty())
                                    <div class="mt-2 p-2 bg-light border border-warning rounded d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <small class="text-danger mb-0 fw-semibold"><i class="bi bi-exclamation-triangle-fill me-1"></i> Belum ada Surat Perintah Penahanan yang dibuat.</small>
                                        <a href="{{ route('doc.surat-perintah-penahanan-document.create', ['accident_id' => $accidentId]) }}" target="_blank" class="btn btn-sm btn-primary">
                                            <i class="bi bi-plus-circle me-1"></i> Buat Surat Perintah Penahanan
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6 mb-2 pe-0">
                                <label class="fw-bold d-block mb-1" for="surat_perintah_penahanan_date">Tgl Surat Penahanan <span class="text-danger required-penahanan">*</span></label>
                                <input class="form-control" id="surat_perintah_penahanan_date" name="surat_perintah_penahanan_date" placeholder="YYYY-MM-DD" value="{{ old('surat_perintah_penahanan_date', $document->surat_perintah_penahanan_date ? $document->surat_perintah_penahanan_date->format('Y-m-d') : '') }}" readonly style="background-color: #e9ecef; cursor: not-allowed;">
                            </div>
                        </div>
                    </div>
                    <hr class="border-secondary border-dashed">
                    <div class="input-group row mb-3 ms-0">
                        <div class="col-sm-3"></div>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-12 row pe-0">
                            <div class="col-md-6 mb-2">
                                <label class="fw-bold d-block mb-1" for="surat_perpanjangan_penahanan_number">No. Surat Perpanjangan Penahanan <span class="text-danger required-ditangguhkan" style="{{ old('penahanan_status', $document->penahanan_status) == 'DITANGGUHKAN' ? '' : 'display:none;' }}">*</span></label>
                                <select id="surat_perpanjangan_penahanan_number" name="surat_perpanjangan_penahanan_number" class="form-control select2"
                                    data-placeholder="-- Pilih Surat Perpanjangan Penahanan --">
                                    <option value="">-- Pilih Surat Perpanjangan Penahanan --</option>
                                    @if($document->surat_perpanjangan_penahanan_number && !$suratPerpanjanganPenahananDocuments->contains('document_number', $document->surat_perpanjangan_penahanan_number))
                                        <option value="{{ $document->surat_perpanjangan_penahanan_number }}" selected>
                                            {{ $document->surat_perpanjangan_penahanan_number }} (Tersimpan sebelumnya)
                                        </option>
                                    @endif
                                    @foreach($suratPerpanjanganPenahananDocuments as $sppp)
                                        @php
                                            $spppSuspects = ($sppp->suspects && $sppp->suspects->count() > 0) ? $sppp->suspects->pluck('name')->implode(', ') : '';
                                        @endphp
                                        <option value="{{ $sppp->document_number }}"
                                            data-date="{{ $sppp->document_date ? \Carbon\Carbon::parse($sppp->document_date)->format('Y-m-d') : '' }}"
                                            {{ old('surat_perpanjangan_penahanan_number', $document->surat_perpanjangan_penahanan_number) == $sppp->document_number ? 'selected' : '' }}>
                                            {{ $sppp->document_number }}
                                            @if($spppSuspects) &mdash; {{ $spppSuspects }} @endif
                                        </option>
                                    @endforeach
                                </select>
                                @if($suratPerpanjanganPenahananDocuments->isEmpty())
                                    <div class="mt-2 p-2 bg-light border border-warning rounded d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <small class="text-danger mb-0 fw-semibold"><i class="bi bi-exclamation-triangle-fill me-1"></i> Belum ada Surat Perpanjangan Penahanan yang dibuat.</small>
                                        <a href="{{ route('doc.surat-permohonan-perpanjangan-penahanan-kejaksaan-document.create', ['accident_id' => $accidentId]) }}" target="_blank" class="btn btn-sm btn-primary">
                                            <i class="bi bi-plus-circle me-1"></i> Buat Surat Perpanjangan Penahanan
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6 mb-2 pe-0">
                                <label class="fw-bold d-block mb-1" for="surat_perpanjangan_penahanan_date">Tgl Surat Perpanjangan Penahanan <span class="text-danger required-ditangguhkan" style="{{ old('penahanan_status', $document->penahanan_status) == 'DITANGGUHKAN' ? '' : 'display:none;' }}">*</span></label>
                                <input class="form-control" id="surat_perpanjangan_penahanan_date" name="surat_perpanjangan_penahanan_date" placeholder="YYYY-MM-DD" value="{{ old('surat_perpanjangan_penahanan_date', $document->surat_perpanjangan_penahanan_date ? $document->surat_perpanjangan_penahanan_date->format('Y-m-d') : '') }}" readonly style="background-color: #e9ecef; cursor: not-allowed;">
                            </div>
                        </div>
                    </div>
                    <div class="input-group row mb-3 ms-0">
                        <div class="col-sm-3"></div>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-12 row pe-0">
                            <div class="col-md-6 mb-2">
                                <label class="fw-bold d-block mb-1" for="surat_perpanjangan_penahanan_court_number">No. Surat Perpanjangan Penahanan ke Pengadilan <span class="text-danger required-ditangguhkan" style="{{ old('penahanan_status', $document->penahanan_status) == 'DITANGGUHKAN' ? '' : 'display:none;' }}">*</span></label>
                                <input id="surat_perpanjangan_penahanan_court_number" type="text" class="form-control" name="surat_perpanjangan_penahanan_court_number" value="{{ old('surat_perpanjangan_penahanan_court_number', $document->surat_perpanjangan_penahanan_court_number) }}" placeholder="Nomor perpanjangan ke Pengadilan">
                            </div>
                            <div class="col-md-6 mb-2 pe-0">
                                <label class="fw-bold d-block mb-1" for="surat_perpanjangan_penahanan_court_date">Tgl Surat Perpanjangan Penahanan ke Pengadilan <span class="text-danger required-ditangguhkan" style="{{ old('penahanan_status', $document->penahanan_status) == 'DITANGGUHKAN' ? '' : 'display:none;' }}">*</span></label>
                                <input class="form-control" id="surat_perpanjangan_penahanan_court_date" name="surat_perpanjangan_penahanan_court_date" placeholder="YYYY-MM-DD" value="{{ old('surat_perpanjangan_penahanan_court_date', $document->surat_perpanjangan_penahanan_court_date ? $document->surat_perpanjangan_penahanan_court_date->format('Y-m-d') : '') }}" data-provide="datepicker">
                            </div>
                        </div>
                    </div>

                    {{-- Suspension Fields (Only for DITANGGUHKAN) --}}
                    <div id="suspensionFields" style="{{ old('penahanan_status', $document->penahanan_status) == 'DITANGGUHKAN' ? '' : 'display:none;' }}">
                        <div class="input-group row mb-3 ms-0">
                            <div class="col-sm-3"></div>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12 row pe-0">
                                <div class="col-md-6 mb-2">
                                    <label class="fw-bold d-block mb-1" for="surat_penangguhan_penahanan_number">No. Surat Penangguhan Penahanan <span class="text-danger required-ditangguhkan">*</span></label>
                                    <select id="surat_penangguhan_penahanan_number" name="surat_penangguhan_penahanan_number" class="form-control select2"
                                        data-placeholder="-- Pilih Surat Penangguhan Penahanan --">
                                        <option value="">-- Pilih Surat Penangguhan Penahanan --</option>
                                        @if($document->surat_penangguhan_penahanan_number && !$suratPenangguhanPenahananDocuments->contains('document_number', $document->surat_penangguhan_penahanan_number))
                                            <option value="{{ $document->surat_penangguhan_penahanan_number }}" selected>
                                                {{ $document->surat_penangguhan_penahanan_number }} (Tersimpan sebelumnya)
                                            </option>
                                        @endif
                                        @foreach($suratPenangguhanPenahananDocuments as $spnp)
                                            @php
                                                $spnpSuspects = ($spnp->suspects && $spnp->suspects->count() > 0) ? $spnp->suspects->pluck('name')->implode(', ') : '';
                                            @endphp
                                            <option value="{{ $spnp->document_number }}"
                                                data-date="{{ $spnp->document_date ? \Carbon\Carbon::parse($spnp->document_date)->format('Y-m-d') : '' }}"
                                                {{ old('surat_penangguhan_penahanan_number', $document->surat_penangguhan_penahanan_number) == $spnp->document_number ? 'selected' : '' }}>
                                                {{ $spnp->document_number }}
                                                @if($spnpSuspects) &mdash; {{ $spnpSuspects }} @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($suratPenangguhanPenahananDocuments->isEmpty())
                                        <div class="mt-2 p-2 bg-light border border-warning rounded d-flex align-items-center justify-content-between flex-wrap gap-2">
                                            <small class="text-danger mb-0 fw-semibold"><i class="bi bi-exclamation-triangle-fill me-1"></i> Belum ada Surat Penangguhan Penahanan yang dibuat.</small>
                                            <a href="{{ route('doc.surat-perintah-penangguhan-penahanan-document.create', ['accident_id' => $accidentId]) }}" target="_blank" class="btn btn-sm btn-primary">
                                                <i class="bi bi-plus-circle me-1"></i> Buat Surat Penangguhan Penahanan
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6 mb-2 pe-0">
                                    <label class="fw-bold d-block mb-1" for="surat_penangguhan_penahanan_date">Tgl Surat Penangguhan Penahanan <span class="text-danger required-ditangguhkan">*</span></label>
                                    <input class="form-control" id="surat_penangguhan_penahanan_date" name="surat_penangguhan_penahanan_date" placeholder="YYYY-MM-DD" value="{{ old('surat_penangguhan_penahanan_date', $document->surat_penangguhan_penahanan_date ? $document->surat_penangguhan_penahanan_date->format('Y-m-d') : '') }}" readonly style="background-color: #e9ecef; cursor: not-allowed;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <h5 class="fw-bold text-blue-dark">TUJUAN & LOKASI</h5>

                {{-- Kejaksaan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="prosecutor_id">Kejaksaan Negeri Tujuan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="prosecutor_id" id="prosecutor_id" required>
                            <option value="">--Pilih Kejaksaan--</option>
                            @foreach ($prosecutors as $prosecutor)
                                <option value="{{ $prosecutor->id }}" {{ old('prosecutor_id', $document->prosecutor_id) == $prosecutor->id ? 'selected' : '' }}>{{ $prosecutor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <hr>

                <h5 class="fw-bold text-blue-dark">BARANG BUKTI & PENYIDIK</h5>

                                {{-- SAKSI (Structured untuk TAHAP 1) --}}
                <div class="input-group row mb-3 ms-0" id="div-saksi">
                    <label class="fw-bold col-sm-3 col-form-label">Data Saksi</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <div class="table-responsive">
                            <table id="saksiTable" class="table table-bordered table-sm table-hover w-100">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Nama Saksi</th>
                                        <th>Tempat Lahir</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Pekerjaan</th>
                                        <th>Alamat Lengkap</th>
                                        <th class="text-center" style="width: 50px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Akan diisi via JavaScript -->
                                    <tr id="emptySaksiRow">
                                        <td colspan="7" class="text-center text-muted small py-3">Belum ada data saksi.</td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-sm btn-success mt-1" data-toggle="modal" data-target="#saksiModal">
                                <i class="bi bi-plus-circle"></i> Tambah Saksi
                            </button>
                        </div>
                        <div id="saksiHiddenInputsContainer"></div>
                    </div>
                </div>

                <hr class="border-secondary border-dashed">

{{-- Barang Bukti (Structured untuk TAHAP 1) --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label">Daftar Barang Bukti</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="bbTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Nama Barang Bukti <span class="text-danger">*</span></th>
                                        <th style="width: 100px;">Jumlah <span class="text-danger">*</span></th>
                                        <th style="width: 150px;">Satuan <span class="text-danger">*</span></th>
                                        <th>Keterangan</th>
                                        <th style="width: 50px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // $document->barang_bukti is already a JSON array, assuming we saved it correctly.
                                        $oldBb = old('daftar_barang_bukti', is_array($document->barang_bukti) && isset($document->barang_bukti[0]['nama']) ? $document->barang_bukti : []);
                                    @endphp
                                    @if(count($oldBb) > 0)
                                        @foreach($oldBb as $i => $bb)
                                        <tr>
                                            <td><input type="text" name="daftar_barang_bukti[{{$i}}][nama]" class="form-control form-control-sm" value="{{ $bb['nama'] ?? '' }}" required></td>
                                            <td><input type="number" name="daftar_barang_bukti[{{$i}}][jumlah]" class="form-control form-control-sm" value="{{ $bb['jumlah'] ?? 1 }}" min="1" required></td>
                                            <td><input type="text" name="daftar_barang_bukti[{{$i}}][satuan]" class="form-control form-control-sm" value="{{ $bb['satuan'] ?? 'buah' }}" required></td>
                                            <td><input type="text" name="daftar_barang_bukti[{{$i}}][keterangan]" class="form-control form-control-sm" value="{{ $bb['keterangan'] ?? '' }}"></td>
                                            <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="bi bi-trash"></i></button></td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td><input type="text" name="daftar_barang_bukti[0][nama]" class="form-control form-control-sm" required placeholder="Contoh: Pisau"></td>
                                            <td><input type="number" name="daftar_barang_bukti[0][jumlah]" class="form-control form-control-sm" value="1" min="1" required></td>
                                            <td><input type="text" name="daftar_barang_bukti[0][satuan]" class="form-control form-control-sm" value="buah" required></td>
                                            <td><input type="text" name="daftar_barang_bukti[0][keterangan]" class="form-control form-control-sm" placeholder="Contoh: Panjang 30cm"></td>
                                            <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="bi bi-trash"></i></button></td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-sm btn-success" id="addBbBtn"><i class="bi bi-plus-circle"></i> Tambah Barang Bukti</button>
                        </div>
                    </div>
                </div>

                {{-- Tempat Simpan BB --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="barang_bukti_storage">Tempat Penyimpanan Barang Bukti</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="barang_bukti_storage" type="text" class="form-control" name="barang_bukti_storage" value="{{ old('barang_bukti_storage', $document->barang_bukti_storage) }}">
                    </div>
                </div>

                {{-- Penyidik --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="investigator_selection">Penyidik / Penyidik Pembantu (Cari Nama/NRP)</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" id="investigator_selection">
                            <option value="">--Pilih Penyidik--</option>
                            @foreach ($authorizedOfficers as $officer)
                                <option value="{{ $officer->id }}" 
                                    data-phone="{{ $officer->phone_number }}"
                                    data-rank-name="{{ $officer->rank->name ?? '' }}"
                                    data-full-name="{{ $officer->full_name }}"
                                    {{ (isset($document) && $document->investigator_pangkat_nama == ($officer->rank->name . ' ' . $officer->full_name)) ? 'selected' : '' }}>
                                    {{ $officer->register_number . ' | ' . ($officer->rank->name ?? '') . ' ' . $officer->full_name . ($officer->position->name ? ' | ' . $officer->position->name : '') }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="investigator_pangkat_nama" id="investigator_pangkat_nama" value="{{ old('investigator_pangkat_nama', $document->investigator_pangkat_nama) }}">
                        <small class="text-muted text-italic">Cari berdasarkan NRP atau Nama Petugas</small>
                    </div>
                </div>

                {{-- HP Penyidik --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="investigator_hp">No. HP Penyidik</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="investigator_hp" type="text" class="form-control" name="investigator_hp" value="{{ old('investigator_hp', $document->investigator_hp) }}" placeholder="08xxxxxxxxxx">
                    </div>
                </div>

                <hr>

                <h5 class="fw-bold text-blue-dark">PEJABAT PENANDATANGAN</h5>

                {{-- Pejabat --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="signatory">Pejabat Penandatangan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="signatory" id="signatory" required>
                            <option value="">--Pilih Yang Menandatangani--</option>
                            @foreach ($authorizedSignatories as $data)
                                @php
                                    $signatoryOfficer = $document->officers->where('class', 'SIGNATORY')->first();
                                    $selectedId = old('signatory', $signatoryOfficer ? ($data->register_number == $signatoryOfficer->register_number ? $data->id : null) : null);
                                @endphp
                                <option value="{{ $data->id }}" {{ $selectedId == $data->id ? 'selected' : '' }}>
                                    {{ $data->register_number . ' - ' . $data->full_name . ' | ' . ($data->position->name ?? '') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Tembusan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label">Tembusan Lainnya</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <div id="tembusanContainer">
                            @php $tembusanList = old('tembusan', $document->tembusan ?? []); @endphp
                            @foreach ($tembusanList as $item)
                                <div class="input-group mb-2">
                                    <input type="text" name="tembusan[]" class="form-control" value="{{ $item }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-danger removeTembusan" type="button">Hapus</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button class="btn btn-primary addTembusan" type="button">Tambah</button>
                    </div>
                </div>


                <div class="mt-4 d-flex justify-content-center">
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-secondary me-2">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                    <button type="button" class="btn btn-primary" id="tahap1FormSubmit">
                        <i class="bi bi-save"></i> Update Dokumen
                    </button>
                </div>
            </form>
        </div>
    </div>

        <!-- Modal Saksi -->
    <div class="modal fade" id="saksiModal" tabindex="-1" aria-labelledby="saksiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-primary" id="saksiModalLabel"><i class="bi bi-person-plus-fill me-2"></i>Tambah Data Saksi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body bg-white">
                <div class="row">
                    <!-- Kolom Kiri: Data Pribadi -->
                    <div class="col-md-6">
                        <h6 class="fw-bold text-secondary mb-3 border-bottom pb-2">Data Pribadi</h6>
                        
                        <div class="form-group row mb-2">
                            <label class="col-sm-4 col-form-label fw-bold">Nama Saksi <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="saksiNama" placeholder="Nama Lengkap">
                            </div>
                        </div>

                        <div class="form-group row mb-2">
                            <label class="col-sm-4 col-form-label fw-bold">Tempat Lahir</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="saksiTempatLahir" placeholder="Tempat Lahir">
                            </div>
                        </div>

                        <div class="form-group row mb-2">
                            <label class="col-sm-4 col-form-label fw-bold">Jenis Kelamin</label>
                            <div class="col-sm-8">
                                <select class="form-control select2-saksi" id="saksiGender" style="width: 100%;">
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    @foreach($genders as $g)
                                        <option value="{{ $g->pusiknas_id }}">{{ $g->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-2">
                            <label class="col-sm-4 col-form-label fw-bold">Agama</label>
                            <div class="col-sm-8">
                                <select class="form-control select2-saksi" id="saksiAgama" style="width: 100%;">
                                    <option value="">-- Pilih Agama --</option>
                                    @foreach($religions as $r)
                                        <option value="{{ $r->pusiknas_id }}">{{ $r->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-2">
                            <label class="col-sm-4 col-form-label fw-bold">Status Perkawinan</label>
                            <div class="col-sm-8">
                                <select class="form-control select2-saksi" id="saksiKawin" style="width: 100%;">
                                    <option value="">-- Pilih Status Perkawinan --</option>
                                    @foreach($maritalStatuses as $m)
                                        <option value="{{ $m->pusiknas_id }}">{{ $m->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-2">
                            <label class="col-sm-4 col-form-label fw-bold">Pendidikan</label>
                            <div class="col-sm-8">
                                <select class="form-control select2-saksi" id="saksiPendidikan" style="width: 100%;">
                                    <option value="">-- Pilih Pendidikan --</option>
                                    @foreach($educations as $e)
                                        <option value="{{ $e->pusiknas_id }}">{{ $e->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-2">
                            <label class="col-sm-4 col-form-label fw-bold">Pekerjaan</label>
                            <div class="col-sm-8">
                                <select class="form-control select2-saksi" id="saksiPekerjaan" style="width: 100%;">
                                    <option value="">-- Pilih Pekerjaan --</option>
                                    @foreach($jobs as $j)
                                        <option value="{{ $j->pusiknas_id }}">{{ $j->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-2">
                            <label class="col-sm-4 col-form-label fw-bold">Nama Ibu</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="saksiIbu" placeholder="Nama Ibu Kandung">
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Alamat -->
                    <div class="col-md-6">
                        <h6 class="fw-bold text-secondary mb-3 border-bottom pb-2">Alamat & Wilayah (Pusiknas)</h6>

                        <div class="form-group row mb-2">
                            <label class="col-sm-4 col-form-label fw-bold">Kewarganegaraan</label>
                            <div class="col-sm-8">
                                <select class="form-control select2-saksi" id="saksiNegara" style="width: 100%;">
                                    <option value="">-- Pilih Negara --</option>
                                    @foreach($countries as $c)
                                        <option value="{{ $c->id }}" data-code="{{ $c->alpha_3 ?? $c->code }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-2">
                            <label class="col-sm-4 col-form-label fw-bold">Provinsi</label>
                            <div class="col-sm-8">
                                <select class="form-control select2-saksi" id="saksiProvinsi" style="width: 100%;">
                                    <option value="">-- Pilih Provinsi --</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-2">
                            <label class="col-sm-4 col-form-label fw-bold">Kabupaten/Kota</label>
                            <div class="col-sm-8">
                                <select class="form-control select2-saksi" id="saksiKabupaten" style="width: 100%;" disabled>
                                    <option value="">-- Pilih Kabupaten/Kota --</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-2">
                            <label class="col-sm-4 col-form-label fw-bold">Kecamatan</label>
                            <div class="col-sm-8">
                                <select class="form-control select2-saksi" id="saksiKecamatan" style="width: 100%;" disabled>
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-2">
                            <label class="col-sm-4 col-form-label fw-bold">Kelurahan / Desa</label>
                            <div class="col-sm-8">
                                <select class="form-control select2-saksi" id="saksiKelurahan" style="width: 100%;" disabled>
                                    <option value="">-- Pilih Kelurahan --</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-2">
                            <label class="col-sm-4 col-form-label fw-bold">Alamat Lengkap</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" id="saksiAlamat" rows="3" placeholder="Alamat lengkap (Jalan/RT/RW)"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanSaksi">
                    <i class="bi bi-save me-1"></i> Simpan Saksi
                </button>
            </div>
            </div>
        </div>
    </div>
                        </div>
                    </div>

    {{-- Shared Modal for Barang Bukti Management --}}
    @include('produktivitas.surat-penyitaan.modal.modal', ['id' => $accidentId])
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    
    <script>
        $(document).ready(function() {
            var _token = $("input[name='_token']").val();
            var accident_id = "{{ $accidentId }}";

            // Prison Data for Dependent Dropdown
            var prisons = {!! json_encode($prisons) !!};
            var oldRutan = "{{ old('penahanan_rutan', $document->penahanan_rutan) }}";
            var oldCabang = "{{ old('penahanan_cabang', $document->penahanan_cabang) }}";

            function populateRutan() {
                var rutanSelect = $('#penahanan_rutan');
                var uniqueNames = [...new Set(prisons.map(p => p.name))].sort();
                
                rutanSelect.empty().append('<option value="">--Pilih Rutan--</option>');
                uniqueNames.forEach(function(name) {
                    var selected = (name === oldRutan) ? 'selected' : '';
                    rutanSelect.append('<option value="' + name + '" ' + selected + '>' + name + '</option>');
                });
                rutanSelect.trigger('change');
            }

            $('#penahanan_rutan').on('change', function() {
                var selectedRutan = $(this).val();
                var cabangSelect = $('#penahanan_cabang');
                cabangSelect.empty().append('<option value="">--Pilih Cabang--</option>');

                if (selectedRutan) {
                    var branches = prisons.filter(p => p.name === selectedRutan).map(p => p.branch).sort();
                    branches.forEach(function(branch) {
                        var selected = (branch === oldCabang) ? 'selected' : '';
                        cabangSelect.append('<option value="' + branch + '" ' + selected + '>' + branch + '</option>');
                    });
                }
                cabangSelect.trigger('change');
            });

            populateRutan();

            // Auto-fetch Laws when Sprindik is selected
            $('#surat_perintah_penyidikan_id').on('change', function() {
                var sprindikId = $(this).val();
                if (sprindikId) {
                    $.ajax({
                        url: "{{ route('doc.tahap-1-document.get-laws') }}",
                        type: 'GET',
                        data: { sprindik_id: sprindikId, accident_id: accident_id },
                        success: function(response) {
                            if (response.success) {
                                $('#pasal_disangkakan_display').html(response.pasal_string ? response.pasal_string.replace(/\n/g, '<br>') : 'Data pasal tidak ditemukan');
                                $('#pasal_disangkakan').val(response.pasal_string);
                            }
                        }
                    });
                }
            });

            // Investigator Auto-fill
            $('#investigator_selection').on('change', function() {
                var selected = $(this).find('option:selected');
                var rankName = selected.data('rank-name');
                var fullName = selected.data('full-name');
                var phone = selected.data('phone');

                if (fullName) {
                    $('#investigator_pangkat_nama').val(rankName + ' ' + fullName);
                    $('#investigator_hp').val(phone);
                } else {
                    $('#investigator_pangkat_nama').val('');
                    $('#investigator_hp').val('');
                }
            });

            // Detention Status Logic
            $('.penahanan-status-radio').change(function() {
                var status = $(this).val();
                if (status === 'TIDAK_DITAHAN') {
                    $('#detentionFieldsContainer').slideUp();
                    $('.required-penahanan').hide();
                    $('.required-ditangguhkan').hide();
                } else {
                    $('#detentionFieldsContainer').slideDown();
                    $('.required-penahanan').show();
                    if (status === 'DITANGGUHKAN') {
                        $('#suspensionFields').slideDown();
                        $('.required-ditangguhkan').show();
                    } else {
                        $('#suspensionFields').slideUp();
                        $('.required-ditangguhkan').hide();
                    }
                }
            });
            $('.penahanan-status-radio:checked').trigger('change');

            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // ===== AUTO-FILL dari dropdown Surat Perintah Penahanan =====
            $('#surat_perintah_penahanan_number').on('change', function() {
                var selected = $(this).find('option:selected');
                var date     = selected.data('date')       || '';
                var rutan    = selected.data('rutan')      || '';
                var cabang   = selected.data('cabang')     || '';
                var sDate    = selected.data('start-date') || '';
                var eDate    = selected.data('end-date')   || '';

                // Isi tanggal SP Penahanan
                $('#surat_perintah_penahanan_date').val(date);

                // Auto-fill tanggal mulai & selesai penahanan
                if (sDate) {
                    $('#penahanan_start_date').datepicker('update', sDate);
                } else if (!$(this).val()) {
                    $('#penahanan_start_date').val('');
                }
                if (eDate) {
                    $('#penahanan_end_date').datepicker('update', eDate);
                } else if (!$(this).val()) {
                    $('#penahanan_end_date').val('');
                }

                // Auto-fill Rutan & Cabang di form penahanan (jika ada data)
                if (rutan) {
                    var cabangSelect = $('#penahanan_cabang');
                    var branches = prisons.filter(p => p.name === rutan).map(p => p.branch).sort();
                    cabangSelect.empty().append('<option value="">--Pilih Cabang--</option>');
                    branches.forEach(function(branch) {
                        var sel = (branch === cabang) ? 'selected' : '';
                        cabangSelect.append('<option value="' + branch + '" ' + sel + '>' + branch + '</option>');
                    });
                    cabangSelect.trigger('change.select2');

                    // Set Rutan dropdown value
                    $('#penahanan_rutan').val(rutan).trigger('change.select2');
                } else if (!$(this).val()) {
                    $('#penahanan_rutan').val('').trigger('change.select2');
                    $('#penahanan_cabang').val('').trigger('change.select2');
                }
            });

            // ===== AUTO-FILL dari dropdown Surat Perpanjangan Penahanan =====
            $('#surat_perpanjangan_penahanan_number').on('change', function() {
                var selected = $(this).find('option:selected');
                var date     = selected.data('date') || '';
                $('#surat_perpanjangan_penahanan_date').val(date);
            });

            // ===== AUTO-FILL dari dropdown Surat Penangguhan Penahanan =====
            $('#surat_penangguhan_penahanan_number').on('change', function() {
                var selected = $(this).find('option:selected');
                var date     = selected.data('date') || '';
                $('#surat_penangguhan_penahanan_date').val(date);
            });
            // ===== END AUTO-FILL =====

            // Specific initialization for Barang Bukti (Tags support typing/tokenizing)
            $('#barang_bukti').select2({
                theme: 'bootstrap4',
                width: '100%',
                multiple: true,
                tags: true,
                tokenSeparators: [',']
            });

            // Update jumlah_bb based on selection
            $('#barang_bukti').on('change', function() {
                var count = $(this).val() ? $(this).val().length : 0;
                $('#jumlah_bb').val(count);
            });
            $('#barang_bukti').trigger('change');

            // ==========================================
        // SAKSI UI LOGIC (TABLE & MODAL)
        // ==========================================
        @php
            $witnesses = \App\Models\Witness::where('accident_id', $document->accident_id)->where('group', 'TAHAP_I')->get();
            $daftarSaksi = [];
            foreach ($witnesses as $w) {
                $daftarSaksi[] = [
                    'nama' => $w->name,
                    'tempat_lahir' => $w->birth_place,
                    'kode_jenis_kelamin' => (string)optional(\App\Models\Lib\Gender::find($w->gender_id))->pusiknas_id,
                    'alamat' => $w->address,
                    'kode_wilayah' => null,
                    'kode_pendidikan' => null,
                    'kode_pekerjaan' => null,
                    'nama_ibu' => null,
                    'kode_agama' => null,
                    'kode_status_perkawinan' => null,
                    'kode_warga_negara' => 'idn',
                    'kode_pekerjaan_text' => ''
                ];
            }
        @endphp
        var saksiList = {!! json_encode($daftarSaksi) !!};
        
        function renderSaksiTable() {
            var tbody = $('#saksiTable tbody');
            tbody.empty();
            
            if(saksiList.length === 0) {
                tbody.append('<tr id="emptySaksiRow"><td colspan="7" class="text-center text-muted small py-3">Belum ada data saksi.</td></tr>');
                return;
            }
            
            saksiList.forEach(function(s, index) {
                var genderText = s.kode_jenis_kelamin == '1' ? 'Laki-laki' : (s.kode_jenis_kelamin == '2' ? 'Perempuan' : '-');
                var tr = $('<tr>');
                tr.append('<td>' + (index + 1) + '</td>');
                tr.append('<td>' + (s.nama || '-') + '</td>');
                tr.append('<td>' + (s.tempat_lahir || '-') + '</td>');
                tr.append('<td>' + genderText + '</td>');
                tr.append('<td>' + (s.kode_pekerjaan_text || '-') + '</td>');
                tr.append('<td>' + (s.alamat || '-') + '</td>');
                
                var actionTd = $('<td class="text-center">');
                var deleteBtn = $('<button type="button" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>');
                deleteBtn.on('click', function() {
                    saksiList.splice(index, 1);
                    renderSaksiTable();
                    renderSaksiHiddenInputs();
                });
                actionTd.append(deleteBtn);
                tr.append(actionTd);
                
                tbody.append(tr);
            });
        }
        
        function renderSaksiHiddenInputs() {
            $('.saksi-hidden-inputs').remove();
            
            saksiList.forEach(function(s, index) {
                var container = $('<div class="saksi-hidden-inputs"></div>');
                container.append('<input type="hidden" name="daftar_saksi['+index+'][nama]" value="'+(s.nama || '')+'">');
                container.append('<input type="hidden" name="daftar_saksi['+index+'][tempat_lahir]" value="'+(s.tempat_lahir || '')+'">');
                container.append('<input type="hidden" name="daftar_saksi['+index+'][kode_jenis_kelamin]" value="'+(s.kode_jenis_kelamin || '')+'">');
                container.append('<input type="hidden" name="daftar_saksi['+index+'][alamat]" value="'+(s.alamat || '')+'">');
                container.append('<input type="hidden" name="daftar_saksi['+index+'][kode_wilayah]" value="'+(s.kode_wilayah || '')+'">');
                container.append('<input type="hidden" name="daftar_saksi['+index+'][kode_pendidikan]" value="'+(s.kode_pendidikan || '')+'">');
                container.append('<input type="hidden" name="daftar_saksi['+index+'][kode_pekerjaan]" value="'+(s.kode_pekerjaan || '')+'">');
                container.append('<input type="hidden" name="daftar_saksi['+index+'][nama_ibu]" value="'+(s.nama_ibu || '')+'">');
                container.append('<input type="hidden" name="daftar_saksi['+index+'][kode_agama]" value="'+(s.kode_agama || '')+'">');
                container.append('<input type="hidden" name="daftar_saksi['+index+'][kode_status_perkawinan]" value="'+(s.kode_status_perkawinan || '')+'">');
                container.append('<input type="hidden" name="daftar_saksi['+index+'][kode_warga_negara]" value="'+(s.kode_warga_negara || 'idn')+'">');
                $('#tahap1Form').append(container);
            });
        }
        
        // Initial render
        renderSaksiTable();
        renderSaksiHiddenInputs();
        
        // Modal Select2 init
        $('.select2-saksi').select2({
            dropdownParent: $('#saksiModal'),
            theme: 'bootstrap4',
            width: '100%'
        });
        
        $('#saksiModal').on('shown.bs.modal', function () {
            // Trigger load province if country is preselected and no province yet
            var currentNegara = $('#saksiNegara').val();
            if(currentNegara && $('#saksiProvinsi').find('option').length <= 1) {
                getLocationSaksi(currentNegara, 'PROVINCE', '#saksiProvinsi');
            }
        });
        
        // Save Saksi
        $('#btnSimpanSaksi').on('click', function() {
            var wilayahKelurahan = $('#saksiKelurahan').find(':selected').data('code') || '';
            var wilayahNegara = $('#saksiNegara').find(':selected').data('code') || 'idn';
            var pekerjaanText = $('#saksiPekerjaan').find(':selected').text() || '';
            
            var newSaksi = {
                nama: $('#saksiNama').val(),
                tempat_lahir: $('#saksiTempatLahir').val(),
                kode_jenis_kelamin: $('#saksiGender').val(),
                kode_agama: $('#saksiAgama').val(),
                kode_status_perkawinan: $('#saksiKawin').val(),
                kode_pendidikan: $('#saksiPendidikan').val(),
                kode_pekerjaan: $('#saksiPekerjaan').val(),
                kode_pekerjaan_text: pekerjaanText,
                nama_ibu: $('#saksiIbu').val(),
                kode_warga_negara: wilayahNegara,
                kode_wilayah: wilayahKelurahan,
                alamat: $('#saksiAlamat').val()
            };
            
            if(!newSaksi.nama) {
                alert('Nama saksi wajib diisi!');
                return;
            }
            
            saksiList.push(newSaksi);
            renderSaksiTable();
            renderSaksiHiddenInputs();
            
            // reset form
            $('#saksiNama, #saksiTempatLahir, #saksiIbu, #saksiAlamat').val('');
            $('.select2-saksi').not('#saksiNegara').val('').trigger('change');
            
            $('#saksiModal').modal('hide');
        });
        
        // Cascading Locations for Saksi Modal
        $(document).on('change', '#saksiNegara', function() {
            var parentId = $(this).val();
            getLocationSaksi(parentId, 'PROVINCE', '#saksiProvinsi');
        });
        $(document).on('change', '#saksiProvinsi', function() {
            var parentId = $(this).val();
            getLocationSaksi(parentId, 'REGENCY', '#saksiKabupaten');
        });
        $(document).on('change', '#saksiKabupaten', function() {
            var parentId = $(this).val();
            getLocationSaksi(parentId, 'DISTRICT', '#saksiKecamatan');
        });
        $(document).on('change', '#saksiKecamatan', function() {
            var parentId = $(this).val();
            getLocationSaksi(parentId, 'VILLAGE', '#saksiKelurahan');
        });
        
                function getLocationSaksi(parentId, classCode, targetSelect) {
            if(!parentId) {
                $(targetSelect).empty().append('<option value="">--Pilih--</option>');
                $(targetSelect).prop('disabled', true);
                $(targetSelect).trigger('change');
                return;
            }
            
            $.ajax({
                url: "{{ route('doc.tahap-1-document.api.locations', ['accident_id' => $accidentId]) }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    'parent_id': parentId,
                    'class': classCode,
                },
                success: function(response) {
                    var data = response.data;
                    var select = $(targetSelect);
                    select.empty().append($('<option>', {
                        value: '',
                        text: '--Pilih--'
                    }));
                    $.each(data, function(index, d) {
                        select.append($('<option>', {
                            value: d.id,
                            text: d.name,
                            'data-code': d.code
                        }));
                    });
                    select.prop('disabled', false);
                    select.trigger('change');
                }
            });
        }

            // Dynamic Rows for Barang Bukti
            let bbIndex = {{ count(old('daftar_barang_bukti', is_array($document->barang_bukti) && isset($document->barang_bukti[0]['nama']) ? $document->barang_bukti : [])) > 0 ? count(old('daftar_barang_bukti', is_array($document->barang_bukti) && isset($document->barang_bukti[0]['nama']) ? $document->barang_bukti : [])) : 1 }};
            $('#addBbBtn').click(function() {
                const row = `
                    <tr>
                        <td><input type="text" name="daftar_barang_bukti[${bbIndex}][nama]" class="form-control form-control-sm" required placeholder="Contoh: Pisau"></td>
                        <td><input type="number" name="daftar_barang_bukti[${bbIndex}][jumlah]" class="form-control form-control-sm" value="1" min="1" required></td>
                        <td><input type="text" name="daftar_barang_bukti[${bbIndex}][satuan]" class="form-control form-control-sm" value="buah" required></td>
                        <td><input type="text" name="daftar_barang_bukti[${bbIndex}][keterangan]" class="form-control form-control-sm" placeholder="Contoh: Panjang 30cm"></td>
                        <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="bi bi-trash"></i></button></td>
                    </tr>
                `;
                $('#bbTable tbody').append(row);
                bbIndex++;
            });

            // Remove Row (Shared for both tables)
            $(document).on('click', '.remove-row', function() {
                const tbody = $(this).closest('tbody');
                if (tbody.find('tr').length > 1) {
                    $(this).closest('tr').remove();
                } else {
                    alert('Minimal harus ada 1 baris.');
                }
            });

            // Initialize Datepicker
            $('[data-provide="datepicker"]').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });

            // Add Tembusan Row
            $('.addTembusan').click(function() {
                $('#tembusanContainer').append(`
                    <div class="input-group mb-2">
                        <input type="text" name="tembusan[]" class="form-control" value="">
                        <div class="input-group-append">
                            <button class="btn btn-outline-danger removeTembusan" type="button">Hapus</button>
                        </div>
                    </div>`);
            });

            // Remove Tembusan Row
            $(document).on('click', '.removeTembusan', function() {
                $(this).closest('.input-group').remove();
            });

            // Helper check field has value
            function hasFieldValue($field) {
                var raw = $field.val();
                if (raw === null || raw === undefined) return false;
                if (Array.isArray(raw)) return raw.length > 0;
                var str = String(raw).trim();
                return str !== '' && str !== '0';
            }

            // Helper clear single field error
            function clearFieldError($field) {
                $field.removeClass('is-invalid border border-danger');
                if ($field.next('.select2-container').length) {
                    $field.next('.select2-container').find('.select2-selection').removeClass('border border-danger is-invalid');
                    $field.next('.select2-container').next('.frontend-error, .invalid-feedback').remove();
                }
                $field.next('.frontend-error, .invalid-feedback').remove();
                $field.siblings('.frontend-error, .invalid-feedback').remove();
                $field.closest('.input-group, .form-group, .mb-3, div').find('.frontend-error, .invalid-feedback').remove();
            }

            // Auto-clear realtime saat user mengetik atau mengubah nilai field
            $(document).on('input change changeDate dp.change keyup blur', 'input, textarea, select', function() {
                var $field = $(this);
                if (hasFieldValue($field)) {
                    clearFieldError($field);
                }
            });

            $(document).on('select2:select select2:unselect change', 'select', function() {
                var $field = $(this);
                if (hasFieldValue($field)) {
                    clearFieldError($field);
                }
            });

            // Continuous watcher
            setInterval(function() {
                $('input.is-invalid, textarea.is-invalid, select.is-invalid').each(function() {
                    var $field = $(this);
                    if (hasFieldValue($field)) {
                        clearFieldError($field);
                    }
                });
            }, 200);

            // Helper scrollToFirstError
            function scrollToFirstError() {
                var $firstError = $('.is-invalid:visible, .border-danger:visible, .frontend-error:visible').first();
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

            // Validasi Submit Form
            $('#tahap1FormSubmit').on('click', function(e) {
                e.preventDefault();

                // Bersihkan error sebelumnya
                $('.is-invalid').removeClass('is-invalid');
                $('.border.border-danger').removeClass('border border-danger');
                $('.select2-selection').removeClass('border border-danger is-invalid');
                $('.frontend-error').remove();
                $('.invalid-feedback').remove();

                let errors = [];

                function markError(fieldSelector, message) {
                    var $field = typeof fieldSelector === 'string' ? $(fieldSelector) : fieldSelector;
                    if (!$field || !$field.length) return;

                    $field.addClass('is-invalid');
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

                // Validasi field wajib di sisi frontend
                checkInput('#document_number', 'Nomor Surat Pengantar');
                checkInput('#document_date', 'Tanggal Surat Pengantar');
                checkSelect('#klasifikasi', 'Klasifikasi Dokumen');
                checkSelect('#surat_pemberitahuan_dimulainya_penyidikan_id', 'Nomor SPDP Terkait');
                checkInput('#berkas_perkara_number', 'Nomor Berkas Perkara');
                checkInput('#berkas_perkara_date', 'Tanggal Berkas Perkara');
                checkInput('#berkas_perkara_rangkap', 'Jumlah Rangkap Berkas Perkara');
                checkSelect('#signatory', 'Pejabat Penandatangan');
                checkSelect('#suspects', 'Tersangka');
                checkSelect('#penahanan_status', 'Status Penahanan');

                var penahananStatus = $('#penahanan_status').val();
                if (penahananStatus === 'DITAHAN' || penahananStatus === 'DITANGGUHKAN') {
                    checkSelect('#penahanan_rutan', 'Nama Rutan');
                    checkInput('#penahanan_start_date', 'Tanggal Mulai Penahanan');
                    checkInput('#penahanan_end_date', 'Tanggal Selesai Penahanan');
                    checkInput('#surat_perintah_penahanan_number', 'Nomor Surat Perintah Penahanan');
                    checkInput('#surat_perintah_penahanan_date', 'Tanggal Surat Perintah Penahanan');
                }
                if (penahananStatus === 'DITANGGUHKAN') {
                    checkInput('#surat_perpanjangan_penahanan_number', 'Nomor Surat Perpanjangan Penahanan');
                    checkInput('#surat_perpanjangan_penahanan_date', 'Tanggal Surat Perpanjangan Penahanan');
                    checkInput('#surat_perpanjangan_penahanan_court_number', 'Nomor Surat Perpanjangan Penahanan ke Pengadilan');
                    checkInput('#surat_perpanjangan_penahanan_court_date', 'Tanggal Surat Perpanjangan Penahanan ke Pengadilan');
                    checkInput('#surat_penangguhan_penahanan_number', 'Nomor Surat Penangguhan Penahanan');
                    checkInput('#surat_penangguhan_penahanan_date', 'Tanggal Surat Penangguhan Penahanan');
                }

                // Jika ada error di frontend, scroll ke field pertama
                if (errors.length > 0) {
                    scrollToFirstError();
                    return false;
                }

                // Validasi sisi server via Ajax
                $.ajax({
                    url: "{{ route('doc.tahap-1-document.api.validate-request-form', ['accident_id' => $accidentId, 'id' => $document->id]) }}",
                    type: 'POST',
                    dataType: 'json',
                    data: $('#tahap1Form').serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: response.message || 'Silahkan menunggu proses update data',
                                icon: 'success',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                $('#tahap1Form')[0].submit();
                            });
                        }
                    },
                    error: function(xhr) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.code == '422' && response.errors) {
                                if (typeof response.errors === 'object' && !Array.isArray(response.errors)) {
                                    $.each(response.errors, function(key, messages) {
                                        var msg = Array.isArray(messages) ? messages[0] : messages;
                                        var $target = $('#' + key + ', [name="' + key + '"], [name="' + key + '[]"]');
                                        if ($target.length) {
                                            markError($target, msg);
                                        } else {
                                            markError('#' + key, msg);
                                        }
                                    });
                                    scrollToFirstError();
                                } else {
                                    var errorMessages = '';
                                    $.each(response.errors, function(key, value) { errorMessages += '- ' + value + '<br>'; });
                                    Swal.fire({ icon: 'error', title: 'Periksa Isian', html: errorMessages });
                                }
                            } else {
                                var message = response.message || response.errors || 'Terjadi kesalahan saat memproses data.';
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Perhatian',
                                    text: typeof message === 'string' ? message : JSON.stringify(message)
                                });
                            }
                        } catch(e) {
                            console.error(e);
                        }
                    }
                });
            });
        });
    

    // Auto-suggest Kode Wilayah from Lokasi Kejadian
    $(document).ready(function() {
        $('#lokasiKejadian').on('change blur', function() {
            const lokasi = $(this).val();
            if (!lokasi) return;

            const match = lokasi.match(/(?:KECAMATAN|KEC)\.?\s*([A-Za-z\s]+?)\s*(?:KABUPATEN|KAB\.|KAB|KOTA|,|$)/i);
            if (match && match[1]) {
                const extracted = match[1].trim().toLowerCase();

                let found = false;
                $('#kodeWilayah option').each(function() {
                    const text = $(this).text().toLowerCase();
                    const normalizedText = text.replace(/[^a-z0-9]/g, '');
                    const normalizedExtracted = extracted.replace(/[^a-z0-9]/g, '');
                    
                    if (normalizedText.includes(normalizedExtracted)) {
                        $('#kodeWilayah').val($(this).val()).trigger('change');
                        found = true;
                        return false;
                    }
                });
            }
        });
    });
</script>
@endpush
