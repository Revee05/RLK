@extends('admin.partials._layout')
@section('title', $panduan->title)

@section('content')
<div class="container mt-4">
    <h3>{{ $panduan->title }}</h3>

    <iframe src="{{ asset($panduan->file_path) }}"
            width="100%" height="700px">
    </iframe>
</div>
@endsection
