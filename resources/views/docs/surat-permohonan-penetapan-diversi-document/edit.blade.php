@php
    $_title = 'Edit Surat Permohonan Penetapan Diversi';
    $payload = $document->payload ?? [];
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
            <h5 class="fw-bold text-blue-dark">Edit Surat Permohonan Penetapan Diversi</h5>

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
            <form action="{{ route('doc.surat-permohonan-penetapan-diversi-document.update', ['id' => $document->id, 'accident_id' => $accidentId]) }}"
                method="POST" id="suratPermohonanPenetapanDiversiForm" novalidate>
                @csrf
                <input type="hidden" name="accidentId" id="accidentId" value="{{ $accidentId }}">

                {{-- ─── INFORMASI DASAR DOKUMEN ─── --}}
                <h6 class="fw-bold text-blue-dark mb-3">Informasi Dasar Dokumen</h6>

                {{-- Nomor LP --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="accidentNumber">Nomor LP</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="accidentNumber" type="text"
                            class="form-control font-weight-bold"
                            name="accidentNumber" value="{{ $accident->no_lp }}" readonly style="background-color: #e9ecef;">
                    </div>
                </div>

                {{-- Nomor Dokumen --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="documentNumber">Nomor Dokumen<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="documentNumber" type="text"
                            class="form-control @error('documentNumber') is-invalid @enderror font-weight-bold"
                            name="documentNumber" value="{{ old('documentNumber', $document->document_number) }}" required
                            placeholder="Nomor Dokumen">
                        @error('documentNumber')
                            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                {{-- Klasifikasi --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="documentClassification">Klasifikasi<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2 @error('documentClassification') is-invalid @enderror" name="documentClassification" id="documentClassification">
                            <option value="">--Pilih Klasifikasi Dokumen--</option>
                            @foreach ($documentClassifications as $documentClassification)
                                <option value="{{ $documentClassification->id }}"
                                    {{ old('documentClassification', $payload['classification_id'] ?? '') == $documentClassification->id ? 'selected' : '' }}>
                                    {{ $documentClassification->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('documentClassification')
                            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                {{-- Lampiran --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="appendix">Lampiran<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="appendix" type="text"
                            class="form-control @error('appendix') is-invalid @enderror"
                            name="appendix" value="{{ old('appendix', $payload['appendix'] ?? '') }}" required
                            placeholder="Lampiran">
                        @error('appendix')
                            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                {{-- Perihal --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="perihal">Perihal<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="perihal" type="text"
                            class="form-control @error('perihal') is-invalid @enderror"
                            name="perihal" value="{{ old('perihal', $payload['perihal'] ?? '') }}" required
                            placeholder="Perihal">
                        @error('perihal')
                            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                {{-- Pengadilan Penerima --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="courtId">Pengadilan Penerima<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2" name="courtId" id="courtId">
                            <option value="">--Pilih Pengadilan--</option>
                            @foreach ($courts as $court)
                                <option value="{{ $court->id }}" {{ old('courtId', $document->court_id ?? ($payload['court_id'] ?? '')) == $court->id ? 'selected' : '' }}>
                                    {{ $court->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('courtId')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>

                {{-- No SP Penyidikan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="suratPerintahPenyidikanDocument">No SP Penyidikan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2" name="suratPerintahPenyidikanDocument" id="suratPerintahPenyidikanDocument">
                            <option value="">--Pilih No Surat Perintah Penyidikan--</option>
                            @foreach ($suratPerintahPenyidikanDocuments as $spSidik)
                                <option value="{{ $spSidik->id }}"
                                    data-document-number="{{ $spSidik->document_number }}"
                                    data-document-date="{{ !empty($spSidik->document_date) ? date('Y-m-d', strtotime($spSidik->document_date)) : '' }}"
                                    {{ old('suratPerintahPenyidikanDocument', $document->surat_perintah_penyidikan_document_id ?? '') == $spSidik->id ? 'selected' : '' }}>
                                    {{ $spSidik->document_number }}
                                </option>
                            @endforeach
                        </select>
                        @error('suratPerintahPenyidikanDocument')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Tanggal Keputusan Bersama --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="agreementDate">Tanggal Keputusan Bersama<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input class="form-control" id="agreementDate" name="agreementDate"
                            placeholder="YYYY-MM-DD" autocomplete="off" value="{{ old('agreementDate', $payload['agreement_date'] ?? '') }}"
                            data-provide="datepicker">
                        @error('agreementDate')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>



                {{-- ─── IDENTITAS ANAK ─── --}}
                <hr>
                <h6 class="fw-bold text-blue-dark mb-3">Identitas Anak (Tersangka Anak)</h6>

                {{-- Pilih Tersangka Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="suspectId">Tersangka Anak<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2 @error('suspectId') is-invalid @enderror" name="suspectId" id="suspectId">
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
                                        } else {
                                            $suspectNatId = $suspect->nationality_id ?? '';
                                        }
                                    }

                                    $suspectFullAddress = ucwords(strtolower(
                                        ($suspect->address ?? '') .
                                        ($suspect->village ? ', ' . $suspect->village->name : '') .
                                        ($suspect->district ? ', ' . $suspect->district->name : '') .
                                        ($suspect->regency ? ', ' . $suspect->regency->name : '') .
                                        ($suspect->province ? ', ' . $suspect->province->name : '')
                                    ));
                                    $suspectFullAddress = trim($suspectFullAddress, " ,\t\n\r\0\x0B");
                                    if (empty($suspectFullAddress)) {
                                        $suspectFullAddress = $suspect->address ?? ($suspect->properties['address'] ?? '');
                                    }
                                @endphp
                                <option value="{{ $suspect->id }}"
                                    data-name="{{ $suspect->name }}"
                                    data-identity-type="{{ $suspect->identity_type_id }}"
                                    data-identity="{{ $suspect->identity_number }}"
                                    data-gender="{{ $suspect->gender_id }}"
                                    data-birthplace="{{ $suspect->birth_place }}"
                                    data-birthdate="{{ !empty($birthDateRaw) ? date('Y-m-d', strtotime($birthDateRaw)) : '' }}"
                                    data-age-year="{{ $suspectAgeYear }}"
                                    data-age-month="{{ $suspectAgeMonth }}"
                                    data-age-day="{{ $suspectAgeDay }}"
                                    data-nationality="{{ $suspectNatId }}"
                                    data-job="{{ $suspect->job_id }}"
                                    data-religion="{{ $suspect->religion_id }}"
                                    data-address="{{ $suspectFullAddress }}"
                                    {{ old('suspectId', $document->suspect_id ?? '') == $suspect->id ? 'selected' : '' }}>
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
                        <input id="childName" type="text" class="form-control" name="childName"
                            value="{{ old('childName', $payload['suspect_name'] ?? ($document->suspect->name ?? '')) }}" required placeholder="Nama Lengkap">
                    </div>
                </div>

                {{-- Jenis Identitas Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childIdentityType">Jenis Identitas<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" id="childIdentityType" name="childIdentityType">
                            <option value="">--Pilih Jenis Identitas--</option>
                            @foreach ($identityTypes as $identityType)
                                <option value="{{ $identityType->id }}" data-identity-type-name="{{ $identityType->name }}"
                                    {{ old('childIdentityType', $payload['suspect_identity_type_id'] ?? ($document->suspect->identity_type_id ?? '')) == $identityType->id ? 'selected' : '' }}>
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
                        <input id="childIdentityNumber" type="text" class="form-control" name="childIdentityNumber"
                            value="{{ old('childIdentityNumber', $payload['suspect_identity_number'] ?? ($document->suspect->identity_number ?? '')) }}"
                            placeholder="Nomor Identitas">
                    </div>
                </div>

                {{-- Kewarganegaraan Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childNationality">Kewarganegaraan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="childNationality" id="childNationality">
                            <option value="">--Pilih Kewarganegaraan--</option>
                            @foreach ($nationalities as $nationality)
                                <option value="{{ $nationality->id }}"
                                    {{ old('childNationality', $payload['suspect_nationality_id'] ?? ($document->suspect->nationality_id ?? '1')) == $nationality->id ? 'selected' : '' }}>
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
                                <option value="{{ $gender->id }}"
                                    {{ old('childGender', $payload['suspect_gender_id'] ?? ($document->suspect->gender_id ?? '')) == $gender->id ? 'selected' : '' }}>
                                    {{ $gender->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Tempat Lahir Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childBirthPlace">Tempat Lahir<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="childBirthPlace" type="text" class="form-control" name="childBirthPlace"
                            value="{{ old('childBirthPlace', $payload['suspect_birth_place'] ?? ($document->suspect->birth_place ?? '')) }}" placeholder="Tempat Lahir">
                    </div>
                </div>

                {{-- Tanggal Lahir Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childBirthDate">Tanggal Lahir<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input class="form-control" id="childBirthDate" name="childBirthDate"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('childBirthDate', !empty($payload['suspect_birth_date']) ? date('Y-m-d', strtotime($payload['suspect_birth_date'])) : (!empty($document->suspect->birth_date) ? date('Y-m-d', strtotime($document->suspect->birth_date)) : '')) }}"
                            data-provide="datepicker">
                    </div>
                </div>

                {{-- Umur Anak --}}
                @php
                    $initY = '';
                    $initM = '';
                    $initD = '';
                    $bDate = $payload['suspect_birth_date'] ?? ($document->suspect->birth_date ?? null);
                    if (!empty($bDate)) {
                        try {
                            $b = \Carbon\Carbon::parse($bDate);
                            $d = $b->diff(!empty($document->document_date) ? \Carbon\Carbon::parse($document->document_date) : \Carbon\Carbon::now());
                            $initY = $d->y;
                            $initM = $d->m;
                            $initD = $d->d;
                        } catch (\Exception $e) {}
                    }
                @endphp
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Umur<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <div class="row">
                            <div class="col-4">
                                <div class="input-group">
                                    <input type="number" min="0" max="100" class="form-control" id="childAgeYear" name="childAgeYear" value="{{ old('childAgeYear', $initY) }}" placeholder="0" readonly style="background-color: #e9ecef;">
                                    <div class="input-group-append"><span class="input-group-text">Tahun</span></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="input-group">
                                    <input type="number" min="0" max="11" class="form-control" id="childAgeMonth" name="childAgeMonth" value="{{ old('childAgeMonth', $initM) }}" placeholder="0" readonly style="background-color: #e9ecef;">
                                    <div class="input-group-append"><span class="input-group-text">Bulan</span></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="input-group">
                                    <input type="number" min="0" max="31" class="form-control" id="childAgeDay" name="childAgeDay" value="{{ old('childAgeDay', $initD) }}" placeholder="0" readonly style="background-color: #e9ecef;">
                                    <div class="input-group-append"><span class="input-group-text">Hari</span></div>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">*Umur dihitung otomatis dari tanggal lahir.</small>
                    </div>
                </div>

                {{-- Pekerjaan Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childJob">Pekerjaan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="childJob" id="childJob">
                            <option value="">--Pilih Pekerjaan--</option>
                            @foreach ($jobs as $job)
                                <option value="{{ $job->id }}"
                                    {{ old('childJob', $payload['suspect_job_id'] ?? ($document->suspect->job_id ?? '')) == $job->id ? 'selected' : '' }}>
                                    {{ $job->name }}
                                </option>
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
                                <option value="{{ $religion->id }}"
                                    {{ old('childReligion', $payload['suspect_religion_id'] ?? ($document->suspect->religion_id ?? '')) == $religion->id ? 'selected' : '' }}>
                                    {{ $religion->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Alamat Anak --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="childAddress">Alamat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <textarea id="childAddress" class="form-control" name="childAddress" rows="2" placeholder="Alamat">{{ old('childAddress', $payload['suspect_address'] ?? ($document->suspect->address ?? '')) }}</textarea>
                    </div>
                </div>

                {{-- ─── HASIL RAPAT KOORDINASI / KEPUTUSAN BERSAMA ─── --}}
                <hr>
                <h6 class="fw-bold text-blue-dark mb-3">Hasil Rapat Koordinasi / Keputusan Bersama</h6>

                {{-- Tanggal Rapat Koordinasi --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="meetingDate">Tanggal Rapat Koordinasi<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input class="form-control @error('meetingDate') is-invalid @enderror" id="meetingDate" name="meetingDate"
                            placeholder="YYYY-MM-DD" autocomplete="off" value="{{ old('meetingDate', !empty($payload['meeting_date']) ? date('Y-m-d', strtotime($payload['meeting_date'])) : date('Y-m-d')) }}"
                            data-provide="datepicker" required>
                        @error('meetingDate')
                            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                {{-- Hasil Kesepakatan Diversi --}}
                @php
                    $currResult = old('agreementResultType', $payload['agreement_result_type'] ?? 'ORANG_TUA');
                @endphp
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="agreementResultType">Hasil Keputusan Bersama<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <div class="d-flex flex-column gap-2 mt-1">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="agreementResultType" id="resultOrangTua" value="ORANG_TUA"
                                    {{ $currResult == 'ORANG_TUA' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="resultOrangTua">
                                    Dikembalikan kepada orang tua
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="agreementResultType" id="resultPembinaan" value="PEMBINAAN"
                                    {{ $currResult == 'PEMBINAAN' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="resultPembinaan">
                                    Dilakukan pembinaan di instansi / lembaga
                                </label>
                            </div>
                        </div>
                        @error('agreementResultType')
                            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                {{-- Detail Pembinaan (Conditional) --}}
                <div id="pembinaanContainer" style="{{ $currResult == 'PEMBINAAN' ? '' : 'display: none;' }}">
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="agreementInstitution">Nama Lembaga Pembinaan<span class="text-danger fs-5">*</span></label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            <input id="agreementInstitution" type="text"
                                class="form-control @error('agreementInstitution') is-invalid @enderror"
                                name="agreementInstitution" value="{{ old('agreementInstitution', $payload['agreement_institution'] ?? '') }}"
                                placeholder="Contoh: Balai Perlindungan Sosial Asuhan Anak (BPSAA) / LPKS">
                            @error('agreementInstitution')
                                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>

                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="agreementDurationMonths">Lama Pembinaan (Bulan)<span class="text-danger fs-5">*</span></label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            <div class="input-group">
                                <input id="agreementDurationMonths" type="number" min="1"
                                    class="form-control @error('agreementDurationMonths') is-invalid @enderror onlyIntegerInput"
                                    name="agreementDurationMonths" value="{{ old('agreementDurationMonths', $payload['agreement_duration_months'] ?? '') }}"
                                    placeholder="Contoh: 3">
                                <div class="input-group-append"><span class="input-group-text">Bulan</span></div>
                            </div>
                            @error('agreementDurationMonths')
                                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Penandatanganan Surat --}}
                <hr>
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="documentDate">Tanggal Ditandatangani Dokumen<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input class="form-control" id="documentDate" name="documentDate"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('documentDate', !empty($document->document_date) ? date('Y-m-d', strtotime($document->document_date)) : '') }}"
                            data-provide="datepicker">
                        @error('documentDate')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                @php
                    $signatoryOfficerId = $currentSignatory ? $currentSignatory->officer_id ?? null : null;
                    if (!$signatoryOfficerId && !empty($payload['signatory_id'])) {
                        $signatoryOfficerId = $payload['signatory_id'];
                    }
                @endphp
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="signatory">Penandatanganan Surat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2" name="signatory" id="signatory">
                            <option value="">--Pilih Yang Menandatangani--</option>
                            @foreach ($authorizedSignatories as $data)
                                @php
                                    $positionName = ($data->position) ? $data->position->name : '-';
                                    $selectedSignatory = old('signatory', $signatoryOfficerId) == $data->id 
                                        || ($currentSignatory && $currentSignatory->register_number == $data->register_number);
                                @endphp
                                <option value="{{ $data->id }}" data-register-number="{{ $data->register_number }}"
                                    {{ $selectedSignatory ? 'selected' : '' }}>
                                    {{ $data->register_number . ' - ' . $data->full_name . ' | ' . $positionName }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">(*Apabila daftar yang menandatangani kosong silahkan hubungi Helpdesk untuk mendapat bantuan)</small>

                        @error('signatory')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Tembusan --}}
                <hr>
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="carbonCopies">Tembusan</label>
                    <div class="col-lg-10 col-md-10 col-12">
                        <div id="carbonCopiesContainer">
                            @php
                                $existingCc = old('carbonCopies', $payload['carbon_copies'] ?? []);
                            @endphp
                            @if(!empty($existingCc))
                                @foreach ($existingCc as $carbonCopy)
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="carbonCopies[]" value="{{ $carbonCopy }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-danger removeCarbonCopiesButton" type="button">Hapus</button>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <button class="btn btn-primary mb-2 addCarbonCopiesButton" type="button">Tambah</button>

                        @error('carbonCopies')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>

                {{-- Tombol Aksi --}}
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-dark-blue me-2" id="suratPermohonanPenetapanDiversiFormSubmit">
                        <i class="bi bi-save"></i> {{ __('Simpan Perubahan') }}
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
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script type="text/javascript">
        // =========================================================================
        // HELPER VALIDASI & SANITASI IDENTITAS
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
            $field.nextAll('.frontend-error, .invalid-feedback').remove();
            $field.siblings('.frontend-error, .invalid-feedback').remove();
            $field.closest('.input-group, .col-lg-10, .col-md-10, .col-12, div').find('.frontend-error, .invalid-feedback').remove();

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
                $field.nextAll('.frontend-error, .invalid-feedback').remove();
                $field.siblings('.frontend-error, .invalid-feedback').remove();
                $field.closest('.input-group, .col-lg-10, .col-md-10, .col-12, div').find('.frontend-error, .invalid-feedback').remove();
                return null;
            }

            var val = ($field.val() || '').trim();
            var errorMsg = '';

            if (val === '') {
                if (isRequired) {
                    errorMsg = 'Tanggal lahir harus diisi.';
                } else {
                    $field.removeClass('is-invalid');
                    $field.nextAll('.frontend-error, .invalid-feedback').remove();
                    $field.siblings('.frontend-error, .invalid-feedback').remove();
                    $field.closest('.input-group, .col-lg-10, .col-md-10, .col-12, div').find('.frontend-error, .invalid-feedback').remove();
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
            $field.nextAll('.frontend-error, .invalid-feedback').remove();
            $field.siblings('.frontend-error, .invalid-feedback').remove();
            $field.closest('.input-group, .col-lg-10, .col-md-10, .col-12, div').find('.frontend-error, .invalid-feedback').remove();

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

        function hasFieldValue(target) {
            var $field = $(target);
            if (!$field || !$field.length) return false;
            var raw = $field.val();
            if (raw === null || raw === undefined) return false;
            if (Array.isArray(raw)) return raw.length > 0;
            var str = String(raw).trim();
            if (str === '' || str === 'null') return false;
            if ($field.is('select') && str === '0') return false;
            return true;
        }

        function clearFieldError(target) {
            if (!target) return;
            var $field = $(target);
            if (!$field.length) return;

            $field.removeClass('is-invalid border border-danger');

            var $s2 = $field.next('.select2-container');
            if (!$s2.length) {
                $s2 = $field.siblings('.select2-container');
            }
            if ($s2.length) {
                $s2.find('.select2-selection').removeClass('border border-danger is-invalid');
                $s2.nextAll('.frontend-error, .invalid-feedback').remove();
                $s2.siblings('.frontend-error, .invalid-feedback').remove();
            }

            $field.nextAll('.frontend-error, .invalid-feedback').remove();
            $field.siblings('.frontend-error, .invalid-feedback').remove();

            var $inputGroup = $field.closest('.input-group');
            if ($inputGroup.length && !$inputGroup.hasClass('row')) {
                $inputGroup.nextAll('.frontend-error, .invalid-feedback').remove();
                $inputGroup.siblings('.frontend-error, .invalid-feedback').remove();
                $inputGroup.parent().find('.frontend-error, .invalid-feedback').remove();
            }

            var $col = $field.closest('.col-lg-10, .col-md-10, .col-sm-12, .col-12, .col-4, .col-8');
            if ($col.length) {
                $col.find('.frontend-error, .invalid-feedback').remove();
            }

            $field.parent().find('.frontend-error, .invalid-feedback').remove();
        }


        // Lock helper: KUNCI field jika memilih tersangka dari database
        function setFieldLock(selector, val, isSelect, forceLock) {
            var $el = $(selector);
            var hasVal = val !== null && val !== undefined && String(val).trim() !== '' && String(val).trim() !== 'null' && String(val).trim() !== '0';
            var shouldLock = (typeof forceLock !== 'undefined') ? forceLock : hasVal;

            if (isSelect) {
                if (hasVal) {
                    $el.val(String(val)).trigger('change.select2').trigger('change');
                    clearFieldError(selector);
                } else {
                    $el.val('').trigger('change.select2').trigger('change');
                }

                if (shouldLock) {
                    $el.prop('disabled', true);
                    $el.next('.select2-container').css('pointer-events', 'none');
                    $el.next('.select2-container').find('.select2-selection').css('background-color', '#e9ecef');
                } else {
                    $el.prop('disabled', false);
                    $el.next('.select2-container').css('pointer-events', '');
                    $el.next('.select2-container').find('.select2-selection').css('background-color', '');
                }
            } else {
                if (hasVal) {
                    $el.val(val).trigger('input').trigger('change');
                    clearFieldError(selector);
                } else {
                    $el.val('').trigger('input').trigger('change');
                }

                if (shouldLock) {
                    $el.prop('readonly', true).css('background-color', '#e9ecef');
                    if ($el.attr('data-provide') === 'datepicker') {
                        $el.css('pointer-events', 'none');
                    }
                } else {
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
                $(sel).val('').trigger('change.select2').trigger('change').prop('disabled', false);
                $(sel).next('.select2-container').css('pointer-events', '');
                $(sel).next('.select2-container').find('.select2-selection').css('background-color', '');
            });
            sanitizeIdentityField('#childIdentityType', '#childIdentityNumber');
        }

        function scrollToFirstError() {
            var $form = $('#suratPermohonanPenetapanDiversiForm');
            var $allErrors = $form.find('.is-invalid, .select2-selection.border-danger, .frontend-error');

            if (!$allErrors.length) return;

            var $firstError = $allErrors.first();
            var $visibleTarget = $firstError;

            if ($firstError.hasClass('select2-selection')) {
                $visibleTarget = $firstError.closest('.select2-container');
            } else if (!$firstError.is(':visible')) {
                var $s2 = $firstError.next('.select2-container');
                if ($s2.length && $s2.is(':visible')) {
                    $visibleTarget = $s2;
                } else {
                    var $visParent = $firstError.closest('.input-group, .row, .col-lg-10, .col-md-10, div:visible');
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

            var $content = $('.content');
            if ($content.length && $visibleTarget.length && $visibleTarget.offset()) {
                var currentScroll = $content.scrollTop();
                var contentOffsetTop = $content.offset().top;
                var targetOffsetTop = $visibleTarget.offset().top;
                var targetScroll = currentScroll + (targetOffsetTop - contentOffsetTop) - 120;
                if (targetScroll < 0) targetScroll = 0;
                $content.stop().animate({ scrollTop: targetScroll }, 350);
            }

            if ($visibleTarget.length && $visibleTarget.offset()) {
                $('html, body, .main-content').stop().animate({
                    scrollTop: Math.max(0, $visibleTarget.offset().top - 120)
                }, 350);
            }

            setTimeout(function() {
                if ($firstError.is('input:not([type="hidden"]), textarea') && $firstError.is(':visible')) {
                    try { $firstError.trigger('focus'); } catch (err) {}
                } else if ($firstError.is('select') || $visibleTarget.hasClass('select2-container')) {
                    $visibleTarget.find('.select2-selection').trigger('focus');
                }
            }, 300);
        }

        $(document).ready(function() {
            setInterval(function() {
                $('#attentionBox').toggleClass('alert-danger alert-warning');
            }, 1000);

            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Datepicker initialization
            $('#documentDate').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                orientation: 'auto bottom'
            });

            $('#documentDate').keydown(function(e) {
                e.preventDefault();
                return false;
            });

            $('#agreementDate, #meetingDate, #childBirthDate').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                orientation: 'auto bottom'
            });

            sanitizeIdentityField('#childIdentityType', '#childIdentityNumber');




            $('#childIdentityType').on('change select2:select', function() {
                sanitizeIdentityField('#childIdentityType', '#childIdentityNumber');
                var val = ($('#childIdentityNumber').val() || '').trim();
                if (val !== '') {
                    validateIdentityField('#childIdentityType', '#childIdentityNumber', true);
                }
            });

            $('#childIdentityNumber').on('input blur keyup', function() {
                sanitizeIdentityField('#childIdentityType', '#childIdentityNumber');
                validateIdentityField('#childIdentityType', '#childIdentityNumber', true);
            });

            $('#childBirthDate').on('change changeDate input', function() {
                validateBirthDateField('#childBirthDate', true);
                var res = calculateAgeFromDate($(this).val());
                if (res) {
                    $('#childAgeYear').val(res.years);
                    $('#childAgeMonth').val(res.months);
                    $('#childAgeDay').val(res.days);
                    clearFieldError('#childAgeYear');
                    clearFieldError('#childAgeMonth');
                    clearFieldError('#childAgeDay');
                } else {
                    $('#childAgeYear').val('');
                    $('#childAgeMonth').val('');
                    $('#childAgeDay').val('');
                }
            });

            $('#suratPerintahPenyidikanDocument').on('change select2:select', function() {
                clearFieldError($(this));
            });

            // Auto-fill & lock dari database Tersangka Anak
            $('#suspectId').on('change select2:select', function() {
                var $opt = $(this).find(':selected');
                if ($opt.val()) {
                    clearFieldError('#suspectId');
                    setFieldLock('#childIdentityType', $opt.data('identity-type'), true, true);
                    setFieldLock('#childIdentityNumber', $opt.data('identity'), false, true);
                    setFieldLock('#childName', $opt.data('name'), false, true);
                    setFieldLock('#childGender', $opt.data('gender'), true, true);
                    setFieldLock('#childBirthPlace', $opt.data('birthplace'), false, true);
                    setFieldLock('#childBirthDate', $opt.data('birthdate'), false, true);
                    setFieldLock('#childAgeYear', $opt.data('age-year'), false, true);
                    setFieldLock('#childAgeMonth', $opt.data('age-month'), false, true);
                    setFieldLock('#childAgeDay', $opt.data('age-day'), false, true);
                    setFieldLock('#childNationality', $opt.data('nationality'), true, true);
                    setFieldLock('#childJob', $opt.data('job'), true, true);
                    setFieldLock('#childReligion', $opt.data('religion'), true, true);
                    setFieldLock('#childAddress', $opt.data('address'), false, true);

                    sanitizeIdentityField('#childIdentityType', '#childIdentityNumber');

                    var childFields = [
                        '#childIdentityType', '#childIdentityNumber', '#childName',
                        '#childGender', '#childBirthPlace', '#childBirthDate',
                        '#childAgeYear', '#childAgeMonth', '#childAgeDay',
                        '#childNationality', '#childJob', '#childReligion', '#childAddress'
                    ];
                    childFields.forEach(function(sel) {
                        if (hasFieldValue(sel)) {
                            clearFieldError(sel);
                        }
                    });
                } else {
                    resetChildFields();
                }
            });

            // Inisialisasi lock tersangka saat edit jika ada tersangka terpilih
            if ($('#suspectId').val()) {
                $('#suspectId').trigger('change');
            }

            $('input[name="agreementResultType"]').on('change', function() {
                if ($(this).val() === 'PEMBINAAN') {
                    $('#pembinaanContainer').slideDown();
                } else {
                    $('#pembinaanContainer').slideUp();
                    $('#agreementInstitution').val('');
                    $('#agreementDurationMonths').val('');
                }
                clearFieldError($('input[name="agreementResultType"]'));
            });

            // Tembusan dynamic add / remove
            $('.addCarbonCopiesButton').on('click', function() {
                var inputGroup = '<div class="input-group mb-2">' +
                    '<input type="text" class="form-control" name="carbonCopies[]" value="">' +
                    '<div class="input-group-append">' +
                    '<button class="btn btn-outline-danger removeCarbonCopiesButton" type="button">Hapus</button>' +
                    '</div>' +
                    '</div>';

                $('#carbonCopiesContainer').append(inputGroup);
            });

            $(document).on('click', '.removeCarbonCopiesButton', function() {
                $(this).closest('.input-group').remove();
            });

            $(document).on('input change changeDate dp.change keyup blur', 'input, textarea, select', function() {
                var $field = $(this);
                var id = $field.attr('id');
                if (id === 'childIdentityNumber' || id === 'childBirthDate') {
                    return;
                }
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

            setInterval(function() {
                $('.is-invalid').each(function() {
                    var $el = $(this);
                    var id = $el.attr('id');
                    if (id === 'childIdentityNumber' || id === 'childBirthDate') {
                        return;
                    }
                    if (hasFieldValue($el)) {
                        clearFieldError($el);
                    }
                });
                $('.select2-selection.border-danger').each(function() {
                    var $sel = $(this).closest('.select2-container').prev('select');
                    if ($sel.length && hasFieldValue($sel)) {
                        clearFieldError($sel);
                    }
                });
            }, 400);

            $('.onlyIntegerInput').on('keypress', function(event) {
                var charCode = (event.which) ? event.which : event.keyCode;
                if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                    event.preventDefault();
                }
            });

            $('#suratPermohonanPenetapanDiversiFormSubmit').on('click', function(e) {
                e.preventDefault();

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
                    if (!$field.length || !$field.is(':visible')) return;
                    var raw = $field.val();
                    var val = (raw !== null && raw !== undefined) ? String(raw).trim() : '';
                    if (!val || val === '') {
                        markError(fieldSelector, label + ' harus diisi');
                    }
                }

                function checkIdentityNumber(typeSelector, numberSelector, label) {
                    var $field = $(numberSelector);
                    if (!$field.length || !$field.is(':visible')) return;
                    var raw = $field.val();
                    var val = (raw !== null && raw !== undefined) ? String(raw).trim() : '';
                    if (!val || val === '') {
                        markError(numberSelector, label + ' harus diisi');
                    } else if (typeof validateIdentityField === 'function') {
                        var err = validateIdentityField(typeSelector, numberSelector, true);
                        if (err) errors.push(err);
                    }
                }

                function checkSelect(fieldSelector, label) {
                    var $field = $(fieldSelector);
                    if (!$field.length) return;
                    var isVis = $field.is(':visible') || ($field.next('.select2-container').length && $field.next('.select2-container').is(':visible'));
                    if (!isVis) return;

                    var raw = $field.val();
                    var hasVal = Array.isArray(raw) ? raw.length > 0 : (raw && String(raw).trim() !== '' && String(raw).trim() !== '0');
                    if (!hasVal) {
                        markError(fieldSelector, label + ' harus dipilih');
                    }
                }

                // 1. Informasi Dasar Dokumen & Tujuan
                checkInput('#documentNumber', 'Nomor Dokumen');
                checkSelect('#documentClassification', 'Klasifikasi');
                checkInput('#appendix', 'Lampiran');
                checkInput('#perihal', 'Perihal');
                checkSelect('#courtId', 'Pengadilan Penerima');

                // 2. Rujukan & Dasar Hukum
                checkSelect('#suratPerintahPenyidikanDocument', 'No SP Penyidikan');
                checkInput('#agreementDate', 'Tanggal Keputusan Bersama');

                // 3. Identitas Anak (Tersangka Anak)
                checkSelect('#suspectId', 'Tersangka Anak');
                checkInput('#childName', 'Nama Anak');
                checkSelect('#childIdentityType', 'Jenis Identitas Anak');
                checkIdentityNumber('#childIdentityType', '#childIdentityNumber', 'Nomor Identitas Anak');
                checkSelect('#childNationality', 'Kewarganegaraan Anak');
                checkSelect('#childGender', 'Jenis Kelamin Anak');
                checkInput('#childBirthPlace', 'Tempat Lahir Anak');
                checkInput('#childBirthDate', 'Tanggal Lahir Anak');
                if ($('#childBirthDate').val()) {
                    var childBirthErr = validateBirthDateField('#childBirthDate', true);
                    if (childBirthErr) errors.push(childBirthErr);
                }
                checkSelect('#childJob', 'Pekerjaan Anak');
                checkSelect('#childReligion', 'Agama Anak');
                checkInput('#childAddress', 'Alamat Anak');

                // 4. Hasil Rapat Koordinasi / Keputusan Bersama
                checkInput('#meetingDate', 'Tanggal Rapat Koordinasi');
                var resType = $('input[name="agreementResultType"]:checked').val();
                if (!resType) {
                    markError('input[name="agreementResultType"]', 'Hasil Keputusan Bersama harus dipilih');
                } else if (resType === 'PEMBINAAN') {
                    checkInput('#agreementInstitution', 'Nama Lembaga Pembinaan');
                    checkInput('#agreementDurationMonths', 'Lama Pembinaan (Bulan)');
                }

                // 5. Penandatangan Dokumen
                checkInput('#documentDate', 'Tanggal Ditandatangani Dokumen');
                var docDateVal = ($('#documentDate').val() || '').trim();
                var originalDocDate = "{{ !empty($document->document_date) ? date('Y-m-d', strtotime($document->document_date)) : '' }}";
                if (docDateVal && docDateVal !== originalDocDate) {
                    var selectedDate = new Date(docDateVal);
                    var today = new Date();
                    today.setHours(0, 0, 0, 0);
                    selectedDate.setHours(0, 0, 0, 0);
                    if (selectedDate < today) {
                        markError('#documentDate', 'Tanggal Ditandatangani Dokumen minimal hari ini (tidak boleh tanggal kemarin/masa lalu)');
                    }
                }
                checkSelect('#signatory', 'Penandatanganan Surat');

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
                    $('#suratPermohonanPenetapanDiversiForm').find(':disabled').prop('disabled', false);
                    $('#suratPermohonanPenetapanDiversiForm')[0].submit();
                });
            });
        });
    </script>
@endpush
