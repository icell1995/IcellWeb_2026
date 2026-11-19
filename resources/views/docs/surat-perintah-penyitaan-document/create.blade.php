@php
    $_title = 'Surat Perintah Penyitaan (S-17)';
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
            <h5 class="fw-bold text-blue-dark">Tambah Surat Perintah Penyitaan (S-17)</h5>

            <div class="alert alert-danger" id="attentionBox">
                <div class="text-center">
                    <b>
                        PERHATIAN !<br><br>
                        DATA INI WAJIB DIISI DENGAN DETAIL DAN LENGKAP KARENA AKAN DIGUNAKAN SEBAGAI SURAT PERINTAH PENYITAAN TERHADAP BENDA ATAU BARANG BUKTI DALAM PROSES PENYIDIKAN KECELAKAAN LALU LINTAS.
                    </b>
                </div>
            </div>
        </div>

        <div class="box-body">
            <form action="{{ route('doc.surat-perintah-penyitaan-document.store') }}" method="POST">
                @csrf
                <input type="hidden" name="accident_id" value="{{ $accidentId }}">

                @php
                    $tglHariIni = date('d-m-Y');
                    $tglLp = ($accident && $accident->accident_report_date) ? \Carbon\Carbon::parse($accident->accident_report_date)->format('d-m-Y') : $tglHariIni;
                    $tglKejadian = ($accident && $accident->accident_date) ? \Carbon\Carbon::parse($accident->accident_date)->format('d-m-Y') : $tglHariIni;
                    $tkpDefault = $accident->accident_road ?? $accident->accident_address ?? '-';
                    $polresDistrict = $accident->polres->name ?? 'Kota';
                    $defaultBerlakuSampai = \Carbon\Carbon::now()->addDays(7)->format('d-m-Y');
                @endphp

                {{-- I. INFORMASI SURAT & DASAR HUKUM --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="no_lp_display">Nomor LP</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="no_lp_display" type="text" class="form-control font-weight-bold" value="{{ $accident->no_lp ?? '-' }}" readonly>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nomor_surat_perintah">Nomor Surat Perintah Penyitaan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="nomor_surat_perintah" type="text" class="form-control font-weight-bold" name="nomor_surat_perintah"
                            value="{{ old('nomor_surat_perintah', $defaultDocNumber) }}" placeholder="Contoh: SP.Sita/0001/I/RES.0.0./2026/Satker" required>
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
                                    data-laws="{{ $sp->formatted_laws ?? '' }}">
                                    {{ $sp->document_number }} ({{ $sp->document_date ? \Carbon\Carbon::parse($sp->document_date)->format('d-m-Y') : '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="dasar_pasal_uu">Dasar Pasal & Undang-Undang<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="dasar_pasal_uu" type="text" class="form-control" name="dasar_pasal_uu"
                            value="{{ $pasalText }}" placeholder="Contoh: Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan" required>
                    </div>
                </div>

                {{-- II. PERKARA & SASARAN PENYITAAN --}}
                <hr>
                <h5 class="fw-bold text-blue-dark">Perkara & Sasaran Penyitaan (Untuk)</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="benda_sitaan_text">Benda / Barang Bukti yang Disita<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <textarea id="benda_sitaan_text" class="form-control" name="benda_sitaan_text" rows="3" placeholder="Rincikan barang bukti atau dokumen yang hendak disita..." required>{{ $barangBuktiText }}</textarea>
                        <small class="text-muted">Rincikan barang bukti atau dokumen yang hendak disita.</small>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tindak_pidana">Dugaan Tindak Pidana<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tindak_pidana" type="text" class="form-control" name="tindak_pidana"
                            value="" placeholder="Contoh: Kecelakaan Lalu Lintas" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="pasal_pelanggaran">Pasal yang Dilanggar<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="pasal_pelanggaran" type="text" class="form-control" name="pasal_pelanggaran"
                            value="" placeholder="Contoh: Pasal 310 ayat (4)" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="pasal_kuhp_baru">Pasal KUHP Baru (Opsional)</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="pasal_kuhp_baru" type="text" class="form-control" name="pasal_kuhp_baru"
                            value="" placeholder="Contoh: Pasal 359 KUHP Baru atau -">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tkp">Tempat Kejadian Perkara (TKP)<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tkp" type="text" class="form-control" name="tkp"
                            value="{{ $tkpDefault }}" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggal_kejadian">Tanggal Kejadian<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tanggal_kejadian" type="text" class="form-control datepicker" name="tanggal_kejadian"
                            value="{{ $tglKejadian }}" autocomplete="off" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="kurun_waktu">Atau Dalam Kurun Waktu</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="kurun_waktu" type="text" class="form-control" name="kurun_waktu"
                            value="" placeholder="Contoh: tahun 2026 atau bulan Oktober 2026">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nama_tersangka">Nama Tersangka</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        @if ($suspects->isNotEmpty())
                            <select class="form-control select2" name="suspect_id" id="suspect_id">
                                <option value="">-- Pilih Tersangka (Opsional) --</option>
                                @foreach ($suspects as $s)
                                    <option value="{{ $s->id }}" data-name="{{ $s->name }}">
                                        {{ $s->name }} (NIK: {{ $s->identity_number ?? $s->nik ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="nama_tersangka" id="nama_tersangka_hidden" value="{{ $namaTersangkaDefault }}">
                        @else
                            <input id="nama_tersangka" type="text" class="form-control" name="nama_tersangka"
                                value="{{ $namaTersangkaDefault }}" placeholder="Nama tersangka (jika ada)">
                        @endif
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggal_berlaku_sampai">Berlaku Sampai Dengan Tanggal<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tanggal_berlaku_sampai" type="text" class="form-control datepicker" name="tanggal_berlaku_sampai"
                            value="{{ $defaultValidUntil }}" placeholder="dd-mm-yyyy" autocomplete="off" required>
                    </div>
                </div>

                {{-- III. PETUGAS YANG DIPERINTAHKAN (LAMPIRAN) --}}
                <hr>
                <h5 class="fw-bold text-blue-dark">Petugas yang Diperintahkan (Lampiran)</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="petugas_1_id">Petugas 1 (Ketua Tim)<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="petugas_1_id" id="petugas_1_id" required>
                            <option value="">-- Pilih Petugas 1 --</option>
                            @foreach ($availableOfficers as $officer)
                                <option value="{{ $officer->id }}">
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
                                <option value="{{ $officer->id }}">
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
                                <option value="{{ $officer->id }}">
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
                            value="{{ $polresDistrict }}" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="penerima_id">Yang Menerima Perintah<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="penerima_id" id="penerima_id" required>
                            <option value="">-- Pilih Penyidik / Penerima Perintah --</option>
                            @foreach ($availableOfficers as $officer)
                                <option value="{{ $officer->id }}">
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
                                <option value="{{ $signatory->id }}">
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
                        <i class="bi bi-save me-1"></i> Simpan Dokumen
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
                var laws = $(this).find(':selected').data('laws');
                if (laws && $('#dasar_pasal_uu').length) $('#dasar_pasal_uu').val(laws);
            });

            $('#suspect_id').on('change', function() {
                var name = $(this).find(':selected').data('name');
                if (name) $('#nama_tersangka_hidden').val(name);
            });

            $('#petugas_1_id').on('change', function() {
                var val = $(this).val();
                if (val && $('#penerima_id').length) {
                    $('#penerima_id').val(val).trigger('change');
                }
            });
        });
    </script>
@endpush

