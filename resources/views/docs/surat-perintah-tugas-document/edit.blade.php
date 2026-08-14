@php
    $_title = 'Edit Surat Perintah Tugas';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
@endpush

@section('content')

<a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"><i
            class="bi bi-arrow-left"></i> Kembali ke Progress Perkara</a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Edit Surat Perintah Tugas (SPRINGAS)</h5>

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
            <form action="{{ route('doc.surat-perintah-tugas-document.update', ['accident_id' => $accidentId, 'id' => $suratPerintahTugasDocumentId]) }}"
                method="POST" enctype="multipart/form-data" id="suratPerintahTugasForm">
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
                    <label class="fw-bold col-sm-2 col-form-label" for="documentNumber">Nomor Dokumen Springas<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="documentNumber" type="text"
                            class="form-control @error('documentNumber') is-invalid @enderror font-weight-bold"
                            name="documentNumber" value="{{ $suratPerintahTugasDocument->document_number }}" required
                            placeholder="Masukkan Nomor Dokumen Springas">

                        @error('documentNumber')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="relatedDocument">No Sprinlidik/Sprindik<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input type="hidden" class="form-control" id="relatedDocumentCode" name="relatedDocumentCode" value="{{($suratPerintahTugasDocument->related_type == 'App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument') ? 'SPRINSIDIK' : 'SPRINLIDIK'}}">
                        <select class="form-control select2" name="relatedDocument" id="relatedDocument">
                            <option value="">--Pilih No Surat Perintah Penyelidikan/Penyidikan--</option>
                            @foreach ($suratPerintahPenyelidikanDocuments as $suratPerintahPenyelidikanDocument)
                                <option value="{{ $suratPerintahPenyelidikanDocument->id }}" data-code="SPRINLIDIK"
                                    @if($suratPerintahTugasDocument->related_id == $suratPerintahPenyelidikanDocument->id){{'selected'}}@endif>
                                    {{ 'SP Lidik : ' . $suratPerintahPenyelidikanDocument->document_number }}</option>
                            @endforeach
                            @foreach($suratPerintahPenyidikanDocuments as $suratPerintahPenyidikanDocument)
                                <option value="{{ $suratPerintahPenyidikanDocument->id }}" data-code="SPRINSIDIK"
                                    @if($suratPerintahTugasDocument->related_id == $suratPerintahPenyidikanDocument->id){{'selected'}}@endif>
                                    {{ 'SP Sidik : ' . $suratPerintahPenyidikanDocument->document_number }}</option>
                            @endforeach
                        </select>

                        @error('relatedDocument')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Tanggal Mulai Tugas<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input class="form-control" id="startDate" name="startDate" placeholder="YYYY-MM-DD"
                            autocomplete="off" value="{{ Carbon\Carbon::parse($suratPerintahTugasDocument->start_date)->format('Y-m-d') }}" data-provide="datepicker">

                        @error('startDate')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Tanggal Akhir Tugas<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input class="form-control" id="endDate" name="endDate" placeholder="YYYY-MM-DD"
                            autocomplete="off" value="@if($suratPerintahTugasDocument->end_date != NULL){{ Carbon\Carbon::parse($suratPerintahTugasDocument->end_date)->format('Y-m-d') }}@endif"
                            data-provide="datepicker" @if($suratPerintahTugasDocument->end_date == NULL){{'disabled'}}@endif>
                        @error('endDate')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-sm-2">
                        <div class="icheck-primary d-inline">
                            <input type="checkbox" id="isFinished" name="isFinished" value="true" aria-label="..." @if($suratPerintahTugasDocument->end_date == NULL){{'checked'}}@endif>
                            <label for="isFinished">
                                <b>Sampai dengan selesai</b>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Tanggal Ditandatangani Dokumen<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input class="form-control" id="documentDate" name="documentDate" placeholder="YYYY-MM-DD"
                            autocomplete="off" value="{{ Carbon\Carbon::parse($suratPerintahTugasDocument->document_date)->format('Y-m-d') }}" data-provide="datepicker">

                        @error('documentDate')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Yang Menandatangani<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2" name="signatory" id="signatory">
                            <option value="">--Pilih Yang Menandatangani--</option>
                            @foreach ($authorizedSignatories as $data)
                                @php
                                    $positionName = $data->position->name ?? '';
                                @endphp
                                <option value="{{$data->id}}" data-register-number="{{$data->register_number}}" 
                                    @if($officers->where('class', 'SIGNATORY')->where('register_number', $data->register_number)->count() != 0){{'selected'}}@endif>{{$data->register_number . ' - ' . $data->full_name . ' | ' . $positionName}}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">(*Apabila daftar yang menandatangani kosong silahkan hubungi Helpdesk
                            untuk
                            mendapat bantuan)</small>

                        @error('signatory')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="task">Tugas<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <textarea id="task" class="form-control noEnterTextArea @error('task') is-invalid @enderror font-weight-bold"
                            name="task" rows="5">{{ $suratPerintahTugasDocument->task_description }}</textarea>

                        @error('task')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>


                <h5 class="fw-bold text-blue-dark">Penyidik Yang Ditugaskan</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Ketua Tim Penyidik<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2" name="officerLeader" id="officerLeader" disabled>
                            <option value="">--Ketua Penyidik--</option>
                            @foreach ($leaderOfficers as $data)
                                @php
                                    $positionName = $data->position->name ?? '';
                                @endphp
                                <option value="{{$data->id}}" data-register-number="{{$data->register_number}}">
                                    {{$data->id . ' - ' . $data->full_name . ' | ' . $positionName}}
                                </option>
                            @endforeach
                        </select>

                        <div style="display: none">
                            <input type="hidden" class="form-control" id="officerLeaderRegisterNumber"
                                name="officerLeaderRegisterNumber">

                            <input type="hidden" class="form-control" id="officerLeaderFirstName"
                                name="officerLeaderFirstName">
                            <input type="hidden" class="form-control" id="officerLeaderLastName"
                                name="officerLeaderLastName">

                            <input type="hidden" class="form-control" id="officerLeaderPhone"
                                name="officerLeaderPhone">

                            <input type="hidden" class="form-control" id="officerLeaderRankId"
                                name="officerLeaderRankId">
                            <input type="hidden" class="form-control" id="officerLeaderRankName"
                                name="officerLeaderRankName">

                            <input type="hidden" class="form-control" id="officerLeaderPositionId"
                                name="officerLeaderPositionId">
                            <input type="hidden" class="form-control" id="officerLeaderPositionName"
                                name="officerLeaderPositionName">

                            <input type="hidden" class="form-control" id="officerLeaderRegionalPoliceId"
                                name="officerLeaderRegionalPoliceId">
                            <input type="hidden" class="form-control" id="officerLeaderRegionalPoliceName"
                                name="officerLeaderRegionalPoliceName">
                            <input type="hidden" class="form-control" id="officerLeaderResortPoliceId"
                                name="officerLeaderResortPoliceId">
                            <input type="hidden" class="form-control" id="officerLeaderResortPoliceName"
                                name="officerLeaderResortPoliceName">
                        </div>
                        <small class="text-muted">(*Apabila terdapat kendala silahkan hubungi Helpdesk untuk mendapat
                            bantuan)</small>

                        @error('officerLeader')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="mb-2 mt-2">
                            <label class="fw-bold">Penyidik<small> (*Pilihan Penyidik akan tampil setelah Ketua Tim
                                    Penyidik dipilih)</small></label>

                            <div id="officer">
                                <div class="alert alert-primary mt-3 mb-3" role="alert">
                                    Pilih personel dari dropdown lalu klik tombol 'Tambah' untuk menambahkan personel sebagai penyidik.
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <select class="custom-select select2-input-group" id="officerMemberOption"
                                                aria-describedby="officerMemberOptionAddButtton" disabled>
                                                <option value="">--Pilih Penyidik--</option>
                                            </select>
                                            <button class="btn btn-primary" id="officerMemberOptionAddButtton"
                                                type="button"><i class="bi bi-plus-circle"></i> Tambah</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="input-group mt-3">
                                    <table class="table table-bordered table-responsive-md" id="officerMemberTable">
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
                                        <tbody>

                                        </tbody>
                                    </table>

                                    @error('personnel')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
		
		@if(strtotime($accident->report_date) < strtotime('2024-01-01') || $suratPerintahTugasDocument->isLegacy == true || $accident->police->is_whitelisted_document_legacy == true && strtotime($accident->police->start_date_whitelisted_document_legacy) <= strtotime($accident->report_date) && strtotime($accident->report_date) <= strtotime($accident->police->end_date_whitelisted_document_legacy))
		        @include('docs.components.form.checkbox.is-legacy', ['document' => $suratPerintahTugasDocument])
		@endif

                <div class="text-center">
                    <button type="submit" class="btn btn-dark-blue" id="suratPerintahTugasFormSubmit">
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
<script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

