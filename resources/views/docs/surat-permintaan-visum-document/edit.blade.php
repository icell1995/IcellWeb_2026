@php
    $_title = 'Ubah Surat Permintaan Visum Et Repertum / Visum Et Psikiatrikum';
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
            <h5 class="fw-bold text-blue-dark">Ubah Surat Permintaan Visum Et Repertum / Visum Et Psikiatrikum (S-48)</h5>

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

            @if (isset($errors) && $errors->any())
                <div class="card-body p-0 mb-3">
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
                <div class="card-body p-0 mb-3">
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                </div>
            @endif
        </div>

        <div class="box-body">
            <form action="{{ route('doc.surat-permintaan-visum-document.update', ['id' => $document->id]) }}"
                method="POST" id="formSuratVisum" novalidate>
                @csrf
                <input type="hidden" name="accident_id" id="accident_id" value="{{ $accidentId }}">

                {{-- ─── 1. INFORMASI DASAR DOKUMEN & RUMAH SAKIT ─── --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nomor_lp">Nomor LP<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nomor_lp" type="text"
                            class="form-control font-weight-bold @error('nomor_lp') is-invalid @enderror"
                            name="nomor_lp" value="{{ old('nomor_lp', $document->nomor_lp ?: ($accident->no_lp ?? '')) }}" required placeholder="Nomor LP" readonly>
                        <input type="hidden" name="accidentNumber" id="accidentNumber" value="{{ $document->nomor_lp ?: ($accident->no_lp ?? '') }}">
                        <input type="hidden" name="tanggal_lp" id="tanggal_lp" value="{{ old('tanggal_lp', $document->tanggal_lp ? \Carbon\Carbon::parse($document->tanggal_lp)->format('Y-m-d') : ($accident->report_date ? \Carbon\Carbon::parse($accident->report_date)->format('Y-m-d') : date('Y-m-d'))) }}">
                        @error('nomor_lp')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nomor_surat">Nomor Dokumen<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nomor_surat" type="text" class="form-control @error('nomor_surat') is-invalid @enderror" name="nomor_surat"
                            value="{{ old('nomor_surat', $document->nomor_surat) }}" required placeholder="Contoh: B/VER/001/I/RES.0.0/{{ date('Y') }}/Lantas">
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
                            value="{{ old('tanggal_surat', $document->tanggal_surat ? \Carbon\Carbon::parse($document->tanggal_surat)->format('Y-m-d') : date('Y-m-d')) }}" data-provide="datepicker" required>
                        @error('tanggal_surat')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="dikeluarkan_di">Dikeluarkan di<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="dikeluarkan_di" type="text" class="form-control @error('dikeluarkan_di') is-invalid @enderror" name="dikeluarkan_di"
                            value="{{ old('dikeluarkan_di', $document->dikeluarkan_di ?: $defaultLocation) }}" placeholder="Contoh: Simalungun" required>
                        @error('dikeluarkan_di')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="satker_penyidik">Satuan Kerja Penyidik<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="satker_penyidik" type="text" class="form-control @error('satker_penyidik') is-invalid @enderror" name="satker_penyidik"
                            value="{{ old('satker_penyidik', $document->satker_penyidik ?: ('Satuan Lalu Lintas Resor ' . ucwords(strtolower($accident->polres->name ?? '')))) }}" placeholder="Contoh: Satuan Lalu Lintas Resor Simalungun" required>
                        @error('satker_penyidik')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="klasifikasi_surat">Klasifikasi Surat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2 @error('klasifikasi_surat') is-invalid @enderror" name="klasifikasi_surat" id="klasifikasi_surat" required>
                            <option value="">-- Pilih Klasifikasi --</option>
                            <option value="BIASA" {{ old('klasifikasi_surat', $document->klasifikasi_surat ?: 'BIASA') == 'BIASA' || old('klasifikasi_surat', $document->klasifikasi_surat) == 'Biasa' ? 'selected' : '' }}>Biasa</option>
                            <option value="RAHASIA" {{ old('klasifikasi_surat', $document->klasifikasi_surat) == 'RAHASIA' || old('klasifikasi_surat', $document->klasifikasi_surat) == 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
                            <option value="SANGAT RAHASIA" {{ old('klasifikasi_surat', $document->klasifikasi_surat) == 'SANGAT RAHASIA' || old('klasifikasi_surat', $document->klasifikasi_surat) == 'Sangat Rahasia' ? 'selected' : '' }}>Sangat Rahasia</option>
                        </select>
                        @error('klasifikasi_surat')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="lampiran_surat">Lampiran</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="lampiran_surat" type="text" class="form-control bg-light text-secondary fw-semibold @error('lampiran_surat') is-invalid @enderror" name="lampiran_surat"
                            value="Otomatis (Sesuai jumlah lembar saat diunduh)" readonly>
                        <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Dihitung otomatis saat dokumen diunduh (misal: <em>1 lembar</em> atau <em>2 lembar</em> sesuai panjang isi surat).</small>
                        @error('lampiran_surat')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                @if(isset($s23Document) && $s23Document)
                    <div class="alert alert-info py-2 mb-3">
                        <i class="bi bi-info-circle-fill me-1"></i> Riwayat Rumah Sakit Terdeteksi: Rujukan RS <b>{{ $s23Document->tempat_rawat_inap }}</b> di <b>{{ $s23Document->kota_rumah_sakit }}</b> (Dokter: <b>{{ $s23Document->nama_dokter }}</b>) dari Dokumen Pembantaran Penahanan (S-23) telah dihubungkan otomatis.
                    </div>
                @endif

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nama_rumah_sakit">Rumah Sakit Tujuan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nama_rumah_sakit" type="text" class="form-control @error('nama_rumah_sakit') is-invalid @enderror" name="nama_rumah_sakit"
                            value="{{ old('nama_rumah_sakit', $document->nama_rumah_sakit) }}" placeholder="Contoh: RSUD Djasamen Saragih / RS Bhayangkara" required>
                        @error('nama_rumah_sakit')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="kota_rumah_sakit">Kota / Kabupaten RS<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="kota_rumah_sakit" type="text" class="form-control @error('kota_rumah_sakit') is-invalid @enderror" name="kota_rumah_sakit"
                            value="{{ old('kota_rumah_sakit', $document->kota_rumah_sakit ?: $defaultLocation) }}" placeholder="Contoh: Kabupaten Simalungun / Pematangsiantar" required>
                        @error('kota_rumah_sakit')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>
                <h5 class="fw-bold text-blue-dark">Identitas Korban (Pemeriksaan Visum)</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="select_korban_visum">Pilih Korban dari Data Perkara</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select id="select_korban_visum" class="form-control select2">
                            <option value="">-- Pilih Korban / Pihak Terlibat --</option>
                            @if(isset($involvedList) && $involvedList->count() > 0)
                                <optgroup label="Pihak Terlibat / Korban (Involved Peoples)">
                                    @foreach($involvedList as $inv)
                                        <option value="{{ $inv->id }}"
                                            data-name="{{ $inv->name }}"
                                            data-nik="{{ $inv->identity_number ?? ($inv->nik ?? '') }}"
                                            data-gender="{{ $inv->gender->name ?? ($inv->gender_id == 1 ? 'Laki-laki' : ($inv->gender_id == 2 ? 'Perempuan' : '')) }}"
                                            data-nationality="{{ $inv->nationality ?? 'Indonesia' }}"
                                            data-birthplace="{{ $inv->birth_place }}"
                                            data-birthdate="{{ $inv->birth_date }}"
                                            data-job="{{ $inv->job->name ?? '' }}"
                                            data-religion="{{ $inv->religion->name ?? '' }}"
                                            data-address="{{ $inv->address }}">
                                            {{ $inv->name }} ({{ $inv->class ?? 'KORBAN' }}) - NIK: {{ $inv->identity_number ?? ($inv->nik ?? '-') }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif

                            @if(isset($suspectList) && $suspectList->count() > 0)
                                <optgroup label="Tersangka (Jika Pemeriksaan Visum Psikiatrikum)">
                                    @foreach($suspectList as $susp)
                                        <option value="{{ $susp->id }}"
                                            data-name="{{ $susp->name }}"
                                            data-nik="{{ $susp->identity_number ?? ($susp->nik ?? '') }}"
                                            data-gender="{{ $susp->gender->name ?? ($susp->gender_short_name ?? '') }}"
                                            data-nationality="{{ $susp->nationality ?? 'Indonesia' }}"
                                            data-birthplace="{{ $susp->birth_place }}"
                                            data-birthdate="{{ $susp->birth_date }}"
                                            data-job="{{ $susp->occupation ?? ($susp->job->name ?? '') }}"
                                            data-religion="{{ $susp->religion_short_name ?? ($susp->religion->name ?? '') }}"
                                            data-address="{{ $susp->address }}">
                                            {{ $susp->name }} (TERSANGKA) - NIK: {{ $susp->identity_number ?? ($susp->nik ?? '-') }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                            <option value="manual">-- Input Manual / Korban Baru --</option>
                        </select>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="visum_nama_korban">Nama Lengkap Korban<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="visum_nama_korban" type="text" class="form-control @error('nama_korban') is-invalid @enderror" name="nama_korban"
                            value="{{ old('nama_korban', $document->nama_korban) }}" placeholder="Nama Lengkap Korban" required>
                        @error('nama_korban')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="visum_nik_korban">Nomor Identitas (NIK)<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="visum_nik_korban" type="text" class="form-control" name="nik_korban"
                            value="{{ old('nik_korban', $document->nik_korban) }}" placeholder="Nomor KTP / NIK" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="visum_jenis_kelamin_korban">Jenis Kelamin<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select id="visum_jenis_kelamin_korban" name="jenis_kelamin_korban" class="form-control select2" required>
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="laki-laki" {{ old('jenis_kelamin_korban', strtolower($document->jenis_kelamin ?? '')) == 'laki-laki' || old('jenis_kelamin_korban', strtolower($document->jenis_kelamin ?? '')) == 'l' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="perempuan" {{ old('jenis_kelamin_korban', strtolower($document->jenis_kelamin ?? '')) == 'perempuan' || old('jenis_kelamin_korban', strtolower($document->jenis_kelamin ?? '')) == 'p' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="visum_kewarganegaraan_korban">Kewarganegaraan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="visum_kewarganegaraan_korban" type="text" class="form-control" name="kewarganegaraan_korban"
                            value="{{ old('kewarganegaraan_korban', $document->kewarganegaraan ?: 'Indonesia') }}" placeholder="Indonesia" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="visum_agama_korban">Agama<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="visum_agama_korban" type="text" class="form-control" name="agama_korban"
                            value="{{ old('agama_korban', $document->agama) }}" placeholder="Contoh: Islam / Kristen" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="visum_tempat_lahir_korban">Tempat Lahir<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="visum_tempat_lahir_korban" type="text" class="form-control" name="tempat_lahir_korban"
                            value="{{ old('tempat_lahir_korban', $document->tempat_lahir) }}" placeholder="Tempat Lahir" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="visum_tanggal_lahir_korban">Tanggal Lahir<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input id="visum_tanggal_lahir_korban" type="text" class="form-control" name="tanggal_lahir_korban"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('tanggal_lahir_korban', $document->tanggal_lahir ? \Carbon\Carbon::parse($document->tanggal_lahir)->format('Y-m-d') : '') }}" data-provide="datepicker" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="visum_pekerjaan_korban">Pekerjaan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="visum_pekerjaan_korban" type="text" class="form-control" name="pekerjaan_korban"
                            value="{{ old('pekerjaan_korban', $document->pekerjaan) }}" placeholder="Pekerjaan" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="visum_alamat_korban">Alamat Tempat Tinggal<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <textarea id="visum_alamat_korban" class="form-control" name="alamat_korban" rows="3"
                            placeholder="Alamat lengkap tempat tinggal korban..." required>{{ old('alamat_korban', $document->alamat) }}</textarea>
                    </div>
                </div>

                <hr>
                <h5 class="fw-bold text-blue-dark">Kronologis & Riwayat Sebelum Dibawa ke RS</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="dugaan_tindak_pidana">Dugaan Tindak Pidana<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="dugaan_tindak_pidana" type="text" class="form-control" name="dugaan_tindak_pidana"
                            value="{{ old('dugaan_tindak_pidana', $document->dugaan_tindak_pidana ?: 'kecelakaan lalu lintas') }}" placeholder="Contoh: kecelakaan lalu lintas" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="pasal_diduga">Pasal yang Diduga<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="pasal_diduga" type="text" class="form-control" name="pasal_diduga"
                            value="{{ old('pasal_diduga', $document->pasal_diduga ?: 'Pasal 310 ayat (4) UU No. 22 Tahun 2009') }}" placeholder="Pasal terkait" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="jenis_luka">Jenis Pemeriksaan / Luka<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select id="jenis_luka" name="jenis_luka" class="form-control select2" required>
                            <option value="fisik" {{ old('jenis_luka', $document->jenis_luka) == 'fisik' ? 'selected' : '' }}>Luka Fisik (Visum et Repertum)</option>
                            <option value="psikis" {{ old('jenis_luka', $document->jenis_luka) == 'psikis' ? 'selected' : '' }}>Luka Psikis (Visum et Psikiatrikum)</option>
                            <option value="fisik dan psikis" {{ old('jenis_luka', $document->jenis_luka) == 'fisik dan psikis' ? 'selected' : '' }}>Fisik dan Psikis</option>
                        </select>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="penemu">Ditemukan Oleh<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="penemu" type="text" class="form-control" name="penemu"
                            value="{{ old('penemu', $document->penemu ?: 'Petugas Kepolisian') }}" placeholder="Polisi / Masyarakat" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="lokasi_temu">Lokasi Ditemukan (TKP)<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="lokasi_temu" type="text" class="form-control" name="lokasi_temu"
                            value="{{ old('lokasi_temu', $document->lokasi_temu ?: ($accident->road_name ?? ($accident->place ?? 'TKP Kejadian'))) }}" placeholder="Lokasi TKP Kejadian" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Waktu Laporan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-12 mb-2 mb-md-0">
                        <input id="hari_lapor" type="text" class="form-control" name="hari_lapor"
                            value="{{ old('hari_lapor', $document->hari_lapor ?: ($accident->report_date ? \Carbon\Carbon::parse($accident->report_date)->locale('id')->translatedFormat('l') : 'Senin')) }}" placeholder="Hari Lapor" required>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                        <input id="tgl_lapor" type="text" class="form-control" name="tgl_lapor" data-provide="datepicker"
                            value="{{ old('tgl_lapor', $document->tanggal_lapor ? \Carbon\Carbon::parse($document->tanggal_lapor)->format('Y-m-d') : ($accident->report_date ? \Carbon\Carbon::parse($accident->report_date)->format('Y-m-d') : date('Y-m-d'))) }}" placeholder="YYYY-MM-DD" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Waktu Temu Korban<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-12 mb-2 mb-md-0">
                        <input id="hari_temu" type="text" class="form-control" name="hari_temu"
                            value="{{ old('hari_temu', $document->hari_temu ?: ($accident->accident_date ? \Carbon\Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('l') : 'Senin')) }}" placeholder="Hari Temu" required>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                        <input id="tgl_temu" type="text" class="form-control" name="tgl_temu" data-provide="datepicker"
                            value="{{ old('tgl_temu', $document->tanggal_temu ? \Carbon\Carbon::parse($document->tanggal_temu)->format('Y-m-d') : ($accident->accident_date ? \Carbon\Carbon::parse($accident->accident_date)->format('Y-m-d') : date('Y-m-d'))) }}" placeholder="YYYY-MM-DD" required>
                    </div>
                </div>

                <hr>
                <h5 class="fw-bold text-blue-dark">Kontak Penyidik, Pejabat Penandatangan & Tanda Terima RS</h5>

                {{-- 1. Pejabat Penandatangan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="select_pejabat_penandatangan">Pilih Pejabat Penandatangan</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select id="select_pejabat_penandatangan" class="form-control select2">
                            <option value="">-- Pilih Pejabat Penandatangan dari Sistem --</option>
                            @if(isset($authorizedSignatories) && $authorizedSignatories->count() > 0)
                                @foreach ($authorizedSignatories as $signatory)
                                    <option value="{{ $signatory->id }}"
                                        data-name="{{ $signatory->full_name }}"
                                        data-rank="{{ $signatory->rank->name ?? ($signatory->rank_id ?? 'AKP') }}"
                                        data-nrp="{{ $signatory->register_number ?? '' }}"
                                        data-position="{{ $signatory->position->name ?? ($signatory->position_id ?? '') }}">
                                        {{ $signatory->full_name }} ({{ $signatory->register_number ?? '-' }}) - {{ $signatory->position->name ?? ($signatory->position_id ?? 'Pejabat Penandatangan') }}
                                    </option>
                                @endforeach
                            @endif
                            <option value="manual">-- Input Manual / Pejabat Lain --</option>
                        </select>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nama_penandatangan">Nama Pejabat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nama_penandatangan" type="text" class="form-control @error('nama_penandatangan') is-invalid @enderror" name="nama_penandatangan"
                            value="{{ old('nama_penandatangan', $document->nama_penandatangan) }}" placeholder="Nama Kasat Lantas / Kanit Gakkum" required>
                        @error('nama_penandatangan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Pangkat & NRP Pejabat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-12 mb-2 mb-md-0">
                        <input id="pangkat_penandatangan" type="text" class="form-control" name="pangkat_penandatangan"
                            value="{{ old('pangkat_penandatangan', $document->pangkat_penandatangan) }}" placeholder="Pangkat (contoh: AKP)" required>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                        <input id="nrp_penandatangan" type="text" class="form-control" name="nrp_penandatangan"
                            value="{{ old('nrp_penandatangan', $document->nrp_penandatangan) }}" placeholder="NRP" required>
                    </div>
                </div>

                {{-- 2. Penyidik Penghubung --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="select_penyidik_kontak">Pilih Penyidik Penghubung</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select id="select_penyidik_kontak" class="form-control select2">
                            <option value="">-- Pilih Penyidik / Personil dari Sistem --</option>
                            @if(isset($memberOfficers) && $memberOfficers->count() > 0)
                                @foreach ($memberOfficers as $officer)
                                    <option value="{{ $officer->id }}"
                                        data-name="{{ $officer->full_name }}"
                                        data-phone="{{ $officer->phone_number ?? '' }}"
                                        data-nrp="{{ $officer->register_number ?? '' }}"
                                        data-rank="{{ $officer->rank->name ?? ($officer->rank_id ?? '') }}">
                                        {{ $officer->full_name }} (NRP: {{ $officer->register_number ?? '-' }})
                                    </option>
                                @endforeach
                            @endif
                            <option value="manual">-- Input Manual / Penyidik Lain --</option>
                        </select>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nama_kontak">Nama Penyidik Penghubung<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nama_kontak" type="text" class="form-control @error('nama_kontak') is-invalid @enderror" name="nama_kontak"
                            value="{{ old('nama_kontak', $document->nama_kontak) }}" placeholder="Nama Penyidik" required>
                        @error('nama_kontak')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="hp_kontak">No. HP Penyidik<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="hp_kontak" type="text" class="form-control" name="hp_kontak"
                            value="{{ old('hp_kontak', $document->hp_kontak) }}" placeholder="08xxxxxxxxxx" required>
                    </div>
                </div>

                {{-- 3. Tanda Terima Serah Terima di RS --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="petugas_penyerah">Petugas Penyerah Korban<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="petugas_penyerah" type="text" class="form-control" name="petugas_penyerah"
                            value="{{ old('petugas_penyerah', $document->petugas_penyerah) }}" placeholder="Petugas Penyerah Korban" required>
                        <small class="text-muted">Petugas kepolisian yang menyerahkan korban ke pihak Rumah Sakit.</small>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="petugas_rs">Petugas Penerima di RS<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="petugas_rs" type="text" class="form-control" name="petugas_rs"
                            value="{{ old('petugas_rs', $document->petugas_rs) }}" placeholder="Nama Dokter / Petugas Medis RS" required>
                        <small class="text-muted">Dokter / petugas medis penerima jenazah/korban di Rumah Sakit.</small>
                    </div>
                </div>

                <hr>

                <div class="text-center my-4">
                    <button type="submit" class="btn btn-dark-blue me-2" id="btnDownloadWord">
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
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script>
        $(document).ready(function() {
            // Animasi Attention Box
            setInterval(function() {
                $('#attentionBox').toggleClass('alert-danger alert-warning');
            }, 1000);

            // Inisialisasi Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Inisialisasi Datepicker
            $('[data-provide="datepicker"]').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });

            $('[data-provide="datepicker"]').keydown(function(e) {
                e.preventDefault();
                return false;
            });

            // Auto-Fill Korban saat opsi dipilih
            $('#select_korban_visum').on('change select2:select', function() {
                var $opt = $(this).find('option:selected');
                var val = $(this).val();

                if (val && val !== 'manual') {
                    $('#visum_nama_korban').val($opt.data('name') || '');
                    $('#visum_nik_korban').val($opt.data('nik') || '');
                    
                    var gender = ($opt.data('gender') || '').toString().toLowerCase();
                    if (gender.includes('perempuan') || gender === 'p' || gender === '2') {
                        $('#visum_jenis_kelamin_korban').val('perempuan').trigger('change');
                    } else {
                        $('#visum_jenis_kelamin_korban').val('laki-laki').trigger('change');
                    }

                    $('#visum_kewarganegaraan_korban').val($opt.data('nationality') || 'Indonesia');
                    $('#visum_tempat_lahir_korban').val($opt.data('birthplace') || '');
                    $('#visum_tanggal_lahir_korban').val($opt.data('birthdate') || '');
                    $('#visum_pekerjaan_korban').val($opt.data('job') || '');
                    $('#visum_agama_korban').val($opt.data('religion') || '');
                    $('#visum_alamat_korban').val($opt.data('address') || '');
                } else if (val === 'manual') {
                    $('#visum_nama_korban').val('');
                    $('#visum_nik_korban').val('');
                    $('#visum_tempat_lahir_korban').val('');
                    $('#visum_tanggal_lahir_korban').val('');
                    $('#visum_pekerjaan_korban').val('');
                    $('#visum_agama_korban').val('');
                    $('#visum_alamat_korban').val('');
                }
            });

            // Auto-Fill Pejabat Penandatangan saat opsi dipilih
            $('#select_pejabat_penandatangan').on('change select2:select', function() {
                var $opt = $(this).find('option:selected');
                var val = $(this).val();

                if (val && val !== 'manual') {
                    $('#nama_penandatangan').val($opt.data('name') || '');
                    $('#pangkat_penandatangan').val($opt.data('rank') || '');
                    $('#nrp_penandatangan').val($opt.data('nrp') || '');
                    clearFieldError('#nama_penandatangan');
                    clearFieldError('#pangkat_penandatangan');
                    clearFieldError('#nrp_penandatangan');
                } else if (val === 'manual') {
                    $('#nama_penandatangan').val('');
                    $('#pangkat_penandatangan').val('');
                    $('#nrp_penandatangan').val('');
                }
            });

            // Auto-Fill Penyidik Penghubung saat opsi dipilih
            $('#select_penyidik_kontak').on('change select2:select', function() {
                var $opt = $(this).find('option:selected');
                var val = $(this).val();

                if (val && val !== 'manual') {
                    var name = $opt.data('name') || '';
                    $('#nama_kontak').val(name);
                    $('#hp_kontak').val($opt.data('phone') || '');
                    clearFieldError('#nama_kontak');
                    clearFieldError('#hp_kontak');
                } else if (val === 'manual') {
                    $('#nama_kontak').val('');
                    $('#hp_kontak').val('');
                }
            });

            // =========================================================================
            // VALIDASI FORM & AUTO-FOCUS KE FIELD KOSONG (KONSISTEN DENGAN S-22 & S-23)
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

            // Validasi Submit Form Visum
            $('#formSuratVisum').on('submit', function(e) {
                e.preventDefault();

                var $btn = $('#btnDownloadWord');
                var originalHtml = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memvalidasi...');

                // Bersihkan pesan error sebelumnya
                $('.is-invalid').removeClass('is-invalid');
                $('.border.border-danger').removeClass('border border-danger');
                $('.select2-selection').removeClass('border border-danger is-invalid');
                $('.frontend-error').remove();
                $('.invalid-feedback').remove();

                var errors = [];

                // 1. Informasi Dasar Dokumen & Rumah Sakit
                checkInput('#nomor_lp', 'Nomor LP', errors);
                checkInput('#nomor_surat', 'Nomor Dokumen', errors);
                checkInput('#tanggal_surat', 'Tanggal Surat', errors);
                checkInput('#dikeluarkan_di', 'Dikeluarkan di', errors);
                checkInput('#satker_penyidik', 'Satuan Kerja Penyidik', errors);
                checkSelect('#klasifikasi_surat', 'Klasifikasi Surat', errors);
                checkInput('#nama_rumah_sakit', 'Rumah Sakit Tujuan', errors);
                checkInput('#kota_rumah_sakit', 'Kota / Kabupaten RS', errors);

                // 2. Identitas Korban
                checkInput('#visum_nama_korban', 'Nama Lengkap Korban', errors);
                checkInput('#visum_nik_korban', 'Nomor Identitas (NIK)', errors);
                checkSelect('#visum_jenis_kelamin_korban', 'Jenis Kelamin', errors);
                checkInput('#visum_kewarganegaraan_korban', 'Kewarganegaraan', errors);
                checkInput('#visum_agama_korban', 'Agama', errors);
                checkInput('#visum_tempat_lahir_korban', 'Tempat Lahir', errors);
                checkInput('#visum_tanggal_lahir_korban', 'Tanggal Lahir', errors);
                checkInput('#visum_pekerjaan_korban', 'Pekerjaan', errors);
                checkInput('#visum_alamat_korban', 'Alamat Tempat Tinggal', errors);

                // 3. Kronologis & Riwayat Sebelum ke RS
                checkInput('#dugaan_tindak_pidana', 'Dugaan Tindak Pidana', errors);
                checkInput('#pasal_diduga', 'Pasal yang Diduga', errors);
                checkSelect('#jenis_luka', 'Jenis Pemeriksaan / Luka', errors);
                checkInput('#penemu', 'Ditemukan Oleh', errors);
                checkInput('#lokasi_temu', 'Lokasi Ditemukan (TKP)', errors);
                checkInput('#hari_lapor', 'Hari Laporan', errors);
                checkInput('#tgl_lapor', 'Tanggal Laporan', errors);
                checkInput('#hari_temu', 'Hari Temu Korban', errors);
                checkInput('#tgl_temu', 'Tanggal Temu Korban', errors);

                // 4. Pejabat Penandatangan, Narahubung & Tanda Terima RS
                checkInput('#nama_penandatangan', 'Nama Pejabat Penandatangan', errors);
                checkInput('#pangkat_penandatangan', 'Pangkat Pejabat', errors);
                checkInput('#nrp_penandatangan', 'NRP Pejabat', errors);
                checkInput('#nama_kontak', 'Nama Penyidik Penghubung', errors);
                checkInput('#hp_kontak', 'No. HP Penyidik', errors);
                checkInput('#petugas_penyerah', 'Petugas Penyerah Korban', errors);
                checkInput('#petugas_rs', 'Petugas Penerima di RS', errors);

                // Jika ada field kosong di frontend, tampilkan SweetAlert lalu auto-scroll & fokus ke field pertama
                if (errors.length > 0) {
                    $btn.prop('disabled', false).html(originalHtml);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Formulir Belum Lengkap',
                            text: 'Terdapat ' + errors.length + ' bidang yang wajib diisi dan belum lengkap. Mohon periksa kembali isian form Anda.',
                            confirmButtonText: 'Periksa Isian'
                        }).then(function() {
                            scrollToFirstError();
                        });
                    }
                    scrollToFirstError();
                    return false;
                }

                $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
                this.submit();

                setTimeout(function() {
                    $btn.prop('disabled', false).html(originalHtml);
                }, 4000);
            });
        });
    </script>
@endpush
