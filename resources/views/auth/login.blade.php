@extends('layouts.app_new') @section('content')

<div class="text-center">
    <h4 class="h4 text-gray-900 mb-4">Masuk</h4>
</div>

<form class="user" method="POST" action="{{ route('new.login') }}">
    @csrf

    <div class="form-group">
        <input type="email" class="form-control form-control-with-icon-left @error('email') is-invalid @enderror" 
               id="email" name="email" value="{{ old('email') }}" required autocomplete="email" 
               placeholder="Email">
        
        <span class="fas fa-envelope form-icon"></span> @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    
    <div class="form-group">
        <input type="password" class="form-control form-control-with-icon-left form-control-with-icon-right @error('password') is-invalid @enderror" 
               id="password" name="password" required autocomplete="new-password" 
               placeholder="Password">
        
        <span class="fas fa-lock form-icon"></span> <span class="fas fa-eye toggle-password" id="togglePassword"></span> @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    
    <button type="submit" class="btn btn-danger btn-block">
        {{ __('Login') }}
    </button>
    <hr>
    
</form>

<div class="text-center">
    @if (Route::has('password.request'))
    <a class="small" href="{{ route('password.request') }}">
        {{ __('Lupa Password?') }}
    </a>
    @endif
</div>
<div class="text-center">
    <a class="small" href="{{route('register')}}">Belum memiliki akun? Daftar!</a>
</div>

@endsection