@extends('web.partials.layout')
@section('all-other-products','aktiv')

@section('css')
    <link href="{{ asset('css/MerchProductPage.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-4">
    <h2 class="text-center fw-bold mb-4">Products Design</h2>
    <div class="products-grid-header mb-4 d-flex align-items-center gap-2 flex-wrap">
        <form id="search-form" class="position-relative flex-grow-1 d-flex" style="max-width:600px;">
            <input 
                type="text" 
                class="form-control search-input rounded-pill ps-4 pe-5"
                placeholder="Search . . . ."
                style="height: 38px;"
                value="{{ request('search') }}"
            >
            <button type="submit" id="search-btn"
                class="btn position-absolute end-0 top-50 translate-middle-y me-3"
                style="z-index:2; background:transparent; border:none; width:38px; height:38px; display:flex; align-items:center; justify-content:center;">
                <i class="fa fa-search text-secondary"></i>
            </button>
        </form>
        <button type="button" class="btn btn-outline-secondary rounded-pill d-flex align-items-center gap-1" style="height:38px;">
            <i class="fa fa-filter"></i> filters
        </button>
        <button type="button" class="btn btn-outline-secondary rounded-pill d-flex align-items-center gap-1" style="height:38px;">
            <i class="fa fa-sort"></i> sort by
        </button>
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
let currentSearch = "";

function renderProduct(product, idx) {
    let batchIdx = idx % 21;
    let cellClass = "cell";
    if (batchIdx === 0 || batchIdx === 8 || batchIdx === 16) cellClass += " span-2";
    let imageUrl = (product.images && product.images.length > 0 && product.images[0].image_path)
        ? `/${product.images[0].image_path}`
        : `https://placehold.co/300x250?text=${encodeURIComponent(product.name)}`;

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

function fetchProducts(batch = 1, search = "") {
    if(isLoading) return;
    isLoading = true;
    let url = "{{ route('merch.products.json') }}?batch=" + batch;
    if (search) url += "&search=" + encodeURIComponent(search);
    fetch(url)
        .then(res => res.json())
        .then(data => {
            const grid = document.getElementById('products-grid');
            if(batch === 1) grid.innerHTML = "";
            data.products.forEach((product, idx) => {
                if (product) {
                    grid.insertAdjacentHTML('beforeend', renderProduct(product, idx));
                }
            });
            if(data.count < 21) {
                document.getElementById('load-more').style.display = 'none';
            } else {
                document.getElementById('load-more').style.display = '';
            }
            isLoading = false;
        })
        .catch(() => { isLoading = false; });
}

document.addEventListener('DOMContentLoaded', function() {
    fetchProducts(currentBatch, currentSearch);

    document.getElementById('load-more').addEventListener('click', function() {
        currentBatch++;
        fetchProducts(currentBatch, currentSearch);
    });

    // Hanya submit form yang trigger search
    const searchInput = document.querySelector('.search-input');
    document.getElementById('search-form').addEventListener('submit', function(e) {
        e.preventDefault();
        currentSearch = searchInput.value;
        currentBatch = 1;
        fetchProducts(currentBatch, currentSearch);
    });
});
</script>
@endsection