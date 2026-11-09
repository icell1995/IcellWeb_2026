@php
    $_title = 'Ubah Surat Permintaan Keterangan Ahli';
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
            <h5 class="fw-bold text-blue-dark">Ubah Surat Permintaan Keterangan Ahli (S-5.12)</h5>

            <div class="alert alert-danger" id="attentionBox">
                <div class="text-center">
                    <b>
                        PERHATIAN !<br><br>
                        DATA INI WAJIB DIISI DENGAN DETAIL DAN LENGKAP KARENA AKAN DIGUNAKAN SEBAGAI SURAT PERMINTAAN KETERANGAN AHLI DALAM PROSES PENYIDIKAN KECELAKAAN LALU LINTAS.
                    </b>
                </div>
            </div>
        </div>

        <div class="box-body">
            <form action="{{ route('doc.surat-permintaan-keterangan-ahli-document.update') }}" method="POST">
                @csrf
                <input type="hidden" name="accident_id" value="{{ $accidentId }}">

                @php
                    $tglHariIni = date('d-m-Y');
                    $polresDistrict = $accident->polres->polres_district ?? 'Kota';
                    $polresName = $accident->polres->name ?? 'Polres';
                    $tglSuratVal = $savedData['tanggal_surat'] ?? ($savedDoc && $savedDoc->document_date ? \Carbon\Carbon::parse($savedDoc->document_date)->format('d-m-Y') : $tglHariIni);
                @endphp

                {{-- I. INFORMASI SURAT & RUJUKAN --}}
                <h5 class="fw-bold text-blue-dark">I. Informasi Surat & Rujukan</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="no_lp_display">Nomor LP</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="no_lp_display" type="text" class="form-control font-weight-bold" value="{{ $accident->no_lp ?? '-' }}" readonly>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nomor_surat">Nomor Surat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="nomor_surat" type="text" class="form-control font-weight-bold" name="nomor_surat"
                            value="{{ $savedData['nomor_surat'] ?? ($savedDoc->document_number ?? 'B/01/I/RES.0.0./' . date('Y') . '/Lantas') }}" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="klasifikasi">Klasifikasi Surat</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="klasifikasi" type="text" class="form-control" name="klasifikasi" value="{{ $savedData['klasifikasi'] ?? 'Biasa' }}" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="lampiran">Lampiran</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="lampiran" type="text" class="form-control" name="lampiran" value="{{ $savedData['lampiran'] ?? '-' }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="hal">Perihal (Hal)<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="hal" type="text" class="form-control font-weight-bold" name="hal" value="{{ $savedData['hal'] ?? 'bantuan permintaan keterangan ahli.' }}" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggal_surat">Tanggal Surat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tanggal_surat" type="text" class="form-control datepicker" name="tanggal_surat" value="{{ $tglSuratVal }}" autocomplete="off" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="kota_surat">Kota Dikeluarkan Surat</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="kota_surat" type="text" class="form-control" name="kota_surat" value="{{ $savedData['kota_surat'] ?? $polresDistrict }}" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="surat_perintah_penyidikan_id">Nomor Surat Perintah Penyidikan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="surat_perintah_penyidikan_id" id="surat_perintah_penyidikan_id">
                            <option value="">-- Pilih No Surat Perintah Penyidikan --</option>
                            @foreach ($suratPerintahPenyidikanDocuments as $sp)
                                <option value="{{ $sp->id }}"
                                    data-laws="{{ $sp->formatted_laws ?? '' }}"
                                    {{ (isset($savedData['surat_perintah_penyidikan_id']) && $savedData['surat_perintah_penyidikan_id'] == $sp->id) ? 'selected' : ($loop->first ? 'selected' : '') }}>
                                    {{ $sp->document_number }} ({{ $sp->document_date ? \Carbon\Carbon::parse($sp->document_date)->format('d-m-Y') : '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- II. TUJUAN SURAT & INSTANSI AHLI --}}
                <hr>
                <h5 class="fw-bold text-blue-dark">II. Tujuan Surat & Instansi Ahli</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tujuan_surat">Kepada Yth.<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tujuan_surat" type="text" class="form-control font-weight-bold" name="tujuan_surat"
                            value="{{ $savedData['tujuan_surat'] ?? ('KEPALA DINAS PERHUBUNGAN KABUPATEN / KOTA ' . strtoupper($polresDistrict)) }}" required>
                        <small class="text-muted">Contoh: KEPALA PUSAT PELAPORAN DAN ANALISIS TRANSAKSI KEUANGAN / DEKAN FAKULTAS HUKUM UNIVERSITAS ... / KEPALA DINAS PERHUBUNGAN</small>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="kota_tujuan">di (Kota Tujuan)</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="kota_tujuan" type="text" class="form-control" name="kota_tujuan" value="{{ $savedData['kota_tujuan'] ?? $polresDistrict }}" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="sebutan_pimpinan">Sebutan Pimpinan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="sebutan_pimpinan" type="text" class="form-control" name="sebutan_pimpinan" value="{{ $savedData['sebutan_pimpinan'] ?? 'Ka/Dekan/Saudara*)' }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nama_instansi_ahli">Instansi Asal Ahli<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="nama_instansi_ahli" type="text" class="form-control" name="nama_instansi_ahli"
                            value="{{ $savedData['nama_instansi_ahli'] ?? 'Dinas Perhubungan / Pusat Laboratorium Forensik / Instansi Terkait' }}" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="bidang_ahli">Bidang Keahlian<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="bidang_ahli" type="text" class="form-control" name="bidang_ahli"
                            value="{{ $savedData['bidang_ahli'] ?? 'teknis kelaikan kendaraan dan analisis kecelakaan lalu lintas' }}" required>
                    </div>
                </div>

                {{-- III. URAIAN PERKARA --}}
                <hr>
                <h5 class="fw-bold text-blue-dark">III. Uraian Perkara</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="suspect_id">Tersangka</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="suspect_id" id="suspect_id">
                            @foreach ($suspects as $sus)
                                <option value="{{ $sus->id }}" {{ (isset($savedData['suspect_id']) && $savedData['suspect_id'] == $sus->id) ? 'selected' : ($loop->first ? 'selected' : '') }}>
                                    {{ $sus->name }}
                                </option>
                            @endforeach
                            @if ($suspects->isEmpty())
                                <option value="" selected>{{ $savedData['nama_tersangka'] ?? $namaTersangka }}</option>
                            @endif
                        </select>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="jenis_tindak_pidana">Jenis Tindak Pidana</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="jenis_tindak_pidana" type="text" class="form-control font-weight-bold" name="jenis_tindak_pidana"
                            value="{{ $savedData['jenis_tindak_pidana'] ?? 'Kecelakaan Lalu Lintas' }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tkp">Tempat Kejadian Perkara (TKP)</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tkp" type="text" class="form-control" name="tkp" value="{{ $savedData['tkp'] ?? ($accident->accident_road ?? $accident->accident_address ?? '-') }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggal_kejadian">Tanggal Kejadian</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tanggal_kejadian" type="text" class="form-control datepicker" name="tanggal_kejadian"
                            value="{{ $savedData['tanggal_kejadian'] ?? ($accident->accident_date ? \Carbon\Carbon::parse($accident->accident_date)->format('d-m-Y') : $tglHariIni) }}" autocomplete="off">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="waktu_kejadian">Waktu Kejadian</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="waktu_kejadian" type="text" class="form-control" name="waktu_kejadian" value="{{ $savedData['waktu_kejadian'] ?? ($accident->accident_time ?? 'sekira jam 10.00 WIB') }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="pasal_pelanggaran">Pasal yang Dilanggar</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <textarea id="pasal_pelanggaran" class="form-control" name="pasal_pelanggaran" rows="2">{{ $savedData['pasal_pelanggaran'] ?? $pasalText }}</textarea>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="pasal_kuhp_baru">Pasal KUHP Baru (Jika Ada)</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="pasal_kuhp_baru" type="text" class="form-control" name="pasal_kuhp_baru" value="{{ $savedData['pasal_kuhp_baru'] ?? '-' }}">
                    </div>
                </div>

                {{-- IV. JADWAL PEMERIKSAAN AHLI --}}
                <hr>
                <h5 class="fw-bold text-blue-dark">IV. Jadwal & Tempat Pemeriksaan Ahli</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="hari_pemeriksaan">Hari Pemeriksaan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="hari_pemeriksaan" type="text" class="form-control" name="hari_pemeriksaan" value="{{ $savedData['hari_pemeriksaan'] ?? 'Senin' }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggal_pemeriksaan">Tanggal Pemeriksaan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tanggal_pemeriksaan" type="text" class="form-control datepicker" name="tanggal_pemeriksaan"
                            value="{{ $savedData['tanggal_pemeriksaan'] ?? \Carbon\Carbon::now()->addDays(3)->format('d-m-Y') }}" autocomplete="off" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="jam_pemeriksaan">Pukul (WIB)<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="jam_pemeriksaan" type="text" class="form-control" name="jam_pemeriksaan" value="{{ $savedData['jam_pemeriksaan'] ?? '09.00 WIB' }}" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tempat_pemeriksaan">Tempat Pemeriksaan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tempat_pemeriksaan" type="text" class="form-control" name="tempat_pemeriksaan"
                            value="{{ $savedData['tempat_pemeriksaan'] ?? ('Ruang Riksa Unit Gakkum Satlantas ' . $polresName) }}" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="officer_id">Penyidik Pemeriksa</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="officer_id" id="officer_id">
                            <option value="">-- Pilih Penyidik --</option>
                            @foreach ($availableOfficers as $off)
                                <option value="{{ $off->id }}" {{ (isset($savedData['officer_id']) && $savedData['officer_id'] == $off->id) ? 'selected' : ($loop->first ? 'selected' : '') }}>
                                    {{ $off->rank->name ?? '' }} {{ $off->full_name ?? ($off->first_name . ' ' . $off->last_name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- V. NARAHUBUNG & PEJABAT PENANDATANGAN --}}
                <hr>
                <h5 class="fw-bold text-blue-dark">V. Narahubung & Pejabat Penandatangan</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="investigator_officer_id">Penyidik Penghubung</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="investigator_officer_id" id="investigator_officer_id">
                            <option value="">-- Pilih Penyidik Penghubung --</option>
                            @foreach ($availableOfficers as $off)
                                <option value="{{ $off->id }}" {{ (isset($savedData['investigator_officer_id']) && $savedData['investigator_officer_id'] == $off->id) ? 'selected' : ($loop->first ? 'selected' : '') }}>
                                    {{ $off->rank->name ?? '' }} {{ $off->full_name ?? ($off->first_name . ' ' . $off->last_name) }} (HP: {{ $off->phone_number ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="helper_officer_id">Penyidik Pembantu Penghubung</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="helper_officer_id" id="helper_officer_id">
                            <option value="">-- Pilih Penyidik Pembantu Penghubung --</option>
                            @foreach ($availableOfficers as $off)
                                <option value="{{ $off->id }}" {{ (isset($savedData['helper_officer_id']) && $savedData['helper_officer_id'] == $off->id) ? 'selected' : ($loop->iteration == 2 ? 'selected' : '') }}>
                                    {{ $off->rank->name ?? '' }} {{ $off->full_name ?? ($off->first_name . ' ' . $off->last_name) }} (HP: {{ $off->phone_number ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="signatory_id">Pejabat Penandatangan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="signatory_id" id="signatory_id" required>
                            <option value="">-- Pilih Pejabat Penandatangan --</option>
                            @foreach ($authorizedSignatories as $sig)
                                <option value="{{ $sig->id }}" {{ (isset($savedData['signatory_id']) && $savedData['signatory_id'] == $sig->id) ? 'selected' : ($loop->first ? 'selected' : '') }}>
                                    {{ $sig->full_name ?? ($sig->first_name . ' ' . $sig->last_name) }} - {{ $sig->position->name ?? 'KASAT LANTAS' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tembusan_1">Tembusan 1</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tembusan_1" type="text" class="form-control" name="tembusan_1" value="{{ $savedData['tembusan_1'] ?? ('Kapolres ' . $polresName . ' (sebagai laporan)') }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tembusan_2">Tembusan 2</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tembusan_2" type="text" class="form-control" name="tembusan_2" value="{{ $savedData['tembusan_2'] ?? 'Pengawas Penyidikan (Wassidik)' }}">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-warning text-white"><i class="bi bi-save me-1"></i> Perbarui Dokumen</button>
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

            if ($.fn.datepicker) {
                $('.datepicker').datepicker({
                    format: 'dd-mm-yyyy',
                    autoclose: true,
                    todayHighlight: true
                });
            }

            $('#surat_perintah_penyidikan_id').on('change', function() {
                var selected = $(this).find(':selected');
                var laws = selected.data('laws');
                if (laws) {
                    $('#pasal_pelanggaran').val(laws);
                }
            });
        });
    </script>
@endpush
