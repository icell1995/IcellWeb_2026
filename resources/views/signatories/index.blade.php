@php
    $_title = 'Pejabat TTE';
@endphp

@extends('layouts.app')


@section('content')
    <div class="box">
        <div class="box-header">
            <h3 class="text-blue-dark fw-bold mb-2">Daftar Pejabat TTE</h3>
            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>Ups! </strong> Ada beberapa masalah dengan pengisian form yang Anda masukkan.
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        <!-- /.box-header -->
        <!-- /.table-responsive -->
        <div class="box-body">
            <div class="my-2">
                <a href="{{ route('signatories.create') }}" id="addSignatory" class="btn btn-primary"><i
                        class="bi bi-plus-circle"></i> Create</a>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-striped table-bordered table-users" id="dataTable" name="dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>NRP</th>
                            <th>NIK</th>
                            <th>Jabatan</th>
                            <th>Pangkat</th>
                            <th>Polda</th>
                            <th>Polres</th>
                            <th>Confirm Form</th>
                            <th>Valid</th>
                            <th>Telp</th>
                            <th>Email</th>
                            <th>Opsi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($signatories as $signatory)
                            @php
                                $identityNumberYear = substr($signatory->identity_number, 10, 2);
                                $registerNumberYear = substr($signatory->register_number, 0, 2);
                                $identityNumberMonth = substr($signatory->identity_number, 8, 2);
                                $registerNumberMonth = substr($signatory->register_number, 2, 2);
                            @endphp
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $signatory->first_title . ' ' . $signatory->first_name . ' ' . $signatory->last_name . ', ' . $signatory->last_title }}
                                </td>
                                <td
                                    class="text-center @if ($signatory->identity_number != null && $signatory->register_number != null) @if ($identityNumberYear != $registerNumberYear || $identityNumberMonth != $registerNumberMonth){{ 'bg-danger' }} @endif @endif">
                                    {{ $signatory->register_number }}
                                    <br>
                                    @if ($signatory->identity_number != null && $signatory->register_number != null)
                                        @if ($identityNumberYear != $registerNumberYear || $identityNumberMonth != $registerNumberMonth) <small
                                                class="text-white"> {{ 'Periksa Bulan & Tahun NIK dan NRP' }} </small>
                                        @endif
                                    @endif
                                </td>
                                <td
                                    class="text-center @if ($signatory->identity_number != null && $signatory->register_number != null) @if ($identityNumberYear != $registerNumberYear || $identityNumberMonth != $registerNumberMonth){{ 'bg-danger' }} @endif @endif">
                                    {{ $signatory->identity_number }}
                                    <br>
                                    @if ($signatory->identity_number != null && $signatory->register_number != null)
                                        @if ($identityNumberYear != $registerNumberYear || $identityNumberMonth != $registerNumberMonth) <small
                                                class="text-white"> {{ 'Periksa Bulan & Tahun NIK dan NRP' }} </small>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-center">{{ $signatory->position_id }}</td>
                                <td class="text-center">{{ $signatory->rank_id }}</td>
                                <td class="text-center">{{ $signatory->polres->polda->name }}</td>
                                <td class="text-center">{{ $signatory->polres->name }}</td>
                                <td class="text-center">
                                    @if ($signatory->polres->is_complete == false)
                                        @if ($signatory->polres->address == null)
                                            <span class="badge badge-warning">Active!</span>
                                            <small class="text-muted">Alamat Belum Diisi (Form Tidak Aktif)</small>
                                        @else
                                            <span class="badge badge-success">Active</span>
                                        @endif
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($signatory->valid == true)
                                        <span class="badge badge-success">Valid</span>
                                    @else
                                        <span class="badge badge-danger">Invalid</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $signatory->phone }}</td>
                                <td class="text-center">{{ $signatory->email }}</td>
                                <td class="text-center">
                                    <a href="{{ route('signatories.edit', $signatory->id) }}"
                                        class="btn btn-warning btn-sm m-1"><i class="bi bi-pencil-square"></i></a>
                                    <form action="{{ route('signatories.destroy', $signatory->id) }}" method="POST"
                                        style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm m-1"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#dataTable').DataTable({
                responsive: true,
                // stateSave: true
            });
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            var get_pol = $('#polda').val();
            if (get_pol == null || get_pol == '-' || get_pol == "") {
                $('#polres').prop('disabled', true);
            } else {
                $('#polres').prop('disabled', false);
                $('#polres_id').prop('disabled', false);
            }
        });
        $('#polda').on('change', function(event) {
            event.preventDefault();
            var poldaId = $(this).val();
            $('#polres').prop('disabled', true);
            $('#polres_id').prop('disabled', true);
            $('#polres').empty();
            $('#polres').append('<option value="">Pilih Polres</option>');
            if (!poldaId) {
                return;
            }

            $.get('{{ url(' pengguna / polres_list ') }}/' + poldaId, function(data) {

                $('#polres').empty()
                var option = '<option value="">Pilih Polres</option>';
                $('#polres').append(option);
                $.each(data, function(key, polres) {

                    var id = polres.id;
                    var name = polres.name;
                    var option = '<option value="' + id + '">' + name + '</option>';
                    // var option = '<option value="' + String(id).padStart(4, '0') + '">' + name + '</option>';
                    $('#polres').prop('disabled', false);
                    $('#polres').append(option);
                });
            });
        });

        // const selected = document.querySelector(".status-title");
        // const optionsContainer = document.querySelector(".status-item");

        // const optionsList = document.querySelectorAll(".item");

        // selected.addEventListener("click", () => {
        //     optionsContainer.classList.toggle("active");
        // });

        // optionsList.forEach(o =>{
        //     o.addEventListener("click", ()=> {
        //         selected.innerHTML = o.querySelector("label").innerHTML;
        //         optionsContainer.classList.remove("active");
        //     })
        // });

        $(function() {
            // $('#role_id').val('');
            $('.btn-edit').on("click", function() {
                var _token = $("input[name='_token']").val();
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ route('edit_modal_pengguna') }}",
                    type: 'GET',
                    data: {
                        _token: _token,
                        id: id
                    },
                    success: function(data) {
                        // var role='';
                        console.log(data);
                        // $('#role_id').remove();
                        $('#id_edit').val(data.users.id);
                        $('#username_edit').val(data.users.username);
                        $('#first_name_edit').val(data.users.first_name);
                        $('#last_name_edit').val(data.users.last_name);
                        $('#role_id_edit').val(data.users.role_id);
                        $('#email_edit').val(data.users.email);
                        $('#phone_edit').val(data.users.phone);
                        $('#polda_id_edit').val(data.users.polda_id);
                        $('#polres_id_edit').val(data.users.polres_id);
                        $('#pangkat_edit').val(data.users.pangkat);
                        $('modal').show();

                        // var dropdown= '<select id="role_id" class="form-control @error('role_id') is-invalid @enderror name="role_id">'
                        //     $('.role-id-dropdown').append(dropdown);
                        $('#role_id_edit').empty();
                        $('#polda_id_edit').empty();
                        $('#polres_id_edit').empty();

                        $.each(data.role, function(key, b) {
                            if (data.users.role_id == b.id) {
                                $('#role_id_edit').append(' <option value=' + b.id +
                                    ' selected>' + b.name + ' </option>').trigger(
                                    'change');
                            } else {
                                $('#role_id_edit').append(' <option value=' + b.id +
                                    '>' + b.name + ' </option>');
                            }
                        });

                        $.each(data.polda, function(key, b) {
                            if (data.users.polda_id == b.id) {
                                $('#polda_id_edit').append(' <option value=' + b.id +
                                    ' selected>' + b.name + ' </option>').trigger(
                                    'change');
                            } else {
                                $('#polda_id_edit').append(' <option value=' + b.id +
                                    '>' + b.name + ' </option>');
                            }
                        });

                        $.each(data.polres, function(key, b) {
                            if (data.users.polres_id == b.id) {
                                $('#polres_id_edit').append(' <option value=' + b.id +
                                    ' selected>' + b.name + ' </option>').trigger(
                                    'change');
                            } else {
                                $('#polres_id_edit').append(' <option value=' + b.id +
                                    '>' + b.name + ' </option>');
                            }
                        });


                        // $('#role_id').append(role);
                        //menampilkan kolom polda dan polres saat load awal view_edit
                        showByRoleEdit($(':input#role_id_edit').val())

                        // function showByRoleEdit(roleId) {
                        var value = $('#role_id_edit').val();
                        switch (value) {
                            case '1':
                                $('#poldas-name_edit').hide();
                                $('#polress-name_edit').hide();
                                break;
                            case '2':
                                $('#poldas-name_edit').show();
                                $('#polress-name_edit').hide();
                                break;
                            case '3':
                                $('#poldas-name_edit').show();
                                $('#polress-name_edit').show();
                                break;
                            case '4':
                                $('#poldas-name_edit').show();
                                $('#polress-name_edit').show();
                                break;
                            case '5':
                                $('#poldas-name_edit').show();
                                $('#polress-name_edit').show();
                            default:
                                $('#poldas-name_edit').hide();
                                $('#polress-name_edit').hide();
                                break;
                        }
                        // }
                    }
                })
            });
        });

        $(document).ready(function() {

            $(':input#role_id_edit').change(function(event) {
                event.preventDefault()
                var value = $(this).val();
                // $('#changer').html(value);
                switch (value) {
                    case '1':
                        $('#poldas-name_edit').hide();
                        $('#polress-name_edit').hide();
                        break;
                    case '2':
                        $('#poldas-name_edit').show();
                        $('#polress-name_edit').hide();
                        break;
                    case '3':
                        $('#poldas-name_edit').show();
                        $('#polress-name_edit').show();
                        break;
                    case '4':
                        $('#poldas-name_edit').show();
                        $('#polress-name_edit').show();
                        break;
                    case '5':
                        $('#poldas-name_edit').show();
                        $('#polress-name_edit').show();
                        break;
                    default:
                        $('#poldas-name_edit').hide();
                        $('#polress-name_edit').hide();
                        break;
                }
                showByRoleEdit(value)
            });

            function showByRoleEdit(roleId) {
                var value = $('#role_id_edit').val();
                switch (value) {
                    case '1':
                        $('#poldas-name_edit').hide();
                        $('#polress-name_edit').hide();
                        break;
                    case '2':
                        $('#poldas-name_edit').show();
                        $('#polress-name_edit').hide();
                        break;
                    case '3':
                        $('#poldas-name_edit').show();
                        $('#polress-name_edit').show();
                        break;
                    case '4':
                        $('#poldas-name_edit').show();
                        $('#polress-name_edit').show();
                        break;
                    case '5':
                        $('#poldas-name_edit').show();
                        $('#polress-name_edit').show();
                        break;
                    default:
                        $('#poldas-name_edit').hide();
                        $('#polress-name_edit').hide();
                        break;
                }
            }
        });
        //END EDIT PENGGUNA

        //ADD PENGGUNA

        //untuk menampilkan atau sembunyikan dropdown
        $(':input#role_id_add').change(function(event) {
            event.preventDefault()
            var value = $(this).val();
            // $('#changer').html(value);
            switch (value) {
                case '1':
                    $('#poldas-name_add').hide();
                    $('#polress-name_add').hide();
                    break;
                case '2':
                    $('#poldas-name_add').show();
                    $('#polress-name_add').hide();
                    break;
                case '3':
                    $('#poldas-name_add').show();
                    $('#polress-name_add').show();
                    break;
                case '4':
                    $('#poldas-name_add').show();
                    $('#polress-name_add').show();
                    break;
                case '5':
                    $('#poldas-name_add').show();
                    $('#polress-name_add').show();
                    break;
                default:
                    $('#poldas-name_add').hide();
                    $('#polress-name_add').hide();
                    break;
            }
            showByRole(value)
        });

        //menampilkan kolom polda dan polres saat load awal view
        function showByRole(roleId) {
            var value = $('#role_id_add').val();
            switch (value) {
                case '1':
                    $('#poldas-name_add').hide();
                    $('#polress-name_add').hide();
                    break;
                case '2':
                    $('#poldas-name_add').show();
                    $('#polress-name_add').hide();
                    break;
                case '3':
                    $('#poldas-name_add').show();
                    $('#polress-name_add').show();
                    break;
                case '4':
                    $('#poldas-name_add').show();
                    $('#polress-name_add').show();
                    break;
                case '5':
                    $('#poldas-name_add').show();
                    $('#polress-name_add').show();
                    break;
                default:
                    $('#poldas-name_add').hide();
                    $('#polress-name_add').hide();
                    break;
            }
        }

        showByRole($(':input#role_id_add').val())

        // menampilkan dropdown polres sesua pilihan polda
        $('#polda_id_add').on('change', function(event) {
            event.preventDefault();
            var poldaId = $(this).val();
            $('#polres_id_add').empty();
            $('#polres_id_add').append('<option value="">Pilih Polres</option>');
            if (!poldaId) {
                return;
            }

            $.get('{{ url('pengguna/polres_list') }}/' + poldaId, function(data) {

                $('#polres_id_add').empty()
                var option = '<option value="">Pilih Polres</option>';
                $('#polres_id_add').append(option);
                $.each(data, function(key, polres) {

                    var id = polres.id;
                    var name = polres.name;
                    var option = '<option value="' + id + '">' + name + '</option>';
                    // var option  = '<option value="' + String(id).padStart(4, '0') + '">' + name + '</option>';

                    $('#polres_id_add').append(option);
                });

                // $('#polres', _form).prop('disabled', false);
            });

        });

        $('#polda_id_edit').on('change', function(event) {
            event.preventDefault();
            var poldaId = $(this).val();
            $('#polres_id_edit').empty();
            $('#polres_id_edit').append('<option value="">Pilih Polres</option>');
            if (!poldaId) {
                return;
            }

            $.get('{{ url('pengguna/polres_list') }}/' + poldaId, function(data) {

                $('#polres_id_edit').empty()
                var option = '<option value="">Pilih Polres</option>';
                $('#polres_id_edit').append(option);
                $.each(data, function(key, polres) {

                    var id = polres.id;
                    var name = polres.name;
                    var option = '<option value="' + id + '">' + name + '</option>';
                    // var option  = '<option value="' + String(id).padStart(4, '0') + '">' + name + '</option>';

                    $('#polres_id_edit').append(option);
                });

                // $('#polres', _form).prop('disabled', false);
            });

        });
    </script>
@endpush
