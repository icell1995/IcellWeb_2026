@php
    $_title = 'Daftar Pejabat TTE Valid';
@endphp

@extends('cms.layouts.app')

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
            <h3 class="fw-bold text-blue-dark"> Check Officer Digital Signature
                {{ $userRoleId == 3 ? $currentPolice->full_name : '' }}</h3>
        </div>
        <div class="boxy-body mt-4">
            <div class="alert alert-danger" id="attentionBox">
                <div class="text-center">
                    <b>
                        PERHATIAN !<br />
                        <br />
                        NAMA PEJABAT BARU AKAN MUNCUL JIKA SUDAH PERNAH MELAKUKAN TANDA TANGAN ELEKTRONIK SPDP DI APLIKASI ICELL
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

                            <th class="text-center">NIK</th>
                            <th class="text-center">Passphrase</th>

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

                                        <td class="text-center align-middle">
                                            {{ $user->officer->identity_number ?? '-' }}
                                        </td>
                                        <td class="text-center align-middle">
                                            {{ $user->officer->passphrase ?? '-' }}
                                        </td>

                                        <td class="text-center align-middle">
                                            @php
                                                $positionClusterId = $user->officer->position->position_cluster_id ?? '';
                                            @endphp

                                            <button type="button" class="btn btn-warning m-1 test-hit-tte" 
                                                data-user-id="{{ $user->officer->user_id }}">
                                                <i class="bi bi-send"></i> Test Hit TTE
                                            </button>
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
            $('.test-hit-tte').on('click', function (e) {
                var userId = $(this).data('user-id');

                //sweetalert input passphrase
                Swal.fire({
                    title: 'Masukkan Passphrase',
                    input: 'text',
                    showCancelButton: true,
                    confirmButtonText: 'Konfirmasi Sekarang',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    showLoaderOnConfirm: true,
                    preConfirm: (passphrase) => {
                        $('#passphrase').val(passphrase);

                        Swal.fire({
                            icon: 'info',
                            title: 'Mohon Menunggu...',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            onBeforeOpen: () => {
                                Swal.showLoading();
                            },
                        });

                        //ajax
                        $.ajax({
                            url: "{{route('cms.check-officer-digital-signature.api.test')}}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                passphrase: passphrase,
                                user_id: userId
                            },
                            success: function (response) {
                                console.log(response.data);
                                var data = response.data;

                                Swal.close();

                                if(data.message == 'SUCCESS') {
                                    return Swal.fire({
                                        icon: 'success',
                                        title: 'Test Berhasil',
                                        text: data.message,
                                        showConfirmButton: true,
                                        confirmButtonText: 'Lanjut',
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            // redirect to document signature index
                                            window.location.href = "{{route('home')}}";
                                        }
                                    });
                                } else {
                                    return false;
                                }
                            },
                            error: function (xhr) {
                                Swal.close();

                                var response = JSON.parse(xhr.responseText);
                                
                                if(response.code == 400){
                                    return Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: response.data.message,
                                    });
                                }else if(response.code == 500){
                                    return Swal.fire({
                                        icon: 'error',
                                        title: 'Maaf, Terjadi Kesalahan',
                                        text: response.message,
                                    });
                                }

                                return false;
                            }
                        });
                    }
                });
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
    </script>
@endpush
