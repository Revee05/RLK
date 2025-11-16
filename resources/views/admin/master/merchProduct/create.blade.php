{{-- filepath: resources/views/admin/master/merchProduct/create.blade.php --}}
@extends('admin.partials._layout')
@section('title','Create Merch Product')
@section('collapseMerch','show')
@section('addmerchproduct','active')
@section('content')
<div class="container">
    <h1>Add Merchandise Product</h1>
    @include('admin.master.merchProduct.form', ['mode' => 'create', 'categories' => $categories])
</div>
@endsection

@section('js')
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>
<script>
$(document).ready(function() {
    $('#deskripsi').summernote({
        placeholder: 'Tulis deskripsi produk disini...',
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
        ],
        height: 150
    });
});
</script>
@endsection