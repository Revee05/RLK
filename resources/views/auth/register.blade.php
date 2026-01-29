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
        <form class="form" method="POST" action="{{ route('register') }}">
            @csrf
            <div class="input-group">
                <input type="text" class="input-field input-cyan @error('name') is-invalid @enderror" name="name"
                    value="{{ old('name') }}" required autocomplete="name" placeholder="Nama" autofocus>
                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="input-group">
                <input type="text" class="input-field input-cyan @error('username') is-invalid @enderror" name="username"
                    value="{{ old('username') }}" required autocomplete="username" placeholder="Username" autofocus>
                @error('username')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
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
                <input type="number" class="input-field input-cyan @error('number') is-invalid @enderror" name="number"
                    value="{{ old('number') }}" required autocomplete="number" placeholder="Nomor Telepon">
                @error('number')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="input-group">
                <div class="password-wrapper">
                    <input id="register-password" type="password" class="input-field input-cyan @error('password') is-invalid @enderror"
                        name="password" required autocomplete="new-password" placeholder="Password">
                    <button type="button" class="password-toggle" id="toggle-register-password" aria-label="Show password"><i class="fas fa-eye"></i></button>
                </div>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="input-group">
                <div class="password-wrapper">
                    <input id="register-password-confirm" type="password" class="input-field input-cyan" placeholder="Ulangi Password"
                        name="password_confirmation" required autocomplete="new-password">
                    <button type="button" class="password-toggle" id="toggle-register-password-confirm" aria-label="Show password"><i class="fas fa-eye"></i></button>
                </div>
            </div>
            <button type="submit" class="button btn-black text-button-normal">
                {{ __('Register') }}
            </button>
            {{-- <a href="index.html" class="btn btn-google btn-user btn-block">
            <i class="fab fa-google fa-fw"></i> Daftar dengan Google
        </a> --}}
        </form>
        <div class="login-daftar-wrapper">
            <a class="font-weight-normal text-black" href="{{ route('login') }}">Sudah memiliki akun? Login</a>
        </div>
    </div>
<script>
['register-password','register-password-confirm'].forEach(function(id){
    var btnId = id === 'register-password' ? 'toggle-register-password' : 'toggle-register-password-confirm';
    var btn = document.getElementById(btnId);
    if(btn){
        btn.addEventListener('click', function(){
            var input = document.getElementById(id);
            if (!input) return;
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
});
</script>
@endsection
