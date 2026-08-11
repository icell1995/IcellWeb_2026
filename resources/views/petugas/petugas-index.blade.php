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
            <h3 class="text-blue-dark fw-semibold mb-2">Daftar Penyidik</h3>
            {{-- <button type="button" class="btn btn-dark-blue mb-3">Filter Pencarian</button> --}}
            <fieldset id="search-filter" class="border rounded-3 p-3">
                <span class="text-danger">* Pilih Kategori Yang Diinginkan</span>
                <form class="row mt-2" action="{{ route('searchOfficer') }}" method="get">
                    {{-- <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-3">
                        <input type="search" id="name" name="search" class="form-control" placeholder="Nama / NRP"
                            value="{{ old('search') }}">
                    </div> --}}
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-3">
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
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-3">
                        <select id="polres" name="polres" class="form-select @error('polres') is-invalid @enderror">
                            @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                                <option value="-" selected>Pilih Polres</option>
                            @endif
                            @foreach ($polres as $polress)
                                <option value="{{ str_pad((string) $polress->id, 4, '0', STR_PAD_LEFT) }}"
                                    {{ old('polres_id') == str_pad((string) $polress->id, 4, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ $polress->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-3">
                        <select name="head_officer" id="head-officer" class="form-select">
                            <option selected value=->Sebagai Kepala</option>
                            <option value="KANIT LAKA">KANIT GAKKUM</option>
                            <option value="KASAT LAKA">KASAT LANTAS</option>
                            <option value="KASI LAKA">KASI LAKA</option>
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-3">
                        <select name="position" id="position" class="form-select">
                            <option selected value=null>Posisi Sebagai</option>
                            <option value="ADMIN">ADMIN</option>
                            <option value="PENYIDIK">PENYIDIK</option>
                            <option value="PENYIDIK PEMBANTU">PENYIDIK PEMBANTU</option>
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-3">
                        <select id="status-select" name="state" class="form-select state">
                            <option value="-">Pilih Status</option>
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
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
                <table class="table table-bordered table-officer" id="dataTable" name="dataTable">
                    <thead>
                        <tr>
                            <th class="text-center align-middle">No</th>
                            <th class="text-center align-middle tbl">Nama Depan</th>
                            <th class="text-center align-middle tbl">Nama Belakang</th>
                            <th class="text-center align-middle">NRP</th>
                            <th class="text-center align-middle">Pangkat</th>
                            <th class="text-center align-middle tbl">Posisi</th>
                            <th class="text-center align-middle tbl">Polres</th>
                            <th class="text-center align-middle">Sebagai Kepala</th>
                            <th class="text-center align-middle">Status</th>
                            <th class="text-center align-middle">Action</th>
                        </tr>
                    </thead>
                    <?php $no = 0; ?>

                    <tbody>
                        @foreach ($petugas as $index => $petugass)
                            {{-- <?php $no++; ?> --}}
                            <tr>
                                <td class="text-center" scope="row">
                                    {{-- {{ $petugas->perPage() - $petugas->perPage() + ($index + 1) }} --}}{{ $index + 1 }}
                                </td>
                                <td>{{ $petugass->first_name }}</td>
                                <td>{{ $petugass->last_name }}</td>
                                <td>{{ $petugass->id }}</td>
                                <td>{{ $petugass->rank_id }}</td>
                                <td>{{ $petugass->position_short_name }}</td>
                                <td>{{ $petugass->polres_name }}</td>
                                @if ($petugass->sebagai_kepala == null)
                                    <td>-</td>
                                @else
                                    <td>{{ $petugass->sebagai_kepala }}</td>
                                @endif
                                @if ($petugass->officer_state == 1)
                                    <td class="text-center"><span class="active">Aktif</span></td>
                                @else
                                    <td class="text-center"><span class="inactive">Tidak Aktif</span></td>
                                @endif
                                <td style="text-align: center">
                                    {{-- @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2) --}}
                                    {{-- <button type="button" class="btn btn-edit">
                            <a href="/petugas/petugas-edit/{{ $petugass->id }}">Edit</a>
                        </button> --}}
                                    <button type="button" class="btn btn-edit btn-secondary mb-1" data-bs-toggle="modal"
                                        data-bs-target="#edit-data" data-id="{{ $petugass->id }}">
                                        Edit
                                    </button>

                                    @if ($petugass->officer_state == 1)
                                        <button type="button" class="btn btn-danger mb-1"><a
                                                class="text-decoration-none text-white"
                                                href="/petugas/petugas_delete/{{ $petugass->id }}">Non
                                                Aktifkan</a></button>
                                    @else
                                        <button type="button" class="btn btn-success mb-1"><a
                                                class="text-decoration-none text-white"
                                                href="/petugas/petugas_delete/{{ $petugass->id }}">Aktifkan</a></button>
                                    @endif
                                    {{-- @endif --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- {{ $petugas->links() }} --}}
            {{-- {{ $petugas->appends(request()->query())->links('pagination::bootstrap-5') }} --}}

            <div class="text-end mt-2">
                <button type="button" class="material-icons floating-btn" data-bs-toggle="modal"
                    data-bs-target="#add-data">add</button>
            </div>
        </div>

        {{-- Modal Add --}}
        @include('petugas.modal.petugas-modal-add')
        {{-- End Modal Add --}}

        {{-- Modal Edit --}}
        @include('petugas.modal.petugas-modal-edit')
        {{-- End Modal Edit --}}
    </div>

    @push('script')
        {{-- <script src="{{ asset('js/petugas.js') }}"></script> --}}
        <script type="text/javascript">
            $(document).ready(function() {
                $('#dataTable').DataTable();
            });

            $(document).ready(function() {
                var get_pol = $('#polda').val();
                if (get_pol == null || get_pol == '-') {
                    $('#polres').prop('disabled', true);
                } else {
                    $('#polres').prop('disabled', false);
                    $('#polres_id').prop('disabled', false);
                }
                var user = "{{ $user }}";
                $("#status-select").val("{{ $status }}");
                if (user == 1) {
                    $("#polda").val("{{ $polda_input }}");
                    $("#polres").val("{{ $polres_input }}");
                } else if (user == 2) {
                    $('#polda').val('{{ $polda_input }}');
                    $('#polres').val('{{ $polres_input }}');
                }
            });

            $('#search_btn').on('click', function() {
                $('#polres').prop('disabled', false);
                var url = '{{ route('searchOfficer') }}';

            });
            $('#polda').on('change', function(event) {
                event.preventDefault();
                var poldaId = $(this).val();
                $('#polres').prop('disabled', true);
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
                        // var option  = '<option value="' + String(id).padStart(4, '0')  + '">' + name + '</option>';
                        $('#polres').prop('disabled', false);
                        $('#polres').append(option);

                    });
                });
            });
            $(function() {
                $('.btn-edit').on("click", function() {
                    var _token = $("input[name='_token']").val();
                    var id = $(this).data('id');

                    $.ajax({
                        url: "{{ route('edit_modal_petugas') }}",
                        type: 'GET',
                        data: {
                            _token: _token,
                            id: id
                        },
                        success: function(data) {
                            console.log(data);
                            $('#nrp_editt').val(data.officer.id);
                            $('#nrp_edit').val(data.officer.id);
                            $('#first_name_edit').val(data.officer.first_name);
                            $('#last_name_edit').val(data.officer.last_name);
                            $('#posisi_edit').val(data.officer.position);
                            $('#polda_id_edit').val(data.officer.polda_id);
                            $('#polres_id_edit').val(data.officer.polres_id);
                            $('#kepala_edit').val(data.officer.sebagai_kepala);
                            $('#pangkat_edit').val(data.officer.rank_id);
                            $('modal').show();

                            $('#polda_id_edit').empty();
                            $('#polres_id_edit').empty();

                            $.each(data.polda, function(key, b) {
                                if (data.officer.polda_id == b.id) {
                                    $('#polda_id_edit').append(' <option value=' + b.id +
                                        ' selected>' + b.name + ' </option>').trigger(
                                        'change');
                                } else {
                                    $('#polda_id_edit').append(' <option value=' + b.id +
                                        '>' + b.name + ' </option>');
                                }
                            });

                            $.each(data.polres, function(key, b) {
                                if (data.officer.polres_id == b.id) {
                                    $('#polres_id_edit').append(' <option value=' + b.id +
                                        ' selected>' + b.name + ' </option>').trigger(
                                        'change');
                                } else {
                                    $('#polres_id_edit').append(' <option value=' + b.id +
                                        '>' + b.name + ' </option>');
                                }
                            });
                        }
                    });
                });
            });

            $('#add-data').on('hidden.bs.modal', function() {
                $('#add-data form')[0].reset();
            });
            //ADD Petugas

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
                        if (polres.polda_id == poldaId) {
                            var id = polres.id;
                            var name = polres.name;
                            var option = '<option value="' + id + '">' + name + '</option>';
                            $('#polres_id_edit').append(option);
                        }

                        // var id   = polres.id;
                        // var name = polres.name;
                        // var option  = '<option value="' + id  + '">' + name + '</option>';
                        // // var option  = '<option value="' + String(id).padStart(4, '0')  + '">' + name + '</option>';

                        // $('#polres_id_edit').append(option);
                    });

                    // $('#polres', _form).prop('disabled', false);
                });
            });
            $("#btn-export").click(function() {
                // alert("Hi");
                var polda = $("#polda").val();
                var polres = $("#polres").val();
                var state = $("#status-select").val();
                window.location = "{{ route('export_petugas') }}" + '?polda=' + polda + '&polres=' + polres +
                    '&state=' + state;
            });
        </script>
    @endpush
@endsection
