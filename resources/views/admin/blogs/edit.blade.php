@extends('admin.partials._layout')
@section('title','Edit blog')
@section('collapseBlog','show')
@section('blogs','active')
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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h5 mb-4 text-gray-800">Master
    <small>blog</small>
    {{-- <a href="" class="btn btn-primary btn-sm float-right"><i class="fa fa-plus-circle"></i> Create</a> --}}
    </h1>
    {{ Form::model($blog, array('route' => array('admin.blogs.update', $blog->id), 'method' => 'PUT','files'=>true)) }}
        @include('admin.blogs.form') 
    @include('admin.partials._errors')
    {{ Form::close() }}
</div>
<!-- /.container-fluid -->
@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
    <script type="text/javascript">
        $('#page').summernote({
            placeholder: 'Tulis content page...',
            toolbar: [
                ['style', ['bold', 'italic', 'underline']],
            ],
            height: 250
        });
        //preview foto blog
        $(document).ready(function () {
          'use strict';

        //foto1
        var previewFotoblog = $('#foto-blog'),
            fotoblog = $('#input-foto-blog');

        //preview 1
        if (fotoblog) {
            fotoblog.on('change', function (e) {
                var reader = new FileReader(),
                files = e.target.files;
                var fsize = files[0].size;
                if(fsize > 2000000) {
                    alert("Ukuran foto terlalu besar, maximal 2mb");
                  } else {
                    reader.onload = function () {
                      if (previewFotoblog) {
                          previewFotoblog.attr('src', reader.result);
                      }
                  };
                    reader.readAsDataURL(files[0]);
                }
                  });
            } 
      });
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    $(document).ready(function(){

      $( "#selTag" ).select2({
        tags: true,
        tokenSeparators: [","],
        ajax: { 
          url: "{{route('admin.blogs.tagpost')}}",
          type: "post",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              _token: CSRF_TOKEN,
              search: params.term // search term
            };
          },
          processResults: function (response) {
            return {
              results: response
            };
          },
          cache: true
        }

      });

    });
    </script>
<script>
  window.IMAGE_MAP = @json(
    $blog->contentImages
      ->pluck('url', 'id')
  );
</script>
@endsection