@extends('admin.partials._layout')
@section('title', 'Tambah Tim')
@section('team', 'active')
@section('css')
    <style type="text/css">
        .preview-cover {
            height: 350px;
            width: 100%;
            overflow: hidden;
            position: relative;
            border: 1px solid #5a5c69;
        }

        .preview-cover img {
            height: 100%;
            width: 100%;
            object-fit: cover;
            object-position: center;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <h1 class="h5 mb-4 text-gray-800">Master
            <small>Tim</small>
        </h1>
        <div class="row">
            <div class="col-sm-12">
                {{ Form::open(['route' => 'master.team.store', 'files' => true]) }}
                <div class="card rounded-0 border-0 shadow">
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-sm-6">
                                {{ Form::label('name', 'Nama') }}
                                {{ Form::text('name', null, ['class' => 'form-control form-control-sm', 'placeholder' => 'Nama']) }}
                            </div>
                            <div class="col-sm-6">
                                {{ Form::label('role', 'Role') }}
                                {{ Form::text('role', null, ['class' => 'form-control form-control-sm', 'placeholder' => 'Role']) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                {{ Form::label('email', 'Email') }}
                                {{ Form::email('email', null, ['class' => 'form-control form-control-sm', 'placeholder' => 'Email']) }}
                            </div>
                            <div class="col-sm-6">
                                {{ Form::label('instagram', 'Instagram URL') }}
                                {{ Form::text('instagram', null, ['class' => 'form-control form-control-sm', 'placeholder' => 'https://instagram.com/...']) }}
                            </div>
                        </div>

                        <div class="preview-cover mb-3">
                            <img class="border-1" src="{{ asset('assets/img/default.jpg') }}" id="foto-avatar">
                        </div>
                        <div class="media-body m-auto">
                            <input id="input-foto-avatar" type="file" name="avatar"
                                class="d-none @error('avatar') is-invalid @enderror" accept="image/*" />
                            <label for="input-foto-avatar" class="btn btn-sm btn-dark rounded-0 btn-block">
                                <i class="fa fa-folder-open"></i> Pilih Foto Avatar
                            </label>
                            @error('avatar')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <a href="{{ route('master.team.index') }}" class="btn btn-primary btn-sm rounded-0">Kembali</a>
                        {{ Form::submit('Simpan', ['class' => 'btn btn-primary btn-sm rounded-0']) }}
                    </div>
                </div>
                {{ Form::close() }}
                @include('admin.partials._errors')
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            'use strict';

            var previewAvatar = $('#foto-avatar'),
                avatar = $('#input-foto-avatar');

            if (avatar) {
                avatar.on('change', function(e) {
                    var reader = new FileReader(),
                        files = e.target.files;
                    var fsize = files[0].size;
                    if (fsize > 2000000) {
                        alert("Ukuran foto terlalu besar, maximal 2mb");
                    } else {
                        reader.onload = function() {
                            if (previewAvatar) {
                                previewAvatar.attr('src', reader.result);
                            }
                        };
                        reader.readAsDataURL(files[0]);
                    }
                });
            }
        });
    </script>
@endsection
