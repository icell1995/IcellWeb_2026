@php
    $_title = 'Ubah Surat Perintah Penyitaan (S-17)';
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
            <h5 class="fw-bold text-blue-dark">Ubah Surat Perintah Penyitaan (S-17)</h5>

            <div class="alert alert-info">
                <div class="text-center">
                    <b>Silahkan perbarui data Surat Perintah Penyitaan di bawah ini jika terdapat perubahan.</b>
                </div>
            </div>
        </div>

        <div class="box-body">
            <form action="{{ route('doc.surat-perintah-penyitaan-document.update') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="accident_id" value="{{ $accidentId }}">

                @php
                    $tglHariIni = date('d-m-Y');
                    $valNoSurat = $savedData['nomor_surat_perintah'] ?? ($savedDoc->document_number ?? '');
                    $valTanggalSurat = !empty($savedData['tanggal_surat']) ? $savedData['tanggal_surat'] : (($savedDoc && $savedDoc->document_date) ? \Carbon\Carbon::parse($savedDoc->document_date)->format('d-m-Y') : $tglHariIni);
                    $valSprindikId = $savedData['surat_perintah_penyidikan_id'] ?? ($sprindik->id ?? '');
                    $valDasarPasal = $savedData['dasar_pasal_uu'] ?? $pasalText;

                    $valBendaSitaan = $savedData['benda_sitaan_text'] ?? $barangBuktiText;
                    $valTindakPidana = $savedData['tindak_pidana'] ?? '';
                    $valPasalPelanggaran = $savedData['pasal_pelanggaran'] ?? '';
                    $valPasalKuhpBaru = $savedData['pasal_kuhp_baru'] ?? '';
                    $valTkp = $savedData['tkp'] ?? ($accident->accident_road ?? $accident->accident_address ?? '-');
                    $valTglKejadian = $savedData['tanggal_kejadian'] ?? (($accident && $accident->accident_date) ? \Carbon\Carbon::parse($accident->accident_date)->format('d-m-Y') : $tglHariIni);
                    $valKurunWaktu = $savedData['kurun_waktu'] ?? '';
                    $valNamaTersangka = $savedData['nama_tersangka'] ?? $namaTersangkaDefault;
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
                    <label class="fw-bold col-sm-3 col-form-label" for="nomor_surat_perintah">Nomor Surat Perintah Penyitaan<span class="text-danger fs-5">*</span></label>
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
                    <label class="fw-bold col-sm-3 col-form-label" for="surat_perintah_penyidikan_id">Nomor Surat Perintah Penyidikan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="surat_perintah_penyidikan_id" id="surat_perintah_penyidikan_id">
                            <option value="">-- Pilih No Surat Perintah Penyidikan --</option>
                            @foreach ($suratPerintahPenyidikanDocuments as $sp)
                                <option value="{{ $sp->id }}"
                                    data-laws="{{ $sp->formatted_laws ?? '' }}"
                                    {{ $valSprindikId == $sp->id ? 'selected' : '' }}>
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
                            value="{{ $valDasarPasal }}" required>
                    </div>
                </div>

                {{-- II. PERKARA & SASARAN PENYITAAN --}}
                <hr>
                <h5 class="fw-bold text-blue-dark">Perkara & Sasaran Penyitaan (Untuk)</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="benda_sitaan_text">Benda / Barang Bukti yang Disita<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <textarea id="benda_sitaan_text" class="form-control" name="benda_sitaan_text" rows="3" required>{{ $valBendaSitaan }}</textarea>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tindak_pidana">Dugaan Tindak Pidana<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tindak_pidana" type="text" class="form-control" name="tindak_pidana"
                            value="{{ $valTindakPidana }}" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="pasal_pelanggaran">Pasal yang Dilanggar<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="pasal_pelanggaran" type="text" class="form-control" name="pasal_pelanggaran"
                            value="{{ $valPasalPelanggaran }}" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="pasal_kuhp_baru">Pasal KUHP Baru (Opsional)</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="pasal_kuhp_baru" type="text" class="form-control" name="pasal_kuhp_baru"
                            value="{{ $valPasalKuhpBaru }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tkp">Tempat Kejadian Perkara (TKP)<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tkp" type="text" class="form-control" name="tkp"
                            value="{{ $valTkp }}" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggal_kejadian">Tanggal Kejadian<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tanggal_kejadian" type="text" class="form-control datepicker" name="tanggal_kejadian"
                            value="{{ $valTglKejadian }}" autocomplete="off" required>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="kurun_waktu">Atau Dalam Kurun Waktu</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="kurun_waktu" type="text" class="form-control" name="kurun_waktu"
                            value="{{ $valKurunWaktu }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nama_tersangka">Nama Tersangka</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        @if ($suspects->isNotEmpty())
                            <select class="form-control select2" name="suspect_id" id="suspect_id">
                                <option value="">-- Pilih Tersangka (Opsional) --</option>
                                @foreach ($suspects as $s)
                                    <option value="{{ $s->id }}" data-name="{{ $s->name }}"
                                        {{ ($savedData['suspect_id'] ?? '') == $s->id ? 'selected' : '' }}>
                                        {{ $s->name }} (NIK: {{ $s->identity_number ?? $s->nik ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="nama_tersangka" id="nama_tersangka_hidden" value="{{ $valNamaTersangka }}">
                        @else
                            <input id="nama_tersangka" type="text" class="form-control" name="nama_tersangka"
                                value="{{ $valNamaTersangka }}">
                        @endif
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggal_berlaku_sampai">Berlaku Sampai Dengan Tanggal<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tanggal_berlaku_sampai" type="text" class="form-control datepicker" name="tanggal_berlaku_sampai"
                            value="{{ $valTglBerlaku }}" autocomplete="off" required>
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

