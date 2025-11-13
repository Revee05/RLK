{{-- filepath: resources/views/admin/master/merchCategory/create.blade.php --}}
@extends('admin.partials._layout')
@section('title','Add Merch Category')
@section('collapseMerch','show')
@section('merchcategory','active')
@section('content')
<div class="container">
    <h1>Add Category</h1>
    @include('admin.master.merchCategory.form', ['mode' => 'create'])
</div>
@endsection