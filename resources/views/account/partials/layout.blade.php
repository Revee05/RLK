<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Beranda - {{ $setting->title }}</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400&display=swap" rel="stylesheet">
    <link href="{{ asset('theme/css/styles.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/account/account_styles.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/account/auction_history_style.css') }}" rel="stylesheet" />
    <!-- Custom fonts for this template-->
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.18/vue.min.js"></script> --}}
    @yield('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
            'pusherKey' => config('broadcasting.connections.pusher.key'),
            'pusherCluster' => config('broadcasting.connections.pusher.options.cluster'),
        ]) !!};
    </script>

    <!-- custom style -->
    <link rel="stylesheet" href="{{ asset('css/account/purchase_history.css') }}">
    <link rel="stylesheet" href="{{ asset('css/account/merch_order_detail.css') }}">
    <style type="text/css">
        .list-group-item {
            border: none;
        }
    </style>
</head>

<body>
    <!-- Header-->
    @include('web.partials.header')

    <!-- Navigation-->
    {{-- @include('web.partials.nav') --}}

    {{-- content --}}
    @yield('content')

    {{-- Modals used by account pages (profile picture modal) --}}
    @include('account.partials.modal-profile-picture')

    <!-- Footer-->
    @include('web.partials.footer')
    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="{{ asset('theme/js/scripts.js') }}"></script>

    <script type="text/javascript" src='//ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js'></script>
    @yield('js')
    {{--  <script type="text/javascript">
            $('.collapse').on('shown.bs.collapse', function(){
            $(this).parent().find(".fa-angle-up").removeClass("fa-angle-up").addClass("fa-angle-down");
            }).on('hidden.bs.collapse', function(){
            $(this).parent().find(".fa-angle-down").removeClass("fa-angle-down").addClass("fa-angle-up");
            });
        </script> --}}
</body>

</html>
