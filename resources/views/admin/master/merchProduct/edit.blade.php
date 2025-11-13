{{-- filepath: resources/views/admin/master/merchProduct/edit.blade.php --}}
@extends('admin.partials._layout')
@section('title','Edit Merch Product')
@section('collapseMerch','show')
@section('merchproduct','active')
@section('content')
<div class="container">
    <h1>Edit Merchandise Product</h1>
    @include('admin.master.merchProduct.form', ['mode' => 'edit', 'merchProduct' => $merchProduct, 'categories' => $categories])
</div>
@endsection