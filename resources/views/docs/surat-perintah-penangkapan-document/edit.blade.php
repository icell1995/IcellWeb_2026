@php
    $_title = 'Edit Surat Perintah Penangkapan';
    $p = $defaults ?? [];
    $sigP = $p['signature'] ?? [];
    $subP = $p['submitted'] ?? [];
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">

    <style>
        #spp_internalOfficer .input-group > .select2-container {
            flex: 1 1 auto;
            min-width: 0;
            width: 1% !important;
        }
    </style>
@endpush

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}">
        <i class="bi bi-arrow-left"></i> Kembali ke Progres Perkara
    </a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Edit Surat Perintah Penangkapan</h5>

            @if ($errors->any())
                <div class="card-body">
                    <div class="alert alert-danger">
                        <ul class="mb-0">
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
            <form method="POST" action="{{ route('doc.surat-perintah-penangkapan-document.update', ['id' => $doc->id, 'accident_id' => $accidentId, 'document_category_id' => '0301']) }}">
                @csrf
                <input type="hidden" name="accident_id" value="{{ $accidentId }}">

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="accidentNumber">Nomor LP<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="accidentNumber" type="text" class="form-control font-weight-bold" readonly
                            value="{{ $accident->no_lp ?? '-' }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="documentNumber">Nomor Surat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="documentNumber" type="text" name="documentNumber"
                            class="form-control @error('documentNumber') is-invalid @enderror font-weight-bold"
                            value="{{ old('documentNumber', $doc->document_number) }}" required>
                        @error('documentNumber')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Tanggal Surat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input class="form-control spp-date @error('documentDate') is-invalid @enderror" id="documentDate"
                            name="documentDate" placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('documentDate', $doc->document_date ? $doc->document_date->format('Y-m-d') : '') }}"
                            data-provide="datepicker" required readonly>
                        @error('documentDate')
                            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="suratPerintahPenyidikanDocument">Surat Perintah Penyidikan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        @php $sprindikSelected = old('suratPerintahPenyidikanDocument'); @endphp
                        <select class="form-control select2" name="suratPerintahPenyidikanDocument" id="suratPerintahPenyidikanDocument" required>
                            <option value="">--Pilih No Surat Perintah Penyidikan--</option>
                            @foreach ($suratPerintahPenyidikanDocuments as $d)
                                <option value="{{ $d->id }}" {{ (string) old('suratPerintahPenyidikanDocument', $doc->sprindik_document_id) === (string) $d->id ? 'selected' : '' }}>
                                    {{ $d->document_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="suratKetetapanTentangPenetapanTersangkaDocument">Surat Ketetapan tentang Penetapan Tersangka<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2" name="suratKetetapanTentangPenetapanTersangkaDocument" id="suratKetetapanTentangPenetapanTersangkaDocument" required>
                            <option value="">--Pilih No Surat Ketetapan tentang Penetapan Tersangka--</option>
                            @foreach ($suratKetetapanTentangPenetapanTersangkaDocuments as $d)
                                <option value="{{ $d->id }}" {{ (string) old('suratKetetapanTentangPenetapanTersangkaDocument', $doc->sket_document_id) === (string) $d->id ? 'selected' : '' }}>
                                    {{ $d->document_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="suratPerintahTugasDocument">Surat Perintah Tugas<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2 @error('suratPerintahTugasDocument') is-invalid @enderror"
                            name="suratPerintahTugasDocument" id="suratPerintahTugasDocument" required>
                            <option value="">--Pilih No Surat Perintah Tugas--</option>
                            @foreach ($suratPerintahTugasDocuments as $d)
                                <option value="{{ $d->id }}" {{ (string) old('suratPerintahTugasDocument', $doc->surat_perintah_tugas_document_id) === (string) $d->id ? 'selected' : '' }}>
                                    {{ $d->document_number }}
                                </option>
                            @endforeach
                        </select>
                        @error('suratPerintahTugasDocument')
                            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                @php
                    $s22RefsReady = filled(old('suratPerintahPenyidikanDocument', $doc->sprindik_document_id))
                        && filled(old('suratKetetapanTentangPenetapanTersangkaDocument', $doc->sket_document_id))
                        && filled(old('suratPerintahTugasDocument', $doc->surat_perintah_tugas_document_id));
                @endphp
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="suspect">Identitas Tersangka<span
                            class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2 @error('suspect') is-invalid @enderror" name="suspect" id="suspect" required
                            @unless ($s22RefsReady) disabled @endunless>
                            <option value="">--Pilih Tersangka--</option>
                            @foreach ($suspects as $s)
                                <option value="{{ $s->id }}" {{ (string) old('suspect', $doc->suspect_id) === (string) $s->id ? 'selected' : '' }}>
                                    {{ $s->name ?? $s->full_name ?? '' }}{{ ! empty($s->identity_number) ? ' — ' . $s->identity_number : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('suspect')
                            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                        <small id="suspectHelpText"
                            class="form-text text-muted d-block mt-1 {{ $s22RefsReady ? 'd-none' : '' }}">
                            (*Pilih Surat Perintah Penyidikan, Surat Ketetapan Tersangka, dan Surat Perintah Tugas
                            terlebih dahulu untuk membuka pilihan tersangka)
                        </small>
                    </div>
                </div>

                <hr>

                @php
                    $plLeaderId = old('officerLeader', $kepadaPrefillLeaderId ?? '');
                @endphp

                <h5 class="fw-bold text-blue-dark">Tim Penyidik</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="spp_officerLeader">Ketua Tim Penyidik <span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9">
                        <select name="officerLeader" id="spp_officerLeader" class="form-control select2" required>
                            <option value="">--Pilih Ketua Penyidik--</option>
                            @foreach ($leaderOfficers as $data)
                                @php
                                    $positionName = $data->position?->name ?? '';
                                @endphp
                                <option value="{{ $data->id }}" data-register-number="{{ $data->register_number }}"
                                    {{ (string) $plLeaderId === (string) $data->id ? 'selected' : '' }}>
                                    {{ $data->register_number . ' - ' . $data->full_name . ' | ' . $positionName }}
                                </option>
                            @endforeach
                        </select>
                        @error('officerLeader')
                            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="row col-12 my-2 ms-0">
                    <label class="fw-bold">Penyidik<small> (*Pilihan Penyidik akan tampil setelah Ketua Tim
                            Penyidik dipilih)</small></label>

                    <div id="spp_internalOfficer">
                        <div class="alert alert-primary my-2" role="alert">
                            1. Pilihan Penyidik akan tampil setelah Ketua Tim Penyidik dipilih. <br />
                            2. Pilih personel lalu klik tombol 'Tambah' untuk menambahkan personel sebagai penyidik.
                        </div>

                        <div class="row my2">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select class="custom-select select2-input-group" id="spp_officerInternalMemberOption"
                                        aria-describedby="spp_officerInternalMemberOptionAddButtton">
                                        <option value="">--Pilih Penyidik--</option>
                                    </select>
                                    <button class="btn btn-primary" type="button"
                                        id="spp_officerInternalMemberOptionAddButtton"><i class="bi bi-plus-circle"></i>
                                        Tambah</button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive my-2">
                            <table class="table table-bordered" id="spp_internalOfficerMemberTable">
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
                                    @foreach ($kepadaInternalRows ?? [] as $o)
                                        @php
                                            $rankName = $o->rank?->name ?? '-';
                                            $positionName = $o->position?->name ?? '-';
                                            $policeName = '-';
                                            if ($o->police) {
                                                $policeName = $o->police->full_name ?? ($o->police->name ?? '-');
                                            }
                                        @endphp
                                        <tr class="text-center">
                                            <td>{{ $o->full_name }}</td>
                                            <td>{{ $rankName }}</td>
                                            <td class="registerNumber">{{ $o->register_number }}</td>
                                            <td>{{ $positionName }}</td>
                                            <td>{{ $policeName }}</td>
                                            <td>
                                                <input type="hidden" name="internalOfficers[]" value="{{ $o->register_number }}">
                                                <button class="btn btn-danger btn-sm spp_deleteInternalOfficer" type="button"><i class="bi bi-trash"></i> Hapus</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @error('internalOfficers')
                            <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="signatoryOfficerId">Yang Menandatangani<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        @php
                            $sigSelected = old('signatoryOfficerId', $sigP['officer_id'] ?? '');
                        @endphp
                        <select class="form-control select2 @error('signatoryOfficerId') is-invalid @enderror"
                            name="signatoryOfficerId" id="signatoryOfficerId" required>
                            <option value="">--Pilih Yang Menandatangani--</option>
                            @foreach (($authorizedSignatories ?? collect()) as $o)
                                @php
                                    $positionName = $o->position?->name ?? '-';
                                @endphp
                                <option value="{{ $o->id }}" {{ (string) $sigSelected === (string) $o->id ? 'selected' : '' }}>
                                    {{ ! empty($o->register_number) ? $o->register_number : '' }} - {{ $o->full_name ?? $o->name ?? '' }} | {{ $positionName }}
                                </option>
                            @endforeach
                        </select>
                        @error('signatoryOfficerId')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="submittedOfficerId">Yang Menyerahkan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        @php
                            $subSelected = old('submittedOfficerId', $subP['officer_id'] ?? '');
                        @endphp
                        <select class="form-control select2 @error('submittedOfficerId') is-invalid @enderror"
                            name="submittedOfficerId" id="submittedOfficerId" required>
                            <option value="">--Pilih Yang Menyerahkan--</option>
                            @foreach (($submitterOfficers ?? collect()) as $o)
                                @php
                                    $positionName = $o->position?->name ?? '-';
                                @endphp
                                <option value="{{ $o->id }}" {{ (string) $subSelected === (string) $o->id ? 'selected' : '' }}>
                                    {{ ! empty($o->register_number) ? $o->register_number : '' }} - {{ $o->full_name ?? $o->name ?? '' }} | {{ $positionName }}
                                </option>
                            @endforeach
                        </select>
                        @error('submittedOfficerId')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Berlaku Sampai Tanggal<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input type="text" class="form-control spp-date @error('validUntilDate') is-invalid @enderror"
                            name="validUntilDate" id="validUntilDate" placeholder="Tanggal (YYYY-MM-DD)"
                            value="{{ old('validUntilDate', $doc->valid_until_date ? \Carbon\Carbon::parse($doc->valid_until_date)->format('Y-m-d') : '') }}"
                            autocomplete="off" readonly>
                        @error('validUntilDate')
                            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Penyerahan Surat Perintah Penangkapan</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input type="text" class="form-control spp-date @error('handoverDate') is-invalid @enderror"
                            name="handoverDate" id="handoverDate" placeholder="Tanggal (YYYY-MM-DD)"
                            value="{{ old('handoverDate', $p['handover']['date'] ?? '') }}" autocomplete="off" readonly>
                        @error('handoverDate')
                            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-dark-blue">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-danger">
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
    <script>
        $(document).ready(function() {
            $('select.select2').not('#suspect').select2({
                theme: 'bootstrap4',
                width: '100%',
                allowClear: true
            });
            $('.select2-input-group').select2({
                theme: 'bootstrap4'
            });
            $('#spp_officerInternalMemberOption').prop('disabled', true).trigger('change');

            function s22RefsCompleteForSuspect() {
                var elSpr = document.getElementById('suratPerintahPenyidikanDocument');
                var elSket = document.getElementById('suratKetetapanTentangPenetapanTersangkaDocument');
                var elSpt = document.getElementById('suratPerintahTugasDocument');
                if (!elSpr || !elSket || !elSpt) {
                    return false;
                }
                var v1 = (elSpr.value || '').trim();
                var v2 = (elSket.value || '').trim();
                var v3 = (elSpt.value || '').trim();
                return v1.length > 0 && v2.length > 0 && v3.length > 0;
            }

            function s22ToggleSuspectEnabled() {
                var ok = s22RefsCompleteForSuspect();
                var $sus = $('#suspect');

                $sus.prop('disabled', !ok);
                $('#suspectHelpText').toggleClass('d-none', ok);

                if (!ok) {
                    $sus.val(null);
                }

                $sus.trigger('change.select2');
            }

            var s22RefSuspectSelectors = '#suratPerintahPenyidikanDocument, #suratKetetapanTentangPenetapanTersangkaDocument, #suratPerintahTugasDocument';
            $(s22RefSuspectSelectors).on('change select2:select select2:clear', s22ToggleSuspectEnabled);

            $('#suspect').select2({
                theme: 'bootstrap4',
                width: '100%',
                allowClear: true
            });
            s22ToggleSuspectEnabled();
            setTimeout(s22ToggleSuspectEnabled, 50);

            $('.spp-date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });
            $('.spp-date').on('keydown', function(e) {
                e.preventDefault();
                return false;
            });

            $('form').on('submit', function(e) {
                if ($('#suspect').prop('disabled')) {
                    e.preventDefault();
                    alert('Isi dulu Surat Perintah Penyidikan, Surat Ketetapan Tersangka, dan Surat Perintah Tugas sebelum memilih tersangka.');
                    return false;
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            var $internalMemberSelect = $('#spp_officerInternalMemberOption');
            var sppCachedInternalMembers = [];

            function sppGetAppendedRegisterSet() {
                var set = {};
                $('#spp_internalOfficerMemberTable tbody tr .registerNumber').each(function() {
                    var r = $(this).text().trim();
                    if (r) {
                        set[r] = true;
                    }
                });
                return set;
            }

            function sppEnsureMemberSelect2() {
                if (!$internalMemberSelect.hasClass('select2-hidden-accessible')) {
                    $internalMemberSelect.select2({
                        theme: 'bootstrap4'
                    });
                }
            }

            function sppRebuildMemberDropdown() {
                $internalMemberSelect.empty();
                $internalMemberSelect.append(new Option('--Pilih Penyidik--', ''));
                var appended = sppGetAppendedRegisterSet();
                sppCachedInternalMembers.forEach(function(member) {
                    var reg = member.register_number;
                    if (!reg || appended[reg]) {
                        return;
                    }
                    var rankName = (member.rank) ? member.rank.name : '-';
                    var positionName = (member.position) ? member.position.name : '-';
                    var policeName = (member.police) ? (member.police.full_name || member.police.name || '-') : '-';
                    $internalMemberSelect.append($('<option>', {
                        value: member.id,
                        text: member.register_number + ' - ' + member.full_name + ' | ' + positionName,
                        'data-register-number': member.register_number,
                        'data-rank-name': rankName,
                        'data-name': member.full_name,
                        'data-position-name': positionName,
                        'data-police-name': policeName,
                    }));
                });
                sppEnsureMemberSelect2();
                $internalMemberSelect.prop('disabled', false);
                $internalMemberSelect.val(null).trigger('change');
            }

            function sppLoadInternalOfficers(registerNumber) {
                if (!registerNumber) {
                    sppCachedInternalMembers = [];
                    $internalMemberSelect.empty();
                    $internalMemberSelect.append(new Option('--Pilih Penyidik--', ''));
                    sppEnsureMemberSelect2();
                    $internalMemberSelect.prop('disabled', true);
                    $internalMemberSelect.val(null).trigger('change');
                    return;
                }
                $.ajax({
                    url: "{{ route('doc.surat-perintah-penyidikan-document.api.internal-officers', ['accident_id' => $accidentId]) }}",
                    type: "GET",
                    dataType: "json",
                    data: {
                        selectedLeaderOfficerRegisterNumber: registerNumber
                    },
                    success: function(response) {
                        sppCachedInternalMembers = response.data || [];
                        sppRebuildMemberDropdown();
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Gagal',
                            text: 'Maaf, terjadi kesalahan teknis.',
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                    }
                });
            }

            $('#spp_officerLeader').on('change', function() {
                var registerNumber = $(this).find(':selected').data('register-number');
                var tablesToCheck = [{
                        tableSelector: '#spp_internalOfficerMemberTable',
                        errorMessage: 'Sudah ada dalam daftar personil, hapus terlebih dahulu untuk memilih sebagai ketua'
                    }
                ];
                var isAppended = false;
                tablesToCheck.forEach(function(table) {
                    $(table.tableSelector).find('tbody tr').each(function() {
                        var appendedRegisterNumber = $(this).find('.registerNumber').text().trim();
                        if (appendedRegisterNumber && appendedRegisterNumber == registerNumber) {
                            isAppended = true;
                            Swal.fire({
                                title: 'Gagal',
                                text: table.errorMessage,
                                icon: 'error',
                                confirmButtonText: 'Ok'
                            });
                            return false;
                        }
                    });
                });
                if (!isAppended) {
                    sppLoadInternalOfficers(registerNumber || '');
                }
            });

            if ($('#spp_officerLeader').val()) {
                sppLoadInternalOfficers($('#spp_officerLeader').find(':selected').data('register-number'));
            }

            $('#spp_officerInternalMemberOptionAddButtton').on('click', function() {
                var selectedOption = $('#spp_officerInternalMemberOption').find('option:selected');
                if (selectedOption.val() == '' || selectedOption.val() == undefined) {
                    return Swal.fire({
                        title: 'Gagal',
                        text: 'Pilih personel penyidik terlebih dahulu',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
                if (!$('#spp_officerLeader').val()) {
                    return Swal.fire({
                        title: 'Gagal',
                        text: 'Pilih Ketua Tim Terlebih Dahulu',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
                var registerNumber = selectedOption.data('register-number');
                var rankName = selectedOption.data('rank-name');
                var name = selectedOption.data('name');
                var positionName = selectedOption.data('position-name');
                var policeName = selectedOption.data('police-name');
                var isDup = false;
                $('#spp_internalOfficerMemberTable tbody tr').each(function() {
                    var appendedRegisterNumber = $(this).find('.registerNumber').text().trim();
                    if (appendedRegisterNumber == registerNumber) {
                        isDup = true;
                        Swal.fire({
                            title: 'Gagal',
                            text: 'Personil sudah ada dalam daftar',
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                        return false;
                    }
                });
                if (isDup) return;
                var newRow = $('<tr class="text-center"></tr>');
                newRow.append('<td>' + name + '</td>');
                newRow.append('<td>' + rankName + '</td>');
                newRow.append('<td class="registerNumber">' + registerNumber + '</td>');
                newRow.append('<td>' + positionName + '</td>');
                newRow.append('<td>' + policeName + '</td>');
                newRow.append('<td><input type="hidden" name="internalOfficers[]" value="' + registerNumber +
                    '"><button class="btn btn-danger btn-sm spp_deleteInternalOfficer" type="button"><i class="bi bi-trash"></i> Hapus</button></td>'
                );
                $('#spp_internalOfficerMemberTable tbody').append(newRow);
                sppRebuildMemberDropdown();
            });

            $(document).on('click', '.spp_deleteInternalOfficer', function() {
                $(this).closest('tr').remove();
                if ($('#spp_officerLeader').val() && sppCachedInternalMembers.length) {
                    sppRebuildMemberDropdown();
                }
            });
        });
    </script>
@endpush
