@php
    $_title = 'Daftar Penyidik Sertifikasi';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
@endpush

@section('content')
    @php
        $userRoleId = Auth::user()->role_id;
    @endphp
    
    <div class="loaderbg" style="display:none"></div>

    <div class="box">
        <div class="box-header">
            <h3 class="fw-bold text-blue-dark"> Daftar Personel Sudah Sertifikasi
                {{ $userRoleId == 3 ? $currentPolice->full_name : '' }}</h3>
        </div>
        <div class="boxy-body mt-4">
            <div class="alert alert-danger" id="attentionBox">
                <div class="text-center">
                    <b>
                        PERHATIAN !<br />
                        <br />
                        NAMA PERSONEL BARU AKAN MUNCUL JIKA SUDAH MENGISI RIWAYAT SERTIFIKASI DI FORM PERSONEL
                    </b>
                </div>
            </div>

            <div class="mt-3 table-responsive">
                <table class="table table-striped table-bordered table-users dataTable table-signatory" name="dataTable" width="100%">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Nama</th>
                            <th class="text-center">NRP</th>

                            <th class="text-center">Pangkat</th>
                            <th class="text-center">Jabatan</th>

                            <th class="text-center">Satker</th>

                            <th class="text-center">Sertifikat</th>
                            <th class="text-center">Opsi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $iteration = 1;
                        @endphp

                        @if (!empty($users))
                            @foreach ($users as $user)
                                @if ($user->officer->is_valid == true)
                                    <tr>
                                        <td class="text-center align-middle">
                                            {{ $iteration }}
                                        </td>
                                        <td class="text-center align-middle">
                                            {{ $user->officer->full_name }} {{($user->officer->status == 'EXIT') ? ', (KELUAR GAKKUM LANTAS)' : (($user->officer->status == 'RETIRE') ? ', (PENSIUN)' : '')}}
                                        </td>
                                        <td class="text-center align-middle">
                                            {{ $user->officer->register_number ?? '' }}
                                        </td>
                                     
                                        <td class="text-center align-middle">
                                            {{ $user->officer->rank->name ?? '-' }}
                                        </td>
                                        <td class="text-center align-middle">
                                            {{ $user->officer->position->name ?? '-' }}
                                            @if($user->officer->flag == 'ADMIN')
                                                {{'(ADMIN)'}}
                                            @elseif($user->officer->class == 'SIGNATORY')
                                                {{'(PENANDATANGAN)'}}
                                            @endif
                                        </td>

                                        <td class="text-center align-middle">
                                            {{ $user->officer->police->full_name ?? '-' }}
                                            <br>
                                            {{ $user->officer->police->parent->full_name ?? '-' }}
                                        </td>

                                        <td class="text-left align-middle">
                                            <div class="card">
                                                <div class="card-body">
                                                    @if ($user->officer->is_certificate_exists == true)
                                                        <hr/>
                                                        @foreach ($user->officer->officerCertificateHistories as $certificate)
                                                            <b>No Sertifikat :</b> {{ $certificate->certificate_number }}
                                                            <br/>
                                                            <b>Sertifikat :</b> {{ (!empty($certificate->certificateType)) ? $certificate->certificateType->name : '-' }}
                                                            <hr/>
                                                        @endforeach
                                                    @else
                                                        <h5>
                                                            <span class="badge bg-danger">
                                                                Belum Memiliki Sertifikat
                                                            </span>
                                                        </h5>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            @php
                                                $positionClusterId = $user->officer->position->position_cluster_id ?? '';
                                            @endphp

                                            @if (in_array($userRoleId, [1,2,3,5]))
                                                <a href="{{ route('personnel.show', ['id' => $user->id, 'policeId' => $user->police_id]) }}" class="btn btn-warning m-1">
                                                    <i class="bi bi-binoculars"></i> Lihat
                                                </a>
                                            @endif
                                        </td>
                                    </tr>

                                    @php
                                        $iteration++;
                                    @endphp
                                @endif

                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

        </div>
    </div>

