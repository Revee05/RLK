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