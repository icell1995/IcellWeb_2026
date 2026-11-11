@php
    $_title = 'Tambah Surat Perintah Penghentian Penyelidikan';
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
        .form-section-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            background: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .form-section-header {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 1.25rem;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .form-section-title {
            font-size: 1rem;
            font-weight: 700;
            color: #1e3a8a;
            margin: 0;
            display: flex;
            align-items: center;
        }
        .form-section-body {
            padding: 1.25rem 1.25rem 0.5rem 1.25rem;
        }
    </style>
@endpush

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}">
        <i class="bi bi-arrow-left"></i> Kembali ke Progress Perkara
    </a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Tambah Surat Perintah Penghentian Penyelidikan</h5>

            <div class="alert alert-danger" id="attentionBox">
                <div class="text-center">
                    <b>
                        PERHATIAN !<br /><br />
                        DATA INI WAJIB DIISI DENGAN LENGKAP DAN DETAIL SESUAI DENGAN FORMAT RESMI
                        SURAT PERINTAH PENGHENTIAN PENYELIDIKAN (SPPP / HENTI LIDIK).
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
            <form action="{{ route('doc.surat-perintah-penghentian-penyelidikan-document.store', ['accident_id' => $accidentId]) }}"
                method="POST" enctype="multipart/form-data" id="sprintHentiLidikForm" novalidate>
                @csrf
                <input type="hidden" name="accident_id" id="accident_id" value="{{ $accidentId }}">

                {{-- SEKSI I: INFORMASI SURAT PERINTAH --}}
                <div class="form-section-card">
                    <div class="form-section-header">
                        <h6 class="form-section-title">
                            <i class="bi bi-file-earmark-text me-2 text-primary"></i> I. INFORMASI SURAT PERINTAH
                        </h6>
                    </div>
                    <div class="form-section-body">
                        {{-- Nomor LP --}}
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label" for="accidentNumber">Nomor LP</label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <input id="accidentNumber" type="text"
                                    class="form-control font-weight-bold bg-light"
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
                                    placeholder="Contoh: SPPP/Henti.Lidik/0001/I/RES.0.0./2026/Satker">
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
                                <input type="text" class="form-control datepicker @error('document_date') is-invalid @enderror"
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
                                <input id="document_location" type="text" class="form-control"
                                    name="document_location" value="{{ old('document_location', $accident->polres->polres_regency ?? ($accident->polres->name ?? '')) }}"
                                    placeholder="Kota/Kabupaten Satuan Kerja">
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
                    </div>
                </div>

                {{-- SEKSI II: DASAR & DOKUMEN RUJUKAN --}}
                <div class="form-section-card">
                    <div class="form-section-header">
                        <h6 class="form-section-title">
                            <i class="bi bi-journal-bookmark me-2 text-primary"></i> II. DASAR & DOKUMEN RUJUKAN
                        </h6>
                    </div>
                    <div class="form-section-body">

                        {{-- Dokumen Rujukan: Sprinlidik Terdahulu --}}
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label" for="surat_perintah_penyelidikan_document_id">Surat Perintah Penyelidikan</label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <select class="form-control select2" name="surat_perintah_penyelidikan_document_id" id="surat_perintah_penyelidikan_document_id">
                                    <option value="">-- Pilih Surat Perintah Penyelidikan Terdahulu --</option>
                                    @foreach ($sprinlidikDocuments as $sp)
                                        <option value="{{ $sp->id }}"
                                            data-dugaan="{{ $sp->dugaan_formatted ?? 'Kecelakaan Lalu Lintas' }}"
                                            data-pasal="{{ $sp->pasal_formatted ?? '' }}"
                                            {{ old('surat_perintah_penyelidikan_document_id', $sprinlidikDocuments->first()->id ?? '') == $sp->id ? 'selected' : '' }}>
                                            {{ $sp->document_number }} ({{ ($sp->document_date ?: $sp->date) ? date('d/m/Y', strtotime($sp->document_date ?: $sp->date)) : '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Dokumen Rujukan: LHGP --}}
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label" for="laporan_hasil_gelar_perkara_document_id">Laporan Hasil Gelar Perkara (LHGP)</label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <select class="form-control select2" name="laporan_hasil_gelar_perkara_document_id" id="laporan_hasil_gelar_perkara_document_id">
                                    <option value="">-- Pilih Laporan Hasil Gelar Perkara --</option>
                                    @foreach ($lhgpDocuments as $lhgp)
                                        <option value="{{ $lhgp->id }}" {{ old('laporan_hasil_gelar_perkara_document_id', $defaultLhgpId ?? ($lhgpDocuments->first()->id ?? '')) == $lhgp->id ? 'selected' : '' }}>
                                            {{ $lhgp->document_number ?? 'LHGP' }} ({{ ($lhgp->document_date ?: $lhgp->date) ? date('d/m/Y', strtotime($lhgp->document_date ?: $lhgp->date)) : '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SEKSI III: PERKARA, TKP & ALASAN PENGHENTIAN --}}
                <div class="form-section-card">
                    <div class="form-section-header">
                        <h6 class="form-section-title">
                            <i class="bi bi-shield-slash me-2 text-primary"></i> III. DATA PERKARA, TKP & ALASAN PENGHENTIAN
                        </h6>
                    </div>
                    <div class="form-section-body">
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

                        {{-- Tempat Kejadian (TKP) --}}
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label" for="crime_scene_location">Tempat Kejadian (TKP)</label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <input id="crime_scene_location" type="text" class="form-control"
                                    name="crime_scene_location" value="{{ old('crime_scene_location', $defaultLocation) }}"
                                    placeholder="Contoh: Jl. Lintas Timur KM 35, Muaro Jambi">
                            </div>
                        </div>

                        {{-- Kurun Waktu Kejadian --}}
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label" for="incident_time">Kurun Waktu Kejadian</label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <input id="incident_time" type="text" class="form-control"
                                    name="incident_time" value="{{ old('incident_time', $defaultTime) }}"
                                    placeholder="Contoh: 14 Maret 2026 sekira pukul 10:30 WIB">
                            </div>
                        </div>

                        {{-- Dilaporkan oleh (Pelapor) --}}
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label" for="pelapor_name">Dilaporkan oleh (Pelapor)</label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <input id="pelapor_name" type="text" class="form-control"
                                    name="pelapor_name" value="{{ old('pelapor_name', $defaultReporter) }}"
                                    placeholder="Nama pelapor atau nama penyidik pelapor LP Model A">
                            </div>
                        </div>

                        {{-- Pihak Terlapor / Pihak Terkait --}}
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label" for="suspects">Terlapor / Pihak Terkait</label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <select class="form-control select2-multiple" name="suspects[]" id="suspects" multiple="multiple"
                                    data-placeholder="Pilih satu atau lebih terlapor / pengemudi terkait">
                                    @foreach ($suspects as $suspect)
                                        <option value="{{ $suspect->id }}" {{ in_array($suspect->id, old('suspects', [])) ? 'selected' : '' }}>
                                            {{ $suspect->name }} (NIK: {{ $suspect->identity_number ?? ($suspect->nik ?? '-') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Alasan Penghentian Penyelidikan --}}
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label" for="kode_alasan">
                                Alasan Penghentian Penyelidikan<span class="text-danger fs-5">*</span>
                            </label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <select class="form-control select2-multiple" name="kode_alasan[]" id="kode_alasan" multiple="multiple"
                                    data-placeholder="Pilih satu atau lebih alasan penghentian penyelidikan" required>
                                    @foreach ($masterAlasan as $kode => $label)
                                        <option value="{{ $kode }}" {{ in_array($kode, old('kode_alasan', [1])) ? 'selected' : '' }}>
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
                                    placeholder="Catatan atau uraian ringkas penghentian penyelidikan...">{{ old('alasan_penghentian') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SEKSI IV: PEJABAT PEMBERI & PENERIMA PERINTAH --}}
                <div class="form-section-card">
                    <div class="form-section-header">
                        <h6 class="form-section-title">
                            <i class="bi bi-people me-2 text-primary"></i> IV. PEJABAT PEMBERI & PENERIMA PERINTAH
                        </h6>
                    </div>
                    <div class="form-section-body">
                        {{-- Pemberi Perintah (Selaku Penyidik) --}}
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label" for="signatory">
                                Pemberi Perintah (Selaku Penyidik)<span class="text-danger fs-5">*</span>
                            </label>
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

                        {{-- Penyelidik Penerima Perintah --}}
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label" for="officers">Penyelidik Penerima Perintah</label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <select class="form-control select2-multiple" name="officers[]" id="officers" multiple="multiple"
                                    data-placeholder="Pilih penyelidik / penyidik pembantu penerima perintah">
                                    @foreach ($teamOfficers as $officer)
                                        <option value="{{ $officer->id }}" {{ in_array($officer->id, old('officers', [])) ? 'selected' : '' }}>
                                            {{ $officer->register_number }} - {{ $officer->full_name }} ({{ $officer->position->name ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-1">
                                    Personel pertama yang dipilih akan dicantumkan pada kolom tanda tangan "Yang Menerima Perintah", dan seluruh personel tercantum dalam lampiran.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SEKSI V: TEMBUSAN --}}
                <div class="form-section-card">
                    <div class="form-section-header">
                        <h6 class="form-section-title">
                            <i class="bi bi-send me-2 text-primary"></i> V. TEMBUSAN
                        </h6>
                    </div>
                    <div class="form-section-body">
                        {{-- Tembusan --}}
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label">Tembusan</label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <div id="carbonCopiesContainer">
                                    @php
                                        $carbonCopies = old('carbon_copies', []);
                                    @endphp
                                    @foreach ($carbonCopies as $cc)
                                        <div class="carbon-copy-item d-flex mb-2">
                                            <input type="text" name="carbon_copies[]" class="form-control me-2" value="{{ $cc }}" placeholder="Nama Tembusan">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-cc"><i class="bi bi-trash"></i></button>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="addCarbonCopy">
                                    <i class="bi bi-plus-circle me-1"></i> Tambah Tembusan
                                </button>
                                <small class="text-muted d-block mt-1">
                                    Klik "Tambah Tembusan" jika surat ini memiliki tembusan kepada pejabat atau instansi terkait.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TOMBOL AKSI TERPUSAT --}}
                <div class="text-center mt-4 mb-3">
                    <button type="submit" class="btn btn-dark-blue px-4 me-2" id="sprintHentiLidikFormSubmit">
                        <i class="bi bi-save me-1"></i> Simpan Surat Perintah
                    </button>
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"
                        class="btn btn-danger px-4">
                        <i class="bi bi-x-circle me-1"></i> Batal
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

            // Select2 Init
            $('.select2').select2({ theme: 'bootstrap4', width: '100%' });
            $('.select2-multiple').select2({
                theme: 'bootstrap4',
                width: '100%',
                closeOnSelect: false
            });

            // Datepicker
            if ($.fn.datepicker) {
                $('.datepicker').datepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true,
                    todayHighlight: true
                });
            }


            // Auto-fill Dugaan & Pasal from selected Sprinlidik
            $('#surat_perintah_penyelidikan_document_id').on('change', function() {
                var selected = $(this).find('option:selected');
                var dugaan = selected.data('dugaan');
                var pasal  = selected.data('pasal');
                if (dugaan && !$('#dugaan_tindak_pidana').val()) {
                    $('#dugaan_tindak_pidana').val(dugaan);
                }
                if (pasal && !$('#pasal_list').val()) {
                    $('#pasal_list').val(pasal);
                }
            });

            // Dynamic Carbon Copies
            $('#addCarbonCopy').on('click', function() {
                var count = $('#carbonCopiesContainer .carbon-copy-item').length + 1;
                var html = `
                    <div class="carbon-copy-item d-flex mb-2">
                        <input type="text" name="carbon_copies[]" class="form-control me-2" placeholder="Tembusan ` + count + `">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-cc"><i class="bi bi-trash"></i></button>
                    </div>
                `;
                $('#carbonCopiesContainer').append(html);
            });

            $(document).on('click', '.remove-cc', function() {
                $(this).closest('.carbon-copy-item').remove();
            });

            // AJAX Validation before form submit
            $('#sprintHentiLidikForm').on('submit', function(e) {
                e.preventDefault();
                var form = this;
                var formData = new FormData(form);

                $('.invalid-feedback.frontend-error').remove();
                $('.is-invalid').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('doc.surat-perintah-penghentian-penyelidikan-document.api.validate-request-form', ['accident_id' => $accidentId]) }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function() {
                        form.submit();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(field, messages) {
                                var input = $('[name="' + field + '"]');
                                if (!input.length) {
                                    input = $('[name="' + field + '[]"]');
                                }
                                input.addClass('is-invalid');
                                input.closest('.col-lg-9').append(
                                    '<span class="invalid-feedback frontend-error d-block"><strong>' + messages[0] + '</strong></span>'
                                );
                            });

                            Swal.fire({
                                icon: 'warning',
                                title: 'Periksa Formulir',
                                text: 'Mohon lengkapi semua kolom wajib yang bertanda bintang (*).'
                            });
                        } else {
                            form.submit();
                        }
                    }
                });
            });
        });
    </script>
@endpush
