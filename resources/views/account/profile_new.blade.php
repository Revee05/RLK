<!--
 Figma-derived account profile content (no navbar/footer)
 - Left: avatar + vertical menu
 - Right: account form
 Uses Bootstrap classes present in the project.
 -->
@extends('account.partials.layout')
@section('content')
    <div class="container" style="max-width:1200px; margin-top:40px; margin-bottom:80px;">
        <div class="row">
            @include('account.partials.nav_new')

            <!-- Right content: form -->
            <div class="col-md-9">
                <div class="card content-border">
                    <div class="card-head border-bottom border-darkblue align-baseline ps-4">
                        <h3 class="mb-0 fw-bolder align-bottom">Akun</h3>
                    </div>
                    <div class="card-body ps-4">
                        {{-- <form method="POST" action="#">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', '') }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">username</label>
                                <input type="text" name="username" class="form-control"
                                    value="{{ old('username', '') }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">E-mail Address</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', '') }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label d-block">Gender</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" id="gender_male"
                                        value="male">
                                    <label class="form-check-label" for="gender_male">Male</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" id="gender_female"
                                        value="female">
                                    <label class="form-check-label" for="gender_female">Female</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Additional Info</label>
                                <textarea name="notes" class="form-control" rows="4">{{ old('notes', '') }}</textarea>
                            </div>

                            <div class="d-flex justify-content-start">
                                <button class="btn" style="background:#58bcc2; color:#fff;">Save</button>
                                <a href="#" class="btn btn-secondary ms-2">Cancel</a>
                            </div>
                        </form> --}}
                        {{ Form::model($user, ['route' => ['update.profil'], 'method' => 'POST']) }}
                        <div class="form-group mb-3">
                            {{ Form::label('name', 'Nama Lengkap') }}
                            {{ Form::text('name', $user->name ?? '', ['class' => 'form-control', 'placeholder' => 'Nama']) }}
                        </div>

                        <div class="form-group mb-3">
                            {{ Form::label('name', 'Username') }}
                            {{ Form::text('username', $user->username ?? '', ['class' => 'form-control', 'placeholder' => 'Username', 'disabled']) }}
                        </div>

                        <div class="form-group mb-3">
                            {{ Form::label('name', 'Email') }}
                            {{ Form::text('email', $user->email ?? '', ['class' => 'form-control', 'placeholder' => 'Email']) }}
                        </div>

                        <div class="form-group mb-3">
                            {{ Form::label('name', 'Jenis Kelamin') }}
                            {{ Form::select('jenis_kelamin', ['perempuan' => 'Perempuan', 'laki_laki' => 'Laki laki'], $user->jenis_kelamin, ['class' => 'form-control', 'placeholder' => 'Pilih Jenis Kelamin']) }}
                        </div>

                        <input type="hidden" name="id" value="{{ Auth::user()->id }}">
                        <br>

                        {{ Form::submit('Simpan', ['class' => 'btn btn-danger btn-sm rounded-0']) }}
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
