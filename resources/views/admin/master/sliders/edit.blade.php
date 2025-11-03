@extends('admin.partials._layout')
@section('title','Edit sliders')
@section('slider','active')
@section('css')
<style type="text/css">
.preview-cover {
height:350px;
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
    <h1 class="h5 mb-4 text-gray-800">Master
    <small>Slider</small>
    {{-- <a href="" class="btn btn-primary btn-sm float-right"><i class="fa fa-plus-circle"></i> Create</a> --}}
    </h1>
    <div class="row">
        <div class="col-sm-12">
            
            {{ Form::model($slider, array('route' => array('master.sliders.update', $slider->id), 'method' => 'PUT','files'=>true)) }}
            <div class="card rounded-0 border-0 shadow">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-sm-5">
                            
                            {{ Form::label('name', 'Name') }}
                            {{ Form::text('name', null, array('class' => 'form-control form-control-sm ','placeholder' => 'Name')) }}
                        </div>
                    </div>
                    <div class="preview-cover">
                        <img class="border-1" @if(isset($slider) && $slider) src="{{asset('uploads/sliders/'.$slider->image)}}" @else src="{{asset('assets/img/default.jpg')}}" @endif id="foto-slider">
                    </div>
                    <div class="media-body m-auto">
                        <input id="input-foto-slider" type="file" name="fotoslider" class="d-none @error('fotoslider') is-invalid @enderror" accept="image/*"/>
                        <label for="input-foto-slider" class="btn btn-sm btn-dark rounded-0 btn-block">
                            <i class="fa fa-folder-open"></i> Pilih Foto slider
                        </label>
                        @error('fotoslider')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <a href="{{ route('master.sliders.index') }}" class="btn btn-primary btn-sm rounded-0">Kembali</a>
                    {{ Form::submit('Simpan', array('class' => 'btn btn-primary btn-sm rounded-0')) }}
                    {{-- Erors notification --}}
                </div>
            </div>
            {{ Form::close() }}
            @include('admin.partials._errors')
        </div>
    </div>
</div>
<!-- /.container-fluid -->
@endsection
@section('js')
<script type="text/javascript">
       $(document).ready(function () {
          'use strict';

        //foto1
        var previewFotoslider = $('#foto-slider'),
            fotoslider = $('#input-foto-slider');

        //preview 1
        if (fotoslider) {
            fotoslider.on('change', function (e) {
                var reader = new FileReader(),
                files = e.target.files;
                var fsize = files[0].size;
                if(fsize > 2000000) {
                    alert("Ukuran foto terlalu besar, maximal 2mb");
                  } else {
                    reader.onload = function () {
                      if (previewFotoslider) {
                          previewFotoslider.attr('src', reader.result);
                      }
                  };
                    reader.readAsDataURL(files[0]);
                }
                  });
            } 
      });
</script>
@endsection