@php
    $_title = 'Surat Permintaan Perpanjangan Penahanan';
    $p = $defaults ?? [];
    $m = $p['meta'] ?? [];
    $lpP = $p['lp'] ?? [];
    $spdpP = $p['spdp'] ?? [];
    $kejaksaanP = $p['kejaksaan_extension'] ?? [];
    $perpanjanganP = $p['perpanjangan_order'] ?? [];
    $crimeP = $p['crime'] ?? [];
    $narrP = $p['narrative'] ?? [];
    $extP = $p['extension'] ?? [];
    $contactP = $p['contact'] ?? [];
    $sigP = $p['signature'] ?? [];
    $ref = $p['references'] ?? [];
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}">
        <i class="bi bi-arrow-left"></i> Kembali ke Progres Perkara
    </a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Edit {{ $_title }}</h5>

            @if ($errors->any())
                <div class="card-body">
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="card-body">
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @if (session('info'))
                <div class="card-body">
                    <div class="alert alert-info">
                        {{ session('info') }}
                    </div>
                </div>
            @endif
        </div>

        <div class="box-body">
            <form action="{{ route('doc.permintaan-perpanjangan-penahanan-document.update', ['id' => $doc->id, 'accident_id' => $accidentId]) }}"
                method="POST" id="pppForm">
                @csrf
                <input type="hidden" name="accident_id" value="{{ $accidentId }}">

                <h6 class="text-blue-dark fw-bold mb-3">Data surat</h6>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="accidentNumber">Nomor LP<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="accidentNumber" type="text" class="form-control font-weight-bold" name="accidentNumber" readonly
                            value="{{ $accident->no_lp ?? '-' }}">
                    </div>
                </div>

                <input type="hidden" name="classificationPreserve" value="{{ old('classificationPreserve', $m['classification'] ?? 'Biasa') }}">

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="documentNumber">Nomor Dokumen<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="documentNumber" type="text"
                            class="form-control @error('documentNumber') is-invalid @enderror font-weight-bold"
                            name="documentNumber" value="{{ old('documentNumber', $doc->document_number) }}" required
                            placeholder="Masukkan Nomor Dokumen">
                        @error('documentNumber')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Tanggal Dokumen<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input class="form-control s21-date @error('documentDate') is-invalid @enderror" id="documentDate"
                            name="documentDate" placeholder="YYYY-MM-DD" autocomplete="off" readonly
                            value="{{ old('documentDate', $doc->document_date) }}" data-provide="datepicker" required>
                        @error('documentDate')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                @php
                    $docClassSel = old('documentClassification', $m['document_classification_id'] ?? '');
                @endphp
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="documentClassification">Klasifikasi<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2 @error('documentClassification') is-invalid @enderror" name="documentClassification" id="documentClassification">
                            <option value="">--Pilih Klasifikasi--</option>
                            @foreach (($documentClassifications ?? collect()) as $documentClassification)
                                <option value="{{ $documentClassification->id }}" {{ (string) $docClassSel === (string) $documentClassification->id ? 'selected' : '' }}>
                                    {{ $documentClassification->name }}</option>
                            @endforeach
                        </select>
                        @error('documentClassification')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <hr>
                <h6 class="text-blue-dark fw-bold mb-3">Kepada — Pengadilan Negeri</h6>

                @php
                    $sketSelected = old('suratKetetapanTentangPenetapanTersangkaDocument', $ref['sket_document_id'] ?? null);
                    $courtSelected = old('court', $doc->court_id ?? ($p['court_id'] ?? null));
                @endphp

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Pengadilan Negeri<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2 @error('court') is-invalid @enderror" name="court" id="court" required>
                            <option value="">--Pilih Pengadilan Negeri--</option>
                            @foreach($courts as $court)
                                <option value="{{ $court->id }}" {{ $courtSelected == $court->id ? 'selected' : '' }}>
                                    {{ $court->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('court')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <hr>
                <h6 class="text-blue-dark fw-bold mb-3">Rujukan dokumen</h6>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="kejaksaanProsecutorId">Nama Kejaksaan Penerima<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        @php
                            $kejMeta = $p['kejaksaan'] ?? [];
                            $kejSelected = old('kejaksaanProsecutorId', $kejMeta['prosecutor_id'] ?? null);
                            $kejaksaanName = old('namaKejaksaan', ($kejMeta['name'] ?? ''));
                        @endphp
                        <select class="form-control select2" name="kejaksaanProsecutorId" id="kejaksaanProsecutorId">
                            <option value="">--Pilih Kejaksaan--</option>
                            @foreach (($prosecutors ?? collect()) as $prosecutor)
                                <option value="{{ $prosecutor->id }}" {{ $kejSelected == $prosecutor->id ? 'selected' : '' }}>
                                    {{ $prosecutor->name }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="namaKejaksaan" value="{{ $kejaksaanName }}">
                    </div>
                </div>

                @php
                    $spdpNumberDb = $spdpP['number'] ?? null;
                    $spdpDateDb = $spdpP['date'] ?? null;
                    $spdpNumberOld = old('spdpNumber', $spdpNumberDb ? $spdpNumberDb : '');
                    $spdpDateOld = old('spdpDate', $spdpDateDb ? (is_string($spdpDateDb) ? $spdpDateDb : '') : '');
                    $spdpSel = old('suratPemberitahuanDimulainyaPenyidikanDocument', $ref['spdp_document_id'] ?? null);
                @endphp
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="suratPemberitahuanDimulainyaPenyidikanDocument">Surat Pemberitahuan Dimulainya Penyidikan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2 @error('suratPemberitahuanDimulainyaPenyidikanDocument') is-invalid @enderror"
                            name="suratPemberitahuanDimulainyaPenyidikanDocument" id="suratPemberitahuanDimulainyaPenyidikanDocument"
                            {{ (($suratPemberitahuanDimulainyaPenyidikanDocuments ?? collect())->count() > 0) ? 'required' : '' }}>
                            <option value="">--Pilih No Surat Pemberitahuan Dimulainya Penyidikan--</option>
                            @foreach ($suratPemberitahuanDimulainyaPenyidikanDocuments as $d)
                                <option value="{{ $d->id }}"
                                    data-document-number="{{ $d->document_number }}"
                                    data-document-date="{{ $d->document_date ? date('Y-m-d', strtotime($d->document_date)) : '' }}"
                                    {{ $spdpSel == $d->id ? 'selected' : '' }}>
                                    {{ $d->document_number }}
                                </option>
                            @endforeach
                        </select>
                        @error('suratPemberitahuanDimulainyaPenyidikanDocument')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror

                        @if (($suratPemberitahuanDimulainyaPenyidikanDocuments ?? collect())->count() === 0)
                            <div class="row g-2 mt-2">
                                <div class="col-md-6">
                                    <input type="text" name="spdpNumber" class="form-control @error('spdpNumber') is-invalid @enderror"
                                        value="{{ $spdpNumberOld }}"
                                        placeholder="Nomor" required>
                                    @error('spdpNumber')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="spdpDate" class="form-control s21-date @error('spdpDate') is-invalid @enderror"
                                        value="{{ $spdpDateOld }}"
                                        placeholder="Tanggal (YYYY-MM-DD)" autocomplete="off" readonly required>
                                    @error('spdpDate')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                        @else
                            <input type="hidden" name="spdpNumber" value="{{ $spdpNumberOld }}">
                            <input type="hidden" name="spdpDate" value="{{ $spdpDateOld }}">
                        @endif
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="suratKetetapanTentangPenetapanTersangkaDocument">Surat Ketetapan tentang Penetapan Tersangka<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2" name="suratKetetapanTentangPenetapanTersangkaDocument" id="suratKetetapanTentangPenetapanTersangkaDocument" required>
                            <option value="">--Pilih--</option>
                            @foreach ($suratKetetapanTentangPenetapanTersangkaDocuments as $d)
                                <option value="{{ $d->id }}" {{ $sketSelected == $d->id ? 'selected' : '' }}>
                                    {{ $d->document_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Surat Perintah Penahanan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        @php
                            $sphSelected = old(
                                'suratPerintahPenahanan',
                                $doc->surat_perintah_penahanan_document_id ?? ($ref['sph_id'] ?? null)
                            );
                        @endphp
                        <select class="form-control select2 @error('suratPerintahPenahanan') is-invalid @enderror"
                            name="suratPerintahPenahanan" id="suratPerintahPenahanan" required>
                            <option value="">--Pilih Surat Perintah Penahanan--</option>
                            @foreach (($suratPerintahPenahananDocuments ?? collect()) as $d)
                                <option value="{{ $d->id }}" {{ (string) $sphSelected === (string) $d->id ? 'selected' : '' }}>
                                    {{ $d->document_number }}
                                </option>
                            @endforeach
                        </select>
                        @error('suratPerintahPenahanan')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Surat Perpanjangan Penahanan dari Kejaksaan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="kejaksaanExtensionNumber" value="{{ old('kejaksaanExtensionNumber', $kejaksaanP['number'] ?? '') }}" placeholder="Nomor">
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control s21-date" name="kejaksaanExtensionDate" value="{{ old('kejaksaanExtensionDate', $kejaksaanP['date'] ?? '') }}"
                                    placeholder="Tanggal (YYYY-MM-DD)" autocomplete="off" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Surat Perintah Perpanjangan Penahanan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="perpanjanganOrderNumber" value="{{ old('perpanjanganOrderNumber', $perpanjanganP['number'] ?? '') }}" placeholder="Nomor">
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control s21-date" name="perpanjanganOrderDate" value="{{ old('perpanjanganOrderDate', $perpanjanganP['date'] ?? '') }}"
                                    placeholder="Tanggal (YYYY-MM-DD)" autocomplete="off" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <h6 class="text-blue-dark fw-bold mb-3">Identitas tersangka</h6>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Tersangka<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex-align-self-center">
                        <select class="form-control select2 @error('suspect') is-invalid @enderror" name="suspect" id="suspect" required>
                            <option value="">--Pilih Tersangka--</option>
                            @foreach ($suspects as $s)
                                <option value="{{ $s->id }}"
                                    {{ old('suspect', $doc->suspect_id) == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }}{{ !empty($s->identity_number) ? ' — ' . $s->identity_number : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('suspect')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                        <small id="suspectHelpText" class="text-muted" style="display:none;">
                            Pilih Surat Perintah Penyidikan dan Surat Ketetapan Tersangka terlebih dahulu.
                        </small>
                    </div>
                </div>

                <hr>
                <h6 class="text-blue-dark fw-bold mb-3">Permohonan perpanjangan penahanan</h6>

                @php
                    $detentionOld = old('detentionEndDate');
                    if ($detentionOld === null) {
                        $detentionOld = $doc->detention_end_date
                            ? $doc->detention_end_date->format('Y-m-d')
                            : ($p['detention_end_date'] ?? '');
                    }
                @endphp

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Akhir masa penahanan berjalan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input type="text" name="detentionEndDate" id="detentionEndDate"
                            class="form-control s21-date @error('detentionEndDate') is-invalid @enderror"
                            value="{{ $detentionOld }}" placeholder="YYYY-MM-DD" autocomplete="off" readonly required>
                        @error('detentionEndDate')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Rutan / tempat penahanan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2" name="extension_prison_id" id="extension_prison_id">
                            <option value="">--Pilih Rutan/Lapas--</option>
                            @foreach (($prisons ?? collect()) as $pr)
                                <option value="{{ $pr->id }}" {{ old('extension_prison_id', $extP['prison_id'] ?? '') == $pr->id ? 'selected' : '' }}>
                                    {{ $pr->province }} — {{ $pr->name }}{{ $pr->branch ? ' ('.$pr->branch.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr>
                <h6 class="text-blue-dark fw-bold mb-3">Kontak &amp; penandatangan</h6>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Penyidik / penyidik pembantu<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <div class="row g-2">
                            @php
                                $contactOfficerIdOld = old('contactOfficerId', $contactP['officer_id'] ?? '');
                            @endphp
                            <div class="col-md-6">
                                <select class="form-control select2" name="contactOfficerId" id="contactOfficerId">
                                    <option value="">--Pilih Penyidik--</option>
                                    @foreach (($contactOfficers ?? collect()) as $o)
                                        <option value="{{ $o->id }}"
                                            data-phone="{{ $o->phone_number ?? $o->phone ?? '' }}"
                                            data-name="{{ $o->full_name ?? '' }}"
                                            {{ (string) $contactOfficerIdOld === (string) $o->id ? 'selected' : '' }}>
                                            @php
                                                $positionName = $o->position->name ?? '-';
                                            @endphp
                                            {{ !empty($o->register_number) ? $o->register_number : '' }} - {{ $o->full_name ?? $o->name ?? '' }} | {{ $positionName }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="contactOfficerName" id="contactOfficerName"
                                    value="{{ old('contactOfficerName', $contactP['officer_name'] ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="contactOfficerPhone" id="contactOfficerPhone"
                                    value="{{ old('contactOfficerPhone', $contactP['officer_phone'] ?? '') }}" placeholder="Telepon">
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $signatorySelectedId = old('signatory', $sigP['officer_id'] ?? '');
                @endphp
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="signatory">Penandatangan Surat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2" name="signatory" id="signatory">
                            <option value="">--Pilih Yang Menandatangani--</option>
                            @foreach (($authorizedSignatories ?? collect()) as $data)
                                @php
                                    $positionName = ($data->position) ? $data->position->name : '-';
                                @endphp
                                <option value="{{ $data->id }}" {{ (string) $signatorySelectedId === (string) $data->id ? 'selected' : '' }}
                                    data-register-number="{{ $data->register_number }}">
                                    {{ $data->register_number . ' - ' . $data->full_name . ' | ' . $positionName }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">(*Apabila daftar yang menandatangani kosong silahkan hubungi Helpdesk untuk
                            mendapat bantuan)</small>
                    </div>
                </div>

                <hr>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="carbonCopies">Tembusan Lainnya<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-12 d-flex align-self-center">
                        <div id="carbonCopiesContainer"></div>
                        <button class="btn btn-primary mb-2 addCarbonCopiesButton" type="button">Tambah</button>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-dark-blue">
                        <i class="bi bi-save"></i> {{ __('Simpan') }}
                    </button>
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> {{ __('Batal') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    @php
        $initialCarbonCopies = old('carbonCopies') ?? ($p['carbon_copies'] ?? []);
    @endphp
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            $('.onlyIntegerInput').on('keypress', function(event) {
                var charCode = (event.which) ? event.which : event.keyCode;
                if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                    event.preventDefault();
                }
            });

            $('#kejaksaanProsecutorId').on('change', function() {
                var opt = $(this).find(':selected');
                if (!opt.val()) {
                    $('input[name="namaKejaksaan"]').val('');
                    return;
                }
                $('input[name="namaKejaksaan"]').val($.trim(opt.text()));
            });
            $('#kejaksaanProsecutorId').trigger('change');

            // SPDP dipilih via dropdown (nomor/tanggal diambil dari database untuk Word)

            function syncContactOfficerFromDropdown() {
                var $opt = $('#contactOfficerId').find(':selected');
                var name = String($opt.data('name') || '').trim();
                var phone = String($opt.data('phone') || '').trim();
                if (name) {
                    $('#contactOfficerName').val(name);
                }
                $('#contactOfficerPhone').val(phone);
            }

            $('#contactOfficerId').on('change', syncContactOfficerFromDropdown);
            syncContactOfficerFromDropdown();

            function s21NormalizeSpaces(str) {
                return String(str || '')
                    .replace(/\u00a0/g, ' ')
                    .replace(/\s+/g, ' ')
                    .trim();
            }

            function s21TitleCaseWords(str) {
                str = s21NormalizeSpaces(str);
                if (!str) return '';
                return str.toLowerCase().replace(/\b\w/g, function(match) {
                    return match.toUpperCase();
                });
            }

            function s21KetuaPengadilanLineFromCourtName(courtName) {
                var name = s21TitleCaseWords(courtName);
                if (!name) return '';
                return s21NormalizeSpaces('Ketua ' + name);
            }

            function s21SyncCarbonCopyCourtRow() {
                var courtId = String($('#court').val() || '').trim();
                var courtName = s21NormalizeSpaces($('#court').find(':selected').text());
                var ketuaLine = s21KetuaPengadilanLineFromCourtName(courtName);

                if (!courtId) {
                    $('#carbonCopyCourt').closest('.input-group').remove();
                    return;
                }

                if ($('#carbonCopyCourt').length) {
                    $('#carbonCopyCourt').val(ketuaLine);
                    return;
                }

                var $wrap = $('<div class="input-group mb-2">' +
                    '<input type="text" class="form-control" id="carbonCopyCourt" name="carbonCopies[]" readonly>' +
                    '<div class="input-group-append"></div>' +
                    '</div>');
                $wrap.find('input').val(ketuaLine);
                $('#carbonCopiesContainer').prepend($wrap);
            }

            function s21HydrateCarbonCopyCourtRowFromInitial() {
                var courtId = String($('#court').val() || '').trim();
                if (!courtId) {
                    return;
                }

                if ($('#carbonCopyCourt').length) {
                    return;
                }

                var $firstKetua = $('#carbonCopiesContainer input[name="carbonCopies[]"]').filter(function() {
                    return s21NormalizeSpaces($(this).val()).toLowerCase().startsWith('ketua');
                }).first();

                if (!$firstKetua.length) {
                    return;
                }

                var $ig = $firstKetua.closest('.input-group');
                $firstKetua.attr('id', 'carbonCopyCourt').prop('readonly', true);
                $ig.find('.removeCarbonCopiesButton').closest('.input-group-append').remove();
            }

            $('#court').on('change', s21SyncCarbonCopyCourtRow);

            function s21ToggleSuspectEnabled() {
                var ok = !!$('#suratPemberitahuanDimulainyaPenyidikanDocument').val() &&
                    !!$('#suratKetetapanTentangPenetapanTersangkaDocument').val();
                $('#suspect').prop('disabled', !ok).trigger('change.select2');
                $('#suspectHelpText').toggle(!ok);
            }

            $('#suratPemberitahuanDimulainyaPenyidikanDocument, #suratKetetapanTentangPenetapanTersangkaDocument')
                .on('change', s21ToggleSuspectEnabled);
            s21ToggleSuspectEnabled();

            var s21DpOpts = { format: 'yyyy-mm-dd', autoclose: true };
            $('.s21-date').each(function() {
                if (this.id === 'documentDate') {
                    $(this).datepicker($.extend({}, s21DpOpts, { endDate: new Date() }));
                } else {
                    $(this).datepicker(s21DpOpts);
                }
            });
            $('.s21-date').on('keydown', function(e) {
                e.preventDefault();
                return false;
            });

            function parseYmdLocal(str) {
                var m = String(str || '').trim().match(/^(\d{4})-(\d{2})-(\d{2})$/);
                if (!m) return null;
                var y = parseInt(m[1], 10), mo = parseInt(m[2], 10) - 1, d = parseInt(m[3], 10);
                var dt = new Date(y, mo, d);
                return isNaN(dt.getTime()) ? null : dt;
            }

            function getDetentionEnd() {
                var $d = $('#detentionEndDate');
                var gd = $d.datepicker('getDate');
                if (gd) return gd;
                return parseYmdLocal($d.val());
            }

            // Masa perpanjangan dihitung di backend dari Akhir masa penahanan berjalan.

            function appendCarbonCopyInput(value = '') {
                value = s21NormalizeSpaces(value);
                var $ig = $('<div class="input-group mb-2">' +
                    '<input type="text" class="form-control" name="carbonCopies[]">' +
                    '<div class="input-group-append">' +
                    '<button class="btn btn-outline-danger removeCarbonCopiesButton" type="button">Hapus</button>' +
                    '</div>' +
                    '</div>');
                $ig.find('input').val(value);
                $('#carbonCopiesContainer').append($ig);
            }

            var initial = @json($initialCarbonCopies);
            if (initial && initial.length) {
                initial.forEach(function(v) {
                    appendCarbonCopyInput(v);
                });
            }

            s21HydrateCarbonCopyCourtRowFromInitial();
            s21SyncCarbonCopyCourtRow();

            $(".addCarbonCopiesButton").click(function() {
                appendCarbonCopyInput('');
            });

            $(document).on("click", ".removeCarbonCopiesButton", function() {
                $(this).closest(".input-group").remove();
            });

            $('#pppForm').on('submit', function(e) {
                if ($('#suspect').prop('disabled')) {
                    e.preventDefault();
                    alert('Isi dulu Sprindik dan SKET sebelum memilih tersangka.');
                    return false;
                }
            });
        });
    </script>
@endpush
