@php
    $_title = 'Edit Surat Permintaan Buka Blokir Rekening Bank';
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
            <h5 class="fw-bold text-blue-dark">Edit Surat Permintaan Buka Blokir Rekening Bank</h5>
            <small class="text-muted d-block mb-3">Kode Dokumen: <b>DCT-0708</b></small>

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
            <form action="{{ route('doc.surat-permintaan-buka-blokir-rekening-bank-document.update', ['id' => $document->id]) }}"
                method="POST" id="formEditBukaBlokirBank" novalidate>
                @csrf
                @method('PUT')
                <input type="hidden" name="accident_id" id="accident_id" value="{{ $accidentId }}">
                <input type="hidden" name="suspect_id" id="suspect_id" value="{{ old('suspect_id', $document->suspect_id) }}">

                {{-- 1. METADATA & BANK TUJUAN --}}
                <div class="form-section-card">
                    <div class="form-section-header">
                        <span><i class="bi bi-file-earmark-text me-2"></i>1. Metadata Surat & Bank Tujuan</span>
                        <span class="badge badge-light border text-muted">Nomor, Tanggal & Pihak Dituju</span>
                    </div>
                    <div class="form-section-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="small fw-bold mb-1" for="document_number">Nomor Surat <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="document_number" name="document_number"
                                    value="{{ old('document_number', $document->document_number) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small fw-bold mb-1" for="document_classification_name">Klasifikasi Surat</label>
                                <input type="text" class="form-control form-control-sm" id="document_classification_name" name="document_classification_name"
                                    value="{{ old('document_classification_name', $document->document_classification_name) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small fw-bold mb-1" for="appendix">Lampiran</label>
                                <input type="text" class="form-control form-control-sm" id="appendix" name="appendix"
                                    value="{{ old('appendix', $document->appendix) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold mb-1" for="document_location">Tempat Dikeluarkan</label>
                                <input type="text" class="form-control form-control-sm" id="document_location" name="document_location"
                                    value="{{ old('document_location', $document->document_location) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold mb-1" for="document_date">Tanggal Surat</label>
                                <input type="text" class="form-control form-control-sm" id="document_date" name="document_date"
                                    value="{{ old('document_date', $document->document_date) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold mb-1" for="target_bank_name">Tujuan Bank (Kepada Yth.) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="target_bank_name" name="target_bank_name"
                                    value="{{ old('target_bank_name', $document->target_bank_name) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold mb-1" for="target_bank_location">Kota Lokasi Bank (di)</label>
                                <input type="text" class="form-control form-control-sm" id="target_bank_location" name="target_bank_location"
                                    value="{{ old('target_bank_location', $document->target_bank_location) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. RUJUKAN DASAR HUKUM & ADMINISTRASI PENYIDIKAN --}}
                <div class="form-section-card">
                    <div class="form-section-header">
                        <span><i class="bi bi-journal-bookmark me-2"></i>2. Dasar Rujukan Hukum & Administrasi Penyidikan (Poin 1)</span>
                        <span class="badge badge-light border text-muted">Rujukan Angka 1</span>
                    </div>
                    <div class="form-section-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="small fw-bold mb-1" for="dasar_kuhap">Dasar KUHAP (Butir 1.a)</label>
                                <textarea class="form-control form-control-sm" id="dasar_kuhap" name="dasar_kuhap" rows="2">{{ old('dasar_kuhap', $document->dasar_kuhap) }}</textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="small fw-bold mb-1" for="undang_undang_lain">Undang-Undang Terkait (Butir 1.f)</label>
                                <input type="text" class="form-control form-control-sm" id="undang_undang_lain" name="undang_undang_lain"
                                    value="{{ old('undang_undang_lain', $document->undang_undang_lain) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold mb-1" for="accident_number">Nomor Laporan Polisi (LP) (Butir 1.g)</label>
                                <input type="text" class="form-control form-control-sm" id="accident_number" name="accident_number"
                                    value="{{ old('accident_number', $document->accident_number) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold mb-1" for="accident_date">Tanggal Laporan Polisi (LP)</label>
                                <input type="text" class="form-control form-control-sm" id="accident_date" name="accident_date"
                                    value="{{ old('accident_date', $document->accident_date) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold mb-1" for="surat_perintah_penyidikan_document_number">Nomor Surat Perintah Penyidikan (Butir 1.h)</label>
                                <input type="text" class="form-control form-control-sm" id="surat_perintah_penyidikan_document_number" name="surat_perintah_penyidikan_document_number"
                                    value="{{ old('surat_perintah_penyidikan_document_number', $document->surat_perintah_penyidikan_document_number) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold mb-1" for="surat_perintah_penyidikan_document_date">Tanggal Surat Perintah Penyidikan</label>
                                <input type="text" class="form-control form-control-sm" id="surat_perintah_penyidikan_document_date" name="surat_perintah_penyidikan_document_date"
                                    value="{{ old('surat_perintah_penyidikan_document_date', $document->surat_perintah_penyidikan_document_date) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold mb-1" for="surat_pemblokiran_nomor">Nomor Surat Pemblokiran Harta Kekayaan (Butir 1.i)</label>
                                <input type="text" class="form-control form-control-sm" id="surat_pemblokiran_nomor" name="surat_pemblokiran_nomor"
                                    value="{{ old('surat_pemblokiran_nomor', $document->surat_pemblokiran_nomor) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold mb-1" for="surat_pemblokiran_tanggal">Tanggal Surat Pemblokiran Harta Kekayaan</label>
                                <input type="text" class="form-control form-control-sm" id="surat_pemblokiran_tanggal" name="surat_pemblokiran_tanggal"
                                    value="{{ old('surat_pemblokiran_tanggal', $document->surat_pemblokiran_tanggal) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small fw-bold mb-1" for="nama_pengadilan_negeri">Nama Pengadilan Negeri (Butir 1.j)</label>
                                <input type="text" class="form-control form-control-sm" id="nama_pengadilan_negeri" name="nama_pengadilan_negeri"
                                    value="{{ old('nama_pengadilan_negeri', $document->nama_pengadilan_negeri) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small fw-bold mb-1" for="penetapan_court_number">Nomor Penetapan Pengadilan</label>
                                <input type="text" class="form-control form-control-sm" id="penetapan_court_number" name="penetapan_court_number"
                                    value="{{ old('penetapan_court_number', $document->penetapan_court_number) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small fw-bold mb-1" for="penetapan_court_date">Tanggal Penetapan Pengadilan</label>
                                <input type="text" class="form-control form-control-sm" id="penetapan_court_date" name="penetapan_court_date"
                                    value="{{ old('penetapan_court_date', $document->penetapan_court_date) }}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="small fw-bold mb-1" for="surat_perintah_kepala_instansi">Kepala Instansi Penerbit Perintah (Butir 1.k)</label>
                                <input type="text" class="form-control form-control-sm" id="surat_perintah_kepala_instansi" name="surat_perintah_kepala_instansi"
                                    value="{{ old('surat_perintah_kepala_instansi', $document->surat_perintah_kepala_instansi) }}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="small fw-bold mb-1" for="surat_perintah_kepala_nomor">Nomor Surat Perintah Pemblokiran</label>
                                <input type="text" class="form-control form-control-sm" id="surat_perintah_kepala_nomor" name="surat_perintah_kepala_nomor"
                                    value="{{ old('surat_perintah_kepala_nomor', $document->surat_perintah_kepala_nomor) }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. INFORMASI PERKARA & PIHAK BANK --}}
                <div class="form-section-card">
                    <div class="form-section-header">
                        <span><i class="bi bi-briefcase me-2"></i>3. Informasi Perkara & Pihak Bank (Poin 2)</span>
                        <span class="badge badge-light border text-muted">Uraian Perkara & Pasal</span>
                    </div>
                    <div class="form-section-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold mb-1" for="target_bank_leader_title">Sebutan Pimpinan Bank</label>
                                <input type="text" class="form-control form-control-sm" id="target_bank_leader_title" name="target_bank_leader_title"
                                    value="{{ old('target_bank_leader_title', $document->target_bank_leader_title) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold mb-1" for="satker_penyidik">Satuan Kerja Penyidik</label>
                                <input type="text" class="form-control form-control-sm" id="satker_penyidik" name="satker_penyidik"
                                    value="{{ old('satker_penyidik', $document->satker_penyidik) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold mb-1" for="dugaan_tindak_pidana">Dugaan Tindak Pidana</label>
                                <input type="text" class="form-control form-control-sm" id="dugaan_tindak_pidana" name="dugaan_tindak_pidana"
                                    value="{{ old('dugaan_tindak_pidana', $document->dugaan_tindak_pidana) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold mb-1" for="pasal_diduga">Pasal yang Disangkakan</label>
                                <input type="text" class="form-control form-control-sm" id="pasal_diduga" name="pasal_diduga"
                                    value="{{ old('pasal_diduga', $document->pasal_diduga) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small fw-bold mb-1" for="uu_nomor">Nomor UU</label>
                                <input type="text" class="form-control form-control-sm" id="uu_nomor" name="uu_nomor"
                                    value="{{ old('uu_nomor', $document->uu_nomor) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small fw-bold mb-1" for="uu_tahun">Tahun UU</label>
                                <input type="text" class="form-control form-control-sm" id="uu_tahun" name="uu_tahun"
                                    value="{{ old('uu_tahun', $document->uu_tahun) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small fw-bold mb-1" for="uu_tentang">Tentang</label>
                                <input type="text" class="form-control form-control-sm" id="uu_tentang" name="uu_tentang"
                                    value="{{ old('uu_tentang', $document->uu_tentang) }}" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="small fw-bold mb-1" for="tempat_kejadian">Tempat Kejadian Perkara (TKP)</label>
                                <input type="text" class="form-control form-control-sm" id="tempat_kejadian" name="tempat_kejadian"
                                    value="{{ old('tempat_kejadian', $document->tempat_kejadian) }}" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="small fw-bold mb-1" for="waktu_kejadian">Waktu Kejadian</label>
                                <input type="text" class="form-control form-control-sm" id="waktu_kejadian" name="waktu_kejadian"
                                    value="{{ old('waktu_kejadian', $document->waktu_kejadian) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. IDENTITAS TERSANGKA --}}
                <div class="form-section-card">
                    <div class="form-section-header">
                        <span><i class="bi bi-person-badge me-2"></i>4. Identitas Tersangka (Poin 2.a - 2.h)</span>
                        <span class="badge badge-light border text-muted">Data Pemilik Rekening / Tersangka</span>
                    </div>
                    <div class="form-section-body">
                        @if ($suspects->count() > 0)
                            <div class="mb-3 p-2 bg-light rounded border">
                                <label class="small fw-bold text-primary mb-1">
                                    <i class="bi bi-magic me-1"></i> Ganti Data dari Database Tersangka:
                                </label>
                                <select class="form-control form-control-sm select2" id="select_suspect">
                                    <option value="">-- Pilih Tersangka --</option>
                                    @foreach ($suspects as $s)
                                        @php
                                            $sName = $s->name ?? '-';
                                            $sNik = $s->nik ?? '-';
                                            $sGender = ($s->gender_id == 1) ? 'LAKI-LAKI' : (($s->gender_id == 2) ? 'PEREMPUAN' : ($s->gender->name ?? '-'));
                                            $sPob = $s->birth_place ?? '-';
                                            $sDob = $s->birth_date ? \Carbon\Carbon::parse($s->birth_date)->locale('id')->translatedFormat('d F Y') : '-';
                                            $sJob = $s->job->name ?? ($s->job_name ?? '-');
                                            $sReligion = $s->religion->name ?? ($s->religion_name ?? 'Islam');
                                            $sAddress = $s->full_address ?? ($s->address ?? '-');
                                        @endphp
                                        <option value="{{ $s->id }}"
                                            data-name="{{ $sName }}"
                                            data-nik="{{ $sNik }}"
                                            data-gender="{{ $sGender }}"
                                            data-pob="{{ $sPob }}"
                                            data-dob="{{ $sDob }}"
                                            data-job="{{ $sJob }}"
                                            data-religion="{{ $sReligion }}"
                                            data-address="{{ $sAddress }}"
                                            {{ (old('suspect_id', $document->suspect_id) == $s->id) ? 'selected' : '' }}>
                                            {{ $sName }} (NIK: {{ $sNik }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold mb-1" for="suspect_name">Nama Tersangka <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="suspect_name" name="suspect_name"
                                    value="{{ old('suspect_name', $document->suspect_name) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold mb-1" for="suspect_identity_number">Nomor Identitas (NIK/No KTP)</label>
                                <input type="text" class="form-control form-control-sm" id="suspect_identity_number" name="suspect_identity_number"
                                    value="{{ old('suspect_identity_number', $document->suspect_identity_number) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small fw-bold mb-1" for="suspect_nationality">Kewarganegaraan</label>
                                <input type="text" class="form-control form-control-sm" id="suspect_nationality" name="suspect_nationality"
                                    value="{{ old('suspect_nationality', $document->suspect_nationality) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small fw-bold mb-1" for="suspect_gender_name">Jenis Kelamin</label>
                                <input type="text" class="form-control form-control-sm" id="suspect_gender_name" name="suspect_gender_name"
                                    value="{{ old('suspect_gender_name', $document->suspect_gender_name) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small fw-bold mb-1" for="suspect_religion_name">Agama</label>
                                <input type="text" class="form-control form-control-sm" id="suspect_religion_name" name="suspect_religion_name"
                                    value="{{ old('suspect_religion_name', $document->suspect_religion_name) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small fw-bold mb-1" for="suspect_birth_place">Tempat Lahir</label>
                                <input type="text" class="form-control form-control-sm" id="suspect_birth_place" name="suspect_birth_place"
                                    value="{{ old('suspect_birth_place', $document->suspect_birth_place) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small fw-bold mb-1" for="suspect_birth_date">Tanggal Lahir</label>
                                <input type="text" class="form-control form-control-sm" id="suspect_birth_date" name="suspect_birth_date"
                                    value="{{ old('suspect_birth_date', $document->suspect_birth_date) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small fw-bold mb-1" for="suspect_job_name">Pekerjaan</label>
                                <input type="text" class="form-control form-control-sm" id="suspect_job_name" name="suspect_job_name"
                                    value="{{ old('suspect_job_name', $document->suspect_job_name) }}">
                            </div>
                            <div class="col-12 mb-2">
                                <label class="small fw-bold mb-1" for="suspect_full_address">Alamat Tempat Tinggal</label>
                                <textarea class="form-control form-control-sm" id="suspect_full_address" name="suspect_full_address" rows="2">{{ old('suspect_full_address', $document->suspect_full_address) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 5. REKENING BUKA BLOKIR & REKENING PENAMPUNGAN SITAAN --}}
                <div class="form-section-card">
                    <div class="form-section-header">
                        <span><i class="bi bi-credit-card me-2"></i>5. Rekening yang Dibuka Blokir & Rekening Penampungan Sitaan (Poin 3)</span>
                        <span class="badge badge-light border text-muted">Data Rekening Bank & Nilai Sita</span>
                    </div>
                    <div class="form-section-body">
                        <h6 class="fw-bold text-secondary mb-2"><i class="bi bi-unlock me-1"></i> A. Rekening yang Dimohon Buka Blokir:</h6>
                        <div class="row mb-3">
                            <div class="col-md-4 mb-2">
                                <label class="small fw-bold mb-1" for="account_number_1">Nomor Rekening 1 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="account_number_1" name="account_number_1"
                                    value="{{ old('account_number_1', $document->account_number_1) }}" required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="small fw-bold mb-1" for="account_holder_name_1">Atas Nama Rekening 1</label>
                                <input type="text" class="form-control form-control-sm" id="account_holder_name_1" name="account_holder_name_1"
                                    value="{{ old('account_holder_name_1', $document->account_holder_name_1) }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="small fw-bold mb-1" for="account_owner_type_1">Status Pemilik Rekening 1</label>
                                <input type="text" class="form-control form-control-sm" id="account_owner_type_1" name="account_owner_type_1"
                                    value="{{ old('account_owner_type_1', $document->account_owner_type_1) }}">
                            </div>
                            <div class="col-12 mb-2">
                                <label class="small fw-bold mb-1" for="account_number_2_line">Baris Rekening Tambahan (Butir 3.b)</label>
                                <input type="text" class="form-control form-control-sm" id="account_number_2_line" name="account_number_2_line"
                                    value="{{ old('account_number_2_line', $document->account_number_2_line) }}">
                            </div>
                        </div>

                        <hr class="my-3">
                        <h6 class="fw-bold text-secondary mb-2"><i class="bi bi-box-arrow-in-down-right me-1"></i> B. Rincian Pemindahan Sitaan ke Rekening Penampungan:</h6>
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="small fw-bold mb-1" for="seizure_amount">Nominal Uang yang Disita (Rp)</label>
                                <input type="text" class="form-control form-control-sm" id="seizure_amount" name="seizure_amount"
                                    value="{{ old('seizure_amount', $document->seizure_amount) }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="small fw-bold mb-1" for="escrow_bank_name">Bank Penampungan Sitaan</label>
                                <input type="text" class="form-control form-control-sm" id="escrow_bank_name" name="escrow_bank_name"
                                    value="{{ old('escrow_bank_name', $document->escrow_bank_name) }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="small fw-bold mb-1" for="escrow_account_number">Nomor Rekening Penampungan</label>
                                <input type="text" class="form-control form-control-sm" id="escrow_account_number" name="escrow_account_number"
                                    value="{{ old('escrow_account_number', $document->escrow_account_number) }}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="small fw-bold mb-1" for="escrow_account_holder_name">Atas Nama Rekening Penampungan</label>
                                <input type="text" class="form-control form-control-sm" id="escrow_account_holder_name" name="escrow_account_holder_name"
                                    value="{{ old('escrow_account_holder_name', $document->escrow_account_holder_name) }}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="small fw-bold mb-1" for="escrow_account_owner_type">Keterangan / Status Rekening Penampungan</label>
                                <input type="text" class="form-control form-control-sm" id="escrow_account_owner_type" name="escrow_account_owner_type"
                                    value="{{ old('escrow_account_owner_type', $document->escrow_account_owner_type) }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 6. NARAHUBUNG, TEMBUSAN & PENANDATANGAN --}}
                <div class="form-section-card">
                    <div class="form-section-header">
                        <span><i class="bi bi-person-lines-fill me-2"></i>6. Narahubung, Tembusan & Pejabat Penandatangan</span>
                        <span class="badge badge-light border text-muted">Poin 4, Tembusan & Tanda Tangan</span>
                    </div>
                    <div class="form-section-body">
                        {{-- NARAHUBUNG --}}
                        <h6 class="fw-bold text-secondary mb-2"><i class="bi bi-telephone me-1"></i> Narahubung / Petugas Koordinasi (Poin 4):</h6>
                        @if ($officers->count() > 0)
                            <div class="mb-2 p-2 bg-light rounded border">
                                <label class="small fw-bold text-primary mb-1">Ganti Narahubung dari Personel:</label>
                                <select class="form-control form-control-sm select2" id="select_contact_officer">
                                    <option value="">-- Pilih Petugas Narahubung --</option>
                                    @foreach ($officers as $off)
                                        @php
                                            $oName = \App\Helpers\PeopleNameHelper::getFullName($off->first_title, $off->first_name, $off->last_name, $off->last_title);
                                            $oPhone = $off->phone_number ?? '';
                                            $oRole = 'Penyidik Pembantu';
                                        @endphp
                                        <option value="{{ $off->id }}"
                                            data-name="{{ $oName }}"
                                            data-phone="{{ $oPhone }}"
                                            data-role="{{ $oRole }}"
                                            {{ (old('contact_officer_id', $document->contact_officer_id) == $off->id) ? 'selected' : '' }}>
                                            {{ $oName }} (NRP: {{ $off->register_number ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <input type="hidden" name="contact_officer_id" id="contact_officer_id" value="{{ old('contact_officer_id', $document->contact_officer_id) }}">
                        <div class="row mb-3">
                            <div class="col-md-4 mb-2">
                                <label class="small fw-bold mb-1" for="contact_officer_role">Sebutan Penyidik</label>
                                <input type="text" class="form-control form-control-sm" id="contact_officer_role" name="contact_officer_role"
                                    value="{{ old('contact_officer_role', $document->contact_officer_role) }}" required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="small fw-bold mb-1" for="contact_officer_name">Nama Narahubung</label>
                                <input type="text" class="form-control form-control-sm" id="contact_officer_name" name="contact_officer_name"
                                    value="{{ old('contact_officer_name', $document->contact_officer_name) }}" required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="small fw-bold mb-1" for="contact_officer_phone">Nomor Telepon / HP</label>
                                <input type="text" class="form-control form-control-sm" id="contact_officer_phone" name="contact_officer_phone"
                                    value="{{ old('contact_officer_phone', $document->contact_officer_phone) }}" required>
                            </div>
                        </div>

                        {{-- TEMBUSAN --}}
                        <hr class="my-3">
                        <h6 class="fw-bold text-secondary mb-2"><i class="bi bi-card-checklist me-1"></i> Tembusan Surat:</h6>
                        <div class="alert alert-light border py-1 px-3 mb-2 small text-muted">
                            1. Kapolri.<br>
                            2. (Diisi di bawah)<br>
                            3. Ketua Dewan Komisioner OJK.<br>
                            4. Kepala PPATK.
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-2">
                                <label class="small fw-bold mb-1" for="carbon_copy_2">Tembusan Butir 2</label>
                                <input type="text" class="form-control form-control-sm" id="carbon_copy_2" name="carbon_copy_2"
                                    value="{{ old('carbon_copy_2', $document->carbon_copy_2) }}">
                            </div>
                        </div>

                        {{-- PENANDATANGAN --}}
                        <hr class="my-3">
                        <h6 class="fw-bold text-secondary mb-2"><i class="bi bi-pen me-1"></i> Pejabat Penandatangan Dokumen:</h6>
                        @if ($authorizedSignatories->count() > 0)
                            <div class="mb-2 p-2 bg-light rounded border">
                                <label class="small fw-bold text-primary mb-1">Ganti Pejabat Penandatangan:</label>
                                <select class="form-control form-control-sm select2" id="select_signatory">
                                    <option value="">-- Pilih Pejabat Penandatangan --</option>
                                    @foreach ($authorizedSignatories as $sign)
                                        @php
                                            $sgName = \App\Helpers\PeopleNameHelper::getFullName($sign->first_title, $sign->first_name, $sign->last_name, $sign->last_title);
                                            $sgRank = $sign->rank->name ?? ($sign->rank_id ?? '-');
                                            $sgNrp = $sign->register_number ?? '-';
                                            $sgPos = $sign->position->name ?? ($sign->position_id ?? 'KASAT LANTAS');
                                        @endphp
                                        <option value="{{ $sign->id }}"
                                            data-name="{{ $sgName }}"
                                            data-rank="{{ $sgRank }}"
                                            data-nrp="{{ $sgNrp }}"
                                            data-position="{{ $sgPos }}"
                                            {{ (old('signatory_id', $document->signatory_id) == $sign->id) ? 'selected' : '' }}>
                                            {{ $sgName }} ({{ $sgRank }} - NRP: {{ $sgNrp }}) - {{ $sgPos }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <input type="hidden" name="signatory_id" id="signatory_id" value="{{ old('signatory_id', $document->signatory_id) }}">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="small fw-bold mb-1" for="signatory_head_text">Header Tanda Tangan</label>
                                <input type="text" class="form-control form-control-sm" id="signatory_head_text" name="signatory_head_text"
                                    value="{{ old('signatory_head_text', $document->signatory_head_text) }}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="small fw-bold mb-1" for="signatory_position_name">Jabatan</label>
                                <input type="text" class="form-control form-control-sm" id="signatory_position_name" name="signatory_position_name"
                                    value="{{ old('signatory_position_name', $document->signatory_position_name) }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="small fw-bold mb-1" for="signatory_name">Nama Penandatangan</label>
                                <input type="text" class="form-control form-control-sm" id="signatory_name" name="signatory_name"
                                    value="{{ old('signatory_name', $document->signatory_name) }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="small fw-bold mb-1" for="signatory_rank_name">Pangkat</label>
                                <input type="text" class="form-control form-control-sm" id="signatory_rank_name" name="signatory_rank_name"
                                    value="{{ old('signatory_rank_name', $document->signatory_rank_name) }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="small fw-bold mb-1" for="signatory_register_number">NRP</label>
                                <input type="text" class="form-control form-control-sm" id="signatory_register_number" name="signatory_register_number"
                                    value="{{ old('signatory_register_number', $document->signatory_register_number) }}">
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
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
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

