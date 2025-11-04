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
            <style>
                body.bg-dark {
                    background: linear-gradient(135deg, #f5e6ca 0%, #f7cac9 50%, #92a8d1 100%);
                    min-height: 93vh;
                    background-attachment: fixed;
                    position: relative;
                }
                .container {
                    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2);
                    border-radius: 20px;
                    background: rgba(255,255,255,0.7);
                    backdrop-filter: blur(4px);
                    padding-top: 30px;
                    padding-bottom: 30px;
                }
                .logo-art-wrapper {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    margin-bottom: 20px;
                }
                .logo-art-bg {
                    width: 170px;
                    height: 110px;
                    border-radius: 30px 70px 70px 30px/60px 40px 70px 90px;
                    background: radial-gradient(circle at 40% 40%, #f7cac9 60%, #92a8d1 100%);
                    box-shadow: 0 8px 32px 0 rgba(146, 168, 209, 0.25);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    position: relative;
                    border: 4px solid #fff3e6;
                    margin: 0 auto;
                }
                .logo-art-bg::after {
                    content: "";
                    position: absolute;
                    width: 140px;
                    height: 80px;
                    border-radius: 30px 70px 70px 30px/60px 40px 70px 90px;
                    left: 50%;
                    top: 50%;
                    transform: translate(-50%, -50%);
                    background: url('https://www.transparenttextures.com/patterns/brush.png');
                    opacity: 0.13;
                    pointer-events: none;
                }
                .logo-art-text {
                    font-family: 'Nunito', 'Segoe UI', Arial, sans-serif;
                    font-weight: 800;
                    font-size: 1.5rem;
                    color: #34495e;
                    letter-spacing: 1px;
                    text-align: center;
                    z-index: 1;
                    text-shadow: 0 2px 8px rgba(146,168,209,0.10), 0 1px 0 #fff;
                    line-height: 1.2;
                    background: linear-gradient(90deg, #f7cac9 30%, #92a8d1 100%);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    background-clip: text;
                }
                .card, .container {
                    transition: box-shadow 0.3s;
                }
                .card:hover, .container:hover {
                    box-shadow: 0 12px 40px 0 rgba(146, 168, 209, 0.4);
                }
            </style>
        </head>
        <body class="bg-dark">
            <div class="container" style="margin-top: 5%;">
                <!-- Outer Row -->
                 <!-- <div class="text-center">
                    <a href="{{route('home')}}">
                        {{-- <img src="{{asset('assets/img/logo-lelang.png')}}"> --}}
                        <img src="{{asset('uploads/logos/'.$setting->logo)}}">
                    </a>
                </div> -->
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