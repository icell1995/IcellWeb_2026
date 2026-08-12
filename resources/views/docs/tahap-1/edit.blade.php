@php
    $_title = 'Edit Surat Pengiriman Berkas Perkara (Tahap I)';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"><i class="bi bi-arrow-left"></i>
        Kembali ke Progress Perkara</a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Edit Surat Pengiriman Berkas Perkara (Tahap I)</h5>

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
                method="POST" enctype="multipart/form-data" id="tahap1Form">
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
                    <label class="fw-bold col-sm-3 col-form-label" for="surat_pemberitahuan_dimulainya_penyidikan_id">Nomor SPDP</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="surat_pemberitahuan_dimulainya_penyidikan_id" id="surat_pemberitahuan_dimulainya_penyidikan_id">
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
                    {{-- Nama Rutan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="penahanan_rutan">Nama Rutan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select id="penahanan_rutan" name="penahanan_rutan" class="form-control select2">
                            <option value="">--Pilih Rutan--</option>
                        </select>
                    </div>
                </div>

                {{-- Cabang Rutan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="penahanan_cabang">Cabang Rutan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select id="penahanan_cabang" name="penahanan_cabang" class="form-control select2">
                            <option value="">--Pilih Cabang--</option>
                        </select>
                    </div>
                </div>

                {{-- Tgl Mulai Penahanan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="penahanan_start_date">Tgl Mulai Penahanan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input class="form-control" id="penahanan_start_date" name="penahanan_start_date" placeholder="YYYY-MM-DD" value="{{ old('penahanan_start_date', $document->penahanan_start_date ? $document->penahanan_start_date->format('Y-m-d') : '') }}" data-provide="datepicker">
                    </div>
                </div>

                {{-- Tgl Selesai Penahanan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="penahanan_end_date">Tgl Selesai Penahanan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input class="form-control" id="penahanan_end_date" name="penahanan_end_date" placeholder="YYYY-MM-DD" value="{{ old('penahanan_end_date', $document->penahanan_end_date ? $document->penahanan_end_date->format('Y-m-d') : '') }}" data-provide="datepicker">
                    </div>
                </div>

                {{-- No Surat Penahanan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="surat_perintah_penahanan_number">No. Surat Penahanan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="surat_perintah_penahanan_number" type="text" class="form-control" name="surat_perintah_penahanan_number" value="{{ old('surat_perintah_penahanan_number', $document->surat_perintah_penahanan_number) }}">
                    </div>
                </div>

                {{-- Tgl Surat Penahanan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="surat_perintah_penahanan_date">Tgl Surat Penahanan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input class="form-control" id="surat_perintah_penahanan_date" name="surat_perintah_penahanan_date" placeholder="YYYY-MM-DD" value="{{ old('surat_perintah_penahanan_date', $document->surat_perintah_penahanan_date ? $document->surat_perintah_penahanan_date->format('Y-m-d') : '') }}" data-provide="datepicker">
                    </div>
                </div>

                <hr class="border-secondary border-dashed">

                {{-- Perpanjangan Penahanan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="surat_perpanjangan_penahanan_number">No. Surat Perpanjangan Penahanan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="surat_perpanjangan_penahanan_number" type="text" class="form-control" name="surat_perpanjangan_penahanan_number" value="{{ old('surat_perpanjangan_penahanan_number', $document->surat_perpanjangan_penahanan_number) }}">
                    </div>
                </div>
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="surat_perpanjangan_penahanan_date">Tgl Surat Perpanjangan Penahanan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input class="form-control" id="surat_perpanjangan_penahanan_date" name="surat_perpanjangan_penahanan_date" placeholder="YYYY-MM-DD" value="{{ old('surat_perpanjangan_penahanan_date', $document->surat_perpanjangan_penahanan_date ? $document->surat_perpanjangan_penahanan_date->format('Y-m-d') : '') }}" data-provide="datepicker">
                    </div>
                </div>

                {{-- Perpanjangan Penahanan ke Pengadilan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="surat_perpanjangan_penahanan_court_number">No. Surat Perpanjangan Penahanan ke Pengadilan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="surat_perpanjangan_penahanan_court_number" type="text" class="form-control" name="surat_perpanjangan_penahanan_court_number" value="{{ old('surat_perpanjangan_penahanan_court_number', $document->surat_perpanjangan_penahanan_court_number) }}">
                    </div>
                </div>
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="surat_perpanjangan_penahanan_court_date">Tgl Surat Perpanjangan Penahanan ke Pengadilan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input class="form-control" id="surat_perpanjangan_penahanan_court_date" name="surat_perpanjangan_penahanan_court_date" placeholder="YYYY-MM-DD" value="{{ old('surat_perpanjangan_penahanan_court_date', $document->surat_perpanjangan_penahanan_court_date ? $document->surat_perpanjangan_penahanan_court_date->format('Y-m-d') : '') }}" data-provide="datepicker">
                    </div>
                </div>

                {{-- Suspension Fields (Only for DITANGGUHKAN) --}}
                <div id="suspensionFields" style="{{ old('penahanan_status', $document->penahanan_status) == 'DITANGGUHKAN' ? '' : 'display:none;' }}">
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-3 col-form-label" for="surat_penangguhan_penahanan_number">No. Surat Penangguhan Penahanan</label>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                            <input id="surat_penangguhan_penahanan_number" type="text" class="form-control" name="surat_penangguhan_penahanan_number" value="{{ old('surat_penangguhan_penahanan_number', $document->surat_penangguhan_penahanan_number) }}">
                        </div>
                    </div>
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-3 col-form-label" for="surat_penangguhan_penahanan_date">Tgl Surat Penangguhan Penahanan</label>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                            <input class="form-control" id="surat_penangguhan_penahanan_date" name="surat_penangguhan_penahanan_date" placeholder="YYYY-MM-DD" value="{{ old('surat_penangguhan_penahanan_date', $document->surat_penangguhan_penahanan_date ? $document->surat_penangguhan_penahanan_date->format('Y-m-d') : '') }}" data-provide="datepicker">
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

                {{-- Barang Bukti (UNIFORM WITH SP3) --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="barang_bukti">Barang Bukti</label>
                    <div class="col-lg-7 col-md-7 col-sm-10 col-10">
                        <select class="form-control select2 @error('barang_bukti') is-invalid @enderror" 
                                id="barang_bukti" name="barang_bukti[]" multi-select multiple="multiple" 
                                data-placeholder="Pilih Barang Bukti dari daftar pool...">
                            {{-- Pool items --}}
                            @foreach($daftarBarangBukti as $bb)
                                @php
                                    $val = $bb->nama_barang . ' (' . $bb->jumlah_barang . ')';
                                    $selectedValues = old('barang_bukti', $document->barang_bukti);
                                    if (!is_array($selectedValues)) $selectedValues = [];
                                    $isSelected = in_array($val, $selectedValues);
                                @endphp
                                <option value="{{ $val }}" {{ $isSelected ? 'selected' : '' }}>{{ $val }}</option>
                            @endforeach
                            
                            {{-- Hand-typed items NOT in pool --}}
                            @if(is_array($document->barang_bukti))
                                @foreach($document->barang_bukti as $bbText)
                                    @php
                                        $inPool = $daftarBarangBukti->contains(fn($p) => ($p->nama_barang . ' (' . $p->jumlah_barang . ')') == $bbText);
                                    @endphp
                                    @if(!$inPool)
                                        <option value="{{ $bbText }}" selected>{{ $bbText }}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <small class="text-muted">Pilih barang bukti yang sudah ada atau tambah baru lewat tombol di samping</small>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-2 py-1">
                        <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="$('#myModalPenyitaan3').modal('show')">
                            <i class="bi bi-plus-circle me-1"></i> Kelola
                        </button>
                    </div>
                </div>

                {{-- Jumlah BB (Calculated Automatically) --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="jumlah_bb">Jumlah Barang Bukti</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="jumlah_bb" type="number" min="0" step="1"
                            class="form-control @error('jumlah_bb') is-invalid @enderror"
                            name="jumlah_bb" value="{{ old('jumlah_bb', $document->jumlah_bb ?? 0) }}"
                            placeholder="0" readonly>
                        <small class="text-muted">Dihitung otomatis dari jumlah pilihan di atas</small>
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
                                    {{ $officer->register_number . ' - ' . ($officer->rank->name ?? '') . ' ' . $officer->full_name }}
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
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Dokumen
                    </button>
                </div>
            </form>
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
                                $('#pasal_disangkakan_display').text(response.pasal_string || 'Data pasal tidak ditemukan');
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
                } else {
                    $('#detentionFieldsContainer').slideDown();
                    if (status === 'DITANGGUHKAN') {
                        $('#suspensionFields').slideDown();
                    } else {
                        $('#suspensionFields').slideUp();
                    }
                }
            });

            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

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

            // Datatable for Barang Bukti Modal
            var table_bb = $('.barang-bukti-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('get_barang_bukti') }}",
                    type: 'GET',
                    data: {
                        _token: _token,
                        accident_id: accident_id
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nama_barang', name: 'nama_barang' },
                    { data: 'jumlah_barang', name: 'jumlah_barang' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' }
                ],
                order: [[1, 'asc']]
            });

            // Handle Save Barang Bukti via AJAX in Modal
            $(".btn-barang-bukti").click(function(e) {
                e.preventDefault();
                var barang_bukti_id = $("#barang_bukti_id").val();
                var nama_barang = $("#nama_barang").val();
                var jumlah_barang = $("#jumlah_barang").val();

                if (!nama_barang || !jumlah_barang) {
                    alert('Mohon isi nama dan jumlah barang bukti.');
                    return;
                }

                $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>');

                $.ajax({
                    url: "{{ route('add_barang_bukti') }}",
                    type: 'POST',
                    data: {
                        _token: _token,
                        barang_bukti_id: barang_bukti_id,
                        accident_id_barang_bukti: accident_id,
                        nama_barang: nama_barang,
                        jumlah_barang: jumlah_barang
                    },
                    success: function(data) {
                        $('#barang-bukti-form')[0].reset();
                        $('#barang_bukti_id').val('');
                        $(".btn-barang-bukti").prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan');
                        table_bb.draw();

                        // Add to Select2 pool directly
                        var newOptionText = nama_barang + ' (' + jumlah_barang + ')';
                        if ($('#barang_bukti').find("option[value='" + newOptionText + "']").length == 0) {
                            var newOption = new Option(newOptionText, newOptionText, true, true);
                            $('#barang_bukti').append(newOption).trigger('change');
                        }
                    },
                    error: function(xhr) {
                        $(".btn-barang-bukti").prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan');
                        alert('Terjadi kesalahan saat menyimpan data.');
                    }
                });
            });

            // Edit BB in Modal
            $(document).on('click', '.editBarangBukti', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var qty = $(this).data('qty');
                $('#barang_bukti_id').val(id);
                $('#nama_barang').val(name);
                $('#jumlah_barang').val(qty);
                $('#nama_barang').focus();
            });

            // Delete BB in Modal
            $(document).on('click', '.deleteBarang', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var qty = $(this).data('qty');
                var optionText = name + ' (' + qty + ')';
                
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: 'Apakah Anda yakin ingin menghapus barang bukti "' + name + '"?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('delete_barang_bukti') }}",
                            type: 'POST',
                            data: { _token: _token, id: id },
                            success: function(response) {
                                if(response.success) {
                                    table_bb.draw();
                                    $('#barang_bukti option').each(function() {
                                        if ($(this).val() == optionText) $(this).remove();
                                    });
                                    $('#barang_bukti').trigger('change');
                                    Swal.fire('Terhapus!', response.message, 'success');
                                } else {
                                    Swal.fire('Gagal!', response.message, 'error');
                                }
                            }
                        });
                    }
                });
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

            // Form AJAX Submission
            $('#tahap1Form').on('submit', function(e) {
                e.preventDefault();
                Swal.fire({ title: 'Menyimpan Data...', text: 'Mohon tunggu sebentar', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                var formData = new FormData(this);
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message, showConfirmButton: false, timer: 2000 }).then(() => { window.location.href = response.redirect; });
                    },
                    error: function(xhr) {
                        Swal.close();
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var errorMsg = '<ul>' + Object.values(errors).map(e => `<li>${e[0]}</li>`).join('') + '</ul>';
                            Swal.fire({ icon: 'error', title: 'Validasi Gagal', html: errorMsg });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal!', text: xhr.responseJSON.message || 'Terjadi kesalahan pada server' });
                        }
                    }
                });
            });
        });
    </script>
@endpush
