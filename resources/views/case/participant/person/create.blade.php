@php
    $_title = 'Tambah Pihak Terlibat';
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
<a class="btn-back" href="{{route('view_produktivitas_accident', ['accident_id' => request()->query('accident_id'), 'page'=>'participants'])}}"><i class="bi bi-arrow-left"></i>Kembali ke Halaman Pihak Terlibat</a>

<div class="box">
    <div class="box-header">
        <h4 class="fw-bold text-blue-dark">Tambah Pihak Terlibat</h4>

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
            action="{{ route('case.participant.person.store', ['accidentId' => $accidentId, 'accident_id' => request()->query('accident_id')]) }}"
            method="POST" enctype="multipart/form-data" id="participantPersonForm">
            @csrf

            <hr/>

            {{-- PILIHAN JENIS PIHAK --}}
            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="jenisPihak">Jenis Pihak<span class="text-danger fs-5">*</span></label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                    <select class="form-control select2" id="jenisPihak" name="jenisPihak">
                        <option value="">-- Pilih Jenis Pihak --</option>
                        <option value="TERLAPOR" @if(old('jenisPihak') == 'TERLAPOR') selected @endif>Terlapor</option>
                        <option value="PELAPOR"  @if(old('jenisPihak') == 'PELAPOR') selected @endif>Pelapor</option>
                    </select>
                </div>
            </div>

            <hr/>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="identityType">Jenis
                    Identitas<span class="text-danger fs-5">*</span></label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                    <select class="form-control select2" id="identityType" name="identityType">
                        <option value="">--Pilih Jenis Identitas--</option>
                        @foreach ($identityTypes as $identityType)
                            <option value="{{ $identityType->id }}"
                                data-identity-type-name="{{ $identityType->name }}"
                                @if(old('identityType') == $identityType->id) selected @endif>
                                {{ $identityType->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="identityNumber">Nomor
                    Identitas<span class="text-danger fs-5">*</span></label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                    <input type="text" class="form-control" id="identityNumber" name="identityNumber"
                        value="{{ old('identityNumber') }}"
                        placeholder="Nomor Identitas">
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="name">Nama<span class="text-danger fs-5">*</span></label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                    <input type="text" class="form-control" id="name" name="name"
                        value="{{ old('name') }}"
                        placeholder="Nama Lengkap">
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="aliasName">Nama Alias
                </label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                    <input type="text" class="form-control" id="aliasName" name="aliasName"
                        value="{{ old('aliasName') }}"
                        placeholder="Nama Alias (Opsional)">
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="gender">Jenis Kelamin<span class="text-danger fs-5">*</span>
                </label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                    <select class="form-control select2" id="gender" name="gender">
                        <option value="">--Pilih Jenis Kelamin--</option>
                        @foreach ($genders as $gender)
                            <option value="{{ $gender->id }}" data-gender-name="{{ $gender->name }}"
                                @if(old('gender') == $gender->id) selected @endif>
                                {{ $gender->name }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- <div class="col-sm-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="isUnknownGender" name="isUnknownGender"
                            value="true" aria-label="..." @if(old('isUnknownGender') == 'true') checked @endif>
                        <label for="isUnknownGender">
                            Tidak Tahu
                        </label>
                    </div>
                </div> --}}
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="birthPlace">Tempat Lahir<span class="text-danger fs-5">*</span>
                </label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                    <input type="text" class="form-control" id="birthPlace" name="birthPlace"
                        placeholder="Tempat Lahir" value="{{ old('birthPlace') }}">
                </div>
                {{-- <div class="col-sm-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                            id="isUnknownBirthPlace" name="isUnknownBirthPlace"
                            value="true" aria-label="..." @if(old('isUnknownBirthPlace') == 'true') checked @endif>
                        <label for="isUnknownBirthPlace">
                            Tidak Tahu
                        </label>
                    </div>
                </div> --}}
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="birthDate">Tanggal Lahir<span class="text-danger fs-5">*</span>
                </label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                    <input type="text" class="form-control" id="birthDate" name="birthDate"
                        placeholder="YYYY-MM-DD" data-provide="datepicker" value="{{ old('birthDate') }}">
                </div>
                {{-- <div class="col-sm-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                            id="isUnknownBirthDate" name="isUnknownBirthDate"
                            value="true" aria-label="..." @if(old('isUnknownBirthDate') == 'true') checked @endif>
                        <label for="isUnknownBirthDate">
                            Tidak Tahu
                        </label>
                    </div>
                </div> --}}
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="father">Ayah Kandung
                </label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                    <input type="text" class="form-control" id="father" name="father"
                        placeholder="Nama Ayah Kandung" value="{{ old('father') }}">
                </div>
                <div class="col-sm-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="isUnknownFather" name="isUnknownFather"
                            value="true" aria-label="..." @if(old('isUnknownFather') == 'true') checked @endif>
                        <label for="isUnknownFather">
                            Tidak Tahu
                        </label>
                    </div>
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="mother">Ibu Kandung
                </label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                    <input type="text" class="form-control" id="mother" name="mother"
                        placeholder="Nama Ibu Kandung" value="{{ old('mother') }}">
                </div>
                <div class="col-sm-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="isUnknownMother" name="isUnknownMother"
                            value="true" aria-label="..." @if(old('isUnknownMother') == 'true') checked @endif>
                        <label for="isUnknownMother">
                            Tidak Tahu
                        </label>
                    </div>
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="nationality">Kewarganegaraan<span class="text-danger fs-5">*</span>
                </label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                    <select class="form-control select2" id="nationality" name="nationality">
                        <option value="">--Pilih Kewarganegaraan--</option>
                        @foreach ($nationalities as $nationality)
                            <option value="{{ $nationality->id }}"
                                data-nationality-name="{{ $nationality->name }}"
                                @if(old('nationality') == $nationality->id) selected @endif>
                                {{ $nationality->name }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- <div class="col-sm-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="isUnknownNationality" name="isUnknownNationality"
                            value="true" aria-label="..." @if(old('isUnknownNationality') == 'true') checked @endif>
                        <label for="isUnknownNationality">
                            Tidak Tahu
                        </label>
                    </div>
                </div> --}}
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="ethnic">Suku<span class="text-danger fs-5">*</span> </label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                    <select class="form-control select2" id="ethnic" name="ethnic">
                        <option value="">--Pilih Suku--</option>
                        @foreach ($ethnics as $ethnic)
                            <option value="{{ $ethnic->id }}" data-ethnic-name="{{ $ethnic->name }}" 
                                @if(old('ethnic') == $ethnic->id) selected @endif>
                                {{ $ethnic->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="job">Pekerjaan<span class="text-danger fs-5">*</span> </label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                    <select class="form-control select2" id="job" name="job">
                        <option value="">--Pilih Pekerjaan--</option>
                        @foreach ($jobs as $job)
                            <option value="{{ $job->id }}" data-job-name="{{ $job->name }}"
                                @if(old('job') == $job->id) selected @endif>
                                {{ $job->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="religion">Agama<span class="text-danger fs-5">*</span> </label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                    <select class="form-control select2" id="religion" name="religion">
                        <option value="">--Pilih Agama--</option>
                        @foreach ($religions as $religion)
                            <option value="{{ $religion->id }}" data-religion-name="{{ $religion->name }}"
                                @if(old('religion') == $religion->id) selected @endif>
                                {{ $religion->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="education">Pendidikan<span class="text-danger fs-5">*</span>
                </label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                    <select class="form-control select2" id="education" name="education">
                        <option value="">--Pilih Pendidikan--</option>
                        @foreach ($educations as $education)
                            <option value="{{ $education->id }}"
                                data-education-name="{{ $education->name }}"
                                @if(old('education') == $education->id) selected @endif>
                                {{ $education->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="maritalStatus">Status Kawin<span class="text-danger fs-5">*</span>
                </label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                    <select class="form-control select2" id="maritalStatus" name="maritalStatus">
                        <option value="">--Pilih Status Kawin--</option>
                        @foreach ($maritalStatuses as $maritalStatus)
                            <option value="{{ $maritalStatus->id }}"
                                data-marital-status-name="{{ $maritalStatus->name }}"
                                @if(old('maritalStatus') == $maritalStatus->id) selected @endif>
                                {{ $maritalStatus->name }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- <div class="col-sm-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                            id="isUnknownMaritalStatus" name="isUnknownMaritalStatus"
                            value="true" aria-label="..." @if(old('isUnknownMaritalStatus') == 'true') checked @endif>
                        <label for="isUnknownMaritalStatus">
                            Tidak Tahu
                        </label>
                    </div>
                </div> --}}
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="phoneNumber">Nomor Telepon
                </label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                    <div class="d-flex mb-2">
                        <div class="form-check m-1">
                            <input class="form-check-input" type="radio"
                                id="existsPhoneNumber"
                                name="isExistsPhoneNumber" value="true" @if(old('isExistsPhoneNumber') == 'true') checked @elseif(empty(old('isExistsPhoneNumber'))) checked @endif>
                            <label for="existsPhoneNumber">
                                Ada Nomor Telepon
                            </label>
                        </div>

                        <div class="form-check m-1">
                            <input class="form-check-input" type="radio"
                                id="notExistsPhoneNumber"
                                name="isExistsPhoneNumber" value="false" @if(old('isExistsPhoneNumber') == 'false') checked @endif>
                            <label for="notExistsPhoneNumber">
                                Tidak ada Nomor Telepon
                            </label>
                        </div>
                    </div>

                    <input type="text" class="form-control mb-2" id="phoneNumber" name="phoneNumber"
                        placeholder="Nomor Telepon" value="{{ old('phoneNumber') }}">

                    <div class="form-check m-1">
                        <input class="form-check-input" type="checkbox"
                            id="isAvailablePhoneNumber" name="isAvailablePhoneNumber" 
                            value="true" aria-label="..." @if(old('isAvailablePhoneNumber') == 'true') checked @elseif(empty(old('isAvailablePhoneNumber'))) checked @endif>
                        <label for="isAvailablePhoneNumber">
                            Bersedia memberikan nomor telepon?
                        </label>
                    </div>
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="email">Email</label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                    <div class="d-flex mb-3">
                        <div class="form-check m-1">
                            <input class="form-check-input" type="radio" id="existsEmail"
                                name="isExistsEmail" value="true" @if(old('isExistsEmail') == 'true') checked @elseif(empty(old('isExistsEmail'))) checked @endif>
                            <label for="existsEmail">
                                Ada Email
                            </label>
                        </div>

                        <div class="form-check m-1">
                            <input class="form-check-input" type="radio" id="notExistsEmail"
                                name="isExistsEmail" value="false" @if(old('isExistsEmail') == 'false') checked @endif>
                            <label for="notExistsEmail">
                                Tidak ada Email
                            </label>
                        </div>
                    </div>

                    <input type="text" class="form-control mb-2" id="email" name="email"
                        placeholder="Email" value="{{ old('email') }}">

                    <div class="form-check m-1">
                        <input class="form-check-input" type="checkbox" id="isAvailableEmail" name="isAvailableEmail"
                            value="true" aria-label="..." @if(old('isAvailableEmail') == 'true') checked @elseif(empty(old('isAvailableEmail'))) checked @endif>
                        <label for="isAvailableEmail">
                            Bersedia memberikan email?
                        </label>
                    </div>
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="country">Negara<span class="text-danger fs-5">*</span> </label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                    <select class="form-control select2" id="country" name="country">
                        <option value="">--Pilih Negara--</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}" data-country-name="{{ $country->name }}"
                             @if(old('country') == $country->id) selected @endif>
                                {{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="countryChildrenLocationSection" style="display:none;">
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="province">Provinsi<span class="text-danger fs-5">*</span>
                    </label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" id="province" name="province">
                            <option value="">--Pilih Provinsi--</option>
                        </select>
                    </div>
                </div>
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="regency">Kabupaten/Kota<span class="text-danger fs-5">*</span>
                    </label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" id="regency" name="regency">
                            <option value="">--Pilih Kabupaten/Kota--</option>
                        </select>
                    </div>
                </div>
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="district">Kecamatan<span class="text-danger fs-5">*</span>
                    </label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" id="district" name="district">
                            <option value="">--Pilih Kecamatan--</option>
                        </select>
                    </div>
                </div>
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="village">Kelurahan/Desa<span class="text-danger fs-5">*</span>
                    </label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" id="village" name="village">
                            <option value="">--Pilih Kelurahan/Desa--</option>
                        </select>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="subVillage">Kampung</label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                        <input type="text" class="form-control" id="subVillage" name="subVillage"
                            placeholder="Kampung (Opsional)" value="{{ old('subVillage') }}">
                    </div>
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="address">Alamat<span class="text-danger fs-5">*</span> </label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                    <input type="text" class="form-control" id="address" name="address" placeholder="Alamat" value="{{ old('address') }}">
                </div>
                {{-- <div class="col-sm-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="isUnknownAddress" name="isUnknownAddress"
                            value="true" aria-label="..." @if(old('isUnknownAddress') == 'true') checked @endif>
                        <label for="isUnknownAddress">
                            Tidak Tahu
                        </label>
                    </div>
                </div> --}}
            </div>

            <br/>
            <hr/>
            
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary" id="participantPersonFormSubmit">
                    <i class="bi bi-save"></i> <span id="btnSimpanLabel">Simpan</span>
                </button>
                <a href="{{route('view_produktivitas_accident', ['accident_id' => request()->query('accident_id'), 'page'=>'participants'])}}" class="btn btn-danger">
                    <i class="bi bi-x-circle"></i> {{ __('Batal') }}
                </a>
            </div>
        </form>

    </div>
</div>
@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

    <script type="text/javascript">
        // Update teks button Simpan saat dropdown Jenis Pihak berubah
        function updateBtnLabel() {
            var val = $('#jenisPihak').val();
            if (val === 'PELAPOR') {
                $('#btnSimpanLabel').text('Simpan Pelapor');
            } else if (val === 'TERLAPOR') {
                $('#btnSimpanLabel').text('Simpan Terlapor');
            } else {
                $('#btnSimpanLabel').text('Simpan');
            }
        }

        $(document).ready(function() {
            // Select2 with Bootstrap4 theme
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

            // Listener perubahan Jenis Pihak
            $('#jenisPihak').on('change select2:select', updateBtnLabel);
            updateBtnLabel(); // set on page load

            // Listener perubahan Jenis Identitas untuk Maxlength
            function updateIdentityNumberLimits() {
                var idType = $('#identityType option:selected').data('identity-type-name') || '';
                var $idNum = $('#identityNumber');
                
                if (idType.includes('KTP') || idType.includes('Kartu Keluarga')) {
                    $idNum.attr('maxlength', 16);
                } else if (idType.includes('SIM')) {
                    $idNum.attr('maxlength', 16);
                } else if (idType.includes('Paspor')) {
                    $idNum.attr('maxlength', 9);
                } else {
                    $idNum.removeAttr('maxlength');
                }

                var max = $idNum.attr('maxlength');
                if (max) {
                    var val = $idNum.val();
                    if (val && val.length > parseInt(max)) {
                        $idNum.val(val.substring(0, parseInt(max)));
                    }
                }
            }

            $(document).on('change select2:select', '#identityType', function() {
                updateIdentityNumberLimits();
            });

            $(document).on('input keyup paste', '#identityNumber', function() {
                var idType = $('#identityType option:selected').data('identity-type-name') || '';
                var val = $(this).val();

                if (idType.includes('KTP') || idType.includes('Kartu Keluarga') || idType.includes('SIM')) {
                    var cleanVal = val.replace(/\D/g, '');
                    if (cleanVal !== val) {
                        $(this).val(cleanVal);
                    }
                } else if (idType.includes('Paspor')) {
                    var cleanVal = val.replace(/[^a-zA-Z0-9]/g, '');
                    if (cleanVal !== val) {
                        $(this).val(cleanVal);
                    }
                }
            });

            updateIdentityNumberLimits(); // set on page load

            // datepicker
            $('#birthDate').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: "true",
                endDate: new Date(),
            });
            $('#birthDate').keydown(function(e) {
                e.preventDefault();
                return false;
            });
        });

        $('#country').on('change', function() {
            var countryId = $(this).find(':selected').val();

            //if indonesia show province, regency, district, village
            if (countryId == 'C101') {
                $('.countryChildrenLocationSection').show();
            } else {
                $('.countryChildrenLocationSection').hide();
            }
        });

        $(document).on('change', '#country', function() {
            var parentId = $(this).find(':selected').val();
            getProvince(parentId);
        });

        function getProvince(parentId, provinceId = null) {
            //province get from ajax
            var parentId = parentId;
            var provinceId = provinceId;
            var classCode = 'PROVINCE';

            $.ajax({
                url: "{{ route('case.participant.person.api.locations', ['accident_id' => $accidentId, 'accidentId' => $accidentId]) }}", // Replace with your backend URL
                type: 'GET',
                dataType: 'json',
                data: {
                    'parent_id': parentId,
                    'class': classCode,
                },
                success: function(response) {
                    var data = response.data;
                    // Clear existing options and add new options
                    var province = $('#province');
                    province.empty().append($('<option>', {
                        value: '',
                        text: '--Pilih Provinsi--'
                    }));
                    $.each(data, function(index, data) {
                        province.append($('<option>', {
                            value: data.id,
                            text: data.name,
                            'data-parent-id': data.parent_id,
                            'data-class': data.class,
                        }));
                    });

                    $('#province').val(provinceId).trigger('change');
                },
                error: function(xhr) {
                    // Handle error if needed
                    console.log(xhr.responseText);
                }
            });
        }

        $(document).on('change', '#province', function() {
            var parentId = $(this).find(':selected').val();
            getRegency(parentId);
        });

        function getRegency(parentId, regencyId = null) {
            //regency get from ajax
            var parentId = parentId;
            var regencyId = regencyId;
            var classCode = 'REGENCY';

            $.ajax({
                url: "{{ route('case.participant.person.api.locations', ['accident_id' => $accidentId, 'accidentId' => $accidentId]) }}", // Replace with your backend URL
                type: 'GET',
                dataType: 'json',
                data: {
                    'parent_id': parentId,
                    'class': classCode,
                },
                success: function(response) {
                    var data = response.data;
                    // Clear existing options and add new options
                    var regency = $('#regency');
                    regency.empty().append($('<option>', {
                        value: '',
                        text: '--Pilih Kabupaten/Kota--'
                    }));
                    $.each(data, function(index, data) {
                        regency.append($('<option>', {
                            value: data.id,
                            text: data.name,
                            'data-parent-id': data.parent_id,
                            'data-class': data.class,
                        }));
                    });

                    $('#regency').val(regencyId).trigger('change');
                },
                error: function(xhr) {
                    // Handle error if needed
                    console.log(xhr.responseText);
                }
            });
        }

        $(document).on('change', '#regency', function() {
            var parentId = $(this).find(':selected').val();
            getDistrict(parentId);
        });

        function getDistrict(parentId, districtId = null) {
            //district get from ajax
            var parentId = parentId;
            var districtId = districtId;
            var classCode = 'DISTRICT';

            $.ajax({
                url: "{{ route('case.participant.person.api.locations', ['accident_id' => $accidentId, 'accidentId' => $accidentId]) }}", // Replace with your backend URL
                type: 'GET',
                dataType: 'json',
                data: {
                    'parent_id': parentId,
                    'class': classCode,
                },
                success: function(response) {
                    var data = response.data;
                    // Clear existing options and add new options
                    var district = $('#district');
                    district.empty().append($('<option>', {
                        value: '',
                        text: '--Pilih Kecamatan--'
                    }));
                    $.each(data, function(index, data) {
                        district.append($('<option>', {
                            value: data.id,
                            text: data.name,
                            'data-parent-id': data.parent_id,
                            'data-class': data.class,
                        }));
                    });

                    $('#district').val(districtId).trigger('change');
                },
                error: function(xhr) {
                    // Handle error if needed
                    console.log(xhr.responseText);
                }
            });
        }

        $(document).on('change', '#district', function() {
            var parentId = $(this).find(':selected').val();
            getVillage(parentId);
        });

        function getVillage(parentId, villageId = null) {
            //village get from ajax
            var parentId = parentId;
            var villageId = villageId;
            var classCode = 'VILLAGE';

            $.ajax({
                url: "{{ route('case.participant.person.api.locations', ['accident_id' => $accidentId, 'accidentId' => $accidentId]) }}", // Replace with your backend URL
                type: 'GET',
                dataType: 'json',
                data: {
                    'parent_id': parentId,
                    'class': classCode,
                },
                success: function(response) {
                    var data = response.data;
                    // Clear existing options and add new options
                    var village = $('#village');
                    village.empty().append($('<option>', {
                        value: '',
                        text: '--Pilih Kelurahan/Desa--'
                    }));
                    $.each(data, function(index, data) {
                        village.append($('<option>', {
                            value: data.id,
                            text: data.name,
                            'data-parent-id': data.parent_id,
                            'data-class': data.class,
                        }));
                    });

                    $('#village').val(villageId).trigger('change');
                },
                error: function(xhr) {
                    // Handle error if needed
                    console.log(xhr.responseText);
                }
            });
        }

        //tidak tahu checked
        $('#isUnknownGender').on('change', function() {
            if ($(this).is(':checked')) {
                $('#gender').val('').trigger('change');
                $('#gender').prop('disabled', true);
            } else {
                $('#gender').prop('disabled', false);
            }
        });
        $('#isUnknownBirthPlace').on('change', function() {
            if ($(this).is(':checked')) {
                $('#birthPlace').val('TIDAK DIKETAHUI');
                $('#birthPlace').prop('readonly', true);
            } else {
                $('#birthPlace').val('');
                $('#birthPlace').prop('readonly', false);
            }
        });
        $('#isUnknownBirthDate').on('change', function() {
            if ($(this).is(':checked')) {
                $('#birthDate').val('');
                $('#birthDate').prop('disabled', true);
            } else {
                $('#birthDate').prop('disabled', false);
            }
        });
        $('#isUnknownFather').on('change', function() {
            if ($(this).is(':checked')) {
                $('#father').val('TIDAK DIKETAHUI');
                $('#father').prop('readonly', true);
            } else {
                if ($('#father').val() === 'TIDAK DIKETAHUI') {
                    $('#father').val('');
                }
                $('#father').prop('readonly', false);
            }
            $('#father').removeClass('is-invalid');
            $('#father').closest('.col-lg-8, .col-md-8, .col-sm-12, .col-12').find('.frontend-error, .invalid-feedback').remove();
        });
        $('#isUnknownMother').on('change', function() {
            if ($(this).is(':checked')) {
                $('#mother').val('TIDAK DIKETAHUI');
                $('#mother').prop('readonly', true);
            } else {
                if ($('#mother').val() === 'TIDAK DIKETAHUI') {
                    $('#mother').val('');
                }
                $('#mother').prop('readonly', false);
            }
            $('#mother').removeClass('is-invalid');
            $('#mother').closest('.col-lg-8, .col-md-8, .col-sm-12, .col-12').find('.frontend-error, .invalid-feedback').remove();
        });
        $('#isUnknownMaritalStatus').on('change', function() {
            if ($(this).is(':checked')) {
                $('#maritalStatus').val('').trigger('change');
                $('#maritalStatus').prop('disabled', true);
            } else {
                $('#maritalStatus').prop('disabled', false);
            }
            $('#maritalStatus').removeClass('is-invalid');
            $('#maritalStatus').siblings('.select2-container').find('.select2-selection').removeClass('border border-danger is-invalid');
            $('#maritalStatus').closest('.col-lg-8, .col-md-8, .col-sm-12, .col-12').find('.frontend-error, .invalid-feedback').remove();
        });
        $('#isUnknownAddress').on('change', function() {
            if ($(this).is(':checked')) {
                $('#address').val("TIDAK DIKETAHUI");
                $('#address').prop('readonly', true);
            } else {
                if ($('#address').val() === 'TIDAK DIKETAHUI') {
                    $('#address').val('');
                }
                $('#address').prop('readonly', false);
            }
            $('#address').removeClass('is-invalid');
            $('#address').closest('.col-lg-8, .col-md-8, .col-sm-12, .col-12').find('.frontend-error, .invalid-feedback').remove();
        });

        //phone and email
        $('input[name="isExistsPhoneNumber"]').on('change', function() {
            var isExistsPhoneNumber = $('input[name="isExistsPhoneNumber"]:checked').val();
            if (isExistsPhoneNumber == 'true') {
                $('#isAvailablePhoneNumber').prop('disabled', false);
                $('#phoneNumber').prop('readonly', false);
                if ($('#phoneNumber').val() === 'TIDAK ADA NOMOR TELEPON') {
                    $('#phoneNumber').val('');
                }
            } else {
                $('#isAvailablePhoneNumber').prop('disabled', true);
                $('#isAvailablePhoneNumber').prop('checked', false);
                $('#phoneNumber').val('TIDAK ADA NOMOR TELEPON');
                $('#phoneNumber').prop('readonly', true);
            }
            $('input[name="isExistsPhoneNumber"]').removeClass('is-invalid');
            $('#phoneNumber').removeClass('is-invalid');
            $('#phoneNumber').closest('.col-lg-8, .col-md-8, .col-sm-12, .col-12').find('.frontend-error, .invalid-feedback').remove();
        });
        $('#isAvailablePhoneNumber').on('change', function() {
            if ($(this).is(':checked')) {
                if ($('#phoneNumber').val() === 'TIDAK BERSEDIA MEMBERIKAN NOMOR TELEPON') {
                    $('#phoneNumber').val('');
                }
                $('#phoneNumber').prop('readonly', false);
            } else {
                $('#phoneNumber').prop('readonly', true);
                $('#phoneNumber').val('TIDAK BERSEDIA MEMBERIKAN NOMOR TELEPON');
            }
            $('#phoneNumber').removeClass('is-invalid');
            $('#phoneNumber').closest('.col-lg-8, .col-md-8, .col-sm-12, .col-12').find('.frontend-error, .invalid-feedback').remove();
        });
        $('input[name="isExistsEmail"]').on('change', function() {
            var isExistsEmail = $('input[name="isExistsEmail"]:checked').val();
            if (isExistsEmail == 'true') {
                $('#isAvailableEmail').prop('disabled', false);
                $('#email').prop('readonly', false);
                if ($('#email').val() === 'TIDAK ADA EMAIL') {
                    $('#email').val('');
                }
            } else {
                $('#isAvailableEmail').prop('disabled', true);
                $('#isAvailableEmail').prop('checked', false);
                $('#email').val('TIDAK ADA EMAIL');
                $('#email').prop('readonly', true);
            }
            $('input[name="isExistsEmail"]').removeClass('is-invalid');
            $('#email').removeClass('is-invalid');
            $('#email').closest('.col-lg-8, .col-md-8, .col-sm-12, .col-12').find('.frontend-error, .invalid-feedback').remove();
        });
        $('#isAvailableEmail').on('change', function() {
            if ($(this).is(':checked')) {
                if ($('#email').val() === 'TIDAK BERSEDIA MEMBERIKAN EMAIL') {
                    $('#email').val('');
                }
                $('#email').prop('readonly', false);
            } else {
                $('#email').prop('readonly', true);
                $('#email').val('TIDAK BERSEDIA MEMBERIKAN EMAIL');
            }
            $('#email').removeClass('is-invalid');
            $('#email').closest('.col-lg-8, .col-md-8, .col-sm-12, .col-12').find('.frontend-error, .invalid-feedback').remove();
        });

        // ==========================================
        // VALIDASI FORMAT & FIELD (PERSIS SEPERTI LHGP)
        // ==========================================
        function validateIdentityNumber() {
            var $field = $('#identityNumber');
            var identityTypeId = $('#identityType').val();
            var identityTypeName = ($('#identityType').find(':selected').data('identity-type-name') || $('#identityType').find(':selected').text() || '').toUpperCase();
            var val = ($field.val() || '').trim();
            var errorMsg = '';

            if ($field.is(':disabled') || val === '') {
                return null;
            }

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

            $field.parent().find('.frontend-error, .invalid-feedback').remove();

            if (errorMsg) {
                $field.addClass('is-invalid');
                $field.after('<div class="invalid-feedback d-block frontend-error font-weight-bold mt-1 text-danger">' + errorMsg + '</div>');
                return errorMsg;
            } else {
                $field.removeClass('is-invalid');
                return null;
            }
        }

        function validatePhone() {
            var $field = $('#phoneNumber');
            var val = ($field.val() || '').trim();
            if ($field.is(':disabled') || val === 'TIDAK ADA NOMOR TELEPON' || val === 'TIDAK BERSEDIA MEMBERIKAN NOMOR TELEPON') {
                $field.removeClass('is-invalid');
                $field.parent().find('.frontend-error, .invalid-feedback').remove();
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

            $field.parent().find('.frontend-error, .invalid-feedback').remove();

            if (errorMsg) {
                $field.addClass('is-invalid');
                $field.after('<div class="invalid-feedback d-block frontend-error font-weight-bold mt-1 text-danger">' + errorMsg + '</div>');
                return errorMsg;
            } else {
                $field.removeClass('is-invalid');
                return null;
            }
        }

        function validateEmail() {
            var $field = $('#email');
            var val = ($field.val() || '').trim();
            if ($field.is(':disabled') || val === 'TIDAK ADA EMAIL' || val === 'TIDAK BERSEDIA MEMBERIKAN EMAIL') {
                $field.removeClass('is-invalid');
                $field.parent().find('.frontend-error, .invalid-feedback').remove();
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

            $field.parent().find('.frontend-error, .invalid-feedback').remove();

            if (errorMsg) {
                $field.addClass('is-invalid');
                $field.after('<div class="invalid-feedback d-block frontend-error font-weight-bold mt-1 text-danger">' + errorMsg + '</div>');
                return errorMsg;
            } else {
                $field.removeClass('is-invalid');
                return null;
            }
        }

        function validateBirthDate() {
            var $field = $('#birthDate');
            if ($field.is(':disabled')) {
                $field.removeClass('is-invalid');
                $field.parent().find('.frontend-error, .invalid-feedback').remove();
                return null;
            }

            var val = ($field.val() || '').trim();
            var errorMsg = '';

            if (val === '') {
                errorMsg = 'Tanggal Lahir harus diisi.';
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

            $field.parent().find('.frontend-error, .invalid-feedback').remove();

            if (errorMsg) {
                markError('#birthDate', errorMsg);
                return errorMsg;
            } else {
                $field.removeClass('is-invalid');
                return null;
            }
        }

        var formErrors = [];

        function markError(fieldSelector, message) {
            var $field = $(fieldSelector);
            if (!$field.length) return;

            $field.addClass('is-invalid');
            var $parentCol = $field.closest('.col-lg-8, .col-md-8, .col-sm-12, .col-12');
            if ($parentCol.length) {
                $parentCol.addClass('flex-wrap');
                $parentCol.find('.frontend-error, .invalid-feedback').remove();
            }

            var errorHtml = '<div class="invalid-feedback d-block frontend-error font-weight-bold mt-1 text-danger" style="width: 100%; font-size: 0.85rem;">' + message + '</div>';

            if ($field.is(':radio')) {
                var $container = $field.closest('.d-flex');
                if ($container.next('.frontend-error').length === 0) {
                    $container.after(errorHtml);
                }
            } else if ($field.siblings('.select2-container').length) {
                $field.siblings('.select2-container').find('.select2-selection').addClass('border border-danger is-invalid');
                if ($field.siblings('.select2-container').next('.frontend-error').length === 0) {
                    $field.siblings('.select2-container').after(errorHtml);
                }
            } else {
                if ($field.next('.frontend-error').length === 0) {
                    $field.after(errorHtml);
                }
            }

            if (!formErrors.includes(message)) {
                formErrors.push(message);
            }
        }

        function checkInput(fieldSelector, label) {
            var $field = $(fieldSelector);
            if (!$field.length || $field.is(':disabled') || $field.closest('.row, .input-group').is(':hidden')) return;
            var val = ($field.val() || '').trim();
            if (!val || val === '') {
                markError(fieldSelector, label + ' harus diisi');
            }
        }

        function checkSelect(fieldSelector, label) {
            var $field = $(fieldSelector);
            if (!$field.length || $field.is(':disabled') || $field.closest('.row, .input-group').is(':hidden')) return;
            var val = $field.val();
            if (!val || val === '' || val === null || val === '0') {
                markError(fieldSelector, label + ' harus dipilih');
            }
        }

        // Realtime event listeners format
        $('#identityNumber').on('input keyup change blur', function() {
            var val = ($(this).val() || '').trim();
            if (val !== '') {
                validateIdentityNumber();
            } else {
                $(this).removeClass('is-invalid');
                $(this).parent().find('.frontend-error, .invalid-feedback').remove();
            }
        });

        $('#identityType').on('change select2:select', function() {
            var val = $('#identityNumber').val() || '';
            if (val !== '') {
                validateIdentityNumber();
            }
        });

        $('#phoneNumber').on('input keyup change blur', function() {
            var val = ($(this).val() || '').trim();
            if (val !== '' && val !== 'TIDAK ADA NOMOR TELEPON' && val !== 'TIDAK BERSEDIA MEMBERIKAN NOMOR TELEPON') {
                validatePhone();
            }
        });

        $('#email').on('input keyup change blur', function() {
            var val = ($(this).val() || '').trim();
            if (val !== '' && val !== 'TIDAK ADA EMAIL' && val !== 'TIDAK BERSEDIA MEMBERIKAN EMAIL') {
                validateEmail();
            }
        });

        $('#birthDate').on('change changeDate input blur', function() {
            var val = ($(this).val() || '').trim();
            if (val !== '') {
                validateBirthDate();
            }
        });

        // Auto-clear realtime saat user mengisi field
        $(document).on('input keyup change', 'input:not(#identityNumber, #phoneNumber, #email, #birthDate), textarea', function() {
            var $field = $(this);
            if (($field.val() || '').trim() !== '') {
                $field.removeClass('is-invalid');
                $field.parent().find('.frontend-error, .invalid-feedback').remove();
            }
        });

        $(document).on('change select2:select', 'select', function() {
            var $field = $(this);
            var val = $field.val();
            if (val && val !== '' && val !== '0') {
                $field.removeClass('is-invalid');
                $field.siblings('.select2-container').find('.select2-selection').removeClass('border border-danger is-invalid');
                $field.parent().find('.frontend-error, .invalid-feedback').remove();
            }
        });

        $(document).on('change', 'input[type="radio"]', function() {
            var name = $(this).attr('name');
            $('input[name="' + name + '"]').removeClass('is-invalid');
            $('input[name="' + name + '"]').closest('.col-lg-8, .col-md-8, .col-sm-12, .col-12').find('.frontend-error, .invalid-feedback').remove();
        });

        function scrollToFirstError() {
            var $firstInvalid = $('.is-invalid:visible, .select2-selection.border-danger:visible, .frontend-error:visible').first();
            if (!$firstInvalid.length) {
                $firstInvalid = $('.frontend-error').first();
            }

            if ($firstInvalid.length) {
                var elem = $firstInvalid[0];

                // 1. Native scrollIntoView agar otomatis mencari container yang scrollable
                try {
                    elem.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } catch (e) {
                    elem.scrollIntoView(true);
                }

                // 2. Animate container .content (karena style3x.css memakai .content overflow: auto)
                var $content = $('.content');
                if ($content.length) {
                    var currentScroll = $content.scrollTop();
                    var contentTop = $content.offset().top;
                    var elemTop = $firstInvalid.offset().top;
                    var targetScroll = currentScroll + (elemTop - contentTop) - 80;
                    if (targetScroll < 0) targetScroll = 0;

                    $content.stop().animate({ scrollTop: targetScroll }, 350);
                }
                $('html, body').stop().animate({ scrollTop: Math.max(0, $firstInvalid.offset().top - 80) }, 350);

                // 3. Fokuskan kursor ke input yang salah
                setTimeout(function() {
                    if (typeof elem.focus === 'function' && !$firstInvalid.hasClass('select2-selection') && !$firstInvalid.hasClass('frontend-error')) {
                        try { elem.focus({ preventScroll: true }); } catch (err) {}
                    } else if ($firstInvalid.hasClass('select2-selection')) {
                        $firstInvalid.closest('.select2-container').prev('select').select2('open');
                    }
                }, 150);
            }
        }

        // Submit Form Handler
        $(document).ready(function() {
            $('#participantPersonFormSubmit').on('click', function(e) {
                e.preventDefault();

                // 1. Bersihkan error sebelumnya
                $('.is-invalid').removeClass('is-invalid');
                $('.select2-selection').removeClass('border border-danger is-invalid');
                $('.frontend-error, .invalid-feedback, small.text-danger').remove();
                formErrors = [];

                // 2. Validasi Jenis Pihak
                checkSelect('#jenisPihak', 'Jenis Pihak');

                // 3. Jenis & Nomor Identitas
                checkSelect('#identityType', 'Jenis Identitas');
                checkInput('#identityNumber', 'Nomor Identitas');
                var idErr = validateIdentityNumber();
                if (idErr) markError('#identityNumber', idErr);

                // 4. Nama Lengkap
                checkInput('#name', 'Nama Lengkap');

                // 5. Jenis Kelamin
                checkSelect('#gender', 'Jenis Kelamin');

                // 6. Tempat Lahir
                checkInput('#birthPlace', 'Tempat Lahir');

                // 7. Tanggal Lahir
                validateBirthDate();

                // 8. Ayah Kandung
                if (!$('#isUnknownFather').is(':checked')) {
                    checkInput('#father', 'Nama Ayah Kandung');
                }

                // 9. Ibu Kandung
                if (!$('#isUnknownMother').is(':checked')) {
                    checkInput('#mother', 'Nama Ibu Kandung');
                }

                // 10. Data Pelengkap
                checkSelect('#nationality', 'Kewarganegaraan');
                checkSelect('#ethnic', 'Suku');
                checkSelect('#job', 'Pekerjaan');
                checkSelect('#religion', 'Agama');
                checkSelect('#education', 'Pendidikan');
                checkSelect('#maritalStatus', 'Status Perkawinan');

                // 11. Nomor Telepon
                var phoneRadioVal = $('input[name="isExistsPhoneNumber"]:checked').val();
                if (!phoneRadioVal) {
                    markError('input[name="isExistsPhoneNumber"]', 'Pilihan nomor telepon harus dipilih');
                } else if (phoneRadioVal === 'true' && $('#isAvailablePhoneNumber').is(':checked')) {
                    checkInput('#phoneNumber', 'Nomor Telepon');
                    var phErr = validatePhone();
                    if (phErr) markError('#phoneNumber', phErr);
                }

                // 12. Email
                var emailRadioVal = $('input[name="isExistsEmail"]:checked').val();
                if (!emailRadioVal) {
                    markError('input[name="isExistsEmail"]', 'Pilihan email harus dipilih');
                } else if (emailRadioVal === 'true' && $('#isAvailableEmail').is(':checked')) {
                    checkInput('#email', 'Email');
                    var emErr = validateEmail();
                    if (emErr) markError('#email', emErr);
                }

                // 13. Negara & Wilayah
                checkSelect('#country', 'Negara');
                if ($('.countryChildrenLocationSection').is(':visible') || $('#country').val() === 'C101') {
                    checkSelect('#province', 'Provinsi');
                    checkSelect('#regency', 'Kabupaten/Kota');
                    checkSelect('#district', 'Kecamatan');
                    checkSelect('#village', 'Kelurahan/Desa');
                }

                // 14. Alamat
                if (!$('#isUnknownAddress').is(':checked')) {
                    checkInput('#address', 'Alamat');
                }

                // 15. Scroll ke error pertama jika ada error
                if (formErrors.length > 0) {
                    scrollToFirstError();
                    return false;
                }

                // 16. Server-side validation via AJAX
                $.ajax({
                    url: "{{ route('case.participant.person.api.validate-request-form', ['accidentId' => $accidentId, 'accident_id' => request()->query('accident_id')]) }}",
                    type: 'POST',
                    dataType: 'json',
                    data: $('#participantPersonForm').serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                $('#participantPersonForm').submit();
                            });
                        }
                    },
                    error: function(xhr) {
                        var response = {};
                        try { response = JSON.parse(xhr.responseText); } catch(e) {}

                        if (xhr.status === 422 || (response && response.code == '422')) {
                            var errors = response.errors || {};
                            $.each(errors, function(key, messages) {
                                var msg = Array.isArray(messages) ? messages[0] : messages;
                                var fieldSelector = '#' + key;
                                if (!$(fieldSelector).length) fieldSelector = '[name="' + key + '"]';
                                markError(fieldSelector, msg);
                            });

                            scrollToFirstError();
                        }
                    }
                });
            });
        });
    </script>
@endpush
