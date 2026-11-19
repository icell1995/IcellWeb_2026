@php
    $_title = 'Surat Perintah Penangkapan';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
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
            <h5 class="fw-bold m-0">Tambah Surat Perintah Penangkapan</h5>

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
            <form action="{{ route('doc.surat-perintah-penangkapan-document.store') }}" method="POST" id="formSpKap">
                @csrf
                <input type="hidden" name="accident_id" id="accident_id" value="{{ $accidentId }}">

                {{-- ─── I. IDENTITAS SURAT & DASAR DOKUMEN ─── --}}
                <div class="card card-outline card-primary mb-4 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="card-title fw-bold m-0">I. Identitas Surat & Dasar Dokumen<span class="text-danger">*</span></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="nomor_surat">Nomor SP.Kap<span class="text-danger">*</span></label>
                                <input type="text" name="nomor_surat" id="nomor_surat" class="form-control"
                                    value="{{ old('nomor_surat', $defaultNomorSpKap) }}" required placeholder="Contoh: SP.Kap/01/X/2026/Satlantas">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="tanggal_surat">Tanggal Surat SP.Kap<span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_surat" id="tanggal_surat" class="form-control"
                                    value="{{ old('tanggal_surat', $todayFormatted) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="dikeluarkan_di">Dikeluarkan di (Kota)<span class="text-danger">*</span></label>
                                <input type="text" name="dikeluarkan_di" id="dikeluarkan_di" class="form-control"
                                    value="{{ old('dikeluarkan_di', $accident->polres->polres_district ?? 'Tempat') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="kopstuk_1">Kesatuan / Polda (Kop Baris 1)<span class="text-danger">*</span></label>
                                <input type="text" name="kopstuk_1" id="kopstuk_1" class="form-control"
                                    value="{{ old('kopstuk_1', $poldaName) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="kopstuk_2">Satuan / Resor (Kop Baris 2)<span class="text-danger">*</span></label>
                                <input type="text" name="kopstuk_2" id="kopstuk_2" class="form-control"
                                    value="{{ old('kopstuk_2', $resorName) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="kopstuk_3">Alamat Satker (Kop Baris 3)</label>
                                <input type="text" name="kopstuk_3" id="kopstuk_3" class="form-control"
                                    value="{{ old('kopstuk_3', $resorAddress) }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ─── II. PERTIMBANGAN & DASAR HUKUM ─── --}}
                <div class="card card-outline card-primary mb-4 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="card-title fw-bold m-0">II. Pertimbangan & Dasar Hukum<span class="text-danger">*</span></h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="pertimbangan">Pertimbangan<span class="text-danger">*</span></label>
                            <textarea name="pertimbangan" id="pertimbangan" class="form-control" rows="2">{{ old('pertimbangan', 'bahwa untuk kepentingan penyidikan, terhadap Tersangka yang dikuatirkan melarikan diri atau merusak barang bukti atau menghilangkan barang bukti harus dilakukan penangkapan, maka perlu dikeluarkan surat perintah.') }}</textarea>
                        </div>

                        <div class="section-subhead">Rujukan Dokumen Dasar Penyidikan :</div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="dasar_kuhap">1. Dasar Hukum KUHAP<span class="text-danger">*</span></label>
                                <input type="text" name="dasar_kuhap" id="dasar_kuhap" class="form-control"
                                    value="{{ old('dasar_kuhap', 'Pasal 5 ayat (1) huruf b angka 1, Pasal 7 ayat (1) huruf d, Pasal 16, Pasal 17, Pasal 18, Pasal 19 dan Pasal 37 Undang-Undang Nomor 8 Tahun 1981 tentang Hukum Acara Pidana') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="dasar_polri">2. Dasar Hukum UU Kepolisian<span class="text-danger">*</span></label>
                                <input type="text" name="dasar_polri" id="dasar_polri" class="form-control"
                                    value="{{ old('dasar_polri', 'Pasal 14 ayat (1) huruf g pasal 16 ayat (1) Undang-Undang Nomor 2 Tahun 2002 tentang Kepolisian Negara Republik Indonesia') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="dasar_uu_pidana">3. Dasar UU Kasus / Pidana Laka<span class="text-danger">*</span></label>
                                <input type="text" name="dasar_uu_pidana" id="dasar_uu_pidana" class="form-control"
                                    value="{{ old('dasar_uu_pidana', 'Pasal 310 Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label" for="dasar_lp_nomor">4. Nomor Laporan Polisi (LP)<span class="text-danger">*</span></label>
                                <input type="text" name="dasar_lp_nomor" id="dasar_lp_nomor" class="form-control"
                                    value="{{ old('dasar_lp_nomor', $accident->no_lp) }}" placeholder="Nomor LP">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="dasar_lp_tanggal">Tanggal LP<span class="text-danger">*</span></label>
                                <input type="date" name="dasar_lp_tanggal" id="dasar_lp_tanggal" class="form-control"
                                    value="{{ old('dasar_lp_tanggal', $accident->lp_date ? \Carbon\Carbon::parse($accident->lp_date)->format('Y-m-d') : $todayFormatted) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label" for="dasar_sp_sidik_nomor">5. Nomor Surat Perintah Penyidikan (SP.Sidik)<span class="text-danger">*</span></label>
                                <input type="text" name="dasar_sp_sidik_nomor" id="dasar_sp_sidik_nomor" class="form-control"
                                    value="{{ old('dasar_sp_sidik_nomor', $sprindik->nomor ?? ($sprindik->document_number ?? '')) }}" placeholder="Nomor SP.Sidik">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="dasar_sp_sidik_tanggal">Tanggal SP.Sidik<span class="text-danger">*</span></label>
                                <input type="date" name="dasar_sp_sidik_tanggal" id="dasar_sp_sidik_tanggal" class="form-control"
                                    value="{{ old('dasar_sp_sidik_tanggal', $sprindik && $sprindik->tanggal ? \Carbon\Carbon::parse($sprindik->tanggal)->format('Y-m-d') : '') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label" for="dasar_sp_gas_nomor">6. Nomor Surat Perintah Tugas (SP.Gas)<span class="text-danger">*</span></label>
                                <input type="text" name="dasar_sp_gas_nomor" id="dasar_sp_gas_nomor" class="form-control"
                                    value="{{ old('dasar_sp_gas_nomor', $springas->nomor ?? ($springas->document_number ?? '')) }}" placeholder="Nomor SP.Gas">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="dasar_sp_gas_tanggal">Tanggal SP.Gas<span class="text-danger">*</span></label>
                                <input type="date" name="dasar_sp_gas_tanggal" id="dasar_sp_gas_tanggal" class="form-control"
                                    value="{{ old('dasar_sp_gas_tanggal', $springas && $springas->tanggal ? \Carbon\Carbon::parse($springas->tanggal)->format('Y-m-d') : '') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="dasar_sp_tap_nomor">7. Nomor S.Tap Tersangka<span class="text-danger">*</span></label>
                                <input type="text" name="dasar_sp_tap_nomor" id="dasar_sp_tap_nomor" class="form-control"
                                    value="{{ old('dasar_sp_tap_nomor', $sTapTersangka->nomor ?? ($sTapTersangka->document_number ?? '')) }}" placeholder="Nomor S.Tap">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="dasar_sp_tap_tanggal">Tanggal S.Tap<span class="text-danger">*</span></label>
                                <input type="date" name="dasar_sp_tap_tanggal" id="dasar_sp_tap_tanggal" class="form-control"
                                    value="{{ old('dasar_sp_tap_tanggal', $sTapTersangka && $sTapTersangka->tanggal ? \Carbon\Carbon::parse($sTapTersangka->tanggal)->format('Y-m-d') : '') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="dasar_sp_tap_nama">Atas Nama Tersangka<span class="text-danger">*</span></label>
                                <input type="text" name="dasar_sp_tap_nama" id="dasar_sp_tap_nama" class="form-control"
                                    value="{{ old('dasar_sp_tap_nama', $firstSuspect['nama'] ?? '') }}" placeholder="Nama Tersangka">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ─── III. PETUGAS PELAKSANA PENANGKAPAN (KEPADA) ─── --}}
                <div class="card card-outline card-primary mb-4 shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="card-title fw-bold m-0">III. Petugas Pelaksana Penangkapan (Kepada)<span class="text-danger">*</span></h6>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnAddOfficer">
                            Tambah Petugas
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-officers mb-2" id="tableOfficers">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Nama Petugas</th>
                                        <th style="width: 220px;">Pangkat</th>
                                        <th style="width: 180px;">NRP</th>
                                        <th style="width: 220px;">Jabatan</th>
                                        <th style="width: 60px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $officersData = old('officers', $defaultOfficers);
                                    @endphp
                                    @foreach ($officersData as $idx => $off)
                                        <tr>
                                            <td class="text-center row-num">{{ $loop->iteration }}</td>
                                            <td>
                                                <input type="text" name="officers[{{ $idx }}][nama]" class="form-control form-control-sm off-name"
                                                    value="{{ $off['nama'] ?? '' }}" placeholder="Nama Petugas" required>
                                            </td>
                                            <td>
                                                <input type="text" name="officers[{{ $idx }}][pangkat]" class="form-control form-control-sm off-pangkat"
                                                    value="{{ $off['pangkat'] ?? '' }}" placeholder="Pangkat">
                                            </td>
                                            <td>
                                                <input type="text" name="officers[{{ $idx }}][nrp]" class="form-control form-control-sm off-nrp"
                                                    value="{{ $off['nrp'] ?? '' }}" placeholder="NRP">
                                            </td>
                                            <td>
                                                @php
                                                    $offJabatan = $off['jabatan'] ?? 'Penyidik';
                                                    if (is_array($offJabatan)) {
                                                        $offJabatan = $offJabatan['name'] ?? 'Penyidik';
                                                    } elseif (is_object($offJabatan)) {
                                                        $offJabatan = $offJabatan->name ?? 'Penyidik';
                                                    } elseif (is_string($offJabatan) && str_starts_with(trim($offJabatan), '{')) {
                                                        $dec = json_decode(trim($offJabatan), true);
                                                        $offJabatan = $dec['name'] ?? $offJabatan;
                                                    }
                                                @endphp
                                                <input type="text" name="officers[{{ $idx }}][jabatan]" class="form-control form-control-sm off-jabatan"
                                                    value="{{ $offJabatan }}" placeholder="Jabatan">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm btn-delete-row" title="Hapus">&times;</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ─── IV. IDENTITAS TERSANGKA & URAIAN TINDAK PIDANA (UNTUK) ─── --}}
                <div class="card card-outline card-primary mb-4 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="card-title fw-bold m-0">IV. Identitas Tersangka & Uraian Tindak Pidana (Untuk)<span class="text-danger">*</span></h6>
                    </div>
                    <div class="card-body">
                        @if (!empty($availableSuspects))
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label" for="selectSuspectAuto">Pilih Tersangka dari Perkara LP (Auto-fill)</label>
                                    <select class="form-select" id="selectSuspectAuto">
                                        <option value="">-- Pilih Tersangka --</option>
                                        @foreach ($availableSuspects as $sIdx => $sItem)
                                            <option value="{{ $sIdx }}">{{ $sItem['nama'] }} (NIK: {{ $sItem['no_identitas'] }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif

                        <input type="hidden" name="tersangka_id" id="tersangka_id" value="{{ old('tersangka_id', $firstSuspect['source_id'] ?? '') }}">

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label" for="tersangka_nama">Nama Lengkap Tersangka<span class="text-danger">*</span></label>
                                <input type="text" name="tersangka_nama" id="tersangka_nama" class="form-control"
                                    value="{{ old('tersangka_nama', $firstSuspect['nama'] ?? '') }}" required placeholder="Nama Lengkap">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="tersangka_nik">Nomor Identitas (NIK / No KTP)</label>
                                <input type="text" name="tersangka_nik" id="tersangka_nik" class="form-control"
                                    value="{{ old('tersangka_nik', $firstSuspect['no_identitas'] ?? '') }}" placeholder="NIK">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="tersangka_kewarganegaraan">Kewarganegaraan<span class="text-danger">*</span></label>
                                <input type="text" name="tersangka_kewarganegaraan" id="tersangka_kewarganegaraan" class="form-control"
                                    value="{{ old('tersangka_kewarganegaraan', $firstSuspect['kewarganegaraan'] ?? 'Indonesia') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="tersangka_jenis_kelamin">Jenis Kelamin<span class="text-danger">*</span></label>
                                <select name="tersangka_jenis_kelamin" id="tersangka_jenis_kelamin" class="form-select">
                                    <option value="Laki-laki" {{ old('tersangka_jenis_kelamin', $firstSuspect['jenis_kelamin'] ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ old('tersangka_jenis_kelamin', $firstSuspect['jenis_kelamin'] ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="tersangka_agama">Agama<span class="text-danger">*</span></label>
                                <input type="text" name="tersangka_agama" id="tersangka_agama" class="form-control"
                                    value="{{ old('tersangka_agama', $firstSuspect['agama'] ?? 'Islam') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="tersangka_tempat_lahir">Tempat Lahir</label>
                                <input type="text" name="tersangka_tempat_lahir" id="tersangka_tempat_lahir" class="form-control"
                                    value="{{ old('tersangka_tempat_lahir', $firstSuspect['tempat_lahir'] ?? '') }}" placeholder="Tempat Lahir">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="tersangka_tanggal_lahir">Tanggal Lahir</label>
                                <input type="date" name="tersangka_tanggal_lahir" id="tersangka_tanggal_lahir" class="form-control"
                                    value="{{ old('tersangka_tanggal_lahir', $firstSuspect && $firstSuspect['tanggal_lahir'] ? \Carbon\Carbon::parse($firstSuspect['tanggal_lahir'])->format('Y-m-d') : '') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="tersangka_pekerjaan">Pekerjaan</label>
                                <input type="text" name="tersangka_pekerjaan" id="tersangka_pekerjaan" class="form-control"
                                    value="{{ old('tersangka_pekerjaan', $firstSuspect['pekerjaan'] ?? '-') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="tersangka_alamat">Alamat Tempat Tinggal<span class="text-danger">*</span></label>
                                <textarea name="tersangka_alamat" id="tersangka_alamat" class="form-control" rows="2">{{ old('tersangka_alamat', $firstSuspect['alamat'] ?? '-') }}</textarea>
                            </div>
                        </div>

                        <div class="section-subhead">Uraian Kasus & Tindak Pidana :</div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="uraian_tindak_pidana">Uraian Dugaan Tindak Pidana<span class="text-danger">*</span></label>
                                <input type="text" name="uraian_tindak_pidana" id="uraian_tindak_pidana" class="form-control"
                                    value="{{ old('uraian_tindak_pidana', 'kejahatan lalu lintas kecelakaan yang mengakibatkan orang lain meninggal dunia / luka berat') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="pasal_pidana">Pasal Pidana<span class="text-danger">*</span></label>
                                <input type="text" name="pasal_pidana" id="pasal_pidana" class="form-control" placeholder="Pasal 310 ayat (4)"
                                    value="{{ old('pasal_pidana', '310 ayat (4)') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="uu_pidana_nomor">Nomor UU<span class="text-danger">*</span></label>
                                <input type="text" name="uu_pidana_nomor" id="uu_pidana_nomor" class="form-control" placeholder="22"
                                    value="{{ old('uu_pidana_nomor', '22') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="uu_pidana_tahun">Tahun UU<span class="text-danger">*</span></label>
                                <input type="text" name="uu_pidana_tahun" id="uu_pidana_tahun" class="form-control" placeholder="2009"
                                    value="{{ old('uu_pidana_tahun', '2009') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="uu_pidana_tentang">Perihal / Tentang UU<span class="text-danger">*</span></label>
                                <input type="text" name="uu_pidana_tentang" id="uu_pidana_tentang" class="form-control"
                                    value="{{ old('uu_pidana_tentang', 'Lalu Lintas dan Angkutan Jalan') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label" for="tkp">TKP (Tempat Kejadian Perkara)<span class="text-danger">*</span></label>
                                <input type="text" name="tkp" id="tkp" class="form-control"
                                    value="{{ old('tkp', $accident->address_accident_1 ?: 'Jalan Raya') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="waktu_kejadian">Waktu Kejadian<span class="text-danger">*</span></label>
                                <input type="text" name="waktu_kejadian" id="waktu_kejadian" class="form-control"
                                    value="{{ old('waktu_kejadian', $namaHariIni) }}" placeholder="Contoh: Senin">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ─── V. MASA BERLAKU & KETENTUAN PERINTAH ─── --}}
                <div class="card card-outline card-primary mb-4 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="card-title fw-bold m-0">V. Masa Berlaku & Ketentuan Tambahan<span class="text-danger">*</span></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="perintah_penggeledahan">Perintah Penggeledahan (Poin 2)<span class="text-danger">*</span></label>
                                <input type="text" name="perintah_penggeledahan" id="perintah_penggeledahan" class="form-control"
                                    value="{{ old('perintah_penggeledahan', 'melakukan penggeledahan badan/pakaian Tersangka') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="berlaku_mulai">Berlaku Sejak Tanggal<span class="text-danger">*</span></label>
                                <input type="date" name="berlaku_mulai" id="berlaku_mulai" class="form-control"
                                    value="{{ old('berlaku_mulai', $todayFormatted) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="berlaku_sampai_date">Sampai Dengan Tanggal (Poin 3)<span class="text-danger">*</span></label>
                                <input type="date" name="berlaku_sampai_date" id="berlaku_sampai_date" class="form-control"
                                    value="{{ old('berlaku_sampai_date', $validUntilFormatted) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="perintah_laporan">Kewajiban Pelaporan & Pembuatan BA (Poin 4)<span class="text-danger">*</span></label>
                                <textarea name="perintah_laporan" id="perintah_laporan" class="form-control" rows="2">{{ old('perintah_laporan', 'setelah melaksanakan perintah ini dengan saksama dan penuh tanggung jawab agar segera melaporkan kepada Atasan Penyidik serta membuat Berita Acara Penangkapan serta Berita Acara Penggeledahan Badan/Pakaian.') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ─── VI. PENANDATANGAN PERINTAH ─── --}}
                <div class="card card-outline card-primary mb-4 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="card-title fw-bold m-0">VI. Penandatangan Perintah<span class="text-danger">*</span></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Kiri: Yang Menerima Perintah --}}
                            <div class="col-md-6 border-end pe-md-4">
                                <div class="section-subhead mt-0 mb-3 text-center">Yang Menerima Perintah</div>

                                @if ($officers->isNotEmpty())
                                    <div class="mb-3">
                                        <label class="form-label" for="selectPenerima">Pilih Petugas Penerima Perintah</label>
                                        <select class="form-select" id="selectPenerima">
                                            <option value="">-- Pilih Petugas Penerima --</option>
                                            @foreach ($officers as $off)
                                                @php
                                                    $posPenerima = is_object($off->position) ? ($off->position->name ?? 'Penyidik Pembantu') : (is_array($off->position) ? ($off->position['name'] ?? 'Penyidik Pembantu') : ($off->position ?? 'Penyidik Pembantu'));
                                                    if (is_string($posPenerima) && str_starts_with(trim($posPenerima), '{')) {
                                                        $dec = json_decode(trim($posPenerima), true);
                                                        $posPenerima = $dec['name'] ?? $posPenerima;
                                                    }
                                                @endphp
                                                <option value="{{ $off->id }}"
                                                    data-nama="{{ $off->full_name ?? ($off->name ?? '') }}"
                                                    data-pangkat="{{ $off->rank->name ?? ($off->rank_name ?? '') }}"
                                                    data-nrp="{{ $off->nrp ?? '' }}"
                                                    data-jabatan="{{ $posPenerima }}">
                                                    {{ $off->full_name ?? ($off->name ?? '') }} ({{ $off->nrp ?? '-' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label class="form-label" for="penerima_nama">Nama Petugas<span class="text-danger">*</span></label>
                                    <input type="text" name="penerima_nama" id="penerima_nama" class="form-control"
                                        value="{{ old('penerima_nama', $defaultOfficers[0]['nama'] ?? '') }}" placeholder="Nama Penerima Perintah" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="penerima_pangkat">Pangkat</label>
                                    <input type="text" name="penerima_pangkat" id="penerima_pangkat" class="form-control"
                                        value="{{ old('penerima_pangkat', $defaultOfficers[0]['pangkat'] ?? '') }}" placeholder="Pangkat">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="penerima_nrp">NRP</label>
                                    <input type="text" name="penerima_nrp" id="penerima_nrp" class="form-control"
                                        value="{{ old('penerima_nrp', $defaultOfficers[0]['nrp'] ?? '') }}" placeholder="NRP">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="penerima_jabatan">Jabatan</label>
                                    @php
                                        $valPenerimaJabatan = old('penerima_jabatan', $defaultOfficers[0]['jabatan'] ?? 'Penyidik Pembantu');
                                        if (is_array($valPenerimaJabatan)) {
                                            $valPenerimaJabatan = $valPenerimaJabatan['name'] ?? 'Penyidik Pembantu';
                                        } elseif (is_object($valPenerimaJabatan)) {
                                            $valPenerimaJabatan = $valPenerimaJabatan->name ?? 'Penyidik Pembantu';
                                        } elseif (is_string($valPenerimaJabatan) && str_starts_with(trim($valPenerimaJabatan), '{')) {
                                            $dec = json_decode(trim($valPenerimaJabatan), true);
                                            $valPenerimaJabatan = $dec['name'] ?? $valPenerimaJabatan;
                                        }
                                    @endphp
                                    <input type="text" name="penerima_jabatan" id="penerima_jabatan" class="form-control"
                                        value="{{ $valPenerimaJabatan }}" placeholder="Jabatan">
                                </div>
                            </div>

                            {{-- Kanan: ATASAN PENYIDIK --}}
                            <div class="col-md-6 ps-md-4">
                                <div class="section-subhead mt-0 mb-3 text-center">ATASAN PENYIDIK</div>

                                @if ($authorizedSignatories->isNotEmpty())
                                    <div class="mb-3">
                                        <label class="form-label" for="selectSignatory">Pilih Pejabat Penandatangan</label>
                                        <select class="form-select" id="selectSignatory">
                                            <option value="">-- Pilih Pejabat Penandatangan --</option>
                                            @foreach ($authorizedSignatories as $sig)
                                                @php
                                                    $posSig = is_object($sig->position) ? ($sig->position->name ?? 'ATASAN PENYIDIK') : (is_array($sig->position) ? ($sig->position['name'] ?? 'ATASAN PENYIDIK') : ($sig->position ?? 'ATASAN PENYIDIK'));
                                                    if (is_string($posSig) && str_starts_with(trim($posSig), '{')) {
                                                        $dec = json_decode(trim($posSig), true);
                                                        $posSig = $dec['name'] ?? $posSig;
                                                    }
                                                @endphp
                                                <option value="{{ $sig->id }}"
                                                    data-nama="{{ $sig->full_name ?? ($sig->name ?? '') }}"
                                                    data-pangkat="{{ $sig->rank->name ?? ($sig->rank_name ?? '') }}"
                                                    data-nrp="{{ $sig->nrp ?? '' }}"
                                                    data-jabatan="{{ $posSig }}">
                                                    {{ $sig->full_name ?? ($sig->name ?? '') }} ({{ $sig->nrp ?? '-' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label class="form-label" for="atasan_nama">Nama Atasan Penyidik<span class="text-danger">*</span></label>
                                    <input type="text" name="atasan_nama" id="atasan_nama" class="form-control"
                                        value="{{ old('atasan_nama', $authorizedSignatories->first()->full_name ?? ($authorizedSignatories->first()->name ?? '')) }}" placeholder="Nama Atasan" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="atasan_pangkat">Pangkat</label>
                                    <input type="text" name="atasan_pangkat" id="atasan_pangkat" class="form-control"
                                        value="{{ old('atasan_pangkat', $authorizedSignatories->first()->rank->name ?? ($authorizedSignatories->first()->rank_name ?? '')) }}" placeholder="Pangkat">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="atasan_nrp">NRP</label>
                                    <input type="text" name="atasan_nrp" id="atasan_nrp" class="form-control"
                                        value="{{ old('atasan_nrp', $authorizedSignatories->first()->nrp ?? '') }}" placeholder="NRP">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="atasan_jabatan">Jabatan</label>
                                    @php
                                        $firstSig = $authorizedSignatories->first();
                                        $defaultSigPos = $firstSig ? (is_object($firstSig->position) ? ($firstSig->position->name ?? 'ATASAN PENYIDIK') : (is_array($firstSig->position) ? ($firstSig->position['name'] ?? 'ATASAN PENYIDIK') : ($firstSig->position ?? 'ATASAN PENYIDIK'))) : 'ATASAN PENYIDIK';
                                        if (is_string($defaultSigPos) && str_starts_with(trim($defaultSigPos), '{')) {
                                            $dec = json_decode(trim($defaultSigPos), true);
                                            $defaultSigPos = $dec['name'] ?? $defaultSigPos;
                                        }
                                        $valAtasanJabatan = old('atasan_jabatan', $defaultSigPos);
                                    @endphp
                                    <input type="text" name="atasan_jabatan" id="atasan_jabatan" class="form-control"
                                        value="{{ $valAtasanJabatan }}" placeholder="Jabatan">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ─── VII. LEMBAR TANDA TERIMA PENYERAHAN SURAT ─── --}}
                <div class="card card-outline card-primary mb-4 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="card-title fw-bold m-0">VII. Lembar Tanda Terima Penyerahan Surat<span class="text-danger">*</span></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="penyerahan_hari">Hari Penyerahan<span class="text-danger">*</span></label>
                                <input type="text" name="penyerahan_hari" id="penyerahan_hari" class="form-control"
                                    value="{{ old('penyerahan_hari', $namaHariIni) }}" placeholder="Contoh: Senin">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="penyerahan_tanggal">Tanggal Penyerahan<span class="text-danger">*</span></label>
                                <input type="date" name="penyerahan_tanggal" id="penyerahan_tanggal" class="form-control"
                                    value="{{ old('penyerahan_tanggal', $todayFormatted) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 border-end pe-md-4">
                                <div class="section-subhead mt-0 mb-3 text-center">Yang Menerima (Tersangka/Keluarga/Kuasa Hukum)</div>
                                <div class="mb-3">
                                    <label class="form-label" for="yang_menerima_nama">Nama Yang Menerima<span class="text-danger">*</span></label>
                                    <input type="text" name="yang_menerima_nama" id="yang_menerima_nama" class="form-control"
                                        value="{{ old('yang_menerima_nama', $firstSuspect['nama'] ?? '') }}" placeholder="Nama Penerima Surat" required>
                                </div>
                            </div>

                            <div class="col-md-6 ps-md-4">
                                <div class="section-subhead mt-0 mb-3 text-center">Yang Menyerahkan (Petugas Penyidik)</div>

                                @if ($officers->isNotEmpty())
                                    <div class="mb-3">
                                        <label class="form-label" for="selectPenyerah">Pilih Petugas Penyerah</label>
                                        <select class="form-select" id="selectPenyerah">
                                            <option value="">-- Pilih Petugas Penyerah --</option>
                                            @foreach ($officers as $off)
                                                <option value="{{ $off->id }}"
                                                    data-nama="{{ $off->full_name ?? ($off->name ?? '') }}"
                                                    data-pangkat="{{ $off->rank->name ?? ($off->rank_name ?? '') }}"
                                                    data-nrp="{{ $off->nrp ?? '' }}">
                                                    {{ $off->full_name ?? ($off->name ?? '') }} ({{ $off->nrp ?? '-' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label class="form-label" for="yang_menyerahkan_nama">Nama Petugas Penyerah<span class="text-danger">*</span></label>
                                    <input type="text" name="yang_menyerahkan_nama" id="yang_menyerahkan_nama" class="form-control"
                                        value="{{ old('yang_menyerahkan_nama', $defaultOfficers[0]['nama'] ?? '') }}" placeholder="Nama Petugas Penyerah" required>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6 mb-3">
                                        <label class="form-label" for="yang_menyerahkan_pangkat">Pangkat</label>
                                        <input type="text" name="yang_menyerahkan_pangkat" id="yang_menyerahkan_pangkat" class="form-control"
                                            value="{{ old('yang_menyerahkan_pangkat', $defaultOfficers[0]['pangkat'] ?? '') }}" placeholder="Pangkat">
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <label class="form-label" for="yang_menyerahkan_nrp">NRP</label>
                                        <input type="text" name="yang_menyerahkan_nrp" id="yang_menyerahkan_nrp" class="form-control"
                                            value="{{ old('yang_menyerahkan_nrp', $defaultOfficers[0]['nrp'] ?? '') }}" placeholder="NRP">
                                    </div>
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
        $(document).ready(function() {
            // Auto fill data tersangka
            const suspectsData = @json($availableSuspects);
            $('#selectSuspectAuto').on('change', function() {
                const idx = $(this).val();
                if (idx !== '' && suspectsData[idx]) {
                    const s = suspectsData[idx];
                    $('#tersangka_id').val(s.source_id || '');
                    $('#tersangka_nama').val(s.nama || '');
                    $('#tersangka_nik').val(s.no_identitas || '');
                    $('#tersangka_kewarganegaraan').val(s.kewarganegaraan || 'Indonesia');
                    if (s.jenis_kelamin) {
                        $('#tersangka_jenis_kelamin').val(s.jenis_kelamin);
                    }
                    $('#tersangka_tempat_lahir').val(s.tempat_lahir || '');
                    if (s.tanggal_lahir) {
                        $('#tersangka_tanggal_lahir').val(s.tanggal_lahir.substring(0, 10));
                    }
                    $('#tersangka_pekerjaan').val(s.pekerjaan || '');
                    $('#tersangka_agama').val(s.agama || 'Islam');
                    $('#tersangka_alamat').val(s.alamat || '');

                    // Sync dengan lembar penyerahan
                    if (!$('#yang_menerima_nama').val()) {
                        $('#yang_menerima_nama').val(s.nama || '');
                    }
                    if (!$('#dasar_sp_tap_nama').val()) {
                        $('#dasar_sp_tap_nama').val(s.nama || '');
                    }
                }
            });

            // Auto fill Yang Menerima Perintah dari Dropdown Petugas
            $('#selectPenerima').on('change', function() {
                const opt = $(this).find(':selected');
                if (opt.val()) {
                    const nama = opt.data('nama') || '';
                    const pangkat = opt.data('pangkat') || '';
                    const nrp = opt.data('nrp') || '';
                    let jabatan = opt.data('jabatan') || 'Penyidik Pembantu';
                    if (typeof jabatan === 'object' && jabatan !== null) {
                        jabatan = jabatan.name || 'Penyidik Pembantu';
                    }

                    $('#penerima_nama').val(nama);
                    $('#penerima_pangkat').val(pangkat);
                    $('#penerima_nrp').val(nrp);
                    $('#penerima_jabatan').val(jabatan);

                    // Sync otomatis dengan dropdown dan input yang menyerahkan di lembar penyerahan
                    $('#selectPenyerah').val(opt.val());
                    $('#yang_menyerahkan_nama').val(nama);
                    $('#yang_menyerahkan_pangkat').val(pangkat);
                    $('#yang_menyerahkan_nrp').val(nrp);
                }
            });

            // Auto fill Yang Menyerahkan dari Dropdown Petugas Penyerah
            $('#selectPenyerah').on('change', function() {
                const opt = $(this).find(':selected');
                if (opt.val()) {
                    $('#yang_menyerahkan_nama').val(opt.data('nama') || '');
                    $('#yang_menyerahkan_pangkat').val(opt.data('pangkat') || '');
                    $('#yang_menyerahkan_nrp').val(opt.data('nrp') || '');
                }
            });

            // Auto fill data atasan penandatangan
            $('#selectSignatory').on('change', function() {
                const opt = $(this).find(':selected');
                if (opt.val()) {
                    let jabatan = opt.data('jabatan') || 'ATASAN PENYIDIK';
                    if (typeof jabatan === 'object' && jabatan !== null) {
                        jabatan = jabatan.name || 'ATASAN PENYIDIK';
                    }
                    $('#atasan_nama').val(opt.data('nama') || '');
                    $('#atasan_pangkat').val(opt.data('pangkat') || '');
                    $('#atasan_nrp').val(opt.data('nrp') || '');
                    $('#atasan_jabatan').val(jabatan);
                }
            });

            // Tambah Petugas (Kepada)
            $('#btnAddOfficer').on('click', function() {
                const rowCount = $('#tableOfficers tbody tr').length;
                const newRow = `
                    <tr>
                        <td class="text-center row-num">${rowCount + 1}</td>
                        <td>
                            <input type="text" name="officers[${rowCount}][nama]" class="form-control form-control-sm off-name" placeholder="Nama Petugas" required>
                        </td>
                        <td>
                            <input type="text" name="officers[${rowCount}][pangkat]" class="form-control form-control-sm off-pangkat" placeholder="Pangkat">
                        </td>
                        <td>
                            <input type="text" name="officers[${rowCount}][nrp]" class="form-control form-control-sm off-nrp" placeholder="NRP">
                        </td>
                        <td>
                            <input type="text" name="officers[${rowCount}][jabatan]" class="form-control form-control-sm off-jabatan" value="Penyidik" placeholder="Jabatan">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm btn-delete-row" title="Hapus">&times;</button>
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
                        $(this).find('.off-name').attr('name', `officers[${index}][nama]`);
                        $(this).find('.off-pangkat').attr('name', `officers[${index}][pangkat]`);
                        $(this).find('.off-nrp').attr('name', `officers[${index}][nrp]`);
                        $(this).find('.off-jabatan').attr('name', `officers[${index}][jabatan]`);
                    });
                } else {
                    alert('Minimal harus ada 1 petugas yang ditugaskan.');
                }
            });
        });
    </script>
@endpush
