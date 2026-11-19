@php
    $_title = 'Ubah Surat Perintah Pengembalian Barang Bukti (S-26.1.5)';
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
            <h5 class="fw-bold text-blue-dark">Ubah Surat Perintah Pengembalian Barang Bukti (S-26.1.5)</h5>

            <div class="alert alert-info">
                <div class="text-center">
                    <b>Silahkan perbarui data Surat Perintah Pengembalian Barang Bukti di bawah ini jika terdapat perubahan.</b>
                </div>
            </div>
        </div>

        <div class="box-body">
            <form action="{{ route('doc.surat-perintah-pengembalian-barang-bukti-document.update') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="accident_id" value="{{ $accidentId }}">

                @php
                    $tglHariIni = date('d-m-Y');
                    $valNoSurat = $savedData['nomor_surat_perintah'] ?? ($savedDoc->document_number ?? '');
                    $valTanggalSurat = !empty($savedData['tanggal_surat']) ? $savedData['tanggal_surat'] : (($savedDoc && $savedDoc->document_date) ? \Carbon\Carbon::parse($savedDoc->document_date)->format('d-m-Y') : $tglHariIni);
                    $valDasarPasal = $savedData['dasar_pasal_uu'] ?? $pasalText;
                    $valSprindikId = $savedData['surat_perintah_penyidikan_id'] ?? ($sprindik->id ?? '');

                    $valNoSpSita = $savedData['no_sp_sita'] ?? ($spSita->document_number ?? '');
                    $valTglSpSita = $savedData['tanggal_sp_sita'] ?? (($spSita && $spSita->document_date) ? \Carbon\Carbon::parse($spSita->document_date)->format('d-m-Y') : '');

                    $valNamaPengadilan = $savedData['nama_pengadilan'] ?? '';
                    $valNoPenetapan = $savedData['no_penetapan_pengadilan'] ?? '';
                    $valTglPenetapan = $savedData['tanggal_penetapan_pengadilan'] ?? '';

                    $valNoSkepSp3 = $savedData['no_skep_sp3'] ?? ($skepSp3->document_number ?? '');
                    $valTglSkepSp3 = $savedData['tanggal_skep_sp3'] ?? (($skepSp3 && $skepSp3->document_date) ? \Carbon\Carbon::parse($skepSp3->document_date)->format('d-m-Y') : '');

                    $valNoSprintSp3 = $savedData['no_sprint_sp3'] ?? ($sprintSp3->document_number ?? '');
                    $valTglSprintSp3 = $savedData['tanggal_sprint_sp3'] ?? (($sprintSp3 && $sprintSp3->document_date) ? \Carbon\Carbon::parse($sprintSp3->document_date)->format('d-m-Y') : '');

                    $valTglPenyitaan = $savedData['tanggal_penyitaan'] ?? $valTglSpSita;
                    $valDaftarBb = $savedData['daftar_barang_bukti_poin'] ?? $daftarBarangBuktiPoinDefault;

                    $cleanStr = function($v, $d = '') {
                        if (empty($v)) return $d;
                        if (is_object($v)) return $v->name ?? $v->full_name ?? $d;
                        if (is_array($v)) return $v['name'] ?? $v['full_name'] ?? $d;
                        if (is_string($v)) {
                            $t = trim($v);
                            if (str_starts_with($t, '{') && str_ends_with($t, '}')) {
                                $dec = json_decode($t, true);
                                if (json_last_error() === JSON_ERROR_NONE && !empty($dec['name'])) {
                                    return $dec['name'];
                                }
                            }
                            return $v;
                        }
                        return $d;
                    };

                    $valPenerimaNama = $savedData['penerima_bb_nama'] ?? $penerimaNama;
                    $valPenerimaNik = $savedData['penerima_bb_nik'] ?? $penerimaNik;
                    $valPenerimaWn = $savedData['penerima_bb_kewarganegaraan'] ?? $penerimaWn;
                    $valPenerimaGender = $savedData['penerima_bb_jenis_kelamin'] ?? $penerimaGender;
                    $valPenerimaTtl = $savedData['penerima_bb_ttl'] ?? $penerimaTtl;
                    $valPenerimaPekerjaan = $cleanStr($savedData['penerima_bb_pekerjaan'] ?? $penerimaPekerjaan, '');
                    $valPenerimaAgama = $cleanStr($savedData['penerima_bb_agama'] ?? $penerimaAgama, '');
                    $valPenerimaAlamat = $savedData['penerima_bb_alamat'] ?? $penerimaAlamat;
                    $valTglBerlaku = $savedData['tanggal_berlaku_sampai'] ?? '';

                    $valPetugas1Id = $savedData['petugas_1_id'] ?? '';
                    $valPetugas2Id = $savedData['petugas_2_id'] ?? '';
                    $valPetugas3Id = $savedData['petugas_3_id'] ?? '';

                    $valTempat = $savedData['tempat_dikeluarkan'] ?? ($accident->polres->name ?? 'Kota');
                    $valPenerimaId = $savedData['penerima_id'] ?? '';
                    $valAtasanId = $savedData['atasan_id'] ?? '';
                @endphp

                {{-- I. INFORMASI SURAT & DASAR HUKUM --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="no_lp_display">Nomor LP</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="no_lp_display" type="text" class="form-control font-weight-bold" value="{{ $accident->no_lp ?? '-' }}" readonly>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nomor_surat_perintah">Nomor Surat Perintah Pengembalian BB<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="nomor_surat_perintah" type="text" class="form-control font-weight-bold" name="nomor_surat_perintah"
                            value="{{ $valNoSurat }}" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggal_surat">Tanggal Surat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tanggal_surat" type="text" class="form-control datepicker" name="tanggal_surat" value="{{ $valTanggalSurat }}" autocomplete="off" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="dasar_pasal_uu">Dasar Undang-Undang Pidana<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="dasar_pasal_uu" type="text" class="form-control" name="dasar_pasal_uu"
                            value="{{ $valDasarPasal }}" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="surat_perintah_penyidikan_id">Surat Perintah Penyidikan (Sprindik)</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="surat_perintah_penyidikan_id" id="surat_perintah_penyidikan_id">
                            <option value="">-- Pilih No Surat Perintah Penyidikan --</option>
                            @foreach ($suratPerintahPenyidikanDocuments as $sp)
                                <option value="{{ $sp->id }}"
                                    data-laws="{{ $sp->formatted_laws ?? '' }}"
                                    data-number="{{ $sp->document_number }}"
                                    data-date="{{ $sp->document_date ? \Carbon\Carbon::parse($sp->document_date)->format('d-m-Y') : '' }}"
                                    {{ $valSprindikId == $sp->id ? 'selected' : '' }}>
                                    {{ $sp->document_number }} ({{ $sp->document_date ? \Carbon\Carbon::parse($sp->document_date)->format('d-m-Y') : '-' }})
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="no_sprindik" id="no_sprindik_hidden" value="{{ $savedData['no_sprindik'] ?? ($sprindik->document_number ?? '') }}">
                        <input type="hidden" name="tanggal_sprindik" id="tanggal_sprindik_hidden" value="{{ $savedData['tanggal_sprindik'] ?? '' }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label">Surat Perintah Penyitaan</label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" name="no_sp_sita" id="no_sp_sita" value="{{ $valNoSpSita }}" placeholder="Nomor SP.Sita">
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <input type="text" class="form-control datepicker" name="tanggal_sp_sita" id="tanggal_sp_sita" value="{{ $valTglSpSita }}" placeholder="Tanggal SP.Sita" autocomplete="off">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label">Penetapan Izin Penyitaan Pengadilan</label>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="nama_pengadilan" id="nama_pengadilan" value="{{ $valNamaPengadilan }}" placeholder="Pengadilan">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control" name="no_penetapan_pengadilan" id="no_penetapan_pengadilan" value="{{ $valNoPenetapan }}" placeholder="Nomor Penetapan">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <input type="text" class="form-control datepicker" name="tanggal_penetapan_pengadilan" id="tanggal_penetapan_pengadilan" value="{{ $valTglPenetapan }}" placeholder="Tanggal Penetapan" autocomplete="off">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label">Surat Ketetapan Penghentian Penyidikan (Skep SP3)</label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" name="no_skep_sp3" id="no_skep_sp3" value="{{ $valNoSkepSp3 }}" placeholder="Nomor Skep SP3">
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <input type="text" class="form-control datepicker" name="tanggal_skep_sp3" id="tanggal_skep_sp3" value="{{ $valTglSkepSp3 }}" placeholder="Tanggal Skep" autocomplete="off">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label">Surat Perintah Penghentian Penyidikan (Sprint SP3)</label>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                        <input type="text" class="form-control" name="no_sprint_sp3" id="no_sprint_sp3" value="{{ $valNoSprintSp3 }}" placeholder="Nomor Sprint SP3">
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <input type="text" class="form-control datepicker" name="tanggal_sprint_sp3" id="tanggal_sprint_sp3" value="{{ $valTglSprintSp3 }}" placeholder="Tanggal Sprint" autocomplete="off">
                    </div>
                </div>

                {{-- II. RINCIAN BARANG BUKTI & PENERIMA --}}
                <hr>
                <h5 class="fw-bold text-blue-dark">Rincian Barang Bukti & Pihak yang Menerima (Untuk)</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggal_penyitaan">Tanggal Penyitaan Barang Bukti<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tanggal_penyitaan" type="text" class="form-control datepicker" name="tanggal_penyitaan" value="{{ $valTglPenyitaan }}" autocomplete="off" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="daftar_barang_bukti_poin">Daftar Barang Bukti yang Dikembalikan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <textarea id="daftar_barang_bukti_poin" class="form-control" name="daftar_barang_bukti_poin" rows="4" required>{{ $valDaftarBb }}</textarea>
                    </div>
                </div>

                @if ($suspects->isNotEmpty())
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-3 col-form-label" for="select_suspect_penerima">Pilih Data Tersangka / Pemilik</label>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                            <select class="form-control select2" id="select_suspect_penerima">
                                <option value="">-- Pilih Tersangka untuk mengisi data otomatis --</option>
                                @foreach ($suspects as $s)
                                    @php
                                        $sJob = $cleanStr($s->job ?? $s->occupation, '');
                                        $sRel = $cleanStr($s->religion ?? $s->religion_short_name, '');
                                        $sGen = $cleanStr($s->gender ?? $s->gender_short_name, 'Laki-laki');
                                        $sPob = $s->birth_place ?? $s->place_of_birth ?? '';
                                        $sDob = ($s->birth_date ?? $s->date_of_birth) ? \Carbon\Carbon::parse($s->birth_date ?? $s->date_of_birth)->format('d-m-Y') : '';
                                        $sTtl = ($sPob && $sDob) ? ($sPob . ', ' . $sDob) : ($sPob ?: ($sDob ?: ''));
                                    @endphp
                                    <option value="{{ $s->id }}"
                                        data-name="{{ $s->name }}"
                                        data-nik="{{ $s->identity_number ?? $s->nik ?? '' }}"
                                        data-gender="{{ $sGen }}"
                                        data-ttl="{{ $sTtl }}"
                                        data-job="{{ $sJob }}"
                                        data-religion="{{ $sRel }}"
                                        data-address="{{ $s->address ?? '' }}">
                                        {{ $s->name }} (NIK: {{ $s->identity_number ?? $s->nik ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Memilih data di atas akan otomatis mengisi formulir identitas penerima di bawah.</small>
                        </div>
                    </div>
                @endif

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="penerima_bb_nama">Nama Penerima Barang Bukti<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="penerima_bb_nama" type="text" class="form-control" name="penerima_bb_nama"
                            value="{{ $valPenerimaNama }}" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="penerima_bb_nik">NIK / Nomor Identitas</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="penerima_bb_nik" type="text" class="form-control" name="penerima_bb_nik"
                            value="{{ $valPenerimaNik }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="penerima_bb_kewarganegaraan">Kewarganegaraan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="penerima_bb_kewarganegaraan" type="text" class="form-control" name="penerima_bb_kewarganegaraan"
                            value="{{ $valPenerimaWn }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="penerima_bb_jenis_kelamin">Jenis Kelamin</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control" name="penerima_bb_jenis_kelamin" id="penerima_bb_jenis_kelamin">
                            <option value="Laki-laki" {{ $valPenerimaGender == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ $valPenerimaGender == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="penerima_bb_ttl">Tempat / Tanggal Lahir</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="penerima_bb_ttl" type="text" class="form-control" name="penerima_bb_ttl"
                            value="{{ $valPenerimaTtl }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="penerima_bb_pekerjaan">Pekerjaan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="penerima_bb_pekerjaan" type="text" class="form-control" name="penerima_bb_pekerjaan"
                            value="{{ $valPenerimaPekerjaan }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="penerima_bb_agama">Agama</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="penerima_bb_agama" type="text" class="form-control" name="penerima_bb_agama"
                            value="{{ $valPenerimaAgama }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="penerima_bb_alamat">Alamat Lengkap</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <textarea id="penerima_bb_alamat" class="form-control" name="penerima_bb_alamat" rows="2">{{ $valPenerimaAlamat }}</textarea>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggal_berlaku_sampai">Berlaku Sampai Dengan Tanggal<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tanggal_berlaku_sampai" type="text" class="form-control datepicker" name="tanggal_berlaku_sampai"
                            value="{{ $valTglBerlaku }}" autocomplete="off" required>
                    </div>
                </div>

                {{-- III. PETUGAS YANG DIPERINTAHKAN --}}
                <hr>
                <h5 class="fw-bold text-blue-dark">Petugas yang Diperintahkan (Kepada)</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="petugas_1_id">Petugas 1 (Ketua Tim)<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="petugas_1_id" id="petugas_1_id" required>
                            <option value="">-- Pilih Petugas 1 --</option>
                            @foreach ($availableOfficers as $officer)
                                <option value="{{ $officer->id }}" {{ $valPetugas1Id == $officer->id ? 'selected' : '' }}>
                                    {{ $officer->rank->name ?? '' }} {{ $officer->full_name }} (NRP: {{ $officer->register_number ?? $officer->nrp }}) - {{ $officer->position->name ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="petugas_2_id">Petugas 2 (Anggota)<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="petugas_2_id" id="petugas_2_id" required>
                            <option value="">-- Pilih Petugas 2 --</option>
                            @foreach ($availableOfficers as $officer)
                                <option value="{{ $officer->id }}" {{ $valPetugas2Id == $officer->id ? 'selected' : '' }}>
                                    {{ $officer->rank->name ?? '' }} {{ $officer->full_name }} (NRP: {{ $officer->register_number ?? $officer->nrp }}) - {{ $officer->position->name ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="petugas_3_id">Petugas 3 (Anggota / Opsional)</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="petugas_3_id" id="petugas_3_id">
                            <option value="">-- Pilih Petugas 3 (Opsional) --</option>
                            @foreach ($availableOfficers as $officer)
                                <option value="{{ $officer->id }}" {{ $valPetugas3Id == $officer->id ? 'selected' : '' }}>
                                    {{ $officer->rank->name ?? '' }} {{ $officer->full_name }} (NRP: {{ $officer->register_number ?? $officer->nrp }}) - {{ $officer->position->name ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- IV. PEJABAT PENANDATANGAN --}}
                <hr>
                <h5 class="fw-bold text-blue-dark">Pejabat Penandatangan</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tempat_dikeluarkan">Kota / Tempat Dikeluarkan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tempat_dikeluarkan" type="text" class="form-control" name="tempat_dikeluarkan"
                            value="{{ $valTempat }}" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="penerima_id">Yang Menerima Perintah<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="penerima_id" id="penerima_id" required>
                            <option value="">-- Pilih Penyidik / Penerima Perintah --</option>
                            @foreach ($availableOfficers as $officer)
                                <option value="{{ $officer->id }}" {{ $valPenerimaId == $officer->id ? 'selected' : '' }}>
                                    {{ $officer->rank->name ?? '' }} {{ $officer->full_name }} (NRP: {{ $officer->register_number ?? $officer->nrp }}) - {{ $officer->position->name ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="atasan_id">Atasan Penyidik<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="atasan_id" id="atasan_id" required>
                            <option value="">-- Pilih Atasan Penyidik / Penandatangan --</option>
                            @foreach ($authorizedSignatories as $signatory)
                                <option value="{{ $signatory->id }}" {{ $valAtasanId == $signatory->id ? 'selected' : '' }}>
                                    {{ $signatory->rank->name ?? '' }} {{ $signatory->full_name }} (NRP: {{ $signatory->register_number ?? $signatory->nrp }}) - {{ $signatory->position->name ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex justify-content-end gap-2 my-4">
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-secondary">
                        Batal
                    </a>
                    <button type="submit" name="download_word" value="1" class="btn btn-success px-4 fw-bold">
                        <i class="bi bi-file-earmark-word me-1"></i> Simpan & Unduh Word
                    </button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
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

            $('.datepicker').datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true,
                todayHighlight: true,
                orientation: 'auto bottom'
            });

            $('#surat_perintah_penyidikan_id').on('change', function() {
                var selected = $(this).find(':selected');
                var laws = selected.data('laws');
                var number = selected.data('number');
                var date = selected.data('date');
                if (laws && $('#dasar_pasal_uu').length) $('#dasar_pasal_uu').val(laws);
                if (number && $('#no_sprindik_hidden').length) $('#no_sprindik_hidden').val(number);
                if (date && $('#tanggal_sprindik_hidden').length) $('#tanggal_sprindik_hidden').val(date);
            });

            $('#petugas_1_id').on('change', function() {
                var val = $(this).val();
                if (val && $('#penerima_id').length) {
                    $('#penerima_id').val(val).trigger('change');
                }
            });

            $('#select_suspect_penerima').on('change', function() {
                var opt = $(this).find(':selected');
                if (opt.val()) {
                    $('#penerima_bb_nama').val(opt.data('name') || '');
                    $('#penerima_bb_nik').val(opt.data('nik') || '');
                    $('#penerima_bb_ttl').val(opt.data('ttl') || '');
                    $('#penerima_bb_pekerjaan').val(opt.data('job') || '');
                    $('#penerima_bb_agama').val(opt.data('religion') || '');
                    $('#penerima_bb_alamat').val(opt.data('address') || '');
                    var gen = opt.data('gender') || '';
                    if (gen.toLowerCase().indexOf('perempuan') !== -1 || gen.toLowerCase().indexOf('wanita') !== -1) {
                        $('#penerima_bb_jenis_kelamin').val('Perempuan');
                    } else {
                        $('#penerima_bb_jenis_kelamin').val('Laki-laki');
                    }
                }
            });
        });
    </script>
@endpush

