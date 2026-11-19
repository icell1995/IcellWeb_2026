@php
    $_title = 'Edit Berita Acara Serah Terima Tersangka Dan Barang Bukti';
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
            <h5 class="fw-bold text-blue-dark">Edit Berita Acara Serah Terima Tersangka Dan Barang Bukti</h5>
            <small class="text-muted d-block mb-3">Kode Dokumen: <b>DCT-0602</b></small>

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
            <form action="{{ route('doc.berita-acara-serah-terima-tersangka-dan-barang-bukti-document.update', ['id' => $document->id, 'accident_id' => $accidentId]) }}"
                method="POST" id="formBaSerahTerima" novalidate>
                @csrf
                <input type="hidden" name="accident_id" id="accident_id" value="{{ $accidentId }}">
                <input type="hidden" name="deliverer_officer_id" id="deliverer_officer_id" value="{{ old('deliverer_officer_id', $selectedDelivererId) }}">

                {{-- 1. WAKTU & TEMPAT PEMBUKAAN BERITA ACARA --}}
                <div class="form-section-card">
                    <div class="form-section-header">
                        <span><i class="bi bi-calendar-event me-2"></i>1. Waktu & Tempat Pembukaan Berita Acara</span>
                        <span class="badge badge-light border text-muted">Paragraf Pembuka</span>
                    </div>
                    <div class="form-section-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="fw-bold small mb-1" for="document_day_name">
                                    Hari
                                </label>
                                <input type="text" class="form-control" id="document_day_name" name="document_day_name"
                                    value="{{ old('document_day_name', $document->document_day_name ?? $documentDayName) }}" required>
                                <div class="field-hint">Contoh: Senin, Selasa, dst.</div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="fw-bold small mb-1" for="document_day_date">
                                    Tanggal (Angka)
                                </label>
                                <input type="text" class="form-control" id="document_day_date" name="document_day_date"
                                    value="{{ old('document_day_date', $document->document_day_date ?? $documentDayDate) }}" required>
                                <div class="field-hint">Contoh: 05</div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="fw-bold small mb-1" for="document_month">
                                    Bulan
                                </label>
                                <input type="text" class="form-control" id="document_month" name="document_month"
                                    value="{{ old('document_month', $document->document_month ?? $documentMonth) }}" required>
                                <div class="field-hint">Contoh: November</div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="fw-bold small mb-1" for="document_year">
                                    Tahun
                                </label>
                                <input type="text" class="form-control" id="document_year" name="document_year"
                                    value="{{ old('document_year', $document->document_year ?? $documentYear) }}" required>
                                <div class="field-hint">Contoh: 2026</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small mb-1" for="document_date_text">
                                    Tanggal Terbilang
                                </label>
                                <input type="text" class="form-control" id="document_date_text" name="document_date_text"
                                    value="{{ old('document_date_text', $document->document_date_text ?? $documentDateText) }}" required>
                                <div class="field-hint">Contoh: lima bulan November tahun dua ribu dua puluh enam</div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="fw-bold small mb-1" for="document_time">
                                    Pukul (WIB)
                                </label>
                                <input type="text" class="form-control" id="document_time" name="document_time"
                                    value="{{ old('document_time', $document->document_time ?? $documentTime) }}" required>
                                <div class="field-hint">Waktu pembuatan dokumen</div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="fw-bold small mb-1" for="document_location">
                                    Tempat / Kota
                                </label>
                                <input type="text" class="form-control" id="document_location" name="document_location"
                                    value="{{ old('document_location', $document->document_location ?? $documentLocation) }}" required>
                                <div class="field-hint">Tempat penutupan dokumen</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. PIHAK PERTAMA (PENYIDIK YANG MENYERAHKAN) --}}
                <div class="form-section-card">
                    <div class="form-section-header">
                        <span><i class="bi bi-person-badge me-2"></i>2. Pihak Pertama (Penyidik / Yang Menyerahkan)</span>
                        <span class="badge badge-light border text-muted">Penyidik Penyerah</span>
                    </div>
                    <div class="form-section-body">
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="fw-bold small text-primary mb-1">
                                    <i class="bi bi-lightning-charge me-1"></i>Pilih Cepat dari Personil Penyidik:
                                </label>
                                <select id="select_deliverer" class="form-control select2">
                                    <option value="">-- Pilih Anggota Penyidik untuk Otomatisasi Input --</option>
                                    @foreach ($officers as $off)
                                        <option value="{{ $off->id }}"
                                            {{ $selectedDelivererId == $off->id ? 'selected' : '' }}
                                            data-name="{{ $off->full_name }}"
                                            data-rank="{{ $off->rank->name ?? '-' }}"
                                            data-nrp="{{ $off->register_number ?? '-' }}"
                                            data-position="{{ $off->position->name ?? 'Penyidik Pembantu' }}">
                                            {{ $off->full_name }} — {{ $off->rank->name ?? '-' }} (NRP: {{ $off->register_number ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small mb-1" for="investigator_name">
                                    Nama Penyidik <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="investigator_name" name="investigator_name"
                                    value="{{ old('investigator_name', $document->investigator_name ?? $investigatorName) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small mb-1" for="investigator_rank_name">
                                    Pangkat
                                </label>
                                <input type="text" class="form-control" id="investigator_rank_name" name="investigator_rank_name"
                                    value="{{ old('investigator_rank_name', $document->investigator_rank_name ?? $investigatorRankName) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold small mb-1" for="investigator_register_number">
                                    NRP
                                </label>
                                <input type="text" class="form-control" id="investigator_register_number" name="investigator_register_number"
                                    value="{{ old('investigator_register_number', $document->investigator_register_number ?? $investigatorRegisterNumber) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold small mb-1" for="investigator_position_name">
                                    Jabatan
                                </label>
                                <input type="text" class="form-control" id="investigator_position_name" name="investigator_position_name"
                                    value="{{ old('investigator_position_name', $document->investigator_position_name ?? $investigatorPositionName) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold small mb-1" for="investigator_role">
                                    Sebutan Tanda Tangan
                                </label>
                                <input type="text" class="form-control" id="investigator_role" name="investigator_role"
                                    value="{{ old('investigator_role', $document->investigator_role ?? $investigatorRole) }}">
                                <div class="field-hint">Ditampilkan di atas TTD Yang Menyerahkan</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. ANGGOTA PENDAMPING (BERSAMA-SAMA DENGAN) --}}
                <div class="form-section-card">
                    <div class="form-section-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-people me-2"></i>3. Anggota Pendamping (Bersama-sama Dengan)</span>
                        <div class="d-flex align-items-center">
                            <button type="button" class="btn btn-sm btn-primary me-2" id="btn-add-accompanying" onclick="addAccompanyingRow()">
                                <i class="bi bi-plus-circle me-1"></i>Tambah Anggota Pendamping
                            </button>
                            <span class="badge badge-light border text-muted">Petugas Pendamping</span>
                        </div>
                    </div>
                    <div class="form-section-body">
                        {{-- Hidden template for officer select options --}}
                        <template id="officer-options-tpl">
                            @foreach ($officers as $off)
                                <option value="{{ $off->id }}"
                                    data-name="{{ $off->full_name }}"
                                    data-rank="{{ $off->rank->name ?? '-' }}"
                                    data-nrp="{{ $off->register_number ?? '-' }}"
                                    data-position="{{ $off->position->name ?? 'Penyidik Pembantu' }}">
                                    {{ $off->full_name }} ({{ $off->rank->name ?? '-' }})
                                </option>
                            @endforeach
                        </template>

                        @php
                            $initAccompanying = old('accompanying_officers', $accompanyingOfficerList ?? [
                                ['name' => $document->officer1_name ?? $officer1Name ?? '-', 'rank_name' => $document->officer1_rank_name ?? $officer1RankName ?? '-', 'register_number' => $document->officer1_register_number ?? $officer1RegisterNumber ?? '-', 'position_name' => $document->officer1_position_name ?? $officer1PositionName ?? '-']
                            ]);
                            if (empty($initAccompanying)) {
                                $initAccompanying = [
                                    ['name' => '-', 'rank_name' => '-', 'register_number' => '-', 'position_name' => '-']
                                ];
                            }
                        @endphp
                        <div id="accompanying-container">
                            @foreach ($initAccompanying as $idx => $acc)
                                <div class="accompanying-card p-3 mb-3 bg-light rounded border" data-index="{{ $idx }}">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold text-dark small accompanying-title">
                                            <i class="bi bi-person me-1"></i>Petugas Pendamping <span class="accompanying-num">{{ $idx + 1 }}</span>
                                        </span>
                                        <div class="d-flex align-items-center" style="min-width: 50%;">
                                            <div class="flex-grow-1 me-2">
                                                <select class="form-control form-control-sm select2 select-accompanying-officer" onchange="pickAccompanyingOfficer(this)">
                                                    <option value="">-- Pilih Pendamping {{ $idx + 1 }} --</option>
                                                    @foreach ($officers as $off)
                                                        <option value="{{ $off->id }}"
                                                            data-name="{{ $off->full_name }}"
                                                            data-rank="{{ $off->rank->name ?? '-' }}"
                                                            data-nrp="{{ $off->register_number ?? '-' }}"
                                                            data-position="{{ $off->position->name ?? 'Penyidik Pembantu' }}">
                                                            {{ $off->full_name }} ({{ $off->rank->name ?? '-' }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-accompanying" onclick="removeAccompanyingRow(this)" style="{{ $loop->first ? 'display: none;' : '' }}">
                                                <i class="bi bi-trash me-1"></i>Hapus
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3 mb-2">
                                            <label class="small fw-bold mb-1">Nama</label>
                                            <input type="text" class="form-control form-control-sm input-accompanying-name" name="accompanying_officers[{{ $idx }}][name]"
                                                value="{{ $acc['name'] ?? '-' }}">
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="small fw-bold mb-1">Pangkat</label>
                                            <input type="text" class="form-control form-control-sm input-accompanying-rank" name="accompanying_officers[{{ $idx }}][rank_name]"
                                                value="{{ $acc['rank_name'] ?? '-' }}">
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="small fw-bold mb-1">NRP</label>
                                            <input type="text" class="form-control form-control-sm input-accompanying-nrp" name="accompanying_officers[{{ $idx }}][register_number]"
                                                value="{{ $acc['register_number'] ?? '-' }}">
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="small fw-bold mb-1">Jabatan</label>
                                            <input type="text" class="form-control form-control-sm input-accompanying-pos" name="accompanying_officers[{{ $idx }}][position_name]"
                                                value="{{ $acc['position_name'] ?? '-' }}">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- 4. DASAR RUJUKAN SERAH TERIMA --}}
                <div class="form-section-card">
                    <div class="form-section-header">
                        <span><i class="bi bi-file-earmark-check me-2"></i>4. Dasar Rujukan Serah Terima</span>
                        <span class="badge badge-light border text-muted">P.21 & Surat Pengantar</span>
                    </div>
                    <div class="form-section-body">
                        {{-- Rujukan 1: Surat P-21 --}}
                        <div class="p-3 mb-3 bg-light rounded border">
                            <span class="fw-bold small text-dark d-block mb-2">
                                1. Surat Pemberitahuan Berkas Perkara Lengkap (P.21) dari Kejaksaan
                            </span>
                            <div class="row">
                                <div class="col-md-5 mb-2">
                                    <label class="small fw-bold mb-1" for="prosecutor_institution_name">
                                        Instansi Kejaksaan
                                    </label>
                                    <input type="text" class="form-control form-control-sm" id="prosecutor_institution_name" name="prosecutor_institution_name"
                                        value="{{ old('prosecutor_institution_name', $document->prosecutor_institution_name ?? $prosecutorInstitutionName) }}">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="small fw-bold mb-1" for="p21_document_number">
                                        Nomor Surat P.21
                                    </label>
                                    <input type="text" class="form-control form-control-sm" id="p21_document_number" name="p21_document_number"
                                        value="{{ old('p21_document_number', $document->p21_document_number ?? $p21DocumentNumber) }}">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="small fw-bold mb-1" for="p21_document_date">
                                        Tanggal P.21
                                    </label>
                                    <input type="text" class="form-control form-control-sm" id="p21_document_date" name="p21_document_date"
                                        value="{{ old('p21_document_date', $document->p21_document_date ?? $p21DocumentDate) }}">
                                </div>
                            </div>
                        </div>

                        {{-- Rujukan 2: Surat Pengantar Penyerahan --}}
                        <div class="p-3 bg-light rounded border">
                            <span class="fw-bold small text-dark d-block mb-2">
                                2. Surat Pengiriman Tersangka dan Barang Bukti dari Satuan Kepolisian
                            </span>
                            <div class="row">
                                <div class="col-md-5 mb-2">
                                    <label class="small fw-bold mb-1" for="deliverer_head_title">
                                        Kepala Satuan Pengirim
                                    </label>
                                    <input type="text" class="form-control form-control-sm" id="deliverer_head_title" name="deliverer_head_title"
                                        value="{{ old('deliverer_head_title', $document->deliverer_head_title ?? $delivererHeadTitle) }}">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="small fw-bold mb-1" for="delivery_letter_number">
                                        Nomor Surat Pengantar
                                    </label>
                                    <input type="text" class="form-control form-control-sm" id="delivery_letter_number" name="delivery_letter_number"
                                        value="{{ old('delivery_letter_number', $document->delivery_letter_number ?? $deliveryLetterNumber) }}">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="small fw-bold mb-1" for="delivery_letter_date">
                                        Tanggal Surat Pengantar
                                    </label>
                                    <input type="text" class="form-control form-control-sm" id="delivery_letter_date" name="delivery_letter_date"
                                        value="{{ old('delivery_letter_date', $document->delivery_letter_date ?? $deliveryLetterDate) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 5. TERSANGKA & BERKAS PERKARA --}}
                <div class="form-section-card">
                    <div class="form-section-header">
                        <span><i class="bi bi-person-exclamation me-2"></i>5. Tersangka & Berkas Perkara</span>
                        <span class="badge badge-light border text-muted">Tersangka & Berkas</span>
                    </div>
                    <div class="form-section-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small mb-1" for="suspect_name">
                                    Nama Tersangka Utama
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="suspect_name" name="suspect_name"
                                        value="{{ old('suspect_name', $document->suspect_name ?? $suspectName) }}" required>
                                    @if ($suspects && $suspects->isNotEmpty())
                                        <select id="select_suspect_quick" class="form-select" style="max-width: 140px;">
                                            <option value="">Pilih...</option>
                                            @foreach ($suspects as $s)
                                                <option value="{{ $s->name }}">{{ $s->name }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                                <div class="field-hint">Nama tersangka pertama yang diserahkan</div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="fw-bold small mb-1" for="berkas_number">
                                    Nomor Berkas Perkara
                                </label>
                                <input type="text" class="form-control" id="berkas_number" name="berkas_number"
                                    value="{{ old('berkas_number', $document->berkas_number ?? $berkasNumber) }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="fw-bold small mb-1" for="berkas_date">
                                    Tanggal Berkas Perkara
                                </label>
                                <input type="text" class="form-control" id="berkas_date" name="berkas_date"
                                    value="{{ old('berkas_date', $document->berkas_date ?? $berkasDate) }}" required>
                            </div>
                            <div class="col-12 mb-2">
                                <label class="fw-bold small mb-1" for="suspect2_line">
                                    Tersangka Tambahan (Baris ke-2 jika ada)
                                </label>
                                <input type="text" class="form-control" id="suspect2_line" name="suspect2_line"
                                    value="{{ old('suspect2_line', $document->suspect2_line ?? $suspect2Line) }}">
                                <div class="field-hint">Tuliskan jika ada tersangka kedua, atau isi "-" jika hanya 1 orang tersangka.</div>
                            </div>
                        </div>

                        {{-- Hubungan ke database tersangka --}}
                        @if ($suspects && $suspects->isNotEmpty())
                            <div class="mt-2 pt-2 border-top">
                                <label class="small text-muted fw-bold d-block mb-1">Tautkan data tersangka perkara (untuk database):</label>
                                @foreach ($suspects as $s)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="suspects[]" id="suspect_{{ $s->id }}" value="{{ $s->id }}"
                                            {{ in_array($s->id, old('suspects', $selectedSuspectIds)) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="suspect_{{ $s->id }}">{{ $s->name }} (NIK: {{ $s->nik ?? '-' }})</label>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- 6. PIHAK KEDUA (PENUNTUT UMUM YANG MENERIMA) --}}
                <div class="form-section-card">
                    <div class="form-section-header">
                        <span><i class="bi bi-briefcase me-2"></i>6. Pihak Kedua (Penuntut Umum Yang Menerima)</span>
                        <span class="badge badge-light border text-muted">Jaksa Penuntut Umum</span>
                    </div>
                    <div class="form-section-body">
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="fw-bold small text-primary mb-1">
                                    <i class="bi bi-building me-1"></i>Pilih Instansi Kejaksaan Negeri:
                                </label>
                                <select id="prosecutor_id" name="prosecutor_id" class="form-control select2">
                                    <option value="">-- Pilih Instansi Kejaksaan --</option>
                                    @foreach ($prosecutors as $p)
                                        <option value="{{ $p->id }}" data-name="{{ $p->name }}"
                                            {{ old('prosecutor_id', $document->prosecutor_id) == $p->id ? 'selected' : '' }}>
                                            {{ $p->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold small mb-1" for="prosecutor_officer_name">
                                    Nama Jaksa Penuntut Umum <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="prosecutor_officer_name" name="prosecutor_officer_name"
                                    value="{{ old('prosecutor_officer_name', $document->prosecutor_officer_name ?? $prosecutorOfficerName) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold small mb-1" for="prosecutor_officer_rank_nip">
                                    Pangkat / NIP
                                </label>
                                <input type="text" class="form-control" id="prosecutor_officer_rank_nip" name="prosecutor_officer_rank_nip"
                                    value="{{ old('prosecutor_officer_rank_nip', $document->prosecutor_officer_rank_nip ?? $prosecutorOfficerRankNip) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold small mb-1" for="prosecutor_officer_position_name">
                                    Jabatan
                                </label>
                                <input type="text" class="form-control" id="prosecutor_officer_position_name" name="prosecutor_officer_position_name"
                                    value="{{ old('prosecutor_officer_position_name', $document->prosecutor_officer_position_name ?? $prosecutorOfficerPositionName) }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 7. PERKARA PIDANA, PASAL, TKP & WAKTU KEJADIAN --}}
                <div class="form-section-card">
                    <div class="form-section-header">
                        <span><i class="bi bi-shield-shaded me-2"></i>7. Perkara Pidana, TKP & Waktu Kejadian</span>
                        <span class="badge badge-light border text-muted">Pasal & Uraian Perkara</span>
                    </div>
                    <div class="form-section-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small mb-1" for="dugaan_tindak_pidana">
                                    Dugaan Tindak Pidana
                                </label>
                                <input type="text" class="form-control" id="dugaan_tindak_pidana" name="dugaan_tindak_pidana"
                                    value="{{ old('dugaan_tindak_pidana', $document->dugaan_tindak_pidana ?? $dugaanTindakPidana) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small mb-1" for="pasal_diduga">
                                    Pasal yang Diduga
                                </label>
                                <input type="text" class="form-control" id="pasal_diduga" name="pasal_diduga"
                                    value="{{ old('pasal_diduga', $document->pasal_diduga ?? $pasalDiduga) }}" required>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="fw-bold small mb-1" for="uu_nomor">
                                    UU Nomor
                                </label>
                                <input type="text" class="form-control" id="uu_nomor" name="uu_nomor"
                                    value="{{ old('uu_nomor', $document->uu_nomor ?? $uuNomor) }}" required>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="fw-bold small mb-1" for="uu_tahun">
                                    UU Tahun
                                </label>
                                <input type="text" class="form-control" id="uu_tahun" name="uu_tahun"
                                    value="{{ old('uu_tahun', $document->uu_tahun ?? $uuTahun) }}" required>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="fw-bold small mb-1" for="uu_tentang">
                                    UU Tentang
                                </label>
                                <input type="text" class="form-control" id="uu_tentang" name="uu_tentang"
                                    value="{{ old('uu_tentang', $document->uu_tentang ?? $uuTentang) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small mb-1" for="tempat_kejadian">
                                    Tempat Kejadian (TKP)
                                </label>
                                <input type="text" class="form-control" id="tempat_kejadian" name="tempat_kejadian"
                                    value="{{ old('tempat_kejadian', $document->tempat_kejadian ?? $tempatKejadian) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small mb-1" for="waktu_kejadian">
                                    Waktu Kejadian
                                </label>
                                <input type="text" class="form-control" id="waktu_kejadian" name="waktu_kejadian"
                                    value="{{ old('waktu_kejadian', $document->waktu_kejadian ?? $waktuKejadian) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 8. PELAKSANAAN SERAH TERIMA & SAKSI-SAKSI --}}
                <div class="form-section-card">
                    <div class="form-section-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-geo-alt me-2"></i>8. Pelaksanaan Serah Terima & Saksi-Saksi</span>
                        <div class="d-flex align-items-center">
                            <button type="button" class="btn btn-sm btn-primary me-2" id="btn-add-witness" onclick="addWitnessRow()">
                                <i class="bi bi-plus-circle me-1"></i>Tambah Saksi
                            </button>
                            <span class="badge badge-light border text-muted">Tempat & Saksi</span>
                        </div>
                    </div>
                    <div class="form-section-body">
                        <div class="row mb-3">
                            <div class="col-md-3 mb-2">
                                <label class="fw-bold small mb-1" for="handover_day">
                                    Hari Serah Terima
                                </label>
                                <input type="text" class="form-control" id="handover_day" name="handover_day"
                                    value="{{ old('handover_day', $document->handover_day ?? $handoverDay) }}" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="fw-bold small mb-1" for="handover_date">
                                    Tanggal Serah Terima
                                </label>
                                <input type="text" class="form-control" id="handover_date" name="handover_date"
                                    value="{{ old('handover_date', $document->handover_date ?? $handoverDate) }}" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="fw-bold small mb-1" for="handover_place">
                                    Tempat Pelaksanaan Serah Terima
                                </label>
                                <input type="text" class="form-control" id="handover_place" name="handover_place"
                                    value="{{ old('handover_place', $document->handover_place ?? $handoverPlace) }}" required>
                            </div>
                        </div>

                        {{-- Container Saksi Dinamis --}}
                        @php
                            $initWitnesses = old('witnesses', $witnessList ?? [
                                ['name' => $document->witness1_name ?? '-', 'rank_nip' => $document->witness1_rank_nip ?? '-', 'position_name' => $document->witness1_position_name ?? '-', 'address' => $document->witness1_address ?? '-']
                            ]);
                            if (empty($initWitnesses)) {
                                $initWitnesses = [
                                    ['name' => '-', 'rank_nip' => '-', 'position_name' => '-', 'address' => '-']
                                ];
                            }
                        @endphp
                        <div id="witnesses-container">
                            @foreach ($initWitnesses as $idx => $wItem)
                                <div class="witness-card p-3 mb-3 bg-light rounded border" data-index="{{ $idx }}">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold small text-dark witness-title">
                                            <i class="bi bi-eye me-1"></i>Saksi <span class="witness-num">{{ $idx + 1 }}</span>
                                        </span>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-witness" onclick="removeWitnessRow(this)" style="{{ $loop->first ? 'display: none;' : '' }}">
                                            <i class="bi bi-trash me-1"></i>Hapus
                                        </button>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3 mb-2">
                                            <label class="small fw-bold mb-1">Nama Saksi <span class="witness-num">{{ $idx + 1 }}</span></label>
                                            <input type="text" class="form-control form-control-sm input-witness-name" name="witnesses[{{ $idx }}][name]"
                                                value="{{ $wItem['name'] ?? '-' }}">
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="small fw-bold mb-1">Pangkat / NIP</label>
                                            <input type="text" class="form-control form-control-sm input-witness-rank" name="witnesses[{{ $idx }}][rank_nip]"
                                                value="{{ $wItem['rank_nip'] ?? '-' }}">
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="small fw-bold mb-1">Jabatan</label>
                                            <input type="text" class="form-control form-control-sm input-witness-pos" name="witnesses[{{ $idx }}][position_name]"
                                                value="{{ $wItem['position_name'] ?? '-' }}">
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="small fw-bold mb-1">Alamat / Instansi</label>
                                            <input type="text" class="form-control form-control-sm input-witness-addr" name="witnesses[{{ $idx }}][address]"
                                                value="{{ $wItem['address'] ?? '-' }}">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Uraian Pelaksanaan --}}
                        <div class="col-12 p-0">
                            <label class="fw-bold small mb-1" for="execution_description">
                                Uraian Pelaksanaan Serah Terima
                            </label>
                            <textarea class="form-control" id="execution_description" name="execution_description" rows="3" required>{{ old('execution_description', $document->execution_description ?? $executionDescription) }}</textarea>
                            <div class="field-hint">Uraian mengenai kondisi saat penyerahan dilakukan</div>
                        </div>
                    </div>
                </div>

                {{-- 9. RINCIAN BARANG BUKTI & PEJABAT MENGETAHUI --}}
                <div class="form-section-card">
                    <div class="form-section-header">
                        <span><i class="bi bi-box-seam me-2"></i>9. Rincian Barang Bukti & Pejabat Mengetahui</span>
                        <span class="badge badge-light border text-muted">Database & Arsip</span>
                    </div>
                    <div class="form-section-body">
                        <div class="mb-3">
                            <label class="fw-bold small mb-1" for="daftar_barang_bukti">
                                Daftar Rincian Barang Bukti (Untuk Arsip Database)
                            </label>
                            <textarea id="daftar_barang_bukti" class="form-control" name="daftar_barang_bukti" rows="4">{{ old('daftar_barang_bukti', $document->daftar_barang_bukti) }}</textarea>
                            <div class="field-hint">Daftar barang bukti kendaraan dari data kecelakaan telah dimuat otomatis.</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small mb-1" for="signatory">
                                    Pejabat Penandatangan / Atasan (Mengetahui)
                                </label>
                                <select id="signatory" name="signatory" class="form-control select2">
                                    <option value="">-- Pilih Pejabat Penandatangan (Opsional) --</option>
                                    @foreach ($authorizedSignatories as $sign)
                                        <option value="{{ $sign->id }}"
                                            {{ old('signatory', $selectedSignatoryId) == $sign->id ? 'selected' : '' }}>
                                            {{ $sign->full_name }} — NRP: {{ $sign->register_number ?? '-' }} ({{ $sign->position->name ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small mb-1" for="signatory_head_text">
                                    Teks Header TTD Atasan
                                </label>
                                <input type="text" class="form-control" id="signatory_head_text" name="signatory_head_text"
                                    value="{{ old('signatory_head_text', $document->signatory_head_text ?? ('a.n. KEPALA KEPOLISIAN RESOR ' . strtoupper($accident->polres->name ?? 'PASURUAN'))) }}">
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
                        <i class="bi bi-save me-1"></i> Perbarui Dokumen
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script>
        // Fungsi Dinamis Saksi Global
        window.reindexWitnesses = function() {
            $('#witnesses-container .witness-card').each(function(index) {
                var num = index + 1;
                $(this).attr('data-index', index);
                $(this).find('.witness-num').text(num);
                $(this).find('.input-witness-name').attr('name', 'witnesses[' + index + '][name]');
                $(this).find('.input-witness-rank').attr('name', 'witnesses[' + index + '][rank_nip]');
                $(this).find('.input-witness-pos').attr('name', 'witnesses[' + index + '][position_name]');
                $(this).find('.input-witness-addr').attr('name', 'witnesses[' + index + '][address]');

                if (index === 0) {
                    $(this).find('.btn-remove-witness').hide();
                } else {
                    $(this).find('.btn-remove-witness').show();
                }
            });
        };

        window.addWitnessRow = function() {
            var count = $('#witnesses-container .witness-card').length;
            var num = count + 1;
            var template = `
                <div class="witness-card p-3 mb-3 bg-light rounded border" data-index="${count}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold small text-dark witness-title">
                            <i class="bi bi-eye me-1"></i>Saksi <span class="witness-num">${num}</span>
                        </span>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-witness" onclick="removeWitnessRow(this)">
                            <i class="bi bi-trash me-1"></i>Hapus
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label class="small fw-bold mb-1">Nama Saksi <span class="witness-num">${num}</span></label>
                            <input type="text" class="form-control form-control-sm input-witness-name" name="witnesses[${count}][name]" value="-">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="small fw-bold mb-1">Pangkat / NIP</label>
                            <input type="text" class="form-control form-control-sm input-witness-rank" name="witnesses[${count}][rank_nip]" value="-">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="small fw-bold mb-1">Jabatan</label>
                            <input type="text" class="form-control form-control-sm input-witness-pos" name="witnesses[${count}][position_name]" value="-">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="small fw-bold mb-1">Alamat / Instansi</label>
                            <input type="text" class="form-control form-control-sm input-witness-addr" name="witnesses[${count}][address]" value="-">
                        </div>
                    </div>
                </div>
            `;
            $('#witnesses-container').append(template);
            window.reindexWitnesses();
        };

        window.removeWitnessRow = function(btn) {
            $(btn).closest('.witness-card').remove();
            window.reindexWitnesses();
        };

        // Fungsi Dinamis Anggota Pendamping Global
        window.reindexAccompanying = function() {
            $('#accompanying-container .accompanying-card').each(function(index) {
                var num = index + 1;
                $(this).attr('data-index', index);
                $(this).find('.accompanying-num').text(num);
                $(this).find('.input-accompanying-name').attr('name', 'accompanying_officers[' + index + '][name]');
                $(this).find('.input-accompanying-rank').attr('name', 'accompanying_officers[' + index + '][rank_name]');
                $(this).find('.input-accompanying-nrp').attr('name', 'accompanying_officers[' + index + '][register_number]');
                $(this).find('.input-accompanying-pos').attr('name', 'accompanying_officers[' + index + '][position_name]');

                if (index === 0) {
                    $(this).find('.btn-remove-accompanying').hide();
                } else {
                    $(this).find('.btn-remove-accompanying').show();
                }
            });
        };

        window.pickAccompanyingOfficer = function(selectEl) {
            var opt = $(selectEl).find(':selected');
            var card = $(selectEl).closest('.accompanying-card');
            if (opt.val()) {
                card.find('.input-accompanying-name').val(opt.data('name') || '');
                card.find('.input-accompanying-rank').val(opt.data('rank') || '');
                card.find('.input-accompanying-nrp').val(opt.data('nrp') || '');
                card.find('.input-accompanying-pos').val(opt.data('position') || 'Penyidik Pembantu');
            }
        };

        window.addAccompanyingRow = function() {
            var count = $('#accompanying-container .accompanying-card').length;
            var num = count + 1;
            var optionsHtml = $('#officer-options-tpl').html() || '';
            var template = `
                <div class="accompanying-card p-3 mb-3 bg-light rounded border" data-index="${count}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-dark small accompanying-title">
                            <i class="bi bi-person me-1"></i>Petugas Pendamping <span class="accompanying-num">${num}</span>
                        </span>
                        <div class="d-flex align-items-center" style="min-width: 50%;">
                            <div class="flex-grow-1 me-2">
                                <select class="form-control form-control-sm select2 select-accompanying-officer" onchange="pickAccompanyingOfficer(this)">
                                    <option value="">-- Pilih Pendamping ${num} --</option>
                                    ${optionsHtml}
                                </select>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-accompanying" onclick="removeAccompanyingRow(this)">
                                <i class="bi bi-trash me-1"></i>Hapus
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label class="small fw-bold mb-1">Nama</label>
                            <input type="text" class="form-control form-control-sm input-accompanying-name" name="accompanying_officers[${count}][name]" value="-">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="small fw-bold mb-1">Pangkat</label>
                            <input type="text" class="form-control form-control-sm input-accompanying-rank" name="accompanying_officers[${count}][rank_name]" value="-">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="small fw-bold mb-1">NRP</label>
                            <input type="text" class="form-control form-control-sm input-accompanying-nrp" name="accompanying_officers[${count}][register_number]" value="-">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="small fw-bold mb-1">Jabatan</label>
                            <input type="text" class="form-control form-control-sm input-accompanying-pos" name="accompanying_officers[${count}][position_name]" value="-">
                        </div>
                    </div>
                </div>
            `;
            var $newCard = $(template);
            $('#accompanying-container').append($newCard);
            $newCard.find('.select-accompanying-officer').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
            window.reindexAccompanying();
        };

        window.removeAccompanyingRow = function(btn) {
            $(btn).closest('.accompanying-card').remove();
            window.reindexAccompanying();
        };

        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Auto-fill Penyidik
            $('#select_deliverer').on('change', function() {
                var opt = $(this).find(':selected');
                if (opt.val()) {
                    $('#investigator_name').val(opt.data('name') || '');
                    $('#investigator_rank_name').val(opt.data('rank') || '');
                    $('#investigator_register_number').val(opt.data('nrp') || '');
                    $('#investigator_position_name').val(opt.data('position') || 'Penyidik Pembantu');
                    $('#deliverer_officer_id').val(opt.val());
                }
            });

            // Auto-fill Pendamping Dinamis
            $(document).on('change', '.select-accompanying-officer', function() {
                window.pickAccompanyingOfficer(this);
            });

            // Auto-fill Kejaksaan
            $('#prosecutor_id').on('change', function() {
                var opt = $(this).find(':selected');
                var name = opt.data('name') || opt.text();
                if (opt.val() && name && !name.startsWith('--')) {
                    $('#prosecutor_institution_name').val(name);
                    $('#handover_place').val('Kantor ' + name);
                }
            });

            // Auto-fill Tersangka Cepat
            $('#select_suspect_quick').on('change', function() {
                var val = $(this).val();
                if (val) {
                    $('#suspect_name').val(val);
                }
            });
        });
    </script>
@endpush