@php
    $memberOfficers = $officers->where('class','MEMBER')->all();
    $leaderOfficer = $officers->where('class','LEADER')->first();
@endphp

@if(strtotime($accident->report_date) < strtotime('2024-01-01') || $suratPerintahTugasDocument->isLegacy == true || $accident->police->is_whitelisted_document_legacy == true && strtotime($accident->police->start_date_whitelisted_document_legacy) <= strtotime($accident->report_date) && strtotime($accident->report_date) <= strtotime($accident->police->end_date_whitelisted_document_legacy))
	@include('docs.components.form.checkbox.is-legacy-js')
@endif

<script type="text/javascript">
    // Retreive Old Data
    $(document).ready(function() {
        var officers = @json($memberOfficers);
        var leader = @json($leaderOfficer);

        //leader
        $(function() {
            // Clear existing options
            $('#officerLeader').empty();

            // Populate options based on response data
            var leaderOfficer = leader;
            var leaderOfficerRegisterNumber = leaderOfficer.register_number || '';
            var leaderOfficerFirstName = leaderOfficer.first_name || '';
            var leaderOfficerLastName = leaderOfficer.last_name || '';
            var leaderOfficerPhone = leaderOfficer.phone_number || '';
            var leaderOfficerRank = leaderOfficer.rank;
            var leaderOfficerRankId = leaderOfficerRank.id || '';
            var leaderOfficerRankName = leaderOfficerRank.name || '';
            var leaderOfficerPosition = leaderOfficer.position;
            var leaderOfficerPositionId = leaderOfficerPosition.id || '';
            var leaderOfficerPositionName = leaderOfficerPosition.name || '';

            var leaderOfficerPolice = leaderOfficer.police ?? null;

            var leaderOfficerResortPolice = null;
            var leaderOfficerResortPoliceId = null;
            var leaderOfficerResortPoliceName = null;
            var leaderOfficerRegionalPolice = null;
            var leaderOfficerRegionalPoliceId = null;
            var leaderOfficerRegionalPoliceName = null;

            var leaderOfficerPoliceName = null;

            if(leaderOfficerPolice){
                if(leaderOfficerPolice.class == 'RESOR'){
                    var leaderOfficerResortPolice = leaderOfficerPolice;
                    var leaderOfficerResortPoliceId = leaderOfficerResortPolice.id;
                    var leaderOfficerResortPoliceName = leaderOfficerResortPolice.full_name;

                    var leaderOfficerRegionalPolice = leaderOfficerPolice.parent;
                    var leaderOfficerRegionalPoliceId = leaderOfficerRegionalPolice.id;
                    var leaderOfficerRegionalPoliceName = leaderOfficerRegionalPolice.full_name;

                    var leaderOfficerPoliceName = leaderOfficerResortPoliceName + ' - ' + leaderOfficerRegionalPoliceName;
                }else if(leaderOfficerPolice.class == 'DAERAH'){
                    var leaderOfficerResortPolice = '';
                    var leaderOfficerResortPoliceId = '';
                    var leaderOfficerResortPoliceName = '';

                    var leaderOfficerRegionalPolice = leaderOfficerPolice;
                    var leaderOfficerRegionalPoliceId = leaderOfficerRegionalPolice.id;
                    var leaderOfficerRegionalPoliceName = leaderOfficerRegionalPolice.full_name;

                    var leaderOfficerPoliceName = leaderOfficerRegionalPoliceName;
                }
            }

            // Append empty option
            $('#officerLeader').append($('<option>', {
                value: leaderOfficerRegisterNumber,
                text: leaderOfficerRegisterNumber + ' - ' + leaderOfficerFirstName + ' ' + leaderOfficerLastName + ' | ' + leaderOfficerPositionName,
                selected: true,
                'data-register-number': leaderOfficerRegisterNumber,
            }));

            // append value to hiiden input
            $('#officerLeaderRegisterNumber').val(leaderOfficerRegisterNumber);
            $('#officerLeaderFirstName').val(leaderOfficerFirstName);
            $('#officerLeaderLastName').val(leaderOfficerLastName);
            $('#officerLeaderPhone').val(leaderOfficerPhone);
            $('#officerLeaderRankId').val(leaderOfficerRankId);
            $('#officerLeaderRankName').val(leaderOfficerRankName);
            $('#officerLeaderPositionId').val(leaderOfficerPositionId);
            $('#officerLeaderPositionName').val(leaderOfficerPositionName);
            $('#officerLeaderRegionalPoliceId').val(leaderOfficerRegionalPoliceId);
            $('#officerLeaderRegionalPoliceName').val(leaderOfficerRegionalPoliceName);
            $('#officerLeaderResortPoliceId').val(leaderOfficerResortPoliceId);
            $('#officerLeaderResortPoliceName').val(leaderOfficerResortPoliceName);

            // Langsung muat dropdown anggota berdasarkan ketua yang sudah ada
            if (leaderOfficerRegisterNumber) {
                fetchOfficerMembers(leaderOfficerRegisterNumber);
            }
        });

        //officers
        $(function() {
            for (var key in officers) {
                if (officers.hasOwnProperty(key)) {
                    var officer = officers[key];
                    var registerNumber = officer.register_number;
                    var rank = officer.rank;
                    var rankName = (rank) ? rank.name : '-';
                    var name = ((officer.first_title) ? officer.first_title + ' ' : '') + officer.first_name + ((officer.last_name) ? ' ' + officer.last_name : '') + ((officer.last_title) ? ', ' + officer.last_title : '');
                    var position = officer.position;
                    var positionName = (position) ? position.name : '-';

                    var police = officer.police ?? null;

                    var resortPolice = null;
                    var resortPoliceId = null;
                    var resortPoliceName = null;
                    var regionalPolice = null;
                    var regionalPoliceId = null;
                    var regionalPoliceName = null;
                    var policeName = null;

                    if(police){
                        if(police.class == 'RESOR'){
                            var resortPolice = police;
                            var resortPoliceId = resortPolice.id;
                            var resortPoliceName = resortPolice.full_name;

                            var regionalPolice = police.parent;
                            var regionalPoliceId = regionalPolice.id;
                            var regionalPoliceName = regionalPolice.full_name;

                            var policeName = resortPoliceName + ' - ' + regionalPoliceName;
                        }else if(police.class == 'DAERAH'){
                            var resortPolice = '';
                            var resortPoliceId = '';
                            var resortPoliceName = '';

                            var regionalPolice = police;
                            var regionalPoliceId = regionalPolice.id;
                            var regionalPoliceName = regionalPolice.full_name;

                            var policeName = regionalPoliceName;
                        }
                    }

                    // Buat baris baru untuk ditambahkan ke dalam tabel
                    var newRow = $('<tr class="text-center"></tr>');

                    // Tambahkan kolom-kolom dengan nilai yang diambil dari selectedOption
                    newRow.append('<td>' + name + '</td>');
                    newRow.append('<td>' + rankName + '</td>');
                    newRow.append('<td class="registerNumber">' + registerNumber + '</td>');
                    newRow.append('<td>' + positionName + '</td>');
                    newRow.append('<td>' + policeName + '</td>');
                    newRow.append('<td><input type="hidden" name="officers[]" value="' + registerNumber + '"><button class="btn btn-danger btn-sm deleteOfficer" type="button"><i class="bi bi-trash"></i></button></td>');

                    // Tambahkan baris ke dalam tabel
                    $('#officerMemberTable tbody').append(newRow);

                    // Hapus event listener deleteOfficer sebelumnya
                    $(document).off('click', '.deleteOfficer');

                    // Tambahkan event listener deleteOfficer yang baru
                    $(document).on('click', '.deleteOfficer', function() {
                        $(this).closest('tr').remove();
                    });
                }
            }
        });
    });

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

        $('#startDate').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: "true",
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

        $('.noEnterTextArea').on('keydown', function(event) {
            if (event.keyCode === 13) { // 13 is the Enter key code
                event.preventDefault();
            }
        });
    });

    // Select2 with Bootstrap4 theme
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        $('.select2-input-group').select2({
            theme: 'bootstrap4'
        });
    });

    // ===Related Document===
    $(document).ready(function() {
        $('#relatedDocument').on('change', function() {
            var relatedDocumentId = $(this).find(':selected').val();
            var relatedDocumentCode = $(this).find(':selected').data('code');
            var accidentId = $('#accidentId').val();

            $('#relatedDocumentCode').val(relatedDocumentCode);

            if (relatedDocumentId !== '') {
                $.ajax({
                    url: "{{ route('doc.surat-perintah-tugas-document.api.related-document', ['accident_id' => $accidentId]) }}",
                    type: "GET",
                    dataType: "json",
                    data: {
                        relatedDocumentId: relatedDocumentId,
                        relatedDocumentCode: relatedDocumentCode
                    },
                    success: function(response) {
                        // Clear existing options
                        $('#officerLeader').empty();

                        // Populate options based on response data
                        var relatedDocument = response.data;
                        var relatedDocumentLeaderOfficer = relatedDocument;

                        var relatedDocumentLeaderOfficerRegisterNumber = relatedDocumentLeaderOfficer.register_number || '';
                        var relatedDocumentLeaderOfficerFirstName = relatedDocumentLeaderOfficer.first_name || '';
                        var relatedDocumentLeaderOfficerLastName = relatedDocumentLeaderOfficer.last_name || '';
                        var relatedDocumentLeaderOfficerPhone = relatedDocumentLeaderOfficer.phone_number || '';
                        var relatedDocumentLeaderOfficerRank = relatedDocumentLeaderOfficer.rank;
                        var relatedDocumentLeaderOfficerRankId = relatedDocumentLeaderOfficerRank.id || '';
                        var relatedDocumentLeaderOfficerRankName = relatedDocumentLeaderOfficerRank.name || '';
                        var relatedDocumentLeaderOfficerPosition = relatedDocumentLeaderOfficer.position;
                        var relatedDocumentLeaderOfficerPositionId = relatedDocumentLeaderOfficerPosition.id || '';
                        var relatedDocumentLeaderOfficerPositionName = relatedDocumentLeaderOfficerPosition.name || '';

                        var relatedDocumentLeaderOfficerPolice = relatedDocumentLeaderOfficer.police ?? null;

                        var relatedDocumentLeaderOfficerResortPolice = null;
                        var relatedDocumentLeaderOfficerResortPoliceId = null;
                        var relatedDocumentLeaderOfficerResortPoliceName = null;
                        var relatedDocumentLeaderOfficerRegionalPolice = null;
                        var relatedDocumentLeaderOfficerRegionalPoliceId = null;
                        var relatedDocumentLeaderOfficerRegionalPoliceName = null;

                        var relatedDocumentLeaderOfficerPoliceName = null;

                        if(relatedDocumentLeaderOfficerPolice){
                            if(relatedDocumentLeaderOfficerPolice.class == 'RESOR'){
                                var relatedDocumentLeaderOfficerResortPolice = relatedDocumentLeaderOfficerPolice;
                                var relatedDocumentLeaderOfficerResortPoliceId = relatedDocumentLeaderOfficerResortPolice.id;
                                var relatedDocumentLeaderOfficerResortPoliceName = relatedDocumentLeaderOfficerResortPolice.full_name;

                                var relatedDocumentLeaderOfficerRegionalPolice = relatedDocumentLeaderOfficerPolice.parent;
                                var relatedDocumentLeaderOfficerRegionalPoliceId = relatedDocumentLeaderOfficerRegionalPolice.id;
                                var relatedDocumentLeaderOfficerRegionalPoliceName = relatedDocumentLeaderOfficerRegionalPolice.full_name;

                                var relatedDocumentLeaderOfficerPoliceName = relatedDocumentLeaderOfficerResortPoliceName + ' - ' + relatedDocumentLeaderOfficerRegionalPoliceName;
                            }else if(relatedDocumentLeaderOfficerPolice.class == 'DAERAH'){
                                var relatedDocumentLeaderOfficerResortPolice = '';
                                var relatedDocumentLeaderOfficerResortPoliceId = '';
                                var relatedDocumentLeaderOfficerResortPoliceName = '';

                                var relatedDocumentLeaderOfficerRegionalPolice = relatedDocumentLeaderOfficerPolice;
                                var relatedDocumentLeaderOfficerRegionalPoliceId = relatedDocumentLeaderOfficerRegionalPolice.id;
                                var relatedDocumentLeaderOfficerRegionalPoliceName = relatedDocumentLeaderOfficerRegionalPolice.full_name;

                                var relatedDocumentLeaderOfficerPoliceName = relatedDocumentLeaderOfficerRegionalPoliceName;
                            }
                        }

                        // Append empty option
                        $('#officerLeader').append($('<option>', {
                            value: relatedDocumentLeaderOfficerRegisterNumber,
                            text: relatedDocumentLeaderOfficerRegisterNumber + ' - ' + relatedDocumentLeaderOfficerFirstName + ' ' + relatedDocumentLeaderOfficerLastName + ' | ' + relatedDocumentLeaderOfficerPositionName,
                            selected: true,
                            'data-register-number': relatedDocumentLeaderOfficerRegisterNumber,
                        }));

                        // append value to hiiden input
                        $('#officerLeaderRegisterNumber').val(relatedDocumentLeaderOfficerRegisterNumber);
                        $('#officerLeaderFirstName').val(relatedDocumentLeaderOfficerFirstName);
                        $('#officerLeaderLastName').val(relatedDocumentLeaderOfficerLastName);
                        $('#officerLeaderPhone').val(relatedDocumentLeaderOfficerPhone);
                        $('#officerLeaderRankId').val(relatedDocumentLeaderOfficerRankId);
                        $('#officerLeaderRankName').val(relatedDocumentLeaderOfficerRankName);
                        $('#officerLeaderPositionId').val(relatedDocumentLeaderOfficerPositionId);
                        $('#officerLeaderPositionName').val(relatedDocumentLeaderOfficerPositionName);
                        $('#officerLeaderRegionalPoliceId').val(relatedDocumentLeaderOfficerRegionalPoliceId);
                        $('#officerLeaderRegionalPoliceName').val(relatedDocumentLeaderOfficerRegionalPoliceName);
                        $('#officerLeaderResortPoliceId').val(relatedDocumentLeaderOfficerResortPoliceId);
                        $('#officerLeaderResortPoliceName').val(relatedDocumentLeaderOfficerResortPoliceName);
                    }
                });
            } else {
                $('#relatedDocumentOption').empty();
            }
        });
    });

    // ===Officer===
    // Fungsi untuk memuat dropdown anggota berdasarkan NRP ketua
    function fetchOfficerMembers(leaderRegisterNumber) {
        if (!leaderRegisterNumber) {
            $('#officerMemberOption').prop('disabled', true).empty().append('<option value="">--Pilih Penyidik--</option>');
            return;
        }

        $.ajax({
            url: "{{ route('doc.surat-perintah-penyelidikan-document.api.internal-officers', ['accident_id' => $accidentId]) }}",
            type: "GET",
            dataType: "json",
            data: {
                selectedLeaderOfficerRegisterNumber: leaderRegisterNumber
            },
            success: function(response) {
                $('#officerMemberOption').empty().append('<option value="">--Pilih Penyidik--</option>');

                response.data.forEach(function(member) {
                    var rankName = (member.rank) ? member.rank.name : '-';
                    var positionName = (member.position) ? member.position.name : '-';

                    var police = member.police ?? null;
                    var resortPoliceName = null;
                    var regionalPoliceName = null;

                    if (police) {
                        if (police.class == 'RESOR') {
                            resortPoliceName = police.full_name;
                            regionalPoliceName = police.parent ? police.parent.full_name : null;
                        } else if (police.class == 'DAERAH') {
                            resortPoliceName = '';
                            regionalPoliceName = police.full_name;
                        }
                    }

                    $('#officerMemberOption').append($('<option>', {
                        value: member.id,
                        text: member.register_number + ' - ' + member.full_name + ' - ' + rankName,
                        'data-register-number': member.register_number,
                        'data-rank-name': rankName,
                        'data-name': member.full_name,
                        'data-position-name': positionName,
                        'data-resort-police-name': resortPoliceName,
                        'data-regional-police-name': regionalPoliceName,
                    }));
                });

                $('#officerMemberOption').prop('disabled', false).select2({
                    theme: 'bootstrap4'
                });

                // Ketua berhasil dimuat = clear error ketua
                $('#officerLeader').removeClass('is-invalid');
                $('#officerLeader').next('.select2-container').next('.frontend-error').remove();
                $('#officerLeader').next('.frontend-error').remove();
            },
            error: function() {
                return Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Maaf, terjadi kesalahan teknis saat memuat daftar penyidik!'
                });
            }
        });
    }

    // Ketika ketua tim dipilih secara manual, otomatis populate dropdown anggota
    $(document).on('change', '#officerLeader', function() {
        var selectedLeaderRegisterNumber = $(this).find('option:selected').data('register-number');
        fetchOfficerMembers(selectedLeaderRegisterNumber);
    });

    $(document).ready(function() {
        $('#officerMemberOptionAddButtton').on('click', function() {
            var selectedOption = $('#officerMemberOption').find('option:selected');
            var signatoryRegisterNumber = $('#signatory').find('option:selected').data('register-number');
            var leaderOfficerRegisterNumber = $('#officerLeader').find('option:selected').data('register-number');

            if(selectedOption.val() == '') {
                return Swal.fire({
                    title: 'Gagal',
                    text: 'Pilih Penyidik Terlebih Dahulu',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
            }else if(selectedOption.data('register-number') == signatoryRegisterNumber) {
                return Swal.fire({
                    title: 'Gagal',
                    text: 'Penyidik sudah dipilih sebagai Penandatangan',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
            }else if(selectedOption.data('register-number') == leaderOfficerRegisterNumber) {
                return Swal.fire({
                    title: 'Gagal',
                    text: 'Penyidik sudah dipilih sebagai Ketua Tim',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
            }

            // Ambil data yang diperlukan dari selectedOption
            var registerNumber = selectedOption.data('register-number');
            var rankName = selectedOption.data('rank-name');
            var name = selectedOption.data('name');
            var positionName = selectedOption.data('position-name');
            var polresName = selectedOption.data('resort-police-name');
            var poldaName = selectedOption.data('regional-police-name');

            var tablesToCheck = [
                {
                    tableSelector: '#officerMemberTable',
                    errorMessage: 'Personil sudah ada dalam daftar penyidik yang ditugaskan'
                },
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

            if (!isAppended) {
            // Buat baris baru untuk ditambahkan ke dalam tabel
                var newRow = $('<tr class="text-center"></tr>');

                // Tambahkan kolom-kolom dengan nilai yang diambil dari selectedOption
                newRow.append('<td>' + name + '</td>');
                newRow.append('<td>' + rankName + '</td>');
                newRow.append('<td class="registerNumber">' + registerNumber + '</td>');
                newRow.append('<td>' + positionName + '</td>');
                newRow.append('<td>' + poldaName + ' - ' + polresName + '</td>');
                newRow.append('<td><input type="hidden" name="officers[]" value="' + registerNumber + '"><button class="btn btn-danger btn-sm deleteOfficer" type="button"><i class="bi bi-trash"></i></button></td>');

                // Tambahkan baris ke dalam tabel
                $('#officerMemberTable tbody').append(newRow);

                // Clear error tabel setelah berhasil tambah anggota
                $('#officerMemberTable').removeClass('border border-danger');
                $('#officerMemberTable').next('.frontend-error').remove();

                // Hapus event listener deleteOfficer sebelumnya
                $(document).off('click', '.deleteOfficer');

                // Tambahkan event listener deleteOfficer yang baru
                $(document).on('click', '.deleteOfficer', function() {
                    $(this).closest('tr').remove();
                });
            }
        });
    });

    // Validasi Submit Form
    $(document).ready(function() {

        // Helper: tandai error pada field (mendukung Select2)
        function markError(selector, message) {
            var $el = $(selector);
            $el.addClass('is-invalid');
            // Jika Select2, tampilkan error setelah .select2-container
            var $target = $el.next('.select2-container').length
                ? $el.next('.select2-container')
                : $el;
            if ($target.next('.frontend-error').length === 0) {
                $target.after('<span class="text-danger frontend-error d-block" style="font-size:0.875em; margin-top:0.25rem">' + message + '</span>');
            }
        }

        // Helper: hapus error pada field (mendukung Select2)
        function clearError(selector) {
            var $el = $(selector);
            $el.removeClass('is-invalid');
            // Hapus dari setelah select2-container atau setelah element itu sendiri
            $el.next('.select2-container').next('.frontend-error').remove();
            $el.next('.frontend-error').remove();
        }

        // Auto-clear saat user ubah nilai
        $('#documentNumber').on('input', function() { clearError(this); });
        $('#relatedDocument').on('change', function() { clearError(this); });
        $('#startDate').on('change', function() { clearError(this); });
        $('#endDate').on('change', function() { clearError(this); });
        $('#documentDate').on('change', function() { clearError(this); });
        $('#signatory').on('change', function() { clearError(this); });
        $('#task').on('input', function() { clearError(this); });
        $('#officerLeader').on('change', function() {
            clearError(this);
            $('#officerMemberTable').removeClass('border border-danger');
            $('#officerMemberTable').next('.frontend-error').remove();
        });


        $('#suratPerintahTugasFormSubmit').on('click', function(e) {
            e.preventDefault();

            var hasError = false;
            var $firstError = null;

            // Bersihkan semua error sebelumnya
            $('.frontend-error').remove();
            $('.is-invalid').removeClass('is-invalid');
            $('#officerMemberTable').removeClass('border border-danger');

            // Validasi No Dokumen
            var documentNumber = $.trim($('#documentNumber').val());
            if (documentNumber === '') {
                markError('#documentNumber', 'No Dokumen harus diisi');
                if (!hasError) $firstError = $('#documentNumber');
                hasError = true;
            } else if (documentNumber.length < 5) {
                markError('#documentNumber', 'No Dokumen harus lengkap (min 5 karakter)');
                if (!hasError) $firstError = $('#documentNumber');
                hasError = true;
            }

            // Validasi Sprinlidik/Sprindik
            if (!$('#relatedDocument').val()) {
                markError('#relatedDocument', 'No Sprinlidik/Sprindik harus diisi');
                if (!hasError) $firstError = $('#relatedDocument');
                hasError = true;
            }

            // Validasi Tanggal Mulai
            if ($.trim($('#startDate').val()) === '') {
                markError('#startDate', 'Tanggal Mulai Tugas harus diisi');
                if (!hasError) $firstError = $('#startDate');
                hasError = true;
            }

            // Validasi Tanggal Akhir (kecuali sampai selesai)
            if (!$('#isFinished').is(':checked') && $.trim($('#endDate').val()) === '') {
                markError('#endDate', 'Tanggal Akhir Tugas harus diisi');
                if (!hasError) $firstError = $('#endDate');
                hasError = true;
            }

            // Validasi Tanggal Ditandatangani
            if ($.trim($('#documentDate').val()) === '') {
                markError('#documentDate', 'Tanggal Ditandatangani harus diisi');
                if (!hasError) $firstError = $('#documentDate');
                hasError = true;
            }

            // Validasi Yang Menandatangani
            if ($('#signatory').val() === '') {
                markError('#signatory', 'Yang Menandatangani harus diisi');
                if (!hasError) $firstError = $('#signatory');
                hasError = true;
            }

            // Validasi Tugas
            if ($.trim($('#task').val()) === '') {
                markError('#task', 'Tugas harus diisi');
                if (!hasError) $firstError = $('#task');
                hasError = true;
            }

            // Validasi Ketua Tim
            var leaderVal = $('#officerLeaderRegisterNumber').val() ||
                            $('#officerLeader').find('option:selected').data('register-number') ||
                            $('#officerLeader').find('option:selected').val();
            if (!leaderVal) {
                markError('#officerLeader', 'Ketua Tim Tugas harus diisi');
                if (!hasError) $firstError = $('#officerLeader');
                hasError = true;
            }

            // Validasi Anggota (tabel)
            if ($('#officerMemberTable tbody tr').length === 0) {
                $('#officerMemberTable').addClass('border border-danger');
                if ($('#officerMemberTable').next('.frontend-error').length === 0) {
                    $('#officerMemberTable').after('<span class="text-danger frontend-error" style="display:block; margin-top:4px">Anggota Tim Tugas harus diisi minimal 1 personil</span>');
                }
                if (!hasError) $firstError = $('#officerMemberTable');
                hasError = true;
            }

            if (hasError) {
                if ($firstError) {
                    $('html, body').animate({ scrollTop: $firstError.offset().top - 120 }, 400);
                }
                return;
            }

            // Lakukan validasi di sisi server menggunakan Ajax
            $.ajax({
                url: "{{ route('doc.surat-perintah-tugas-document.api.validate-request-form', ['accident_id' => $accidentId]) }}",
                type: 'POST',
                dataType: 'json',
                data: $('#suratPerintahTugasForm').serialize(),
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Berhasil',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'Ok'
                        }).then((result) => {
                            $('#suratPerintahTugasForm').submit();
                        });
                    }
                },
                error: function(xhr) {
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

