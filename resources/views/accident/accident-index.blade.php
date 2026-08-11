@php
    $_title = 'Register Perkara';
@endphp

@extends('layouts.app')

@section('title', $_title)

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
            <h3 class="text-blue-dark fw-semibold mb-2">Register Perkara</h3>

            <fieldset id="search-filter" class="border rounded-3 p-3">
                <span class="text-danger">*Pilih Kategori Yang Diinginkan</span>
                <form class="row mt-2" method="GET" action="{{ route('search_accident') }}">
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12 mb-3">
                        <label for="polda" class="fw-semibold fs-6 mb-1">Polda</label>
                        {{-- <span id="get_user" style="">{{ Auth::user()->role_id }}</span>
                    <span id="get_polda" style="">{{ Auth::user()->polda_id }}</span> --}}
                        <select id="polda_id" name="polda_id" class="form-select @error('polda_id') is-invalid @enderror">
                            @if (Auth::user()->role_id == 1)
                                <option value="-" selected>Semua Polda</option>
                            @endif
                            @foreach ($poldas as $polda)
                                <option value="{{ $polda->id }}" {{ old('polda_id') == $polda->id ? 'selected' : '' }}>
                                    {{ $polda->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12 mb-3">
                        <label for="polres" class="fw-semibold fs-6 mb-1">Polres</label>
                        <select id="polres_id" name="polres_id"
                            class="form-select @error('polres_id') is-invalid @enderror">
                            @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                                <option value="-">Pilih Polres</option>
                            @endif
                            @foreach ($polress as $polres)
                                <option value="{{ $polres->id }}" {{ old('polres_id') == $polres->id ? 'selected' : '' }}>
                                    {{ $polres->name }}
                                </option>
                                {{-- <option value="{{ str_pad((string) $polres->id, 4, '0', STR_PAD_LEFT) }}"
                                    {{ old('polres_id') == str_pad((string) $polres->id, 4, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ $polres->name }}
                                </option> --}}
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12 mb-3">
                        <label for="" class="fw-semibold fs-6 mb-1">Tanggal Kejadian</label>
                        <input class="form-select datepicker" type="text" id="accident_date" name="accident_date"
                            placeholder="DD - MM - YYYY" autocomplete="off">
                        <span class="text-danger error-text birth_date_err"></span>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12 mb-3">
                        <label for="" class="fw-semibold fs-6 mb-1">Tipe laka</label>
                        <select name="tipe_laka" id="tipe_laka" class="form-select">
                            <option value="0">Pilih Jenis Laka</option>
                            <option value="1">TUNGGAL</option>
                            <option value="2">KONTRA</option>
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <input type="text" id="no_lp" placeholder="Cari berdasarkan nomor LP"
                            class="search-accident form-control" name="no_lp" value="{{ old('no_lp') }}">
                    </div>
                    <div class="text-start">
                        <button type="submit" class="btn btn-dark-blue" id="btn-search-accident">
                            {{ __('Search') }}
                        </button>
                        <button type="button" class="btn btn-secondary">
                            <a href="{{ url()->current() }}" class="text-decoration-none text-white"> Reset </a>
                        </button>
                    </div>
                </form>
            </fieldset>
        </div>
        <div class="box-body">
            <div class="table-responsive mt-3">
                <table class="table table-bordered table-officer" width="100%" id="dataTable" name="dataTable">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Nomor LP</th>
                            <th class="text-center">Nama Polres</th>
                            <th class="text-center">Nama Petugas Pelapor</th>
                            <th class="text-center">Tanggal Laka</th>
                            <th class="text-center">Tipe Laka</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <?php $no = 0; ?>
                    @if ($status == 'failed' || !isset($data))
                        Data Tidak Ditemukan
                    @endif
                    @if(isset($data))
                        @foreach ($data as $index => $datas)
                            <?php $no++; ?>
                            <tbody>
                                <tr>
                                    <td class="text-center" scope="row">
                                        {{ $data->perPage() - $data->perPage() + ($index + 1) }}</td>
                                    <td>{{ $datas->no_lp }}</td>
                                    <td>{{ $datas->polres_name }}</td>
                                    <td>{{ $datas->rank_id }} {{ $datas->first_name }} {{ $datas->last_name }}
                                    </td>
                                    <td>{{ $datas->tgl_laka }}</td>
                                    <td>{{ $datas->tipe_laka }}</td>
                                    <td>
                                        @if (in_array(Auth::user()->role_id, ['3', '1']))
                                            <button type="button" class="btn btn-lihat btn-dark-blue"><a
                                                    class="text-decoration-none text-white"
                                                    href="/accident/view?accident_id={{ $datas->id }}">Lihat</a></button>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        @endforeach
                    @endif
                </table>
            </div>
            @if(isset($data)){{ $data->links() }}@endif
        </div>
    </div>

    @push('script')
        <script type="text/javascript">
            $(document).ready(function() {
                var get_pol = $('#polda_id').val();
                if (get_pol == null || get_pol == '-') {
                    $('#polres_id').prop('disabled', true);
                } else {
                    $('#polres_id').prop('disabled', false);
                }
                $("#hasil_days").hide()
                $("#btn-export").hide()


            });

            $('#accident_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: "true",
                orientation: 'auto bottom'
            });

            $('#polda_id').on('change', function(event) {
                event.preventDefault();
                var poldaId = $(this).val();
                $('#polres_id').prop('disabled', true);
                $('#polres_id').empty();
                $('#polres_id').append('<option value="">Pilih Polres</option>');
                if (!poldaId) {
                    return;
                }

                $.get('{{ url('pengguna/polres_list') }}/' + poldaId, function(data) {
                    // console.log(data);
                    $('#polres_id').empty()
                    var option = '<option value="">Pilih Polres</option>';
                    $('#polres_id').append(option);

                    $.each(data, function(key, polres) {
                        var id = polres.id;
                        var name = polres.name;
                        // var option = '<option value="' + String(id).padStart(4, '0') + '">' + name + '</option>';
                        var option = '<option value="' + id + '">' + name + '</option>';

                        $('#polres_id').append(option);
                    });

                    $('#polres_id').prop('disabled', false);
                });
            });

            $('#btn-search-accident').click(function() {
                $('#polres_id').prop('disabled', false);

            });
        </script>
    @endpush
@endsection
