{{-- filepath: resources/views/admin/master/merchCategory/index.blade.php --}}
@extends('admin.partials._layout')
@section('title','Merch Categories')
@section('collapseMerch','show')
@section('merchcategory','active')
@section('content')
<div class="container">
    <h1>Merchandise Categories</h1>
    <a href="{{ route('master.merchCategory.create') }}" class="btn btn-success mb-3">Add Category</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Slug</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $cat)
            <tr>
                <td>{{ $cat->name }}</td>
                <td>{{ $cat->slug }}</td>
                <td>
                    <a href="{{ route('master.merchCategory.edit', $cat->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('master.merchCategory.destroy', $cat->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this category?')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">No categories found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection