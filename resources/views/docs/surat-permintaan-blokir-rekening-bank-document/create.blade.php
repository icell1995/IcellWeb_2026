@php
    $_title = 'Surat Permintaan Pemblokiran Rekening Bank';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
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
        .card-custom {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .card-custom .card-header {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}">
        <i class="bi bi-arrow-left"></i> Kembali ke Progress Perkara
    </a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Surat Permintaan Pemblokiran Rekening Bank</h5>
            <small class="text-muted d-block mb-3">Kode Dokumen: <b>DCT-0707</b> │ Kategori: <b>07 (Dokumen Umum / Pemblokiran Bank)</b></small>

            <div class="alert alert-info">
                <i class="bi bi-info-circle me-1"></i>
                Surat Permintaan Pemblokiran Rekening Bank diterbitkan oleh Penyidik kepada Pihak Bank untuk meminta pemblokiran rekening bank atas nama tersangka guna kepentingan penyidikan tindak pidana kecelakaan lalu lintas.
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="box-body">
            <form action="{{ route('doc.surat-permintaan-blokir-rekening-bank-document.store', ['accident_id' => $accidentId]) }}" method="POST">
                @csrf
                <input type="hidden" name="accident_id" value="{{ $accidentId }}">

                <!-- CARD 1: INFORMASI UMUM SURAT -->
                <div class="card card-custom">
                    <div class="card-header text-primary">
                        <i class="bi bi-envelope-paper me-1"></i> I. INFORMASI & NOMOR SURAT
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Nomor Surat</label>
                                <input type="text" name="nomor" class="form-control" placeholder="Contoh: B/...../XI/RES.1.24/2026/Satlantas" value="{{ old('nomor') }}">
                                <small class="text-muted">Bisa dikosongkan jika penomoran belum tersedia.</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Klasifikasi / Sifat Surat <span class="text-danger">*</span></label>
                                <select name="sifat" class="form-control custom-select" required>
                                    <option value="Biasa" {{ old('sifat', 'Biasa') == 'Biasa' ? 'selected' : '' }}>Biasa</option>
                                    <option value="Segera" {{ old('sifat') == 'Segera' ? 'selected' : '' }}>Segera</option>
                                    <option value="Rahasia" {{ old('sifat') == 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Lampiran</label>
                                <input type="text" name="lampiran" class="form-control" placeholder="Contoh: 1 (satu) lembar / -" value="{{ old('lampiran', '-') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Perihal Surat <span class="text-danger">*</span></label>
                                <input type="text" name="perihal" class="form-control" value="{{ old('perihal', 'Permintaan Pemblokiran Rekening Bank') }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">Tanggal Surat <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">Dikeluarkan di (Kota Terbit)</label>
                                <input type="text" name="tempat_surat" class="form-control" value="{{ old('tempat_surat', ucwords(strtolower($accident->polres->polres_district ?? ($accident->polres->name ?? 'Pasuruan')))) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: BANK TUJUAN -->
                <div class="card card-custom">
                    <div class="card-header text-primary">
                        <i class="bi bi-bank me-1"></i> II. PIHAK BANK TUJUAN (KEPADA YTH)
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label font-weight-bold">Pihak / Bank Tujuan (Kepada Yth.) <span class="text-danger">*</span></label>
                                <textarea name="bank_tujuan" class="form-control" rows="3" required placeholder="Contoh:&#10;Direktur Utama PT. Bank Rakyat Indonesia (Persero) Tbk.&#10;Up. Pimpinan Kantor Cabang Pasuruan">{{ old('bank_tujuan', $defaultBankTujuan) }}</textarea>
                                <small class="text-muted">Dapat diisi lebih dari satu baris.</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Tempat / Kota Bank Tujuan <span class="text-danger">*</span></label>
                                <input type="text" name="tempat_tujuan" class="form-control" value="{{ old('tempat_tujuan', $defaultTempatTujuan) }}" placeholder="Contoh: Pasuruan / Jakarta / Tempat" required>
                                <small class="text-muted">Akan dicetak di bawah kata "di".</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: DASAR HUKUM / RUJUKAN -->
                <div class="card card-custom">
                    <div class="card-header text-primary">
                        <i class="bi bi-book me-1"></i> III. DASAR HUKUM & RUJUKAN
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">a. Dasar KUHP</label>
                                <textarea name="dasar_kuhp" class="form-control" rows="2">{{ old('dasar_kuhp', $defaultDasarKuhp) }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">b. Dasar KUHAP</label>
                                <textarea name="dasar_kuhap" class="form-control" rows="2">{{ old('dasar_kuhap', $defaultDasarKuhap) }}</textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">c. Dasar UU Kepolisian</label>
                                <input type="text" name="dasar_polri" class="form-control" value="{{ old('dasar_polri', $defaultDasarPolri) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">d. Dasar UU Tindak Pidana (LLAJ / Khusus)</label>
                                <input type="text" name="dasar_uu_pidana" class="form-control" value="{{ old('dasar_uu_pidana', $defaultDasarUuPidana) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">e. Laporan Polisi</label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text">Nomor</span>
                                    <input type="text" name="dasar_lp_nomor" class="form-control" value="{{ old('dasar_lp_nomor', $noLp) }}">
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text">Tanggal</span>
                                    <input type="text" name="dasar_lp_tanggal" class="form-control" value="{{ old('dasar_lp_tanggal', $tglLp) }}">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">f. Surat Perintah Penyidikan</label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text">Nomor</span>
                                    <input type="text" name="dasar_sp_sidik_nomor" class="form-control" value="{{ old('dasar_sp_sidik_nomor', $nomorSprindik ?? '-') }}">
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text">Tanggal</span>
                                    <input type="text" name="dasar_sp_sidik_tanggal" class="form-control" value="{{ old('dasar_sp_sidik_tanggal', $tanggalSprindik ?? '-') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 4: PERKARA & IDENTITAS TERSANGKA -->
                <div class="card card-custom">
                    <div class="card-header text-primary">
                        <i class="bi bi-person-badge me-1"></i> IV. INFORMASI PERKARA & IDENTITAS TERSANGKA
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label font-weight-bold">Satuan Kerja Penyidik</label>
                                <input type="text" name="satker" class="form-control" value="{{ old('satker', 'Satuan Lalu Lintas ' . ($accident->polres->full_name ?? ($accident->polres->name ?? 'Polres'))) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-weight-bold">Dugaan Tindak Pidana</label>
                                <input type="text" name="tindak_pidana" class="form-control" value="{{ old('tindak_pidana', $tindakPidana) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-weight-bold">Pasal yang Dipersangkakan</label>
                                <input type="text" name="pasal_pidana" class="form-control" value="{{ old('pasal_pidana', $pasalPidana) }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label font-weight-bold">UU Nomor</label>
                                <input type="text" name="uu_nomor" class="form-control" value="{{ old('uu_nomor', $uuNomor) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label font-weight-bold">UU Tahun</label>
                                <input type="text" name="uu_tahun" class="form-control" value="{{ old('uu_tahun', $uuTahun) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">UU Tentang</label>
                                <input type="text" name="uu_tentang" class="form-control" value="{{ old('uu_tentang', $uuTentang) }}">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Tempat Kejadian Perkara (TKP)</label>
                                <input type="text" name="tkp" class="form-control" value="{{ old('tkp', $tkp) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Waktu Kejadian</label>
                                <input type="text" name="waktu_kejadian" class="form-control" value="{{ old('waktu_kejadian', $waktuKejadian) }}">
                            </div>
                        </div>

                        <hr>
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-person me-1"></i> Identitas Tersangka (Pemilik Rekening)</h6>

                        @if ($suspects->isNotEmpty())
                            <div class="mb-3">
                                <label class="form-label font-weight-bold text-success">Pilih Data Tersangka dari Database:</label>
                                <select id="select_suspect" name="suspect_id" class="form-control custom-select">
                                    <option value="">-- Pilih Tersangka --</option>
                                    @foreach ($suspects as $s)
                                        <option value="{{ $s->id }}"
                                            data-name="{{ $s->name }}"
                                            data-nik="{{ $s->nik ?: ($s->sim_number ?: '-') }}"
                                            data-gender="{{ $s->gender->name ?? 'Laki-laki' }}"
                                            data-ttl="{{ ($s->birth_place ?: '-') . ', ' . ($s->birth_date ? \Carbon\Carbon::parse($s->birth_date)->locale('id')->translatedFormat('d F Y') : '-') }}"
                                            data-job="{{ $s->job->name ?? 'Swasta' }}"
                                            data-religion="{{ $s->religion->name ?? 'Islam' }}"
                                            data-address="{{ $s->address ?? '-' }}"
                                            {{ (old('suspect_id', $selectedSuspect->id ?? '') == $s->id) ? 'selected' : '' }}>
                                            {{ $s->name }} (NIK: {{ $s->nik ?: '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">a. Nama Tersangka <span class="text-danger">*</span></label>
                                <input type="text" id="tersangka_nama" name="tersangka_nama" class="form-control" value="{{ old('tersangka_nama', $selectedSuspect->name ?? '-') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">b. Nomor Identitas (NIK/SIM/Paspor)</label>
                                <input type="text" id="tersangka_no_identitas" name="tersangka_no_identitas" class="form-control" value="{{ old('tersangka_no_identitas', $selectedSuspect->nik ?? ($selectedSuspect->sim_number ?? '-')) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">c. Kewarganegaraan</label>
                                <input type="text" name="tersangka_kewarganegaraan" class="form-control" value="{{ old('tersangka_kewarganegaraan', 'WNI') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">d. Jenis Kelamin</label>
                                <input type="text" id="tersangka_jenis_kelamin" name="tersangka_jenis_kelamin" class="form-control" value="{{ old('tersangka_jenis_kelamin', $selectedSuspect->gender->name ?? 'Laki-laki') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">e. Tempat/Tanggal Lahir</label>
                                <input type="text" id="tersangka_ttl" name="tersangka_ttl" class="form-control" value="{{ old('tersangka_ttl', ($selectedSuspect && $selectedSuspect->birth_place ? $selectedSuspect->birth_place . ', ' : '') . ($selectedSuspect && $selectedSuspect->birth_date ? \Carbon\Carbon::parse($selectedSuspect->birth_date)->locale('id')->translatedFormat('d F Y') : '-')) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">f. Pekerjaan</label>
                                <input type="text" id="tersangka_pekerjaan" name="tersangka_pekerjaan" class="form-control" value="{{ old('tersangka_pekerjaan', $selectedSuspect->job->name ?? 'Swasta') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">g. Agama</label>
                                <input type="text" id="tersangka_agama" name="tersangka_agama" class="form-control" value="{{ old('tersangka_agama', $selectedSuspect->religion->name ?? 'Islam') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">h. Alamat</label>
                                <input type="text" id="tersangka_alamat" name="tersangka_alamat" class="form-control" value="{{ old('tersangka_alamat', $selectedSuspect->address ?? '-') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 5: OBJEK & JADWAL PEMBLOKIRAN -->
                <div class="card card-custom">
                    <div class="card-header text-primary">
                        <i class="bi bi-credit-card me-1"></i> V. OBJEK & JADWAL PEMBLOKIRAN
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Objek yang Dimohonkan Pemblokiran <span class="text-danger">*</span></label>
                            <textarea name="objek_blokir" class="form-control" rows="3" required placeholder="Contoh:&#10;Rekening Bank BRI Nomor Rekening 0123-01-001234-50-1 atas nama Ahmad Santoso">{{ old('objek_blokir', $defaultObjekBlokir) }}</textarea>
                            <small class="text-muted">Tuliskan nama bank, nomor rekening, dan atas nama rekening yang diblokir.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Hari & Tanggal Kehadiran <span class="text-danger">*</span></label>
                                <input type="text" name="blokir_hari_tanggal" class="form-control" value="{{ old('blokir_hari_tanggal', ($hariIni ?? 'Senin') . ', ' . \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y')) }}" placeholder="Contoh: Senin, 10 November 2026" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Pukul / Waktu <span class="text-danger">*</span></label>
                                <input type="text" name="blokir_waktu" class="form-control" value="{{ old('blokir_waktu', $jamIni) }}" placeholder="Contoh: 09:00 WIB" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Tempat Kehadiran <span class="text-danger">*</span></label>
                                <input type="text" name="blokir_tempat" class="form-control" value="{{ old('blokir_tempat', $tempatDefault) }}" placeholder="Contoh: Ruang Unit Gakkum Satlantas..." required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 6: NARAHUBUNG & PEJABAT PENANDATANGAN -->
                <div class="card card-custom">
                    <div class="card-header text-primary">
                        <i class="bi bi-people me-1"></i> VI. NARAHUBUNG & PEJABAT PENANDATANGAN
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-telephone me-1"></i> Narahubung / Penyidik yang Ditunjuk</h6>
                        <div class="row mb-4">
                            <div class="col-md-4 mb-2">
                                <label class="form-label font-weight-bold">Pilih Personel Penyidik:</label>
                                <select id="select_penyidik" name="penyidik_id" class="form-control custom-select">
                                    <option value="">-- Pilih dari Personel --</option>
                                    @foreach ($officers as $off)
                                        <option value="{{ $off->id }}"
                                            data-name="{{ $off->full_name }}"
                                            data-position="{{ $off->position->name ?? 'Penyidik Pembantu' }}"
                                            data-phone="{{ $off->phone_number ?? ($off->user->phone_number ?? '') }}">
                                            {{ $off->full_name }} ({{ $off->position->name ?? 'Personil' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label class="form-label font-weight-bold">Sebutan</label>
                                <input type="text" id="penyidik_sebutan" name="penyidik_sebutan" class="form-control" value="{{ old('penyidik_sebutan', 'Penyidik Pembantu') }}">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label font-weight-bold">Nama Penyidik</label>
                                <input type="text" id="penyidik_nama" name="penyidik_nama" class="form-control" value="{{ old('penyidik_nama') }}" placeholder="Nama personil...">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label font-weight-bold">No. HP Penyidik</label>
                                <input type="text" id="penyidik_nohp" name="penyidik_nohp" class="form-control" value="{{ old('penyidik_nohp') }}" placeholder="Contoh: 081234567890">
                            </div>
                        </div>

                        <hr>
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-pen me-1"></i> Pejabat Penandatangan</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Pilih Pejabat Penandatangan <span class="text-danger">*</span></label>
                                <select name="signatory" class="form-control custom-select" required>
                                    <option value="">-- Pilih Penandatangan --</option>
                                    @foreach ($authorizedSignatories as $signatory)
                                        <option value="{{ $signatory->id }}" {{ old('signatory') == $signatory->id ? 'selected' : '' }}>
                                            {{ $signatory->full_name }} — {{ $signatory->position->name ?? 'Pejabat' }} ({{ $signatory->rank->name ?? '-' }} NRP {{ $signatory->register_number ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Atas Nama (Head Text Tanda Tangan)</label>
                                <input type="text" name="signatory_head_text" class="form-control" placeholder="Contoh: a.n. KEPALA KEPOLISIAN RESOR PASURUAN (Opsional)" value="{{ old('signatory_head_text') }}">
                                <small class="text-muted">Kosongkan jika ditandatangani langsung atas nama Kasat Lantas.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 7: TEMBUSAN -->
                <div class="card card-custom">
                    <div class="card-header text-primary">
                        <i class="bi bi-list-check me-1"></i> VII. TEMBUSAN SURAT
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-md-6 mb-2">
                                <label class="form-label font-weight-bold">Tembusan 1</label>
                                <input type="text" name="tembusan_1" class="form-control" value="{{ old('tembusan_1', 'Kapolres ' . ($accident->polres->name ?? 'Pasuruan') . ' (sebagai laporan)') }}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label font-weight-bold">Tembusan 2</label>
                                <input type="text" name="tembusan_2" class="form-control" value="{{ old('tembusan_2', 'Wakapolres ' . ($accident->polres->name ?? 'Pasuruan')) }}">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4 mb-2">
                                <label class="form-label font-weight-bold">Tembusan 3</label>
                                <input type="text" name="tembusan_3" class="form-control" value="{{ old('tembusan_3', 'Kabid Propam Polda ' . ($accident->polres->polda->name ?? 'Jatim')) }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label font-weight-bold">Tembusan 4</label>
                                <input type="text" name="tembusan_4" class="form-control" value="{{ old('tembusan_4', 'Kasiwas Polres ' . ($accident->polres->name ?? 'Pasuruan')) }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label font-weight-bold">Tembusan 5</label>
                                <input type="text" name="tembusan_5" class="form-control" value="{{ old('tembusan_5', 'Pengawas Penyidikan / Arsip') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TOMBOL SIMPAN -->
                <div class="d-flex justify-content-end mb-4">
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-secondary me-2">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Simpan Surat Permintaan Blokir
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            // Auto fill suspect data
            $('#select_suspect').on('change', function() {
                var selected = $(this).find('option:selected');
                if (selected.val()) {
                    $('#tersangka_nama').val(selected.data('name'));
                    $('#tersangka_no_identitas').val(selected.data('nik'));
                    $('#tersangka_jenis_kelamin').val(selected.data('gender'));
                    $('#tersangka_ttl').val(selected.data('ttl'));
                    $('#tersangka_pekerjaan').val(selected.data('job'));
                    $('#tersangka_agama').val(selected.data('religion'));
                    $('#tersangka_alamat').val(selected.data('address'));
                }
            });

            // Auto fill investigator data
            $('#select_penyidik').on('change', function() {
                var selected = $(this).find('option:selected');
                if (selected.val()) {
                    $('#penyidik_nama').val(selected.data('name'));
                    $('#penyidik_sebutan').val(selected.data('position'));
                    if (selected.data('phone')) {
                        $('#penyidik_nohp').val(selected.data('phone'));
                    }
                }
            });
        });
    </script>
@endpush

