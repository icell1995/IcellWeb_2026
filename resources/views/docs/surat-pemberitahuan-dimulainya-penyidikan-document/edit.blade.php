@php
    $_title = 'Edit Surat Pemberitahuan Dimulainya Penyidikan (SPDP)';
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
            <h5 class="fw-bold text-blue-dark">Edit Surat Pemberitahuan Dimulainya Penyidikan (SPDP)</h5>

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
                action="{{ route('doc.surat-pemberitahuan-dimulainya-penyidikan-document.update', ['accident_id' => $accidentId, 'id'=> $suratPemberitahuanDimulainyaPenyidikanDocumentId]) }}"
                method="POST" enctype="multipart/form-data" id="suratPemberitahuanDimulainyaPenyidikanForm" novalidate>
                @csrf
                <input type="hidden" name="accidentId" id="accidentId" value="{{ $accidentId }}">
		<input type="hidden" name="specialInfo" id="specialInfo" value="{{ $accident->special_info }}">

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
                    <label class="fw-bold col-sm-2 col-form-label" for="documentNumber">Nomor Dokumen<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
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
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
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
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
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
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
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
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
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
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
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
                    <div class="alert alert-success">
                        <div class="text-center">
                            <b>
                                PASTIKAN 
                                <a href="{{route('doc.laporan-hasil-gelar-perkara-document.create', ['accident_id' => $accidentId])}}">
                                    LHGP (PENETAPAN TERSANGKA)
                                </a> 
                                LALU 
                                <a href={{route('doc.surat-ketetapan-tentang-penetapan-tersangka-document.create', ['accident_id' => $accidentId])}}>
                                    SURAT KETETAPAN TENTANG PENETAPAN TERSANGKA
                                </a> 
                                SUDAH DIBUAT SEBELUM MEMBUAT DOKUMEN INI
                            </b>
                        </div>
                    </div>
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="suspects">Tersangka yang disebutkan di dalam SPDP
                            saat surat dikirimkan ke Kejaksaan <small class="text-danger">*)Jika Ada</small></label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                            <select class="form-control select2-multiple" name="suspects[]" id="suspects" multiple>
                                @foreach ($suspects as $suspect)
                                    @php
                                        $isSelected = in_array($suspect->id, $selectedSuspects);
                                    @endphp

                                    <option value="{{ $suspect->id }}" @if($isSelected){{'selected'}}@endif>{{ 
                                        $suspect->name 
                                    }}
                                    @foreach($suspect->suratKetetapanTentangPenetapanTersangkaDocument as $item)
                                        {{
                                            " ( " . $item->document_number . " )"
                                        }}        
                                    @endforeach
                                    </option>
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
		    @if($accident->special_info != 'TABRAK_LARI')
                        <div class="alert alert-success">
                            <div class="text-center">
                                <b>
                                    PASTIKAN 
                                    <a href="{{route('view_produktivitas_accident', ['accident_id' => request()->query('accident_id'), 'page'=>'participants'])}}">
                                        DATA TERLAPOR
                                    </a> 
                                    SUDAH DIMASUKAN SEBELUM MEMBUAT DOKUMEN INI
                                </b>
                            </div>
                        </div>
                    @endif
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="informant">Pelapor</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12">
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
                        <label class="fw-bold col-sm-2 col-form-label" for="reportedPerson">Terlapor</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                            <select class="form-control select2" name="reportedPerson" id="reportedPerson">
                                <option value="">--Pilih Terlapor--</option>
                                @foreach ($reportedPersons as $reportedPerson)
                                    <option value="{{ $reportedPerson->id }}">{{ $reportedPerson->name }}</option>
                                @endforeach
                            </select>

                            @error('reportedPerson')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="prosecutor">Nama Kejaksaan Penerima<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
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
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
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
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
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
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
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
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
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
                    <div class="col-lg-10 col-md-10 col-12">
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

                <hr>
		
		@if(strtotime($accident->report_date) < strtotime('2024-01-01') || $suratPemberitahuanDimulainyaPenyidikanDocument->is_legacy == true || $accident->police->is_whitelisted_document_legacy == true)
                	@include('docs.components.form.checkbox.is-legacy', ['document' => $suratPemberitahuanDimulainyaPenyidikanDocument])
		@endif

                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg" id="suratPemberitahuanDimulainyaPenyidikanFormSubmit">
                        <i class="bi bi-save"></i> {{ __('Simpan')}}
                    </button>
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-danger btn-lg">
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

@if(strtotime($accident->report_date) < strtotime('2024-01-01') || $suratPemberitahuanDimulainyaPenyidikanDocument->is_legacy == true || $accident->police->is_whitelisted_document_legacy == true)
	@include('docs.components.form.checkbox.is-legacy-js')
@endif

