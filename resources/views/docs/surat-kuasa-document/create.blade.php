@php
    $_title = 'Surat Kuasa Penasehat Hukum';
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
            <h5 class="fw-bold text-blue-dark">Formulir Surat Kuasa Penasehat Hukum</h5>
            <small class="text-muted d-block mb-3">Kode Dokumen: <b>DCT-0703</b></small>

            @if (isset($errors) && $errors->any())
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
            <form action="{{ route('doc.surat-kuasa-document.store', ['accident_id' => $accidentId]) }}" method="POST">
                @csrf
                <input type="hidden" name="accident_id" value="{{ $accidentId }}">

                <!-- CARD 1: INFORMASI TEMPAT & TANGGAL -->
                <div class="card card-custom">
                    <div class="card-header text-primary">
                        <i class="bi bi-geo-alt me-1"></i> I. TEMPAT & TANGGAL SURAT
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">
                                    Dikeluarkan di <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="dikeluarkan_di" class="form-control" value="{{ old('dikeluarkan_di', $tempatDefault) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">
                                    Tanggal Surat (Teks Resmi) <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="tanggal_surat" class="form-control" value="{{ old('tanggal_surat', \Carbon\Carbon::parse($today)->locale('id')->translatedFormat('d F Y')) }}" placeholder="Contoh: 04 November 2026" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Tanggal Pembuatan (Kalender Sistem)</label>
                                <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $today) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: IDENTITAS PEMBERI KUASA (YANG MEMBERI KUASA / TERSANGKA) -->
                <div class="card card-custom">
                    <div class="card-header text-primary d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-person-fill me-1"></i> II. IDENTITAS PEMBERI KUASA (YANG MEMBERI KUASA / TERSANGKA)
                        </div>
                        <div>
                            <span class="badge bg-light text-secondary border">Yang bertanda tangan di bawah ini</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Auto-fill Helper -->
                        <div class="p-3 mb-3 bg-light rounded border">
                            <label class="form-label font-weight-bold text-dark">
                                <i class="bi bi-magic me-1 text-primary"></i> Ambil Data Otomatis dari Tersangka / Pihak Terlibat Perkara Ini:
                            </label>
                            <select id="select_auto_pemberi_kuasa" class="form-control custom-select">
                                <option value="">-- Pilih Nama Tersangka / Pihak untuk Mengisi Otomatis --</option>
                                @if (isset($suspects) && count($suspects) > 0)
                                    <optgroup label="Tersangka">
                                        @foreach ($suspects as $s)
                                            @php
                                                $sTtl = trim(($s->birth_place ? $s->birth_place . ', ' : '') . ($s->birth_date ? \Carbon\Carbon::parse($s->birth_date)->locale('id')->translatedFormat('d F Y') : ''), ', ');
                                                $sGender = $s->gender->name ?? ($s->gender_id == '1' ? 'Laki-laki' : ($s->gender_id == '2' ? 'Perempuan' : ''));
                                                $sAgama = $s->religion->name ?? ($s->religion_id ?? '');
                                                $sJob = $s->job->name ?? ($s->occupation ?? '');
                                                $sUmur = $s->age ? ($s->age . ' Tahun') : '';
                                            @endphp
                                            <option value="suspect_{{ $s->id }}"
                                                data-nama="{{ $s->name }}"
                                                data-nik="{{ $s->identity_number ?: ($s->nik ?: '') }}"
                                                data-kewarganegaraan="{{ $s->citizenship ?: 'Indonesia' }}"
                                                data-gender="{{ $sGender }}"
                                                data-ttl="{{ $sTtl }}"
                                                data-umur="{{ $sUmur }}"
                                                data-pekerjaan="{{ $sJob }}"
                                                data-agama="{{ $sAgama }}"
                                                data-alamat="{{ $s->address }}"
                                                data-nohp="{{ $s->phone ?: ($s->phone_number ?: '') }}">
                                                [Tersangka] {{ $s->name }} (NIK: {{ $s->identity_number ?: '-' }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                @if (isset($reportedPersons) && count($reportedPersons) > 0)
                                    <optgroup label="Pihak Terlapor / Terlibat">
                                        @foreach ($reportedPersons as $rp)
                                            @php
                                                $rpTtl = trim(($rp->birth_place ? $rp->birth_place . ', ' : '') . ($rp->birth_date ? \Carbon\Carbon::parse($rp->birth_date)->locale('id')->translatedFormat('d F Y') : ''), ', ');
                                                $rpUmur = $rp->age ? ($rp->age . ' Tahun') : '';
                                            @endphp
                                            <option value="rp_{{ $rp->id }}"
                                                data-nama="{{ $rp->name }}"
                                                data-nik="{{ $rp->identity_number }}"
                                                data-kewarganegaraan="Indonesia"
                                                data-gender="{{ $rp->gender }}"
                                                data-ttl="{{ $rpTtl }}"
                                                data-umur="{{ $rpUmur }}"
                                                data-pekerjaan="{{ $rp->occupation }}"
                                                data-agama="{{ $rp->religion }}"
                                                data-alamat="{{ $rp->address }}"
                                                data-nohp="{{ $rp->phone_number }}">
                                                [Pihak Terlibat] {{ $rp->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                @if (isset($witnesses) && count($witnesses) > 0)
                                    <optgroup label="Saksi">
                                        @foreach ($witnesses as $w)
                                            <option value="w_{{ $w->id }}"
                                                data-nama="{{ $w->name ?? ($w->nama ?? '') }}"
                                                data-nik="{{ $w->identity_number ?? ($w->nik ?? '') }}"
                                                data-kewarganegaraan="Indonesia"
                                                data-gender="{{ $w->gender ?? '' }}"
                                                data-ttl="{{ trim(($w->birth_place ?? '') . ', ' . (!empty($w->birth_date) ? date('d-m-Y', strtotime($w->birth_date)) : ''), ', ') }}"
                                                data-umur="{{ $w->age ?? ($w->umur ?? '') }}"
                                                data-pekerjaan="{{ $w->occupation ?? ($w->pekerjaan ?? '') }}"
                                                data-agama="{{ $w->religion ?? '' }}"
                                                data-alamat="{{ $w->address ?? ($w->alamat ?? '') }}"
                                                data-nohp="{{ $w->phone_number ?? '' }}">
                                                [Saksi] {{ $w->name ?? ($w->nama ?? 'Saksi') }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="pemberi_nama" name="pemberi_nama" class="form-control" value="{{ old('pemberi_nama', $firstSuspect->name ?? '') }}" placeholder="Contoh: Ahmad Fauzi" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">
                                    Nomor Identitas (NIK/KTP)
                                </label>
                                <input type="text" id="pemberi_no_identitas" name="pemberi_no_identitas" class="form-control" value="{{ old('pemberi_no_identitas', $firstSuspect->identity_number ?? '') }}" placeholder="Contoh: 3514012345670001">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">
                                    Kewarganegaraan
                                </label>
                                <input type="text" id="pemberi_kewarganegaraan" name="pemberi_kewarganegaraan" class="form-control" value="{{ old('pemberi_kewarganegaraan', 'Indonesia') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">
                                    Jenis Kelamin
                                </label>
                                <select id="pemberi_jenis_kelamin" name="pemberi_jenis_kelamin" class="form-control custom-select">
                                    <option value="Laki-laki" {{ old('pemberi_jenis_kelamin', ($firstSuspect && $firstSuspect->gender_id == '2' ? 'Perempuan' : 'Laki-laki')) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ old('pemberi_jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">
                                    Tempat/Tanggal Lahir
                                </label>
                                <input type="text" id="pemberi_ttl" name="pemberi_ttl" class="form-control" value="{{ old('pemberi_ttl', $defaultPemberiTtl) }}" placeholder="Contoh: Pasuruan, 12 Januari 1990">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label font-weight-bold">
                                    Umur
                                </label>
                                <input type="text" id="pemberi_umur" name="pemberi_umur" class="form-control" value="{{ old('pemberi_umur', ($firstSuspect && $firstSuspect->age ? $firstSuspect->age . ' Tahun' : '')) }}" placeholder="Contoh: 35 Tahun">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">
                                    Pekerjaan
                                </label>
                                <input type="text" id="pemberi_pekerjaan" name="pemberi_pekerjaan" class="form-control" value="{{ old('pemberi_pekerjaan', $firstSuspect->occupation ?? '') }}" placeholder="Contoh: Wiraswasta / Karyawan Swasta">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">
                                    Agama
                                </label>
                                <input type="text" id="pemberi_agama" name="pemberi_agama" class="form-control" value="{{ old('pemberi_agama', $firstSuspect->religion->name ?? 'Islam') }}" placeholder="Contoh: Islam">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">
                                    Nomor HP
                                </label>
                                <input type="text" id="pemberi_nohp" name="pemberi_nohp" class="form-control" value="{{ old('pemberi_nohp', $firstSuspect->phone ?? '') }}" placeholder="Contoh: 081234567890">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label font-weight-bold">
                                    Alamat Tempat Tinggal
                                </label>
                                <textarea id="pemberi_alamat" name="pemberi_alamat" rows="2" class="form-control" placeholder="Contoh: Dusun Krajan RT 01 RW 02, Desa Warungdowo, Kec. Pohjentrek, Kab. Pasuruan">{{ old('pemberi_alamat', $firstSuspect->address ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">
                                    Telepon Rumah/Kantor
                                </label>
                                <input type="text" id="pemberi_telp" name="pemberi_telp" class="form-control" value="{{ old('pemberi_telp', '-') }}" placeholder="Contoh: 0343-421234 atau -">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">
                                    Alamat Email
                                </label>
                                <input type="text" id="pemberi_email" name="pemberi_email" class="form-control" value="{{ old('pemberi_email', '-') }}" placeholder="Contoh: nama@domain.com atau -">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">
                                    Platform Medsos
                                </label>
                                <input type="text" id="pemberi_medsos_platform" name="pemberi_medsos_platform" class="form-control" value="{{ old('pemberi_medsos_platform', 'Instagram') }}" placeholder="Contoh: Instagram / Facebook / -">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">
                                    Akun Media Sosial
                                </label>
                                <input type="text" id="pemberi_medsos_akun" name="pemberi_medsos_akun" class="form-control" value="{{ old('pemberi_medsos_akun', '-') }}" placeholder="Contoh: @username atau -">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: IDENTITAS PENERIMA KUASA (PENASEHAT HUKUM / ADVOKAT) -->
                <div class="card card-custom">
                    <div class="card-header text-primary d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-briefcase-fill me-1"></i> III. IDENTITAS PENERIMA KUASA (PENASEHAT HUKUM / ADVOKAT)
                        </div>
                        <div>
                            <span class="badge bg-light text-secondary border">Dengan ini memberikan kuasa hukum kepada</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">
                                    Nama Lengkap Penasehat Hukum <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="penerima_nama" class="form-control font-weight-bold" value="{{ old('penerima_nama') }}" placeholder="Contoh: Bambang Setyawan, S.H., M.H." required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">
                                    Nomor Identitas (NIK/No. Anggota Advokat)
                                </label>
                                <input type="text" name="penerima_no_identitas" class="form-control" value="{{ old('penerima_no_identitas') }}" placeholder="Contoh: 3514... / NIA. 12.34567">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">
                                    Kewarganegaraan
                                </label>
                                <input type="text" name="penerima_kewarganegaraan" class="form-control" value="{{ old('penerima_kewarganegaraan', 'Indonesia') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">
                                    Jenis Kelamin
                                </label>
                                <select name="penerima_jenis_kelamin" class="form-control custom-select">
                                    <option value="Laki-laki" {{ old('penerima_jenis_kelamin', 'Laki-laki') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ old('penerima_jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">
                                    Tempat/Tanggal Lahir
                                </label>
                                <input type="text" name="penerima_ttl" class="form-control" value="{{ old('penerima_ttl', '-') }}" placeholder="Contoh: Surabaya, 10 Agustus 1985">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label font-weight-bold">
                                    Umur
                                </label>
                                <input type="text" name="penerima_umur" class="form-control" value="{{ old('penerima_umur', '-') }}" placeholder="Contoh: 39 Tahun">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">
                                    Pekerjaan / Profesi
                                </label>
                                <input type="text" name="penerima_pekerjaan" class="form-control" value="{{ old('penerima_pekerjaan', 'Advokat / Penasihat Hukum') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">
                                    Agama
                                </label>
                                <input type="text" name="penerima_agama" class="form-control" value="{{ old('penerima_agama', 'Islam') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">
                                    Nomor HP
                                </label>
                                <input type="text" name="penerima_nohp" class="form-control" value="{{ old('penerima_nohp') }}" placeholder="Contoh: 081234567890">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label font-weight-bold">
                                    Alamat Kantor / Domisili
                                </label>
                                <textarea name="penerima_alamat" rows="2" class="form-control" placeholder="Contoh: Kantor Advokat & Konsultan Hukum, Jl. Pahlawan No. 45, Kota Pasuruan">{{ old('penerima_alamat') }}</textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">
                                    Telepon Kantor
                                </label>
                                <input type="text" name="penerima_telp" class="form-control" value="{{ old('penerima_telp', '-') }}" placeholder="Contoh: 0343-421111 atau -">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">
                                    Email Kantor / Pribadi
                                </label>
                                <input type="text" name="penerima_email" class="form-control" value="{{ old('penerima_email', '-') }}" placeholder="Contoh: advokat@lawfirm.com">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">
                                    Platform Medsos
                                </label>
                                <input type="text" name="penerima_medsos_platform" class="form-control" value="{{ old('penerima_medsos_platform', 'Instagram') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">
                                    Akun Media Sosial
                                </label>
                                <input type="text" name="penerima_medsos_akun" class="form-control" value="{{ old('penerima_medsos_akun', '-') }}" placeholder="Contoh: @lawyer_account">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 4: KLAUSUL KHUSUS & RUJUKAN PERKARA -->
                <div class="card card-custom">
                    <div class="card-header text-primary">
                        <i class="bi bi-shield-check me-1"></i> IV. KLAUSUL KHUSUS & RUJUKAN PERKARA
                    </div>
                    <div class="card-body">
                        <div class="alert alert-secondary">
                            <p class="mb-1 font-weight-bold"><i class="bi bi-info-circle me-1"></i> Rujukan Teks Klausul Khusus:</p>
                            <small class="d-block font-italic">
                                "Khusus: Untuk memberikan bantuan hukum di dalam proses penyidikan, kepada pemberi kuasa (tersangka) yang dipersangkakan telah melakukan Tindak Pidana sesuai pasal dan undang-undang terkait, yang terjadi di tempat dan waktu kejadian perkara, berdasarkan..."
                            </small>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">
                                    Tindak Pidana
                                </label>
                                <input type="text" name="tindak_pidana" class="form-control" value="{{ old('tindak_pidana', 'Kecelakaan Lalu Lintas') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">
                                    Pasal Pidana
                                </label>
                                <input type="text" name="pasal_pidana" class="form-control" value="{{ old('pasal_pidana', '310 ayat (4)') }}" placeholder="Contoh: 310 ayat (4)">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">
                                    Undang-Undang Nomor
                                </label>
                                <input type="text" name="uu_nomor" class="form-control" value="{{ old('uu_nomor', '22') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">
                                    Undang-Undang Tahun
                                </label>
                                <input type="text" name="uu_tahun" class="form-control" value="{{ old('uu_tahun', '2009') }}">
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label font-weight-bold">
                                    Undang-Undang Tentang
                                </label>
                                <input type="text" name="uu_tentang" class="form-control" value="{{ old('uu_tentang', 'Lalu Lintas dan Angkutan Jalan') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">
                                    Tempat Kejadian Perkara (TKP)
                                </label>
                                <textarea name="tkp" rows="2" class="form-control">{{ old('tkp', $tkpDefault) }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">
                                    Waktu Kejadian
                                </label>
                                <textarea name="waktu_kejadian" rows="2" class="form-control">{{ old('waktu_kejadian', $waktuKejadianDefault) }}</textarea>
                            </div>
                        </div>

                        <h6 class="fw-bold text-dark mt-3 mb-2 border-bottom pb-1">
                            <i class="bi bi-list-check me-1"></i> Rujukan Dasar (Poin 1, 2, 3 & Surat Penetapan)
                        </h6>

                        <!-- 1. Laporan Polisi -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">
                                    1. Laporan Polisi Nomor:
                                </label>
                                <input type="text" name="dasar_lp_nomor" class="form-control" value="{{ old('dasar_lp_nomor', $noLp) }}" placeholder="Contoh: LP/A/01/I/2026/SPKT/POLRES PASURUAN">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">
                                    Tanggal LP:
                                </label>
                                <input type="text" name="dasar_lp_tanggal" class="form-control" value="{{ old('dasar_lp_tanggal', $tanggalLp) }}" placeholder="Contoh: 15 Januari 2026">
                            </div>
                        </div>

                        <!-- 2. Surat Perintah Penyidikan -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">
                                    2. Surat Perintah Penyidikan Nomor:
                                </label>
                                <input type="text" name="dasar_sp_sidik_nomor" class="form-control" value="{{ old('dasar_sp_sidik_nomor', $noSpSidik) }}" placeholder="Contoh: SP.Sidik/01/I/2026/Satlantas">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">
                                    Tanggal SP Sidik:
                                </label>
                                <input type="text" name="dasar_sp_sidik_tanggal" class="form-control" value="{{ old('dasar_sp_sidik_tanggal', $tanggalSpSidik) }}" placeholder="Contoh: 15 Januari 2026">
                            </div>
                        </div>

                        <!-- 3. Surat Perintah Tugas Penyidikan -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">
                                    3. Surat Perintah Tugas Penyidikan Nomor:
                                </label>
                                <input type="text" name="dasar_sp_gas_sidik_nomor" class="form-control" value="{{ old('dasar_sp_gas_sidik_nomor', $noSpGasSidik) }}" placeholder="Contoh: SP.Gas/01/I/2026/Satlantas">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">
                                    Tanggal SP Gas Sidik:
                                </label>
                                <input type="text" name="dasar_sp_gas_sidik_tanggal" class="form-control" value="{{ old('dasar_sp_gas_sidik_tanggal', $tanggalSpGasSidik) }}" placeholder="Contoh: 15 Januari 2026">
                            </div>
                        </div>

                        <!-- 4. Surat Penetapan Penunjukan Penasehat Hukum -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">
                                    Surat Penetapan Penunjukan Penasehat Hukum Nomor:
                                </label>
                                <input type="text" name="surat_penetapan_nomor" class="form-control" value="{{ old('surat_penetapan_nomor', '-') }}" placeholder="Contoh: B/05/I/2026/Satlantas atau -">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">
                                    Tanggal Surat Penetapan:
                                </label>
                                <input type="text" name="surat_penetapan_tanggal" class="form-control" value="{{ old('surat_penetapan_tanggal', '-') }}" placeholder="Contoh: 16 Januari 2026 atau -">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 5: PEJABAT YANG MENGETAHUI (OPSIONAL) -->
                <div class="card card-custom">
                    <div class="card-header text-primary">
                        <i class="bi bi-person-badge me-1"></i> V. PENCATATAN PENYIDIK / MENGETAHUI (OPSIONAL)
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <label class="form-label font-weight-bold">Penyidik / Pejabat Internal Polri</label>
                                <select name="mengetahui_signatory_id" class="form-control select2">
                                    <option value="">-- Tidak Memerlukan Tanda Tangan Mengetahui --</option>
                                    @foreach ($officers as $officer)
                                        <option value="{{ $officer->id }}" {{ old('mengetahui_signatory_id') == $officer->id ? 'selected' : '' }}>
                                            {{ $officer->full_name }} — NRP: {{ $officer->register_number ?? '-' }} ({{ $officer->position->name ?? 'Petugas' }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Pilih apabila Surat Kuasa dicatatkan dalam administrasi penyidikan dengan mengetahui Penyidik/Kanit.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TOMBOL SIMPAN -->
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-circle me-1"></i> Simpan Surat Kuasa Penasehat Hukum
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Handler Autofill Pemberi Kuasa
            $('#select_auto_pemberi_kuasa').on('change', function() {
                var selected = $(this).find('option:selected');
                if (!selected.val()) return;

                $('#pemberi_nama').val(selected.data('nama') || '');
                $('#pemberi_no_identitas').val(selected.data('nik') || '');
                $('#pemberi_kewarganegaraan').val(selected.data('kewarganegaraan') || 'Indonesia');
                if (selected.data('gender')) {
                    $('#pemberi_jenis_kelamin').val(selected.data('gender'));
                }
                $('#pemberi_ttl').val(selected.data('ttl') || '');
                $('#pemberi_umur').val(selected.data('umur') || '');
                $('#pemberi_pekerjaan').val(selected.data('pekerjaan') || '');
                if (selected.data('agama')) {
                    $('#pemberi_agama').val(selected.data('agama'));
                }
                $('#pemberi_alamat').val(selected.data('alamat') || '');
                $('#pemberi_nohp').val(selected.data('nohp') || '');
            });
        });
    </script>
@endpush
