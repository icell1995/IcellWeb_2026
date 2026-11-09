@php
    $_title = 'Surat Panggilan Saksi';
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
            <h5 class="fw-bold text-blue-dark">Tambah Surat Panggilan Saksi Ke-1 (S-5)</h5>

            <div class="alert alert-danger" id="attentionBox">
                <div class="text-center">
                    <b>
                        PERHATIAN !<br><br>
                        DATA INI WAJIB DIISI DENGAN DETAIL DAN LENGKAP KARENA AKAN DIGUNAKAN SEBAGAI KELENGKAPAN BERKAS
                        PERKARA PENYIDIKAN KECELAKAAN LALU LINTAS.
                    </b>
                </div>
            </div>
        </div>

        <div class="box-body">
            <form action="{{ route('doc.surat-panggilan-saksi-document.store') }}" method="POST">
                @csrf
                <input type="hidden" name="accident_id" value="{{ $accidentId }}">

                @php
                    $tglHariIni = date('d-m-Y');
                    $tglSprindik = ($sprindik && $sprindik->document_date) ? \Carbon\Carbon::parse($sprindik->document_date)->format('d-m-Y') : date('d-m-Y');
                    $tglPemeriksaanDefault = \Carbon\Carbon::now()->addDays(3)->format('d-m-Y');
                @endphp

                {{-- I. INFORMASI SURAT & RUJUKAN --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="no_lp_display">Nomor LP</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="no_lp_display" type="text" class="form-control font-weight-bold" value="{{ $accident->no_lp ?? '-' }}" readonly>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nomor_surat">Nomor Surat Panggilan Saksi<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="nomor_surat" type="text" class="form-control font-weight-bold" name="nomor_surat"
                            value="S.Pgl/Saksi.1/001/{{ date('m') }}/RES.0.0./{{ date('Y') }}/Lantas" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggal_surat">Tanggal Surat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tanggal_surat" type="text" class="form-control datepicker" name="tanggal_surat" value="{{ $tglHariIni }}" autocomplete="off" required>
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
                                    {{ $loop->first ? 'selected' : '' }}>
                                    {{ $sp->document_number }} ({{ $sp->document_date ? \Carbon\Carbon::parse($sp->document_date)->format('d-m-Y') : '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- II. IDENTITAS SAKSI --}}
                <hr>
                <h5 class="fw-bold text-blue-dark">Identitas Saksi yang Dipanggil</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nama_saksi">Nama Lengkap Saksi<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="nama_saksi" type="text" class="form-control font-weight-bold" name="nama_saksi"
                            value="{{ $accident->witness_name ?? '' }}" placeholder="Nama Lengkap Saksi" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="pekerjaan_saksi">Pekerjaan Saksi</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="pekerjaan_saksi" type="text" class="form-control" name="pekerjaan_saksi" value="" placeholder="Pekerjaan Saksi">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="alamat_saksi">Alamat Tinggal Saksi</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <textarea id="alamat_saksi" name="alamat_saksi" class="form-control" rows="2" placeholder="Alamat Tinggal Saksi">{{ $accident->witness_address ?? '' }}</textarea>
                    </div>
                </div>

                {{-- III. JADWAL DAN TEMPAT PEMERIKSAAN --}}
                <hr>
                <h5 class="fw-bold text-blue-dark">Jadwal & Tempat Pemeriksaan</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="investigator_id">Menghadap Kepada (Penyidik)<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="investigator_id" id="investigator_id" required>
                            <option value="">-- Pilih Penyidik / Pemeriksa --</option>
                            @foreach ($availableOfficers as $officer)
                                <option value="{{ $officer->id }}">
                                    {{ $officer->rank->name ?? '' }} {{ $officer->full_name }} (NRP: {{ $officer->register_number }}) - {{ $officer->position->name ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tempat_pemeriksaan">Tempat / Ruang Pemeriksaan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tempat_pemeriksaan" type="text" class="form-control" name="tempat_pemeriksaan"
                            value="" placeholder="Ruang / Tempat Pemeriksaan">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label">Waktu Pemeriksaan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="small text-muted">Hari</label>
                                <input type="text" name="hari_pemeriksaan" class="form-control" value="{{ \Carbon\Carbon::parse($tglPemeriksaanDefault)->locale('id')->isoFormat('dddd') }}" placeholder="Hari">
                            </div>
                            <div class="col-md-5">
                                <label class="small text-muted">Tanggal</label>
                                <input type="text" name="tanggal_pemeriksaan" id="tanggal_pemeriksaan" class="form-control datepicker" value="{{ $tglPemeriksaanDefault }}" autocomplete="off">
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted">Pukul / Jam</label>
                                <input type="text" name="jam_pemeriksaan" class="form-control" value="" placeholder="Contoh: 09.00 WIB">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- IV. MATERI PERKARA --}}
                <hr>
                <h5 class="fw-bold text-blue-dark">Materi Perkara</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="jenis_tindak_pidana">Jenis Tindak Pidana</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="jenis_tindak_pidana" type="text" class="form-control" name="jenis_tindak_pidana"
                            value="Dugaan terjadinya tindak pidana kecelakaan lalu lintas">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="pasal_pelanggaran">Pasal Pelanggaran</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="pasal_pelanggaran" type="text" class="form-control" name="pasal_pelanggaran"
                            value="{{ $pasalText }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="suspect_id">Atas Nama Terlapor / Tersangka</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="suspect_id" id="suspect_id">
                            <option value="">-- Pilih Tersangka --</option>
                            @foreach ($suspects as $s)
                                <option value="{{ $s->id }}" {{ $loop->first ? 'selected' : '' }}>
                                    {{ $s->name }} (NIK: {{ $s->identity_number ?? '-' }}) - {{ $s->job->name ?? $s->job_name ?? 'Swasta' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="dokumen_bawaan">Dokumen yang Wajib Dibawa</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="dokumen_bawaan" type="text" class="form-control" name="dokumen_bawaan"
                            value="" placeholder="Identitas Diri (KTP/SIM asli) dan dokumen pendukung lainnya yang berkaitan">
                    </div>
                </div>

                {{-- V. KONTAK & PEJABAT PENANDATANGAN --}}
                <hr>
                <h5 class="fw-bold text-blue-dark">Kontak Petugas & Penandatangan</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="liaison_officer_id">Petugas Penghubung / Kontak</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="liaison_officer_id" id="liaison_officer_id">
                            <option value="">-- Pilih Petugas Penghubung --</option>
                            @foreach ($availableOfficers as $officer)
                                <option value="{{ $officer->id }}" data-phone="{{ $officer->phone_number ?? '' }}">
                                    {{ $officer->rank->name ?? '' }} {{ $officer->full_name }} (NRP: {{ $officer->register_number }})
                                </option>
                            @endforeach
                        </select>
                        <div class="mt-2">
                            <input type="text" name="kontak_penyidik_hp" id="kontak_penyidik_hp" class="form-control"
                                placeholder="No. HP / WhatsApp (opsional, jika kosong menggunakan data petugas)">
                        </div>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="signatory_id">Pejabat Penandatangan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="signatory_id" id="signatory_id" required>
                            <option value="">-- Pilih Pejabat Penandatangan --</option>
                            @foreach ($authorizedSignatories as $signatory)
                                <option value="{{ $signatory->id }}">
                                    {{ $signatory->rank->name ?? '' }} {{ $signatory->full_name }} (NRP: {{ $signatory->register_number }}) - {{ $signatory->position->name ?? '-' }}
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
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="bi bi-save me-1"></i> Simpan Dokumen
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script type="text/javascript">
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
                var laws = $(this).find(':selected').data('laws');
                if (laws && $('#pasal_pelanggaran').length) $('#pasal_pelanggaran').val(laws);
            });

            $('#liaison_officer_id').on('change', function() {
                var phone = $(this).find(':selected').data('phone');
                if (phone && !$('#kontak_penyidik_hp').val()) {
                    $('#kontak_penyidik_hp').val(phone);
                }
            });
        });
    </script>
@endpush
