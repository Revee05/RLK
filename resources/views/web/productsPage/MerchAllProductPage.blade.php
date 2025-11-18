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
            <input type="text" class="form-control search-input rounded-pill ps-4 pe-5" placeholder="Search . . . ."
                style="height: 38px;" value="{{ request('search') }}">
            <button type="submit" id="search-btn" class="btn position-absolute end-0 top-50 translate-middle-y me-3"
                style="z-index:2; background:transparent; border:none; width:38px; height:38px; display:flex; align-items:center; justify-content:center;">
                <i class="fa fa-search text-secondary"></i>
            </button>
        </form>
        <!-- Filter kategori -->
        <div class="dropdown">
            <button class="btn btn-outline-secondary rounded-pill d-flex align-items-center gap-1 dropdown-toggle"
                type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="height:38px;">
                <i class="fa fa-filter"></i> <span id="filter-label">filters</span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="filterDropdown" id="category-dropdown">
                <li><a class="dropdown-item category-item" data-category="">Semua Kategori</a></li>
                {{-- Kategori akan diisi via JS --}}
            </ul>
        </div>
        <!-- Sort by -->
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
let currentCategory = "";
let currentSort = "";
let categoriesCached = null;
let categoriesVersionCached = null;

function scrollToGrid() {
    const grid = document.getElementById('products-grid');
    if (grid) {
        grid.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

function populateCategories(categories) {
    if (!categories || categories.length === 0) return;
    categoriesCached = categories;
    const dropdown = document.getElementById('category-dropdown');
    dropdown.innerHTML = `<li><a class="dropdown-item category-item" data-category="">Semua Kategori</a></li>`;
    categories.forEach(cat => {
        dropdown.innerHTML +=
            `<li><a class="dropdown-item category-item" data-category="${cat.slug}">${cat.name}</a></li>`;
    });
}

function renderProduct(product, idx) {
    let batchIdx = idx % 21;
    let cellClass = "cell";
    if (batchIdx === 0 || batchIdx === 8 || batchIdx === 16) cellClass += " span-2";
    let imageUrl = (product.images && product.images.length > 0 && product.images[0].image_path) ?
        `/${product.images[0].image_path}` :
        `https://placehold.co/300x250?text=${encodeURIComponent(product.name)}`;

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

function fetchProducts(batch = 1, search = "", category = "", sort = "") {
    if (isLoading) return;
    isLoading = true;
    let url = "{{ route('merch.products.json') }}?batch=" + batch;
    if (search) url += "&search=" + encodeURIComponent(search);
    if (category) url += "&category=" + encodeURIComponent(category);
    if (sort) url += "&sort=" + encodeURIComponent(sort);
    fetch(url)
        .then(res => res.json())
        .then(data => {
            // Cek versi kategori
            if (data.categories_version) {
                const localVersion = localStorage.getItem('merch_categories_version');
                if (localVersion !== data.categories_version) {
                    // Versi berubah, update cache & render ulang
                    if (data.categories && data.categories.length) {
                        localStorage.setItem('merch_categories', JSON.stringify(data.categories));
                        localStorage.setItem('merch_categories_version', data.categories_version);
                        categoriesCached = data.categories;
                        populateCategories(data.categories);
                    }
                }
            }

            const grid = document.getElementById('products-grid');
            if (batch === 1) {
                grid.innerHTML = "";
                scrollToGrid(); // scroll ke atas grid saat filter/search/sort baru
            }
            data.products.forEach((product, idx) => {
                if (product) {
                    grid.insertAdjacentHTML('beforeend', renderProduct(product, idx));
                }
            });
            if (!data.has_more_featured && !data.has_more_normal) {
                document.getElementById('load-more').style.display = 'none';
            } else {
                document.getElementById('load-more').style.display = '';
            }
            isLoading = false;
        })
        .catch(() => {
            isLoading = false;
        });
}

document.addEventListener('DOMContentLoaded', function() {
    // Cek localStorage untuk kategori & versinya
    const cached = localStorage.getItem('merch_categories');
    const cachedVersion = localStorage.getItem('merch_categories_version');
    if (cached && cachedVersion) {
        try {
            const categories = JSON.parse(cached);
            categoriesVersionCached = cachedVersion;
            if (Array.isArray(categories) && categories.length > 0) {
                categoriesCached = categories;
                populateCategories(categories);
            }
        } catch (e) {}
    }

    fetchProducts(currentBatch, currentSearch, currentCategory, currentSort);

    document.getElementById('load-more').addEventListener('click', function() {
        currentBatch++;
        fetchProducts(currentBatch, currentSearch, currentCategory, currentSort);
    });

    // Submit search
    const searchInput = document.querySelector('.search-input');
    document.getElementById('search-form').addEventListener('submit', function(e) {
        e.preventDefault();
        currentSearch = searchInput.value;
        currentBatch = 1;
        fetchProducts(currentBatch, currentSearch, currentCategory, currentSort);
    });

    // Filter kategori
    document.getElementById('category-dropdown').addEventListener('click', function(e) {
        if (e.target.classList.contains('category-item')) {
            currentCategory = e.target.getAttribute('data-category');
            document.getElementById('filter-label').textContent = e.target.textContent;
            // Highlight aktif
            document.querySelectorAll('#category-dropdown .category-item').forEach(item => item
                .classList.remove('active'));
            e.target.classList.add('active');
            currentBatch = 1;
            fetchProducts(currentBatch, currentSearch, currentCategory, currentSort);
        }
    });

    // Sort by
    document.getElementById('sort-dropdown').addEventListener('click', function(e) {
        if (e.target.classList.contains('sort-item')) {
            currentSort = e.target.getAttribute('data-sort');
            document.getElementById('sort-label').textContent = e.target.textContent;
            // Highlight aktif
            document.querySelectorAll('#sort-dropdown .sort-item').forEach(item => item.classList
                .remove('active'));
            e.target.classList.add('active');
            currentBatch = 1;
            fetchProducts(currentBatch, currentSearch, currentCategory, currentSort);
        }
    });
});
</script>
@endsection