@extends('layouts.auth_layout')
@section('content')
    <!-- Nested Row within Card Body -->
    <div class="auth-card-body d-flex flex-column p-5 justify-content-center align-items-center">
        <div class="text-center">
            @if (isset($setting) && !empty($setting->logo) && file_exists(public_path('uploads/logos/' . $setting->logo)))
                <a href="{{ route('home') }}">
                    <img src="{{ asset('uploads/logos/' . $setting->logo) }}" alt="{{ config('app.name', 'Lelang') }}"
                        class="img-fluid mb-4 auth-logo">
                </a>
            @else
                <a href="{{ route('home') }}" class="d-inline-block mb-3 text-decoration-none">
                    <img src="{{ asset('assets/img/logo-lelang.png') }}" alt="{{ config('app.name', 'Lelang') }}"
                        class="img-fluid mb-4 auth-logo">
                </a>
            @endif
        </div>
        <form class="user mb-4 w-75" method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group">
                <input type="text" class="form-control form-control-user text-black @error('name') is-invalid @enderror"
                    name="name" value="{{ old('name') }}" required autocomplete="name" placeholder="Name" autofocus>
                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group">
                <input type="text"
                    class="form-control form-control-user text-black @error('username') is-invalid @enderror"
                    name="username" value="{{ old('username') }}" required autocomplete="username" placeholder="Username"
                    autofocus>
                @error('username')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group">
                <input type="email" class="form-control form-control-user text-black @error('email') is-invalid @enderror"
                    name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email">
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <input type="password"
                    class="form-control form-control-user text-black @error('password') is-invalid @enderror"
                    name="password" required autocomplete="new-password" placeholder="Password">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group">
                <input type="password" class="form-control form-control-user text-black" placeholder="Ulangi Password"
                    name="password_confirmation" required autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-black btn-user btn-block text-button-normal">
                {{ __('Register') }}
            </button>
            {{-- <a href="index.html" class="btn btn-google btn-user btn-block">
            <i class="fab fa-google fa-fw"></i> Daftar dengan Google
        </a> --}}
        </form>
        <div class="text-center">
            <a class="font-weight-normal text-black" href="{{ route('login') }}">Sudah memiliki akun? Login</a>
        </div>
    </div>
@endsection