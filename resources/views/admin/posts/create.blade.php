@extends('admin.partials._layout')
@section('title','Create posts')
@section('collapseadmin','show')
@section('post','active')
@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css">
@endsection
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h5 mb-4 text-gray-800">Add
    <small>Posts</small>
    {{-- <a href="" class="btn btn-primary btn-sm float-right"><i class="fa fa-plus-circle"></i> Create</a> --}}
    </h1>
    {{ Form::open(array('route' => 'admin.posts.store')) }}
        @include('admin.posts.form') 
    @include('admin.partials._errors')
    {{ Form::close() }}
</div>
<!-- /.container-fluid -->
@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
    <script type="text/javascript">
        $('#page').summernote({
            placeholder: 'Tulis content page...',
            toolbar: [
                ['style', ['bold', 'italic', 'underline']],
            ],
            height: 250
        });
    </script>
@endsection