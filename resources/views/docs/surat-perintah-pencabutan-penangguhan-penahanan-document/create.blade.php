@php
    $_title = 'Surat Perintah Pencabutan Penangguhan Penahanan (S-19)';
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
            <h5 class="fw-bold text-blue-dark">Surat Perintah Pencabutan Penangguhan Penahanan (S-19 / SPRIN CABUT GUHAN)</h5>
            <small class="text-muted d-block mb-3">Kode Dokumen: <b>s19</b> │ Kategori: <b>0604</b></small>

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
            <form action="{{ route('doc.surat-perintah-pencabutan-penangguhan-penahanan-document.store', ['accident_id' => $accidentId]) }}"
                method="POST" id="suratPerintahPencabutanPenangguhanPenahananForm" novalidate>
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

                {{-- Nomor Dokumen S-19 --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nomor">Nomor Dokumen S-19<span class="text-danger fs-5">*</span>
                        <small class="text-muted d-block font-weight-normal">(Nomor Surat Perintah Pencabutan Penangguhan Penahanan)</small>
                    </label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="nomor" type="text"
                            class="form-control @error('nomor') is-invalid @enderror font-weight-bold"
                            name="nomor" value="{{ old('nomor') }}" required
                            placeholder="Contoh: SP.CabutGuhan/01/X/2026/Reskrim">
                        @error('nomor')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Tanggal Dokumen S-19 --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggal">Tanggal S-19<span class="text-danger fs-5">*</span></label>
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
                            value="{{ old('nomor_surat_perintah_penahanan', $suratPerintahPenahanan->nomor ?? $suratPerintahPenahanan->document_number ?? '') }}"
                            placeholder="Contoh: SP.Han/01/X/2026/Reskrim" readonly style="background-color: #e9ecef;">
                        <small class="text-muted">(*Nomor Surat Perintah Penahanan diambil otomatis dari dokumen S-17 perkara ini)</small>
                        @error('nomor_surat_perintah_penahanan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Nomor Surat Perintah Penangguhan Penahanan (S-18) (Read Only) --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nomor_surat_perintah_penangguhan">Nomor Surat Perintah Penangguhan (S-18)<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="nomor_surat_perintah_penangguhan" type="text"
                            class="form-control @error('nomor_surat_perintah_penangguhan') is-invalid @enderror font-weight-bold"
                            name="nomor_surat_perintah_penangguhan"
                            value="{{ old('nomor_surat_perintah_penangguhan', $suratPerintahPenangguhan->nomor ?? $suratPerintahPenangguhan->document_number ?? '') }}"
                            placeholder="Contoh: SP.Guhan/01/X/2026/Reskrim" readonly style="background-color: #e9ecef;">
                        <small class="text-muted">(*Nomor Surat Perintah Penangguhan Penahanan diambil otomatis dari dokumen S-18 yang dicabut)</small>
                        @error('nomor_surat_perintah_penangguhan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                {{-- 2. KETENTUAN PENEMPATAN & SISA WAKTU MASA PENAHANAN --}}
                <h5 class="fw-bold text-blue-dark">2. Ketentuan Penempatan & Sisa Waktu Masa Penahanan</h5>

                {{-- Jenis Penahanan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="kode_jenis_penahanan">Jenis Penahanan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="kode_jenis_penahanan" id="kode_jenis_penahanan" required>
                            @foreach ($masterJenisPenahanan as $val => $lbl)
                                <option value="{{ $val }}" {{ old('kode_jenis_penahanan', 1) == $val ? 'selected' : '' }}>
                                    {{ $val . ' - ' . $lbl }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">(*1 = Rutan, 2 = Rumah, 3 = Kota)</small>
                    </div>
                </div>

                {{-- Dropdown Satker Rutan / Lapas (Khusus Jenis Penahanan 1 - Rutan) --}}
                <div class="input-group row mb-3 ms-0" id="rutanSection">
                    <label class="fw-bold col-sm-3 col-form-label" for="kode_satker_tempat_penahanan">Rutan / Lapas Tempat Penahanan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="kode_satker_tempat_penahanan" id="kode_satker_tempat_penahanan">
                            <option value="">-- Pilih Rutan / Tempat Penahanan --</option>
                            <option value="Rutan {{ $accident->polres->full_name ?? $accident->polres->name ?? 'Polres' }}"
                                {{ old('kode_satker_tempat_penahanan', 'Rutan ' . ($accident->polres->full_name ?? $accident->polres->name ?? 'Polres')) == ('Rutan ' . ($accident->polres->full_name ?? $accident->polres->name ?? 'Polres')) ? 'selected' : '' }}>
                                Rutan {{ $accident->polres->full_name ?? $accident->polres->name ?? 'Polres' }}
                            </option>
                            @foreach ($prisons as $p)
                                <option value="{{ $p->name }}" {{ old('kode_satker_tempat_penahanan') == $p->name ? 'selected' : '' }}>
                                    {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">(*Pilih Rutan tempat tersangka ditahan kembali)</small>
                    </div>
                </div>

                {{-- Tempat / Alamat Penahanan --}}
                <div class="input-group row mb-3 ms-0" id="lokasiPenahananSection">
                    <label class="fw-bold col-sm-3 col-form-label" id="label_tempat_penahanan" for="tempat_penahanan">
                        Tempat / Lokasi Penahanan<span class="text-danger fs-5">*</span>
                    </label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input type="text" class="form-control font-weight-bold" name="tempat_penahanan" id="tempat_penahanan"
                            placeholder="Ketik nama tempat atau alamat penahanan..."
                            value="{{ old('tempat_penahanan') }}">
                        <small class="text-muted" id="help_tempat_penahanan">(*Tempat penahanan tersangka saat ditahan kembali)</small>
                    </div>
                </div>

                {{-- Sisa Waktu Masa Penahanan (Hari) --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="jumlah_hari">Sisa Waktu Penahanan (Hari)<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="jumlah_hari" type="number" min="1" max="120"
                            class="form-control font-weight-bold"
                            name="jumlah_hari" value="{{ old('jumlah_hari', 20) }}" required>
                        <small class="text-muted">(*Jumlah sisa hari penahanan yang harus dijalani tersangka)</small>
                    </div>
                </div>

                {{-- Tanggal Mulai & Tanggal Akhir --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggal_mulai">Periode Penahanan Kembali<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-12">
                        <label class="small text-muted mb-1">Tanggal Mulai:</label>
                        <input class="form-control @error('tanggal_mulai') is-invalid @enderror" id="tanggal_mulai" name="tanggal_mulai"
                            placeholder="YYYY-MM-DD" autocomplete="off" value="{{ old('tanggal_mulai', date('Y-m-d')) }}"
                            data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-date-today-highlight="true" required>
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-6 col-12">
                        <label class="small text-muted mb-1">Tanggal Berakhir (s.d.):</label>
                        <input class="form-control @error('tanggal_akhir') is-invalid @enderror" id="tanggal_akhir" name="tanggal_akhir"
                            placeholder="YYYY-MM-DD" autocomplete="off" value="{{ old('tanggal_akhir', date('Y-m-d', strtotime('+19 days'))) }}"
                            data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-date-today-highlight="true" required>
                    </div>
                </div>

                {{-- Alasan Pencabutan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="alasan_pencabutan">Alasan Pencabutan Penangguhan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <textarea id="alasan_pencabutan" class="form-control" name="alasan_pencabutan" rows="2"
                            placeholder="Contoh: Tersangka melanggar persyaratan yang telah ditetapkan dan/atau tidak mematuhi kewajiban wajib lapor.">{{ old('alasan_pencabutan', 'tersangka melanggar persyaratan yang telah ditetapkan') }}</textarea>
                    </div>
                </div>

                <hr class="my-4">

                {{-- 3. DAFTAR TERSANGKA --}}
                <h5 class="fw-bold text-blue-dark">3. Tersangka yang Dicabut Penangguhannya<span class="text-danger fs-5">*</span></h5>

                @if ($suspects->count() == 0)
                    <div class="alert alert-warning" role="alert">
                        Belum ada Tersangka yang terdaftar pada perkara ini. Silahkan tambahkan Tersangka pada menu Progress Perkara terlebih dahulu.
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
                                @foreach ($suspects as $idx => $suspect)
                                    @php
                                        $age = $suspect->age ?? ($suspect->birth_date ? \Carbon\Carbon::parse($suspect->birth_date)->age : '-');
                                    @endphp
                                    <tr>
                                        <td class="text-center align-middle">
                                            <input type="checkbox" name="suspects[]" value="{{ $suspect->id }}"
                                                class="suspect-checkbox" id="suspect_{{ $suspect->id }}"
                                                data-name="{{ $suspect->name }}"
                                                data-address="{{ $suspect->address ?? '' }}"
                                                {{ (is_array(old('suspects')) && in_array($suspect->id, old('suspects'))) || ($idx === 0 && !old('suspects')) ? 'checked' : '' }}>
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

                {{-- 4. PETUGAS YANG DIPERINTAHKAN --}}
                <h5 class="fw-bold text-blue-dark">4. Petugas yang Diperintahkan</h5>

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

                {{-- 5. PEJABAT PENANDATANGAN --}}
                <h5 class="fw-bold text-blue-dark">5. Pejabat Penandatangan</h5>

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
                        <small class="text-muted">(*Pejabat yang berwenang menandatangani Surat Perintah Pencabutan Penangguhan Penahanan)</small>
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
            $('#tanggal, #tanggal_mulai, #tanggal_akhir').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                orientation: 'auto bottom'
            }).on('changeDate', function() {
                $(this).trigger('change');
            });

            // Auto calculate tanggal_akhir when tanggal_mulai or jumlah_hari changes
            function calculateTanggalAkhir() {
                var startVal = $('#tanggal_mulai').val();
                var days = parseInt($('#jumlah_hari').val(), 10);
                if (startVal && !isNaN(days) && days > 0) {
                    var startDate = new Date(startVal);
                    if (!isNaN(startDate.getTime())) {
                        startDate.setDate(startDate.getDate() + (days - 1));
                        var yyyy = startDate.getFullYear();
                        var mm = String(startDate.getMonth() + 1).padStart(2, '0');
                        var dd = String(startDate.getDate()).padStart(2, '0');
                        $('#tanggal_akhir').val(yyyy + '-' + mm + '-' + dd).datepicker('update');
                    }
                }
            }
            $('#jumlah_hari, #tanggal_mulai').on('change input', calculateTanggalAkhir);

            // Helper untuk mendapatkan alamat tersangka yang dicentang
            function getSelectedSuspectAddress() {
                var $checked = $('.suspect-checkbox:checked').first();
                if ($checked.length) {
                    return $checked.data('address') || '';
                }
                return '';
            }

            var defaultPolresRutan = "Rutan {{ $accident->polres->full_name ?? $accident->polres->name ?? 'Polres' }}";
            var defaultCity = "Kota {{ $accident->polres->city ?? $accident->polres->name ?? '' }}";

            // Toggle tampilan Tempat / Lokasi Penahanan berdasarkan Jenis Penahanan
            function toggleJenisPenahanan(isInitialLoad) {
                var jenis = $('#kode_jenis_penahanan').val();
                var currentVal = $('#tempat_penahanan').val() ? $('#tempat_penahanan').val().trim() : '';

                if (jenis == '1') {
                    // 1 - Penahanan Rutan
                    $('#rutanSection').slideDown();
                    $('#label_tempat_penahanan').html('Keterangan / Nama Rutan <small class="text-muted font-weight-normal">(Opsional / Kustom)</small>');
                    $('#tempat_penahanan').attr('placeholder', 'Atau ketik kustom nama tempat penahanan (Contoh: ' + defaultPolresRutan + ')');
                    $('#help_tempat_penahanan').text('(*Nama Rutan tempat penahanan tersangka saat ditahan kembali)');

                    if (!isInitialLoad && !$('#kode_satker_tempat_penahanan').val()) {
                        $('#kode_satker_tempat_penahanan').val(defaultPolresRutan).trigger('change');
                    }
                } else if (jenis == '2') {
                    // 2 - Penahanan Rumah
                    $('#rutanSection').slideUp();
                    $('#kode_satker_tempat_penahanan').val('').trigger('change');

                    $('#label_tempat_penahanan').html('Alamat Rumah Tempat Tinggal<span class="text-danger fs-5">*</span>');
                    $('#tempat_penahanan').attr('placeholder', 'Contoh: Jl. ..., Kel. ..., Kec. ...');
                    $('#help_tempat_penahanan').text('(*Alamat rumah tinggal / kediaman tersangka tempat penahanan rumah dijalani)');

                    if (!isInitialLoad || !currentVal) {
                        var suspectAddress = getSelectedSuspectAddress();
                        if (suspectAddress && (!currentVal || currentVal.toLowerCase().indexOf('rutan') !== -1 || currentVal.toLowerCase().indexOf('lapas') !== -1 || currentVal.toLowerCase().indexOf('kota') === 0)) {
                            $('#tempat_penahanan').val(suspectAddress);
                        }
                    }
                } else if (jenis == '3') {
                    // 3 - Penahanan Kota
                    $('#rutanSection').slideUp();
                    $('#kode_satker_tempat_penahanan').val('').trigger('change');

                    $('#label_tempat_penahanan').html('Wilayah Kota Tempat Penahanan<span class="text-danger fs-5">*</span>');
                    $('#tempat_penahanan').attr('placeholder', 'Contoh: ' + defaultCity);
                    $('#help_tempat_penahanan').text('(*Wilayah kota tempat tersangka menjalani penahanan kota)');

                    if (!isInitialLoad || !currentVal) {
                        if (!currentVal || currentVal.toLowerCase().indexOf('rutan') !== -1 || currentVal.toLowerCase().indexOf('lapas') !== -1) {
                            $('#tempat_penahanan').val(defaultCity);
                        }
                    }
                }
            }

            // Sync pilihan Rutan ke input tempat_penahanan (khusus jenis 1)
            $('#kode_satker_tempat_penahanan').on('change', function() {
                var jenis = $('#kode_jenis_penahanan').val();
                if (jenis == '1') {
                    var rutanVal = $(this).val();
                    if (rutanVal) {
                        $('#tempat_penahanan').val(rutanVal);
                    }
                }
            });

            // Saat jenis penahanan berubah
            $('#kode_jenis_penahanan').on('change', function() {
                toggleJenisPenahanan(false);
            });

            // Saat tersangka dicentang/berubah
            $(document).on('change', '.suspect-checkbox', function() {
                var jenis = $('#kode_jenis_penahanan').val();
                if (jenis == '2') {
                    var addr = getSelectedSuspectAddress();
                    if (addr) {
                        $('#tempat_penahanan').val(addr);
                    }
                }
            });

            // Jalankan inisialisasi pada load awal
            toggleJenisPenahanan(true);

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

            // Handle Submit Form with Inline markError & AJAX Validation
            $('#btnSubmitForm').on('click', function(e) {
                e.preventDefault();

                // Bersihkan error sebelumnya
                $('.frontend-error').remove();
                $('.is-invalid').removeClass('is-invalid');
                $('.border-danger').removeClass('border-danger');

                var errors = [];

                function scrollToFirstError() {
                    var $firstError = $('.is-invalid, .frontend-error').first();
                    if ($firstError.length) {
                        $('html, body').animate({
                            scrollTop: $firstError.offset().top - 120
                        }, 500);
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
                checkInput('#nomor', 'Nomor Dokumen S-19');
                checkInput('#tanggal', 'Tanggal S-19');
                checkInput('#nomor_spdp', 'Nomor SPDP');
                checkInput('#tanggal_spdp', 'Tanggal SPDP');
                checkInput('#kode_satker_penerbit_spdp', 'Kode Satker Penerbit SPDP');
                checkInput('#nomor_surat_perintah_penahanan', 'Nomor Surat Perintah Penahanan (S-17)');
                checkInput('#nomor_surat_perintah_penangguhan', 'Nomor Surat Perintah Penangguhan (S-18)');

                // 2. Validasi Ketentuan Penahanan
                checkSelect('#kode_jenis_penahanan', 'Jenis Penahanan');
                var currentJenis = $('#kode_jenis_penahanan').val();
                if (currentJenis == '1') {
                    var rutanSelect = $('#kode_satker_tempat_penahanan').val();
                    var rutanInput = $('#tempat_penahanan').val();
                    if (!rutanSelect && !rutanInput) {
                        markError('#kode_satker_tempat_penahanan', 'Rutan / Tempat Penahanan harus dipilih atau diisi');
                    }
                } else if (currentJenis == '2') {
                    checkInput('#tempat_penahanan', 'Alamat Rumah Tempat Tinggal');
                } else if (currentJenis == '3') {
                    checkInput('#tempat_penahanan', 'Wilayah Kota Tempat Penahanan');
                }

                // 3. Validasi Tersangka minimal 1
                if ($('.suspect-checkbox:checked').length === 0) {
                    markError('#suspectTable', 'Tersangka yang Dicabut Penangguhannya harus dipilih minimal 1 orang');
                }

                // 4. Validasi Ketua Tim & Pejabat Penandatangan
                checkSelect('#officerLeader', 'Ketua Tim');
                checkSelect('#signatory', 'Pejabat Penandatangan');

                // Jika terdapat error di sisi frontend, scroll ke elemen pertama dan batalkan submit
                if (errors.length > 0) {
                    scrollToFirstError();
                    return false;
                }

                // Validasi AJAX ke server
                $.ajax({
                    url: "{{ route('doc.surat-perintah-pencabutan-penangguhan-penahanan-document.api.validate-request-form', ['accident_id' => $accidentId]) }}",
                    type: 'POST',
                    dataType: 'json',
                    data: $('#suratPerintahPencabutanPenangguhanPenahananForm').serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: response.message || 'Silahkan menunggu proses simpan data',
                                icon: 'success',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                $('#suratPerintahPencabutanPenangguhanPenahananForm')[0].submit();
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
