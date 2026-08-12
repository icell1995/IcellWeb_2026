<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lupa Kata Sandi</title>
    <link rel="shortcut icon" href="{{ asset('images/logo1.png') }}" />

    {{-- Bootsrap Icons --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    {{-- Java Script --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <script src="{{ asset('js/login.js') }}"></script>
</head>

<body class="login-page">
    <div class="login-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center mb-3">
                    <div class="login-img">
                        <img src="{{ asset('images/logo1.png') }}">
                        <img src="{{ asset('images/logo2.png') }}">
                    </div>
                    <div class="login-header">
                        <h2>ICELL</h2>
                        <h4>Informasi Cepat Penyidikan Lalu Lintas</h4>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-6 text-center mb-5">
                    <div class="login-wrap">
                        <hr>
                        <h5>Lupa Kata Sandi</h5>
                        <small class="">Silahkan Masukkan Email Yang Terdaftar</small>

                        @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                        @endif

                        <form method="POST" action="{{ route('forget-password') }}">
                            @csrf

                            <div class="mb-3">
                                <input id="email" type="email"
                                    class="forms-control @error('email') is-invalid @enderror" name="email"
                                    value="{{ old('email') }}" placeholder="Silahkan Masukkan E-mail" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-login">{{ __('Send Password Reset Link') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>


{{-- <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Reset Password') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                        @endif

                        <form method="POST" action="{{ route('forget-password') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="email"
                                    class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Send Password Reset Link') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
