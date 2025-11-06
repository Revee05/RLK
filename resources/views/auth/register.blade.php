@extends('layouts.app_new') @section('content')

<div class="text-center">
    <h4 class="h4 text-gray-900 mb-4">Register</h4>
</div>

<form class="user" method="POST" action="{{ route('register') }}">
    @csrf

    <div class="form-group">
        <input type="text" class="form-control form-control-with-icon-left @error('name') is-invalid @enderror" 
               id="name" name="name" value="{{ old('name') }}" required autocomplete="name" 
               placeholder="Name" autofocus>
        
        <span class="fas fa-user form-icon"></span> @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <input type="text" class="form-control form-control-with-icon-left @error('username') is-invalid @enderror" 
               id="username" name="username" value="{{ old('username') }}" required autocomplete="username" 
               placeholder="Username">
        
        <span class="fas fa-at form-icon"></span> @error('username')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

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

    <div class="form-group">
        <input type="password" class="form-control form-control-with-icon-left form-control-with-icon-right" 
               id="password_confirmation" name="password_confirmation" required autocomplete="new-password" 
               placeholder="Ulangi Password">
        
        <span class="fas fa-lock form-icon"></span> <span class="fas fa-eye toggle-password" id="togglePasswordConfirmation"></span> </div>
    
    <button type="submit" class="btn btn-danger btn-block">
        {{ __('Register') }}
    </button>
    <hr>
    
</form>

<div class="text-center">
    <a class="small" href="{{route('login')}}">Sudah memiliki akun? Login!</a>
</div>

@endsection