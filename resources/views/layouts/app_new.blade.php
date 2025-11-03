<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Lelang - Login</title>
        <!-- Custom fonts for this template-->
        <link href="{{asset('assets/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
        <link
            href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
            rel="stylesheet">
            <!-- Custom styles for this template-->
            <link href="{{asset('assets/css/sb-admin-2.min.css')}}" rel="stylesheet">
        </head>
        <body class="bg-dark">
            <div class="container" style="margin-top: 5%;">
                <!-- Outer Row -->
                 <div class="text-center">
                    <a href="{{route('home')}}">
                        {{-- <img src="{{asset('assets/img/logo-lelang.png')}}"> --}}
                        <img src="{{asset('uploads/logos/'.$setting->logo)}}">
                    </a>
                </div>
                <div class="row justify-content-center align-items-center h-100">
                    <div class="col-xl-5 col-lg-7 col-md-4 mx-auto">
                        <div class="card o-hidden border-0 shadow-lg my-5 rounded-0">
                            <div class="card-body p-0">
                                <!-- Nested Row within Card Body -->
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Bootstrap core JavaScript-->
            <script src="{{asset('assets/vendor/jquery/jquery.min.js')}}"></script>
            <script src="{{asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
            <!-- Core plugin JavaScript-->
            <script src="{{asset('assets/vendor/jquery-easing/jquery.easing.min.js')}}"></script>
            <!-- Custom scripts for all pages-->
            <script src="{{asset('assets/js/sb-admin-2.min.js')}}"></script>
        </body>
    </html>