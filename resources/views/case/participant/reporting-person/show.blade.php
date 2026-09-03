@php
    $_title = 'Lihat Pelapor';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
@endpush

@section('content')
<a class="btn-back" href="{{route('view_produktivitas_accident', ['accident_id' => request()->query('accident_id'), 'page'=>'participants'])}}"><i class="bi bi-arrow-left"></i>Kembali ke Halaman Pihak Terlibat</a>

<div class="box">
    <div class="box-header">
        <h4 class="fw-bold text-blue-dark">Lihat Pelapor</h4>

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
            action="{{ route('case.participant.reporting-person.update', ['id'=> $id, 'accidentId' => $accidentId, 'accident_id' => request()->query('accident_id')]) }}"
            method="POST" enctype="multipart/form-data" id="reportingPersonForm">
            @csrf

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
                                @if(old('identityType', $reportingPerson->identity_type_id) == $identityType->id) selected @endif>
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
                        value="{{ old('identityNumber', $reportingPerson->identity_number) }}"
                        placeholder="Nomor Identitas">
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="name">Nama<span class="text-danger fs-5">*</span></label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                    <input type="text" class="form-control" id="name" name="name"
                        value="{{ old('name', $reportingPerson->name) }}"
                        placeholder="Nama Lengkap">
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="aliasName">Nama Alias
                </label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                    <input type="text" class="form-control" id="aliasName" name="aliasName"
                        value="{{ old('aliasName', $reportingPerson->alias_name ?? $reportingPerson->name_alias) }}"
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
                                @if(old('gender', $reportingPerson->gender_id) == $gender->id) selected @endif>
                                {{ $gender->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="isUnknownGender" name="isUnknownGender"
                            value="true" aria-label="..." @if(old('isUnknownGender', var_export($reportingPerson->is_unknown_gender, true)) == 'true') checked @endif>
                        <label for="isUnknownGender">
                            Tidak Tahu
                        </label>
                    </div>
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="birthPlace">Tempat Lahir<span class="text-danger fs-5">*</span>
                </label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                    <input type="text" class="form-control" id="birthPlace" name="birthPlace"
                        placeholder="Tempat Lahir" value="{{ old('birthPlace', $reportingPerson->birth_place) }}">
                </div>
                <div class="col-sm-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                            id="isUnknownBirthPlace" name="isUnknownBirthPlace"
                            value="true" aria-label="..." @if(old('isUnknownBirthPlace', var_export($reportingPerson->is_unknown_birth_place, true)) == 'true') checked @endif>
                        <label for="isUnknownBirthPlace">
                            Tidak Tahu
                        </label>
                    </div>
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="birthDate">Tanggal Lahir<span class="text-danger fs-5">*</span>
                </label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                    <input type="text" class="form-control" id="birthDate" name="birthDate"
                        placeholder="YYYY-MM-DD" data-provide="datepicker" value="{{ old('birthDate', $reportingPerson->birth_date) }}">
                </div>
                <div class="col-sm-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                            id="isUnknownBirthDate" name="isUnknownBirthDate"
                            value="true" aria-label="..." @if(old('isUnknownBirthDate', var_export($reportingPerson->is_unknown_birth_date, true)) == 'true') checked @endif>
                        <label for="isUnknownBirthDate">
                            Tidak Tahu
                        </label>
                    </div>
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="father">Ayah Kandung
                </label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                    <input type="text" class="form-control" id="father" name="father"
                        placeholder="Nama Ayah Kandung" value="{{ old('father', $reportingPerson->father_name) }}">
                </div>
                <div class="col-sm-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="isUnknownFather" name="isUnknownFather"
                            value="true" aria-label="..." @if(old('isUnknownFather', var_export($reportingPerson->is_unknown_father, true)) == 'true') checked @endif>
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
                        placeholder="Nama Ibu Kandung" value="{{ old('mother', $reportingPerson->mother_name) }}">
                </div>
                <div class="col-sm-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="isUnknownMother" name="isUnknownMother"
                            value="true" aria-label="..." @if(old('isUnknownMother', var_export($reportingPerson->is_unknown_mother, true)) == 'true') checked @endif>
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
                                @if(old('nationality', $reportingPerson->nationality_id) == $nationality->id) selected @endif>
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
                                @if(old('ethnic', $reportingPerson->ethnic_id) == $ethnic->id) selected @endif>
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
                                @if(old('job', $reportingPerson->job_id) == $job->id) selected @endif>
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
                                @if(old('religion', $reportingPerson->religion_id) == $religion->id) selected @endif>
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
                                @if(old('education', $reportingPerson->education_id) == $education->id) selected @endif>
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
                                @if(old('maritalStatus', $reportingPerson->marital_status_id) == $maritalStatus->id) selected @endif>
                                {{ $maritalStatus->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                            id="isUnknownMaritalStatus" name="isUnknownMaritalStatus"
                            value="true" aria-label="..." @if(old('isUnknownMaritalStatus', var_export($reportingPerson->is_unknown_marital_status, true)) == 'true') checked @endif>
                        <label for="isUnknownMaritalStatus">
                            Tidak Tahu
                        </label>
                    </div>
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="phoneNumber">Nomor Telepon
                </label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                    <div class="d-flex mb-2">
                        <div class="form-check m-1">
                            <input class="form-check-input" type="radio"
                                id="existsPhoneNumber"
                                name="isExistsPhoneNumber" value="true" @if(old('isExistsPhoneNumber', var_export($reportingPerson->is_exists_phone_number, true)) == 'true') checked @endif>
                            <label for="existsPhoneNumber">
                                Ada Nomor Telepon
                            </label>
                        </div>

                        <div class="form-check m-1">
                            <input class="form-check-input" type="radio"
                                id="notExistsPhoneNumber"
                                name="isExistsPhoneNumber" value="false" @if(old('isExistsPhoneNumber', var_export($reportingPerson->is_exists_phone_number, true)) == 'false') checked @endif>
                            <label for="notExistsPhoneNumber">
                                Tidak ada Nomor Telepon
                            </label>
                        </div>
                    </div>

                    <input type="text" class="form-control mb-2" id="phoneNumber" name="phoneNumber"
                        placeholder="Nomor Telepon" value="{{ old('phoneNumber', $reportingPerson->phone_number) }}">

                    <div class="form-check m-1">
                        <input class="form-check-input" type="checkbox"
                            id="isAvailablePhoneNumber" name="isAvailablePhoneNumber" 
                            value="true" aria-label="..." @if(old('isAvailablePhoneNumber', var_export($reportingPerson->is_available_phone_number, true)) == 'true') checked @endif>
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
                                name="isExistsEmail" value="true" @if(old('isExistsEmail', var_export($reportingPerson->is_exists_email, true)) == 'true') checked @endif>
                            <label for="existsEmail">
                                Ada Email
                            </label>
                        </div>

                        <div class="form-check m-1">
                            <input class="form-check-input" type="radio" id="notExistsEmail"
                                name="isExistsEmail" value="false" @if(old('isExistsEmail', var_export($reportingPerson->is_exists_email, true)) == 'false') checked @endif>
                            <label for="notExistsEmail">
                                Tidak ada Email
                            </label>
                        </div>
                    </div>

                    <input type="text" class="form-control mb-2" id="email" name="email"
                        placeholder="Email" value="{{ old('email', $reportingPerson->email) }}">

                    <div class="form-check m-1">
                        <input class="form-check-input" type="checkbox" 
                            id="isAvailableEmail" name="isAvailableEmail"
                            value="true" aria-label="..." @if(old('isAvailableEmail', var_export($reportingPerson->is_available_email, true)) == "true") checked @endif>
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
                            @if(old('country', $reportingPerson->country_id) == $country->id) selected @endif>
                                {{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="countryChildrenLocationSection" @if($reportingPerson->country_id != 'C101') style="display:none;" @endif>
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
                            placeholder="Kampung (Opsional)" value="{{ old('subVillage', $reportingPerson->sub_village) }}">
                    </div>
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="address">Alamat<span class="text-danger fs-5">*</span> </label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                    <input type="text" class="form-control" id="address" name="address" placeholder="Alamat" value="{{ old('address', $reportingPerson->address) }}">
                </div>
                <div class="col-sm-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="isUnknownAddress" name="isUnknownAddress"
                            value="true" aria-label="..." @if(old('isUnknownAddress', var_export($reportingPerson->is_unknown_address, true)) == 'true') checked @endif>
                        <label for="isUnknownAddress">
                            Tidak Tahu
                        </label>
                    </div>
                </div>
            </div>

            <br/>
            <hr/>
        </form>

    </div>
</div>
@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

    <script type="text/javascript">
        //first load initialize
        $(document).ready(function(){
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
                url: "{{ route('case.participant.reporting-person.api.locations', ['accident_id' => $accidentId, 'accidentId' => $accidentId]) }}", // Replace with your backend URL
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
            var regencyId = "{{ $reportingPerson->regency_id }}";
            getRegency(parentId, regencyId);
        });

        function getRegency(parentId, regencyId = null) {
            //regency get from ajax
            var parentId = parentId;
            var regencyId = regencyId;
            var classCode = 'REGENCY';

            $.ajax({
                url: "{{ route('case.participant.reporting-person.api.locations', ['accident_id' => $accidentId, 'accidentId' => $accidentId]) }}", // Replace with your backend URL
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
            var districtId = "{{ $reportingPerson->district_id }}";
            getDistrict(parentId, districtId);
        });

        function getDistrict(parentId, districtId = null) {
            //district get from ajax
            var parentId = parentId;
            var districtId = districtId;
            var classCode = 'DISTRICT';

            $.ajax({
                url: "{{ route('case.participant.reporting-person.api.locations', ['accident_id' => $accidentId, 'accidentId' => $accidentId]) }}", // Replace with your backend URL
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
            var villageId = "{{ $reportingPerson->village_id }}";
            getVillage(parentId, villageId);
        });

        function getVillage(parentId, villageId = null) {
            //village get from ajax
            var parentId = parentId;
            var villageId = villageId;
            var classCode = 'VILLAGE';

            $.ajax({
                url: "{{ route('case.participant.reporting-person.api.locations', ['accident_id' => $accidentId, 'accidentId' => $accidentId]) }}", // Replace with your backend URL
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

        $(document).ready(function() {
            var countryId = "{{ $reportingPerson->country_id }}";
            var provinceId = "{{ $reportingPerson->province_id }}";

            getProvince(countryId, provinceId);

            //trigger is unknown
            $('#isUnknownGender').trigger('change');
            $('#isUnknownBirthPlace').trigger('change');
            $('#isUnknownBirthDate').trigger('change');
            $('#isUnknownFather').trigger('change');
            $('#isUnknownMother').trigger('change');
            /*$('#isUnknownNationality').trigger('change');*/
            $('#isUnknownMaritalStatus').trigger('change');
            $('#isUnknownAddress').trigger('change');

            var isAvailablePhoneNumber = "{{ $reportingPerson->is_available_phone_number }}";
            var isAvailableEmail = "{{ $reportingPerson->is_available_email }}";

            //trigger phone and email
            $('input[name="isExistsPhoneNumber"]').trigger('change');
            $('input[name="isExistsEmail"]').trigger('change');

            if($('input[name="isExistsPhoneNumber"]').find(':checked').val() == 'true' && isAvailablePhoneNumber != 'true') {
                $('#isAvailablePhoneNumber').prop('checked', false).trigger('change');
            }
            
            if($('input[name="isExistsEmail"]').find(':checked').val() == 'true' && isAvailableEmail != 'true') {
                $('#isAvailableEmail').prop('checked', false).trigger('change');
            }
            
            var phoneNumber = "{{ $reportingPerson->phone_number }}";
            var email = "{{ $reportingPerson->email }}";

            if($('input[name="isExistsPhoneNumber"]').find(':checked').val() == 'true' && isAvailablePhoneNumber == 'true') {
                $('#phoneNumber').val(phoneNumber);
            }
            if($('input[name="isExistsEmail"]').find(':checked').val() == 'true' && isAvailableEmail == 'true') {
                $('#email').val(email);
            }
        });

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
                $('#father').val('');
                $('#father').prop('readonly', false);
            }
        });
        $('#isUnknownMother').on('change', function() {
            if ($(this).is(':checked')) {
                $('#mother').val('TIDAK DIKETAHUI');
                $('#mother').prop('readonly', true);
            } else {
                $('#mother').val('');
                $('#mother').prop('readonly', false);
            }
        });
        /*$('#isUnknownNationality').on('change', function() {
            if ($(this).is(':checked')) {
                $('#nationality').val(3).trigger('change');
                $('#nationality').select2({
                    readonly: true,
                    disabled: false,
                });
            } else {
                $('#nationality').val('').trigger('change');
                $('#nationality').select2({
                    readonly: false,
                    disabled: false,
                });
            }
        });
        $('#nationality').on('change', function() {
            if ($(this).find(':selected').val() == 3) {
                $('#isUnknownNationality').prop('checked', true);
                $('#nationality').prop('disabled', true);
            }
        });*/
        $('#isUnknownMaritalStatus').on('change', function() {
            if ($(this).is(':checked')) {
                $('#maritalStatus').val('').trigger('change');
                $('#maritalStatus').prop('disabled', true);
            } else {
                $('#maritalStatus').prop('disabled', false);
            }
        });
        $('#isUnknownAddress').on('change', function() {
            if ($(this).is(':checked')) {
                console.log('#address');
                $('#address').val("TIDAK DIKETAHUI");
                $('#address').prop('readonly', true);
            } else {
                $('#address').val('');
                $('#address').prop('readonly', false);
            }
        });

        //phone and email
        $('input[name="isExistsPhoneNumber"]').on('change', function() {
            var isExistsPhoneNumber = $('input[name="isExistsPhoneNumber"]:checked')
                .val();
            if (isExistsPhoneNumber == 'true') {
                $('#isAvailablePhoneNumber').prop('disabled', false);
                $('#isAvailablePhoneNumber').prop('checked', true);
                $('#phoneNumber').prop('readonly', false);
                $('#phoneNumber').val('');
            } else {
                $('#isAvailablePhoneNumber').prop('disabled', true);
                $('#isAvailablePhoneNumber').prop('checked', false);
                $('#phoneNumber').val('TIDAK ADA NOMOR TELEPON');
                $('#phoneNumber').prop('readonly', true);
            }
        });
        $('#isAvailablePhoneNumber').on('change', function() {
            if ($(this).is(':checked')) {
                $('#phoneNumber').val('');
                $('#phoneNumber').prop('readonly', false);
            } else {
                $('#phoneNumber').prop('readonly', true);
                $('#phoneNumber').val('TIDAK BERSEDIA MEMBERIKAN NOMOR TELEPON');
            }
        });
        $('input[name="isExistsEmail"]').on('change', function() {
            var isExistsEmail = $('input[name="isExistsEmail"]:checked').val();
            if (isExistsEmail == 'true') {
                $('#isAvailableEmail').prop('disabled', false);
                $('#isAvailableEmail').prop('checked', true);
                $('#email').prop('readonly', false);
                $('#email').val('');
            } else {
                $('#isAvailableEmail').prop('disabled', true);
                $('#isAvailableEmail').prop('checked', false);
                $('#email').val('TIDAK ADA EMAIL');
                $('#email').prop('readonly', true);
            }
        });
        $('#isAvailableEmail').on('change', function() {
            if ($(this).is(':checked')) {
                $('#email').val('');
                $('#email').prop('readonly', false);
            } else {
                $('#email').prop('readonly', true);
                $('#email').val('TIDAK BERSEDIA MEMBERIKAN EMAIL');
            }
        });

        // Validasi Submit Form
        $(document).ready(function() {
            $('#reportingPersonFormSubmit').on('click', function(e) {
                e.preventDefault();

                // Lakukan validasi di sisi server menggunakan Ajax
                $.ajax({
                    url: "{{ route('case.participant.reporting-person.api.validate-request-form', ['accidentId' => $accidentId, 'accident_id' => request()->query('accident_id')]) }}",
                    type: 'POST',
                    dataType: 'json',
                    data: $('#reportingPersonForm').serialize(),
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
                                $('#reportingPersonForm').submit();
                            });
                        }
                    },
                    error: function(xhr) {
                        // Tangani error jika terjadi kesalahan saat melakukan validasi
                        response = JSON.parse(xhr.responseText);

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
