@extends('admin.partials._layout')
@section('title','Setting data')
@section('setting','active')
@section('css')
<style type="text/css">
.preview-cover {
    height:auto;
    width: 100%;
    overflow: hidden;
    position: relative;
    border: 1px solid  #5a5c69;
}
.preview-cover img{
    height:100%;
    width: 100%;
    object-fit: cover;
    object-position: center;
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
    <div class="row">
        <div class="col-sm-6">
            
            <div class="card shadow mb-4 rounded-0">
                <div class="card-body">
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
                    {{ Form::submit('Simpan', array('class' => 'btn btn-primary btn-sm rounded-0')) }}
                    
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card rounded-0 border-0">
                <div class="card-body p-0">
                    <div class="preview-cover">
                        <img class="border-1" @if(isset($setting) && $setting->logo) src="{{asset('uploads/logos/'.$setting->logo)}}" @else src="{{asset('assets/img/default.jpg')}}" @endif id="foto-logo">
                    </div>
                    <div class="media-body m-auto">
                        <input id="input-foto-logo" type="file" name="logo" class="d-none @error('logo') is-invalid @enderror" accept="image/*"/>
                        <label for="input-foto-logo" class="btn btn-sm btn-dark rounded-0 btn-block">
                            <i class="fa fa-folder-open"></i> Pilih Foto logo
                        </label>
                        <p>Logo Size : 169x70</p>
                        @error('logo')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
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
         $(document).ready(function () {
          'use strict';

        //foto1
        var previewFotologo = $('#foto-logo'),
            fotologo = $('#input-foto-logo');

        //preview 1
        if (fotologo) {
            fotologo.on('change', function (e) {
                var reader = new FileReader(),
                files = e.target.files;
                var fsize = files[0].size;
                if(fsize > 2000000) {
                    alert("Ukuran foto terlalu besar, maximal 2mb");
                  } else {
                    reader.onload = function () {
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