@extends('web.partials.layout')
@section('all-other-products','aktiv')

@section('css')
    <link href="{{ asset('css/MerchProductPage.css') }}" rel="stylesheet">
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
    if (batchIdx === 0 || batchIdx === 8 || batchIdx === 16) cellClass += " span-2";
    let imageUrl = (product.images && product.images.length > 0 && product.images[0].image_path)
        ? `/${product.images[0].image_path}`
        : `https://placehold.co/300x250?text=${encodeURIComponent(product.name)}`;

    // Hitung harga setelah diskon jika ada
    let priceHtml = '';
    if (product.discount && product.discount > 0) {
        let discountedPrice = Math.round(product.price * (1 - product.discount / 100));
        priceHtml = `
            <span class="product-price">Rp ${discountedPrice.toLocaleString('id-ID')}</span>
            <span class="product-price-original">Rp ${Number(product.price).toLocaleString('id-ID')}</span>
        `;
    } else {
        priceHtml = `<span class="product-price">Rp ${Number(product.price).toLocaleString('id-ID')}</span>`;
    }

    // render view
    return `
    <a href="/merch/${product.slug}" class="${cellClass}" style="text-decoration:none; color:inherit;">
        <div class="card product-card h-100">
            ${product.discount ? `<div class="discount-badge">-${product.discount}%</div>` : ''}
            <img src="${imageUrl}" class="card-img-top" alt="${product.name}">
            <div class="card-body text-left p-2">
                <div class="product-title">${product.name}</div>
                <div>${priceHtml}</div>
            </div>
        </div>
    </a>
    `;
}

function fetchProducts(batch = 1) {
    if(isLoading) return;
    isLoading = true;
    fetch("{{ route('merch.products.json') }}?batch=" + batch)
        .then(res => res.json())
        .then(data => {
            const grid = document.getElementById('products-grid');
            data.products.forEach((product, idx) => {
                if (product) {
                    grid.insertAdjacentHTML('beforeend', renderProduct(product, idx));
                } else {
                    // Optional: render cell kosong jika ingin grid tetap rapat
                    // grid.insertAdjacentHTML('beforeend', `<div class="cell${([0,8,16].includes(idx) ? ' span-2' : '')}"></div>`);
                }
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