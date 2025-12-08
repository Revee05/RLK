@extends('web.partials.layout')
@section('all-other-products','aktiv')

@section('css')
<link href="{{ asset('css/MerchProductPage.css') }}" rel="stylesheet">
@endsection

@section('content')

@php
    $favoriteProductIds = \App\Favorite::where('user_id', Auth::id())->pluck('product_id')->toArray();
@endphp

<script>
    const userFavorites = @json($favoriteProductIds);
</script>

<div class="container py-4">
    <h2 class="text-center fw-bold mb-4">Products Design</h2>
    <div class="products-grid-header mb-4 d-flex align-items-center gap-2 flex-wrap">
        <form id="search-form" class="position-relative flex-grow-1 d-flex" style="max-width:600px;">
            <input type="text" class="form-control search-input rounded-pill ps-4 pe-5"
                   placeholder="Search . . . ." style="height: 38px;" value="{{ request('search') }}">
            <button type="submit" id="search-btn"
                    class="btn position-absolute end-0 top-50 translate-middle-y me-3"
                    style="z-index:2; background:transparent; border:none; width:38px; height:38px;
                           display:flex; align-items:center; justify-content:center;">
                <i class="fa fa-search text-secondary"></i>
            </button>
        </form>

        <div class="dropdown">
            <button class="btn btn-outline-secondary rounded-pill d-flex align-items-center gap-1 dropdown-toggle"
                    type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="height:38px;">
                <i class="fa fa-filter"></i> <span id="filter-label">filters</span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="filterDropdown" id="category-dropdown">
                <li><a class="dropdown-item category-item" data-category="">Semua Kategori</a></li>
            </ul>
        </div>

        <div class="dropdown">
            <button class="btn btn-outline-secondary rounded-pill d-flex align-items-center gap-1 dropdown-toggle"
                    type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="height:38px;">
                <i class="fa fa-sort"></i> <span id="sort-label">sort by</span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="sortDropdown" id="sort-dropdown">
                <li><a class="dropdown-item sort-item" data-sort="">Default</a></li>
                <li><a class="dropdown-item sort-item" data-sort="newest">Product Terbaru</a></li>
                <li><a class="dropdown-item sort-item" data-sort="oldest">Product Terlama</a></li>
                <li><a class="dropdown-item sort-item" data-sort="cheapest">Product Termurah</a></li>
                <li><a class="dropdown-item sort-item" data-sort="priciest">Product Termahal</a></li>
            </ul>
        </div>
    </div>

    <div id="products-grid" class="products-grid-parent"></div>

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
let currentCategory = "";
let currentSort = "";

function renderProduct(product, idx) {
    let batchIdx = idx % 21;
    let cellClass = "cell";
    if (batchIdx === 0 || batchIdx === 8 || batchIdx === 16) cellClass += " span-2";

    let imageUrl = product.image
        ? `/${product.image}`
        : `https://placehold.co/300x250?text=${encodeURIComponent(product.name)}`;

    const isFavorite = userFavorites.includes(product.id);
    const favoriteIcon = isFavorite ? "/icons/heart_fill.svg" : "/icons/heart_outline.svg";

    let priceHtml = product.price !== null
        ? `<span class="product-price">Rp ${Number(product.price).toLocaleString('id-ID')}</span>`
        : `<span class="product-price">-</span>`;

    return `
    <div class="${cellClass}">
        <div class="card product-card h-100 position-relative">
            <div class="product-image-wrapper position-relative">
                <img src="${imageUrl}" class="card-img-top" alt="${product.name}">
                <img src="${favoriteIcon}" data-id="${product.id}" class="favorite-icon" alt="Favorite">
            </div>
            <a href="/merch/${product.slug}" style="text-decoration:none; color:inherit;">
                <div class="card-body text-left p-2">
                    <div class="product-title">${product.name}</div>
                    <div>${priceHtml}</div>
                </div>
            </a>
        </div>
    </div>`;
}

function fetchProducts(batch = 1, search = "", category = "", sort = "") {
    if (isLoading) return;
    isLoading = true;

    let url = "{{ route('merch.products.json') }}?batch=" + batch;
    if (search) url += "&search=" + search;
    if (category) url += "&category=" + category;
    if (sort) url += "&sort=" + sort;

    fetch(url)
        .then(res => res.json())
        .then(data => {
            const grid = document.getElementById('products-grid');
            if (batch === 1) grid.innerHTML = "";
            data.products.forEach((product, idx) => {
                grid.insertAdjacentHTML('beforeend', renderProduct(product, idx));
            });
            document.getElementById('load-more').style.display =
                (!data.has_more_featured && !data.has_more_normal) ? 'none' : '';
            isLoading = false;
        })
        .catch(() => {
            isLoading = false;
        });
}

document.addEventListener('DOMContentLoaded', function() {
    fetchProducts(currentBatch, currentSearch, currentCategory, currentSort);

    document.getElementById('load-more').addEventListener('click', function() {
        currentBatch++;
        fetchProducts(currentBatch, currentSearch, currentCategory, currentSort);
    });
});

// Toggle Favorite
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('favorite-icon')) {
        const icon = e.target;
        const productId = icon.getAttribute('data-id');

        fetch("{{ route('favorite.toggle') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === "added") {
                icon.src = "/icons/heart_fill.svg";
                userFavorites.push(parseInt(productId));
            } else {
                icon.src = "/icons/heart_outline.svg";
                const index = userFavorites.indexOf(parseInt(productId));
                if (index > -1) userFavorites.splice(index, 1);
            }
        });
    }
});
</script>
@endsection
