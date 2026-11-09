@php
    $_title = 'Edit Berita Acara Pemeriksaan Saksi';
    $data = $savedData ?? ($document->data ?? []);
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
            <h5 class="fw-bold text-blue-dark">Ubah Berita Acara Pemeriksaan Saksi (S-4.3.2)</h5>

            <div class="alert alert-warning" id="attentionBox">
                <div class="text-center">
                    <b>
                        PERHATIAN !<br><br>
                        PERUBAHAN DATA AKAN MEMPERBARUI BERITA ACARA PEMERIKSAAN SAKSI DAN MEMPENGARUHI HASIL CETAK TEMPLATE WORD.
                    </b>
                </div>
            </div>
        </div>

        <div class="box-body">
            <form id="bapForm" action="{{ route('doc.berita-acara-pemeriksaan-saksi-document.update') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="accident_id" value="{{ $accidentId }}">

                {{-- I. WAKTU & DASAR PEMERIKSAAN --}}
                <h5 class="fw-bold text-blue-dark">I. Waktu, Tempat & Dasar Pemeriksaan</h5>
                <hr>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="no_lp_display">Nomor LP</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input type="text" class="form-control font-weight-bold" name="no_lp" value="{{ $data['no_lp'] ?? ($accident->no_lp ?? '-') }}" readonly>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggal_lp">Tanggal LP</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input type="date" class="form-control" name="tanggal_lp" value="{{ $data['tanggal_lp'] ?? date('Y-m-d') }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="no_sprindik">Dasar Surat Perintah Penyidikan (SP.Sidik)<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control font-weight-bold" name="no_sprindik" value="{{ $data['no_sprindik'] ?? '' }}" placeholder="Nomor SP.Sidik" required>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <input type="date" class="form-control" name="tanggal_sprindik" value="{{ $data['tanggal_sprindik'] ?? date('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="hari_pemeriksaan">Waktu Pemeriksaan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="hari_pemeriksaan" id="hari_pemeriksaan" value="{{ $data['hari_pemeriksaan'] ?? 'Senin' }}" placeholder="Hari" required>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="date" class="form-control" name="tanggal_pemeriksaan" id="tanggal_pemeriksaan" value="{{ $data['tanggal_pemeriksaan'] ?? date('Y-m-d') }}" required>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-6 col-6">
                        <input type="text" class="form-control" name="jam_pemeriksaan" value="{{ $data['jam_pemeriksaan'] ?? '10.00' }}" placeholder="Pukul (10.00)" required>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-6 col-6">
                        <select name="zona_waktu" class="form-select">
                            <option value="WIB" {{ ($data['zona_waktu'] ?? '') == 'WIB' ? 'selected' : '' }}>WIB</option>
                            <option value="WITA" {{ ($data['zona_waktu'] ?? '') == 'WITA' ? 'selected' : '' }}>WITA</option>
                            <option value="WIT" {{ ($data['zona_waktu'] ?? '') == 'WIT' ? 'selected' : '' }}>WIT</option>
                        </select>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="penyidik_1_officer_id">Penyidik Pemeriksa 1<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-select select2" name="penyidik_1_officer_id" id="penyidik_1_officer_id">
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

                {{-- II. IDENTITAS SAKSI --}}
                <h5 class="fw-bold text-blue-dark mt-4">II. Identitas Saksi yang Diperiksa</h5>
                <hr>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nama_saksi">Nama Saksi<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input type="text" class="form-control font-weight-bold" name="nama_saksi" id="nama_saksi" value="{{ $data['nama_saksi'] ?? '' }}" placeholder="Nama Lengkap Saksi" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nik_saksi">Nomor Identitas (NIK/KTP)</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input type="text" class="form-control" name="nik_saksi" id="nik_saksi" value="{{ $data['nik_saksi'] ?? '' }}" placeholder="Nomor KTP/SIM">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tempat_lahir_saksi">Tempat / Tanggal Lahir</label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" name="tempat_lahir_saksi" id="tempat_lahir_saksi" value="{{ $data['tempat_lahir_saksi'] ?? '' }}" placeholder="Tempat Lahir">
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <input type="date" class="form-control" name="tanggal_lahir_saksi" id="tanggal_lahir_saksi" value="{{ $data['tanggal_lahir_saksi'] ?? '1995-01-01' }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="jenis_kelamin_saksi">Jenis Kelamin & Agama</label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <select name="jenis_kelamin_saksi" id="jenis_kelamin_saksi" class="form-select">
                            <option value="Laki-laki" {{ ($data['jenis_kelamin_saksi'] ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ ($data['jenis_kelamin_saksi'] ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <input type="text" class="form-control" name="agama_saksi" id="agama_saksi" value="{{ $data['agama_saksi'] ?? 'Islam' }}" placeholder="Agama">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="pekerjaan_saksi">Pekerjaan & Kewarganegaraan</label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" name="pekerjaan_saksi" id="pekerjaan_saksi" value="{{ $data['pekerjaan_saksi'] ?? 'Swasta' }}" placeholder="Pekerjaan">
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <input type="text" class="form-control" name="kewarganegaraan_saksi" id="kewarganegaraan_saksi" value="{{ $data['kewarganegaraan_saksi'] ?? 'Indonesia' }}" placeholder="Kewarganegaraan">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="alamat_saksi">Alamat Tempat Tinggal</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <textarea class="form-control" name="alamat_saksi" id="alamat_saksi" rows="2" placeholder="Alamat KTP / Tempat Tinggal">{{ $data['alamat_saksi'] ?? '' }}</textarea>
                    </div>
                </div>

                {{-- III. URAIAN PERKARA --}}
                <h5 class="fw-bold text-blue-dark mt-4">III. Uraian Perkara Laka Lantas</h5>
                <hr>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tindak_pidana">Tindak Pidana</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input type="text" class="form-control" name="tindak_pidana" value="{{ $data['tindak_pidana'] ?? 'Kecelakaan Lalu Lintas' }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="pasal_pelanggaran">Pasal & Perihal UU</label>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <input type="text" class="form-control" name="pasal_pelanggaran" value="{{ $data['pasal_pelanggaran'] ?? 'Pasal 310 ayat (4)' }}">
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" name="perihal_uu" value="{{ $data['perihal_uu'] ?? 'Lalu Lintas dan Angkutan Jalan' }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tkp">Tempat Kejadian Perkara (TKP)</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input type="text" class="form-control" name="tkp" value="{{ $data['tkp'] ?? '' }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="waktu_kejadian">Waktu Kejadian</label>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="date" class="form-control" name="tanggal_kejadian" value="{{ $data['tanggal_kejadian'] ?? date('Y-m-d') }}">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="hari_kejadian" value="{{ $data['hari_kejadian'] ?? 'Senin' }}" placeholder="Hari kejadian">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="waktu_kejadian" value="{{ $data['waktu_kejadian'] ?? '10.00 WIB' }}" placeholder="Waktu (contoh: 11.30 WIB)">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nama_tersangka">Nama Tersangka</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input type="text" class="form-control" name="nama_tersangka" value="{{ $data['nama_tersangka'] ?? '' }}" placeholder="Nama Tersangka">
                    </div>
                </div>

                {{-- IV. PERTANYAAN & JAWABAN PEMERIKSAAN --}}
                <h5 class="fw-bold text-blue-dark mt-4">IV. Tanya Jawab Pemeriksaan Saksi</h5>
                <hr>

                {{-- Pertanyaan 1 --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <span class="fw-bold text-dark">1. Pertanyaan Kondisi Kesehatan & Kesediaan Diperiksa</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Pertanyaan:</label>
                            <textarea class="form-control" name="pertanyaan_1" rows="2">{{ $data['pertanyaan_1'] ?? 'Apakah pada saat ini saudara dalam keadaan sehat baik jasmani maupun rohani, bersedia diperiksa dan akan memberikan keterangan yang sebenarnya?' }}</textarea>
                        </div>
                        <div>
                            <label class="form-label fw-bold small text-muted">Jawaban Saksi:</label>
                            <textarea class="form-control" name="jawaban_1" rows="2">{{ $data['jawaban_1'] ?? 'Ya, saya dalam keadaan sehat baik jasmani maupun rohani, bersedia diperiksa dan akan memberikan keterangan yang sebenarnya.' }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Pertanyaan 2 --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <span class="fw-bold text-dark">2. Pertanyaan Pemahaman Maksud Pemeriksaan</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Pertanyaan:</label>
                            <textarea class="form-control" name="pertanyaan_2" rows="2">{{ $data['pertanyaan_2'] ?? 'Pada saat ini saudara akan diperiksa sebagai Saksi sehubungan dengan adanya dugaan terjadinya Tindak Pidana Kecelakaan Lalu Lintas, apakah saudara mengerti penjelasan Penyidik tersebut?' }}</textarea>
                        </div>
                        <div>
                            <label class="form-label fw-bold small text-muted">Jawaban Saksi:</label>
                            <textarea class="form-control" name="jawaban_2" rows="2">{{ $data['jawaban_2'] ?? 'Ya, saya mengerti sesuai dengan penjelasan Penyidik bahwa saya akan diperiksa sebagai Saksi sehubungan dengan adanya dugaan terjadinya Tindak Pidana Kecelakaan Lalu Lintas tersebut.' }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Pertanyaan 3 --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <span class="fw-bold text-dark">3. Riwayat Pendidikan & Pekerjaan</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Pertanyaan:</label>
                            <textarea class="form-control" name="pertanyaan_3" rows="2">{{ $data['pertanyaan_3'] ?? 'Agar saudara jelaskan riwayat hidup saudara secara singkat dan jelas baik mengenai pendidikan dan riwayat pekerjaan saudara sampai dengan saat sekarang ini?' }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Riwayat Pendidikan:</label>
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label small">SD:</label>
                                    <input type="text" class="form-control form-control-sm" name="sd_saksi" value="{{ $data['sd_saksi'] ?? 'SD Negeri, lulus tahun 2007' }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">SMP:</label>
                                    <input type="text" class="form-control form-control-sm" name="smp_saksi" value="{{ $data['smp_saksi'] ?? 'SMP Negeri, lulus tahun 2010' }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">SMA / SMK:</label>
                                    <input type="text" class="form-control form-control-sm" name="sma_saksi" value="{{ $data['sma_saksi'] ?? 'SMA Negeri, lulus tahun 2013' }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Perguruan Tinggi:</label>
                                    <input type="text" class="form-control form-control-sm" name="pt_saksi" value="{{ $data['pt_saksi'] ?? '-' }}">
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="form-label fw-bold small text-muted">Riwayat Pekerjaan:</label>
                            <input type="text" class="form-control" name="pekerjaan_riwayat" value="{{ $data['pekerjaan_riwayat'] ?? 'Bekerja sebagai Karyawan Swasta' }}">
                        </div>
                    </div>
                </div>

                {{-- Pertanyaan 4 --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <span class="fw-bold text-dark">4. Hubungan dengan Pihak Pelapor / Korban</span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Nama Pelapor / Korban:</label>
                                <input type="text" class="form-control" name="nama_pelapor_korban" value="{{ $data['nama_pelapor_korban'] ?? 'Pelapor / Korban' }}">
                            </div>
                        </div>
                        <div>
                            <label class="form-label fw-bold small text-muted">Jawaban Saksi:</label>
                            <textarea class="form-control" name="jawaban_4_custom" rows="2">{{ $data['jawaban_4_custom'] ?? 'Saya tidak mengenal dengan orang yang bernama tersebut di atas dan tidak ada hubungan pekerjaan atau keluarga dengannya.' }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Pertanyaan 5 --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <span class="fw-bold text-dark">5. Hubungan dengan Pihak Tersangka</span>
                    </div>
                    <div class="card-body">
                        <div>
                            <label class="form-label fw-bold small text-muted">Jawaban Saksi:</label>
                            <textarea class="form-control" name="jawaban_5_custom" rows="2">{{ $data['jawaban_5_custom'] ?? 'Saya tidak mengenal dengan orang yang bernama tersangka tersebut dan tidak ada hubungan keluarga dengannya.' }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Pertanyaan Materi Pokok / 7 KAH (Dinamis) --}}
                <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
                    <h6 class="fw-bold text-blue-dark m-0">Pertanyaan Materi Pokok / Kronologis Kejadian (7 KAH)</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary fw-bold" id="btnAddQ">
                        <i class="bi bi-plus-circle"></i> Tambah Pertanyaan (7 KAH)
                    </button>
                </div>

                <div id="kronologisContainer">
                    @php
                        $savedQuestions = $data['pertanyaan_kronologis'] ?? [];
                        $savedAnswers = $data['jawaban_kronologis'] ?? [];
                    @endphp

                    @if (!empty($savedQuestions) && count($savedQuestions) > 0)
                        @foreach ($savedQuestions as $qIndex => $qVal)
                            <div class="card mb-3 kronologis-item" data-index="{{ $qIndex }}">
                                <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                    <span class="fw-bold item-label">{{ 6 + $qIndex }}. Pertanyaan Kronologis Kejadian</span>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row py-0 px-2" title="Hapus Pertanyaan Ini">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-muted">Pertanyaan:</label>
                                        <textarea class="form-control mb-2" name="pertanyaan_kronologis[]" rows="2">{{ $qVal }}</textarea>
                                    </div>
                                    <div>
                                        <label class="form-label fw-bold small text-muted">Jawaban Saksi:</label>
                                        <textarea class="form-control" name="jawaban_kronologis[]" rows="2">{{ $savedAnswers[$qIndex] ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="card mb-3 kronologis-item" data-index="0">
                            <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                <span class="fw-bold item-label">6. Pertanyaan Kronologis Kejadian</span>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row py-0 px-2" title="Hapus Pertanyaan Ini">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-muted">Pertanyaan:</label>
                                    <textarea class="form-control mb-2" name="pertanyaan_kronologis[]" rows="2">Apakah saudara melihat atau mengetahui secara langsung terjadinya peristiwa kecelakaan lalu lintas tersebut? Jelaskan apa yang saudara lihat!</textarea>
                                </div>
                                <div>
                                    <label class="form-label fw-bold small text-muted">Jawaban Saksi:</label>
                                    <textarea class="form-control" name="jawaban_kronologis[]" rows="2">Ya, pada saat kejadian saya sedang berada di sekitar TKP dan melihat secara langsung peristiwa kecelakaan lalu lintas tersebut...</textarea>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Opsi Jeda Tanda Tangan --}}
                <div class="form-check form-switch my-3 p-3 bg-light rounded border">
                    <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="sertakan_jeda" name="sertakan_jeda_tanda_tangan" value="1" {{ ($data['sertakan_jeda_tanda_tangan'] ?? '1') == '1' ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold" for="sertakan_jeda">
                        Sertakan Klausul Penghentian Pemeriksaan Sementara & Tanda Tangan Saksi di Tengah
                    </label>
                </div>

                {{-- Pertanyaan Penutup --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <span class="fw-bold text-dark">Pertanyaan Penutup Standar</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">a. Keterangan Tambahan:</label>
                            <input type="text" class="form-control" name="jawaban_penutup_1" value="{{ $data['jawaban_penutup_1'] ?? 'Tidak ada keterangan lain yang saya tambahkan.' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">b. Kebenaran Keterangan:</label>
                            <input type="text" class="form-control" name="jawaban_penutup_2" value="{{ $data['jawaban_penutup_2'] ?? 'Semua keterangan yang telah saya berikan tersebut di atas sudah benar dan dapat dipertanggung jawabkan.' }}">
                        </div>
                        <div>
                            <label class="form-label fw-bold small text-muted">c. Paksaan / Tekanan:</label>
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
                        <input type="text" class="form-control" name="tempat_tanda_tangan" value="{{ $data['tempat_tanda_tangan'] ?? '' }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="pemeriksa_officer_id">Penyidik yang Menandatangani</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-select select2" name="pemeriksa_officer_id" id="pemeriksa_officer_id">
                            <option value="">-- Samakan dengan Penyidik Pemeriksa 1 --</option>
                            @foreach ($officers as $off)
                                <option value="{{ $off->id }}"
                                    {{ ($data['pemeriksa_officer_id'] ?? '') == $off->id ? 'selected' : '' }}
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

            function updateQuestionNumbers() {
                var startNo = 6;
                $('#kronologisContainer .kronologis-item').each(function(idx) {
                    var currentNumber = startNo + idx;
                    $(this).find('.item-label').text(currentNumber + '. Pertanyaan Kronologis Kejadian');
                });
            }

            $('#btnAddQ').click(function() {
                var count = $('#kronologisContainer .kronologis-item').length;
                var nextNumber = 6 + count;
                var html = `
                    <div class="card mb-3 kronologis-item" data-index="${count}">
                        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                            <span class="fw-bold item-label">${nextNumber}. Pertanyaan Kronologis Kejadian</span>
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
                                <label class="form-label fw-bold small text-muted">Jawaban Saksi:</label>
                                <textarea class="form-control" name="jawaban_kronologis[]" rows="2" placeholder="Ketikkan jawaban saksi di sini..."></textarea>
                            </div>
                        </div>
                    </div>
                `;
                $('#kronologisContainer').append(html);
                updateQuestionNumbers();
            });

            $(document).on('click', '.btn-delete-row', function() {
                if ($('#kronologisContainer .kronologis-item').length <= 1) {
                    alert('Minimal harus ada 1 pertanyaan kronologis kejadian.');
                    return;
                }
                $(this).closest('.kronologis-item').remove();
                updateQuestionNumbers();
            });

            $('#btnDownloadDirect').click(function() {
                var form = $('#bapForm');
                var originalAction = form.attr('action');
                var originalTarget = form.attr('target');

                form.attr('action', "{{ route('doc.berita-acara-pemeriksaan-saksi.download-word') }}");
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
