@php
    $_title = 'Edit Surat Laporan Persetujuan Penyitaan';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
    <style>
        input::placeholder, textarea::placeholder {
            color: #adb5bd !important;
            opacity: 1;
        }
        input:-ms-input-placeholder, textarea:-ms-input-placeholder {
            color: #adb5bd !important;
        }
        input::-ms-input-placeholder, textarea::-ms-input-placeholder {
            color: #adb5bd !important;
        }
        
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
            <h5 class="fw-bold text-blue-dark">Edit Surat Laporan Persetujuan Penyitaan (S-13)</h5>
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

        <div class="box-body mx-2">
            <form action="{{ route('doc.surat-laporan-persetujuan-penyitaan-document.update', ['id' => $id, 'accident_id' => $accidentId]) }}"
                method="POST" enctype="multipart/form-data" id="suratPermintaanIzinPenyitaanForm" novalidate>
                @csrf
                <input type="hidden" name="accident_id" id="accident_id" value="{{ $accidentId }}">

                <!-- 1. DATA DOKUMEN UTAMA -->
                <!-- <div class="card mb-4 informasi-dokumen-card">
                    <div class="card-header bg-light">
                        <h6 class="fw-bold mb-0 text-blue-dark">Informasi Dokumen</h6>
                    </div> -->
                    <!-- <div class="card-body"> -->
                        <!-- Nomor LP -->
                        <div class="form-group row mb-3 align-items-center">
                            <label class="fw-bold col-lg-3 col-md-3 col-sm-12 col-form-label" for="accidentNumber">Nomor LP</label>
                            <div class="col-lg-9 col-md-9 col-sm-12">
                                <input id="accidentNumber" type="text"
                                    class="form-control font-weight-bold"
                                    name="accidentNumber" value="{{ $accident->no_lp }}" readonly>
                            </div>
                        </div>

                        <!-- Nomor Dokumen -->
                        <div class="form-group row mb-3 align-items-center">
                            <label class="fw-bold col-lg-3 col-md-3 col-sm-12 col-form-label" for="document_number">
                                Nomor Dokumen<span class="text-danger fs-5">*</span>
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

                        <!-- Tanggal Dokumen -->
                        <div class="form-group row mb-3 align-items-center">
                            <label class="fw-bold col-lg-3 col-md-3 col-sm-12 col-form-label" for="document_date">
                                Tanggal Dokumen<span class="text-danger fs-5">*</span>
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
                                <input class="form-control @error('sprindik_date') is-invalid @enderror"
                                    id="sprindik_date" name="sprindik_date" placeholder="YYYY-MM-DD"
                                    autocomplete="off"
                                    value="{{ old('sprindik_date', $document->sprindik_date ? \Carbon\Carbon::parse($document->sprindik_date)->format('Y-m-d') : '') }}" required
                                    readonly style="background-color: #e9ecef; cursor: not-allowed; pointer-events: none;">
                                @error('sprindik_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Surat Perintah Penyitaan -->
                        @php
                            $defaultHasSp = old('has_surat_perintah_penyitaan');
                            if ($defaultHasSp === null) {
                                // Default dari data existing (legacy/current)
                                $defaultHasSp = (!empty($document->surat_perintah_penyitaan_number) || !empty($document->surat_perintah_penyitaan_date) || !empty($document->surat_perintah_penyitaan_file)) ? '1' : '0';
                            }
                            $hasSpFile = !empty($document->surat_perintah_penyitaan_file);
                        @endphp
                        <div class="form-group row mb-3 align-items-start">
                            <label class="fw-bold col-lg-3 col-md-3 col-sm-12 col-form-label">
                                Ada Surat Perintah Penyitaan?<span class="text-danger">*</span>
                            </label>
                            <div class="col-lg-9 col-md-9 col-sm-12">
                                <div class="form-check form-check-inline mt-2">
                                    <input class="form-check-input @error('has_surat_perintah_penyitaan') is-invalid @enderror" type="radio" name="has_surat_perintah_penyitaan" id="has_sp_ada" value="1" {{ $defaultHasSp == '1' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="has_sp_ada">Ada SPRINSITA</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('has_surat_perintah_penyitaan') is-invalid @enderror" type="radio" name="has_surat_perintah_penyitaan" id="has_sp_tidak" value="0" {{ $defaultHasSp == '0' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="has_sp_tidak">Tidak ada SPRINSITA</label>
                                </div>
                                @error('has_surat_perintah_penyitaan')
                                    <div class="invalid-feedback d-block">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                @enderror

                                <div id="sp_penyitaan_container" class="mt-3 p-3 border rounded" style="display: none;">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="fw-bold d-block mb-2" for="surat_perintah_penyitaan_number">Nomor SPRINSITA<span class="text-danger">*</span></label>
                                            <input id="surat_perintah_penyitaan_number" type="text"
                                                class="form-control @error('surat_perintah_penyitaan_number') is-invalid @enderror"
                                                name="surat_perintah_penyitaan_number"
                                                value="{{ old('surat_perintah_penyitaan_number', $document->surat_perintah_penyitaan_number) }}"
                                                placeholder="Contoh: SP.Sita/01/I/2026/Reskrim">
                                            @error('surat_perintah_penyitaan_number')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="fw-bold d-block mb-2" for="surat_perintah_penyitaan_date">Tanggal SPRINSITA<span class="text-danger">*</span></label>
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
                                    <div class="row">
                                        <div class="col-12 sp-file-box" data-has-existing="{{ $hasSpFile ? 'true' : 'false' }}">
                                            <label class="fw-bold d-block mb-2" for="surat_perintah_penyitaan_file">Upload SPRINSITA<span class="text-danger">*</span></label>
                                            
                                            <div class="sp-preview-wrapper p-2 border rounded bg-white align-items-center {{ $hasSpFile ? 'd-flex' : 'd-none' }}">
                                                <div class="me-auto">
                                                    <i class="bi bi-paperclip text-muted me-2"></i>
                                                    <span class="sp-status fw-bold text-dark" title="{{ $document->surat_perintah_penyitaan_file }}">SPRINSITA sudah diunggah: {{ $document->surat_perintah_penyitaan_file }}</span>
                                                </div>
                                                <div>
                                                    @if($hasSpFile)
                                                        <a href="{{ asset('file/penyitaan/surat-perintah-penyitaan/' . $document->surat_perintah_penyitaan_file) }}" target="_blank" class="btn btn-sm btn-outline-primary me-2 sp-view-btn">Lihat SPRINSITA</a>
                                                    @else
                                                        <a href="#" target="_blank" class="btn btn-sm btn-outline-primary me-2 sp-view-btn">Lihat SPRINSITA</a>
                                                    @endif
                                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-change-sp">Ganti SPRINSITA</button>
                                                </div>
                                            </div>

                                            <input class="form-control sp-file-input {{ $hasSpFile ? 'd-none' : '' }} @error('surat_perintah_penyitaan_file') is-invalid @enderror" type="file" name="surat_perintah_penyitaan_file" id="surat_perintah_penyitaan_file" accept="application/pdf">
                                            <small class="text-muted sp-file-help {{ $hasSpFile ? 'd-none' : '' }}">Format PDF, Maksimal 10 MB</small>

                                            @error('surat_perintah_penyitaan_file')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
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
                                <input class="form-control @error('spdp_date') is-invalid @enderror"
                                    id="spdp_date" name="spdp_date" placeholder="YYYY-MM-DD"
                                    autocomplete="off"
                                    readonly style="background-color: #e9ecef; cursor: not-allowed; pointer-events: none;"
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
                                    name="court_id" id="court_id" required>
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
                        @endphp

                        <!-- 4. DAFTAR ORANG & BARANG SITAAN -->
                        <div class="form-group row mt-4 mb-3 align-items-center">
                            <div class="col-lg-3 col-md-3 col-sm-12">
                                <h6 class="fw-bold mb-0 text-dark">Daftar Orang dan Barang Sitaan<span class="text-danger fs-5">*</span></h6>
                            </div>
                            <div class="col-lg-9 col-md-9 col-sm-12">
                                <button class="btn btn-md btn-primary" id="addPersonBtn" type="button">
                                    <i class="bi bi-person-plus-fill me-1"></i> Tambah Orang
                                </button>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <strong>Pastikan data tersangka/terlapor sudah tersedia sebelum menambahkan orang dan barang sitaan:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Jika ada tersangka, pastikan <a href="{{ route('doc.laporan-hasil-gelar-perkara-document.create', ['accident_id' => $accidentId]) }}" class="fw-bold text-primary text-decoration-underline">LHGP (Penetapan Tersangka)</a> dan <a href="{{ route('doc.surat-ketetapan-tentang-penetapan-tersangka-document.create', ['accident_id' => $accidentId]) }}" class="fw-bold text-primary text-decoration-underline">Surat Ketetapan tentang Penetapan Tersangka</a> sudah dibuat.</li>
                                <li>Jika tidak ada tersangka, pastikan <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId, 'page' => 'participants']) }}" class="fw-bold text-primary text-decoration-underline">Data Terlapor</a> sudah dimasukkan.</li>
                            </ul>
                        </div>
                        <div id="persons-container">
                            @php
                                $renderPersons = [];
                                if (old('persons')) {
                                    $renderPersons = old('persons');
                                    foreach ($renderPersons as $idx => &$rp) {
                                        if (empty($rp['bap_file']) && isset($persons) && count($persons) > 0) {
                                            $matchedPerson = null;
                                            if (!empty($rp['id'])) {
                                                $matchedPerson = $persons->firstWhere('id', $rp['id']);
                                            }
                                            if (!$matchedPerson && !empty($rp['person_id']) && !empty($rp['person_type'])) {
                                                $matchedPerson = $persons->first(function ($p) use ($rp) {
                                                    if ($rp['person_type'] === 'suspect') return $p->suspect_id === $rp['person_id'];
                                                    if ($rp['person_type'] === 'witness') return $p->witness_id === $rp['person_id'];
                                                    if ($rp['person_type'] === 'reported_person') return $p->reported_person_id === $rp['person_id'];
                                                    return false;
                                                });
                                            }
                                            if ($matchedPerson) {
                                                $rp['bap_file'] = $matchedPerson->bap_file;
                                                if (empty($rp['id'])) {
                                                    $rp['id'] = $matchedPerson->id;
                                                }
                                            }
                                        }
                                    }
                                    unset($rp);
                                } elseif (isset($persons) && count($persons) > 0) {
                                    foreach ($persons as $personModel) {
                                        $items = [];
                                        foreach ($personModel->seizedItems as $itemModel) {
                                            $items[] = [
                                                'name' => $itemModel->name,
                                                'quantity' => $itemModel->quantity,
                                                'unit' => $itemModel->unit,
                                                'description' => $itemModel->description,
                                            ];
                                        }
                                        $renderPersons[] = [
                                            'id' => $personModel->id,
                                            'person_id' => $personModel->suspect_id ?? $personModel->witness_id ?? $personModel->reported_person_id,
                                            'person_type' => $personModel->suspect_id ? 'suspect' : ($personModel->witness_id ? 'witness' : 'reported_person'),
                                            'bap_date' => $personModel->bap_date ? (is_string($personModel->bap_date) ? date('Y-m-d', strtotime($personModel->bap_date)) : $personModel->bap_date->format('Y-m-d')) : '',
                                            'bap_file' => $personModel->bap_file ?? null,
                                            'is_seized_at_work_unit' => $personModel->is_seized_at_work_unit,
                                            'seized_location' => $personModel->seized_location,
                                            'seized_items' => $items,
                                        ];
                                    }
                                }
                            @endphp
                            
                            @foreach($renderPersons as $pIdx => $oldPerson)
                                <div class="card mb-3 border-primary person-card" data-index="{{ $pIdx }}">
                                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-2">
                                        <h6 class="mb-0">Data Pihak #{{ $pIdx + 1 }} dan Barang Sitaannya</h6>
                                        <button type="button" class="btn btn-sm btn-light text-danger remove-person-btn"><i class="bi bi-trash"></i> Hapus</button>
                                    </div>
                                    <div class="card-body bg-white">
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="fw-bold">Identitas Orang <span class="text-danger">*</span></label>
                                                <select class="form-select person-select" name="persons[{{ $pIdx }}][person_id]" required>
                                                    <option value="">-- Pilih Identitas --</option>
                                                    <optgroup label="Tersangka">
                                                        @foreach($suspects as $s)
                                                            <option value="{{ $s->id }}" data-type="suspect" {{ (isset($oldPerson['person_id']) && $oldPerson['person_id'] == $s->id) ? 'selected' : '' }}>{{ $s->name }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                    <optgroup label="Terlapor">
                                                        @foreach($reportedPersons as $s)
                                                            <option value="{{ $s->id }}" data-type="reported_person" {{ (isset($oldPerson['person_id']) && $oldPerson['person_id'] == $s->id) ? 'selected' : '' }}>{{ $s->name }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                </select>
                                                <input type="hidden" class="person-type-input" name="persons[{{ $pIdx }}][person_type]" value="{{ $oldPerson['person_type'] ?? '' }}">
                                                <input type="hidden" name="persons[{{ $pIdx }}][id]" value="{{ $oldPerson['id'] ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="row align-items-start">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold mb-2 d-block" style="min-height: 24px;">Tgl Berita Acara Penyitaan <span class="text-danger">*</span></label>
                                                <input class="form-control datepicker" name="persons[{{ $pIdx }}][bap_date]" placeholder="YYYY-MM-DD" autocomplete="off" value="{{ $oldPerson['bap_date'] ?? '' }}" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold mb-2 d-block" style="min-height: 24px;">Upload Berita Acara Penyitaan <span class="text-danger">*</span></label>
                                                @php
                                                    $hasExistingBap = !empty($oldPerson['bap_file']);
                                                    $bapFileName = '';
                                                    $bapUrl = '#';
                                                    if ($hasExistingBap) {
                                                        $bapFileName = basename($oldPerson['bap_file']);
                                                        $bapUrl = asset('file/penyitaan/berita-acara-penyitaan/' . $bapFileName);
                                                    }
                                                @endphp
                                                <div class="bap-file-box"
                                                     @if($hasExistingBap)
                                                         data-server-url="{{ $bapUrl }}"
                                                         data-server-name="{{ $bapFileName }}"
                                                     @endif>
                                                    <input type="file" class="form-control bap-file-input {{ $hasExistingBap ? 'd-none' : '' }}" name="persons[{{ $pIdx }}][bap_file]" accept=".pdf,application/pdf" {{ $hasExistingBap ? '' : 'required' }}>
                                                    <div class="bap-preview-wrapper {{ $hasExistingBap ? 'd-flex' : 'd-none' }} align-items-center justify-content-between p-2 border rounded bg-light" style="min-height: 38px;">
                                                        <div class="d-flex align-items-center text-truncate me-2">
                                                            <i class="bi bi-paperclip text-secondary me-1 fs-6"></i>
                                                            <span class="bap-file-status text-truncate small fw-semibold text-dark" title="{{ $bapFileName }}">
                                                                {{ $hasExistingBap ? 'BAP sudah diunggah: ' . $bapFileName : '' }}
                                                            </span>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-1 flex-shrink-0 small">
                                                            <a href="{{ $bapUrl }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2 btn-view-bap" style="font-size: 0.8rem; line-height: 1.8;">
                                                                Lihat BAP
                                                            </a>
                                                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 btn-change-bap" style="font-size: 0.8rem; line-height: 1.8;">
                                                                Ganti BAP
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label class="fw-bold">Lokasi Penyitaan <span class="text-danger">*</span></label>
                                                    @php $isWorkUnit = !isset($oldPerson['is_seized_at_work_unit']) || $oldPerson['is_seized_at_work_unit'] === '1' || $oldPerson['is_seized_at_work_unit'] === 'true' || $oldPerson['is_seized_at_work_unit'] === true; @endphp
                                                    <div class="d-flex align-items-center mt-1 gap-3">
                                                        <div class="form-check form-check-inline mb-0">
                                                            <input class="form-check-input location-radio" type="radio" name="persons[{{ $pIdx }}][is_seized_at_work_unit]" value="1" {{ $isWorkUnit ? 'checked' : '' }} data-target="location-input-{{ $pIdx }}">
                                                            <label class="form-check-label">Di Satker</label>
                                                        </div>
                                                        <div class="form-check form-check-inline mb-0">
                                                            <input class="form-check-input location-radio" type="radio" name="persons[{{ $pIdx }}][is_seized_at_work_unit]" value="0" {{ !$isWorkUnit ? 'checked' : '' }} data-target="location-input-{{ $pIdx }}">
                                                            <label class="form-check-label">Di luar Satker</label>
                                                        </div>
                                                        <div id="location-input-{{ $pIdx }}" style="display: {{ $isWorkUnit ? 'none' : 'block' }}; flex: 1;">
                                                            <input type="text" class="form-control @error("persons.{$pIdx}.seized_location") is-invalid @enderror" name="persons[{{ $pIdx }}][seized_location]" placeholder="Contoh: Jl. Ahmad Yani No. 10 / Desa Bontoa" value="{{ $oldPerson['seized_location'] ?? '' }}" {{ !$isWorkUnit ? 'required' : 'disabled' }}>
                                                            @error("persons.{$pIdx}.seized_location")
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <hr class="mt-0">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="fw-bold">Daftar Barang Sitaan</h6>
                                            <button type="button" class="btn btn-sm btn-primary add-item-btn" data-person-index="{{ $pIdx }}"><i class="bi bi-plus"></i> Tambah Barang</button>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm items-table" id="items-table-{{ $pIdx }}">
                                                <thead class="table-secondary text-center">
                                                    <tr>
                                                        <th>Nama Barang <span class="text-danger">*</span></th>
                                                        <th style="width: 15%">Jumlah <span class="text-danger">*</span></th>
                                                        <th style="width: 15%">Satuan <span class="text-danger">*</span></th>
                                                        <th style="width: 25%">Keterangan</th>
                                                        <th style="width: 5%">Opsi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(isset($oldPerson['seized_items']))
                                                        @foreach($oldPerson['seized_items'] as $itemIdx => $oldItem)
                                                            <tr>
                                                                <td><input type="text" class="form-control form-control-sm" name="persons[{{ $pIdx }}][seized_items][{{ $itemIdx }}][name]" value="{{ $oldItem['name'] ?? '' }}" placeholder="Contoh: Sepeda Motor Honda Vario No Pol AB 123 CD" required></td>
                                                                <td><input type="number" step="any" min="0.01" class="form-control form-control-sm" name="persons[{{ $pIdx }}][seized_items][{{ $itemIdx }}][quantity]" value="{{ $oldItem['quantity'] ?? '1' }}" required></td>
                                                                <td><input type="text" class="form-control form-control-sm" name="persons[{{ $pIdx }}][seized_items][{{ $itemIdx }}][unit]" value="{{ $oldItem['unit'] ?? '' }}" placeholder="Contoh: Unit / Buah / Lembar" required></td>
                                                                <td><input type="text" class="form-control form-control-sm" name="persons[{{ $pIdx }}][seized_items][{{ $itemIdx }}][description]" value="{{ $oldItem['description'] ?? '' }}" placeholder="Contoh: Kondisi rusak ringan / surat lengkap"></td>
                                                                <td class="text-center align-middle">
                                                                    <button type="button" class="btn btn-sm btn-danger remove-item-btn"><i class="bi bi-trash"></i></button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <hr class="mt-4 mb-4">

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
                            $existingOfficerId = null;
                            $signatoryDoc = $officers->where('class', 'SIGNATORY')->first();
                            if ($signatoryDoc) {
                                $matched = $authorizedSignatories->firstWhere('register_number', $signatoryDoc->register_number);
                                if ($matched) {
                                    $existingOfficerId = $matched->id;
                                }
                            }
                            $selectedOfficerId = old('officers', $existingOfficerId);
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
                                        @endphp
                                        <option value="{{ $signatory->id }}" {{ $selectedOfficerId == $signatory->id ? 'selected' : '' }}>
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
                            <label class="fw-bold col-sm-3 col-form-label" for="carbonCopies">Tembusan</label>
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
                    <!-- </div> -->
                <!-- </div> -->




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



@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>


    <script type="text/javascript">
        $(document).ready(function() {
            // Scroll to first error on page load (backend validation)
            var $backendError = $('.is-invalid:visible, .frontend-error:visible').first();
            if ($backendError.length) {
                $backendError[0].scrollIntoView({behavior: 'smooth', block: 'center'});
            }

            setInterval(function() {
                $('#attentionBox').toggleClass('alert-danger alert-warning');
            }, 1000);

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
                tags: false,
                placeholder: '-- Pilih atau Ketik No SP Penyidikan --',
                allowClear: true
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
                } else {
                    $('#sprindik_date').val('');
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
                tags: false,
                placeholder: '-- Pilih atau Ketik Nomor SPDP --',
                allowClear: true,
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



            // Auto-fill Tgl SPDP when selecting existing SPDP with data-date
            $('#spdp_number').on('change', function() {
                var selected = $(this).find(':selected');
                var date = selected.data('date');
                if (date) {
                    $('#spdp_date').val(date).removeClass('is-invalid');
                } else {
                    $('#spdp_date').val('');
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
            

    var suspectsList = @json($suspects);
    var reportedPersonsList = @json($reportedPersons);
    var personIndex = {{ count($renderPersons ?? []) }};

    function renderPersonSelect(index) {
        var options = '<option value="">-- Pilih Identitas --</option>';
        options += '<optgroup label="Tersangka">';
        suspectsList.forEach(function(s) {
            options += `<option value="${s.id}" data-type="suspect">${s.name}</option>`;
        });
        options += '</optgroup><optgroup label="Terlapor">';
        reportedPersonsList.forEach(function(s) {
            options += `<option value="${s.id}" data-type="reported_person">${s.name}</option>`;
        });
        options += '</optgroup>';
        return options;
    }

    $('#addPersonBtn').click(function() {
        var html = `
            <div class="card mb-3 border-primary person-card" data-index="${personIndex}">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-2">
                    <h6 class="mb-0">Data Pihak #${personIndex + 1} dan Barang Sitaannya</h6>
                    <button type="button" class="btn btn-sm btn-light text-danger remove-person-btn"><i class="bi bi-trash"></i> Hapus</button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="fw-bold">Identitas Orang <span class="text-danger">*</span></label>
                            <select class="form-select person-select" name="persons[${personIndex}][person_id]" required>
                                ${renderPersonSelect(personIndex)}
                            </select>
                            <input type="hidden" class="person-type-input" name="persons[${personIndex}][person_type]">
                            <input type="hidden" name="persons[${personIndex}][id]" value="">
                        </div>
                    </div>
                    <div class="row align-items-start">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold mb-2 d-block" style="min-height: 24px;">Tgl Berita Acara Penyitaan <span class="text-danger">*</span></label>
                            <input class="form-control datepicker" name="persons[${personIndex}][bap_date]" placeholder="YYYY-MM-DD" autocomplete="off" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold mb-2 d-block" style="min-height: 24px;">Upload Berita Acara Penyitaan <span class="text-danger">*</span></label>
                            <div class="bap-file-box">
                                <input type="file" class="form-control bap-file-input" name="persons[${personIndex}][bap_file]" accept=".pdf,application/pdf" required>
                                <div class="bap-preview-wrapper d-none align-items-center justify-content-between p-2 border rounded bg-light" style="min-height: 38px;">
                                    <div class="d-flex align-items-center text-truncate me-2">
                                        <i class="bi bi-paperclip text-secondary me-1 fs-6"></i>
                                        <span class="bap-file-status text-truncate small fw-semibold text-dark"></span>
                                    </div>
                                    <div class="d-flex align-items-center gap-1 flex-shrink-0 small">
                                        <a href="#" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2 btn-view-bap" style="font-size: 0.8rem; line-height: 1.8;">
                                            Lihat BAP
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 btn-change-bap" style="font-size: 0.8rem; line-height: 1.8;">
                                            Ganti BAP
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="fw-bold">Lokasi Penyitaan <span class="text-danger">*</span></label>
                            <div class="d-flex align-items-center mt-1 gap-3">
                                <div class="form-check form-check-inline mb-0">
                                    <input class="form-check-input location-radio" type="radio" name="persons[${personIndex}][is_seized_at_work_unit]" value="1" checked data-target="location-input-${personIndex}">
                                    <label class="form-check-label">Di Satker</label>
                                </div>
                                <div class="form-check form-check-inline mb-0">
                                    <input class="form-check-input location-radio" type="radio" name="persons[${personIndex}][is_seized_at_work_unit]" value="0" data-target="location-input-${personIndex}">
                                    <label class="form-check-label">Di luar Satker</label>
                                </div>
                                <div id="location-input-${personIndex}" style="display: none; flex: 1;">
                                    <input type="text" class="form-control" name="persons[${personIndex}][seized_location]" placeholder="Contoh: Jl. Ahmad Yani No. 10 / Desa Bontoa" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="mt-0">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold">Daftar Barang Sitaan</h6>
                        <button type="button" class="btn btn-sm btn-primary add-item-btn" data-person-index="${personIndex}"><i class="bi bi-plus"></i> Tambah Barang</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm items-table" id="items-table-${personIndex}">
                            <thead class="table-secondary text-center">
                                <tr>
                                    <th>Nama Barang <span class="text-danger">*</span></th>
                                    <th style="width: 15%">Jumlah <span class="text-danger">*</span></th>
                                    <th style="width: 15%">Satuan <span class="text-danger">*</span></th>
                                    <th style="width: 25%">Keterangan</th>
                                    <th style="width: 5%">Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
        $('#persons-container').append(html);
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
        personIndex++;
    });

    $(document).on('change', '.person-select', function() {
        var type = $(this).find('option:selected').data('type');
        $(this).closest('.person-card').find('.person-type-input').val(type || '');
    });

    $(document).on('change', '.location-radio', function() {
        var targetId = $(this).data('target');
        var isWorkUnit = $(this).val() === '1';
        var $inputContainer = $('#' + targetId);
        var $input = $inputContainer.find('input');
        
        if (isWorkUnit) {
            $inputContainer.hide();
            $input.prop('disabled', true);
            $input.prop('required', false);
            $input.removeClass('is-invalid');
            $inputContainer.find('.frontend-error, .invalid-feedback').remove();
        } else {
            $inputContainer.show();
            $input.prop('disabled', false);
            $input.prop('required', true);
        }
    });

    $(document).on('input', 'input[name^="persons"][name$="[seized_location]"]', function() {
        if ($(this).val().trim()) {
            $(this).removeClass('is-invalid');
            $(this).closest('div').find('.frontend-error, .invalid-feedback').remove();
        }
    });

    $(document).on('click', '.remove-person-btn', function() {
        var $card = $(this).closest('.person-card');
        var prevUrl = $card.find('.bap-file-box').data('object-url');
        if (prevUrl) {
            URL.revokeObjectURL(prevUrl);
        }
        $card.remove();
    });

    $(document).on('click', '.add-item-btn', function() {
        var pIdx = $(this).data('person-index');
        var tbody = $('#items-table-' + pIdx + ' tbody');
        var itemIdx = tbody.find('tr').length;
        var tr = `
            <tr>
                <td><input type="text" class="form-control form-control-sm" name="persons[${pIdx}][seized_items][${itemIdx}][name]" placeholder="Contoh: Sepeda Motor Honda Vario No Pol AB 123 CD" required></td>
                <td><input type="number" step="any" min="0.01" class="form-control form-control-sm" name="persons[${pIdx}][seized_items][${itemIdx}][quantity]" value="1" required></td>
                <td><input type="text" class="form-control form-control-sm" name="persons[${pIdx}][seized_items][${itemIdx}][unit]" placeholder="Contoh: Unit / Buah / Lembar" required></td>
                <td><input type="text" class="form-control form-control-sm" name="persons[${pIdx}][seized_items][${itemIdx}][description]" placeholder="Contoh: Kondisi rusak ringan / surat lengkap"></td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-sm btn-danger remove-item-btn"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
        `;
        tbody.append(tr);
    });

    $(document).on('click', '.remove-item-btn', function() {
        $(this).closest('tr').remove();
    });

    // Remove old addSeizedItemModal if exists
    $('#addSeizedItemModal').remove();

    // BAP File Preview & Change Handler
    $(document).on('change', '.bap-file-input', function() {
        var input = this;
        var $box = $(this).closest('.bap-file-box');
        var $wrapper = $box.find('.bap-preview-wrapper');
        var $status = $box.find('.bap-file-status');
        var $viewBtn = $box.find('.btn-view-bap');

        var prevUrl = $box.data('object-url');
        if (prevUrl) {
            URL.revokeObjectURL(prevUrl);
            $box.removeData('object-url');
        }

        if (input.files && input.files.length > 0) {
            var file = input.files[0];
            var objectUrl = URL.createObjectURL(file);
            $box.data('object-url', objectUrl);

            $status.text('BAP sudah dipilih: ' + file.name).attr('title', file.name);
            $viewBtn.attr('href', objectUrl);

            $(input).addClass('d-none');
            $wrapper.removeClass('d-none').addClass('d-flex');

            $(input).removeClass('is-invalid');
            $wrapper.removeClass('border border-danger');
            $box.parent().find('.frontend-error').remove();
        } else {
            var serverUrl = $box.data('server-url');
            var serverName = $box.data('server-name');
            if (serverUrl) {
                $status.text('BAP sudah diunggah: ' + serverName).attr('title', serverName);
                $viewBtn.attr('href', serverUrl);
                $(input).addClass('d-none');
                $wrapper.removeClass('d-none').addClass('d-flex');
            } else {
                $(input).removeClass('d-none');
                $wrapper.addClass('d-none').removeClass('d-flex');
            }
        }
    });

    $(document).on('click', '.btn-change-bap', function(e) {
        e.preventDefault();
        var $box = $(this).closest('.bap-file-box');
        var $fileInput = $box.find('.bap-file-input');
        $fileInput.trigger('click');
    });

    $(window).on('beforeunload', function() {
        $('.bap-file-box').each(function() {
            var url = $(this).data('object-url');
            if (url) {
                URL.revokeObjectURL(url);
            }
        });
        var spUrl = $('.sp-file-box').data('object-url');
        if (spUrl) {
            URL.revokeObjectURL(spUrl);
        }
    });

    // ─── TOGGLE SURAT PERINTAH PENYITAAN ───────────────────────────────────
    $('input[name="has_surat_perintah_penyitaan"]').on('change', function() {
        if ($(this).val() == '1') {
            $('#sp_penyitaan_container').slideDown();
        } else {
            $('#sp_penyitaan_container').slideUp();
            // Reset fields if toggled to No, allowing validation to pass
            // But we do not clear input fields dynamically in edit so user can revert without losing data
            // Just clear visual validation errors
            $('#sp_penyitaan_container .frontend-error').remove();
            $('#sp_penyitaan_container .invalid-feedback').remove();
            $('#sp_penyitaan_container .is-invalid').removeClass('is-invalid');
        }
    });

    if ($('input[name="has_surat_perintah_penyitaan"]:checked').val() == '1') {
        $('#sp_penyitaan_container').show();
    }

    // SP Penyitaan File Preview Logic
    $('.sp-file-input').on('change', function(e) {
        var input = e.target;
        var $box = $(this).closest('.sp-file-box');
        var $wrapper = $box.find('.sp-preview-wrapper');
        var $status = $box.find('.sp-status');
        var $viewBtn = $box.find('.sp-view-btn');

        if ($box.data('object-url')) {
            URL.revokeObjectURL($box.data('object-url'));
            $box.data('object-url', null);
        }

        if (input.files && input.files[0]) {
            var file = input.files[0];
            var objectUrl = URL.createObjectURL(file);
            $box.data('object-url', objectUrl);

            $status.text('SPRINSITA siap diunggah: ' + file.name).attr('title', file.name);
            $viewBtn.attr('href', objectUrl);
            $(this).addClass('d-none');
            $wrapper.removeClass('d-none').addClass('d-flex');
            $box.find('.sp-file-help').addClass('d-none');
        } else {
            var hasExisting = $box.data('has-existing') === true || $box.data('has-existing') === 'true';
            if (hasExisting) {
                var serverName = '{{ $document->surat_perintah_penyitaan_file ?? "" }}';
                var serverUrl = '{{ !empty($document->surat_perintah_penyitaan_file) ? asset("file/penyitaan/surat-perintah-penyitaan/" . $document->surat_perintah_penyitaan_file) : "#" }}';
                $status.text('SPRINSITA sudah diunggah: ' + serverName).attr('title', serverName);
                $viewBtn.attr('href', serverUrl);
                $(this).addClass('d-none');
                $wrapper.removeClass('d-none').addClass('d-flex');
            } else {
                $(this).removeClass('d-none');
                $wrapper.addClass('d-none').removeClass('d-flex');
                $box.find('.sp-file-help').removeClass('d-none');
            }
        }
    });

    $(document).on('click', '.btn-change-sp', function(e) {
        e.preventDefault();
        var $box = $(this).closest('.sp-file-box');
        var $fileInput = $box.find('.sp-file-input');
        $fileInput.trigger('click');
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
                    var $target = $el.next('.select2-container').length ? $el.next('.select2-container') : ($el.closest('.bap-file-box').length ? $el.closest('.bap-file-box') : $el);
                    if ($el.closest('.bap-file-box').length) {
                        $el.closest('.bap-file-box').find('.bap-preview-wrapper').addClass('border border-danger');
                    }
                    $target.next('.invalid-feedback').remove();
                    $target.after('<div class="invalid-feedback d-block frontend-error">' + msg + '</div>');
                    errors.push(msg);
                }

                var hasSp = $('input[name="has_surat_perintah_penyitaan"]:checked').val();
                if (!hasSp) {
                    markError($('input[name="has_surat_perintah_penyitaan"]').closest('.col-lg-9'), 'Pilihan Ada/Tidak Ada SPRINSITA wajib dipilih.');
                } else if (hasSp == '1') {
                    var $spNumber = $('#surat_perintah_penyitaan_number');
                    if (!$spNumber.val() || !$spNumber.val().trim()) {
                        markError($spNumber, 'Nomor SPRINSITA wajib diisi jika Ada SPRINSITA.');
                    }
                    var $spDate = $('#surat_perintah_penyitaan_date');
                    if (!$spDate.val() || !$spDate.val().trim()) {
                        markError($spDate, 'Tanggal SPRINSITA wajib diisi jika Ada SPRINSITA.');
                    }
                    var $spFile = $('#surat_perintah_penyitaan_file');
                    var spHasExisting = $('.sp-file-box').data('has-existing') === true || $('.sp-file-box').data('has-existing') === 'true';
                    if (!spHasExisting && (!$spFile.val() || !$spFile[0].files || $spFile[0].files.length === 0)) {
                        markError($spFile, 'File SPRINSITA wajib diupload jika Ada SPRINSITA.');
                    } else if ($spFile[0].files && $spFile[0].files.length > 0) {
                        var spFileObj = $spFile[0].files[0];
                        var spIsPdf = spFileObj.type === 'application/pdf' || spFileObj.name.toLowerCase().endsWith('.pdf');
                        if (!spIsPdf) {
                            markError($spFile, 'Format file SPRINSITA harus berupa PDF.');
                        } else if (spFileObj.size > 10 * 1024 * 1024) {
                            markError($spFile, 'Ukuran file SPRINSITA maksimal 10 MB.');
                        }
                    }
                }

                if (!$('#document_number').val().trim()) {
                    markError($('#document_number'), 'Nomor Dokumen wajib diisi.');
                }
                var docClassificationVal = $('#documentClassification').val() || $('#classification').val();
                if (!docClassificationVal) {
                    markError($('#documentClassification').length ? $('#documentClassification') : $('#classification'), 'Klasifikasi Dokumen wajib dipilih.');
                }
                if (!$('#document_date').val().trim()) {
                    markError($('#document_date'), 'Tanggal Dokumen wajib diisi.');
                }
                if (!$('#surat_perintah_penyidikan_document_id').val()) {
                    markError($('#surat_perintah_penyidikan_document_id'), 'No SP Penyidikan wajib dipilih.');
                }
                if (!$('#sprindik_date').val().trim()) {
                    markError($('#sprindik_date'), 'Tanggal SP Penyidikan wajib diisi.');
                }
                var selSprindik = $('#surat_perintah_penyidikan_document_id').find(':selected');
                if (selSprindik.data('number') && !$('#sprindik_number').val()) {
                    $('#sprindik_number').val(selSprindik.data('number'));
                }
                if (!$('#court_id').val()) {
                    markError($('#court_id'), 'Pengadilan Negeri Tujuan wajib dipilih.');
                }
                if (!$('#officerLeader').val()) {
                    markError($('#officerLeader'), 'Ketua Tim Penyidik wajib dipilih.');
                }

                var selectedOfficers = $('#officers').val();
                if (!selectedOfficers || selectedOfficers.length === 0) {
                    markError($('#officers'), 'Pejabat Penandatangan wajib dipilih minimal 1 orang.');
                }

                

                // Check Carbon Copies
                var ccCount = $('#carbonCopiesContainer input[name="carbonCopies[]"]').length;
                if (ccCount === 0) {
                    markError($('#carbonCopiesContainer'), 'Tembusan wajib diisi minimal 1.');
                } else {
                    $('#carbonCopiesContainer input[name="carbonCopies[]"]').each(function() {
                        if (!$(this).val().trim()) {
                            markError($(this), 'Isi tembusan tidak boleh kosong.');
                        }
                    });
                }

                // Check Persons
                var personCount = $('.person-card').length;
                if (personCount === 0) {
                    markError($('#persons-container'), 'Minimal harus ada 1 orang saksi/tersangka yang barangnya disita.');
                } else {
                    $('.person-card').each(function() {
                        var $personSelect = $(this).find('select[name^="persons"][name$="[person_id]"]');
                        if (!$personSelect.val()) {
                            markError($personSelect, 'Identitas orang wajib dipilih.');
                        }

                        var $bapDate = $(this).find('input[name^="persons"][name$="[bap_date]"]');
                        if (!$bapDate.val() || !$bapDate.val().trim()) {
                            markError($bapDate, 'Tgl Berita Acara Penyitaan wajib diisi.');
                        }

                        var $bapBox = $(this).find('.bap-file-box');
                        var $bapFile = $(this).find('input[type="file"][name^="persons"][name$="[bap_file]"]');
                        var hasExistingBap = !!($bapBox.data('server-url') || $bapBox.data('server-name'));
                        var hasSelectedFile = $bapFile.length && $bapFile[0].files && $bapFile[0].files.length > 0;

                        if (!hasExistingBap && !hasSelectedFile) {
                            markError($bapFile, 'BAP wajib diupload.');
                        } else if (hasSelectedFile) {
                            var file = $bapFile[0].files[0];
                            var isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
                            if (!isPdf) {
                                markError($bapFile, 'Format file BAP harus berupa PDF.');
                            } else if (file.size > 10 * 1024 * 1024) {
                                markError($bapFile, 'Ukuran file BAP maksimal 10 MB.');
                            }
                        }

                        // Validate Lokasi Penyitaan
                        var isWorkUnit = $(this).find('input[name^="persons"][name$="[is_seized_at_work_unit]"]:checked').val() === '1';
                        var $seizedLocation = $(this).find('input[name^="persons"][name$="[seized_location]"]');
                        if (!isWorkUnit && (!$seizedLocation.val() || !$seizedLocation.val().trim())) {
                            markError($seizedLocation, 'Lokasi penyitaan wajib diisi jika penyitaan dilakukan di luar Satker.');
                        }

                        // Validate seized items
                        var itemCount = $(this).find('.items-table tbody tr').length;
                        if (itemCount === 0) {
                            markError($(this).find('.items-table'), 'Minimal harus ada 1 barang sitaan.');
                        } else {
                            $(this).find('.items-table tbody tr').each(function() {
                                var $itemName = $(this).find('input[name^="persons"][name$="[name]"]');
                                if (!$itemName.val() || !$itemName.val().trim()) {
                                    markError($itemName, 'Nama barang sitaan wajib diisi.');
                                }
                                var $itemQty = $(this).find('input[name^="persons"][name$="[quantity]"]');
                                var qtyVal = parseFloat($itemQty.val());
                                if (!$itemQty.val() || isNaN(qtyVal) || qtyVal <= 0) {
                                    markError($itemQty, 'Jumlah barang sitaan harus lebih besar dari 0.');
                                }
                                var $itemUnit = $(this).find('input[name^="persons"][name$="[unit]"]');
                                if (!$itemUnit.val() || !$itemUnit.val().trim()) {
                                    markError($itemUnit, 'Satuan barang sitaan wajib diisi.');
                                }
                            });
                        }
                    });
                }

                if (errors.length > 0) {
                    var $firstError = $('.is-invalid:visible, .frontend-error:visible').first();
                    if ($firstError.length) {
                        $firstError[0].scrollIntoView({behavior: 'smooth', block: 'center'});
                    }
                    return false;
                }

                Swal.fire({
                    title: 'Konfirmasi Perubahan',
                    text: 'Apakah perubahan Surat Laporan Persetujuan Penyitaan sudah sesuai?',
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
