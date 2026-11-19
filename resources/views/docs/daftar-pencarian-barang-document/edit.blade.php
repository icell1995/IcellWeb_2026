@php
    $_title = 'Edit Daftar Pencarian Barang (DPB)';
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
            <h5 class="fw-bold text-blue-dark">Edit Daftar Pencarian Barang (DPB)</h5>
            <small class="text-muted d-block mb-3">Kode Dokumen: <b>DCT-0705</b></small>

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
            <form action="{{ route('doc.daftar-pencarian-barang-document.update', ['id' => $document->id]) }}"
                method="POST" id="formEditDaftarPencarianBarang" novalidate>
                @csrf
                @method('PUT')
                <input type="hidden" name="accident_id" id="accident_id" value="{{ $accidentId }}">
                <input type="hidden" name="case_vehicle_id" id="case_vehicle_id" value="{{ old('case_vehicle_id', $document->case_vehicle_id) }}">

                {{-- 1. METADATA & DASAR RUJUKAN --}}
                <div class="form-section-card">
                    <div class="form-section-header">
                        <span><i class="bi bi-file-earmark-text me-2"></i>1. Nomor Dokumen & Dasar Rujukan</span>
                        <span class="badge badge-light border text-muted">Nomor DPB & Dasar LP</span>
                    </div>
                    <div class="form-section-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="small fw-bold mb-1" for="document_number">Nomor Daftar Pencarian Barang (DPB) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="document_number" name="document_number"
                                    value="{{ old('document_number', $document->document_number) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small fw-bold mb-1" for="accident_number">Nomor Laporan Polisi (LP) (Berdasarkan 1)</label>
                                <input type="text" class="form-control form-control-sm" id="accident_number" name="accident_number"
                                    value="{{ old('accident_number', $document->accident_number) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small fw-bold mb-1" for="accident_date">Tanggal Laporan Polisi (LP)</label>
                                <input type="text" class="form-control form-control-sm" id="accident_date" name="accident_date"
                                    value="{{ old('accident_date', $document->accident_date) }}" required>
                            </div>
                            <div class="col-12 mb-2">
                                <label class="small fw-bold mb-1" for="court_permit_text">Dasar Hukum Butir 2 (Izin Pengadilan / Surat Perintah)</label>
                                <textarea class="form-control form-control-sm" id="court_permit_text" name="court_permit_text" rows="2">{{ old('court_permit_text', $document->court_permit_text) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. PERKARA & NARAHUBUNG --}}
                <div class="form-section-card">
                    <div class="form-section-header">
                        <span><i class="bi bi-briefcase me-2"></i>2. Informasi Dugaan Perkara & Petugas Penerima/Narahubung</span>
                        <span class="badge badge-light border text-muted">Uraian Perkara & Kontak Petugas</span>
                    </div>
                    <div class="form-section-body">
                        <div class="row">
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
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold mb-1" for="tempat_kejadian">Tempat Kejadian Perkara (TKP)</label>
                                <input type="text" class="form-control form-control-sm" id="tempat_kejadian" name="tempat_kejadian"
                                    value="{{ old('tempat_kejadian', $document->tempat_kejadian) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold mb-1" for="waktu_kejadian">Waktu Kejadian</label>
                                <input type="text" class="form-control form-control-sm" id="waktu_kejadian" name="waktu_kejadian"
                                    value="{{ old('waktu_kejadian', $document->waktu_kejadian) }}" required>
                            </div>
                        </div>

                        {{-- NARAHUBUNG --}}
                        <hr class="my-3">
                        <h6 class="fw-bold text-secondary mb-2"><i class="bi bi-person-lines-fill me-1"></i> Petugas Penerima / Kontak Narahubung Penyerahan Barang:</h6>
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
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="small fw-bold mb-1" for="contact_officer_role">Sebutan Petugas</label>
                                <input type="text" class="form-control form-control-sm" id="contact_officer_role" name="contact_officer_role"
                                    value="{{ old('contact_officer_role', $document->contact_officer_role) }}" required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="small fw-bold mb-1" for="contact_officer_name">Nama Petugas Penyerahan</label>
                                <input type="text" class="form-control form-control-sm" id="contact_officer_name" name="contact_officer_name"
                                    value="{{ old('contact_officer_name', $document->contact_officer_name) }}" required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="small fw-bold mb-1" for="contact_officer_phone">Nomor Telepon / HP</label>
                                <input type="text" class="form-control form-control-sm" id="contact_officer_phone" name="contact_officer_phone"
                                    value="{{ old('contact_officer_phone', $document->contact_officer_phone) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. TABEL KETERANGAN BARANG YANG DICARI (1 - 12) --}}
                <div class="form-section-card">
                    <div class="form-section-header">
                        <span><i class="bi bi-box-seam me-2"></i>3. Tabel Keterangan Barang yang Dicari (Poin 1 s/d 12)</span>
                        <span class="badge badge-light border text-muted">Spesifikasi Detail Barang/Kendaraan</span>
                    </div>
                    <div class="form-section-body">
                        @if ($caseVehicles->count() > 0)
                            <div class="mb-3 p-2 bg-light rounded border">
                                <label class="small fw-bold text-primary mb-1">
                                    <i class="bi bi-magic me-1"></i> Ganti Data dari Kendaraan Terdata:
                                </label>
                                <select class="form-control form-control-sm select2" id="select_case_vehicle">
                                    <option value="">-- Pilih Kendaraan dari Kasus Ini --</option>
                                    @foreach ($caseVehicles as $v)
                                        @php
                                            $vPlate = $v->plate_number ?? '-';
                                            $vType = $v->vehicle_type ?? '-';
                                            $vDesign = $v->vehicle_design ?? '-';
                                            $vColor = $v->vehicle_color ?? '-';
                                            $vYear = $v->production_year ?? '-';
                                            $vCapacity = $v->engine_capacity ? ($v->engine_capacity . ' cc') : '-';
                                            $vFrame = $v->frame_number ?? '-';
                                            $vEngine = $v->engine_number ?? '-';
                                            $vEngineFrame = 'No. Rangka: ' . $vFrame . ' / No. Mesin: ' . $vEngine;
                                            $vItemName = '1 (satu) unit Kendaraan Bermotor ' . ($vType != '-' ? $vType : '') . ' ' . ($vDesign != '-' ? $vDesign : '');
                                        @endphp
                                        <option value="{{ $v->id }}"
                                            data-item-name="{{ trim($vItemName) }}"
                                            data-plate="{{ $vPlate }}"
                                            data-type="{{ $vType }}"
                                            data-model="{{ $vDesign }}"
                                            data-color="{{ $vColor }}"
                                            data-year="{{ $vYear }}"
                                            data-capacity="{{ $vCapacity }}"
                                            data-engine-frame="{{ $vEngineFrame }}"
                                            {{ (old('case_vehicle_id', $document->case_vehicle_id) == $v->id) ? 'selected' : '' }}>
                                            Plat: {{ $vPlate }} ({{ $vType }} - {{ $vDesign }}, Warna: {{ $vColor }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="row">
                            {{-- 1. Nama Barang & 2. Jumlah --}}
                            <div class="col-md-8 mb-3">
                                <label class="small fw-bold mb-1" for="item_name">1. Nama Barang <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="item_name" name="item_name"
                                    value="{{ old('item_name', $document->item_name) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small fw-bold mb-1" for="quantity">2. Jumlah</label>
                                <input type="text" class="form-control form-control-sm" id="quantity" name="quantity"
                                    value="{{ old('quantity', $document->quantity) }}">
                            </div>

                            {{-- 3. Tanda-tanda khusus --}}
                            <div class="col-12 mb-2">
                                <h6 class="fw-bold text-secondary mb-2"><i class="bi bi-tag me-1"></i> 3. Tanda-tanda Khusus:</h6>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="small fw-bold mb-1" for="registration_number">a. Nomor Registrasi (No Polisi)</label>
                                <input type="text" class="form-control form-control-sm" id="registration_number" name="registration_number"
                                    value="{{ old('registration_number', $document->registration_number) }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="small fw-bold mb-1" for="cylinder_capacity">b. Isi Silinder</label>
                                <input type="text" class="form-control form-control-sm" id="cylinder_capacity" name="cylinder_capacity"
                                    value="{{ old('cylinder_capacity', $document->cylinder_capacity) }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="small fw-bold mb-1" for="fuel_type">c. Bahan Bakar</label>
                                <input type="text" class="form-control form-control-sm" id="fuel_type" name="fuel_type"
                                    value="{{ old('fuel_type', $document->fuel_type) }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="small fw-bold mb-1" for="tnkb_color">d. Warna TNKB (Plat)</label>
                                <input type="text" class="form-control form-control-sm" id="tnkb_color" name="tnkb_color"
                                    value="{{ old('tnkb_color', $document->tnkb_color) }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="small fw-bold mb-1" for="registration_year">e. Tahun Registrasi</label>
                                <input type="text" class="form-control form-control-sm" id="registration_year" name="registration_year"
                                    value="{{ old('registration_year', $document->registration_year) }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="small fw-bold mb-1" for="bpkb_number">f. Nomor BPKB</label>
                                <input type="text" class="form-control form-control-sm" id="bpkb_number" name="bpkb_number"
                                    value="{{ old('bpkb_number', $document->bpkb_number) }}">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="small fw-bold mb-1" for="special_signs_other">g. Tanda Khusus Lainnya</label>
                                <input type="text" class="form-control form-control-sm" id="special_signs_other" name="special_signs_other"
                                    value="{{ old('special_signs_other', $document->special_signs_other) }}">
                            </div>

                            {{-- 4. Ciri-ciri --}}
                            <div class="col-12 mb-2">
                                <h6 class="fw-bold text-secondary mb-2"><i class="bi bi-aspect-ratio me-1"></i> 4. Ciri-ciri (Tipe, Ukuran, dll):</h6>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="small fw-bold mb-1" for="item_type">a. Tipe</label>
                                <input type="text" class="form-control form-control-sm" id="item_type" name="item_type"
                                    value="{{ old('item_type', $document->item_type) }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="small fw-bold mb-1" for="item_category">b. Jenis</label>
                                <input type="text" class="form-control form-control-sm" id="item_category" name="item_category"
                                    value="{{ old('item_category', $document->item_category) }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="small fw-bold mb-1" for="item_model">c. Model</label>
                                <input type="text" class="form-control form-control-sm" id="item_model" name="item_model"
                                    value="{{ old('item_model', $document->item_model) }}">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="small fw-bold mb-1" for="physical_features_other">d. Ciri-ciri Lainnya</label>
                                <input type="text" class="form-control form-control-sm" id="physical_features_other" name="physical_features_other"
                                    value="{{ old('physical_features_other', $document->physical_features_other) }}">
                            </div>

                            {{-- 5 s/d 12 --}}
                            <div class="col-12 mb-2">
                                <h6 class="fw-bold text-secondary mb-2"><i class="bi bi-card-text me-1"></i> Rincian 5 s/d 12:</h6>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small fw-bold mb-1" for="item_color">5. Warna</label>
                                <input type="text" class="form-control form-control-sm" id="item_color" name="item_color"
                                    value="{{ old('item_color', $document->item_color) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small fw-bold mb-1" for="manufacture_year">6. Tahun Pembuatan</label>
                                <input type="text" class="form-control form-control-sm" id="manufacture_year" name="manufacture_year"
                                    value="{{ old('manufacture_year', $document->manufacture_year) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small fw-bold mb-1" for="estimated_price">7. Taksiran Harga</label>
                                <input type="text" class="form-control form-control-sm" id="estimated_price" name="estimated_price"
                                    value="{{ old('estimated_price', $document->estimated_price) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small fw-bold mb-1" for="trade_mark">8. Merek Dagang</label>
                                <input type="text" class="form-control form-control-sm" id="trade_mark" name="trade_mark"
                                    value="{{ old('trade_mark', $document->trade_mark) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small fw-bold mb-1" for="engine_frame_number">9. Nomor Pembuatan (Rangka & Mesin)</label>
                                <input type="text" class="form-control form-control-sm" id="engine_frame_number" name="engine_frame_number"
                                    value="{{ old('engine_frame_number', $document->engine_frame_number) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small fw-bold mb-1" for="manufacture_place">10. Tempat Pembuatan</label>
                                <input type="text" class="form-control form-control-sm" id="manufacture_place" name="manufacture_place"
                                    value="{{ old('manufacture_place', $document->manufacture_place) }}">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="small fw-bold mb-1" for="loss_chronology">11. Cara Hilangnya</label>
                                <textarea class="form-control form-control-sm" id="loss_chronology" name="loss_chronology" rows="2">{{ old('loss_chronology', $document->loss_chronology) }}</textarea>
                            </div>
                            <div class="col-12 mb-2">
                                <label class="small fw-bold mb-1" for="important_notes">12. Keterangan Lain yang Penting</label>
                                <textarea class="form-control form-control-sm" id="important_notes" name="important_notes" rows="2">{{ old('important_notes', $document->important_notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. KEPADA & PENANDATANGAN --}}
                <div class="form-section-card">
                    <div class="form-section-header">
                        <span><i class="bi bi-send me-2"></i>4. Distribusi (Kepada) & Pejabat Penandatangan</span>
                        <span class="badge badge-light border text-muted">Tembusan Kepada & Tanda Tangan</span>
                    </div>
                    <div class="form-section-body">
                        {{-- KEPADA --}}
                        <h6 class="fw-bold text-secondary mb-2"><i class="bi bi-envelope me-1"></i> Kepada (Penerima DPB):</h6>
                        <div class="alert alert-light border py-1 px-3 mb-2 small text-muted">
                            1. Direskrimum/Sus/Narkoba jajaran.<br>
                            2. Kapolres jajaran.<br>
                            3. (Diisi di bawah)
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-2">
                                <label class="small fw-bold mb-1" for="kepada_3">Kepada Butir 3</label>
                                <input type="text" class="form-control form-control-sm" id="kepada_3" name="kepada_3"
                                    value="{{ old('kepada_3', $document->kepada_3) }}">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="small fw-bold mb-1" for="document_location">Tempat Penetapan</label>
                                <input type="text" class="form-control form-control-sm" id="document_location" name="document_location"
                                    value="{{ old('document_location', $document->document_location) }}">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="small fw-bold mb-1" for="document_date">Tanggal Surat</label>
                                <input type="text" class="form-control form-control-sm" id="document_date" name="document_date"
                                    value="{{ old('document_date', $document->document_date) }}">
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
                        <i class="bi bi-save me-1"></i> Simpan Perubahan DPB
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

            // Auto-fill dari Kendaraan Terdata
            $('#select_case_vehicle').on('change', function() {
                var opt = $(this).find(':selected');
                if (opt.val()) {
                    $('#case_vehicle_id').val(opt.val());
                    if (opt.data('item-name')) $('#item_name').val(opt.data('item-name'));
                    if (opt.data('plate') && opt.data('plate') !== '-') $('#registration_number').val(opt.data('plate'));
                    if (opt.data('type') && opt.data('type') !== '-') $('#item_type').val(opt.data('type'));
                    if (opt.data('model') && opt.data('model') !== '-') $('#item_model').val(opt.data('model'));
                    if (opt.data('color') && opt.data('color') !== '-') $('#item_color').val(opt.data('color'));
                    if (opt.data('year') && opt.data('year') !== '-') $('#manufacture_year').val(opt.data('year'));
                    if (opt.data('capacity') && opt.data('capacity') !== '-') $('#cylinder_capacity').val(opt.data('capacity'));
                    if (opt.data('engine-frame') && opt.data('engine-frame') !== '-') $('#engine_frame_number').val(opt.data('engine-frame'));
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

