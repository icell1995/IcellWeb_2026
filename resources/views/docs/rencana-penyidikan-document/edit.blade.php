@php
    $_title = 'Ubah Rencana Penyidikan';
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
            <h5 class="fw-bold text-blue-dark">Ubah Rencana Penyidikan (S-3.2)</h5>

            <div class="alert alert-info">
                <div class="text-center">
                    <b>Dokumen ini telah tersimpan sebelumnya. Anda dapat memperbarui data atau mengunduh kembali template Word.</b>
                </div>
            </div>
        </div>

        <div class="box-body">
            <form action="{{ route('doc.rencana-penyidikan-document.update') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="accident_id" value="{{ $accidentId }}">

                @php
                    $polres = $accident->polres ?? null;
                    $valNomorSurat = old('nomor_surat', $savedData['nomor_surat'] ?? ('B/01/' . date('m') . '/RES.0.0./' . date('Y') . '/Lantas'));
                    $valTanggalSurat = old('tanggal_surat', $savedData['tanggal_surat'] ?? date('d-m-Y'));
                    $valSpId = old('surat_perintah_penyidikan_id', $savedData['surat_perintah_penyidikan_id'] ?? '');
                    $valJenisTindakPidana = old('jenis_tindak_pidana', $savedData['jenis_tindak_pidana'] ?? 'Kecelakaan Lalu Lintas');
                    $valPasal = old('pasal_dilanggar', $savedData['pasal_dilanggar'] ?? $pasalText);
                    $valKorban = old('korban', $savedData['korban'] ?? ($accident->victim_name ?? ($accident->casualties ?? 'Korban Laka Lantas')));
                    $valSuspectId = old('suspect_id', $savedData['suspect_id'] ?? ($suspect->id ?? ''));
                    $valKetSaksi = old('keterangan_saksi', $savedData['keterangan_saksi'] ?? 'Telah didengar keterangan saksi-saksi di sekitar TKP terkait kronologis terjadinya tabrakan.');
                    $valKetTsk = old('keterangan_tersangka', $savedData['keterangan_tersangka'] ?? 'Tersangka mengakui lalai saat mengemudikan kendaraan sehingga tidak dapat mengendalikan laju kendaraan.');
                    $valBarangBukti = old('barang_bukti', $savedData['barang_bukti'] ?? '1 (satu) unit kendaraan bermotor beserta STNK dan SIM pengemudi.');
                    $valKesimpulan1 = old('kesimpulan_1', $savedData['kesimpulan_1'] ?? 'Telah terjadi peristiwa kecelakaan lalu lintas yang mengakibatkan korban/kerugian materiil.');
                    $valKesimpulan2 = old('kesimpulan_2', $savedData['kesimpulan_2'] ?? 'Diduga pengemudi kendaraan melanggar aturan berlalu lintas sebagaimana dimaksud dalam pasal yang dipersangkakan.');
                    $valRencanaA = old('rencana_a', $savedData['rencana_a'] ?? 'Melakukan pemeriksaan saksi-saksi dan tersangka secara mendalam.');
                    $valRencanaB = old('rencana_b', $savedData['rencana_b'] ?? 'Melakukan penyitaan barang bukti serta melengkapi administrasi penyidikan.');
                    $valSaranA = old('saran_a', $savedData['saran_a'] ?? 'Segera berkoordinasi dengan Jaksa Penuntut Umum terkait perkembangan penyidikan.');
                    $valSaranB = old('saran_b', $savedData['saran_b'] ?? 'Mempercepat proses pemberkasan perkara (Tahap I).');
                    $valPenerimaId = old('penerima_id', $savedData['penerima_id'] ?? '');
                    $valAtasanId = old('atasan_id', $savedData['atasan_id'] ?? '');
                @endphp

                {{-- I. INFORMASI SURAT & RUJUKAN --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="no_lp_display">Nomor LP</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="no_lp_display" type="text" class="form-control font-weight-bold" value="{{ $accident->no_lp ?? '-' }}" readonly>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nomor_surat">Nomor Surat Rencana Penyidikan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="nomor_surat" type="text" class="form-control font-weight-bold" name="nomor_surat"
                            value="{{ $valNomorSurat }}" required>
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
                                    {{ $valSpId == $sp->id ? 'selected' : '' }}>
                                    {{ $sp->document_number }} ({{ $sp->document_date ? \Carbon\Carbon::parse($sp->document_date)->format('d-m-Y') : '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- II. PERKARA --}}
                <hr>
                <h5 class="fw-bold text-blue-dark">Perkara</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="jenis_tindak_pidana">Jenis Tindak Pidana</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="jenis_tindak_pidana" type="text" class="form-control" name="jenis_tindak_pidana"
                            value="{{ $valJenisTindakPidana }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="pasal_dilanggar">Pasal yang Dilanggar</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="pasal_dilanggar" type="text" class="form-control" name="pasal_dilanggar"
                            value="{{ $valPasal }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="korban">Korban</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="korban" type="text" class="form-control" name="korban"
                            value="{{ $valKorban }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="suspect_id">Tersangka</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="suspect_id" id="suspect_id">
                            <option value="">-- Pilih Tersangka --</option>
                            @foreach ($suspects as $s)
                                <option value="{{ $s->id }}" {{ $valSuspectId == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }} (NIK: {{ $s->identity_number ?? '-' }}) - {{ $s->job->name ?? $s->job_name ?? 'Swasta' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- III. HASIL PEMERIKSAAN SEMENTARA & KESIMPULAN --}}
                <hr>
                <h5 class="fw-bold text-blue-dark">Pemeriksaan Sementara & Kesimpulan</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="keterangan_saksi">Keterangan Singkat Saksi</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <textarea id="keterangan_saksi" name="keterangan_saksi" class="form-control" rows="2">{{ $valKetSaksi }}</textarea>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="keterangan_tersangka">Keterangan Tersangka</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <textarea id="keterangan_tersangka" name="keterangan_tersangka" class="form-control" rows="2">{{ $valKetTsk }}</textarea>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="barang_bukti">Barang Bukti yang Disita</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <textarea id="barang_bukti" name="barang_bukti" class="form-control" rows="2">{{ $valBarangBukti }}</textarea>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="kesimpulan_1">Kesimpulan Poin 1</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="kesimpulan_1" type="text" class="form-control" name="kesimpulan_1"
                            value="{{ $valKesimpulan1 }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="kesimpulan_2">Kesimpulan Poin 2</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="kesimpulan_2" type="text" class="form-control" name="kesimpulan_2"
                            value="{{ $valKesimpulan2 }}">
                    </div>
                </div>

                {{-- IV. RENCANA PENYIDIKAN & KEGIATAN --}}
                <hr>
                <h5 class="fw-bold text-blue-dark">Rencana Penyidikan & Kegiatan</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="rencana_a">Rencana Penyidikan (Poin a)</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="rencana_a" type="text" class="form-control" name="rencana_a"
                            value="{{ $valRencanaA }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="rencana_b">Rencana Penyidikan (Poin b)</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="rencana_b" type="text" class="form-control" name="rencana_b"
                            value="{{ $valRencanaB }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="saran_a">Saran (Poin a)</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="saran_a" type="text" class="form-control" name="saran_a"
                            value="{{ $valSaranA }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="saran_b">Saran (Poin b)</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="saran_b" type="text" class="form-control" name="saran_b"
                            value="{{ $valSaranB }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label">Kegiatan Sidik (Tabel)</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th>Waktu/Tempat</th>
                                        <th>Giat Sidik Yang Akan Dilakukan</th>
                                        <th style="width: 130px;">Personil</th>
                                        <th>Alat/Sarana</th>
                                        <th style="width: 120px;">Dana</th>
                                        <th style="width: 80px;">Ket</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="text" name="t_waktu_tempat" class="form-control form-control-sm" value="{{ old('t_waktu_tempat', $savedData['t_waktu_tempat'] ?? 'Kantor Satlantas / TKP') }}"></td>
                                        <td><input type="text" name="t_giat" class="form-control form-control-sm" value="{{ old('t_giat', $savedData['t_giat'] ?? 'Pemeriksaan saksi, pemeriksaan tersangka dan olah TKP lanjutan') }}"></td>
                                        <td><input type="text" name="t_personil" class="form-control form-control-sm text-center" value="{{ old('t_personil', $savedData['t_personil'] ?? '2 Personil') }}"></td>
                                        <td><input type="text" name="t_alat" class="form-control form-control-sm" value="{{ old('t_alat', $savedData['t_alat'] ?? 'Kendaraan Dinas & Blanko') }}"></td>
                                        <td><input type="text" name="t_dana" class="form-control form-control-sm" value="{{ old('t_dana', $savedData['t_dana'] ?? 'Duk Ops') }}"></td>
                                        <td><input type="text" name="t_ket" class="form-control form-control-sm" value="{{ old('t_ket', $savedData['t_ket'] ?? '-') }}"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- V. PEJABAT PENANDATANGAN --}}
                <hr>
                <h5 class="fw-bold text-blue-dark">Pejabat Penandatangan</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="penerima_id">Yang Menerima Perintah<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="penerima_id" id="penerima_id" required>
                            <option value="">-- Pilih Penyidik / Penerima Perintah --</option>
                            @foreach ($availableOfficers as $officer)
                                <option value="{{ $officer->id }}" {{ $valPenerimaId == $officer->id ? 'selected' : '' }}>
                                    {{ $officer->rank->name ?? '' }} {{ $officer->full_name }} (NRP: {{ $officer->register_number }}) - {{ $officer->position->name ?? '-' }}
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
                    <a href="{{ route('doc.rencana-penyidikan.download-word', ['accident_id' => $accidentId]) }}" class="btn btn-outline-success px-3 fw-bold" target="_blank">
                        <i class="bi bi-file-earmark-word me-1"></i> Unduh Word (.docx)
                    </a>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan Dokumen
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
                if (laws && $('#pasal_dilanggar').length) $('#pasal_dilanggar').val(laws);
            });
        });
    </script>
@endpush
