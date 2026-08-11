@php
    $_title = 'Reset Password';
@endphp
@extends('layouts.app')

@section('content')
        <div class="box">
            <div class="box-header">
                <div class="radius-card justify-content-center">
                    @if ($errors->any())
                    <div class="text-center alert alert-danger col-lg-12 col-md-12 col-sm-12 mb-3" role="alert">
                        @foreach ($errors->all() as $error)
                        <span class="sr-only">Error:</span>{{ $error }}
                        @endforeach
                    </div>
                    @endif
                    <h3 class="text-blue-dark fw-bold mb-2">Reset Password</h3>
                    <form method="post" action="{{route('post_reset_password')}}">
                        @csrf
                        <div>
                            <input class="col-lg-5 col-md-5 col-sm-12 col-12 mb-3" id="username" name="username" value="{{$username}}" hidden>

                        </div>
                        <div class="col-lg-8 col-md-8 col-sm-12 col-12 mb-3">
                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold text-blue-dark col-sm-3" for="newPassword">Kata Sandi (Baru)</label>
                                <div class="col-lg-7 col-md-7 col-sm-8 col-8">
                                    <input class="form-control col-lg-5 col-md-5 col-sm-12 col-12 @error('newPassword') is-invalid @enderror" type="password" id="newPassword" name="newPassword" title="New Password" value="{{ $newPassword ?? old('newPassword') }}" >
                                </div>
                                @error('newPassword')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold text-blue-dark col-sm-3" for="confPassword">Konfirmasi Kata Sandi (Baru)</label>
                                <div class="col-lg-7 col-md-7 col-sm-8 col-8">
                                    <input class="form-control col-lg-5 col-md-5 col-sm-12 col-12 @error('confPassword') is-invalid @enderror" type="password" id="confPassword" name="newPassword_confirmation" title="Confirmation Password" value="{{ $confPassword ?? old('confPassword') }}">
                                </div>
                                @error('confPassword')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="input-group row mb-3">
                            <div class="text-start">
                                <button type="submit" class="btn btn-dark-blue">
                                    {{ __('Reset Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
@endsection
