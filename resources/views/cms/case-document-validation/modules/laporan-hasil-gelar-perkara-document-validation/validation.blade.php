@extends('cms.layouts.app')

@section('_title', 'Validasi LHGP')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet"
        href="https://www.jqueryscript.net/demo/Time-Selection-Popover-jQuery-Timepicker/dist/css/timepicker.css">
    <link href="{{ asset('libs/bootstrap-duallistbox/bootstrap-duallistbox.css') }}" rel="stylesheet">
@endpush

@section('content')
    <a class="btn-back" href="{{ route('cms.case-document-validation.index') }}"><i
            class="bi bi-arrow-left"></i>
            Kembali ke Validasi Perkara</a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Validasi Laporan Hasil Gelar Perkara (LHGP)</h5>

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
            <form action="#"
                method="POST" enctype="multipart/form-data" id="laporanHasilGelarPerkaraForm">
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
                                <option value="{{ $suratPerintahPenyidikanDocument->id }}"
                                @if($laporanHasilGelarPerkaraDocument->surat_perintah_penyidikan_document_id == $suratPerintahPenyidikanDocument->id){{'selected'}}@endif>{{ $suratPerintahPenyidikanDocument->document_number }}</option>
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
                                    value="BIASA" @if($laporanHasilGelarPerkaraDocument->document_type == 'BIASA'){{'checked'}}@endif>
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
                                value="KHUSUS" @if($laporanHasilGelarPerkaraDocument->document_type == 'KHUSUS'){{'checked'}}@endif>
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
                            @foreach($caseDegreeTypes as $caseDegreeType)
                                <option value="{{$caseDegreeType->id}}" @if($laporanHasilGelarPerkaraDocument->case_degree_type_id == $caseDegreeType->id){{'selected'}}@endif>{{$caseDegreeType->name}}</option>
                            @endforeach
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
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                        <input class="form-control" id="caseDegreeInviteReference" name="caseDegreeInviteReference"
                            placeholder="No Surat Undangan" value="{{$laporanHasilGelarPerkaraDocument->case_degree_invite_reference}}">

                        @error('caseDegreeInviteReference')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Tanggal Surat Undangan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                        <input class="form-control" id="caseDegreeInviteDate" name="caseDegreeInviteDate"
                            placeholder="YYYY-MM-DD" autocomplete="off" value="{{$laporanHasilGelarPerkaraDocument->case_degree_invite_date}}"
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
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                        <input class="form-control" id="date" name="date" placeholder="YYYY-MM-DD"
                            autocomplete="off" value="{{$laporanHasilGelarPerkaraDocument->date}}" data-provide="datepicker">

                        @error('date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Waktu Pelaksanaan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                        <input class="form-control" id="time" name="time" placeholder="hh:mm"
                            autocomplete="off" value="{{$laporanHasilGelarPerkaraDocument->time}}">

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
                                <option value="{{$timezone->id}}" @if($laporanHasilGelarPerkaraDocument->timezone_id == $timezone->id){{'selected'}}@endif>{{$timezone->name}}</option>
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
                            autocomplete="off" value="{{$laporanHasilGelarPerkaraDocument->place}}">

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
                            value="{{$laporanHasilGelarPerkaraDocument->case_degree_leader}}">

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
                            placeholder="Contoh : 5" autocomplete="off" value="{{$laporanHasilGelarPerkaraDocument->attendees}}">

                        @error('attendees')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>

                <div id="recommendedSuspectSection" @if($laporanHasilGelarPerkaraDocument->case_degree_type_id != '1' && $laporanHasilGelarPerkaraDocument->case_degree_type_id != '4' && $laporanHasilGelarPerkaraDocument->case_degree_type_id != '16')style="display: none;"@endif>
                    <h5 class="fw-bold text-blue-dark">Rekomendasi Data Tersangka</h5>

                    <!-- Penetapan Tersangka -->
                    <div id="suspectDeterminationSection" @if($laporanHasilGelarPerkaraDocument->case_degree_type_id != '1')style="display: none;"@endif>
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
                    <div id="arrestSuspectSection" @if($laporanHasilGelarPerkaraDocument->case_degree_type_id != '4')style="display: none;"@endif>
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label">Tersangka yang Direkomendasikan untuk Ditahan
                            </label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <select class="form-control select2-multiple" name="arrestSuspects[]" id="arrestSuspects"
                                    multiple="multiple"
                                    data-placeholder="--Tersangka yang Direkomendasikan untuk Ditahan--">
                                    @foreach ($arrerstedSuspects as $arrerstedSuspect)
                                        @php
                                            $isSelected = in_array($arrerstedSuspect->id, array_column($laporanHasilGelarPerkaraDocument->suspects->toArray(), 'id'));
                                        @endphp

                                        <option value="{{$arrerstedSuspect->id}}" @if($isSelected){{'selected'}}@endif>{{$arrerstedSuspect->name}}</option>
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
                    <div id="suspectRevocationSection" @if($laporanHasilGelarPerkaraDocument->case_degree_type_id != '16')style="display: none;"@endif>
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label">Daftar Tersangka</label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                <select class="form-control select2-multiple" name="revocationSuspects[]"
                                    id="revocationSuspects" multiple="multiple"
                                    data-placeholder="--Tersangka yang Direkomendasikan untuk Ditahan--">
                                    @foreach ($revocationSuspects as $revocationSuspect)
                                        @php
                                            $isSelected = in_array($revocationSuspect->id, array_column($laporanHasilGelarPerkaraDocument->suspects->toArray(), 'id'));
                                        @endphp

                                        <option value="{{$revocationSuspect->id}}" @if($isSelected){{'selected'}}@endif>{{$revocationSuspect->name}}</option>
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
                        <textarea class="form-control noEnterTextArea" id="discussion" name="discussion" rows="10"
                            autocomplete="off">{{$laporanHasilGelarPerkaraDocument->discussion}}</textarea>

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
                        <textarea class="form-control noEnterTextArea" id="conclusion" name="conclusion" rows="10"
                            autocomplete="off">{{$laporanHasilGelarPerkaraDocument->conclusion}}</textarea>

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
                        <textarea class="form-control noEnterTextArea" id="closing" name="closing" rows="10"
                            autocomplete="off">{{$laporanHasilGelarPerkaraDocument->closing}}</textarea>

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
                    <label class="fw-bold col-sm-3 col-form-label">Tanggal Ditandatangani Dokumen <span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input class="form-control" id="documentDate" name="documentDate" placeholder="YYYY-MM-DD"
                            autocomplete="off" value="{{$laporanHasilGelarPerkaraDocument->document_date}}" data-provide="datepicker">

                        @error('documentDate')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                @php
                    $upperUnitLevelOfficer = $laporanHasilGelarPerkaraDocumentOfficers->where('class', 'SIGNATORY')->where('flag', 'UPPER_UNIT_LEVEL')->first();
                    $upperUnitLevelOfficerCountResult = $laporanHasilGelarPerkaraDocumentOfficers->where('class', 'SIGNATORY')->where('flag', 'UPPER_UNIT_LEVEL')->count();
                    $isUpperUnitLevelOfficer = $upperUnitLevelOfficerCountResult != 0 ? true : false;
                @endphp
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="isUpperUnitDocument">Penandatangan Dokumen </label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="isUpperUnitDocument"
                                name="isUpperUnitDocument" value="true" aria-label="..." disabled @if($isUpperUnitLevelOfficer == true){{'checked'}}@endif>
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
                </div>

                <div id="signatoryOfficer" @if($isUpperUnitLevelOfficer == true)style="display: none;"@endif>
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-3 col-form-label">Yang Menandatangani<span class="text-danger fs-5">*</span> </label>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                            <select class="form-control select2" name="signatory" id="signatory">
                                <option value="">--Pilih Yang Menandatangani--</option>
                                @foreach ($authorizedSignatories as $data)
                                    @php
                                        $positionName = $data->position->name ?? '';
                                    @endphp
                                    <option value="{{$data->id}}" @if($laporanHasilGelarPerkaraDocumentOfficers->where('class', 'SIGNATORY')->where('register_number', $data->register_number)->count() != 0){{'selected'}}@endif>
                                        {{$data->register_number . ' - ' . $data->full_name . ' | ' . $positionName}}
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

                <div id="signatoryUpperUnitOfficer" @if($isUpperUnitLevelOfficer == false)style="display: none"@endif>
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-3 col-form-label">Nama Pejabat Satuan Atas</label>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                            <input class="form-control" id="upperUnitOfficerName" name="upperUnitOfficerName"
                                placeholder="" autocomplete="off" value="{{$upperUnitLevelOfficer->first_name ?? null}}">
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
                                value="{{$upperUnitLevelOfficer->register_number ?? null}}">
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
                                    <option value="{{$rank->id}}" @if($upperUnitLevelOfficer) @if($upperUnitLevelOfficer->rank_id == $rank->id){{'selected'}}@endif @endif>
                                        {{$rank->full_name . ' (' .  $rank->name . ')'}}</option>
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
                                    <option value="{{$position->id}}" @if($upperUnitLevelOfficer) @if($upperUnitLevelOfficer->position_id == $position->id){{'selected'}}@endif @endif>{{$position->name}}</option>
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
                                    name="isOnBehalfOfSuperiorOfficer" value="true" aria-label="..." @if($isUpperUnitLevelOfficer) @if($upperUnitLevelOfficer->is_on_behalf == true){{'checked'}}@endif @endif>
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
                                value="{{$upperUnitLevelOfficer->on_behalf_name ?? null}}">
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

                        @php
                            $photos = $laporanHasilGelarPerkaraDocument->laporanHasilGelarPerkaraDocumentFiles->where('type', 'IMAGE')->all();
                        @endphp
                        @if(count($photos) != 0)
                            <div class="card mt-4">
                                <div class="card-body">
                                    {{-- Show Uploaded Photos --}}
                                    <div class="row">
                                        @foreach($photos as $photo)
                                            <div class="col-sm-3">
                                                <img src="{{ asset('documents/laporan-hasil-gelar-perkara-document/images/' . $photo->name) }}" class="img-fluid" alt="Photo">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <hr/>
                <div class="card">
                    <div class="card-header">
                        <h5 class="fw-bold text-blue-dark">Attachments</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <a target="_blank"
                                                    href="@if (isset($laporanHasilGelarPerkaraDocument->documentCategory)) {{ route($laporanHasilGelarPerkaraDocument->documentCategory->base_route . '.download', ['id' => $laporanHasilGelarPerkaraDocument->id, 'accident_id' => $laporanHasilGelarPerkaraDocument->accident->id, 'document_category_id' => $laporanHasilGelarPerkaraDocument->documentCategory->id]) }} @endif"
                                                    class="btn btn-primary btn-lg"><i class="bi bi-printer"></i> Cetak</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if(isset($laporanHasilGelarPerkaraDocument->laporanHasilGelarPerkaraDocumentAttachment->name))
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-grid gap-2">
                                                <a href="{{ asset('documents/attachments/' . $laporanHasilGelarPerkaraDocument->laporanHasilGelarPerkaraDocumentAttachment->name) }}"
                                                    class="btn btn-secondary btn-lg" target="_blank">
                                                    <i class="bi bi-file-earmark"></i> Lihat Dokumen Unggah</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if(isset($laporanHasilGelarPerkaraDocument->laporanHasilGelarPerkaraDocumentAttachment))
                            @if(in_array($laporanHasilGelarPerkaraDocument->laporanHasilGelarPerkaraDocumentAttachment->extension, ['pdf']))
                                <!-- 16:9 aspect ratio -->
                                <br>
                                <div class="embed-responsive embed-responsive-16by1">
                                    <iframe style="top:0;left:0;width:100%;height:1024px;" class="embed-responsive-item" src="{{ asset('documents/attachments/' . $laporanHasilGelarPerkaraDocument->laporanHasilGelarPerkaraDocumentAttachment->name) }}" allowfullscreen></iframe>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </form>

            <div class="mt-4">
                <div class="text-center">
                    <button type="button" class="btn btn-danger" id="laporanHasilGelarPerkaraRejectValidationButton" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="bi bi-box-arrow-in-down-left"></i> KEMBALIKAN
                    </button>
                  
                    <button type="button" class="btn btn-primary" id="laporanHasilGelarPerkaraApproveValidationButton" data-bs-toggle="modal" data-bs-target="#approveModal">
                        <i class="bi bi-check2"></i> VALIDASI
                    </button>

                    {{--<form action="{{route('cms.case-document-validation.module.laporan-hasil-gelar-perkara-document.validation.approve', ['accident_id' => $laporanHasilGelarPerkaraDocument->accident->id, 'id' => $laporanHasilGelarPerkaraDocument->id, 'document_category_id' => $laporanHasilGelarPerkaraDocument->document_category_id])}}" method="POST" id="approveValidationForm" class="approveValidationForm d-inline">
                        @csrf
                        @method('POST')
                        <input type="hidden" name="isApproved" id="isApproved" value="true">
                        <input type="hidden" name="isLegacy" id="isLegacy" value="false">
                        <button type="submit" class="btn btn-primary laporanHasilGelarPerkaraApproveValidationFormSubmit" id="laporanHasilGelarPerkaraApproveValidationFormSubmit">
                            <i class="bi bi-check2"></i> VALIDASI
                        </button>
                    </form> --}}

                    <a href="{{ route('cms.case-document-validation.index') }}"
                        class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> {{ __('Batal') }}
                    </a>
                </div>
            </div>
        </div>

    </div>

<!-- Modal Approve-->
<div class="modal fade" id="approveModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">Validasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{route('cms.case-document-validation.module.laporan-hasil-gelar-perkara-document.validation.approve', ['accident_id' => $laporanHasilGelarPerkaraDocument->accident->id, 'id' => $laporanHasilGelarPerkaraDocument->id, 'document_category_id' => $laporanHasilGelarPerkaraDocument->document_category_id])}}" 
                method="POST" id="approveValidationForm" class="d-inline">
                <div class="modal-body">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="isApproved" id="isApproved" value="true">
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="isLegacy" name="isLegacy"
                                value="true" aria-label="..."
                                @if ($laporanHasilGelarPerkaraDocument->is_legacy == true || old('isLegacy') == 1) {{ 'checked' }} @endif>
                            <label for="isLegacy">
                                <b>Tandai Dokumen Legacy</b>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="laporanHasilGelarPerkaraApproveValidationFormSubmit">Validasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Approve-->

