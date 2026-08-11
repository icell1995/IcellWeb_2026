@php
    $_title = 'Daftar Personel';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
@endpush

@section('content')
    <div class="loaderbg" style="display:none"></div>

    <div class="box">
        <div class="box-header">
            <h3 class="fw-bold text-blue-dark"> Daftar Anggota - SAT LANTAS
                {{ Auth::user()->role_id == 3 ? $currentPolice->full_name : '' }} (ADMIN)</h3>
        </div>
        <div class="boxy-body mt-4">
            <div class="d-flex justify-content-between">
                <div class="col-3">
                    <select class="form-control select2" id="policeSearch" name="policeSearch">
                        @if (Auth::user()->role_id == 1)
                            <option value="">Pilih Satker</option>
                            @foreach ($polices as $police)
                                <option value="{{ $police->id }}"
                                    @if ($currentPoliceId == $police->id) {{ 'selected' }} @endif>{{ $police->name }}
                                </option>
                                @foreach ($police->children->where('is_active', true) as $child)
                                    <option value="{{ $child->id }}"
                                        @if ($currentPoliceId == $child->id) {{ 'selected' }} @endif>- {{ $child->name }}
                                    </option>
                                @endforeach
                            @endforeach
                        @elseif(Auth::user()->role_id == 3)
                            @foreach ($polices as $police)
                                <option value="{{ $police->id }}"
                                    @if ($currentPoliceId == $police->id) {{ 'selected' }} @endif>{{ $police->full_name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-3 text-end">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#checkOfficerModal" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Tambah Anggota
                    </a>
                </div>
            </div>
            <div class="mt-3 table-responsive">
                <table class="table table-striped table-bordered table-users dataTable" name="dataTable" width="100%">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Nama</th>
                            <th class="text-center">NRP</th>
                            <th class="text-center">Pangkat</th>
                            <th class="text-center">Jabatan</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $iteration = 1;
                        @endphp

                        @if (!empty($officers))
                            @foreach ($officers as $officer)
                                @if ($officer->status == 'PRESENT')
                                    <tr>
                                        <td class="text-center align-middle">
                                            {{ $iteration }}
                                        </td>
                                        <td class="text-center align-middle">
                                            {{ $officer->full_name }}
                                        </td>
                                        <td class="text-center align-middle">
                                            {{ $officer->register_number }}
                                        </td>
                                        <td class="text-center align-middle">
                                            {{ $officer->rank->name ?? '-' }}
                                        </td>
                                        <td class="text-center align-middle">
                                            {{ $officer->position()->first()->name ?? '-' }}
                                        </td>
                                        <td class="text-center align-middle">
                                            @if ($officer->is_valid == false)
                                                <div class="d-grid gap-2">
                                                    <button type="button"
                                                        class="btn btn-sm btn-warning"
                                                        disabled><b>PERSONNEL BELUM DIVALIDASI OLEH HELPDESK</b></button>
                                                </div>
                                            @else
                                                <div class="d-grid gap-2">
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        disabled>Login terakhir : 05 Aug 2023 08:55:18</button>
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        disabled>Kata Kunci: KATA KUNCI SUDAH DI GANTI</button>
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        disabled>Data diganti oleh : 11012205, Waktu : 21 Feb 2023
                                                        04:27:31</button>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">
                                            @php
                                                $positionClusterId = $officer->position()->first()->position_cluster_id ?? '';
                                            @endphp

                                            @if (($positionClusterId != '6' && $positionClusterId != '12') || Auth::user()->role_id == 1)
                                                <a href="{{ route('personnel.edit', ['id' => $officer->id, 'policeId' => $officer->police_id]) }}"
                                                    class="btn btn-primary m-1">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </a>

                                                <a href="{{ route('personnel.move', ['id' => $officer->id, 'policeId' => $officer->police_id]) }}"
                                                    class="btn btn-success m-1">
                                                    <i class="bi bi-pencil-square"></i> Mutasi
                                                </a>

                                                {{-- <a href="" class="btn btn-warning m-1">
                                                    <i class="fa fa-folder"></i> Lihat
                                                </a> --}}
                                            @endif

                                            @if (!empty($officer->user))
                                                <a href="{{ route('personnel.change-password', ['id' => $officer->id, 'policeId' => $officer->police_id]) }}"
                                                    class="btn btn-danger m-1">
                                                    <i class="bi bi-pencil-square"></i> Ubah Password
                                                </a>
                                            @endif


                                            @if (Auth::user()->role_id == 1)
                                                @if ($officer->is_valid == false)
                                                    <a href="{{ '' }}" class="btn btn-secondary m-1">
                                                        <i class="bi bi-check-circle"></i> Validasi
                                                    </a>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endif

                                @php
                                    $iteration++;
                                @endphp
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

        </div>
    </div>


    <!-- Check Officer-->
    <div class="modal fade" id="checkOfficerModal" tabindex="-1" role="dialog" aria-labelledby="checkOfficerModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content" id="modalContent">
                <!-- Header Modal -->
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-blue-dark" id="checkOfficerModalLabel">Periksa Data Anggota</h5>
                </div>

                <!-- Body Modal -->
                <div class="modal-body">
                    <form id="checkOfficerForm">
                        <div class="mb-3">
                            <label class="fw-bold" for="registerNumber">NRP</label>
                            <input type="text" class="form-control" id="registerNumber" placeholder="Masukkan NRP">
                        </div>
                    </form>
                </div>

                <!-- Footer Modal -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark-blue" id="saveCheckOfficerForm"><i class="bi bi-save"></i>
                        Submit</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="bi bi-x-circle"></i>
                        Batal</button>
                </div>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Delete Button -->
    <script src="{{ asset('js/laravel.js') }}"></script>

    <script>
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
                        console.log(data);
                        if (response.code == '200') {
                            var positionName = (data.position) ? data.position.name : '-';

                            var infoMessages = 'Nama: ' + data.full_name + '<br>';
                            infoMessages += 'Satker: ' + data.police.full_name + '<br>';
                            infoMessages += 'Jabatan: ' + positionName + '<br>';
                            infoMessages += '<br>';
                            infoMessages += 'Dengan Admin Satker: ....<br>';
                            infoMessages += 'No Telp: ....<br>';

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
                                icon: 'error',
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
