@extends('admin.partials._layout')
@section('title','Create posts')
@section('collapseBlog','show')
@section('addblog','active')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
.preview-container img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 5px;
    margin: 5px;
}
</style>
@endsection

@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <h1 class="h5 mb-4 text-gray-800">Tambah <small>Blog</small></h1>
    {{ Form::open(['route' => 'admin.blogs.store', 'files' => true]) }}
        @include('admin.blogs.form')
    {{ Form::close() }}
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
  $('#page').summernote({
      placeholder: 'Tulis konten blog...',
      height: 250,
      toolbar: [['style', ['bold', 'italic', 'underline']], ['para', ['ul', 'ol']]],
  });

  // Preview Multi Image
  $('#input-foto-blog').on('change', function(e) {
      const files = e.target.files;
      $('#preview-container').html('');
      Array.from(files).forEach(file => {
          const reader = new FileReader();
          reader.onload = e => $('#preview-container').append(`<img src="${e.target.result}" />`);
          reader.readAsDataURL(file);
      });
  });
</script>
@endsection