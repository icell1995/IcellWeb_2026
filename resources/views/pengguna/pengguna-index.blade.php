@extends('layouts.app')

@section('content')
    <div class="box">
        <div class="box-header">
            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>Ups! </strong> Ada beberapa masalah dengan pengisian form yang Anda masukkan.
                    <ul class="m-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <h3 class="text-blue-dark fw-semibold mb-2">Daftar Pengguna</h3>
            <fieldset id="search-filter" class="border rounded-3 p-3">
                <span class="text-danger">* Pilih Kategori Yang Dinginkan</span>
                <form class="row mt-2" action="{{ route('searchUser') }}" method="get">
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12 mb-3">
                        <input type="search" id="name" name="search" class="form-control" placeholder="Nama / NRP">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12 mb-3">
                        <select id="status-select" class="form-select filter" name="state">
                            <option value="" selected>Pilih Status</option>
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12 mb-3">
                        <select id="polda" name="polda" class="form-select @error('polda') is-invalid @enderror">
                            @if (Auth::user()->role_id == 1)
                                <option value="-" selected>Semua Polda</option>
                            @endif
                            @foreach ($polda as $poldas)
                                <option value="{{ $poldas->id }}" {{ old('polda_id') == $poldas->id ? 'selected' : '' }}>
                                    {{ $poldas->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12 mb-3">
                        <select id="polres" name="polres" class="form-select @error('polres') is-invalid @enderror">
                            @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                                <option value="-" selected>Pilih Polres</option>
                            @endif
                            @foreach ($polres as $polress)
                                {{-- <option value="{{ $polress->id }}" {{ old('polres_id')==$polress->id ? 'selected' : ''
                            }}>
                            {{ $polress->name }}
                        </option> --}}
                                <option value="{{ str_pad((string) $polress->id, 4, '0', STR_PAD_LEFT) }}"
                                    {{ old('polres_id') == str_pad((string) $polress->id, 4, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ $polress->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="text-start">
                        <button type="submit" id="search_btn" class="btn btn-dark-blue">{{ __('Cari') }}</button>
                        {{-- <button type="submit" id="reset_btn" class="btn btn-warning">{{ __('Reset') }}</button> --}}
                    </div>
                </form>
            </fieldset>
        </div>
        <div class="box-body">
            <div class="table-responsive mt-3">
                <table class="table table-bordered table-officer" cellspacing="0" width="100%" id="dataTable"
                    name="dataTable">
                    <thead>
                        <tr>
                            <th class="text-center align-middle">No</th>
                            <th class="text-center align-middle">Username</th>
                            <th class="text-center align-middle">Nama Depan</th>
                            <th class="text-center align-middle">Nama Belakang</th>
                            <th class="text-center align-middle">Email</th>
                            <th class="text-center align-middle">Level</th>
                            <th class="text-center align-middle">Polda</th>
                            <th class="text-center align-middle">Polres</th>
                            <th class="text-center align-middle">Status</th>
                            <th class="text-center align-middle">Action</th>
                        </tr>
                    </thead>
                    <?php $no = 0; ?>

                    @foreach ($pengguna as $key => $penggunas)
                        <?php $no++; ?>
                        <tbody>
                            <td class="text-center">{{ $pengguna->firstItem() + $key }}</td>
                            <td>{{ $penggunas->username }}</td>
                            <td>{{ $penggunas->first_name }}</td>
                            <td>{{ $penggunas->last_name }}</td>
                            <td>{{ $penggunas->email }}</td>
                            <td>{{ $penggunas->role->name }}</td>
                            @if ($penggunas->polda_id == null)
                                <td>-</td>
                            @else
                                <td>{{ $penggunas->polda->name }}</td>
                            @endif

                            @if ($penggunas->polres_id == null)
                                <td>-</td>
                            @else
                                <td>{{ $penggunas->polres->name }}</td>
                            @endif
                            @if ($penggunas->state == 1)
                                <td class="text-center"><span class="active">Aktif</span></td>
                            @else
                                <td class="text-center"><span class="inactive">Tidak Aktif</span></td>
                            @endif
                            <td class="text-center">

                                {{-- <button type="button" class="btn btn-edit">
                                    <a href="/pengguna/pengguna-edit/{{ $penggunas->id }}">Edit</a>
                                </button> --}}
                                @if (Auth::user()->role_id != 4)
                                    <button type="button" class="btn btn-secondary btn-edit" data-bs-toggle="modal"
                                        data-bs-target="#edit-data" data-id="{{ $penggunas->id }}">
                                        Edit
                                    </button>
                                @endif
                                @if (Auth::user()->role_id != 4)
                                    @if ($penggunas->state == 1)
                                        <button type="button" class="btn btn-danger mb-1"><a
                                                class="text-decoration-none text-white"
                                                href="/pengguna/pengguna_delete/{{ $penggunas->id }}">Non
                                                Aktifkan</a></button>
                                    @else
                                        <button type="button" class="btn btn-success mb-1"><a
                                                class="text-decoration-none text-white"
                                                href="/pengguna/pengguna_delete/{{ $penggunas->id }}">Aktifkan</a></button>
                                    @endif
                                @endif
                            </td>
                        </tbody>
                    @endforeach
                </table>
            </div>

            {{ $pengguna->links() }}

            <div class="text-end">
                <button type="button" class="material-icons floating-btn" data-bs-toggle="modal"
                    data-bs-target="#add-data">add</button>
            </div>
        </div>

        {{-- Modal Add --}}
        @include('pengguna.modal.pengguna-modal-add')
        {{-- End Modal Add --}}

        {{-- Modal Edit --}}
        @include('pengguna.modal.pengguna-modal-edit')
        {{-- End Modal Edit --}}
    </div>

    @push('script')
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

                $.get('{{ url('pengguna/polres_list') }}/' + poldaId, function(data) {

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

@endsection
