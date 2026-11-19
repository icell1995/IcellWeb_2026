@php
    $_title = 'Tambah Surat Perintah Penghentian Penyidikan';
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
            <h5 class="fw-bold text-blue-dark">Tambah Surat Perintah Penghentian Penyidikan</h5>

            <div class="alert alert-danger" id="attentionBox">
                <div class="text-center">
                    <b>
                        PERHATIAN !<br /><br />
                        DATA INI WAJIB DIISI DENGAN LENGKAP DAN DETAIL SESUAI DENGAN BERKAS PERKARA
                        PENGHENTIAN PENYIDIKAN YANG BERLAKU.
                    </b>
                </div>
            </div>

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
            <form action="{{ route('doc.surat-perintah-penghentian-penyidikan-document.store', ['accident_id' => $accidentId]) }}"
                method="POST" enctype="multipart/form-data" id="sprintHentiForm" novalidate>
                @csrf
                <input type="hidden" name="accident_id" id="accident_id" value="{{ $accidentId }}">

                {{-- Nomor LP --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="accidentNumber">Nomor LP</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="accidentNumber" type="text"
                            class="form-control font-weight-bold"
                            value="{{ $accident->no_lp }}" readonly>
                    </div>
                </div>

                {{-- Nomor Surat Perintah --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="document_number">Nomor Surat Perintah<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="document_number" type="text"
                            class="form-control @error('document_number') is-invalid @enderror font-weight-bold"
                            name="document_number" value="{{ old('document_number') }}" required
                            placeholder="Contoh: SP.Sidik-Henti/01/X/2026/Satlantas">
                        @error('document_number')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Tanggal Surat Perintah --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="document_date">Tanggal Surat Perintah<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input class="form-control datepicker @error('document_date') is-invalid @enderror"
                            id="document_date" name="document_date" placeholder="YYYY-MM-DD"
                            autocomplete="off" value="{{ old('document_date', date('Y-m-d')) }}" required>
                        @error('document_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Dikeluarkan di --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="document_location">Dikeluarkan di</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="document_location" type="text" class="form-control font-weight-bold"
                            name="document_location" value="{{ old('document_location', $accident->polres->polres_regency ?? ($accident->polres->name ?? '')) }}"
                            placeholder="Kota/Kabupaten">
                    </div>
                </div>

                {{-- Klasifikasi Dokumen --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="document_classification_id">Klasifikasi Dokumen</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="document_classification_id" id="document_classification_id">
                            <option value="">-- Pilih Klasifikasi Dokumen --</option>
                            @foreach ($documentClassifications as $dc)
                                <option value="{{ $dc->id }}" {{ old('document_classification_id') == $dc->id ? 'selected' : '' }}>
                                    {{ $dc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Dokumen Rujukan: Sprindik --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="surat_perintah_penyidikan_document_id">Nomor Surat Perintah Penyidikan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="surat_perintah_penyidikan_document_id" id="surat_perintah_penyidikan_document_id">
                            <option value="">-- Pilih Surat Perintah Penyidikan Terdahulu --</option>
                            @foreach ($sprindikDocuments as $sp)
                                <option value="{{ $sp->id }}"
                                    data-dugaan="{{ $sp->dugaan_formatted ?? 'Kecelakaan Lalu Lintas' }}"
                                    data-pasal="{{ $sp->pasal_formatted ?? '' }}"
                                    {{ old('surat_perintah_penyidikan_document_id') == $sp->id ? 'selected' : '' }}>
                                    {{ $sp->document_number }} ({{ $sp->document_date ? date('d/m/Y', strtotime($sp->document_date)) : '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Dokumen Rujukan: SPDP --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="surat_pemberitahuan_dimulainya_penyidikan_document_id">Nomor Surat Pemberitahuan Dimulainya Penyidikan (SPDP)</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="surat_pemberitahuan_dimulainya_penyidikan_document_id" id="surat_pemberitahuan_dimulainya_penyidikan_document_id">
                            <option value="">-- Pilih SPDP Terdahulu --</option>
                            @foreach ($spdpDocuments as $spdp)
                                <option value="{{ $spdp->id }}" {{ old('surat_pemberitahuan_dimulainya_penyidikan_document_id') == $spdp->id ? 'selected' : '' }}>
                                    {{ $spdp->document_number }} ({{ $spdp->document_date ? date('d/m/Y', strtotime($spdp->document_date)) : '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Dokumen Rujukan: SKET Penetapan Tersangka --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="surat_ketetapan_tentang_penetapan_tersangka_document_id">Nomor Surat Ketetapan Penetapan Tersangka</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="surat_ketetapan_tentang_penetapan_tersangka_document_id" id="surat_ketetapan_tentang_penetapan_tersangka_document_id">
                            <option value="">-- Pilih Surat Ketetapan Penetapan Tersangka --</option>
                            @foreach ($skpptDocuments as $skppt)
                                <option value="{{ $skppt->id }}" {{ old('surat_ketetapan_tentang_penetapan_tersangka_document_id') == $skppt->id ? 'selected' : '' }}>
                                    {{ $skppt->document_number }} ({{ $skppt->document_date ? date('d/m/Y', strtotime($skppt->document_date)) : '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>


                {{-- Dokumen Rujukan: LHGP --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="laporan_hasil_gelar_perkara_document_id">Nomor Laporan Hasil Gelar Perkara</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="laporan_hasil_gelar_perkara_document_id" id="laporan_hasil_gelar_perkara_document_id">
                            <option value="">-- Pilih Laporan Hasil Gelar Perkara --</option>
                            @foreach ($lhgpDocuments as $lhgp)
                                <option value="{{ $lhgp->id }}" {{ old('laporan_hasil_gelar_perkara_document_id') == $lhgp->id ? 'selected' : '' }}>
                                    {{ $lhgp->document_number ?? 'LHGP' }} ({{ $lhgp->document_date ? date('d/m/Y', strtotime($lhgp->document_date)) : '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Dugaan Tindak Pidana --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="dugaan_tindak_pidana">Dugaan Tindak Pidana</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="dugaan_tindak_pidana" type="text" class="form-control"
                            name="dugaan_tindak_pidana" value="{{ old('dugaan_tindak_pidana', $defaultDugaan) }}"
                            placeholder="Contoh: Kecelakaan Lalu Lintas">
                    </div>
                </div>

                {{-- Pasal yang Dipersangkakan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="pasal_list">Pasal yang Dipersangkakan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <textarea id="pasal_list" class="form-control" name="pasal_list" rows="2"
                            placeholder="Contoh: Pasal 310 ayat (4) UU No. 22 Tahun 2009">{{ old('pasal_list', $defaultPasal) }}</textarea>
                    </div>
                </div>

                {{-- Alasan Penghentian --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="kode_alasan">Alasan Penghentian<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2-multiple" name="kode_alasan[]" id="kode_alasan" multiple="multiple"
                            data-placeholder="Pilih satu atau lebih alasan penghentian penyidikan" required>
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

                {{-- Uraian Alasan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="alasan_penghentian">Uraian Alasan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <textarea id="alasan_penghentian" class="form-control" name="alasan_penghentian" rows="2"
                            placeholder="Catatan atau uraian ringkas penghentian penyidikan...">{{ old('alasan_penghentian') }}</textarea>
                    </div>
                </div>

                {{-- Tersangka yang Dihentikan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="suspects">Tersangka yang Dihentikan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2-multiple" name="suspects[]" id="suspects" multiple="multiple"
                            data-placeholder="Pilih satu atau lebih tersangka">
                            @foreach ($suspects as $suspect)
                                <option value="{{ $suspect->id }}" {{ in_array($suspect->id, old('suspects', [])) ? 'selected' : '' }}>
                                    {{ $suspect->name }} (NIK: {{ $suspect->identity_number ?? ($suspect->nik ?? '-') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Pemberi Perintah / Penandatangan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="signatory">Pemberi Perintah / Penandatangan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="signatory" id="signatory" required>
                            <option value="">-- Pilih Pejabat Pemberi Perintah --</option>
                            @foreach ($authorizedSignatories as $signatory)
                                <option value="{{ $signatory->id }}" {{ old('signatory') == $signatory->id ? 'selected' : '' }}>
                                    {{ $signatory->register_number }} - {{ $signatory->full_name }} ({{ $signatory->position->name ?? '-' }})
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

                {{-- Penyidik Penerima Perintah --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="officers">Penyidik Penerima Perintah</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2-multiple" name="officers[]" id="officers" multiple="multiple"
                            data-placeholder="Pilih penyidik / penyidik pembantu penerima perintah">
                            @foreach ($teamOfficers as $officer)
                                <option value="{{ $officer->id }}" {{ in_array($officer->id, old('officers', [])) ? 'selected' : '' }}>
                                    {{ $officer->register_number }} - {{ $officer->full_name }} ({{ $officer->position->name ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Tembusan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label">Tembusan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <div id="carbonCopiesContainer">
                            @php
                                $carbonCopies = old('carbonCopies', []);
                            @endphp
                            @if(count($carbonCopies) > 0)
                                @foreach($carbonCopies as $index => $copy)
                                    <div class="input-group mb-2 carbon-copy-row">
                                        <input type="text" name="carbonCopies[]" class="form-control carbon-copy-input" value="{{ $copy }}">
                                        @if($index >= 1)
                                            <button type="button" class="btn btn-danger removeCarbonCopy"><i class="bi bi-trash"></i></button>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="input-group mb-2 carbon-copy-row">
                                    <input type="text" name="carbonCopies[]" class="form-control carbon-copy-input" placeholder="Contoh: Kepala Kepolisian Daerah...">
                                </div>
                            @endif
                        </div>
                        <button class="btn btn-outline-primary btn-sm mt-1 addCarbonCopiesButton" type="button">
                            <i class="bi bi-plus"></i> Tambah Tembusan Lainnya
                        </button>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="button" class="btn btn-dark-blue" id="sprintHentiFormSubmit">
                        <i class="bi bi-save"></i> {{ __('Simpan') }}
                    </button>
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"
                        class="btn btn-danger">
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
        $(document).ready(function() {
            // Attention Box blinking
            setInterval(function() {
                $('#attentionBox').toggleClass('alert-danger alert-warning');
            }, 1000);

            // Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            $('.select2-multiple').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Datepicker
            if ($.fn.datepicker) {
                $('.datepicker').datepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true,
                    todayHighlight: true
                });
            }

            // Auto-fill Dugaan & Pasal from selected Sprindik
            $('#surat_perintah_penyidikan_document_id').on('change', function() {
                var selected = $(this).find(':selected');
                var dugaan = selected.data('dugaan');
                var pasal  = selected.data('pasal');
                if (dugaan) {
                    $('#dugaan_tindak_pidana').val(dugaan);
                }
                if (pasal) {
                    $('#pasal_list').val(pasal);
                }
            });

            // Dynamic Tembusan (Carbon Copies)
            $('.addCarbonCopiesButton').on('click', function() {
                var html = `
                    <div class="input-group mb-2 carbon-copy-row">
                        <input type="text" name="carbonCopies[]" class="form-control carbon-copy-input" placeholder="Contoh: Kepala Kejaksaan Negeri...">
                        <button type="button" class="btn btn-danger removeCarbonCopy"><i class="bi bi-trash"></i></button>
                    </div>`;
                $('#carbonCopiesContainer').append(html);
            });

            $(document).on('click', '.removeCarbonCopy', function() {
                $(this).closest('.carbon-copy-row').remove();
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

            // Validasi Submit Form
            $('#sprintHentiFormSubmit').on('click', function(e) {
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
                checkInput('#document_number', 'Nomor Surat Perintah');
                checkInput('#document_date', 'Tanggal Surat Perintah');
                checkSelect('#kode_alasan', 'Alasan Penghentian');
                checkSelect('#signatory', 'Pemberi Perintah / Penandatangan');

                // Jika ada error di frontend, scroll ke field pertama
                if (errors.length > 0) {
                    scrollToFirstError();
                    return false;
                }

                // Validasi sisi server via Ajax
                $.ajax({
                    url: "{{ route('doc.surat-perintah-penghentian-penyidikan-document.api.validate-request-form', ['accident_id' => $accidentId]) }}",
                    type: 'POST',
                    dataType: 'json',
                    data: $('#sprintHentiForm').serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: response.message || 'Silahkan menunggu proses simpan data',
                                icon: 'success',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                $('#sprintHentiForm')[0].submit();
                            });
                        }
                    },
                    error: function(xhr) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.code == '422' && response.errors) {
                                if (typeof response.errors === 'object' && !Array.isArray(response.errors)) {
                                    $.each(response.errors, function(key, messages) {
                                        var msg = Array.isArray(messages) ? messages[0] : messages;
                                        var $target = $('#' + key + ', [name="' + key + '"], [name="' + key + '[]"]');
                                        if ($target.length) {
                                            markError($target, msg);
                                        } else {
                                            markError('#' + key, msg);
                                        }
                                    });
                                    scrollToFirstError();
                                } else {
                                    var errorMessages = '';
                                    $.each(response.errors, function(key, value) { errorMessages += '- ' + value + '<br>'; });
                                    Swal.fire({ icon: 'error', title: 'Periksa Isian', html: errorMessages });
                                }
                            } else {
                                var message = response.message || response.errors || 'Terjadi kesalahan saat memproses data.';
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Perhatian',
                                    text: typeof message === 'string' ? message : JSON.stringify(message)
                                });
                            }
                        } catch(e) {
                            console.error(e);
                        }
                    }
                });
            });
        });
    </script>
@endpush
