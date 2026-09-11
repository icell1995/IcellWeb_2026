@php
    $_title = 'Surat Kesepakatan Diversi';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
    <style>
        .input-group > div.d-flex,
        .input-group .d-flex.align-self-center {
            flex-wrap: wrap !important;
        }
        .select2-container--bootstrap4 .select2-selection.border-danger,
        .select2-container--bootstrap4.is-invalid .select2-selection,
        .select2-selection.border-danger {
            border: 1px solid #dc3545 !important;
        }
        .frontend-error {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.85rem;
            color: #dc3545;
            font-weight: 600;
        }
        .form-control.is-invalid {
            border-color: #dc3545 !important;
        }
    </style>
@endpush

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"><i
            class="bi bi-arrow-left"></i> Kembali ke Progres Perkara</a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Edit Surat Kesepakatan Diversi</h5>

            <div class="alert alert-danger" id="attentionBox">
                <div class="text-center">
                    <b>
                        PERHATIAN !<br />
                        <br />
                        PASTIKAN PROSES DIVERSI TELAH DILAKSANAKAN DAN MENCAPAI KESEPAKATAN ANTARA PIHAK ANAK DAN PIHAK KORBAN
                        SEBELUM MEMBUAT DOKUMEN INI.
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
            <form action="{{ route('doc.surat-kesepakatan-diversi-document.update', ['accident_id' => $accidentId, 'id' => $document->id]) }}"
                method="POST" enctype="multipart/form-data" id="suratKesepakatanDiversiForm" novalidate>
                @csrf
                <input type="hidden" name="accidentId" id="accidentId" value="{{ $accidentId }}">

                {{-- Nomor LP --}}
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


                {{-- ─── IDENTITAS PIHAK I (ANAK & PENDAMPING) ─── --}}
                <hr>
                <h5 class="fw-bold text-blue-dark">Identitas Pihak I (Anak yang Berkonflik dengan Hukum)</h5>

                {{-- Pilih Tersangka Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="suspectId">Tersangka Anak<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="suspectId" id="suspectId">
                            <option value="">--Pilih Tersangka Anak--</option>
                            @foreach ($suspects as $suspect)
                                @php
                                    $suspectAgeYear = '';
                                    $suspectAgeMonth = '';
                                    $suspectAgeDay = '';
                                    $suspectAgeText = 'Umur tidak diketahui';
                                    $birthDateRaw = $suspect->birth_date ?? $suspect->date_of_birth;
                                    if (!empty($birthDateRaw)) {
                                        try {
                                            $birth = \Carbon\Carbon::parse($birthDateRaw);
                                            $diff = $birth->diff(\Carbon\Carbon::now());
                                            $suspectAgeYear = $diff->y;
                                            $suspectAgeMonth = $diff->m;
                                            $suspectAgeDay = $diff->d;
                                            $suspectAgeText = "{$diff->y} tahun {$diff->m} bulan {$diff->d} hari";
                                        } catch (\Exception $e) {
                                            $suspectAgeText = 'Umur tidak diketahui';
                                        }
                                    } elseif (!empty($suspect->age)) {
                                        $suspectAgeYear = $suspect->age;
                                        $suspectAgeText = $suspect->age . ' tahun';
                                    }

                                    $suspectNatId = '';
                                    $natRaw = trim($suspect->nationality ?? '');
                                    $natUpper = strtoupper($natRaw);
                                    if (!empty($natRaw)) {
                                        if (
                                            str_contains($natUpper, 'INDO') ||
                                            str_contains($natUpper, 'WNI') ||
                                            str_contains($natUpper, 'WARGA NEGARA INDONESIA') ||
                                            $natUpper === '1'
                                        ) {
                                            $suspectNatId = '1';
                                        } elseif (
                                            str_contains($natUpper, 'WNA') ||
                                            str_contains($natUpper, 'ASING') ||
                                            str_contains($natUpper, 'RUSIA') ||
                                            str_contains($natUpper, 'CHINA') ||
                                            str_contains($natUpper, 'CINA') ||
                                            str_contains($natUpper, 'INGGERIS') ||
                                            str_contains($natUpper, 'INGGRIS') ||
                                            str_contains($natUpper, 'PRANCIS') ||
                                            str_contains($natUpper, 'ARAB') ||
                                            str_contains($natUpper, 'CEKO') ||
                                            $natUpper === '2'
                                        ) {
                                            $suspectNatId = '2';
                                        } elseif (
                                            str_contains($natUpper, 'TIDAK') ||
                                            str_contains($natUpper, 'UNKNOWN') ||
                                            $natUpper === '3'
                                        ) {
                                            $suspectNatId = '3';
                                        }
                                    } elseif (!empty($suspect->country_id)) {
                                        $suspectNatId = ($suspect->country_id === 'C101') ? '1' : '2';
                                    } elseif (!empty($suspect->country_short_name) && strtoupper($suspect->country_short_name) === 'IDN') {
                                        $suspectNatId = '1';
                                    }
                                @endphp
                                <option value="{{ $suspect->id }}"
                                    data-identity-type="{{ $suspect->identity_type_id }}"
                                    data-identity="{{ $suspect->identity_number }}"
                                    data-name="{{ $suspect->name }}"
                                    data-gender="{{ $suspect->gender_id }}"
                                    data-birthplace="{{ $suspect->birth_place ?? $suspect->place_of_birth }}"
                                    data-birthdate="{{ $birthDateRaw ? \Carbon\Carbon::parse($birthDateRaw)->format('Y-m-d') : '' }}"
                                    data-age-year="{{ $suspectAgeYear }}"
                                    data-age-month="{{ $suspectAgeMonth }}"
                                    data-age-day="{{ $suspectAgeDay }}"
                                    data-nationality="{{ $suspectNatId }}"
                                    data-job="{{ $suspect->job_id }}"
                                    data-religion="{{ $suspect->religion_id }}"
                                    data-address="{{ $suspect->address ?? ($suspect->properties['address'] ?? '') }}"
                                    {{ old('suspectId', $payload['suspectId'] ?? '') == $suspect->id ? 'selected' : '' }}>
                                    {{ $suspect->name }} ({{ $suspectAgeText }})
                                </option>
                            @endforeach
                        </select>
                        @error('suspectId')
                            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                {{-- Nama Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childName">Nama<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="childName" type="text" class="form-control" name="childName" value="{{ old('childName', $payload['childName'] ?? '') }}" required placeholder="Nama Lengkap">
                    </div>
                </div>

                {{-- Jenis Identitas Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childIdentityType">Jenis Identitas<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" id="childIdentityType" name="childIdentityType">
                            <option value="">--Pilih Jenis Identitas--</option>
                            @foreach ($identityTypes as $identityType)
                                <option value="{{ $identityType->id }}" data-identity-type-name="{{ $identityType->name }}" {{ old('childIdentityType', $payload['childIdentityType'] ?? '') == $identityType->id ? 'selected' : '' }}>
                                    {{ $identityType->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Nomor Identitas Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childIdentityNumber">Nomor Identitas<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        @php
                            $currentChildIdType = old('childIdentityType', $payload['childIdentityType'] ?? '');
                        @endphp
                        <input id="childIdentityNumber" type="text" class="form-control" name="childIdentityNumber" value="{{ old('childIdentityNumber', $payload['childIdentityNumber'] ?? '') }}"
                            placeholder="{{ empty($currentChildIdType) ? 'Pilih Jenis Identitas terlebih dahulu' : 'Nomor Identitas' }}"
                            @if(empty($currentChildIdType)) disabled @endif>
                    </div>
                </div>

                {{-- Kewarganegaraan Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childNationality">Kewarganegaraan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="childNationality" id="childNationality">
                            <option value="">--Pilih Kewarganegaraan--</option>
                            @foreach ($nationalities as $nationality)
                                <option value="{{ $nationality->id }}" {{ old('childNationality', $payload['childNationality'] ?? '') == $nationality->id ? 'selected' : '' }}>
                                    {{ $nationality->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Jenis Kelamin Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGender">Jenis Kelamin<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="childGender" id="childGender">
                            <option value="">--Pilih Jenis Kelamin--</option>
                            @foreach ($genders as $gender)
                                <option value="{{ $gender->id }}" {{ old('childGender', $payload['childGender'] ?? '') == $gender->id ? 'selected' : '' }}>{{ $gender->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Tempat Lahir Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childBirthPlace">Tempat Lahir<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="childBirthPlace" type="text" class="form-control" name="childBirthPlace" value="{{ old('childBirthPlace', $payload['childBirthPlace'] ?? '') }}" placeholder="Tempat Lahir">
                    </div>
                </div>

                {{-- Tanggal Lahir Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childBirthDate">Tanggal Lahir<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="childBirthDate" type="text" class="form-control" name="childBirthDate" value="{{ old('childBirthDate', $payload['childBirthDate'] ?? '') }}" placeholder="YYYY-MM-DD" data-provide="datepicker" autocomplete="off">
                    </div>
                </div>

                {{-- Umur Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Umur<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <div class="row">
                            <div class="col-4">
                                <div class="input-group">
                                    <input type="number" min="0" max="100" class="form-control" id="childAgeYear" name="childAgeYear" value="{{ old('childAgeYear', $payload['childAgeYear'] ?? '') }}" placeholder="0" readonly style="background-color: #e9ecef;">
                                    <div class="input-group-append"><span class="input-group-text">Tahun</span></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="input-group">
                                    <input type="number" min="0" max="11" class="form-control" id="childAgeMonth" name="childAgeMonth" value="{{ old('childAgeMonth', $payload['childAgeMonth'] ?? '') }}" placeholder="0" readonly style="background-color: #e9ecef;">
                                    <div class="input-group-append"><span class="input-group-text">Bulan</span></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="input-group">
                                    <input type="number" min="0" max="31" class="form-control" id="childAgeDay" name="childAgeDay" value="{{ old('childAgeDay', $payload['childAgeDay'] ?? '') }}" placeholder="0" readonly style="background-color: #e9ecef;">
                                    <div class="input-group-append"><span class="input-group-text">Hari</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pekerjaan Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childJob">Pekerjaan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="childJob" id="childJob">
                            <option value="">--Pilih Pekerjaan--</option>
                            @foreach ($jobs as $job)
                                <option value="{{ $job->id }}" {{ old('childJob', $payload['childJob'] ?? '') == $job->id ? 'selected' : '' }}>{{ $job->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Agama Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childReligion">Agama<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="childReligion" id="childReligion">
                            <option value="">--Pilih Agama--</option>
                            @foreach ($religions as $religion)
                                <option value="{{ $religion->id }}" {{ old('childReligion', $payload['childReligion'] ?? '') == $religion->id ? 'selected' : '' }}>{{ $religion->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Alamat Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childAddress">Alamat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <textarea id="childAddress" class="form-control" name="childAddress" rows="2" placeholder="Alamat">{{ old('childAddress', $payload['childAddress'] ?? '') }}</textarea>
                    </div>
                </div>

                {{-- ─── IDENTITAS PENDAMPING ANAK ─── --}}
                <div class="mt-4 mb-3">
                    <h6 class="fw-bold text-blue-dark">Identitas Pendamping Anak</h6>
                </div>

                {{-- Pendamping Anak Dari --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianFrom">Pendamping Anak Dari<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="childGuardianFrom" id="childGuardianFrom">
                            <option value="">--Pilih Pendamping Dari--</option>
                            @foreach(['Orang Tua', 'Wali', 'Balai Pemasyarakatan (BAPAS)', 'Pekerja Sosial (PEKSOS)', 'Penasihat Hukum / Advokat', 'Lainnya'] as $opt)
                                <option value="{{ $opt }}" {{ old('childGuardianFrom', $payload['childGuardianFrom'] ?? '') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Nama Pendamping Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianName">Nama<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="childGuardianName" type="text" class="form-control" name="childGuardianName" value="{{ old('childGuardianName', $payload['childGuardianName'] ?? '') }}" placeholder="Nama Lengkap">
                    </div>
                </div>

                {{-- Jenis Identitas Pendamping Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianIdentityType">Jenis Identitas<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" id="childGuardianIdentityType" name="childGuardianIdentityType">
                            <option value="">--Pilih Jenis Identitas--</option>
                            @foreach ($identityTypes as $identityType)
                                <option value="{{ $identityType->id }}" data-identity-type-name="{{ $identityType->name }}" {{ old('childGuardianIdentityType', $payload['childGuardianIdentityType'] ?? '') == $identityType->id ? 'selected' : '' }}>
                                    {{ $identityType->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Nomor Identitas Pendamping Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianIdentity">Nomor Identitas<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        @php
                            $currentCgIdType = old('childGuardianIdentityType', $payload['childGuardianIdentityType'] ?? '');
                        @endphp
                        <input id="childGuardianIdentity" type="text" class="form-control" name="childGuardianIdentity" value="{{ old('childGuardianIdentity', $payload['childGuardianIdentity'] ?? '') }}"
                            placeholder="{{ empty($currentCgIdType) ? 'Pilih Jenis Identitas terlebih dahulu' : 'Nomor Identitas' }}"
                            @if(empty($currentCgIdType)) disabled @endif>
                    </div>
                </div>

                {{-- Kewarganegaraan Pendamping Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianNationality">Kewarganegaraan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="childGuardianNationality" id="childGuardianNationality">
                            <option value="">--Pilih Kewarganegaraan--</option>
                            @foreach ($nationalities as $nationality)
                                <option value="{{ $nationality->id }}" {{ old('childGuardianNationality', $payload['childGuardianNationality'] ?? '') == $nationality->id ? 'selected' : '' }}>
                                    {{ $nationality->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Jenis Kelamin Pendamping Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianGender">Jenis Kelamin<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="childGuardianGender" id="childGuardianGender">
                            <option value="">--Pilih Jenis Kelamin--</option>
                            @foreach ($genders as $gender)
                                <option value="{{ $gender->id }}" {{ old('childGuardianGender', $payload['childGuardianGender'] ?? '') == $gender->id ? 'selected' : '' }}>{{ $gender->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Tempat Lahir Pendamping Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianBirthPlace">Tempat Lahir<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="childGuardianBirthPlace" type="text" class="form-control" name="childGuardianBirthPlace" value="{{ old('childGuardianBirthPlace', $payload['childGuardianBirthPlace'] ?? '') }}" placeholder="Tempat Lahir">
                    </div>
                </div>

                {{-- Tanggal Lahir Pendamping Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianBirthDate">Tanggal Lahir<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="childGuardianBirthDate" type="text" class="form-control" name="childGuardianBirthDate" value="{{ old('childGuardianBirthDate', $payload['childGuardianBirthDate'] ?? '') }}" placeholder="YYYY-MM-DD" data-provide="datepicker" autocomplete="off">
                    </div>
                </div>

                {{-- Pekerjaan Pendamping Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianJob">Pekerjaan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="childGuardianJob" id="childGuardianJob">
                            <option value="">--Pilih Pekerjaan--</option>
                            @foreach ($jobs as $job)
                                <option value="{{ $job->id }}" {{ old('childGuardianJob', $payload['childGuardianJob'] ?? '') == $job->id ? 'selected' : '' }}>{{ $job->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Agama Pendamping Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianReligion">Agama<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="childGuardianReligion" id="childGuardianReligion">
                            <option value="">--Pilih Agama--</option>
                            @foreach ($religions as $religion)
                                <option value="{{ $religion->id }}" {{ old('childGuardianReligion', $payload['childGuardianReligion'] ?? '') == $religion->id ? 'selected' : '' }}>{{ $religion->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Alamat Pendamping Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianAddress">Alamat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <textarea id="childGuardianAddress" class="form-control" name="childGuardianAddress" rows="2" placeholder="Alamat">{{ old('childGuardianAddress', $payload['childGuardianAddress'] ?? '') }}</textarea>
                    </div>
                </div>

                {{-- Hubungan Keluarga Pendamping Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianRelation">Hubungan Keluarga<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="childGuardianRelation" id="childGuardianRelation">
                            <option value="">--Pilih Hubungan Keluarga--</option>
                            @foreach(['Ayah Kandung', 'Ibu Kandung', 'Kakek / Nenek', 'Paman / Bibi', 'Kakak Kandung', 'Wali', 'Penasihat Hukum', 'Lainnya'] as $opt)
                                <option value="{{ $opt }}" {{ old('childGuardianRelation', $payload['childGuardianRelation'] ?? '') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- ─── IDENTITAS PIHAK II (KORBAN & PENDAMPING) ─── --}}
                <hr>
                <h5 class="fw-bold text-blue-dark">Identitas Pihak II (Korban)</h5>

                {{-- Pilih Korban dari Pihak Perkara --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="victimSelect">Pilih Korban</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="victimSelect" id="victimSelect">
                            <option value="">--Pilih Korban dari Pihak Pelapor Perkara--</option>
                            <option value="manual">--Input Manual Korban--</option>
                            @if(isset($reportingPersons))
                                @foreach ($reportingPersons as $victim)
                                    @php
                                        $victimAgeYear = '';
                                        $victimAgeMonth = '';
                                        $victimAgeDay = '';
                                        $victimAgeText = 'Umur tidak diketahui';
                                        $birthDateRaw = $victim->birth_date;
                                        if (!empty($birthDateRaw)) {
                                            try {
                                                $birth = \Carbon\Carbon::parse($birthDateRaw);
                                                $diff = $birth->diff(\Carbon\Carbon::now());
                                                $victimAgeYear = $diff->y;
                                                $victimAgeMonth = $diff->m;
                                                $victimAgeDay = $diff->d;
                                                $victimAgeText = "{$diff->y} tahun {$diff->m} bulan {$diff->d} hari";
                                            } catch (\Exception $e) {
                                                $victimAgeText = 'Umur tidak diketahui';
                                            }
                                        } elseif (!empty($victim->age)) {
                                            $victimAgeYear = $victim->age;
                                            $victimAgeText = $victim->age . ' tahun';
                                        }

                                        $victimNatId = '';
                                        if (!empty($victim->nationality_id)) {
                                            $victimNatId = $victim->nationality_id;
                                        } elseif (!empty($victim->country_id)) {
                                            $victimNatId = ($victim->country_id === 'C101') ? '1' : '2';
                                        }
                                    @endphp
                                    <option value="{{ $victim->id }}"
                                        data-identity-type="{{ $victim->identity_type_id }}"
                                        data-identity="{{ $victim->identity_number }}"
                                        data-name="{{ $victim->name }}"
                                        data-gender="{{ $victim->gender_id }}"
                                        data-birthplace="{{ $victim->birth_place }}"
                                        data-birthdate="{{ $birthDateRaw ? \Carbon\Carbon::parse($birthDateRaw)->format('Y-m-d') : '' }}"
                                        data-age-year="{{ $victimAgeYear }}"
                                        data-age-month="{{ $victimAgeMonth }}"
                                        data-age-day="{{ $victimAgeDay }}"
                                        data-nationality="{{ $victimNatId }}"
                                        data-job="{{ $victim->job_id }}"
                                        data-religion="{{ $victim->religion_id }}"
                                        data-address="{{ $victim->address }}"
                                        {{ old('victimSelect', $payload['victimSelect'] ?? '') == $victim->id ? 'selected' : '' }}>
                                        {{ $victim->name }} ({{ $victimAgeText }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>



                {{-- Nama Korban --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="victimName">Nama<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="victimName" type="text" class="form-control" name="victimName" value="{{ old('victimName', $payload['victimName'] ?? '') }}" required placeholder="Nama Lengkap">
                    </div>
                </div>

                {{-- Jenis Identitas Korban --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="victimIdentityType">Jenis Identitas<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" id="victimIdentityType" name="victimIdentityType">
                            <option value="">--Pilih Jenis Identitas--</option>
                            @foreach ($identityTypes as $identityType)
                                <option value="{{ $identityType->id }}" data-identity-type-name="{{ $identityType->name }}" {{ old('victimIdentityType', $payload['victimIdentityType'] ?? '') == $identityType->id ? 'selected' : '' }}>
                                    {{ $identityType->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Nomor Identitas Korban --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="victimIdentityNumber">Nomor Identitas<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        @php
                            $currentVictimIdType = old('victimIdentityType', $payload['victimIdentityType'] ?? '');
                        @endphp
                        <input id="victimIdentityNumber" type="text" class="form-control" name="victimIdentityNumber" value="{{ old('victimIdentityNumber', $payload['victimIdentityNumber'] ?? '') }}"
                            placeholder="{{ empty($currentVictimIdType) ? 'Pilih Jenis Identitas terlebih dahulu' : 'Nomor Identitas' }}"
                            @if(empty($currentVictimIdType)) disabled @endif>
                    </div>
                </div>

                {{-- Kewarganegaraan Korban --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="victimNationality">Kewarganegaraan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="victimNationality" id="victimNationality">
                            <option value="">--Pilih Kewarganegaraan--</option>
                            @foreach ($nationalities as $nationality)
                                <option value="{{ $nationality->id }}" {{ old('victimNationality', $payload['victimNationality'] ?? '') == $nationality->id ? 'selected' : '' }}>
                                    {{ $nationality->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Jenis Kelamin Korban --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="victimGender">Jenis Kelamin<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="victimGender" id="victimGender">
                            <option value="">--Pilih Jenis Kelamin--</option>
                            @foreach ($genders as $gender)
                                <option value="{{ $gender->id }}" {{ old('victimGender', $payload['victimGender'] ?? '') == $gender->id ? 'selected' : '' }}>{{ $gender->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Tempat Lahir Korban --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="victimBirthPlace">Tempat Lahir<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="victimBirthPlace" type="text" class="form-control" name="victimBirthPlace" value="{{ old('victimBirthPlace', $payload['victimBirthPlace'] ?? '') }}" placeholder="Tempat Lahir">
                    </div>
                </div>

                {{-- Tanggal Lahir Korban --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="victimBirthDate">Tanggal Lahir<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="victimBirthDate" type="text" class="form-control" name="victimBirthDate" value="{{ old('victimBirthDate', $payload['victimBirthDate'] ?? '') }}" placeholder="YYYY-MM-DD" data-provide="datepicker" autocomplete="off">
                    </div>
                </div>

                {{-- Umur Korban --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Umur<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <div class="row">
                            <div class="col-4">
                                <div class="input-group">
                                    <input type="number" min="0" max="100" class="form-control" id="victimAgeYear" name="victimAgeYear" value="{{ old('victimAgeYear', $payload['victimAgeYear'] ?? '') }}" placeholder="0" readonly style="background-color: #e9ecef;">
                                    <div class="input-group-append"><span class="input-group-text">Tahun</span></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="input-group">
                                    <input type="number" min="0" max="11" class="form-control" id="victimAgeMonth" name="victimAgeMonth" value="{{ old('victimAgeMonth', $payload['victimAgeMonth'] ?? '') }}" placeholder="0" readonly style="background-color: #e9ecef;">
                                    <div class="input-group-append"><span class="input-group-text">Bulan</span></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="input-group">
                                    <input type="number" min="0" max="31" class="form-control" id="victimAgeDay" name="victimAgeDay" value="{{ old('victimAgeDay', $payload['victimAgeDay'] ?? '') }}" placeholder="0" readonly style="background-color: #e9ecef;">
                                    <div class="input-group-append"><span class="input-group-text">Hari</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pekerjaan Korban --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="victimJob">Pekerjaan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="victimJob" id="victimJob">
                            <option value="">--Pilih Pekerjaan--</option>
                            @foreach ($jobs as $job)
                                <option value="{{ $job->id }}" {{ old('victimJob', $payload['victimJob'] ?? '') == $job->id ? 'selected' : '' }}>{{ $job->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Agama Korban --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="victimReligion">Agama<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="victimReligion" id="victimReligion">
                            <option value="">--Pilih Agama--</option>
                            @foreach ($religions as $religion)
                                <option value="{{ $religion->id }}" {{ old('victimReligion', $payload['victimReligion'] ?? '') == $religion->id ? 'selected' : '' }}>{{ $religion->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Alamat Korban --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="victimAddress">Alamat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <textarea id="victimAddress" class="form-control" name="victimAddress" rows="2" placeholder="Alamat">{{ old('victimAddress', $payload['victimAddress'] ?? '') }}</textarea>
                    </div>
                </div>

                {{-- Status Pendampingan Korban (Radio Button) --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Pendamping Korban<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <div class="d-flex mb-2">
                            <div class="form-check me-4">
                                <input class="form-check-input" type="radio" id="victimNotAccompanied" name="isVictimAccompanied" value="0" {{ old('isVictimAccompanied', $payload['isVictimAccompanied'] ?? '0') == '0' ? 'checked' : '' }}>
                                <label class="form-check-label" for="victimNotAccompanied">
                                    Tidak Didampingi (Korban Dewasa)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" id="victimAccompanied" name="isVictimAccompanied" value="1" {{ old('isVictimAccompanied', $payload['isVictimAccompanied'] ?? '') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="victimAccompanied">
                                    Didampingi (Orang Tua / Wali / Pendamping)
                                </label>
                            </div>
                        </div>
                        <small class="text-muted">(*Korban dewasa tidak wajib didampingi)</small>
                    </div>
                </div>

                <div id="victimGuardianContainer" style="display: {{ old('isVictimAccompanied', $payload['isVictimAccompanied'] ?? '') == '1' ? 'block' : 'none' }};">
                    {{-- Pendamping Korban Dari --}}
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianFrom">Pendamping Dari</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            <select class="form-control select2" name="victimGuardianFrom" id="victimGuardianFrom">
                                <option value="">--Pilih Pendamping Dari--</option>
                                @foreach(['Orang Tua', 'Wali', 'Pekerja Sosial (PEKSOS)', 'Penasihat Hukum / Advokat', 'Lainnya'] as $opt)
                                    <option value="{{ $opt }}" {{ old('victimGuardianFrom', $payload['victimGuardianFrom'] ?? '') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Nama Pendamping Korban --}}
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianName">Nama</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            <input id="victimGuardianName" type="text" class="form-control" name="victimGuardianName" value="{{ old('victimGuardianName', $payload['victimGuardianName'] ?? '') }}" placeholder="Nama Lengkap">
                        </div>
                    </div>

                    {{-- Jenis Identitas Pendamping Korban --}}
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianIdentityType">Jenis Identitas</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            <select class="form-control select2" id="victimGuardianIdentityType" name="victimGuardianIdentityType">
                                <option value="">--Pilih Jenis Identitas--</option>
                                @foreach ($identityTypes as $identityType)
                                    <option value="{{ $identityType->id }}" data-identity-type-name="{{ $identityType->name }}" {{ old('victimGuardianIdentityType', $payload['victimGuardianIdentityType'] ?? '') == $identityType->id ? 'selected' : '' }}>
                                        {{ $identityType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Nomor Identitas Pendamping Korban --}}
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianIdentity">Nomor Identitas</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            @php
                                $currentVgIdType = old('victimGuardianIdentityType', $payload['victimGuardianIdentityType'] ?? '');
                            @endphp
                            <input id="victimGuardianIdentity" type="text" class="form-control" name="victimGuardianIdentity" value="{{ old('victimGuardianIdentity', $payload['victimGuardianIdentity'] ?? '') }}"
                                placeholder="{{ empty($currentVgIdType) ? 'Pilih Jenis Identitas terlebih dahulu' : 'Nomor Identitas' }}"
                                @if(empty($currentVgIdType)) disabled @endif>
                        </div>
                    </div>

                    {{-- Kewarganegaraan Pendamping Korban --}}
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianNationality">Kewarganegaraan</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            <select class="form-control select2" name="victimGuardianNationality" id="victimGuardianNationality">
                                <option value="">--Pilih Kewarganegaraan--</option>
                                @foreach ($nationalities as $nationality)
                                    <option value="{{ $nationality->id }}" {{ old('victimGuardianNationality', $payload['victimGuardianNationality'] ?? '') == $nationality->id ? 'selected' : '' }}>
                                        {{ $nationality->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Jenis Kelamin Pendamping Korban --}}
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianGender">Jenis Kelamin</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            <select class="form-control select2" name="victimGuardianGender" id="victimGuardianGender">
                                <option value="">--Pilih Jenis Kelamin--</option>
                                @foreach ($genders as $gender)
                                    <option value="{{ $gender->id }}" {{ old('victimGuardianGender', $payload['victimGuardianGender'] ?? '') == $gender->id ? 'selected' : '' }}>{{ $gender->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Tempat Lahir Pendamping Korban --}}
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianBirthPlace">Tempat Lahir</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            <input id="victimGuardianBirthPlace" type="text" class="form-control" name="victimGuardianBirthPlace" value="{{ old('victimGuardianBirthPlace', $payload['victimGuardianBirthPlace'] ?? '') }}" placeholder="Tempat Lahir">
                        </div>
                    </div>

                    {{-- Tanggal Lahir Pendamping Korban --}}
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianBirthDate">Tanggal Lahir</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            <input id="victimGuardianBirthDate" type="text" class="form-control" name="victimGuardianBirthDate" value="{{ old('victimGuardianBirthDate', $payload['victimGuardianBirthDate'] ?? '') }}" placeholder="YYYY-MM-DD" data-provide="datepicker" autocomplete="off">
                        </div>
                    </div>

                    {{-- Pekerjaan Pendamping Korban --}}
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianJob">Pekerjaan</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            <select class="form-control select2" name="victimGuardianJob" id="victimGuardianJob">
                                <option value="">--Pilih Pekerjaan--</option>
                                @foreach ($jobs as $job)
                                    <option value="{{ $job->id }}" {{ old('victimGuardianJob', $payload['victimGuardianJob'] ?? '') == $job->id ? 'selected' : '' }}>{{ $job->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Agama Pendamping Korban --}}
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianReligion">Agama</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            <select class="form-control select2" name="victimGuardianReligion" id="victimGuardianReligion">
                                <option value="">--Pilih Agama--</option>
                                @foreach ($religions as $religion)
                                    <option value="{{ $religion->id }}" {{ old('victimGuardianReligion', $payload['victimGuardianReligion'] ?? '') == $religion->id ? 'selected' : '' }}>{{ $religion->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Alamat Pendamping Korban --}}
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianAddress">Alamat</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            <textarea id="victimGuardianAddress" class="form-control" name="victimGuardianAddress" rows="2" placeholder="Alamat">{{ old('victimGuardianAddress', $payload['victimGuardianAddress'] ?? '') }}</textarea>
                        </div>
                    </div>

                    {{-- Hubungan Keluarga Pendamping Korban --}}
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianRelation">Hubungan Keluarga</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            <select class="form-control select2" name="victimGuardianRelation" id="victimGuardianRelation">
                                <option value="">--Pilih Hubungan Keluarga--</option>
                                @foreach(['Ayah Kandung', 'Ibu Kandung', 'Kakek / Nenek', 'Paman / Bibi', 'Kakak Kandung', 'Suami / Istri', 'Anak Kandung', 'Wali', 'Penasihat Hukum', 'Lainnya'] as $opt)
                                    <option value="{{ $opt }}" {{ old('victimGuardianRelation', $payload['victimGuardianRelation'] ?? '') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- ─── WAKTU DAN TEMPAT PELAKSANAAN MUSYAWARAH DIVERSI ─── --}}
                <hr>
                <h5 class="fw-bold text-blue-dark">Waktu dan Tempat Pelaksanaan Musyawarah Diversi</h5>

                {{-- Tanggal Musyawarah --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="diversionDate">Tanggal Musyawarah<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input class="form-control" id="diversionDate" name="diversionDate"
                            placeholder="YYYY-MM-DD" autocomplete="off" value="{{ old('diversionDate', $document->diversion_date ?? $payload['diversionDate'] ?? '') }}"
                            data-provide="datepicker">

                        @error('diversionDate')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Tempat Pelaksanaan Musyawarah (Ruang & Alamat) --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="diversionRoom">Tempat Musyawarah<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" id="diversionRoom" name="diversionRoom"
                            placeholder="Ruang Musyawarah (Contoh: Ruang Mediasi)" value="{{ old('diversionRoom', $document->diversion_room ?? $payload['diversionRoom'] ?? '') }}">

                        @error('diversionRoom')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" id="diversionStreet" name="diversionStreet"
                            placeholder="Jalan / Alamat Tempat Pelaksanaan Musyawarah" value="{{ old('diversionStreet', $document->diversion_street ?? $payload['diversionStreet'] ?? '') }}">

                        @error('diversionStreet')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Fasilitator Diversi --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="facilitatorOfficer">Fasilitator Diversi<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2" name="facilitatorOfficer" id="facilitatorOfficer">
                            <option value="">--Pilih Fasilitator Diversi--</option>
                            @foreach ($authorizedSignatories->merge($officers)->sortBy('first_name') as $data)
                                @php
                                    $positionName = ($data->position) ? $data->position->name : '-';
                                @endphp
                                <option value="{{ $data->id }}" data-register-number="{{ $data->register_number }}" {{ old('facilitatorOfficer', $payload['facilitatorOfficer'] ?? '') == $data->id ? 'selected' : '' }}>
                                    {{ $data->register_number . ' - ' . $data->full_name . ' | ' . $positionName }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">(*Apabila daftar fasilitator kosong silahkan hubungi Helpdesk untuk mendapat bantuan)</small>

                        @error('facilitatorOfficer')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- ─── PASAL-PASAL KESEPAKATAN DIVERSI ─── --}}
                <hr>
                <h5 class="fw-bold text-blue-dark">Isi Kesepakatan Diversi (Pasal-Pasal S-44.3)</h5>

                {{-- Pasal 1: Bentuk Kesepakatan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Pasal 1 (Bentuk Diversi)<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <small class="text-muted d-block mb-2">Pilih satu atau lebih bentuk kesepakatan diversi yang disepakati para pihak:</small>

                        {{-- Opsi 1: Ganti Kerugian --}}
                        <div class="form-check mb-2">
                            <input class="form-check-input deal-toggle" type="checkbox" id="dealCompensationCheck" name="dealCompensationCheck" value="1" {{ old('dealCompensationCheck', $payload['dealCompensationCheck'] ?? '') ? 'checked' : '' }} data-target="#boxCompensation">
                            <label class="form-check-label fw-bold" for="dealCompensationCheck">
                                [1] Pihak keluarga Anak memberikan ganti kerugian berupa uang
                            </label>
                        </div>
                        <div id="boxCompensation" class="mb-3 ps-4" style="display: {{ old('dealCompensationCheck', $payload['dealCompensationCheck'] ?? '') ? 'block' : 'none' }};">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <input type="text" class="form-control form-control-sm" name="compensationAmount" value="{{ old('compensationAmount', $payload['compensationAmount'] ?? '') }}" placeholder="Besaran uang (Contoh: 5.000.000)">
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control form-control-sm" name="compensationPeriod" value="{{ old('compensationPeriod', $payload['compensationPeriod'] ?? '') }}" placeholder="Jangka waktu pembayaran (Contoh: 1 bulan)">
                                </div>
                            </div>
                            <textarea class="form-control form-control-sm" name="compensationConsideration" rows="2" placeholder="Pertimbangan">{{ old('compensationConsideration', $payload['compensationConsideration'] ?? '') }}</textarea>
                        </div>

                        {{-- Opsi 2: Rehabilitasi Sosial --}}
                        <div class="form-check mb-2">
                            <input class="form-check-input deal-toggle" type="checkbox" id="dealRehabCheck" name="dealRehabCheck" value="1" {{ old('dealRehabCheck', $payload['dealRehabCheck'] ?? '') ? 'checked' : '' }} data-target="#boxRehab">
                            <label class="form-check-label fw-bold" for="dealRehabCheck">
                                [2] Terhadap Anak diberikan Rehabilitasi Sosial dan Psikososial
                            </label>
                        </div>
                        <div id="boxRehab" class="mb-3 ps-4" style="display: {{ old('dealRehabCheck', $payload['dealRehabCheck'] ?? '') ? 'block' : 'none' }};">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <input type="text" class="form-control form-control-sm" name="rehabOrganizer" value="{{ old('rehabOrganizer', $payload['rehabOrganizer'] ?? '') }}" placeholder="Pelaksana rehabilitasi (Contoh: Dinas Sosial / Balai Rehabilitasi)">
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control form-control-sm" name="rehabPeriod" value="{{ old('rehabPeriod', $payload['rehabPeriod'] ?? '') }}" placeholder="Lama pelaksanaan (Contoh: 3 bulan)">
                                </div>
                            </div>
                            <textarea class="form-control form-control-sm" name="rehabConsideration" rows="2" placeholder="Pertimbangan">{{ old('rehabConsideration', $payload['rehabConsideration'] ?? '') }}</textarea>
                        </div>

                        {{-- Opsi 3: Pengembalian ke Orang Tua --}}
                        <div class="form-check mb-2">
                            <input class="form-check-input deal-toggle" type="checkbox" id="dealBapasCheck" name="dealBapasCheck" value="1" {{ old('dealBapasCheck', $payload['dealBapasCheck'] ?? '') ? 'checked' : '' }} data-target="#boxBapas">
                            <label class="form-check-label fw-bold" for="dealBapasCheck">
                                [3] Anak dikembalikan ke orang tua dengan pengawasan BAPAS
                            </label>
                        </div>
                        <div id="boxBapas" class="mb-3 ps-4" style="display: {{ old('dealBapasCheck', $payload['dealBapasCheck'] ?? '') ? 'block' : 'none' }};">
                            <input type="text" class="form-control form-control-sm mb-2" name="bapasSupervisionName" value="{{ old('bapasSupervisionName', $payload['bapasSupervisionName'] ?? '') }}" placeholder="Nama BAPAS pengawas (Contoh: BAPAS Jakarta Selatan)">
                            <textarea class="form-control form-control-sm" name="parentSupervisionConsideration" rows="2" placeholder="Pertimbangan">{{ old('parentSupervisionConsideration', $payload['parentSupervisionConsideration'] ?? '') }}</textarea>
                        </div>

                        {{-- Opsi 4: Pelayanan Masyarakat --}}
                        <div class="form-check mb-2">
                            <input class="form-check-input deal-toggle" type="checkbox" id="dealCommunityCheck" name="dealCommunityCheck" value="1" {{ old('dealCommunityCheck', $payload['dealCommunityCheck'] ?? '') ? 'checked' : '' }} data-target="#boxCommunity">
                            <label class="form-check-label fw-bold" for="dealCommunityCheck">
                                [4] Anak melakukan Pelayanan Masyarakat di Yayasan/Lembaga Pendidikan
                            </label>
                        </div>
                        <div id="boxCommunity" class="mb-3 ps-4" style="display: {{ old('dealCommunityCheck', $payload['dealCommunityCheck'] ?? '') ? 'block' : 'none' }};">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <input type="text" class="form-control form-control-sm" name="communityServiceLocation" value="{{ old('communityServiceLocation', $payload['communityServiceLocation'] ?? '') }}" placeholder="Yayasan / Lembaga Pendidikan">
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control form-control-sm" name="communityServicePeriod" value="{{ old('communityServicePeriod', $payload['communityServicePeriod'] ?? '') }}" placeholder="Lama pelayanan (Contoh: 14 hari)">
                                </div>
                            </div>
                            <textarea class="form-control form-control-sm" name="communityServiceConsideration" rows="2" placeholder="Pertimbangan">{{ old('communityServiceConsideration', $payload['communityServiceConsideration'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Pasal 2: Pemaafan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="pasal2Content">Pasal 2 (Pemaafan)<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <textarea class="form-control" id="pasal2Content" name="pasal2Content" rows="2" required placeholder="Pernyataan pemaafan pihak korban kepada anak">{{ old('pasal2Content', $payload['pasal2Content'] ?? '') }}</textarea>
                        <small class="text-muted">Pernyataan kesepakatan pemaafan dari korban dan keluarga korban kepada anak.</small>
                    </div>
                </div>

                {{-- ─── PASAL TAMBAHAN (PASAL 3, 4, DAN SETERUSNYA) ─── --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Pasal Tambahan</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <div id="additionalPasalContainer">
                            {{-- Baris Pasal 3 (default) --}}
                            <div class="pasal-item mb-2" data-pasal="3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-secondary px-2 py-2 pasal-badge" style="min-width: 65px; font-size: 0.85rem;">Pasal 3.</span>
                                    <textarea class="form-control form-control-sm pasal-text" name="additionalPasals[0][content]" id="pasal3Content" rows="2" placeholder="Klausul / ketentuan tambahan (opsional)">{{ old('additionalPasals.0.content', old('pasal3Content', $payload['pasal3Content'] ?? '')) }}</textarea>
                                    <input type="hidden" class="pasal-number" name="additionalPasals[0][number]" value="3">
                                    <button type="button" class="btn btn-outline-danger btn-sm btn-remove-pasal" title="Hapus Pasal" style="height: fit-content;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="btnAddPasal">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Pasal
                            </button>
                            <small class="text-muted ms-2">Klik untuk menambahkan Pasal 4, Pasal 5, dan seterusnya bila terdapat kesepakatan tambahan lainnya.</small>
                        </div>
                    </div>
                </div>

                {{-- ─── SAKSI-SAKSI ─── --}}
                <hr>
                <h5 class="fw-bold text-blue-dark">Saksi-Saksi</h5>

                {{-- Saksi PK BAPAS: Nama & Pangkat/NIP --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="bapasOfficerName">Saksi BAPAS<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" id="bapasOfficerName" name="bapasOfficerName" value="{{ old('bapasOfficerName', $payload['bapasOfficerName'] ?? '') }}" placeholder="Nama Petugas PK BAPAS" required>
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" id="bapasOfficerRankNip" name="bapasOfficerRankNip" value="{{ old('bapasOfficerRankNip', $payload['bapasOfficerRankNip'] ?? '') }}" placeholder="Pangkat / NIP PK BAPAS" required>
                    </div>
                </div>

                {{-- Saksi PEKSOS: Nama & Pangkat/NIP --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="socialWorkerName">Saksi PEKSOS<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" id="socialWorkerName" name="socialWorkerName" value="{{ old('socialWorkerName', $payload['socialWorkerName'] ?? '') }}" placeholder="Nama Pekerja Sosial (PEKSOS)" required>
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" id="socialWorkerRankNip" name="socialWorkerRankNip" value="{{ old('socialWorkerRankNip', $payload['socialWorkerRankNip'] ?? '') }}" placeholder="Pangkat / NIP (atau -)" required>
                    </div>
                </div>

                {{-- Saksi Lainnya --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="communityWitnessName">Saksi Lainnya<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input type="text" class="form-control" id="communityWitnessName" name="communityWitnessName" value="{{ old('communityWitnessName', $payload['communityWitnessName'] ?? '') }}" placeholder="Nama Penasihat Hukum Anak / Tokoh Masyarakat / Toga / Toma" required>
                    </div>
                </div>

                <hr>

                {{-- Checkbox Legacy --}}
                @if(strtotime($accident->report_date) < strtotime('2024-01-01') || ($accident->police && $accident->police->is_whitelisted_document_legacy == true && strtotime($accident->police->start_date_whitelisted_document_legacy) <= strtotime($accident->report_date) && strtotime($accident->report_date) <= strtotime($accident->police->end_date_whitelisted_document_legacy)))
                    @include('docs.components.form.checkbox.is-legacy')
                @endif

                <div class="text-center">
                    <button type="submit" class="btn btn-dark-blue me-2" id="suratKesepakatanDiversiFormSubmit">
                        <i class="bi bi-save"></i> {{ __('Simpan') }}
                    </button>
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> {{ __('Batal') }}
                    </a>
                </div>
            </form>
        </div>

    </div>
@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js" defer></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" defer></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js" defer></script>
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

@if(strtotime($accident->report_date) < strtotime('2024-01-01') || ($accident->police && $accident->police->is_whitelisted_document_legacy == true && strtotime($accident->police->start_date_whitelisted_document_legacy) <= strtotime($accident->report_date) && strtotime($accident->report_date) <= strtotime($accident->police->end_date_whitelisted_document_legacy)))
    @include('docs.components.form.checkbox.is-legacy-js')
@endif

    <script type="text/javascript">
        $(document).ready(function() {
            // Blinking timer for attention box
            setInterval(function () {
                $('#attentionBox').toggleClass('alert-danger alert-warning');
            }, 1000);


            $('#diversionDate').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                orientation: 'auto bottom'
            });

            $('#childBirthDate').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                orientation: 'auto bottom'
            });

            $('#childGuardianBirthDate').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                orientation: 'auto bottom'
            });

            $('#victimBirthDate').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                orientation: 'auto bottom'
            });

            $('#victimGuardianBirthDate').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                orientation: 'auto bottom'
            });
        });

        // Select2 with Bootstrap4 theme
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        });

        // Toggle Pendamping Korban & Bentuk Diversi
        $(document).ready(function() {
            $('input[name="isVictimAccompanied"]').on('change', function() {
                if ($(this).val() === '1') {
                    $('#victimGuardianContainer').slideDown();
                } else {
                    $('#victimGuardianContainer').slideUp();
                }
            });

            $('.deal-toggle').on('change', function() {
                var target = $(this).data('target');
                if ($(this).is(':checked')) {
                    $(target).slideDown();
                } else {
                    $(target).slideUp();
                }
            });

            // =========================================================================
            // VALIDASI FORMAT & SANITIZE IDENTITAS (PERSIS SEPERTI PELAPOR / TERLAPOR)
            // =========================================================================
            function sanitizeIdentityField(typeSelector, numberSelector) {
                var $type = $(typeSelector);
                var $field = $(numberSelector);
                var identityTypeId = $type.val();
                var identityTypeName = ($type.find(':selected').data('identity-type-name') || $type.find(':selected').text() || '').toUpperCase();
                var val = $field.val() || '';

                if (!identityTypeId || identityTypeId === '') {
                    $field.prop('disabled', true);
                    $field.attr('placeholder', 'Pilih Jenis Identitas terlebih dahulu');
                    $field.val('');
                    $field.removeAttr('maxlength');
                    $field.removeClass('is-invalid');
                    $field.closest('div').find('.frontend-error, .invalid-feedback').remove();
                    return '';
                } else {
                    $field.prop('disabled', false);
                    $field.attr('placeholder', 'Nomor Identitas');
                }

                if (identityTypeId == 10 || identityTypeName.indexOf('KTP') !== -1 || identityTypeName.indexOf('KARTU TANDA PENDUDUK') !== -1) {
                    $field.attr('maxlength', 16);
                    val = val.replace(/[^0-9]/g, '');
                    if (val.length > 16) val = val.slice(0, 16);
                } else if (identityTypeId == 8 || identityTypeName.indexOf('KK') !== -1 || identityTypeName.indexOf('KARTU KELUARGA') !== -1) {
                    $field.attr('maxlength', 16);
                    val = val.replace(/[^0-9]/g, '');
                    if (val.length > 16) val = val.slice(0, 16);
                } else if (identityTypeId == 13 || identityTypeName.indexOf('SIM') !== -1 || identityTypeName.indexOf('SURAT IZIN MENGEMUDI') !== -1) {
                    $field.attr('maxlength', 16);
                    val = val.replace(/[^0-9]/g, '');
                    if (val.length > 16) val = val.slice(0, 16);
                } else if (identityTypeId == 12 || identityTypeName.indexOf('PASPOR') !== -1 || identityTypeName.indexOf('PASSPORT') !== -1) {
                    $field.attr('maxlength', 9);
                    val = val.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
                    if (val.length > 9) val = val.slice(0, 9);
                } else {
                    $field.removeAttr('maxlength');
                }

                if ($field.val() !== val) {
                    $field.val(val);
                }
                return val;
            }

            function validateIdentityField(typeSelector, numberSelector, isRequired = true) {
                var $type = $(typeSelector);
                var $field = $(numberSelector);
                var identityTypeId = $type.val();
                var identityTypeName = ($type.find(':selected').data('identity-type-name') || $type.find(':selected').text() || '').toUpperCase();
                var val = ($field.val() || '').trim();
                var errorMsg = '';

                if ($field.is(':disabled')) {
                    return null;
                }

                if (val === '') {
                    if (isRequired) {
                        errorMsg = 'Nomor Identitas harus diisi.';
                    } else {
                        return null;
                    }
                } else {
                    if (identityTypeId == 10 || identityTypeName.indexOf('KTP') !== -1 || identityTypeName.indexOf('KARTU TANDA PENDUDUK') !== -1) {
                        if (!/^[0-9]+$/.test(val)) {
                            errorMsg = 'Nomor KTP harus berupa angka saja.';
                        } else if (val.length !== 16) {
                            errorMsg = 'Nomor KTP harus tepat 16 digit (saat ini: ' + val.length + ' digit).';
                        }
                    } else if (identityTypeId == 8 || identityTypeName.indexOf('KK') !== -1 || identityTypeName.indexOf('KARTU KELUARGA') !== -1) {
                        if (!/^[0-9]+$/.test(val)) {
                            errorMsg = 'Nomor Kartu Keluarga (KK) harus berupa angka saja.';
                        } else if (val.length !== 16) {
                            errorMsg = 'Nomor Kartu Keluarga (KK) harus tepat 16 digit (saat ini: ' + val.length + ' digit).';
                        }
                    } else if (identityTypeId == 13 || identityTypeName.indexOf('SIM') !== -1 || identityTypeName.indexOf('SURAT IZIN MENGEMUDI') !== -1) {
                        if (!/^[0-9]+$/.test(val)) {
                            errorMsg = 'Nomor SIM harus berupa angka saja.';
                        } else if (val.length !== 12 && val.length !== 14 && val.length !== 16) {
                            errorMsg = 'Nomor SIM harus 12, 14, atau 16 digit (saat ini: ' + val.length + ' digit).';
                        }
                    } else if (identityTypeId == 12 || identityTypeName.indexOf('PASPOR') !== -1 || identityTypeName.indexOf('PASSPORT') !== -1) {
                        if (!/^[a-zA-Z0-9]+$/.test(val)) {
                            errorMsg = 'Nomor Passport harus alfanumerik (huruf dan angka saja).';
                        } else if (val.length < 7 || val.length > 9) {
                            errorMsg = 'Nomor Passport harus 7 sampai 9 karakter (saat ini: ' + val.length + ' karakter).';
                        }
                    }
                }

                $field.removeClass('is-invalid');
                $field.closest('div').find('.frontend-error, .invalid-feedback').remove();

                if (errorMsg) {
                    $field.addClass('is-invalid');
                    $field.after('<div class="invalid-feedback d-block frontend-error font-weight-bold mt-1 text-danger">' + errorMsg + '</div>');
                    return errorMsg;
                }
                return null;
            }

            function validateBirthDateField(fieldSelector, isRequired = true) {
                var $field = $(fieldSelector);
                if ($field.is(':disabled')) {
                    $field.removeClass('is-invalid');
                    $field.closest('div').find('.frontend-error, .invalid-feedback').remove();
                    return null;
                }

                var val = ($field.val() || '').trim();
                var errorMsg = '';

                if (val === '') {
                    if (isRequired) {
                        errorMsg = 'Tanggal Lahir harus diisi.';
                    } else {
                        return null;
                    }
                } else {
                    var bDate = new Date(val);
                    var today = new Date();
                    today.setHours(23, 59, 59, 999);
                    if (isNaN(bDate.getTime())) {
                        errorMsg = 'Format tanggal lahir tidak valid (YYYY-MM-DD).';
                    } else if (bDate > today) {
                        errorMsg = 'Tanggal lahir tidak boleh melebihi hari ini.';
                    }
                }

                $field.removeClass('is-invalid');
                $field.closest('div').find('.frontend-error, .invalid-feedback').remove();

                if (errorMsg) {
                    $field.addClass('is-invalid');
                    $field.after('<div class="invalid-feedback d-block frontend-error font-weight-bold mt-1 text-danger">' + errorMsg + '</div>');
                    return errorMsg;
                }
                return null;
            }

            // Realtime listeners untuk sanitasi dan validasi identitas Anak
            $('#childIdentityType').on('change select2:select', function() {
                sanitizeIdentityField('#childIdentityType', '#childIdentityNumber');
                var val = ($('#childIdentityNumber').val() || '').trim();
                if (val !== '') {
                    validateIdentityField('#childIdentityType', '#childIdentityNumber', true);
                }
            });
            $('#childIdentityNumber').on('input paste keyup change blur', function() {
                var val = sanitizeIdentityField('#childIdentityType', '#childIdentityNumber');
                if (val !== '') {
                    validateIdentityField('#childIdentityType', '#childIdentityNumber', true);
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).closest('div').find('.frontend-error, .invalid-feedback').remove();
                }
            });

            // Realtime listeners untuk sanitasi dan validasi identitas Pendamping Anak
            $('#childGuardianIdentityType').on('change select2:select', function() {
                sanitizeIdentityField('#childGuardianIdentityType', '#childGuardianIdentity');
                var val = ($('#childGuardianIdentity').val() || '').trim();
                if (val !== '') {
                    validateIdentityField('#childGuardianIdentityType', '#childGuardianIdentity', false);
                }
            });
            $('#childGuardianIdentity').on('input paste keyup change blur', function() {
                var val = sanitizeIdentityField('#childGuardianIdentityType', '#childGuardianIdentity');
                if (val !== '') {
                    validateIdentityField('#childGuardianIdentityType', '#childGuardianIdentity', false);
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).closest('div').find('.frontend-error, .invalid-feedback').remove();
                }
            });

            // Realtime listeners untuk sanitasi dan validasi identitas Korban
            $('#victimIdentityType').on('change select2:select', function() {
                sanitizeIdentityField('#victimIdentityType', '#victimIdentityNumber');
                var val = ($('#victimIdentityNumber').val() || '').trim();
                if (val !== '') {
                    validateIdentityField('#victimIdentityType', '#victimIdentityNumber', true);
                }
            });
            $('#victimIdentityNumber').on('input paste keyup change blur', function() {
                var val = sanitizeIdentityField('#victimIdentityType', '#victimIdentityNumber');
                if (val !== '') {
                    validateIdentityField('#victimIdentityType', '#victimIdentityNumber', true);
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).closest('div').find('.frontend-error, .invalid-feedback').remove();
                }
            });

            // Realtime listeners untuk sanitasi dan validasi identitas Pendamping Korban
            $('#victimGuardianIdentityType').on('change select2:select', function() {
                sanitizeIdentityField('#victimGuardianIdentityType', '#victimGuardianIdentity');
                var val = ($('#victimGuardianIdentity').val() || '').trim();
                if (val !== '') {
                    validateIdentityField('#victimGuardianIdentityType', '#victimGuardianIdentity', false);
                }
            });
            $('#victimGuardianIdentity').on('input paste keyup change blur', function() {
                var val = sanitizeIdentityField('#victimGuardianIdentityType', '#victimGuardianIdentity');
                if (val !== '') {
                    validateIdentityField('#victimGuardianIdentityType', '#victimGuardianIdentity', false);
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).closest('div').find('.frontend-error, .invalid-feedback').remove();
                }
            });

            // Birthdate realtime validation
            $('#childBirthDate').on('change changeDate input blur', function() {
                validateBirthDateField('#childBirthDate', true);
            });
            $('#childGuardianBirthDate').on('change changeDate input blur', function() {
                if ($(this).val()) validateBirthDateField('#childGuardianBirthDate', false);
            });
            $('#victimBirthDate').on('change changeDate input blur', function() {
                validateBirthDateField('#victimBirthDate', true);
            });
            $('#victimGuardianBirthDate').on('change changeDate input blur', function() {
                if ($(this).val()) validateBirthDateField('#victimGuardianBirthDate', false);
            });

            // Initial sanitization on load
            sanitizeIdentityField('#childIdentityType', '#childIdentityNumber');
            sanitizeIdentityField('#childGuardianIdentityType', '#childGuardianIdentity');
            sanitizeIdentityField('#victimIdentityType', '#victimIdentityNumber');
            sanitizeIdentityField('#victimGuardianIdentityType', '#victimGuardianIdentity');

            // Kalkulasi Umur (Tahun, Bulan, Hari) dari Tanggal Lahir
            function calculateAgeFromDate(dateString) {
                if (!dateString) return null;
                var birth = new Date(dateString);
                if (isNaN(birth.getTime())) return null;
                var today = new Date();
                if (birth > today) return null;

                var years = today.getFullYear() - birth.getFullYear();
                var months = today.getMonth() - birth.getMonth();
                var days = today.getDate() - birth.getDate();

                if (days < 0) {
                    months -= 1;
                    var prevMonthLastDay = new Date(today.getFullYear(), today.getMonth(), 0).getDate();
                    days += prevMonthLastDay;
                }
                if (months < 0) {
                    years -= 1;
                    months += 12;
                }
                return { years: years, months: months, days: days };
            }

            // Auto-calculate saat Tanggal Lahir Anak dipilih/diubah
            $('#childBirthDate').on('change changeDate input', function() {
                var res = calculateAgeFromDate($(this).val());
                if (res) {
                    $('#childAgeYear').val(res.years);
                    $('#childAgeMonth').val(res.months);
                    $('#childAgeDay').val(res.days);
                } else {
                    $('#childAgeYear').val('');
                    $('#childAgeMonth').val('');
                    $('#childAgeDay').val('');
                }
            });

            // Auto-calculate saat Tanggal Lahir Korban dipilih/diubah
            $('#victimBirthDate').on('change changeDate input', function() {
                var res = calculateAgeFromDate($(this).val());
                if (res) {
                    $('#victimAgeYear').val(res.years);
                    $('#victimAgeMonth').val(res.months);
                    $('#victimAgeDay').val(res.days);
                } else {
                    $('#victimAgeYear').val('');
                    $('#victimAgeMonth').val('');
                    $('#victimAgeDay').val('');
                }
            });

            // Lock helper: HANYA kunci jika field memiliki nilai dari database.
            function setFieldLock(selector, val, isSelect) {
                var $el = $(selector);
                var hasVal = val !== null && val !== undefined && String(val).trim() !== '' && String(val).trim() !== 'null' && String(val).trim() !== '0';

                if (isSelect) {
                    if (hasVal) {
                        $el.val(String(val)).trigger('change.select2');
                        $el.prop('disabled', true);
                        $el.next('.select2-container').css('pointer-events', 'none');
                        $el.next('.select2-container').find('.select2-selection').css('background-color', '#e9ecef');
                    } else {
                        $el.val('').trigger('change.select2');
                        $el.prop('disabled', false);
                        $el.next('.select2-container').css('pointer-events', '');
                        $el.next('.select2-container').find('.select2-selection').css('background-color', '');
                    }
                } else {
                    if (hasVal) {
                        $el.val(val);
                        $el.prop('readonly', true).css('background-color', '#e9ecef');
                        if ($el.attr('data-provide') === 'datepicker') {
                            $el.css('pointer-events', 'none');
                        }
                    } else {
                        $el.val('');
                        $el.prop('readonly', false).css('background-color', '');
                        $el.css('pointer-events', '');
                    }
                }
            }

            function resetChildFields() {
                var textFields = ['#childName', '#childIdentityNumber', '#childBirthPlace', '#childBirthDate', '#childAddress', '#childAgeYear', '#childAgeMonth', '#childAgeDay'];
                textFields.forEach(function(sel) {
                    $(sel).val('').prop('readonly', false).css('background-color', '').css('pointer-events', '');
                });
                var selectFields = ['#childIdentityType', '#childNationality', '#childGender', '#childJob', '#childReligion'];
                selectFields.forEach(function(sel) {
                    $(sel).val('').trigger('change.select2').prop('disabled', false);
                    $(sel).next('.select2-container').css('pointer-events', '');
                    $(sel).next('.select2-container').find('.select2-selection').css('background-color', '');
                });
                sanitizeIdentityField('#childIdentityType', '#childIdentityNumber');
            }

            function resetVictimFields() {
                var textFields = ['#victimName', '#victimIdentityNumber', '#victimBirthPlace', '#victimBirthDate', '#victimAddress', '#victimAgeYear', '#victimAgeMonth', '#victimAgeDay'];
                textFields.forEach(function(sel) {
                    $(sel).val('').prop('readonly', false).css('background-color', '').css('pointer-events', '');
                });
                var selectFields = ['#victimIdentityType', '#victimNationality', '#victimGender', '#victimJob', '#victimReligion'];
                selectFields.forEach(function(sel) {
                    $(sel).val('').trigger('change.select2').prop('disabled', false);
                    $(sel).next('.select2-container').css('pointer-events', '');
                    $(sel).next('.select2-container').find('.select2-selection').css('background-color', '');
                });
                sanitizeIdentityField('#victimIdentityType', '#victimIdentityNumber');
            }

            // Auto-fill dan lock dari database Tersangka Anak
            $('#suspectId').on('change', function() {
                var $opt = $(this).find(':selected');
                if ($opt.val()) {
                    setFieldLock('#childIdentityType', $opt.data('identity-type'), true);
                    setFieldLock('#childIdentityNumber', $opt.data('identity'), false);
                    setFieldLock('#childName', $opt.data('name'), false);
                    setFieldLock('#childGender', $opt.data('gender'), true);
                    setFieldLock('#childBirthPlace', $opt.data('birthplace'), false);
                    setFieldLock('#childBirthDate', $opt.data('birthdate'), false);
                    setFieldLock('#childAgeYear', $opt.data('age-year'), false);
                    setFieldLock('#childAgeMonth', $opt.data('age-month'), false);
                    setFieldLock('#childAgeDay', $opt.data('age-day'), false);
                    setFieldLock('#childNationality', $opt.data('nationality'), true);
                    setFieldLock('#childJob', $opt.data('job'), true);
                    setFieldLock('#childReligion', $opt.data('religion'), true);
                    setFieldLock('#childAddress', $opt.data('address'), false);

                    sanitizeIdentityField('#childIdentityType', '#childIdentityNumber');
                } else {
                    resetChildFields();
                }
            });

            // Auto-fill dan lock dari database Korban / Pelapor
            $('#victimSelect').on('change', function() {
                var $opt = $(this).find(':selected');
                var val = $opt.val();
                if (val && val !== 'manual') {
                    setFieldLock('#victimIdentityType', $opt.data('identity-type'), true);
                    setFieldLock('#victimIdentityNumber', $opt.data('identity'), false);
                    setFieldLock('#victimName', $opt.data('name'), false);
                    setFieldLock('#victimGender', $opt.data('gender'), true);
                    setFieldLock('#victimBirthPlace', $opt.data('birthplace'), false);
                    setFieldLock('#victimBirthDate', $opt.data('birthdate'), false);
                    setFieldLock('#victimAgeYear', $opt.data('age-year'), false);
                    setFieldLock('#victimAgeMonth', $opt.data('age-month'), false);
                    setFieldLock('#victimAgeDay', $opt.data('age-day'), false);
                    setFieldLock('#victimNationality', $opt.data('nationality'), true);
                    setFieldLock('#victimJob', $opt.data('job'), true);
                    setFieldLock('#victimReligion', $opt.data('religion'), true);
                    setFieldLock('#victimAddress', $opt.data('address'), false);

                    sanitizeIdentityField('#victimIdentityType', '#victimIdentityNumber');
                } else if (val === 'manual') {
                    resetVictimFields();
                } else {
                    resetVictimFields();
                }
            });

            // Dynamic Pasal Tambahan (Pasal 3, 4, dst.)
            $('#btnAddPasal').on('click', function() {
                var currentItems = $('#additionalPasalContainer .pasal-item').length;
                var nextNumber = 3;
                if (currentItems > 0) {
                    var lastNumber = parseInt($('#additionalPasalContainer .pasal-item:last').data('pasal')) || (currentItems + 2);
                    nextNumber = lastNumber + 1;
                }
                var newIndex = currentItems;
                var html = `
                    <div class="pasal-item mb-2 mt-2" data-pasal="${nextNumber}">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-secondary px-2 py-2 pasal-badge" style="min-width: 65px; font-size: 0.85rem;">Pasal ${nextNumber}.</span>
                            <textarea class="form-control form-control-sm pasal-text" name="additionalPasals[${newIndex}][content]" rows="2" placeholder="Klausul / ketentuan tambahan"></textarea>
                            <input type="hidden" class="pasal-number" name="additionalPasals[${newIndex}][number]" value="${nextNumber}">
                            <button type="button" class="btn btn-outline-danger btn-sm btn-remove-pasal" title="Hapus Pasal" style="height: fit-content;">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                $('#additionalPasalContainer').append(html);
            });

            // Hapus Pasal & Re-indexing otomatis
            $(document).on('click', '.btn-remove-pasal', function() {
                $(this).closest('.pasal-item').remove();
                $('#additionalPasalContainer .pasal-item').each(function(idx) {
                    var num = idx + 3;
                    $(this).attr('data-pasal', num);
                    $(this).find('.pasal-badge').text('Pasal ' + num + '.');
                    $(this).find('.pasal-number').val(num).attr('name', 'additionalPasals[' + idx + '][number]');
                    $(this).find('.pasal-text').attr('name', 'additionalPasals[' + idx + '][content]');
                    if (idx === 0) {
                        $(this).find('.pasal-text').attr('id', 'pasal3Content');
                    } else {
                        $(this).find('.pasal-text').removeAttr('id');
                    }
                });
            });
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
            $field.closest('.input-group, .mb-3, .col-lg-10, .col-md-10, div').find('.frontend-error, .invalid-feedback').remove();
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
        $(document).ready(function() {
            $('#suratKesepakatanDiversiFormSubmit').on('click', function(e) {
                e.preventDefault();

                // Bersihkan error sebelumnya
                $('.is-invalid').removeClass('is-invalid');
                $('.border.border-danger').removeClass('border border-danger');
                $('.select2-selection').removeClass('border border-danger is-invalid');
                $('.frontend-error').remove();
                $('.invalid-feedback').remove();

                let errors = [];

                function markError(fieldSelector, message) {
                    var $field = $(fieldSelector);
                    if (!$field.length) return;

                    $field.addClass('is-invalid');
                    if ($field.next('.select2-container').length) {
                        $field.next('.select2-container').find('.select2-selection').addClass('border border-danger is-invalid');
                    }
                    var $target = $field.next('.select2-container').length ? $field.next('.select2-container') : $field;
                    $target.siblings('.frontend-error, .invalid-feedback').remove();
                    $target.next('.frontend-error, .invalid-feedback').remove();
                    $target.after('<div class="invalid-feedback d-block frontend-error font-weight-bold mt-1 text-danger">' + message + '</div>');
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

                // 1. Validasi Dokumen & Musyawarah
                checkInput('#diversionDate', 'Tanggal Musyawarah');
                checkInput('#diversionRoom', 'Tempat Musyawarah');
                checkInput('#diversionStreet', 'Jalan Tempat Musyawarah');
                checkSelect('#facilitatorOfficer', 'Fasilitator Diversi');

                // 2. Validasi Identitas Anak (Pihak I)
                checkSelect('#childIdentityType', 'Jenis Identitas Anak');
                checkInput('#childIdentityNumber', 'Nomor Identitas Anak');
                var childIdErr = validateIdentityField('#childIdentityType', '#childIdentityNumber', true);
                if (childIdErr) errors.push(childIdErr);

                checkInput('#childName', 'Nama Anak');
                checkSelect('#childGender', 'Jenis Kelamin Anak');
                checkInput('#childBirthPlace', 'Tempat Lahir Anak');
                var childBirthErr = validateBirthDateField('#childBirthDate', true);
                if (childBirthErr) errors.push(childBirthErr);

                checkSelect('#childNationality', 'Kewarganegaraan Anak');
                checkSelect('#childJob', 'Pekerjaan Anak');
                checkSelect('#childReligion', 'Agama Anak');
                checkInput('#childAddress', 'Alamat Anak');

                // 3. Validasi Pendamping Anak
                checkSelect('#childGuardianFrom', 'Pendamping Anak Dari');
                checkSelect('#childGuardianRelation', 'Hubungan Keluarga Pendamping Anak');
                checkInput('#childGuardianName', 'Nama Pendamping Anak');
                if ($('#childGuardianIdentity').val()) {
                    var cgIdErr = validateIdentityField('#childGuardianIdentityType', '#childGuardianIdentity', false);
                    if (cgIdErr) errors.push(cgIdErr);
                }
                if ($('#childGuardianBirthDate').val()) {
                    var cgBirthErr = validateBirthDateField('#childGuardianBirthDate', false);
                    if (cgBirthErr) errors.push(cgBirthErr);
                }

                // 4. Validasi Identitas Korban (Pihak II)
                checkSelect('#victimIdentityType', 'Jenis Identitas Korban');
                checkInput('#victimIdentityNumber', 'Nomor Identitas Korban');
                var victimIdErr = validateIdentityField('#victimIdentityType', '#victimIdentityNumber', true);
                if (victimIdErr) errors.push(victimIdErr);

                checkInput('#victimName', 'Nama Korban');
                checkSelect('#victimGender', 'Jenis Kelamin Korban');
                checkInput('#victimBirthPlace', 'Tempat Lahir Korban');
                var victimBirthErr = validateBirthDateField('#victimBirthDate', true);
                if (victimBirthErr) errors.push(victimBirthErr);

                checkSelect('#victimNationality', 'Kewarganegaraan Korban');
                checkSelect('#victimJob', 'Pekerjaan Korban');
                checkSelect('#victimReligion', 'Agama Korban');
                checkInput('#victimAddress', 'Alamat Korban');

                // 5. Validasi Pendamping Korban (bila dicentang/didampingi)
                if ($('input[name="isVictimAccompanied"]:checked').val() === '1') {
                    checkInput('#victimGuardianName', 'Nama Pendamping Korban');
                    if ($('#victimGuardianIdentity').val()) {
                        var vgIdErr = validateIdentityField('#victimGuardianIdentityType', '#victimGuardianIdentity', false);
                        if (vgIdErr) errors.push(vgIdErr);
                    }
                    if ($('#victimGuardianBirthDate').val()) {
                        var vgBirthErr = validateBirthDateField('#victimGuardianBirthDate', false);
                        if (vgBirthErr) errors.push(vgBirthErr);
                    }
                }

                // 6. Validasi Kesepakatan (Pasal 1 & Pasal 2)
                if (!$('#dealCompensationCheck').is(':checked') && !$('#dealRehabCheck').is(':checked') && !$('#dealBapasCheck').is(':checked') && !$('#dealCommunityCheck').is(':checked')) {
                    markError('#dealCompensationCheck', 'Pilih minimal satu bentuk kesepakatan diversi pada Pasal 1');
                }
                checkInput('#pasal2Content', 'Pasal 2 (Pemaafan)');

                // 7. Validasi Saksi-Saksi
                checkInput('#bapasOfficerName', 'Nama Saksi PK BAPAS');
                checkInput('#bapasOfficerRankNip', 'Pangkat / NIP Saksi PK BAPAS');
                checkInput('#socialWorkerName', 'Nama Saksi Pekerja Sosial (PEKSOS)');
                checkInput('#socialWorkerRankNip', 'Pangkat / NIP Saksi Pekerja Sosial');
                checkInput('#communityWitnessName', 'Saksi Lainnya');

                if (errors.length > 0) {
                    scrollToFirstError();
                    return false;
                }

                Swal.fire({
                    title: 'Berhasil',
                    text: 'Silahkan menunggu proses simpan data',
                    icon: 'success',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    $('#suratKesepakatanDiversiForm').find(':disabled').prop('disabled', false);
                    $('#suratKesepakatanDiversiForm')[0].submit();
                });
            });
        });
    </script>
@endpush
