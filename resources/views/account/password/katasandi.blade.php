@extends('account.partials.layout')
@section('content')
    <section class="py-4" id="customer-account">
        <div class="container">
            <div class="row bg-white py-4" style="border-radius: 10px;">
                <div class="col-sm-3 border-end">
                    @include('account.partials.nav')
                </div>
                <div class="col-sm-9">
                    <div class="card">
                        <div class="card-header bg-transparant">
                            Pengaturan Kata Sandi
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-8">
                                    @include('admin.partials._success')
                                    {{-- Erors notification --}}
                                    @include('admin.partials._errors')
                                    {{ Form::model($user, ['route' => ['update.profil'], 'method' => 'POST']) }}
                                    <div class="form-group">
                                        {{ Form::label('name', 'Password') }}
                                        {{ Form::password('password', ['class' => 'form-control', 'placeholder' => 'Password']) }}
                                    </div>
                                    <div class="form-group py-2">
                                        {{ Form::label('name', 'Konfirmasi Password') }}
                                        {{ Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => 'Konfirmasi Password']) }}
                                    </div>
                                    <input type="hidden" name="id" value="{{ Auth::user()->id }}">
                                    <input type="hidden" name="katasandi" value="1">
                                    <br>
                                    {{ Form::submit('Update Password', ['class' => 'btn btn-danger btn-sm rounded-0']) }}
                                    {{ Form::close() }}
                                </div>
                                <div class="col-sm-4">

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
