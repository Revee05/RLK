<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>{{$setting->title ?? ''}}</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Bootstrap icons-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400&display=swap" rel="stylesheet">
        <link href="{{asset('css/app.css')}}" rel="stylesheet" />
        <link href="{{asset('theme/css/styles.css')}}" rel="stylesheet" />
        <link href="{{asset('theme/css/omahkoding.css')}}?=v.0.0.14" rel="stylesheet" />
        <link href="{{ asset('css/header.css') }}" rel="stylesheet">
        <link href="{{ asset('css/footer.css') }}" rel="stylesheet">
        <!-- Custom fonts for this template-->
        <link href="{{asset('assets/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
        {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.18/vue.min.js"></script> --}}
        @yield('css')
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- <script>
            window.Laravel = {!! json_encode([
                'csrfToken' => csrf_token(),
                'pusherKey' => config('broadcasting.connections.pusher.key'),
                'pusherCluster' => config('broadcasting.connections.pusher.options.cluster')
            ]) !!};
        </script> -->
        <!-- PUSHER CONFIG -->
    <script>
        window.Laravel = {
            csrfToken: "{{ csrf_token() }}",
            userId: {{ auth()->check() ? auth()->id() : 'null' }},
            pusherKey: "{{ config('broadcasting.connections.pusher.key') }}",
            pusherCluster: "{{ config('broadcasting.connections.pusher.options.cluster') }}"
        };
    </script>
    <style>
        #winner-popup {
            position: fixed;
            top: 0; left: 0;
            width:100%; height:100%;
            backdrop-filter: blur(6px);
            background: rgba(0,0,0,0.45);
            display:none;
            justify-content:center;
            align-items:center;
            z-index: 999999;
            animation: fadeInBg .25s ease;
        }

        @keyframes fadeInBg {
            from { opacity:0; }
            to   { opacity:1; }
        }

        .popup-card {
            width: 540px;
            background: #fff;
            border-radius: 16px;
            padding: 40px 32px;
            text-align: center;
            position: relative;
            animation: scaleIn .25s ease;
        }

        @keyframes scaleIn {
            from { transform: scale(.85); opacity: 0; }
            to   { transform: scale(1); opacity: 1; }
        }

        .popup-card h3 {
            font-size: 20px;
            margin-top: 16px;
            margin-bottom: 12px;
            font-weight: 700;
            color: #1A1A1A;
        }

        .popup-card p {
            font-size: 14px;
            line-height: 22px;
            color: #666;
            margin-bottom: 28px;
            padding: 0 18px;
        }

        .popup-close {
            position: absolute;
            top: 18px;
            right: 18px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: none;
            background: #f3f3f3;
            font-size: 20px;
            cursor: pointer;
        }

        .popup-btn {
            background: #0A2B44;
            padding: 12px 24px;
            border-radius: 8px;
            color: white;
            font-weight: 400;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }

        /* Ikon Winner / Loser */
        .popup-icon-winner {
            font-size: 40px;
            color: #FF8A00;
        }

        .popup-icon-loser {
            font-size: 40px;
            color: #A3A3A3;
        }
    </style>
    </head>
    <body>
        <!-- ==========================
           POPUP NOTIFIKASI
        ========================== -->
        <div id="winner-popup" style="display:none;">
            <div class="popup-card">
                <div id="popup-icon"></div>
                <h3 id="popup-title"></h3>
                <p id="popup-desc"></p>

                <a id="winner-btn" class="popup-btn" href="#">Lihat Karya Lelang Tersedia</a>

                <button class="popup-close" onclick="closeWinnerPopup()">×</button>
            </div>
        </div>
        <script>
        function closeWinnerPopup() {
            document.getElementById("winner-popup").style.display = "none";
            localStorage.setItem("popup_closed", "yes");
        }
        </script>
        <!-- ===========================
        LARAVEL ECHO + PUSHER LISTENER
        =============================== -->

        <script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/7.2.0/pusher.min.js"></script>

            <!-- Header-->
            @include('web.partials.header')
            
            <!-- Navigation-->
            {{-- @include('web.partials.nav') --}}

            {{-- content --}}
            @yield('content')

        <!-- Footer-->
        @include('web.partials.footer')
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="{{asset('theme/js/scripts.js')}}"></script>
        
        @yield('js')

        <script>
            let userId = window.Laravel.userId;

            if (userId) {
                window.Echo.private(`auction-result.${userId}`)
                    .listen('.AuctionResultEvent', (data) => {

                        if (localStorage.getItem("popup_closed") === "yes") return;

                        let popup = document.getElementById("winner-popup");
                        let title = document.getElementById("popup-title");
                        let desc  = document.getElementById("popup-desc");
                        let icon  = document.getElementById("popup-icon");
                        let btn   = document.getElementById("winner-btn");

                        if (data.type === "winner") {

                            icon.innerHTML = "🎉";
                            icon.className = "popup-icon-winner";

                            title.innerHTML = 
                                `🎉 SELAMAT! ANDA TERPILIH SEBAGAI PEMENANG LELANG <br> <b>${data.title}</b> 🎉`;

                            desc.innerHTML =
                                `Penawaran Anda dikonfirmasi sebagai yang tertinggi.<br>
                                Selesaikan pembayaran Anda dalam waktu 24 jam agar karya aman.`;

                            btn.innerHTML = "Proses Pembayaranmu Sekarang";
                            btn.href = data.checkout_url;
                            btn.style.display = "inline-block";

                        } else {

                            icon.className = "popup-icon-loser";

                            title.innerHTML = 
                                `😢 Mohon maaf penawaranmu untuk <b>${data.title}</b> Belum Berhasil😢`;

                            desc.innerHTML =
                                `Sayangnya, penawaran terakhir Anda dikalahkan pada penutupan lelang.<br>
                                Jangan berkecil hati! Kami memiliki banyak karya serupa dari seniman terbaik lainnya.`;

                            btn.innerHTML = "Lihat Karya Lelang Tersedia";
                            btn.href = "/lelang"; // bisa diganti sesuai halaman koleksi
                            btn.style.display = "inline-block";
                        }

                        popup.style.display = "flex";
                    });
            }
        </script>

        @stack('scripts')
        @stack('js')
    </body>
</html>