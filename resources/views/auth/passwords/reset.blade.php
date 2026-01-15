@extends('layouts.app_new')
@section('content')
<!-- Nested Row within Card Body -->
<div class="p-5">
    <div class="text-center">
        <h1 class="h4 text-gray-900 mb-4">{{ __('Reset Password') }}</h1>
    </div>
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="form-group">
            <input id="email" type="email" class="form-control form-control-user @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
            @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group">
            <div class="input-group">
                <div class="password-wrapper">
                    <input id="password" type="password" class="form-control form-control-user @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="New Password">
                    <button class="password-toggle" type="button" id="toggle-password-reset" aria-label="Show password"><i class="fas fa-eye"></i></button>
                </div>
            </div>
            @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group">
            <div class="input-group">
                <div class="password-wrapper">
                    <input id="password-confirm" type="password" class="form-control form-control-user" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm New Password">
                    <button class="password-toggle" type="button" id="toggle-password-confirm-reset" aria-label="Show password"><i class="fas fa-eye"></i></button>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-danger btn-user btn-block">
        {{ __('Reset Password') }}
        </button>
    </form>
</div>
<script>
['password','password-confirm'].forEach(function(id){
    var btnId = id === 'password' ? 'toggle-password-reset' : 'toggle-password-confirm-reset';
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
