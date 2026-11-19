@php
    $_title = 'Edit SPDP — Pusiknas Bareskrim (SPPT-TI)';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
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
        <i class="bi bi-arrow-left"></i> Kembali ke Progres Perkara
    </a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">
                Edit Surat Pemberitahuan Dimulainya Penyidikan
                <span class="badge bg-info text-white ms-2">SPPT-TI / Pusiknas Bareskrim</span>
            </h5>

            <div class="alert alert-danger" id="attentionBox">
                <div class="text-center">
                    <b>
                        PERHATIAN !<br /><br />
                        DATA INI WAJIB DIISI DENGAN DETAIL DAN LENGKAP KARENA AKAN DIPERTUKARKAN DENGAN
                        PUSIKNAS BARESKRIM POLRI DALAM KERANGKA SPPT-TI (SISTEM PERADILAN PIDANA BERBASIS
                        TEKNOLOGI INFORMASI). KODE PROSES: <strong>DIK-10</strong>
                    </b>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
        </div>

        <div class="box-body">
            <form action="{{ route('doc.surat-pemberitahuan-dimulainya-penyidikan-pusiknas-document.update', ['id' => $document->id, 'accident_id' => $accidentId]) }}"
                method="POST" enctype="multipart/form-data" id="spdpPusiknasForm" novalidate>
                @csrf
                <input type="hidden" name="accident_id" value="{{ $accidentId }}">

                <div class="row">
                    <div class="col-12">
                        
                        {{-- Nomor LP --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold">Nomor LP (Laporan Polisi)</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control font-weight-bold" value="{{ $accident->no_lp }}" readonly>
                            </div>
                        </div>

                        {{-- Nomor SPDP --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="documentNumber">Nomor SPDP <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input id="documentNumber" type="text" class="form-control" name="documentNumber" value="{{ old('documentNumber', $document->document_number ?? '') }}" placeholder="Contoh: SPDP/326/IX/2024" required>
                            </div>
                        </div>

                        {{-- Tanggal SPDP --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="documentDate">Tanggal SPDP <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input id="documentDate" type="text" class="form-control" name="documentDate" value="{{ old('documentDate', $document->document_date ?? '') }}" placeholder="YYYY-MM-DD" autocomplete="off" required>
                            </div>
                        </div>

                        {{-- Klasifikasi --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="documentClassification">Klasifikasi Surat <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control select2" name="documentClassification" id="documentClassification" required>
                                    <option value="">-- Pilih Klasifikasi --</option>
                                    @foreach ($documentClassifications as $dc)
                                        <option value="{{ $dc->id }}" {{ old('documentClassification', $document->document_classification_id ?? '') == $dc->id ? 'selected' : '' }}>{{ $dc->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr>

                        {{-- SP Penyidikan --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="suratPerintahPenyidikanDocument">Nomor SP Penyidikan (SPRINDIK) <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control select2" name="suratPerintahPenyidikanDocument" id="suratPerintahPenyidikanDocument" required>
                                    <option value="">-- Pilih No SP Penyidikan --</option>
                                    @foreach ($suratPerintahPenyidikanDocuments as $sprindik)
                                        <option value="{{ $sprindik->id }}" 
                                            data-document-date="{{ date('Y-m-d', strtotime($sprindik->document_date)) }}" 
                                            data-pasal="{{ $sprindik->pasal_formatted }}"
                                            {{ old('suratPerintahPenyidikanDocument', $document->surat_perintah_penyidikan_document_id ?? '') == $sprindik->id ? 'selected' : '' }}>
                                            {{ $sprindik->document_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold">Tanggal SP Penyidikan</label>
                            <div class="col-sm-9">
                                <input class="form-control" id="suratPerintahPenyidikanDocumentDate" name="suratPerintahPenyidikanDocumentDate" placeholder="Otomatis terisi" autocomplete="off" readonly>
                            </div>
                        </div>

                        {{-- SP Tugas --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="suratPerintahTugasDocument">Nomor SP Tugas Penyidikan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control select2" name="suratPerintahTugasDocument" id="suratPerintahTugasDocument" required>
                                    <option value="">-- Pilih No SP Tugas --</option>
                                    @foreach ($suratPerintahTugasDocuments as $spt)
                                        <option value="{{ $spt->id }}" {{ old('suratPerintahTugasDocument', $document->surat_perintah_tugas_document_id ?? '') == $spt->id ? 'selected' : '' }}>{{ $spt->document_number }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr>

                        {{-- Kejaksaan --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="prosecutor">Kejaksaan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control select2" name="prosecutor" id="prosecutor" required>
                                    <option value="">-- Pilih Kejaksaan --</option>
                                    @foreach ($prosecutors as $p)
                                        <option value="{{ $p->id }}" {{ old('prosecutor', $document->prosecutor_id ?? '') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Pengadilan --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="court">Pengadilan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control select2" name="court" id="court" required>
                                    <option value="">-- Pilih Pengadilan --</option>
                                    @foreach ($courts as $c)
                                        <option value="{{ $c->id }}" {{ old('court', $document->court_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr>

                        {{-- Pasal UU (readonly auto) --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold">Pasal UU yang Disangkakan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" id="pasal_uu_disangkakan" class="form-control" value="Otomatis mengambil dari SP Penyidikan yang dipilih" readonly>
                            </div>
                        </div>

                        {{-- Kode Wilayah --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="kode_wilayah">Kode Wilayah (Kecamatan) <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control select2" name="kode_wilayah" id="kode_wilayah" required>
                                    <option value="">-- Pilih Kecamatan --</option>
                                    @foreach ($districts as $district)
                                        <option value="{{ $district['KodePuskarda'] }}" {{ old('kode_wilayah', $document->messages['kode_wilayah'] ?? $defaultKodeWilayah ?? '') == $district['KodePuskarda'] ? 'selected' : '' }}>
                                            {{ $district['KodePuskarda'] }} — {{ $district['Nama'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Lokasi Kejadian (Opsional) --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="lokasi_kejadian">Lokasi Kejadian <small class="text-muted">(opsional)</small></label>
                            <div class="col-sm-9">
                                <input id="lokasi_kejadian" type="text" class="form-control" name="lokasi_kejadian" value="{{ old('lokasi_kejadian', $document->messages['lokasi_kejadian'] ?? $accident->road_name) }}" placeholder="Contoh: Jl. Kota Baru Indah Blok B7 No.30">
                            </div>
                        </div>

                        {{-- Waktu Kejadian (readonly) --}}
                        <div class="form-group row mb-3 d-none">
                            <label class="col-sm-3 col-form-label fw-bold">Waktu Kejadian <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="Sekitar pukul {{ \Carbon\Carbon::parse($accident->accident_time)->format('H:i') }} WIB" readonly>
                                <span class="text-muted small">Otomatis dari data perkara</span>
                            </div>
                        </div>

                        {{-- Tanggal Kejadian (readonly) --}}
                        <div class="form-group row mb-3 d-none">
                            <label class="col-sm-3 col-form-label fw-bold">Tahun/Bulan/Tanggal Kejadian <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <div class="row">
                                    <div class="col-md-4 mb-2 mb-md-0">
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-muted">Tahun</span>
                                            <input type="text" class="form-control text-center bg-white" value="{{ \Carbon\Carbon::parse($accident->accident_date)->format('Y') }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2 mb-md-0">
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-muted">Bulan</span>
                                            <input type="text" class="form-control text-center bg-white" value="{{ \Carbon\Carbon::parse($accident->accident_date)->format('m') }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-muted">Tanggal</span>
                                            <input type="text" class="form-control text-center bg-white" value="{{ \Carbon\Carbon::parse($accident->accident_date)->format('d') }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <span class="text-muted small d-block mt-1">Otomatis dari data perkara</span>
                            </div>
                        </div>

                        {{-- Uraian Singkat Perkara (Opsional) - Hidden per user request --}}
                        <div class="form-group row mb-3 d-none">
                            <label class="col-sm-3 col-form-label fw-bold" for="uraianSingkatPerkara">Uraian Singkat Perkara <small class="text-muted">(opsional)</small></label>
                            <div class="col-sm-9">
                                <textarea id="uraianSingkatPerkara" class="form-control" name="uraianSingkatPerkara" rows="3" placeholder="Deskripsikan singkat perkara...">{{ old('uraianSingkatPerkara', $document->messages['uraian_singkat_perkara'] ?? $accident->damage_lose_desc) }}</textarea>
                            </div>
                        </div>

                        {{-- Sumber Dana (Opsional) --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="sumber_dana">Sumber Dana <small class="text-muted">(opsional)</small></label>
                            <div class="col-sm-9">
                                <input id="sumber_dana" type="text" class="form-control" name="sumber_dana" value="{{ old('sumber_dana', $document->messages['sumber_dana'] ?? '') }}" placeholder="Opsional">
                            </div>
                        </div>

                        {{-- Sumber Informasi (Opsional) --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="sumber_informasi">Sumber Informasi <small class="text-muted">(opsional)</small></label>
                            <div class="col-sm-9">
                                <input id="sumber_informasi" type="text" class="form-control" name="sumber_informasi" value="{{ old('sumber_informasi', $document->messages['sumber_informasi'] ?? '') }}" placeholder="Opsional">
                            </div>
                        </div>

                        <hr>

                        {{-- Tersangka / Terlapor --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold">Ada Tersangka? <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input isSuspectExists" type="radio" id="suspectExists" name="isSuspectExists" value="true" {{ old('isSuspectExists', $document->is_suspect_exists ? 'true' : 'false') == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="suspectExists">Ada Tersangka</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input isSuspectExists" type="radio" id="suspectNotExists" name="isSuspectExists" value="false" {{ old('isSuspectExists', $document->is_suspect_exists ? 'true' : 'false') == 'false' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="suspectNotExists">Tidak Ada Tersangka (Terlapor)</label>
                                </div>
                            </div>
                        </div>

                        <div id="suspectExistsSection">
                            <div class="alert alert-success small">Pastikan LHGP Penetapan Tersangka dan Surat Ketetapan sudah dibuat sebelum memilih tersangka.</div>
                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label fw-bold" for="suspects">Pilih Tersangka <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <select class="form-control select2-multiple" name="suspects[]" id="suspects" multiple>
                                        @foreach ($suspects as $suspect)
                                            <option value="{{ $suspect->id }}" {{ in_array($suspect->id, old('suspects', $selectedSuspects ?? [])) ? 'selected' : '' }}>{{ $suspect->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div id="suspectNotExistsSection" style="display:none;">
                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label fw-bold" for="reportedPerson">Pilih Terlapor</label>
                                <div class="col-sm-9">
                                    <select class="form-control select2" name="reportedPerson" id="reportedPerson">
                                        <option value="">-- Pilih Terlapor --</option>
                                        @foreach ($reportedPersons as $rp)
                                            <option value="{{ $rp->id }}" {{ old('reportedPerson', $document->reportedPersons->first()->id ?? '') == $rp->id ? 'selected' : '' }}>{{ $rp->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- Tembusan --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold">Tembusan <span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <div id="carbonCopiesContainer">
                                    @php
                                        $carbonCopies = old('carbonCopies', is_array($document->carbon_copies) ? $document->carbon_copies : (json_decode($document->carbon_copies, true) ?? []));
                                    @endphp
                                    @if(count($carbonCopies) > 0)
                                        @foreach($carbonCopies as $index => $copy)
                                            <div class="input-group mb-2 carbon-copy-row">
                                                <input type="text" name="carbonCopies[]" class="form-control carbon-copy-input" value="{{ $copy }}" required>
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

                        {{-- Penandatangan --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="signatory">Penandatangan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control select2" name="signatory" id="signatory" required>
                                    <option value="">-- Pilih Penandatangan --</option>
                                    @php
                                        $savedSignatory = $document->officers ? ($document->officers->where('class', 'SIGNATORY')->first() ?? $document->officers->first()) : null;
                                        $savedRegisterNumber = $savedSignatory ? $savedSignatory->register_number : null;
                                    @endphp
                                    @foreach ($authorizedSignatories as $officer)
                                        <option value="{{ $officer->id }}" {{ old('signatory') == $officer->id || (empty(old('signatory')) && $savedRegisterNumber == $officer->register_number) ? 'selected' : '' }}>
                                            {{ $officer->full_name ?? ($officer->first_name . ' ' . $officer->last_name) }} — {{ $officer->position->name ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Lampiran --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="appendix">Jumlah Lampiran <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input id="appendix" type="number" class="form-control onlyIntegerInput" name="appendix" value="{{ old('appendix', $document->appendix ?? 1) }}" min="1" max="999" required>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Submit --}}
                <div class="d-flex gap-2 mt-3 align-center justify-content-center">
                    <button type="button" id="spdpPusiknasFormSubmit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan SPDP
                    </button>
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> Batal
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

            // Attention box blink
            setInterval(function () { $('#attentionBox').toggleClass('alert-danger alert-warning'); }, 1000);

            // Datepicker
            $('#documentDate').datepicker({ format: 'yyyy-mm-dd', autoclose: true, endDate: new Date() });
            $('#documentDate').keydown(function (e) { e.preventDefault(); return false; });

            // Select2
            $('.select2').select2({ theme: 'bootstrap4', width: '100%' });
            $('.select2-multiple').select2({ theme: 'bootstrap4', width: '100%' });

            // Auto-fill tanggal sprindik & pasal
            $('#suratPerintahPenyidikanDocument').on('change', function () {
                var selected = $(this).find(':selected');
                var documentDate = selected.data('document-date');
                var pasal = selected.data('pasal');
                
                $('#suratPerintahPenyidikanDocumentDate').val(documentDate || '');
                $('#pasal_uu_disangkakan').val(pasal || 'Otomatis mengambil dari SP Penyidikan yang dipilih');
            });
            
            // Auto-fill Tembusan & Dynamic Rows
            function addCarbonCopyRow(value) {
                var row = `
                    <div class="input-group mb-2 carbon-copy-row">
                        <input type="text" name="carbonCopies[]" class="form-control carbon-copy-input" value="${value}" required>
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

            function updateTembusan(isUserAction = false) {
                var prosecutor = $('#prosecutor').find(':selected').text().trim();
                var court = $('#court').find(':selected').text().trim();
                
                var firstVal = '';
                if (prosecutor && prosecutor !== '-- Pilih Kejaksaan --') {
                    firstVal = 'Kepala ' + toTitleCase(prosecutor);
                }
                
                var secondVal = '';
                if (court && court !== '-- Pilih Pengadilan --') {
                    secondVal = 'Ketua ' + toTitleCase(court);
                }

                var rows = $('#carbonCopiesContainer .carbon-copy-row');
                
                if (isUserAction && rows.length >= 1) {
                    $('#carbonCopiesContainer .carbon-copy-input').eq(0).val(firstVal).attr('readonly', true);
                }
                if (isUserAction && rows.length >= 2) {
                    $('#carbonCopiesContainer .carbon-copy-input').eq(1).val(secondVal).attr('readonly', true);
                }
            }
            $('#prosecutor, #court').on('change', function() { updateTembusan(true); });
            // Do not call updateTembusan on load for edit form, preserve existing values
            
            // Trigger UI updates for edit
            $('.isSuspectExists:checked').trigger('change');
            $('#suratPerintahPenyidikanDocument').trigger('change');

            // Toggle tersangka / terlapor
            $(document).on('change', '.isSuspectExists', function () {
                var val = $(this).val();
                if (val === 'true') {
                    $('#suspectExistsSection').show();
                    $('#suspectNotExistsSection').hide();
                } else {
                    $('#suspectExistsSection').hide();
                    $('#suspectNotExistsSection').show();
                }
            });

            // Integer only
            $('.onlyIntegerInput').on('keypress', function (e) {
                var charCode = (e.which) ? e.which : e.keyCode;
                if (charCode > 31 && (charCode < 48 || charCode > 57)) { e.preventDefault(); }
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
                $field.closest('.input-group, .form-group, .mb-3, .col-sm-9, div').find('.frontend-error, .invalid-feedback').remove();
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
            $('#spdpPusiknasFormSubmit').on('click', function (e) {
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
                checkInput('#documentNumber', 'Nomor SPDP');
                checkInput('#documentDate', 'Tanggal SPDP');
                checkSelect('#documentClassification', 'Klasifikasi Surat');
                checkSelect('#suratPerintahPenyidikanDocument', 'Nomor SP Penyidikan');
                checkSelect('#suratPerintahTugasDocument', 'Nomor SP Tugas Penyidikan');
                checkSelect('#prosecutor', 'Kejaksaan');
                checkSelect('#court', 'Pengadilan');
                checkSelect('#kode_wilayah', 'Kode Wilayah (Kecamatan)');

                // Tersangka / Terlapor
                var isSuspect = $('.isSuspectExists:checked').val();
                if (isSuspect === 'true') {
                    checkSelect('#suspects', 'Pilih Tersangka');
                } else {
                    checkSelect('#reportedPerson', 'Pilih Terlapor');
                }

                // Tembusan
                $('#carbonCopiesContainer .carbon-copy-input').each(function(idx) {
                    var val = ($(this).val() || '').trim();
                    if (!val) {
                        markError($(this), 'Tembusan ke-' + (idx + 1) + ' harus diisi');
                    }
                });

                checkSelect('#signatory', 'Penandatangan');
                checkInput('#appendix', 'Jumlah Lampiran');

                // Jika ada error di frontend, scroll ke field pertama
                if (errors.length > 0) {
                    scrollToFirstError();
                    return false;
                }

                // Validasi sisi server via Ajax
                $.ajax({
                    url: "{{ route('doc.surat-pemberitahuan-dimulainya-penyidikan-pusiknas-document.api.validate-request-form', ['accident_id' => $accidentId, 'document_id' => $document->id]) }}",
                    type: 'POST',
                    dataType: 'json',
                    data: $('#spdpPusiknasForm').serialize(),
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: response.message || 'Silahkan menunggu proses simpan data',
                                icon: 'success',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                $('#spdpPusiknasForm')[0].submit();
                            });
                        }
                    },
                    error: function (xhr) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.code == '422' && response.errors) {
                                if (typeof response.errors === 'object' && !Array.isArray(response.errors)) {
                                    $.each(response.errors, function(key, messages) {
                                        var msg = Array.isArray(messages) ? messages[0] : messages;
                                        var $target = $('#' + key + ', [name="' + key + '"], [name="' + key + '[]"]');
                                        if ($target.length) {
                                            markError($target, msg);
                                        } else if (key === 'carbonCopies') {
                                            markError('#carbonCopiesContainer .carbon-copy-input:first', msg);
                                        } else if (key.indexOf('carbonCopies.') === 0) {
                                            var idx = parseInt(key.split('.')[1]);
                                            markError($('#carbonCopiesContainer .carbon-copy-input').eq(idx), msg);
                                        } else if (key === 'suspects') {
                                            markError('#suspects', msg);
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
                                var message = response.message || response.errors || 'Terjadi kesalahan saat memproses data.';
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Perhatian',
                                    text: typeof message === 'string' ? message : JSON.stringify(message)
                                });
                            }
                        } catch (e) {
                            console.error(e);
                        }
                    }
                });
            });

        });
        // Auto-suggest Kode Wilayah from Lokasi Kejadian
    $('#lokasi_kejadian').on('change blur', function() {
        const lokasi = $(this).val();
        if (!lokasi) return;

        const match = lokasi.match(/(?:KECAMATAN|KEC)\.?\s*([A-Za-z\s]+?)\s*(?:KABUPATEN|KAB\.|KAB|KOTA|,|$)/i);
        if (match && match[1]) {
            const extracted = match[1].trim().toLowerCase();

            // Loop through options and select if matches
            let found = false;
            $('#kode_wilayah option').each(function() {
                const text = $(this).text().toLowerCase();
                // Replace non-alphanumeric to normalize
                const normalizedText = text.replace(/[^a-z0-9]/g, '');
                const normalizedExtracted = extracted.replace(/[^a-z0-9]/g, '');
                
                if (normalizedText.includes(normalizedExtracted)) {
                    $('#kode_wilayah').val($(this).val()).trigger('change');
                    found = true;
                    return false; // break loop
                }
            });
        }
    });
</script>
@endpush
