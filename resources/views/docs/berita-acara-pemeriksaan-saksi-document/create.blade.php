@php
    $_title = 'Berita Acara Pemeriksaan Saksi';
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
            <h5 class="fw-bold text-blue-dark">Tambah Berita Acara Pemeriksaan Saksi (S-4.3.2)</h5>

            <div class="alert alert-danger" id="attentionBox">
                <div class="text-center">
                    <b>
                        PERHATIAN !<br><br>
                        DATA INI DIGUNAKAN SEBAGAI BERITA ACARA PEMERIKSAAN (BAP) SAKSI FORMALITAS RESMI MINDIK POLRI DALAM PROSES PENYIDIKAN KECELAKAAN LALU LINTAS.
                    </b>
                </div>
            </div>
        </div>

        <div class="box-body">
            <form id="bapForm" action="{{ route('doc.berita-acara-pemeriksaan-saksi-document.store') }}" method="POST">
                @csrf
                <input type="hidden" name="accident_id" value="{{ $accidentId }}">

                @php
                    $tglHariIni = date('Y-m-d');
                    $hariIni = \Carbon\Carbon::now()->locale('id')->isoFormat('dddd');
                    $polresName = $accident->polres->name ?? 'Polres';
                    $polresCity = $accident->polres->polres_district ?? ($accident->polres->city ?? 'Kota');
                    $noLp = $accident->no_lp ?? '-';
                    $tglLp = $accident->accident_report_date ?? $accident->accident_date ?? $tglHariIni;
                    $tkp = $accident->accident_road ?? $accident->accident_address ?? '-';
                    $waktuKejadian = $accident->accident_time ?? '10.00';
                    $tglKejadian = $accident->accident_date ?? $tglHariIni;

                    $suspectName = '-';
                    if ($suspects->isNotEmpty()) {
                        $suspectName = $suspects->first()->name ?? '-';
                    }

                    $sprindikNo = $sprindik->document_number ?? ('SP.Sidik/01/' . date('m') . '/RES.0.0/' . date('Y') . '/Lantas');
                    $sprindikDate = $sprindik && $sprindik->document_date ? \Carbon\Carbon::parse($sprindik->document_date)->format('Y-m-d') : $tglHariIni;
                @endphp

                {{-- I. WAKTU & DASAR PEMERIKSAAN --}}
                <h5 class="fw-bold text-blue-dark">I. Waktu, Tempat & Dasar Pemeriksaan</h5>
                <hr>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="no_lp_display">Nomor LP</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input type="text" class="form-control font-weight-bold" name="no_lp" value="{{ $noLp }}" readonly>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggal_lp">Tanggal LP</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input type="date" class="form-control" name="tanggal_lp" value="{{ \Carbon\Carbon::parse($tglLp)->format('Y-m-d') }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="no_sprindik">Dasar Surat Perintah Penyidikan (SP.Sidik)<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control font-weight-bold" name="no_sprindik" value="{{ $sprindikNo }}" placeholder="Nomor SP.Sidik" required>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <input type="date" class="form-control" name="tanggal_sprindik" value="{{ $sprindikDate }}" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="hari_pemeriksaan">Waktu Pemeriksaan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="hari_pemeriksaan" id="hari_pemeriksaan" value="{{ $hariIni }}" placeholder="Hari (contoh: Senin)" required>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="date" class="form-control" name="tanggal_pemeriksaan" id="tanggal_pemeriksaan" value="{{ $tglHariIni }}" required>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-6 col-6">
                        <input type="text" class="form-control" name="jam_pemeriksaan" value="{{ date('H:i') }}" placeholder="Pukul (10.00)" required>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-6 col-6">
                        <select name="zona_waktu" class="form-select">
                            <option value="WIB" selected>WIB</option>
                            <option value="WITA">WITA</option>
                            <option value="WIT">WIT</option>
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
                        <input type="text" class="form-control" name="penyidik_1_nama" id="penyidik_1_nama" placeholder="Nama Penyidik 1" required>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="penyidik_1_pangkat" id="penyidik_1_pangkat" placeholder="Pangkat Penyidik 1">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="penyidik_1_nrp" id="penyidik_1_nrp" placeholder="NRP Penyidik 1">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="penyidik_2_officer_id">Penyidik Pemeriksa 2 (Pendamping)</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-select select2" name="penyidik_2_officer_id" id="penyidik_2_officer_id">
                            <option value="">-- Pilih Penyidik 2 (Opsional) --</option>
                            @foreach ($officers as $off)
                                <option value="{{ $off->id }}"
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
                        <input type="text" class="form-control" name="penyidik_2_nama" id="penyidik_2_nama" placeholder="Nama Penyidik 2">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="penyidik_2_pangkat" id="penyidik_2_pangkat" placeholder="Pangkat Penyidik 2">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="penyidik_2_nrp" id="penyidik_2_nrp" placeholder="NRP Penyidik 2">
                    </div>
                </div>

                {{-- II. IDENTITAS SAKSI --}}
                <h5 class="fw-bold text-blue-dark mt-4">II. Identitas Saksi yang Diperiksa</h5>
                <hr>

                @if ($saksiList->isNotEmpty())
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-3 col-form-label" for="select_saksi">Pilih dari Daftar Saksi</label>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                            <select class="form-select select2" id="select_saksi">
                                <option value="">-- Pilih Saksi untuk Auto-Fill --</option>
                                @foreach ($saksiList as $s)
                                    <option value="{{ $s->id }}"
                                        data-name="{{ $s->name }}"
                                        data-nik="{{ $s->id_number ?? $s->nik ?? '' }}"
                                        data-birthplace="{{ $s->city ?? '' }}"
                                        data-birthdate="{{ $s->birth_date ? \Carbon\Carbon::parse($s->birth_date)->format('Y-m-d') : '' }}"
                                        data-gender="{{ ($s->gender == '1' || strtolower($s->gender) == 'laki-laki') ? 'Laki-laki' : 'Perempuan' }}"
                                        data-religion="{{ $s->religion ?? 'Islam' }}"
                                        data-job="{{ $s->job ?? 'Swasta' }}"
                                        data-address="{{ $s->address ?? '' }}"
                                        data-citizen="{{ $s->citizen ?? 'Indonesia' }}">
                                        {{ $s->name }} ({{ $s->job ?? 'Saksi' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nama_saksi">Nama Saksi<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input type="text" class="form-control font-weight-bold" name="nama_saksi" id="nama_saksi" placeholder="Nama Lengkap Saksi" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nik_saksi">Nomor Identitas (NIK/KTP)</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input type="text" class="form-control" name="nik_saksi" id="nik_saksi" placeholder="Nomor KTP/SIM">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tempat_lahir_saksi">Tempat / Tanggal Lahir</label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" name="tempat_lahir_saksi" id="tempat_lahir_saksi" placeholder="Tempat Lahir">
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <input type="date" class="form-control" name="tanggal_lahir_saksi" id="tanggal_lahir_saksi" value="1995-01-01">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="jenis_kelamin_saksi">Jenis Kelamin & Agama</label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <select name="jenis_kelamin_saksi" id="jenis_kelamin_saksi" class="form-select">
                            <option value="Laki-laki" selected>Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <input type="text" class="form-control" name="agama_saksi" id="agama_saksi" value="Islam" placeholder="Agama">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="pekerjaan_saksi">Pekerjaan & Kewarganegaraan</label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" name="pekerjaan_saksi" id="pekerjaan_saksi" value="Swasta" placeholder="Pekerjaan">
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <input type="text" class="form-control" name="kewarganegaraan_saksi" id="kewarganegaraan_saksi" value="Indonesia" placeholder="Kewarganegaraan">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="alamat_saksi">Alamat Tempat Tinggal</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <textarea class="form-control" name="alamat_saksi" id="alamat_saksi" rows="2" placeholder="Alamat KTP / Tempat Tinggal"></textarea>
                    </div>
                </div>

                {{-- III. URAIAN PERKARA --}}
                <h5 class="fw-bold text-blue-dark mt-4">III. Uraian Perkara Laka Lantas</h5>
                <hr>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tindak_pidana">Tindak Pidana</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input type="text" class="form-control" name="tindak_pidana" value="Kecelakaan Lalu Lintas">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="pasal_pelanggaran">Pasal & Perihal UU</label>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <input type="text" class="form-control" name="pasal_pelanggaran" value="Pasal 310 ayat (4)">
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" name="perihal_uu" value="Lalu Lintas dan Angkutan Jalan">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tkp">Tempat Kejadian Perkara (TKP)</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input type="text" class="form-control" name="tkp" value="{{ $tkp }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="waktu_kejadian">Waktu Kejadian</label>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="date" class="form-control" name="tanggal_kejadian" value="{{ \Carbon\Carbon::parse($tglKejadian)->format('Y-m-d') }}">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="hari_kejadian" value="{{ \Carbon\Carbon::parse($tglKejadian)->locale('id')->isoFormat('dddd') }}" placeholder="Hari kejadian">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="waktu_kejadian" value="{{ $waktuKejadian }}" placeholder="Waktu (contoh: 11.30 WIB)">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nama_tersangka">Nama Tersangka</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input type="text" class="form-control" name="nama_tersangka" value="{{ $suspectName }}" placeholder="Nama Tersangka">
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
                            <textarea class="form-control" name="pertanyaan_1" rows="2">Apakah pada saat ini saudara dalam keadaan sehat baik jasmani maupun rohani, bersedia diperiksa dan akan memberikan keterangan yang sebenarnya?</textarea>
                        </div>
                        <div>
                            <label class="form-label fw-bold small text-muted">Jawaban Saksi:</label>
                            <textarea class="form-control" name="jawaban_1" rows="2">Ya, saya dalam keadaan sehat baik jasmani maupun rohani, bersedia diperiksa dan akan memberikan keterangan yang sebenarnya.</textarea>
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
                            <textarea class="form-control" name="pertanyaan_2" rows="2">Pada saat ini saudara akan diperiksa sebagai Saksi sehubungan dengan adanya dugaan terjadinya Tindak Pidana Kecelakaan Lalu Lintas sebagaimana dimaksud dalam Pasal 310 ayat (4) UU Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan, yang terjadi di {{ $tkp }}, apakah saudara mengerti penjelasan Penyidik tersebut?</textarea>
                        </div>
                        <div>
                            <label class="form-label fw-bold small text-muted">Jawaban Saksi:</label>
                            <textarea class="form-control" name="jawaban_2" rows="2">Ya, saya mengerti sesuai dengan penjelasan Penyidik bahwa saya akan diperiksa sebagai Saksi sehubungan dengan adanya dugaan terjadinya Tindak Pidana Kecelakaan Lalu Lintas tersebut.</textarea>
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
                            <textarea class="form-control" name="pertanyaan_3" rows="2">Agar saudara jelaskan riwayat hidup saudara secara singkat dan jelas baik mengenai pendidikan dan riwayat pekerjaan saudara sampai dengan saat sekarang ini?</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Riwayat Pendidikan:</label>
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label small">SD:</label>
                                    <input type="text" class="form-control form-control-sm" name="sd_saksi" value="" placeholder="Contoh: SD Negeri, lulus thn...">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">SMP:</label>
                                    <input type="text" class="form-control form-control-sm" name="smp_saksi" value="" placeholder="Contoh: SMP Negeri, lulus thn...">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">SMA / SMK:</label>
                                    <input type="text" class="form-control form-control-sm" name="sma_saksi" value="" placeholder="Contoh: SMA Negeri, lulus thn...">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Perguruan Tinggi:</label>
                                    <input type="text" class="form-control form-control-sm" name="pt_saksi" value="" placeholder="Perguruan Tinggi (Bila ada)">
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="form-label fw-bold small text-muted">Riwayat Pekerjaan:</label>
                            <input type="text" class="form-control" name="pekerjaan_riwayat" value="" placeholder="Contoh: Bekerja sebagai Swasta sampai sekarang">
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
                                <input type="text" class="form-control" name="nama_pelapor_korban" value="{{ $accident->reporter_name ?? '' }}" placeholder="Nama Pelapor / Korban">
                            </div>
                        </div>
                        <div>
                            <label class="form-label fw-bold small text-muted">Jawaban Saksi:</label>
                            <textarea class="form-control" name="jawaban_4_custom" rows="2">Saya tidak mengenal dengan orang yang bernama tersebut di atas dan tidak ada hubungan pekerjaan atau keluarga dengannya.</textarea>
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
                            <textarea class="form-control" name="jawaban_5_custom" rows="2">Saya tidak mengenal dengan orang yang bernama {{ $suspectName }} dan tidak ada hubungan keluarga dengannya.</textarea>
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
                <p class="text-muted small">Tambahkan pertanyaan kronologis kejadian (Siapa, Apa, Dimana, Dengan apa, Mengapa, Bagaimana, Kapan). Anda dapat menambah atau menghapus butir pertanyaan secara leluasa.</p>

                <div id="kronologisContainer">
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
                                <textarea class="form-control mb-2" name="pertanyaan_kronologis[]" rows="2" placeholder="Ketikkan butir pertanyaan di sini...">Apakah saudara melihat atau mengetahui secara langsung terjadinya peristiwa kecelakaan lalu lintas tersebut? Jelaskan apa yang saudara lihat!</textarea>
                            </div>
                            <div>
                                <label class="form-label fw-bold small text-muted">Jawaban Saksi:</label>
                                <textarea class="form-control" name="jawaban_kronologis[]" rows="2" placeholder="Ketikkan jawaban saksi di sini...">Ya, pada saat kejadian saya sedang berada di sekitar TKP dan melihat secara langsung peristiwa kecelakaan lalu lintas tersebut...</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3 kronologis-item" data-index="1">
                        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                            <span class="fw-bold item-label">7. Pertanyaan Kronologis Kejadian</span>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row py-0 px-2" title="Hapus Pertanyaan Ini">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Pertanyaan:</label>
                                <textarea class="form-control mb-2" name="pertanyaan_kronologis[]" rows="2" placeholder="Ketikkan butir pertanyaan di sini...">Bagaimanakah posisi kendaraan dan keadaan korban sesaat setelah peristiwa kecelakaan tersebut terjadi? Jelaskan!</textarea>
                            </div>
                            <div>
                                <label class="form-label fw-bold small text-muted">Jawaban Saksi:</label>
                                <textarea class="form-control" name="jawaban_kronologis[]" rows="2" placeholder="Ketikkan jawaban saksi di sini...">Sesaat setelah tabrakan terjadi, saya melihat kendaraan berhenti di badan jalan dan korban tergeletak kemudian warga sekitar segera memberikan pertolongan...</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Opsi Jeda Tanda Tangan --}}
                <div class="form-check form-switch my-3 p-3 bg-light rounded border">
                    <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="sertakan_jeda" name="sertakan_jeda_tanda_tangan" value="1" checked>
                    <label class="form-check-label fw-bold" for="sertakan_jeda">
                        Sertakan Klausul Penghentian Pemeriksaan Sementara & Tanda Tangan Saksi di Tengah
                    </label>
                    <div class="text-muted small ms-4">
                        Menyertakan klausul: "Sampai disini pemeriksaan dihentikan, kemudian dipersilahkan untuk membaca kembali..." beserta kolom tanda tangan saksi sebelum pertanyaan penutup.
                    </div>
                </div>

                {{-- Pertanyaan Penutup --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <span class="fw-bold text-dark">Pertanyaan Penutup Standar</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">a. Keterangan Tambahan:</label>
                            <input type="text" class="form-control" name="jawaban_penutup_1" value="Tidak ada keterangan lain yang saya tambahkan.">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">b. Kebenaran Keterangan:</label>
                            <input type="text" class="form-control" name="jawaban_penutup_2" value="Semua keterangan yang telah saya berikan tersebut di atas sudah benar dan dapat dipertanggung jawabkan.">
                        </div>
                        <div>
                            <label class="form-label fw-bold small text-muted">c. Paksaan / Tekanan:</label>
                            <input type="text" class="form-control" name="jawaban_penutup_3" value="Selama dalam pemeriksaan ini saya tidak merasa dipaksa, ditekan atau dipengaruhi oleh pihak lain.">
                        </div>
                    </div>
                </div>

                {{-- V. PENUTUP & PENANDATANGAN --}}
                <h5 class="fw-bold text-blue-dark mt-4">V. Penutup & Tanda Tangan Pemeriksa</h5>
                <hr>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tempat_tanda_tangan">Tempat Ditutupnya BAP</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input type="text" class="form-control" name="tempat_tanda_tangan" value="{{ $polresCity }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="pemeriksa_officer_id">Penyidik yang Menandatangani</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-select select2" name="pemeriksa_officer_id" id="pemeriksa_officer_id">
                            <option value="">-- Samakan dengan Penyidik Pemeriksa 1 --</option>
                            @foreach ($officers as $off)
                                <option value="{{ $off->id }}"
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
                        <input type="text" class="form-control" name="jabatan_pemeriksa" id="jabatan_pemeriksa" value="Penyidik Pembantu" placeholder="Jabatan">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control font-weight-bold" name="nama_pemeriksa" id="nama_pemeriksa" placeholder="Nama Pemeriksa">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="pangkat_nrp_pemeriksa" id="pangkat_nrp_pemeriksa" placeholder="Pangkat / NRP">
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
                        <i class="bi bi-save"></i> Simpan Dokumen
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

                    // Default pemeriksa juga terisi
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

            // Auto-fill Saksi
            $('#select_saksi').change(function() {
                var selected = $(this).find(':selected');
                if (selected.val()) {
                    $('#nama_saksi').val(selected.data('name') || '');
                    $('#nik_saksi').val(selected.data('nik') || '');
                    $('#tempat_lahir_saksi').val(selected.data('birthplace') || '');
                    if (selected.data('birthdate')) {
                        $('#tanggal_lahir_saksi').val(selected.data('birthdate'));
                    }
                    if (selected.data('gender')) {
                        $('#jenis_kelamin_saksi').val(selected.data('gender'));
                    }
                    if (selected.data('religion')) {
                        $('#agama_saksi').val(selected.data('religion'));
                    }
                    if (selected.data('job')) {
                        $('#pekerjaan_saksi').val(selected.data('job'));
                    }
                    if (selected.data('address')) {
                        $('#alamat_saksi').val(selected.data('address'));
                    }
                    if (selected.data('citizen')) {
                        $('#kewarganegaraan_saksi').val(selected.data('citizen'));
                    }
                }
            });

            // Update Dynamic Question Numbers
            function updateQuestionNumbers() {
                var startNo = 6;
                $('#kronologisContainer .kronologis-item').each(function(idx) {
                    var currentNumber = startNo + idx;
                    $(this).find('.item-label').text(currentNumber + '. Pertanyaan Kronologis Kejadian');
                });
            }

            // Add Dynamic Row
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

            // Delete Row
            $(document).on('click', '.btn-delete-row', function() {
                if ($('#kronologisContainer .kronologis-item').length <= 1) {
                    alert('Minimal harus ada 1 pertanyaan kronologis kejadian.');
                    return;
                }
                $(this).closest('.kronologis-item').remove();
                updateQuestionNumbers();
            });

            // Download Direct via form submission to downloadWord route
            $('#btnDownloadDirect').click(function() {
                var form = $('#bapForm');
                var originalAction = form.attr('action');
                var originalTarget = form.attr('target');

                form.attr('action', "{{ route('doc.berita-acara-pemeriksaan-saksi.download-word') }}");
                form.attr('target', '_blank');
                form.submit();

                // Restore form action
                setTimeout(function() {
                    form.attr('action', originalAction);
                    form.removeAttr('target');
                }, 1000);
            });
        });
    </script>
@endpush
