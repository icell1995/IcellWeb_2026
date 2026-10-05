@php
    $_title = 'Edit Surat Pengiriman Berkas Perkara Tahap II';
@endphp

@php
    $messages = is_string($document->messages) ? json_decode($document->messages, true) : ($document->messages ?? []);
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
                Edit Surat Pengiriman Berkas Perkara Tahap II (P21)
                <span class="badge bg-info text-white ms-2">SPPT-TI / Pusiknas Bareskrim</span>
            </h5>

            <div class="alert alert-danger" id="attentionBox">
                <div class="text-center">
                    <b>
                        PERHATIAN !<br /><br />
                        DATA INI WAJIB DIISI DENGAN DETAIL DAN LENGKAP KARENA AKAN DIPERTUKARKAN DENGAN
                        PUSIKNAS BARESKRIM POLRI DALAM KERANGKA SPPT-TI (SISTEM PERADILAN PIDANA BERBASIS
                        TEKNOLOGI INFORMASI). KODE PROSES: <strong>BPT2</strong>
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
            <form action="{{ route('doc.tahap-2-document.update', [$document->id, 'accident_id' => $accidentId]) }}"
                method="POST" enctype="multipart/form-data" id="bpt2EditForm">
                @csrf
                @method('POST')
                <input type="hidden" name="accident_id" value="{{ $accidentId }}">

                @php
                    $signatoryOfficer = $document->officers->where('class', 'SIGNATORY')->first();
                    $signatoryId = $messages['signatory_id'] ?? ($signatoryOfficer ? $signatoryOfficer->id : null);
                    $currentSuspects = $selectedSuspects ?? [];
                    $daftarBB    = $messages['daftar_barang_bukti'] ?? [];
                    $daftarSaksi = $messages['daftar_saksi'] ?? [];
                    $carbonCopies = old('carbonCopies', $document->carbon_copies ?? []);
                    if(is_string($carbonCopies)) $carbonCopies = json_decode($carbonCopies, true) ?? [];
                @endphp

                {{-- Nomor LP --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="no_lp">Nomor LP</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="no_lp" type="text" class="form-control bg-light" value="{{ $accident->no_lp }}" readonly>
                    </div>
                </div>

                {{-- Nomor Surat Pengantar --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="documentNumber">Nomor Surat Pengantar <span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="documentNumber" type="text"
                            class="form-control @error('documentNumber') is-invalid @enderror"
                            name="documentNumber" value="{{ old('documentNumber', $document->document_number) }}" required
                            placeholder="Contoh: B/001/I/RES.0.0./2026/Satker">
                        @error('documentNumber')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Tanggal Surat Pengantar --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="documentDate">Tanggal Surat Pengantar <span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input class="form-control @error('documentDate') is-invalid @enderror" id="documentDate" name="documentDate"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('documentDate', $document->document_date ? $document->document_date->format('Y-m-d') : '') }}"
                            data-provide="datepicker" required>
                        @error('documentDate')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Jumlah Lampiran --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="appendix">Jumlah Lampiran</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="appendix" type="number" class="form-control" name="appendix" value="{{ old('appendix', $document->appendix ?? 1) }}" min="1" required>
                    </div>
                </div>

                <hr>

                {{-- Surat Perintah Penyidikan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="surat_perintah_penyidikan_id">Nomor Surat Perintah Penyidikan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="surat_perintah_penyidikan_id" id="surat_perintah_penyidikan_id">
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
                    <label class="fw-bold col-sm-3 col-form-label" for="noSpdp">Nomor SPDP <span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        @if($spdpDocuments->count() > 0)
                            <select class="form-control select2 @error('noSpdp') is-invalid @enderror" name="noSpdp" id="noSpdp" required>
                                <option value="">--Pilih No SPDP--</option>
                                @foreach ($spdpDocuments as $spdp)
                                    <option value="{{ $spdp->document_number }}" {{ old('noSpdp', $document->no_spdp) == $spdp->document_number ? 'selected' : '' }}>
                                        {{ $spdp->document_number }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" class="form-control @error('noSpdp') is-invalid @enderror" name="noSpdp" id="noSpdp" value="{{ old('noSpdp', $document->no_spdp) }}" required>
                            <small class="text-muted">Dokumen SPDP belum tersedia, silakan ketik manual.</small>
                        @endif
                        @error('noSpdp')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
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

                {{-- Nomor Berkas Perkara --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="noBerkasPerkara">Nomor Berkas Perkara <span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="noBerkasPerkara" type="text" class="form-control" name="noBerkasPerkara" value="{{ old('noBerkasPerkara', $document->no_berkas_perkara) }}" required placeholder="Contoh: BP/001/.../RES.0.0./2026/Satker">
                    </div>
                </div>

                                {{-- No Surat P21 --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="p21Number">No. Surat P21</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="p21Number" type="text" class="form-control" name="p21Number" value="{{ old('p21Number', $messages['p21_number'] ?? '') }}" placeholder="Contoh: B-1234/...">
                    </div>
                </div>

                {{-- Tanggal Surat P21 --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggalTerimaP21">Tanggal Surat P21</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tanggalTerimaP21" type="text" class="form-control" name="tanggalTerimaP21" value="{{ old('tanggalTerimaP21', $document->tanggal_terima_p21 ? $document->tanggal_terima_p21->format('Y-m-d') : '') }}" placeholder="YYYY-MM-DD" autocomplete="off" data-provide="datepicker">
                    </div>
                </div>

                <hr>

                <hr>

                {{-- Uraian Singkat Perkara --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="uraianPerkara">Uraian Singkat Perkara <span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <textarea id="uraianPerkara" class="form-control @error('uraianPerkara') is-invalid @enderror" name="uraianPerkara" rows="4" placeholder="Uraikan secara singkat dugaan tindak pidana yang terjadi..." required>{{ old('uraianPerkara', $messages['uraian_singkat_perkara'] ?? $accident->damage_lose_desc ?? '') }}</textarea>
                        @error('uraianPerkara')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Lokasi Kejadian --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="lokasiKejadian">Lokasi Kejadian <span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="lokasiKejadian" type="text" class="form-control" name="lokasiKejadian" value="{{ old('lokasiKejadian', $messages['lokasi_kejadian'] ?? $accident->road_name) }}" placeholder="Jalan/Tempat kejadian" required>
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
<input type="hidden" name="waktuKejadian" value="{{ old('waktuKejadian', $messages['waktu_kejadian'] ?? ('Sekitar pukul ' . ($accident->accident_time ? \Carbon\Carbon::parse($accident->accident_time)->format('H:i') : '-') . ' WIB')) }}">

                {{-- Tanggal Kejadian (Hidden) --}}
<input type="hidden" name="tahunKejadian" value="{{ old('tahunKejadian', $messages['tahun_kejadian'] ?? ($accident->accident_date ? date('Y', strtotime($accident->accident_date)) : '')) }}">
<input type="hidden" name="bulanKejadian" value="{{ old('bulanKejadian', $messages['bulan_kejadian'] ?? ($accident->accident_date ? date('n', strtotime($accident->accident_date)) : '')) }}">
<input type="hidden" name="tanggalKejadian" value="{{ old('tanggalKejadian', $messages['tanggal_kejadian'] ?? ($accident->accident_date ? date('j', strtotime($accident->accident_date)) : '')) }}">

                <hr>

                <hr>
{{-- Daftar Tersangka --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="suspects">Daftar Tersangka</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2 @error('suspects') is-invalid @enderror"
                                name="suspects[]" id="suspects" multiple="multiple">
                            @foreach ($suspects as $suspect)
                                <option value="{{ $suspect->id }}" {{ in_array($suspect->id, old('suspects', $selectedSuspects)) ? 'selected' : '' }}>
                                    {{ $suspect->name }} - {{ $suspect->identity_number }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih Tersangka yang Dikirimkan</small>
                    </div>
                </div>

                {{-- Pasal Disangkakan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label">Pasal yang Disangkakan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <div class="p-2 bg-light border rounded text-muted" id="pasal_disangkakan_display" style="min-height: 80px;">{{ old('pasal_disangkakan', $document->pasal_disangkakan ?? 'Akan terisi otomatis berdasarkan Sprindik terpilih') }}</div>
                        <input type="hidden" id="pasal_disangkakan" name="pasal_disangkakan" value="{{ old('pasal_disangkakan', $document->pasal_disangkakan) }}">
                    </div>
                </div>

                <hr>

                {{-- Tujuan Kejaksaan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="prosecutor">Kejaksaan Negeri Tujuan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="prosecutor" id="prosecutor">
                            <option value="">--Pilih Kejaksaan--</option>
                            @foreach ($prosecutors as $p)
                                <option value="{{ $p->id }}" {{ old('prosecutor', $document->prosecutor_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr>

                {{-- Status Penahanan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label">Status Penahanan <span class="text-danger fs-5">*</span></label>
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
                            <input class="form-control" id="penahanan_start_date" name="penahanan_start_date" placeholder="YYYY-MM-DD" value="{{ old('penahanan_start_date', $document->penahanan_start_date ? $document->penahanan_start_date->format('Y-m-d') : '') }}" data-provide="datepicker">
                        </div>
                        <div class="col-md-6 mb-2 pe-0">
                            <label class="fw-bold d-block mb-1" for="penahanan_end_date">Tgl Selesai Penahanan</label>
                            <input class="form-control" id="penahanan_end_date" name="penahanan_end_date" placeholder="YYYY-MM-DD" value="{{ old('penahanan_end_date', $document->penahanan_end_date ? $document->penahanan_end_date->format('Y-m-d') : '') }}" data-provide="datepicker">
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
                                    <option value="{{ $spp->id }}"
                                        data-number="{{ $spp->document_number }}"
                                        data-date="{{ $spp->document_date ? \Carbon\Carbon::parse($spp->document_date)->format('Y-m-d') : '' }}"
                                        data-rutan="{{ $spp->lokasi_penahanan ?? '' }}"
                                        data-cabang="{{ $spp->cabang_penahanan ?? '' }}">
                                        {{ $spp->document_number }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-info"><i class="bi bi-info-circle"></i> Pilih untuk auto-fill nomor, tanggal, rutan, dan cabang. Dapat diubah manual setelah dipilih.</small>
                            <input id="surat_perintah_penahanan_number" type="text" class="form-control mt-2" name="surat_perintah_penahanan_number" value="{{ old('surat_perintah_penahanan_number', $document->surat_perintah_penahanan_number) }}" placeholder="Nomor surat (terisi otomatis atau isi manual)">
                        </div>
                        <div class="col-md-6 mb-2 pe-0">
                            <label class="fw-bold d-block mb-1" for="surat_perintah_penahanan_date">Tgl Surat Penahanan</label>
                            <input class="form-control" id="surat_perintah_penahanan_date" name="surat_perintah_penahanan_date" placeholder="YYYY-MM-DD" value="{{ old('surat_perintah_penahanan_date', $document->surat_perintah_penahanan_date ? $document->surat_perintah_penahanan_date->format('Y-m-d') : '') }}" data-provide="datepicker">
                        </div>
                    </div>
                </div>
                <hr class="border-secondary border-dashed">
                <div class="input-group row mb-3 ms-0">
                    <div class="col-sm-3"></div>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12 row pe-0">
                        <div class="col-md-6 mb-2">
                            <label class="fw-bold d-block mb-1" for="surat_perpanjangan_penahanan_number">No. Surat Perpanjangan Penahanan</label>
                            <input id="surat_perpanjangan_penahanan_number" type="text" class="form-control" name="surat_perpanjangan_penahanan_number" value="{{ old('surat_perpanjangan_penahanan_number', $document->surat_perpanjangan_penahanan_number) }}">
                        </div>
                        <div class="col-md-6 mb-2 pe-0">
                            <label class="fw-bold d-block mb-1" for="surat_perpanjangan_penahanan_date">Tgl Surat Perpanjangan Penahanan</label>
                            <input class="form-control" id="surat_perpanjangan_penahanan_date" name="surat_perpanjangan_penahanan_date" placeholder="YYYY-MM-DD" value="{{ old('surat_perpanjangan_penahanan_date', $document->surat_perpanjangan_penahanan_date ? $document->surat_perpanjangan_penahanan_date->format('Y-m-d') : '') }}" data-provide="datepicker">
                        </div>
                    </div>
                </div>
                <div class="input-group row mb-3 ms-0">
                    <div class="col-sm-3"></div>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12 row pe-0">
                        <div class="col-md-6 mb-2">
                            <label class="fw-bold d-block mb-1" for="surat_perpanjangan_penahanan_court_number">No. Surat Perpanjangan Penahanan ke Pengadilan</label>
                            <input id="surat_perpanjangan_penahanan_court_number" type="text" class="form-control" name="surat_perpanjangan_penahanan_court_number" value="{{ old('surat_perpanjangan_penahanan_court_number', $document->surat_perpanjangan_penahanan_court_number) }}">
                        </div>
                        <div class="col-md-6 mb-2 pe-0">
                            <label class="fw-bold d-block mb-1" for="surat_perpanjangan_penahanan_court_date">Tgl Surat Perpanjangan Penahanan ke Pengadilan</label>
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
                                <label class="fw-bold d-block mb-1" for="surat_penangguhan_penahanan_number">No. Surat Penangguhan Penahanan</label>
                                <input id="surat_penangguhan_penahanan_number" type="text" class="form-control" name="surat_penangguhan_penahanan_number" value="{{ old('surat_penangguhan_penahanan_number', $document->surat_penangguhan_penahanan_number) }}">
                            </div>
                            <div class="col-md-6 mb-2 pe-0">
                                <label class="fw-bold d-block mb-1" for="surat_penangguhan_penahanan_date">Tgl Surat Penangguhan Penahanan</label>
                                <input class="form-control" id="surat_penangguhan_penahanan_date" name="surat_penangguhan_penahanan_date" placeholder="YYYY-MM-DD" value="{{ old('surat_penangguhan_penahanan_date', $document->surat_penangguhan_penahanan_date ? $document->surat_penangguhan_penahanan_date->format('Y-m-d') : '') }}" data-provide="datepicker">
                            </div>
                        </div>
                    </div>
                </div>
                </div>

                {{-- SAKSI --}}
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

                {{-- Barang Bukti --}}
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
                                <tbody id="bbBody">
                                    @if(count($daftarBB) > 0)
                                        @foreach($daftarBB as $bb)
                                            <tr>
                                                <td><input type="text" name="bb_nama[]" class="form-control form-control-sm" value="{{ $bb['nama'] ?? '' }}" required></td>
                                                <td><input type="text" name="bb_jumlah[]" class="form-control form-control-sm" value="{{ $bb['jumlah'] ?? '1' }}"></td>
                                                <td><input type="text" name="bb_satuan[]" class="form-control form-control-sm" value="{{ $bb['satuan'] ?? 'unit' }}"></td>
                                                <td><input type="text" name="bb_keterangan[]" class="form-control form-control-sm" value="{{ $bb['keterangan'] ?? '' }}"></td>
                                                <td><button type="button" class="btn btn-sm btn-danger removeBBRow"><i class="bi bi-trash"></i></button></td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td><input type="text" name="bb_nama[]" class="form-control form-control-sm" required placeholder="Nama barang bukti"></td>
                                            <td><input type="text" name="bb_jumlah[]" class="form-control form-control-sm" value="1"></td>
                                            <td><input type="text" name="bb_satuan[]" class="form-control form-control-sm" value="unit"></td>
                                            <td><input type="text" name="bb_keterangan[]" class="form-control form-control-sm" placeholder="Keterangan"></td>
                                            <td><button type="button" class="btn btn-sm btn-danger removeBBRow"><i class="bi bi-trash"></i></button></td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-sm btn-success" id="addBBRow"><i class="bi bi-plus-circle"></i> Tambah Barang Bukti</button>
                        </div>
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
                                    data-phone="{{ $officer->phone_number ?? ($officer->user->no_hp ?? '') }}"
                                    data-rank-name="{{ $officer->rank->name ?? '' }}"
                                    data-full-name="{{ $officer->full_name }}">
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

                {{-- Penandatangan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="signatory">Pejabat Penandatangan <span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="signatory" id="signatory" required>
                            <option value="">--Pilih Yang Menandatangani--</option>
                            @foreach ($authorizedSignatories as $data)
                                <option value="{{ $data->id }}" {{ old('signatory', $signatoryId) == $data->id ? 'selected' : '' }}>
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
                            @foreach ($carbonCopies as $item)
                                <div class="input-group mb-2">
                                    <input type="text" name="carbonCopies[]" class="form-control" value="{{ $item }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-danger removeCarbonCopy" type="button">Hapus</button>
                                    </div>
                                </div>
                            @endforeach
                            @if(count($carbonCopies) === 0)
                                <div class="input-group mb-2">
                                    <input type="text" name="carbonCopies[]" class="form-control" placeholder="Masukkan tembusan..." required>
                                </div>
                            @endif
                        </div>
                        <button class="btn btn-primary addCarbonCopiesButton" type="button">+ Tambah Tembusan Lainnya</button>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="mt-4 d-flex justify-content-center">
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-secondary me-2">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                    <button type="submit" id="bpt2EditFormSubmit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Perubahan
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
                                <input type="text" class="form-control" id="saksiTempatLahir" placeholder="Kota Kelahiran">
                            </div>
                        </div>

                        <div class="form-group row mb-2">
                            <label class="col-sm-4 col-form-label fw-bold">Jenis Kelamin</label>
                            <div class="col-sm-8">
                                <select class="form-control select2-saksi" id="saksiGender" style="width: 100%;">
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    @foreach($refGender as $g)
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
                                    @foreach($refAgama as $r)
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
                                    @foreach($refPendidikan as $e)
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
                        <h6 class="fw-bold text-secondary mb-3 border-bottom pb-2">Alamat &amp; Wilayah (Pusiknas)</h6>

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

@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script>
    // Auto-suggest Kode Wilayah from Lokasi Kejadian
    $('#lokasiKejadian').on('change blur', function() {
        const lokasi = $(this).val();
        if (!lokasi) return;
        
        const match = lokasi.match(/(?:KECAMATAN|KEC)\.?\s*([A-Za-z\s]+?)\s*(?:KABUPATEN|KAB\.|KAB|KOTA|,|$)/i);
        if (match && match[1]) {
            const extracted = match[1].trim().toLowerCase();
            
            // Loop through options and select if matches
            let found = false;
            $('#kodeWilayah option').each(function() {
                const text = $(this).text().toLowerCase();
                if (text.includes(extracted)) {
                    $('#kodeWilayah').val($(this).val()).trigger('change');
                    found = true;
                    return false; // break loop
                }
            });
        }
    });
</script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>
    <script>
    // Auto-suggest Kode Wilayah from Lokasi Kejadian
    $('#lokasiKejadian').on('change blur', function() {
        const lokasi = $(this).val();
        if (!lokasi) return;
        
        const match = lokasi.match(/(?:KECAMATAN|KEC)\.?\s*([A-Za-z\s]+?)\s*(?:KABUPATEN|KAB\.|KAB|KOTA|,|$)/i);
        if (match && match[1]) {
            const extracted = match[1].trim().toLowerCase();
            
            // Loop through options and select if matches
            let found = false;
            $('#kodeWilayah option').each(function() {
                const text = $(this).text().toLowerCase();
                if (text.includes(extracted)) {
                    $('#kodeWilayah').val($(this).val()).trigger('change');
                    found = true;
                    return false; // break loop
                }
            });
        }
    });
</script>

    <script type="text/javascript">
        $(document).ready(function () {

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

            // Select2 init
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Datepicker
            if(typeof $('[data-provide="datepicker"]').datepicker === 'function') {
                $('[data-provide="datepicker"]').datepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true,
                    todayHighlight: true
                });
            }

            // Tembusan Dinamis
            $('.addCarbonCopiesButton').on('click', function () {
                var newRow = $(
                    '<div class="input-group mb-2">' +
                    '<input type="text" name="carbonCopies[]" class="form-control" placeholder="Masukkan tembusan..." required>' +
                    '<div class="input-group-append"><button class="btn btn-outline-danger removeCarbonCopy" type="button">Hapus</button></div>' +
                    '</div>'
                );
                $('#tembusanContainer').append(newRow);
            });

            $(document).on('click', '.removeCarbonCopy', function () {
                $(this).closest('.input-group').remove();
            });

            // Barang Bukti Dinamis
            $('#addBBRow').on('click', function() {
                var newRow = '<tr>' +
                    '<td><input type="text" name="bb_nama[]" class="form-control form-control-sm" required placeholder="Nama barang bukti"></td>' +
                    '<td><input type="text" name="bb_jumlah[]" class="form-control form-control-sm" value="1"></td>' +
                    '<td><input type="text" name="bb_satuan[]" class="form-control form-control-sm" value="unit"></td>' +
                    '<td><input type="text" name="bb_keterangan[]" class="form-control form-control-sm" placeholder="Keterangan"></td>' +
                    '<td><button type="button" class="btn btn-sm btn-danger removeBBRow"><i class="bi bi-trash"></i></button></td>' +
                '</tr>';
                $('#bbBody').append(newRow);
            });

            $(document).on('click', '.removeBBRow', function() {
                if($('#bbBody tr').length > 1) {
                    $(this).closest('tr').remove();
                }
            });

            // ==========================================
            // SAKSI UI LOGIC (TABLE & MODAL)
            // ==========================================
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
                $('#saksiHiddenInputsContainer').empty();

                saksiList.forEach(function(s, index) {
                    var container = $('<div>');
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
                    $('#saksiHiddenInputsContainer').append(container);
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
                getLocationSaksi($(this).val(), 'PROVINCE', '#saksiProvinsi');
                $('#saksiKabupaten, #saksiKecamatan, #saksiKelurahan').empty().append('<option value="">-- Pilih --</option>').prop('disabled', true);
            });
            $(document).on('change', '#saksiProvinsi', function() {
                getLocationSaksi($(this).val(), 'REGENCY', '#saksiKabupaten');
                $('#saksiKecamatan, #saksiKelurahan').empty().append('<option value="">-- Pilih --</option>').prop('disabled', true);
            });
            $(document).on('change', '#saksiKabupaten', function() {
                getLocationSaksi($(this).val(), 'DISTRICT', '#saksiKecamatan');
                $('#saksiKelurahan').empty().append('<option value="">-- Pilih --</option>').prop('disabled', true);
            });
            $(document).on('change', '#saksiKecamatan', function() {
                getLocationSaksi($(this).val(), 'VILLAGE', '#saksiKelurahan');
            });

            function getLocationSaksi(parentId, classCode, targetSelect) {
                if(!parentId) {
                    $(targetSelect).empty().append('<option value="">--Pilih--</option>').prop('disabled', true);
                    return;
                }

                $.ajax({
                    url: "{{ route('doc.tahap-2-document.api.locations', ['accident_id' => $accidentId]) }}",
                    type: 'GET',
                    dataType: 'json',
                    data: { 'parent_id': parentId, 'class': classCode },
                    success: function(response) {
                        var data = response.data;
                        var select = $(targetSelect);
                        select.empty().append($('<option>', { value: '', text: '--Pilih--' }));
                        $.each(data, function(i, d) {
                            select.append($('<option>', { value: d.id, text: d.name, 'data-code': d.code }));
                        });
                        select.prop('disabled', false).trigger('change.select2');
                    }
                });
            }

            // Penahanan toggle
            $('input[name="penahanan_status"]').on('change', function() {
                var val = $(this).val();
                if(val === 'TIDAK_DITAHAN') {
                    $('#detentionFieldsContainer').hide();
                } else {
                    $('#detentionFieldsContainer').show();
                }
                if(val === 'DITANGGUHKAN') {
                    $('#suspensionFields').show();
                } else {
                    $('#suspensionFields').hide();
                }
            });
            // Initial toggle on load
            $('input[name="penahanan_status"]:checked').trigger('change');

            // Sprindik → pasal_disangkakan AJAX
            $('#surat_perintah_penyidikan_id').on('change', function() {
                var spId = $(this).val();
                if(!spId) {
                    $('#pasal_disangkakan_display').html('Akan terisi otomatis berdasarkan Sprindik terpilih');
                    $('#pasal_disangkakan').val('');
                    return;
                }
                $.ajax({
                    url: "{{ route('doc.tahap-2-document.api.ajax-pasal', ['accident_id' => $accidentId]) }}",
                    type: 'GET',
                    data: { sprindik_id: spId },
                    success: function(res) {
                        var pasalStr = res.pasal_string || res.pasal || '';
                        if(pasalStr) {
                            $('#pasal_disangkakan_display').html(pasalStr.replace(/\n/g, '<br>'));
                            $('#pasal_disangkakan').val(pasalStr);
                        } else {
                            $('#pasal_disangkakan_display').html('Data pasal tidak ditemukan');
                            $('#pasal_disangkakan').val('');
                        }
                    },
                    error: function() {
                        $('#pasal_disangkakan_display').html('Gagal mengambil data pasal');
                    }
                });
            });

            // Investigator autofill
            $('#investigator_selection').on('change', function() {
                var selected = $(this).find(':selected');
                var rankName = selected.data('rank-name') || '';
                var fullName = selected.data('full-name') || '';
                var phone = selected.data('phone') || '';
                if(fullName) {
                    $('#investigator_pangkat_nama').val((rankName ? rankName + ' ' : '') + fullName);
                }
                if(phone) {
                    $('#investigator_hp').val(phone);
                }
            });
        });
    // Auto-suggest Kode Wilayah from Lokasi Kejadian
    $('#lokasiKejadian').on('change blur', function() {
        const lokasi = $(this).val();
        if (!lokasi) return;
        
        const match = lokasi.match(/(?:KECAMATAN|KEC)\.?\s*([A-Za-z\s]+?)\s*(?:KABUPATEN|KAB\.|KAB|KOTA|,|$)/i);
        if (match && match[1]) {
            const extracted = match[1].trim().toLowerCase();
            
            // Loop through options and select if matches
            let found = false;
            $('#kodeWilayah option').each(function() {
                const text = $(this).text().toLowerCase();
                if (text.includes(extracted)) {
                    $('#kodeWilayah').val($(this).val()).trigger('change');
                    found = true;
                    return false; // break loop
                }
            });
        }
    });

    $('#penahanan_rutan').on('change', function() {
        var branch = $(this).find(':selected').data('branch');
        if (branch) {
            $('#penahanan_cabang').val(branch).trigger('change');
        } else {
            $('#penahanan_cabang').val('').trigger('change');
        }
    });
</script>
@endpush
