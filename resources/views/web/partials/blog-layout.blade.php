<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>{{ $setting->title ?? 'Blog' }}</title>
  @yield('css')
</head>
<body>

  @include('web.partials.header')

  @yield('content')

  @include('web.partials.footer')

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  @yield('js')

</body>
</html>
