@php
    $_title = 'Ubah Surat Panggilan Tersangka';
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
            <h5 class="fw-bold text-blue-dark">Ubah Surat Panggilan Tersangka Ke-1 (S-5)</h5>

            <div class="alert alert-info">
                <div class="text-center">
                    <b>Dokumen ini telah tersimpan sebelumnya. Anda dapat memperbarui data atau mengunduh kembali template Word.</b>
                </div>
            </div>
        </div>

        <div class="box-body">
            <form action="{{ route('doc.surat-panggilan-tersangka-document.update') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="accident_id" value="{{ $accidentId }}">

                @php
                    $valNomorSurat = old('nomor_surat', $savedData['nomor_surat'] ?? ('S.Pgl/Tsk.1/001/' . date('m') . '/RES.0.0./' . date('Y') . '/Lantas'));
                    $valTanggalSurat = old('tanggal_surat', $savedData['tanggal_surat'] ?? date('d-m-Y'));
                    $valSpId = old('surat_perintah_penyidikan_id', $savedData['surat_perintah_penyidikan_id'] ?? '');
                    $valSketId = old('surat_ketetapan_tersangka_id', $savedData['surat_ketetapan_tersangka_id'] ?? '');
                    $valSuspectId = old('suspect_id', $savedData['suspect_id'] ?? ($suspect->id ?? ''));
                    $valPekerjaanTsk = old('pekerjaan_tersangka', $savedData['pekerjaan_tersangka'] ?? $pekerjaanTersangka);
                    $valAlamatTsk = old('alamat_tersangka', $savedData['alamat_tersangka'] ?? $alamatTersangka);
                    $valInvestigatorId = old('investigator_id', $savedData['investigator_id'] ?? '');
                    $valTempatPemeriksaan = old('tempat_pemeriksaan', $savedData['tempat_pemeriksaan'] ?? 'Ruang Unit Penegakan Hukum (Gakkum) Satlantas');
                    $valHariPemeriksaan = old('hari_pemeriksaan', $savedData['hari_pemeriksaan'] ?? 'Senin');
                    $valTanggalPemeriksaan = old('tanggal_pemeriksaan', $savedData['tanggal_pemeriksaan'] ?? \Carbon\Carbon::now()->addDays(3)->format('d-m-Y'));
                    $valJamPemeriksaan = old('jam_pemeriksaan', $savedData['jam_pemeriksaan'] ?? '09.00 WIB');
                    $valJenisTindakPidana = old('jenis_tindak_pidana', $savedData['jenis_tindak_pidana'] ?? 'Dugaan terjadinya tindak pidana kecelakaan lalu lintas');
                    $valPasal = old('pasal_pelanggaran', $savedData['pasal_pelanggaran'] ?? $pasalText);
                    $valDokumenBawaan = old('dokumen_bawaan', $savedData['dokumen_bawaan'] ?? 'Identitas Diri (KTP/SIM asli), STNK kendaraan, dan dokumen pendukung lainnya');
                    $valLiaisonId = old('liaison_officer_id', $savedData['liaison_officer_id'] ?? '');
                    $valHp = old('kontak_penyidik_hp', $savedData['kontak_penyidik_hp'] ?? '');
                    $valSignatoryId = old('signatory_id', $savedData['signatory_id'] ?? '');
                @endphp

                {{-- I. INFORMASI SURAT & RUJUKAN --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="no_lp_display">Nomor LP</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="no_lp_display" type="text" class="form-control font-weight-bold" value="{{ $accident->no_lp ?? '-' }}" readonly>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nomor_surat">Nomor Surat Panggilan Tersangka<span class="text-danger fs-5">*</span></label>
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

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="surat_ketetapan_tersangka_id">Nomor Ketetapan Tersangka (Sket)</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="surat_ketetapan_tersangka_id" id="surat_ketetapan_tersangka_id">
                            <option value="">-- Pilih No Ketetapan Tersangka --</option>
                            @foreach ($suratKetetapanTentangPenetapanTersangkaDocuments as $sk)
                                <option value="{{ $sk->id }}" {{ $valSketId == $sk->id ? 'selected' : '' }}>
                                    {{ $sk->document_number }} ({{ $sk->document_date ? \Carbon\Carbon::parse($sk->document_date)->format('d-m-Y') : '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- II. IDENTITAS TERSANGKA --}}
                <hr>
                <h5 class="fw-bold text-blue-dark">Identitas Tersangka yang Dipanggil</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="suspect_id">Nama Lengkap Tersangka<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="suspect_id" id="suspect_id" required>
                            <option value="">-- Pilih Tersangka --</option>
                            @foreach ($suspects as $s)
                                <option value="{{ $s->id }}" 
                                    data-name="{{ $s->name }}"
                                    data-job="{{ $s->job->name ?? $s->occupation ?? 'Swasta' }}"
                                    data-address="{{ $s->address ?? '' }}"
                                    {{ $valSuspectId == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }} (NIK: {{ $s->identity_number ?? '-' }}) - {{ $s->job->name ?? $s->occupation ?? 'Swasta' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="pekerjaan_tersangka">Pekerjaan Tersangka</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="pekerjaan_tersangka" type="text" class="form-control" name="pekerjaan_tersangka"
                            value="{{ $valPekerjaanTsk }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="alamat_tersangka">Alamat Tinggal Tersangka</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <textarea id="alamat_tersangka" name="alamat_tersangka" class="form-control" rows="2">{{ $valAlamatTsk }}</textarea>
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
                                <option value="{{ $officer->id }}" {{ $valInvestigatorId == $officer->id ? 'selected' : '' }}>
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
                            value="{{ $valTempatPemeriksaan }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label">Waktu Pemeriksaan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="small text-muted">Hari</label>
                                <input type="text" name="hari_pemeriksaan" class="form-control" value="{{ $valHariPemeriksaan }}">
                            </div>
                            <div class="col-md-5">
                                <label class="small text-muted">Tanggal</label>
                                <input type="text" name="tanggal_pemeriksaan" id="tanggal_pemeriksaan" class="form-control datepicker" value="{{ $valTanggalPemeriksaan }}" autocomplete="off">
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted">Pukul / Jam</label>
                                <input type="text" name="jam_pemeriksaan" class="form-control" value="{{ $valJamPemeriksaan }}">
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
                            value="{{ $valJenisTindakPidana }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="pasal_pelanggaran">Pasal Pelanggaran</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="pasal_pelanggaran" type="text" class="form-control" name="pasal_pelanggaran"
                            value="{{ $valPasal }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="dokumen_bawaan">Dokumen yang Wajib Dibawa</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="dokumen_bawaan" type="text" class="form-control" name="dokumen_bawaan"
                            value="{{ $valDokumenBawaan }}">
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
                                <option value="{{ $officer->id }}" data-phone="{{ $officer->phone_number ?? '' }}" {{ $valLiaisonId == $officer->id ? 'selected' : '' }}>
                                    {{ $officer->rank->name ?? '' }} {{ $officer->full_name }} (NRP: {{ $officer->register_number }})
                                </option>
                            @endforeach
                        </select>
                        <div class="mt-2">
                            <input type="text" name="kontak_penyidik_hp" id="kontak_penyidik_hp" class="form-control"
                                value="{{ $valHp }}" placeholder="No. HP / WhatsApp (opsional, jika kosong menggunakan data petugas)">
                        </div>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="signatory_id">Pejabat Penandatangan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="signatory_id" id="signatory_id" required>
                            <option value="">-- Pilih Pejabat Penandatangan --</option>
                            @foreach ($authorizedSignatories as $signatory)
                                <option value="{{ $signatory->id }}" {{ $valSignatoryId == $signatory->id ? 'selected' : '' }}>
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
                    <a href="{{ route('doc.surat-panggilan-tersangka.download-word', ['accident_id' => $accidentId]) }}" class="btn btn-outline-success px-3 fw-bold" target="_blank">
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
                if (laws && $('#pasal_pelanggaran').length) $('#pasal_pelanggaran').val(laws);
            });

            $('#suspect_id').on('change', function() {
                var opt = $(this).find(':selected');
                var job = opt.data('job');
                var addr = opt.data('address');
                if (job) $('#pekerjaan_tersangka').val(job);
                if (addr) $('#alamat_tersangka').val(addr);
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
