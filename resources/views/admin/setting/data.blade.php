@extends('admin.partials._layout')
@section('title','Setting data')
@section('setting','active')
@section('css')
<style type="text/css">
.preview-cover {
    height: auto;
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

/* Separator between form and logo column */
.logo-column {
    border-left: 1px solid #e3e6f0;
    padding-left: 20px;
}

@media (max-width: 767.98px) {
    .logo-column {
        border-left: none;
        padding-left: 0;
        margin-top: 15px;
    }
}
</style>
@endsection
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h5 mb-4 text-gray-800">Setting
        <small>Aplikasi</small>
        {{-- <a href="" class="btn btn-primary btn-sm float-right"><i class="fa fa-plus-circle"></i> Create</a> --}}
    </h1>
    {{ Form::open(array('route' => 'setting.update.data','files'=>true)) }}
    <div class="card shadow mb-4 rounded-0">
        <div class="card-body">
            <div class="row">

                <!-- kolom kiri -->
                <div class="col-md-8">
                    <div class="form-group">
                        {{ Form::label('name', 'Title') }}
                        {{ Form::text('title', $setting->title, array('class' => 'form-control form-control-sm ','placeholder' => 'Nama Brand')) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('name', 'Tagline') }}
                        {{ Form::text('tagline', $setting->tagline, array('class' => 'form-control form-control-sm ','placeholder' => 'Nama Brand')) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('name', 'Alamat') }}
                        {{ Form::textarea('address', $setting->address, array('class' => 'form-control form-control-sm ','placeholder' => 'Nama Brand','rows'=>'3')) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('name', 'Phone') }}
                        {{ Form::text('phone', $setting->phone, array('class' => 'form-control form-control-sm ','placeholder' => 'Nama Brand')) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('name', 'WA') }}
                        {{ Form::text('wa', $setting->wa, array('class' => 'form-control form-control-sm ','placeholder' => 'Nama Brand')) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('name', 'Email') }}
                        {{ Form::text('email', $setting->email, array('class' => 'form-control form-control-sm ','placeholder' => 'Nama Brand')) }}
                    </div>
                    <input type="hidden" name="id" value="{{$setting->id}}">
                </div>

                <!-- kolom kanan -->
                <div class="col-md-4 logo-column d-flex flex-column">
                    <div class="preview-cover mb-3">
                        <img class="border-1" @if(isset($setting) && $setting->logo)
                        src="{{asset('uploads/logos/'.$setting->logo)}}" @else src="{{asset('assets/img/default.jpg')}}"
                        @endif id="foto-logo">
                    </div>
                    <div class="media-body">
                        <input id="input-foto-logo" type="file" name="logo"
                            class="d-none @error('logo') is-invalid @enderror" accept="image/*" />
                        <label for="input-foto-logo" class="btn btn-sm btn-dark rounded-0 btn-block">
                            <i class="fa fa-folder-open"></i> Pilih Foto logo
                        </label>
                        <p class="mt-2">Logo Size : 169x70</p>
                        @error('logo')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="mt-auto text-right">
                        {{ Form::submit('Simpan', array('class' => 'btn btn-primary btn-sm rounded-0')) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ Form::close() }}
    {{-- Erors notification --}}
    @include('admin.partials._errors')
</div>
<!-- /.container-fluid -->
@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    'use strict';

    //foto1
    var previewFotologo = $('#foto-logo'),
        fotologo = $('#input-foto-logo');

    //preview 1
    if (fotologo) {
        fotologo.on('change', function(e) {
            var reader = new FileReader(),
                files = e.target.files;
            var fsize = files[0].size;
            if (fsize > 2000000) {
                alert("Ukuran foto terlalu besar, maximal 2mb");
            } else {
                reader.onload = function() {
                    if (previewFotologo) {
                        previewFotologo.attr('src', reader.result);
                    }
                };
                reader.readAsDataURL(files[0]);
            }
        });
    }
});
</script>
@endsection