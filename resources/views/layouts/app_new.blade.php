<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Lelang - Login</title>
    
    <link href="{{asset('assets/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;700&family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    
    <link href="{{asset('assets/css/sb-admin-2.min.css')}}" rel="stylesheet">
    
    <style>
        /* CSS DASAR LAYOUT */
        body { margin: 0; padding: 0; overflow-x: hidden; font-family: 'Nunito', sans-serif; } /* Font dasar diatur ke Nunito */
        .split-container-wrapper { min-height: 100vh; }
        .slider-column { padding: 0; height: 100vh; position: relative; }
        .auth-carousel { height: 100%; }
        .auth-carousel .carousel-inner { height: 100%; }
        .auth-carousel .carousel-item { height: 100%; transition: transform 0.6s ease-in-out; }
        .auth-carousel .carousel-item img { height: 100%; width: 100%; object-fit: cover; object-position: center; }
        .form-column { display: flex; flex-direction: column; justify-content: center; padding: 2.5rem 3.5rem; background-color: #FFFFFF; }
        .logo-wrapper { text-align: center; margin-bottom: 2rem; }
        .logo-wrapper img { max-height: 45px; width: auto; }
        @media (max-width: 991.98px) {
            .slider-column { height: auto; }
            .form-column { padding-top: 4rem; padding-bottom: 4rem; }
        }

        /* =============================================
        ====== GAYA BARU (FONT ARTISTIK) ======
        ============================================= */

        /* [ BARU ] Style untuk Judul (Masuk / Register) */
        .form-column .h4, .form-column h4 {
            font-family: 'Cormorant Garamond', serif; /* <-- FONT BARU ANDA */
            font-size: 3rem; /* Perbesar untuk font yang anggun ini (48px) */
            font-weight: 700; /* Dibuat tebal agar terlihat jelas */
            color: #2a2a2a;
            margin-bottom: 1.5rem; /* Didekatkan sedikit ke form */
            text-align: center;
        }

        /* =============================================
        ====== GAYA BARU (IKON INTERAKTIF) ======
        ============================================= */

        /* 1. Wrapper .form-group dibuat 'relative' */
        .form-column .form-group { position: relative; margin-bottom: 1.25rem; }

        /* 2. Style untuk Form Input (Bentuk Pil) */
        .form-column .form-control { border-radius: 50px; padding: 1.5rem 1.5rem; font-size: 0.9rem; border: 1px solid #eaecf4; box-shadow: none; transition: border-color 0.3s ease, box-shadow 0.3s ease; }
        
        /* 3. Style untuk IKON KIRI (Email, User, Gembok) */
        .form-column .form-icon { position: absolute; left: 25px; top: 50%; transform: translateY(-50%); color: #b8b8b8; z-index: 10; transition: color 0.3s ease; }

        /* 4. Style untuk IKON KANAN (Mata 'toggle password') */
        .form-column .toggle-password { position: absolute; right: 25px; top: 50%; transform: translateY(-50%); color: #b8b8b8; cursor: pointer; z-index: 10; transition: color 0.3s ease; }
        .form-column .toggle-password:hover { color: #333; }

        /* 5. Input yang punya ikon KIRI (beri padding kiri) */
        .form-column .form-control-with-icon-left { padding-left: 4rem !important; }
        
        /* 6. Input yang punya ikon KANAN (beri padding kanan) */
        .form-column .form-control-with-icon-right { padding-right: 4rem !important; }

        /* 7. Efek saat form di-klik (focus) */
        .form-column .form-control:focus { border-color: #e53935; box-shadow: 0 0 0 0.2rem rgba(229, 57, 53, 0.2); }
        
        /* 8. Saat form di-focus, ikon kiri juga berubah warna */
        .form-column .form-control:focus + .form-icon { color: #e53935; }
        
        /* =============================================
        ====== GAYA TOMBOL & LINK ======
        ============================================= */
        
        /* Style untuk Tombol (Login / Register) */
        .form-column .btn { border-radius: 50px; padding: 0.85rem 1.5rem; font-weight: 700; letter-spacing: .05rem; font-size: .8rem; text-transform: uppercase; transition: all 0.3s ease; font-family: 'Nunito', sans-serif; } /* Pastikan font tombol tetap Nunito */
        .form-column .btn:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(229, 57, 53, 0.3); }
        
        /* Style untuk link kecil (Lupa Password? dll) */
        .form-column .small { font-size: 85%; transition: color 0.3s ease; }
        .form-column .small:hover { color: #e53935; }
    </style>
</head>
<body>

    <div class="container-fluid p-0">
        <div class="row g-0 split-container-wrapper">
            
            <div class="col-lg-7 d-none d-lg-block slider-column">
                <div id="myCarousel" class="carousel slide auth-carousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($sliders as $idx => $slide)
                            <div class="carousel-item @if($idx == 0) active @endif">
                                <img src="{{asset('uploads/sliders/'.$slide->image)}}" class="d-block w-100" alt="Slide Gambar">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-lg-5 col-md-12 form-column">
                
                <div class="logo-wrapper">
                    <a href="{{route('home')}}">
                        <img src="{{asset('uploads/logos/'.$setting->logo)}}" alt="Rasanya Lelang Karya Logo">
                    </a>
                </div>

                @yield('content')

            </div>
        </div>
    </div>

    <script src="{{asset('assets/vendor/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/vendor/jquery-easing/jquery.easing.min.js')}}"></script>
    <script src="{{asset('assets/js/sb-admin-2.min.js')}}"></script>

    <script>
    $(document).ready(function(){
        
        // Skrip untuk Carousel
        $('#myCarousel').carousel({
            interval: 2000
        });

        // Skrip untuk Toggle Password
        function togglePasswordVisibility(inputId, toggleId) {
            $(toggleId).click(function() {
                $(this).toggleClass("fa-eye fa-eye-slash");
                var input = $(inputId);
                if (input.attr("type") === "password") {
                    input.attr("type", "text");
                } else {
                    input.attr("type", "password");
                }
            });
        }

        // Terapkan fungsi ke field password login & register
        togglePasswordVisibility("#password", "#togglePassword");
        togglePasswordVisibility("#password_confirmation", "#togglePasswordConfirmation");

    });
    </script>

</body>
</html>