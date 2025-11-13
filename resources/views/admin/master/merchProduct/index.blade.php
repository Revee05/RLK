{{-- filepath: resources/views/admin/master/merchProduct/index.blade.php --}}
@extends('admin.partials._layout')
@section('title','Daftar Merchandise Product')
@section('collapseMerch','show')
@section('merchproduct','active')
@section('content')
<div class="container">
    <h1>All Merchandise Products</h1>
    <a href="{{ route('master.merchProduct.create') }}" class="btn btn-success mb-3">Add Merchandise Product</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Categories</th>
                <th>Price</th>
                <th>Discount</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Images</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($merchProducts as $merchProduct)
            <tr>
                <td>{{ $merchProduct->name }}</td>
                <td>
                    @foreach($merchProduct->categories as $cat)
                        <span class="badge bg-info text-dark">{{ $cat->name }}</span>
                    @endforeach
                </td>
                <td>{{ $merchProduct->price }}</td>
                <td>{{ $merchProduct->discount }}</td>
                <td>{{ $merchProduct->stock }}</td>
                <td>{{ ucfirst($merchProduct->status) }}</td>
                <td>
                    @foreach($merchProduct->images as $img)
                        <img src="{{ asset('storage/'.$img->image_path) }}" alt="Image" width="40" class="mb-1">
                    @endforeach
                </td>
                <td>
                    <a href="{{ route('master.merchProduct.edit', $merchProduct->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('master.merchProduct.destroy', $merchProduct->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this product?')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">No merchandise products found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection