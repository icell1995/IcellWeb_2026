@php
    $_title = 'Daftar Tersangka';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
@endpush

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"><i
            class="bi bi-arrow-left"></i>
        Kembali ke Progres Perkara</a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Tambah Daftar Tersangka</h5>

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

        <form action="{{ route('doc.daftar-tersangka-document.store', ['accident_id' => $accidentId]) }}"
            method="POST" enctype="multipart/form-data" id="daftarTersangkaForm">
            @csrf
            <input type="hidden" name="accidentId" id="accidentId" value="{{ $accidentId }}">

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="accidentNumber">Nomor LP</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex-align-self-center">
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
                <label class="fw-bold col-sm-2 col-form-label" for="suratPemberitahuanDimulainyaPenyidikanDocumentNumber">No SPDP<span class="text-danger fs-5">*</span></label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex-align-self-center">
                    <select class="form-control select2" name="suratPemberitahuanDimulainyaPenyidikanDocumentNumber" id="suratPemberitahuanDimulainyaPenyidikanDocumentNumber">
                        <option value="">--Pilih No SPDP--</option>
                    </select>

                    @error('suratPemberitahuanDimulainyaPenyidikanDocumentNumber')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label">Penandatanganan Surat<span class="text-danger fs-5">*</span></label>
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
                <label class="fw-bold col-sm-2 col-form-label">Daftar Tersangka<span class="text-danger fs-5">*</span></label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex-align-self-center">
                    <div class="input-group">
                        <select class="custom-select form-control select2-input-group" name="suspect" id="suspect">
                            <option value="">--Pilih Tersangka--</option>
                        </select>
                        <button class="btn btn-primary" type="button"
                            id="suspectOptionAddButtton"><i class="bi bi-plus-circle"></i>
                            Tambah</button>
                    </div>
                    
                    <small class="text-muted">(*Apabila daftar tersangka kosong silahkan hubungi Helpdesk untuk
                        mendapat bantuan)</small>

                    @error('suspect')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <hr>

            <h5 class="fw-bold text-blue-dark">Tersangka</h5>

            <div class="row col-12 my-3 ms-0">
                <div id="suspect">
                    <div class="input-group mt-3">
                        <table class="table table-bordered table-responsive-md" id="suspectTable">
                            <thead class="table-danger">
                                <tr class="text-center">
                                    <th scope="col">JENIS / NOMOR IDENTITAS</th>
                                    <th scope="col">NAMA</th>
                                    <th scope="col">TEMPAT / TANGGAL LAHIR</th>
                                    <th scope="col">KATEGORI PELAKU</th>
                                    <th scope="col">PERAN PELAKU</th>
                                    <th scope="col">FOTO PELAKU</th>
                                    <th scope="col">Opsi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                        @error('suspect')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            <hr>

		    @include('docs.components.form.checkbox.is-legacy')

            <hr>

            <div class="text-center">
                <button type="submit" class="btn btn-dark-blue" id="daftarTersangkaFormSubmit">
                    <i class="bi bi-save"></i> {{ __('Simpan') }}
                </button>
                <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"
                    class="btn btn-danger">
                    <i class="bi bi-x-circle"></i> {{ __('Batal') }}
                </a>
            </div>
        </form>
    </div>
