@php
    $_title = 'Berita Acara Pemeriksaan Ahli';
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
            <h5 class="fw-bold text-blue-dark">Tambah Berita Acara Pemeriksaan Ahli (S-4.3.5)</h5>

            <div class="alert alert-danger" id="attentionBox">
                <div class="text-center">
                    <b>
                        PERHATIAN !<br><br>
                        DATA INI DIGUNAKAN SEBAGAI BERITA ACARA PEMERIKSAAN (BAP) AHLI FORMALITAS RESMI MINDIK POLRI DALAM PROSES PENYIDIKAN KECELAKAAN LALU LINTAS.
                    </b>
                </div>
            </div>
        </div>

        <div class="box-body">
            <form id="bapAhliForm" action="{{ route('doc.berita-acara-pemeriksaan-ahli-document.store') }}" method="POST">
                @csrf
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

                    $suspectName = '-';
                    if ($suspects->isNotEmpty()) {
                        $suspectName = $suspects->first()->name ?? '-';
                    }

                    $sprindikNo = $sprindik->document_number ?? ('SP.Sidik/01/' . date('m') . '/RES.0.0/' . date('Y') . '/Lantas');
                    $sprindikDate = $sprindik && $sprindik->document_date ? \Carbon\Carbon::parse($sprindik->document_date)->format('Y-m-d') : $tglHariIni;

                    // Data ahli dari Surat Permintaan Keterangan Ahli (jika ada)
                    $prefillNamaAhli = '';
                    $prefillInstansi = 'Dinas Perhubungan';
                    $prefillBidang = 'Lalu Lintas dan Angkutan Jalan';
                    $prefillJabatan = 'Ahli Transportasi Darat';
                    if (!empty($permintaanAhliDoc) && !empty($permintaanAhliDoc->data)) {
                        $dAhli = $permintaanAhliDoc->data;
                        $prefillNamaAhli = $dAhli['tujuan_surat'] ?? '';
                        $prefillInstansi = $dAhli['nama_instansi_ahli'] ?? $prefillInstansi;
                        $prefillBidang = $dAhli['bidang_keahlian'] ?? $prefillBidang;
                        $prefillJabatan = $dAhli['jabatan_ahli'] ?? $prefillJabatan;
                    }
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
                        <input type="text" class="form-control" name="hari_pemeriksaan" id="hari_pemeriksaan" value="{{ $hariIni }}" placeholder="Hari (contoh: Jumat)" required>
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

                {{-- II. IDENTITAS AHLI --}}
                <h5 class="fw-bold text-blue-dark mt-4">II. Identitas Ahli yang Diperiksa</h5>
                <hr>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nama_ahli">Nama Lengkap & Gelar Ahli<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input type="text" class="form-control font-weight-bold" name="nama_ahli" id="nama_ahli" value="{{ $prefillNamaAhli }}" placeholder="Nama Lengkap beserta Gelar Akademik Ahli" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="bidang_keahlian">Bidang Keahlian & Instansi<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control font-weight-bold" name="bidang_keahlian" id="bidang_keahlian" value="{{ $prefillBidang }}" placeholder="Contoh: Lalu Lintas dan Angkutan Jalan" required>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <input type="text" class="form-control font-weight-bold" name="instansi_ahli" id="instansi_ahli" value="{{ $prefillInstansi }}" placeholder="Instansi / Universitas Tempat Bertugas" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nik_ahli">Nomor Identitas (NIK/NIP)</label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" name="nik_ahli" id="nik_ahli" placeholder="Nomor KTP / NIP Ahli">
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <input type="text" class="form-control" name="pekerjaan_ahli" id="pekerjaan_ahli" value="{{ $prefillJabatan }}" placeholder="Jabatan / Pekerjaan">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tempat_lahir_ahli">Tempat / Tanggal Lahir</label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" name="tempat_lahir_ahli" id="tempat_lahir_ahli" placeholder="Tempat Lahir">
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <input type="date" class="form-control" name="tanggal_lahir_ahli" id="tanggal_lahir_ahli" value="1985-01-01">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="jenis_kelamin_ahli">Jenis Kelamin, Agama & Kewarganegaraan</label>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <select name="jenis_kelamin_ahli" id="jenis_kelamin_ahli" class="form-select">
                            <option value="Laki-laki" selected>Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="agama_ahli" id="agama_ahli" value="Islam" placeholder="Agama">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="kewarganegaraan_ahli" id="kewarganegaraan_ahli" value="Indonesia" placeholder="Kewarganegaraan">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="alamat_ahli">Alamat Tempat Tinggal / Kantor</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <textarea class="form-control" name="alamat_ahli" id="alamat_ahli" rows="2" placeholder="Alamat Rumah atau Kantor Ahli"></textarea>
                    </div>
                </div>

                {{-- III. URAIAN PERKARA --}}
                <h5 class="fw-bold text-blue-dark mt-4">III. Uraian Perkara yang Dimintakan Pendapat Ahli</h5>
                <hr>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tindak_pidana">Tindak Pidana</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input type="text" class="form-control font-weight-bold" name="tindak_pidana" value="Kecelakaan Lalu Lintas">
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
                    <label class="fw-bold col-sm-3 col-form-label" for="nama_tersangka">Nama Tersangka & Pelapor</label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" name="nama_tersangka" value="{{ $suspectName }}" placeholder="Nama Tersangka">
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <input type="text" class="form-control" name="nama_pelapor_korban" value="Pelapor / Korban" placeholder="Nama Pelapor / Korban">
                    </div>
                </div>

                {{-- IV. PERTANYAAN & JAWABAN PEMERIKSAAN AHLI (1 s.d. 16, DINAMIS, & PENUTUP 17-19) --}}
                <h5 class="fw-bold text-blue-dark mt-4">IV. Tanya Jawab Pemeriksaan Ahli</h5>
                <hr>

                {{-- Pertanyaan 1 --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <span class="fw-bold text-dark">1. Kondisi Kesehatan & Kesediaan Diperiksa</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Pertanyaan:</label>
                            <textarea class="form-control" name="pertanyaan_1" rows="2">Apakah pada saat ini saudara Ahli dalam keadaan sehat baik jasmani maupun rohani, bersedia diperiksa dan akan memberikan keterangan yang sebenarnya?</textarea>
                        </div>
                        <div>
                            <label class="form-label fw-bold small text-muted">Jawaban Ahli:</label>
                            <textarea class="form-control" name="jawaban_1" rows="2">Ya, saya dalam keadaan sehat baik jasmani maupun rohani, bersedia diperiksa dan akan memberikan keterangan yang sebenarnya.</textarea>
                        </div>
                    </div>
                </div>

                {{-- Pertanyaan 2 --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <span class="fw-bold text-dark">2. Pemahaman Maksud Pemeriksaan sebagai Ahli</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Pertanyaan:</label>
                            <textarea class="form-control" name="pertanyaan_2" rows="2">Pada saat ini saudara akan diperiksa sebagai Ahli sehubungan dengan adanya dugaan terjadinya Tindak Pidana Kecelakaan Lalu Lintas sebagaimana dimaksud dalam Pasal 310 ayat (4) UU Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan, yang terjadi di {{ $tkp }}, apakah saudara Ahli mengerti penjelasan Penyidik tersebut?</textarea>
                        </div>
                        <div>
                            <label class="form-label fw-bold small text-muted">Jawaban Ahli:</label>
                            <textarea class="form-control" name="jawaban_2" rows="2">Ya, saya mengerti sesuai dengan penjelasan Penyidik bahwa saya akan diperiksa sebagai Ahli sehubungan dengan adanya dugaan terjadinya Tindak Pidana Kecelakaan Lalu Lintas tersebut.</textarea>
                        </div>
                    </div>
                </div>

                {{-- Pertanyaan 3 --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <span class="fw-bold text-dark">3. Kesediaan Mengangkat Sumpah (Pasal 120 ayat (2) KUHAP)</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Pertanyaan:</label>
                            <textarea class="form-control" name="pertanyaan_3" rows="2">Sesuai dengan ketentuan Pasal 120 ayat (2) KUHAP, maka sebelum Ahli memberikan keterangan mengenai perkara yang akan dilakukan pemeriksaan oleh Penyidik wajib mengangkat sumpah, sehubungan dengan hal tersebut apakah saudara ahli bersedia mengucapkan sumpah menurut agama yang saudara anut?</textarea>
                        </div>
                        <div>
                            <label class="form-label fw-bold small text-muted">Jawaban Ahli:</label>
                            <textarea class="form-control" name="jawaban_3" rows="2">Sebelum memberikan keterangan selaku ahli, saya bersedia mengangkat sumpah dihadapan Penyidik sesuai dengan agama saya yang selanjutnya dituangkan dalam Berita Acara Sumpah.</textarea>
                        </div>
                    </div>
                </div>

                {{-- Pertanyaan 4 --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <span class="fw-bold text-dark">4. Surat Penunjukan dari Pimpinan Instansi Ahli</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Nomor Surat Penunjukan:</label>
                                <input type="text" class="form-control" name="no_surat_penunjukan_ahli" value="" placeholder="Nomor Surat Penunjukan Ahli">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Tanggal Surat Penunjukan:</label>
                                <input type="date" class="form-control" name="tanggal_penunjukan_ahli" value="{{ $tglHariIni }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Pejabat yang Menandatangani:</label>
                                <input type="text" class="form-control" name="pejabat_penunjuk_ahli" value="" placeholder="Pejabat yang Menandatangani">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Pertanyaan:</label>
                            <textarea class="form-control" name="pertanyaan_4" rows="2">Dalam pemeriksaan ini apakah saudara Ahli telah mendapatkan Surat Penunjukan dari Pimpinan Instansi saudara, jika mendapatkan berapa nomor suratnya dan ditanda tangani oleh siapa? jelaskan!</textarea>
                        </div>
                        <div>
                            <label class="form-label fw-bold small text-muted">Jawaban Ahli:</label>
                            <textarea class="form-control" name="jawaban_4" rows="2">Dalam pemeriksaan ini saya telah mendapatkan Surat Penunjukan dari pimpinan instansi saya untuk memberikan keterangan ahli dalam perkara ini.</textarea>
                        </div>
                    </div>
                </div>

                {{-- Pertanyaan 5 --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <span class="fw-bold text-dark">5. Riwayat Hidup, Pendidikan & Pekerjaan Ahli</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 mb-3">
                            <div class="col-md-3">
                                <label class="form-label small">SD:</label>
                                <input type="text" class="form-control form-control-sm" name="sd_ahli" value="" placeholder="SD (Bila diisi)">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">SMP:</label>
                                <input type="text" class="form-control form-control-sm" name="smp_ahli" value="" placeholder="SMP (Bila diisi)">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">SMA / SMK:</label>
                                <input type="text" class="form-control form-control-sm" name="sma_ahli" value="" placeholder="SMA / SMK (Bila diisi)">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">S1:</label>
                                <input type="text" class="form-control form-control-sm" name="s1_ahli" value="" placeholder="S1 (Jurusan/Tahun)">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">S2 / Pascasarjana:</label>
                                <input type="text" class="form-control form-control-sm" name="s2_ahli" value="" placeholder="S2 (Bila ada)">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">S3 / Doktoral:</label>
                                <input type="text" class="form-control form-control-sm" name="s3_ahli" value="" placeholder="S3 (Bila ada)">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Pendidikan Keahlian / Sertifikasi:</label>
                                <input type="text" class="form-control form-control-sm" name="diklat_ahli" value="" placeholder="Pendidikan Keahlian / Sertifikasi">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Riwayat Pekerjaan:</label>
                                <input type="text" class="form-control form-control-sm" name="pekerjaan_riwayat_ahli" value="" placeholder="Riwayat Pekerjaan Ahli">
                            </div>
                        </div>
                        <div>
                            <label class="form-label fw-bold small text-muted">Jawaban Lengkap Riwayat Ahli:</label>
                            <textarea class="form-control" name="jawaban_5" rows="3">Riwayat pendidikan dan riwayat pekerjaan saya adalah sebagaimana rincian pendidikan formal S1/S2 dan sertifikasi keahlian teknis yang saya tekuni pada instansi saya sampai dengan saat ini.</textarea>
                        </div>
                    </div>
                </div>

                {{-- Pertanyaan 6 - 9: Keahlian, Pengalaman & Tupoksi --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <span class="fw-bold text-dark">6 s.d. 9. Keahlian, Pengalaman Persidangan & Tugas Tanggung Jawab</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">6. Apakah saudara Ahli mempunyai ilmu dan keahlian dibidang Tindak Pidana ini? Jelaskan:</label>
                            <textarea class="form-control" name="jawaban_6" rows="2">Ya, saya mempunyai ilmu dan keahlian di bidang tersebut, karena latar belakang pendidikan dan pekerjaan saya secara khusus menekuni bidang ini.</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">7. Pengalaman memberikan keterangan ahli di tingkat penyidikan / pengadilan:</label>
                            <textarea class="form-control" name="jawaban_7" rows="2">Bahwa saya sudah sering memberikan keterangan sebagai ahli di bidang lalu lintas baik di tingkat penyidikan kepolisian maupun di persidangan pengadilan negeri.</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">8. Jabatan, tugas dan tanggung jawab saudara di instansi tempat bekerja:</label>
                            <textarea class="form-control" name="jawaban_8" rows="2">Jabatan saya adalah ahli teknis yang bertugas memberikan telaah teknis dan saintifik serta bertanggung jawab kepada pimpinan instansi.</textarea>
                        </div>
                        <div>
                            <label class="form-label fw-bold small text-muted">9. Tugas dan fungsi instansi tempat ahli bekerja:</label>
                            <textarea class="form-control" name="jawaban_9" rows="2">Instansi tempat saya bekerja mempunyai tugas dan fungsi menyelenggarakan urusan keselamatan dan manajemen sarana prasarana perhubungan.</textarea>
                        </div>
                    </div>
                </div>

                {{-- Pertanyaan 10 - 11: Hubungan Pihak --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <span class="fw-bold text-dark">10 & 11. Hubungan dengan Pihak Pelapor/Korban dan Tersangka</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">10. Hubungan dengan Pelapor / Korban:</label>
                            <textarea class="form-control" name="jawaban_10" rows="2">Saya tidak mengenal dengan orang yang bernama Pelapor / Korban tersebut di atas dan tidak ada hubungan pekerjaan maupun hubungan keluarga dengannya.</textarea>
                        </div>
                        <div>
                            <label class="form-label fw-bold small text-muted">11. Hubungan dengan Tersangka ({{ $suspectName }}):</label>
                            <textarea class="form-control" name="jawaban_11" rows="2">Saya tidak mengenal dengan orang yang bernama Tersangka tersebut di atas dan tidak ada hubungan keluarga maupun pekerjaan dengannya.</textarea>
                        </div>
                    </div>
                </div>

                {{-- Pertanyaan 12 - 16: Materi Analisis Keahlian --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <span class="fw-bold text-dark">12 s.d. 16. Analisis Teknis, Pengertian, Unsur Pidana & Kausalitas</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">12. Pola dasar dalam Tindak Pidana Kecelakaan Lalu Lintas yang terjadi:</label>
                            <textarea class="form-control" name="jawaban_12" rows="3">Pola dasar terjadinya kecelakaan lalu lintas meliputi faktor pengemudi (kelalaian, kecepatan tidak terkontrol, gagal mengantisipasi ruang jalan), faktor kelaikan kendaraan, serta geometri jalan lingkungan di TKP.</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">13. Pengertian Tindak Pidana Kecelakaan Lalu Lintas dan dasar aturannya:</label>
                            <textarea class="form-control" name="jawaban_13" rows="2">Kecelakaan Lalu Lintas adalah suatu peristiwa di jalan yang tidak diduga dan tidak disengaja melibatkan kendaraan dengan atau tanpa pengguna jalan lain yang mengakibatkan korban manusia dan/atau kerugian harta benda (Pasal 1 angka 24 UU No. 22 Tahun 2009).</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">14. Unsur-unsur yang harus terpenuhi dalam Pasal 310 UU No. 22 Tahun 2009:</label>
                            <textarea class="form-control" name="jawaban_14" rows="3">Unsur-unsur yang harus dipenuhi: (1) Setiap orang yang mengemudikan kendaraan bermotor; (2) Karena kelalaiannya (kealpaannya/culpa); (3) Mengakibatkan terjadinya kecelakaan lalu lintas dengan kerusakan benda/luka/kematian.</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">15. Kriteria seseorang dapat dinyatakan melakukan tindak pidana tersebut (Kausalitas):</label>
                            <textarea class="form-control" name="jawaban_15" rows="2">Seseorang dikriteriakan melakukan tindak pidana tersebut apabila terdapat hubungan kausalitas langsung antara kelalaian pengemudi dalam mengendalikan kendaraannya dengan timbulnya akibat fatalitas kecelakaan.</textarea>
                        </div>
                        <div>
                            <label class="form-label fw-bold small text-muted">16. Analisis Pandangan Keahlian atas Kronologis Kasus & Pemenuhan Unsur Pasal:</label>
                            <textarea class="form-control" name="jawaban_16" rows="4">Berdasarkan analisis teknis dan saintifik dari kronologis kejadian di TKP, perbuatan Tersangka telah memenuhi seluruh rumusan unsur Pasal 310 ayat (4) UU No. 22 Tahun 2009, karena pengemudi terbukti lalai tidak memprioritaskan keselamatan pengguna jalan lain yang mengakibatkan korban meninggal dunia.</textarea>
                        </div>
                    </div>
                </div>

                {{-- Pertanyaan Tambahan Dinamis (Jika Ada) --}}
                <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
                    <h6 class="fw-bold text-blue-dark m-0">Pertanyaan Tambahan / Kronologis Khusus Ahli (Opsional)</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary fw-bold" id="btnAddQ">
                        <i class="bi bi-plus-circle"></i> Tambah Pertanyaan Ahli
                    </button>
                </div>
                <p class="text-muted small">Tambahkan pertanyaan spesifik lainnya kepada Ahli jika diperlukan. Butir pertanyaan akan otomatis diberi nomor berurutan.</p>

                <div id="kronologisContainer">
                    {{-- Container for dynamic questions if added by user --}}
                </div>

                {{-- Pertanyaan Penutup Standar (17, 18, 19) --}}
                <div class="card mb-3 mt-3">
                    <div class="card-header bg-light py-2">
                        <span class="fw-bold text-dark">Pertanyaan Penutup Standar</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Keterangan Tambahan:</label>
                            <input type="text" class="form-control" name="jawaban_penutup_1" value="Tidak ada keterangan lain yang saya tambahkan.">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Kebenaran Keterangan:</label>
                            <input type="text" class="form-control" name="jawaban_penutup_2" value="Semua keterangan yang telah saya berikan tersebut di atas sudah benar dan dapat dipertanggung jawabkan.">
                        </div>
                        <div>
                            <label class="form-label fw-bold small text-muted">Paksaan / Tekanan:</label>
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
                        $('#pangkat_nrp_pemeriksa').val(rank + ' NRP ' . nrp);
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
                var startNo = 17;
                $('#kronologisContainer .kronologis-item').each(function(idx) {
                    var currentNumber = startNo + idx;
                    $(this).find('.item-label').text(currentNumber + '. Pertanyaan Tambahan Ahli');
                });
            }

            // Add Dynamic Row
            $('#btnAddQ').click(function() {
                var count = $('#kronologisContainer .kronologis-item').length;
                var nextNumber = 17 + count;
                var html = `
                    <div class="card mb-3 kronologis-item" data-index="${count}">
                        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                            <span class="fw-bold item-label">${nextNumber}. Pertanyaan Tambahan Ahli</span>
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
                                <label class="form-label fw-bold small text-muted">Jawaban Ahli:</label>
                                <textarea class="form-control" name="jawaban_kronologis[]" rows="2" placeholder="Ketikkan jawaban ahli di sini..."></textarea>
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

            // Download Direct via form submission to downloadWord route
            $('#btnDownloadDirect').click(function() {
                var form = $('#bapAhliForm');
                var originalAction = form.attr('action');
                var originalTarget = form.attr('target');

                form.attr('action', "{{ route('doc.berita-acara-pemeriksaan-ahli.download-word') }}");
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
