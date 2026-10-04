@php
    $_title = 'Edit Surat Permintaan Izin Penyitaan (S-12)';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
    <style>
        .informasi-dokumen-card .form-group.row {
            margin-bottom: 1rem;
            margin-left: 0;
            margin-right: 0;
        }
        .informasi-dokumen-card .col-form-label {
            padding-top: calc(0.375rem + 1px);
            padding-bottom: calc(0.375rem + 1px);
            margin-bottom: 0;
            line-height: 1.5;
            font-size: 0.95rem;
        }
        .informasi-dokumen-card .form-control {
            height: 38px;
            font-size: 0.95rem;
            padding: 0.375rem 0.75rem;
            line-height: 1.5;
        }
        .informasi-dokumen-card .select2-container--bootstrap4 .select2-selection--single {
            height: 38px !important;
            line-height: 1.5;
            padding: 0.375rem 0.75rem;
            display: flex;
            align-items: center;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            position: relative;
        }
        .informasi-dokumen-card .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
            line-height: 1.5;
            padding-left: 0;
            padding-right: 36px;
            color: #495057;
            width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .informasi-dokumen-card .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
            height: 36px;
            position: absolute;
            top: 1px;
            right: 3px;
            width: 20px;
        }
        .informasi-dokumen-card .select2-container--bootstrap4 .select2-selection--single .select2-selection__clear {
            position: absolute;
            top: 50%;
            transform: translateY(calc(-50% - 2px));
            right: 25px;
            margin: 0 !important;
            float: none;
            width: 18px;
            height: 18px;
            padding: 0 !important;
            line-height: 18px;
            text-align: center;
            font-size: 1.15rem;
            font-weight: bold;
            color: #6c757d;
            background: transparent;
            border-radius: 50%;
            cursor: pointer;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .informasi-dokumen-card .select2-container--bootstrap4 .select2-selection--single .select2-selection__clear:hover {
            color: #dc3545;
            background: rgba(220, 53, 69, 0.1);
        }
    </style>
@endpush

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}">
        <i class="bi bi-arrow-left"></i> Kembali ke Progres Perkara
    </a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Edit Surat Permintaan Izin Penyitaan (S-12)</h5>
            <div class="alert alert-danger" id="attentionBox">
                <div class="text-center">
                    <b>
                        PERHATIAN !<br /><br />
                        DATA INI WAJIB DIISI DENGAN DETAIL DAN LENGKAP KARENA AKAN DIPERTUKARKAN DENGAN APARAT PENEGAK HUKUM
                        LAINNYA DALAM KERANGKA SISTEM PENANGANAN PERKARA TERPADU BERBASIS TEKNOLOGI INFORMASI (SPPT-TI).
                    </b>
                </div>
            </div>

            <!-- Error Alerts -->
            @if ($errors->any())
                <div class="card-body">
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
                <div class="card-body">
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                </div>
            @endif
        </div>

        <div class="box-body">
            <form action="{{ route('doc.surat-permintaan-izin-penyitaan-document.update', ['id' => $id, 'accident_id' => $accidentId]) }}"
                method="POST" enctype="multipart/form-data" id="suratPermintaanIzinPenyitaanForm" novalidate>
                @csrf
                <input type="hidden" name="accident_id" id="accident_id" value="{{ $accidentId }}">

                <!-- 1. DATA DOKUMEN UTAMA -->
                <div class="card mb-4 informasi-dokumen-card">
                    <div class="card-header bg-light">
                        <h6 class="fw-bold mb-0 text-blue-dark">Informasi Dokumen</h6>
                    </div>
                    <div class="card-body">
                        <!-- Nomor LP -->
                        <div class="form-group row mb-3 align-items-center">
                            <label class="fw-bold col-lg-3 col-md-3 col-sm-12 col-form-label" for="accidentNumber">Nomor LP</label>
                            <div class="col-lg-9 col-md-9 col-sm-12">
                                <input id="accidentNumber" type="text"
                                    class="form-control font-weight-bold"
                                    name="accidentNumber" value="{{ $accident->no_lp }}" readonly>
                            </div>
                        </div>

                        <!-- Nomor Dokumen S-12 -->
                        <div class="form-group row mb-3 align-items-center">
                            <label class="fw-bold col-lg-3 col-md-3 col-sm-12 col-form-label" for="document_number">
                                Nomor Dokumen S-12<span class="text-danger fs-5">*</span>
                            </label>
                            <div class="col-lg-9 col-md-9 col-sm-12">
                                <input id="document_number" type="text"
                                    class="form-control @error('document_number') is-invalid @enderror font-weight-bold"
                                    name="document_number"
                                    value="{{ old('document_number', $document->document_number) }}" required
                                    placeholder="Contoh: B/12/I/2026/Satlantas">
                                @error('document_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Klasifikasi -->
                        <div class="form-group row mb-3 align-items-center">
                            <label class="fw-bold col-lg-3 col-md-3 col-sm-12 col-form-label" for="documentClassification">
                                Klasifikasi<span class="text-danger fs-5">*</span>
                            </label>
                            <div class="col-lg-9 col-md-9 col-sm-12">
                                <select class="form-control select2 @error('documentClassification') is-invalid @enderror" name="documentClassification" id="documentClassification" required>
                                    <option value="">-- Pilih Klasifikasi --</option>
                                    @foreach ($documentClassifications as $dc)
                                        <option value="{{ $dc->id }}" {{ (old('documentClassification', old('classification', $document->document_classification_id)) == $dc->id) ? 'selected' : '' }}>{{ $dc->name }}</option>
                                    @endforeach
                                </select>
                                @error('documentClassification')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Tanggal Dokumen S-12 -->
                        <div class="form-group row mb-3 align-items-center">
                            <label class="fw-bold col-lg-3 col-md-3 col-sm-12 col-form-label" for="document_date">
                                Tanggal Dokumen S-12<span class="text-danger fs-5">*</span>
                            </label>
                            <div class="col-lg-9 col-md-9 col-sm-12">
                                <input class="form-control datepicker @error('document_date') is-invalid @enderror"
                                    id="document_date" name="document_date" placeholder="YYYY-MM-DD"
                                    autocomplete="off"
                                    value="{{ old('document_date', $document->document_date ? \Carbon\Carbon::parse($document->document_date)->format('Y-m-d') : date('Y-m-d')) }}" required>
                                @error('document_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- No SP Penyidikan (Selector) & Tgl SP Penyidikan -->
                        <div class="form-group row mb-3 align-items-center">
                            <label class="fw-bold col-lg-3 col-md-3 col-sm-12 col-form-label" for="surat_perintah_penyidikan_document_id">
                                Nomor SP Penyidikan<span class="text-danger fs-5">*</span>
                            </label>
                                                        <div class="col-lg-5 col-md-5 col-sm-12 mb-2 mb-md-0">
                                @php
                                    $currentSprindikNumber = old('sprindik_number', $document->sprindik_number);
                                    $hasSprindikMatch = false;
                                @endphp
                                <select class="form-control select2-sprindik-tags @error('sprindik_number') is-invalid @enderror"
                                    id="sprindik_number" name="sprindik_number" required>
                                    <option value="">-- Pilih atau Ketik No SP Penyidikan --</option>
                                    @if(isset($suratPerintahPenyidikanDocuments) && $suratPerintahPenyidikanDocuments->isNotEmpty())
                                        @foreach ($suratPerintahPenyidikanDocuments as $sprindikDoc)
                                            @php
                                                $isMatch = ($currentSprindikNumber == $sprindikDoc->document_number);
                                                if ($isMatch) $hasSprindikMatch = true;
                                                $sprindikLeader = $sprindikDoc->suratPerintahPenyidikanDocumentOfficers ? $sprindikDoc->suratPerintahPenyidikanDocumentOfficers->where('class', 'LEADER')->first() : null;
                                            @endphp
                                            <option value="{{ $sprindikDoc->document_number }}"
                                                data-id="{{ $sprindikDoc->id }}"
                                                data-date="{{ $sprindikDoc->document_date ? \Carbon\Carbon::parse($sprindikDoc->document_date)->format('Y-m-d') : '' }}"
                                                data-leader-nrp="{{ $sprindikLeader ? $sprindikLeader->register_number : '' }}"
                                                {{ $isMatch ? 'selected' : '' }}>
                                                {{ $sprindikDoc->document_number }}
                                            </option>
                                        @endforeach
                                    @endif
                                    @if($currentSprindikNumber && !$hasSprindikMatch)
                                        <option value="{{ $currentSprindikNumber }}" selected>{{ $currentSprindikNumber }}</option>
                                    @endif
                                </select>
                                <input type="hidden" id="surat_perintah_penyidikan_document_id" name="surat_perintah_penyidikan_document_id" value="{{ old('surat_perintah_penyidikan_document_id', isset($document) ? $document->surat_perintah_penyidikan_document_id : '') }}">
                                @error('sprindik_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label class="fw-bold col-lg-2 col-md-2 col-sm-12 col-form-label text-md-end" for="sprindik_date">
                                Tgl SP Penyidikan<span class="text-danger fs-5">*</span>
                            </label>
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <input class="form-control datepicker @error('sprindik_date') is-invalid @enderror"
                                    id="sprindik_date" name="sprindik_date" placeholder="YYYY-MM-DD"
                                    autocomplete="off"
                                    value="{{ old('sprindik_date', $document->sprindik_date ? \Carbon\Carbon::parse($document->sprindik_date)->format('Y-m-d') : '') }}" required>
                                @error('sprindik_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Nomor & Tanggal Surat Perintah Penyitaan (Optional) -->
                        <div class="form-group row mb-3 align-items-center">
                            <label class="fw-bold col-lg-3 col-md-3 col-sm-12 col-form-label" for="surat_perintah_penyitaan_number">
                                Nomor SP Penyitaan
                            </label>
                            <div class="col-lg-5 col-md-5 col-sm-12 mb-2 mb-md-0">
                                <input id="surat_perintah_penyitaan_number" type="text"
                                    class="form-control @error('surat_perintah_penyitaan_number') is-invalid @enderror"
                                    name="surat_perintah_penyitaan_number"
                                    value="{{ old('surat_perintah_penyitaan_number', $document->surat_perintah_penyitaan_number) }}"
                                    placeholder="Nomor Surat Perintah Penyitaan (Jika Ada)">
                                @error('surat_perintah_penyitaan_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label class="fw-bold col-lg-2 col-md-2 col-sm-12 col-form-label text-md-end" for="surat_perintah_penyitaan_date">
                                Tgl SP Sita
                            </label>
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <input class="form-control datepicker @error('surat_perintah_penyitaan_date') is-invalid @enderror"
                                    id="surat_perintah_penyitaan_date" name="surat_perintah_penyitaan_date"
                                    placeholder="YYYY-MM-DD" autocomplete="off"
                                    value="{{ old('surat_perintah_penyitaan_date', $document->surat_perintah_penyitaan_date ? \Carbon\Carbon::parse($document->surat_perintah_penyitaan_date)->format('Y-m-d') : '') }}">
                                @error('surat_perintah_penyitaan_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Nomor & Tanggal SPDP (Optional) -->
                        <div class="form-group row mb-3 align-items-center">
                            <label class="fw-bold col-lg-3 col-md-3 col-sm-12 col-form-label" for="spdp_number">
                                Nomor SPDP
                            </label>
                            <div class="col-lg-5 col-md-5 col-sm-12 mb-2 mb-md-0">
                                @php
                                    $currentSpdpNumber = old('spdp_number', $document->spdp_number ?? '');
                                    $hasSpdpMatch = false;
                                @endphp
                                <select class="form-control select2-spdp-tags @error('spdp_number') is-invalid @enderror"
                                    id="spdp_number" name="spdp_number">
                                    <option value="">-- Pilih atau Ketik Nomor SPDP --</option>
                                    @if(isset($spdpDocuments) && $spdpDocuments->isNotEmpty())
                                        @foreach ($spdpDocuments as $spdpDoc)
                                            @php
                                                $isMatch = ($currentSpdpNumber == $spdpDoc->document_number);
                                                if ($isMatch) $hasSpdpMatch = true;
                                            @endphp
                                            <option value="{{ $spdpDoc->document_number }}"
                                                data-date="{{ $spdpDoc->document_date ? \Carbon\Carbon::parse($spdpDoc->document_date)->format('Y-m-d') : '' }}"
                                                {{ $isMatch ? 'selected' : '' }}>
                                                {{ $spdpDoc->document_number }}
                                            </option>
                                        @endforeach
                                    @endif
                                    @if(!empty($currentSpdpNumber) && !$hasSpdpMatch)
                                        <option value="{{ $currentSpdpNumber }}" selected>{{ $currentSpdpNumber }}</option>
                                    @endif
                                </select>
                                @error('spdp_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label class="fw-bold col-lg-2 col-md-2 col-sm-12 col-form-label text-md-end" for="spdp_date">
                                Tgl SPDP
                            </label>
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <input class="form-control datepicker @error('spdp_date') is-invalid @enderror"
                                    id="spdp_date" name="spdp_date" placeholder="YYYY-MM-DD"
                                    autocomplete="off"
                                    value="{{ old('spdp_date', $document->spdp_date ? \Carbon\Carbon::parse($document->spdp_date)->format('Y-m-d') : '') }}">
                                @error('spdp_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Pengadilan Negeri Tujuan -->
                        <div class="form-group row mb-3 align-items-center">
                            <label class="fw-bold col-lg-3 col-md-3 col-sm-12 col-form-label" for="court_id">
                                Pengadilan Negeri Tujuan<span class="text-danger fs-5">*</span>
                            </label>
                            <div class="col-lg-9 col-md-9 col-sm-12">
                                <select class="form-control select2 @error('court_id') is-invalid @enderror"
                                    name="court_id" id="court_id">
                                    <option value="">-- Pilih Pengadilan Negeri --</option>
                                    @foreach ($courts as $court)
                                        <option value="{{ $court->id }}"
                                            {{ old('court_id', $document->court_id) == $court->id ? 'selected' : '' }}>
                                            {{ $court->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">*Pilih Pengadilan Negeri yang berwenang memberikan izin penyitaan.</small>
                                @error('court_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Pejabat Penandatangan -->
                        @php
                            // Match existing signatory officers by register_number (excluding leader officer)
                            $existingOfficerIds = [];
                            foreach ($officers->where('class', '!=', 'LEADER') as $docOfficer) {
                                $matched = $authorizedSignatories->firstWhere('register_number', $docOfficer->register_number);
                                if ($matched) {
                                    $existingOfficerIds[] = $matched->id;
                                }
                            }
                            $currentSelectedSuspectIds = old('suspects', $selectedSuspectIds ?? []);
                            $hasSuspectsInDoc = count($currentSelectedSuspectIds) > 0;
                            $isSuspectExistsVal = old('isSuspectExists', $hasSuspectsInDoc ? 'true' : 'false');
                        @endphp

                        <!-- Opsi Ada Tersangka / Tidak Ada Tersangka -->
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label">Ada Tersangka?<span class="text-danger fs-5">*</span></label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <div class="d-flex align-items-center h-100">
                                    <div class="form-check me-4">
                                        <input class="form-check-input isSuspectExists" type="radio" id="suspectExists" name="isSuspectExists" value="true" {{ $isSuspectExistsVal == 'true' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="suspectExists">
                                            Ada Tersangka
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input isSuspectExists" type="radio" id="suspectNotExists" name="isSuspectExists" value="false" {{ $isSuspectExistsVal == 'false' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="suspectNotExists">
                                            Tidak Ada Tersangka
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Tersangka (Jika Ada Tersangka) -->
                        <div id="suspectExistsSection" {{ $isSuspectExistsVal == 'false' ? 'style=display:none;' : '' }}>
                            <div class="alert alert-success">
                                <div class="text-center">
                                    <b>
                                        PASTIKAN 
                                        <a href="{{ route('doc.laporan-hasil-gelar-perkara-document.create', ['accident_id' => $accidentId]) }}">
                                            LHGP (PENETAPAN TERSANGKA)
                                        </a> 
                                        LALU 
                                        <a href="{{ route('doc.surat-ketetapan-tentang-penetapan-tersangka-document.create', ['accident_id' => $accidentId]) }}">
                                            SURAT KETETAPAN TENTANG PENETAPAN TERSANGKA
                                        </a> 
                                        SUDAH DIBUAT SEBELUM MEMBUAT DOKUMEN INI
                                    </b>
                                </div>
                            </div>

                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-3 col-form-label" for="suspects">
                                    Tersangka yang disebutkan di dalam S.P. Izin Penyitaan ke Pengadilan<span class="text-danger fs-5">*</span>
                                </label>
                                <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                    <select class="form-control select2-multiple @error('suspects') is-invalid @enderror"
                                        name="suspects[]" id="suspects" multiple="multiple"
                                        data-placeholder="Pilih Tersangka Terkait (Bisa Lebih Dari Satu)">
                                        @foreach ($suspects as $suspect)
                                            @php
                                                $isSuspectSelected = is_array($currentSelectedSuspectIds) && in_array($suspect->id, $currentSelectedSuspectIds);
                                            @endphp
                                            <option value="{{ $suspect->id }}" {{ $isSuspectSelected ? 'selected' : '' }}>
                                                {{ $suspect->name }} @if(!empty($suspect->id_card_number)) (NIK: {{ $suspect->id_card_number }}) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">(*Pilih tersangka yang tersangkut dalam permohonan izin penyitaan ini)</small>
                                    @error('suspects')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Form Terlapor & Pelapor (Jika Tidak Ada Tersangka) -->
                        <div id="suspectNotExistsSection" {{ $isSuspectExistsVal == 'true' ? 'style=display:none;' : '' }}>
                            <div class="alert alert-success">
                                <div class="text-center">
                                    <b>
                                        PASTIKAN 
                                        <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId, 'page' => 'participants']) }}">
                                            DATA TERLAPOR
                                        </a> 
                                        SUDAH DIMASUKKAN SEBELUM MEMBUAT DOKUMEN INI
                                    </b>
                                </div>
                            </div>

                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-3 col-form-label" for="reportedPerson">
                                    Pilih Terlapor<span class="text-danger fs-5">*</span>
                                </label>
                                <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                    <select class="form-control select2 @error('reportedPerson') is-invalid @enderror"
                                        name="reportedPerson" id="reportedPerson" data-placeholder="Pilih Terlapor">
                                        <option value="" disabled {{ (old('reportedPerson') || empty($selectedReportedPersonIds)) ? 'selected' : '' }}>Pilih Terlapor</option>
                                        @foreach ($reportedPersons as $reportedPerson)
                                            @php
                                                $isRpSelected = old('reportedPerson') == $reportedPerson->id || in_array($reportedPerson->id, $selectedReportedPersonIds);
                                            @endphp
                                            <option value="{{ $reportedPerson->id }}" {{ $isRpSelected ? 'selected' : '' }}>
                                                {{ $reportedPerson->name ?? '-' }} (NIK: {{ $reportedPerson->identity_number ?? '-' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('reportedPerson')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-3 col-form-label" for="informant">
                                    Pilih Pelapor
                                </label>
                                <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                    <select class="form-control select2 @error('informant') is-invalid @enderror"
                                        name="informant" id="informant" data-placeholder="Pilih Pelapor">
                                        <option value="" disabled {{ old('informant') ? '' : 'selected' }}>Pilih Pelapor</option>
                                        @foreach ($informants as $informant)
                                            <option value="{{ $informant->id }}"
                                                {{ old('informant') == $informant->id ? 'selected' : '' }}>
                                                {{ $informant->name ?? '-' }} (NIK: {{ $informant->identity_number ?? '-' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('informant')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <!-- Ketua Tim Penyidik -->
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label" for="officerLeader">
                                Ketua Tim Penyidik<span class="text-danger fs-5">*</span>
                            </label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <select class="form-control select2 @error('officerLeader') is-invalid @enderror" name="officerLeader" id="officerLeader" required>
                                    <option value="">--Pilih Ketua Penyidik--</option>
                                    @foreach ($leaderOfficers as $data)
                                        @php
                                            $positionName = $data->position->name ?? '';
                                            $isSelected = (old('officerLeader') == $data->id) || (!old('officerLeader') && isset($currentLeaderOfficer) && $currentLeaderOfficer->register_number == $data->register_number);
                                        @endphp
                                        <option value="{{ $data->id }}" data-register-number="{{ $data->register_number }}"
                                            {{ $isSelected ? 'selected' : '' }}>
                                            {{ $data->register_number . ' - ' . $data->full_name . ' | ' . $positionName }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">(*Apabila daftar ketua penyidik kosong silahkan hubungi Helpdesk untuk mendapat bantuan)</small>
                                @error('officerLeader')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Yang Menandatangani (Pejabat Penandatangan) -->
                        @php
                            $oldOfficer = old('officers');
                            if (!$oldOfficer) {
                                $oldOfficer = !empty($existingOfficerIds) && is_array($existingOfficerIds) ? $existingOfficerIds[0] : null;
                            }
                        @endphp
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label" for="officers">
                                Yang Menandatangani<span class="text-danger fs-5">*</span>
                            </label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <select class="form-control select2 @error('officers') is-invalid @enderror"
                                    name="officers" id="officers"
                                    data-placeholder="Pilih Pejabat Penandatangan">
                                    <option value=""></option>
                                    @foreach ($authorizedSignatories as $signatory)
                                        @php
                                            $positionName = $signatory->position->name ?? '';
                                            $rankName = $signatory->rank->name ?? '';
                                            $fullName = \App\Helpers\PeopleNameHelper::getFullName($signatory->first_title, $signatory->first_name, $signatory->last_name, $signatory->last_title);
                                            $isSelected = $oldOfficer == $signatory->id;
                                        @endphp
                                        <option value="{{ $signatory->id }}" {{ $isSelected ? 'selected' : '' }}>
                                            {{ $signatory->register_number }} - {{ $fullName }} ({{ $rankName }} / {{ $positionName }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">(*Wajib dipilih minimal 1 pejabat yang berwenang menandatangani surat)</small>
                                @error('officers')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Tembusan -->
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label" for="carbonCopies">Tembusan<span class="text-danger fs-5">*</span></label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <div id="carbonCopiesContainer">
                                    @php
                                        $existingCarbonCopies = old('carbonCopies', $document->carbon_copies ?? []);
                                    @endphp
                                    @if(!empty($existingCarbonCopies))
                                        @foreach($existingCarbonCopies as $carbonCopy)
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
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. UNDANG-UNDANG / PASAL YANG DIPERSANGKAKAN & UNDANG-UNDANG KHUSUS TAMBAHAN -->
                <div class="card mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-blue-dark">Undang-Undang / Pasal yang Dipersangkakan<span class="text-danger fs-5">*</span></h6>
                        <button class="btn btn-md btn-primary" id="addLawButton" type="button" data-bs-toggle="modal" data-bs-target="#addLawModal">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Pasal
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="lawTable">
                                <thead class="table-danger text-center">
                                    <tr>
                                        <th style="width: 25%">Jenis Kejahatan</th>
                                        <th style="width: 25%">Golongan Kejahatan</th>
                                        <th style="width: 25%">Undang-Undang</th>
                                        <th style="width: 20%">Pasal</th>
                                        <th style="width: 5%">Opsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($mainLaws) && count($mainLaws) > 0)
                                        @foreach ($mainLaws as $law)
                                            <tr class="law-row text-center">
                                                <td>
                                                    {{ $law->crimeType->name ?? '-' }}
                                                    <input type="hidden" name="lawCrimeTypeIds[]" value="{{ $law->crime_type_id }}">
                                                </td>
                                                <td>
                                                    {{ $law->crimeClass->name ?? '-' }}
                                                    <input type="hidden" name="lawCrimeClassIds[]" value="{{ $law->crime_class_id }}">
                                                </td>
                                                <td>
                                                    {{ $law->crimeConstitution->name ?? '-' }}
                                                    <input type="hidden" name="lawCrimeConstitutionIds[]" value="{{ $law->crime_constitution_id }}">
                                                </td>
                                                <td>
                                                    {{ $law->constitution_chapter ?? '-' }}
                                                    <input type="hidden" name="lawCrimeConstitutionChapters[]" value="{{ $law->constitution_chapter }}">
                                                </td>
                                                <td class="text-center align-middle">
                                                    <button type="button" class="btn btn-sm btn-danger remove-law-row"><i class="bi bi-trash"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <small class="text-muted d-block mb-4">*Wajib memilih minimal 1 UU/Pasal yang dipersangkakan.</small>

                        <hr class="my-4">

                        <div class="row col-12 my-2 ms-0">
                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-3 col-form-label" for="additionalLaw">Undang-Undang Khusus Tambahan</label>
                                <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                    <input id="additionalLaw" type="text"
                                        class="form-control @error('additionalLaw') is-invalid @enderror font-weight-bold"
                                        name="additionalLaw" value="{{ old('additionalLaw') }}"
                                        placeholder="(Jika Ada) Contoh: Undang-Undang nomor 22 tahun 2009 LLAJ tentang Pengemudi mabuk">
                                    <div class="row mt-2">
                                        <div class="col">
                                            <button class="btn btn-primary" id="saveAdditionalLawButton" type="button"><i class="bi bi-plus-circle"></i> Tambah</button>
                                            <button class="btn btn-secondary" id="clearAdditionalLawButton" type="button"><i class="bi bi-trash"></i> Bersihkan</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-primary my-3" role="alert">
                                *Jika ada yang berkaitan dengan tindak pidana yang dipersangkakan <br />
                                Contoh: Undang-Undang nomor 22 tahun 2009 LLAJ tentang Pengemudi mabuk
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="additionalLawTable">
                                    <thead class="table-danger text-center">
                                        <tr>
                                            <th>Nama</th>
                                            <th style="width: 10%">Opsi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(old('lawAdditionalNames'))
                                            @foreach(old('lawAdditionalNames') as $lawName)
                                                <tr class="text-center">
                                                    <td>{{ $lawName }}</td>
                                                    <td>
                                                        <input type="hidden" name="lawAdditionalNames[]" value="{{ $lawName }}">
                                                        <button type="button" class="btn btn-danger btn-sm deleteAdditionalLaw"><i class="bi bi-trash"></i></button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @elseif(isset($additionalDbLaws) && count($additionalDbLaws) > 0)
                                            @foreach ($additionalDbLaws as $addLaw)
                                                @php
                                                    $lawName = $addLaw->constitution ?? $addLaw->description ?? '';
                                                @endphp
                                                <tr class="text-center">
                                                    <td>{{ $lawName }}</td>
                                                    <td>
                                                        <input type="hidden" name="lawAdditionalNames[]" value="{{ $lawName }}">
                                                        <button type="button" class="btn btn-danger btn-sm deleteAdditionalLaw"><i class="bi bi-trash"></i></button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. DAFTAR BARANG YANG DIMINTAKAN IZIN PENYITAAN -->
                <div class="card mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-blue-dark">Daftar Barang yang Dimintakan Izin Penyitaan</h6>
                        <button class="btn btn-md btn-primary" id="addSeizedItemRowButton" type="button"
                            data-bs-toggle="modal" data-bs-target="#addSeizedItemModal">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Barang Sitaan
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="seizedItemTable">
                                <thead class="table-danger text-center">
                                    <tr>
                                        <th style="width: 30%">Nama Barang<span class="text-danger">*</span></th>
                                        <th style="width: 20%">Jenis / Kategori</th>
                                        <th style="width: 15%">Jumlah</th>
                                        <th style="width: 15%">Satuan</th>
                                        <th style="width: 15%">Keterangan</th>
                                        <th style="width: 5%">Opsi</th>
                                    </tr>
                                </thead>
                                <tbody id="seizedItemTableBody">
                                    @php
                                        if (old('seized_items')) {
                                            $renderItems = old('seized_items');
                                        } elseif ($seizedItems && count($seizedItems) > 0) {
                                            $renderItems = $seizedItems->map(function($s) {
                                                return [
                                                    'nama' => $s->nama,
                                                    'jenis' => $s->jenis,
                                                    'jumlah' => $s->jumlah,
                                                    'satuan' => $s->satuan,
                                                    'keterangan' => $s->keterangan,
                                                ];
                                            })->toArray();
                                        } else {
                                            $renderItems = [];
                                        }
                                    @endphp
                                    @foreach ($renderItems as $idx => $item)
                                        <tr class="seized-item-row text-center" data-index="{{ $idx }}">
                                            <td>
                                                {{ $item['nama'] ?? '' }}
                                                <input type="hidden" name="seized_items[{{ $idx }}][nama]" value="{{ $item['nama'] ?? '' }}">
                                            </td>
                                            <td>
                                                {{ $item['jenis'] ?? '' }}
                                                <input type="hidden" name="seized_items[{{ $idx }}][jenis]" value="{{ $item['jenis'] ?? '' }}">
                                            </td>
                                            <td class="text-center">
                                                {{ $item['jumlah'] ?? 1 }}
                                                <input type="hidden" name="seized_items[{{ $idx }}][jumlah]" value="{{ $item['jumlah'] ?? 1 }}">
                                            </td>
                                            <td>
                                                {{ $item['satuan'] ?? 'Unit' }}
                                                <input type="hidden" name="seized_items[{{ $idx }}][satuan]" value="{{ $item['satuan'] ?? 'Unit' }}">
                                            </td>
                                            <td>
                                                {{ $item['keterangan'] ?? '' }}
                                                <input type="hidden" name="seized_items[{{ $idx }}][keterangan]" value="{{ $item['keterangan'] ?? '' }}">
                                            </td>
                                            <td class="text-center align-middle">
                                                <button type="button" class="btn btn-sm btn-danger remove-seized-item-row">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <!-- BUTTON SUBMIT & BATAL -->
                <div class="text-center my-4">
                    <button type="button" class="btn btn-dark-blue btn-md px-4" id="suratPermintaanIzinPenyitaanFormSubmit">
                        <i class="bi bi-save me-1"></i> Simpan
                    </button>
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"
                        class="btn btn-danger btn-md px-4 ms-2">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

<!-- Modal Tambah Barang Sitaan -->
<div class="modal fade" id="addSeizedItemModal" tabindex="-1" role="dialog" aria-labelledby="addSeizedItemModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-blue-dark" id="addSeizedItemModalLabel">Tambah Barang Sitaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addSeizedItemForm">
                    <div class="mb-3">
                        <label class="fw-bold" for="seizedItemNamaModal">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="seizedItemNamaModal"
                            placeholder="Contoh: Sepeda Motor Honda Vario No Pol B 123 CD">
                        <div class="text-danger small" id="seizedItemNamaModal-error"></div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold" for="seizedItemJenisModal">Jenis / Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="seizedItemJenisModal"
                            placeholder="Contoh: Kendaraan / Dokumen / Barang Bukti">
                        <div class="text-danger small" id="seizedItemJenisModal-error"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold" for="seizedItemJumlahModal">Jumlah <span class="text-danger">*</span></label>
                            <input type="number" step="any" min="0" class="form-control text-end" id="seizedItemJumlahModal" placeholder="Contoh: 1">
                            <div class="text-danger small" id="seizedItemJumlahModal-error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold" for="seizedItemSatuanModal">Satuan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="seizedItemSatuanModal"
                                placeholder="Contoh: Unit / Lembar / Buah">
                            <div class="text-danger small" id="seizedItemSatuanModal-error"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold" for="seizedItemKeteranganModal">Keterangan</label>
                        <input type="text" class="form-control" id="seizedItemKeteranganModal"
                            placeholder="Contoh: Kondisi rusak ringan / surat lengkap">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="cancelSeizedItemModal">
                    <i class="bi bi-x-circle me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-dark-blue" id="saveSeizedItemModalButton">
                    <i class="bi bi-save me-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Law -->
<div class="modal fade" id="addLawModal" tabindex="-1" role="dialog" aria-labelledby="addLawModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="modalContent">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-blue-dark" id="addLawModalLabel">Tambah Kejahatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-primary my-3" role="alert">
                    Jika tidak terdapat opsi yang sesuai, silahkan menghubungi Helpdesk ICELL untuk koordinasi.
                </div>
                <form id="addLawForm">
                    <div class="mb-3 form-validate">
                        <label class="fw-bold" for="crimeTypeLawForm">Jenis Kejahatan</label>
                        <select class="form-control select2-modal" id="crimeTypeLawForm" name="crimeTypeLawForm" style="width:100%">
                            <option value="">--Pilih Jenis Kejahatan--</option>
                            @foreach ($crimeTypes as $crimeType)
                                <option value="{{ $crimeType->id }}" data-crime-type-name="{{ $crimeType->name }}"
                                    data-crime-class-id="{{ $crimeType->crimeClass->id ?? '' }}"
                                    data-crime-constitution-id="{{ $crimeType->crimeConstitution->id ?? '' }}">
                                    {{ $crimeType->name }}</option>
                            @endforeach
                        </select>
                        <div class="error text-danger" id="crimeTypeLawForm-error"></div>
                    </div>

                    <div class="mb-3 form-validate">
                        <label class="fw-bold" for="crimeClassLawForm">Golongan Kejahatan</label>
                        <select class="form-control" id="crimeClassLawForm" disabled>
                            <option value="">--Pilih Golongan Kejahatan--</option>
                            @foreach ($crimeClasses as $crimeClass)
                                <option value="{{ $crimeClass->id }}"
                                    data-crime-class-name="{{ $crimeClass->name }}">{{ $crimeClass->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 form-validate">
                        <label class="fw-bold" for="crimeConstitutionLawForm">Undang-Undang</label>
                        <select class="form-control" id="crimeConstitutionLawForm" disabled>
                            <option value="">--Pilih Undang-Undang--</option>
                            @foreach ($crimeConstitutions as $crimeConstitution)
                                <option value="{{ $crimeConstitution->id }}"
                                    data-crime-constitution-name="{{ $crimeConstitution->name }}"
                                    data-chapter="{{ $crimeConstitution->chapter }}">{{ $crimeConstitution->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="fw-bold" for="constitutionChapterLawForm">Pasal</label>
                        <select class="form-control" id="constitutionChapterLawForm" name="constitutionChapterLawForm">
                            <option value="">--Pilih Pasal-Ayat--</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i> Batal</button>
                <button type="button" class="btn btn-dark-blue" id="saveAddLawFormButton"><i class="bi bi-save me-1"></i> Simpan</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>


    <script type="text/javascript">
        $(document).ready(function() {
            setInterval(function() {
                $('#attentionBox').toggleClass('alert-danger alert-warning');
            }, 1000);

            // Toggle Ada Tersangka / Tidak Ada Tersangka
            $(document).on('change', '.isSuspectExists', function() {
                var isSuspectExists = $(this).val();

                if (isSuspectExists == 'true') {
                    $('#suspectExistsSection').show();
                    $('#suspectNotExistsSection').hide();
                } else if (isSuspectExists == 'false') {
                    $('#suspectExistsSection').hide();
                    $('#suspectNotExistsSection').show();
                }

                $('#suspects, #reportedPerson, #informant').removeClass('is-invalid');
                $('#suspects, #reportedPerson, #informant').next('.select2-container').find('.select2-selection').removeClass('border border-danger is-invalid');
                $('#suspectExistsSection, #suspectNotExistsSection').find('.frontend-error, .invalid-feedback').remove();
            });

            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            $('.select2-multiple').select2({
                theme: 'bootstrap4',
                width: '100%',
                allowClear: true
            });

                        // SP Penyidikan Tags
            $('.select2-sprindik-tags').select2({
                theme: 'bootstrap4',
                width: '100%',
                tags: true,
                placeholder: '-- Pilih atau Ketik No SP Penyidikan --',
                allowClear: true,
                createTag: function (params) {
                    var term = $.trim(params.term);
                    if (term === '') return null;
                    return { id: term, text: term, newTag: true };
                }
            });

            $('#sprindik_number').on('change', function() {
                var selected = $(this).find(':selected');
                var id = selected.data('id');
                var date = selected.data('date');
                var leaderNrp = selected.data('leader-nrp');

                if (id) {
                    $('#surat_perintah_penyidikan_document_id').val(id);
                } else {
                    $('#surat_perintah_penyidikan_document_id').val('');
                }
                
                if (date) {
                    $('#sprindik_date').val(date).removeClass('is-invalid');
                }
                if (leaderNrp && !$('#officerLeader').val()) {
                    var leaderOpt = $('#officerLeader option[data-register-number="' + leaderNrp + '"]');
                    if (leaderOpt.length) {
                        $('#officerLeader').val(leaderOpt.val()).trigger('change');
                    }
                }
            });

            // Single field SPDP Number: Select existing SPDP or type manual/free-text
            $('.select2-spdp-tags').select2({
                theme: 'bootstrap4',
                width: '100%',
                tags: true,
                placeholder: '-- Pilih atau Ketik Nomor SPDP --',
                allowClear: true,
                createTag: function (params) {
                    var term = $.trim(params.term);
                    if (term === '') {
                        return null;
                    }
                    return {
                        id: term,
                        text: term,
                        newTag: true
                    };
                },
                templateResult: function (data) {
                    if (!data.id) {
                        return data.text;
                    }
                    var $el = $(data.element);
                    var date = $el.data('date');
                    if (date) {
                        return $('<span>' + data.text + ' <span class="text-muted small ms-1">(Tgl: ' + date + ')</span></span>');
                    }
                    return data.text;
                },
                templateSelection: function (data) {
                    return data.id || data.text;
                }
            });

            var spdpJustSelected = false;
            $('.select2-spdp-tags').on('select2:select', function() {
                spdpJustSelected = true;
            });

            $('.select2-spdp-tags').on('select2:closing', function(e) {
                if (spdpJustSelected) {
                    spdpJustSelected = false;
                    return;
                }
                var $select = $(this);
                var searchInput = $('.select2-container--open .select2-search__field');
                if (searchInput.length) {
                    var term = $.trim(searchInput.val());
                    if (term && term !== '') {
                        if (!$select.find('option[value="' + term + '"]').length) {
                            var newOption = new Option(term, term, true, true);
                            $select.append(newOption);
                        }
                        $select.val(term).trigger('change');
                    }
                }
            });

            $('.select2-spdp-tags').on('select2:close', function() {
                spdpJustSelected = false;
            });

            // Auto-fill Tgl SPDP when selecting existing SPDP with data-date
            $('#spdp_number').on('change', function() {
                var selected = $(this).find(':selected');
                var date = selected.data('date');
                if (date) {
                    $('#spdp_date').val(date).removeClass('is-invalid');
                }
            });

            // Initialize Datepickers
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                orientation: 'auto bottom'
            });

            // Auto-fill Sprindik data when selecting from dropdown
            $('#surat_perintah_penyidikan_document_id').on('change', function() {
                var selected = $(this).find(':selected');
                var number = selected.data('number');
                var date = selected.data('date');
                var leaderNrp = selected.data('leader-nrp');

                if (number) {
                    $('#sprindik_number').val(number).removeClass('is-invalid');
                } else {
                    $('#sprindik_number').val('');
                }
                if (date) {
                    $('#sprindik_date').val(date).removeClass('is-invalid');
                }
                if (leaderNrp && !$('#officerLeader').val()) {
                    var leaderOpt = $('#officerLeader option[data-register-number="' + leaderNrp + '"]');
                    if (leaderOpt.length) {
                        $('#officerLeader').val(leaderOpt.val()).trigger('change');
                    }
                }
            });

            // Initial auto-fill for Sprindik on page load if pre-selected
            var initialSprindik = $('#surat_perintah_penyidikan_document_id').find(':selected');
            if (initialSprindik.length && initialSprindik.val()) {
                if (initialSprindik.data('number') && !$('#sprindik_number').val()) {
                    $('#sprindik_number').val(initialSprindik.data('number'));
                }
                if (initialSprindik.data('date') && !$('#sprindik_date').val()) {
                    $('#sprindik_date').val(initialSprindik.data('date'));
                }
            }

            // Tembusan (Carbon Copies)
            $(".addCarbonCopiesButton").click(function() {
                var inputGroup = '<div class="input-group mb-2">' +
                    '<input type="text" class="form-control" name="carbonCopies[]" value="">' +
                    '<div class="input-group-append">' +
                    '<button class="btn btn-outline-danger removeCarbonCopiesButton" type="button">Hapus</button>' +
                    '</div>' +
                    '</div>';

                $("#carbonCopiesContainer").append(inputGroup);
            });

            $(document).on("click", ".removeCarbonCopiesButton", function() {
                $(this).closest(".input-group").remove();
            });

            // ─── DYNAMIC LAW MODAL & ROWS (SPRINDIK STYLE) ─────────────────
            $('#addLawModal').on('shown.bs.modal', function () {
                $('#crimeTypeLawForm').select2({
                    dropdownParent: $('#addLawModal'),
                    theme: 'bootstrap4',
                    width: '100%'
                });
            });

            $('#crimeTypeLawForm').on('change', function() {
                var selected = $(this).find(':selected');
                var crimeClassId = selected.data('crime-class-id');
                var crimeConstitutionId = selected.data('crime-constitution-id');

                if (crimeClassId) {
                    $('#crimeClassLawForm').val(crimeClassId).trigger('change');
                } else {
                    $('#crimeClassLawForm').val('');
                }

                if (crimeConstitutionId) {
                    $('#crimeConstitutionLawForm').val(crimeConstitutionId).trigger('change');
                    var chapterStr = $('#crimeConstitutionLawForm option:selected').data('chapter');
                    var optionsHtml = '<option value="">--Pilih Pasal-Ayat--</option>';
                    if (chapterStr) {
                        var chapters = chapterStr.toString().split(';');
                        chapters.forEach(function(chap) {
                            if (chap.trim()) {
                                optionsHtml += '<option value="' + chap.trim() + '">' + chap.trim() + '</option>';
                            }
                        });
                    }
                    $('#constitutionChapterLawForm').html(optionsHtml);
                } else {
                    $('#crimeConstitutionLawForm').val('');
                    $('#constitutionChapterLawForm').html('<option value="">--Pilih Pasal-Ayat--</option>');
                }
            });

            $('#saveAddLawFormButton').on('click', function() {
                var crimeTypeId = $('#crimeTypeLawForm').val();
                var crimeTypeName = $('#crimeTypeLawForm option:selected').data('crime-type-name') || '';
                var crimeClassId = $('#crimeClassLawForm').val();
                var crimeClassName = $('#crimeClassLawForm option:selected').data('crime-class-name') || '';
                var crimeConstitutionId = $('#crimeConstitutionLawForm').val();
                var crimeConstitutionName = $('#crimeConstitutionLawForm option:selected').data('crime-constitution-name') || '';
                var chapter = $('#constitutionChapterLawForm').val() || '';

                if (!crimeTypeId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Silahkan pilih Jenis Kejahatan terlebih dahulu.'
                    });
                    return;
                }

                if (crimeConstitutionId && !chapter) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Pasal UU Khusus harus diisi.'
                    });
                    return;
                }

                var rowHtml = `
                    <tr class="law-row">
                        <td>
                            ${crimeTypeName}
                            <input type="hidden" name="lawCrimeTypeIds[]" value="${crimeTypeId}">
                        </td>
                        <td>
                            ${crimeClassName}
                            <input type="hidden" name="lawCrimeClassIds[]" value="${crimeClassId}">
                        </td>
                        <td>
                            ${crimeConstitutionName}
                            <input type="hidden" name="lawCrimeConstitutionIds[]" value="${crimeConstitutionId}">
                        </td>
                        <td>
                            ${chapter}
                            <input type="hidden" name="lawCrimeConstitutionChapters[]" value="${chapter}">
                        </td>
                        <td class="text-center align-middle">
                            <button type="button" class="btn btn-sm btn-danger remove-law-row"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                `;

                $('#lawTable tbody').append(rowHtml);
                $('#addLawModal').modal('hide');

                $('#crimeTypeLawForm').val('').trigger('change');
                $('#crimeClassLawForm').val('');
                $('#crimeConstitutionLawForm').val('');
                $('#constitutionChapterLawForm').html('<option value="">--Pilih Pasal-Ayat--</option>');
            });

            $(document).on('click', '.remove-law-row', function() {
                $(this).closest('tr').remove();
            });

            // Additional Law
            $('#saveAdditionalLawButton').on('click', function() {
                var lawAdditionalName = $('#additionalLaw').val();

                if (lawAdditionalName == '') {
                    $('#additionalLaw').parent().find('small').remove();
                    $('#additionalLaw').parent().append(
                        '<small class="text-danger">Inputan ini wajib diisi</small>');
                    return false;
                } else {
                    $('#additionalLaw').parent().find('small').remove();
                    $('#additionalLawTable tbody').append(
                        '<tr class="text-center">' +
                        '<td>' + lawAdditionalName + '</td>' +
                        '<td>' +
                        '<input type="hidden" name="lawAdditionalNames[]" value="' + lawAdditionalName + '">' +
                        '<button type="button" class="btn btn-danger btn-sm deleteAdditionalLaw"><i class="bi bi-trash"></i></button>' +
                        '</td>' +
                        '</tr>'
                    );

                    $('#additionalLaw').val('');
                }
            });

            $('#clearAdditionalLawButton').on('click', function() {
                $('#additionalLaw').val('');
                $('#additionalLaw').parent().find('small').remove();
            });

            $(document).on('click', '.deleteAdditionalLaw, .remove-additional-law-row', function() {
                $(this).closest('tr').remove();
            });

            // ─── MODAL TAMBAH BARANG SITAAN ────────────────────────────────
            var seizedItemIndex = {{ count($renderItems) }};

            // Reset form modal saat dibuka
            $('#addSeizedItemModal').on('show.bs.modal', function() {
                $('#seizedItemNamaModal').val('').removeClass('is-invalid');
                $('#seizedItemNamaModal-error').text('');
                $('#seizedItemJenisModal').val('').removeClass('is-invalid');
                $('#seizedItemJenisModal-error').text('');
                $('#seizedItemJumlahModal').val('').removeClass('is-invalid');
                $('#seizedItemJumlahModal-error').text('');
                $('#seizedItemSatuanModal').val('').removeClass('is-invalid');
                $('#seizedItemSatuanModal-error').text('');
                $('#seizedItemKeteranganModal').val('');
            });

            $('#saveSeizedItemModalButton').on('click', function() {
                var nama = $('#seizedItemNamaModal').val().trim();
                var jenis = $('#seizedItemJenisModal').val().trim();
                var jumlah = $('#seizedItemJumlahModal').val().trim();
                var satuan = $('#seizedItemSatuanModal').val().trim();
                var keterangan = $('#seizedItemKeteranganModal').val().trim();

                var hasError = false;

                if (!nama) {
                    $('#seizedItemNamaModal').addClass('is-invalid');
                    $('#seizedItemNamaModal-error').text('Nama Barang wajib diisi.');
                    hasError = true;
                } else {
                    $('#seizedItemNamaModal').removeClass('is-invalid');
                    $('#seizedItemNamaModal-error').text('');
                }

                if (!jenis) {
                    $('#seizedItemJenisModal').addClass('is-invalid');
                    $('#seizedItemJenisModal-error').text('Jenis / Kategori wajib diisi.');
                    hasError = true;
                } else {
                    $('#seizedItemJenisModal').removeClass('is-invalid');
                    $('#seizedItemJenisModal-error').text('');
                }

                if (!jumlah) {
                    $('#seizedItemJumlahModal').addClass('is-invalid');
                    $('#seizedItemJumlahModal-error').text('Jumlah wajib diisi.');
                    hasError = true;
                } else {
                    $('#seizedItemJumlahModal').removeClass('is-invalid');
                    $('#seizedItemJumlahModal-error').text('');
                }

                if (!satuan) {
                    $('#seizedItemSatuanModal').addClass('is-invalid');
                    $('#seizedItemSatuanModal-error').text('Satuan wajib diisi.');
                    hasError = true;
                } else {
                    $('#seizedItemSatuanModal').removeClass('is-invalid');
                    $('#seizedItemSatuanModal-error').text('');
                }

                if (hasError) return;

                var newRow = `
                    <tr class="seized-item-row text-center" data-index="${seizedItemIndex}">
                        <td>
                            ${nama}
                            <input type="hidden" name="seized_items[${seizedItemIndex}][nama]" value="${nama}">
                        </td>
                        <td>
                            ${jenis}
                            <input type="hidden" name="seized_items[${seizedItemIndex}][jenis]" value="${jenis}">
                        </td>
                        <td class="text-center">
                            ${jumlah}
                            <input type="hidden" name="seized_items[${seizedItemIndex}][jumlah]" value="${jumlah}">
                        </td>
                        <td>
                            ${satuan}
                            <input type="hidden" name="seized_items[${seizedItemIndex}][satuan]" value="${satuan}">
                        </td>
                        <td>
                            ${keterangan}
                            <input type="hidden" name="seized_items[${seizedItemIndex}][keterangan]" value="${keterangan}">
                        </td>
                        <td class="text-center align-middle">
                            <button type="button" class="btn btn-sm btn-danger remove-seized-item-row">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;

                $('#seizedItemTableBody').append(newRow);
                seizedItemIndex++;
                $('#addSeizedItemModal').modal('hide');
            });

            $(document).on('click', '.remove-seized-item-row', function() {
                $(this).closest('tr').remove();
            });


            // ─── CLIENT VALIDATION & SUBMIT ────────────────────────────────
            $('#suratPermintaanIzinPenyitaanFormSubmit').on('click', function(e) {
                e.preventDefault();

                // Clear previous errors
                $('.is-invalid').removeClass('is-invalid');
                $('.border-danger').removeClass('border-danger');
                $('.frontend-error').remove();

                var errors = [];

                function markError($el, msg) {
                    $el.addClass('is-invalid');
                    if ($el.next('.select2-container').length) {
                        $el.next('.select2-container').find('.select2-selection').addClass('border border-danger is-invalid');
                    }
                    var $target = $el.next('.select2-container').length ? $el.next('.select2-container') : $el;
                    $target.after('<div class="invalid-feedback d-block frontend-error">' + msg + '</div>');
                    errors.push(msg);
                }

                if (!$('#document_number').val().trim()) {
                    markError($('#document_number'), 'Nomor Dokumen S-12 wajib diisi.');
                }
                var docClassificationVal = $('#documentClassification').val() || $('#classification').val();
                if (!docClassificationVal) {
                    markError($('#documentClassification').length ? $('#documentClassification') : $('#classification'), 'Klasifikasi Dokumen wajib dipilih.');
                }
                if (!$('#document_date').val().trim()) {
                    markError($('#document_date'), 'Tanggal Dokumen S-12 wajib diisi.');
                }
                if (!$('#surat_perintah_penyidikan_document_id').val()) {
                    markError($('#surat_perintah_penyidikan_document_id'), 'No SP Penyidikan wajib dipilih.');
                }
                if (!$('#sprindik_date').val().trim()) {
                    markError($('#sprindik_date'), 'Tanggal SP Penyidikan wajib diisi.');
                }
                if (!$('#court_id').val()) {
                    markError($('#court_id'), 'Pengadilan Negeri Tujuan wajib dipilih.');
                }
                if ($('.isSuspectExists:checked').val() === 'true') {
                    if (!$('#suspects').val() || $('#suspects').val().length === 0) {
                        markError($('#suspects'), 'Tersangka yang disebutkan di dalam S.P. Izin Penyitaan ke Pengadilan harus diisi.');
                    }
                } else {
                    if (!$('#reportedPerson').val()) {
                        markError($('#reportedPerson'), 'Terlapor yang disebutkan di dalam S.P. Izin Penyitaan ke Pengadilan harus diisi.');
                    }
                }
                var selSprindik = $('#surat_perintah_penyidikan_document_id').find(':selected');
                if (selSprindik.data('number') && !$('#sprindik_number').val()) {
                    $('#sprindik_number').val(selSprindik.data('number'));
                }

                if (!$('#officerLeader').val()) {
                    markError($('#officerLeader'), 'Ketua Tim Penyidik wajib dipilih.');
                }

                var selectedOfficers = $('#officers').val();
                if (!selectedOfficers || selectedOfficers.length === 0) {
                    markError($('#officers'), 'Pejabat Penandatangan wajib dipilih minimal 1 orang.');
                }

                // Check Laws
                var mainLawCount = $('#lawTable tbody tr.law-row').length;
                var additionalLawCount = 0;
                $('#additionalLawTable tbody tr input[type="hidden"]').each(function() {
                    if ($(this).val().trim() !== '') {
                        additionalLawCount++;
                    }
                });
                if (mainLawCount === 0 && additionalLawCount === 0) {
                    markError($('#lawTable'), 'Daftar UU / Pasal yang dipersangkakan wajib diisi minimal 1 pasal.');
                }

                // Check Tembusan
                var carbonCopiesCount = 0;
                $('#carbonCopiesContainer input[name="carbonCopies[]"]').each(function() {
                    if ($(this).val().trim() !== '') {
                        carbonCopiesCount++;
                    }
                });
                if (carbonCopiesCount === 0) {
                    markError($('#carbonCopiesContainer'), 'Tembusan wajib diisi.');
                }

                // Check Seized Items
                if ($('#seizedItemTable tbody tr.seized-item-row').length === 0) {
                    markError($('#seizedItemTable'), 'Daftar Barang yang Dimintakan Izin Penyitaan harus diisi minimal 1 barang.');
                }

                if (errors.length > 0) {
                    var $firstError = $('.is-invalid:visible, .frontend-error:visible').first();
                    if ($firstError.length) {
                        $('html, body').animate({
                            scrollTop: Math.max(0, $firstError.offset().top - 120)
                        }, 400);
                    }
                    return false;
                }

                Swal.fire({
                    title: 'Konfirmasi Perubahan',
                    text: 'Apakah perubahan Surat Permintaan Izin Penyitaan sudah sesuai?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0b2f64',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Update Data',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#suratPermintaanIzinPenyitaanForm')[0].submit();
                    }
                });
            });
        });
    </script>
@endpush
