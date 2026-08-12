@php
    $_title = 'Tahap II';
@endphp


@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('libs/bootstrap-duallistbox/bootstrap-duallistbox.css') }}" rel="stylesheet">
@endpush

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"><i
            class="bi bi-arrow-left"></i> Kembali ke Progres Perkara</a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Tambah Tahap II</h5>

            <div class="alert alert-danger" id="attentionBox">
                <div class="text-center">
                    <b>
                        PERHATIAN !<br />
                        <br />
                        DATA INI WAJIB DIISI DENGAN DETAIL DAN LENGKAP KARENA AKAN DIPERTUKARKAN DENGAN APARAT PENEGAK HUKUM
                        LAINNYA DALAM KERANGKA SISTEM PENANGANAN PERKARA TERPADU BERBASIS TEKNOLOGI INFORMASI (SPPT-TI).
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
            <form
                action="{{ route('doc.tahap-2-document.store', ['accident_id' => $accidentId]) }}"
                method="POST" enctype="multipart/form-data" id="tahap1Form">
                @csrf
                <input type="hidden" name="accidentId" id="accidentId" value="{{ $accidentId }}">

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="accidentNumber">Nomor LP</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="accidentNumber" type="text"
                            class="form-control @error('accidentNumber') is-invalid @enderror font-weight-bold"
                            name="accidentNumber" value="{{-- $accident->no_lp --}}" required placeholder="" readonly>
                        @error('accidentNumber')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="p21DocumentNumber">No P21<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex-align-self-center">
                        <select class="form-control select2" name="p21DocumentNumber" id="p21DocumentNumber">
                            <option value="">--Pilih No P21--</option>
                        </select>

                        @error('p21DocumentNumber')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="suspects">Nama Tersangka (Bisa Dipilih Lebih Dari Satu)<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <select class="form-control select2-multiple" name="suspects[]" id="suspects" multiple>
                            
                        </select>

                        @error('suspects')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="budget">Anggaran<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="budget" type="text"
                            class="form-control @error('budget') is-invalid @enderror font-weight-bold"
                            name="budget" value="{{ old('budget') }}" required
                            placeholder="Masukkan Anggaran">

                        @error('budget')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Yang Menandatangani<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex-align-self-center">
                        <select class="form-control select2" name="signatory" id="signatory">
                            <option value="">--Pilih Yang Menandatangani--</option>

                        </select>
                        <small class="text-muted">(*Apabila daftar yang menandatangani kosong silahkan hubungi Helpdesk untuk
                            mendapat bantuan)</small>

                        @error('signatory')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="carbonCopies">Tembusan</label>
                    <div class="col-lg-10 col-md-10 col-12 d-flex align-self-center">
                        <div id="carbonCopiesContainer">
                        </div>

                        <button class="btn btn-primary mb-2 addCarbonCopiesButton" type="button">Tambah</button>

                        @error('carbonCopies')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>

                <h5 class="fw-bold text-blue-dark">Tersangka</h5>

                <div class="row col-12 my-3 ms-0">
                    
                </div>

                <hr>

		        @include('docs.components.form.checkbox.is-legacy')

                <div class="text-center">
                    <button type="submit" class="btn btn-dark-blue"
                        id="tahap1FormSubmit">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js" defer></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" defer></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js" defer></script>
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script src="{{ asset('libs/bootstrap-duallistbox/jquery.bootstrap-duallistbox.js') }}"></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

    @include('docs.components.form.checkbox.is-legacy-js')
    
    <script type="text/javascript">
        $(document).ready(function() {
            setInterval(function() {
                $('#attentionBox').toggleClass('alert-danger alert-warning');
            }, 1000);

            $('#documentDate').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: "true",
                endDate: new Date()
            });
            $('#documentDate').keydown(function(e) {
                e.preventDefault();
                return false;
            });
        });

        // Select2 with Bootstrap4 theme
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            $('.select2-multiple').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            /*$("#suspects").bootstrapDualListbox({
                nonSelectedListLabel: 'Tersangka Belum Dipilih',
                selectedListLabel: 'Tersangka Dipilih',
                preserveSelectionOnMove: 'moved',
                moveOnSelect: false,
            });

            $('#suspects').on('change', function() {
                // Dapatkan nilai-nilai yang dipilih
                var selectedValues = $(this).val();

                // Dapatkan semua opsi dalam elemen input
                var allOptions = $(this).find('option');

                // Hapus atribut "selected" dari semua opsi
                allOptions.removeAttr('selected');

                // Tandai kembali opsi yang dipilih
                selectedValues.forEach(function(value) {
                    allOptions.filter('[value="' + value + '"]').attr('selected', 'selected');
                });
            });*/
        });

        $(document).ready(function() {
            $('#court').on('change', function() {
                var courtId = $(this).find(':selected').val();
                var courtName = $(this).find(':selected').text();
                var modifiedCourtName = courtName.toLowerCase().replace(/\b\w/g, function(match) {
                    return match.toUpperCase();
                });

                if (courtId) {
                    //check is #carbonCopyCourt exist
                    var isCarbonCopyCourtExist = $('#carbonCopyCourt').length;

                    if (isCarbonCopyCourtExist) {
                        $('#carbonCopyCourt').val('Ketua ' + modifiedCourtName);
                    } else {
                        var inputGroup = '<div class="input-group mb-2">' +
                            '<input type="text" class="form-control" id="carbonCopyCourt" name="carbonCopies[]" value="Ketua ' +
                            modifiedCourtName + '">' +
                            '<div class="input-group-append">' +
                            '</div>' +
                            '</div>';

                        $("#carbonCopiesContainer").append(inputGroup);
                    }
                } else {
                    $('#carbonCopyCourt').closest(".input-group").remove();
                }
            });

            $(".addCarbonCopiesButton").click(function() {
                var inputGroup = '<div class="input-group mb-2">' +
                    '<input type="text" class="form-control" name="carbonCopies[]" value="">' +
                    '<div class="input-group-append">' +
                    '<button class="btn btn-outline-danger removeCarbonCopiesButton" type="button">Hapus</button>' +
                    '</div>' +
                    '</div>';

                $("#carbonCopiesContainer").append(inputGroup);
            });

            // Menghapus input ketika tombol "Remove" di klik
            $(document).on("click", ".removeCarbonCopiesButton", function() {
                $(this).closest(".input-group").remove();
            });
        });

        $(document).ready(function() {
            $(document).on('change', '.isSuspectExists', function() {
                var isSuspectExists = $(this).val();

                if (isSuspectExists == 'true') {
                    $('#suspectExistsSection').show();
                    $('#suspectNotExistsSection').hide();
                } else if (isSuspectExists == 'false') {
                    $('#suspectExistsSection').hide();
                    $('#suspectNotExistsSection').show();
                }
            });

            $('.onlyIntegerInput').on('keypress', function(event) {
                var charCode = (event.which) ? event.which : event.keyCode;

                // Allow only numeric input (disallow decimal point)
                if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                    event.preventDefault();
                }
            });

            $('#suratPerintahPenyidikanDocument').on('change', function() {
                var documentDate = $(this).find(':selected').data('document-date');
                $('#suratPerintahPenyidikanDocumentDate').val(documentDate);
            });   
        });

        // Validasi Submit Form
        $(document).ready(function() {
            $('#tahap1FormSubmit').on('click', function(e) {
                e.preventDefault();

                // Lakukan validasi di sisi server menggunakan Ajax
                $.ajax({
                    url: "{{ route('doc.tahap-2-document.api.validate-request-form', ['accident_id' => $accidentId]) }}",
                    type: 'POST',
                    dataType: 'json',
                    data: $('#tahap1Form').serialize(),
                    success: function(response) {
                        // Cek jika validasi berhasil di sisi server
                        if (response.success) {
                            // sweetalert2 berhasil sebelum submit form
                            Swal.fire({
                                title: 'Berhasil',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                // Submit form
                                $('#tahap1Form')
                                .submit();
                            });
                        }
                    },
                    error: function(xhr) {
                        // Tangani error jika terjadi kesalahan saat melakukan validasi
                        response = JSON.parse(xhr.responseText);

                        if (response.code == '422') {
                            var errorMessages = '';

                            $.each(response.errors, function(key, value) {
                                errorMessages += '- ' + value + '<br>';
                            });

                            return Swal.fire({
                                icon: 'error',
                                title: 'Mohon Periksa Kembali Isian Anda',
                                html: errorMessages,
                            });
                        }
                    }
                });
            });
        });
    </script>
@endpush
