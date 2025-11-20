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
                <th>Variants</th>
                <th>Images</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($merchProducts as $merchProduct)
            <tr>
                <td>{{ $loop->iteration }}</td>
                {{-- Nama Produk --}}
                <td><span class="shrinkable-name" title="{{ $merchProduct->name }}">{{ $merchProduct->name }}</span></td>
                {{-- Kategori --}}
                <td>
                    <span class="shrinkable-category" title="{{ $merchProduct->categories->pluck('name')->join(', ') }}">
                        @foreach($merchProduct->categories as $cat)
                            <span class="badge bg-info text-dark">{{ $cat->name }}</span>
                        @endforeach
                    </span>
                </td>
                {{-- Tipe Produk --}}
                <td>
                    @if($merchProduct->type === 'featured')
                        <span class="badge bg-primary text-white">Featured</span>
                    @else
                        <span class="badge bg-secondary text-white">Normal</span>
                    @endif
                </td>
                {{-- Harga Dasar (bisa harga minimum dari semua size) --}}
                <td>
                    @php
                        $prices = [];
                        foreach ($merchProduct->variants as $variant) {
                            if ($variant->sizes->count()) {
                                foreach ($variant->sizes as $size) {
                                    if (!is_null($size->price)) $prices[] = $size->price;
                                }
                            } else {
                                if (!is_null($variant->price)) $prices[] = $variant->price;
                            }
                        }
                        $minPrice = count($prices) ? min($prices) : null;
                    @endphp
                    {{ $minPrice !== null ? number_format($minPrice, 0, ',', '.') : '-' }}
                </td>
                {{-- Diskon (bisa diskon terbesar dari semua size) --}}
                <td>
                    @php
                        $maxDiscount = $merchProduct->variants->flatMap(function($variant) {
                            return $variant->sizes;
                        })->max('discount');
                    @endphp
                    {{ $maxDiscount ?? $merchProduct->discount }}
                </td>
                {{-- Total Stock --}}
                <td>
                    {{
                        $merchProduct->variants->flatMap(function($variant) {
                            return $variant->sizes;
                        })->sum('stock')
                    }}
                </td>
                {{-- Status --}}
                <td>
                    @if($merchProduct->status === 'active')
                        <span class="badge bg-success text-white">Publish</span>
                    @elseif($merchProduct->status === 'inactive')
                        <span class="badge bg-warning text-white">Draft</span>
                    @else
                        <span class="badge bg-light text-dark">{{ ucfirst($merchProduct->status) }}</span>
                    @endif
                </td>
                {{-- Daftar Variant --}}
                <td>
                    @foreach($merchProduct->variants as $variant)
                        <span class="badge bg-light text-dark mb-1">{{ $variant->name }}</span>
                    @endforeach
                </td>
                {{-- Gambar utama per variant --}}
                <td>
                    @foreach($merchProduct->variants as $variant)
                        @if($variant->images->first())
                            <div class="d-inline-block text-center me-1">
                                <img src="{{ asset($variant->images->first()->image_path) }}" alt="Image" width="40" class="mb-1">
                                @if($variant->images->first()->label)
                                    <div class="small">{{ $variant->images->first()->label }}</div>
                                @endif
                                <div class="small text-muted">{{ $variant->name }}</div>
                            </div>
                        @endif
                    @endforeach
                </td>
                {{-- Action --}}
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
                <td colspan="11" class="text-center">No merchandise products found.</td>
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
            { "orderable": false, "targets": [2,8,9,10] } // Disable sort for Categories, Variants, Images, Action
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