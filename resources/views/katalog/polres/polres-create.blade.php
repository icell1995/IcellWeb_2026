@php
    $_title = 'Tambah Katalog Polres'
@endphp

@extends('layouts.app')

@section('content')
<a class="btn-back" href="{{ URL($name) }}"><i class="bi bi-arrow-left"></i> Kembali ke Daftar Polres</a>

<div class="box">
    <div class="box-header">
        <h3 class="fw-bold text-blue-dark">Tambah Polres</h3>
    </div>

    @if ($errors->any())
    <div class="text-center alert alert-danger col-lg-12 col-md-12 col-sm-12" role="alert">
        @foreach ($errors->all() as $error)
        <span class="sr-only">Error:</span>{{ $error }}
        @endforeach
    </div>
    @endif

    <div class="box-body">
        <form action="{{ URL::to($name) }}" method="POST">
            @csrf
            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="polda" class="col-lg-2 font-weight-bold">POLDA</label>
                <div class="col-lg-3 form-group m-0">
                    <select class="col-lg-12 select2 form-control" name="polda" id="polda">
                        <option value=""></option>
                        @foreach ($polda as $value)
                        @if (old('polda') == $value->id && old('polda'))
                        <option value="{{ $value->id }}" selected="selected">{{ $value->name }}
                        </option>
                        @else
                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                        @endif
                        @endforeach
                    </select>
                    @if ($errors->any())
                    @foreach ($errors->all() as $error)
                    @if ($error == 'Polda harus diisi.')
                    <span class="text-danger" style="padding-left: 200px;">Polda harus
                        diisi.</span>
                    @endif
                    @endforeach
                    @endif
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="sort" class="col-lg-2 font-weight-bold">Sort</label>
                <div class="col-lg-3 form-group m-0">
                    <input id="sort" type="text" class="col-lg-12 form-control @error('sort') is-invalid @enderror"
                        name="sort" value="{{ old('sort', $last_sort) }}" required autocomplete="sort"
                        autofocus>
                    @error('id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="id" class="col-lg-2 font-weight-bold">ID</label>
                <div class="col-lg-3 form-group m-0" style="padding-left:15px;">
                    <input id="id" type="text" class="col-lg-12 form-control @error('id') is-invalid @enderror" name="id"
                        value="{{ old('id', $last_id) }}" required autocomplete="first_name" autofocus>
                    @error('first_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="nama_polres" class="col-lg-2 font-weight-bold">Nama Polres<span
                        class="text-danger">*</span></label>
                <div class="col-lg-3 form-group m-0" style="padding-left:15px;">
                    <input name="nama_lengkap_id" class="col-lg-12 form-control" type="text"
                        value="{{ old('nama_lengkap_id') }}">
                    @if ($errors->any())
                    @foreach ($errors->all() as $error)
                    @if ($error == 'Nama lengkap harus diisi.')
                    <span class="text-danger" style="padding-left: 200px;">Nama Polres harus
                        diisi.</span>
                    @endif
                    @endforeach
                    @endif
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="alamat_polres" class="col-lg-2 font-weight-bold">Alamat Polres<span
                        class="text-danger">*</span></label>
                <div class="col-lg-3 form-group m-0" style="padding-left:15px;">
                    <input name="alamat_polres" class="col-lg-12 form-control" type="text"
                        value="{{ old('alamat_polres') }}">
                    @if ($errors->any())
                    @foreach ($errors->all() as $error)
                    @if ($error == 'Alamat Polres Harus diisi.')
                    <span class="text-danger" style="padding-left: 200px;">Alamat Polres Harus diisi.</span>
                    @endif
                    @endforeach
                    @endif
                </div>
            </div>

            <div class="col-lg-3 col-md-3 mb-4">
                <input type="checkbox" class="cb" name="arsip" id="arsip" value="arsip">
                <label for="arsip">Diarsipkan</label>
            </div>

            <div class="col-lg-12 pull-left">
                <button type="submit" class="btn btn-primary">
                    {{ __('Tambah Polres') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('script')
<script type="text/javascript">
    $("#polda").change(function() {
            var _token = $("input[name='_token']").val();
            var grp_id = $(this).val();
            alert(grp_id);
            $.ajax({
                url: "{{ route('polres_search') }}",
                data: {
                    _token: _token,
                    grp_id: grp_id
                },
                type: 'POST',
                // processData: false,
                dataType: 'json',
                success: function(response) {
                    $('#id').val(response.data)
                }
            });
        });
</script>
@endpush


