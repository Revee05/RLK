@extends('web.partials.layout')
@section('all-other-products','aktiv')

@section('css')
    <link href="{{ asset('css/MerchProductPage.css') }}" rel="stylesheet">
    <style>
        .cell.span-2 {
            grid-column: span 2;
        }
    </style>
@endsection

@section('content')
<div class="container py-4">
    <h2 class="text-center fw-bold mb-4">Products Design</h2>
    <div class="products-grid-header mb-4">
        <input type="text" class="form-control search-input" placeholder="Search Product...">
        <div class="d-flex">
            <button class="btn btn-outline-secondary btn-sm">Filter</button>
            <button class="btn btn-outline-secondary btn-sm">Sort</button>
        </div>
    </div>
    <div id="products-grid" class="products-grid-parent">
        <!-- Produk akan dimunculkan di sini -->
    </div>
    <div class="text-center mt-4">
        <button id="load-more" class="btn btn-primary">Load More</button>
    </div>
</div>
@endsection

@section('js')
<script>
let currentBatch = 1;
let isLoading = false;

// Index cell yang span 2 pada setiap batch 21 data
// Baris 1: 0 (span2), 1, 2, 3
// Baris 2: 4, 5, 6, 7
// Baris 3: 8 (span1), 9 (span2), 10 (span1)
// dst
function renderProduct(product, idx) {
    let batchIdx = idx % 21;
    let cellClass = "cell";
    // Atur cell span-2 sesuai pola
    if (batchIdx === 0 || batchIdx === 8 || batchIdx === 16) cellClass += " span-2";
    let imageUrl = (product.images && product.images.length > 0 && product.images[0].image_path)
        ? `/${product.images[0].image_path}`
        : `https://placehold.co/300x250?text=${encodeURIComponent(product.name)}`;
    return `
    <div class="${cellClass}">
        <div class="card product-card h-100">
            <img src="${imageUrl}" class="card-img-top" alt="${product.name}">
            <div class="card-body text-left p-2">
                <div class="product-title">${product.name}</div>
                <div class="product-price">Rp ${Number(product.price).toLocaleString('id-ID')}</div>
                <div class="product-stock">Stok: ${product.stock}</div>
                ${product.discount ? `<div class="product-discount">Diskon: ${product.discount}%</div>` : ''}
            </div>
        </div>
    </div>
    `;
}

function fetchProducts(batch = 1) {
    if(isLoading) return;
    isLoading = true;
    fetch("{{ route('merch.products.batch') }}?batch=" + batch)
        .then(res => res.json())
        .then(data => {
            const grid = document.getElementById('products-grid');
            data.products.forEach((product, idx) => {
                grid.insertAdjacentHTML('beforeend', renderProduct(product, (batch-1)*21 + idx));
            });
            if(data.count < 21) {
                document.getElementById('load-more').style.display = 'none';
            }
            isLoading = false;
        })
        .catch(() => { isLoading = false; });
}

document.addEventListener('DOMContentLoaded', function() {
    fetchProducts(currentBatch);

    document.getElementById('load-more').addEventListener('click', function() {
        currentBatch++;
        fetchProducts(currentBatch);
    });
});
</script>
@endsection