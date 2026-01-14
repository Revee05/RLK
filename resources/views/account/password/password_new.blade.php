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
                            {{ Form::password('password', ['class' => 'form-control input-field input-cyan', 'placeholder' => 'Password']) }}
                        </div>
                        <div class="form-group py-2">
                            {{ Form::label('name', 'Konfirmasi Password') }}
                            {{ Form::password('password_confirmation', ['class' => 'form-control input-field input-cyan', 'placeholder' => 'Konfirmasi Password']) }}
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
        });
    </script>
@endsection
