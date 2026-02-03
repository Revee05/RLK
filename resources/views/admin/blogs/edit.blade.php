@extends('admin.partials._layout')
@section('title','Edit blog')
@section('collapseBlog','show')
@section('blogs','active')
@section('blog','active')
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
<script>
  window.BLOG_RAW_BODY = @json($blog->body ?? '');
  window.IMAGE_MAP = @json(
    isset($images)
      ? collect($images)->mapWithKeys(fn($img) => [
          $img->id => asset('uploads/blogs/' . $img->filename)
        ])
      : []
  );
</script>
@endsection