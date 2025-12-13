@extends('admin.partials._layout')
@section('title','Tambah Seniman')
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
    border-radius: 8px;
}
.preview-cover img{
    height:100%;
    width: 100%;
    object-fit: cover;
    object-position: center;
}
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css">
@endsection
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-plus"></i> Tambah Seniman Baru
        </h1>
        <a href="{{ route('master.karya.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>
    <div class="row">
        <div class="col-sm-12">
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-edit"></i> Form Data Seniman
                    </h6>
                </div>
                <div class="card-body">
                    {{ Form::open(array('route' => 'master.karya.store','files'=>true)) }}
                    @include('admin.master.karya.form')
                    <hr class="mt-4 mb-4">
                    <div class="form-group mb-0">
                        <a href="{{ route('master.karya.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        {{ Form::submit('Simpan', array('class' => 'btn btn-primary')) }}
                    </div>
                    {{ Form::close() }}

                    {{-- Erors notification --}}
                    @include('admin.partials._errors')
                       
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
        $('#bio-singkat').summernote({
            placeholder: 'Tulis bio singkat untuk display hooks...',
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough']],
                ['fontsize', ['fontsize']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                
            ],
            height: 100
        });
                
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
        
        // Live preview untuk address (extract city from end)
        $('input[name="address"]').on('input', function(){
            var address = $(this).val();
            var city = '';
            if(address) {
                var parts = address.split(',');
                city = parts.length > 0 ? parts[parts.length - 1].trim() : address;
            }
            $('#preview-location').text(city || 'Nama kota muncul di sini...');
        });
        
        // Live preview untuk bio singkat (summernote)
        $('#bio-singkat').on('summernote.change', function(we, contents) {
            $('#preview-bio').html(contents || 'Bio singkat akan muncul di sini...');
        });
      });
    </script>
@endsection