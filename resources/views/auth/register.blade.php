@extends('layouts.app_new')
@section('content')
<!-- Nested Row within Card Body -->
<div class="p-5">
    <div class="text-center">
        <h1 class="h4 text-gray-900 mb-4">Register</h1>
    </div>
    <form class="user" method="POST" action="{{ route('register') }}">
        @csrf
        <div class="form-group">
            <input type="text" class="form-control form-control-user @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" placeholder="Name" autofocus>
            @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group">
            <input type="text" class="form-control form-control-user @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" placeholder="Username" autofocus>
            @error('username')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group">
            <input type="email" class="form-control form-control-user @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email">
            @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        
        <div class="form-group">
            <input type="password" class="form-control form-control-user @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Password">
            @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group">
            <input type="password" class="form-control form-control-user" placeholder="Ulangi Password" name="password_confirmation" required autocomplete="new-password">
        </div>
        <button type="submit" class="btn btn-danger btn-user btn-block">
        {{ __('Register') }}
        </button>
        <hr>
        {{-- <a href="index.html" class="btn btn-google btn-user btn-block">
            <i class="fab fa-google fa-fw"></i> Daftar dengan Google
        </a> --}}
    </form>
    {{-- <hr> --}}
    <div class="text-center">
        {{-- <a class="small" href="forgot-password.html">Lupa Password?</a> --}}
    </div>
    <div class="text-center">
        <a class="small" href="{{route('login')}}">Sudah memiliki akun? Login!</a>
    </div>
</div>
@endsection