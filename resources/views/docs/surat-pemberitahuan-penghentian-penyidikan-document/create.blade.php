@php
    $_title = 'Surat Pemberitahuan Penghentian Penyidikan';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
    <style>
        .select2-container .select2-selection.border-danger,
        .select2-container .select2-selection.is-invalid {
            border-color: #dc3545 !important;
        }
        .invalid-feedback.frontend-error {
            display: block;
            font-size: 80%;
            color: #dc3545;
            margin-top: 0.25rem;
        }
    </style>
@endpush

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}">
        <i class="bi bi-arrow-left"></i> Kembali ke Progress Perkara
    </a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Tambah Surat Pemberitahuan Penghentian Penyidikan</h5>

            <div class="alert alert-danger" id="attentionBox">
                <div class="text-center">
                    <b>
                        PERHATIAN !<br /><br />
                        DATA INI WAJIB DIISI DENGAN DETAIL DAN LENGKAP KARENA AKAN DIGUNAKAN SEBAGAI KELENGKAPAN BERKAS
                        PERKARA PENYIDIKAN KECELAKAAN LALU LINTAS.
                    </b>
                </div>
            </div>

            <!-- error alert -->
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
        </div>

        <div class="box-body">
            <form action="{{ route('doc.surat-pemberitahuan-penghentian-penyidikan-document.store', ['accident_id' => $accidentId]) }}"
                  method="POST" enctype="multipart/form-data" id="sp3PusiknasForm" novalidate>
                @csrf
                <input type="hidden" name="accident_id" value="{{ $accidentId }}">
                <input type="hidden" name="noLp" value="{{ $accident->no_lp }}">

                {{-- Nomor LP --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="accidentNumber">Nomor LP</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="accidentNumber" type="text" class="form-control font-weight-bold" value="{{ $accident->no_lp }}" readonly>
                    </div>
                </div>

                {{-- Nomor Surat --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="noSp3">Nomor Surat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="noSp3" type="text" class="form-control @error('noSp3') is-invalid @enderror font-weight-bold"
                            name="noSp3" value="{{ old('noSp3') }}" placeholder="Contoh: B/0001/I/RES.0.0./2026/Satker" required>
                        @error('noSp3')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Tanggal Surat --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggalSp3">Tanggal Surat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tanggalSp3" type="text" class="form-control datepicker @error('tanggalSp3') is-invalid @enderror"
                            name="tanggalSp3" value="{{ old('tanggalSp3', date('Y-m-d')) }}" placeholder="YYYY-MM-DD" autocomplete="off" required>
                        @error('tanggalSp3')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Klasifikasi Surat --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="klasifikasi">Klasifikasi Surat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="klasifikasi" id="klasifikasi" required>
                            <option value="">-- Pilih Klasifikasi Dokumen --</option>
                            @foreach ($documentClassifications as $dc)
                                <option value="{{ $dc->id }}" {{ old('klasifikasi') == $dc->id ? 'selected' : '' }}>{{ $dc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Jumlah Lampiran --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="appendix">Jumlah Lampiran<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="appendix" type="number" class="form-control onlyIntegerInput" name="appendix" value="{{ old('appendix', 1) }}" min="1" max="999" required>
                    </div>
                </div>

                <h5 class="fw-bold text-blue-dark mt-4 mb-3">Dokumen Dasar Terkait</h5>

                {{-- Nomor SPDP Terkait --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="noSpdp">Nomor SPDP Terkait<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="noSpdp" id="noSpdp" required>
                            <option value="">-- Pilih Nomor SPDP --</option>
                            @foreach ($spdpDocuments as $spdp)
                                <option value="{{ $spdp->document_number }}"
                                    data-prosecutor-id="{{ $spdp->prosecutor_id }}"
                                    {{ old('noSpdp') == $spdp->document_number ? 'selected' : '' }}>
                                    {{ $spdp->document_number }} ({{ $spdp->document_date ? date('d/m/Y', strtotime($spdp->document_date)) : '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Nomor SK Penghentian Penyidikan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="noSkPenghentian">Nomor SK Penghentian Penyidikan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2-tags" name="noSkPenghentian" id="noSkPenghentian" required>
                            <option value="">-- Pilih Dokumen SK Penghentian atau Ketik Nomor Dokumen --</option>
                            @foreach ($sketHentiDocuments as $sk)
                                <option value="{{ $sk->document_number }}"
                                    data-document-date="{{ $sk->document_date ? date('Y-m-d', strtotime($sk->document_date)) : '' }}"
                                    data-prosecutor-id="{{ $sk->prosecutor_id }}"
                                    data-court-id="{{ $sk->court_id }}"
                                    {{ old('noSkPenghentian') == $sk->document_number ? 'selected' : '' }}>
                                    {{ $sk->document_number }} ({{ $sk->document_date ? date('d/m/Y', strtotime($sk->document_date)) : '-' }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih dari SK Penghentian yang terdaftar di perkara ini, atau ketik manual jika dokumen fisik dari luar.</small>
                    </div>
                </div>

                {{-- Tanggal SK Penghentian --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggalSkPenghentian">Tanggal SK Penghentian<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tanggalSkPenghentian" type="text" class="form-control datepicker" name="tanggalSkPenghentian" value="{{ old('tanggalSkPenghentian') }}" placeholder="YYYY-MM-DD" autocomplete="off" required>
                    </div>
                </div>

                {{-- Nomor SP Penghentian Penyidikan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="noSpPenghentian">Nomor SP Penghentian Penyidikan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2-tags" name="noSpPenghentian" id="noSpPenghentian" required>
                            <option value="">-- Pilih Dokumen Surat Perintah Penghentian atau Ketik Nomor Dokumen --</option>
                            @foreach ($sprintHentiDocuments as $sph)
                                <option value="{{ $sph->document_number }}"
                                    data-document-date="{{ $sph->document_date ? date('Y-m-d', strtotime($sph->document_date)) : '' }}"
                                    {{ old('noSpPenghentian') == $sph->document_number ? 'selected' : '' }}>
                                    {{ $sph->document_number }} ({{ $sph->document_date ? date('d/m/Y', strtotime($sph->document_date)) : '-' }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih dari Surat Perintah Penghentian yang terdaftar, atau ketik manual jika dokumen fisik dari luar.</small>
                    </div>
                </div>

                {{-- Tanggal SP Penghentian --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggalSpPenghentian">Tanggal SP Penghentian<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tanggalSpPenghentian" type="text" class="form-control datepicker" name="tanggalSpPenghentian" value="{{ old('tanggalSpPenghentian') }}" placeholder="YYYY-MM-DD" autocomplete="off" required>
                    </div>
                </div>

                <h5 class="fw-bold text-blue-dark mt-4 mb-3">Alasan Penghentian & Tersangka</h5>

                {{-- Alasan Penghentian --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="kode_alasan">Alasan Penghentian<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2-multiple" name="kode_alasan[]" id="kode_alasan" multiple="multiple" data-placeholder="Pilih satu atau lebih alasan penghentian penyidikan" required>
                            @foreach ($masterAlasan as $kode => $label)
                                <option value="{{ $kode }}" {{ in_array($kode, old('kode_alasan', [])) ? 'selected' : '' }}>
                                    {{ $kode }}. {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('kode_alasan')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Tersangka --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="suspects">Tersangka yang Dihentikan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2-multiple" name="suspects[]" id="suspects" multiple="multiple" data-placeholder="Pilih tersangka (opsional)">
                            @foreach ($suspects as $suspect)
                                <option value="{{ $suspect->id }}" {{ in_array($suspect->id, old('suspects', [])) ? 'selected' : '' }}>
                                    {{ $suspect->name }} (NIK: {{ $suspect->identity_number ?? ($suspect->nik ?? '-') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <h5 class="fw-bold text-blue-dark mt-4 mb-3">Instansi Terkait (Kejaksaan & Pengadilan)</h5>

                {{-- Kejaksaan Negeri Terkait --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="prosecutor_id">Kejaksaan Negeri Terkait<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="prosecutor_id" id="prosecutor_id" required>
                            <option value="">-- Pilih Kejaksaan Negeri --</option>
                            @foreach ($prosecutors as $pros)
                                <option value="{{ $pros->id }}" {{ old('prosecutor_id', $defaultProsecutorId ?? '') == $pros->id ? 'selected' : '' }}>{{ $pros->name }}</option>
                            @endforeach
                        </select>
                        @error('prosecutor_id')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Pengadilan Negeri Terkait --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="court_id">Pengadilan Negeri Terkait<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="court_id" id="court_id" required>
                            <option value="">-- Pilih Pengadilan Negeri --</option>
                            @foreach ($courts as $crt)
                                <option value="{{ $crt->id }}" {{ old('court_id', $defaultCourtId ?? '') == $crt->id ? 'selected' : '' }}>{{ $crt->name }}</option>
                            @endforeach
                        </select>
                        @error('court_id')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <h5 class="fw-bold text-blue-dark mt-4 mb-3">Tembusan & Pejabat Penandatangan</h5>

                {{-- Penandatangan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="signatory">Pejabat Penandatangan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="signatory" id="signatory" required>
                            <option value="">-- Pilih Pejabat Penandatangan --</option>
                            @foreach ($authorizedSignatories as $officer)
                                <option value="{{ $officer->id }}" {{ old('signatory') == $officer->id ? 'selected' : '' }}>
                                    {{ $officer->register_number }} - {{ $officer->full_name ?? ($officer->first_name . ' ' . $officer->last_name) }} ({{ $officer->position->name ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        @error('signatory')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Tembusan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label">Tembusan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <div id="carbonCopiesContainer">
                            @if(old('carbonCopies'))
                                @php
                                    $oldCopies = old('carbonCopies');
                                    if (count($oldCopies) < 2) {
                                        $oldCopies = array_pad($oldCopies, 2, '');
                                    }
                                @endphp
                                @foreach($oldCopies as $index => $copy)
                                    <div class="input-group mb-2 carbon-copy-row">
                                        <input type="text" name="carbonCopies[]" class="form-control carbon-copy-input" value="{{ $copy }}" {{ $index < 2 ? 'readonly placeholder=' . ($index == 0 ? 'Otomatis dari Kejaksaan...' : 'Otomatis dari Pengadilan...') : 'placeholder="Contoh: Kepala Kepolisian Daerah..."' }} required>
                                        @if($index >= 2)
                                            <button type="button" class="btn btn-danger removeCarbonCopy"><i class="bi bi-trash"></i></button>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="input-group mb-2 carbon-copy-row">
                                    <input type="text" name="carbonCopies[]" class="form-control carbon-copy-input" placeholder="Otomatis dari Kejaksaan..." readonly required>
                                </div>
                                <div class="input-group mb-2 carbon-copy-row">
                                    <input type="text" name="carbonCopies[]" class="form-control carbon-copy-input" placeholder="Otomatis dari Pengadilan..." readonly required>
                                </div>
                            @endif
                        </div>
                        <button class="btn btn-outline-primary btn-sm mt-1 addCarbonCopiesButton" type="button"><i class="bi bi-plus"></i> Tambah Tembusan Lainnya</button>
                        @error('carbonCopies')
                            <span class="text-danger small d-block mt-1"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                {{-- Tombol Aksi --}}
                <div class="text-center">
                    <button type="button" class="btn btn-dark-blue" id="sp3PusiknasFormSubmit">
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
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            // Attention blink
            setInterval(function () { $('#attentionBox').toggleClass('alert-danger alert-warning'); }, 1000);

            // Datepicker
            $('#tanggalSp3, #tanggalSkPenghentian, #tanggalSpPenghentian').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });

            // Auto-fill Tembusan & Dynamic Rows
            function addCarbonCopyRow(value) {
                var row = `
                    <div class="input-group mb-2 carbon-copy-row">
                        <input type="text" name="carbonCopies[]" class="form-control carbon-copy-input" value="${value}" placeholder="Contoh: Kepala Kepolisian Daerah..." required>
                        <button type="button" class="btn btn-danger removeCarbonCopy"><i class="bi bi-trash"></i></button>
                    </div>
                `;
                $('#carbonCopiesContainer').append(row);
            }

            $('.addCarbonCopiesButton').on('click', function () {
                addCarbonCopyRow('');
            });

            $(document).on('click', '.removeCarbonCopy', function () {
                $(this).closest('.carbon-copy-row').remove();
            });

            function toTitleCase(str) {
                return str.replace(/\w\S*/g, function(txt){
                    return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
                });
            }

            function updateTembusan() {
                var prosecutor = $('#prosecutor_id').find(':selected').text().trim();
                var court = $('#court_id').find(':selected').text().trim();
                
                var firstVal = '';
                if (prosecutor && prosecutor !== '-- Pilih Kejaksaan Negeri --' && prosecutor !== '-- Pilih Kejaksaan --') {
                    firstVal = 'Kepala ' + toTitleCase(prosecutor);
                }
                
                var secondVal = '';
                if (court && court !== '-- Pilih Pengadilan Negeri --' && court !== '-- Pilih Pengadilan --') {
                    secondVal = 'Ketua ' + toTitleCase(court);
                }

                var rows = $('#carbonCopiesContainer .carbon-copy-row');
                
                if (rows.length >= 1) {
                    $('#carbonCopiesContainer .carbon-copy-input').eq(0).val(firstVal).attr('readonly', true);
                }
                if (rows.length >= 2) {
                    $('#carbonCopiesContainer .carbon-copy-input').eq(1).val(secondVal).attr('readonly', true);
                }
            }
            $('#prosecutor_id, #court_id').on('change', updateTembusan);
            updateTembusan();

            // Select2
            $('.select2').select2({ theme: 'bootstrap4', width: '100%' });
            $('.select2-multiple').select2({ theme: 'bootstrap4', width: '100%' });
            $('.select2-tags').select2({
                theme: 'bootstrap4',
                width: '100%',
                tags: true
            });

            // Auto-fill from supporting documents
            $('#noSkPenghentian').on('change', function () {
                var selected = $(this).find(':selected');
                var docDate = selected.data('document-date');
                if (docDate) {
                    $('#tanggalSkPenghentian').val(docDate);
                }
                var prosecutorId = selected.data('prosecutor-id');
                if (prosecutorId) {
                    $('#prosecutor_id').val(prosecutorId).trigger('change');
                }
                var courtId = selected.data('court-id');
                if (courtId) {
                    $('#court_id').val(courtId).trigger('change');
                }
            });

            $('#noSpdp').on('change', function () {
                var selected = $(this).find(':selected');
                var prosecutorId = selected.data('prosecutor-id');
                if (prosecutorId && !$('#prosecutor_id').val()) {
                    $('#prosecutor_id').val(prosecutorId).trigger('change');
                }
            });

            $('#noSpPenghentian').on('change', function () {
                var selected = $(this).find(':selected');
                var docDate = selected.data('document-date');
                if (docDate) {
                    $('#tanggalSpPenghentian').val(docDate);
                }
            });

            // Helper check field has value
            function hasFieldValue($field) {
                var raw = $field.val();
                if (raw === null || raw === undefined) return false;
                if (Array.isArray(raw)) return raw.length > 0;
                var str = String(raw).trim();
                return str !== '' && str !== '0';
            }

            // Helper clear single field error
            function clearFieldError($field) {
                $field.removeClass('is-invalid border border-danger');
                if ($field.next('.select2-container').length) {
                    $field.next('.select2-container').find('.select2-selection').removeClass('border border-danger is-invalid');
                    $field.next('.select2-container').next('.frontend-error, .invalid-feedback').remove();
                }
                $field.next('.frontend-error, .invalid-feedback').remove();
                $field.siblings('.frontend-error, .invalid-feedback').remove();
                $field.closest('.input-group, .form-group, .mb-3, div').find('.frontend-error, .invalid-feedback').remove();
            }

            // Auto-clear realtime saat user mengetik atau mengubah nilai field
            $(document).on('input change changeDate dp.change keyup blur', 'input, textarea, select', function() {
                var $field = $(this);
                if (hasFieldValue($field)) {
                    clearFieldError($field);
                }
            });

            $(document).on('select2:select select2:unselect change', 'select', function() {
                var $field = $(this);
                if (hasFieldValue($field)) {
                    clearFieldError($field);
                }
            });

            // Continuous watcher
            setInterval(function() {
                $('input.is-invalid, textarea.is-invalid, select.is-invalid').each(function() {
                    var $field = $(this);
                    if (hasFieldValue($field)) {
                        clearFieldError($field);
                    }
                });
            }, 200);

            // Helper scrollToFirstError
            function scrollToFirstError() {
                var $firstError = $('.is-invalid:visible, .border-danger:visible, .frontend-error:visible').first();
                if (!$firstError.length) {
                    $firstError = $('.is-invalid, .border-danger').first();
                }
                if ($firstError && $firstError.length) {
                    var el = $firstError[0];
                    if (el && typeof el.scrollIntoView === 'function') {
                        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    var topPos = $firstError.offset() ? $firstError.offset().top : 0;
                    $('html, body, .content-wrapper, .wrapper, main').stop().animate({
                        scrollTop: Math.max(0, topPos - 140)
                    }, 400);
                } else {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }

            // AJAX Validate & Submit
            $('#sp3PusiknasFormSubmit').on('click', function (e) {
                e.preventDefault();

                // Bersihkan error sebelumnya
                $('.is-invalid').removeClass('is-invalid');
                $('.border.border-danger').removeClass('border border-danger');
                $('.select2-selection').removeClass('border border-danger is-invalid');
                $('.frontend-error').remove();
                $('.invalid-feedback').remove();

                let errors = [];

                function markError(fieldSelector, message) {
                    var $field = typeof fieldSelector === 'string' ? $(fieldSelector) : fieldSelector;
                    if (!$field || !$field.length) return;

                    $field.addClass('is-invalid');
                    if ($field.next('.select2-container').length) {
                        $field.next('.select2-container').find('.select2-selection').addClass('border border-danger is-invalid');
                    }
                    var $target = $field.next('.select2-container').length ? $field.next('.select2-container') : $field;
                    $target.siblings('.frontend-error, .invalid-feedback').remove();
                    $target.next('.frontend-error, .invalid-feedback').remove();
                    $target.after('<div class="invalid-feedback d-block frontend-error">' + message + '</div>');
                    errors.push(message);
                }

                function checkInput(fieldSelector, label) {
                    var $field = $(fieldSelector);
                    if ($field.is(':disabled') || !$field.is(':visible')) return;
                    var raw = $field.val();
                    var val = (raw !== null && raw !== undefined) ? String(raw).trim() : '';
                    if (!val || val === '') {
                        markError(fieldSelector, label + ' harus diisi');
                    }
                }

                function checkSelect(fieldSelector, label) {
                    var $field = $(fieldSelector);
                    if ($field.is(':disabled') || (!$field.is(':visible') && !$field.next('.select2-container:visible').length)) return;
                    var raw = $field.val();
                    var hasVal = Array.isArray(raw) ? raw.length > 0 : (raw && String(raw).trim() !== '' && String(raw).trim() !== '0');
                    if (!hasVal) {
                        markError(fieldSelector, label + ' harus dipilih');
                    }
                }

                // Validasi field wajib di sisi frontend
                checkInput('#noSp3', 'Nomor Surat');
                checkInput('#tanggalSp3', 'Tanggal Surat');
                checkSelect('#klasifikasi', 'Klasifikasi Surat');
                checkInput('#appendix', 'Jumlah Lampiran');
                checkSelect('#noSpdp', 'Nomor SPDP Terkait');
                checkSelect('#noSkPenghentian', 'Nomor SK Penghentian');
                checkInput('#tanggalSkPenghentian', 'Tanggal SK Penghentian');
                checkSelect('#noSpPenghentian', 'Nomor SP Penghentian');
                checkInput('#tanggalSpPenghentian', 'Tanggal SP Penghentian');
                checkSelect('#kode_alasan', 'Alasan Penghentian');
                checkSelect('#prosecutor_id', 'Kejaksaan Negeri Terkait');
                checkSelect('#court_id', 'Pengadilan Negeri Terkait');
                checkSelect('#signatory', 'Pejabat Penandatangan');

                // Tembusan
                $('#carbonCopiesContainer .carbon-copy-input').each(function(idx) {
                    var val = ($(this).val() || '').trim();
                    if (!val) {
                        markError($(this), 'Tembusan ke-' + (idx + 1) + ' harus diisi');
                    }
                });

                // Jika ada error di frontend, scroll ke field pertama
                if (errors.length > 0) {
                    scrollToFirstError();
                    return false;
                }

                $.ajax({
                    url: "{{ route('doc.surat-pemberitahuan-penghentian-penyidikan-document.api.validate-request-form', ['accident_id' => $accidentId]) }}",
                    type: 'POST',
                    dataType: 'json',
                    data: $('#sp3PusiknasForm').serialize(),
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: response.message || 'Silahkan menunggu proses simpan data',
                                icon: 'success',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                $('#sp3PusiknasForm')[0].submit();
                            });
                        }
                    },
                    error: function (xhr) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.code == '422' && response.errors) {
                                if (typeof response.errors === 'object' && !Array.isArray(response.errors)) {
                                    $.each(response.errors, function (key, messages) {
                                        var msg = Array.isArray(messages) ? messages[0] : messages;
                                        var $target = $('#' + key + ', [name="' + key + '"], [name="' + key + '[]"]');
                                        if ($target.length) {
                                            markError($target, msg);
                                        } else if (key === 'carbonCopies') {
                                            markError('#carbonCopiesContainer .carbon-copy-input:first', msg);
                                        } else if (key.indexOf('carbonCopies.') === 0) {
                                            var idx = parseInt(key.split('.')[1]);
                                            markError($('#carbonCopiesContainer .carbon-copy-input').eq(idx), msg);
                                        } else {
                                            markError('#' + key, msg);
                                        }
                                    });
                                    scrollToFirstError();
                                } else {
                                    var errorMessages = '';
                                    $.each(response.errors, function (key, value) { errorMessages += '- ' + value + '<br>'; });
                                    Swal.fire({ icon: 'error', title: 'Periksa Isian', html: errorMessages });
                                }
                            } else {
                                var message = response.message || response.errors || 'Terjadi kesalahan sistem';
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Perhatian',
                                    text: typeof message === 'string' ? message : JSON.stringify(message)
                                });
                            }
                        } catch (err) {
                            $('#sp3PusiknasForm')[0].submit();
                        }
                    }
                });
            });
        });
    </script>
@endpush
