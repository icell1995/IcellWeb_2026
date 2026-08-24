@php
    $_title = 'LHGP';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet"
        href="https://www.jqueryscript.net/demo/Time-Selection-Popover-jQuery-Timepicker/dist/css/timepicker.css">
    <link href="{{ asset('libs/bootstrap-duallistbox/bootstrap-duallistbox.css') }}" rel="stylesheet">
@endpush

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"><i
            class="bi bi-arrow-left"></i> Kembali ke Produktivitas</a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Tambah Laporan Hasil Gelar Perkara (LHGP)</h5>

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

            <!-- error alert -->
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
            <form action="{{ route('doc.laporan-hasil-gelar-perkara-document.store', ['accident_id' => $accidentId]) }}"
                method="POST" enctype="multipart/form-data" id="laporanHasilGelarPerkaraForm" novalidate>
                @csrf
                <input type="hidden" name="accidentId" id="accidentId" value="{{ $accidentId }}">

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="accidentNumber">Nomor LP</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="accidentNumber" type="text"
                            class="form-control @error('accidentNumber') is-invalid @enderror font-weight-bold"
                            name="accidentNumber" value="{{ $accident->no_lp }}" required placeholder="" readonly>
                        @error('accidentNumber')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="suratPerintahPenyidikanDocument">No Sprindik<span class="text-danger fs-5">*</span>
                    </label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2" name="suratPerintahPenyidikanDocument"
                            id="suratPerintahPenyidikanDocument">
                            <option value="">--Pilih No Surat Perintah Penyidikan--</option>
                            @foreach ($suratPerintahPenyidikanDocuments as $suratPerintahPenyidikanDocument)
                                <option value="{{ $suratPerintahPenyidikanDocument->id }}">
                                    {{ $suratPerintahPenyidikanDocument->document_number }}</option>
                            @endforeach
                        </select>

                        @error('suratPerintahPenyidikanDocument')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Jenis LHGP<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <div class="d-flex mb-3">
                            <div class="form-check me-1">
                                <input class="form-check-input" type="radio" id="typeCommon" name="documentType"
                                    value="BIASA">
                                <label for="typeCommon">
                                    Biasa
                                    {{-- <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="tooltip"
                                        data-bs-placement="bottom" title="Jenis Gelar Perkara Biasa adalah ...">
                                        <i class="bi bi-info-circle"></i>
                                    </button> --}}
                                    <i class="text-warning ms-1 bi bi-info-circle" data-bs-toggle="tooltip"
                                        data-bs-placement="bottom" title="Jenis Gelar Perkara Biasa adalah ..."
                                        viewBox="0 0 20 20"></i>
                                    </button>
                                </label>
                            </div>
                            <div class="form-check ms-1">
                                <input class="form-check-input" type="radio" id="typeSpecial" name="documentType"
                                    value="KHUSUS">
                                <label for="typeSpecial">
                                    Khusus
                                    {{-- <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="tooltip"
                                        data-bs-placement="bottom" title="Jenis Gelar Perkara Khusus adalah ...">
                                        <i class="bi bi-info-circle"></i>
                                    </button> --}}
                                    <i class="text-warning ms-1 bi bi-info-circle" data-bs-toggle="tooltip"
                                        data-bs-placement="bottom" title="Jenis Gelar Perkara Khusus adalah ..."
                                        viewBox="0 0 20 20"></i>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="caseDegreeType">Jenis Gelar Perkara<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2" name="caseDegreeType" id="caseDegreeType">
                            <option value="">--Pilih Jenis Gelar Perkara--</option>
                        </select>

                        @error('caseDegreeType')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>

                <h5 class="fw-bold text-blue-dark">Pelaksanaan</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Ref Surat Undangan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input class="form-control" id="caseDegreeInviteReference" name="caseDegreeInviteReference"
                            placeholder="No Surat Undangan" value="{{ old('caseDegreeInviteReference') }}">

                        @error('caseDegreeInviteReference')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Tanggal Surat Undangan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input class="form-control" id="caseDegreeInviteDate" name="caseDegreeInviteDate"
                            placeholder="YYYY-MM-DD" autocomplete="off" value="{{ old('caseDegreeInviteDate') }}"
                            data-provide="datepicker">

                        @error('caseDegreeInviteDate')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Tanggal Pelaksanaan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input class="form-control" id="date" name="date" placeholder="YYYY-MM-DD"
                            autocomplete="off" value="{{ old('date') }}" data-provide="datepicker">

                        @error('date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Waktu Pelaksanaan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input class="form-control" id="time" name="time" placeholder="hh:mm"
                            autocomplete="off" value="{{ old('time') }}">

                        @error('time')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-sm-2">
                        <select class="form-control select2" name="timezone" id="timezone">
                            <option value="">--Pilih Zona Waktu--</option>
                            @foreach ($timezones as $timezone)
                                <option value="{{ $timezone->id }}">{{ $timezone->name }}</option>
                            @endforeach
                        </select>

                        @error('timezone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Tempat Pelaksanaan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input class="form-control" id="place" name="place" placeholder="Nama Tempat Pelaksanaan"
                            autocomplete="off" value="{{ old('place') }}">

                        @error('place')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Pimpinan Gelar Perkara<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input class="form-control" id="caseDegreeLeader" name="caseDegreeLeader"
                            placeholder="Contoh : AKP Budi Setiabudi, S.H." autocomplete="off"
                            value="{{ old('caseDegreeLeader') }}">

                        @error('caseDegreeLeader')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Jumlah Peserta<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input class="form-control onlyIntegerInput" id="attendees" name="attendees"
                            placeholder="Contoh : 5" autocomplete="off" value="{{ old('attendees') }}">

                        @error('attendees')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>

                <div id="recommendedSuspectSection" style="display: none;">
                    <h5 class="fw-bold text-blue-dark">Rekomendasi Data Tersangka</h5>

                    <!-- Penetapan Tersangka -->
                    <div id="suspectDeterminationSection" style="display: none;">
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label">Sumber Data Tersangka<span class="text-danger fs-5">*</span></label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                <select class="form-control select2" name="suspectDeterminationDataSource"
                                    id="suspectDeterminationDataSource">
                                    <option value="">--Pihak Terlibat--</option>
                                    @foreach ($suspectSources as $suspectSource)
                                        <option value="{{ $suspectSource->id }}">{{ $suspectSource->name }}</option>
                                    @endforeach
                                </select>

                                @error('suspectDeterminationDataSource')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label">Daftar Tersangka</label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                <div class="input-group">
                                    <select class="custom-select select2-input-group" id="suspectDeterminationOption"
                                        name="suspectDeterminationOption"
                                        aria-describedby="suspectDeterminationOptionAddButtton">
                                        <option value="">--Pilih Tersangka--</option>
                                    </select>
                                    <button class="btn btn-primary" type="button"
                                        id="suspectDeterminationOptionAddButtton"><i class="bi bi-plus-circle"></i>
                                        Tambah</button>
                                </div>
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="text-dark col-sm-12 col-form-label">Tersangka yang Direkomendasikan untuk
                                Ditetapkan Status Tersangkanya</label>
                            <div class="col-sm-12">
                                <table class="table table-bordered table-responsive-md" id="suspectDeterminationTable">
                                    <thead class="table-danger">
                                        <tr class="text-center">
                                            <th scope="col">Jenis / Nomor Identitas</th>
                                            <th scope="col">Nama</th>
                                            <th scope="col">Tempat / Tanggal Lahir</th>
                                            <th scope="col">Keterangan</th>
                                            <th scope="col">Opsi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Penahanan Tersangka -->
                    <div id="arrestSuspectSection" style="display: none;">
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label">Tersangka yang Direkomendasikan untuk Ditahan
                            </label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <select class="form-control select2-multiple" name="arrestSuspects[]" id="arrestSuspects"
                                    multiple="multiple"
                                    data-placeholder="--Tersangka yang Direkomendasikan untuk Ditahan--">
                                    @foreach ($arrerstedSuspects as $arrerstedSuspect)
                                        <option value="{{ $arrerstedSuspect->id }}">{{ $arrerstedSuspect->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('arrestSuspects')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Pencabutan Status Tersangka -->
                    <div id="suspectRevocationSection" style="display: none;">
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label">Daftar Tersangka</label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                <select class="form-control select2-multiple" name="revocationSuspects[]"
                                    id="revocationSuspects" multiple="multiple"
                                    data-placeholder="--Tersangka yang Direkomendasikan untuk Ditahan--">
                                    @foreach ($revocationSuspects as $revocationSuspect)
                                        <option value="{{ $revocationSuspect->id }}">{{ $revocationSuspect->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('revocationSuspects')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr>
                </div>

                <h5 class="fw-bold text-blue-dark">Pembahasan</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Pembahasan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <textarea class="form-control noEnterTextArea" id="discussion" name="discussion" rows="10" autocomplete="off">{{ old('discussion') }}</textarea>

                        @error('discussion')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Kesimpulan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <textarea class="form-control noEnterTextArea" id="conclusion" name="conclusion" rows="10" autocomplete="off">{{ old('conclusion') }}</textarea>

                        @error('conclusion')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Penutup<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <textarea class="form-control noEnterTextArea" id="closing" name="closing" rows="10" autocomplete="off">{{ old('closing') }}</textarea>

                        @error('closing')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>

                <h5 class="fw-bold text-blue-dark">Penandatangan Surat</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label">Tanggal Ditandatangani Dokumen<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input class="form-control" id="documentDate" name="documentDate" placeholder="YYYY-MM-DD"
                            autocomplete="off" value="{{ old('documentDate') }}" data-provide="datepicker">

                        @error('documentDate')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="isUpperUnitDocument">Penandatangan Dokumen<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="isUpperUnitDocument"
                                name="isUpperUnitDocument" value="true" aria-label="..." disabled>
                            <label for="isUpperUnitDocument">
                                LHGP di Tingkat Satuan Atas
                            </label>
                        </div>

                        @error('isUpperUnitDocument')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div> --}}

                <div id="signatoryOfficer">
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-3 col-form-label">Yang Menandatangani<span class="text-danger fs-5">*</span></label>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                            <select class="form-control select2" name="signatory" id="signatory">
                                <option value="">--Pilih Yang Menandatangani--</option>
                                @foreach ($authorizedSignatories as $data)
                                    @php
                                        $positionName = $data->position->name ?? '';
                                    @endphp
                                    <option value="{{ $data->id }}">
                                        {{ $data->register_number . ' - ' . $data->full_name . ' | ' . $positionName }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">(*Apabila daftar yang menandatangani kosong silahkan hubungi Helpdesk
                                untuk mendapat bantuan)</small>

                            @error('signatory')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div id="signatoryUpperUnitOfficer" style="display: none;">
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-3 col-form-label">Nama Pejabat Satuan Atas</label>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                            <input class="form-control" id="upperUnitOfficerName" name="upperUnitOfficerName"
                                placeholder="" autocomplete="off" value="{{ old('upperUnitOfficerName') }}">
                            @error('upperUnitOfficerName')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-3 col-form-label">NRP Pejabat Satuan Atas</label>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                            <input class="form-control onlyIntegerInput" id="upperUnitOfficerRegisterNumber"
                                name="upperUnitOfficerRegisterNumber" placeholder="" autocomplete="off"
                                value="{{ old('upperUnitOfficerRegisterNumber') }}">
                            @error('upperUnitOfficerRegisterNumber')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-3 col-form-label">Pangkat Pejabat Satuan Atas</label>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                            <select class="form-control select2" id="upperUnitOfficerRank" name="upperUnitOfficerRank">
                                <option value="">--Pilih Pangkat--</option>
                                @foreach ($ranks as $rank)
                                    <option value="{{ $rank->id }}">
                                        {{ $rank->full_name . ' (' . $rank->name . ')' }}</option>
                                @endforeach
                            </select>

                            @error('upperUnitOfficerRank')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-3 col-form-label">Jabatan Pejabat Satuan Atas</label>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                            <select class="form-control select2" id="upperUnitOfficerPosition"
                                name="upperUnitOfficerPosition">
                                <option value="">--Pilih Jabatan--</option>
                                @foreach ($positions as $position)
                                    <option value="{{ $position->id }}">{{ $position->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted"> Jika Jabatan Yang Ingin Dipilih Tidak Muncul, Silahkan Hubungi
                                Helpdesk Untuk Mendapat Bantuan</small>
                            @error('upperUnitOfficerPosition')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="input-group row mb-3 ms-0">
                        <div class="offset-sm-3 col-lg-9 col-md-9 col-sm-12 col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="isOnBehalfOfSuperiorOfficer"
                                    name="isOnBehalfOfSuperiorOfficer" value="true" aria-label="...">
                                <label for="isOnBehalfOfSuperiorOfficer">
                                    Atas Nama Atasan Pejabat
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="input-group row mb-3 ms-0" style="display: none;">
                        <label class="fw-bold col-sm-3 col-form-label">Atasan Pejabat Satuan Atas</label>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                            <input class="form-control" id="upperUnitSuperiorOfficerName"
                                name="upperUnitSuperiorOfficerName" placeholder="" autocomplete="off"
                                value="{{ old('upperUnitSuperiorOfficerName') }}">
                            @error('upperUnitSuperiorOfficerName')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr>

                <h5 class="fw-bold text-blue-dark">Dokumentasi</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Foto Kegiatan</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input type="file" class="form-control" id="photos" name="photos[]" multiple>

                        <small class="text-muted">(*File foto harus tipe JPG, JPEG, PNG)</small>

                        @error('photos')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

		<hr>

		@if(strtotime($accident->report_date) < strtotime('2024-01-01') || $accident->police->is_whitelisted_document_legacy == true && strtotime($accident->police->start_date_whitelisted_document_legacy) <= strtotime($accident->report_date) && strtotime($accident->report_date) <= strtotime($accident->police->end_date_whitelisted_document_legacy))
                	@include('docs.components.form.checkbox.is-legacy')
		@endif

                <div class="text-center">
                    <button type="submit" class="btn btn-dark-blue" id="laporanHasilGelarPerkaraFormSubmit">
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


    <!-- Modal Add Manual Suspect-->
    <div class="modal fade" id="addNewSuspectModal" tabindex="-1" role="dialog"
        aria-labelledby="addNewSuspectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content" id="modalContent">
                <!-- Header Modal -->
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-blue-dark" id="addNewSuspectModalLabel">DATA TERSANGKA</h5>
                </div>

                <!-- Body Modal -->
                <div class="modal-body">
                    <form id="addNewSuspectForm">
                        <input type="hidden" class="form-control" id="addNewSuspectFormMode" value="">
                        <input type="hidden" class="form-control" id="oldSuspectId" value="">

                        <div class="row mb-3">
                            <label class="fw-bold col-sm-2 col-form-label" for="identityStatusFieldNewSuspect">Status
                                Identitas<span class="text-danger fs-5">*</span></label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-items-center">
                                {{--<div class="form-check me-1">
                                    <input class="form-check-input" type="radio"
                                        id="identityStatusOnlyNameFieldNewSuspect" name="identityStatusFieldNewSuspect"
                                        value="WITHOUT_IDENTITY">
                                    <label for="identityStatusOnlyNameFieldNewSuspect">
                                        <b>Hanya Diketahui Nama</b>
                                    </label>
                                </div>--}}
                                <div class="form-check ms-1">
                                    <input class="form-check-input" type="radio"
                                        id="identityStatusWithIdentityFieldNewSuspect"
                                        name="identityStatusFieldNewSuspect" value="WITH_IDENTITY" checked>
                                    <label for="identityStatusWithIdentityFieldNewSuspect">
                                        <b>Diketahui Nama dan Identitas</b>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="fw-bold col-sm-2 col-form-label" for="identityTypeFieldNewSuspect">Jenis
                                Identitas<span class="text-danger fs-5">*</span></label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                <select class="form-control" id="identityTypeFieldNewSuspect">
                                    <option value="">--Pilih Jenis Identitas--</option>
                                    @foreach ($identityTypes as $identityType)
                                        <option value="{{ $identityType->id }}"
                                            data-identity-type-name="{{ $identityType->name }}">{{ $identityType->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="fw-bold col-sm-2 col-form-label" for="identityNumberFieldNewSuspect">Nomor
                                Identitas<span class="text-danger fs-5">*</span></label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                <input type="text" class="form-control" id="identityNumberFieldNewSuspect"
                                    placeholder="Nomor Identitas">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="fw-bold col-sm-2 col-form-label" for="nameFieldNewSuspect">Nama<span class="text-danger fs-5">*</span></label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                <input type="text" class="form-control" id="nameFieldNewSuspect"
                                    placeholder="Nama Lengkap">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="fw-bold col-sm-2 col-form-label" for="genderFieldNewSuspect">Jenis Kelamin<span class="text-danger fs-5">*</span>
                            </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                <select class="form-control" id="genderFieldNewSuspect">
                                    <option value="">--Pilih Jenis Kelamin--</option>
                                    @foreach ($genders as $gender)
                                        <option value="{{ $gender->id }}" data-gender-name="{{ $gender->name }}">
                                             {{ $gender->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{--<div class="col-sm-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="isUnknownGenderFieldNewSuspect"
                                        value="true" aria-label="...">
                                    <label for="isUnknownGenderFieldNewSuspect">
                                        Tidak Tahu
                                    </label>
                                </div>
                            </div>--}}
                        </div>

                        <div class="row mb-3">
                            <label class="fw-bold col-sm-2 col-form-label" for="birthPlaceFieldNewSuspect">Tempat Lahir<span class="text-danger fs-5">*</span>
                            </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                <input type="text" class="form-control" id="birthPlaceFieldNewSuspect"
                                    placeholder="Tempat Lahir">
                            </div>
                            {{--<div class="col-sm-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        id="isUnknownBirthPlaceFieldNewSuspect" value="true" aria-label="...">
                                    <label for="isUnknownBirthPlaceFieldNewSuspect">
                                        Tidak Tahu
                                    </label>
                                </div>
                            </div>--}}
                        </div>

                        <div class="row mb-3">
                            <label class="fw-bold col-sm-2 col-form-label" for="birthDateFieldNewSuspect">Tanggal Lahir<span class="text-danger fs-5">*</span>
                            </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                <input type="text" class="form-control" id="birthDateFieldNewSuspect"
                                    placeholder="YYYY-MM-DD" data-provide="datepicker">
                            </div>
                            {{--<div class="col-sm-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        id="isUnknownBirthDateFieldNewSuspect" value="true" aria-label="...">
                                    <label for="isUnknownBirthDateFieldNewSuspect">
                                        Tidak Tahu
                                    </label>
                                </div>
                            </div>--}}
                        </div>

                        <div class="row mb-3">
                            <label class="fw-bold col-sm-2 col-form-label" for="fatherFieldNewSuspect">Ayah Kandung
                            </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                <input type="text" class="form-control" id="fatherFieldNewSuspect"
                                    placeholder="Nama Ayah Kandung">
                            </div>
                            <div class="col-sm-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="isUnknownFatherFieldNewSuspect"
                                        value="true" aria-label="...">
                                    <label for="isUnknownFatherFieldNewSuspect">
                                        Tidak Tahu
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="fw-bold col-sm-2 col-form-label" for="motherFieldNewSuspect">Ibu Kandung
                            </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                <input type="text" class="form-control" id="motherFieldNewSuspect"
                                    placeholder="Nama Ibu Kandung">
                            </div>
                            <div class="col-sm-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="isUnknownMotherFieldNewSuspect"
                                        value="true" aria-label="...">
                                    <label for="isUnknownMotherFieldNewSuspect">
                                        Tidak Tahu
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="fw-bold col-sm-2 col-form-label" for="nationalityFieldNewSuspect">Kebangsaan<span class="text-danger fs-5">*</span>
                            </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                <input type="text" class="form-control" id="nationalityFieldNewSuspect"
                                    placeholder="Kebangsaan">
                            </div>
                            {{--<div class="col-sm-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        id="isUnknownNationalityFieldNewSuspect" value="true" aria-label="...">
                                    <label for="isUnknownNationalityFieldNewSuspect">
                                        Tidak Tahu
                                    </label>
                                </div>
                            </div>--}}
                        </div>
                        <div class="row mb-3">
                            <label class="fw-bold col-sm-2 col-form-label" for="ethnicFieldNewSuspect">Suku<span class="text-danger fs-5">*</span> </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                <select class="form-control" id="ethnicFieldNewSuspect">
                                    <option value="">--Pilih Suku--</option>
                                    @foreach ($ethnics as $ethnic)
                                        <option value="{{ $ethnic->id }}" data-ethnic-name="{{ $ethnic->name }}">
                                            {{ $ethnic->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="fw-bold col-sm-2 col-form-label" for="jobFieldNewSuspect">Pekerjaan<span class="text-danger fs-5">*</span> </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                <select class="form-control" id="jobFieldNewSuspect">
                                    <option value="">--Pilih Pekerjaan--</option>
                                    @foreach ($jobs as $job)
                                        <option value="{{ $job->id }}" data-job-name="{{ $job->name }}">
                                            {{ $job->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="fw-bold col-sm-2 col-form-label" for="religionFieldNewSuspect">Agama<span class="text-danger fs-5">*</span> </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                <select class="form-control" id="religionFieldNewSuspect">
                                    <option value="">--Pilih Agama--</option>
                                    @foreach ($religions as $religion)
                                        <option value="{{ $religion->id }}" data-religion-name="{{ $religion->name }}">
                                            {{ $religion->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="fw-bold col-sm-2 col-form-label" for="educationFieldNewSuspect">Pendidikan<span class="text-danger fs-5">*</span>
                            </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                <select class="form-control" id="educationFieldNewSuspect">
                                    <option value="">--Pilih Pendidikan--</option>
                                    @foreach ($educations as $education)
                                        <option value="{{ $education->id }}"
                                            data-education-name="{{ $education->name }}">{{ $education->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="fw-bold col-sm-2 col-form-label" for="maritalStatusFieldNewSuspect">Status Kawin<span class="text-danger fs-5">*</span>
                            </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                <select class="form-control" id="maritalStatusFieldNewSuspect">
                                    <option value="">--Pilih Status Kawin--</option>
                                    @foreach ($maritalStatuses as $maritalStatus)
                                        <option value="{{ $maritalStatus->id }}"
                                            data-marital-status-name="{{ $maritalStatus->name }}">
                                            {{ $maritalStatus->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{--<div class="col-sm-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        id="isUnknownMaritalStatusFieldNewSuspect" value="true" aria-label="...">
                                    <label for="isUnknownMaritalStatusFieldNewSuspect">
                                        Tidak Tahu
                                    </label>
                                </div>
                            </div>--}}
                        </div>

                        <div class="row mb-3">
                            <label class="fw-bold col-sm-2 col-form-label" for="phoneNumberFieldNewSuspect">Nomor Telepon
                            </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                <div class="d-flex mb-2">
                                    <div class="form-check m-1">
                                        <input class="form-check-input" type="radio"
                                            id="existsPhoneNumberFieldNewSuspect"
                                            name="isExistsPhoneNumberFieldNewSuspect" value="true">
                                        <label for="existsPhoneNumberFieldNewSuspect">
                                            Ada Nomor Telepon
                                        </label>
                                    </div>

                                    <div class="form-check m-1">
                                        <input class="form-check-input" type="radio"
                                            id="notExistsPhoneNumberFieldNewSuspect"
                                            name="isExistsPhoneNumberFieldNewSuspect" value="false">
                                        <label for="notExistsPhoneNumberFieldNewSuspect">
                                            Tidak ada Nomor Telepon
                                        </label>
                                    </div>
                                </div>

                                <input type="text" class="form-control mb-2" id="phoneNumberFieldNewSuspect"
                                    placeholder="Nomor Telepon">

                                <div class="form-check m-1">
                                    <input class="form-check-input" type="checkbox"
                                        id="isAvailablePhoneNumberFieldNewSuspect" value="true" aria-label="...">
                                    <label for="isAvailablePhoneNumberFieldNewSuspect">
                                        Bersedia memberikan nomor telepon?
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="fw-bold col-sm-2 col-form-label" for="emailFieldNewSuspect">Email</label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                <div class="d-flex mb-3">
                                    <div class="form-check m-1">
                                        <input class="form-check-input" type="radio" id="existsEmailFieldNewSuspect"
                                            name="isExistsEmailFieldNewSuspect" value="true">
                                        <label for="existsEmailFieldNewSuspect">
                                            Ada Email
                                        </label>
                                    </div>

                                    <div class="form-check m-1">
                                        <input class="form-check-input" type="radio" id="notExistsEmailFieldNewSuspect"
                                            name="isExistsEmailFieldNewSuspect" value="false">
                                        <label for="notExistsEmailFieldNewSuspect">
                                            Tidak ada Email
                                        </label>
                                    </div>
                                </div>

                                <input type="text" class="form-control mb-2" id="emailFieldNewSuspect"
                                    placeholder="Email">

                                <div class="form-check m-1">
                                    <input class="form-check-input" type="checkbox" id="isAvailableEmailFieldNewSuspect"
                                        value="true" aria-label="...">
                                    <label for="isAvailableEmailFieldNewSuspect">
                                        Bersedia memberikan email?
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="fw-bold col-sm-2 col-form-label" for="countryFieldNewSuspect">Negara<span class="text-danger fs-5">*</span> </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                <select class="form-control" id="countryFieldNewSuspect">
                                    <option value="">--Pilih Negara--</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}" data-country-name="{{ $country->name }}">
                                            {{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="countryChildrenLocationSectionNewSuspect" style="display:none;">
                            <div class="row mb-3">
                                <label class="fw-bold col-sm-2 col-form-label" for="provinceFieldNewSuspect">Provinsi<span class="text-danger fs-5">*</span>
                                </label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                    <select class="form-control" id="provinceFieldNewSuspect">
                                        <option value="">--Pilih Provinsi--</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="fw-bold col-sm-2 col-form-label" for="regencyFieldNewSuspect">Kabupaten/Kota<span class="text-danger fs-5">*</span>
                                </label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                    <select class="form-control" id="regencyFieldNewSuspect">
                                        <option value="">--Pilih Kabupaten/Kota--</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="fw-bold col-sm-2 col-form-label" for="districtFieldNewSuspect">Kecamatan<span class="text-danger fs-5">*</span>
                                </label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                    <select class="form-control" id="districtFieldNewSuspect">
                                        <option value="">--Pilih Kecamatan--</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="fw-bold col-sm-2 col-form-label" for="villageFieldNewSuspect">Kelurahan/Desa<span class="text-danger fs-5">*</span>
                                </label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                    <select class="form-control" id="villageFieldNewSuspect">
                                        <option value="">--Pilih Kelurahan/Desa--</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="fw-bold col-sm-2 col-form-label" for="addressFieldNewSuspect">Alamat<span class="text-danger fs-5">*</span> </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                <input type="text" class="form-control" id="addressFieldNewSuspect"
                                    placeholder="Alamat">
                            </div>
                            {{--<div class="col-sm-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="isUnknownAddressFieldNewSuspect"
                                        value="true" aria-label="...">
                                    <label for="isUnknownAddressFieldNewSuspect">
                                        Tidak Tahu
                                    </label>
                                </div>
                            </div>--}}
                        </div>
                    </form>
                </div>

                <!-- Footer Modal -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i>
                        Batal</button>
                    <button type="button" class="btn btn-dark-blue" id="saveAddNewSuspectForm"><i
                            class="bi bi-save"></i>
                        Simpan</button>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script src="https://www.jqueryscript.net/demo/Time-Selection-Popover-jQuery-Timepicker/dist/js/timepicker.js"></script>
    <script src="{{ asset('libs/bootstrap-duallistbox/jquery.bootstrap-duallistbox.js') }}"></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

@if(strtotime($accident->report_date) < strtotime('2024-01-01') || $accident->police->is_whitelisted_document_legacy == true && strtotime($accident->police->start_date_whitelisted_document_legacy) <= strtotime($accident->report_date) && strtotime($accident->report_date) <= strtotime($accident->police->end_date_whitelisted_document_legacy))
    @include('docs.components.form.checkbox.is-legacy-js')
@endif

    <script type="text/javascript">
        $(document).ready(function() {
            $(function() {
                $('[data-toggle="tooltip"]').tooltip()
            });

            setInterval(function() {
                $('#attentionBox').toggleClass('alert-danger alert-warning');
            }, 1000);

            $('.onlyIntegerInput').on('keypress', function(event) {
                var charCode = (event.which) ? event.which : event.keyCode;

                // Allow only numeric input (disallow decimal point)
                if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                    event.preventDefault();
                }
            });

            $('.noEnterTextArea').on('keydown', function(event) {
                if (event.keyCode === 13) { // 13 is the Enter key code
                    event.preventDefault();
                }
            });

            $('#documentDate').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: "true",
                orientation: 'auto bottom',
                startDate: new Date()
            });
            $('#documentDate').keydown(function(e) {
                e.preventDefault();
                return false;
            });

            $('#date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: "true",
                orientation: 'auto bottom',
                endDate: new Date()
            });
            $('#date').keydown(function(e) {
                e.preventDefault();
                return false;
            });

            $('#time').timepicker({
                showMeridian: false,
                minuteStep: 1,
                defaultTime: false
            }).on('changeTime.timepicker change input hide.timepicker', function() {
                var val = ($(this).val() || '').trim();
                if (val !== '') {
                    $(this).removeClass('is-invalid');
                    $(this).next('.frontend-error, .invalid-feedback').remove();
                    $(this).parent().find('.frontend-error, .invalid-feedback').remove();
                }
            });
            $('#time').keydown(function(e) {
                e.preventDefault();
                return false;
            });

            $('#date, #caseDegreeInviteDate, #documentDate').on('change changeDate input', function() {
                var val = ($(this).val() || '').trim();
                if (val !== '') {
                    $(this).removeClass('is-invalid');
                    $(this).next('.frontend-error, .invalid-feedback').remove();
                    $(this).parent().find('.frontend-error, .invalid-feedback').remove();
                }
            });

            $('#birthDateFieldNewSuspect').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: "true",
                endDate: new Date(),
                container: '#addNewSuspectModal',
            });
            $('#birthDateFieldNewSuspect').keydown(function(e) {
                e.preventDefault();
                return false;
            });

            $('#caseDegreeInviteDate').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: "true",
                orientation: 'auto bottom',
                endDate: new Date()
            });
            $('#caseDegreeInviteDate').keydown(function(e) {
                e.preventDefault();
                return false;
            });
        });

        // Select2 with Bootstrap4 theme
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            $('.select2-multiple').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            $('.select2-input-group').select2({
                theme: 'bootstrap4',
            });

            $('#identityTypeFieldNewSuspect').select2({
                theme: 'bootstrap4',
                width: '100%',
                dropdownParent: $('#identityTypeFieldNewSuspect').parent()
            });
            $('#genderFieldNewSuspect').select2({
                theme: 'bootstrap4',
                width: '100%',
                dropdownParent: $('#genderFieldNewSuspect').parent()
            });
            $('#ethnicFieldNewSuspect').select2({
                theme: 'bootstrap4',
                width: '100%',
                dropdownParent: $('#ethnicFieldNewSuspect').parent()
            });
            $('#jobFieldNewSuspect').select2({
                theme: 'bootstrap4',
                width: '100%',
                dropdownParent: $('#jobFieldNewSuspect').parent()
            });
            $('#religionFieldNewSuspect').select2({
                theme: 'bootstrap4',
                width: '100%',
                dropdownParent: $('#religionFieldNewSuspect').parent()
            });
            $('#educationFieldNewSuspect').select2({
                theme: 'bootstrap4',
                width: '100%',
                dropdownParent: $('#educationFieldNewSuspect').parent()
            });
            $('#maritalStatusFieldNewSuspect').select2({
                theme: 'bootstrap4',
                width: '100%',
                dropdownParent: $('#maritalStatusFieldNewSuspect').parent()
            });
            $('#countryFieldNewSuspect').select2({
                theme: 'bootstrap4',
                width: '100%',
                dropdownParent: $('#countryFieldNewSuspect').parent()
            });
            $('#provinceFieldNewSuspect').select2({
                theme: 'bootstrap4',
                width: '100%',
                dropdownParent: $('#provinceFieldNewSuspect').parent()
            });
            $('#regencyFieldNewSuspect').select2({
                theme: 'bootstrap4',
                width: '100%',
                dropdownParent: $('#regencyFieldNewSuspect').parent()
            });
            $('#districtFieldNewSuspect').select2({
                theme: 'bootstrap4',
                width: '100%',
                dropdownParent: $('#districtFieldNewSuspect').parent()
            });
            $('#villageFieldNewSuspect').select2({
                theme: 'bootstrap4',
                width: '100%',
                dropdownParent: $('#villageFieldNewSuspect').parent()
            });

            /*$("#arrestSuspects").bootstrapDualListbox({
                nonSelectedListLabel: 'Daftar Tersangka',
                selectedListLabel: 'Tersangka yang Ditahan',
                preserveSelectionOnMove: 'moved',
                moveOnSelect: false,
            });

            $("#revocationSuspects").bootstrapDualListbox({
                nonSelectedListLabel: 'Daftar Tersangka',
                selectedListLabel: 'Dicabut Status Tersangkanya',
                preserveSelectionOnMove: 'moved',
                moveOnSelect: false,
            });*/
        });

        $(document).ready(function() {
            $('#caseDegreeType').on('change', function() {
                var caseDegreeTypeId = $(this).find(':selected').val();

                if (caseDegreeTypeId == 1) {
                    $('#recommendedSuspectSection').show();
                    $('#suspectDeterminationSection').show();
                    $('#arrestSuspectSection').hide();
                    $('#suspectRevocationSection').hide();
                } else if (caseDegreeTypeId == 4) {
                    $('#recommendedSuspectSection').show();
                    $('#suspectDeterminationSection').hide();
                    $('#arrestSuspectSection').show();
                    $('#suspectRevocationSection').hide();
                } else if (caseDegreeTypeId == 16) {
                    $('#recommendedSuspectSection').show();
                    $('#suspectDeterminationSection').hide();
                    $('#arrestSuspectSection').hide();
                    $('#suspectRevocationSection').show();
                } else {
                    $('#suspectDeterminationSection').hide();
                    $('#arrestSuspectSection').hide();
                    $('#suspectRevocationSection').hide();
                    $('#recommendedSuspectSection').hide();
                }
            });

            $('#suspectDeterminationDataSource').on('change', function() {
                var suspectDeterminationDataSourceId = $(this).find(':selected').val();

                if (suspectDeterminationDataSourceId == 3) {
                    // $('#addNewSuspectModal').modal({
                    //     'show': true,
                    //     'dialog': {
                    //         'class': 'modal-dialog-scrollable modal-xl'
                    //     }
                    // });
                    $('#addNewSuspectModal').modal('show');
                } else if (suspectDeterminationDataSourceId == 1) {
                    $.ajax({
                        url: "{{ route('doc.laporan-hasil-gelar-perkara-document.api.witnesses', ['accident_id' => $accidentId]) }}", // Replace with your backend URL
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            var data = response.data;
                            // Clear existing options and add new options
                            var suspectDeterminationOption = $('#suspectDeterminationOption');
                            suspectDeterminationOption.empty();
                            $.each(data, function(index, data) {
                                suspectDeterminationOption.append($('<option>', {
                                    value: data.id,
                                    text: data.name,
                                    "data-name": data.name,
                                    "data-identity-number": (data
                                            .identity_number) ? data
                                        .identity_number : '-',
                                    "data-birth-place": (data.birth_place) ?
                                        data.birth_place : '-',
                                    "data-birth-date": (data.birth_date) ?
                                        data.birth_date : '-',
                                    "data-identity-type-name": (data
                                            .identity_type) ? data
                                        .identity_type.name : '-',
                                }));
                            });
                        },
                        error: function(xhr) {
                            // Handle error if needed
                            console.log(xhr.responseText);
                        }
                    });
                } else if (suspectDeterminationDataSourceId == 2) {
                    $.ajax({
                        url: "{{ route('doc.laporan-hasil-gelar-perkara-document.api.suspects', ['accident_id' => $accidentId]) }}", // Replace with your backend URL
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            'flag': 'TERLAPOR',
                        },
                        success: function(response) {
                            console.log(response);
                            var data = response.data;
                            // Clear existing options and add new options
                            var suspectDeterminationOption = $('#suspectDeterminationOption');
                            suspectDeterminationOption.empty();
                            $.each(data, function(index, data) {
                                suspectDeterminationOption.append($('<option>', {
                                    value: data.id,
                                    text: data.name,
                                    "data-name": data.name,
                                    "data-identity-number": (data
                                            .identity_number) ? data
                                        .identity_number : '-',
                                    "data-birth-place": (data.birth_place) ?
                                        data.birth_place : '-',
                                    "data-birth-date": (data.birth_date) ?
                                        data.birth_date : '-',
                                    "data-identity-type-name": (data
                                            .identity_type) ? data
                                        .identity_type.name : '-',
                                }));
                            });
                        },
                        error: function(xhr) {
                            // Handle error if needed
                            console.log(xhr.responseText);
                        }
                    });
                }
            });

            $('#testAddNewSuspect').on('click', function() {
                $('#addNewSuspectModal').modal('show');
                // $('#addNewSuspectModal').modal({
                //     'show': true,
                //     'width': '100%',
                // });
            });
        });

        $(document).ready(function() {
            $('#suspectDeterminationOptionAddButtton').on('click', function() {
                var suspectDeterminationOptionId = $('#suspectDeterminationOption').find(':selected').val();
                var suspectDeterminationOptionName = $('#suspectDeterminationOption').find(':selected')
                    .data('name');
                var suspectDeterminationOptionIdentityTypeName = $('#suspectDeterminationOption').find(
                    ':selected').data('identity-type-name');
                var suspectDeterminationOptionIdentityNumber = $('#suspectDeterminationOption').find(
                    ':selected').data('identity-number');
                var suspectDeterminationOptionBirthPlace = $('#suspectDeterminationOption').find(
                    ':selected').data('birth-place');
                var suspectDeterminationOptionBirthDate = $('#suspectDeterminationOption').find(':selected')
                    .data('birth-date');
                var suspectSourceId = $('#suspectDeterminationDataSource').find(':selected').val();
                var suspectSourceName = $('#suspectDeterminationDataSource').find(':selected').text();

                if (suspectDeterminationOptionId != '') {
                    $('#suspectDeterminationTable').append(
                        '<tr>' +
                        '<td>' + suspectDeterminationOptionIdentityTypeName + ' / ' +
                        suspectDeterminationOptionIdentityNumber + '</td>' +
                        '<td>' + suspectDeterminationOptionName + '</td>' +
                        '<td>' + suspectDeterminationOptionBirthPlace + '/' +
                        suspectDeterminationOptionBirthDate + '</td>' +
                        '<td>' + suspectSourceName + '</td>' +
                        '<td>' +
                        '<input type="hidden" name="suspectDeterminationSuspectSourceId[]" value="' +
                        suspectSourceId + '">' +
                        '<input type="hidden" name="suspectDeterminationIds[]" value="' +
                        suspectDeterminationOptionId + '">' +
                        '<input type="hidden" name="suspectDeterminationIdentityStatus[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationIdentityTypeId[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationIdentityNumber[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationName[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationGenderId[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationBirthPlace[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationBirthDate[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationFatherName[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationMotherName[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationNationality[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationEthnicId[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationJobId[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationReligionId[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationEducationId[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationMaritalStatusId[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationPhoneNumber[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationIsExistsPhoneNumber[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationIsAvailablePhoneNumber[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationEmail[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationIsExistsEmail[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationIsAvailableEmail[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationCountryId[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationProvinceId[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationRegencyId[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationDistrictId[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationVillageId[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationAddress[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationIsUnknownGender[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationIsUnknownBirthPlace[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationIsUnknownBirthDate[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationIsUnknownFather[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationIsUnknownMother[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationIsUnknownNationality[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationIsUnknownMaritalStatus[]" value="">' +
                        '<input type="hidden" name="suspectDeterminationIsUnknownAddress[]" value="">' +

                        '<button type="button" class="btn btn-danger btn-sm ml-2 removeSuspectDeterminationButton"><i class="bi bi-trash"></i></button>' +
                        '</td>' +
                        '</tr>'
                    );

                    // Hapus event listener removeSuspectDeterminationButton sebelumnya
                    $(document).off('click', '.removeSuspectDeterminationButton');

                    // Tambahkan event listener removeSuspectDeterminationButton yang baru
                    $(document).on('click', '.removeSuspectDeterminationButton', function() {
                        $(this).closest('tr').remove();
                    });
                }
            });
        });

        $(document).ready(function() {
            $('#isUpperUnitDocument').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#signatoryOfficer').hide();
                    $('#signatoryUpperUnitOfficer').show();
                } else {
                    $('#signatoryOfficer').show();
                    $('#signatoryUpperUnitOfficer').hide();
                }
            });

            $('#isOnBehalfOfSuperiorOfficer').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#upperUnitSuperiorOfficerName').parent().parent().show();
                } else {
                    $('#upperUnitSuperiorOfficerName').parent().parent().hide();
                }
            });
        });

        $(document).ready(function() {
            function withIdentitySection() {
                var identityStatus = $('input[name="identityStatusFieldNewSuspect"]:checked').val();
                if (identityStatus == 'WITHOUT_IDENTITY') {
                    //disabled
                    $('#identityTypeFieldNewSuspect').prop('disabled', true);
                    $('#identityNumberFieldNewSuspect').prop('disabled', true);
                    $('#genderFieldNewSuspect').prop('disabled', true);
                    $('#birthPlaceFieldNewSuspect').prop('disabled', true);
                    $('#birthDateFieldNewSuspect').prop('disabled', true);
                    $('#fatherFieldNewSuspect').prop('disabled', true);
                    $('#motherFieldNewSuspect').prop('disabled', true);
                    $('#nationalityFieldNewSuspect').prop('disabled', true);
                    $('#ethnicFieldNewSuspect').prop('disabled', true);
                    $('#jobFieldNewSuspect').prop('disabled', true);
                    $('#religionFieldNewSuspect').prop('disabled', true);
                    $('#educationFieldNewSuspect').prop('disabled', true);
                    $('#maritalStatusFieldNewSuspect').prop('disabled', true);
                    $('#phoneNumberFieldNewSuspect').prop('disabled', true);
                    $('#emailFieldNewSuspect').prop('disabled', true);
                    $('#countryFieldNewSuspect').prop('disabled', true);
                    $('#provinceFieldNewSuspect').prop('disabled', true);
                    $('#regencyFieldNewSuspect').prop('disabled', true);
                    $('#districtFieldNewSuspect').prop('disabled', true);
                    $('#villageFieldNewSuspect').prop('disabled', true);
                    $('#addressFieldNewSuspect').prop('disabled', true);

                    $('#isUnknownGenderFieldNewSuspect').prop('disabled', true);
                    $('#isUnknownBirthPlaceFieldNewSuspect').prop('disabled', true);
                    $('#isUnknownBirthDateFieldNewSuspect').prop('disabled', true);
                    $('#isUnknownFatherFieldNewSuspect').prop('disabled', true);
                    $('#isUnknownMotherFieldNewSuspect').prop('disabled', true);
                    $('#isUnknownNationalityFieldNewSuspect').prop('disabled', true);
                    $('#isUnknownMaritalStatusFieldNewSuspect').prop('disabled', true);
                    $('#isUnknownAddressFieldNewSuspect').prop('disabled', true);
                    $('#existsPhoneNumberFieldNewSuspect').prop('disabled', true);
                    $('#existsEmailFieldNewSuspect').prop('disabled', true);
                    $('#isAvailablePhoneNumberFieldNewSuspect').prop('disabled', true);
                    $('#isAvailableEmailFieldNewSuspect').prop('disabled', true);

                    //empty value input
                    $('#identityNumberFieldNewSuspect').val('');
                    $('#birthPlaceFieldNewSuspect').val('');
                    $('#birthDateFieldNewSuspect').val('');
                    $('#fatherFieldNewSuspect').val('');
                    $('#motherFieldNewSuspect').val('');
                    $('#nationalityFieldNewSuspect').val('');
                    $('#phoneNumberFieldNewSuspect').val('');
                    $('#emailFieldNewSuspect').val('');
                    $('#addressFieldNewSuspect').val('');

                    //tidak tahu checked
                    $('#isUnknownGenderFieldNewSuspect').prop('checked', true).trigger('change');
                    $('#isUnknownBirthPlaceFieldNewSuspect').prop('checked', true).trigger('change');
                    $('#isUnknownBirthDateFieldNewSuspect').prop('checked', true).trigger('change');
                    $('#isUnknownFatherFieldNewSuspect').prop('checked', true).trigger('change');
                    $('#isUnknownMotherFieldNewSuspect').prop('checked', true).trigger('change');
                    $('#isUnknownNationalityFieldNewSuspect').prop('checked', true).trigger('change');
                    $('#isUnknownMaritalStatusFieldNewSuspect').prop('checked', true).trigger('change');
                    $('#isUnknownAddressFieldNewSuspect').prop('checked', true).trigger('change');

                    //selected tidak tahu
                    $('#identityTypeFieldNewSuspect').val(15).trigger('change');
                    $('#genderFieldNewSuspect').val('').trigger('change');
                    $('#ethnicFieldNewSuspect').val('177').trigger('change');
                    $('#jobFieldNewSuspect').val('0').trigger('change');
                    $('#religionFieldNewSuspect').val('0').trigger('change');
                    $('#educationFieldNewSuspect').val('0').trigger('change');
                    $('#maritalStatusFieldNewSuspect').val('').trigger('change');
                    $('#countryFieldNewSuspect').val('').trigger('change');
                    $('#provinceFieldNewSuspect').val('').trigger('change');
                    $('#regencyFieldNewSuspect').val('').trigger('change');
                    $('#districtFieldNewSuspect').val('').trigger('change');
                    $('#villageFieldNewSuspect').val('').trigger('change');

                    //email and phone
                    $('input[name="isExistsPhoneNumberFieldNewSuspect"]').val(['false']).prop('checked', true)
                        .trigger('change');
                    $('input[name="isExistsEmailFieldNewSuspect"]').val(['false']).prop('checked', true).trigger(
                        'change');
                    $('#isAvailablePhoneNumberFieldNewSuspect').prop('checked', false);
                    $('#isAvailableEmailFieldNewSuspect').prop('checked', false);
                } else if (identityStatus == 'WITH_IDENTITY') {
                    // enabled
                    $('#identityTypeFieldNewSuspect').prop('disabled', false);
                    $('#identityNumberFieldNewSuspect').prop('disabled', false);
                    $('#genderFieldNewSuspect').prop('disabled', false);
                    $('#birthPlaceFieldNewSuspect').prop('disabled', false);
                    $('#birthDateFieldNewSuspect').prop('disabled', false);
                    $('#fatherFieldNewSuspect').prop('disabled', false);
                    $('#motherFieldNewSuspect').prop('disabled', false);
                    $('#nationalityFieldNewSuspect').prop('disabled', false);
                    $('#ethnicFieldNewSuspect').prop('disabled', false);
                    $('#jobFieldNewSuspect').prop('disabled', false);
                    $('#religionFieldNewSuspect').prop('disabled', false);
                    $('#educationFieldNewSuspect').prop('disabled', false);
                    $('#maritalStatusFieldNewSuspect').prop('disabled', false);
                    $('#phoneNumberFieldNewSuspect').prop('disabled', false);
                    $('#emailFieldNewSuspect').prop('disabled', false);
                    $('#countryFieldNewSuspect').prop('disabled', false);
                    $('#provinceFieldNewSuspect').prop('disabled', false);
                    $('#regencyFieldNewSuspect').prop('disabled', false);
                    $('#districtFieldNewSuspect').prop('disabled', false);
                    $('#villageFieldNewSuspect').prop('disabled', false);
                    $('#addressFieldNewSuspect').prop('disabled', false);

                    $('#isUnknownGenderFieldNewSuspect').prop('disabled', false);
                    $('#isUnknownBirthPlaceFieldNewSuspect').prop('disabled', false);
                    $('#isUnknownBirthDateFieldNewSuspect').prop('disabled', false);
                    $('#isUnknownFatherFieldNewSuspect').prop('disabled', false);
                    $('#isUnknownMotherFieldNewSuspect').prop('disabled', false);
                    $('#isUnknownNationalityFieldNewSuspect').prop('disabled', false);
                    $('#isUnknownMaritalStatusFieldNewSuspect').prop('disabled', false);
                    $('#isUnknownAddressFieldNewSuspect').prop('disabled', false);
                    $('#existsPhoneNumberFieldNewSuspect').prop('disabled', false);
                    $('#existsEmailFieldNewSuspect').prop('disabled', false);
                    $('#isAvailablePhoneNumberFieldNewSuspect').prop('disabled', false);
                    $('#isAvailableEmailFieldNewSuspect').prop('disabled', false);

                    //tidak tahu unchecked
                    $('#isUnknownGenderFieldNewSuspect').prop('checked', false).trigger('change');
                    $('#isUnknownBirthPlaceFieldNewSuspect').prop('checked', false).trigger('change');
                    $('#isUnknownBirthDateFieldNewSuspect').prop('checked', false).trigger('change');
                    $('#isUnknownFatherFieldNewSuspect').prop('checked', false).trigger('change');
                    $('#isUnknownMotherFieldNewSuspect').prop('checked', false).trigger('change');
                    $('#isUnknownNationalityFieldNewSuspect').prop('checked', false).trigger('change');
                    $('#isUnknownMaritalStatusFieldNewSuspect').prop('checked', false).trigger('change');
                    $('#isUnknownAddressFieldNewSuspect').prop('checked', false).trigger('change');

                    //unselected tidak tahu
                    $('#identityTypeFieldNewSuspect').val('').trigger('change');
                    $('#genderFieldNewSuspect').val('').trigger('change');
                    $('#ethnicFieldNewSuspect').val('').trigger('change');
                    $('#jobFieldNewSuspect').val('').trigger('change');
                    $('#religionFieldNewSuspect').val('').trigger('change');
                    $('#educationFieldNewSuspect').val('').trigger('change');
                    $('#maritalStatusFieldNewSuspect').val('').trigger('change');
                    $('#countryFieldNewSuspect').val('').trigger('change');
                    $('#provinceFieldNewSuspect').val('').trigger('change');
                    $('#regencyFieldNewSuspect').val('').trigger('change');
                    $('#districtFieldNewSuspect').val('').trigger('change');
                    $('#villageFieldNewSuspect').val('').trigger('change');

                    //email and phone
                    $('input[name="isExistsPhoneNumberFieldNewSuspect"]').val(['false']).prop('checked', true)
                        .trigger('change');
                    $('input[name="isExistsEmailFieldNewSuspect"]').val(['false']).prop('checked', true).trigger(
                        'change');
                    $('#isAvailablePhoneNumberFieldNewSuspect').prop('checked', false);
                    $('#isAvailableEmailFieldNewSuspect').prop('checked', false);
                }
            }

            $(function() {
                withIdentitySection();
            });

            $('input[name="identityStatusFieldNewSuspect"]').on('change', function() {
                withIdentitySection();
            });

            function clearModalFieldError($field) {
                if (!$field || !$field.length) return;
                $field.removeClass('is-invalid');
                if ($field.next('.select2-container').length) {
                    $field.next('.select2-container').find('.select2-selection').removeClass('border border-danger is-invalid');
                }
                $field.next('.frontend-error, .invalid-feedback').remove();
                $field.siblings('.frontend-error, .invalid-feedback').remove();
                $field.parent().find('.frontend-error, .invalid-feedback').remove();
                $field.closest('.row, .form-group').find('.frontend-error, .invalid-feedback').remove();
            }

            //tidak tahu checked
            $('#isUnknownGenderFieldNewSuspect').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#genderFieldNewSuspect').val('').trigger('change');
                    $('#genderFieldNewSuspect').prop('disabled', true);
                } else {
                    $('#genderFieldNewSuspect').prop('disabled', false);
                }
                clearModalFieldError($('#genderFieldNewSuspect'));
            });
            $('#isUnknownBirthPlaceFieldNewSuspect').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#birthPlaceFieldNewSuspect').val('TIDAK DIKETAHUI');
                    $('#birthPlaceFieldNewSuspect').prop('disabled', true);
                } else {
                    $('#birthPlaceFieldNewSuspect').val('');
                    $('#birthPlaceFieldNewSuspect').prop('disabled', false);
                }
                clearModalFieldError($('#birthPlaceFieldNewSuspect'));
            });
            $('#isUnknownBirthDateFieldNewSuspect').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#birthDateFieldNewSuspect').val('');
                    $('#birthDateFieldNewSuspect').prop('disabled', true);
                } else {
                    $('#birthDateFieldNewSuspect').prop('disabled', false);
                }
                clearModalFieldError($('#birthDateFieldNewSuspect'));
            });
            $('#isUnknownFatherFieldNewSuspect').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#fatherFieldNewSuspect').val('TIDAK DIKETAHUI');
                    $('#fatherFieldNewSuspect').prop('disabled', true);
                } else {
                    $('#fatherFieldNewSuspect').val('');
                    $('#fatherFieldNewSuspect').prop('disabled', false);
                }
                clearModalFieldError($('#fatherFieldNewSuspect'));
            });
            $('#isUnknownMotherFieldNewSuspect').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#motherFieldNewSuspect').val('TIDAK DIKETAHUI');
                    $('#motherFieldNewSuspect').prop('disabled', true);
                } else {
                    $('#motherFieldNewSuspect').val('');
                    $('#motherFieldNewSuspect').prop('disabled', false);
                }
                clearModalFieldError($('#motherFieldNewSuspect'));
            });
            $('#isUnknownNationalityFieldNewSuspect').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#nationalityFieldNewSuspect').val('TIDAK DIKETAHUI');
                    $('#nationalityFieldNewSuspect').prop('disabled', true);
                } else {
                    $('#nationalityFieldNewSuspect').val('');
                    $('#nationalityFieldNewSuspect').prop('disabled', false);
                }
                clearModalFieldError($('#nationalityFieldNewSuspect'));
            });
            $('#isUnknownMaritalStatusFieldNewSuspect').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#maritalStatusFieldNewSuspect').val('').trigger('change');
                    $('#maritalStatusFieldNewSuspect').prop('disabled', true);
                } else {
                    $('#maritalStatusFieldNewSuspect').prop('disabled', false);
                }
                clearModalFieldError($('#maritalStatusFieldNewSuspect'));
            });
            $('#isUnknownAddressFieldNewSuspect').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#addressFieldNewSuspect').val('TIDAK DIKETAHUI');
                    $('#addressFieldNewSuspect').prop('disabled', true);
                } else {
                    $('#addressFieldNewSuspect').val('');
                    $('#addressFieldNewSuspect').prop('disabled', false);
                }
                clearModalFieldError($('#addressFieldNewSuspect'));
            });

            $('#addNewSuspectModal').on('change', 'input[type="checkbox"]', function() {
                var $row = $(this).closest('.row, .form-group');
                clearModalFieldError($row.find('input, select, textarea'));
            });

            //province get from ajax
            $('#countryFieldNewSuspect').on('change', function() {
                var parentId = $(this).find(':selected').val();
                var classCode = 'PROVINCE';

                $.ajax({
                    url: "{{ route('doc.laporan-hasil-gelar-perkara-document.api.locations', ['accident_id' => $accidentId]) }}", // Replace with your backend URL
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        'parent_id': parentId,
                        'class': classCode,
                    },
                    success: function(response) {
                        var data = response.data;
                        // Clear existing options and add new options
                        var provinceFieldNewSuspect = $('#provinceFieldNewSuspect');
                        provinceFieldNewSuspect.empty().append($('<option>', {
                            value: '',
                            text: '--Pilih Provinsi--'
                        }));
                        $.each(data, function(index, data) {
                            provinceFieldNewSuspect.append($('<option>', {
                                value: data.id,
                                text: data.name,
                                'data-parent-id': data.parent_id,
                                'data-class': data.class,
                            }));
                        });
                    },
                    error: function(xhr) {
                        // Handle error if needed
                        console.log(xhr.responseText);
                    }
                });
            });

            //regency get from ajax
            $('#provinceFieldNewSuspect').on('change', function() {
                var parentId = $(this).find(':selected').val();
                var classCode = 'REGENCY';

                $.ajax({
                    url: "{{ route('doc.laporan-hasil-gelar-perkara-document.api.locations', ['accident_id' => $accidentId]) }}", // Replace with your backend URL
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        'parent_id': parentId,
                        'class': classCode,
                    },
                    success: function(response) {
                        var data = response.data;
                        // Clear existing options and add new options
                        var regencyFieldNewSuspect = $('#regencyFieldNewSuspect');
                        regencyFieldNewSuspect.empty().append($('<option>', {
                            value: '',
                            text: '--Pilih Kabupaten/Kota--'
                        }));
                        $.each(data, function(index, data) {
                            regencyFieldNewSuspect.append($('<option>', {
                                value: data.id,
                                text: data.name,
                                'data-parent-id': data.parent_id,
                                'data-class': data.class,
                            }));
                        });
                    },
                    error: function(xhr) {
                        // Handle error if needed
                        console.log(xhr.responseText);
                    }
                });
            });

            //district get from ajax
            $('#regencyFieldNewSuspect').on('change', function() {
                var parentId = $(this).find(':selected').val();
                var classCode = 'DISTRICT';

                $.ajax({
                    url: "{{ route('doc.laporan-hasil-gelar-perkara-document.api.locations', ['accident_id' => $accidentId]) }}", // Replace with your backend URL
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        'parent_id': parentId,
                        'class': classCode,
                    },
                    success: function(response) {
                        var data = response.data;
                        // Clear existing options and add new options
                        var districtFieldNewSuspect = $('#districtFieldNewSuspect');
                        districtFieldNewSuspect.empty().append($('<option>', {
                            value: '',
                            text: '--Pilih Kecamatan--'
                        }));
                        $.each(data, function(index, data) {
                            districtFieldNewSuspect.append($('<option>', {
                                value: data.id,
                                text: data.name,
                                'data-parent-id': data.parent_id,
                                'data-class': data.class,
                            }));
                        });
                    },
                    error: function(xhr) {
                        // Handle error if needed
                        console.log(xhr.responseText);
                    }
                });
            });

            //district get from ajax
            $('#districtFieldNewSuspect').on('change', function() {
                var parentId = $(this).find(':selected').val();
                var classCode = 'VILLAGE';

                $.ajax({
                    url: "{{ route('doc.laporan-hasil-gelar-perkara-document.api.locations', ['accident_id' => $accidentId]) }}", // Replace with your backend URL
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        'parent_id': parentId,
                        'class': classCode,
                    },
                    success: function(response) {
                        var data = response.data;
                        // Clear existing options and add new options
                        var villageFieldNewSuspect = $('#villageFieldNewSuspect');
                        villageFieldNewSuspect.empty().append($('<option>', {
                            value: '',
                            text: '--Pilih Desa/Kelurahan--'
                        }));
                        $.each(data, function(index, data) {
                            villageFieldNewSuspect.append($('<option>', {
                                value: data.id,
                                text: data.name,
                                'data-parent-id': data.parent_id,
                                'data-class': data.class,
                            }));
                        });
                    },
                    error: function(xhr) {
                        // Handle error if needed
                        console.log(xhr.responseText);
                    }
                });
            });

            //identity type
            $('#identityTypeFieldNewSuspect').on('change', function() {
                var identityTypeId = $(this).find(':selected').val();
                var identityTypeName = $(this).find(':selected').text();

                if (identityTypeId == 15 || identityTypeId == 16) {
                    $('#identityNumberFieldNewSuspect').prop('disabled', true);
                    $('#identityNumberFieldNewSuspect').val(identityTypeName);
                } else {
                    $('#identityNumberFieldNewSuspect').prop('disabled', false);
                    $('#identityNumberFieldNewSuspect').val('');
                }
            });
        });

        //phone and email
        $('input[name="isExistsPhoneNumberFieldNewSuspect"]').on('change', function() {
            var isExistsPhoneNumberFieldNewSuspect = $('input[name="isExistsPhoneNumberFieldNewSuspect"]:checked')
                .val();
            if (isExistsPhoneNumberFieldNewSuspect == 'true') {
                $('#isAvailablePhoneNumberFieldNewSuspect').prop('disabled', false);
                $('#isAvailablePhoneNumberFieldNewSuspect').prop('checked', true);
                $('#phoneNumberFieldNewSuspect').prop('disabled', false);
                $('#phoneNumberFieldNewSuspect').val('');
            } else {
                $('#isAvailablePhoneNumberFieldNewSuspect').prop('disabled', true);
                $('#isAvailablePhoneNumberFieldNewSuspect').prop('checked', false);
                $('#phoneNumberFieldNewSuspect').val('TIDAK ADA NOMOR TELEPON');
                $('#phoneNumberFieldNewSuspect').prop('disabled', true);
            }
            clearModalFieldError($('#phoneNumberFieldNewSuspect'));
            clearModalFieldError($('input[name="isExistsPhoneNumberFieldNewSuspect"]'));
        });
        $('#isAvailablePhoneNumberFieldNewSuspect').on('change', function() {
            if ($(this).is(':checked')) {
                $('#phoneNumberFieldNewSuspect').val('');
                $('#phoneNumberFieldNewSuspect').prop('disabled', false);
            } else {
                $('#phoneNumberFieldNewSuspect').prop('disabled', true);
                $('#phoneNumberFieldNewSuspect').val('TIDAK BERSEDIA MEMBERIKAN NOMOR TELEPON');
            }
            clearModalFieldError($('#phoneNumberFieldNewSuspect'));
        });
        $('input[name="isExistsEmailFieldNewSuspect"]').on('change', function() {
            var isExistsEmailFieldNewSuspect = $('input[name="isExistsEmailFieldNewSuspect"]:checked').val();
            if (isExistsEmailFieldNewSuspect == 'true') {
                $('#isAvailableEmailFieldNewSuspect').prop('disabled', false);
                $('#isAvailableEmailFieldNewSuspect').prop('checked', true);
                $('#emailFieldNewSuspect').prop('disabled', false);
                $('#emailFieldNewSuspect').val('');
            } else {
                $('#isAvailableEmailFieldNewSuspect').prop('disabled', true);
                $('#isAvailableEmailFieldNewSuspect').prop('checked', false);
                $('#emailFieldNewSuspect').val('TIDAK ADA EMAIL');
                $('#emailFieldNewSuspect').prop('disabled', true);
            }
            clearModalFieldError($('#emailFieldNewSuspect'));
            clearModalFieldError($('input[name="isExistsEmailFieldNewSuspect"]'));
        });
        $('#isAvailableEmailFieldNewSuspect').on('change', function() {
            if ($(this).is(':checked')) {
                $('#emailFieldNewSuspect').val('');
                $('#emailFieldNewSuspect').prop('disabled', false);
            } else {
                $('#emailFieldNewSuspect').prop('disabled', true);
                $('#emailFieldNewSuspect').val('TIDAK BERSEDIA MEMBERIKAN EMAIL');
            }
            clearModalFieldError($('#emailFieldNewSuspect'));
        });

        $('#countryFieldNewSuspect').on('change', function() {
            var countryId = $(this).find(':selected').val();

            //if indonesia show province, regency, district, village
            if (countryId == 'C101') {
                $('.countryChildrenLocationSectionNewSuspect').show();
            } else {
                $('.countryChildrenLocationSectionNewSuspect').hide();
            }
        });

        function validateModalIdentityNumber() {
            var $field = $('#identityNumberFieldNewSuspect');
            var identityTypeId = $('#identityTypeFieldNewSuspect').val();
            var identityTypeName = ($('#identityTypeFieldNewSuspect').find(':selected').data('identity-type-name') || $('#identityTypeFieldNewSuspect').find(':selected').text() || '').toUpperCase();
            var val = ($field.val() || '').trim();
            var errorMsg = '';

            // Skip validation if empty or disabled without wiping required error
            if ($field.is(':disabled') || val === '') {
                return null;
            }

            if (identityTypeId == 10 || identityTypeName.indexOf('KTP') !== -1 || identityTypeName.indexOf('KARTU TANDA PENDUDUK') !== -1) { // KTP
                if (!/^[0-9]+$/.test(val)) {
                    errorMsg = 'Nomor KTP harus berupa angka saja.';
                } else if (val.length !== 16) {
                    errorMsg = 'Nomor KTP harus tepat 16 digit (saat ini: ' + val.length + ' digit).';
                }
            } else if (identityTypeId == 8 || identityTypeName.indexOf('KK') !== -1 || identityTypeName.indexOf('KARTU KELUARGA') !== -1) { // KK
                if (!/^[0-9]+$/.test(val)) {
                    errorMsg = 'Nomor Kartu Keluarga (KK) harus berupa angka saja.';
                } else if (val.length !== 16) {
                    errorMsg = 'Nomor Kartu Keluarga (KK) harus tepat 16 digit (saat ini: ' + val.length + ' digit).';
                }
            } else if (identityTypeId == 13 || identityTypeName.indexOf('SIM') !== -1 || identityTypeName.indexOf('SURAT IZIN MENGEMUDI') !== -1) { // SIM
                if (!/^[0-9]+$/.test(val)) {
                    errorMsg = 'Nomor SIM harus berupa angka saja.';
                } else if (val.length !== 12 && val.length !== 14 && val.length !== 16) {
                    errorMsg = 'Nomor SIM harus 12, 14, atau 16 digit (saat ini: ' + val.length + ' digit).';
                }
            } else if (identityTypeId == 12 || identityTypeName.indexOf('PASPOR') !== -1 || identityTypeName.indexOf('PASSPORT') !== -1) { // Passport
                if (!/^[a-zA-Z0-9]+$/.test(val)) {
                    errorMsg = 'Nomor Passport harus alfanumerik (huruf dan angka saja).';
                } else if (val.length < 7 || val.length > 9) {
                    errorMsg = 'Nomor Passport harus 7 sampai 9 karakter (saat ini: ' + val.length + ' karakter).';
                }
            }

            $field.next('.frontend-error, .invalid-feedback').remove();
            $field.siblings('.frontend-error, .invalid-feedback').remove();

            if (errorMsg) {
                $field.addClass('is-invalid');
                $field.after('<div class="invalid-feedback d-block frontend-error">' + errorMsg + '</div>');
                return errorMsg;
            } else {
                $field.removeClass('is-invalid');
                return null;
            }
        }

        $('#identityNumberFieldNewSuspect').on('input keyup change blur', function() {
            var val = ($(this).val() || '').trim();
            if (val !== '') {
                validateModalIdentityNumber();
            }
        });
        $('#identityTypeFieldNewSuspect').on('change select2:select', function() {
            var val = $('#identityNumberFieldNewSuspect').val() || '';
            if (val !== '') {
                validateModalIdentityNumber();
            }
        });

        function validateModalPhone() {
            var $field = $('#phoneNumberFieldNewSuspect');
            var val = ($field.val() || '').trim();
            if ($field.is(':disabled') || val === 'TIDAK ADA NOMOR TELEPON' || val === 'TIDAK BERSEDIA MEMBERIKAN NOMOR TELEPON') {
                $field.removeClass('is-invalid');
                $field.next('.frontend-error, .invalid-feedback').remove();
                $field.siblings('.frontend-error, .invalid-feedback').remove();
                return null;
            }
            if (val === '') {
                return null;
            }

            var errorMsg = '';
            if (!/^[0-9]+$/.test(val)) {
                errorMsg = 'Nomor telepon harus berupa angka saja.';
            } else if (val.length < 10 || val.length > 13) {
                errorMsg = 'Nomor telepon harus antara 10 sampai 13 digit (saat ini: ' + val.length + ' digit).';
            }

            $field.next('.frontend-error, .invalid-feedback').remove();
            $field.siblings('.frontend-error, .invalid-feedback').remove();

            if (errorMsg) {
                $field.addClass('is-invalid');
                $field.after('<div class="invalid-feedback d-block frontend-error">' + errorMsg + '</div>');
                return errorMsg;
            } else {
                $field.removeClass('is-invalid');
                return null;
            }
        }

        function validateModalEmail() {
            var $field = $('#emailFieldNewSuspect');
            var val = ($field.val() || '').trim();
            if ($field.is(':disabled') || val === 'TIDAK ADA EMAIL' || val === 'TIDAK BERSEDIA MEMBERIKAN EMAIL') {
                $field.removeClass('is-invalid');
                $field.next('.frontend-error, .invalid-feedback').remove();
                $field.siblings('.frontend-error, .invalid-feedback').remove();
                return null;
            }
            if (val === '') {
                return null;
            }

            var errorMsg = '';
            var emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!emailRegex.test(val)) {
                errorMsg = 'Format email tidak valid (contoh: nama@email.com).';
            }

            $field.next('.frontend-error, .invalid-feedback').remove();
            $field.siblings('.frontend-error, .invalid-feedback').remove();

            if (errorMsg) {
                $field.addClass('is-invalid');
                $field.after('<div class="invalid-feedback d-block frontend-error">' + errorMsg + '</div>');
                return errorMsg;
            } else {
                $field.removeClass('is-invalid');
                return null;
            }
        }

        function validateModalBirthDate() {
            var $field = $('#birthDateFieldNewSuspect');
            var val = ($field.val() || '').trim();
            if ($field.is(':disabled')) {
                $field.removeClass('is-invalid');
                $field.next('.frontend-error, .invalid-feedback').remove();
                $field.siblings('.frontend-error, .invalid-feedback').remove();
                return null;
            }
            if (val === '') {
                return null;
            }

            var errorMsg = '';
            var bDate = new Date(val);
            var today = new Date();
            today.setHours(23, 59, 59, 999);
            if (isNaN(bDate.getTime())) {
                errorMsg = 'Format tanggal lahir tidak valid (YYYY-MM-DD).';
            } else if (bDate > today) {
                errorMsg = 'Tanggal lahir tidak boleh melebihi hari ini.';
            }

            $field.next('.frontend-error, .invalid-feedback').remove();
            $field.siblings('.frontend-error, .invalid-feedback').remove();

            if (errorMsg) {
                $field.addClass('is-invalid');
                $field.after('<div class="invalid-feedback d-block frontend-error">' + errorMsg + '</div>');
                return errorMsg;
            } else {
                $field.removeClass('is-invalid');
                return null;
            }
        }

        $('#phoneNumberFieldNewSuspect').on('input keyup change blur', function() {
            var val = ($(this).val() || '').trim();
            if (val !== '' && val !== 'TIDAK ADA NOMOR TELEPON' && val !== 'TIDAK BERSEDIA MEMBERIKAN NOMOR TELEPON') {
                validateModalPhone();
            }
        });

        $('#emailFieldNewSuspect').on('input keyup change blur', function() {
            var val = ($(this).val() || '').trim();
            if (val !== '' && val !== 'TIDAK ADA EMAIL' && val !== 'TIDAK BERSEDIA MEMBERIKAN EMAIL') {
                validateModalEmail();
            }
        });

        $('#birthDateFieldNewSuspect').on('change changeDate input blur', function() {
            var val = ($(this).val() || '').trim();
            if (val !== '') {
                validateModalBirthDate();
            }
        });

        // Auto-clear error realtime untuk field di dalam modal tersangka saat diisi
        $('#addNewSuspectModal').on('input keyup change', 'input:not(#identityNumberFieldNewSuspect, #phoneNumberFieldNewSuspect, #emailFieldNewSuspect, #birthDateFieldNewSuspect), textarea', function() {
            var $field = $(this);
            if (($field.val() || '').trim() !== '') {
                $field.removeClass('is-invalid');
                $field.next('.frontend-error, .invalid-feedback').remove();
                $field.siblings('.frontend-error, .invalid-feedback').remove();
                $field.parent().find('.frontend-error, .invalid-feedback').remove();
            }
        });

        $('#addNewSuspectModal').on('change select2:select', 'select', function() {
            var $field = $(this);
            var val = $field.val();
            if (val && val !== '' && val !== '0') {
                $field.removeClass('is-invalid');
                if ($field.next('.select2-container').length) {
                    $field.next('.select2-container').find('.select2-selection').removeClass('border border-danger is-invalid');
                }
                $field.next('.frontend-error, .invalid-feedback').remove();
                $field.siblings('.frontend-error, .invalid-feedback').remove();
                $field.parent().find('.frontend-error, .invalid-feedback').remove();
            }
        });

        $('#addNewSuspectModal').on('change', 'input[type="radio"]', function() {
            var name = $(this).attr('name');
            $('#addNewSuspectModal input[name="' + name + '"]').removeClass('is-invalid');
            $('#addNewSuspectModal input[name="' + name + '"]').closest('.d-flex').next('.frontend-error, .invalid-feedback').remove();
            $('#addNewSuspectModal input[name="' + name + '"]').closest('.d-flex').find('.frontend-error, .invalid-feedback').remove();
        });

        $('#addNewSuspectModal').on('show.bs.modal', function (e) {
            if (e.target !== this) return;
            $('#addNewSuspectModal .is-invalid').removeClass('is-invalid');
            $('#addNewSuspectModal .select2-selection').removeClass('border border-danger is-invalid');
            $('#addNewSuspectModal .frontend-error, #addNewSuspectModal .invalid-feedback, #addNewSuspectModal small.text-danger').remove();
            var modalEl = document.getElementById('addNewSuspectModal');
            var modalBodyEl = document.querySelector('#addNewSuspectModal .modal-body');
            if (modalEl) modalEl.scrollTop = 0;
            if (modalBodyEl) modalBodyEl.scrollTop = 0;
        });

        $(document).ready(function() {
            $('#saveAddNewSuspectForm').on('click', function() {
                var identityStatusFieldNewSuspect = $('input[name="identityStatusFieldNewSuspect"]:checked')
                    .val();
                var identityTypeFieldNewSuspect = $('#identityTypeFieldNewSuspect').find(':selected').val();
                var identityNumberFieldNewSuspect = $('#identityNumberFieldNewSuspect').val();
                var nameFieldNewSuspect = $('#nameFieldNewSuspect').val();
                var genderFieldNewSuspect = $('#genderFieldNewSuspect').find(':selected').val();
                var birthPlaceFieldNewSuspect = $('#birthPlaceFieldNewSuspect').val();
                var birthDateFieldNewSuspect = $('#birthDateFieldNewSuspect').val();
                var fatherFieldNewSuspect = $('#fatherFieldNewSuspect').val();
                var motherFieldNewSuspect = $('#motherFieldNewSuspect').val();
                var nationalityFieldNewSuspect = $('#nationalityFieldNewSuspect').val();
                var ethnicFieldNewSuspect = $('#ethnicFieldNewSuspect').find(':selected').val();
                var jobFieldNewSuspect = $('#jobFieldNewSuspect').find(':selected').val();
                var religionFieldNewSuspect = $('#religionFieldNewSuspect').find(':selected').val();
                var educationFieldNewSuspect = $('#educationFieldNewSuspect').find(':selected').val();
                var maritalStatusFieldNewSuspect = $('#maritalStatusFieldNewSuspect').find(':selected')
                    .val();
                var phoneNumberFieldNewSuspect = $('#phoneNumberFieldNewSuspect').val();
                var isExistsPhoneNumberFieldNewSuspect = $(
                    'input[name="isExistsPhoneNumberFieldNewSuspect"]:checked').val();
                var isAvailablePhoneNumberFieldNewSuspect = $('#isAvailablePhoneNumberFieldNewSuspect').is(
                    ':checked');
                var emailFieldNewSuspect = $('#emailFieldNewSuspect').val();
                var isExistsEmailFieldNewSuspect = $('input[name="isExistsEmailFieldNewSuspect"]:checked')
                    .val();
                var isAvailableEmailFieldNewSuspect = $('#isAvailableEmailFieldNewSuspect').is(':checked');
                var countryFieldNewSuspect = $('#countryFieldNewSuspect').find(':selected').val();
                var provinceFieldNewSuspect = $('#provinceFieldNewSuspect').find(':selected').val();
                var regencyFieldNewSuspect = $('#regencyFieldNewSuspect').find(':selected').val();
                var districtFieldNewSuspect = $('#districtFieldNewSuspect').find(':selected').val();
                var villageFieldNewSuspect = $('#villageFieldNewSuspect').find(':selected').val();
                var addressFieldNewSuspect = $('#addressFieldNewSuspect').val();
                var isUnknownGenderFieldNewSuspect = $('#isUnknownGenderFieldNewSuspect').is(':checked');
                var isUnknownBirthPlaceFieldNewSuspect = $('#isUnknownBirthPlaceFieldNewSuspect').is(
                    ':checked');
                var isUnknownBirthDateFieldNewSuspect = $('#isUnknownBirthDateFieldNewSuspect').is(
                    ':checked');
                var isUnknownFatherFieldNewSuspect = $('#isUnknownFatherFieldNewSuspect').is(':checked');
                var isUnknownMotherFieldNewSuspect = $('#isUnknownMotherFieldNewSuspect').is(':checked');
                var isUnknownNationalityFieldNewSuspect = $('#isUnknownNationalityFieldNewSuspect').is(
                    ':checked');
                var isUnknownMaritalStatusFieldNewSuspect = $('#isUnknownMaritalStatusFieldNewSuspect').is(
                    ':checked');
                var isUnknownAddressFieldNewSuspect = $('#isUnknownAddressFieldNewSuspect').is(':checked');

                var suspectSourceId = $('#suspectDeterminationDataSource').find(':selected').val();
                var suspectSourceName = $('#suspectDeterminationDataSource').find(':selected').text();

                // Bersihkan error modal sebelumnya
                $('#addNewSuspectModal .is-invalid').removeClass('is-invalid');
                $('#addNewSuspectModal .select2-selection').removeClass('border border-danger is-invalid');
                $('#addNewSuspectModal .frontend-error, #addNewSuspectModal .invalid-feedback, #addNewSuspectModal small.text-danger').remove();

                var modalErrors = [];

                function markModalError(fieldSelector, message) {
                    var $field = $(fieldSelector);
                    if ($field.is(':radio')) {
                        $field.addClass('is-invalid');
                        var $container = $field.closest('.d-flex');
                        if ($container.next('.frontend-error').length === 0) {
                            $container.after('<div class="invalid-feedback d-block frontend-error">' + message + '</div>');
                        }
                    } else if ($field.next('.select2-container').length) {
                        $field.addClass('is-invalid');
                        $field.next('.select2-container').find('.select2-selection').addClass('border border-danger is-invalid');
                        if ($field.next('.select2-container').next('.frontend-error').length === 0) {
                            $field.next('.select2-container').after('<div class="invalid-feedback d-block frontend-error">' + message + '</div>');
                        }
                    } else {
                        $field.addClass('is-invalid');
                        if ($field.next('.frontend-error').length === 0) {
                            $field.after('<div class="invalid-feedback d-block frontend-error">' + message + '</div>');
                        }
                    }
                    if (!modalErrors.includes(message)) {
                        modalErrors.push(message);
                    }
                }

                function checkModalInput(fieldSelector, label) {
                    var $field = $(fieldSelector);
                    if ($field.is(':disabled') || $field.closest('.row').is(':hidden')) return;
                    var val = ($field.val() || '').trim();
                    if (!val || val === '') {
                        markModalError(fieldSelector, label + ' harus diisi');
                    }
                }

                function checkModalSelect(fieldSelector, label) {
                    var $field = $(fieldSelector);
                    if ($field.is(':disabled') || $field.closest('.row').is(':hidden')) return;
                    var val = $field.val();
                    if (!val || val === '' || val === null || val === '0') {
                        markModalError(fieldSelector, label + ' harus dipilih');
                    }
                }

                // 1. Jenis Identitas
                checkModalSelect('#identityTypeFieldNewSuspect', 'Jenis Identitas');

                // 2. Nomor Identitas
                checkModalInput('#identityNumberFieldNewSuspect', 'Nomor Identitas');
                var idErr = validateModalIdentityNumber();
                if (idErr) {
                    markModalError('#identityNumberFieldNewSuspect', idErr);
                }

                // 3. Nama
                checkModalInput('#nameFieldNewSuspect', 'Nama Lengkap');

                // 4. Jenis Kelamin
                if (!$('#isUnknownGenderFieldNewSuspect').is(':checked')) {
                    checkModalSelect('#genderFieldNewSuspect', 'Jenis Kelamin');
                }

                // 5. Tempat Lahir
                if (!$('#isUnknownBirthPlaceFieldNewSuspect').is(':checked')) {
                    checkModalInput('#birthPlaceFieldNewSuspect', 'Tempat Lahir');
                }

                // 6. Tanggal Lahir
                if (!$('#isUnknownBirthDateFieldNewSuspect').is(':checked')) {
                    checkModalInput('#birthDateFieldNewSuspect', 'Tanggal Lahir');
                    var bDateErr = validateModalBirthDate();
                    if (bDateErr) {
                        markModalError('#birthDateFieldNewSuspect', bDateErr);
                    }
                }

                // 7. Ayah Kandung (jika tidak dicentang tidak tahu)
                if (!$('#isUnknownFatherFieldNewSuspect').is(':checked')) {
                    checkModalInput('#fatherFieldNewSuspect', 'Nama Ayah Kandung');
                }

                // 8. Ibu Kandung (jika tidak dicentang tidak tahu)
                if (!$('#isUnknownMotherFieldNewSuspect').is(':checked')) {
                    checkModalInput('#motherFieldNewSuspect', 'Nama Ibu Kandung');
                }

                // 9. Kebangsaan
                if (!$('#isUnknownNationalityFieldNewSuspect').is(':checked')) {
                    checkModalInput('#nationalityFieldNewSuspect', 'Kebangsaan');
                }

                // 10. Suku
                checkModalSelect('#ethnicFieldNewSuspect', 'Suku');

                // 11. Pekerjaan
                checkModalSelect('#jobFieldNewSuspect', 'Pekerjaan');

                // 12. Agama
                checkModalSelect('#religionFieldNewSuspect', 'Agama');

                // 13. Pendidikan
                checkModalSelect('#educationFieldNewSuspect', 'Pendidikan');

                // 14. Status Kawin
                if (!$('#isUnknownMaritalStatusFieldNewSuspect').is(':checked')) {
                    checkModalSelect('#maritalStatusFieldNewSuspect', 'Status Kawin');
                }

                // 15. Nomor Telepon Radio & Input
                var phoneRadioVal = $('input[name="isExistsPhoneNumberFieldNewSuspect"]:checked').val();
                if (!phoneRadioVal) {
                    markModalError('input[name="isExistsPhoneNumberFieldNewSuspect"]', 'Pilihan nomor telepon harus dipilih');
                } else if (phoneRadioVal === 'true' && $('#isAvailablePhoneNumberFieldNewSuspect').is(':checked')) {
                    checkModalInput('#phoneNumberFieldNewSuspect', 'Nomor Telepon');
                    var pErr = validateModalPhone();
                    if (pErr) {
                        markModalError('#phoneNumberFieldNewSuspect', pErr);
                    }
                }

                // 16. Email Radio & Input
                var emailRadioVal = $('input[name="isExistsEmailFieldNewSuspect"]:checked').val();
                if (!emailRadioVal) {
                    markModalError('input[name="isExistsEmailFieldNewSuspect"]', 'Pilihan email harus dipilih');
                } else if (emailRadioVal === 'true' && $('#isAvailableEmailFieldNewSuspect').is(':checked')) {
                    checkModalInput('#emailFieldNewSuspect', 'Email');
                    var emErr = validateModalEmail();
                    if (emErr) {
                        markModalError('#emailFieldNewSuspect', emErr);
                    }
                }

                // 17. Negara & Wilayah
                checkModalSelect('#countryFieldNewSuspect', 'Negara');
                if ($('.countryChildrenLocationSectionNewSuspect').is(':visible') || $('#countryFieldNewSuspect').val() === 'C101') {
                    checkModalSelect('#provinceFieldNewSuspect', 'Provinsi');
                    checkModalSelect('#regencyFieldNewSuspect', 'Kabupaten/Kota');
                    checkModalSelect('#districtFieldNewSuspect', 'Kecamatan');
                    checkModalSelect('#villageFieldNewSuspect', 'Kelurahan/Desa');
                }

                // 18. Alamat
                if (!$('#isUnknownAddressFieldNewSuspect').is(':checked')) {
                    checkModalInput('#addressFieldNewSuspect', 'Alamat');
                }

                // Jika ada error di modal, scroll modal dengan halus ke paling atas
                if (modalErrors.length > 0) {
                    $('#addNewSuspectModal, #addNewSuspectModal .modal-body').stop().animate({
                        scrollTop: 0
                    }, 400);
                    return false;
                }

                // save add new suspect
                //append all value to table
                $('#suspectDeterminationTable').append(
                    '<tr>' +
                    '<td>' + $('#identityTypeFieldNewSuspect').find(':selected').text() + ' / ' +
                    identityNumberFieldNewSuspect + '</td>' +
                    '<td>' + nameFieldNewSuspect + '</td>' +
                    '<td>' + birthPlaceFieldNewSuspect + '/' + birthDateFieldNewSuspect + '</td>' +
                    '<td>' + suspectSourceName + '</td>' +
                    '<td>' +
                    '<input type="hidden" name="suspectDeterminationSuspectSourceId[]" value="' +
                    suspectSourceId + '">' +
                    '<input type="hidden" name="suspectDeterminationIds[]" value="">' +
                    '<input type="hidden" name="suspectDeterminationIdentityStatus[]" value="' +
                    identityStatusFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationIdentityTypeId[]" value="' +
                    identityTypeFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationIdentityNumber[]" value="' +
                    identityNumberFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationName[]" value="' +
                    nameFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationGenderId[]" value="' +
                    genderFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationBirthPlace[]" value="' +
                    birthPlaceFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationBirthDate[]" value="' +
                    birthDateFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationFatherName[]" value="' +
                    fatherFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationMotherName[]" value="' +
                    motherFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationNationality[]" value="' +
                    nationalityFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationEthnicId[]" value="' +
                    ethnicFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationJobId[]" value="' +
                    jobFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationReligionId[]" value="' +
                    religionFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationEducationId[]" value="' +
                    educationFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationMaritalStatusId[]" value="' +
                    maritalStatusFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationPhoneNumber[]" value="' +
                    phoneNumberFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationIsExistsPhoneNumber[]" value="' +
                    isExistsPhoneNumberFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationIsAvailablePhoneNumber[]" value="' +
                    isAvailablePhoneNumberFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationEmail[]" value="' +
                    emailFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationIsExistsEmail[]" value="' +
                    isExistsEmailFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationIsAvailableEmail[]" value="' +
                    isAvailableEmailFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationCountryId[]" value="' +
                    countryFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationProvinceId[]" value="' +
                    provinceFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationRegencyId[]" value="' +
                    regencyFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationDistrictId[]" value="' +
                    districtFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationVillageId[]" value="' +
                    villageFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationAddress[]" value="' +
                    addressFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationIsUnknownGender[]" value="' +
                    isUnknownGenderFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationIsUnknownBirthPlace[]" value="' +
                    isUnknownBirthPlaceFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationIsUnknownBirthDate[]" value="' +
                    isUnknownBirthDateFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationIsUnknownFather[]" value="' +
                    isUnknownFatherFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationIsUnknownMother[]" value="' +
                    isUnknownMotherFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationIsUnknownNationality[]" value="' +
                    isUnknownNationalityFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationIsUnknownMaritalStatus[]" value="' +
                    isUnknownMaritalStatusFieldNewSuspect + '">' +
                    '<input type="hidden" name="suspectDeterminationIsUnknownAddress[]" value="' +
                    isUnknownAddressFieldNewSuspect + '">' +

                    //'<button type="button" class="btn btn-warning btn-sm editSuspectDeterminationButton"><i class="bi bi-pencil-square"></i></button>' +
                    '<button type="button" class="btn btn-danger btn-sm ml-2 removeSuspectDeterminationButton"><i class="bi bi-trash"></i></button>' +
                    '</td>' +
                    '</tr>'
                );

                //clear input all value input
                $('#identityTypeFieldNewSuspect').val('').trigger('change');
                $('#identityNumberFieldNewSuspect').val('');
                $('#nameFieldNewSuspect').val('');
                $('#genderFieldNewSuspect').val('').trigger('change');
                $('#birthPlaceFieldNewSuspect').val('');
                $('#birthDateFieldNewSuspect').val('');
                $('#fatherFieldNewSuspect').val('');
                $('#motherFieldNewSuspect').val('');
                $('#nationalityFieldNewSuspect').val('');
                $('#ethnicFieldNewSuspect').val('177').trigger('change');
                $('#jobFieldNewSuspect').val('0').trigger('change');
                $('#religionFieldNewSuspect').val('0').trigger('change');
                $('#educationFieldNewSuspect').val('0').trigger('change');
                $('#maritalStatusFieldNewSuspect').val('').trigger('change');
                $('#phoneNumberFieldNewSuspect').val('');
                $('#emailFieldNewSuspect').val('');
                $('#countryFieldNewSuspect').val('').trigger('change');
                $('#provinceFieldNewSuspect').val('').trigger('change');
                $('#regencyFieldNewSuspect').val('').trigger('change');
                $('#districtFieldNewSuspect').val('').trigger('change');
                $('#villageFieldNewSuspect').val('').trigger('change');
                $('#addressFieldNewSuspect').val('');

                //close modal
                $('#addNewSuspectModal').modal('hide');
                $('.modal-backdrop').hide();

                // Hapus event listener removeSuspectDeterminationButton sebelumnya
                $(document).off('click', '.removeSuspectDeterminationButton');

                // Tambahkan event listener removeSuspectDeterminationButton yang baru
                $(document).on('click', '.removeSuspectDeterminationButton', function() {
                    $(this).closest('tr').remove();
                });
            });
        });

        // get case degree type
        $(document).ready(function() {
            // Handler for radio buttons change event
            $('input[name="documentType"]').on('change', function() {
                var selectedType = $(this).val();

                // Make AJAX call to get the options based on the selected type
                $.ajax({
                    url: "{{ route('doc.laporan-hasil-gelar-perkara-document.api.case-degree-types', ['accident_id' => $accidentId]) }}", // Replace with your backend URL
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        var data = response.data;
                        // Clear existing options and add new options
                        var caseDegreeType = $('#caseDegreeType');
                        caseDegreeType.empty().append($('<option>', {
                            value: '',
                            text: '--Pilih Jenis Gelar Perkara--'
                        }));
                        $.each(data, function(index, item) {
                            if (item.id != 0 && (item.name || '').toUpperCase() !== 'TIDAK DIKETAHUI') {
                                caseDegreeType.append($('<option>', {
                                    value: item.id,
                                    text: item.name,
                                    'data-emp-id': item.emp_id
                                }));
                            }
                        });
                    },
                    error: function(xhr) {
                        // Handle error if needed
                        console.log(xhr.responseText);
                    }
                });
            });
        });

        // Helper cek nilai field aman untuk array (select2 multiple) maupun string
        function hasFieldValue($field) {
            var raw = $field.val();
            if (raw === null || raw === undefined) return false;
            if (Array.isArray(raw)) return raw.length > 0;
            var str = String(raw).trim();
            return str !== '' && str !== '0';
        }

        // Auto-clear error merah ketika field diisi/diubah
        $(document).on('input change changeTime.timepicker hide.timepicker changeDate', 'input, textarea, select', function() {
            var $field = $(this);
            if ($field.closest('#addNewSuspectModal').length || $field.is('#identityNumberFieldNewSuspect, #phoneNumberFieldNewSuspect, #emailFieldNewSuspect, #birthDateFieldNewSuspect, #documentNumber')) {
                return;
            }
            if (hasFieldValue($field)) {
                $field.removeClass('is-invalid');
                if ($field.next('.select2-container').length) {
                    $field.next('.select2-container').find('.select2-selection').removeClass('border border-danger is-invalid');
                }
                $field.next('.frontend-error, .invalid-feedback').remove();
                $field.siblings('.frontend-error, .invalid-feedback').remove();
                $field.parent().find('.frontend-error, .invalid-feedback').remove();
            }
        });
        // Untuk select2
        $(document).on('select2:select select2:unselect change', 'select', function() {
            var $field = $(this);
            if ($field.closest('#addNewSuspectModal').length || $field.is('#identityNumberFieldNewSuspect, #phoneNumberFieldNewSuspect, #emailFieldNewSuspect, #birthDateFieldNewSuspect, #documentNumber')) {
                return;
            }
            if (hasFieldValue($field)) {
                $field.removeClass('is-invalid');
                if ($field.next('.select2-container').length) {
                    $field.next('.select2-container').find('.select2-selection').removeClass('border border-danger is-invalid');
                }
                $field.next('.frontend-error, .invalid-feedback').remove();
                $field.siblings('.frontend-error, .invalid-feedback').remove();
                $field.parent().find('.frontend-error, .invalid-feedback').remove();
            }
        });
        // Radio button auto clear
        $(document).on('change', 'input[type="radio"]', function() {
            var name = $(this).attr('name');
            $('input[name="' + name + '"]').removeClass('is-invalid');
            $('input[name="' + name + '"]').closest('.d-flex').next('.frontend-error, .invalid-feedback').remove();
            $('input[name="' + name + '"]').closest('.d-flex').find('.frontend-error, .invalid-feedback').remove();
        });
        // Clear table error saat tersangka ditambah
        $(document).on('click', '#suspectDeterminationOptionAddButtton, #saveAddNewSuspectForm', function() {
            $('#suspectDeterminationTable').removeClass('is-invalid border border-danger');
            $('#suspectDeterminationTable').next('.frontend-error, .invalid-feedback').remove();
        });

        // Continuous watcher untuk input yang diupdate oleh plugin popover (seperti timepicker)
        setInterval(function() {
            $('input.is-invalid, textarea.is-invalid, select.is-invalid').each(function() {
                var $field = $(this);
                if ($field.closest('#addNewSuspectModal').length || $field.is('#identityNumberFieldNewSuspect, #phoneNumberFieldNewSuspect, #emailFieldNewSuspect, #birthDateFieldNewSuspect, #documentNumber')) {
                    return;
                }
                if (hasFieldValue($field)) {
                    $field.removeClass('is-invalid');
                    if ($field.next('.select2-container').length) {
                        $field.next('.select2-container').find('.select2-selection').removeClass('border border-danger is-invalid');
                    }
                    $field.next('.frontend-error, .invalid-feedback').remove();
                    $field.siblings('.frontend-error, .invalid-feedback').remove();
                    $field.parent().find('.frontend-error, .invalid-feedback').remove();
                }
            });
        }, 200);

        // Validasi Submit Form
        $(document).ready(function() {
            $(document).on('click', '#laporanHasilGelarPerkaraFormSubmit', function(e) {
                e.preventDefault();

                // Bersihkan semua error sebelumnya
                $('.is-invalid').removeClass('is-invalid');
                $('.border.border-danger').removeClass('border border-danger');
                $('.select2-selection').removeClass('border border-danger is-invalid');
                $('.frontend-error').remove();
                $('.invalid-feedback').remove();

                var errors = [];

                // Helper: tandai field merah dan kumpulkan error
                function markError(fieldId, message) {
                    var $field = $(fieldId);
                    if ($field.is('table')) {
                        $field.addClass('border border-danger is-invalid');
                    } else if ($field.is(':radio')) {
                        $field.addClass('is-invalid');
                        var $container = $field.closest('.d-flex');
                        if ($container.next('.frontend-error').length === 0) {
                            $container.after('<div class="invalid-feedback d-block frontend-error">' + message + '</div>');
                        }
                        errors.push(message);
                        return;
                    } else {
                        $field.addClass('is-invalid');
                    }
                    if ($field.next('.select2-container').length) {
                        $field.next('.select2-container').find('.select2-selection').addClass('border border-danger is-invalid');
                    }
                    var $target = $field.next('.select2-container').length ? $field.next('.select2-container') : $field;
                    if ($target.next('.frontend-error').length === 0) {
                        $target.after('<div class="invalid-feedback d-block frontend-error">' + message + '</div>');
                    }
                    errors.push(message);
                }

                // Helper: cek select2 / select
                function checkSelect(fieldId, label) {
                    var $field = $(fieldId);
                    if (!hasFieldValue($field)) {
                        markError(fieldId, label + ' harus diisi');
                    } else {
                        $field.removeClass('is-invalid');
                        if ($field.next('.select2-container').length) {
                            $field.next('.select2-container').find('.select2-selection').removeClass('border border-danger is-invalid');
                        }
                        $field.next('.frontend-error, .invalid-feedback').remove();
                        $field.siblings('.frontend-error, .invalid-feedback').remove();
                        $field.parent().find('.frontend-error, .invalid-feedback').remove();
                    }
                }

                // Helper: cek input text
                function checkInput(fieldId, label) {
                    var $field = $(fieldId);
                    if ($field.is(':disabled')) return;
                    var raw = $field.val();
                    var val = (raw !== null && raw !== undefined) ? String(raw).trim() : '';
                    if (!val || val === '') {
                        markError(fieldId, label + ' harus diisi');
                    } else {
                        $field.removeClass('is-invalid');
                        $field.next('.frontend-error, .invalid-feedback').remove();
                        $field.parent().find('.frontend-error, .invalid-feedback').remove();
                    }
                }

                // 1. No Sprindik
                checkSelect('#suratPerintahPenyidikanDocument', 'No Surat Perintah Penyidikan');

                // 2. Jenis LHGP
                if (!$('input[name="documentType"]:checked').val()) {
                    markError('input[name="documentType"]', 'Jenis LHGP harus dipilih');
                }

                // 3. Jenis Gelar Perkara
                checkSelect('#caseDegreeType', 'Jenis Gelar Perkara');

                // 4. Ref Surat Undangan
                checkInput('#caseDegreeInviteReference', 'No Surat Undangan Gelar Perkara');

                // 5. Tanggal Surat Undangan
                checkInput('#caseDegreeInviteDate', 'Tanggal Surat Undangan Gelar Perkara');

                // 6. Tanggal Pelaksanaan
                checkInput('#date', 'Tanggal Pelaksanaan');

                // 7. Waktu Pelaksanaan
                checkInput('#time', 'Waktu Pelaksanaan');

                // 8. Zona Waktu
                checkSelect('#timezone', 'Zona Waktu Pelaksanaan');

                // 9. Tempat Pelaksanaan
                checkInput('#place', 'Tempat Pelaksanaan');

                // 10. Pimpinan Gelar Perkara
                checkInput('#caseDegreeLeader', 'Pimpinan Gelar Perkara');

                // 11. Jumlah Peserta
                var attendeesVal = ($('#attendees').val() || '').trim();
                if (!attendeesVal || isNaN(attendeesVal) || parseInt(attendeesVal) < 1) {
                    markError('#attendees', 'Jumlah Peserta harus diisi minimal 1');
                }

                // 12. Rekomendasi Tersangka (Berdasarkan Jenis Gelar Perkara)
                var caseDegreeTypeVal = $('#caseDegreeType').val();
                if (caseDegreeTypeVal === '1') {
                    if ($('#suspectDeterminationTable tbody tr').length === 0) {
                        markError('#suspectDeterminationTable', 'Tersangka yang direkomendasikan untuk ditetapkan status tersangkanya harus diisi minimal 1');
                    }
                } else if (caseDegreeTypeVal === '4') {
                    var arrestVal = $('#arrestSuspects').val();
                    if (!arrestVal || arrestVal.length === 0) {
                        markError('#arrestSuspects', 'Tersangka yang direkomendasikan untuk ditahan harus dipilih minimal 1');
                    }
                } else if (caseDegreeTypeVal === '16') {
                    var revVal = $('#revocationSuspects').val();
                    if (!revVal || revVal.length === 0) {
                        markError('#revocationSuspects', 'Tersangka yang direkomendasikan untuk dicabut status tersangkanya harus dipilih minimal 1');
                    }
                }

                // 13. Pembahasan
                checkInput('#discussion', 'Pembahasan');

                // 14. Kesimpulan
                checkInput('#conclusion', 'Kesimpulan');

                // 15. Penutup
                checkInput('#closing', 'Penutup');

                // 16. Tanggal Ditandatangani Dokumen
                checkInput('#documentDate', 'Tanggal Dokumen LHGP');
                var docDateVal = ($('#documentDate').val() || '').trim();
                if (docDateVal) {
                    var selectedDate = new Date(docDateVal);
                    var today = new Date();
                    today.setHours(0, 0, 0, 0);
                    selectedDate.setHours(0, 0, 0, 0);
                    if (selectedDate < today) {
                        markError('#documentDate', 'Tanggal Dokumen LHGP minimal hari ini (tidak boleh tanggal kemarin/masa lalu)');
                    }
                }

                // 17. Penandatangan
                if ($('#isUpperUnitDocument').is(':checked')) {
                    checkInput('#upperUnitOfficerName', 'Nama Pejabat Satuan Atas');
                    checkInput('#upperUnitOfficerRegisterNumber', 'NRP Pejabat Satuan Atas');
                    checkSelect('#upperUnitOfficerRank', 'Pangkat Pejabat Satuan Atas');
                    checkSelect('#upperUnitOfficerPosition', 'Jabatan Pejabat Satuan Atas');
                    if ($('#isOnBehalfOfSuperiorOfficer').is(':checked')) {
                        checkInput('#upperUnitSuperiorOfficerName', 'Atasan Pejabat Satuan Atas');
                    }
                } else {
                    checkSelect('#signatory', 'Yang Menandatangani');
                }

                // Jika ada error, scroll ke field pertama yang error
                if (errors.length > 0) {
                    var $firstError = $('.is-invalid, .border-danger').first();
                    var $target = null;
                    if ($firstError && $firstError.length) {
                        if ($firstError.is(':visible')) {
                            $target = $firstError;
                        } else if ($firstError.next('.select2-container').is(':visible')) {
                            $target = $firstError.next('.select2-container');
                        } else {
                            $target = $firstError.closest(':visible');
                        }
                    }
                    if (!$target || !$target.length || !$target.offset()) {
                        $target = $('.frontend-error:visible, .is-invalid:visible, .border-danger:visible').first();
                    }

                    if ($target && $target.length) {
                        if ($target[0] && typeof $target[0].scrollIntoView === 'function') {
                            $target[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        if ($target.offset()) {
                            $('html, body, .content-wrapper, .wrapper').animate({
                                scrollTop: Math.max(0, $target.offset().top - 120)
                            }, 400);
                        }
                    }
                    return;
                }

                // Lakukan validasi di sisi server menggunakan Ajax
                $.ajax({
                    url: "{{ route('doc.laporan-hasil-gelar-perkara-document.api.validate-request-form', ['accident_id' => $accidentId]) }}",
                    type: 'POST',
                    dataType: 'json',
                    data: $('#laporanHasilGelarPerkaraForm').serialize(),
                    success: function(response) {
                        // Cek jika validasi berhasil di sisi server
                        if (response.success) {
                            // sweetalert2 berhasil sebelum submit form
                            Swal.fire({
                                title: 'Berhasil',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                // Submit form
                                $('#laporanHasilGelarPerkaraForm').submit();
                            });
                        }
                    },
                    error: function(xhr) {
                        // Tangani error jika terjadi kesalahan saat melakukan validasi
                        var response = {};
                        try {
                            response = JSON.parse(xhr.responseText);
                        } catch(e) {
                            response = { message: 'Terjadi kesalahan pada server.' };
                        }

                        if (response.code == '422') {
                            var errorMessages = '';

                            $.each(response.errors, function(key, value) {
                                errorMessages += '- ' + value + '<br>';
                            });

                            return Swal.fire({
                                icon: 'error',
                                title: 'Mohon Periksa Kembali Isian Anda',
                                html: errorMessages,
                            });
                        }
                    }
                });
            });
        });
    </script>
@endpush
