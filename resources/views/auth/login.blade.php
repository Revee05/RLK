@extends('layouts.auth_layout')
@section('content')
    <!-- Nested Row within Card Body -->
    <div class="p-5">
        <div class="text-center">
            <h1 class="h4 text-gray-900 mb-4">Masuk</h1>
        </div>
        <form class="user" method="POST" action="{{ route('new.login') }}">
            @csrf
            <div class="form-group">
                <input type="email" class="form-control form-control-user @error('email') is-invalid @enderror"
                    name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email">
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <input type="password" class="form-control form-control-user @error('password') is-invalid @enderror"
                    name="password" required autocomplete="new-password" placeholder="Password">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <button type="submit" class="btn btn-danger btn-user btn-block">
                {{ __('Login') }}
            </button>
            <hr>
            {{-- <a href="index.html" class="btn btn-google btn-user btn-block">
                <i class="fab fa-google fa-fw"></i> Daftar dengan Google
            </a> --}}
        </form>
        {{-- <hr> --}}
        <div class="text-center">
            @if (Route::has('password.request'))
                <a class="small" href="{{ route('password.request') }}">
                    {{ __('Lupa Password?') }}
                </a>
            @endif
        </div>
        <div class="text-center">
            <a class="small" href="{{ route('register') }}">Belum memiliki akun? Daftar!</a>
        </div>
    </div>
@endsection
