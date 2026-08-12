@php
    $_title = 'Lihat Data Personel';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
@endpush

@section('content')
<a class="btn-back" href="{{ route('personnel.index', ['policeId' => $policeId]) }}"><i class="bi bi-arrow-left"></i>Kembali ke Halaman Daftar Personel</a>

<div class="box">
    <div class="box-header">
        <h4 class="fw-bold text-blue-dark">Lihat Personnel</h4>

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

    <div class="boxy-body">
        <form
            action="#"
            method="POST" enctype="multipart/form-data" id="officerForm">
            
            <input type="hidden" name="id" value="{{ $officer->id }}">
            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="name">Nama Lengkap</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                        value="{{ $currentOfficer->full_name }}" required
                        placeholder="Masukkan Nama Lengkap Dan Gelar Pendidikan" disabled>
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label">Status Kepegawaian</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                    <div class="d-flex">
                        <div class="form-check mx-1">
                            <input class="form-check-input" type="radio" id="typePoliceEmployment" name="employmentType"
                                value="1" disabled
                               @if ($currentOfficer->employment_type_id == 1) checked @endif>
                            <label for="typePoliceEmployment">
                                Anggota Polri
                            </label>
                        </div>

                        <div class="form-check mx-1">
                            <input class="form-check-input" type="radio" id="typeCivilEmployment" name="employmentType"
                                value="2" disabled
                                @if ($currentOfficer->employment_type_id == 2)
                                        checked @endif>
                            <label for="typeCivilEmployment">
                                PNS Polri
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label">Pangkat/Golongan</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                    <select class="form-control select2" name="rank" id="rank" disabled>
                        <option value="">--Pilih Pangkat--</option>
                        @foreach ($ranks as $rank)
                            <option value="{{ $rank->id }}"
                                {{ ($currentOfficer->rank_id == $rank->id) ? 'selected' : '' }}>
                                {{ $rank->full_name . ' (' . $rank->name . ')' }}
                            </option>
                        @endforeach
                    </select>

                    @error('rank')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label">Tanggal Lahir</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                    <input class="form-control" id="birthDate" name="birthDate" placeholder="YYYY-MM-DD" autocomplete="off" disabled
                        value="{{ $currentOfficer->birth_date }}" data-provide="datepicker">

                    @error('birthDate')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="registerNumber">NRP</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                    <input id="registerNumber" type="text" class="form-control @error('registerNumber') is-invalid @enderror"
                        name="registerNumber" value="{{ $currentOfficer->register_number }}"
                        required placeholder="Masukkan NRP" disabled>

                    @error('registerNumber')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="email">Email</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                    <input id="email" type="text" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ $currentOfficer->email }}" required disabled
                        placeholder="Masukkan Email">
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="phoneNumber">Nomor Telepon</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                    <input id="phoneNumber" type="text" class="form-control @error('phoneNumber') is-invalid @enderror"
                        name="phoneNumber" value="{{ $currentOfficer->phone_number }}" required disabled
                        placeholder="Masukkan Nomor Telepon">
                    @error('phoneNumber')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label">Jenis Kelamin</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                    <select class="form-control select2" name="gender" id="gender" disabled>
                        <option value="">--Pilih Jenis Kelamin--</option>

                        @foreach ($genders as $gender)
                            <option value="{{ $gender->id }}"
                                {{ ($currentOfficer->gender_id == $gender->id) ? 'selected' : '' }}>
                                {{ $gender->name }}</option>
                        @endforeach

                    </select>

                    @error('gender')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label">Agama</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                    <select class="form-control select2" name="religion" id="religion" disabled>
                        <option value="">--Pilih Agama--</option>

                        @foreach ($religions as $religion)
                            <option value="{{ $religion->id }}"
                                {{ ($currentOfficer->religion_id == $religion->id) ? 'selected' : '' }}>
                                {{ $religion->name }}</option>
                        @endforeach
                    </select>

                    @error('religion')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label">Jabatan Struktural</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                    <select class="form-control select2" name="position" id="position" disabled>
                        <option value="">--Pilih Jabatan--</option>
                        <option value="">--Tidak Ada Pilihan (Silahkan Hubungi Helpdesk)--</option>
                        @foreach ($positions as $position)
                            @php
                                $isCanSignatory = $position->positionCLuster ? ($position->positionCluster->is_can_signatory == true ? 'true' : 'false') : 'false';
                            @endphp
                            <option value="{{ $position->id }}" data-is-can-signatory="{{ $isCanSignatory }}"
                                {{ ($currentOfficer->position_id == $position->id) ? 'selected' : '' }}>
                                {{ $position->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('position')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror

                    @if (isset($currentOfficer->class))
                        @if ($currentOfficer->class == 'SIGNATORY')
                            @php $isRegisterSignatorySection = 'checked'; @endphp
                        @endif
                    @endif
                    <div class="mt-4" @if (isset($isRegisterSignatorySection) == false) style="display: none;" @endif
                        id="isRegisterSignatorySection">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="isRegisterSignatory"
                                name="isRegisterSignatory" value="true" aria-label="..." disabled
                                @if (isset($isRegisterSignatorySection) == true) checked @endif>
                            <label for="isRegisterSignatory">
                                Daftarkan Personnel Ini Sebagai Pejabat Penandatangan Tanda Tangan Elektronik
                            </label>
                        </div>

                        <small class="text-muted">(*Untuk mendaftarkan pejabat penandatangan tanda tangan elektronik, jika buat
                            baru setelah dibuat data personil ini perlu melewati tahap validasi oleh tim helpdesk.)</small>
                    </div>
                </div>
            </div>

            <div id="registerSignatorySection" class="mb-4"
                @if (isset($isRegisterSignatorySection) == false) style="display: none;" @endif>
                <div class="card">
                    <div class="card-body">
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label">Jenis Identitas</label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                                <select class="form-control select2" name="registerSignatoryIdentityType"
                                    id="registerSignatoryIdentityType" disabled>
                                    @foreach ($registerSignatoryIdentityTypes as $registerSignatoryIdentityType)
                                        <option value="{{ $registerSignatoryIdentityType->id }}" selected>
                                            {{ $registerSignatoryIdentityType->name }}</option>
                                    @endforeach
                                </select>

                                @error('registerSignatoryIdentityType')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="registerSignatoryIdentityNumber">Nomor NIK
                            </label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                                <input id="registerSignatoryIdentityNumber" type="text"
                                    class="form-control @error('registerSignatoryIdentityNumber') is-invalid @enderror"
                                    name="registerSignatoryIdentityNumber"
                                    value="{{ $currentOfficer->identity_number }}"
                                    required placeholder="Masukkan nomor induk kependudukan" disabled>

                                @error('registerSignatoryIdentityNumber')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label">Jenjang Pendidikan Terakhir</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                    <select class="form-control select2" name="education" id="education" disabled>
                        <option value="">--Pilih Pendidikan--</option>

                        @foreach ($educations as $education)
                            <option value="{{ $education->id }}"
                                {{ $currentOfficer->education_id == $education->id ? 'selected' : '' }}>
                                {{ $education->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('education')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="educationInstitutionName">Universitas / Perguruan Tinggi
                    / Sekolah</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                    <input id="educationInstitutionName" type="text"
                        class="form-control @error('educationInstitutionName') is-invalid @enderror"
                        name="educationInstitutionName" disabled
                        value="{{ $currentOfficer->education_institution_name }}"
                        required placeholder="Nama Universitas / Perguruan Tinggi / Sekolah (Opsional)">

                    @error('educationInstitutionName')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label">Diktuk Polri</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                    <select class="form-control select2" name="policeDiktukEducation" id="policeDiktukEducation" disabled>
                        <option value="">--Pilih Pendidikan Diktuk Polri--</option>
                        <option value="">--Tidak Ada Pilihan (Silahkan Hubungi Helpdesk)--</option>

                        @foreach ($policeDiktukEducations as $policeDiktukEducation)
                            <option value="{{ $policeDiktukEducation->id }}"
                                {{ $currentOfficer->police_diktuk_education_id == $policeDiktukEducation->id ? 'selected' : '' }}>
                                {{ $policeDiktukEducation->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('policeDiktukEducation')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="policeDiktukEducationGraduateYear">Tahun Lulus Diktuk.
                    POLRI</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                    <input id="policeDiktukEducationGraduateYear" type="text"
                        class="form-control @error('policeDiktukEducationGraduateYear') is-invalid @enderror"
                        name="policeDiktukEducationGraduateYear" disabled
                        value="{{ $currentOfficer->police_diktuk_education_graduate_year }}"
                        required placeholder="Tahun Lulus">

                    @error('policeDiktukEducationGraduateYear')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            @if(empty(Auth::user()->police_id) || Auth::user()->role_id == 5)
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="isRegisterAdmin">Flag</label>
                    <div class="col-lg-10 col-md-10 col-sm-10">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="isRegisterAdmin"
                                name="isRegisterAdmin" value="true" aria-label="..." disabled
                                @if ($currentOfficer->flag == 'ADMIN') checked @endif>
                            <label for="isRegisterAdmin">
                                Daftarkan Personnel Ini Sebagai Admin Satker
                            </label>
                        </div>

                        @error('isRegisterAdmin')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="isRegisterAdminCanEntryDocument"
                                name="isRegisterAdminCanEntryDocument" value="true" aria-label="..." disabled
                                @php
                                    $isCanEntryDocument = $user->properties['is_can_entry_document'] ?? false;
                                @endphp
                                @if ($isCanEntryDocument == true) checked @endif>
                            <label for="isRegisterAdminCanEntryDocument">
                                Jadikan Personnel Ini Dapat Entry Dokumen
                            </label>
                        </div>

                        @error('isRegisterAdminCanEntryDocument')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            @else
                <input type="hidden" name="isRegisterAdmin" value="{{ $currentOfficer->flag == 'ADMIN' ? 'true' : 'false' }}">
                <input type="hidden" name="isRegisterAdminCanEntryDocument" value="{{ $user->properties['is_can_entry_document'] ?? 'false' }}">
            @endif

            <br/>
            <hr/>

            <div class="box-header">
                <h5 class="fw-bold text-blue-dark">RIWAYAT JABATAN</h5>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="mb-2 mt-2">

                        <div id="careerHistory">
                            <div class="input-group mt-3">
                                <table class="table table-bordered table-responsive-md" id="careerHistoryTable">
                                    <thead class="table-danger">
                                        <tr class="text-center">
                                            <th scope="col">Fungsi</th>
                                            <th scope="col">Jabatan</th>
                                            <th scope="col">Tahun</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <br/>
            <hr/>

            <div class="box-header">
                <h5 class="fw-bold">PENDIDIKAN LANTAS / DIKJUR</h5>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="mb-2 mt-2">

                        <div id="policeDikjurEducation">
                            <div class="input-group mt-3">
                                <table class="table table-bordered table-responsive-md" id="policeDikjurEducationTable">
                                    <thead class="table-danger">
                                        <tr class="text-center">
                                            <th scope="col">Tempat Pendidikan</th>
                                            <th scope="col">Tahun Lulus</th>
                                            <th scope="col">Materi Pendidikan</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <h4 class="fw-bold text-blue-dark">KEPENYIDIKAN</h4>

            <div class="row">
                <div class="col-12 my-2">
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label">Status Kepenyidikan</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12">

                            <div class="mb-3">
                                <div class="icheck-primary">
                                    <input type="radio" id="existsOfficerSkepPenyidik" name="isExistsOfficerSkepPenyidik"
                                        value="true" disabled>
                                    <label for="existsOfficerSkepPenyidik">
                                        Sudah ada Skep Penyidik
                                    </label>
                                </div>

                                <div class="icheck-primary">
                                    <input type="radio" id="notExistsOfficerSkepPenyidik"
                                        name="isExistsOfficerSkepPenyidik" value="false"
                                        checked disabled>
                                    <label for="notExistsOfficerSkepPenyidik">
                                        Belum ada Skep Penyidik
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="officerSkepPenyidikSection" style="display:none;">
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="officerSkepPenyidikNumber">NOMOR SKEP PENYIDIK</label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                                <input id="officerSkepPenyidikNumber" type="text"
                                    class="form-control @error('officerSkepPenyidikNumber') is-invalid @enderror"
                                    name="officerSkepPenyidikNumber"
                                    value="" disabled
                                    required placeholder="Masukkan Nomor Skep">
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <br/>
            <hr/>

            <div class="box-header">
                <h5 class="fw-bold">RIWAYAT SERTIFIKASI</h5>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label">Status sertifikasi</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                            <h6 class="fw-bold">*Silahkan isi jika sudah memiliki sertifikat</h6>

                            <div class="mb-3">
                                <div class="icheck-primary">
                                    <input type="radio" id="existsOfficerCertificate" name="isExistsOfficerCertificate"
                                        value="true" disabled>
                                    <label for="existsOfficerCertificate">
                                        Sudah Memiliki Sertifikasi
                                    </label>
                                </div>

                                <div class="icheck-primary">
                                    <input type="radio" id="notExistsOfficerCertificate"
                                        name="isExistsOfficerCertificate" value="false" disabled>
                                    <label for="notExistsOfficerCertificate">
                                        Belum Memiliki Sertifikasi
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="officerCertificateSection" style="display:none;">
                        <div id="certificate">
                            <div class="input-group mt-3">
                                <table class="table table-bordered table-responsive-md" id="certificateTable">
                                    <thead class="table-danger">
                                        <tr class="text-center">
                                            <th scope="col">Jenis Sertifikat</th>
                                            <th scope="col">Nomor Sertifikat</th>
                                            <th scope="col">Tanggal Mulai Berlaku</th>
                                            <th scope="col">Tanggal Kadaluwarsa</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <br/>
            <hr/>

            <div class="card">
                <div class="card-body">
                    <div class="box-header">
                        <h5 class="fw-bold text-blue-dark">STATUS BKO</h5>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="mb-2 mt-2">
                                <div class="input-group row mb-3 ms-0">
                                    <label class="fw-bold col-sm-2 col-form-label">Status BKO</label>
                                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">

                                        <div class="mb-3">
                                            <div class="icheck-primary">
                                                <input type="radio" id="isOfficerOperationControlAssistance" name="isOfficerOperationControlAssistance" value="true" disabled>
                                                <label for="isOfficerOperationControlAssistance">
                                                    Ya
                                                </label>
                                            </div>

                                            <div class="icheck-primary">
                                                <input type="radio" id="isNotOfficerOperationControlAssistance" name="isOfficerOperationControlAssistance" value="false" disabled checked>
                                                <label for="isNotOfficerOperationControlAssistance">
                                                    Tidak
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="officerOperationControlAssistanceSection" style="display:none;">
                                    <div class="input-group row mb-3 ms-0">
                                        <label class="fw-bold col-sm-2 col-form-label" for="officerOperationControlAssistanceNumber">No.Surat BKO</label>
                                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                                            <input id="officerOperationControlAssistanceNumber" type="text"
                                                class="form-control @error('officerOperationControlAssistanceNumber') is-invalid @enderror font-weight-bold" name="officerOperationControlAssistanceNumber"
                                                value="" placeholder="" disabled>
                                        </div>
                                    </div>

                                    <div class="input-group row mb-3 ms-0">
                                        <label class="fw-bold col-sm-2 col-form-label" for="officerOperationControlAssistanceDate">Tanggal BKO</label>
                                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                                            <input id="officerOperationControlAssistanceDate" type="text"
                                                class="form-control @error('officerOperationControlAssistanceDate') is-invalid @enderror font-weight-bold" name="officerOperationControlAssistanceDate"
                                                value="" placeholder="YYYY-MM-DD" disabled>
                                        </div>
                                    </div>

                                    <div class="input-group row mb-3 ms-0">
                                        <label class="fw-bold col-sm-2 col-form-label" for="officerOperationControlAssistanceOriginPolice">Satker Asal BKO</label>
                                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                                            <input id="officerOperationControlAssistanceOriginPoliceName" type="text"
                                                class="form-control @error('officerOperationControlAssistanceOriginPoliceName') is-invalid @enderror font-weight-bold" name="officerOperationControlAssistanceOriginPoliceName"
                                                value="" disabled placeholder="">

                                            <input id="officerOperationControlAssistanceOriginPoliceId" type="hidden"
                                                class="form-control font-weight-bold" name="officerOperationControlAssistanceOriginPoliceId"
                                                value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
<script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        var officer = @json($officer);
        var careerHistories = @json($officer->officerCareerHistories);
        var policeDikjurEducations = @json($officer->officerPoliceDikjurEducations);
        var certificateHistories = @json($officer->officerCertificateHistories);
        var investigativeDetail = @json($officer->officerInvestigativeDetail);
        var operationControlAssistance = @json($officer->officerOperationControlAssistance);
   
        //careerHistory
        $(function(){
            for (var key in careerHistories){
                if(careerHistories.hasOwnProperty(key)){
                    var careerHistory = careerHistories[key];
                    var careerHistoryPoliceDivisionId = careerHistory.police_division.id ?? '';
                    var careerHistoryPoliceDivisionName = careerHistory.police_division.name ?? '';
                    var careerHistoryPositionName = careerHistory.position_name;
                    var careerHistoryYear = careerHistory.year;

                    // Append data ke table
                    $('#careerHistoryTable tbody').append(
                        '<tr>' +
                        '<td>' + careerHistoryPoliceDivisionName + '</td>' +
                        '<td>' + careerHistoryPositionName + '</td>' +
                        '<td>' + careerHistoryYear + '</td>' +
                        '</tr>'
                    );
                }
            }
        });

        //policeDikjurEducations
        $(function(){
            for (var key in policeDikjurEducations){
                if(policeDikjurEducations.hasOwnProperty(key)){
                    var policeDikjurEducation = policeDikjurEducations[key];
                    var policeDikjurEducationPlaceId = policeDikjurEducation.police_dikjur_education_place.id ?? '';
                    var policeDikjurEducationPlaceName = policeDikjurEducation.police_dikjur_education_place.name ?? '';
                    var policeDikjurEducationGraduateYear = policeDikjurEducation.graduate_year;
                    var policeDikjurEducationMaterialId = policeDikjurEducation.police_dikjur_education_material.id ?? '';
                    var policeDikjurEducationMaterialName = policeDikjurEducation.police_dikjur_education_material.name ?? '';

                    // Append data ke table
                    $('#policeDikjurEducationTable tbody').append(
                        '<tr>' +
                        '<td>' + policeDikjurEducationPlaceName + '</td>' +
                        '<td>' + policeDikjurEducationGraduateYear + '</td>' +
                        '<td>' + policeDikjurEducationMaterialName + '</td>' +
                        '</tr>'
                    );
                }
            }
        });

        //certificateHistories
        $(function(){
            if (officer != null) {
                var isOfficerCertificateExists = officer.is_certificate_exists;
                if(isOfficerCertificateExists == true || isOfficerCertificateExists == 'true'){
                    $('input[name="isExistsOfficerCertificate"][value="true"]').prop('checked', true).trigger('change');
                    $('#officerCertificateSection').show();
                } else {
                    $('input[name="isExistsOfficerCertificate"][value="false"]').prop('checked', true).trigger('change');
                    $('#officerCertificateSection').hide();
                }
            }

            for (var key in certificateHistories){
                if(certificateHistories.hasOwnProperty(key)){
                    var certificateHistory = certificateHistories[key];
                    var certificateHistoryTypeId = certificateHistory.certificate_type.id ?? '';
                    var certificateHistoryTypeName = certificateHistory.certificate_type.name ?? '';
                    var certificateHistoryNumber = certificateHistory.certificate_number;
                    var certificateHistoryStartDate = certificateHistory.begin_date;
                    var certificateHistoryEndDate = certificateHistory.expired_date;

                    // Append data ke table
                    $('#certificateTable tbody').append(
                        '<tr>' +
                        '<td>' + certificateHistoryTypeName + '</td>' +
                        '<td>' + certificateHistoryNumber + '</td>' +
                        '<td>' + certificateHistoryStartDate + '</td>' +
                        '<td>' + certificateHistoryEndDate + '</td>' +
                        '</tr>'
                    );
                }
            }
        });

        //operationControlAssistance
        $(function(){
            if (operationControlAssistance != null) {
                var isOfficerOperationControlAssistance = operationControlAssistance.is_operation_control_assistance;
                if(isOfficerOperationControlAssistance == true || isOfficerOperationControlAssistance == 'true'){
                    $('input[name="isOfficerOperationControlAssistance"][value="true"]').prop('checked', true).trigger('change');
                    $('#officerOperationControlAssistanceSection').show();

                    var operationControlAssistanceLetterNumber = operationControlAssistance.letter_number;
                    var operationControlAssistanceDate = operationControlAssistance.date;
                    var operationControlAssistanceOriginPoliceId = operationControlAssistance.origin_police.id;
                    var operationControlAssistanceOriginPoliceName = operationControlAssistance.origin_police.full_name;

                    $('#officerOperationControlAssistanceNumber').val(operationControlAssistanceLetterNumber);
                    $('#officerOperationControlAssistanceDate').val(operationControlAssistanceDate);
                    $('#officerOperationControlAssistanceOriginPoliceName').val(operationControlAssistanceOriginPoliceName);
                    $('#officerOperationControlAssistanceOriginPoliceId').val(operationControlAssistanceOriginPoliceId);
                } else {
                    $('input[name="isOfficerOperationControlAssistance"][value="false"]').prop('checked', true).trigger('change');
                    $('#officerOperationControlAssistanceSection').hide();
                }
            }
        });

        //investigativeDetail
        $(function(){
            if (investigativeDetail != null) {
                var isOfficerSkepPenyidikExists = investigativeDetail.is_skep_penyidik_exists;
                if(isOfficerSkepPenyidikExists == true || isOfficerSkepPenyidikExists == 'true'){
                    $('input[name="isExistsOfficerSkepPenyidik"][value="true"]').prop('checked', true).trigger('change');
                    $('#officerSkepPenyidikSection').show();

                    var officerSkepPenyidikNumber = investigativeDetail.skep_penyidik_number;
                    $('#officerSkepPenyidikNumber').val(officerSkepPenyidikNumber);
                } else {
                    $('input[name="isExistsOfficerSkepPenyidik"][value="false"]').prop('checked', true).trigger('change');
                    $('#officerSkepPenyidikSection').hide();
                }
            }
        });
    });

    //first load initialize
    $(document).ready(function() {
        $('#policeDiktukEducationGraduateYear').on('input', function(e) {
            var input = $(this).val();
            var maxLength = 4; // Maksimal panjang karakter

            // Menghapus karakter selain angka
            input = input.replace(/[^0-9]/g, '');

            // Memastikan panjang karakter tidak melebihi maxLength
            if (input.length > maxLength) {
                input = input.slice(0, maxLength);
            }

            $(this).val(input);
        });

        $(function(){
            var positionIsCanSignatory = $('#position').find(':selected').data('is-can-signatory');

            if (positionIsCanSignatory == true || positionIsCanSignatory == 'true') {
                $('#isRegisterSignatorySection').show();
            } else {
                $('#isRegisterSignatory').prop('checked', false).trigger('change');
                $('#isRegisterSignatorySection').hide();
            }
        });
    });

    //datepicker
    $(document).ready(function() {
        $('#birthDate').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: "true",
            endDate: new Date(),
        });
        $('#birthDate').keydown(function(e) {
            e.preventDefault();
            return false;
        });

        $('#certificateStartDate').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: "true",
            container: '#addCertificateModal'
        });
        $('#certificateStartDate').keydown(function(e) {
            e.preventDefault();
            return false;
        });
        $('#certificateEndDate').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: "true",
            container: '#addCertificateModal'
        });
        $('#certificateEndDate').keydown(function(e) {
            e.preventDefault();
            return false;
        });

        $('#officerOperationControlAssistanceDate').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: "true",
        });
        $('#officerOperationControlAssistanceDate').keydown(function(e) {
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
            theme: 'bootstrap4'
        });

        $('#addPoliceDikjurEducationModal #policeDikjurEducationPlace').select2({
            dropdownParent: $('#policeDikjurEducationPlace').parent(),
            theme: 'bootstrap4',
            width: '100%'
        });
        $('#addPoliceDikjurEducationModal #policeDikjurEducationMaterial').select2({
            dropdownParent: $('#policeDikjurEducationMaterial').parent(),
            theme: 'bootstrap4',
            width: '100%'
        });
        
        $('#careerHistoryForm #careerHistoryPoliceDivision').select2({
            dropdownParent: $('#careerHistoryPoliceDivision').parent(),
            theme: 'bootstrap4',
            width: '100%'
        });
    });

    //dikjur
    $(document).ready(function() {
        $('#policeDikjurEducationGraduateYear').on('input', function(e) {
            var input = $(this).val();
            var maxLength = 4; // Maksimal panjang karakter

            // Menghapus karakter selain angka
            input = input.replace(/[^0-9]/g, '');

            // Memastikan panjang karakter tidak melebihi maxLength
            if (input.length > maxLength) {
                input = input.slice(0, maxLength);
            }

            $(this).val(input);
        });
    });

    //certificate history
    $(document).ready(function() {
    });

    //career history
    $(document).ready(function() {
        $('#careerHistoryYear').on('input', function(e) {
            var input = $(this).val();
            var maxLength = 4; // Maksimal panjang karakter

            // Menghapus karakter selain angka
            input = input.replace(/[^0-9]/g, '');

            // Memastikan panjang karakter tidak melebihi maxLength
            if (input.length > maxLength) {
                input = input.slice(0, maxLength);
            }

            $(this).val(input);
        });
    });

    // skep penyidik
    $(document).ready(function() {
        $('input[name="isExistsOfficerSkepPenyidik"]').on('change', function() {
            if ($(this).val() == 'true') {
                $('#officerSkepPenyidikSection').show();
            } else {
                $('#officerSkepPenyidikSection').hide();
            }
        });
        
        $('input[name="isOfficerOperationControlAssistance"]' ).on('change', function() {
            if ($(this).val() == 'true') {
                $('#officerOperationControlAssistanceSection').show();
            } else {
                $('#officerOperationControlAssistanceSection').hide();
            }
        });

        $('#position').on('change', function() {
            var isCanSignatory = $('#position').find(':selected').data('is-can-signatory');

            if (isCanSignatory == true || isCanSignatory == 'true') {
                $('#isRegisterSignatorySection').show();
            } else {
                $('#isRegisterSignatory').prop('checked', false).trigger('change');
                $('#isRegisterSignatorySection').hide();
            }
        });

        $('#isRegisterSignatory').on('change', function() {
            if ($(this).is(':checked')) {
                $('#registerSignatorySection').show();
            } else {
                $('#registerSignatorySection').hide();
            }
        });

        $('input[name="employmentType"]').on('change', function() {
            var employmentTypeId = $(this).val();

            //call ajax to get ranks
            $.ajax({
                url: "{{ route('personnel.api.ranks', ['policeId' => $policeId]) }}",
                type: "GET",
                data: {
                    employmentTypeId: employmentTypeId
                },
                success: function(response) {
                    $('#rank').empty();
                    $('#rank').append(
                        '<option value="" selected disabled>Pilih Pangkat</option>');
                    $.each(response.data, function(key, value) {
                        $('#rank').append('<option value="' + value.id +
                            '" data-rank-name="' + value.name + '">' + value
                            .full_name + ' (' + value.name + ')' + '</option>');
                    });
                },
                error: function(xhr) {
                    //sweet alert
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong! Please try again later.'
                    });
                }
            });

            //call ajax to get positions
            $.ajax({
                url: "{{ route('personnel.api.positions', ['policeId' => $policeId]) }}",
                type: "GET",
                data: {
                    employmentTypeId: employmentTypeId
                },
                success: function(response) {
                    $('#isRegisterSignatory').prop('checked', false).trigger('change');
                    $('#isRegisterSignatorySection').hide();

                    $('#position').empty();
                    $('#position').append(
                        '<option value="" selected disabled>Pilih Jabatan</option>');
                    $.each(response.data, function(key, value) {
                        $('#position').append('<option value="' + value.id +
                            '" data-position-name="' + value.name +
                            '" data-is-can-signatory="' + value.position_cluster
                            .is_can_signatory + '">' + value.name + '</option>');
                    });
                },
                error: function(xhr) {
                    //sweet alert
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong! Please try again later.'
                    });
                }
            });
        });
    });

    // bko
    $(document).ready(function() {
        $('#officerOperationControlAssistancePoliceSearchOption').select2({
            theme: 'bootstrap4',
        });
    });
</script>
@endpush
