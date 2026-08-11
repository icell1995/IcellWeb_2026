<form
    action="@if (isset($method)) @if ($method == 'PUT'){{ route('personnel.update', ['policeId' => $policeId, 'id' => $officer->id]) }} @endif @else{{ route('personnel.store', ['policeId' => $policeId]) }}@endif"
    method="POST" enctype="multipart/form-data" id="officerForm">
    @csrf
    @if (isset($method))
        @if ($method == 'PUT')
            @method('PUT')
        @endif
    @endif
    @php
        $currentOfficer = null;
        if (isset($officer)) {
            $currentOfficer = $officer;
        }
    @endphp

    <input type="hidden" name="formMode" value="{{ isset($method) ? ($method == 'PUT' ? 'EDIT' : 'CREATE') : 'CREATE' }}">

    <div class="input-group row mb-3 ms-0">
        <label class="fw-bold col-sm-2 col-form-label" for="name">Nama Lengkap</label>
        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                value="{{ old('name', $currentOfficer->full_name ?? null) }}" required
                placeholder="Masukkan Nama Lengkap Dan Gelar Pendidikan">
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
                        value="1" @php $employmentType = $currentOfficer->employment_type ?? NULL; @endphp
                        @if (isset($currentOfficer->employment_type) == true) @if (old('employmentType', $currentOfficer->employment_type) == 1)
                                checked @endif
                    @elseif (isset($currentOfficer->employment_type) == false && empty($employmentType)) checked @endif>
                    <label for="typePoliceEmployment">
                        Anggota Polri
                    </label>
                </div>

                <div class="form-check mx-1">
                    <input class="form-check-input" type="radio" id="typeCivilEmployment" name="employmentType"
                        value="2"
                        @if (isset($currentOfficer->employment_type) == true) @if (old('employmentType', $currentOfficer->employment_type) == 2)
                                checked @endif
                        @endif>
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
            <select class="form-control select2" name="rank" id="rank">
                <option value="">--Pilih Pangkat--</option>
                @foreach ($ranks as $rank)
                    <option value="{{ $rank->id }}"
                        {{ old('rank', $currentOfficer->rank_id ?? null) == $rank->id ? 'selected' : '' }}>
                        {{ $rank->full_name . ' (' . $rank->name . ')' }}
                    </option>
                @endforeach
            </select>

            <small class="text-muted">(*Apabila daftar pangkat ingin pilih kosong silahkan hubungi Helpdesk untuk
                mendapat bantuan)</small>

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
            <input class="form-control" id="birthDate" name="birthDate" placeholder="YYYY-MM-DD" autocomplete="off"
                value="{{ old('birthDate', $currentOfficer->birth_date ?? null) }}" data-provide="datepicker">

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
                name="registerNumber" value="{{ old('registerNumber', $currentOfficer->register_number ?? null) }}"
                required placeholder="Masukkan NRP">

            @error('registerNumber')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="input-group row mb-3 ms-0">
        <label class="fw-bold col-sm-2 col-form-label">Jenis Kelamin</label>
        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
            <select class="form-control select2" name="gender" id="gender">
                <option value="">--Pilih Jenis Kelamin--</option>

                @foreach ($genders as $gender)
                    <option value="{{ $gender->id }}"
                        {{ old('gender', $currentOfficer->gender_id ?? null) == $gender->id ? 'selected' : '' }}>
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
            <select class="form-control select2" name="religion" id="religion">
                <option value="">--Pilih Agama--</option>

                @foreach ($religions as $religion)
                    <option value="{{ $religion->id }}"
                        {{ old('religion', $currentOfficer->religion_id ?? null) == $religion->id ? 'selected' : '' }}>
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
            <select class="form-control select2" name="position" id="position">
                <option value="">--Pilih Jabatan--</option>
                <option value="">--Tidak Ada Pilihan (Silahkan Hubungi Helpdesk)--</option>
                @foreach ($positions as $position)
                    @php
                        $isCanSignatory = $position->positionCLuster ? ($position->positionCluster->is_can_signatory == true ? 'true' : 'false') : 'false';
                    @endphp
                    <option value="{{ $position->id }}" data-is-can-signatory="{{ $isCanSignatory }}"
                        {{ old('position', $currentOfficer->position_id ?? null) == $position->id ? 'selected' : '' }}>
                        {{ $position->name }}
                    </option>
                @endforeach
            </select>

            <small class="text-muted">(*Apabila daftar jabatan ingin pilih kosong silahkan hubungi Helpdesk untuk
                mendapat bantuan)</small>

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
                        name="isRegisterSignatory" value="true" aria-label="..."
                        @if (isset($isRegisterSignatorySection) == true || old('isRegisterSignatory') == 'true') checked @endif>
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
                            id="registerSignatoryIdentityType">
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
                            value="{{ isset($isRegisterSignatorySection) == true ? $currentOfficer->identity_number : old('registerSignatoryIdentityNumber') }}"
                            required placeholder="Masukkan nomor induk kependudukan">

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
            <select class="form-control select2" name="education" id="education">
                <option value="">--Pilih Pendidikan--</option>

                @foreach ($educations as $education)
                    <option value="{{ $education->id }}"
                        {{ old('education', $currentOfficer->education_id ?? null) == $education->id ? 'selected' : '' }}>
                        {{ $education->name }}
                    </option>
                @endforeach
            </select>

            <small class="text-muted">(*Apabila daftar pendidikan ingin pilih kosong silahkan hubungi Helpdesk untuk
                mendapat bantuan)</small>

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
                name="educationInstitutionName"
                value="{{ old('educationInstitutionName', $currentOfficer->education_institution_name ?? null) }}"
                required placeholder="Nama Universitas / Perguruan Tinggi / Sekolah (Opsional)">

            @error('educationInstitutionName')
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
                name="phoneNumber" value="{{ old('phoneNumber', $currentOfficer->phone_number ?? null) }}" required
                placeholder="Masukkan Nomor Telepon">
            @error('phoneNumber')
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
                name="email" value="{{ old('email', $currentOfficer->email ?? null) }}" required
                placeholder="Masukkan Email">
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="input-group row mb-3 ms-0">
        <label class="fw-bold col-sm-2 col-form-label">Diktuk Polri</label>
        <div class="col-lg-10 col-md-10 col-sm-12 col-12">
            <select class="form-control select2" name="policeDiktukEducation" id="policeDiktukEducation">
                <option value="">--Pilih Pendidikan Diktuk Polri--</option>
                <option value="">--Tidak Ada Pilihan (Silahkan Hubungi Helpdesk)--</option>

                @foreach ($policeDiktukEducations as $policeDiktukEducation)
                    <option value="{{ $policeDiktukEducation->id }}"
                        {{ old('policeDiktukEducation', $currentOfficer->police_diktuk_education_id ?? null) == $policeDiktukEducation->id ? 'selected' : '' }}>
                        {{ $policeDiktukEducation->name }}
                    </option>
                @endforeach
            </select>

            <small class="text-muted">(*Apabila daftar diktuk ingin pilih kosong silahkan hubungi Helpdesk untuk
                mendapat bantuan)</small>

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
                name="policeDiktukEducationGraduateYear"
                value="{{ old('policeDiktukEducationGraduateYear', $currentOfficer->police_diktuk_education_graduate_year ?? null) }}"
                required placeholder="Tahun Lulus">

            @error('policeDiktukEducationGraduateYear')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    {{-- <br/>
    <hr/>

    <div class="box-header">
        <h5 class="fw-bold">PENDIDIKAN LANTAS / DIKJUR</h5>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="mb-2 mt-2">

                <div id="policeSpecialEducation">
                    <div class="row">
                        <div class="col">
                                <button class="btn btn-primary float-right" id="addPoliceSpecialEducationButton" type="button" data-toggle="modal" data-target="#addPoliceSpecialEducationModal"><i class="fas fa-plus text-white"></i> Tambah</button>
                        </div>
                    </div>

                    <div class="input-group mt-3">
                        <table class="table table-bordered table-responsive-md" id="policeSpecialEducationTable">
                            <thead class="table-danger">
                                <tr class="text-center">
                                    <th scope="col">Tempat Pendidikan</th>
                                    <th scope="col">Tahun Lulus</th>
                                    <th scope="col">Materi Pendidikan</th>
                                    <th scope="col">Opsi</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    <hr>

    <h4 class="fw-bold text-blue-dark">KEPENYIDIKAN</h4>

    <div class="row">
        <div class="col-12 my-2">
            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label">Status Kepenyidikan</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                    <h6 class="fw-bold">*Silahkan isi jika sudah memiliki Skep</h6>

                    @php
                        $officerSkepPenyidik = null;
                        $isSkepPenyidikExists = false;
                        if (isset($currentOfficer->officerInvestigativeDetail)) {
                            $isSkepPenyidikExists = $currentOfficer->officerInvestigativeDetail->is_skep_penyidik_exists ?? false;
                            $skepPenyidikNumber = $currentOfficer->officerInvestigativeDetail->skep_penyidik_number ?? null;
                        }
                    @endphp
                    <div class="mb-3">
                        <div class="icheck-primary">
                            <input type="radio" id="existsOfficerSkepPenyidik" name="isExistsOfficerSkepPenyidik"
                                value="true" @if ($isSkepPenyidikExists == true) checked @endif>
                            <label for="existsOfficerSkepPenyidik">
                                Sudah ada Skep Penyidik
                            </label>
                        </div>

                        <div class="icheck-primary">
                            <input type="radio" id="notExistsOfficerSkepPenyidik"
                                name="isExistsOfficerSkepPenyidik" value="false"
                                @if ($isSkepPenyidikExists == false) checked @endif>
                            <label for="notExistsOfficerSkepPenyidik">
                                Belum ada Skep Penyidik
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div id="officerSkepPenyidikSection" @if ($isSkepPenyidikExists == false) style="display:none;" @endif>
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="officerSkepPenyidikNumber">NOMOR SKEP
                        PENYIDIK : </label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="officerSkepPenyidikNumber" type="text"
                            class="form-control @error('officerSkepPenyidikNumber') is-invalid @enderror"
                            name="officerSkepPenyidikNumber"
                            value="@if ($isSkepPenyidikExists == true) {{ $skepPenyidikNumber }}@else{{ old('officerSkepPenyidikNumber', null) }} @endif"
                            required placeholder="Masukkan Nomor Skep">
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- <br/>
    <hr/>

    <div class="box-header">
        <h5 class="fw-bold">RIWAYAT SERTIFIKASI</h5>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="mb-2 mt-2">

                <div id="certification">
                    <div class="row">
                        <div class="col">
                                <button class="btn btn-primary float-right" id="addCertificationButton" type="button" data-toggle="modal" data-target="#addCertificationModal"><i class="fas fa-plus text-white"></i> Tambah</button>
                        </div>
                    </div>

                    <div class="input-group mt-3">
                        <table class="table table-bordered table-responsive-md" id="certificationTable">
                            <thead class="table-danger">
                                <tr class="text-center">
                                    <th scope="col">Nomor Register</th>
                                    <th scope="col">Tanggal Mulai Berlaku</th>
                                    <th scope="col">Tanggal Kadaluwarsa</th>
                                    <th scope="col">Opsi</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    {{--
    <br/>
    <hr/>

    <div class="box-header">
        <h5 class="fw-bold">STATUS BKO</h5>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="mb-2 mt-2">
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Status BKO : </label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <h6>*Silahkan isi jika sudah BKO</h6>

                        <div class="mb-3">
                            <div class="icheck-primary">
                                <input type="radio" id="existsOfficerInvestigation" name="isExistsOfficerInvestigation" value="true">
                                <label for="existsOfficerInvestigation">
                                    Ya
                                </label>
                            </div>

                            <div class="icheck-primary">
                                <input type="radio" id="notExistsOfficerInvestigation" name="isExistsOfficerInvestigation" value="false">
                                <label for="notExistsOfficerInvestigation">
                                    Tidak
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="officerInvestigationLicenseSection">
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="officerInvestigationLicenseNumber">No.Surat BKO : </label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            <input id="officerInvestigationLicenseNumber" type="text"
                                class="form-control @error('officerInvestigationLicenseNumber') is-invalid @enderror font-weight-bold" name="officerInvestigationLicenseNumber"
                                value="" required placeholder="">
                        </div>
                    </div>

                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="officerInvestigationLicenseNumber">Tanggal BKO : </label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            <input id="officerInvestigationLicenseNumber" type="text"
                                class="form-control @error('officerInvestigationLicenseNumber') is-invalid @enderror font-weight-bold" name="officerInvestigationLicenseNumber"
                                value="" required placeholder="">
                        </div>
                    </div>

                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="officerInvestigationLicenseNumber">Satker BKO : </label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            <input id="officerInvestigationLicenseNumber" type="text"
                                class="form-control @error('officerInvestigationLicenseNumber') is-invalid @enderror font-weight-bold" name="officerInvestigationLicenseNumber"
                                value="" required placeholder="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    --}}

    <div class="text-center">
        <button type="submit" class="btn btn-success" id="officerFormSubmit">
            <i class="bi bi-save"></i> {{ __('Simpan') }}
        </button>
        <a href="{{ route('personnel.index') }}" class="btn btn-danger">
            <i class="bi bi-x-circle"></i> {{ __('Batal') }}
        </a>
    </div>
</form>
