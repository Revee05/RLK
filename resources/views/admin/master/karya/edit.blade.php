@extends('admin.partials._layout')
@section('title','Edit karya')
@section('collapseMaster','show')
@section('karya','active')
@section('css')
<style type="text/css">
.preview-cover {
    height:160px;
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
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css">
@endsection
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h5 mb-4 text-gray-800">Master
    <small>karya</small>
    {{-- <a href="" class="btn btn-primary btn-sm float-right"><i class="fa fa-plus-circle"></i> Create</a> --}}
    </h1>
    <div class="row">
        <div class="col-sm-12">
            
            <div class="card shadow mb-4 rounded-0">
                <div class="card-body">
                    {{ Form::model($karya, array('route' => array('master.karya.update', $karya->id), 'method' => 'PUT','files'=>true)) }}
                    @include('admin.master.karya.form')
                    <a href="{{ route('master.karya.index') }}" class="btn btn-primary btn-sm rounded-0">Kembali</a>
                    {{ Form::submit('Simpan', array('class' => 'btn btn-primary btn-sm rounded-0')) }}
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->
@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
    <script type="text/javascript">
        $('#biografi').summernote({
            placeholder: 'Tulis biografi...',
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough']],
                ['fontsize', ['fontsize']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
            ],
            height: 150
        });
        
        $('#bio-singkat').summernote({
            placeholder: 'Tulis bio singkat...',
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough']],
                ['fontsize', ['fontsize']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                
            ],
            height: 100
        });
        
        function getVal() {
          const val = document.querySelector('#tiktok').value;
          console.log(val);
        }
         $(document).ready(function () {
          'use strict';

        //foto1
        var previewFotoSeniman = $('#foto-seniman'),
            fotoSeniman = $('#input-foto-seniman');

        //preview 1
        if (fotoSeniman) {
            fotoSeniman.on('change', function (e) {
                var reader = new FileReader(),
                files = e.target.files;
                var fsize = files[0].size;
                if(fsize > 2000000) {
                    alert("Ukuran foto terlalu besar, maximal 2mb");
                  } else {
                    reader.onload = function () {
                      if (previewFotoSeniman) {
                          previewFotoSeniman.attr('src', reader.result);
                      }
                      // Update preview card image
                      $('#preview-image').attr('src', reader.result);
                  };
                    reader.readAsDataURL(files[0]);
                }
                  });
            }
            
        // Live preview untuk nama
        $('input[name="name"]').on('input', function(){
            $('#preview-name').text($(this).val() || 'Nama Seniman');
        });
        
        // Live preview untuk address
        $('input[name="address"]').on('input', function(){
            $('#preview-location').text($(this).val() || 'Kota Asal');
        });
        
        // Live preview untuk bio singkat (summernote)
        $('#bio-singkat').on('summernote.change', function(we, contents) {
            $('#preview-bio').html(contents || 'Bio singkat akan muncul di sini...');
        });
      });
    </script>
@endsection