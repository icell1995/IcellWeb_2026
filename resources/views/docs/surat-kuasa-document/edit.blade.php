@php
    $_title = 'Edit Surat Kuasa Penasehat Hukum';
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
            <h5 class="fw-bold text-blue-dark">Edit Surat Kuasa Penasehat Hukum</h5>
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
            <form action="{{ route('doc.surat-kuasa-document.update', ['id' => $document->id, 'accident_id' => $accidentId]) }}" method="POST">
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
                                <input type="text" name="dikeluarkan_di" class="form-control" value="{{ old('dikeluarkan_di', $document->dikeluarkan_di ?: ($document->tempat_surat ?: $tempatDefault)) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">
                                    Tanggal Surat (Teks Resmi) <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="tanggal_surat" class="form-control" value="{{ old('tanggal_surat', $document->tanggal_surat ?: ($document->tanggal ? \Carbon\Carbon::parse($document->tanggal)->locale('id')->translatedFormat('d F Y') : '')) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Tanggal Pembuatan (Kalender Sistem)</label>
                                <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $document->tanggal ? date('Y-m-d', strtotime($document->tanggal)) : $today) }}">
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
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="pemberi_nama" name="pemberi_nama" class="form-control" value="{{ old('pemberi_nama', $document->pemberi_nama ?: $document->pemberi_kuasa_nama) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">
                                    Nomor Identitas (NIK/KTP)
                                </label>
                                <input type="text" id="pemberi_no_identitas" name="pemberi_no_identitas" class="form-control" value="{{ old('pemberi_no_identitas', $document->pemberi_no_identitas ?: $document->pemberi_kuasa_nik) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">
                                    Kewarganegaraan
                                </label>
                                <input type="text" id="pemberi_kewarganegaraan" name="pemberi_kewarganegaraan" class="form-control" value="{{ old('pemberi_kewarganegaraan', $document->pemberi_kewarganegaraan ?: ($document->pemberi_kuasa_kewarganegaraan ?: 'Indonesia')) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">
                                    Jenis Kelamin
                                </label>
                                @php
                                    $pGender = old('pemberi_jenis_kelamin', $document->pemberi_jenis_kelamin ?: $document->pemberi_kuasa_jenis_kelamin);
                                @endphp
                                <select id="pemberi_jenis_kelamin" name="pemberi_jenis_kelamin" class="form-control custom-select">
                                    <option value="Laki-laki" {{ $pGender == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ $pGender == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">
                                    Tempat/Tanggal Lahir
                                </label>
                                <input type="text" id="pemberi_ttl" name="pemberi_ttl" class="form-control" value="{{ old('pemberi_ttl', $document->pemberi_ttl) }}" placeholder="Contoh: Pasuruan, 12 Januari 1990">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label font-weight-bold">
                                    Umur
                                </label>
                                <input type="text" id="pemberi_umur" name="pemberi_umur" class="form-control" value="{{ old('pemberi_umur', $document->pemberi_umur ?: $document->pemberi_kuasa_umur) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">
                                    Pekerjaan
                                </label>
                                <input type="text" id="pemberi_pekerjaan" name="pemberi_pekerjaan" class="form-control" value="{{ old('pemberi_pekerjaan', $document->pemberi_pekerjaan ?: $document->pemberi_kuasa_pekerjaan) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">
                                    Agama
                                </label>
                                <input type="text" id="pemberi_agama" name="pemberi_agama" class="form-control" value="{{ old('pemberi_agama', $document->pemberi_agama ?: $document->pemberi_kuasa_agama) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">
                                    Nomor HP
                                </label>
                                <input type="text" id="pemberi_nohp" name="pemberi_nohp" class="form-control" value="{{ old('pemberi_nohp', $document->pemberi_nohp ?: $document->pemberi_kuasa_telepon) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label font-weight-bold">
                                    Alamat Tempat Tinggal
                                </label>
                                <textarea id="pemberi_alamat" name="pemberi_alamat" rows="2" class="form-control">{{ old('pemberi_alamat', $document->pemberi_alamat ?: $document->pemberi_kuasa_alamat) }}</textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">
                                    Telepon Rumah/Kantor
                                </label>
                                <input type="text" id="pemberi_telp" name="pemberi_telp" class="form-control" value="{{ old('pemberi_telp', $document->pemberi_telp ?: '-') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">
                                    Alamat Email
                                </label>
                                <input type="text" id="pemberi_email" name="pemberi_email" class="form-control" value="{{ old('pemberi_email', $document->pemberi_email ?: '-') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">
                                    Platform Medsos
                                </label>
                                <input type="text" id="pemberi_medsos_platform" name="pemberi_medsos_platform" class="form-control" value="{{ old('pemberi_medsos_platform', $document->pemberi_medsos_platform ?: 'Instagram') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">
                                    Akun Media Sosial
                                </label>
                                <input type="text" id="pemberi_medsos_akun" name="pemberi_medsos_akun" class="form-control" value="{{ old('pemberi_medsos_akun', $document->pemberi_medsos_akun ?: '-') }}">
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
                                <input type="text" name="penerima_nama" class="form-control font-weight-bold" value="{{ old('penerima_nama', $document->penerima_nama ?: $document->penerima_kuasa_nama) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">
                                    Nomor Identitas (NIK/No. Anggota Advokat)
                                </label>
                                <input type="text" name="penerima_no_identitas" class="form-control" value="{{ old('penerima_no_identitas', $document->penerima_no_identitas ?: $document->penerima_kuasa_nik) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">
                                    Kewarganegaraan
                                </label>
                                <input type="text" name="penerima_kewarganegaraan" class="form-control" value="{{ old('penerima_kewarganegaraan', $document->penerima_kewarganegaraan ?: ($document->penerima_kuasa_kewarganegaraan ?: 'Indonesia')) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">
                                    Jenis Kelamin
                                </label>
                                @php
                                    $penGender = old('penerima_jenis_kelamin', $document->penerima_jenis_kelamin ?: $document->penerima_kuasa_jenis_kelamin);
                                @endphp
                                <select name="penerima_jenis_kelamin" class="form-control custom-select">
                                    <option value="Laki-laki" {{ $penGender == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ $penGender == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">
                                    Tempat/Tanggal Lahir
                                </label>
                                <input type="text" name="penerima_ttl" class="form-control" value="{{ old('penerima_ttl', $document->penerima_ttl) }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label font-weight-bold">
                                    Umur
                                </label>
                                <input type="text" name="penerima_umur" class="form-control" value="{{ old('penerima_umur', $document->penerima_umur ?: $document->penerima_kuasa_umur) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">
                                    Pekerjaan / Profesi
                                </label>
                                <input type="text" name="penerima_pekerjaan" class="form-control" value="{{ old('penerima_pekerjaan', $document->penerima_pekerjaan ?: ($document->penerima_kuasa_pekerjaan ?: 'Advokat / Penasihat Hukum')) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">
                                    Agama
                                </label>
                                <input type="text" name="penerima_agama" class="form-control" value="{{ old('penerima_agama', $document->penerima_agama ?: ($document->penerima_kuasa_agama ?: 'Islam')) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">
                                    Nomor HP
                                </label>
                                <input type="text" name="penerima_nohp" class="form-control" value="{{ old('penerima_nohp', $document->penerima_nohp ?: $document->penerima_kuasa_telepon) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label font-weight-bold">
                                    Alamat Kantor / Domisili
                                </label>
                                <textarea name="penerima_alamat" rows="2" class="form-control">{{ old('penerima_alamat', $document->penerima_alamat ?: $document->penerima_kuasa_alamat) }}</textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">
                                    Telepon Kantor
                                </label>
                                <input type="text" name="penerima_telp" class="form-control" value="{{ old('penerima_telp', $document->penerima_telp ?: '-') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">
                                    Email Kantor / Pribadi
                                </label>
                                <input type="text" name="penerima_email" class="form-control" value="{{ old('penerima_email', $document->penerima_email ?: '-') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">
                                    Platform Medsos
                                </label>
                                <input type="text" name="penerima_medsos_platform" class="form-control" value="{{ old('penerima_medsos_platform', $document->penerima_medsos_platform ?: 'Instagram') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">
                                    Akun Media Sosial
                                </label>
                                <input type="text" name="penerima_medsos_akun" class="form-control" value="{{ old('penerima_medsos_akun', $document->penerima_medsos_akun ?: '-') }}">
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
                                <input type="text" name="tindak_pidana" class="form-control" value="{{ old('tindak_pidana', $document->tindak_pidana ?: 'Kecelakaan Lalu Lintas') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">
                                    Pasal Pidana
                                </label>
                                <input type="text" name="pasal_pidana" class="form-control" value="{{ old('pasal_pidana', $document->pasal_pidana ?: '310 ayat (4)') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">
                                    Undang-Undang Nomor
                                </label>
                                <input type="text" name="uu_nomor" class="form-control" value="{{ old('uu_nomor', $document->uu_nomor ?: '22') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">
                                    Undang-Undang Tahun
                                </label>
                                <input type="text" name="uu_tahun" class="form-control" value="{{ old('uu_tahun', $document->uu_tahun ?: '2009') }}">
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label font-weight-bold">
                                    Undang-Undang Tentang
                                </label>
                                <input type="text" name="uu_tentang" class="form-control" value="{{ old('uu_tentang', $document->uu_tentang ?: 'Lalu Lintas dan Angkutan Jalan') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">
                                    Tempat Kejadian Perkara (TKP)
                                </label>
                                <textarea name="tkp" rows="2" class="form-control">{{ old('tkp', $document->tkp) }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">
                                    Waktu Kejadian
                                </label>
                                <textarea name="waktu_kejadian" rows="2" class="form-control">{{ old('waktu_kejadian', $document->waktu_kejadian) }}</textarea>
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
                                <input type="text" name="dasar_lp_nomor" class="form-control" value="{{ old('dasar_lp_nomor', $document->dasar_lp_nomor ?: $document->nomor_lp) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">
                                    Tanggal LP:
                                </label>
                                <input type="text" name="dasar_lp_tanggal" class="form-control" value="{{ old('dasar_lp_tanggal', $document->dasar_lp_tanggal) }}">
                            </div>
                        </div>

                        <!-- 2. Surat Perintah Penyidikan -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">
                                    2. Surat Perintah Penyidikan Nomor:
                                </label>
                                <input type="text" name="dasar_sp_sidik_nomor" class="form-control" value="{{ old('dasar_sp_sidik_nomor', $document->dasar_sp_sidik_nomor) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">
                                    Tanggal SP Sidik:
                                </label>
                                <input type="text" name="dasar_sp_sidik_tanggal" class="form-control" value="{{ old('dasar_sp_sidik_tanggal', $document->dasar_sp_sidik_tanggal) }}">
                            </div>
                        </div>

                        <!-- 3. Surat Perintah Tugas Penyidikan -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">
                                    3. Surat Perintah Tugas Penyidikan Nomor:
                                </label>
                                <input type="text" name="dasar_sp_gas_sidik_nomor" class="form-control" value="{{ old('dasar_sp_gas_sidik_nomor', $document->dasar_sp_gas_sidik_nomor) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">
                                    Tanggal SP Gas Sidik:
                                </label>
                                <input type="text" name="dasar_sp_gas_sidik_tanggal" class="form-control" value="{{ old('dasar_sp_gas_sidik_tanggal', $document->dasar_sp_gas_sidik_tanggal) }}">
                            </div>
                        </div>

                        <!-- 4. Surat Penetapan Penunjukan Penasehat Hukum -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">
                                    Surat Penetapan Penunjukan Penasehat Hukum Nomor:
                                </label>
                                <input type="text" name="surat_penetapan_nomor" class="form-control" value="{{ old('surat_penetapan_nomor', $document->surat_penetapan_nomor ?: '-') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">
                                    Tanggal Surat Penetapan:
                                </label>
                                <input type="text" name="surat_penetapan_tanggal" class="form-control" value="{{ old('surat_penetapan_tanggal', $document->surat_penetapan_tanggal ?: '-') }}">
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
                                        <option value="{{ $officer->id }}" {{ old('mengetahui_signatory_id', $document->mengetahui_signatory_id) == $officer->id ? 'selected' : '' }}>
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
                    <button type="submit" class="btn btn-warning btn-lg text-dark fw-bold">
                        <i class="bi bi-check-circle me-1"></i> Perbarui Surat Kuasa Penasehat Hukum
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
        });
    </script>
@endpush
