<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('_title')</title>
    <link rel="shortcut icon" href="{{ asset('images/logo2x.png') }}" />

    {{-- Bootsrap Icons --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">

    <!-- ===Google Sans Font=== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap1x.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style2x.css') }}">

    {{-- Chart.js --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.3.3/chart.min.js"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.2.1/dist/chart.umd.min.js"></script>

    <!-- HighChart -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    {{-- Java Script --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <script src="{{ asset('js/bootstrap1x.js') }}"></script>
    <script src="{{ asset('js/app1x.js') }}"></script>

    <!-- Data Table -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>

    <!-- Datepicker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"
        integrity="sha512-LsnSViqQyaXpD4mBBdRYeP6sRwJiJveh2ZIbW41EBrNmKxgr/LFZIiWT6yr+nycvhvauz8c2nYMhrP80YhG7Cw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css"
        integrity="sha512-34s5cpvaNG3BknEWSuOncX28vz97bRI59UnVtEEpFX536A7BtZSJHsDyFoCl8S7Dt2TPzcrCEoHBGeM4SUBDBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    @stack('style')
</head>

<body data-layout="fluid">
    <div id="app">
        <div class="wrapper">
            <div class="d-flex">
                <div class="sidemenu">
                    @include('cms.sidebar.sidebarmenu')
                </div>
                <div class="main-content">
                    <header>
                        <nav class="navbar navbar-expand light-blue">
                            <div class="container-fluid">
                                <span class="btn-burger float-end fs-4 rounded-circle"><i class="bi bi-list"></i></span>
                                <img class="img-header ms-2" src="{{ asset('images/logo1x.png') }}" alt="profile_image">
                                <img class="img-header" src="{{ asset('images/logo2x.png') }}" alt="profile_image">
                                <span class="fs-5 fw-bold px-2 text-blue-dark">INFORMASI CEPAT PENYELIDIKAN DAN PENYIDIKAN LAKA LANTAS</span>

                                <button class="navbar-toggler" type="button" data-toggle="collapse"
                                    data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown"
                                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                                    <i class="ba ba-person"></i>
                                </button>

                                <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
                                    <ul class="navbar-nav">
                                        <li class="nav-item dropdown profile">
                                            <a class="nav-link dropdown-toggle text-blue-dark" href="#"
                                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                {{ Auth::user()->first_name }} {{ ' ' }}
                                                {{ Auth::user()->last_name }}
                                            </a>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('profile') }}">Profil</a>
                                                </li>
                                                <li><a class="dropdown-item" href="{{ route('reset_password') }}">Ubah
                                                        Kata Sandi</a></li>
                                                <li><a class="dropdown-item" href="{{ route('login') }}"
                                                        onclick="event.preventDefault();
                                                    document.getElementById('logout-form').submit();">Keluar</a>
                                                </li>
                                            </ul>
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                class="d-none">
                                                @csrf
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                                @if (Auth::getUser()->avatar)
                                    <img class="img-profile rounded-circle"
                                        src="/image-profile/profile640/{{ Auth::getUser()->avatar }}"
                                        alt="profile_image" onerror="this.onerror=null;this.src='/image-profile/profile640/user.png';">
                                @endif
                            </div>
                        </nav>
                    </header>
                    <div class="content">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @stack('script')
</body>

</html>
