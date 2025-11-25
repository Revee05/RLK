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

                        @include('admin.partials._success')
                        {{-- Erors notification --}}
                        @include('admin.partials._errors')
                        {{ Form::model($user, ['route' => ['update.profil'], 'method' => 'POST']) }}
                        <div class="form-group">
                            {{ Form::label('name', 'Password') }}
                            {{ Form::password('password', ['class' => 'form-control input-field input-cyan', 'placeholder' => 'Password']) }}
                        </div>
                        <div class="form-group py-2">
                            {{ Form::label('name', 'Konfirmasi Password') }}
                            {{ Form::password('password_confirmation', ['class' => 'form-control input-field input-cyan', 'placeholder' => 'Konfirmasi Password']) }}
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
