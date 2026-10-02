@php
    $_title = 'Tahap I';
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
            <h5 class="fw-bold text-blue-dark">
                Tambah Surat Pengiriman Berkas Perkara (Tahap I)
                <span class="badge bg-info text-white ms-2">SPPT-TI / Pusiknas Bareskrim</span>
            </h5>

            <div class="alert alert-danger" id="attentionBox">
                <div class="text-center">
                    <b>
                        PERHATIAN !<br /><br />
                        DATA INI WAJIB DIISI DENGAN DETAIL DAN LENGKAP KARENA AKAN DIPERTUKARKAN DENGAN
                        PUSIKNAS BARESKRIM POLRI DALAM KERANGKA SPPT-TI (SISTEM PERADILAN PIDANA BERBASIS
                        TEKNOLOGI INFORMASI). KODE PROSES: <strong>BPT-1</strong>
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
            <form action="{{ route('doc.tahap-1-document.store', ['accident_id' => $accidentId]) }}"
                method="POST" enctype="multipart/form-data" id="tahap1Form">
                @csrf
                <input type="hidden" name="accident_id" value="{{ $accidentId }}">
                {{-- Nomor LP --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="no_lp">Nomor LP</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="no_lp" type="text" class="form-control bg-light" value="{{ $accident->no_lp }}" readonly>
                    </div>
                </div>

                {{-- Nomor Dokumen --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="document_number">Nomor Dokumen<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="document_number" type="text"
                            class="form-control @error('document_number') is-invalid @enderror"
                            name="document_number" value="{{ old('document_number') }}" required
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
                            placeholder="YYYY-MM-DD" autocomplete="off" value="{{ old('document_date') }}"
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
                            <option value="Biasa" {{ old('klasifikasi') == 'Biasa' ? 'selected' : '' }}>Biasa</option>
                            <option value="Rahasia" {{ old('klasifikasi') == 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
                            <option value="Sangat Rahasia" {{ old('klasifikasi') == 'Sangat Rahasia' ? 'selected' : '' }}>Sangat Rahasia</option>
                        </select>
                    </div>
                </div>

                {{-- Lampiran --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="lampiran">Lampiran</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="lampiran" type="text" class="form-control" name="lampiran" value="{{ old('lampiran') }}" placeholder="Contoh: 1 (satu) berkas">
                    </div>
                </div>
                <hr>
                {{-- Surat Perintah Penyidikan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="surat_perintah_penyidikan_id">Nomor Surat Perintah Penyidikan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="surat_perintah_penyidikan_id" id="surat_perintah_penyidikan_id" required>
                            <option value="">--Pilih No Surat Perintah Penyidikan--</option>
                            @foreach ($suratPerintahPenyidikanDocuments as $sp)
                                <option value="{{ $sp->id }}" {{ old('surat_perintah_penyidikan_id') == $sp->id ? 'selected' : '' }}>
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
                                <option value="{{ $spdp->id }}" {{ old('surat_pemberitahuan_dimulainya_penyidikan_id') == $spdp->id ? 'selected' : '' }}>
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
                                <option value="{{ $st->id }}" {{ old('surat_ketetapan_penetapan_tersangka_id') == $st->id ? 'selected' : '' }}>
                                    {{ $st->document_number }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                {{-- Berkas Perkara Number --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="berkas_perkara_number">Nomor Berkas Perkara<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="berkas_perkara_number" type="text" class="form-control" name="berkas_perkara_number" value="{{ old('berkas_perkara_number') }}" required placeholder="BP/01/I/2026/Satker">
                    </div>
                </div>

                {{-- Berkas Perkara Date --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="berkas_perkara_date">Tanggal Dokumen Ditandatangani<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input class="form-control" id="berkas_perkara_date" name="berkas_perkara_date" placeholder="YYYY-MM-DD" value="{{ old('berkas_perkara_date') }}" data-provide="datepicker" required>
                    </div>
                </div>

                {{-- Jumlah Rangkap --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="berkas_perkara_rangkap">Jumlah Rangkap<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="berkas_perkara_rangkap" type="number" class="form-control" name="berkas_perkara_rangkap" value="{{ old('berkas_perkara_rangkap', 2) }}" min="1" required>
                    </div>
                </div>
                <hr>
                
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

                                {{-- Tanggal Kejadian --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label">Tanggal Kejadian <span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <div class="row">
                            <div class="col-sm-4">
                                <input type="number" class="form-control" name="tahunKejadian" placeholder="Tahun" value="{{ old('tahunKejadian', $document->messages['tahun_kejadian'] ?? ($accident->accident_date ? date('Y', strtotime($accident->accident_date)) : '')) }}" required>
                            </div>
                            <div class="col-sm-4">
                                <select class="form-control" name="bulanKejadian" required>
                                    <option value="">-- Bulan --</option>
                                    @for($i=1; $i<=12; $i++)
                                        <option value="{{$i}}" {{ old('bulanKejadian', $document->messages['bulan_kejadian'] ?? ($accident->accident_date ? date('n', strtotime($accident->accident_date)) : '')) == $i ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" name="tanggalKejadian" placeholder="Tanggal" min="1" max="31" value="{{ old('tanggalKejadian', $document->messages['tanggal_kejadian'] ?? ($accident->accident_date ? date('j', strtotime($accident->accident_date)) : '')) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Waktu Kejadian --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="waktuKejadian">Waktu Kejadian <span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="waktuKejadian" type="text" class="form-control" name="waktuKejadian" value="{{ old('waktuKejadian', $document->messages['waktu_kejadian'] ?? ('Sekitar pukul ' . ($accident->accident_time ? \Carbon\Carbon::parse($accident->accident_time)->format('H:i') : '-') . ' WIB')) }}" placeholder="Sekitar pukul ..." required>
                    </div>
                </div>

                {{-- Daftar Tersangka --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="suspects">Daftar Tersangka<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2 @error('suspects') is-invalid @enderror" 
                                name="suspects[]" id="suspects" multiple="multiple" required>
                            @foreach ($suspects as $suspect)
                                <option value="{{ $suspect->id }}" {{ (is_array(old('suspects')) && in_array($suspect->id, old('suspects'))) ? 'selected' : '' }}>
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
                        <div class="p-2 bg-light border rounded text-muted" id="pasal_disangkakan_display" style="min-height: 80px;">{{ old('pasal_disangkakan', 'Akan terisi otomatis berdasarkan Sprindik terpilih') }}</div>
                        <input type="hidden" id="pasal_disangkakan" name="pasal_disangkakan" value="{{ old('pasal_disangkakan') }}">
                    </div>
                </div>

                <hr>

                {{-- Status Penahanan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label">Status Penahanan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12 d-flex align-items-center">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input penahanan-status-radio" type="radio" name="penahanan_status" id="status_ditahan" value="DITAHAN" {{ old('penahanan_status', 'DITAHAN') == 'DITAHAN' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="status_ditahan">Ditahan</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input penahanan-status-radio" type="radio" name="penahanan_status" id="status_ditangguhkan" value="DITANGGUHKAN" {{ old('penahanan_status') == 'DITANGGUHKAN' ? 'checked' : '' }}>
                            <label class="form-check-label" for="status_ditangguhkan">Ditangguhkan</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input penahanan-status-radio" type="radio" name="penahanan_status" id="status_tidak_ditahan" value="TIDAK_DITAHAN" {{ old('penahanan_status') == 'TIDAK_DITAHAN' ? 'checked' : '' }}>
                            <label class="form-check-label" for="status_tidak_ditahan">Tidak Ditahan</label>
                        </div>
                    </div>
                </div>

                <div id="detentionFieldsContainer" style="{{ old('penahanan_status', 'DITAHAN') == 'TIDAK_DITAHAN' ? 'display:none;' : '' }}">
                                    <div class="input-group row mb-3 ms-0">
                    <div class="col-sm-3"></div>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12 row pe-0">
                        <div class="col-md-6 mb-2">
                            <label class="fw-bold d-block mb-1" for="penahanan_rutan">Nama Rutan</label>
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
                            <label class="fw-bold d-block mb-1" for="penahanan_start_date">Tgl Mulai Penahanan</label>
                            <input class="form-control" id="penahanan_start_date" name="penahanan_start_date" placeholder="YYYY-MM-DD" value="{{ old('penahanan_start_date') }}" data-provide="datepicker">
                        </div>
                        <div class="col-md-6 mb-2 pe-0">
                            <label class="fw-bold d-block mb-1" for="penahanan_end_date">Tgl Selesai Penahanan</label>
                            <input class="form-control" id="penahanan_end_date" name="penahanan_end_date" placeholder="YYYY-MM-DD" value="{{ old('penahanan_end_date') }}" data-provide="datepicker">
                        </div>
                    </div>
                </div>
                <div class="input-group row mb-3 ms-0">
                    <div class="col-sm-3"></div>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12 row pe-0">
                        <div class="col-md-6 mb-2">
                            <label class="fw-bold d-block mb-1" for="surat_perintah_penahanan_ref">No. Surat Penahanan</label>
                            <select id="surat_perintah_penahanan_ref" class="form-control select2 mb-2"
                            data-placeholder="-- Pilih Surat Perintah Penahanan --">
                            <option value="">-- Pilih Surat Perintah Penahanan --</option>
                            @foreach($suratPerintahPenahananDocuments as $spp)
                                @php
                                    $sppSuspects = $spp->suspect->pluck('name')->implode(', ');
                                @endphp
                                <option value="{{ $spp->id }}"
                                    data-number="{{ $spp->document_number }}"
                                    data-date="{{ $spp->document_date ? $spp->document_date->format('Y-m-d') : '' }}"
                                    data-rutan="{{ $spp->lokasi_penahanan }}"
                                    data-cabang="{{ $spp->cabang_penahanan }}">
                                    {{ $spp->document_number }}
                                    @if($sppSuspects) &mdash; {{ $sppSuspects }} @endif
                                </option>
                            @endforeach
                        </select>
                        <small class="text-info"><i class="bi bi-info-circle"></i> Pilih untuk auto-fill nomor, tanggal, rutan, dan cabang. Dapat diubah manual setelah dipilih.</small>
                        <input id="surat_perintah_penahanan_number" type="text" class="form-control mt-2" name="surat_perintah_penahanan_number" value="{{ old('surat_perintah_penahanan_number') }}" placeholder="Nomor surat (terisi otomatis atau isi manual)">
                        </div>
                        <div class="col-md-6 mb-2 pe-0">
                            <label class="fw-bold d-block mb-1" for="surat_perintah_penahanan_date">Tgl Surat Penahanan</label>
                            <input class="form-control" id="surat_perintah_penahanan_date" name="surat_perintah_penahanan_date" placeholder="YYYY-MM-DD" value="{{ old('surat_perintah_penahanan_date') }}" data-provide="datepicker">
                        </div>
                    </div>
                </div>
                <hr class="border-secondary border-dashed">
                <div class="input-group row mb-3 ms-0">
                    <div class="col-sm-3"></div>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12 row pe-0">
                        <div class="col-md-6 mb-2">
                            <label class="fw-bold d-block mb-1" for="surat_perpanjangan_penahanan_number">No. Surat Perpanjangan Penahanan</label>
                            <input id="surat_perpanjangan_penahanan_number" type="text" class="form-control" name="surat_perpanjangan_penahanan_number" value="{{ old('surat_perpanjangan_penahanan_number') }}">
                        </div>
                        <div class="col-md-6 mb-2 pe-0">
                            <label class="fw-bold d-block mb-1" for="surat_perpanjangan_penahanan_date">Tgl Surat Perpanjangan Penahanan</label>
                            <input class="form-control" id="surat_perpanjangan_penahanan_date" name="surat_perpanjangan_penahanan_date" placeholder="YYYY-MM-DD" value="{{ old('surat_perpanjangan_penahanan_date') }}" data-provide="datepicker">
                        </div>
                    </div>
                </div>
                <div class="input-group row mb-3 ms-0">
                    <div class="col-sm-3"></div>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12 row pe-0">
                        <div class="col-md-6 mb-2">
                            <label class="fw-bold d-block mb-1" for="surat_perpanjangan_penahanan_court_number">No. Surat Perpanjangan Penahanan ke Pengadilan</label>
                            <input id="surat_perpanjangan_penahanan_court_number" type="text" class="form-control" name="surat_perpanjangan_penahanan_court_number" value="{{ old('surat_perpanjangan_penahanan_court_number') }}">
                        </div>
                        <div class="col-md-6 mb-2 pe-0">
                            <label class="fw-bold d-block mb-1" for="surat_perpanjangan_penahanan_court_date">Tgl Surat Perpanjangan Penahanan ke Pengadilan</label>
                            <input class="form-control" id="surat_perpanjangan_penahanan_court_date" name="surat_perpanjangan_penahanan_court_date" placeholder="YYYY-MM-DD" value="{{ old('surat_perpanjangan_penahanan_court_date') }}" data-provide="datepicker">
                        </div>
                    </div>
                </div>

                {{-- Suspension Fields (Only for DITANGGUHKAN) --}}
                <div id="suspensionFields" style="{{ old('penahanan_status') == 'DITANGGUHKAN' ? '' : 'display:none;' }}">
                    <div class="input-group row mb-3 ms-0">
                        <div class="col-sm-3"></div>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-12 row pe-0">
                            <div class="col-md-6 mb-2">
                                <label class="fw-bold d-block mb-1" for="surat_penangguhan_penahanan_number">No. Surat Penangguhan Penahanan</label>
                                <input id="surat_penangguhan_penahanan_number" type="text" class="form-control" name="surat_penangguhan_penahanan_number" value="{{ old('surat_penangguhan_penahanan_number') }}">
                            </div>
                            <div class="col-md-6 mb-2 pe-0">
                                <label class="fw-bold d-block mb-1" for="surat_penangguhan_penahanan_date">Tgl Surat Penangguhan Penahanan</label>
                                <input class="form-control" id="surat_penangguhan_penahanan_date" name="surat_penangguhan_penahanan_date" placeholder="YYYY-MM-DD" value="{{ old('surat_penangguhan_penahanan_date') }}" data-provide="datepicker">
                            </div>
                        </div>
                    </div>
                </div>
                </div>

                <hr>
                {{-- Kejaksaan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="prosecutor_id">Kejaksaan Negeri Tujuan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="prosecutor_id" id="prosecutor_id" required>
                            <option value="">--Pilih Kejaksaan--</option>
                            @foreach ($prosecutors as $prosecutor)
                                <option value="{{ $prosecutor->id }}" {{ old('prosecutor_id') == $prosecutor->id ? 'selected' : '' }}>{{ $prosecutor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr>
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
                                        $oldBb = old('daftar_barang_bukti', []);
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
                        <input id="barang_bukti_storage" type="text" class="form-control" name="barang_bukti_storage" value="{{ old('barang_bukti_storage') }}" placeholder="Contoh: Gudang BB Satlantas">
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
                                    data-full-name="{{ $officer->full_name }}">
                                    {{ $officer->register_number . ' | ' . ($officer->rank->name ?? '') . ' ' . $officer->full_name . ($officer->position->name ? ' | ' . $officer->position->name : '') }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="investigator_pangkat_nama" id="investigator_pangkat_nama" value="{{ old('investigator_pangkat_nama') }}">
                        <small class="text-muted text-italic">Cari berdasarkan NRP atau Nama Petugas</small>
                    </div>
                </div>

                {{-- HP Penyidik --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="investigator_hp">No. HP Penyidik</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="investigator_hp" type="text" class="form-control" name="investigator_hp" value="{{ old('investigator_hp') }}" placeholder="08xxxxxxxxxx">
                    </div>
                </div>

                <hr>

                {{-- Pejabat --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="signatory">Pejabat Penandatangan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="signatory" id="signatory" required>
                            <option value="">--Pilih Yang Menandatangani--</option>
                            @foreach ($authorizedSignatories as $data)
                                <option value="{{ $data->id }}" {{ old('signatory') == $data->id ? 'selected' : '' }}>
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
                            @php $tembusan = old('tembusan', []); @endphp
                            @foreach ($tembusan as $item)
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
                        <i class="bi bi-save"></i> Simpan Dokumen
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

        {{-- Shared Modal for Barang Bukti Management --}}
        @include('produktivitas.surat-penyitaan.modal.modal', ['id' => $accidentId])
    </div>
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
            var oldRutan = "{{ old('penahanan_rutan') }}";
            var oldCabang = "{{ old('penahanan_cabang') }}";

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
            width: '100%',
                theme: 'bootstrap4',
                width: '100%'
            });

            // ===== AUTO-FILL dari dropdown Surat Perintah Penahanan =====
            $('#surat_perintah_penahanan_ref').on('change', function() {
                var selected = $(this).find('option:selected');
                var number   = selected.data('number') || '';
                var date     = selected.data('date')   || '';
                var rutan    = selected.data('rutan')  || '';
                var cabang   = selected.data('cabang') || '';

                // Isi nomor & tanggal SP Penahanan
                $('#surat_perintah_penahanan_number').val(number);
                if (date) {
                    $('#surat_perintah_penahanan_date').datepicker('update', date);
                } else {
                    $('#surat_perintah_penahanan_date').val('');
                }

                // Auto-fill Rutan & Cabang di form penahanan (jika ada data)
                if (rutan) {
                    // Re-populate cabang select based on rutan
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
                }
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

            // AJAX for Pasal Disangkakan based on Sprindik selection
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
                        },
                        error: function() {
                            console.error('Failed to fetch laws for Sprindik ID: ' + sprindikId);
                        }
                    });
                }
            });

            // ==========================================
        // SAKSI UI LOGIC (TABLE & MODAL)
        // ==========================================
        var saksiList = {!! isset($messages['konten_dokumen']['daftar_saksi']) ? json_encode($messages['konten_dokumen']['daftar_saksi']) : '[]' !!};
        
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
            // Dynamic Rows for Barang Bukti
            let bbIndex = {{ count(old('daftar_barang_bukti', [])) > 0 ? count(old('daftar_barang_bukti', [])) : 1 }};
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

