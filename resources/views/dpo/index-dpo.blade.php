@php
    $_title = 'DPO';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <style>
        .select2-container--bootstrap4 .select2-selection--single {
            height: 38px !important;
            line-height: 38px !important;
        }
    </style>
@endpush

@section('content')
    <div class="box">
        <div class="box-header">
            @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
            @endif
            <h3 class="text-blue-dark fw-semibold mb-2">Daftar Pencarian Orang</h3>
            <fieldset id="search-filter" class="border rounded-3 p-3">
                <form class="row mt-2" action="{{ route('search_dpo')}}" method="get">
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12 mb-3">
                        <label class="fw-bold text-blue-dark" for="">Search</label>
                        <input type="text" id="search" placeholder="Cari berdasarkan" class="form-control" name="search"
                            value="">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12 mb-3">
                        <label class="fw-bold text-blue-dark" for="polda">Polda</label>
                        <select id="polda" name="polda" class="form-control select2 @error('polda') is-invalid @enderror">
                            @if( Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                            <option value="-" }}>Semua Polda</option>
                            @endif
                            @foreach ($polda as $poldas)
                            @if(in_array($poldas->name, ['POLDA XE', 'PJR INDUK KORLANTAS', 'PUSDIKLANTAS', 'DIT GAKKUM KORLANTAS']))
                                @continue
                            @endif
                            <option value="{{ $poldas->id }}" {{ old('polda_id')==$poldas->id ? 'selected' : '' }}>
                                {{ $poldas->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12 mb-3">
                        <label class="fw-bold text-blue-dark" for="polres">Polres</label>
                        <select id="polres" name="polres" class="form-control select2 @error('polres') is-invalid @enderror">
                            @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                            <option value="-" }}>Pilih Polres</option>
                            @endif
                            @foreach ($polres as $polress)
                            <option value="{{ $polress->id }}" {{ old('polres_id')==$polress->id ? 'selected' : ''
                                }}>
                                {{ $polress->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12 mb-3">
                        <label class="fw-bold text-blue-dark" for="">Tanggal Kejadian</label>
                        <input class="form-control" type="text" id="accident_date" name="accident_date"
                            placeholder="DD - MM - YYYY" autocomplete="off">
                        <span class="text-danger error-text birth_date_err"></span>
                    </div>
                    <div class="text-start">
                        <button type="submit" id="search_btn" class="btn btn-dark-blue">{{ __('Cari') }}</button>
                    </div>
                </form>
            </fieldset>
        </div>

        <form class="form-dpo">
            @csrf
            <div class="box-body">
                <div class="table-responsive mt-3">
                    <table class="display table table-bordered table-dpo" cellspacing="0" width="100%" id="dataTable" name="dataTable">
                        <thead>
                            <tr>
                                <th class="text-center align-middle text-blue-dark">No</th>
                                <th class="text-center align-middle text-blue-dark" width="25%">No LP</th>
                                <th class="text-center align-middle text-blue-dark">Tanggal Kejadian</th>
                                <th class="text-center align-middle text-blue-dark">Polres</th>
                                <th class="text-center align-middle text-blue-dark">Status SELRA</th>
                                <th class="text-center align-middle text-blue-dark">Action</th>
                            </tr>
                        </thead>
                        @if ($accident == null)
                        <tbody>
                        </tbody>
                        Tidak ada data Daftar Pencarian Orang
                        @else
                        <?php $no=0;?>
                        @foreach($accident as $index => $accidents)
                        <?php $no++ ?>
                        <tbody>
                            <th class="text-center align-middle" scope="row">{{ (($accident->perPage())-$accident->perPage())+($index+1) }}</th>
                            <td class="text-center align-middle text-blue-dark fw-bold">{{ $accidents->no_lp}}
                                {{-- <i class="fa fa-angle-right dropdown-sidemenu"></i> </td> --}}
                            <td class="text-center align-middle text-blue-dark">{{ $accidents->accident_date}}</td>
                            <td class="text-center align-middle text-blue-dark">{{ $accidents->polres_name}}</td>
                            <td class="text-center align-middle text-blue-dark">{{ $accidents->selra}}</td>
                            <td class="text-center align-middle text-blue-dark">
                                {{-- <button type="button" class="btn btn-lihat" data-toggle="modal"
                                     data-target="#data-dpo" data-id="{{$accidents->accident_id}}">
                                     Lihat
                                </button> --}}
                                <button type="button" class="btn btn-success mb-1" data-toggle="modal"
                                    data-target="#data-dpo" data-id="{{$accidents->accident_id}}">
                                    Lihat
                                </button>
                            </td>
                        </tbody>
                        @endforeach
                        @endif
                    </table>
                    <div class="pull-left">
                        Showing
                        {{$accident->firstItem()}}
                        to
                        {{$accident->lastItem()}}
                        of
                        {{$accident->total()}}
                    </div>
                    <div class="pull-right">
                        {{$accident->links()}}
                    </div>
                </div>
                <!-- /.table-responsive -->
            </div>
        </form>
    </div>

{{-- <div id="dpo-data" name="dpo-data" class="modal fade" role="dialog">
    @csrf
    <div class="dialog-modal">
        <div class="content-modal modal-background">
            @if ($errors->any())
            <div class="text-center alert alert-danger col-lg-12 col-md-12 col-sm-12" role="alert">
                @foreach ($errors->all() as $error)
                <span class="sr-only">Error:</span>{{ $error }}
                @endforeach
            </div>
            @endif
            <div class="header-modal">
                <h3 class="title-modal">Nama-nama DPO</h3>
            </div>

            <form>
                @csrf
                <div id="result-dpo" name="result-dpo" class="modal-body" style="padding: 0; margin: 10px">
                    <div id="test" name="test">

                    </div>
                </div>
            </form>
        </div>
    </div>
</div> --}}

<div id="dpo-data" name="dpo-data" class="modal fade" role="dialog">
    @csrf
    <div class="dialog-modal">
        @if ($errors->any())
        <div class="text-center alert alert-danger col-lg-12 col-md-12 col-sm-12" role="alert">
            @foreach ($errors->all() as $error)
            <span class="sr-only">Error:</span>{{ $error }}
            @endforeach
        </div>
        @endif

        <div class="content-modal modal-background">
            <div class="header-modal">
                <h3 class="title-modal">Nama-nama DPO</h3>
            </div>

            <form>
                @csrf
                <div id="result-dpo" name="result-dpo" class="modal-body" style="padding: 0; margin: 10px">
                    <div id="test" name="test">
                    </div>

                </div>
            </form>

        </div>

    </div>
</div>


@push('script')
<script type="text/javascript">
    $(document).ready(function(){
        var get_pol = $('#polda').val();
            if( get_pol == null || get_pol == '-'){
                $('#polres').prop('disabled', true);
            }else{
                $('#polres').prop('disabled', false);
            }
    });

    $(".btn-lihat").click(function(e) {
                    e.preventDefault();

                    var _token = $("input[name='_token']").val();
                    var id = $(this).data('id');
                    $.ajax({
                        url: "{{ route('list_dpo') }}",
                        type: 'GET',
                        data: {
                            _token: _token,
                            id: id,
                        },
                        success: function(data) {
                            $("#test").remove();


                            $('#result-dpo').append(' <div id="test" name="test"> </div>')

                            for(i=0;i<data.dpo.length;i++){

                                var status ;
                                if(data.dpo[i].state == 0)
                                {
                                    status = 'Masih dalam Pencarian'
                                }else
                                {
                                    status = 'Sudah Tertangkap'
                                }


                                $('#test').append(
                                    '<div name"list-dpo" id="list-dpo">'+
                                        '<div class="sub-field col-sm-12 col-md-12">'+
                                            '<div class="input-group">'+
                                                '<label for="nrp" class="col-md-4 col-form-label" style="color:#ffffff">Nama</label>'+
                                                '<input id="dpo_name" type="text" class="form-control"'+
                                                'name="dpo_name" value="'+data.dpo[i].name+'"'+
                                                'autocomplete="dpo_name">'+
                                            '</div>'+
                                        '</div>'+

                                        '<div class="sub-field col-sm-12 col-md-12">'+
                                            '<div class="input-group">'+
                                                '<label for="nrp" class="col-md-4 col-form-label" style="color:#ffffff">Jenis Kelamin</label>'+
                                                '<input id="dpo_name" type="text"'+
                                                'class="form-control"'+
                                                'name="dpo_name" value="'+data.dpo[i].gender+'"'+
                                                'autocomplete="dpo_name">'+
                                            '</div>'+
                                        '</div>'+

                                        '<div class="sub-field col-sm-12 col-md-12">'+
                                            '<div class="input-group">'+
                                                '<label for="nrp" class="col-md-4 col-form-label" style="color:#ffffff">Status</label>'+
                                                '<input id="dpo_name" type="text"'+
                                                'class="form-control"'+
                                                'name="dpo_name" value="'+status+'" '+
                                                'autocomplete="dpo_name">'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'
                                )

                            }

                            $('#dpo-data').modal('show');

                        }
                    });

                });
    $('#polda').on('change', function(event) {
        event.preventDefault();
        var poldaId = $(this).val();
        $('#polres').prop('disabled', true);
        $('#polres').empty();
        $('#polres').append('<option value="">Pilih Polres</option>');
        $('#polres').trigger('change');
        if(! poldaId) {
            return;
        }

        $.get('{{ url("pengguna/polres_list") }}/'+ poldaId, function(data) {

            $('#polres').empty()
            var option = '<option value="">Pilih Polres</option>';
                $('#polres').append(option);
                $.each(data, function(key, polres) {

                var id   = polres.id;
                var name = polres.name;
                var option  = '<option value="' + id + '">' + name + '</option>';

                $('#polres').append(option);
            });

            $('#polres').prop('disabled', false);
            $('#polres').trigger('change');
        });

    });

    $('#accident_date').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: "true",
        orientation: 'auto bottom'
    });
</script>
<script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    });
</script>
@endpush

@endsection
