@php
    $_title = 'SPDP — Pusiknas Bareskrim (SPPT-TI)';
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
            <h5 class="fw-bold text-blue-dark">
                Tambah SPDP — Surat Pemberitahuan Dimulainya Penyidikan
                <span class="badge badge-info ms-2">SPPT-TI / Pusiknas Bareskrim</span>
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
            <form action="{{ route('doc.spdp-pusiknas-document.store', ['accident_id' => $accidentId]) }}"
                method="POST" enctype="multipart/form-data" id="spdpPusiknasForm">
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
                                <input id="documentNumber" type="text" class="form-control" name="documentNumber" value="{{ old('documentNumber') }}" placeholder="Contoh: SPDP/326/IX/2024" required>
                            </div>
                        </div>

                        {{-- Tanggal SPDP --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="documentDate">Tanggal SPDP <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input id="documentDate" type="text" class="form-control" name="documentDate" value="{{ old('documentDate') }}" placeholder="YYYY-MM-DD" autocomplete="off" required>
                            </div>
                        </div>

                        {{-- Klasifikasi --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="documentClassification">Klasifikasi Surat <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control select2" name="documentClassification" id="documentClassification" required>
                                    <option value="">-- Pilih Klasifikasi --</option>
                                    @foreach ($documentClassifications as $dc)
                                        <option value="{{ $dc->id }}" {{ old('documentClassification') == $dc->id ? 'selected' : '' }}>{{ $dc->name }}</option>
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
                                            {{ old('suratPerintahPenyidikanDocument') == $sprindik->id ? 'selected' : '' }}>
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
                                        <option value="{{ $spt->id }}" {{ old('suratPerintahTugasDocument') == $spt->id ? 'selected' : '' }}>{{ $spt->document_number }}</option>
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
                                        <option value="{{ $p->id }}" {{ old('prosecutor') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
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
                                        <option value="{{ $c->id }}" {{ old('court') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
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
                            <label class="col-sm-3 col-form-label fw-bold" for="kode_wilayah">Kode Wilayah <span class="text-danger">*</span> <br><small class="text-muted">(format: XX.XX.XX)</small></label>
                            <div class="col-sm-9">
                                <input id="kode_wilayah" type="text" class="form-control" name="kode_wilayah" value="{{ old('kode_wilayah') }}" placeholder="Contoh: 31.74.09" required>
                            </div>
                        </div>

                        {{-- Lokasi Kejadian (Opsional) --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="lokasi_kejadian">Lokasi Kejadian <small class="text-muted">(opsional)</small></label>
                            <div class="col-sm-9">
                                <input id="lokasi_kejadian" type="text" class="form-control" name="lokasi_kejadian" value="{{ old('lokasi_kejadian', $accident->accident_location) }}" placeholder="Contoh: Jl. Kota Baru Indah Blok B7 No.30">
                            </div>
                        </div>

                        {{-- Waktu Kejadian (readonly) --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold">Waktu Kejadian <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="Sekitar pukul {{ \Carbon\Carbon::parse($accident->accident_time)->format('H:i') }} WIB" readonly>
                                <span class="text-muted small">Otomatis dari data perkara</span>
                            </div>
                        </div>

                        {{-- Tanggal Kejadian (readonly) --}}
                        <div class="form-group row mb-3">
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

                        {{-- Uraian Singkat Perkara (Opsional) --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="uraianSingkatPerkara">Uraian Singkat Perkara <small class="text-muted">(opsional)</small></label>
                            <div class="col-sm-9">
                                <textarea id="uraianSingkatPerkara" class="form-control" name="uraianSingkatPerkara" rows="3" placeholder="Deskripsikan singkat perkara...">{{ old('uraianSingkatPerkara') }}</textarea>
                            </div>
                        </div>

                        {{-- Sumber Dana (Opsional) --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="sumber_dana">Sumber Dana <small class="text-muted">(opsional)</small></label>
                            <div class="col-sm-9">
                                <input id="sumber_dana" type="text" class="form-control" name="sumber_dana" value="{{ old('sumber_dana') }}" placeholder="Opsional">
                            </div>
                        </div>

                        {{-- Sumber Informasi (Opsional) --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="sumber_informasi">Sumber Informasi <small class="text-muted">(opsional)</small></label>
                            <div class="col-sm-9">
                                <input id="sumber_informasi" type="text" class="form-control" name="sumber_informasi" value="{{ old('sumber_informasi') }}" placeholder="Opsional">
                            </div>
                        </div>

                        <hr>

                        {{-- Tersangka / Terlapor --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold">Ada Tersangka? <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input isSuspectExists" type="radio" id="suspectExists" name="isSuspectExists" value="true" checked>
                                    <label class="form-check-label" for="suspectExists">Ada Tersangka</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input isSuspectExists" type="radio" id="suspectNotExists" name="isSuspectExists" value="false">
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
                                            <option value="{{ $suspect->id }}">{{ $suspect->name }}</option>
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
                                            <option value="{{ $rp->id }}">{{ $rp->name }}</option>
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
                                    @if(old('carbonCopies'))
                                        @foreach(old('carbonCopies') as $index => $copy)
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
                                    @foreach ($authorizedSignatories as $officer)
                                        <option value="{{ $officer->id }}">{{ $officer->full_name ?? ($officer->first_name . ' ' . $officer->last_name) }} — {{ $officer->position->name ?? '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Lampiran --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="appendix">Jumlah Lampiran <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input id="appendix" type="number" class="form-control onlyIntegerInput" name="appendix" value="{{ old('appendix', 1) }}" min="1" max="999" required>
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

            function updateTembusan() {
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
                
                if (rows.length >= 1) {
                    $('#carbonCopiesContainer .carbon-copy-input').eq(0).val(firstVal).attr('readonly', true);
                }
                if (rows.length >= 2) {
                    $('#carbonCopiesContainer .carbon-copy-input').eq(1).val(secondVal).attr('readonly', true);
                }
            }
            $('#prosecutor, #court').on('change', updateTembusan);
            updateTembusan();

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

            // AJAX Validate & Submit
            $('#spdpPusiknasFormSubmit').on('click', function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('doc.spdp-pusiknas-document.api.validate-request-form', ['accident_id' => $accidentId]) }}",
                    type: 'POST',
                    dataType: 'json',
                    data: $('#spdpPusiknasForm').serialize(),
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil Validasi',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'Simpan'
                            }).then(function (result) {
                                if (result.isConfirmed) { $('#spdpPusiknasForm').submit(); }
                            });
                        }
                    },
                    error: function (xhr) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.code == '422') {
                            var errorMessages = '';
                            $.each(response.errors, function (key, value) { errorMessages += '- ' + value + '<br>'; });
                            Swal.fire({ icon: 'error', title: 'Periksa Isian', html: errorMessages });
                        }
                    }
                });
            });

        });
    </script>
@endpush
