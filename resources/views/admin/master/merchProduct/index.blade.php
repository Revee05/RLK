{{-- filepath: resources/views/admin/master/merchProduct/index.blade.php --}}
@extends('admin.partials._layout')
@section('title','Daftar Merchandise Product')
@section('collapseMerch','show')
@section('merchproduct','active')

<style>
    .shrinkable-name {
        max-width: 150px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
        vertical-align: middle;
    }
    .shrinkable-category {
        max-width: 120px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
        vertical-align: middle;
    }
</style>

@section('content')
<div class="container">
    <h1>All Merchandise Products</h1>
    <a href="{{ route('master.merchProduct.create') }}" class="btn btn-success mb-3">Add Merchandise Product</a>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <table class="table table-bordered" id="merchProductTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Categories</th>
                <th>Type</th>
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
                <td>{{ $loop->iteration }}</td>
                <td><span class="shrinkable-name" title="{{ $merchProduct->name }}">{{ $merchProduct->name }}</span></td>
                <td>
                    <span class="shrinkable-category" title="{{ $merchProduct->categories->pluck('name')->join(', ') }}">
                        @foreach($merchProduct->categories as $cat)
                            <span class="badge bg-info text-dark">{{ $cat->name }}</span>
                        @endforeach
                    </span>
                </td>
                <td>
                    @if($merchProduct->type === 'featured')
                        <span class="badge bg-primary text-white">Featured</span>
                    @else
                        <span class="badge bg-secondary text-white">Normal</span>
                    @endif
                </td>
                <td>{{ $merchProduct->price }}</td>
                <td>{{ $merchProduct->discount }}</td>
                <td>{{ $merchProduct->stock }}</td>
                <td>
                    @if($merchProduct->status === 'active')
                        <span class="badge bg-success text-white">Publish</span>
                    @elseif($merchProduct->status === 'inactive')
                        <span class="badge bg-warning text-white">Draft</span>
                    @else
                        <span class="badge bg-light text-dark">{{ ucfirst($merchProduct->status) }}</span>
                    @endif
                </td>
                <td>
                    @foreach($merchProduct->images as $img)
                        <div class="d-inline-block text-center me-1">
                            <img src="{{ asset($img->image_path) }}" alt="Image" width="40" class="mb-1">
                            @if($img->label)
                                <div class="small">{{ $img->label }}</div>
                            @endif
                        </div>
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
                <td colspan="10" class="text-center">No merchandise products found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    var table = $('#merchProductTable').DataTable({
        "columnDefs": [
            { "orderable": false, "targets": [2,7,8] } // Disable sort for Categories, Images, Action
        ]
    });

    // Debounce untuk search
    let debounceTimer;
    $('#merchProductTable_filter input').off().on('input', function() {
        clearTimeout(debounceTimer);
        const searchTerm = this.value;
        debounceTimer = setTimeout(function() {
            table.search(searchTerm).draw();
        }, 1000); // 1000ms debounce
    });
});
</script>
@endpush