@php
    $_title = 'Edit Surat Permintaan Pemblokiran Rekening Bank';
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
            <h5 class="fw-bold text-blue-dark">Edit Surat Permintaan Pemblokiran Rekening Bank</h5>
            <small class="text-muted d-block mb-3">Nomor: <b>{{ $document->nomor ?: 'Draft' }}</b> │ Kode: <b>DCT-0707</b></small>

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
            <form action="{{ route('doc.surat-permintaan-blokir-rekening-bank-document.update', ['id' => $document->id, 'accident_id' => $accidentId]) }}" method="POST">
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
                                <input type="text" name="nomor" class="form-control" value="{{ old('nomor', $document->nomor) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Klasifikasi / Sifat Surat <span class="text-danger">*</span></label>
                                <select name="sifat" class="form-control custom-select" required>
                                    <option value="Biasa" {{ old('sifat', $document->sifat) == 'Biasa' ? 'selected' : '' }}>Biasa</option>
                                    <option value="Segera" {{ old('sifat', $document->sifat) == 'Segera' ? 'selected' : '' }}>Segera</option>
                                    <option value="Rahasia" {{ old('sifat', $document->sifat) == 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Lampiran</label>
                                <input type="text" name="lampiran" class="form-control" value="{{ old('lampiran', $document->lampiran ?? '-') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Perihal Surat <span class="text-danger">*</span></label>
                                <input type="text" name="perihal" class="form-control" value="{{ old('perihal', $document->perihal ?? 'Permintaan Pemblokiran Rekening Bank') }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">Tanggal Surat <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $document->tanggal ? \Carbon\Carbon::parse($document->tanggal)->format('Y-m-d') : date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">Dikeluarkan di (Kota Terbit)</label>
                                <input type="text" name="tempat_surat" class="form-control" value="{{ old('tempat_surat', $document->tempat_surat) }}">
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
                                <textarea name="bank_tujuan" class="form-control" rows="3" required>{{ old('bank_tujuan', $document->bank_tujuan) }}</textarea>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Tempat / Kota Bank Tujuan <span class="text-danger">*</span></label>
                                <input type="text" name="tempat_tujuan" class="form-control" value="{{ old('tempat_tujuan', $document->tempat_tujuan ?? 'Tempat') }}" required>
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
                                <textarea name="dasar_kuhp" class="form-control" rows="2">{{ old('dasar_kuhp', $document->dasar_kuhp) }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">b. Dasar KUHAP</label>
                                <textarea name="dasar_kuhap" class="form-control" rows="2">{{ old('dasar_kuhap', $document->dasar_kuhap) }}</textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">c. Dasar UU Kepolisian</label>
                                <input type="text" name="dasar_polri" class="form-control" value="{{ old('dasar_polri', $document->dasar_polri) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">d. Dasar UU Tindak Pidana</label>
                                <input type="text" name="dasar_uu_pidana" class="form-control" value="{{ old('dasar_uu_pidana', $document->dasar_uu_pidana) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">e. Laporan Polisi</label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text">Nomor</span>
                                    <input type="text" name="dasar_lp_nomor" class="form-control" value="{{ old('dasar_lp_nomor', $document->dasar_lp_nomor) }}">
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text">Tanggal</span>
                                    <input type="text" name="dasar_lp_tanggal" class="form-control" value="{{ old('dasar_lp_tanggal', $document->dasar_lp_tanggal) }}">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">f. Surat Perintah Penyidikan</label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text">Nomor</span>
                                    <input type="text" name="dasar_sp_sidik_nomor" class="form-control" value="{{ old('dasar_sp_sidik_nomor', $document->dasar_sp_sidik_nomor) }}">
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text">Tanggal</span>
                                    <input type="text" name="dasar_sp_sidik_tanggal" class="form-control" value="{{ old('dasar_sp_sidik_tanggal', $document->dasar_sp_sidik_tanggal) }}">
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
                                <input type="text" name="satker" class="form-control" value="{{ old('satker', $document->satker) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-weight-bold">Dugaan Tindak Pidana</label>
                                <input type="text" name="tindak_pidana" class="form-control" value="{{ old('tindak_pidana', $document->tindak_pidana) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-weight-bold">Pasal yang Dipersangkakan</label>
                                <input type="text" name="pasal_pidana" class="form-control" value="{{ old('pasal_pidana', $document->pasal_pidana) }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label font-weight-bold">UU Nomor</label>
                                <input type="text" name="uu_nomor" class="form-control" value="{{ old('uu_nomor', $document->uu_nomor) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label font-weight-bold">UU Tahun</label>
                                <input type="text" name="uu_tahun" class="form-control" value="{{ old('uu_tahun', $document->uu_tahun) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">UU Tentang</label>
                                <input type="text" name="uu_tentang" class="form-control" value="{{ old('uu_tentang', $document->uu_tentang) }}">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Tempat Kejadian Perkara (TKP)</label>
                                <input type="text" name="tkp" class="form-control" value="{{ old('tkp', $document->tkp) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Waktu Kejadian</label>
                                <input type="text" name="waktu_kejadian" class="form-control" value="{{ old('waktu_kejadian', $document->waktu_kejadian) }}">
                            </div>
                        </div>

                        <hr>
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-person me-1"></i> Identitas Tersangka (Pemilik Rekening)</h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">a. Nama Tersangka <span class="text-danger">*</span></label>
                                <input type="text" name="tersangka_nama" class="form-control" value="{{ old('tersangka_nama', $document->tersangka_nama) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">b. Nomor Identitas (NIK/SIM/Paspor)</label>
                                <input type="text" name="tersangka_no_identitas" class="form-control" value="{{ old('tersangka_no_identitas', $document->tersangka_no_identitas) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">c. Kewarganegaraan</label>
                                <input type="text" name="tersangka_kewarganegaraan" class="form-control" value="{{ old('tersangka_kewarganegaraan', $document->tersangka_kewarganegaraan ?? 'WNI') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">d. Jenis Kelamin</label>
                                <input type="text" name="tersangka_jenis_kelamin" class="form-control" value="{{ old('tersangka_jenis_kelamin', $document->tersangka_jenis_kelamin) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">e. Tempat/Tanggal Lahir</label>
                                <input type="text" name="tersangka_ttl" class="form-control" value="{{ old('tersangka_ttl', $document->tersangka_ttl) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">f. Pekerjaan</label>
                                <input type="text" name="tersangka_pekerjaan" class="form-control" value="{{ old('tersangka_pekerjaan', $document->tersangka_pekerjaan) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">g. Agama</label>
                                <input type="text" name="tersangka_agama" class="form-control" value="{{ old('tersangka_agama', $document->tersangka_agama) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">h. Alamat</label>
                                <input type="text" name="tersangka_alamat" class="form-control" value="{{ old('tersangka_alamat', $document->tersangka_alamat) }}">
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
                            <textarea name="objek_blokir" class="form-control" rows="3" required>{{ old('objek_blokir', $document->objek_blokir) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Hari & Tanggal Kehadiran <span class="text-danger">*</span></label>
                                <input type="text" name="blokir_hari_tanggal" class="form-control" value="{{ old('blokir_hari_tanggal', $document->blokir_hari_tanggal) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Pukul / Waktu <span class="text-danger">*</span></label>
                                <input type="text" name="blokir_waktu" class="form-control" value="{{ old('blokir_waktu', $document->blokir_waktu) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Tempat Kehadiran <span class="text-danger">*</span></label>
                                <input type="text" name="blokir_tempat" class="form-control" value="{{ old('blokir_tempat', $document->blokir_tempat) }}" required>
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
                            <div class="col-md-3 mb-2">
                                <label class="form-label font-weight-bold">Sebutan</label>
                                <input type="text" name="penyidik_sebutan" class="form-control" value="{{ old('penyidik_sebutan', $document->penyidik_sebutan ?? 'Penyidik Pembantu') }}">
                            </div>
                            <div class="col-md-5 mb-2">
                                <label class="form-label font-weight-bold">Nama Penyidik</label>
                                <input type="text" name="penyidik_nama" class="form-control" value="{{ old('penyidik_nama', $document->penyidik_nama) }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label font-weight-bold">No. HP Penyidik</label>
                                <input type="text" name="penyidik_nohp" class="form-control" value="{{ old('penyidik_nohp', $document->penyidik_nohp) }}">
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
                                        <option value="{{ $signatory->id }}" {{ old('signatory', $document->signatory_id) == $signatory->id ? 'selected' : '' }}>
                                            {{ $signatory->full_name }} — {{ $signatory->position->name ?? 'Pejabat' }} ({{ $signatory->rank->name ?? '-' }} NRP {{ $signatory->register_number ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Atas Nama (Head Text Tanda Tangan)</label>
                                <input type="text" name="signatory_head_text" class="form-control" value="{{ old('signatory_head_text', $document->signatory_head_text) }}">
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
                                <input type="text" name="tembusan_1" class="form-control" value="{{ old('tembusan_1', $document->tembusan_1) }}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label font-weight-bold">Tembusan 2</label>
                                <input type="text" name="tembusan_2" class="form-control" value="{{ old('tembusan_2', $document->tembusan_2) }}">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4 mb-2">
                                <label class="form-label font-weight-bold">Tembusan 3</label>
                                <input type="text" name="tembusan_3" class="form-control" value="{{ old('tembusan_3', $document->tembusan_3) }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label font-weight-bold">Tembusan 4</label>
                                <input type="text" name="tembusan_4" class="form-control" value="{{ old('tembusan_4', $document->tembusan_4) }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label font-weight-bold">Tembusan 5</label>
                                <input type="text" name="tembusan_5" class="form-control" value="{{ old('tembusan_5', $document->tembusan_5) }}">
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
                        <i class="bi bi-check-circle me-1"></i> Perbarui Surat Permintaan Blokir
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

