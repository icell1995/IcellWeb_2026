@php
    $_title = 'SP3 — Pusiknas Bareskrim (SPPT-TI)';
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
                Tambah SP3 — Surat Pemberitahuan Penghentian Penyidikan
                <span class="badge badge-warning ms-2 text-dark">SPPT-TI / Pusiknas Bareskrim</span>
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
            <form action="{{ route('doc.sp3-pusiknas-document.store', ['accident_id' => $accidentId]) }}"
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
                                <input id="noSp3" type="text" class="form-control" name="noSp3" value="{{ old('noSp3') }}" placeholder="Contoh: B/1073/XII/RES 1.25/2024/SAT RESKRIM" required>
                            </div>
                        </div>

                        {{-- Tanggal SP3 --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="tanggalSp3">Tanggal SP3 <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input id="tanggalSp3" type="text" class="form-control" name="tanggalSp3" value="{{ old('tanggalSp3') }}" placeholder="YYYY-MM-DD" autocomplete="off" required>
                            </div>
                        </div>

                        {{-- Nomor SPDP Terkait --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="noSpdp">Nomor SPDP Terkait <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control select2" name="noSpdp" id="noSpdp" required>
                                    <option value="">-- Pilih Nomor SPDP --</option>
                                    @foreach ($spdpDocuments as $spdp)
                                        <option value="{{ $spdp->document_number }}" {{ old('noSpdp') == $spdp->document_number ? 'selected' : '' }}>
                                            {{ $spdp->document_number }} ({{ $spdp->document_date ? date('d/m/Y', strtotime($spdp->document_date)) : '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr>

                        {{-- Alasan Penghentian --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold">Alasan Penghentian Penyidikan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <p class="text-muted small mb-2">Pilih satu atau lebih alasan penghentian penyidikan:</p>
                                <div class="row">
                                    @foreach ($masterAlasan as $kode => $label)
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="kode_alasan[]" id="alasan_{{ $kode }}" value="{{ $kode }}" {{ in_array($kode, old('kode_alasan', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="alasan_{{ $kode }}">
                                                    <strong>{{ $kode }}.</strong> {{ $label }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- Tersangka --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="suspects">Tersangka yang Dihentikan <small class="text-muted">(opsional)</small></label>
                            <div class="col-sm-9">
                                <select class="form-control select2-multiple" name="suspects[]" id="suspects" multiple>
                                    @foreach ($suspects as $suspect)
                                        <option value="{{ $suspect->id }}">{{ $suspect->name }}</option>
                                    @endforeach
                                </select>
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
                                        <option value="{{ $officer->id }}" {{ old('signatory') == $officer->id ? 'selected' : '' }}>
                                            {{ $officer->full_name ?? ($officer->first_name . ' ' . $officer->last_name) }} — {{ $officer->position->name ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr>

                        {{-- Lampiran Digital (PDF) --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="dokumen_digital">File SP3 (PDF) <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="file" id="dokumen_digital" name="dokumen_digital" class="form-control" accept="application/pdf" required>
                                <small class="text-muted">Upload dokumen SP3 hasil scan/digital dalam format PDF.</small>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Submit --}}
                <div class="d-flex gap-2 mt-3">
                    <button type="button" id="sp3PusiknasFormSubmit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan SP3
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
            // Attention blink
            setInterval(function () { $('#attentionBox').toggleClass('alert-danger alert-warning'); }, 1000);

            // Datepicker
            $('#tanggalSp3').datepicker({ format: 'yyyy-mm-dd', autoclose: true, endDate: new Date() });
            $('#tanggalSp3').keydown(function (e) { e.preventDefault(); return false; });

            // Select2
            $('.select2').select2({ theme: 'bootstrap4', width: '100%' });
            $('.select2-multiple').select2({ theme: 'bootstrap4', width: '100%' });

            // AJAX Validate & Submit
            $('#sp3PusiknasFormSubmit').on('click', function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('doc.sp3-pusiknas-document.api.validate-request-form', ['accident_id' => $accidentId]) }}",
                    type: 'POST',
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    data: new FormData($('#sp3PusiknasForm')[0]),
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil Validasi',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'Simpan'
                            }).then(function (result) {
                                if (result.isConfirmed) { $('#sp3PusiknasForm').submit(); }
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
