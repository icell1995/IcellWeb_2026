@php
    $_title = 'Edit Berita Acara Pemeriksaan Tersangka';
    $data = $savedData ?? ($document->data ?? []);
    $cleanVal = function($val, $fallback = '') {
        if (empty($val)) return $fallback;
        if (is_array($val)) return $val['name'] ?? $fallback;
        if (is_object($val)) return $val->name ?? $fallback;
        if (is_string($val) && str_starts_with(trim($val), '{')) {
            $decoded = json_decode($val, true);
            if (json_last_error() === JSON_ERROR_NONE && !empty($decoded['name'])) {
                return $decoded['name'];
            }
        }
        return (string)$val;
    };
    $pekerjaanVal = $cleanVal($data['pekerjaan_tersangka'] ?? null, 'Swasta');
    $agamaVal = $cleanVal($data['agama_tersangka'] ?? null, 'Islam');
    $genderRaw = $cleanVal($data['jenis_kelamin_tersangka'] ?? null, 'Laki-laki');
    $genderVal = stripos((string)$genderRaw, 'perempuan') !== false ? 'Perempuan' : 'Laki-laki';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}">
        <i class="bi bi-arrow-left"></i> Kembali ke Progress Perkara
    </a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Ubah Berita Acara Pemeriksaan Tersangka (S-5.3.3)</h5>

            <div class="alert alert-warning" id="attentionBox">
                <div class="text-center">
                    <b>
                        PERHATIAN !<br><br>
                        PERUBAHAN DATA AKAN MEMPERBARUI BERITA ACARA PEMERIKSAAN TERSANGKA DAN MEMPENGARUHI HASIL CETAK TEMPLATE WORD RESMI MINDIK POLRI.
                    </b>
                </div>
            </div>
        </div>

        <div class="box-body">
            <form id="bapTersangkaForm" action="{{ route('doc.berita-acara-pemeriksaan-tersangka-document.update') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="accident_id" value="{{ $accidentId }}">

                @php
                    $tglHariIni = date('Y-m-d');
                    $hariIni = \Carbon\Carbon::now()->locale('id')->isoFormat('dddd');
                    $polresCity = $accident->polres->polres_district ?? ($accident->polres->city ?? 'Kota');
                    $noLp = $accident->no_lp ?? '-';
                    $tglLp = $accident->accident_report_date ?? $accident->accident_date ?? $tglHariIni;
                    $tkp = $accident->accident_road ?? $accident->accident_address ?? '-';
                    $waktuKejadian = $accident->accident_time ?? '10.00';
                    $tglKejadian = $accident->accident_date ?? $tglHariIni;
                    $namaPelapor = $accident->reporter_name ?? 'Pelapor / Korban';

                    $sprindikNo = $sprindik->document_number ?? ('SP.Sidik/01/' . date('m') . '/RES.0.0/' . date('Y') . '/Lantas');
                    $sprindikDate = $sprindik && $sprindik->document_date ? \Carbon\Carbon::parse($sprindik->document_date)->format('Y-m-d') : $tglHariIni;
                @endphp

                {{-- I. WAKTU & DASAR PEMERIKSAAN --}}
                <h5 class="fw-bold text-blue-dark">I. Waktu, Tempat & Dasar Pemeriksaan</h5>
                <hr>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="no_lp_display">Nomor LP</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input type="text" class="form-control font-weight-bold" name="no_lp" value="{{ $data['no_lp'] ?? $noLp }}" readonly>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggal_lp">Tanggal LP</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input type="date" class="form-control" name="tanggal_lp" value="{{ $data['tanggal_lp'] ?? \Carbon\Carbon::parse($tglLp)->format('Y-m-d') }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="no_sprindik">Dasar Surat Perintah Penyidikan (SP.Sidik)<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control font-weight-bold" name="no_sprindik" value="{{ $data['no_sprindik'] ?? $sprindikNo }}" placeholder="Nomor SP.Sidik" required>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <input type="date" class="form-control" name="tanggal_sprindik" value="{{ $data['tanggal_sprindik'] ?? $sprindikDate }}" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="hari_pemeriksaan">Waktu Pemeriksaan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="hari_pemeriksaan" id="hari_pemeriksaan" value="{{ $data['hari_pemeriksaan'] ?? $hariIni }}" placeholder="Hari (contoh: Jumat)" required>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="date" class="form-control" name="tanggal_pemeriksaan" id="tanggal_pemeriksaan" value="{{ $data['tanggal_pemeriksaan'] ?? $tglHariIni }}" required>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-6 col-6">
                        <input type="text" class="form-control" name="jam_pemeriksaan" value="{{ $data['jam_pemeriksaan'] ?? '10.00' }}" placeholder="Pukul (10.00)" required>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-6 col-6">
                        <select name="zona_waktu" class="form-select">
                            <option value="WIB" {{ ($data['zona_waktu'] ?? 'WIB') == 'WIB' ? 'selected' : '' }}>WIB</option>
                            <option value="WITA" {{ ($data['zona_waktu'] ?? '') == 'WITA' ? 'selected' : '' }}>WITA</option>
                            <option value="WIT" {{ ($data['zona_waktu'] ?? '') == 'WIT' ? 'selected' : '' }}>WIT</option>
                        </select>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="penyidik_1_officer_id">Penyidik Pemeriksa 1<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-select select2" name="penyidik_1_officer_id" id="penyidik_1_officer_id" required>
                            <option value="">-- Pilih Penyidik 1 --</option>
                            @foreach ($officers as $off)
                                <option value="{{ $off->id }}"
                                    {{ ($data['penyidik_1_officer_id'] ?? '') == $off->id ? 'selected' : '' }}
                                    data-name="{{ $off->full_name ?? ($off->first_name . ' ' . $off->last_name) }}"
                                    data-rank="{{ $off->rank->name ?? '-' }}"
                                    data-nrp="{{ $off->register_number ?? '-' }}"
                                    data-position="{{ $off->position->name ?? 'Penyidik Pembantu' }}">
                                    {{ $off->rank->name ?? '' }} {{ $off->full_name ?? ($off->first_name . ' ' . $off->last_name) }} (NRP: {{ $off->register_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label"></label>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="penyidik_1_nama" id="penyidik_1_nama" value="{{ $data['penyidik_1_nama'] ?? '' }}" placeholder="Nama Penyidik 1" required>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="penyidik_1_pangkat" id="penyidik_1_pangkat" value="{{ $data['penyidik_1_pangkat'] ?? '' }}" placeholder="Pangkat Penyidik 1">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="penyidik_1_nrp" id="penyidik_1_nrp" value="{{ $data['penyidik_1_nrp'] ?? '' }}" placeholder="NRP Penyidik 1">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="penyidik_2_officer_id">Penyidik Pemeriksa 2 (Pendamping)</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-select select2" name="penyidik_2_officer_id" id="penyidik_2_officer_id">
                            <option value="">-- Pilih Penyidik 2 (Opsional) --</option>
                            @foreach ($officers as $off)
                                <option value="{{ $off->id }}"
                                    {{ ($data['penyidik_2_officer_id'] ?? '') == $off->id ? 'selected' : '' }}
                                    data-name="{{ $off->full_name ?? ($off->first_name . ' ' . $off->last_name) }}"
                                    data-rank="{{ $off->rank->name ?? '-' }}"
                                    data-nrp="{{ $off->register_number ?? '-' }}"
                                    data-position="{{ $off->position->name ?? 'Penyidik Pembantu' }}">
                                    {{ $off->rank->name ?? '' }} {{ $off->full_name ?? ($off->first_name . ' ' . $off->last_name) }} (NRP: {{ $off->register_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label"></label>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="penyidik_2_nama" id="penyidik_2_nama" value="{{ $data['penyidik_2_nama'] ?? '' }}" placeholder="Nama Penyidik 2">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="penyidik_2_pangkat" id="penyidik_2_pangkat" value="{{ $data['penyidik_2_pangkat'] ?? '' }}" placeholder="Pangkat Penyidik 2">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="penyidik_2_nrp" id="penyidik_2_nrp" value="{{ $data['penyidik_2_nrp'] ?? '' }}" placeholder="NRP Penyidik 2">
                    </div>
                </div>

                {{-- II. IDENTITAS TERSANGKA --}}
                <h5 class="fw-bold text-blue-dark mt-4">II. Identitas Tersangka yang Diperiksa</h5>
                <hr>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nama_tersangka">Nama Lengkap Tersangka<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input type="text" class="form-control font-weight-bold" name="nama_tersangka" id="nama_tersangka" value="{{ $data['nama_tersangka'] ?? '' }}" placeholder="Nama Lengkap Tersangka" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nik_tersangka">Nomor Identitas (NIK / No. KTP)</label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" name="nik_tersangka" id="nik_tersangka" value="{{ $data['nik_tersangka'] ?? '' }}" placeholder="16 Digit NIK">
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <input type="text" class="form-control" name="pekerjaan_tersangka" id="pekerjaan_tersangka" value="{{ $pekerjaanVal }}" placeholder="Pekerjaan">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tempat_lahir_tersangka">Tempat / Tanggal Lahir</label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" name="tempat_lahir_tersangka" id="tempat_lahir_tersangka" value="{{ $data['tempat_lahir_tersangka'] ?? '' }}" placeholder="Tempat Lahir">
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <input type="date" class="form-control" name="tanggal_lahir_tersangka" id="tanggal_lahir_tersangka" value="{{ $data['tanggal_lahir_tersangka'] ?? '1990-01-01' }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="jenis_kelamin_tersangka">Jenis Kelamin, Agama & Kewarganegaraan</label>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <select name="jenis_kelamin_tersangka" id="jenis_kelamin_tersangka" class="form-select">
                            <option value="Laki-laki" {{ $genderVal == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ $genderVal == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="agama_tersangka" id="agama_tersangka" value="{{ $agamaVal }}" placeholder="Agama">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="kewarganegaraan_tersangka" id="kewarganegaraan_tersangka" value="{{ $data['kewarganegaraan_tersangka'] ?? 'Indonesia' }}" placeholder="Kewarganegaraan">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="alamat_tersangka">Alamat Tempat Tinggal</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <textarea class="form-control" name="alamat_tersangka" id="alamat_tersangka" rows="2" placeholder="Alamat Tempat Tinggal Tersangka">{{ $data['alamat_tersangka'] ?? '' }}</textarea>
                    </div>
                </div>

                {{-- III. URAIAN PERKARA --}}
                <h5 class="fw-bold text-blue-dark mt-4">III. Uraian Perkara Tindak Pidana</h5>
                <hr>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tindak_pidana">Tindak Pidana</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input type="text" class="form-control font-weight-bold" name="tindak_pidana" value="{{ $data['tindak_pidana'] ?? 'Kecelakaan Lalu Lintas' }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="pasal_pelanggaran">Pasal & Perihal UU</label>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="pasal_pelanggaran" value="{{ $data['pasal_pelanggaran'] ?? 'Pasal 310 ayat (4)' }}">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="uu_nomor_tahun" value="{{ $data['uu_nomor_tahun'] ?? 'Nomor 22 Tahun 2009' }}">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="perihal_uu" value="{{ $data['perihal_uu'] ?? 'Lalu Lintas dan Angkutan Jalan' }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tkp">Tempat Kejadian Perkara (TKP)</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input type="text" class="form-control" name="tkp" value="{{ $data['tkp'] ?? $tkp }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="waktu_kejadian">Waktu Kejadian</label>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="date" class="form-control" name="tanggal_kejadian" value="{{ $data['tanggal_kejadian'] ?? \Carbon\Carbon::parse($tglKejadian)->format('Y-m-d') }}">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="hari_kejadian" value="{{ $data['hari_kejadian'] ?? \Carbon\Carbon::parse($tglKejadian)->locale('id')->isoFormat('dddd') }}" placeholder="Hari kejadian">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="waktu_kejadian" value="{{ $data['waktu_kejadian'] ?? $waktuKejadian }}" placeholder="Waktu (contoh: 11.30 WIB)">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nama_pelapor">Nama Pelapor / Korban</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input type="text" class="form-control" name="nama_pelapor" value="{{ $data['nama_pelapor'] ?? $namaPelapor }}" placeholder="Nama Pelapor / Korban">
                    </div>
                </div>

                {{-- IV. PERTANYAAN & JAWABAN PEMERIKSAAN TERSANGKA --}}
                <h5 class="fw-bold text-blue-dark mt-4">IV. Tanya Jawab Pemeriksaan Tersangka</h5>
                <hr>

                {{-- Pertanyaan 1 --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <span class="fw-bold text-dark">1. Kondisi Kesehatan & Kesediaan Diperiksa</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Pertanyaan:</label>
                            <textarea class="form-control" name="pertanyaan_1" rows="2">{{ $data['pertanyaan_1'] ?? 'Apakah pada saat ini saudara dalam keadaan sehat baik jasmani maupun rohani, bersedia diperiksa dan akan memberikan keterangan yang sebenarnya?' }}</textarea>
                        </div>
                        <div>
                            <label class="form-label fw-bold small text-muted">Jawaban Tersangka:</label>
                            <textarea class="form-control" name="jawaban_1" rows="2">{{ $data['jawaban_1'] ?? 'Ya, saya dalam keadaan sehat baik jasmani maupun rohani, bersedia diperiksa dan akan memberikan keterangan yang sebenarnya.' }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Pertanyaan 2 --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <span class="fw-bold text-dark">2. Pemahaman Maksud Pemeriksaan sebagai Tersangka</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Pertanyaan:</label>
                            <textarea class="form-control" name="pertanyaan_2" rows="3">{{ $data['pertanyaan_2'] ?? ('Pada saat ini saudara akan diperiksa sebagai Tersangka sehubungan dengan adanya dugaan terjadinya Tindak Pidana Kecelakaan Lalu Lintas sebagaimana dimaksud dalam Pasal 310 ayat (4) UU Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan, yang terjadi di ' . $tkp . ', apakah saudara mengerti penjelasan Penyidik tersebut?') }}</textarea>
                        </div>
                        <div>
                            <label class="form-label fw-bold small text-muted">Jawaban Tersangka:</label>
                            <textarea class="form-control" name="jawaban_2" rows="3">{{ $data['jawaban_2'] ?? 'Ya, saya mengerti sesuai dengan penjelasan Penyidik bahwa saya akan diperiksa sebagai Tersangka sehubungan dengan adanya dugaan terjadinya Tindak Pidana Kecelakaan Lalu Lintas tersebut.' }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Pertanyaan 3 & 4: Hak Didampingi Kuasa Hukum --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <span class="fw-bold text-dark">3 & 4. Hak Didampingi Penasehat Hukum / Pengacara</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Nama Kuasa Hukum (Bila didampingi):</label>
                                <input type="text" class="form-control" name="nama_kuasa_hukum" id="nama_kuasa_hukum" value="{{ $data['nama_kuasa_hukum'] ?? '' }}" placeholder="Kosongkan jika tidak didampingi">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Kantor Hukum:</label>
                                <input type="text" class="form-control" name="kantor_kuasa_hukum" value="{{ $data['kantor_kuasa_hukum'] ?? '' }}" placeholder="Nama Kantor Hukum">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Tanggal Surat Kuasa:</label>
                                <input type="date" class="form-control" name="tgl_surat_kuasa" value="{{ $data['tgl_surat_kuasa'] ?? $tglHariIni }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">3. Pertanyaan Kesediaan Didampingi Penasehat Hukum:</label>
                            <textarea class="form-control mb-2" name="pertanyaan_3" rows="2">{{ $data['pertanyaan_3'] ?? 'Sesuai dengan hak-hak saudara sebagai Tersangka yang mempunyai hak untuk didampingi oleh Pengacara atau Penasehat Hukum, apakah dalam pemeriksaan ini saudara akan didampingi oleh Pengacara atau Penasehat Hukum, dari mana dan apakah ada surat kuasanya?' }}</textarea>
                            <label class="form-label fw-bold small text-muted">Jawaban Tersangka:</label>
                            <textarea class="form-control" name="jawaban_3" id="jawaban_3" rows="2">{{ $data['jawaban_3'] ?? 'Sesuai dengan hak-hak saya sebagai Tersangka yang mempunyai hak untuk didampingi oleh Pengacara atau Penasehat Hukum, dalam pemeriksaan ini saya tidak didampingi oleh kuasa hukum dan bersedia memberikan keterangan sendiri.' }}</textarea>
                        </div>

                        <div>
                            <label class="form-label fw-bold small text-muted">4. Pertanyaan Keberlanjutan Pemeriksaan:</label>
                            <textarea class="form-control mb-2" name="pertanyaan_4" rows="2">{{ $data['pertanyaan_4'] ?? 'Walaupun pada saat pemeriksaan ini saudara tidak didampingi oleh Pengacara atau Penasehat Hukum, apakah pemeriksaan ini dapat dilanjutkan? jelaskan!' }}</textarea>
                            <label class="form-label fw-bold small text-muted">Jawaban Tersangka:</label>
                            <textarea class="form-control" name="jawaban_4" rows="2">{{ $data['jawaban_4'] ?? 'Walaupun pada saat pemeriksaan ini saya tidak didampingi oleh Pengacara atau Penasehat Hukum, pemeriksaan ini dapat dilanjutkan.' }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Pertanyaan 5 & 6: Catatan Hukum & Putusan Pengadilan --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <span class="fw-bold text-dark">5 & 6. Riwayat Perkara Pidana & Putusan Pengadilan</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">5. Apakah saudara pernah dihukum atau terlibat dalam suatu perkara Tindak Pidana? Jelaskan:</label>
                            <textarea class="form-control" name="jawaban_5" rows="2">{{ $data['jawaban_5'] ?? 'Saya sebelumnya belum pernah dihukum atau terlibat dalam perkara tindak pidana lainnya.' }}</textarea>
                        </div>
                        <div>
                            <label class="form-label fw-bold small text-muted">6. Apakah saudara dapat menyerahkan putusan dari pengadilan?</label>
                            <textarea class="form-control" name="jawaban_6" rows="2">{{ $data['jawaban_6'] ?? 'Karena saya belum pernah dihukum, maka saya tidak ada putusan pengadilan yang diserahkan.' }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Pertanyaan 7: Riwayat Hidup / Pendidikan & Pekerjaan --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <span class="fw-bold text-dark">7. Riwayat Hidup, Pendidikan & Pekerjaan Tersangka</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <label class="form-label small">SD:</label>
                                <input type="text" class="form-control form-control-sm" name="sd_tersangka" value="{{ $data['sd_tersangka'] ?? 'SD Negeri, lulus tahun 2002' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">SMP:</label>
                                <input type="text" class="form-control form-control-sm" name="smp_tersangka" value="{{ $data['smp_tersangka'] ?? 'SMP Negeri, lulus tahun 2005' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">SMA / SMK:</label>
                                <input type="text" class="form-control form-control-sm" name="sma_tersangka" value="{{ $data['sma_tersangka'] ?? 'SMA Negeri, lulus tahun 2008' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">S1 (Bila ada):</label>
                                <input type="text" class="form-control form-control-sm" name="s1_tersangka" value="{{ $data['s1_tersangka'] ?? '-' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">S2 (Bila ada):</label>
                                <input type="text" class="form-control form-control-sm" name="s2_tersangka" value="{{ $data['s2_tersangka'] ?? '-' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">S3 (Bila ada):</label>
                                <input type="text" class="form-control form-control-sm" name="s3_tersangka" value="{{ $data['s3_tersangka'] ?? '-' }}">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small">Riwayat Pekerjaan:</label>
                                @php
                                    $pek1Val = $data['pekerjaan_1'] ?? ('Bekerja sebagai ' . $pekerjaanVal . ' sampai sekarang');
                                    if (str_contains($pek1Val, '{"id"')) {
                                        $pek1Val = 'Bekerja sebagai ' . $pekerjaanVal . ' sampai sekarang';
                                    }
                                @endphp
                                <input type="text" class="form-control form-control-sm mb-1" name="pekerjaan_1" value="{{ $pek1Val }}">
                                <input type="text" class="form-control form-control-sm" name="pekerjaan_2" value="{{ $data['pekerjaan_2'] ?? '-' }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pertanyaan 8 & 9: Hubungan Para Pihak --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <span class="fw-bold text-dark">8 & 9. Hubungan dengan Korban/Pelapor & Saksi Lain</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">8. Hubungan dengan Korban / Pelapor ({{ $namaPelapor }}):</label>
                            <textarea class="form-control" name="jawaban_8" rows="2">{{ $data['jawaban_8'] ?? ('Saya tidak mengenal dengan orang yang bernama ' . $namaPelapor . ' tersebut di atas dan tidak ada hubungan keluarga maupun pekerjaan dengannya.') }}</textarea>
                        </div>
                        <div>
                            <label class="form-label fw-bold small text-muted">9. Hubungan dengan Saksi Lain / Pihak Terkait:</label>
                            <textarea class="form-control" name="jawaban_9" rows="2">{{ $data['jawaban_9'] ?? 'Saya tidak mengenal dengan para saksi yang melihat kejadian tersebut di atas dan tidak ada hubungan keluarga maupun pekerjaan dengannya.' }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Pertanyaan 10: 7 KAH (Kronologis Perkara Tersangka) --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <span class="fw-bold text-dark">10. Kronologis Perkara (7 KAH: Siapa, Apa, Dimana, Dengan Apa, Mengapa, Bagaimana, Kapan)</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Pertanyaan:</label>
                            <textarea class="form-control" name="pertanyaan_10" rows="2">{{ $data['pertanyaan_10'] ?? 'Pertanyaan 7 KAH: Jelaskan secara rinci kronologis kecelakaan lalu lintas yang saudara alami dari awal sebelum berangkat hingga terjadinya tabrakan di TKP!' }}</textarea>
                        </div>
                        <div>
                            <label class="form-label fw-bold small text-muted">Jawaban Tersangka:</label>
                            <textarea class="form-control" name="jawaban_10" rows="4">{{ $data['jawaban_10'] ?? ('Bahwa benar pada hari kejadian sekira pukul ' . $waktuKejadian . ', saya sedang mengemudikan kendaraan bermotor melintas di ' . $tkp . '. Saat berada di lokasi kejadian, terjadi tabrakan tersebut.') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Pertanyaan 11 s.d. 14: Bukti, Saksi, Hak Meringankan & Kondisi Mengemudi --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <span class="fw-bold text-dark">11 s.d. 14. Barang Bukti, Saksi, Hak Meringankan & Kondisi Pengemudi</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">11. Barang bukti yang diserahkan kepada Penyidik:</label>
                            <textarea class="form-control" name="jawaban_11" rows="2">{{ $data['jawaban_11'] ?? 'Barang bukti yang saya serahkan kepada Penyidik berupa 1 (satu) unit kendaraan bermotor yang saya kemudikan beserta STNK dan Surat Izin Mengemudi (SIM).' }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">12. Saksi-saksi yang mengetahui kejadian:</label>
                            <textarea class="form-control" name="jawaban_12" rows="2">{{ $data['jawaban_12'] ?? 'Saksi-saksi yang mengetahui kejadian kecelakaan tersebut adalah warga sekitar lokasi kejadian dan pengguna jalan yang berada di dekat tempat kejadian.' }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">13. Hak mengajukan saksi atau ahli yang meringankan (a de charge):</label>
                            <textarea class="form-control" name="jawaban_13" rows="2">{{ $data['jawaban_13'] ?? 'Untuk sementara ini saya belum mengajukan saksi atau ahli yang meringankan (a de charge), dan apabila nantinya ada akan saya susulkan dan sampaikan kepada Penyidik.' }}</textarea>
                        </div>
                        <div>
                            <label class="form-label fw-bold small text-muted">14. Kondisi saat mengemudikan kendaraan (pengaruh alkohol/obat-obatan):</label>
                            <textarea class="form-control" name="jawaban_14" rows="2">{{ $data['jawaban_14'] ?? 'Tidak, saya mengemudikan kendaraan dalam keadaan sadar dan tidak berada dalam pengaruh alkohol maupun obat-obatan.' }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Pertanyaan Tambahan Dinamis (Jika Ada) --}}
                <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
                    <h6 class="fw-bold text-blue-dark m-0">Pertanyaan Tambahan / Kronologis Khusus Tersangka (Opsional)</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary fw-bold" id="btnAddQ">
                        <i class="bi bi-plus-circle"></i> Tambah Pertanyaan Tersangka
                    </button>
                </div>
                <p class="text-muted small">Tambahkan pertanyaan lanjutan kepada Tersangka jika diperlukan. Nomor butir pertanyaan akan berurutan secara otomatis.</p>

                <div id="kronologisContainer">
                    @php
                        $pertanyaanTambahan = $data['pertanyaan_kronologis'] ?? [];
                        $jawabanTambahan = $data['jawaban_kronologis'] ?? [];
                    @endphp
                    @if (!empty($pertanyaanTambahan) && is_array($pertanyaanTambahan))
                        @foreach ($pertanyaanTambahan as $idx => $qText)
                            @php
                                $aText = $jawabanTambahan[$idx] ?? '';
                                $qNumber = 15 + $idx;
                            @endphp
                            <div class="card mb-3 kronologis-item" data-index="{{ $idx }}">
                                <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                    <span class="fw-bold item-label">{{ $qNumber }}. Pertanyaan Tambahan Tersangka</span>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row py-0 px-2" title="Hapus Pertanyaan Ini">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-muted">Pertanyaan:</label>
                                        <textarea class="form-control mb-2" name="pertanyaan_kronologis[]" rows="2" placeholder="Ketikkan butir pertanyaan di sini...">{{ $qText }}</textarea>
                                    </div>
                                    <div>
                                        <label class="form-label fw-bold small text-muted">Jawaban Tersangka:</label>
                                        <textarea class="form-control" name="jawaban_kronologis[]" rows="2" placeholder="Ketikkan jawaban tersangka di sini...">{{ $aText }}</textarea>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                {{-- Pertanyaan Penutup Standar --}}
                <div class="card mb-3 mt-3">
                    <div class="card-header bg-light py-2">
                        <span class="fw-bold text-dark">Pertanyaan Penutup Standar</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Keterangan Tambahan:</label>
                            <input type="text" class="form-control" name="jawaban_penutup_1" value="{{ $data['jawaban_penutup_1'] ?? 'Tidak ada keterangan lain yang saya tambahkan.' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Kebenaran Keterangan:</label>
                            <input type="text" class="form-control" name="jawaban_penutup_2" value="{{ $data['jawaban_penutup_2'] ?? 'Semua keterangan yang telah saya berikan tersebut di atas sudah benar dan dapat dipertanggung jawabkan.' }}">
                        </div>
                        <div>
                            <label class="form-label fw-bold small text-muted">Paksaan / Tekanan:</label>
                            <input type="text" class="form-control" name="jawaban_penutup_3" value="{{ $data['jawaban_penutup_3'] ?? 'Selama dalam pemeriksaan ini saya tidak merasa dipaksa, ditekan atau dipengaruhi oleh pihak lain.' }}">
                        </div>
                    </div>
                </div>

                {{-- V. PENUTUP & PENANDATANGAN --}}
                <h5 class="fw-bold text-blue-dark mt-4">V. Penutup & Tanda Tangan Pemeriksa</h5>
                <hr>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tempat_tanda_tangan">Tempat Ditutupnya BAP</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input type="text" class="form-control" name="tempat_tanda_tangan" value="{{ $data['tempat_tanda_tangan'] ?? $polresCity }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="pemeriksa_officer_id">Penyidik yang Menandatangani</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-select select2" name="pemeriksa_officer_id" id="pemeriksa_officer_id">
                            <option value="">-- Pilih Pemeriksa --</option>
                            @foreach ($officers as $off)
                                <option value="{{ $off->id }}"
                                    {{ ($data['pemeriksa_officer_id'] ?? ($data['penyidik_1_officer_id'] ?? '')) == $off->id ? 'selected' : '' }}
                                    data-name="{{ $off->full_name ?? ($off->first_name . ' ' . $off->last_name) }}"
                                    data-rank="{{ $off->rank->name ?? '-' }}"
                                    data-nrp="{{ $off->register_number ?? '-' }}"
                                    data-position="{{ $off->position->name ?? 'Penyidik Pembantu' }}">
                                    {{ $off->rank->name ?? '' }} {{ $off->full_name ?? ($off->first_name . ' ' . $off->last_name) }} (NRP: {{ $off->register_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="input-group row mb-4 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label"></label>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="jabatan_pemeriksa" id="jabatan_pemeriksa" value="{{ $data['jabatan_pemeriksa'] ?? 'Penyidik Pembantu' }}" placeholder="Jabatan">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control font-weight-bold" name="nama_pemeriksa" id="nama_pemeriksa" value="{{ $data['nama_pemeriksa'] ?? '' }}" placeholder="Nama Pemeriksa">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="pangkat_nrp_pemeriksa" id="pangkat_nrp_pemeriksa" value="{{ $data['pangkat_nrp_pemeriksa'] ?? '' }}" placeholder="Pangkat / NRP">
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-secondary px-4">
                        Batal
                    </a>
                    <button type="button" class="btn btn-success px-4 fw-bold" id="btnDownloadDirect">
                        <i class="bi bi-file-earmark-word"></i> Download Word (.docx)
                    </button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="bi bi-save"></i> Perbarui Dokumen
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

            // Auto-fill Penyidik 1
            $('#penyidik_1_officer_id').change(function() {
                var selected = $(this).find(':selected');
                if (selected.val()) {
                    var name = selected.data('name') || '';
                    var rank = selected.data('rank') || '';
                    var nrp = selected.data('nrp') || '';
                    var pos = selected.data('position') || 'Penyidik Pembantu';

                    $('#penyidik_1_nama').val(name);
                    $('#penyidik_1_pangkat').val(rank);
                    $('#penyidik_1_nrp').val(nrp);

                    // Default pemeriksa juga terisi jika belum ada
                    if (!$('#pemeriksa_officer_id').val()) {
                        $('#nama_pemeriksa').val(name);
                        $('#jabatan_pemeriksa').val(pos);
                        $('#pangkat_nrp_pemeriksa').val(rank + ' NRP ' + nrp);
                    }
                }
            });

            // Auto-fill Penyidik 2
            $('#penyidik_2_officer_id').change(function() {
                var selected = $(this).find(':selected');
                if (selected.val()) {
                    $('#penyidik_2_nama').val(selected.data('name') || '');
                    $('#penyidik_2_pangkat').val(selected.data('rank') || '');
                    $('#penyidik_2_nrp').val(selected.data('nrp') || '');
                }
            });

            // Auto-fill Pemeriksa Penandatangan
            $('#pemeriksa_officer_id').change(function() {
                var selected = $(this).find(':selected');
                if (selected.val()) {
                    var name = selected.data('name') || '';
                    var rank = selected.data('rank') || '';
                    var nrp = selected.data('nrp') || '';
                    var pos = selected.data('position') || 'Penyidik Pembantu';

                    $('#nama_pemeriksa').val(name);
                    $('#jabatan_pemeriksa').val(pos);
                    $('#pangkat_nrp_pemeriksa').val(rank + ' NRP ' + nrp);
                }
            });

            // Update Dynamic Question Numbers
            function updateQuestionNumbers() {
                var startNo = 15;
                $('#kronologisContainer .kronologis-item').each(function(idx) {
                    var currentNumber = startNo + idx;
                    $(this).find('.item-label').text(currentNumber + '. Pertanyaan Tambahan Tersangka');
                });
            }

            // Add Dynamic Row
            $('#btnAddQ').click(function() {
                var count = $('#kronologisContainer .kronologis-item').length;
                var nextNumber = 15 + count;
                var html = `
                    <div class="card mb-3 kronologis-item" data-index="${count}">
                        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                            <span class="fw-bold item-label">${nextNumber}. Pertanyaan Tambahan Tersangka</span>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row py-0 px-2" title="Hapus Pertanyaan Ini">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Pertanyaan:</label>
                                <textarea class="form-control mb-2" name="pertanyaan_kronologis[]" rows="2" placeholder="Ketikkan butir pertanyaan di sini..."></textarea>
                            </div>
                            <div>
                                <label class="form-label fw-bold small text-muted">Jawaban Tersangka:</label>
                                <textarea class="form-control" name="jawaban_kronologis[]" rows="2" placeholder="Ketikkan jawaban tersangka di sini..."></textarea>
                            </div>
                        </div>
                    </div>
                `;
                $('#kronologisContainer').append(html);
                updateQuestionNumbers();
            });

            // Delete Row
            $(document).on('click', '.btn-delete-row', function() {
                $(this).closest('.kronologis-item').remove();
                updateQuestionNumbers();
            });

            // Direct Download
            $('#btnDownloadDirect').click(function() {
                var form = $('#bapTersangkaForm');
                var originalAction = form.attr('action');
                var originalTarget = form.attr('target');

                form.attr('action', "{{ route('doc.berita-acara-pemeriksaan-tersangka.download-word') }}");
                form.attr('target', '_blank');
                form.submit();

                setTimeout(function() {
                    form.attr('action', originalAction);
                    form.removeAttr('target');
                }, 1000);
            });
        });
    </script>
@endpush
