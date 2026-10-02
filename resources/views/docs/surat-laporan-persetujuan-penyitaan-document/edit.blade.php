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
            <h5 class="fw-bold text-blue-dark">Edit Surat Laporan Persetujuan Penyitaan</h5>
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
            <form action="{{ route('doc.surat-laporan-persetujuan-penyitaan-document.update', ['id' => $id, 'accident_id' => $accidentId]) }}"
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

                <!-- 4. DAFTAR ORANG & BARANG SITAAN -->
                <div class="card mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-blue-dark">Daftar Orang dan Barang Sitaan<span class="text-danger fs-5">*</span></h6>
                        <button class="btn btn-md btn-primary" id="addPersonBtn" type="button">
                            <i class="bi bi-person-plus-fill me-1"></i> Tambah Orang
                        </button>
                    </div>
                    <div class="card-body bg-light">
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
                                            'person_id' => $personModel->suspect_id ?? $personModel->witness_id ?? $personModel->reported_person_id,
                                            'person_type' => $personModel->suspect_id ? 'suspect' : ($personModel->witness_id ? 'witness' : 'reported_person'),
                                            'bap_number' => $personModel->bap_number,
                                            'bap_date' => $personModel->bap_date ? (is_string($personModel->bap_date) ? date('Y-m-d', strtotime($personModel->bap_date)) : $personModel->bap_date->format('Y-m-d')) : '',
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
                                        <h6 class="mb-0">Data Orang #{{ $pIdx + 1 }}</h6>
                                        <button type="button" class="btn btn-sm btn-light text-danger remove-person-btn"><i class="bi bi-trash"></i> Hapus</button>
                                    </div>
                                    <div class="card-body bg-white">
                                        <div class="row">
                                            <div class="col-md-6 mb-3 d-flex flex-column">
                                                <label class="fw-bold">Identitas Orang <span class="text-danger">*</span></label>
                                                <select class="form-select person-select mt-auto" name="persons[{{ $pIdx }}][person_id]" required>
                                                    <option value="">-- Pilih Identitas --</option>
                                                    <optgroup label="Tersangka">
                                                        @foreach($suspects as $s)
                                                            <option value="{{ $s->id }}" data-type="suspect" {{ (isset($oldPerson['person_id']) && $oldPerson['person_id'] == $s->id) ? 'selected' : '' }}>{{ $s->name }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                    <optgroup label="Saksi">
                                                        @foreach(isset($witnesses) ? $witnesses : [] as $s)
                                                            <option value="{{ $s->id }}" data-type="witness" {{ (isset($oldPerson['person_id']) && $oldPerson['person_id'] == $s->id) ? 'selected' : '' }}>{{ $s->name }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                    <optgroup label="Terlapor">
                                                        @foreach($reportedPersons as $s)
                                                            <option value="{{ $s->id }}" data-type="reported_person" {{ (isset($oldPerson['person_id']) && $oldPerson['person_id'] == $s->id) ? 'selected' : '' }}>{{ $s->name }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                </select>
                                                <input type="hidden" class="person-type-input" name="persons[{{ $pIdx }}][person_type]" value="{{ $oldPerson['person_type'] ?? '' }}">
                                            </div>
                                            <div class="col-md-4 mb-3 d-flex flex-column">
                                                <label class="fw-bold">No. Berita Acara Penyitaan <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control mt-auto" name="persons[{{ $pIdx }}][bap_number]" placeholder="No. Berita Acara Penyitaan" value="{{ $oldPerson['bap_number'] ?? '' }}" required>
                                            </div>
                                            <div class="col-md-2 mb-3 d-flex flex-column">
                                                <label class="fw-bold">Tgl Berita Acara Penyitaan <span class="text-danger">*</span></label>
                                                <input class="form-control datepicker mt-auto" name="persons[{{ $pIdx }}][bap_date]" placeholder="YYYY-MM-DD" autocomplete="off" value="{{ $oldPerson['bap_date'] ?? '' }}" required>
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
                                                            <input type="text" class="form-control" name="persons[{{ $pIdx }}][seized_location]" placeholder="Contoh: Jl. Ahmad Yani No. 10 / Desa Bontoa" value="{{ $oldPerson['seized_location'] ?? '' }}" {{ !$isWorkUnit ? 'required' : 'disabled' }}>
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

                var rowHtml = `
                    <tr class="law-row text-center">
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

    var suspectsList = @json($suspects);
    var witnessesList = @json(isset($witnesses) ? $witnesses : []);
    var reportedPersonsList = @json($reportedPersons);
    var personIndex = {{ count($renderPersons ?? []) }};

    function renderPersonSelect(index) {
        var options = '<option value="">-- Pilih Identitas --</option>';
        options += '<optgroup label="Tersangka">';
        suspectsList.forEach(function(s) {
            options += `<option value="${s.id}" data-type="suspect">${s.name}</option>`;
        });
        options += '</optgroup><optgroup label="Saksi">';
        witnessesList.forEach(function(s) {
            options += `<option value="${s.id}" data-type="witness">${s.name}</option>`;
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
                    <h6 class="mb-0">Data Orang #${personIndex + 1}</h6>
                    <button type="button" class="btn btn-sm btn-light text-danger remove-person-btn"><i class="bi bi-trash"></i> Hapus</button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3 d-flex flex-column">
                            <label class="fw-bold">Identitas Orang <span class="text-danger">*</span></label>
                            <select class="form-select person-select mt-auto" name="persons[${personIndex}][person_id]" required>
                                ${renderPersonSelect(personIndex)}
                            </select>
                            <input type="hidden" class="person-type-input" name="persons[${personIndex}][person_type]">
                        </div>
                        <div class="col-md-4 mb-3 d-flex flex-column">
                            <label class="fw-bold">No. Berita Acara Penyitaan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control mt-auto" name="persons[${personIndex}][bap_number]" placeholder="No. Berita Acara Penyitaan" required>
                        </div>
                        <div class="col-md-2 mb-3 d-flex flex-column">
                            <label class="fw-bold">Tgl Berita Acara Penyitaan <span class="text-danger">*</span></label>
                            <input class="form-control datepicker mt-auto" name="persons[${personIndex}][bap_date]" placeholder="YYYY-MM-DD" autocomplete="off" required>
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
        } else {
            $inputContainer.show();
            $input.prop('disabled', false);
            $input.prop('required', true);
        }
    });

    $(document).on('click', '.remove-person-btn', function() {
        $(this).closest('.person-card').remove();
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
                        var $bapNumber = $(this).find('input[name^="persons"][name$="[bap_number]"]');
                        if (!$bapNumber.val() || !$bapNumber.val().trim()) {
                            markError($bapNumber, 'No. Berita Acara Penyitaan wajib diisi.');
                        }
                        var $bapDate = $(this).find('input[name^="persons"][name$="[bap_date]"]');
                        if (!$bapDate.val() || !$bapDate.val().trim()) {
                            markError($bapDate, 'Tgl Berita Acara Penyitaan wajib diisi.');
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
                        $('html, body').animate({
                            scrollTop: Math.max(0, $firstError.offset().top - 120)
                        }, 400);
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