@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" defer></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

    <!-- Delete Button -->
    <script src="{{ asset('js/laravel.js') }}"></script>

    <script>
        $(document).ready(function() {
            setInterval(function() {
                $('#attentionBox').toggleClass('alert-danger alert-warning');
            }, 1000);
        });

        $(document).ready(function() {
            $('#registerNumber').keydown(function(e) {
                // Mengizinkan angka, backspace, dan tombol panah
                if (
                    // Tombol angka (0-9)
                    (e.keyCode >= 48 && e.keyCode <= 57) ||
                    // Tombol numpad (0-9)
                    (e.keyCode >= 96 && e.keyCode <= 105) ||
                    // Tombol backspace
                    e.keyCode === 8 ||
                    // Tombol Delete
                    e.keyCode === 46 ||
                    // Tombol panah kiri dan kanan
                    (e.keyCode >= 37 && e.keyCode <= 40)
                ) {
                    return true; // Mengizinkan input
                } else {
                    e.preventDefault(); // Mencegah input karakter lain
                    return false;
                }
            });
        });

        $(document).ready(function() {
            $('.dataTable').DataTable({
                responsive: true,
            });
        });

        // Select2 with Bootstrap4 theme
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        });


        $(document).ready(function() {
            $('#saveCheckOfficerForm').click(function() {
                var registerNumber = $('#registerNumber').val();

                if (registerNumber === '') {
                    //remove error message
                    $('#registerNumberFormError').remove();
                    //append error message to input
                    $('#registerNumber').parent().append(
                        '<small id="registerNumberFormError" class="form-text text-danger">Kolom ini wajib diisi.</small>'
                    );

                    return false;
                }

                if (isNaN(registerNumber)) {
                    //remove error message
                    $('#registerNumberFormError').remove();
                    //append error message to input
                    $('#registerNumber').parent().append(
                        '<small id="registerNumberFormError" class="form-text text-danger">Kolom ini wajib diisi Angka.</small>'
                    );

                    return false;
                }

                $.ajax({
                    url: "{{ route('personnel.api.check-officer', ['policeId' => $currentPoliceId]) }}",
                    method: "GET",
                    data: {
                        registerNumber: registerNumber
                    },
                    success: function(response) {
                        //if data is found
                        // show swal
                        var data = response.data;
                        if (response.code == '200') {
                            var dataOfficer = data.officer;
                            var dataAdmins = data.admins;
                            var positionName = (dataOfficer.position) ? dataOfficer.position.name : '-';

                            var infoMessages = 'Nama: ' + dataOfficer.full_name + '<br/>';
                            infoMessages += 'NRP: ' + dataOfficer.register_number + '<br/>';
                            infoMessages += 'Satker: ' + dataOfficer.police.full_name + '<br/>';
                            infoMessages += 'Jabatan: ' + positionName + '<br/>';
                            infoMessages += '<br/>';

                            var iteration = 1;
                             dataAdmins.forEach(function(admin) {
                                infoMessages += 'Dengan Admin Satker ' + iteration + ': ' + (admin.full_name ?? '-') + '<br/>';
                                infoMessages += 'No Telp/WA: ' + (admin.phone_number ?? '-') + '<br/>';
                                infoMessages += '<br/>';

                                iteration++;
                            });
                            
                            //close modal
                            $('#checkOfficerModal').modal('hide');
                            $('.modal-backdrop').remove();

                            return Swal.fire({
                                icon: 'success',
                                title: 'NRP Sudah terdaftar',
                                html: infoMessages,
                                confirmButtonText: 'Ok',
                            });
                        }
                    },
                    error: function(error, xhr, status) {
                        if (status == 'Not Found') {
                            //close modal
                            $('#checkOfficerModal').modal('hide');
                            $('.modal-backdrop').remove();

                            // show notif data not found and confirmitaion wanna to create new data
                            Swal.fire({
                                icon: 'info',
                                title: 'Data Tidak Ditemukan',
                                text: 'Data personnel dengan NRP ' + registerNumber +
                                    ' tidak ditemukan',
                                showCancelButton: true,
                                cancelButtonText: 'Tutup',
                                confirmButtonText: 'Tambah Personnel Baru',

                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // redirect to create new data
                                    window.location.href =
                                        "{{ route('personnel.create', ['policeId' => $currentPoliceId]) }}";
                                }
                            });
                        }
                    }
                });

            });
        });

        $(document).ready(function() {
            $('#policeSearch').change(function() {
                var policeId = $(this).val();

                if (policeId != '') {
                    window.location.href = "{{ route('personnel.index') }}" + '?policeId=' + policeId;
                }
            });
        });
    </script>
@endpush
