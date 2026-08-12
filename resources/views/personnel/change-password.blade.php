@php
    $_title = 'Ubah Kata Sandi';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="box">
        <div class="box-header">
            <h4 class="fw-bold text-blue-dark">Ubah Kata Sandi</h4>

            <!-- error alert -->
            @if ($errors->any())
                <div class="card-body">
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="card-body">
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                </div>
            @endif
        </div>

        <div class="box-body">
            <div class="card">
                <div class="card-body">
                    <form
                        action="{{ route('personnel.update-password', [
                            'id' => $id,
                            'policeId' => $policeId,
                        ]) }}"
                        method="POST" enctype="multipart/form-data" id="">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="id" value="{{ $id }}">
                        <div class="input-group row mb-3">
                            <label class="fw-bold col-sm-3 col-form-label" for="name">Nama</label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <input id="name" type="text"
                                    class="form-control @error('name') is-invalid @enderror font-weight-bold" name="name"
                                    value="{{ $officer->full_name }}" placeholder="" disabled>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="input-group row mb-3">
                            <label class="fw-bold col-sm-3 col-form-label" for="registerNumber">NRP</label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <input id="registerNumber" type="text"
                                    class="form-control @error('registerNumber') is-invalid @enderror font-weight-bold"
                                    name="registerNumber" value="{{ $officer->register_number }}" placeholder="" disabled>

                                @error('registerNumber')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="input-group row mb-3">
                            <label class="fw-bold col-sm-3 col-form-label" for="rankName">Pangkat</label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <input id="rankName" type="text"
                                    class="form-control @error('rankName') is-invalid @enderror font-weight-bold"
                                    name="rankName" value="{{ $officer->rank->full_name ?? '-' }}" placeholder="" disabled>

                                @error('rankName')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Password Baru -->
                        <div class="input-group row mb-3">
                            <label class="fw-bold col-sm-3 col-form-label" for="password">Password Baru</label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                <input id="password" type="password" class="form-control font-weight-bold" name="password" value="" required placeholder="">
                            </div>
                            <div class="col-lg-1 col-md-1 col-sm-12 col-12">
                                <button class="btn btn-primary" id="toggleViewPassword" type="button">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Konfirmasi Password Baru -->
                        <div class="input-group row mb-3">
                            <label class="fw-bold col-sm-3 col-form-label" for="password_confirmation">Konfirmasi Password Baru</label>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                <input id="password_confirmation" type="password" class="form-control font-weight-bold" name="password_confirmation" value="" required placeholder="">
                            </div>
                            <div class="col-lg-1 col-md-1 col-sm-12 col-12">
                                <button class="btn btn-primary" id="toggleViewPasswordConfirmation" type="button">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center">
                            <div class="m-1">
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-save"></i> Simpan
                                </button>
                            </div>
                            <div class="m-1">
                                <a href="{{ route('personnel.index', ['policeId' => $policeId]) }}" class="btn btn-danger">
                                    <i class="bi bi-x-circle"></i> Batal
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

    <script type="text/javascript">
        // Select2 with Bootstrap4 theme
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        });

        $(document).ready(function() {
            // Password Baru
            $('#toggleViewPassword').on('click', function() {
                togglePasswordVisibility($('#password'), $(this));
            });

            // Konfirmasi Password Baru
            $('#toggleViewPasswordConfirmation').on('click', function() {
                togglePasswordVisibility($('#password_confirmation'), $(this));
            });

            function togglePasswordVisibility(inputField, toggleButton) {
                const fieldType = inputField.attr('type');
                if (fieldType === 'password') {
                    inputField.attr('type', 'text');
                    toggleButton.html('<i class="bi bi-eye-slash-fill"></i>');
                } else {
                    inputField.attr('type', 'password');
                    toggleButton.html('<i class="bi bi-eye-fill"></i>');
                }
            }
        });
    </script>
@endpush
