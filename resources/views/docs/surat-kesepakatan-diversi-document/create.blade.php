@php
    $_title = 'Surat Kesepakatan Diversi';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('libs/bootstrap-duallistbox/bootstrap-duallistbox.css') }}" rel="stylesheet">
@endpush

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"><i
            class="bi bi-arrow-left"></i> Kembali ke Progres Perkara</a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Tambah Surat Kesepakatan Diversi</h5>

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

                {{-- Nomor Dokumen --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="documentNumber">Nomor Dokumen<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="documentNumber" type="text"
                            class="form-control @error('documentNumber') is-invalid @enderror font-weight-bold"
                            name="documentNumber" value="{{ old('documentNumber') }}" required
                            placeholder="Masukkan Nomor Dokumen">

                        @error('documentNumber')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Tanggal Ditandatangani Dokumen --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="documentDate">Tanggal Ditandatangani Dokumen<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input class="form-control" id="documentDate" name="documentDate"
                            placeholder="YYYY-MM-DD" autocomplete="off" value="{{ old('documentDate') }}"
                            data-provide="datepicker">

                        @error('documentDate')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- ─── WAKTU DAN TEMPAT PELAKSANAAN MUSYAWARAH DIVERSI ─── --}}
                <hr>
                <h6 class="fw-bold text-blue-dark mb-3">Waktu dan Tempat Pelaksanaan Musyawarah Diversi</h6>

                {{-- Tanggal & Hari Musyawarah --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="diversionDate">Waktu Musyawarah<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input class="form-control" id="diversionDate" name="diversionDate"
                            placeholder="Tanggal Musyawarah (YYYY-MM-DD)" autocomplete="off" value="{{ old('diversionDate') }}"
                            data-provide="datepicker">
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" id="diversionDay" name="diversionDay"
                            placeholder="Hari (Contoh: Senin)" value="{{ old('diversionDay') }}">
                    </div>
                </div>

                {{-- Ruang & Jalan/Tempat Musyawarah --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="diversionRoom">Tempat Musyawarah<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" id="diversionRoom" name="diversionRoom"
                            placeholder="Ruang (Contoh: Ruang Riksa / Ruang Diversi)" value="{{ old('diversionRoom') }}">
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" id="diversionStreet" name="diversionStreet"
                            placeholder="Jalan / Alamat Tempat Pelaksanaan" value="{{ old('diversionStreet') }}">
                    </div>
                </div>

                {{-- Fasilitator Diversi --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="facilitatorOfficer">Fasilitator Diversi<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2" name="facilitatorOfficer" id="facilitatorOfficer">
                            <option value="">--Pilih Fasilitator (Penyidik / Penyidik Pembantu)--</option>
                            @foreach ($authorizedSignatories as $data)
                                @php
                                    $positionName = ($data->position) ? $data->position->name : '-';
                                @endphp
                                <option value="{{ $data->id }}" data-register-number="{{ $data->register_number }}" {{ old('facilitatorOfficer') == $data->id ? 'selected' : '' }}>
                                    {{ $data->register_number . ' - ' . $data->full_name . ' | ' . $positionName }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">(*Penyidik / Penyidik Pembantu yang bertindak sebagai Fasilitator Diversi)</small>

                        @error('facilitatorOfficer')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- ─── IDENTITAS PIHAK I (ANAK & PENDAMPING) ─── --}}
                <hr>
                <h6 class="fw-bold text-blue-dark mb-3">Identitas Pihak I (Anak yang Berkonflik dengan Hukum)</h6>

                {{-- Pilih Tersangka Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="suspectId">Tersangka Anak<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2" name="suspectId" id="suspectId">
                            <option value="">--Pilih Tersangka Anak--</option>
                            @foreach ($suspects as $suspect)
                                @php
                                    $suspectAge = 'Umur tidak diketahui';
                                    if (!empty($suspect->birth_date)) {
                                        try {
                                            $birth = \Carbon\Carbon::parse($suspect->birth_date);
                                            $diff = $birth->diff(\Carbon\Carbon::now());
                                            $suspectAge = "{$diff->y} tahun {$diff->m} bulan {$diff->d} hari";
                                        } catch (\Exception $e) {
                                            $suspectAge = 'Umur tidak diketahui';
                                        }
                                    } elseif (!empty($suspect->age)) {
                                        $suspectAge = $suspect->age . ' tahun';
                                    }
                                @endphp
                                <option value="{{ $suspect->id }}"
                                    data-name="{{ $suspect->name }}"
                                    data-identity="{{ $suspect->identity_number }}"
                                    data-nationality="{{ $suspect->nationality ?? 'WNI' }}"
                                    data-gender="{{ $suspect->gender_id }}"
                                    data-birthplace="{{ $suspect->birth_place }}"
                                    data-birthdate="{{ $suspect->birth_date }}"
                                    data-age-year="{{ isset($diff) ? $diff->y : $suspect->age }}"
                                    data-age-month="{{ isset($diff) ? $diff->m : 0 }}"
                                    data-age-day="{{ isset($diff) ? $diff->d : 0 }}"
                                    data-job="{{ $suspect->job_id }}"
                                    data-religion="{{ $suspect->religion_id }}"
                                    data-address="{{ $suspect->address ?? ($suspect->properties['address'] ?? '') }}"
                                    {{ old('suspectId') == $suspect->id ? 'selected' : '' }}>
                                    {{ $suspect->name }} ({{ $suspectAge }})
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
                    <label class="fw-bold col-sm-2 col-form-label" for="childName">Nama Lengkap Anak<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="childName" type="text" class="form-control" name="childName" value="{{ old('childName') }}" required placeholder="Nama lengkap anak">
                    </div>
                </div>

                {{-- Nomor Identitas Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childIdentityNumber">Nomor Identitas<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="childIdentityNumber" type="text" class="form-control" name="childIdentityNumber" value="{{ old('childIdentityNumber') }}" placeholder="NIK / KIA / Paspor">
                    </div>
                </div>

                {{-- Kewarganegaraan Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childNationality">Kewarganegaraan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="childNationality" type="text" class="form-control" name="childNationality" value="{{ old('childNationality', 'WNI') }}">
                    </div>
                </div>

                {{-- Jenis Kelamin Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGender">Jenis Kelamin<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2" name="childGender" id="childGender">
                            <option value="">--Pilih Jenis Kelamin--</option>
                            @foreach ($genders as $gender)
                                <option value="{{ $gender->id }}" {{ old('childGender') == $gender->id ? 'selected' : '' }}>{{ $gender->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Tempat / Tanggal Lahir Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childBirthPlace">Tempat & Tanggal Lahir<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input id="childBirthPlace" type="text" class="form-control" name="childBirthPlace" value="{{ old('childBirthPlace') }}" placeholder="Tempat Lahir">
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input id="childBirthDate" type="text" class="form-control calculate-age" data-target-prefix="childAge" name="childBirthDate" value="{{ old('childBirthDate') }}" placeholder="YYYY-MM-DD" data-provide="datepicker" autocomplete="off">
                    </div>
                </div>

                {{-- Umur Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Umur Anak<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <div class="row">
                            <div class="col-4">
                                <div class="input-group">
                                    <input type="number" min="0" max="100" class="form-control" id="childAgeYear" name="childAgeYear" value="{{ old('childAgeYear') }}" placeholder="0">
                                    <div class="input-group-append"><span class="input-group-text">Tahun</span></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="input-group">
                                    <input type="number" min="0" max="11" class="form-control" id="childAgeMonth" name="childAgeMonth" value="{{ old('childAgeMonth', 0) }}" placeholder="0">
                                    <div class="input-group-append"><span class="input-group-text">Bulan</span></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="input-group">
                                    <input type="number" min="0" max="31" class="form-control" id="childAgeDay" name="childAgeDay" value="{{ old('childAgeDay', 0) }}" placeholder="0">
                                    <div class="input-group-append"><span class="input-group-text">Hari</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pekerjaan Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childJob">Pekerjaan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2" name="childJob" id="childJob">
                            <option value="">--Pilih Pekerjaan--</option>
                            @foreach ($jobs as $job)
                                <option value="{{ $job->id }}" {{ old('childJob') == $job->id ? 'selected' : (str_contains(strtolower($job->name), 'pelajar') ? 'selected' : '') }}>{{ $job->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Agama Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childReligion">Agama<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
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
                    <label class="fw-bold col-sm-2 col-form-label" for="childAddress">Alamat Lengkap<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <textarea id="childAddress" class="form-control" name="childAddress" rows="2" placeholder="Alamat domisili anak">{{ old('childAddress') }}</textarea>
                    </div>
                </div>

                {{-- Pendamping Anak Dari --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianFrom">Pendamping Anak Dari<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="childGuardianFrom" type="text" class="form-control" name="childGuardianFrom" value="{{ old('childGuardianFrom', 'Orang Tua') }}" placeholder="Contoh: Orang Tua / Wali / Pendamping BAPAS">
                    </div>
                </div>

                {{-- Hubungan Keluarga Pendamping Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianRelation">Hubungan Keluarga<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="childGuardianRelation" type="text" class="form-control" name="childGuardianRelation" value="{{ old('childGuardianRelation', 'Ayah Kandung') }}" placeholder="Contoh: Ayah Kandung / Ibu Kandung / Wali">
                    </div>
                </div>

                {{-- Nama Pendamping Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianName">Nama Pendamping Anak<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="childGuardianName" type="text" class="form-control" name="childGuardianName" value="{{ old('childGuardianName') }}" placeholder="Nama lengkap pendamping anak">
                    </div>
                </div>

                {{-- Nomor Identitas Pendamping Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianIdentity">Nomor Identitas Pendamping<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="childGuardianIdentity" type="text" class="form-control" name="childGuardianIdentity" value="{{ old('childGuardianIdentity') }}" placeholder="NIK KTP pendamping anak">
                    </div>
                </div>

                {{-- Kewarganegaraan Pendamping Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianNationality">Kewarganegaraan Pendamping<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="childGuardianNationality" type="text" class="form-control" name="childGuardianNationality" value="{{ old('childGuardianNationality', 'WNI') }}" placeholder="WNI / WNA">
                    </div>
                </div>

                {{-- Jenis Kelamin Pendamping Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianGender">Jenis Kelamin Pendamping<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2" name="childGuardianGender" id="childGuardianGender">
                            <option value="">--Pilih Jenis Kelamin--</option>
                            @foreach ($genders as $gender)
                                <option value="{{ $gender->id }}" {{ old('childGuardianGender') == $gender->id ? 'selected' : '' }}>{{ $gender->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Tempat & Tanggal Lahir Pendamping Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianBirthPlace">Tempat & Tanggal Lahir<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input id="childGuardianBirthPlace" type="text" class="form-control" name="childGuardianBirthPlace" value="{{ old('childGuardianBirthPlace') }}" placeholder="Tempat Lahir Pendamping">
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input id="childGuardianBirthDate" type="text" class="form-control" name="childGuardianBirthDate" value="{{ old('childGuardianBirthDate') }}" placeholder="YYYY-MM-DD" data-provide="datepicker" autocomplete="off">
                    </div>
                </div>

                {{-- Pekerjaan Pendamping Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianJob">Pekerjaan Pendamping<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
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
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianReligion">Agama Pendamping<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
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
                    <label class="fw-bold col-sm-2 col-form-label" for="childGuardianAddress">Alamat Pendamping Anak<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <textarea id="childGuardianAddress" class="form-control" name="childGuardianAddress" rows="2" placeholder="Alamat domisili pendamping anak">{{ old('childGuardianAddress') }}</textarea>
                    </div>
                </div>

                {{-- ─── IDENTITAS PIHAK II (KORBAN & PENDAMPING) ─── --}}
                <hr>
                <h6 class="fw-bold text-blue-dark mb-3">Identitas Pihak II (Korban)</h6>

                {{-- Pilih Korban --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="victimId">Pilih Korban</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2" name="victimId" id="victimId">
                            <option value="">--Pilih Korban (Bila Ada dalam Perkara)--</option>
                            @foreach ($involvedPeoples as $person)
                                @php
                                    $personAge = 'Umur tidak diketahui';
                                    if (!empty($person->birth_date)) {
                                        try {
                                            $pBirth = \Carbon\Carbon::parse($person->birth_date);
                                            $pDiff = $pBirth->diff(\Carbon\Carbon::now());
                                            $personAge = "{$pDiff->y} tahun {$pDiff->m} bulan {$pDiff->d} hari";
                                        } catch (\Exception $e) {
                                            $personAge = 'Umur tidak diketahui';
                                        }
                                    } elseif (!empty($person->age)) {
                                        $personAge = $person->age . ' tahun';
                                    }
                                @endphp
                                <option value="{{ $person->id }}"
                                    data-name="{{ $person->name }}"
                                    data-identity="{{ $person->identity_number }}"
                                    data-nationality="{{ $person->nationality ?? 'WNI' }}"
                                    data-gender="{{ $person->gender_id }}"
                                    data-birthplace="{{ $person->birth_place }}"
                                    data-birthdate="{{ $person->birth_date ? \Carbon\Carbon::parse($person->birth_date)->format('Y-m-d') : '' }}"
                                    data-age-year="{{ isset($pDiff) ? $pDiff->y : $person->age }}"
                                    data-age-month="{{ isset($pDiff) ? $pDiff->m : 0 }}"
                                    data-age-day="{{ isset($pDiff) ? $pDiff->d : 0 }}"
                                    data-job="{{ $person->job_id }}"
                                    data-religion="{{ $person->religion_id }}"
                                    data-address="{{ $person->address }}"
                                    {{ old('victimId') == $person->id ? 'selected' : '' }}>
                                    {{ $person->name }} ({{ $person->status ?? 'Korban' }}) - {{ $personAge }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih korban dari pihak yang terlibat dalam perkara untuk mengisi otomatis, atau isi manual di bawah.</small>
                    </div>
                </div>

                {{-- Nama Korban --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="victimName">Nama Lengkap Korban<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="victimName" type="text" class="form-control" name="victimName" value="{{ old('victimName') }}" required placeholder="Nama lengkap korban">
                    </div>
                </div>

                {{-- Nomor Identitas Korban --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="victimIdentityNumber">Nomor Identitas Korban<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="victimIdentityNumber" type="text" class="form-control" name="victimIdentityNumber" value="{{ old('victimIdentityNumber') }}" placeholder="NIK KTP / Paspor korban">
                    </div>
                </div>

                {{-- Kewarganegaraan Korban --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="victimNationality">Kewarganegaraan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="victimNationality" type="text" class="form-control" name="victimNationality" value="{{ old('victimNationality', 'WNI') }}">
                    </div>
                </div>

                {{-- Jenis Kelamin Korban --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="victimGender">Jenis Kelamin<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2" name="victimGender" id="victimGender">
                            <option value="">--Pilih Jenis Kelamin--</option>
                            @foreach ($genders as $gender)
                                <option value="{{ $gender->id }}" {{ old('victimGender') == $gender->id ? 'selected' : '' }}>{{ $gender->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Tempat & Tanggal Lahir Korban --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="victimBirthPlace">Tempat & Tanggal Lahir<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input id="victimBirthPlace" type="text" class="form-control" name="victimBirthPlace" value="{{ old('victimBirthPlace') }}" placeholder="Tempat Lahir">
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input id="victimBirthDate" type="text" class="form-control calculate-age" data-target-prefix="victimAge" name="victimBirthDate" value="{{ old('victimBirthDate') }}" placeholder="YYYY-MM-DD" data-provide="datepicker" autocomplete="off">
                    </div>
                </div>

                {{-- Umur Korban --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Umur Korban<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <div class="row">
                            <div class="col-4">
                                <div class="input-group">
                                    <input type="number" min="0" max="100" class="form-control" id="victimAgeYear" name="victimAgeYear" value="{{ old('victimAgeYear') }}" placeholder="0">
                                    <div class="input-group-append"><span class="input-group-text">Tahun</span></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="input-group">
                                    <input type="number" min="0" max="11" class="form-control" id="victimAgeMonth" name="victimAgeMonth" value="{{ old('victimAgeMonth', 0) }}" placeholder="0">
                                    <div class="input-group-append"><span class="input-group-text">Bulan</span></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="input-group">
                                    <input type="number" min="0" max="31" class="form-control" id="victimAgeDay" name="victimAgeDay" value="{{ old('victimAgeDay', 0) }}" placeholder="0">
                                    <div class="input-group-append"><span class="input-group-text">Hari</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pekerjaan Korban --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="victimJob">Pekerjaan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
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
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
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
                    <label class="fw-bold col-sm-2 col-form-label" for="victimAddress">Alamat Korban<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <textarea id="victimAddress" class="form-control" name="victimAddress" rows="2" placeholder="Alamat domisili korban">{{ old('victimAddress') }}</textarea>
                    </div>
                </div>

                {{-- Checkbox Pendamping Korban --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Pendamping Korban</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="isVictimAccompanied" name="isVictimAccompanied" value="1" {{ old('isVictimAccompanied') ? 'checked' : '' }}>
                            <label class="form-check-label" for="isVictimAccompanied">
                                Korban didampingi oleh orang tua/wali/pendamping (*korban dewasa tidak wajib didampingi)
                            </label>
                        </div>
                    </div>
                </div>

                <div id="victimGuardianContainer" style="display: {{ old('isVictimAccompanied') ? 'block' : 'none' }};">
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianFrom">Pendamping Dari</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                            <input id="victimGuardianFrom" type="text" class="form-control" name="victimGuardianFrom" value="{{ old('victimGuardianFrom', 'Orang Tua') }}" placeholder="Contoh: Orang Tua / Wali / Pendamping">
                        </div>
                    </div>

                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianRelation">Hubungan Keluarga</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                            <input id="victimGuardianRelation" type="text" class="form-control" name="victimGuardianRelation" value="{{ old('victimGuardianRelation') }}" placeholder="Contoh: Ibu Kandung / Ayah Kandung">
                        </div>
                    </div>

                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianName">Nama Pendamping Korban</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                            <input id="victimGuardianName" type="text" class="form-control" name="victimGuardianName" value="{{ old('victimGuardianName') }}" placeholder="Nama lengkap pendamping korban">
                        </div>
                    </div>

                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianIdentity">Nomor Identitas Pendamping</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                            <input id="victimGuardianIdentity" type="text" class="form-control" name="victimGuardianIdentity" value="{{ old('victimGuardianIdentity') }}" placeholder="NIK KTP pendamping korban">
                        </div>
                    </div>

                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianNationality">Kewarganegaraan Pendamping</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                            <input id="victimGuardianNationality" type="text" class="form-control" name="victimGuardianNationality" value="{{ old('victimGuardianNationality', 'WNI') }}" placeholder="WNI / WNA">
                        </div>
                    </div>

                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianGender">Jenis Kelamin Pendamping</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                            <select class="form-control select2" name="victimGuardianGender" id="victimGuardianGender">
                                <option value="">--Pilih Jenis Kelamin--</option>
                                @foreach ($genders as $gender)
                                    <option value="{{ $gender->id }}" {{ old('victimGuardianGender') == $gender->id ? 'selected' : '' }}>{{ $gender->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianBirthPlace">Tempat & Tanggal Lahir</label>
                        <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                            <input id="victimGuardianBirthPlace" type="text" class="form-control" name="victimGuardianBirthPlace" value="{{ old('victimGuardianBirthPlace') }}" placeholder="Tempat Lahir Pendamping">
                        </div>
                        <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                            <input id="victimGuardianBirthDate" type="text" class="form-control" name="victimGuardianBirthDate" value="{{ old('victimGuardianBirthDate') }}" placeholder="YYYY-MM-DD" data-provide="datepicker" autocomplete="off">
                        </div>
                    </div>

                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianJob">Pekerjaan Pendamping</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                            <select class="form-control select2" name="victimGuardianJob" id="victimGuardianJob">
                                <option value="">--Pilih Pekerjaan--</option>
                                @foreach ($jobs as $job)
                                    <option value="{{ $job->id }}" {{ old('victimGuardianJob') == $job->id ? 'selected' : '' }}>{{ $job->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianReligion">Agama Pendamping</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                            <select class="form-control select2" name="victimGuardianReligion" id="victimGuardianReligion">
                                <option value="">--Pilih Agama--</option>
                                @foreach ($religions as $religion)
                                    <option value="{{ $religion->id }}" {{ old('victimGuardianReligion') == $religion->id ? 'selected' : '' }}>{{ $religion->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="victimGuardianAddress">Alamat Pendamping Korban</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                            <textarea id="victimGuardianAddress" class="form-control" name="victimGuardianAddress" rows="2" placeholder="Alamat domisili pendamping korban">{{ old('victimGuardianAddress') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- ─── PASAL-PASAL KESEPAKATAN DIVERSI ─── --}}
                <hr>
                <h6 class="fw-bold text-blue-dark mb-3">Isi Kesepakatan Diversi (Pasal-Pasal S-44.3)</h6>

                {{-- Pasal 1: Bentuk Kesepakatan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Pasal 1 (Bentuk Diversi)<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <small class="text-muted d-block mb-2">Pilih satu atau lebih bentuk kesepakatan diversi yang disepakati para pihak:</small>

                        {{-- Opsi 1: Ganti Kerugian --}}
                        <div class="form-check mb-2">
                            <input class="form-check-input deal-toggle" type="checkbox" id="dealCompensationCheck" name="dealCompensationCheck" value="1" {{ old('dealCompensationCheck') ? 'checked' : '' }} data-target="#boxCompensation">
                            <label class="form-check-label fw-bold" for="dealCompensationCheck">
                                [1] Pihak keluarga Anak memberikan ganti kerugian berupa uang
                            </label>
                        </div>
                        <div id="boxCompensation" class="mb-3 ps-4" style="display: {{ old('dealCompensationCheck') ? 'block' : 'none' }};">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <input type="text" class="form-control form-control-sm" name="compensationAmount" value="{{ old('compensationAmount') }}" placeholder="Besaran uang (Contoh: 5.000.000)">
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control form-control-sm" name="compensationPeriod" value="{{ old('compensationPeriod') }}" placeholder="Jangka waktu pembayaran (Contoh: 1 bulan)">
                                </div>
                            </div>
                            <textarea class="form-control form-control-sm" name="compensationConsideration" rows="2" placeholder="Pertimbangan">{{ old('compensationConsideration') }}</textarea>
                        </div>

                        {{-- Opsi 2: Rehabilitasi Sosial --}}
                        <div class="form-check mb-2">
                            <input class="form-check-input deal-toggle" type="checkbox" id="dealRehabCheck" name="dealRehabCheck" value="1" {{ old('dealRehabCheck') ? 'checked' : '' }} data-target="#boxRehab">
                            <label class="form-check-label fw-bold" for="dealRehabCheck">
                                [2] Terhadap Anak diberikan Rehabilitasi Sosial dan Psikososial
                            </label>
                        </div>
                        <div id="boxRehab" class="mb-3 ps-4" style="display: {{ old('dealRehabCheck') ? 'block' : 'none' }};">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <input type="text" class="form-control form-control-sm" name="rehabOrganizer" value="{{ old('rehabOrganizer') }}" placeholder="Pelaksana rehabilitasi (Contoh: Dinas Sosial / Balai Rehabilitasi)">
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control form-control-sm" name="rehabPeriod" value="{{ old('rehabPeriod') }}" placeholder="Lama pelaksanaan (Contoh: 3 bulan)">
                                </div>
                            </div>
                            <textarea class="form-control form-control-sm" name="rehabConsideration" rows="2" placeholder="Pertimbangan">{{ old('rehabConsideration') }}</textarea>
                        </div>

                        {{-- Opsi 3: Pengembalian ke Orang Tua --}}
                        <div class="form-check mb-2">
                            <input class="form-check-input deal-toggle" type="checkbox" id="dealBapasCheck" name="dealBapasCheck" value="1" {{ old('dealBapasCheck') ? 'checked' : '' }} data-target="#boxBapas">
                            <label class="form-check-label fw-bold" for="dealBapasCheck">
                                [3] Anak dikembalikan ke orang tua dengan pengawasan BAPAS
                            </label>
                        </div>
                        <div id="boxBapas" class="mb-3 ps-4" style="display: {{ old('dealBapasCheck') ? 'block' : 'none' }};">
                            <input type="text" class="form-control form-control-sm mb-2" name="bapasSupervisionName" value="{{ old('bapasSupervisionName') }}" placeholder="Nama BAPAS pengawas (Contoh: BAPAS Jakarta Selatan)">
                            <textarea class="form-control form-control-sm" name="parentSupervisionConsideration" rows="2" placeholder="Pertimbangan">{{ old('parentSupervisionConsideration') }}</textarea>
                        </div>

                        {{-- Opsi 4: Pelayanan Masyarakat --}}
                        <div class="form-check mb-2">
                            <input class="form-check-input deal-toggle" type="checkbox" id="dealCommunityCheck" name="dealCommunityCheck" value="1" {{ old('dealCommunityCheck') ? 'checked' : '' }} data-target="#boxCommunity">
                            <label class="form-check-label fw-bold" for="dealCommunityCheck">
                                [4] Anak melakukan Pelayanan Masyarakat di Yayasan/Lembaga Pendidikan
                            </label>
                        </div>
                        <div id="boxCommunity" class="mb-3 ps-4" style="display: {{ old('dealCommunityCheck') ? 'block' : 'none' }};">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <input type="text" class="form-control form-control-sm" name="communityServiceLocation" value="{{ old('communityServiceLocation') }}" placeholder="Yayasan / Lembaga Pendidikan">
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control form-control-sm" name="communityServicePeriod" value="{{ old('communityServicePeriod') }}" placeholder="Lama pelayanan (Contoh: 14 hari)">
                                </div>
                            </div>
                            <textarea class="form-control form-control-sm" name="communityServiceConsideration" rows="2" placeholder="Pertimbangan">{{ old('communityServiceConsideration') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Pasal 2: Pemaafan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="pasal2Content">Pasal 2 (Pemaafan)<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <textarea class="form-control" id="pasal2Content" name="pasal2Content" rows="2" required placeholder="Contoh: Anak Korban [Nama Korban] dan kedua orang tuanya memaafkan perbuatan Anak [Nama Anak]">{{ old('pasal2Content') }}</textarea>
                        <small class="text-muted">Pernyataan pemaafan korban dan kedua orang tuanya kepada anak.</small>
                    </div>
                </div>

                {{-- ─── PASAL TAMBAHAN (PASAL 3, 4, DAN SETERUSNYA) ─── --}}
                <div class="row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Pasal Tambahan</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <div id="additionalPasalContainer">
                            {{-- Baris Pasal 3 (default) --}}
                            <div class="pasal-item mb-2" data-pasal="3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-secondary px-2 py-2 pasal-badge" style="min-width: 65px; font-size: 0.85rem;">Pasal 3.</span>
                                    <textarea class="form-control form-control-sm pasal-text" name="additionalPasals[0][content]" id="pasal3Content" rows="2" placeholder="Klausul / ketentuan tambahan (opsional)">{{ old('additionalPasals.0.content', old('pasal3Content')) }}</textarea>
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
                <h6 class="fw-bold text-blue-dark mb-3">Saksi-Saksi</h6>

                {{-- Saksi PK BAPAS --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Saksi BAPAS<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" name="bapasOfficerName" value="{{ old('bapasOfficerName') }}" placeholder="Nama Petugas PK BAPAS" required>
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" name="bapasOfficerRankNip" value="{{ old('bapasOfficerRankNip') }}" placeholder="Pangkat / NIP PK BAPAS" required>
                    </div>
                </div>

                {{-- Saksi PEKSOS --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Saksi Pekerja Sosial<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" name="socialWorkerName" value="{{ old('socialWorkerName') }}" placeholder="Nama Pekerja Sosial (PEKSOS)" required>
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" name="socialWorkerRankNip" value="{{ old('socialWorkerRankNip') }}" placeholder="Pangkat / NIP (atau -)" required>
                    </div>
                </div>

                {{-- Saksi Lainnya --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Saksi Lainnya<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input type="text" class="form-control" name="communityWitnessName" value="{{ old('communityWitnessName') }}" placeholder="Nama Penasihat Hukum Anak / Tokoh Masyarakat / Toga / Toma" required>
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
            $('#documentDate').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                orientation: 'auto bottom'
            });

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

            $('#diversionDate').on('change changeDate', function() {
                var dateVal = $(this).val();
                if (dateVal) {
                    var days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    var d = new Date(dateVal);
                    if (!isNaN(d.getTime())) {
                        $('#diversionDay').val(days[d.getDay()]);
                    }
                }
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
        });

        // Toggle Pendamping Korban
        $(document).ready(function() {
            $('#isVictimAccompanied').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#victimGuardianContainer').show();
                } else {
                    $('#victimGuardianContainer').hide();
                }
            });

            $('.deal-toggle').on('change', function() {
                var target = $(this).data('target');
                if ($(this).is(':checked')) {
                    $(target).show();
                } else {
                    $(target).hide();
                }
            });

            // Auto-fill dari dropdown Tersangka Anak
            $('#suspectId').on('change', function() {
                var $opt = $(this).find(':selected');
                if ($opt.val()) {
                    $('#childName').val($opt.data('name') || '').trigger('change');
                    $('#childIdentityNumber').val($opt.data('identity') || '');
                    $('#childNationality').val($opt.data('nationality') || 'WNI');
                    if ($opt.data('gender')) $('#childGender').val($opt.data('gender')).trigger('change');
                    $('#childBirthPlace').val($opt.data('birthplace') || '');
                    if ($opt.data('birthdate')) {
                        $('#childBirthDate').val($opt.data('birthdate')).trigger('change');
                    }
                    if ($opt.data('age-year') !== undefined) $('#childAgeYear').val($opt.data('age-year'));
                    if ($opt.data('age-month') !== undefined) $('#childAgeMonth').val($opt.data('age-month'));
                    if ($opt.data('age-day') !== undefined) $('#childAgeDay').val($opt.data('age-day'));
                    if ($opt.data('religion')) $('#childReligion').val($opt.data('religion')).trigger('change');
                    if ($opt.data('job')) $('#childJob').val($opt.data('job')).trigger('change');
                    $('#childAddress').val($opt.data('address') || '');
                }
            });

            // Auto-fill dari dropdown Korban
            $('#victimId').on('change', function() {
                var $opt = $(this).find(':selected');
                if ($opt.val()) {
                    $('#victimName').val($opt.data('name') || '').trigger('change');
                    $('#victimIdentityNumber').val($opt.data('identity') || '');
                    $('#victimNationality').val($opt.data('nationality') || 'WNI');
                    if ($opt.data('gender')) $('#victimGender').val($opt.data('gender')).trigger('change');
                    $('#victimBirthPlace').val($opt.data('birthplace') || '');
                    if ($opt.data('birthdate')) {
                        $('#victimBirthDate').val($opt.data('birthdate')).trigger('change');
                    }
                    if ($opt.data('age-year') !== undefined) $('#victimAgeYear').val($opt.data('age-year'));
                    if ($opt.data('age-month') !== undefined) $('#victimAgeMonth').val($opt.data('age-month'));
                    if ($opt.data('age-day') !== undefined) $('#victimAgeDay').val($opt.data('age-day'));
                    if ($opt.data('religion')) $('#victimReligion').val($opt.data('religion')).trigger('change');
                    if ($opt.data('job')) $('#victimJob').val($opt.data('job')).trigger('change');
                    $('#victimAddress').val($opt.data('address') || '');
                }
            });

            // Auto update teks Pasal 2
            function updatePasal2() {
                var childName = $('#childName').val() || '[Nama Anak]';
                var victimName = $('#victimName').val() || '[Nama Korban]';
                var defaultText = 'Anak Korban ' + victimName + ' dan kedua orang tuanya memaafkan perbuatan Anak ' + childName;
                if (!$('#pasal2Content').val() || $('#pasal2Content').data('is-auto') !== false) {
                    $('#pasal2Content').val(defaultText);
                }
            }

            $('#childName, #victimName').on('input change', function() {
                updatePasal2();
            });

            $('#pasal2Content').on('input', function() {
                $(this).data('is-auto', false);
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

                // Validasi semua field wajib
                checkInput('#documentNumber', 'Nomor Dokumen');
                checkInput('#documentDate', 'Tanggal Ditandatangani Dokumen');
                checkSelect('#facilitatorOfficer', 'Fasilitator Diversi');
                checkInput('#childName', 'Nama Anak');
                checkInput('#victimName', 'Nama Korban');
                checkInput('#pasal2Content', 'Pasal 2 (Pemaafan)');

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
                    $('#suratKesepakatanDiversiForm')[0].submit();
                });
            });
        });
    </script>
@endpush
