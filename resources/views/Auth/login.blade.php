<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ICELL Login</title>
    <link rel="shortcut icon" href="{{ asset('images/logo1x.png') }}" />

    {{-- Bootsrap Icons --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap1x.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style2x.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login-custom.css') }}">
    
    <!-- Modal Login Instruction CSS -->
    <link rel="stylesheet" href="{{ asset('css/login-instruction-modal.css') }}">

    <!-- Mobile optimization script -->
    <script>
        // Fix 100vh issue on mobile browsers
        function setVH() {
            let vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty('--vh', `${vh}px`);
        }
        window.addEventListener('resize', setVH);
        window.addEventListener('orientationchange', setVH);
        setVH();
    </script>
</head>

<body class="login-page">
    <section class="login-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12 text-center mb-4">
                    <div class="login-img">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-4 col-sm-4">
                                <img src="{{ asset('images/logo2x.png') }}" class="img-fluid" alt="Logo Polisi">
                            </div>
                            <div class="col-4 col-sm-4">
                                <img src="{{ asset('images/logo1x.png') }}" class="img-fluid" alt="Logo Lalu Lintas">
                            </div>
                            <div class="col-4 col-sm-4">
                                <img src="{{ asset('images/logoICELLTransparent.png') }}" class="img-fluid" alt="Logo ICELL">
                            </div>
                        </div>
                    </div>

                    <div class="login-header">
                        <h2>ICELL</h2>
                        <h4>Informasi Cepat Penyidikan Lalu Lintas</h4>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-8 text-center mb-5">
                    <div class="login-wrap">
                        <h5>Selamat Datang</h5>
                        <small class="">Silahkan Masukkan Nama Pengguna dan Kata Sandi</small>
                        @if ($errors->any())
                            <div class="card border-danger">
                                <div class="card-body text-danger text-center">
                                    @foreach ($errors->all() as $error)
                                        <span class="fw-bold">{{ $error }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <form class="mt-3" method="POST" autocomplete="off" id="form-login">
                            @csrf
                            <div class="mb-3">
                                <input class="forms-control @error('username') is-invalid @enderror" type="text"
                                    name="username" id="username" autocomplete="username" required
                                    placeholder="Silahkan Masukkan Nama Pengguna">
                                @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="password-container mb-3">
                                <input class="forms-control @error('password') is-invalid @enderror" type="password"
                                    name="password" id="password" placeholder="Silahkan Masukkan Kata Sandi"
                                    autocomplete="current-password">
                                <span toggle="#togglePassword" class="bi bi-eye field-icon toggle-password"
                                    id="togglePassword"></span>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <a class="text-start forgot-password" href="{{ route('forget-password') }}">Lupa Kata
                                    Sandi?
                                    <strong class="ms-1">Klik Disini</strong>
                                </a>
                            </div>
                            <hr>
                            <div class="mb-3 d-flex flex-column flex-md-row">
                                <div class="captcha mb-3 mb-md-0 col-12 col-md-7">
                                    <span class="img-captcha me-1">{!! Captcha::img() !!}</span>
                                    <button type="button" class="btn reload btn-secondary" id="reload"
                                        data-refresh-url="{{ route('refresh_captcha') }}">
                                        <i class="bi bi-arrow-clockwise fw-bold"></i>
                                    </button>
                                </div>
                                <div class="col-12 col-md-5">
                                    <input id="captcha" type="text" class="forms-control input-captcha"
                                        placeholder="Masukkan Captcha" name="captcha">
                                </div>
                            </div>
                            <hr>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-login">Masuk</button>
                            </div>
                            <div>
                                <button type="button" class="btn btn-confidential" data-bs-toggle="modal" data-bs-target="#privacyPolicyModal"> <span class="bi bi-shield-lock icon-lock"></span>
                                    Kebijakan privasi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Include Login Instruction Modal from components -->
    @include('components.login-instruction-modal')

    <!-- Modal Kebijakan Privasi -->
    <div class="modal fade" id="privacyPolicyModal" tabindex="-1" aria-labelledby="privacyPolicyModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 justify-content-center">
                    <h5 class="modal-title text-2xl text-center fw-bold" id="privacyPolicyModalLabel">PERHATIAN !</h5>
                </div>
                <div class="modal-body border-0">
                    <p>Setiap orang dengan sengaja dan tanpa hak atau melawan hukum mengakses Komputer atau Sistem Elektronik milik orang lain dengan cara apa punakan dikenakan pidana penjara paling lama 8 Tahun sebagaimana dimaksud dalam pasal 30 UU ITE.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-primary w-50 rounded-5" data-bs-dismiss="modal">
                        Saya Mengerti
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- OTP Modal -->
    <div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="otpModalLabel">Verifikasi OTP</h5>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-shield-lock fs-1 text-primary"></i>
                        <h5 class="mt-2">Kode OTP telah dikirim</h5>
                        <p class="text-muted" id="otpInstructions"></p>
                    </div>

                    <div id="fallbackOtpContainer" style="display: none;">
                        <p class="text-danger text-center fw-bold">Kode OTP Anda:</p>
                        <h2 class="text-center fw-bold" id="fallbackOtpCode"></h2>
                    </div>

                    <form id="form-otp">
                        @csrf
                        <div class="otp-input-container d-flex justify-content-center gap-2 my-4">
                            @for ($i = 0; $i < 6; $i++)
                                <input type="text" class="form-control otp-input text-center fs-4" maxlength="1"
                                    inputmode="numeric" pattern="[0-9]" required>
                            @endfor
                        </div>

                        {{-- <div class="d-flex justify-content-center mb-3">
                            <button type="submit" class="btn btn-login w-50">Verifikasi</button>
                        </div> --}}
                    </form>

                    <!-- Alert untuk OTP -->
                    <div id="otpAlertContainer" class="mb-2" style="min-height: 38px;">
                        <div id="otpError" class="alert alert-danger text-danger py-2 px-3 mb-1 d-none" role="alert">
                        </div>
                        <div id="otpSuccess" class="alert alert-success py-2 px-3 mb-1 d-none" role="alert">
                        </div>
                    </div>

                    <!-- Resend OTP Section -->
                    <div class="text-center mt-3">
                        <div id="resendSection">
                            <p class="text-muted small mb-2">Tidak menerima kode OTP?</p>

                            <!-- Countdown Timer -->
                            <div id="countdownContainer" class="mb-2">
                                <span class="text-primary">
                                    <i class="bi bi-clock"></i>
                                    Kirim ulang dalam <span id="countdown" class="fw-bold">1:00</span>
                                </span>
                            </div>

                            <!-- Resend Button - Hidden by default -->
                            <button type="button" id="resendOtpBtn" class="btn btn-link btn-sm d-none" disabled>
                                <i class="bi bi-arrow-clockwise"></i>
                                <span id="resendBtnText">Kirim Ulang Kode</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Screen -->
    <div id="loadingScreen" class="loading-screen d-none">
        <div class="loading-overlay">
            <div class="loading-content text-center">
                <!-- Logo atau Branding -->
                <div class="loading-logo mb-2">
                    <img src="{{ asset('images/logoICELLTransparent.png') }}" alt="ICELL Logo"
                        class="loading-logo-img">
                </div>

                <!-- Loading Text -->
                <div class="loading-text">
                    <h4 class="text-white mb-2" id="loadingTitle">Memproses...</h4>
                    <p class="text-white-50 mb-3" id="loadingMessage">Sedang memverifikasi kredensial...</p>
                </div>

                <!-- Progress Bar -->
                <div class="progress loading-progress mb-3">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar"
                        style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                        id="loadingProgressBar">
                    </div>
                </div>

                <div class="loading-steps">
                    <small class="text-white-50" id="loadingStep">Memverifikasi kredensial...</small>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/bootstrap1x.js') }}"></script>
    <script src="{{ asset('js/login1x.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

    <!-- Routes for JavaScript -->
    <script>
        window.routes = {
            authenticate: "{{ route('authenticate') }}",
            verifyOtp: "{{ route('verifyOtp') }}",
            resendOtp: "{{ route('resendOtp') }}"
        };

        // Mobile optimization
        document.addEventListener('DOMContentLoaded', function() {
            // Handle responsive layout
            function handleResponsiveLayout() {
                const viewportWidth = window.innerWidth;
                const captchaContainer = document.querySelector('.captcha');
                const captchaInput = document.querySelector('.input-captcha');

                // Apply specific mobile optimizations if needed
                if (viewportWidth < 576) {
                    // Any specific mobile adjustments can go here
                }
            }

            // Run on page load and resize
            handleResponsiveLayout();
            window.addEventListener('resize', handleResponsiveLayout);
            window.addEventListener('orientationchange', handleResponsiveLayout);
        });
    </script>

    <!-- Custom Login Script -->
    <script src="{{ asset('js/login-custom.js') }}?v={{ time() }}"></script>

    <!-- Login Instruction Modal Script -->
    <script src="{{ asset('js/login-instruction-modal.js') }}"></script>
</body>

</html>
