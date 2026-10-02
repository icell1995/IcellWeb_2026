@php
    $_title = 'Edit SP3 — Pusiknas Bareskrim (SPPT-TI)';
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
                Edit Surat Pemberitahuan Penghentian Penyidikan
                <span class="badge bg-info text-white ms-2">SPPT-TI / Pusiknas Bareskrim</span>
            </h5>

            <div class="alert alert-danger" id="attentionBox">
                <div class="text-center">
                    <b>
                        PERHATIAN !<br /><br />
                        DATA INI AKAN DIPERTUKARKAN DENGAN PUSIKNAS BARESKRIM POLRI
                        DALAM KERANGKA SPPT-TI. KODE PROSES: <strong>DIK-40</strong>
                    </b>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
        </div>

        <div class="box-body">
            <form action="{{ route('doc.surat-pemberitahuan-penghentian-penyidikan-document.update', ['id' => $sp3->id, 'accident_id' => $accidentId]) }}"
                  method="POST" enctype="multipart/form-data" id="sp3PusiknasForm">
                @csrf
                <input type="hidden" name="accident_id" value="{{ $accidentId }}">
                <input type="hidden" name="noLp" value="{{ $accident->no_lp }}">

                <div class="row">
                    <div class="col-12">
                        
                        {{-- Nomor LP --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold">Nomor LP (dari perkara)</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control font-weight-bold" value="{{ $accident->no_lp }}" readonly>
                            </div>
                        </div>

                        {{-- Nomor SP3 --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="noSp3">Nomor SP3 <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input id="noSp3" type="text" class="form-control @error('noSp3') is-invalid @enderror" name="noSp3" value="{{ old('noSp3', isset($sp3) ? $sp3->document_number : '') }}" placeholder="Contoh: B/1073/XII/RES 1.25/2024/SAT RESKRIM" required>
                                @error('noSp3')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- Tanggal SP3 --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="tanggalSp3">Tanggal SP3 <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input id="tanggalSp3" type="text" class="form-control" name="tanggalSp3" value="{{ old('tanggalSp3', $sp3->document_date ? date('Y-m-d', strtotime($sp3->document_date)) : '') }}" placeholder="YYYY-MM-DD" autocomplete="off" required>
                            </div>
                        </div>

                        {{-- Klasifikasi Surat --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="klasifikasi">Klasifikasi Surat <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control select2" name="klasifikasi" id="klasifikasi" required>
                                    <option value="">-- Pilih Klasifikasi --</option>
                                    @foreach ($documentClassifications as $dc)
                                        <option value="{{ $dc->id }}" {{ old('klasifikasi', $sp3->document_classification_id ?? '') == $dc->id ? 'selected' : '' }}>{{ $dc->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Nomor SPDP Terkait --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="noSpdp">Nomor SPDP Terkait <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control select2" name="noSpdp" id="noSpdp" required>
                                    <option value="">-- Pilih Nomor SPDP --</option>
                                    @foreach ($spdpDocuments as $spdp)
                                        <option value="{{ $spdp->document_number }}" {{ old('noSpdp', $sp3->no_spdp ?? '') == $spdp->document_number ? 'selected' : '' }}>
                                            {{ $spdp->document_number }} ({{ $spdp->document_date ? date('d/m/Y', strtotime($spdp->document_date)) : '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr>

                        {{-- Nomor SK Penghentian --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="noSkPenghentian">Nomor SK Penghentian <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input id="noSkPenghentian" type="text" class="form-control" name="noSkPenghentian" value="{{ old('noSkPenghentian', $sp3->no_sk_penghentian ?? '') }}" placeholder="Surat Ketetapan Penghentian Nomor" required>
                            </div>
                        </div>

                        {{-- Tanggal SK Penghentian --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="tanggalSkPenghentian">Tanggal SK Penghentian <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input id="tanggalSkPenghentian" type="text" class="form-control" name="tanggalSkPenghentian" value="{{ old('tanggalSkPenghentian', $sp3->tanggal_sk_penghentian ? date('Y-m-d', strtotime($sp3->tanggal_sk_penghentian)) : '') }}" placeholder="YYYY-MM-DD" autocomplete="off" required>
                            </div>
                        </div>

                        {{-- Nomor SP Penghentian --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="noSpPenghentian">Nomor SP Penghentian <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input id="noSpPenghentian" type="text" class="form-control" name="noSpPenghentian" value="{{ old('noSpPenghentian', $sp3->no_sp_penghentian ?? '') }}" placeholder="Surat Perintah Penghentian Nomor" required>
                            </div>
                        </div>

                        {{-- Tanggal SP Penghentian --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="tanggalSpPenghentian">Tanggal SP Penghentian <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input id="tanggalSpPenghentian" type="text" class="form-control" name="tanggalSpPenghentian" value="{{ old('tanggalSpPenghentian', $sp3->tanggal_sp_penghentian ? date('Y-m-d', strtotime($sp3->tanggal_sp_penghentian)) : '') }}" placeholder="YYYY-MM-DD" autocomplete="off" required>
                            </div>
                        </div>

                        <hr>

                        {{-- Alasan Penghentian --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold">Alasan Penghentian Penyidikan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control select2" name="kode_alasan[]" id="kode_alasan" multiple="multiple" data-placeholder="Pilih satu atau lebih alasan penghentian penyidikan" required>
                                    @foreach ($masterAlasan as $kode => $label)
                                        <option value="{{ $kode }}" {{ in_array($kode, old('kode_alasan', $kodeAlasan)) ? 'selected' : '' }}>
                                            {{ $kode }}. {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr>

                        {{-- Tersangka --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="suspects">Tersangka yang Dihentikan <small class="text-muted">(opsional)</small></label>
                            <div class="col-sm-9">
                                <select class="form-control select2-multiple" name="suspects[]" id="suspects" multiple>
                                    @php
                                        $savedSuspectIds = json_decode($sp3->suspect_ids, true) ?? [];
                                    @endphp
                                    @foreach ($suspects as $suspect)
                                        <option value="{{ $suspect->id }}" {{ in_array($suspect->id, old('suspects', $savedSuspectIds)) ? 'selected' : '' }}>
                                            {{ $suspect->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Tembusan --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold">Tembusan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <div id="carbonCopiesContainer">
                                    @php
                                        $savedCarbonCopies = is_string($sp3->carbon_copies) ? json_decode($sp3->carbon_copies, true) : ($sp3->carbon_copies ?? []);
                                        $carbonCopies = old('carbonCopies', $savedCarbonCopies);
                                    @endphp
                                    @if(count($carbonCopies) > 0)
                                        @foreach($carbonCopies as $index => $copy)
                                            <div class="input-group mb-2 carbon-copy-row">
                                                <input type="text" name="carbonCopies[]" class="form-control carbon-copy-input" value="{{ $copy }}" required>
                                                @if($index >= 1)
                                                    <button type="button" class="btn btn-danger removeCarbonCopy"><i class="bi bi-trash"></i></button>
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="input-group mb-2 carbon-copy-row">
                                            <input type="text" name="carbonCopies[]" class="form-control carbon-copy-input" placeholder="Masukkan tembusan..." required>
                                        </div>
                                    @endif
                                </div>
                                <button class="btn btn-outline-primary btn-sm mt-1 addCarbonCopiesButton" type="button"><i class="bi bi-plus"></i> Tambah Tembusan Lainnya</button>
                                @error('carbonCopies')
                                    <span class="text-danger small d-block mt-1"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <hr>

                        {{-- Penandatangan --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="signatory">Penandatangan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control select2" name="signatory" id="signatory" required>
                                    <option value="">-- Pilih Penandatangan --</option>
                                    @foreach ($authorizedSignatories as $officer)
                                        <option value="{{ $officer->id }}" {{ old('signatory', $extraData['signatory_id'] ?? '') == $officer->id ? 'selected' : '' }}>
                                            {{ $officer->full_name ?? ($officer->first_name . ' ' . $officer->last_name) }} — {{ $officer->position->name ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Jumlah Lampiran --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="appendix">Jumlah Lampiran <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input id="appendix" type="number" class="form-control onlyIntegerInput" name="lampiran" value="{{ old('lampiran', $sp3->appendix ?? 1) }}" min="1" max="999" required>
                            </div>
                        </div>



                    </div>
                </div>

                {{-- Submit --}}
                <div class="d-flex justify-content-center gap-2 mt-3">
                    <button type="button" id="sp3PusiknasFormSubmit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan
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
            @if ($errors->has('noSp3'))
                $('html, body').animate({
                    scrollTop: $("#noSp3").offset().top - 100
                }, 500);
                $("#noSp3").focus();
            @endif

            // Attention blink
            setInterval(function () { $('#attentionBox').toggleClass('alert-danger alert-warning'); }, 1000);

            // Datepicker
            $('#tanggalSp3, #tanggalSkPenghentian, #tanggalSpPenghentian').datepicker({ format: 'yyyy-mm-dd', autoclose: true, endDate: new Date() });
            $('#tanggalSp3, #tanggalSkPenghentian, #tanggalSpPenghentian').keydown(function (e) { e.preventDefault(); return false; });

            // Add carbon copies dynamically
            $('.addCarbonCopiesButton').on('click', function () {
                var html = `
                    <div class="input-group mb-2 carbon-copy-row">
                        <input type="text" name="carbonCopies[]" class="form-control carbon-copy-input" placeholder="Masukkan tembusan..." required>
                        <button type="button" class="btn btn-danger removeCarbonCopy"><i class="bi bi-trash"></i></button>
                    </div>`;
                $('#carbonCopiesContainer').append(html);
            });

            $(document).on('click', '.removeCarbonCopy', function () {
                $(this).closest('.carbon-copy-row').remove();
            });

            // Select2
            $('.select2').select2({ theme: 'bootstrap4', width: '100%' });
            $('.select2-multiple').select2({ theme: 'bootstrap4', width: '100%' });

            // AJAX Validate & Submit
            $('#sp3PusiknasFormSubmit').on('click', function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('doc.surat-pemberitahuan-penghentian-penyidikan-document.api.validate-request-form', ['accident_id' => $accidentId]) }}",
                    type: 'POST',
                    dataType: 'json',
                    data: $('#sp3PusiknasForm').serialize(),
                    success: function (response) {
                        if (response.success) {
                            $('#sp3PusiknasForm')[0].submit();
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
