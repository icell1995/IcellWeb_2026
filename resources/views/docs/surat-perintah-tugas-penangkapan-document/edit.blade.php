@php
    $_title = 'Edit Surat Perintah Tugas Penangkapan';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .card-header.bg-light {
            background-color: #f8f9fa !important;
            border-bottom: 1px solid #ebedf2;
            padding: 12px 20px;
        }
        .form-label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
        .section-subhead {
            font-weight: 700;
            font-size: 0.95rem;
            color: #1e293b;
            margin-top: 15px;
            margin-bottom: 10px;
        }
        .table-officers th {
            background-color: #f8f9fa;
            font-size: 13px;
            vertical-align: middle;
            text-align: center;
        }
        .table-officers td {
            vertical-align: middle;
        }
        .btn-dark-blue {
            background-color: rgb(34, 37, 119) !important;
            border-color: rgb(34, 37, 119) !important;
            color: #ffffff !important;
        }
        .btn-dark-blue:hover {
            background-color: rgb(39, 43, 139) !important;
            border-color: rgb(39, 43, 139) !important;
            color: #ffffff !important;
        }
    </style>
@endpush

@section('content')

    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"><i
            class="bi bi-arrow-left"></i> Kembali ke Progress Perkara</a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold m-0">Ubah Surat Perintah Tugas Penangkapan</h5>

            <div class="alert alert-danger mt-3" id="attentionBox">
                <div class="text-center">
                    <b>
                        PERHATIAN !<br /><br />
                        DATA INI WAJIB DIISI DENGAN LENGKAP KARENA MERUPAKAN BAGIAN DARI BERKAS ADMINISTRASI PENYIDIKAN (MINDIK)
                        DALAM SISTEM PENANGANAN PERKARA TERPADU BERBASIS TEKNOLOGI INFORMASI (SPPT-TI).
                    </b>
                </div>
            </div>

            @if (isset($errors) && $errors->any())
                <div class="card-body p-0 mb-3">
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
                <div class="card-body p-0 mb-3">
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                </div>
            @endif
        </div>

        <div class="box-body">
            <form action="{{ route('doc.surat-perintah-tugas-penangkapan-document.update', ['id' => $doc->id]) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="accident_id" value="{{ $accidentId }}">

            {{-- ─── KOPSTUK & HEADER (AUTO-GENERATED HIDDEN) ─── --}}
            <input type="hidden" name="kopstuk_1" value="{{ old('kopstuk_1', $doc->kopstuk_1) }}">
            <input type="hidden" name="kopstuk_2" value="{{ old('kopstuk_2', $doc->kopstuk_2) }}">
            <input type="hidden" name="kopstuk_3" value="{{ old('kopstuk_3', $doc->kopstuk_3) }}">
            <input type="hidden" name="kopstuk_4" value="{{ old('kopstuk_4', $doc->kopstuk_4) }}">
            <input type="hidden" name="nomor_surat" value="{{ old('nomor_surat', $doc->nomor_surat) }}">
            <input type="hidden" name="tanggal_surat" value="{{ old('tanggal_surat', $doc->tanggal_surat ? \Carbon\Carbon::parse($doc->tanggal_surat)->format('Y-m-d') : date('Y-m-d')) }}">
            <input type="hidden" name="dikeluarkan_di" value="{{ old('dikeluarkan_di', $doc->dikeluarkan_di) }}">

            {{-- ─── I. PERTIMBANGAN & DASAR HUKUM ─── --}}
            <div class="card card-outline card-primary mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="card-title fw-bold m-0">I. Pertimbangan & Dasar Hukum<span class="text-danger">*</span></h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="pertimbangan">Pertimbangan<span class="text-danger">*</span></label>
                        <textarea name="pertimbangan" id="pertimbangan" class="form-control" rows="3" required>{{ old('pertimbangan', $doc->pertimbangan) }}</textarea>
                    </div>

                    <div class="section-subhead">Dasar Hukum & Dokumen Rujukan:</div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="dasar_kuhap">1. Dasar KUHAP<span class="text-danger">*</span></label>
                            <textarea name="dasar_kuhap" id="dasar_kuhap" class="form-control" rows="2" required>{{ old('dasar_kuhap', $doc->dasar_kuhap) }}</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="dasar_polri">2. Dasar UU Kepolisian RI<span class="text-danger">*</span></label>
                            <textarea name="dasar_polri" id="dasar_polri" class="form-control" rows="2" required>{{ old('dasar_polri', $doc->dasar_polri) }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="dasar_uu_pidana">3. Dasar UU Pidana / Khusus (LLAJ)<span class="text-danger">*</span></label>
                            <textarea name="dasar_uu_pidana" id="dasar_uu_pidana" class="form-control" rows="2" required>{{ old('dasar_uu_pidana', $doc->dasar_uu_pidana) }}</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="dasar_lp_nomor">4. Laporan Polisi Nomor<span class="text-danger">*</span></label>
                            <input type="text" name="dasar_lp_nomor" id="dasar_lp_nomor" class="form-control"
                                value="{{ old('dasar_lp_nomor', $doc->dasar_lp_nomor) }}"
                                readonly style="background-color: #f1f5f9; cursor: not-allowed;" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="dasar_lp_tanggal">Tanggal LP<span class="text-danger">*</span></label>
                            <input type="date" name="dasar_lp_tanggal" id="dasar_lp_tanggal" class="form-control"
                                value="{{ old('dasar_lp_tanggal', $doc->dasar_lp_tanggal ? \Carbon\Carbon::parse($doc->dasar_lp_tanggal)->format('Y-m-d') : date('Y-m-d')) }}"
                                readonly style="background-color: #f1f5f9; cursor: not-allowed; pointer-events: none;" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="dasar_sp_sidik_nomor">5. Surat Perintah Penyidikan Nomor<span class="text-danger">*</span></label>
                            <input type="text" name="dasar_sp_sidik_nomor" id="dasar_sp_sidik_nomor" class="form-control"
                                value="{{ old('dasar_sp_sidik_nomor', $doc->dasar_sp_sidik_nomor) }}"
                                readonly style="background-color: #f1f5f9; cursor: not-allowed;" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="dasar_sp_sidik_tanggal">Tanggal SP.Sidik<span class="text-danger">*</span></label>
                            <input type="date" name="dasar_sp_sidik_tanggal" id="dasar_sp_sidik_tanggal" class="form-control"
                                value="{{ old('dasar_sp_sidik_tanggal', $doc->dasar_sp_sidik_tanggal ? \Carbon\Carbon::parse($doc->dasar_sp_sidik_tanggal)->format('Y-m-d') : date('Y-m-d')) }}"
                                readonly style="background-color: #f1f5f9; cursor: not-allowed; pointer-events: none;" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="dasar_sp_kap_nomor">6. Surat Perintah Penangkapan (SP.Kap) Nomor<span class="text-danger">*</span></label>
                            <input type="text" name="dasar_sp_kap_nomor" id="dasar_sp_kap_nomor" class="form-control"
                                value="{{ old('dasar_sp_kap_nomor', $doc->dasar_sp_kap_nomor) }}"
                                readonly style="background-color: #f1f5f9; cursor: not-allowed;" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="dasar_sp_kap_tanggal">Tanggal SP.Kap<span class="text-danger">*</span></label>
                            <input type="date" name="dasar_sp_kap_tanggal" id="dasar_sp_kap_tanggal" class="form-control"
                                value="{{ old('dasar_sp_kap_tanggal', $doc->dasar_sp_kap_tanggal ? \Carbon\Carbon::parse($doc->dasar_sp_kap_tanggal)->format('Y-m-d') : date('Y-m-d')) }}"
                                readonly style="background-color: #f1f5f9; cursor: not-allowed; pointer-events: none;" required>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── II. PETUGAS PELAKSANA (KEPADA) ─── --}}
            <div class="card card-outline card-primary mb-4 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="card-title fw-bold m-0">II. Daftar Petugas Yang Ditugaskan (Kepada)<span class="text-danger">*</span></h6>
                    <button type="button" class="btn btn-sm btn-primary" id="btnAddOfficer">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Petugas
                    </button>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih Cepat dari Data Petugas Polres:</label>
                        <select id="selectQuickOfficer" class="form-select">
                            <option value="">-- Pilih Petugas untuk ditambahkan ke daftar --</option>
                            @foreach ($officers as $off)
                                @php
                                    $pangkat = $off->rank->name ?? ($off->rank_name ?? ($off->pangkat ?? ''));
                                    $nrp = $off->nrp ?: ($off->register_number ?: ($off->id ?: ''));
                                    $jabatan = is_object($off->position) ? ($off->position->name ?? '') : (is_array($off->position) ? ($off->position['name'] ?? '') : ($off->position ?? ''));
                                    if (is_string($jabatan) && str_starts_with(trim($jabatan), '{')) {
                                        $dec = json_decode(trim($jabatan), true);
                                        $jabatan = $dec['name'] ?? $jabatan;
                                    }
                                @endphp
                                <option value="{{ $off->id }}"
                                    data-nama="{{ $off->full_name ?: $off->name }}"
                                    data-pangkat="{{ $pangkat }}"
                                    data-nrp="{{ $nrp }}"
                                    data-jabatan="{{ $jabatan ?: 'BANIT GAKKUM SATLANTAS' }}">
                                    {{ $off->full_name ?: $off->name }} ({{ $pangkat }} / NRP {{ $nrp }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih petugas dari dropdown di atas atau klik tombol <strong>Tambah Petugas</strong>. Data pangkat dan NRP akan terisi otomatis berdasarkan petugas yang dipilih.</small>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-officers mb-0" id="tableOfficers">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Nama Petugas<span class="text-danger">*</span></th>
                                    <th style="width: 200px;">Pangkat<span class="text-danger">*</span></th>
                                    <th style="width: 170px;">NRP<span class="text-danger">*</span></th>
                                    <th>Jabatan<span class="text-danger">*</span></th>
                                    <th style="width: 60px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $savedOfficers = old('officers', $doc->officers ?: []);
                                    if (empty($savedOfficers)) {
                                        $savedOfficers = [
                                            [
                                                'nama' => $doc->penerima_nama,
                                                'pangkat' => $doc->penerima_pangkat,
                                                'nrp' => $doc->penerima_nrp,
                                                'jabatan' => $doc->penerima_jabatan ?: 'BANIT GAKKUM SATLANTAS',
                                            ]
                                        ];
                                    }
                                @endphp
                                @foreach ($savedOfficers as $idx => $offItem)
                                    <tr>
                                        <td class="text-center row-num">{{ $idx + 1 }}</td>
                                        <td>
                                            <select name="officers[{{ $idx }}][nama]" class="form-select form-select-sm off-select-officer" required>
                                                <option value="">-- Pilih Petugas --</option>
                                                @php $matched = false; @endphp
                                                @foreach ($officers as $off)
                                                    @php
                                                        $pangkat = $off->rank->name ?? ($off->rank_name ?? ($off->pangkat ?? ''));
                                                        $nrp = $off->nrp ?: ($off->register_number ?: ($off->id ?: ''));
                                                        $jabatan = is_object($off->position) ? ($off->position->name ?? '') : (is_array($off->position) ? ($off->position['name'] ?? '') : ($off->position ?? ''));
                                                        if (is_string($jabatan) && str_starts_with(trim($jabatan), '{')) {
                                                            $dec = json_decode(trim($jabatan), true);
                                                            $jabatan = $dec['name'] ?? $jabatan;
                                                        }
                                                        $offNama = $off->full_name ?: $off->name;
                                                        $isSel = (isset($offItem['nama']) && $offItem['nama'] == $offNama);
                                                        if ($isSel) $matched = true;
                                                    @endphp
                                                    <option value="{{ $offNama }}"
                                                        data-nama="{{ $offNama }}"
                                                        data-pangkat="{{ $pangkat }}"
                                                        data-nrp="{{ $nrp }}"
                                                        data-jabatan="{{ $jabatan ?: 'BANIT GAKKUM SATLANTAS' }}"
                                                        {{ $isSel ? 'selected' : '' }}>
                                                        {{ $offNama }} ({{ $pangkat }} / NRP {{ $nrp }})
                                                    </option>
                                                @endforeach
                                                @if (!$matched && !empty($offItem['nama']))
                                                    <option value="{{ $offItem['nama'] }}"
                                                        data-nama="{{ $offItem['nama'] }}"
                                                        data-pangkat="{{ $offItem['pangkat'] ?? '' }}"
                                                        data-nrp="{{ $offItem['nrp'] ?? '' }}"
                                                        data-jabatan="{{ $offItem['jabatan'] ?? 'BANIT GAKKUM SATLANTAS' }}"
                                                        selected>
                                                        {{ $offItem['nama'] }} ({{ $offItem['pangkat'] ?? '' }} / NRP {{ $offItem['nrp'] ?? '' }})
                                                    </option>
                                                @endif
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="officers[{{ $idx }}][pangkat]" class="form-control form-control-sm off-pangkat"
                                                value="{{ $offItem['pangkat'] ?? '' }}" placeholder="Pangkat" readonly style="background-color: #f1f5f9; cursor: not-allowed;" required>
                                        </td>
                                        <td>
                                            <input type="text" name="officers[{{ $idx }}][nrp]" class="form-control form-control-sm off-nrp"
                                                value="{{ $offItem['nrp'] ?? '' }}" placeholder="NRP" readonly style="background-color: #f1f5f9; cursor: not-allowed;" required>
                                        </td>
                                        <td>
                                            <input type="text" name="officers[{{ $idx }}][jabatan]" class="form-control form-control-sm off-jabatan"
                                                value="{{ $offItem['jabatan'] ?? 'BANIT GAKKUM SATLANTAS' }}" placeholder="Jabatan" required>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row" title="Hapus">
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

            {{-- ─── III. KETENTUAN TUGAS (UNTUK) ─── --}}
            <div class="card card-outline card-primary mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="card-title fw-bold m-0">III. Ketentuan Pelaksanaan Tugas (Untuk)<span class="text-danger">*</span></h6>
                </div>
                <div class="card-body">
                    <input type="hidden" name="tersangka_id" id="tersangka_id" value="{{ old('tersangka_id', $doc->tersangka_id) }}">

                    <div class="section-subhead mt-0">1. Tugas Penangkapan Terhadap Tersangka Sesuai SP.Kap:</div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="tersangka_nama">Nama Tersangka<span class="text-danger">*</span></label>
                            <input type="text" name="tersangka_nama" id="tersangka_nama" class="form-control"
                                value="{{ old('tersangka_nama', $doc->tersangka_nama) }}" placeholder="Nama Tersangka" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="untuk_sp_kap_nomor">Rujukan Nomor SP.Kap<span class="text-danger">*</span></label>
                            <input type="text" name="untuk_sp_kap_nomor" id="untuk_sp_kap_nomor" class="form-control"
                                value="{{ old('untuk_sp_kap_nomor', $doc->untuk_sp_kap_nomor ?: $doc->dasar_sp_kap_nomor) }}" placeholder="Nomor SP.Kap"
                                readonly style="background-color: #f1f5f9; cursor: not-allowed;" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="untuk_sp_kap_tanggal">Tanggal SP.Kap<span class="text-danger">*</span></label>
                            <input type="date" name="untuk_sp_kap_tanggal" id="untuk_sp_kap_tanggal" class="form-control"
                                value="{{ old('untuk_sp_kap_tanggal', $doc->untuk_sp_kap_tanggal ? \Carbon\Carbon::parse($doc->untuk_sp_kap_tanggal)->format('Y-m-d') : ($doc->dasar_sp_kap_tanggal ? \Carbon\Carbon::parse($doc->dasar_sp_kap_tanggal)->format('Y-m-d') : date('Y-m-d'))) }}"
                                readonly style="background-color: #f1f5f9; cursor: not-allowed; pointer-events: none;" required>
                        </div>
                    </div>

                    <div class="section-subhead">2. Masa Berlaku Surat Perintah Tugas:</div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="berlaku_sampai">Berlaku Sejak Dikeluarkan s.d Tanggal / Keterangan<span class="text-danger">*</span></label>
                            <input type="text" name="berlaku_sampai" id="berlaku_sampai" class="form-control"
                                value="{{ old('berlaku_sampai', $doc->berlaku_sampai) }}" required>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── IV. PENANDATANGAN DOKUMEN ─── --}}
            <div class="card card-outline card-primary mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="card-title fw-bold m-0">IV. Penandatangan Dokumen<span class="text-danger">*</span></h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Kiri: Yang Menerima Perintah --}}
                        <div class="col-md-6 border-end pe-md-4">
                            <h6 class="fw-bold mb-3 text-secondary">
                                <i class="bi bi-person-check me-1"></i> Yang Menerima Perintah<span class="text-danger">*</span>
                            </h6>
                            <div class="mb-3">
                                <label class="form-label" for="selectPenerima">Pilih Petugas Penerima Perintah:</label>
                                <select id="selectPenerima" class="form-select">
                                    <option value="">-- Pilih Petugas Penyidik --</option>
                                    @foreach ($officers as $off)
                                        @php
                                            $pangkat = $off->rank->name ?? ($off->rank_name ?? ($off->pangkat ?? ''));
                                            $nrp = $off->nrp ?: ($off->register_number ?: ($off->id ?: ''));
                                            $jabatan = is_object($off->position) ? ($off->position->name ?? '') : (is_array($off->position) ? ($off->position['name'] ?? '') : ($off->position ?? ''));
                                            if (is_string($jabatan) && str_starts_with(trim($jabatan), '{')) {
                                                $dec = json_decode(trim($jabatan), true);
                                                $jabatan = $dec['name'] ?? $jabatan;
                                            }
                                        @endphp
                                        <option value="{{ $off->id }}"
                                            data-nama="{{ $off->full_name ?: $off->name }}"
                                            data-pangkat="{{ $pangkat }}"
                                            data-nrp="{{ $nrp }}"
                                            data-jabatan="{{ $jabatan ?: 'Penyidik Pembantu' }}">
                                            {{ $off->full_name ?: $off->name }} ({{ $pangkat }} / NRP {{ $nrp }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="penerima_nama">Nama Petugas Penerima<span class="text-danger">*</span></label>
                                <input type="text" name="penerima_nama" id="penerima_nama" class="form-control"
                                    value="{{ old('penerima_nama', $doc->penerima_nama) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="penerima_pangkat">Pangkat<span class="text-danger">*</span></label>
                                <input type="text" name="penerima_pangkat" id="penerima_pangkat" class="form-control"
                                    value="{{ old('penerima_pangkat', $doc->penerima_pangkat) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="penerima_nrp">NRP<span class="text-danger">*</span></label>
                                <input type="text" name="penerima_nrp" id="penerima_nrp" class="form-control"
                                    value="{{ old('penerima_nrp', $doc->penerima_nrp) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="penerima_jabatan">Jabatan<span class="text-danger">*</span></label>
                                <input type="text" name="penerima_jabatan" id="penerima_jabatan" class="form-control"
                                    value="{{ old('penerima_jabatan', $doc->penerima_jabatan) }}" required>
                            </div>
                        </div>

                        {{-- Kanan: Atasan Penyidik --}}
                        <div class="col-md-6 ps-md-4">
                            <h6 class="fw-bold mb-3 text-secondary">
                                <i class="bi bi-shield-check me-1"></i> Atasan Penyidik<span class="text-danger">*</span>
                            </h6>
                            <div class="mb-3">
                                <label class="form-label" for="selectSignatory">Pilih Pejabat Penandatangan:</label>
                                <select id="selectSignatory" class="form-select">
                                    <option value="">-- Pilih Pejabat Penandatangan --</option>
                                    @foreach ($authorizedSignatories as $sig)
                                        @php
                                            $sigNama = $sig->full_name ?? ($sig->name ?? '');
                                            $sigPangkat = $sig->rank->name ?? ($sig->rank_name ?? '');
                                            $sigNrp = $sig->nrp ?: ($sig->register_number ?: ($sig->id ?: ''));
                                            $sigJabatan = 'ATASAN PENYIDIK';
                                            $posVal = is_object($sig->position) ? ($sig->position->name ?? '') : (is_array($sig->position) ? ($sig->position['name'] ?? '') : ($sig->position ?? ''));
                                            if (is_string($posVal) && str_starts_with(trim($posVal), '{')) {
                                                $dec = json_decode(trim($posVal), true);
                                                $posVal = $dec['name'] ?? $posVal;
                                            }
                                            $sigJabatan = $posVal ?: 'ATASAN PENYIDIK';
                                        @endphp
                                        <option value="{{ $sig->id }}"
                                            data-nama="{{ $sigNama }}"
                                            data-pangkat="{{ $sigPangkat }}"
                                            data-nrp="{{ $sigNrp }}"
                                            data-jabatan="{{ $sigJabatan }}">
                                            {{ $sigNama }} ({{ $sigPangkat }} / NRP {{ $sigNrp }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="atasan_nama">Nama Atasan Penyidik<span class="text-danger">*</span></label>
                                <input type="text" name="atasan_nama" id="atasan_nama" class="form-control"
                                    value="{{ old('atasan_nama', $doc->atasan_nama) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="atasan_pangkat">Pangkat<span class="text-danger">*</span></label>
                                <input type="text" name="atasan_pangkat" id="atasan_pangkat" class="form-control"
                                    value="{{ old('atasan_pangkat', $doc->atasan_pangkat) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="atasan_nrp">NRP<span class="text-danger">*</span></label>
                                <input type="text" name="atasan_nrp" id="atasan_nrp" class="form-control"
                                    value="{{ old('atasan_nrp', $doc->atasan_nrp) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="atasan_jabatan">Jabatan<span class="text-danger">*</span></label>
                                <input type="text" name="atasan_jabatan" id="atasan_jabatan" class="form-control"
                                    value="{{ old('atasan_jabatan', $doc->atasan_jabatan) }}" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── ACTION BUTTONS ─── --}}
            <div class="text-end mb-4">
                <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" name="action" value="save" class="btn btn-dark-blue px-4 py-2 fw-bold">
                    <i class="bi bi-save me-1"></i> Simpan Dokumen
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('script')
    <script>
        const officerOptions = [
            @foreach ($officers as $off)
                @php
                    $pangkat = $off->rank->name ?? ($off->rank_name ?? ($off->pangkat ?? ''));
                    $nrp = $off->nrp ?: ($off->register_number ?: ($off->id ?: ''));
                    $jabatan = is_object($off->position) ? ($off->position->name ?? '') : (is_array($off->position) ? ($off->position['name'] ?? '') : ($off->position ?? ''));
                    if (is_string($jabatan) && str_starts_with(trim($jabatan), '{')) {
                        $dec = json_decode(trim($jabatan), true);
                        $jabatan = $dec['name'] ?? $jabatan;
                    }
                    $offNama = $off->full_name ?: $off->name;
                @endphp
                {
                    id: "{{ $off->id }}",
                    nama: {!! json_encode($offNama) !!},
                    pangkat: {!! json_encode($pangkat) !!},
                    nrp: {!! json_encode($nrp) !!},
                    jabatan: {!! json_encode($jabatan ?: 'BANIT GAKKUM SATLANTAS') !!},
                    label: {!! json_encode($offNama . ' (' . $pangkat . ' / NRP ' . $nrp . ')') !!}
                },
            @endforeach
        ];

        function buildOfficerSelectHtml(nameAttr, selectedNama) {
            var html = `<select name="${nameAttr}" class="form-select form-select-sm off-select-officer" required>`;
            html += '<option value="">-- Pilih Petugas --</option>';
            officerOptions.forEach(function(o) {
                var isSel = (selectedNama && selectedNama === o.nama) ? 'selected' : '';
                html += `<option value="${o.nama}" data-nama="${o.nama}" data-pangkat="${o.pangkat}" data-nrp="${o.nrp}" data-jabatan="${o.jabatan}" ${isSel}>${o.label}</option>`;
            });
            html += '</select>';
            return html;
        }

        $(document).ready(function() {
            // Auto fill Penerima Perintah
            $('#selectPenerima').on('change', function() {
                var opt = $(this).find(':selected');
                if (opt.val()) {
                    $('#penerima_nama').val(opt.data('nama') || '');
                    $('#penerima_pangkat').val(opt.data('pangkat') || '');
                    $('#penerima_nrp').val(opt.data('nrp') || '');
                    $('#penerima_jabatan').val(opt.data('jabatan') || 'Penyidik Pembantu');
                }
            });

            // Auto fill Atasan Penyidik
            $('#selectSignatory').on('change', function() {
                var opt = $(this).find(':selected');
                if (opt.val()) {
                    $('#atasan_nama').val(opt.data('nama') || '');
                    $('#atasan_pangkat').val(opt.data('pangkat') || '');
                    $('#atasan_nrp').val(opt.data('nrp') || '');
                    $('#atasan_jabatan').val(opt.data('jabatan') || 'ATASAN PENYIDIK');
                }
            });

            // Event saat dropdown petugas di tabel berubah
            $(document).on('change', '.off-select-officer', function() {
                var tr = $(this).closest('tr');
                var opt = $(this).find(':selected');
                if (opt.val()) {
                    tr.find('.off-pangkat').val(opt.data('pangkat') || '');
                    tr.find('.off-nrp').val(opt.data('nrp') || '');
                    tr.find('.off-jabatan').val(opt.data('jabatan') || 'BANIT GAKKUM SATLANTAS');
                } else {
                    tr.find('.off-pangkat').val('');
                    tr.find('.off-nrp').val('');
                    tr.find('.off-jabatan').val('');
                }
            });

            // Quick add officer from top dropdown
            $('#selectQuickOfficer').on('change', function() {
                var opt = $(this).find(':selected');
                if (opt.val()) {
                    var rowCount = $('#tableOfficers tbody tr').length;
                    var selectHtml = buildOfficerSelectHtml(`officers[${rowCount}][nama]`, opt.data('nama'));
                    var newRow = `
                        <tr>
                            <td class="text-center row-num">${rowCount + 1}</td>
                            <td>${selectHtml}</td>
                            <td>
                                <input type="text" name="officers[${rowCount}][pangkat]" class="form-control form-control-sm off-pangkat"
                                    value="${opt.data('pangkat') || ''}" placeholder="Pangkat" readonly style="background-color: #f1f5f9; cursor: not-allowed;" required>
                            </td>
                            <td>
                                <input type="text" name="officers[${rowCount}][nrp]" class="form-control form-control-sm off-nrp"
                                    value="${opt.data('nrp') || ''}" placeholder="NRP" readonly style="background-color: #f1f5f9; cursor: not-allowed;" required>
                            </td>
                            <td>
                                <input type="text" name="officers[${rowCount}][jabatan]" class="form-control form-control-sm off-jabatan"
                                    value="${opt.data('jabatan') || 'BANIT GAKKUM SATLANTAS'}" placeholder="Jabatan" required>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    $('#tableOfficers tbody').append(newRow);
                    $(this).val('');
                }
            });

            // Tambah Petugas manual (langsung tampil dropdown di baris baru)
            $('#btnAddOfficer').on('click', function() {
                var rowCount = $('#tableOfficers tbody tr').length;
                var selectHtml = buildOfficerSelectHtml(`officers[${rowCount}][nama]`, '');
                var newRow = `
                    <tr>
                        <td class="text-center row-num">${rowCount + 1}</td>
                        <td>${selectHtml}</td>
                        <td>
                            <input type="text" name="officers[${rowCount}][pangkat]" class="form-control form-control-sm off-pangkat"
                                placeholder="Pangkat" readonly style="background-color: #f1f5f9; cursor: not-allowed;" required>
                        </td>
                        <td>
                            <input type="text" name="officers[${rowCount}][nrp]" class="form-control form-control-sm off-nrp"
                                placeholder="NRP" readonly style="background-color: #f1f5f9; cursor: not-allowed;" required>
                        </td>
                        <td>
                            <input type="text" name="officers[${rowCount}][jabatan]" class="form-control form-control-sm off-jabatan"
                                value="BANIT GAKKUM SATLANTAS" placeholder="Jabatan" required>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#tableOfficers tbody').append(newRow);
            });

            // Hapus Petugas
            $(document).on('click', '.btn-delete-row', function() {
                if ($('#tableOfficers tbody tr').length > 1) {
                    $(this).closest('tr').remove();
                    $('#tableOfficers tbody tr').each(function(index) {
                        $(this).find('.row-num').text(index + 1);
                        $(this).find('.off-select-officer').attr('name', `officers[${index}][nama]`);
                        $(this).find('.off-pangkat').attr('name', `officers[${index}][pangkat]`);
                        $(this).find('.off-nrp').attr('name', `officers[${index}][nrp]`);
                        $(this).find('.off-jabatan').attr('name', `officers[${index}][jabatan]`);
                    });
                } else {
                    alert('Minimal harus ada 1 petugas dalam daftar penugasan.');
                }
            });
        });
    </script>
@endpush

