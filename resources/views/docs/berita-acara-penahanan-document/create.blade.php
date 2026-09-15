@php
    $_title = 'Berita Acara Penahanan (BA-HAN)';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://www.jqueryscript.net/demo/Time-Selection-Popover-jQuery-Timepicker/dist/css/timepicker.css">
    <style>
        .input-group > .select2-container--bootstrap4,
        .input-group > .select2-container {
            flex: 1 1 auto;
            width: 1% !important;
        }
        .input-group > .select2-container--bootstrap4 .select2-selection--single,
        .input-group > .select2-container .select2-selection--single {
            height: calc(2.25rem + 2px) !important;
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
            display: flex;
            align-items: center;
        }
        .input-group > .btn {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
            z-index: 2;
        }
    </style>
@endpush

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"><i
            class="bi bi-arrow-left"></i> Kembali ke Progres Perkara</a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Tambah Berita Acara Penahanan (BA-HAN)</h5>

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
            <form action="{{ route('doc.berita-acara-penahanan-document.store', ['accident_id' => $accidentId]) }}"
                method="POST" enctype="multipart/form-data" id="beritaAcaraPenahananForm" novalidate>
                @csrf
                <input type="hidden" name="accidentId" id="accidentId" value="{{ $accidentId }}">

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="accidentNumber">Nomor LP</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="accidentNumber" type="text"
                            class="form-control @error('accidentNumber') is-invalid @enderror font-weight-bold"
                            name="accidentNumber" value="{{ $accident->no_lp }}" required placeholder="" readonly>
                        @error('accidentNumber')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>


                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="suratPerintahPenahananDocument">No Surat Perintah Penahanan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2 @error('suratPerintahPenahananDocument') is-invalid @enderror"
                            name="suratPerintahPenahananDocument" id="suratPerintahPenahananDocument" required>
                            <option value="">--Pilih No Surat Perintah Penahanan--</option>
                            @foreach ($suratPerintahPenahananDocuments as $doc)
                                <option value="{{ $doc->id }}"
                                    data-document-number="{{ $doc->document_number }}"
                                    data-document-date="{{ $doc->document_date }}"
                                    data-suspect-id="{{ $doc->suspect_id ?? '' }}"
                                    data-detention-place="{{ $doc->detention_place ?? '' }}"
                                    data-detention-branch="{{ $doc->detention_branch ?? '' }}"
                                    data-start-date="{{ $doc->start_date ?? '' }}"
                                    data-end-date="{{ $doc->end_date ?? '' }}"
                                    {{ old('suratPerintahPenahananDocument') == $doc->id ? 'selected' : '' }}>
                                    {{ $doc->document_number }}
                                </option>
                            @endforeach
                        </select>

                        @error('suratPerintahPenahananDocument')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <input type="hidden" id="suratPerintahPenahananDate" name="suratPerintahPenahananDate"
                    value="{{ old('suratPerintahPenahananDate') }}">
                <input type="hidden" id="suratPerintahPenahananDocumentNumber" name="suratPerintahPenahananDocumentNumber"
                    value="{{ old('suratPerintahPenahananDocumentNumber') }}">


                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="time">Waktu Pelaksanaan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input class="form-control @error('time') is-invalid @enderror" id="time" name="time"
                            placeholder="hh:mm" autocomplete="off" value="{{ old('time') }}" required>

                        @error('time')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-12 col-12">
                        <select class="form-control select2 @error('timezone') is-invalid @enderror" name="timezone" id="timezone" required>
                            <option value="">--Pilih Zona Waktu--</option>
                            @foreach ($timezones as $timezone)
                                <option value="{{ $timezone->id }}" {{ old('timezone') == $timezone->id ? 'selected' : '' }}>{{ $timezone->name }}</option>
                            @endforeach
                        </select>

                        @error('timezone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="place">Tempat Pelaksanaan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input class="form-control @error('place') is-invalid @enderror" id="place" name="place"
                            placeholder="Nama Tempat Pelaksanaan" autocomplete="off"
                            value="{{ old('place') }}" required>

                        @error('place')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>

                <h5 class="fw-bold text-blue-dark">Tim Penyidik</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Penyidik Pembuat Berita Acara<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex-align-self-center">
                        <select class="form-control select2" name="officerLeader" id="officerLeader">
                            <option value="">--Pilih Penyidik Pembuat Berita Acara--</option>
                            @foreach ($internalOfficers as $data)
                                @php
                                    $positionName = $data->position->name ?? '';
                                @endphp
                                <option value="{{ $data->id }}" data-register-number="{{ $data->register_number }}">
                                    {{ $data->register_number . ' - ' . $data->full_name . ' | ' . $positionName }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">(*Apabila daftar penyidik kosong silahkan hubungi Helpdesk untuk mendapat bantuan)</small>

                        @error('officerLeader')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Jabatan Selaku<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <div class="d-flex mb-2 @error('investigatorRole') border border-danger p-2 rounded is-invalid @enderror" id="investigatorRoleWrapper">
                            <div class="form-check me-3">
                                <input class="form-check-input @error('investigatorRole') is-invalid @enderror" type="radio" id="rolePenyidik" name="investigatorRole"
                                    value="Penyidik" {{ old('investigatorRole') == 'Penyidik' ? 'checked' : '' }}>
                                <label for="rolePenyidik">
                                    Penyidik
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input @error('investigatorRole') is-invalid @enderror" type="radio" id="rolePenyidikPembantu" name="investigatorRole"
                                    value="Penyidik Pembantu" {{ old('investigatorRole') == 'Penyidik Pembantu' ? 'checked' : '' }}>
                                <label for="rolePenyidikPembantu">
                                    Penyidik Pembantu
                                </label>
                            </div>
                        </div>

                        @error('investigatorRole')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="row col-12 my-3 ms-0">
                    <label class="fw-bold">Penyidik<small class="fw-normal text-muted"> (*Pilihan Penyidik akan tampil setelah
                            Penyidik Pembuat Berita Acara dipilih)</small></label>

                    <div id="internalOfficer">
                        <div class="alert alert-primary my-3" role="alert">
                            Pilih personel lalu klik tombol 'Tambah' untuk menambahkan personel sebagai penyidik pendamping.
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select class="custom-select select2-input-group" id="officerInternalMemberOption"
                                        aria-describedby="officerInternalMemberOptionAddButtton">
                                        <option value="">--Pilih Penyidik--</option>
                                    </select>
                                    <button class="btn btn-primary" type="button"
                                        id="officerInternalMemberOptionAddButtton"><i class="bi bi-plus-circle"></i>
                                        Tambah</button>
                                </div>
                            </div>
                        </div>

                        <div class="input-group mt-3">
                            <table class="table table-bordered table-responsive-md" id="internalOfficerMemberTable">
                                <thead class="table-danger">
                                    <tr class="text-center">
                                        <th scope="col">Nama</th>
                                        <th scope="col">Pangkat</th>
                                        <th scope="col">NRP</th>
                                        <th scope="col">Jabatan</th>
                                        <th scope="col">Kesatuan</th>
                                        <th scope="col">Opsi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                            @error('personnel')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr>

                <h5 class="fw-bold text-blue-dark">Tersangka yang Ditahan</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="suspect">Tersangka yang Ditahan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2 @error('suspect') is-invalid @enderror" name="suspect" id="suspect" required>
                            <option value="">--Pilih Tersangka--</option>
                            @foreach ($suspects as $suspectItem)
                                <option value="{{ $suspectItem->id }}" {{ old('suspect') == $suspectItem->id ? 'selected' : '' }}>
                                    {{ $suspectItem->name }} (NIK : {{ $suspectItem->identity_number ?? '-' }})
                                </option>
                            @endforeach
                        </select>

                        @error('suspect')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>

                <h5 class="fw-bold text-blue-dark">Penempatan & Masa Penahanan</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="detentionPlace">Rumah Tahanan Negara<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="detentionPlace" type="text"
                            class="form-control @error('detentionPlace') is-invalid @enderror font-weight-bold"
                            name="detentionPlace" value="{{ old('detentionPlace', 'Rumah Tahanan Negara ' . ($police->name ?? '')) }}" required
                            placeholder="Contoh: Rumah Tahanan Negara Polres ..." readonly>

                        @error('detentionPlace')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="detentionBranch">Cabang (Satker)<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="detentionBranch" type="text"
                            class="form-control @error('detentionBranch') is-invalid @enderror font-weight-bold"
                            name="detentionBranch" value="{{ old('detentionBranch', $police->name ?? '') }}" required
                            placeholder="Contoh: Polres ..." readonly>

                        @error('detentionBranch')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Masa Penahanan Mulai<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input class="form-control" id="startDate" name="startDate" placeholder="YYYY-MM-DD"
                            autocomplete="off" value="{{ old('startDate', date('Y-m-d')) }}" readonly>

                        @error('startDate')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Masa Penahanan Selesai<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input class="form-control" id="endDate" name="endDate" placeholder="YYYY-MM-DD"
                            autocomplete="off" value="{{ old('endDate', date('Y-m-d', strtotime('+19 days'))) }}" readonly>

                        @error('endDate')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-sm-2">
                        <small class="text-muted d-block mt-2">Selama 20 (dua puluh) hari</small>
                    </div>
                </div>

                <hr>

                <h5 class="fw-bold text-blue-dark">Pelaksanaan & Keadaan Fisik Tersangka</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="task">Uraian Pelaksanaan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <textarea id="task" class="form-control @error('task') is-invalid @enderror font-weight-bold" name="task" rows="3"
                            placeholder="Adapun pelaksanaannya sebagai berikut: [...tambahkan untuk isi bagian ini...]" required>{{ old('task') }}</textarea>

                        @error('task')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="healthCondition">Keadaan Kesehatan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <textarea id="healthCondition" class="form-control @error('healthCondition') is-invalid @enderror font-weight-bold"
                            name="healthCondition" rows="3" required
                            placeholder="Keadaan kesehatan/fisik tersangka sebelum dimasukkan ke dalam ruang tahanan [...tambahkan untuk isi bagian ini...] dalam keadaan sehat.">{{ old('healthCondition') }}</textarea>

                        @error('healthCondition')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>

                <h5 class="fw-bold text-blue-dark">Penandatangan Dokumen</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="documentDate">Tanggal Ditandatangani Dokumen<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input class="form-control @error('documentDate') is-invalid @enderror" id="documentDate"
                            name="documentDate" placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('documentDate') }}" data-provide="datepicker"
                            data-date-format="yyyy-mm-dd" data-date-autoclose="true" required>

                        @error('documentDate')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>
                @if(strtotime($accident->report_date) < strtotime('2024-01-01') || (isset($accident->police) && $accident->police->is_whitelisted_document_legacy == true && strtotime($accident->police->start_date_whitelisted_document_legacy) <= strtotime($accident->report_date) && strtotime($accident->report_date) <= strtotime($accident->police->end_date_whitelisted_document_legacy)))
                    @include('docs.components.form.checkbox.is-legacy')
                @endif
                <hr>

                <div class="text-center">
                    <button type="submit" class="btn btn-dark-blue" id="beritaAcaraPenahananFormSubmit" name="submit_action" value="save">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://www.jqueryscript.net/demo/Time-Selection-Popover-jQuery-Timepicker/dist/js/timepicker.js"></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