<script type="text/javascript">
    $(document).ready(function() {
        setInterval(function() {
            $('#attentionBox').toggleClass('alert-danger alert-warning');
        }, 1000);

        $('#documentDate').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: "true",
            orientation: 'auto bottom',
            startDate: new Date()
        });
        $('#documentDate').keydown(function(e) {
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
    });

    $(document).ready(function() {
        $('#court').on('change', function() {
            var courtId = $(this).find(':selected').val();
            var courtName = $(this).find(':selected').text();
            var modifiedCourtName = courtName.toLowerCase().replace(/\b\w/g, function(match) {
                                        return match.toUpperCase();
                                    });

            if(courtId){
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
            if (documentDate) {
                clearFieldError($('#suratPerintahPenyidikanDocumentDate'));
            }
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

            // Clear error saat toggle
            $('#suspects, #reportedPerson, #informant').removeClass('is-invalid');
            $('#suspects, #reportedPerson, #informant').next('.select2-container').find('.select2-selection').removeClass('border border-danger is-invalid');
            $('#suspectExistsSection, #suspectNotExistsSection').find('.frontend-error, .invalid-feedback').remove();
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

    // Continuous watcher untuk membersihkan highlight jika value sudah terisi
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
        $('#suratPemberitahuanDimulainyaPenyidikanFormSubmit').on('click', function(e) {
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

                if ($field.is('table')) {
                    $field.addClass('border border-danger is-invalid');
                    var $wrapper = $field.closest('.table-responsive, .input-group');
                    var $container = $wrapper.length ? $wrapper : $field;
                    $container.siblings('.frontend-error, .invalid-feedback').remove();
                    $container.next('.frontend-error, .invalid-feedback').remove();
                    $container.after('<div class="invalid-feedback d-block frontend-error">' + message + '</div>');
                    errors.push(message);
                    return;
                } else if ($field.is(':radio')) {
                    $field.addClass('is-invalid');
                    var $container = $field.closest('.d-flex, .form-check-group, .row');
                    $container.siblings('.frontend-error, .invalid-feedback').remove();
                    $container.next('.frontend-error, .invalid-feedback').remove();
                    $container.after('<div class="invalid-feedback d-block frontend-error">' + message + '</div>');
                    errors.push(message);
                    return;
                } else {
                    $field.addClass('is-invalid');
                }
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

            // 1. Validasi Field Utama Berbintang (*)
            checkInput('#documentNumber', 'Nomor Dokumen');
            checkSelect('#documentClassification', 'Klasifikasi Dokumen');
            checkSelect('#suratPerintahPenyidikanDocument', 'No SP Penyidikan');
            checkInput('#suratPerintahPenyidikanDocumentDate', 'Tanggal SP Penyidikan');
            checkSelect('#suratPerintahTugasDocument', 'No SP Tugas Penyidikan');

            // 2. Validasi Tersangka / Terlapor Berdasarkan Pilihan Radio (*)
            var isSuspectExistsVal = $('input[name="isSuspectExists"]:checked').val();
            if (isSuspectExistsVal === 'true') {
                checkSelect('#suspects', 'Tersangka yang disebutkan di dalam SPDP');
            } else {
                var specialInfo = ($('#specialInfo').val() || '').trim();
                if (specialInfo !== 'TABRAK_LARI') {
                    checkSelect('#reportedPerson', 'Terlapor');
                }
            }

            // 3. Validasi Kejaksaan, Lampiran, Tanggal, Penandatangan, dan Pengadilan (*)
            checkSelect('#prosecutor', 'Nama Kejaksaan Penerima');
            checkInput('#appendix', 'Lampiran');
            checkInput('#documentDate', 'Tanggal Ditandatangani Dokumen');
            var docDateVal = ($('#documentDate').val() || '').trim();
            if (docDateVal) {
                var selectedDate = new Date(docDateVal);
                var today = new Date();
                today.setHours(0, 0, 0, 0);
                selectedDate.setHours(0, 0, 0, 0);
                if (selectedDate < today) {
                    markError('#documentDate', 'Tanggal Ditandatangani Dokumen minimal hari ini (tidak boleh tanggal kemarin/masa lalu)');
                }
            }
            checkSelect('#signatory', 'Penandatanganan Surat');
            checkSelect('#court', 'Pengadilan sebagai Tembusan');

            // Jika ada error di frontend, scroll ke field pertama
            if (errors.length > 0) {
                scrollToFirstError();
                return false;
            }

            // Lakukan validasi di sisi server menggunakan Ajax
            $.ajax({
                url: "{{ route('doc.surat-pemberitahuan-dimulainya-penyidikan-document.api.validate-request-form', ['accident_id' => $accidentId]) }}",
                type: 'POST',
                dataType: 'json',
                data: $('#suratPemberitahuanDimulainyaPenyidikanForm').serialize(),
                success: function(response) {
                    // Cek jika validasi berhasil di sisi server
                    if (response.success) {
                        Swal.fire({
                            title: 'Berhasil',
                            text: response.message || 'Silahkan menunggu proses simpan data',
                            icon: 'success',
                            confirmButtonText: 'Ok'
                        }).then((result) => {
                            $('#suratPemberitahuanDimulainyaPenyidikanForm')[0].submit();
                        });
                    }
                },
                error: function(xhr) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.code == '422' && response.errors) {
                            $.each(response.errors, function(key, messages) {
                                var msg = Array.isArray(messages) ? messages[0] : messages;
                                var $target = $('#' + key + ', [name="' + key + '"]');
                                if ($target.length) {
                                    markError($target, msg);
                                } else if (key === 'reportedPerson') {
                                    markError('#reportedPerson', msg);
                                } else if (key === 'suspects') {
                                    markError('#suspects', msg);
                                } else if (key === 'carbonCopies') {
                                    markError('#court', msg);
                                }
                            });
                            scrollToFirstError();
                        } else {
                            var message = response.message || response.errors || 'Terjadi kesalahan saat memproses data.';
                            Swal.fire({
                                icon: 'error',
                                title: 'Perhatian',
                                text: typeof message === 'string' ? message : JSON.stringify(message)
                            });
                        }
                    } catch (e) {
                        console.error(e);
                    }
                }
            });
        });
    });
</script>
@endpush
