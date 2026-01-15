@extends('account.partials.layout')
@section('content')
    <div class="container" style="max-width:1200px; margin-top:40px; margin-bottom:80px;">
        <div class="row">
            @include('account.partials.nav_new')

            <!-- Right content: form -->
            <div class="col-md-9">
                <div class="card content-border">
                    <div class="card-head border-bottom border-darkblue align-baseline ps-4">
                        <h3 class="mb-0 fw-bolder align-bottom">Ubah Password</h3>
                    </div>
                    <div class="card-body ps-4">

                        {{-- @include('admin.partials._success') --}}
                        {{-- Erors notification --}}
                        {{-- @include('admin.partials._errors') --}}
                        {{ Form::model($user, ['route' => ['update.profil'], 'method' => 'POST']) }}
                        <div class="form-group">
                            {{ Form::label('name', 'Password') }}
                            <div class="password-wrapper">
                                {{ Form::password('password', ['id' => 'account-password', 'class' => 'form-control input-field input-cyan', 'placeholder' => 'Password']) }}
                                <button type="button" class="password-toggle" id="toggle-account-password" aria-label="Show password"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        <div class="form-group py-2">
                            {{ Form::label('name', 'Konfirmasi Password') }}
                            <div class="password-wrapper">
                                {{ Form::password('password_confirmation', ['id' => 'account-password-confirm', 'class' => 'form-control input-field input-cyan', 'placeholder' => 'Konfirmasi Password']) }}
                                <button type="button" class="password-toggle" id="toggle-account-password-confirm" aria-label="Show password"><i class="fas fa-eye"></i></button>
                            </div>
                            @error('password')
                                @foreach ($errors->all() as $error)
                                    <div class="invalid-feedback d-block">{{ $error }}</div>
                                @endforeach
                            @enderror
                        </div>
                        <input type="hidden" name="id" value="{{ Auth::user()->id }}">
                        <input type="hidden" name="katasandi" value="1">
                        <br>
                        {{ Form::submit('Update Password', ['class' => 'btn btn-cyan rounded-3 text-dark mb-3']) }}
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <div aria-live="polite" aria-atomic="true" class="position-relative">
        <div id="passwordToastContainer"
            style="position: fixed; right: 1rem; top: 1rem; z-index: 10800;
            display: flex; flex-direction: column; gap: .5rem;
        ">
        </div>
    </div>

        <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('passwordToastContainer');

            @if (session('success'))
                (function() {
                    const toastSuccess = document.createElement('div');
                    toastSuccess.className = 'toast align-items-center text-white bg-success border-0';
                    toastSuccess.setAttribute('role', 'alert');
                    toastSuccess.setAttribute('aria-live', 'assertive');
                    toastSuccess.setAttribute('aria-atomic', 'true');
                    toastSuccess.innerHTML = `
                        <div class="d-flex">
                            <div class="toast-body">{!! session('success') !!}</div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>`;
                    container.appendChild(toastSuccess);
                    new bootstrap.Toast(toastSuccess, {
                        delay: 4000
                    }).show();
                })();
            @endif

            @if (session('error'))
                (function() {
                    const toastError = document.createElement('div');
                    toastError.className = 'toast align-items-center text-white bg-danger border-0';
                    toastError.setAttribute('role', 'alert');
                    toastError.setAttribute('aria-live', 'assertive');
                    toastError.setAttribute('aria-atomic', 'true');
                    toastError.innerHTML = `
                        <div class="d-flex">
                            <div class="toast-body">{!! session('error') !!}</div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>`;
                    container.appendChild(toastError);
                    new bootstrap.Toast(toastError, {
                        delay: 4000
                    }).show();
                })();
            @endif

            // Password toggle for account password page
            function attachToggle(btnId, inputId){
                var btn = document.getElementById(btnId);
                if(!btn) return;
                btn.addEventListener('click', function(){
                    var input = document.getElementById(inputId);
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

            attachToggle('toggle-account-password','account-password');
            attachToggle('toggle-account-password-confirm','account-password-confirm');
        });
    </script>
@endsection
