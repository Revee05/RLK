{{-- filepath: resources/views/admin/master/merchCategory/edit.blade.php --}}
@extends('admin.partials._layout')
@section('title','Edit Merch Category')
@section('collapseMerch','show')
@section('merchcategory','active')
@section('merch','active')
@section('content')
<div class="container">
    <h1>Edit Category</h1>
    @include('admin.master.merchCategory.form', ['mode' => 'edit', 'category' => $category])
</div>
@endsection