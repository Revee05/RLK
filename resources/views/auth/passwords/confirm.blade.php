@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Confirm Password') }}</div>

                <div class="card-body">
                    {{ __('Please confirm your password before continuing.') }}

                    <form method="POST" action="{{ route('password.confirm') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <div class="input-group">
                                    <div class="password-wrapper">
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                                        <button class="password-toggle" type="button" id="toggle-password-confirm" aria-label="Show password"><i class="fas fa-eye"></i></button>
                                    </div>
                                </div>

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Confirm Password') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('toggle-password-confirm')?.addEventListener('click', function(){
    var input = document.getElementById('password');
    if (!input) return;
    var icon = this.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        if(icon){ icon.classList.remove('fa-eye'); icon.classList.add('fa-eye-slash'); }
        this.setAttribute('aria-label','Hide password');
    }
    else {
        input.type = 'password';
        if(icon){ icon.classList.remove('fa-eye-slash'); icon.classList.add('fa-eye'); }
        this.setAttribute('aria-label','Show password');
    }
});
</script>
@endsection
