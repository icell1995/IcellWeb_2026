@php
    $_title = 'Edit S.Ket Penetapan Tersangka';
@endphp


@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
@endpush

@section('content')
<a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"><i class="bi bi-arrow-left"></i>
        Kembali ke Progress Perkara</a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Edit Surat Ketetapan Tentang Penetapan Tersangka</h5>

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
            <form
                action="{{ route('doc.surat-ketetapan-tentang-penetapan-tersangka-document.update', ['accident_id' => $accidentId, 'id'=> $suratKetetapanTentangPenetapanTersangkaDocumentId]) }}"
                method="POST" enctype="multipart/form-data" id="suratKetetapanTentangPenetapanTersangkaForm">
                @csrf
                <input type="hidden" name="accidentId" id="accidentId" value="{{ $accidentId }}">
                <input type="hidden" name="caseFlag" id="caseFlag" value="{{ $accident->case_flag }}">

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="accidentNumber">Nomor LP</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
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
                    <label class="fw-bold col-sm-3 col-form-label" for="documentNumber">Nomor Dokumen<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="documentNumber" type="text"
                            class="form-control @error('documentNumber') is-invalid @enderror font-weight-bold"
                            name="documentNumber" value="{{ $suratKetetapanTentangPenetapanTersangkaDocument->document_number }}" required
                            placeholder="Masukkan Nomor Dokumen">

                        @error('documentNumber')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>


                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="suratPerintahPenyidikanDocument">No Sprindik<span class="text-danger fs-5">*</span>
                    </label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="suratPerintahPenyidikanDocument"
                            id="suratPerintahPenyidikanDocument">
                            <option value="">--Pilih No Surat Perintah Penyidikan--</option>
                            @foreach ($suratPerintahPenyidikanDocuments as $suratPerintahPenyidikanDocument)
                                <option value="{{ $suratPerintahPenyidikanDocument->id }}" @if($suratKetetapanTentangPenetapanTersangkaDocument->surat_perintah_penyidikan_document_id == $suratPerintahPenyidikanDocument->id){{'selected'}}@endif>
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
                    <label class="fw-bold col-sm-3 col-form-label" for="prosecutor">Kejaksaan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="prosecutor" id="prosecutor">
                            <option value="">--Pilih Kejaksaan--</option>
                            @foreach ($prosecutors as $prosecutor)
                                <option value="{{ $prosecutor->id }}" @if($suratKetetapanTentangPenetapanTersangkaDocument->prosecutor_id == $prosecutor->id){{'selected'}}@endif>{{ $prosecutor->name }}</option>
                            @endforeach
                        </select>

                        @error('prosecutor')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label">Tanggal Ditandatangani Dokumen<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input class="form-control" id="documentDate" name="documentDate"
                            placeholder="YYYY-MM-DD" autocomplete="off" value="{{$suratKetetapanTentangPenetapanTersangkaDocument->document_date}}"
                            data-provide="datepicker">

                        @error('documentDate')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label">Yang Menandatangani<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="signatory" id="signatory">
                            <option value="">--Pilih Yang Menandatangani--</option>
                            @foreach ($authorizedSignatories as $data)
                                @php
                                    $positionName = $data->position->name ?? '';
                                @endphp
                                <option value="{{ $data->id }}" data-register-number="{{ $data->register_number }}" 
                                    @if($suratKetetapanTentangPenetapanTersangkaDocumentOfficers->where('class', 'SIGNATORY')->where('register_number', $data->register_number)->count() != 0){{'selected'}}@endif>{{$data->register_number . ' - ' . $data->full_name . ' | ' . $positionName}}
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

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="suspectSource">Sumber Yang Menyebutkan Tersangka
                    </label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="suspectSource" id="suspectSource">
                            <option value="">--Pilih Sumber--</option>
                            @foreach ($suspectSources as $suspectSource)
                                <option value="{{ $suspectSource->id }}" data-code="{{ $suspectSource->code }}" @if($suratKetetapanTentangPenetapanTersangkaDocument->suspect_source_id == $suspectSource->id){{'selected'}}@endif>{{ $suspectSource->name }}</option>
                            @endforeach
                        </select>

                        @error('suspectSource')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0" id="resumeSuspectDeterminationDateSection" @if($suratKetetapanTentangPenetapanTersangkaDocument->suspect_source_id != 4)style="display: none;"@endif>
                    <label class="fw-bold col-sm-3 col-form-label">Tanggal Resume Penetapan Tersangka<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input class="form-control" id="resumeSuspectDeterminationDate"
                            name="resumeSuspectDeterminationDate" placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{$suratKetetapanTentangPenetapanTersangkaDocument->resume_suspect_determination_date}}" data-provide="datepicker">

                        @error('resumeSuspectDeterminationDate')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0" id="laporanHasilGelarPerkaraDocumentSuspectDeterminationSection" @if($suratKetetapanTentangPenetapanTersangkaDocument->suspect_source_id != 5)style="display: none;"@endif>
                    <label class="fw-bold col-sm-3 col-form-label"
                        for="laporanHasilGelarPerkaraDocumentSuspectDetermination">Tanggal Gelar Perkara Penetapan
                        Tersangka<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="laporanHasilGelarPerkaraDocumentSuspectDetermination"
                            id="laporanHasilGelarPerkaraDocumentSuspectDetermination">
                            <option value="">--Pilih Tanggal LHGP Penetapan Tersangka--</option>
                            @foreach ($laporanHasilGelarPerkaraDocumentSuspectDeterminations as $laporanHasilGelarPerkaraDocumentSuspectDetermination)
                                <option value="{{ $laporanHasilGelarPerkaraDocumentSuspectDetermination->id }}"
                                    @if($suratKetetapanTentangPenetapanTersangkaDocument->laporan_hasil_gelar_perkara_document_id == $laporanHasilGelarPerkaraDocumentSuspectDetermination->id){{'selected'}}@endif>{{Carbon\Carbon::parse($laporanHasilGelarPerkaraDocumentSuspectDetermination->document_date)->locale('id')->translatedFormat('l') . ', ' . Carbon\Carbon::parse($laporanHasilGelarPerkaraDocumentSuspectDetermination->document_date)->locale('id')->translatedFormat('d F Y')}}
                                </option>
                            @endforeach
                        </select>

                        @error('laporanHasilGelarPerkaraDocumentSuspectDetermination')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0" id="suspectSection" @if(empty($suratKetetapanTentangPenetapanTersangkaDocument->suspect_source_id))style="display: none;"@endif>
                    <label class="fw-bold col-sm-3 col-form-label" for="suspect">Tersangka yang Ditetapkan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="suspect" id="suspect">
                            <option value="">--Pilih Tersangka--</option>
                        </select>

                        @error('suspect')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>

                <div id="suspectDetail" style="display:none;">
                    <h5 class="fw-bold text-blue-dark">LENGKAPI DATA TERSANGKA</h5>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-3 col-form-label" for="identityTypeFieldSuspect">Jenis
                                    Identitas<span class="text-danger fs-5">*</span></label>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                    <select class="form-control select2" id="identityTypeFieldSuspect"
                                        name="identityTypeFieldSuspect">
                                        <option value="">--Pilih Jenis Identitas--</option>
                                        @foreach ($identityTypes as $identityType)
                                            <option value="{{ $identityType->id }}"
                                                data-identity-type-name="{{ $identityType->name }}">
                                                {{ $identityType->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-3 col-form-label" for="identityNumberFieldSuspect">Nomor
                                    Identitas<span class="text-danger fs-5">*</span></label>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                    <input type="text" class="form-control" id="identityNumberFieldSuspect"
                                        name="identityNumberFieldSuspect" placeholder="Nomor Identitas">
                                </div>
                            </div>

                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-3 col-form-label" for="nameFieldSuspect">Nama<span class="text-danger fs-5">*</span></label>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                    <input type="text" class="form-control" id="nameFieldSuspect"
                                        name="nameFieldSuspect" placeholder="Nama Lengkap">
                                </div>
                            </div>

                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-3 col-form-label" for="genderFieldSuspect">Jenis Kelamin<span class="text-danger fs-5">*</span>
                                </label>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                    <select class="form-control select2" id="genderFieldSuspect"
                                        name="genderFieldSuspect">
                                        <option value="">--Pilih Jenis Kelamin--</option>
                                        @foreach ($genders as $gender)
                                            <option value="{{ $gender->id }}" data-gender-name="{{ $gender->name }}">
                                                {{ $gender->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="isUnknownGenderFieldSuspect"
                                            name="isUnknownGenderFieldSuspect" value="true" aria-label="...">
                                        <label for="isUnknownGenderFieldSuspect">
                                            Tidak Tahu
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-3 col-form-label" for="birthPlaceFieldSuspect">Tempat Lahir<span class="text-danger fs-5">*</span>
                                </label>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                    <input type="text" class="form-control" id="birthPlaceFieldSuspect"
                                        name="birthPlaceFieldSuspect" placeholder="Tempat Lahir">
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="isUnknownBirthPlaceFieldSuspect"
                                            name="isUnknownBirthPlaceFieldSuspect" value="true" aria-label="...">
                                        <label for="isUnknownBirthPlaceFieldSuspect">
                                            Tidak Tahu
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-3 col-form-label" for="birthDateFieldSuspect">Tanggal Lahir<span class="text-danger fs-5">*</span>
                                </label>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                    <input type="text" class="form-control" id="birthDateFieldSuspect"
                                        name="birthDateFieldSuspect" placeholder="YYYY-MM-DD" data-provide="datepicker">
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="isUnknownBirthDateFieldSuspect"
                                            name="isUnknownBirthDateFieldSuspect" value="true" aria-label="...">
                                        <label for="isUnknownBirthDateFieldSuspect">
                                            Tidak Tahu
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-3 col-form-label" for="fatherFieldSuspect">Ayah Kandung
                                </label>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                    <input type="text" class="form-control" id="fatherFieldSuspect"
                                        name="fatherFieldSuspect" placeholder="Nama Ayah Kandung">
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="isUnknownFatherFieldSuspect"
                                            name="isUnknownFatherFieldSuspect" value="true" aria-label="...">
                                        <label for="isUnknownFatherFieldSuspect">
                                            Tidak Tahu
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-3 col-form-label" for="motherFieldSuspect">Ibu Kandung
                                </label>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                    <input type="text" class="form-control" id="motherFieldSuspect"
                                        name="motherFieldSuspect" placeholder="Nama Ibu Kandung">
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="isUnknownMotherFieldSuspect"
                                            name="isUnknownMotherFieldSuspect" value="true" aria-label="...">
                                        <label for="isUnknownMotherFieldSuspect">
                                            Tidak Tahu
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-3 col-form-label" for="nationalityFieldSuspect">Kebangsaan<span class="text-danger fs-5">*</span>
                                </label>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                    <input type="text" class="form-control" id="nationalityFieldSuspect"
                                        name="nationalityFieldSuspect" placeholder="Kebangsaan">
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="isUnknownNationalityFieldSuspect"
                                            name="isUnknownNationalityFieldSuspect" value="true" aria-label="...">
                                        <label for="isUnknownNationalityFieldSuspect">
                                            Tidak Tahu
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-3 col-form-label" for="ethnicFieldSuspect">Suku<span class="text-danger fs-5">*</span></label>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                    <select class="form-control select2" id="ethnicFieldSuspect"
                                        name="ethnicFieldSuspect">
                                        <option value="">--Pilih Suku--</option>
                                        @foreach ($ethnics as $ethnic)
                                            <option value="{{ $ethnic->id }}" data-ethnic-name="{{ $ethnic->name }}">
                                                {{ $ethnic->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-3 col-form-label" for="jobFieldSuspect">Pekerjaan<span class="text-danger fs-5">*</span></label>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                    <select class="form-control select2" id="jobFieldSuspect" name="jobFieldSuspect">
                                        <option value="">--Pilih Pekerjaan--</option>
                                        @foreach ($jobs as $job)
                                            <option value="{{ $job->id }}" data-job-name="{{ $job->name }}">
                                                {{ $job->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-3 col-form-label" for="religionFieldSuspect">Agama<span class="text-danger fs-5">*</span></label>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                    <select class="form-control select2" id="religionFieldSuspect"
                                        name="religionFieldSuspect">
                                        <option value="">--Pilih Agama--</option>
                                        @foreach ($religions as $religion)
                                            <option value="{{ $religion->id }}"
                                                data-religion-name="{{ $religion->name }}">{{ $religion->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-3 col-form-label" for="educationFieldSuspect">Pendidikan<span class="text-danger fs-5">*</span>
                                </label>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                    <select class="form-control select2" id="educationFieldSuspect"
                                        name="educationFieldSuspect">
                                        <option value="">--Pilih Pendidikan--</option>
                                        @foreach ($educations as $education)
                                            <option value="{{ $education->id }}"
                                                data-education-name="{{ $education->name }}">{{ $education->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-3 col-form-label" for="maritalStatusFieldSuspect">Status
                                    Kawin<span class="text-danger fs-5">*</span></label>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                    <select class="form-control select2" id="maritalStatusFieldSuspect"
                                        name="maritalStatusFieldSuspect">
                                        <option value="">--Pilih Status Kawin--</option>
                                        @foreach ($maritalStatuses as $maritalStatus)
                                            <option value="{{ $maritalStatus->id }}"
                                                data-marital-status-name="{{ $maritalStatus->name }}">
                                                {{ $maritalStatus->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="isUnknownMaritalStatusFieldSuspect"
                                            name="isUnknownMaritalStatusFieldSuspect" value="true" aria-label="...">
                                        <label for="isUnknownMaritalStatusFieldSuspect">
                                            Tidak Tahu
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-3 col-form-label" for="phoneNumberFieldSuspect">Nomor Telepon
                                </label>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                    <div class="d-flex mb-3">
                                        <div class="form-check m-1">
                                            <input class="form-check-input" type="radio" id="existsPhoneNumberFieldSuspect"
                                                name="isExistsPhoneNumberFieldSuspect" value="true">
                                            <label for="existsPhoneNumberFieldSuspect">
                                                Ada No Telp
                                            </label>
                                        </div>
                                        <div class="form-check m-1">
                                            <input class="form-check-input" type="radio" id="notExistsPhoneNumberFieldSuspect"
                                                name="isExistsPhoneNumberFieldSuspect" value="false">
                                            <label for="notExistsPhoneNumberFieldSuspect">
                                                Tidak ada No Telp
                                            </label>
                                        </div>
                                    </div>

                                    <input type="text" class="form-control mb-3" id="phoneNumberFieldSuspect"
                                        name="phoneNumberFieldSuspect" placeholder="Nomor Telepon">

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="isAvailablePhoneNumberFieldSuspect"
                                            name="isAvailablePhoneNumberFieldSuspect" value="true" aria-label="...">
                                        <label for="isAvailablePhoneNumberFieldSuspect">
                                            Bersedia memberikan nomor telepon?
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-3 col-form-label" for="emailFieldSuspect">Email</label>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                    <div class="d-flex mb-3">
                                        <div class="form-check m-1">
                                            <input class="form-check-input" type="radio" id="existsEmailFieldSuspect"
                                                name="isExistsEmailFieldSuspect" value="true">
                                            <label for="existsEmailFieldSuspect">
                                                Ada Email
                                            </label>
                                        </div>
                                        <div class="form-check m-1">
                                            <input class="form-check-input" type="radio" id="notExistsEmailFieldSuspect"
                                                name="isExistsEmailFieldSuspect" value="false">
                                            <label for="notExistsEmailFieldSuspect">
                                                Tidak ada Email
                                            </label>
                                        </div>
                                    </div>

                                    <input type="text" class="form-control mb-3" id="emailFieldSuspect"
                                        name="emailFieldSuspect" placeholder="Email">

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="isAvailableEmailFieldSuspect"
                                            name="isAvailableEmailFieldSuspect" value="true" aria-label="...">
                                        <label for="isAvailableEmailFieldSuspect">
                                            Bersedia memberikan email?
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-3 col-form-label" for="countryFieldSuspect">Negara<span class="text-danger fs-5">*</span></label>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                    <select class="form-control select2" id="countryFieldSuspect"
                                        name="countryFieldSuspect">
                                        <option value="">--Pilih Negara--</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}"
                                                data-country-name="{{ $country->name }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="countryChildrenLocationSectionSuspect" style="display:none;">
                                <div class="input-group row mb-3 ms-0">
                                    <label class="fw-bold col-sm-3 col-form-label" for="provinceFieldSuspect">Provinsi<span class="text-danger fs-5">*</span>
                                    </label>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                        <select class="form-control select2" id="provinceFieldSuspect"
                                            name="provinceFieldSuspect">
                                            <option value="">--Pilih Provinsi--</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="input-group row mb-3 ms-0">
                                    <label class="fw-bold col-sm-3 col-form-label"
                                        for="regencyFieldSuspect">Kabupaten/Kota<span class="text-danger fs-5">*</span></label>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                        <select class="form-control select2" id="regencyFieldSuspect"
                                            name="regencyFieldSuspect">
                                            <option value="">--Pilih Kabupaten/Kota--</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="input-group row mb-3 ms-0">
                                    <label class="fw-bold col-sm-3 col-form-label" for="districtFieldSuspect">Kecamatan<span class="text-danger fs-5">*</span>
                                    </label>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                        <select class="form-control select2" id="districtFieldSuspect"
                                            name="districtFieldSuspect">
                                            <option value="">--Pilih Kecamatan--</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="input-group row mb-3 ms-0">
                                    <label class="fw-bold col-sm-3 col-form-label"
                                        for="villageFieldSuspect">Kelurahan/Desa</label>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                        <select class="form-control select2" id="villageFieldSuspect"
                                            name="villageFieldSuspect">
                                            <option value="">--Pilih Kelurahan/Desa--</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-3 col-form-label" for="addressFieldSuspect">Alamat<span class="text-danger fs-5">*</span></label>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                    <input type="text" class="form-control" id="addressFieldSuspect"
                                        name="addressFieldSuspect" placeholder="Alamat">
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="isUnknownAddressFieldSuspect"
                                            name="isUnknownAddressFieldSuspect" value="true" aria-label="...">
                                        <label for="isUnknownAddressFieldSuspect">
                                            Tidak Tahu
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    @if($accident->case_flag != 'JATANLIN')
                        <div id="suspectVehicle">
                            <h5 class="fw-bold text-blue-dark">KENDARAAN YANG TERKAIT DENGAN TERSANGKA</h5>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="input-group row mb-3 ms-0">
                                        <label class="fw-bold col-sm-2 col-form-label" for="vehicleFieldSuspect">Kendaraan<span class="text-danger fs-5">*</span></label>
                                        <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                            <select class="form-control select2" id="vehicleFieldSuspect"
                                                name="vehicleFieldSuspect">
                                                <option value="">--Pilih Kendaraan--</option>
                                                @foreach ($vehicleList as $vehicle)
                                                    <option value="{{ $vehicle['nopol'] }}"
                                                        data-identity-number="{{ $vehicle['nomor_identitas'] ?? '' }}"
                                                        data-identity-type="{{ $vehicle['tipe_identitas'] ?? '' }}"
                                                        data-driving-license-type="{{ $vehicle['jenis_sim'] ?? '' }}"
                                                        data-driver-name="{{ $vehicle['nama_pengemudi'] ?? '' }}"
                                                        data-accident-number="{{ $vehicle['no_lp'] ?? '' }}"
                                                        data-vehicle-type="{{ $vehicle['jenis_ranmor'] ?? '' }}"
                                                        data-vehicle-plate="{{ $vehicle['nopol'] ?? '' }}"
                                                        data-accident-location="{{ $vehicle['lokasi_kejadian'] ?? '' }}"
                                                        data-accident-date="{{ $vehicle['tgl_kejadian'] ?? '' }}"
                                                        data-accident-type="{{ $vehicle['jenis_kecelakaan'] ?? '' }}"
                                                        data-reason="{{ $vehicle['penyebab'] ?? '' }}"
                                                        data-victim="{{ $vehicle['total_korban'] ?? '' }}"
                                                        data-material-loss="{{ $vehicle['total_kermat'] ?? '' }}"
                                                        data-latitude="{{ $vehicle['latitude'] ?? '' }}"
                                                        data-longitude="{{ $vehicle['longtitude'] ?? '' }}"
                                                        data-accident-type-id="{{ $vehicle['accident_type_id'] ?? '' }}"
                                                        data-accident-type-name="{{ $vehicle['accident_type_name'] ?? '' }}"
                                                        data-vehicle-type-id="{{ $vehicle['vehicle_type_id'] ?? '' }}"
                                                        data-vehicle-type-name="{{ $vehicle['vehicle_type_name'] ?? '' }}"
                                                        data-accident-cause-id="{{ $vehicle['accident_cause_id'] ?? '' }}"
                                                        data-accident-cause-name="{{ $vehicle['accident_cause_name'] ?? '' }}"
                                                        data-identity-type-id="{{ $vehicle['identity_type_id'] ?? '' }}"
                                                        data-identity-type-name="{{ $vehicle['identity_type_name'] ?? '' }}"
                                                        data-driving-license-type-id="{{ $vehicle['driving_license_type_id'] ?? '' }}"
                                                        data-driving-license-type-name="{{ $vehicle['driving_license_type_name'] ?? '' }}"
                                                        >
                                                        Nopol : {{ $vehicle['nopol'] }} || {{ $vehicle['vehicle_type_name'] ?? '-' }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endif
                </div>

                <hr>

                @if(strtotime($accident->report_date) < strtotime('2024-01-01') || $suratKetetapanTentangPenetapanTersangkaDocument->is_legacy == true || $accident->police->is_whitelisted_document_legacy == true && strtotime($accident->police->start_date_whitelisted_document_legacy) <= strtotime($accident->report_date) && strtotime($accident->report_date) <= strtotime($accident->police->end_date_whitelisted_document_legacy))
                    @include('docs.components.form.checkbox.is-legacy', ['document' => $suratKetetapanTentangPenetapanTersangkaDocument])
                @endif

                <div class="text-center">
                    <button type="submit" class="btn btn-dark-blue"
                        id="suratKetetapanTentangPenetapanTersangkaFormSubmit">
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

@php
    $suspects = $suspects;
    $selectedSuspect = $suratKetetapanTentangPenetapanTersangkaDocument->suspect->first();
@endphp

@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js" defer></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" defer></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js" defer></script>
<script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
<script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

@if(strtotime($accident->report_date) < strtotime('2024-01-01') || $suratKetetapanTentangPenetapanTersangkaDocument->is_legacy == true || $accident->police->is_whitelisted_document_legacy == true && strtotime($accident->police->start_date_whitelisted_document_legacy) <= strtotime($accident->report_date) && strtotime($accident->report_date) <= strtotime($accident->police->end_date_whitelisted_document_legacy))
    @include('docs.components.form.checkbox.is-legacy-js')
@endif

<script type="text/javascript">
    $(document).ready(function() {
        var suspects = @json($suspects);
        var selectedSuspect = @json($selectedSuspect);

        $(function() {
            var suspectOptions = $('#suspect');

            suspectOptions.empty();

            suspectOptions.append($('<option>', {
                value: '',
                text : '--Pilih Tersangka--',
            }));

            for (var key in suspects) {
                if (suspects.hasOwnProperty(key)) {
                    var data = suspects[key];

                    suspectOptions.append($('<option>', {
                        value: data.id,
                        text: data.name,

                        "data-identity-type-id": (data.identity_type_id) ? data.identity_type_id : '',
                        "data-identity-number": (data.identity_number) ? data.identity_number : '',
                        "data-name": data.name,
                        "data-gender-id": (data.gender_id) ? data.gender_id : '',
                        "data-birth-place": (data.birth_place) ? data.birth_place : '',
                        "data-birth-date": (data.birth_date) ? data.birth_date : '',
                        "data-father-name": (data.father_name) ? data.father_name : '',
                        "data-mother-name": (data.mother_name) ? data.mother_name : '',
                        "data-nationality": (data.nationality) ? data.nationality : '',
                        "data-ethnic-id": (data.ethnic_id) ? data.ethnic_id : '',
                        "data-job-id": (data.job_id) ? data.job_id : '',
                        "data-religion-id": (data.religion_id) ? data.religion_id : '',
                        "data-education-id": (data.education_id) ? data.education_id : '',
                        "data-marital-status-id": (data.marital_status_id) ? data.marital_status_id : '',
                        "data-phone-number": (data.phone_number) ? data.phone_number : '',
                        "data-email": (data.email_address) ? data.email_address : '',
                        "data-country-id": (data.country_id) ? data.country_id : '',
                        "data-province-id": (data.province_id) ? data.province_id : '',
                        "data-regency-id": (data.regency_id) ? data.regency_id : '',
                        "data-district-id": (data.district_id) ? data.district_id : '',
                        "data-village-id": (data.village_id) ? data.village_id : '',
                        "data-address": (data.address) ? data.address : '',

                        "data-is-available-email": (data.properties) ? data.properties.is_available_email : null,
                        "data-is-available-phone-number": (data.properties) ? data.properties.is_available_phone_number : null,
                        "data-is-exists-email": (data.properties) ? data.properties.is_exists_email : null,
                        "data-is-exists-phone-number": (data.properties) ? data.properties.is_exists_phone_number : null,
                        "data-is-unknown-address": (data.properties) ? data.properties.is_unknown_address : null,
                        "data-is-unknown-birth-date": (data.properties) ? data.properties.is_unknown_birth_date : null,
                        "data-is-unknown-birth-place": (data.properties) ? data.properties.is_unknown_birth_place : null,
                        "data-is-unknown-father": (data.properties) ? data.properties.is_unknown_father : null,
                        "data-is-unknown-mother": (data.properties) ? data.properties.is_unknown_mother : null,
                        "data-is-unknown-nationality": (data.properties) ? data.properties.is_unknown_nationality : null,
                        "data-is-unknown-gender": (data.properties) ? data.properties.is_unknown_gender : null,
                        "data-is-unknown-marital-status": (data.properties) ? data.properties.is_unknown_marital_status : null,
                        
                        "data-vehicle-plate-number": (data.vehicle_associated_suspect) ? data.vehicle_associated_suspect.plate_number : null,
                    }));
                }
            }

            suspectOptions.val(selectedSuspect.id).trigger('change');
        });
    });

    $(document).ready(function() {
        setInterval(function() {
            $('#attentionBox').toggleClass('alert-danger alert-warning');
        }, 1000);

        $('#documentDate').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: "true",
            orientation: 'auto bottom',
            endDate: new Date()
        });
        $('#documentDate').keydown(function(e) {
            e.preventDefault();
            return false;
        });

        $('#birthDateFieldSuspect').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: "true",
            endDate: new Date()
        });
        $('#birthDateFieldSuspect').keydown(function(e) {
            e.preventDefault();
            return false;
        });

        $('#resumeSuspectDeterminationDate').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: "true",
            endDate: new Date()
        });
        $('#resumeSuspectDeterminationDate').keydown(function(e) {
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
    });

    $(document).ready(function() {
        $('#suspectSource').on('change', function() {
            var suspectSourceCode = $(this).find(':selected').data('code');

            if(suspectSourceCode == 'SS-SKTPT-TDDDLP'){
                $('#resumeSuspectDeterminationDateSection').show();
                $('#suspectSection').show();
                $('#laporanHasilGelarPerkaraDocumentSuspectDeterminationSection').hide();
                $("#suspectSource option[value='']").remove();
            }else if(suspectSourceCode == 'SS-SKTPT-TDMGPPT'){
                $('#laporanHasilGelarPerkaraDocumentSuspectDeterminationSection').show();
                $('#suspectSection').show();
                $('#resumeSuspectDeterminationDateSection').hide();
                $("#suspectSource option[value='']").remove();
            }
        });
    });

    $(document).ready(function() {
        $('#suspectSource').on('change', function() {
            var suspectSourceCode = $(this).find(':selected').data('code');

            $('#suspectDetail').hide();
            $('#laporanHasilGelarPerkaraDocumentSuspectDetermination').val('').trigger('change');

            if(suspectSourceCode == 'SS-SKTPT-TDDDLP'){
                $('#suspect').empty().append($('<option>', {
                    value: '',
                    text : '--Pilih Tersangka--',
                }));

                $.ajax({
                    url: "{{route('doc.surat-ketetapan-tentang-penetapan-tersangka-document.api.suspects', ['accident_id' => $accidentId])}}", // Replace with your backend URL
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        'notFlag': 'TERSANGKA',
                    },
                    success: function(response) {
                        var data = response.data;
                        //Clear existing options and add new options
                        var suspect = $('#suspect');

                        suspect.empty();

                        suspect.append($('<option>', {
                            value: '',
                            text : '--Pilih Tersangka--',
                        }));

                        $.each(data, function(index, data) {
                            suspect.append($('<option>', {
                                value: data.id,
                                text: data.name,

                                "data-identity-type-id": (data.identity_type_id) ? data.identity_type_id : '',
                                "data-identity-number": (data.identity_number) ? data.identity_number : '',
                                "data-name": data.name,
                                "data-gender-id": (data.gender_id) ? data.gender_id : '',
                                "data-birth-place": (data.birth_place) ? data.birth_place : '',
                                "data-birth-date": (data.birth_date) ? data.birth_date : '',
                                "data-father-name": (data.father_name) ? data.father_name : '',
                                "data-mother-name": (data.mother_name) ? data.mother_name : '',
                                "data-nationality": (data.nationality) ? data.nationality : '',
                                "data-ethnic-id": (data.ethnic_id) ? data.ethnic_id : '',
                                "data-job-id": (data.job_id) ? data.job_id : '',
                                "data-religion-id": (data.religion_id) ? data.religion_id : '',
                                "data-education-id": (data.education_id) ? data.education_id : '',
                                "data-marital-status-id": (data.marital_status_id) ? data.marital_status_id : '',
                                "data-phone-number": (data.phone_number) ? data.phone_number : '',
                                "data-email": (data.email_address) ? data.email_address : '',
                                "data-country-id": (data.country_id) ? data.country_id : '',
                                "data-province-id": (data.province_id) ? data.province_id : '',
                                "data-regency-id": (data.regency_id) ? data.regency_id : '',
                                "data-district-id": (data.district_id) ? data.district_id : '',
                                "data-village-id": (data.village_id) ? data.village_id : '',
                                "data-address": (data.address) ? data.address : '',

                                "data-is-available-email": (data.properties) ? data.properties.is_available_email : null,
                                "data-is-available-phone-number": (data.properties) ? data.properties.is_available_phone_number : null,
                                "data-is-exists-email": (data.properties) ? data.properties.is_exists_email : null,
                                "data-is-exists-phone-number": (data.properties) ? data.properties.is_exists_phone_number : null,
                                "data-is-unknown-address": (data.properties) ? data.properties.is_unknown_address : null,
                                "data-is-unknown-birth-date": (data.properties) ? data.properties.is_unknown_birth_date : null,
                                "data-is-unknown-birth-place": (data.properties) ? data.properties.is_unknown_birth_place : null,
                                "data-is-unknown-father": (data.properties) ? data.properties.is_unknown_father : null,
                                "data-is-unknown-mother": (data.properties) ? data.properties.is_unknown_mother : null,
                                "data-is-unknown-nationality": (data.properties) ? data.properties.is_unknown_nationality : null,
                                "data-is-unknown-gender": (data.properties) ? data.properties.is_unknown_gender : null,
                                "data-is-unknown-marital-status": (data.properties) ? data.properties.is_unknown_marital_status : null,
                            }));
                        });
                    },
                    error: function(xhr) {
                        // Handle error if needed
                        console.log(xhr.responseText);
                    }
                });

            }else if(suspectSourceCode == 'SS-SKTPT-TDMGPPT'){
                $('#suspect').empty().append($('<option>', {
                    value: '',
                    text : '--Pilih Tersangka--',
                }));

                $(document).on('change', '#laporanHasilGelarPerkaraDocumentSuspectDetermination', function() {
                    var laporanHasilGelarPerkaraDocumentId = $(this).find(':selected').val();

                    $('#suspectDetail').hide();
                    $('#suspect').empty().append($('<option>', {
                        value: '',
                        text : '--Pilih Tersangka--',
                    }));

                    if(laporanHasilGelarPerkaraDocumentId)
                    {
                        $.ajax({
                            url: "{{route('doc.surat-ketetapan-tentang-penetapan-tersangka-document.api.suspects', ['accident_id' => $accidentId])}}", // Replace with your backend URL
                            type: 'GET',
                            dataType: 'json',
                            data: {
                                'laporanHasilGelarPerkaraDocumentId': laporanHasilGelarPerkaraDocumentId,
                            },
                            success: function(response) {
                                var data = response.data;
                                //Clear existing options and add new options
                                var suspect = $('#suspect');

                                suspect.empty();

                                suspect.append($('<option>', {
                                    value: '',
                                    text : '--Pilih Tersangka--',
                                }));

                                $.each(data, function(index, data) {
                                    suspect.append($('<option>', {
                                        value: data.id,
                                        text: data.name,

                                        "data-identity-type-id": (data.identity_type_id) ? data.identity_type_id : '',
                                        "data-identity-number": (data.identity_number) ? data.identity_number : '',
                                        "data-name": data.name,
                                        "data-gender-id": (data.gender_id) ? data.gender_id : '',
                                        "data-birth-place": (data.birth_place) ? data.birth_place : '',
                                        "data-birth-date": (data.birth_date) ? data.birth_date : '',
                                        "data-father-name": (data.father_name) ? data.father_name : '',
                                        "data-mother-name": (data.mother_name) ? data.mother_name : '',
                                        "data-nationality": (data.nationality) ? data.nationality : '',
                                        "data-ethnic-id": (data.ethnic_id) ? data.ethnic_id : '',
                                        "data-job-id": (data.job_id) ? data.job_id : '',
                                        "data-religion-id": (data.religion_id) ? data.religion_id : '',
                                        "data-education-id": (data.education_id) ? data.education_id : '',
                                        "data-marital-status-id": (data.marital_status_id) ? data.marital_status_id : '',
                                        "data-phone-number": (data.phone_number) ? data.phone_number : '',
                                        "data-email": (data.email_address) ? data.email_address : '',
                                        "data-country-id": (data.country_id) ? data.country_id : '',
                                        "data-province-id": (data.province_id) ? data.province_id : '',
                                        "data-regency-id": (data.regency_id) ? data.regency_id : '',
                                        "data-district-id": (data.district_id) ? data.district_id : '',
                                        "data-village-id": (data.village_id) ? data.village_id : '',
                                        "data-address": (data.address) ? data.address : '',

                                        "data-is-available-email": (data.properties) ? data.properties.is_available_email : null,
                                        "data-is-available-phone-number": (data.properties) ? data.properties.is_available_phone_number : null,
                                        "data-is-exists-email": (data.properties) ? data.properties.is_exists_email : null,
                                        "data-is-exists-phone-number": (data.properties) ? data.properties.is_exists_phone_number : null,
                                        "data-is-unknown-address": (data.properties) ? data.properties.is_unknown_address : null,
                                        "data-is-unknown-birth-date": (data.properties) ? data.properties.is_unknown_birth_date : null,
                                        "data-is-unknown-birth-place": (data.properties) ? data.properties.is_unknown_birth_place : null,
                                        "data-is-unknown-father": (data.properties) ? data.properties.is_unknown_father : null,
                                        "data-is-unknown-mother": (data.properties) ? data.properties.is_unknown_mother : null,
                                        "data-is-unknown-nationality": (data.properties) ? data.properties.is_unknown_nationality : null,
                                        "data-is-unknown-gender": (data.properties) ? data.properties.is_unknown_gender : null,
                                        "data-is-unknown-marital-status": (data.properties) ? data.properties.is_unknown_marital_status : null,

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
            }
        });

        $(document).on('change', '#suspect', function() {
            var suspectId = $(this).find(':selected').val();
            var identityTypeId = $(this).find(':selected').data('identity-type-id');
            var identityNumber = $(this).find(':selected').data('identity-number');
            var name = $(this).find(':selected').data('name');
            var genderId = $(this).find(':selected').data('gender-id');
            var birthPlace = $(this).find(':selected').data('birth-place');
            var birthDate = $(this).find(':selected').data('birth-date');
            var fatherName = $(this).find(':selected').data('father-name');
            var motherName = $(this).find(':selected').data('mother-name');
            var nationality = $(this).find(':selected').data('nationality');
            var ethnicId = $(this).find(':selected').data('ethnic-id');
            var jobId = $(this).find(':selected').data('job-id');
            var religionId = $(this).find(':selected').data('religion-id');
            var educationId = $(this).find(':selected').data('education-id');
            var maritalStatusId = $(this).find(':selected').data('marital-status-id');
            var phoneNumber = $(this).find(':selected').data('phone-number');
            var email = $(this).find(':selected').data('email');
            var countryId = $(this).find(':selected').data('country-id');
            var provinceId = $(this).find(':selected').data('province-id');
            var regencyId = $(this).find(':selected').data('regency-id');
            var districtId = $(this).find(':selected').data('district-id');
            var villageId = $(this).find(':selected').data('village-id');
            var address = $(this).find(':selected').data('address');

            var is_available_email = $(this).find(':selected').data('is-available-email');
            var is_available_phone_number = $(this).find(':selected').data('is-available-phone-number');
            var is_exists_email = $(this).find(':selected').data('is-exists-email');
            var is_exists_phone_number = $(this).find(':selected').data('is-exists-phone-number');
            var is_unknown_address = $(this).find(':selected').data('is-unknown-address');
            var is_unknown_birth_date = $(this).find(':selected').data('is-unknown-birth-date');
            var is_unknown_birth_place = $(this).find(':selected').data('is-unknown-birth-place');
            var is_unknown_father = $(this).find(':selected').data('is-unknown-father');
            var is_unknown_mother = $(this).find(':selected').data('is-unknown-mother');
            var is_unknown_nationality = $(this).find(':selected').data('is-unknown-nationality');
            var is_unknown_gender = $(this).find(':selected').data('is-unknown-gender');
            var is_unknown_marital_status = $(this).find(':selected').data('is-unknown-marital-status');

            var vehiclePlateNumber = $(this).find(':selected').data('vehicle-plate-number');

            if(suspectId){
                $('#suspectDetail').show();
            }else{
                $('#suspectDetail').hide();
            }

            $('#identityTypeFieldSuspect').val(identityTypeId).trigger('change');
            $('#identityNumberFieldSuspect').val(identityNumber);
            $('#nameFieldSuspect').val(name);
            $('#genderFieldSuspect').val(genderId).trigger('change');
            $('#birthPlaceFieldSuspect').val(birthPlace);
            $('#birthDateFieldSuspect').val(birthDate);
            $('#fatherFieldSuspect').val(fatherName);
            $('#motherFieldSuspect').val(motherName);
            $('#nationalityFieldSuspect').val(nationality);
            $('#ethnicFieldSuspect').val(ethnicId).trigger('change');
            $('#jobFieldSuspect').val(jobId).trigger('change');
            $('#religionFieldSuspect').val(religionId).trigger('change');
            $('#educationFieldSuspect').val(educationId).trigger('change');
            $('#maritalStatusFieldSuspect').val(maritalStatusId).trigger('change');
            $('#phoneNumberFieldSuspect').val(phoneNumber);
            $('#emailFieldSuspect').val(email);
            $('#countryFieldSuspect').val(countryId).trigger('change');
            $('#addressFieldSuspect').val(address);
            $('#vehicleFieldSuspect').val(vehiclePlateNumber).trigger('change');

            if(is_exists_email == true || is_exists_email == "true"){
                $('#existsEmailFieldSuspect').prop('checked', true).trigger('change');
                $('#notExistsEmailFieldSuspect').prop('checked', false);
                $('#emailFieldSuspect').prop("disabled", false);
            }else{
                $('#existsEmailFieldSuspect').prop('checked', false);
                $('#notExistsEmailFieldSuspect').prop('checked', true).trigger('change');
                $('#emailFieldSuspect').prop("disabled", true);
            }

            if(is_exists_phone_number == true || is_exists_phone_number == "true"){
                $('#existsPhoneNumberFieldSuspect').prop('checked', true).trigger('change');
                $('#notExistsPhoneNumberFieldSuspect').prop('checked', false);
                $('#phoneNumberFieldSuspect').prop("disabled", false);
            }else{
                $('#existsPhoneNumberFieldSuspect').prop('checked', false);
                $('#notExistsPhoneNumberFieldSuspect').prop('checked', true).trigger('change');
                $('#phoneNumberFieldSuspect').prop("disabled", true);
            }

            if(is_available_email == true || is_available_email == "true"){
                $('#isAvailableEmailFieldSuspect').prop('checked', true);
            }else{
                $('#isAvailableEmailFieldSuspect').prop('checked', false).trigger('change');
            }

            if(is_available_phone_number == true || is_available_phone_number == "true"){
                $('#isAvailablePhoneNumberFieldSuspect').prop('checked', true);
            }else{
                $('#isAvailablePhoneNumberFieldSuspect').prop('checked', false).trigger('change');
            }

            if(is_unknown_address == true || is_unknown_address == "true"){
                $('#isUnknownAddressFieldSuspect').prop('checked', true).trigger('change');
            }else{
                $('#isUnknownAddressFieldSuspect').prop('checked', false);
            }

            if(is_unknown_birth_date == true || is_unknown_birth_date == "true"){
                $('#isUnknownBirthDateFieldSuspect').prop('checked', true).trigger('change');
            }else{
                $('#isUnknownBirthDateFieldSuspect').prop('checked', false);
            }

            if(is_unknown_birth_place == true || is_unknown_birth_place == "true"){
                $('#isUnknownBirthPlaceFieldSuspect').prop('checked', true).trigger('change');
            }else{
                $('#isUnknownBirthPlaceFieldSuspect').prop('checked', false);
            }

            if(is_unknown_father == true || is_unknown_father == "true"){
                $('#isUnknownFatherFieldSuspect').prop('checked', true).trigger('change');
            }else{
                $('#isUnknownFatherFieldSuspect').prop('checked', false);
            }

            if(is_unknown_mother == true || is_unknown_mother == "true"){
                $('#isUnknownMotherFieldSuspect').prop('checked', true).trigger('change');
            }else{
                $('#isUnknownMotherFieldSuspect').prop('checked', false);
            }

            if(is_unknown_nationality == true || is_unknown_nationality == "true"){
                $('#isUnknownNationalityFieldSuspect').prop('checked', true).trigger('change');
            }else{
                $('#isUnknownNationalityFieldSuspect').prop('checked', false);
            }

            if(is_unknown_gender == true || is_unknown_gender == "true"){
                $('#isUnknownGenderFieldSuspect').prop('checked', true).trigger('change');
            }else{
                $('#isUnknownGenderFieldSuspect').prop('checked', false);
            }

            if(is_unknown_marital_status == true || is_unknown_marital_status == "true"){
                $('#isUnknownMaritalStatusFieldSuspect').prop('checked', true).trigger('change');
            }else{
                $('#isUnknownMaritalStatusFieldSuspect').prop('checked', false);
            }


            /*if(countryId != 'C101'){
                $('#provinceFieldSuspect').prop("readonly", true);
                $('#regencyFieldSuspect').prop("readonly", true);
                $('#districtFieldSuspect').prop("readonly", true);
                $('#villageFieldSuspect').prop("readonly", true);
            }else{
                $('#provinceFieldSuspect').prop("readonly", false);
                $('#regencyFieldSuspect').prop("readonly", false);
                $('#districtFieldSuspect').prop("readonly", false);
                $('#villageFieldSuspect').prop("readonly", false);

                $('#countryFieldSuspect').val(countryId).trigger('change');
                $('#countryFieldSuspect').on('change', function(){
                    var parentId = $(this).find(':selected').val();
                    getProvince(parentId, provinceId);
                });

                $('#provinceFieldSuspect').val(provinceId).trigger('change');
                $('#provinceFieldSuspect').on('change', function(){
                    var parentId = $(this).find(':selected').val();
                    getRegency(parentId, regencyId);
                });

                $('#regencyFieldSuspect').val(regencyId).trigger('change');
                $('#regencyFieldSuspect').on('change', function(){
                    var parentId = $(this).find(':selected').val();
                    getDistrict(parentId, regencyId);
                });

                $('#districtFieldSuspect').val(districtId).trigger('change');
                $('#districtFieldSuspect').on('change', function(){
                    var parentId = $(this).find(':selected').val();
                    getVillage(parentId, districtId);
                });

                $('#villageFieldSuspect').val(villageId).trigger('change');
            }*/
        });

        $(document).on('change', '#countryFieldSuspect', function(){
            var parentId = $(this).find(':selected').val();
            var provinceId = $('#suspect').find(':selected').data('province-id');
            
            // Jika Indonesia (C101), tampilkan section lokasi dan load provinsi
            if (parentId == 'C101') {
                $('.countryChildrenLocationSectionSuspect').show();
                getProvince(parentId, provinceId);
            } else {
                // Jika negara selain Indonesia, sembunyikan section lokasi dan reset nilai dropdown
                $('.countryChildrenLocationSectionSuspect').hide();
                
                // Reset nilai dropdown
                $('#provinceFieldSuspect').empty().append($('<option>', {
                    value: '',
                    text: '--Pilih Provinsi--'
                }));
                $('#regencyFieldSuspect').empty().append($('<option>', {
                    value: '',
                    text: '--Pilih Kabupaten/Kota--'
                }));
                $('#districtFieldSuspect').empty().append($('<option>', {
                    value: '',
                    text: '--Pilih Kecamatan--'
                }));
                $('#villageFieldSuspect').empty().append($('<option>', {
                    value: '',
                    text: '--Pilih Kelurahan/Desa--'
                }));
            }
        });

        function getProvince(parentId, provinceId = null){
            //province get from ajax
            var parentId = parentId;
            var provinceId = provinceId;
            var classCode = 'PROVINCE';

            $.ajax({
                url: "{{route('doc.laporan-hasil-gelar-perkara-document.api.locations', ['accident_id' => $accidentId])}}", // Replace with your backend URL
                type: 'GET',
                dataType: 'json',
                data: {
                    'parent_id': parentId,
                    'class': classCode,
                },
                success: function(response) {
                    var data = response.data;
                    // Clear existing options and add new options
                    var provinceFieldSuspect = $('#provinceFieldSuspect');
                    provinceFieldSuspect.empty().append($('<option>', {
                        value: '',
                        text : '--Pilih Provinsi--'
                    }));
                    $.each(data, function(index, data) {
                        provinceFieldSuspect.append($('<option>', {
                            value: data.id,
                            text : data.name,
                            'data-parent-id': data.parent_id,
                            'data-class': data.class,
                        }));
                    });

                    $('#provinceFieldSuspect').val(provinceId).trigger('change');
                },
                error: function(xhr) {
                    // Handle error if needed
                    console.log(xhr.responseText);
                }
            });
        }

        $(document).on('change', '#provinceFieldSuspect', function(){
            var parentId = $(this).find(':selected').val();
            var regencyId = $('#suspect').find(':selected').data('regency-id');
            getRegency(parentId, regencyId);
        });

        function getRegency(parentId, regencyId = null){
            //regency get from ajax
            var parentId = parentId;
            var regencyId = regencyId;
            var classCode = 'REGENCY';

            $.ajax({
                url: "{{route('doc.laporan-hasil-gelar-perkara-document.api.locations', ['accident_id' => $accidentId])}}", // Replace with your backend URL
                type: 'GET',
                dataType: 'json',
                data: {
                    'parent_id': parentId,
                    'class': classCode,
                },
                success: function(response) {
                    var data = response.data;
                    // Clear existing options and add new options
                    var regencyFieldSuspect = $('#regencyFieldSuspect');
                    regencyFieldSuspect.empty().append($('<option>', {
                        value: '',
                        text : '--Pilih Kabupaten/Kota--'
                    }));
                    $.each(data, function(index, data) {
                        regencyFieldSuspect.append($('<option>', {
                            value: data.id,
                            text : data.name,
                            'data-parent-id': data.parent_id,
                            'data-class': data.class,
                        }));
                    });

                    $('#regencyFieldSuspect').val(regencyId).trigger('change');
                },
                error: function(xhr) {
                    // Handle error if needed
                    console.log(xhr.responseText);
                }
            });
        }

        $(document).on('change', '#regencyFieldSuspect', function(){
            var parentId = $(this).find(':selected').val();
            var districtId = $('#suspect').find(':selected').data('district-id');
            getDistrict(parentId, districtId);
        });

        function getDistrict(parentId, districtId = null){
            //district get from ajax
            var parentId = parentId;
            var districtId = districtId;
            var classCode = 'DISTRICT';

            $.ajax({
                url: "{{route('doc.laporan-hasil-gelar-perkara-document.api.locations', ['accident_id' => $accidentId])}}", // Replace with your backend URL
                type: 'GET',
                dataType: 'json',
                data: {
                    'parent_id': parentId,
                    'class': classCode,
                },
                success: function(response) {
                    var data = response.data;
                    // Clear existing options and add new options
                    var districtFieldSuspect = $('#districtFieldSuspect');
                    districtFieldSuspect.empty().append($('<option>', {
                        value: '',
                        text : '--Pilih Kecamatan--'
                    }));
                    $.each(data, function(index, data) {
                        districtFieldSuspect.append($('<option>', {
                            value: data.id,
                            text : data.name,
                            'data-parent-id': data.parent_id,
                            'data-class': data.class,
                        }));
                    });

                    $('#districtFieldSuspect').val(districtId).trigger('change');
                },
                error: function(xhr) {
                    // Handle error if needed
                    console.log(xhr.responseText);
                }
            });
        }

        $(document).on('change', '#districtFieldSuspect', function(){
            var parentId = $(this).find(':selected').val();
            var villageId = $('#suspect').find(':selected').data('village-id');
            getVillage(parentId, villageId);
        });

        function getVillage(parentId, villageId = null){
            //village get from ajax
            var parentId = parentId;
            var villageId = villageId;
            var classCode = 'VILLAGE';

            $.ajax({
                url: "{{route('doc.laporan-hasil-gelar-perkara-document.api.locations', ['accident_id' => $accidentId])}}", // Replace with your backend URL
                type: 'GET',
                dataType: 'json',
                data: {
                    'parent_id': parentId,
                    'class': classCode,
                },
                success: function(response) {
                    var data = response.data;
                    // Clear existing options and add new options
                    var villageFieldSuspect = $('#villageFieldSuspect');
                    villageFieldSuspect.empty().append($('<option>', {
                        value: '',
                        text : '--Pilih Kelurahan/Desa--'
                    }));
                    $.each(data, function(index, data) {
                        villageFieldSuspect.append($('<option>', {
                            value: data.id,
                            text : data.name,
                            'data-parent-id': data.parent_id,
                            'data-class': data.class,
                        }));
                    });

                    $('#villageFieldSuspect').val(villageId).trigger('change');
                },
                error: function(xhr) {
                    // Handle error if needed
                    console.log(xhr.responseText);
                }
            });
        }
    });

    $(document).ready(function(){
        //tidak tahu checked
        $('#isUnknownGenderFieldSuspect').on('change', function(){
            if($(this).is(':checked')){
                $('#genderFieldSuspect').val('0').trigger('change');
                $('#genderFieldSuspect').prop('disabled', true);
            }else{
                $('#genderFieldSuspect').val('').trigger('change');
                $('#genderFieldSuspect').prop('disabled', false);
            }
        });
        $('#isUnknownBirthPlaceFieldSuspect').on('change', function(){
            if($(this).is(':checked')){
                $('#birthPlaceFieldSuspect').val('TIDAK DIKETAHUI');
                $('#birthPlaceFieldSuspect').prop('disabled', true);
            }else{
                $('#birthPlaceFieldSuspect').prop('disabled', false);
            }
        });
        $('#isUnknownBirthDateFieldSuspect').on('change', function(){
            if($(this).is(':checked')){
                $('#birthDateFieldSuspect').val('');
                $('#birthDateFieldSuspect').prop('disabled', true);
            }else{
                $('#birthDateFieldSuspect').prop('disabled', false);
            }
        });
        $('#isUnknownFatherFieldSuspect').on('change', function(){
            if($(this).is(':checked')){
                $('#fatherFieldSuspect').val('TIDAK DIKETAHUI');
                $('#fatherFieldSuspect').prop('disabled', true);
            }else{
                $('#fatherFieldSuspect').prop('disabled', false);
            }
        });
        $('#isUnknownMotherFieldSuspect').on('change', function(){
            if($(this).is(':checked')){
                $('#motherFieldSuspect').val('TIDAK DIKETAHUI');
                $('#motherFieldSuspect').prop('disabled', true);
            }else{
                $('#motherFieldSuspect').prop('disabled', false);
            }
        });
        $('#isUnknownNationalityFieldSuspect').on('change', function(){
            if($(this).is(':checked')){
                $('#nationalityFieldSuspect').val('TIDAK DIKETAHUI');
                $('#nationalityFieldSuspect').prop('disabled', true);
            }else{
                $('#nationalityFieldSuspect').prop('disabled', false);
            }
        });
        $('#isUnknownMaritalStatusFieldSuspect').on('change', function(){
            if($(this).is(':checked')){
                $('#maritalStatusFieldSuspect').val('0').trigger('change');
                $('#maritalStatusFieldSuspect').prop('disabled', true);
            }else{
                $('#maritalStatusFieldSuspect').val('').trigger('change');
                $('#maritalStatusFieldSuspect').prop('disabled', false);
            }
        });
        $('#isUnknownAddressFieldSuspect').on('change', function(){
            if($(this).is(':checked')){
                $('#addressFieldSuspect').val('TIDAK DIKETAHUI');
                $('#addressFieldSuspect').prop('disabled', true);
            }else{
                $('#addressFieldSuspect').prop('disabled', false);
            }
        });

        //phone and email
        $('input[name="isExistsPhoneNumberFieldSuspect"]').on('change', function(){
            var isExistsPhoneNumberFieldSuspect = $('input[name="isExistsPhoneNumberFieldSuspect"]:checked').val();
            var lastPhoneNumber = $('#phoneNumberFieldSuspect').val();
            if(isExistsPhoneNumberFieldSuspect == 'true'){
                $('#isAvailablePhoneNumberFieldSuspect').prop('disabled', false);
                $('#isAvailablePhoneNumberFieldSuspect').prop('checked', true);
                $('#phoneNumberFieldSuspect').prop('disabled', false);

                if(lastPhoneNumber == 'TIDAK ADA NOMOR TELEPON' || lastPhoneNumber == 'TIDAK BERSEDIA MEMBERIKAN NOMOR TELEPON'){
                    $('#phoneNumberFieldSuspect').val('');
                }else{
                    $('#phoneNumberFieldSuspect').val(lastPhoneNumber);
                }
            }else{
                $('#isAvailablePhoneNumberFieldSuspect').prop('disabled', true);
                $('#isAvailablePhoneNumberFieldSuspect').prop('checked', false);
                $('#phoneNumberFieldSuspect').val('TIDAK ADA NOMOR TELEPON');
                $('#phoneNumberFieldSuspect').prop('disabled', true);
            }
        });
        $('#isAvailablePhoneNumberFieldSuspect').on('change', function(){
            var lastPhoneNumber = $('#phoneNumberFieldSuspect').val();
            var existsPhoneNumberFieldSuspect = $('#existsPhoneNumberFieldSuspect').is(':checked');

            if($(this).is(':checked')){
                $('#phoneNumberFieldSuspect').prop('disabled', false);

                if(lastPhoneNumber == 'TIDAK ADA NOMOR TELEPON' || lastPhoneNumber == 'TIDAK BERSEDIA MEMBERIKAN NOMOR TELEPON'){
                    $('#phoneNumberFieldSuspect').val('');
                }else{
                    $('#phoneNumberFieldSuspect').val(lastPhoneNumber);
                }
            }else{
                $('#phoneNumberFieldSuspect').prop('disabled', true);

                if(existsPhoneNumberFieldSuspect){
                    $('#phoneNumberFieldSuspect').val('TIDAK BERSEDIA MEMBERIKAN NOMOR TELEPON');
                }
            }
        });
        $('input[name="isExistsEmailFieldSuspect"]').on('change', function(){
            var isExistsEmailFieldSuspect = $('input[name="isExistsEmailFieldSuspect"]:checked').val();
            var lastEmail = $('#emailFieldSuspect').val();

            if(isExistsEmailFieldSuspect == 'true'){
                $('#isAvailableEmailFieldSuspect').prop('disabled', false);
                $('#isAvailableEmailFieldSuspect').prop('checked', true);
                $('#emailFieldSuspect').prop('disabled', false);

                if(lastEmail == 'TIDAK ADA EMAIL' || lastEmail == 'TIDAK BERSEDIA MEMBERIKAN EMAIL'){
                    $('#emailFieldSuspect').val('');
                }else{
                    $('#emailFieldSuspect').val(lastEmail);
                }
            }else{
                $('#isAvailableEmailFieldSuspect').prop('disabled', true);
                $('#isAvailableEmailFieldSuspect').prop('checked', false);
                $('#emailFieldSuspect').val('TIDAK ADA EMAIL');
                $('#emailFieldSuspect').prop('disabled', true);
            }
        });
        $('#isAvailableEmailFieldSuspect').on('change', function(){
            var lastEmail = $('#emailFieldSuspect').val();
            var existsEmailFieldSuspect = $('#existsEmailFieldSuspect').is(':checked');

            if($(this).is(':checked')){
                $('#emailFieldSuspect').prop('disabled', false);

                if(lastEmail == 'TIDAK ADA EMAIL' || lastEmail == 'TIDAK BERSEDIA MEMBERIKAN EMAIL'){
                    $('#emailFieldSuspect').val('');
                }else{
                    $('#emailFieldSuspect').val(lastEmail);
                }
            }else{
                $('#emailFieldSuspect').prop('disabled', true);

                if(existsEmailFieldSuspect){
                    $('#emailFieldSuspect').val('TIDAK BERSEDIA MEMBERIKAN EMAIL');
                }
            }
        });
    });

    //identity type
    $('#identityTypeFieldSuspect').on('change', function(){
        var identityTypeId = $(this).find(':selected').val();
        var identityTypeName = $(this).find(':selected').text();

        if(identityTypeId == 15 || identityTypeId == 16){
            $('#identityNumberFieldSuspect').prop('disabled', true);
            $('#identityNumberFieldSuspect').val(identityTypeName);
        }else{
            $('#identityNumberFieldSuspect').prop('disabled', false);
            $('#identityNumberFieldSuspect').val('');
        }
    });

    // Validasi Submit Form
    $(document).ready(function() {
        $('#suratKetetapanTentangPenetapanTersangkaFormSubmit').on('click', function(e) {
            e.preventDefault();

            // Lakukan validasi di sisi server menggunakan Ajax
            $.ajax({
                url: "{{ route('doc.surat-ketetapan-tentang-penetapan-tersangka-document.api.validate-request-form', ['accident_id' => $accidentId]) }}",
                type: 'POST',
                dataType: 'json',
                data: $('#suratKetetapanTentangPenetapanTersangkaForm').serialize(),
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
                            $('#suratKetetapanTentangPenetapanTersangkaForm').submit();
                        });
                    }
                },
                error: function(xhr) {
                    // Tangani error jika terjadi kesalahan saat melakukan validasi
                    response = JSON.parse(xhr.responseText);

                    if(response.code == '422'){
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