@if(strtotime($accident->report_date) < strtotime('2024-01-01') || (isset($accident->police) && $accident->police->is_whitelisted_document_legacy == true && strtotime($accident->police->start_date_whitelisted_document_legacy) <= strtotime($accident->report_date) && strtotime($accident->report_date) <= strtotime($accident->police->end_date_whitelisted_document_legacy)))
    @include('docs.components.form.checkbox.is-legacy-js')
@endif

    <script>
        $(document).ready(function() {
            // Attention Box blinking effect (100% konsisten dengan Sprinlidik)
            setInterval(function() {
                $('#attentionBox').toggleClass('alert-danger alert-warning');
            }, 1000);

            // Select2 with Bootstrap4 theme
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
            $('.select2-input-group').select2({
                theme: 'bootstrap4'
            });

            // Inisialisasi Datepicker
            $('[data-provide="datepicker"]').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                orientation: 'auto bottom'
            });

            // Auto-fill data saat dokumen Surat Perintah Penahanan dipilih
            $('#suratPerintahPenahananDocument').on('change', function() {
                var selected = $(this).find(':selected');
                var docNumber = selected.data('document-number') || selected.text().trim();
                var docDate = selected.data('document-date');
                var suspectId = selected.data('suspect-id');
                var detentionPlace = selected.data('detention-place');
                var detentionBranch = selected.data('detention-branch');
                var startDate = selected.data('start-date');
                var endDate = selected.data('end-date');

                if (docNumber && docNumber !== '--Pilih No Surat Perintah Penahanan--') {
                    $('#suratPerintahPenahananDocumentNumber').val(docNumber);
                }

                if (docDate) {
                    $('#suratPerintahPenahananDate').val(docDate);
                } else {
                    $('#suratPerintahPenahananDate').val('');
                }

                if (detentionPlace) {
                    $('#detentionPlace').val(detentionPlace);
                }
                if (detentionBranch) {
                    $('#detentionBranch').val(detentionBranch);
                }
                if (startDate) {
                    $('#startDate').val(startDate);
                }
                if (endDate) {
                    $('#endDate').val(endDate);
                }
            });

            // Trigger auto-fill jika ada SP-HAN yang sudah terpilih saat page load
            if ($('#suratPerintahPenahananDocument').val()) {
                $('#suratPerintahPenahananDocument').trigger('change');
            }

            // Cegah input keyboard pada datepicker agar selalu melalui dialog kalender
            $('#suratPerintahPenahananDate, #documentDate, #startDate, #endDate, #birthDateFieldSuspect').keydown(function(e) {
                e.preventDefault();
                return false;
            });

            // Inisialisasi Timepicker yang 100% konsisten dengan LHGP
            $('#time').timepicker({
                showMeridian: false,
                minuteStep: 1,
                defaultTime: false
            }).on('changeTime.timepicker change input hide.timepicker', function() {
                var val = ($(this).val() || '').trim();
                if (val !== '') {
                    $(this).removeClass('is-invalid');
                    $(this).next('.frontend-error, .invalid-feedback').remove();
                    $(this).parent().find('.frontend-error, .invalid-feedback').remove();
                }
            });

            $('#time').keydown(function(e) {
                e.preventDefault();
                return false;
            });

            // ==========================================
            // INTERNAL OFFICER HANDLING
            // ==========================================
            $('#officerInternalMemberOption').prop('disabled', true);

            $('#officerLeader').on('change', function() {
                var selectedLeader = $(this).find('option:selected').data('register-number');
                var registerNumber = $(this).find('option:selected').data('register-number');

                // Cek apakah opsi sudah terappend dalam tabel
                var isAppended = false;
                $('#internalOfficerMemberTable tbody tr').each(function() {
                    var appendedRegisterNumber = $(this).find('.registerNumber').text();

                    if (appendedRegisterNumber == registerNumber) {
                        isAppended = true;
                        Swal.fire({
                            title: 'Gagal',
                            text: 'Sudah ada dalam daftar personil, hapus terlebih dahulu untuk memilih sebagai penyidik utama',
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                        return false;
                    }
                });

                if (!isAppended) {
                    if (selectedLeader && selectedLeader !== '') {
                        $.ajax({
                            url: "{{ route('doc.berita-acara-penahanan-document.api.internal-officers', ['accident_id' => $accidentId]) }}",
                            type: "GET",
                            dataType: "json",
                            data: {
                                selectedLeaderOfficerRegisterNumber: selectedLeader
                            },
                            success: function(response) {
                                $('#officerInternalMemberOption').empty();
                                $('#officerInternalMemberOption').append('<option value="">--Pilih Penyidik--</option>');

                                response.data.forEach(function(member) {
                                    var rankName = (member.rank) ? member.rank.name : '-';
                                    var positionName = (member.position) ? member.position.name : '-';
                                    var policeName = (member.police) ? member.police.full_name : '-';

                                    $('#officerInternalMemberOption').append(
                                        '<option value="' + member.id + '"' +
                                        ' data-register-number="' + member.register_number + '"' +
                                        ' data-rank-name="' + rankName + '"' +
                                        ' data-name="' + member.full_name + '"' +
                                        ' data-position-name="' + positionName + '"' +
                                        ' data-police-name="' + policeName + '"' +
                                        '>' +
                                        member.register_number + ' - ' + member.full_name + ' | ' + positionName +
                                        '</option>'
                                    );
                                });

                                $('#officerInternalMemberOption').prop('disabled', false);
                            },
                            error: function(xhr, status, error) {
                                Swal.fire({
                                    title: 'Gagal',
                                    text: 'Gagal memuat data penyidik internal',
                                    icon: 'error',
                                    confirmButtonText: 'Ok'
                                });
                            }
                        });
                    } else {
                        $('#officerInternalMemberOption').prop('disabled', true);
                        $('#officerInternalMemberOption').empty();
                    }
                }
            });

            $('#officerInternalMemberOptionAddButtton').on('click', function() {
                var selectedOption = $('#officerInternalMemberOption').find('option:selected');

                if (selectedOption.val() == '' || !selectedOption.val()) {
                    return Swal.fire({
                        title: 'Gagal',
                        text: 'Pilih Penyidik Terlebih Dahulu',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }

                var registerNumber = selectedOption.data('register-number');
                var rankName = selectedOption.data('rank-name');
                var name = selectedOption.data('name');
                var positionName = selectedOption.data('position-name');
                var policeName = selectedOption.data('police-name');

                var isAppended = false;
                $('#internalOfficerMemberTable tbody tr').each(function() {
                    var appendedRegisterNumber = $(this).find('.registerNumber').text();
                    if (appendedRegisterNumber == registerNumber) {
                        isAppended = true;
                        Swal.fire({
                            title: 'Gagal',
                            text: 'Personil sudah ada dalam daftar',
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                        return false;
                    }
                });

                if (!isAppended) {
                    var newRow = $('<tr class="text-center"></tr>');
                    newRow.append('<td>' + name + '</td>');
                    newRow.append('<td>' + rankName + '</td>');
                    newRow.append('<td class="registerNumber">' + registerNumber + '</td>');
                    newRow.append('<td>' + positionName + '</td>');
                    newRow.append('<td>' + policeName + '</td>');
                    newRow.append('<td><input type="hidden" name="internalOfficers[]" value="' + registerNumber + '"><button class="btn btn-danger btn-sm deleteInternalOfficer" type="button"><i class="bi bi-trash"></i></button></td>');

                    $('#internalOfficerMemberTable tbody').append(newRow);

                    $(document).off('click', '.deleteInternalOfficer');
                    $(document).on('click', '.deleteInternalOfficer', function() {
                        $(this).closest('tr').remove();
                    });
                }
            });

            function hasFieldValue($field) {
                if ($field.is(':disabled')) return true;
                if ($field.is('select')) {
                    var val = $field.val();
                    return val && val !== '' && val !== '0' && val !== null;
                }
                if ($field.is('input[type="radio"]')) {
                    var name = $field.attr('name');
                    return $('input[name="' + name + '"]:checked').length > 0;
                }
                var val = ($field.val() || '').trim();
                return val !== '';
            }

            // Bersihkan error saat input/select/textarea berubah (termasuk timepicker & datepicker)
            $(document).on('input change changeTime.timepicker hide.timepicker changeDate blur select2:select select2:unselect', 'input, select, textarea', function() {
                var $field = $(this);
                if (hasFieldValue($field)) {
                    $field.removeClass('is-invalid border-danger');
                    if ($field.next('.select2-container').length) {
                        $field.next('.select2-container').find('.select2-selection').removeClass('border border-danger is-invalid');
                    }
                    $field.next('.frontend-error, .invalid-feedback').remove();
                    $field.siblings('.frontend-error, .invalid-feedback').remove();
                    $field.parent().find('.frontend-error, .invalid-feedback').remove();
                }

                if ($('input[name="investigatorRole"]:checked').length > 0) {
                    $('#investigatorRoleWrapper').removeClass('border border-danger is-invalid');
                    $('#investigatorRoleWrapper').next('.frontend-error, .invalid-feedback').remove();
                }
            });

            // Continuous watcher untuk input yang diupdate oleh plugin popover
            setInterval(function() {
                $('input.is-invalid, textarea.is-invalid, select.is-invalid').each(function() {
                    var $field = $(this);
                    if (hasFieldValue($field)) {
                        $field.removeClass('is-invalid border-danger');
                        if ($field.next('.select2-container').length) {
                            $field.next('.select2-container').find('.select2-selection').removeClass('border border-danger is-invalid');
                        }
                        $field.next('.frontend-error, .invalid-feedback').remove();
                        $field.siblings('.frontend-error, .invalid-feedback').remove();
                        $field.parent().find('.frontend-error, .invalid-feedback').remove();
                    }
                });

                if ($('input[name="investigatorRole"]:checked').length > 0) {
                    $('#investigatorRoleWrapper').removeClass('border border-danger is-invalid');
                    $('#investigatorRoleWrapper').next('.frontend-error, .invalid-feedback').remove();
                }
            }, 200);

            // Validasi Submit Form (Konsisten dengan Sprinlidik)
            $(document).on('click', '#beritaAcaraPenahananFormSubmit', function(e) {
                e.preventDefault();

                // Bersihkan semua error sebelumnya
                $('.is-invalid').removeClass('is-invalid');
                $('.border.border-danger').removeClass('border border-danger');
                $('.select2-selection').removeClass('border border-danger is-invalid');
                $('.frontend-error').remove();

                var errors = [];

                function markError(fieldId, message) {
                    var $field = $(fieldId);
                    $field.addClass('is-invalid');
                    if ($field.next('.select2-container').length) {
                        $field.next('.select2-container').find('.select2-selection').addClass('border border-danger is-invalid');
                    }
                    var $target = $field.next('.select2-container').length ? $field.next('.select2-container') : $field;
                    if ($target.next('.frontend-error').length === 0) {
                        $target.after('<div class="invalid-feedback d-block frontend-error">' + message + '</div>');
                    }
                    errors.push(message);
                }

                function checkSelect(fieldId, label) {
                    var $field = $(fieldId);
                    var val = $field.val();
                    if (!val || val === '' || val === '0' || val === null) {
                        markError(fieldId, label + ' harus dipilih');
                    }
                }

                function checkInput(fieldId, label) {
                    var $field = $(fieldId);
                    var val = ($field.val() || '').trim();
                    if (!val || val === '') {
                        markError(fieldId, label + ' harus diisi');
                    }
                }

                // Cek field wajib
                checkSelect('#suratPerintahPenahananDocument', 'Surat Perintah Penahanan');
                checkInput('#time', 'Waktu Pelaksanaan');
                checkSelect('#timezone', 'Zona Waktu');
                checkInput('#place', 'Tempat Pelaksanaan');
                checkSelect('#officerLeader', 'Penyidik Pembuat Berita Acara');

                if ($('input[name="investigatorRole"]:checked').length === 0) {
                    $('#investigatorRoleWrapper').addClass('border border-danger p-2 rounded is-invalid');
                    if ($('#investigatorRoleWrapper').next('.frontend-error').length === 0) {
                        $('#investigatorRoleWrapper').after('<div class="invalid-feedback d-block frontend-error">Jabatan Selaku harus dipilih</div>');
                    }
                    errors.push('Jabatan Selaku harus dipilih');
                }

                checkSelect('#suspect', 'Tersangka yang Ditahan');
                checkInput('#detentionPlace', 'Rumah Tahanan Negara');
                checkInput('#detentionBranch', 'Cabang Rutan / Satker');
                checkInput('#startDate', 'Tanggal Mulai Penahanan');
                checkInput('#endDate', 'Tanggal Berakhir Penahanan');
                checkInput('#task', 'Uraian Pelaksanaan');
                checkInput('#healthCondition', 'Keadaan Kesehatan');
                checkInput('#documentDate', 'Tanggal Ditandatangani Dokumen');

                // Helper: Scroll ke field error pertama (100% identik dengan Sprinlidik)
                function scrollToFirstError() {
                    setTimeout(function() {
                        var $firstError = $('.is-invalid, .border-danger').first();
                        var $target = null;
                        if ($firstError && $firstError.length) {
                            if ($firstError.is(':visible')) {
                                $target = $firstError;
                            } else if ($firstError.next('.select2-container').is(':visible')) {
                                $target = $firstError.next('.select2-container');
                            } else if ($firstError.siblings('.select2-container').is(':visible')) {
                                $target = $firstError.siblings('.select2-container');
                            } else {
                                $target = $firstError.closest(':visible');
                            }
                        }
                        if (!$target || !$target.length || !$target.offset()) {
                            $target = $('.frontend-error:visible, .is-invalid:visible, .border-danger:visible').first();
                        }

                        if ($target && $target.length) {
                            if ($target[0] && typeof $target[0].scrollIntoView === 'function') {
                                $target[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                            if ($target.offset()) {
                                $('html, body, .content-wrapper, .wrapper').animate({
                                    scrollTop: Math.max(0, $target.offset().top - 120)
                                }, 400);
                            }
                        }
                    }, 100);
                }

                // Jika ada error di frontend, scroll ke field pertama
                if (errors.length > 0) {
                    scrollToFirstError();
                    return;
                }

                // Lakukan validasi di sisi server menggunakan Ajax (Sesuai Pola Sprinlidik)
                $.ajax({
                    url: "{{ route('doc.berita-acara-penahanan-document.api.validate-request-form', ['accident_id' => $accidentId]) }}",
                    type: 'POST',
                    dataType: 'json',
                    data: $('#beritaAcaraPenahananForm').serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                $('#beritaAcaraPenahananForm').submit();
                            });
                        }
                    },
                    error: function(xhr) {
                        var response = {};
                        try {
                            response = JSON.parse(xhr.responseText);
                        } catch (e) {
                            response = { message: 'Terjadi kesalahan tidak dikenal pada server.' };
                        }

                        var errorMessages = '';
                        if (response.errors) {
                            if (typeof response.errors === 'object') {
                                $.each(response.errors, function(key, value) {
                                    errorMessages += '- ' + value + '<br>';
                                    var $field = $('#' + key);
                                    $field.addClass('is-invalid');
                                    if ($field.next('.select2-container').length) {
                                        $field.next('.select2-container').find('.select2-selection').addClass('border border-danger is-invalid');
                                    }
                                });
                            } else {
                                errorMessages = response.errors;
                            }
                        } else {
                            errorMessages = response.message || 'Terjadi kesalahan sistem.';
                        }

                        return Swal.fire({
                            icon: 'error',
                            title: 'Mohon Periksa Kembali Isian Anda',
                            html: errorMessages,
                        }).then(function() {
                            scrollToFirstError();
                        });
                    }
                });
            });

            var $initialError = $('.is-invalid, .border-danger').first();
            if ($initialError.length) {
                setTimeout(function() {
                    var $target = ($initialError.is(':visible')) ? $initialError : ($initialError.next('.select2-container').is(':visible') ? $initialError.next('.select2-container') : $initialError.closest(':visible'));
                    if ($target && $target.length) {
                        if ($target[0] && typeof $target[0].scrollIntoView === 'function') {
                            $target[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        if ($target.offset()) {
                            $('html, body, .content-wrapper, .wrapper').animate({
                                scrollTop: Math.max(0, $target.offset().top - 120)
                            }, 400);
                        }
                    }
                }, 300);
            }
        });
    </script>
@endpush
