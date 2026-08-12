@extends('cms.layouts.app')

@section('_title', 'Validasi SPDP')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('libs/bootstrap-duallistbox/bootstrap-duallistbox.css') }}" rel="stylesheet">
@endpush

@section('content')
    <a class="btn-back" href="{{ route('cms.case-document-validation.index') }}"><i
            class="bi bi-arrow-left"></i>
            Kembali ke Validasi Perkara</a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Validasi SPDP</h5>

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
                action="#"
                method="POST" enctype="multipart/form-data" id="suratPemberitahuanDimulainyaPenyidikanForm">
                @csrf
                <input type="hidden" name="accidentId" id="accidentId" value="{{ $accidentId }}">

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="accidentNumber">Nomor LP</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
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
                    <label class="fw-bold col-sm-2 col-form-label" for="documentNumber">Nomor Dokumen<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="documentNumber" type="text"
                            class="form-control @error('documentNumber') is-invalid @enderror font-weight-bold"
                            name="documentNumber" value="{{ $suratPemberitahuanDimulainyaPenyidikanDocument->document_number }}" required
                            placeholder="Masukkan Nomor Dokumen">

                        @error('documentNumber')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="documentClassification">Klasifikasi<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="documentClassification" id="documentClassification">
                            <option value="">--Pilih Klasifikasi--</option>
                            @foreach ($documentClassifications as $documentClassification)
                                <option value="{{ $documentClassification->id }}" @if($suratPemberitahuanDimulainyaPenyidikanDocument->document_classification_id){{'selected'}}@endif>{{ $documentClassification->name }}</option>
                            @endforeach
                        </select>

                        @error('documentClassification')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="suratPerintahPenyidikanDocument">No SP Penyidikan<span class="text-danger fs-5">*</span>
                    </label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="suratPerintahPenyidikanDocument"
                            id="suratPerintahPenyidikanDocument">
                            <option value="">--Pilih No Surat Perintah Penyidikan--</option>
                            @foreach ($suratPerintahPenyidikanDocuments as $suratPerintahPenyidikanDocument)
                                <option value="{{ $suratPerintahPenyidikanDocument->id }}"
                                    data-document-date="{{date('Y-m-d', strtotime($suratPerintahPenyidikanDocument->document_date))}}"
                                     @if($suratPemberitahuanDimulainyaPenyidikanDocument->surat_perintah_penyidikan_document_id == $suratPerintahPenyidikanDocument->id){{'selected'}}@endif>{{ $suratPerintahPenyidikanDocument->document_number }}</option>
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
                    <label class="fw-bold col-sm-2 col-form-label">Tanggal SP Penyidikan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input class="form-control" id="suratPerintahPenyidikanDocumentDate"
                            name="suratPerintahPenyidikanDocumentDate" placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{$suratPemberitahuanDimulainyaPenyidikanDocument->suratPerintahPenyidikanDocument ? date('Y-m-d', strtotime($suratPemberitahuanDimulainyaPenyidikanDocument->suratPerintahPenyidikanDocument->document_date)) : null}}" readonly>

                        @error('suratPerintahPenyidikanDocumentDate')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="suratPerintahTugasDocument">No SP Tugas Penyidikan<span class="text-danger fs-5">*</span>
                    </label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="suratPerintahTugasDocument" id="suratPerintahTugasDocument">
                            <option value="">--Pilih No Surat Perintah Tugas--</option>
                            @foreach ($suratPerintahTugasDocuments as $suratPerintahTugasDocument)
                                <option value="{{ $suratPerintahTugasDocument->id }}" @if($suratPemberitahuanDimulainyaPenyidikanDocument->surat_perintah_tugas_document_id == $suratPerintahTugasDocument->id){{'selected'}}@endif>
                                    {{ $suratPerintahTugasDocument->document_number }}</option>
                            @endforeach
                        </select>

                        @error('suratPerintahTugasDocument')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Ada Tersangka?<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <div class="d-flex mb-3">
                            <div class="form-check m-2">
                                <input class="form-check-input isSuspectExists" type="radio" id="suspectExists" name="isSuspectExists"
                                    value="true" @if($suratPemberitahuanDimulainyaPenyidikanDocument->is_suspect_exists == true){{'checked'}}@endif>
                                <label for="suspectExists">
                                    Ada Tersangka
                                </label>
                            </div>
                            <div class="form-check m-2">
                                <input class="form-check-input isSuspectExists" type="radio" id="suspectNotExists" name="isSuspectExists"
                                    value="false" @if($suratPemberitahuanDimulainyaPenyidikanDocument->is_suspect_exists == false){{'checked'}}@endif>
                                <label for="suspectNotExists">
                                    Tidak Ada Tersangka
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="suspectExistsSection" @if($suratPemberitahuanDimulainyaPenyidikanDocument->is_suspect_exists == false)style="display: none;"@endif>
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="suspects">Tersangka yang disebutkan di dalam SPDP
                            saat surat dikirimkan ke Kejaksaan <small class="text-danger">*)Jika Ada</small></label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            <select class="form-control select2-multiple" name="suspects[]" id="suspects" multiple>
                                @foreach ($suspects as $suspect)
                                    @php
                                        $isSelected = in_array($suspect->id, $selectedSuspects);
                                    @endphp

                                    <option value="{{ $suspect->id }}" @if($isSelected){{'selected'}}@endif>{{ $suspect->name }}</option>
                                @endforeach
                            </select>

                            @error('suspects')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div id="suspectNotExistsSection" @if($suratPemberitahuanDimulainyaPenyidikanDocument->is_suspect_exists == true)style="display: none;"@endif>
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="informant">Pelapor<span class="text-danger fs-5">*</span></label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            <select class="form-control select2" name="informant" id="informant">
                                <option value="">--Pilih Pelapor--</option>
                                @foreach ($informants as $informant)
                                    <option value="{{ $informant->id }}">{{ $informant->name }}</option>
                                @endforeach
                            </select>

                            @error('informant')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="reportedSuspect">Terlapor<span class="text-danger fs-5">*</span></label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                            <select class="form-control select2" name="reportedSuspect" id="reportedSuspect">
                                <option value="">--Pilih Terlapor--</option>
                                @foreach ($reportedSuspects as $reportedSuspect)
                                    <option value="{{ $reportedSuspect->id }}">{{ $reportedSuspect->name }}</option>
                                @endforeach
                            </select>

                            @error('reportedSuspect')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="prosecutor">Nama Kejaksaan Penerima<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="prosecutor" id="prosecutor">
                            <option value="">--Pilih Kejaksaan--</option>
                            @foreach ($prosecutors as $prosecutor)
                                <option value="{{ $prosecutor->id }}" @if($suratPemberitahuanDimulainyaPenyidikanDocument->prosecutor_id == $prosecutor->id){{'selected'}}@endif>{{ $prosecutor->name }}</option>
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
                    <label class="fw-bold col-sm-2 col-form-label" for="appendix">Lampiran<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="appendix" type="text"
                            class="form-control @error('appendix') is-invalid @enderror onlyIntegerInput" name="appendix"
                            value="{{ $suratPemberitahuanDimulainyaPenyidikanDocument->appendix }}" required placeholder="Lampiran">

                        @error('appendix')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Tanggal Ditandatangani Dokumen<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input class="form-control" id="documentDate" name="documentDate"
                            placeholder="YYYY-MM-DD" autocomplete="off" value="{{ $suratPemberitahuanDimulainyaPenyidikanDocument->document_date }}"
                            data-provide="datepicker">

                        @error('documentDate')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Penandatanganan Surat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="signatory" id="signatory">
                            <option value="">--Pilih Yang Menandatangani--</option>
                            @foreach ($authorizedSignatories as $data)
                                @php
                                    $positionName = ($data->position) ? $data->position->name : '-';
                                @endphp
                                <option value="{{$data->id}}" data-register-number="{{$data->register_number}}" @if($suratPemberitahuanDimulainyaPenyidikanDocumentOfficers->where('class', 'SIGNATORY')->where('register_number', $data->register_number)->count() != 0){{'selected'}}@endif>{{$data->register_number . ' - ' . $data->full_name . ' | ' . $positionName}}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">(*Apabila daftar yang menandatangani kosong silahkan hubungi Helpdesk untuk
                            mendapat bantuan)</small>

                        @error('signatory')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="court">Pengadilan sebagai Tembusan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2" name="court" id="court">
                            <option value="">--Pilih Pengadilan--</option>
                            @foreach ($courts as $court)
                                <option value="{{ $court->id }}" @if($suratPemberitahuanDimulainyaPenyidikanDocument->court_id == $court->id){{'selected'}}@endif>{{ $court->name }}</option>
                            @endforeach
                        </select>

                        @error('court')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="carbonCopies">Tembusan Lainnya</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <div id="carbonCopiesContainer">
                            @if(!empty($suratPemberitahuanDimulainyaPenyidikanDocument->carbon_copies))
                                @foreach($suratPemberitahuanDimulainyaPenyidikanDocument->carbon_copies as $carbonCopy)
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="carbonCopies[]" value="{{$carbonCopy}}">
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
                                                    href="@if (isset($suratPemberitahuanDimulainyaPenyidikanDocument->documentCategory)) {{ route($suratPemberitahuanDimulainyaPenyidikanDocument->documentCategory->base_route . '.download', ['id' => $suratPemberitahuanDimulainyaPenyidikanDocument->id, 'accident_id' => $suratPemberitahuanDimulainyaPenyidikanDocument->accident->id, 'document_category_id' => $suratPemberitahuanDimulainyaPenyidikanDocument->documentCategory->id]) }} @endif"
                                                    class="btn btn-primary btn-lg"><i class="bi bi-printer"></i> Cetak</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if(isset($suratPemberitahuanDimulainyaPenyidikanDocument->suratPemberitahuanDimulainyaPenyidikanDocumentAttachment->name))
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-grid gap-2">
                                                <a href="{{ asset('documents/attachments/' . $suratPemberitahuanDimulainyaPenyidikanDocument->suratPemberitahuanDimulainyaPenyidikanDocumentAttachment->name) }}"
                                                    class="btn btn-secondary btn-lg" target="_blank">
                                                    <i class="bi bi-file-earmark"></i> Lihat Dokumen Unggah</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if(isset($suratPemberitahuanDimulainyaPenyidikanDocument->suratPemberitahuanDimulainyaPenyidikanDocumentAttachment))
                            @if(in_array($suratPemberitahuanDimulainyaPenyidikanDocument->suratPemberitahuanDimulainyaPenyidikanDocumentAttachment->extension, ['doc', 'docx']))
                                <!-- 16:9 aspect ratio -->
                                <br>
                                <div class="embed-responsive embed-responsive-16by1">
                                    <iframe style="top:0;left:0;width:100%;height:1024px;" class="embed-responsive-item" src="{{ $filePreviewUrl }}" allowfullscreen></iframe>
                                </div>
                            @endif
                        @endif

                        <div class="input-group row mb-3 ms-0 mt-2">
                            <label class="fw-bold col-sm-2 col-form-label text-lg" for="id"><h4>Pasal Yang Dikenakan<span class="text-danger fs-5">*</span></h4></label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12 align-self-center">
                                <input id="id" type="text" readonly
                                    class="form-control form-control-lg @error('crimeConstitutionText') is-invalid @enderror onlyIntegerInput" name="crimeConstitutionText"
                                    value="{{ $crimeConstitutionText }}" required placeholder="Pasal Yang Dikenakan">
              
                                
                                @error('crimeConstitutionText')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0 mt-2">
                            <label class="fw-bold col-sm-2 col-form-label text-lg" for="id"><h4>ID Dokumen<span class="text-danger fs-5">*</span></h4></label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12 align-self-center">
                                <input id="id" type="text" readonly
                                    class="form-control form-control-lg @error('documentId') is-invalid @enderror onlyIntegerInput" name="documentId"
                                    value="{{ $suratPemberitahuanDimulainyaPenyidikanDocument->id }}" required placeholder="ID Dokumen">
              
                                
                                @error('documentId')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                                @if($suratPemberitahuanDimulainyaPenyidikanDocument->is_legacy == false)
                                <div class="form-group mt-3 d-flex justify-content-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="isScannedQRCode" name="isScannedQRCode"
                                            value="true" aria-label="...">
                                        <label>
                                            <h5><b>Sudah Melakukan Check Scan QR Code</b></h5>
                                        </label>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>


                    </div>
                </div>

            </form>

            <div class="mt-4">
                <div class="text-center">
                    <button type="button" class="btn btn-danger" id="suratPemberitahuanDimulainyaPenyidikanRejectValidationButton" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="bi bi-box-arrow-in-down-left"></i> KEMBALIKAN
                    </button>

                    <button type="button" class="btn btn-primary" id="suratPemberitahuanDimulainyaPenyidikanApproveValidationButton" data-bs-toggle="modal" data-bs-target="#approveModal"
                       @if($suratPemberitahuanDimulainyaPenyidikanDocument->is_legacy == false){{'disabled'}}@endif
                    >
                        <i class="bi bi-check2"></i> VALIDASI
                    </button>

                    {{-- <form action="{{route('cms.case-document-validation.module.surat-pemberitahuan-dimulainya-penyidikan-document.validation.approve', ['accident_id' => $suratPemberitahuanDimulainyaPenyidikanDocument->accident->id, 'id' => $suratPemberitahuanDimulainyaPenyidikanDocument->id, 'document_category_id' => $suratPemberitahuanDimulainyaPenyidikanDocument->document_category_id])}}" method="POST" id="approveValidationForm" class="d-inline">
                        @csrf
                        @method('POST')
                        <input type="hidden" name="isApproved" id="isApproved" value="true">
                        <input type="hidden" name="isLegacy" id="isLegacy" value="false">
                        <button type="submit" class="btn btn-primary suratPemberitahuanDimulainyaPenyidikanApproveValidationFormSubmit" id="suratPemberitahuanDimulainyaPenyidikanApproveValidationFormSubmit">
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

            <form action="{{route('cms.case-document-validation.module.surat-pemberitahuan-dimulainya-penyidikan-document.validation.approve', ['accident_id' => $suratPemberitahuanDimulainyaPenyidikanDocument->accident->id, 'id' => $suratPemberitahuanDimulainyaPenyidikanDocument->id, 'document_category_id' => $suratPemberitahuanDimulainyaPenyidikanDocument->document_category_id])}}" 
                method="POST" id="approveValidationForm" class="d-inline">
                <div class="modal-body">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="isApproved" id="isApproved" value="true">
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="isLegacy" name="isLegacy"
                                value="true" aria-label="..."
                                @if ($suratPemberitahuanDimulainyaPenyidikanDocument->is_legacy == true || old('isLegacy') == 1) {{ 'checked' }} @endif>
                            <label for="isLegacy">
                                <b>Tandai Dokumen Legacy</b>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="isApplyLeagcySettingToAllDocument" name="isApplyLeagcySettingToAllDocument"
                                value="true" aria-label="..."
                                @if (old('isApplyLeagcySettingToAllDocument') == 1) {{ 'checked' }} @endif>
                            <label for="isApplyLeagcySettingToAllDocument">
                                <b>Tandai Setting Legacy Diterapkan Untuk Semua Dokumen Lainnya</b>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="suratPemberitahuanDimulainyaPenyidikanApproveValidationFormSubmit">Validasi</button>
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

            <form action="{{route('cms.case-document-validation.module.surat-pemberitahuan-dimulainya-penyidikan-document.validation.reject', ['accident_id' => $suratPemberitahuanDimulainyaPenyidikanDocument->accident->id, 'id' => $suratPemberitahuanDimulainyaPenyidikanDocument->id, 'document_category_id' => $suratPemberitahuanDimulainyaPenyidikanDocument->document_category_id])}}" 
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
                    <button type="submit" class="btn btn-primary" id="suratPemberitahuanDimulainyaPenyidikanRejectValidationFormSubmit">Kembalikan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Reject-->
@endsection

@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js" defer></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" defer></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js" defer></script>
<script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
<script src="{{asset('libs/bootstrap-duallistbox/jquery.bootstrap-duallistbox.js')}}"></script>
<script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        setInterval(function() {
            $('#attentionBox').toggleClass('alert-danger alert-warning');
        }, 1000);

        $('#documentDate').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: "true",
            endDate: new Date()
        });
        $('#documentDate').keydown(function(e) {
            e.preventDefault();
            return false;
        });
    });
    
    $(document).ready(function() {
        $('#isScannedQRCode').on('change', function() {
            if($(this).is(':checked')) {
                $('#suratPemberitahuanDimulainyaPenyidikanApproveValidationButton').prop('disabled', false);
            } else {
                $('#suratPemberitahuanDimulainyaPenyidikanApproveValidationButton').prop('disabled', true);
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

        /*$("#suspect").bootstrapDualListbox({
            nonSelectedListLabel: 'Tersangka Belum Dipilih',
            selectedListLabel: 'Tersangka Dipilih',
            preserveSelectionOnMove: 'moved',
            moveOnSelect: false,
        });*/
    });

    $(document).ready(function() {
        $('#court').on('change', function() {
            var courtId = $(this).find(':selected').val();
            var courtName = $(this).find(':selected').text();
            var modifiedCourtName = courtName.toLowerCase().replace(/\b\w/g, function(match) {
                                        return match.toUpperCase();
                                    });

            if(courtId){
                //check is #carbonCopyCourt exist
                var isCarbonCopyCourtExist = $('#carbonCopyCourt').length;

                if(isCarbonCopyCourtExist){
                    $('#carbonCopyCourt').val('Ketua ' + modifiedCourtName);
                }else{
                    var inputGroup = '<div class="input-group mb-2">' +
                        '<input type="text" class="form-control" id="carbonCopyCourt" name="carbonCopies[]" value="Ketua ' + modifiedCourtName + '">' +
                        '<div class="input-group-append">' +
                        '</div>' +
                        '</div>';

                    $("#carbonCopiesContainer").append(inputGroup);
                }
            }else{
                $('#carbonCopyCourt').closest(".input-group").remove();
            }
        });

        $(".addCarbonCopiesButton").click(function() {
            var inputGroup = '<div class="input-group mb-2">' +
                '<input type="text" class="form-control" name="carbonCopies[]" value="">' +
                '<div class="input-group-append">' +
                '<button class="btn btn-outline-danger removeCarbonCopiesButton" type="button">Hapus</button>' +
                '</div>' +
                '</div>';

            $("#carbonCopiesContainer").append(inputGroup);
        });

        // Menghapus input ketika tombol "Remove" di klik
        $(document).on("click", ".removeCarbonCopiesButton", function() {
            $(this).closest(".input-group").remove();
        });
    });

    $(document).ready(function() {
        $('.onlyIntegerInput').on('keypress', function(event) {
            var charCode = (event.which) ? event.which : event.keyCode;

            // Allow only numeric input (disallow decimal point)
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            event.preventDefault();
            }
        });

        $('#suratPerintahPenyidikanDocument').on('change', function() {
            var documentDate = $(this).find(':selected').data('document-date');
            $('#suratPerintahPenyidikanDocumentDate').val(documentDate);
        });

        $('.isSuspectExists').on('change', function() {
            var isSuspectExists = $(this).val();

            if (isSuspectExists == 'true') {
                $('#suspectExistsSection').show();
                $('#suspectNotExistsSection').hide();
            } else if (isSuspectExists == 'false') {
                $('#suspectExistsSection').hide();
                $('#suspectNotExistsSection').show();
            }
        });
    });

    $(document).ready(function() {
        $('.suratPemberitahuanDimulainyaPenyidikanApproveValidationFormSubmit').on('click', function(e) {
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

                case '6':
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
