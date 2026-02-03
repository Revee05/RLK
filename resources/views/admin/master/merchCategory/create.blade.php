{{-- filepath: resources/views/admin/master/merchCategory/create.blade.php --}}
@extends('admin.partials._layout')
@section('title','Add Merch Category')
@section('collapseMerch','show')
@section('merchcategory','active')
@section('merch','active')
@section('content')
<div class="container">
    <h4>Tambah Kategori Merchandise</h4>
    @include('admin.master.merchCategory.form')
</div>
@endsection

@push('scripts')
    @stack('scripts')
@endpush