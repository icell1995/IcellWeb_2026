@php
    $_title = 'Surat Kesepakatan Diversi';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
    <style>
        .input-group.row > .col-lg-10,
        .input-group.row > .col-md-10,
        .input-group > div.d-flex,
        .input-group .d-flex.align-self-center {
            flex-direction: column !important;
            align-items: stretch !important;
            flex-wrap: wrap !important;
        }
        .input-group.row > .col-lg-10 > .select2-container,
        .input-group.row > .col-md-10 > .select2-container,
        .input-group .select2-container {
            width: 100% !important;
        }
        .select2-container--bootstrap4 .select2-selection.border-danger,
        .select2-container--bootstrap4.is-invalid .select2-selection,
        .select2-selection.border-danger {
            border: 1px solid #dc3545 !important;
        }
        .frontend-error,
        .invalid-feedback {
            display: block !important;
            width: 100% !important;
            margin-top: 0.25rem !important;
            font-size: 0.85rem !important;
            color: #dc3545 !important;
            font-weight: 600 !important;
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
            <h5 class="fw-bold text-blue-dark">Tambah Surat Kesepakatan Diversi</h5>

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
            <form action="{{ route('doc.surat-kesepakatan-diversi-document.store', ['accident_id' => $accidentId]) }}"
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
                                    {{ old('suspectId') == $suspect->id ? 'selected' : '' }}>
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
                        <input id="childName" type="text" class="form-control" name="childName" value="{{ old('childName') }}" required placeholder="Nama Lengkap">
                    </div>
                </div>

                {{-- Jenis Identitas Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childIdentityType">Jenis Identitas<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" id="childIdentityType" name="childIdentityType">
                            <option value="">--Pilih Jenis Identitas--</option>
                            @foreach ($identityTypes as $identityType)
                                <option value="{{ $identityType->id }}" data-identity-type-name="{{ $identityType->name }}" {{ old('childIdentityType') == $identityType->id ? 'selected' : '' }}>
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
                        <input id="childIdentityNumber" type="text" class="form-control" name="childIdentityNumber" value="{{ old('childIdentityNumber') }}"
                            placeholder="{{ empty(old('childIdentityType')) ? 'Pilih Jenis Identitas terlebih dahulu' : 'Nomor Identitas' }}"
                            @if(empty(old('childIdentityType'))) disabled @endif>
                    </div>
                </div>

                {{-- Kewarganegaraan Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childNationality">Kewarganegaraan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="childNationality" id="childNationality">
                            <option value="">--Pilih Kewarganegaraan--</option>
                            @foreach ($nationalities as $nationality)
                                <option value="{{ $nationality->id }}" {{ old('childNationality') == $nationality->id ? 'selected' : '' }}>
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
                                <option value="{{ $gender->id }}" {{ old('childGender') == $gender->id ? 'selected' : '' }}>{{ $gender->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Tempat Lahir Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childBirthPlace">Tempat Lahir<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="childBirthPlace" type="text" class="form-control" name="childBirthPlace" value="{{ old('childBirthPlace') }}" placeholder="Tempat Lahir">
                    </div>
                </div>

                {{-- Tanggal Lahir Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childBirthDate">Tanggal Lahir<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="childBirthDate" type="text" class="form-control" name="childBirthDate" value="{{ old('childBirthDate') }}" placeholder="YYYY-MM-DD" data-provide="datepicker" autocomplete="off">
                    </div>
                </div>

                {{-- Umur Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Umur<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <div class="row">
                            <div class="col-4">
                                <div class="input-group">
                                    <input type="number" min="0" max="100" class="form-control" id="childAgeYear" name="childAgeYear" value="{{ old('childAgeYear') }}" placeholder="0" readonly style="background-color: #e9ecef;">
                                    <div class="input-group-append"><span class="input-group-text">Tahun</span></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="input-group">
                                    <input type="number" min="0" max="11" class="form-control" id="childAgeMonth" name="childAgeMonth" value="{{ old('childAgeMonth') }}" placeholder="0" readonly style="background-color: #e9ecef;">
                                    <div class="input-group-append"><span class="input-group-text">Bulan</span></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="input-group">
                                    <input type="number" min="0" max="31" class="form-control" id="childAgeDay" name="childAgeDay" value="{{ old('childAgeDay') }}" placeholder="0" readonly style="background-color: #e9ecef;">
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
                                <option value="{{ $job->id }}" {{ old('childJob') == $job->id ? 'selected' : '' }}>{{ $job->name }}</option>
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
                                <option value="{{ $religion->id }}" {{ old('childReligion') == $religion->id ? 'selected' : '' }}>{{ $religion->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Alamat Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childAddress">Alamat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <textarea id="childAddress" class="form-control" name="childAddress" rows="2" placeholder="Alamat">{{ old('childAddress') }}</textarea>
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
                            @foreach(['Orang Tua', 'Wali', 'Pendamping dari ......'] as $opt)
                                <option value="{{ $opt }}" {{ old('childGuardianFrom') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                        <div id="childGuardianFromDetailContainer" class="mt-2 w-100" style="display: {{ old('childGuardianFrom') == 'Pendamping dari ......' ? 'block' : 'none' }};">
                            <input type="text" class="form-control" name="childGuardianFromDetail" id="childGuardianFromDetail"
                                value="{{ old('childGuardianFromDetail') }}" placeholder="Sebutkan instansi / lembaga pendamping (contoh: BAPAS / PEKSOS / Advokat)">
                        </div>
                    </div>
                </div>

                {{-- Nama Pendamping Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianName">Nama<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="childGuardianName" type="text" class="form-control" name="childGuardianName" value="{{ old('childGuardianName') }}" placeholder="Nama Lengkap">
                    </div>
                </div>

                {{-- Jenis Identitas Pendamping Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianIdentityType">Jenis Identitas<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" id="childGuardianIdentityType" name="childGuardianIdentityType">
                            <option value="">--Pilih Jenis Identitas--</option>
                            @foreach ($identityTypes as $identityType)
                                <option value="{{ $identityType->id }}" data-identity-type-name="{{ $identityType->name }}" {{ old('childGuardianIdentityType') == $identityType->id ? 'selected' : '' }}>
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
                        <input id="childGuardianIdentity" type="text" class="form-control" name="childGuardianIdentity" value="{{ old('childGuardianIdentity') }}"
                            placeholder="{{ empty(old('childGuardianIdentityType')) ? 'Pilih Jenis Identitas terlebih dahulu' : 'Nomor Identitas' }}"
                            @if(empty(old('childGuardianIdentityType'))) disabled @endif>
                    </div>
                </div>

                {{-- Kewarganegaraan Pendamping Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianNationality">Kewarganegaraan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="childGuardianNationality" id="childGuardianNationality">
                            <option value="">--Pilih Kewarganegaraan--</option>
                            @foreach ($nationalities as $nationality)
                                <option value="{{ $nationality->id }}" {{ old('childGuardianNationality') == $nationality->id ? 'selected' : '' }}>
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
                                <option value="{{ $gender->id }}" {{ old('childGuardianGender') == $gender->id ? 'selected' : '' }}>{{ $gender->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Tempat Lahir Pendamping Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianBirthPlace">Tempat Lahir<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="childGuardianBirthPlace" type="text" class="form-control" name="childGuardianBirthPlace" value="{{ old('childGuardianBirthPlace') }}" placeholder="Tempat Lahir">
                    </div>
                </div>

                {{-- Tanggal Lahir Pendamping Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianBirthDate">Tanggal Lahir<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="childGuardianBirthDate" type="text" class="form-control" name="childGuardianBirthDate" value="{{ old('childGuardianBirthDate') }}" placeholder="YYYY-MM-DD" data-provide="datepicker" autocomplete="off">
                    </div>
                </div>

                {{-- Pekerjaan Pendamping Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianJob">Pekerjaan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="childGuardianJob" id="childGuardianJob">
                            <option value="">--Pilih Pekerjaan--</option>
                            @foreach ($jobs as $job)
                                <option value="{{ $job->id }}" {{ old('childGuardianJob') == $job->id ? 'selected' : '' }}>{{ $job->name }}</option>
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
                                <option value="{{ $religion->id }}" {{ old('childGuardianReligion') == $religion->id ? 'selected' : '' }}>{{ $religion->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Alamat Pendamping Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianAddress">Alamat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <textarea id="childGuardianAddress" class="form-control" name="childGuardianAddress" rows="2" placeholder="Alamat">{{ old('childGuardianAddress') }}</textarea>
                    </div>
                </div>

                {{-- Hubungan Keluarga Pendamping Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianRelation">Hubungan Keluarga<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="childGuardianRelation" id="childGuardianRelation">
                            <option value="">--Pilih Hubungan Keluarga--</option>
                            @foreach(['Ayah Kandung', 'Ibu Kandung', 'Kakek / Nenek', 'Paman / Bibi', 'Kakak Kandung', 'Wali', 'Penasihat Hukum', 'Lainnya'] as $opt)
                                <option value="{{ $opt }}" {{ old('childGuardianRelation') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
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
                                        {{ old('victimSelect') == $victim->id ? 'selected' : '' }}>
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
                        <input id="victimName" type="text" class="form-control" name="victimName" value="{{ old('victimName') }}" required placeholder="Nama Lengkap">
                    </div>
                </div>

                {{-- Jenis Identitas Korban --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="victimIdentityType">Jenis Identitas<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" id="victimIdentityType" name="victimIdentityType">
                            <option value="">--Pilih Jenis Identitas--</option>
                            @foreach ($identityTypes as $identityType)
                                <option value="{{ $identityType->id }}" data-identity-type-name="{{ $identityType->name }}" {{ old('victimIdentityType') == $identityType->id ? 'selected' : '' }}>
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
                        <input id="victimIdentityNumber" type="text" class="form-control" name="victimIdentityNumber" value="{{ old('victimIdentityNumber') }}"
                            placeholder="{{ empty(old('victimIdentityType')) ? 'Pilih Jenis Identitas terlebih dahulu' : 'Nomor Identitas' }}"
                            @if(empty(old('victimIdentityType'))) disabled @endif>
                    </div>
                </div>

                {{-- Kewarganegaraan Korban --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="victimNationality">Kewarganegaraan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="victimNationality" id="victimNationality">
                            <option value="">--Pilih Kewarganegaraan--</option>
                            @foreach ($nationalities as $nationality)
                                <option value="{{ $nationality->id }}" {{ old('victimNationality') == $nationality->id ? 'selected' : '' }}>
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
                                <option value="{{ $gender->id }}" {{ old('victimGender') == $gender->id ? 'selected' : '' }}>{{ $gender->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Tempat Lahir Korban --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="victimBirthPlace">Tempat Lahir<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="victimBirthPlace" type="text" class="form-control" name="victimBirthPlace" value="{{ old('victimBirthPlace') }}" placeholder="Tempat Lahir">
                    </div>
                </div>

                {{-- Tanggal Lahir Korban --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="victimBirthDate">Tanggal Lahir<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="victimBirthDate" type="text" class="form-control" name="victimBirthDate" value="{{ old('victimBirthDate') }}" placeholder="YYYY-MM-DD" data-provide="datepicker" autocomplete="off">
                    </div>
                </div>

                {{-- Umur Korban --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Umur<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <div class="row">
                            <div class="col-4">
                                <div class="input-group">
                                    <input type="number" min="0" max="100" class="form-control" id="victimAgeYear" name="victimAgeYear" value="{{ old('victimAgeYear') }}" placeholder="0" readonly style="background-color: #e9ecef;">
                                    <div class="input-group-append"><span class="input-group-text">Tahun</span></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="input-group">
                                    <input type="number" min="0" max="11" class="form-control" id="victimAgeMonth" name="victimAgeMonth" value="{{ old('victimAgeMonth') }}" placeholder="0" readonly style="background-color: #e9ecef;">
                                    <div class="input-group-append"><span class="input-group-text">Bulan</span></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="input-group">
                                    <input type="number" min="0" max="31" class="form-control" id="victimAgeDay" name="victimAgeDay" value="{{ old('victimAgeDay') }}" placeholder="0" readonly style="background-color: #e9ecef;">
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
                                <option value="{{ $job->id }}" {{ old('victimJob') == $job->id ? 'selected' : '' }}>{{ $job->name }}</option>
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
                                <option value="{{ $religion->id }}" {{ old('victimReligion') == $religion->id ? 'selected' : '' }}>{{ $religion->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Alamat Korban --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="victimAddress">Alamat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <textarea id="victimAddress" class="form-control" name="victimAddress" rows="2" placeholder="Alamat">{{ old('victimAddress') }}</textarea>
                    </div>
                </div>

                {{-- Status Pendampingan Korban (Radio Button) --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Pendamping Korban<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <div class="d-flex mb-2">
                            <div class="form-check me-4">
                                <input class="form-check-input" type="radio" id="victimNotAccompanied" name="isVictimAccompanied" value="0" {{ old('isVictimAccompanied', '0') == '0' ? 'checked' : '' }}>
                                <label class="form-check-label" for="victimNotAccompanied">
                                    Tidak Didampingi (Korban Dewasa)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" id="victimAccompanied" name="isVictimAccompanied" value="1" {{ old('isVictimAccompanied') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="victimAccompanied">
                                    Didampingi (Orang Tua / Wali / Pendamping)
                                </label>
                            </div>
                        </div>
                        <small class="text-muted">(*Korban dewasa tidak wajib didampingi)</small>
                    </div>
                </div>

                <div id="victimGuardianContainer" style="display: {{ old('isVictimAccompanied') == '1' ? 'block' : 'none' }};">
                    {{-- Pendamping Korban Dari --}}
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianFrom">Pendamping Dari</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            <select class="form-control select2" name="victimGuardianFrom" id="victimGuardianFrom">
                                <option value="">--Pilih Pendamping Dari--</option>
                                @foreach(['Orang Tua', 'Wali', 'Pendamping dari ......'] as $opt)
                                    <option value="{{ $opt }}" {{ old('victimGuardianFrom') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                            <div id="victimGuardianFromDetailContainer" class="mt-2 w-100" style="display: {{ old('victimGuardianFrom') == 'Pendamping dari ......' ? 'block' : 'none' }};">
                                <input type="text" class="form-control" name="victimGuardianFromDetail" id="victimGuardianFromDetail"
                                    value="{{ old('victimGuardianFromDetail') }}" placeholder="Sebutkan instansi / lembaga pendamping (contoh: BAPAS / PEKSOS / Advokat)">
                            </div>
                        </div>
                    </div>

                    {{-- Nama Pendamping Korban --}}
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianName">Nama</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            <input id="victimGuardianName" type="text" class="form-control" name="victimGuardianName" value="{{ old('victimGuardianName') }}" placeholder="Nama Lengkap">
                        </div>
                    </div>

                    {{-- Jenis Identitas Pendamping Korban --}}
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianIdentityType">Jenis Identitas</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            <select class="form-control select2" id="victimGuardianIdentityType" name="victimGuardianIdentityType">
                                <option value="">--Pilih Jenis Identitas--</option>
                                @foreach ($identityTypes as $identityType)
                                    <option value="{{ $identityType->id }}" data-identity-type-name="{{ $identityType->name }}" {{ old('victimGuardianIdentityType') == $identityType->id ? 'selected' : '' }}>
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
                            <input id="victimGuardianIdentity" type="text" class="form-control" name="victimGuardianIdentity" value="{{ old('victimGuardianIdentity') }}"
                                placeholder="{{ empty(old('victimGuardianIdentityType')) ? 'Pilih Jenis Identitas terlebih dahulu' : 'Nomor Identitas' }}"
                                @if(empty(old('victimGuardianIdentityType'))) disabled @endif>
                        </div>
                    </div>

                    {{-- Kewarganegaraan Pendamping Korban --}}
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianNationality">Kewarganegaraan</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            <select class="form-control select2" name="victimGuardianNationality" id="victimGuardianNationality">
                                <option value="">--Pilih Kewarganegaraan--</option>
                                @foreach ($nationalities as $nationality)
                                    <option value="{{ $nationality->id }}" {{ old('victimGuardianNationality') == $nationality->id ? 'selected' : '' }}>
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
                                    <option value="{{ $gender->id }}" {{ old('victimGuardianGender') == $gender->id ? 'selected' : '' }}>{{ $gender->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Tempat Lahir Pendamping Korban --}}
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianBirthPlace">Tempat Lahir</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            <input id="victimGuardianBirthPlace" type="text" class="form-control" name="victimGuardianBirthPlace" value="{{ old('victimGuardianBirthPlace') }}" placeholder="Tempat Lahir">
                        </div>
                    </div>

                    {{-- Tanggal Lahir Pendamping Korban --}}
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianBirthDate">Tanggal Lahir</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            <input id="victimGuardianBirthDate" type="text" class="form-control" name="victimGuardianBirthDate" value="{{ old('victimGuardianBirthDate') }}" placeholder="YYYY-MM-DD" data-provide="datepicker" autocomplete="off">
                        </div>
                    </div>

                    {{-- Pekerjaan Pendamping Korban --}}
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianJob">Pekerjaan</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            <select class="form-control select2" name="victimGuardianJob" id="victimGuardianJob">
                                <option value="">--Pilih Pekerjaan--</option>
                                @foreach ($jobs as $job)
                                    <option value="{{ $job->id }}" {{ old('victimGuardianJob') == $job->id ? 'selected' : '' }}>{{ $job->name }}</option>
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
                                    <option value="{{ $religion->id }}" {{ old('victimGuardianReligion') == $religion->id ? 'selected' : '' }}>{{ $religion->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Alamat Pendamping Korban --}}
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianAddress">Alamat</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            <textarea id="victimGuardianAddress" class="form-control" name="victimGuardianAddress" rows="2" placeholder="Alamat">{{ old('victimGuardianAddress') }}</textarea>
                        </div>
                    </div>

                    {{-- Hubungan Keluarga Pendamping Korban --}}
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianRelation">Hubungan Keluarga</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            <select class="form-control select2" name="victimGuardianRelation" id="victimGuardianRelation">
                                <option value="">--Pilih Hubungan Keluarga--</option>
                                @foreach(['Ayah Kandung', 'Ibu Kandung', 'Kakek / Nenek', 'Paman / Bibi', 'Kakak Kandung', 'Suami / Istri', 'Anak Kandung', 'Wali', 'Penasihat Hukum', 'Lainnya'] as $opt)
                                    <option value="{{ $opt }}" {{ old('victimGuardianRelation') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
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
                            placeholder="YYYY-MM-DD" autocomplete="off" value="{{ old('diversionDate') }}"
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
                            placeholder="Ruang Musyawarah (Contoh: Ruang Mediasi)" value="{{ old('diversionRoom') }}">

                        @error('diversionRoom')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" id="diversionStreet" name="diversionStreet"
                            placeholder="Jalan / Alamat Tempat Pelaksanaan Musyawarah" value="{{ old('diversionStreet') }}">

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
                                <option value="{{ $data->id }}" data-register-number="{{ $data->register_number }}" {{ old('facilitatorOfficer') == $data->id ? 'selected' : '' }}>
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
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold text-blue-dark mb-1">Isi Kesepakatan Diversi (Pasal-Pasal S-44.3)</h5>
                        <small class="text-muted">Kelola kesepakatan per-pasal secara fleksibel. Setiap pasal dapat berisi narasi dan/atau butir-butir penomoran berurutan [1], [2], dst.</small>
                    </div>
                </div>

                <div id="pasalContainer" class="mb-3">
                    {{-- Dinamis dibuat oleh JavaScript --}}
                </div>

                <div class="d-flex align-items-center gap-2 mb-4">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="btnAddPasal">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Pasal Baru
                    </button>
                    <small class="text-muted">Klik untuk menambahkan Pasal 3, Pasal 4, dan seterusnya bila terdapat klausul kesepakatan tambahan.</small>
                </div>

                {{-- ─── SAKSI-SAKSI ─── --}}
                <hr>
                <h5 class="fw-bold text-blue-dark">Saksi-Saksi</h5>

                {{-- Saksi PK BAPAS: Nama & Pangkat/NIP --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="bapasOfficerName">Saksi BAPAS<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" id="bapasOfficerName" name="bapasOfficerName" value="{{ old('bapasOfficerName') }}" placeholder="Nama Petugas PK BAPAS" required>
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" id="bapasOfficerRankNip" name="bapasOfficerRankNip" value="{{ old('bapasOfficerRankNip') }}" placeholder="Pangkat / NIP PK BAPAS" required>
                    </div>
                </div>

                {{-- Saksi PEKSOS: Nama & Pangkat/NIP --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="socialWorkerName">Saksi PEKSOS<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" id="socialWorkerName" name="socialWorkerName" value="{{ old('socialWorkerName') }}" placeholder="Nama Pekerja Sosial (PEKSOS)" required>
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" id="socialWorkerRankNip" name="socialWorkerRankNip" value="{{ old('socialWorkerRankNip') }}" placeholder="Pangkat / NIP (atau -)" required>
                    </div>
                </div>

                {{-- Saksi Lainnya --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="communityWitnessName">Saksi Lainnya<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input type="text" class="form-control" id="communityWitnessName" name="communityWitnessName" value="{{ old('communityWitnessName') }}" placeholder="Nama Penasihat Hukum Anak / Tokoh Masyarakat / Toga / Toma" required>
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
        // =========================================================================
        // HELPER VALIDASI & SANITASI IDENTITAS (LEVEL SKRIP GLOBAL)
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
                $field.parent().addClass('flex-wrap');
                $field.addClass('is-invalid');
                $field.after('<div class="invalid-feedback d-block w-100 frontend-error font-weight-bold mt-1 text-danger">' + errorMsg + '</div>');
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
                $field.parent().addClass('flex-wrap');
                $field.addClass('is-invalid');
                $field.after('<div class="invalid-feedback d-block w-100 frontend-error font-weight-bold mt-1 text-danger">' + errorMsg + '</div>');
                return errorMsg;
            }
            return null;
        }

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

        window.sanitizeIdentityField = sanitizeIdentityField;
        window.validateIdentityField = validateIdentityField;
        window.validateBirthDateField = validateBirthDateField;
        window.calculateAgeFromDate = calculateAgeFromDate;

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

            $('#childGuardianFrom').on('change', function() {
                if ($(this).val() === 'Pendamping dari ......') {
                    $('#childGuardianFromDetailContainer').slideDown();
                } else {
                    $('#childGuardianFromDetailContainer').slideUp();
                }
            });

            $('#victimGuardianFrom').on('change', function() {
                if ($(this).val() === 'Pendamping dari ......') {
                    $('#victimGuardianFromDetailContainer').slideDown();
                } else {
                    $('#victimGuardianFromDetailContainer').slideUp();
                }
            });


            // =========================================================================
            // EVENT LISTENERS IDENTITAS
            // =========================================================================

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

            // Initial check jika suspectId sudah terisi saat load
            if ($('#suspectId').val()) {
                $('#suspectId').trigger('change');
            }

            // =========================================================================
            // DYNAMIC PER-PASAL & POIN LIST (OPSI B)
            // =========================================================================
            var initialPasals = @json(old('pasals', $initialPasals ?? []));
            if (!initialPasals || initialPasals.length === 0) {
                initialPasals = [
                    {
                        number: 1,
                        subtitle: '',
                        content: '',
                        points: []
                    }
                ];
            }

            function renderPasals() {
                var $container = $('#pasalContainer');
                $container.empty();

                initialPasals.forEach(function(pasal, pIdx) {
                    var pNum = pIdx + 1;
                    pasal.number = pNum;

                    var subtitleVal = pasal.subtitle || '';
                    var contentVal = pasal.content || '';
                    var points = Array.isArray(pasal.points) ? pasal.points : [];

                    var deleteBtnHtml = (pNum > 1)
                        ? `<button type="button" class="btn btn-outline-danger btn-sm btn-delete-pasal" title="Hapus Pasal">
                               <i class="bi bi-trash"></i> Hapus Pasal
                           </button>`
                        : `<span class="badge bg-light text-muted border py-2 px-3">Pasal Wajib</span>`;

                    var $card = $(`
                        <div class="card mb-3 border shadow-sm pasal-card" data-pasal-index="${pIdx}">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2 flex-grow-1">
                                    <span class="badge bg-primary px-3 py-2 fs-6 pasal-badge flex-shrink-0">Pasal ${pNum}.</span>
                                    <input type="hidden" name="pasals[${pIdx}][number]" class="pasal-number-input" value="${pNum}">
                                    <input type="text" name="pasals[${pIdx}][subtitle]" class="form-control form-control-sm pasal-subtitle-input flex-grow-1 w-100" placeholder="Sub-judul pasal (opsional, contoh: [Jenis kesepakatan Diversi yang disepakati])" value="${$('<div>').text(subtitleVal).html()}">
                                </div>
                                <div class="flex-shrink-0 ms-auto">
                                    ${deleteBtnHtml}
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-secondary mb-1">Narasi / Teks Isi Pasal:</label>
                                    <textarea class="form-control form-control-sm pasal-content-input" name="pasals[${pIdx}][content]" rows="2" placeholder="Tuliskan narasi isi pasal (bisa dikombinasikan dengan butir berurutan di bawah)...">${$('<div>').text(contentVal).html()}</textarea>
                                </div>
                                <div class="pasal-points-container">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label fw-bold small text-secondary mb-0">Poin / Butir Berurutan (Opsional):</label>
                                    </div>
                                    <div class="points-list">
                                    </div>
                                    <div class="mt-2 d-flex flex-wrap gap-2 align-items-center">
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-add-point">
                                            <i class="bi bi-plus-circle me-1"></i> Tambah Poin [<span class="next-point-num">${points.length + 1}</span>]
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);

                    var $pointsList = $card.find('.points-list');
                    points.forEach(function(ptText, ptIdx) {
                        renderPointItem($pointsList, pIdx, ptIdx, ptText);
                    });

                    $container.append($card);
                });

                updateAllIndices();
            }

            function renderPointItem($pointsList, pIdx, ptIdx, ptText) {
                var ptNum = ptIdx + 1;
                var $ptItem = $(`
                    <div class="point-item mb-2 d-flex align-items-start gap-2" data-point-index="${ptIdx}">
                        <span class="badge bg-secondary px-2 py-2 point-badge" style="min-width: 42px; font-size: 0.85rem; margin-top: 2px;">[${ptNum}]</span>
                        <textarea class="form-control form-control-sm point-text" name="pasals[${pIdx}][points][${ptIdx}]" rows="2" placeholder="Isi butir kesepakatan...">${$('<div>').text(ptText || '').html()}</textarea>
                        <button type="button" class="btn btn-outline-danger btn-sm btn-delete-point" title="Hapus Butir" style="margin-top: 2px;">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                `);
                $pointsList.append($ptItem);
            }

            function updateAllIndices() {
                $('.pasal-card').each(function(pIdx) {
                    var pNum = pIdx + 1;
                    var $card = $(this);
                    $card.attr('data-pasal-index', pIdx);
                    $card.find('.pasal-badge').text('Pasal ' + pNum + '.');
                    $card.find('.pasal-number-input').attr('name', `pasals[${pIdx}][number]`).val(pNum);
                    $card.find('.pasal-subtitle-input').attr('name', `pasals[${pIdx}][subtitle]`);
                    $card.find('.pasal-content-input').attr('name', `pasals[${pIdx}][content]`);

                    var $points = $card.find('.point-item');
                    $points.each(function(ptIdx) {
                        var ptNum = ptIdx + 1;
                        var $pt = $(this);
                        $pt.attr('data-point-index', ptIdx);
                        $pt.find('.point-badge').text(`[${ptNum}]`);
                        $pt.find('.point-text').attr('name', `pasals[${pIdx}][points][${ptIdx}]`);
                    });
                    $card.find('.next-point-num').text($points.length + 1);
                });
            }

            function syncPasalsFromDOM() {
                var list = [];
                $('.pasal-card').each(function(pIdx) {
                    var pNum = pIdx + 1;
                    var sub = $(this).find('.pasal-subtitle-input').val() || '';
                    var cont = $(this).find('.pasal-content-input').val() || '';
                    var pts = [];
                    $(this).find('.point-text').each(function() {
                        pts.push($(this).val() || '');
                    });
                    list.push({
                        number: pNum,
                        subtitle: sub,
                        content: cont,
                        points: pts
                    });
                });
                return list;
            }

            // Inisialisasi render
            renderPasals();

            // Event: Tambah Pasal
            $('#btnAddPasal').on('click', function() {
                initialPasals = syncPasalsFromDOM();
                var nextNum = initialPasals.length + 1;
                initialPasals.push({
                    number: nextNum,
                    subtitle: '',
                    content: '',
                    points: []
                });
                renderPasals();
            });

            // Event: Hapus Pasal
            $(document).on('click', '.btn-delete-pasal', function() {
                initialPasals = syncPasalsFromDOM();
                var $card = $(this).closest('.pasal-card');
                var pIdx = parseInt($card.attr('data-pasal-index'), 10);
                if (pIdx > 0) {
                    initialPasals.splice(pIdx, 1);
                    renderPasals();
                }
            });

            // Event: Tambah Poin
            $(document).on('click', '.btn-add-point', function() {
                var $card = $(this).closest('.pasal-card');
                var pIdx = parseInt($card.attr('data-pasal-index'), 10);
                var $pointsList = $card.find('.points-list');
                var ptIdx = $pointsList.find('.point-item').length;
                renderPointItem($pointsList, pIdx, ptIdx, '');
                updateAllIndices();
            });

            // Event: Hapus Poin
            $(document).on('click', '.btn-delete-point', function() {
                var $pt = $(this).closest('.point-item');
                $pt.remove();
                updateAllIndices();
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

        // Helper scrollToFirstError (Akurat scroll ke error paling atas)
        function scrollToFirstError() {
            var $form = $('#suratKesepakatanDiversiForm');
            
            // Ambil semua elemen error dalam form berdasarkan urutan DOM dokumen
            var $allErrors = $form.find('.is-invalid, .select2-selection.border-danger, .frontend-error').filter(function() {
                var $t = $(this);
                // Abaikan template pasal / poin yang tersembunyi
                if ($t.closest('#pasalTemplate, #pointTemplate, template').length) return false;
                return true;
            });

            if (!$allErrors.length) {
                $allErrors = $('.is-invalid, .select2-selection.border-danger, .frontend-error').filter(function() {
                    return $(this).closest('#pasalTemplate, #pointTemplate, template').length === 0;
                });
            }

            if (!$allErrors.length) {
                return;
            }

            // Elemen pertama dalam urutan DOM dokumen selalu merupakan elemen paling atas di halaman
            var $firstError = $allErrors.first();

            // Tentukan target elemen visible yang akan di-scroll
            var $visibleTarget = $firstError;
            if ($firstError.is('select') && $firstError.next('.select2-container').length) {
                $visibleTarget = $firstError.next('.select2-container');
            } else if ($firstError.hasClass('select2-selection')) {
                $visibleTarget = $firstError.closest('.select2-container');
            } else if (!$firstError.is(':visible')) {
                var $visParent = $firstError.closest('.input-group, .row, .col-lg-10, .col-md-10, div:visible');
                if ($visParent.length) {
                    $visibleTarget = $visParent;
                }
            }

            var domEl = $visibleTarget[0];

            // 1. Native scrollIntoView jika didukung browser
            if (domEl && typeof domEl.scrollIntoView === 'function') {
                try {
                    domEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } catch (e) {
                    domEl.scrollIntoView(true);
                }
            }

            // 2. Animate scroll presisi pada container .content (karena style3x.css menggunakan .content overflow: auto)
            var $content = $('.content');
            if ($content.length && $visibleTarget.length && $visibleTarget.offset()) {
                var currentScroll = $content.scrollTop();
                var contentOffsetTop = $content.offset().top;
                var targetOffsetTop = $visibleTarget.offset().top;
                
                // Rumus posisi scroll presisi:
                // targetScroll = posisi scroll saat ini + jarak vertikal dari top container ke elemen - padding atas (120px)
                var targetScroll = currentScroll + (targetOffsetTop - contentOffsetTop) - 120;
                if (targetScroll < 0) targetScroll = 0;

                $content.stop().animate({
                    scrollTop: targetScroll
                }, 350);
            }

            // 3. Fallback animasi window / html / body
            if ($visibleTarget.length && $visibleTarget.offset()) {
                $('html, body, .main-content').stop().animate({
                    scrollTop: Math.max(0, $visibleTarget.offset().top - 120)
                }, 350);
            }

            // 4. Fokuskan kursor ke input yang salah
            setTimeout(function() {
                if ($firstError.is('input:not([type="hidden"]), textarea') && $firstError.is(':visible')) {
                    try { $firstError.trigger('focus'); } catch (err) {}
                } else if ($firstError.is('select') || $visibleTarget.hasClass('select2-container')) {
                    $visibleTarget.find('.select2-selection').trigger('focus');
                }
            }, 300);
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

                    if ($field.parent().hasClass('input-group') && !$field.parent().hasClass('row')) {
                        $target = $field.parent();
                    }

                    $target.parent().addClass('flex-wrap');
                    $target.siblings('.frontend-error, .invalid-feedback').remove();
                    $target.next('.frontend-error, .invalid-feedback').remove();
                    $target.after('<div class="invalid-feedback d-block w-100 frontend-error font-weight-bold mt-1 text-danger">' + message + '</div>');
                    errors.push(message);
                }

                function checkInput(fieldSelector, label) {
                    var $field = $(fieldSelector);
                    if (!$field.length || $field.is(':disabled') || !$field.is(':visible')) return;
                    var raw = $field.val();
                    var val = (raw !== null && raw !== undefined) ? String(raw).trim() : '';
                    if (!val || val === '') {
                        markError(fieldSelector, label + ' harus diisi');
                    }
                }

                function checkSelect(fieldSelector, label) {
                    var $field = $(fieldSelector);
                    if (!$field.length || $field.is(':disabled')) return;
                    var isVis = $field.is(':visible') || ($field.next('.select2-container').length && $field.next('.select2-container').is(':visible'));
                    if (!isVis) return;

                    var raw = $field.val();
                    var hasVal = Array.isArray(raw) ? raw.length > 0 : (raw && String(raw).trim() !== '' && String(raw).trim() !== '0');
                    if (!hasVal) {
                        markError(fieldSelector, label + ' harus dipilih');
                    }
                }

                // =============================================================
                // URUTAN VALIDASI SESUAI TAMPILAN FORM DARI ATAS KE BAWAH:
                // =============================================================

                // 1. Identitas Pihak I (Anak yang Berkonflik dengan Hukum)
                checkSelect('#suspectId', 'Tersangka Anak');
                checkInput('#childName', 'Nama Anak');
                checkSelect('#childIdentityType', 'Jenis Identitas Anak');
                checkInput('#childIdentityNumber', 'Nomor Identitas Anak');
                if (typeof validateIdentityField === 'function') {
                    var childIdErr = validateIdentityField('#childIdentityType', '#childIdentityNumber', true);
                    if (childIdErr) errors.push(childIdErr);
                }

                checkSelect('#childNationality', 'Kewarganegaraan Anak');
                checkSelect('#childGender', 'Jenis Kelamin Anak');
                checkInput('#childBirthPlace', 'Tempat Lahir Anak');
                checkInput('#childBirthDate', 'Tanggal Lahir Anak');
                if (typeof validateBirthDateField === 'function') {
                    var childBirthErr = validateBirthDateField('#childBirthDate', true);
                    if (childBirthErr) errors.push(childBirthErr);
                }

                checkSelect('#childJob', 'Pekerjaan Anak');
                checkSelect('#childReligion', 'Agama Anak');
                checkInput('#childAddress', 'Alamat Anak');

                // Pendamping Anak
                checkSelect('#childGuardianFrom', 'Pendamping Anak Dari');
                if ($('#childGuardianFrom').val() === 'Pendamping dari ......') {
                    checkInput('#childGuardianFromDetail', 'Detail Instansi / Lembaga Pendamping Anak');
                }
                checkInput('#childGuardianName', 'Nama Pendamping Anak');
                checkSelect('#childGuardianRelation', 'Hubungan Keluarga Pendamping Anak');
                if ($('#childGuardianIdentity').val() && typeof validateIdentityField === 'function') {
                    var cgIdErr = validateIdentityField('#childGuardianIdentityType', '#childGuardianIdentity', false);
                    if (cgIdErr) errors.push(cgIdErr);
                }
                if ($('#childGuardianBirthDate').val() && typeof validateBirthDateField === 'function') {
                    var cgBirthErr = validateBirthDateField('#childGuardianBirthDate', false);
                    if (cgBirthErr) errors.push(cgBirthErr);
                }

                // 2. Identitas Pihak II (Korban & Pendamping Korban)
                checkInput('#victimName', 'Nama Korban');
                checkSelect('#victimIdentityType', 'Jenis Identitas Korban');
                checkInput('#victimIdentityNumber', 'Nomor Identitas Korban');
                if (typeof validateIdentityField === 'function') {
                    var victimIdErr = validateIdentityField('#victimIdentityType', '#victimIdentityNumber', true);
                    if (victimIdErr) errors.push(victimIdErr);
                }

                checkSelect('#victimNationality', 'Kewarganegaraan Korban');
                checkSelect('#victimGender', 'Jenis Kelamin Korban');
                checkInput('#victimBirthPlace', 'Tempat Lahir Korban');
                checkInput('#victimBirthDate', 'Tanggal Lahir Korban');
                if (typeof validateBirthDateField === 'function') {
                    var victimBirthErr = validateBirthDateField('#victimBirthDate', true);
                    if (victimBirthErr) errors.push(victimBirthErr);
                }

                checkSelect('#victimJob', 'Pekerjaan Korban');
                checkSelect('#victimReligion', 'Agama Korban');
                checkInput('#victimAddress', 'Alamat Korban');

                // Pendamping Korban (bila dicentang/didampingi)
                if ($('input[name="isVictimAccompanied"]:checked').val() === '1') {
                    checkInput('#victimGuardianName', 'Nama Pendamping Korban');
                    if ($('#victimGuardianIdentity').val() && typeof validateIdentityField === 'function') {
                        var vgIdErr = validateIdentityField('#victimGuardianIdentityType', '#victimGuardianIdentity', false);
                        if (vgIdErr) errors.push(vgIdErr);
                    }
                    if ($('#victimGuardianBirthDate').val() && typeof validateBirthDateField === 'function') {
                        var vgBirthErr = validateBirthDateField('#victimGuardianBirthDate', false);
                        if (vgBirthErr) errors.push(vgBirthErr);
                    }
                }

                // 3. Pelaksanaan Musyawarah Diversi & Fasilitator
                checkInput('#diversionDate', 'Tanggal Musyawarah');
                checkInput('#diversionRoom', 'Tempat Musyawarah');
                checkInput('#diversionStreet', 'Jalan Tempat Musyawarah');
                checkSelect('#facilitatorOfficer', 'Fasilitator Diversi');

                // 4. Isi Kesepakatan (Pasal 1)
                var p1Content = ($('textarea[name="pasals[0][content]"]').val() || '').trim();
                var hasP1Points = false;
                $('.pasal-card[data-pasal-index="0"]').find('.point-text').each(function() {
                    if (($(this).val() || '').trim() !== '') {
                        hasP1Points = true;
                    }
                });

                if (p1Content === '' && !hasP1Points) {
                    markError('textarea[name="pasals[0][content]"]', 'Pasal 1 harus diisi (narasi atau minimal satu butir kesepakatan)');
                }

                // 5. Saksi-Saksi Musyawarah Diversi
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