@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js" defer></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" defer></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js" defer></script>
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

    @include('docs.components.form.checkbox.is-legacy-js')
    
    <script type="text/javascript">
        $(document).ready(function() {
            setInterval(function() {
                $('#attentionBox').toggleClass('alert-danger alert-warning');
            }, 1000);

            $("#isMovedOfficers").change(function() {
                if ($(this).is(":checked")) {
                    $("#movedOfficers").show();
                } else {
                    $("#movedOfficers").hide();
                }
            });

            $("#isExternalOfficers").change(function() {
                if ($(this).is(":checked")) {
                    $("#externalOfficers").show();
                } else {
                    $("#externalOfficers").hide();
                }
            });

            $("#isRenewalDocument").change(function() {
                if ($(this).is(":checked")) {
                    $("#referenceOfRenewalDocument").show();
                } else {
                    $("#referenceOfRenewalDocument").hide();
                }
            });

            $('#documentDate').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: "true",
                orientation: 'auto bottom',
                endDate: new Date()
            });
            $('#documentDate').keydown(function(e) {
                e.preventDefault();
                return false;
            });

            $('#startDate').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: "true",
                orientation: 'auto bottom',
            }).on('changeDate', function(selected) {
                var startDate = new Date(selected.date.valueOf());
                $('#endDate').datepicker('setStartDate', startDate);
            });
            $('#startDate').keydown(function(e) {
                e.preventDefault();
                return false;
            })

            $('#endDate').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: "true",
                orientation: 'auto bottom',
            }).on('changeDate', function(selected) {
                var endDate = new Date(selected.date.valueOf());
                $('#startDate').datepicker('setEndDate', endDate);
            });
            $('#endDate').keydown(function(e) {
                e.preventDefault();
                return false;
            });

            $('#isFinished').on('change', function() {
                if (this.checked) {
                    $('#endDate').prop('disabled', true);
                    $('#endDate').val('');
                } else {
                    $('#endDate').prop('disabled', false);
                }
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
            $('.select2-input-group').select2({
                theme: 'bootstrap4'
            });

            $('#addManualMovedOfficerModal #rank').select2({
                dropdownParent: $('#rank').parent(),
                theme: 'bootstrap4',
                width: '100%'
            });
            $('#addManualMovedOfficerModal #position').select2({
                dropdownParent: $('#position').parent(),
                theme: 'bootstrap4',
                width: '100%'
            });
        });

        // Internal Officer
        $(document).ready(function() {
            $('#officerInternalMemberOption').prop('disabled', true);
            $('#officerLeader').on('change', function() {
                var selectedLeader = $(this).find('option:selected').data('register-number');
                var registerNumber = $(this).find('option:selected').data('register-number');

                // Cek apakah opsi sudah terappend dalam tabel
                var tablesToCheck = [{
                        tableSelector: '#movedOfficerMemberTable',
                        errorMessage: 'Sudah ada dalam daftar personil yang telah pindah, hapus terlebih dahulu untuk memilih sebagai ketua'
                    },
                    {
                        tableSelector: '#internalOfficerMemberTable',
                        errorMessage: 'Sudah ada dalam daftar personil, hapus terlebih dahulu untuk memilih sebagai ketua'
                    },
                    {
                        tableSelector: '#externalOfficerMemberTable',
                        errorMessage: 'Sudah ada dalam daftar personil luar, hapus terlebih dahulu untuk memilih sebagai ketua'
                    }
                ];

                // Cek apakah opsi sudah terappend dalam tabel
                var isAppended = false;
                tablesToCheck.forEach(function(table) {
                    $(table.tableSelector).find('tbody tr').each(function() {
                        var appendedRegisterNumber = $(this).find('.registerNumber').text();

                        if (appendedRegisterNumber == registerNumber) {
                            isAppended = true;
                            Swal.fire({
                                title: 'Gagal',
                                text: table.errorMessage,
                                icon: 'error',
                                confirmButtonText: 'Ok'
                            });
                            return false; // Keluar dari perulangan
                        }
                    });
                });

            });

            $('#officerInternalMemberOptionAddButtton').on('click', function() {
                var selectedOption = $('#officerInternalMemberOption').find('option:selected');

                if (selectedOption.val() == '') {
                    return Swal.fire({
                        title: 'Gagal',
                        text: 'Pilih Ketua Tim Terlebih Dahulu',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }

                // Ambil data yang diperlukan dari selectedOption
                var registerNumber = selectedOption.data('register-number');
                var rankName = selectedOption.data('rank-name');
                var name = selectedOption.data('name');
                var positionName = selectedOption.data('position-name');
                var policeName = selectedOption.data('police-name');

                // Cek apakah opsi sudah terappend dalam tabel
                var isAppended = false;
                $('#internalOfficerMemberTable tbody tr').each(function() {
                    var appendedRegisterNumber = $(this).find('.registerNumber').text();

                    if (appendedRegisterNumber == registerNumber) {
                        isAppended = true;
                        return Swal.fire({
                            title: 'Gagal',
                            text: 'Personil sudah ada dalam daftar',
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                    }
                });

                if (!isAppended) {
                    // Buat baris baru untuk ditambahkan ke dalam tabel
                    var newRow = $('<tr class="text-center"></tr>');

                    // Tambahkan kolom-kolom dengan nilai yang diambil dari selectedOption
                    newRow.append('<td>' + name + '</td>');
                    newRow.append('<td>' + rankName + '</td>');
                    newRow.append('<td class="registerNumber">' + registerNumber + '</td>');
                    newRow.append('<td>' + positionName + '</td>');
                    newRow.append('<td>' + policeName + '</td>');
                    newRow.append('<td><input type="hidden" name="internalOfficers[]" value="' +
                        registerNumber +
                        '"><button class="btn btn-danger btn-sm deleteInternalOfficer" type="button"><i class="bi bi-trash"></i></button></td>'
                    );

                    // Tambahkan baris ke dalam tabel
                    $('#internalOfficerMemberTable tbody').append(newRow);

                    // Hapus event listener deleteInternalOfficer sebelumnya
                    $(document).off('click', '.deleteInternalOfficer');

                    // Tambahkan event listener deleteInternalOfficer yang baru
                    $(document).on('click', '.deleteInternalOfficer', function() {
                        $(this).closest('tr').remove();
                    });
                }
            });
        });

        // Validasi Submit Form
        $(document).ready(function() {
            $('#daftarTersangkaFormSubmit').on('click', function(e) {
                e.preventDefault();

                // Lakukan validasi di sisi server menggunakan Ajax
                $.ajax({
                    url: "{{ route('doc.daftar-tersangka-document.api.validate-request-form', ['accident_id' => $accidentId]) }}",
                    type: 'POST',
                    dataType: 'json',
                    data: $('#daftarTersangkaForm').serialize(),
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
                                $('#daftarTersangkaForm').submit();
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
