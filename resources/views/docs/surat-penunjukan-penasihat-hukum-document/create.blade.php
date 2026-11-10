@php
    $_title = 'Surat Penunjukan Penasihat Hukum';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <style>
        .input-group > .select2-container--bootstrap4 {
            flex: 1 1 auto;
            width: 1% !important;
        }
        .input-group > .select2-container--bootstrap4 .select2-selection--single {
            height: calc(1.5em + .75rem + 2px) !important;
            display: flex;
            align-items: center;
        }
        .form-section-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            background: #ffffff;
            overflow: hidden;
        }
        .form-section-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 18px;
            font-weight: 700;
            color: #1e3a8a;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .form-section-body {
            padding: 18px;
        }
        .field-hint {
            font-size: 0.78rem;
            color: #64748b;
            margin-top: 2px;
        }
        .badge-variable {
            font-family: monospace;
            background: #e0f2fe;
            color: #0369a1;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.75rem;
        }
    </style>
@endpush

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}">
        <i class="bi bi-arrow-left"></i> Kembali ke Progress Perkara
    </a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Surat Penunjukan Penasihat Hukum</h5>
            <small class="text-muted d-block mb-3">Kode Dokumen: <b>DCT-0704</b></small>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <div class="box-body">
            <form action="{{ route('doc.surat-penunjukan-penasihat-hukum-document.store', ['accident_id' => $accidentId]) }}"
                method="POST" id="formSuratPenunjukan" novalidate>
                @csrf
                <input type="hidden" name="accident_id" id="accident_id" value="{{ $accidentId }}">
                <input type="hidden" name="suspect_id" id="suspect_id" value="{{ old('suspect_id', $firstSuspect->id ?? '') }}">

                {{-- 1. METADATA & ALAMAT TUJUAN SURAT --}}
                <div class="form-section-card">
                    <div class="form-section-header">
                        <span><i class="bi bi-envelope-paper me-2"></i>1. Metadata & Alamat Tujuan Surat</span>
                        <span class="badge badge-light border text-muted">Nomor & Tujuan</span>
                    </div>
                    <div class="form-section-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold small mb-1" for="document_number">
                                    Nomor Surat <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="document_number" name="document_number"
                                    value="{{ old('document_number', $defaultDocNumber) }}" required>
                                <div class="field-hint">Contoh: B/123/XI/RES.1.24/2026/Satlantas</div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="fw-bold small mb-1" for="document_classification_name">
                                    Klasifikasi
                                </label>
                                <input type="text" class="form-control" id="document_classification_name" name="document_classification_name"
                                    value="{{ old('document_classification_name', 'Biasa') }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="fw-bold small mb-1" for="appendix">
                                    Lampiran
                                </label>
                                <input type="text" class="form-control" id="appendix" name="appendix"
                                    value="{{ old('appendix', '-') }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="fw-bold small mb-1" for="document_location">
                                    Kota Dikeluarkan
                                </label>
                                <input type="text" class="form-control" id="document_location" name="document_location"
                                    value="{{ old('document_location', $defaultKota) }}" required>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="fw-bold small mb-1" for="document_date">
                                    Tanggal Surat
                                </label>
                                <input type="text" class="form-control" id="document_date" name="document_date"
                                    value="{{ old('document_date', $defaultDocDate) }}" required>
                            </div>
                            <div class="col-md-7 mb-2">
                                <label class="fw-bold small mb-1" for="target_legal_aid_name">
                                    Kepada Yth. (Nama Lembaga Bantuan Hukum) <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="target_legal_aid_name" name="target_legal_aid_name"
                                    value="{{ old('target_legal_aid_name', $defaultTargetLegalAid) }}" placeholder="Contoh: Ketua Pos Bantuan Hukum (Posbakum) Pengadilan Negeri Pasuruan" required>
                            </div>
                            <div class="col-md-5 mb-2">
                                <label class="fw-bold small mb-1" for="target_legal_aid_location">
                                    di Tempat / Kota
                                </label>
                                <input type="text" class="form-control" id="target_legal_aid_location" name="target_legal_aid_location"
                                    value="{{ old('target_legal_aid_location', $defaultKota) }}" placeholder="Contoh: Pasuruan" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. DASAR RUJUKAN PENUNJUKAN (POIN 1) --}}
                <div class="form-section-card">
                    <div class="form-section-header">
                        <span><i class="bi bi-file-earmark-ruled me-2"></i>2. Dasar Rujukan Penunjukan (Poin 1)</span>
                        <span class="badge badge-light border text-muted">Dasar Hukum & Dokumen</span>
                    </div>
                    <div class="form-section-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small mb-1" for="dasar_kuhap">
                                    Rujukan KUHAP (Poin 1.a)
                                </label>
                                <input type="text" class="form-control" id="dasar_kuhap" name="dasar_kuhap"
                                    value="{{ old('dasar_kuhap', $defaultDasarKuhap) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small mb-1" for="undang_undang_lain">
                                    Undang-Undang Lain (Poin 1.c)
                                </label>
                                <input type="text" class="form-control" id="undang_undang_lain" name="undang_undang_lain"
                                    value="{{ old('undang_undang_lain', $defaultUuLain) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small mb-1" for="accident_number">
                                    Nomor Laporan Polisi (Poin 1.d)
                                </label>
                                <input type="text" class="form-control" id="accident_number" name="accident_number"
                                    value="{{ old('accident_number', $defaultNoLp) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small mb-1" for="accident_date">
                                    Tanggal Laporan Polisi (Poin 1.d)
                                </label>
                                <input type="text" class="form-control" id="accident_date" name="accident_date"
                                    value="{{ old('accident_date', $defaultTglLp) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small mb-1" for="surat_perintah_penyidikan_document_number">
                                    Nomor Surat Perintah Penyidikan (Poin 1.e)
                                </label>
                                <input type="text" class="form-control" id="surat_perintah_penyidikan_document_number" name="surat_perintah_penyidikan_document_number"
                                    value="{{ old('surat_perintah_penyidikan_document_number', $defaultNoSprindik) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small mb-1" for="surat_perintah_penyidikan_document_date">
                                    Tanggal Surat Perintah Penyidikan (Poin 1.e)
                                </label>
                                <input type="text" class="form-control" id="surat_perintah_penyidikan_document_date" name="surat_perintah_penyidikan_document_date"
                                    value="{{ old('surat_perintah_penyidikan_document_date', $defaultTglSprindik) }}" required>
                            </div>
                        </div>

                        {{-- Rujukan Surat Keterangan Tidak Mampu (SKTM) --}}
                        <div class="p-3 bg-light rounded border mt-2">
                            <span class="fw-bold small text-dark d-block mb-2">
                                <i class="bi bi-file-text me-1"></i>Rujukan Surat Keterangan Tidak Mampu (SKTM) - Poin 1.f
                            </span>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="small fw-bold mb-1" for="surat_keterangan_tidak_mampu_instansi">
                                        Surat Kepala (Instansi Penerbit SKTM)
                                    </label>
                                    <input type="text" class="form-control form-control-sm" id="surat_keterangan_tidak_mampu_instansi" name="surat_keterangan_tidak_mampu_instansi"
                                        value="{{ old('surat_keterangan_tidak_mampu_instansi', 'Kelurahan Bangil') }}" placeholder="Contoh: Kelurahan Bangil / Desa Sukorejo">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="small fw-bold mb-1" for="surat_keterangan_tidak_mampu_number">
                                        Nomor SKTM
                                    </label>
                                    <input type="text" class="form-control form-control-sm" id="surat_keterangan_tidak_mampu_number" name="surat_keterangan_tidak_mampu_number"
                                        value="{{ old('surat_keterangan_tidak_mampu_number', '470/12/424.011/2026') }}">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="small fw-bold mb-1" for="surat_keterangan_tidak_mampu_date">
                                        Tanggal SKTM
                                    </label>
                                    <input type="text" class="form-control form-control-sm" id="surat_keterangan_tidak_mampu_date" name="surat_keterangan_tidak_mampu_date"
                                        value="{{ old('surat_keterangan_tidak_mampu_date', $defaultDocDate) }}">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="small fw-bold mb-1" for="surat_keterangan_tidak_mampu_person_name">
                                        Nama pada SKTM
                                    </label>
                                    <input type="text" class="form-control form-control-sm" id="surat_keterangan_tidak_mampu_person_name" name="surat_keterangan_tidak_mampu_person_name"
                                        value="{{ old('surat_keterangan_tidak_mampu_person_name', $firstSuspect->name ?? '') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. IDENTITAS TERSANGKA (POIN 2) --}}
                <div class="form-section-card">
                    <div class="form-section-header">
                        <span><i class="bi bi-person me-2"></i>3. Identitas Tersangka yang Didampingi (Poin 2)</span>
                        <span class="badge badge-light border text-muted">Data Tersangka</span>
                    </div>
                    <div class="form-section-body">
                        @if ($suspects && $suspects->isNotEmpty())
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="fw-bold small text-primary mb-1">
                                        <i class="bi bi-lightning-charge me-1"></i>Pilih Cepat Tersangka dari Perkara:
                                    </label>
                                    <select id="select_suspect" class="form-control select2">
                                        <option value="">-- Pilih Tersangka untuk Memuat Data Otomatis --</option>
                                        @foreach ($suspects as $s)
                                            <option value="{{ $s->id }}"
                                                data-name="{{ $s->name }}"
                                                data-nik="{{ $s->nik ?? '-' }}"
                                                data-gender="{{ $s->gender->name ?? ($s->gender_id == 1 ? 'Laki-laki' : 'Perempuan') }}"
                                                data-pob="{{ $s->birth_place ?? '-' }}"
                                                data-dob="{{ $s->birth_date ? \Carbon\Carbon::parse($s->birth_date)->locale('id')->translatedFormat('d F Y') : '-' }}"
                                                data-job="{{ $s->job->name ?? '-' }}"
                                                data-religion="{{ $s->religion->name ?? 'Islam' }}"
                                                data-address="{{ $s->address ?? '-' }}">
                                                {{ $s->name }} (NIK: {{ $s->nik ?? '-' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small mb-1" for="suspect_name">
                                    Nama Tersangka (Poin 2.a) <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="suspect_name" name="suspect_name"
                                    value="{{ old('suspect_name', $firstSuspect->name ?? '') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small mb-1" for="suspect_identity_number">
                                    Nomor Identitas / NIK (Poin 2.b)
                                </label>
                                <input type="text" class="form-control" id="suspect_identity_number" name="suspect_identity_number"
                                    value="{{ old('suspect_identity_number', $firstSuspect->nik ?? '') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="fw-bold small mb-1" for="suspect_nationality">
                                    Kewarganegaraan (Poin 2.c)
                                </label>
                                <input type="text" class="form-control" id="suspect_nationality" name="suspect_nationality"
                                    value="{{ old('suspect_nationality', 'Indonesia') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="fw-bold small mb-1" for="suspect_gender_name">
                                    Jenis Kelamin (Poin 2.d)
                                </label>
                                <input type="text" class="form-control" id="suspect_gender_name" name="suspect_gender_name"
                                    value="{{ old('suspect_gender_name', $firstSuspect && $firstSuspect->gender ? $firstSuspect->gender->name : ($firstSuspect && $firstSuspect->gender_id == 1 ? 'Laki-laki' : 'Perempuan')) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="fw-bold small mb-1" for="suspect_birth_place">
                                    Tempat Lahir (Poin 2.e)
                                </label>
                                <input type="text" class="form-control" id="suspect_birth_place" name="suspect_birth_place"
                                    value="{{ old('suspect_birth_place', $firstSuspect->birth_place ?? '') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="fw-bold small mb-1" for="suspect_birth_date">
                                    Tanggal Lahir (Poin 2.e)
                                </label>
                                <input type="text" class="form-control" id="suspect_birth_date" name="suspect_birth_date"
                                    value="{{ old('suspect_birth_date', $firstSuspect && $firstSuspect->birth_date ? \Carbon\Carbon::parse($firstSuspect->birth_date)->locale('id')->translatedFormat('d F Y') : '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small mb-1" for="suspect_job_name">
                                    Pekerjaan (Poin 2.f)
                                </label>
                                <input type="text" class="form-control" id="suspect_job_name" name="suspect_job_name"
                                    value="{{ old('suspect_job_name', $firstSuspect && $firstSuspect->job ? $firstSuspect->job->name : 'Swasta') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small mb-1" for="suspect_religion_name">
                                    Agama (Poin 2.g)
                                </label>
                                <input type="text" class="form-control" id="suspect_religion_name" name="suspect_religion_name"
                                    value="{{ old('suspect_religion_name', $firstSuspect && $firstSuspect->religion ? $firstSuspect->religion->name : 'Islam') }}">
                            </div>
                            <div class="col-12 mb-2">
                                <label class="fw-bold small mb-1" for="suspect_full_address">
                                    Alamat Lengkap (Poin 2.h)
                                </label>
                                <input type="text" class="form-control" id="suspect_full_address" name="suspect_full_address"
                                    value="{{ old('suspect_full_address', $firstSuspect->address ?? '') }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. PERKARA PIDANA, PASAL, TKP & WAKTU KEJADIAN --}}
                <div class="form-section-card">
                    <div class="form-section-header">
                        <span><i class="bi bi-shield-shaded me-2"></i>4. Perkara Pidana, TKP & Waktu Kejadian</span>
                        <span class="badge badge-light border text-muted">Pasal & Uraian Perkara</span>
                    </div>
                    <div class="form-section-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small mb-1" for="dugaan_tindak_pidana">
                                    Dugaan Tindak Pidana
                                </label>
                                <input type="text" class="form-control" id="dugaan_tindak_pidana" name="dugaan_tindak_pidana"
                                    value="{{ old('dugaan_tindak_pidana', $defaultTindakPidana) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small mb-1" for="pasal_diduga">
                                    Pasal yang Diduga
                                </label>
                                <input type="text" class="form-control" id="pasal_diduga" name="pasal_diduga"
                                    value="{{ old('pasal_diduga', $defaultPasal) }}" required>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="fw-bold small mb-1" for="uu_nomor">
                                    UU Nomor
                                </label>
                                <input type="text" class="form-control" id="uu_nomor" name="uu_nomor"
                                    value="{{ old('uu_nomor', $defaultUuNomor) }}" required>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="fw-bold small mb-1" for="uu_tahun">
                                    UU Tahun
                                </label>
                                <input type="text" class="form-control" id="uu_tahun" name="uu_tahun"
                                    value="{{ old('uu_tahun', $defaultUuTahun) }}" required>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="fw-bold small mb-1" for="uu_tentang">
                                    UU Tentang
                                </label>
                                <input type="text" class="form-control" id="uu_tentang" name="uu_tentang"
                                    value="{{ old('uu_tentang', $defaultUuTentang) }}" required>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="fw-bold small mb-1" for="tempat_kejadian">
                                    Tempat Kejadian (TKP)
                                </label>
                                <input type="text" class="form-control" id="tempat_kejadian" name="tempat_kejadian"
                                    value="{{ old('tempat_kejadian', $defaultTkp) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold small mb-1" for="waktu_kejadian">
                                    Waktu Kejadian
                                </label>
                                <input type="text" class="form-control" id="waktu_kejadian" name="waktu_kejadian"
                                    value="{{ old('waktu_kejadian', $defaultWaktuKejadian) }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="fw-bold small mb-1" for="kurun_waktu">
                                    Kurun Waktu (Opsional)
                                </label>
                                <input type="text" class="form-control" id="kurun_waktu" name="kurun_waktu"
                                    value="{{ old('kurun_waktu', '-') }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 5. JADWAL PEMERIKSAAN & PETUGAS NARAHUBUNG (POIN 3) --}}
                <div class="form-section-card">
                    <div class="form-section-header">
                        <span><i class="bi bi-calendar-check me-2"></i>5. Jadwal Pemeriksaan & Narahubung (Poin 3)</span>
                        <span class="badge badge-light border text-muted">Jadwal & Kontak Person</span>
                    </div>
                    <div class="form-section-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold small mb-1" for="examination_day">
                                    Hari Pemeriksaan
                                </label>
                                <input type="text" class="form-control" id="examination_day" name="examination_day"
                                    value="{{ old('examination_day', $defaultExamDay) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold small mb-1" for="examination_date">
                                    Tanggal Pemeriksaan
                                </label>
                                <input type="text" class="form-control" id="examination_date" name="examination_date"
                                    value="{{ old('examination_date', $defaultExamDate) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold small mb-1" for="examination_time">
                                    Pukul (WIB)
                                </label>
                                <input type="text" class="form-control" id="examination_time" name="examination_time"
                                    value="{{ old('examination_time', '09.00') }}" placeholder="Contoh: 09.00" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small mb-1" for="examination_office_name">
                                    Nama Kantor Pemeriksaan
                                </label>
                                <input type="text" class="form-control" id="examination_office_name" name="examination_office_name"
                                    value="{{ old('examination_office_name', $defaultExamOffice) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small mb-1" for="examination_office_address">
                                    Alamat Kantor Pemeriksaan
                                </label>
                                <input type="text" class="form-control" id="examination_office_address" name="examination_office_address"
                                    value="{{ old('examination_office_address', $defaultExamAddress) }}" required>
                            </div>
                        </div>

                        {{-- Petugas Narahubung --}}
                        <div class="p-3 bg-light rounded border mt-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-dark small"><i class="bi bi-telephone-outbound me-1"></i>Petugas Narahubung Koordinasi</span>
                                <div class="w-50">
                                    <select id="select_contact_officer" class="form-control form-control-sm select2">
                                        <option value="">-- Pilih Petugas Narahubung --</option>
                                        @foreach ($officers as $off)
                                            <option value="{{ $off->id }}"
                                                data-name="{{ $off->full_name }}"
                                                data-phone="{{ $off->phone_number ?? '' }}"
                                                data-role="{{ $off->position->name ?? 'Penyidik Pembantu' }}">
                                                {{ $off->full_name }} ({{ $off->rank->name ?? '-' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="contact_officer_id" id="contact_officer_id" value="{{ old('contact_officer_id') }}">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label class="small fw-bold mb-1" for="contact_officer_role">Sebutan Petugas</label>
                                    <input type="text" class="form-control form-control-sm" id="contact_officer_role" name="contact_officer_role"
                                        value="{{ old('contact_officer_role', 'Penyidik Pembantu') }}">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="small fw-bold mb-1" for="contact_officer_name">Nama Petugas</label>
                                    <input type="text" class="form-control form-control-sm" id="contact_officer_name" name="contact_officer_name"
                                        value="{{ old('contact_officer_name', '') }}" placeholder="Contoh: AHMAD SUBARI, S.H.">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="small fw-bold mb-1" for="contact_officer_phone">Nomor HP / WhatsApp</label>
                                    <input type="text" class="form-control form-control-sm" id="contact_officer_phone" name="contact_officer_phone"
                                        value="{{ old('contact_officer_phone', '') }}" placeholder="Contoh: 08123456789">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 6. TEMBUSAN & PEJABAT PENANDATANGAN --}}
                <div class="form-section-card">
                    <div class="form-section-header">
                        <span><i class="bi bi-pen me-2"></i>6. Tembusan & Pejabat Penandatangan</span>
                        <span class="badge badge-light border text-muted">Tembusan & Tanda Tangan</span>
                    </div>
                    <div class="form-section-body">
                        {{-- Tembusan --}}
                        <div class="mb-4">
                            <label class="fw-bold small text-dark mb-2"><i class="bi bi-card-checklist me-1"></i>Daftar Tembusan Surat:</label>
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label class="small fw-bold mb-1" for="carbon_copy_1">Tembusan 1</label>
                                    <input type="text" class="form-control form-control-sm" id="carbon_copy_1" name="carbon_copy_1"
                                        value="{{ old('carbon_copy_1', 'Kepala Kepolisian Resor ' . ($accident->polres->name ?? 'Pasuruan')) }}">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="small fw-bold mb-1" for="carbon_copy_2">Tembusan 2</label>
                                    <input type="text" class="form-control form-control-sm" id="carbon_copy_2" name="carbon_copy_2"
                                        value="{{ old('carbon_copy_2', 'Pengawas Penyidikan (Wasidik) Polres ' . ($accident->polres->name ?? 'Pasuruan')) }}">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="small fw-bold mb-1" for="carbon_copy_3">Tembusan 3</label>
                                    <input type="text" class="form-control form-control-sm" id="carbon_copy_3" name="carbon_copy_3"
                                        value="{{ old('carbon_copy_3', 'Arsip') }}">
                                </div>
                            </div>
                        </div>

                        {{-- Pejabat Penandatangan --}}
                        <div class="p-3 bg-light rounded border">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold text-dark small"><i class="bi bi-award me-1"></i>Pejabat Penandatangan (Kanan Bawah)</span>
                                <div class="w-50">
                                    <select id="select_signatory" class="form-control form-control-sm select2">
                                        <option value="">-- Pilih Pejabat Penandatangan --</option>
                                        @foreach ($authorizedSignatories as $sign)
                                            <option value="{{ $sign->id }}"
                                                data-name="{{ $sign->full_name }}"
                                                data-rank="{{ $sign->rank->name ?? '-' }}"
                                                data-nrp="{{ $sign->register_number ?? '-' }}"
                                                data-position="{{ $sign->position->name ?? 'KASAT LANTAS' }}">
                                                {{ $sign->full_name }} — NRP: {{ $sign->register_number ?? '-' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="signatory_id" id="signatory_id" value="{{ old('signatory_id') }}">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="small fw-bold mb-1" for="signatory_head_text">Header Tanda Tangan</label>
                                    <input type="text" class="form-control form-control-sm" id="signatory_head_text" name="signatory_head_text"
                                        value="{{ old('signatory_head_text', 'a.n. KEPALA KEPOLISIAN RESOR ' . strtoupper($accident->polres->name ?? 'PASURUAN')) }}">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="small fw-bold mb-1" for="signatory_position_name">Jabatan</label>
                                    <input type="text" class="form-control form-control-sm" id="signatory_position_name" name="signatory_position_name"
                                        value="{{ old('signatory_position_name', 'KASAT LANTAS') }}">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="small fw-bold mb-1" for="signatory_name">Nama Penandatangan</label>
                                    <input type="text" class="form-control form-control-sm" id="signatory_name" name="signatory_name"
                                        value="{{ old('signatory_name', '') }}" placeholder="Contoh: AGUS SETIAWAN, S.I.K., M.Si.">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="small fw-bold mb-1" for="signatory_rank_name">Pangkat</label>
                                    <input type="text" class="form-control form-control-sm" id="signatory_rank_name" name="signatory_rank_name"
                                        value="{{ old('signatory_rank_name', 'AJUN KOMISARIS POLISI') }}">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="small fw-bold mb-1" for="signatory_register_number">NRP</label>
                                    <input type="text" class="form-control form-control-sm" id="signatory_register_number" name="signatory_register_number"
                                        value="{{ old('signatory_register_number', '') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TOMBOL SUBMIT --}}
                <div class="text-end mb-4">
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-secondary px-4 py-2 me-2">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold" id="btnSubmit">
                        <i class="bi bi-save me-1"></i> Simpan Dokumen
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Auto-fill Tersangka
            $('#select_suspect').on('change', function() {
                var opt = $(this).find(':selected');
                if (opt.val()) {
                    $('#suspect_id').val(opt.val());
                    $('#suspect_name').val(opt.data('name') || '');
                    $('#suspect_identity_number').val(opt.data('nik') || '-');
                    $('#suspect_gender_name').val(opt.data('gender') || '-');
                    $('#suspect_birth_place').val(opt.data('pob') || '-');
                    $('#suspect_birth_date').val(opt.data('dob') || '-');
                    $('#suspect_job_name').val(opt.data('job') || '-');
                    $('#suspect_religion_name').val(opt.data('religion') || 'Islam');
                    $('#suspect_full_address').val(opt.data('address') || '-');
                    $('#surat_keterangan_tidak_mampu_person_name').val(opt.data('name') || '');
                }
            });

            // Auto-fill Narahubung
            $('#select_contact_officer').on('change', function() {
                var opt = $(this).find(':selected');
                if (opt.val()) {
                    $('#contact_officer_id').val(opt.val());
                    $('#contact_officer_name').val(opt.data('name') || '');
                    $('#contact_officer_phone').val(opt.data('phone') || '');
                    $('#contact_officer_role').val(opt.data('role') || 'Penyidik Pembantu');
                }
            });

            // Auto-fill Pejabat Penandatangan
            $('#select_signatory').on('change', function() {
                var opt = $(this).find(':selected');
                if (opt.val()) {
                    $('#signatory_id').val(opt.val());
                    $('#signatory_name').val(opt.data('name') || '');
                    $('#signatory_rank_name').val(opt.data('rank') || '');
                    $('#signatory_register_number').val(opt.data('nrp') || '');
                    $('#signatory_position_name').val(opt.data('position') || 'KASAT LANTAS');
                }
            });
        });
    </script>
@endpush