<!-- Modal Reject-->
<div class="modal fade" id="rejectModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Kembalikan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{route('cms.case-document-validation.module.laporan-hasil-gelar-perkara-document.validation.reject', ['accident_id' => $laporanHasilGelarPerkaraDocument->accident->id, 'id' => $laporanHasilGelarPerkaraDocument->id, 'document_category_id' => $laporanHasilGelarPerkaraDocument->document_category_id])}}" 
                method="POST" id="rejectValidationForm" class="d-inline">
                <div class="modal-body">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="isRejected" id="isRejected" value="true">
                    
                    <select class="form-control" name="rejectStatusOption" id="rejectStatusOption">
                        @foreach($rejectStatusOptions as $rejectStatusOption)
                            <option value="{{ $rejectStatusOption->id }}" @if($rejectStatusOption->id == '4') selected @endif>{{ $rejectStatusOption->name . ' ' . '(' . $rejectStatusOption->id . ')' }}</option>
                        @endforeach
                    </select>
                    
                    <div id="rejectReasonField" class="mt-4">
                        <label class="fw-bold" for="rejectReason">Message</label>
                        <textarea class="form-control mt-2" id="rejectReason" name="rejectReason" rows="5"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="laporanHasilGelarPerkaraRejectValidationFormSubmit">Kembalikan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Reject-->

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

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="identityStatusFieldNewSuspect">Status
                                Identitas<span class="text-danger fs-5">*</span> </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex">
                                <div class="form-check me-1">
                                    <input class="form-check-input" type="radio"
                                        id="identityStatusOnlyNameFieldNewSuspect" name="identityStatusFieldNewSuspect"
                                        value="WITHOUT_IDENTITY">
                                    <label for="identityStatusOnlyNameFieldNewSuspect">
                                        <b>Hanya Diketahui Nama</b>
                                    </label>
                                </div>
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

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="identityTypeFieldNewSuspect">Jenis
                                Identitas<span class="text-danger fs-5">*</span> </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
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

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="identityNumberFieldNewSuspect">Nomor
                                Identitas<span class="text-danger fs-5">*</span> </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                                <input type="text" class="form-control" id="identityNumberFieldNewSuspect"
                                    placeholder="Nomor Identitas">
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="nameFieldNewSuspect">Nama<span class="text-danger fs-5">*</span> </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                                <input type="text" class="form-control" id="nameFieldNewSuspect"
                                    placeholder="Nama Lengkap">
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="genderFieldNewSuspect">Jenis Kelamin<span class="text-danger fs-5">*</span>
                            </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                                <select class="form-control" id="genderFieldNewSuspect">
                                    <option value="">--Pilih Jenis Kelamin--</option>
                                    @foreach ($genders as $gender)
                                        <option value="{{ $gender->id }}" data-gender-name="{{ $gender->name }}">
                                            {{ $gender->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="isUnknownGenderFieldNewSuspect"
                                        value="true" aria-label="...">
                                    <label for="isUnknownGenderFieldNewSuspect">
                                        Tidak Tahu
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="birthPlaceFieldNewSuspect">Tempat Lahir<span class="text-danger fs-5">*</span>
                            </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                                <input type="text" class="form-control" id="birthPlaceFieldNewSuspect"
                                    placeholder="Tempat Lahir">
                            </div>
                            <div class="col-sm-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        id="isUnknownBirthPlaceFieldNewSuspect" value="true" aria-label="...">
                                    <label for="isUnknownBirthPlaceFieldNewSuspect">
                                        Tidak Tahu
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="birthDateFieldNewSuspect">Tanggal Lahir<span class="text-danger fs-5">*</span>
                            </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                                <input type="text" class="form-control" id="birthDateFieldNewSuspect"
                                    placeholder="YYYY-MM-DD" data-provide="datepicker">
                            </div>
                            <div class="col-sm-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        id="isUnknownBirthDateFieldNewSuspect" value="true" aria-label="...">
                                    <label for="isUnknownBirthDateFieldNewSuspect">
                                        Tidak Tahu
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="fatherFieldNewSuspect">Ayah Kandung
                            </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
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
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="motherFieldNewSuspect">Ibu Kandung
                            </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
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
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="nationalityFieldNewSuspect">Kebangsaan<span class="text-danger fs-5">*</span>
                            </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                                <input type="text" class="form-control" id="nationalityFieldNewSuspect"
                                    placeholder="Kebangsaan">
                            </div>
                            <div class="col-sm-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        id="isUnknownNationalityFieldNewSuspect" value="true" aria-label="...">
                                    <label for="isUnknownNationalityFieldNewSuspect">
                                        Tidak Tahu
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="ethnicFieldNewSuspect">Suku<span class="text-danger fs-5">*</span> </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                                <select class="form-control" id="ethnicFieldNewSuspect">
                                    <option value="">--Pilih Suku--</option>
                                    @foreach ($ethnics as $ethnic)
                                        <option value="{{ $ethnic->id }}" data-ethnic-name="{{ $ethnic->name }}">
                                            {{ $ethnic->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="jobFieldNewSuspect">Pekerjaan<span class="text-danger fs-5">*</span> </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                                <select class="form-control" id="jobFieldNewSuspect">
                                    <option value="">--Pilih Pekerjaan--</option>
                                    @foreach ($jobs as $job)
                                        <option value="{{ $job->id }}" data-job-name="{{ $job->name }}">
                                            {{ $job->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="religionFieldNewSuspect">Agama<span class="text-danger fs-5">*</span> </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                                <select class="form-control" id="religionFieldNewSuspect">
                                    <option value="">--Pilih Agama--</option>
                                    @foreach ($religions as $religion)
                                        <option value="{{ $religion->id }}" data-religion-name="{{ $religion->name }}">
                                            {{ $religion->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="educationFieldNewSuspect">Pendidikan<span class="text-danger fs-5">*</span>
                            </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                                <select class="form-control" id="educationFieldNewSuspect">
                                    <option value="">--Pilih Pendidikan--</option>
                                    @foreach ($educations as $education)
                                        <option value="{{ $education->id }}"
                                            data-education-name="{{ $education->name }}">{{ $education->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="maritalStatusFieldNewSuspect">Status Kawin<span class="text-danger fs-5">*</span>
                            </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                                <select class="form-control" id="maritalStatusFieldNewSuspect">
                                    <option value="">--Pilih Status Kawin--</option>
                                    @foreach ($maritalStatuses as $maritalStatus)
                                        <option value="{{ $maritalStatus->id }}"
                                            data-marital-status-name="{{ $maritalStatus->name }}">
                                            {{ $maritalStatus->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        id="isUnknownMaritalStatusFieldNewSuspect" value="true" aria-label="...">
                                    <label for="isUnknownMaritalStatusFieldNewSuspect">
                                        Tidak Tahu
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
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

                                    <div class="form-check -1">
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

                        <div class="input-group row mb-3 ms-0">
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

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="countryFieldNewSuspect">Negara<span class="text-danger fs-5">*</span> </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
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
                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-2 col-form-label" for="provinceFieldNewSuspect">Provinsi<span class="text-danger fs-5">*</span>
                                </label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                                    <select class="form-control" id="provinceFieldNewSuspect">
                                        <option value="">--Pilih Provinsi--</option>
                                    </select>
                                </div>
                            </div>
                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-2 col-form-label" for="regencyFieldNewSuspect">Kabupaten/Kota<span class="text-danger fs-5">*</span>
                                </label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                                    <select class="form-control" id="regencyFieldNewSuspect">
                                        <option value="">--Pilih Kabupaten/Kota--</option>
                                    </select>
                                </div>
                            </div>
                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-2 col-form-label" for="districtFieldNewSuspect">Kecamatan<span class="text-danger fs-5">*</span>
                                </label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                                    <select class="form-control" id="districtFieldNewSuspect">
                                        <option value="">--Pilih Kecamatan--</option>
                                    </select>
                                </div>
                            </div>
                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-2 col-form-label" for="villageFieldNewSuspect">Kelurahan/Desa<span class="text-danger fs-5">*</span>
                                </label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                                    <select class="form-control" id="villageFieldNewSuspect">
                                        <option value="">--Pilih Kelurahan/Desa--</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="addressFieldNewSuspect">Alamat<span class="text-danger fs-5">*</span> </label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                                <input type="text" class="form-control" id="addressFieldNewSuspect"
                                    placeholder="Alamat">
                            </div>
                            <div class="col-sm-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="isUnknownAddressFieldNewSuspect"
                                        value="true" aria-label="...">
                                    <label for="isUnknownAddressFieldNewSuspect">
                                        Tidak Tahu
                                    </label>
                                </div>
                            </div>
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

@php
    $suspectDeterminations = $laporanHasilGelarPerkaraDocument->suspects;
@endphp

@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js" defer></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" defer></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js" defer></script>
<script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
<script src="https://www.jqueryscript.net/demo/Time-Selection-Popover-jQuery-Timepicker/dist/js/timepicker.js"></script>
<script src="{{ asset('libs/bootstrap-duallistbox/jquery.bootstrap-duallistbox.js') }}"></script>
<script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

<script type="text/javascript">
    @if($laporanHasilGelarPerkaraDocument->case_degree_type_id == '1')
        $(document).ready(function() {
            var suspectDeterminations = @json($suspectDeterminations);
            //suspectDeterminations
            $(function() {
                for (var key in suspectDeterminations) {
                    if (suspectDeterminations.hasOwnProperty(key)) {
                        var suspectDetermination = suspectDeterminations[key];
                        var isNewRecord = false;
                        var id = suspectDetermination.id;
                        var identityStatus = suspectDetermination.status;
                        var identityTypeId = suspectDetermination.identity_type_id;
                        var identityTypeName = (suspectDetermination.identity_type) ? suspectDetermination.identity_type.name : '';
                        var identityNumber = suspectDetermination.identity_number;
                        var name = suspectDetermination.name;
                        var gender = suspectDetermination.gender_id;
                        var birthPlace = suspectDetermination.birth_place;
                        var birthDate = suspectDetermination.birth_date;
                        var father = suspectDetermination.father_name;
                        var mother = suspectDetermination.mother_name;
                        var nationality = suspectDetermination.nationality;
                        var ethnic = suspectDetermination.ethnic_id;
                        var job = suspectDetermination.job_id;
                        var religion = suspectDetermination.religion_id;
                        var education = suspectDetermination.education_id;
                        var maritalStatus = suspectDetermination.marital_status_id;
                        var phoneNumber = suspectDetermination.phone_number;
                        var isExistsPhoneNumber = suspectDetermination.properties.is_exists_phone_number;
                        var isAvailablePhoneNumber = suspectDetermination.properties.is_available_phone_number;
                        var email = suspectDetermination.email;
                        var isExistsEmail = suspectDetermination.properties.is_exists_email;
                        var isAvailableEmail = suspectDetermination.properties.is_available_email;
                        var country = suspectDetermination.country_id;
                        var province = suspectDetermination.province_id;
                        var regency = suspectDetermination.regency_id;
                        var district = suspectDetermination.district_id;
                        var village = suspectDetermination.village_id;
                        var address = suspectDetermination.address;
                        var isUnknownGender = suspectDetermination.properties.is_unknown_gender;
                        var isUnknownBirthPlace = suspectDetermination.properties.is_birth_place;
                        var isUnknownBirthDate = suspectDetermination.properties.is_birth_date;
                        var isUnknownFather = suspectDetermination.properties.is_unknown_father;
                        var isUnknownMother = suspectDetermination.properties.is_unknown_mother;
                        var isUnknownNationality = suspectDetermination.properties.is_unknown_nationality;
                        var isUnknownMaritalStatus = suspectDetermination.properties.is_unknown_marital_status;
                        var isUnknownAddress = suspectDetermination.properties.is_unknown_address;

                        var suspectSourceId = suspectDetermination.suspect_source.id;
                        var suspectSourceName = suspectDetermination.suspect_source.name;

                        //append all value to table
                        $('#suspectDeterminationTable').append(
                                '<tr>' +
                                    '<td>' + identityTypeName + ' / ' + identityNumber + '</td>' +
                                    '<td>' + name + '</td>' +
                                    '<td>' + birthPlace + '/' + birthDate + '</td>' +
                                    '<td>' + suspectSourceName + '</td>' +
                                    '<td>' +
                                        '<input type="hidden" name="suspectDeterminationIsNewRecord[]" value="' + isNewRecord + '">' +
                                        '<input type="hidden" name="suspectDeterminationSuspectSourceId[]" value="' + suspectSourceId + '">' +
                                        '<input type="hidden" name="suspectDeterminationIds[]" value="' + id + '">' +
                                        '<input type="hidden" name="suspectDeterminationIdentityStatus[]" value="' + identityStatus + '">' +
                                        '<input type="hidden" name="suspectDeterminationIdentityTypeId[]" value="' + identityTypeId + '">' +
                                        '<input type="hidden" name="suspectDeterminationIdentityNumber[]" value="' + identityNumber + '">' +
                                        '<input type="hidden" name="suspectDeterminationName[]" value="' + name + '">' +
                                        '<input type="hidden" name="suspectDeterminationGenderId[]" value="' + gender + '">' +
                                        '<input type="hidden" name="suspectDeterminationBirthPlace[]" value="' + birthPlace + '">' +
                                        '<input type="hidden" name="suspectDeterminationBirthDate[]" value="' + birthDate + '">' +
                                        '<input type="hidden" name="suspectDeterminationFatherName[]" value="' + father + '">' +
                                        '<input type="hidden" name="suspectDeterminationMotherName[]" value="' + mother + '">' +
                                        '<input type="hidden" name="suspectDeterminationNationality[]" value="' + nationality + '">' +
                                        '<input type="hidden" name="suspectDeterminationEthnicId[]" value="' + ethnic + '">' +
                                        '<input type="hidden" name="suspectDeterminationJobId[]" value="' + job + '">' +
                                        '<input type="hidden" name="suspectDeterminationReligionId[]" value="' + religion + '">' +
                                        '<input type="hidden" name="suspectDeterminationEducationId[]" value="' + education + '">' +
                                        '<input type="hidden" name="suspectDeterminationMaritalStatusId[]" value="' + maritalStatus + '">' +
                                        '<input type="hidden" name="suspectDeterminationPhoneNumber[]" value="' + phoneNumber + '">' +
                                        '<input type="hidden" name="suspectDeterminationIsExistsPhoneNumber[]" value="' + isExistsPhoneNumber + '">' +
                                        '<input type="hidden" name="suspectDeterminationIsAvailablePhoneNumber[]" value="' + isAvailablePhoneNumber + '">' +
                                        '<input type="hidden" name="suspectDeterminationEmail[]" value="' + email + '">' +
                                        '<input type="hidden" name="suspectDeterminationIsExistsEmail[]" value="' + isExistsEmail + '">' +
                                        '<input type="hidden" name="suspectDeterminationIsAvailableEmail[]" value="' + isAvailableEmail + '">' +
                                        '<input type="hidden" name="suspectDeterminationCountryId[]" value="' + country + '">' +
                                        '<input type="hidden" name="suspectDeterminationProvinceId[]" value="' + province + '">' +
                                        '<input type="hidden" name="suspectDeterminationRegencyId[]" value="' + regency + '">' +
                                        '<input type="hidden" name="suspectDeterminationDistrictId[]" value="' + district + '">' +
                                        '<input type="hidden" name="suspectDeterminationVillageId[]" value="' + village + '">' +
                                        '<input type="hidden" name="suspectDeterminationAddress[]" value="' + address + '">' +
                                        '<input type="hidden" name="suspectDeterminationIsUnknownGender[]" value="' + isUnknownGender + '">' +
                                        '<input type="hidden" name="suspectDeterminationIsUnknownBirthPlace[]" value="' + isUnknownBirthPlace + '">' +
                                        '<input type="hidden" name="suspectDeterminationIsUnknownBirthDate[]" value="' + isUnknownBirthDate + '">' +
                                        '<input type="hidden" name="suspectDeterminationIsUnknownFather[]" value="' + isUnknownFather + '">' +
                                        '<input type="hidden" name="suspectDeterminationIsUnknownMother[]" value="' + isUnknownMother + '">' +
                                        '<input type="hidden" name="suspectDeterminationIsUnknownNationality[]" value="' + isUnknownNationality + '">' +
                                        '<input type="hidden" name="suspectDeterminationIsUnknownMaritalStatus[]" value="' + isUnknownMaritalStatus + '">' +
                                        '<input type="hidden" name="suspectDeterminationIsUnknownAddress[]" value="' + isUnknownAddress + '">' +

                                        //'<button type="button" class="btn btn-warning btn-sm editSuspectDeterminationButton"><i class="bi bi-pencil-square"></i></button>' +
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
                }
            });
        });
    @endif

    $(document).ready(function() {
        $(function () {
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
            endDate: new Date()
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

        $('#time').timepicker();
        $('#time').keydown(function(e) {
            e.preventDefault();
            return false;
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

            if(caseDegreeTypeId == 1){
                $('#recommendedSuspectSection').show();
                $('#suspectDeterminationSection').show();
                $('#arrestSuspectSection').hide();
                $('#suspectRevocationSection').hide();
            }else if(caseDegreeTypeId == 4){
                $('#recommendedSuspectSection').show();
                $('#suspectDeterminationSection').hide();
                $('#arrestSuspectSection').show();
                $('#suspectRevocationSection').hide();
            }else if(caseDegreeTypeId == 16){
                $('#recommendedSuspectSection').show();
                $('#suspectDeterminationSection').hide();
                $('#arrestSuspectSection').hide();
                $('#suspectRevocationSection').show();
            }else{
                $('#suspectDeterminationSection').hide();
                $('#arrestSuspectSection').hide();
                $('#suspectRevocationSection').hide();
                 $('#recommendedSuspectSection').hide();
            }
        });

        $('#suspectDeterminationDataSource').on('change', function() {
            var suspectDeterminationDataSourceId = $(this).find(':selected').val();

            if(suspectDeterminationDataSourceId == 3){
                $('#addNewSuspectModal').modal({
                    'show': true,
                    'dialog': {
                        'class': 'modal-dialog-scrollable modal-xl'
                    }
                });
            }else if(suspectDeterminationDataSourceId == 1){
                $.ajax({
                    url: "{{route('doc.laporan-hasil-gelar-perkara-document.api.witnesses', ['accident_id' => $accidentId])}}", // Replace with your backend URL
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
                                "data-identity-number": (data.identity_number) ? data.identity_number : '-',
                                "data-birth-place": (data.birth_place) ? data.birth_place : '-',
                                "data-birth-date": (data.birth_date) ? data.birth_date : '-',
                                "data-identity-type-name": (data.identity_type) ? data.identity_type.name : '-',
                            }));
                        });
                    },
                    error: function(xhr) {
                        // Handle error if needed
                        console.log(xhr.responseText);
                    }
                });
            }else if(suspectDeterminationDataSourceId == 2){
                $.ajax({
                    url: "{{route('doc.laporan-hasil-gelar-perkara-document.api.suspects', ['accident_id' => $accidentId])}}", // Replace with your backend URL
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        'flag': 'TERLAPOR',
                    },
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
                                "data-identity-number": (data.identity_number) ? data.identity_number : '-',
                                "data-birth-place": (data.birth_place) ? data.birth_place : '-',
                                "data-birth-date": (data.birth_date) ? data.birth_date : '-',
                                "data-identity-type-name": (data.identity_type) ? data.identity_type.name : '-',
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
            $('#addNewSuspectModal').modal({
                'show': true,
                'width': '100%',
            });
        });
    });

    $(document).ready(function() {
        $('#suspectDeterminationOptionAddButtton').on('click', function() {
            var isNewRecord = true;
            var suspectDeterminationOptionId = $('#suspectDeterminationOption').find(':selected').val();
            var suspectDeterminationOptionName = $('#suspectDeterminationOption').find(':selected').data('name');
            var suspectDeterminationOptionIdentityTypeName = $('#suspectDeterminationOption').find(':selected').data('identity-type-name');
            var suspectDeterminationOptionIdentityNumber = $('#suspectDeterminationOption').find(':selected').data('identity-number');
            var suspectDeterminationOptionBirthPlace = $('#suspectDeterminationOption').find(':selected').data('birth-place');
            var suspectDeterminationOptionBirthDate = $('#suspectDeterminationOption').find(':selected').data('birth-date');
            var suspectSourceId = $('#suspectDeterminationDataSource').find(':selected').val();
            var suspectSourceName = $('#suspectDeterminationDataSource').find(':selected').text();

            if(suspectDeterminationOptionId != ''){
                $('#suspectDeterminationTable').append(
                    '<tr>' +
                        '<td>' + suspectDeterminationOptionIdentityTypeName + ' / ' + suspectDeterminationOptionIdentityNumber + '</td>' +
                        '<td>' + suspectDeterminationOptionName + '</td>' +
                        '<td>' + suspectDeterminationOptionBirthPlace + '/' + suspectDeterminationOptionBirthDate + '</td>' +
                        '<td>' + suspectSourceName + '</td>' +
                        '<td>' +
                            '<input type="hidden" name="suspectDeterminationIsNewRecord[]" value="' + isNewRecord + '">' +
                            '<input type="hidden" name="suspectDeterminationSuspectSourceId[]" value="' + suspectSourceId + '">' +
                            '<input type="hidden" name="suspectDeterminationIds[]" value="'+ suspectDeterminationOptionId +'">' +
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

                $('.removeSuspectDeterminationButton').on('click', function() {
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
        function withIdentitySection(){
            var identityStatus = $('input[name="identityStatusFieldNewSuspect"]:checked').val();
            if(identityStatus == 'WITHOUT_IDENTITY'){
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
                $('input[name="isExistsPhoneNumberFieldNewSuspect"]').val(['false']).prop('checked', true).trigger('change');
                $('input[name="isExistsEmailFieldNewSuspect"]').val(['false']).prop('checked', true).trigger('change');
                $('#isAvailablePhoneNumberFieldNewSuspect').prop('checked', false);
                $('#isAvailableEmailFieldNewSuspect').prop('checked', false);
            }else if(identityStatus == 'WITH_IDENTITY'){
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
                $('input[name="isExistsPhoneNumberFieldNewSuspect"]').val(['false']).prop('checked', true).trigger('change');
                $('input[name="isExistsEmailFieldNewSuspect"]').val(['false']).prop('checked', true).trigger('change');
                $('#isAvailablePhoneNumberFieldNewSuspect').prop('checked', false);
                $('#isAvailableEmailFieldNewSuspect').prop('checked', false);
            }
        }

        $(function(){
            withIdentitySection();
        });

        $('input[name="identityStatusFieldNewSuspect"]').on('change', function(){
            withIdentitySection();
        });

        //tidak tahu checked
        $('#isUnknownGenderFieldNewSuspect').on('change', function(){
            if($(this).is(':checked')){
                $('#genderFieldNewSuspect').val('').trigger('change');
                $('#genderFieldNewSuspect').prop('disabled', true);
            }else{
                $('#genderFieldNewSuspect').prop('disabled', false);
            }
        });
        $('#isUnknownBirthPlaceFieldNewSuspect').on('change', function(){
            if($(this).is(':checked')){
                $('#birthPlaceFieldNewSuspect').val('TIDAK DIKETAHUI');
                $('#birthPlaceFieldNewSuspect').prop('disabled', true);
            }else{
                $('#birthPlaceFieldNewSuspect').val('');
                $('#birthPlaceFieldNewSuspect').prop('disabled', false);
            }
        });
        $('#isUnknownBirthDateFieldNewSuspect').on('change', function(){
            if($(this).is(':checked')){
                $('#birthDateFieldNewSuspect').val('');
                $('#birthDateFieldNewSuspect').prop('disabled', true);
            }else{
                $('#birthDateFieldNewSuspect').prop('disabled', false);
            }
        });
        $('#isUnknownFatherFieldNewSuspect').on('change', function(){
            if($(this).is(':checked')){
                $('#fatherFieldNewSuspect').val('TIDAK DIKETAHUI');
                $('#fatherFieldNewSuspect').prop('disabled', true);
            }else{
                $('#fatherFieldNewSuspect').val('');
                $('#fatherFieldNewSuspect').prop('disabled', false);
            }
        });
        $('#isUnknownMotherFieldNewSuspect').on('change', function(){
            if($(this).is(':checked')){
                $('#motherFieldNewSuspect').val('TIDAK DIKETAHUI');
                $('#motherFieldNewSuspect').prop('disabled', true);
            }else{
                $('#motherFieldNewSuspect').val('');
                $('#motherFieldNewSuspect').prop('disabled', false);
            }
        });
        $('#isUnknownNationalityFieldNewSuspect').on('change', function(){
            if($(this).is(':checked')){
                $('#nationalityFieldNewSuspect').val('TIDAK DIKETAHUI');
                $('#nationalityFieldNewSuspect').prop('disabled', true);
            }else{
                $('#nationalityFieldNewSuspect').val('');
                $('#nationalityFieldNewSuspect').prop('disabled', false);
            }
        });
        $('#isUnknownMaritalStatusFieldNewSuspect').on('change', function(){
            if($(this).is(':checked')){
                $('#maritalStatusFieldNewSuspect').val('').trigger('change');
                $('#maritalStatusFieldNewSuspect').prop('disabled', true);
            }else{
                $('#maritalStatusFieldNewSuspect').prop('disabled', false);
            }
        });
        $('#isUnknownAddressFieldNewSuspect').on('change', function(){
            if($(this).is(':checked')){
                $('#addressFieldNewSuspect').val('TIDAK DIKETAHUI');
                $('#addressFieldNewSuspect').prop('disabled', true);
            }else{
                $('#addressFieldNewSuspect').val('');
                $('#addressFieldNewSuspect').prop('disabled', false);
            }
        });

        //province get from ajax
        $('#countryFieldNewSuspect').on('change', function(){
            var parentId = $(this).find(':selected').val();
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
                    var provinceFieldNewSuspect = $('#provinceFieldNewSuspect');
                    provinceFieldNewSuspect.empty().append($('<option>', {
                        value: '',
                        text : '--Pilih Provinsi--'
                    }));
                    $.each(data, function(index, data) {
                        provinceFieldNewSuspect.append($('<option>', {
                            value: data.id,
                            text : data.name,
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
        $('#provinceFieldNewSuspect').on('change', function(){
            var parentId = $(this).find(':selected').val();
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
                    var regencyFieldNewSuspect = $('#regencyFieldNewSuspect');
                    regencyFieldNewSuspect.empty().append($('<option>', {
                        value: '',
                        text : '--Pilih Kabupaten/Kota--'
                    }));
                    $.each(data, function(index, data) {
                        regencyFieldNewSuspect.append($('<option>', {
                            value: data.id,
                            text : data.name,
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
        $('#regencyFieldNewSuspect').on('change', function(){
            var parentId = $(this).find(':selected').val();
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
                    var districtFieldNewSuspect = $('#districtFieldNewSuspect');
                    districtFieldNewSuspect.empty().append($('<option>', {
                        value: '',
                        text : '--Pilih Kecamatan--'
                    }));
                    $.each(data, function(index, data) {
                        districtFieldNewSuspect.append($('<option>', {
                            value: data.id,
                            text : data.name,
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
        $('#districtFieldNewSuspect').on('change', function(){
            var parentId = $(this).find(':selected').val();
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
                    var villageFieldNewSuspect = $('#villageFieldNewSuspect');
                    villageFieldNewSuspect.empty().append($('<option>', {
                        value: '',
                        text : '--Pilih Desa/Kelurahan--'
                    }));
                    $.each(data, function(index, data) {
                        villageFieldNewSuspect.append($('<option>', {
                            value: data.id,
                            text : data.name,
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
        $('#identityTypeFieldNewSuspect').on('change', function(){
            var identityTypeId = $(this).find(':selected').val();
            var identityTypeName = $(this).find(':selected').text();

            if(identityTypeId == 15 || identityTypeId == 16){
                $('#identityNumberFieldNewSuspect').prop('disabled', true);
                $('#identityNumberFieldNewSuspect').val(identityTypeName);
            }else{
                $('#identityNumberFieldNewSuspect').prop('disabled', false);
                $('#identityNumberFieldNewSuspect').val('');
            }
        });
    });

    //phone and email
    $('input[name="isExistsPhoneNumberFieldNewSuspect"]').on('change', function(){
        var isExistsPhoneNumberFieldNewSuspect = $('input[name="isExistsPhoneNumberFieldNewSuspect"]:checked').val();
        if(isExistsPhoneNumberFieldNewSuspect == 'true'){
            $('#isAvailablePhoneNumberFieldNewSuspect').prop('disabled', false);
            $('#isAvailablePhoneNumberFieldNewSuspect').prop('checked', true);
            $('#phoneNumberFieldNewSuspect').prop('disabled', false);
            $('#phoneNumberFieldNewSuspect').val('');
        }else{
            $('#isAvailablePhoneNumberFieldNewSuspect').prop('disabled', true);
            $('#isAvailablePhoneNumberFieldNewSuspect').prop('checked', false);
            $('#phoneNumberFieldNewSuspect').val('TIDAK ADA NOMOR TELEPON');
            $('#phoneNumberFieldNewSuspect').prop('disabled', true);
        }
    });
    $('#isAvailablePhoneNumberFieldNewSuspect').on('change', function(){
        if($(this).is(':checked')){
            $('#phoneNumberFieldNewSuspect').val('');
            $('#phoneNumberFieldNewSuspect').prop('disabled', false);
        }else{
            $('#phoneNumberFieldNewSuspect').prop('disabled', true);
            $('#phoneNumberFieldNewSuspect').val('TIDAK BERSEDIA MEMBERIKAN NOMOR TELEPON');
        }
    });
    $('input[name="isExistsEmailFieldNewSuspect"]').on('change', function(){
        var isExistsEmailFieldNewSuspect = $('input[name="isExistsEmailFieldNewSuspect"]:checked').val();
        if(isExistsEmailFieldNewSuspect == 'true'){
            $('#isAvailableEmailFieldNewSuspect').prop('disabled', false);
            $('#isAvailableEmailFieldNewSuspect').prop('checked', true);
            $('#emailFieldNewSuspect').prop('disabled', false);
            $('#emailFieldNewSuspect').val('');
        }else{
            $('#isAvailableEmailFieldNewSuspect').prop('disabled', true);
            $('#isAvailableEmailFieldNewSuspect').prop('checked', false);
            $('#emailFieldNewSuspect').val('TIDAK ADA EMAIL');
            $('#emailFieldNewSuspect').prop('disabled', true);
        }
    });
    $('#isAvailableEmailFieldNewSuspect').on('change', function(){
        if($(this).is(':checked')){
            $('#emailFieldNewSuspect').val('');
            $('#emailFieldNewSuspect').prop('disabled', false);
        }else{
            $('#emailFieldNewSuspect').prop('disabled', true);
            $('#emailFieldNewSuspect').val('TIDAK BERSEDIA MEMBERIKAN EMAIL');
        }
    });

    $('#countryFieldNewSuspect').on('change', function(){
        var countryId = $(this).find(':selected').val();

        //if indonesia show province, regency, district, village
        if(countryId == 'C101'){
            $('.countryChildrenLocationSectionNewSuspect').show();
        }else{
            $('.countryChildrenLocationSectionNewSuspect').hide();
        }
    });

    $(document).ready(function() {
        $('#saveAddNewSuspectForm').on('click', function(){
            var identityStatusFieldNewSuspect = $('input[name="identityStatusFieldNewSuspect"]:checked').val();
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
            var maritalStatusFieldNewSuspect = $('#maritalStatusFieldNewSuspect').find(':selected').val();
            var phoneNumberFieldNewSuspect = $('#phoneNumberFieldNewSuspect').val();
            var isExistsPhoneNumberFieldNewSuspect = $('input[name="isExistsPhoneNumberFieldNewSuspect"]:checked').val();
            var isAvailablePhoneNumberFieldNewSuspect = $('#isAvailablePhoneNumberFieldNewSuspect').is(':checked');
            var emailFieldNewSuspect = $('#emailFieldNewSuspect').val();
            var isExistsEmailFieldNewSuspect = $('input[name="isExistsEmailFieldNewSuspect"]:checked').val();
            var isAvailableEmailFieldNewSuspect = $('#isAvailableEmailFieldNewSuspect').is(':checked');
            var countryFieldNewSuspect = $('#countryFieldNewSuspect').find(':selected').val();
            var provinceFieldNewSuspect = $('#provinceFieldNewSuspect').find(':selected').val();
            var regencyFieldNewSuspect = $('#regencyFieldNewSuspect').find(':selected').val();
            var districtFieldNewSuspect = $('#districtFieldNewSuspect').find(':selected').val();
            var villageFieldNewSuspect = $('#villageFieldNewSuspect').find(':selected').val();
            var addressFieldNewSuspect = $('#addressFieldNewSuspect').val();
            var isUnknownGenderFieldNewSuspect = $('#isUnknownGenderFieldNewSuspect').is(':checked');
            var isUnknownBirthPlaceFieldNewSuspect = $('#isUnknownBirthPlaceFieldNewSuspect').is(':checked');
            var isUnknownBirthDateFieldNewSuspect = $('#isUnknownBirthDateFieldNewSuspect').is(':checked');
            var isUnknownFatherFieldNewSuspect = $('#isUnknownFatherFieldNewSuspect').is(':checked');
            var isUnknownMotherFieldNewSuspect = $('#isUnknownMotherFieldNewSuspect').is(':checked');
            var isUnknownNationalityFieldNewSuspect = $('#isUnknownNationalityFieldNewSuspect').is(':checked');
            var isUnknownMaritalStatusFieldNewSuspect = $('#isUnknownMaritalStatusFieldNewSuspect').is(':checked');
            var isUnknownAddressFieldNewSuspect = $('#isUnknownAddressFieldNewSuspect').is(':checked');

            var suspectSourceId = $('#suspectDeterminationDataSource').find(':selected').val();
            var suspectSourceName = $('#suspectDeterminationDataSource').find(':selected').text();

            //form validation
            if ($('#identityStatusWithIdentityFieldNewSuspect').is(':checked')) {
                    //identity type
                    if (!identityTypeFieldNewSuspect) {
                        // remove small text error di bawah inputan
                        $('#identityTypeFieldNewSuspect').parent().parent().find('small').remove();

                        // append small text error di bawah inputan
                        var small = $('#identityTypeFieldNewSuspect').parent().parent().find('small');
                        if (small.length == 0) {
                            $('#identityTypeFieldNewSuspect').parent().parent().append(
                                '<small class="text-danger">Inputan ini wajib diisi</small>');
                        }
                        return false;
                    }

                    //identity number
                    if (!identityNumberFieldNewSuspect) {
                        // remove small text error di bawah inputan
                        $('#identityNumberFieldNewSuspect').parent().parent().find('small').remove();

                        // append small text error di bawah inputan
                        var small = $('#identityNumberFieldNewSuspect').parent().parent().find('small');
                        if (small.length == 0) {
                            $('#identityNumberFieldNewSuspect').parent().parent().append(
                                '<small class="text-danger">Inputan ini wajib diisi</small>');
                        }
                        return false;
                    }

                    //name
                    if (!nameFieldNewSuspect) {
                        // remove small text error di bawah inputan
                        $('#nameFieldNewSuspect').parent().parent().find('small').remove();

                        // append small text error di bawah inputan
                        var small = $('#nameFieldNewSuspect').parent().parent().find('small');
                        if (small.length == 0) {
                            $('#nameFieldNewSuspect').parent().parent().append(
                                '<small class="text-danger">Inputan ini wajib diisi</small>');
                        }
                        return false;
                    }

                    //gender
                    if (!genderFieldNewSuspect && !isUnknownGenderFieldNewSuspect) {
                        // remove small text error di bawah inputan
                        $('#genderFieldNewSuspect').parent().parent().find('small').remove();

                        // append small text error di bawah inputan
                        var small = $('#genderFieldNewSuspect').parent().parent().find('small');
                        if (small.length == 0) {
                            $('#genderFieldNewSuspect').parent().parent().append(
                                '<small class="text-danger">Inputan ini wajib diisi</small>');
                        }
                        return false;
                    }

                    //birth place
                    if (!birthPlaceFieldNewSuspect && !isUnknownBirthPlaceFieldNewSuspect) {
                        // remove small text error di bawah inputan
                        $('#birthPlaceFieldNewSuspect').parent().parent().find('small').remove();

                        // append small text error di bawah inputan
                        var small = $('#birthPlaceFieldNewSuspect').parent().parent().find('small');
                        if (small.length == 0) {
                            $('#birthPlaceFieldNewSuspect').parent().parent().append(
                                '<small class="text-danger">Inputan ini wajib diisi</small>');
                        }
                        return false;
                    }

                    //birth date
                    if(!birthDateFieldNewSuspect && !isUnknownBirthDateFieldNewSuspect){
                        // remove small text error di bawah inputan
                        $('#birthDateFieldNewSuspect').parent().parent().find('small').remove();

                        // append small text error di bawah inputan
                        var small = $('#birthDateFieldNewSuspect').parent().parent().find('small');
                        if (small.length == 0 && !isUnknownBirthDateFieldNewSuspect) {
                            $('#birthDateFieldNewSuspect').parent().parent().append(
                                '<small class="text-danger">Inputan ini wajib diisi</small>');
                        }
                        return false;
                    }

                    //father name
                    if (!fatherFieldNewSuspect && !isUnknownFatherFieldNewSuspect) {
                        // remove small text error di bawah inputan
                        $('#fatherFieldNewSuspect').parent().parent().find('small').remove();

                        // append small text error di bawah inputan
                        var small = $('#fatherFieldNewSuspect').parent().parent().find('small');
                        if (small.length == 0 && !isUnknownFatherFieldNewSuspect) {
                            $('#fatherFieldNewSuspect').parent().parent().append(
                                '<small class="text-danger">Inputan ini wajib diisi</small>');
                        }
                        return false;
                    }

                    //mother name
                    if (!motherFieldNewSuspect && !isUnknownMotherFieldNewSuspect) {
                        // remove small text error di bawah inputan
                        $('#motherFieldNewSuspect').parent().parent().find('small').remove();

                        // append small text error di bawah inputan
                        var small = $('#motherFieldNewSuspect').parent().parent().find('small');
                        if (small.length == 0 && !isUnknownMotherFieldNewSuspect) {
                            $('#motherFieldNewSuspect').parent().parent().append(
                                '<small class="text-danger">Inputan ini wajib diisi</small>');
                        }
                        return false;
                    }

                    //nationality
                    if(!nationalityFieldNewSuspect && !isUnknownNationalityFieldNewSuspect){
                        // remove small text error di bawah inputan
                        $('#nationalityFieldNewSuspect').parent().parent().find('small').remove();

                        // append small text error di bawah inputan
                        var small = $('#nationalityFieldNewSuspect').parent().parent().find('small');
                        if (small.length == 0 && !isUnknownNationalityFieldNewSuspect) {
                            $('#nationalityFieldNewSuspect').parent().parent().append(
                                '<small class="text-danger">Inputan ini wajib diisi</small>');
                        }
                    }

                    //ethnic
                    if (!ethnicFieldNewSuspect) {
                        // remove small text error di bawah inputan
                        $('#ethnicFieldNewSuspect').parent().parent().find('small').remove();

                        // append small text error di bawah inputan
                        var small = $('#ethnicFieldNewSuspect').parent().parent().find('small');
                        if (small.length == 0) {
                            $('#ethnicFieldNewSuspect').parent().parent().append(
                                '<small class="text-danger">Inputan ini wajib diisi</small>');
                        }
                    }

                    //job
                    if (!jobFieldNewSuspect) {
                        // remove small text error di bawah inputan
                        $('#jobFieldNewSuspect').parent().parent().find('small').remove();

                        // append small text error di bawah inputan
                        var small = $('#jobFieldNewSuspect').parent().parent().find('small');
                        if (small.length == 0) {
                            $('#jobFieldNewSuspect').parent().parent().append(
                                '<small class="text-danger">Inputan ini wajib diisi</small>');
                        }
                    }

                    //religion
                    if (!religionFieldNewSuspect) {
                        // remove small text error di bawah inputan
                        $('#religionFieldNewSuspect').parent().parent().find('small').remove();

                        // append small text error di bawah inputan
                        var small = $('#religionFieldNewSuspect').parent().parent().find('small');
                        if (small.length == 0) {
                            $('#religionFieldNewSuspect').parent().parent().append(
                                '<small class="text-danger">Inputan ini wajib diisi</small>');
                        }
                    }

                    //education
                    if (!educationFieldNewSuspect) {
                        // remove small text error di bawah inputan
                        $('#educationFieldNewSuspect').parent().parent().find('small').remove();

                        // append small text error di bawah inputan
                        var small = $('#educationFieldNewSuspect').parent().parent().find('small');
                        if (small.length == 0) {
                            $('#educationFieldNewSuspect').parent().parent().append(
                                '<small class="text-danger">Inputan ini wajib diisi</small>');
                        }
                    }

                    //marital status
                    if (!maritalStatusFieldNewSuspect && !isUnknownMaritalStatusFieldNewSuspect) {
                        // remove small text error di bawah inputan
                        $('#maritalStatusFieldNewSuspect').parent().parent().find('small').remove();

                        // append small text error di bawah inputan
                        var small = $('#maritalStatusFieldNewSuspect').parent().parent().find('small');
                        if (small.length == 0) {
                            $('#maritalStatusFieldNewSuspect').parent().parent().append(
                                '<small class="text-danger">Inputan ini wajib diisi</small>');
                        }
                    }

                    //isExistsPhoneNumberFieldNewSuspect was checked
                    if (!$('input[name="isExistsPhoneNumberFieldNewSuspect"]').is(':checked')) {
                        // append small text error di bawah inputan
                        var small = $('input[name="isExistsPhoneNumberFieldNewSuspect"]').parent().parent()
                            .find('small');
                        if (small.length == 0) {
                            $('input[name="isExistsPhoneNumberFieldNewSuspect"]').parent().parent().append(
                                '<small class="text-danger">Inputan ini wajib diisi</small>');
                        }
                        return false;
                    }

                    //isExistsEmailFieldNewSuspect was checked
                    if (!$('input[name="isExistsEmailFieldNewSuspect"]').is(':checked')) {
                        // append small text error di bawah inputan
                        var small = $('input[name="isExistsEmailFieldNewSuspect"]').parent().parent().find(
                            'small');
                        if (small.length == 0) {
                            $('input[name="isExistsEmailFieldNewSuspect"]').parent().parent().append(
                                '<small class="text-danger">Inputan ini wajib diisi</small>');
                        }
                        return false;
                    }

                    //country
                    if (!countryFieldNewSuspect) {
                        // remove small text error di bawah inputan
                        $('#countryFieldNewSuspect').parent().parent().find('small').remove();

                        // append small text error di bawah inputan
                        var small = $('#countryFieldNewSuspect').parent().parent().find('small');
                        if (small.length == 0) {
                            $('#countryFieldNewSuspect').parent().parent().append(
                                '<small class="text-danger">Inputan ini wajib diisi</small>');
                        }
                    }

                    //province
                    if (!provinceFieldNewSuspect && countryFieldNewSuspect == 'C101') {
                        // remove small text error di bawah inputan
                        $('#provinceFieldNewSuspect').parent().parent().find('small').remove();

                        // append small text error di bawah inputan
                        var small = $('#provinceFieldNewSuspect').parent().parent().find('small');
                        if (small.length == 0) {
                            $('#provinceFieldNewSuspect').parent().parent().append(
                                '<small class="text-danger">Inputan ini wajib diisi</small>');
                        }
                    }

                    //regency
                    if (!regencyFieldNewSuspect && countryFieldNewSuspect == 'C101') {
                        // remove small text error di bawah inputan
                        $('#regencyFieldNewSuspect').parent().parent().find('small').remove();

                        // append small text error di bawah inputan
                        var small = $('#regencyFieldNewSuspect').parent().parent().find('small');
                        if (small.length == 0) {
                            $('#regencyFieldNewSuspect').parent().parent().append(
                                '<small class="text-danger">Inputan ini wajib diisi</small>');
                        }
                    }

                    //district
                    if (!districtFieldNewSuspect && countryFieldNewSuspect == 'C101') {
                        // remove small text error di bawah inputan
                        $('#districtFieldNewSuspect').parent().parent().find('small').remove();

                        // append small text error di bawah inputan
                        var small = $('#districtFieldNewSuspect').parent().parent().find('small');
                        if (small.length == 0) {
                            $('#districtFieldNewSuspect').parent().parent().append(
                                '<small class="text-danger">Inputan ini wajib diisi</small>');
                        }
                    }

                    //village
                    if (!villageFieldNewSuspect && countryFieldNewSuspect == 'C101') {
                        // remove small text error di bawah inputan
                        $('#villageFieldNewSuspect').parent().parent().find('small').remove();

                        // append small text error di bawah inputan
                        var small = $('#villageFieldNewSuspect').parent().parent().find('small');
                        if (small.length == 0) {
                            $('#villageFieldNewSuspect').parent().parent().append(
                                '<small class="text-danger">Inputan ini wajib diisi</small>');
                        }
                    }

                    //address
                    if (!addressFieldNewSuspect && !isUnknownAddressFieldNewSuspect) {
                        // remove small text error di bawah inputan
                        $('#addressFieldNewSuspect').parent().parent().find('small').remove();

                        // append small text error di bawah inputan
                        var small = $('#addressFieldNewSuspect').parent().parent().find('small');
                        if (small.length == 0) {
                            $('#addressFieldNewSuspect').parent().parent().append(
                                '<small class="text-danger">Inputan ini wajib diisi</small>');
                        }
                    }
                }

            // save add new suspect
            //append all value to table
             $('#suspectDeterminationTable').append(
                    '<tr>' +
                        '<td>' + $('#identityTypeFieldNewSuspect').find(':selected').text() + ' / ' + identityNumberFieldNewSuspect + '</td>' +
                        '<td>' + nameFieldNewSuspect + '</td>' +
                        '<td>' + birthPlaceFieldNewSuspect + '/' + birthDateFieldNewSuspect + '</td>' +
                        '<td>' + suspectSourceName + '</td>' +
                        '<td>' +
                            '<input type="hidden" name="suspectDeterminationSuspectSourceId[]" value="' + suspectSourceId + '">' +
                            '<input type="hidden" name="suspectDeterminationIds[]" value="">' +
                            '<input type="hidden" name="suspectDeterminationIdentityStatus[]" value="' + identityStatusFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationIdentityTypeId[]" value="' + identityTypeFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationIdentityNumber[]" value="' + identityNumberFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationName[]" value="' + nameFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationGenderId[]" value="' + genderFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationBirthPlace[]" value="' + birthPlaceFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationBirthDate[]" value="' + birthDateFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationFatherName[]" value="' + fatherFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationMotherName[]" value="' + motherFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationNationality[]" value="' + nationalityFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationEthnicId[]" value="' + ethnicFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationJobId[]" value="' + jobFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationReligionId[]" value="' + religionFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationEducationId[]" value="' + educationFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationMaritalStatusId[]" value="' + maritalStatusFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationPhoneNumber[]" value="' + phoneNumberFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationIsExistsPhoneNumber[]" value="' + isExistsPhoneNumberFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationIsAvailablePhoneNumber[]" value="' + isAvailablePhoneNumberFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationEmail[]" value="' + emailFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationIsExistsEmail[]" value="' + isExistsEmailFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationIsAvailableEmail[]" value="' + isAvailableEmailFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationCountryId[]" value="' + countryFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationProvinceId[]" value="' + provinceFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationRegencyId[]" value="' + regencyFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationDistrictId[]" value="' + districtFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationVillageId[]" value="' + villageFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationAddress[]" value="' + addressFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationIsUnknownGender[]" value="' + isUnknownGenderFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationIsUnknownBirthPlace[]" value="' + isUnknownBirthPlaceFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationIsUnknownBirthDate[]" value="' + isUnknownBirthDateFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationIsUnknownFather[]" value="' + isUnknownFatherFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationIsUnknownMother[]" value="' + isUnknownMotherFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationIsUnknownNationality[]" value="' + isUnknownNationalityFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationIsUnknownMaritalStatus[]" value="' + isUnknownMaritalStatusFieldNewSuspect + '">' +
                            '<input type="hidden" name="suspectDeterminationIsUnknownAddress[]" value="' + isUnknownAddressFieldNewSuspect + '">' +

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
                url: "{{route('doc.laporan-hasil-gelar-perkara-document.api.case-degree-types', ['accident_id' => $accidentId])}}", // Replace with your backend URL
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
                    $.each(data, function(index, data) {
                        caseDegreeType.append($('<option>', {
                            value: data.id,
                            text: data.name,
                            'data-emp-id': data.emp_id
                        }));
                    });
                },
                error: function(xhr) {
                    // Handle error if needed
                    console.log(xhr.responseText);
                }
            });
        });
    });


    $(document).ready(function() {
        $('.laporanHasilGelarPerkaraApproveValidationFormSubmit').on('click', function(e) {
            e.preventDefault();
            var form = $('#approveValidationForm');
            $('#isApproved').val('true');

            //sweetalert confirm
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan menyetujui dokumen ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Setuju',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                console.log(result);
                if (result.value) {
                    form.submit();
                }
            });
        });

        $('#rejectStatusOption').on('change', function(e) {    
            var rejectStatusOption = $('#rejectStatusOption').find('option:selected').val();

            switch(rejectStatusOption){
                case '4':
                    $('#rejectReasonField').show();
                break;

                case '5':
                    $('#rejectReasonField').show();
                break;

                default:
                    return false;
            }
        });

        $('#rejectReason').on('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    });

    $(document).ready(function() {
        // AJAX Submit for Approve Form
        $("#approveValidationForm").on("submit", function(e) {
            e.preventDefault();
            var form = $(this);
            
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Anda akan menyetujui dokumen ini!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Setuju",
                cancelButtonText: "Batal",
                reverseButtons: true
            }).then((result) => {
                if (result.value || result.isConfirmed) {
                    var btn = form.find("button[type='submit']");
                    btn.prop("disabled", true);
                    
                    $.ajax({
                        url: form.attr("action"),
                        type: "POST",
                        data: form.serialize(),
                        success: function(response) {
                            Swal.fire({
                                icon: "success",
                                title: "Berhasil",
                                text: response.message || "Dokumen berhasil disetujui.",
                                showConfirmButton: false,
                                timer: 1500,
                                timerProgressBar: true,
                                allowOutsideClick: false,
                                allowEscapeKey: false
                            }).then(() => {
                                window.close();
                            });
                        },
                        error: function(xhr) {
                            btn.prop("disabled", false);
                            var msg = "Terjadi kesalahan saat menyetujui dokumen.";
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                icon: "error",
                                title: "Gagal",
                                text: msg,
                                confirmButtonText: "OK"
                            });
                        }
                    });
                }
            });
        });

        // AJAX Submit for Reject Form
        $("#rejectValidationForm").on("submit", function(e) {
            e.preventDefault();
            var form = $(this);
            var btn = form.find("button[type='submit']");
            btn.prop("disabled", true);
            
            $.ajax({
                url: form.attr("action"),
                type: "POST",
                data: form.serialize(),
                success: function(response) {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil",
                        text: response.message || "Dokumen berhasil dikembalikan.",
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then(() => {
                        window.close();
                    });
                },
                error: function(xhr) {
                    btn.prop("disabled", false);
                    var msg = "Terjadi kesalahan saat mengembalikan dokumen.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: "error",
                        title: "Gagal",
                        text: msg,
                        confirmButtonText: "OK"
                    });
                }
            });
        });
    });

</script>
@endpush
