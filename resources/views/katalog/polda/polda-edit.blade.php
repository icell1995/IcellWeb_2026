@php
    $_title = 'Edit Katalog Polda'
@endphp

@extends('layouts.app')

@section('content')
<a class="btn-back" href="{{ URL($name) }}"><i class="bi bi-arrow-left"></i> Kembali ke Daftar Polda</a>

<div class="box">
    <div class="box-header">
        <h3 class="fw-bold text-blue-dark">Ubah Polda</h3>
    </div>

    @if ($errors->any())
    <div class="text-center alert alert-danger col-lg-12 col-md-12 col-sm-12" role="alert">
        @foreach ($errors->all() as $error)
        <span class="sr-only">Error:</span>{{ $error }}
        @endforeach
    </div>
    @endif

    <form action="{{ URL::to($name.'/'.$polda->id) }}" method="POST">
        @method('put')
        @csrf
        <div class="input-group row mb-3 ms-0">
            <label class="fw-bold col-sm-2 col-form-label" for="timezone" class="col-lg-2 font-weight-bold">Timezone GMT</label>
            <div class="col-lg-3 form-group m-0">
                <input id="timezone" type="text"
                    class="col-lg-12 form-control @error('timezone') is-invalid @enderror" name="timezone"
                    value="{{ old('timezone',$polda->timezone) }}" required autocomplete="sort" autofocus>

                @error('timezone')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>

        <div class="input-group row mb-3 ms-0">
            <label class="fw-bold col-sm-2 col-form-label" for="sort" class="col-lg-2 font-weight-bold">Sort</label>
            <div class="col-lg-3 form-group m-0">
                <input id="sort" type="text"
                    class="form-control col-lg-12 @error('sort') is-invalid @enderror" name="sort"
                    value="{{ old('sort',$polda->sort) }}" required autocomplete="sort" autofocus>

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
                <input id="id" type="text" class="form-control @error('id') is-invalid @enderror" name="id"
                    value="{{ old('id',$polda->id) }}" required autocomplete="first_name" autofocus>

                @error('first_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>

        <div class="input-group row mb-3 ms-0">
            <label class="fw-bold col-sm-2 col-form-label" for="nama_polda" class="col-lg-2 font-weight-bold">Nama Polda</label>
            <div class="col-lg-3 form-group m-0" style="padding-left:15px;">
                <input id="nama_polda" type="text"
                    class="col-lg-12 form-control @error('nama_polda') is-invalid @enderror"
                    name="nama_polda" value="{{ old('nama_polda',$polda->name) }}" required
                    autocomplete="first_name" autofocus>

                @error('nama_polda')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>

        <div class="input-group row mb-3 ms-0">
            <label class="fw-bold col-sm-2 col-form-label" for="alamat_polda" class="col-lg-2 font-weight-bold">Alamat Polda</label>
            <div class="col-lg-3 form-group m-0" style="padding-left:15px;">
                <input id="alamat_polda" type="text"
                    class="col-lg-12 form-control @error('alamat_polda') is-invalid @enderror"
                    name="alamat_polda" value="{{ old('nama_polda',$polda->address) }}" required
                    autocomplete="alamat_polda" autofocus>

                @error('alamat_polda')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>

        <div class="col-lg-3 col-md-3 mb-4">
            <input type="checkbox" class="cb" name="arsip" id="arsip" value="1"
                {{($polda->state==1)?"":"checked"}} >
            <label for="arsip">Diarsipkan</label>
        </div>

        <div class="col-lg-12 pull-left">
            <button type="submit" class="btn btn-primary">
                {{ __('Update') }}
            </button>
        </div>
    </form>
</div>
@endsection
