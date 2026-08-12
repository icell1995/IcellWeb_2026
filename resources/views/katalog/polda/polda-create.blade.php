@php
    $_title = 'Tambah Katalog Polda'
@endphp

@extends('layouts.app')

@section('content')
<a class="btn-back" href="{{ URL($name) }}"><i class="bi bi-arrow-left"></i> Kembali ke Daftar Polda</a>

<div class="box">
    <div class="box-header">
        <h3 class="fw-bold text-blue-dark">Tambah Polda</h3>
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
                <label class="fw-bold col-sm-2 col-form-label" for="timezone" class="col-lg-2 font-weight-bold">Timezone GMT</label>
                <div class="col-lg-3 form-group m-0">
                    <input id="timezone" type="text"
                        class="col-lg-12 form-control @error('timezone') is-invalid @enderror" name="timezone"
                        value="{{ old('timezone') }}" required autocomplete="sort" autofocus>

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
                    <input id="sort" type="text" class="col-lg-12 form-control @error('sort') is-invalid @enderror"
                        name="sort" value="{{ old('sort',$last_sort) }}" required autocomplete="sort" autofocus>

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
                        value="{{ old('id',$newId) }}" required autocomplete="first_name" autofocus>

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
                        class="col-lg-12 form-control @error('nama_polda') is-invalid @enderror" name="nama_polda"
                        value="{{ old('nama_polda') }}" required autocomplete="nama_polda" autofocus>

                    @error('nama_polda')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="nama_polda" class="col-lg-2 font-weight-bold">Alamat Polda</label>
                <div class="col-lg-3 form-group m-0" style="padding-left:15px;">
                    <input id="alamat_polda" type="text"
                        class="col-lg-12 form-control @error('alamat_polda') is-invalid @enderror" name="alamat_polda"
                        value="{{ old('alamat_polda') }}" required autocomplete="alamat_polda" autofocus>

                    @error('alamat_polda')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <div class="col-lg-3 col-md-3 mb-4">
                <input type="checkbox" class="cb" name="arsip" id="arsip" value="1">
                <label for="arsip">Diarsipkan</label>
            </div>

            <div class="col-lg-12 pull-left">
                <button type="submit" class="btn btn-primary">
                    {{ __('Tambah Polda') }}
                </button>
            </div>
        </form>
    </div>    
</div>
@endsection
