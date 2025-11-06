<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Lelang - Login test</title>
    <!-- Custom fonts for this template-->
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style-auth.css') }}" rel="stylesheet">

</head>

<body class="d-flex justify-content-center align-items-center min-vh-100 py-5">

    <div class="container p-0 auth-container justify-content-center align-items-center">
        <!-- close icon placed outside top-right corner -->
        <a href="{{ route('home') }}" class="auth-close" title="Close">
            <i class="far fa-times-circle"></i>
        </a>
        <!-- Outer Row -->
        <!-- <div class="text-center">
                    <a href="{{ route('home') }}">
                        {{-- <img src="{{asset('assets/img/logo-lelang.png')}}"> --}}
                        <img src="{{ asset('uploads/logos/' . $setting->logo) }}">
                    </a>
                </div> -->
        <div class="row g-0 h-100">
            <div class="col-md-6 auth-left h-100">
                <div class="w-100">
                    @yield('content')
                </div>
            </div>
            <div class="col-md-6 d-none d-md-block h-100 p-0">
                <div class="auth-image"></div>
            </div>
        </div>
    </div>
    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- Core plugin JavaScript-->
    <script src="{{ asset('assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <!-- Custom scripts for all pages-->
    <script src="{{ asset('assets/js/sb-admin-2.min.js') }}"></script>
</body>

</html>
