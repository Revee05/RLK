@extends('layouts.auth_layout')
@section('content')
    <!-- Nested Row within Card Body -->
    <div class="auth-form-body">
        <div class="logo-wrapper">
            @if (isset($setting) && !empty($setting->logo) && file_exists(public_path('uploads/logos/' . $setting->logo)))
                <a href="{{ route('home') }}">
                    <img src="{{ asset('uploads/logos/' . $setting->logo) }}" alt="{{ config('app.name', 'Lelang') }}"
                        class="auth-logo">
                </a>
            @else
                <a href="{{ route('home') }}">
                    <img src="{{ asset('assets/img/logo-lelang.png') }}" alt="{{ config('app.name', 'Lelang') }}"
                        class="auth-logo">
                </a>
            @endif
        </div>
        <form class="form" method="POST" action="{{ route('new.login') }}">
            @csrf
            <div class="input-group">
                <input type="email" class="input-field input-cyan @error('email') is-invalid @enderror" name="email"
                    value="{{ old('email') }}" required autocomplete="email" placeholder="Email">
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span> 
                @enderror
            </div>

            <div class="input-group">
                <div class="password-wrapper">
                    <input id="login-password" type="password" class="input-field input-cyan @error('password') is-invalid @enderror"
                        name="password" required autocomplete="new-password" placeholder="Password">
                    <button type="button" class="password-toggle" id="toggle-login-password" aria-label="Show password"><i class="fas fa-eye"></i></button>
                </div>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="lupa-password-wrapper">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">
                        {{ __('Lupa Password?') }}
                    </a>
                @endif
            </div>
            <button type="submit" class="button btn-black text-button-normal">
                {{ __('Login') }}
            </button>
            {{-- <a href="index.html" class="btn btn-google btn-user btn-block">
                <i class="fab fa-google fa-fw"></i> Daftar dengan Google
            </a> --}}
        </form>
        {{-- <hr> --}}

        <div class="login-daftar-wrapper">
            <a class="font-weight-normal text-black" href="{{ route('register') }}">Belum memiliki akun? Daftar</a>
        </div>

    </div>
<script>
var toggleLoginBtn = document.getElementById('toggle-login-password');
if(toggleLoginBtn){
    toggleLoginBtn.addEventListener('click', function(){
        var input = document.getElementById('login-password');
        if(!input) return;
        var icon = this.querySelector('i');
        if(input.type === 'password'){
            input.type = 'text';
            if(icon){ icon.classList.remove('fa-eye'); icon.classList.add('fa-eye-slash'); }
            this.setAttribute('aria-label','Hide password');
        } else {
            input.type = 'password';
            if(icon){ icon.classList.remove('fa-eye-slash'); icon.classList.add('fa-eye'); }
            this.setAttribute('aria-label','Show password');
        }
    });
}
</script>
@endsection
