<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>{{ $setting->title ?? '' }}</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
    <link href="{{ asset('theme/css/styles.css') }}" rel="stylesheet" />
    <link href="{{ asset('theme/css/omahkoding.css') }}?=v.0.0.14" rel="stylesheet" />
    <link href="{{ asset('css/header.css') }}" rel="stylesheet">
    <link href="{{ asset('css/footer.css') }}" rel="stylesheet">
    <link href="{{ asset('css/tentang/tentang_styles.css') }}" rel="stylesheet">
    <link href="{{ asset('css/popup/style.css') }}" rel="stylesheet">
    <!-- Custom fonts for this template-->
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.18/vue.min.js"></script> --}}
    @yield('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- PUSHER CONFIG -->
    <script>
        window.Laravel = {
            csrfToken: "{{ csrf_token() }}",
            userId: {{ auth()->check() ? auth()->id() : 'null' }},
            pusherKey: "{{ config('broadcasting.connections.pusher.key') }}",
            pusherCluster: "{{ config('broadcasting.connections.pusher.options.cluster') }}"
        };
        @if (auth()->check())
            window.initialBannerNotifications = @json(\App\UserBannerNotification::where('user_id', auth()->id())->where('is_read', false)->get());
        @endif
    </script>
</head>

<body>
    <!-- Header-->
    @include('web.partials.header')

    <!-- Navigation-->
    {{-- @include('web.partials.nav') --}}

    <!-- content -->
    @yield('content')

        <!-- (moved) scripts and footer are included once below -->
    <!-- Footer-->
    @include('web.partials.footer')
    @include('web.partials.modals.terms_modal')

    {{-- Popup Notification --}}
    @include('web.partials.winner-popup')

    {{-- JS LIBRARY --}}
    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="{{ asset('theme/js/scripts.js') }}"></script>
    <!-- Pusher -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/7.2.0/pusher.min.js"></script>
    <!-- Echo -->
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.0/dist/echo.iife.js"></script>

    @yield('js')
    <!-- INIT ECHO -->
    <script>
        window.Echo = new Echo({
            broadcaster: "pusher",
            key: window.Laravel.pusherKey,
            cluster: window.Laravel.pusherCluster,
            forceTLS: true,
            authEndpoint: "/broadcasting/auth",
            auth: {
                headers: {
                    "X-CSRF-TOKEN": window.Laravel.csrfToken,
                },
            },
        });
    </script>

    {{--Popup Script --}}
    <script src="{{ asset('js/notification-popup.js') }}"></script>

    @stack('scripts')
    @stack('js')
</body>

</html>
