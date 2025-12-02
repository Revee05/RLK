@extends('web.partials.layout')
@section('lelang','aktiv')
@section('css')
<link href="{{ asset('css/lelang/LelangProductPage.css') }}" rel="stylesheet">
<style type="text/css">
.mark-lelang {
    position: absolute;
    z-index: 10;
    top: 10px;
    right: 10px;
}
</style>
@endsection
@section('content')
<section class="py-4">
    <div class="container">
        <div class="products-grid-header mb-4 d-flex align-items-center gap-2 flex-wrap">
            <form id="search-form" class="position-relative flex-grow-1 d-flex" style="max-width:600px;">
                <input type="text" class="form-control search-input rounded-pill ps-4 pe-5" placeholder="Search . . . ."
                    style="height: 38px;">
                <button type="submit" id="search-btn" class="btn position-absolute end-0 top-50 translate-middle-y me-3"
                    style="z-index:2; background:transparent; border:none; width:38px; height:38px; display:flex; align-items:center; justify-content:center;">
                    <i class="fa fa-search text-secondary"></i>
                </button>
            </form>
            <div class="dropdown">
                <button class="btn btn-outline-secondary rounded-pill d-flex align-items-center gap-1 dropdown-toggle"
                    type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                    style="height:38px;">
                    <i class="fa fa-filter"></i> <span id="filter-label">filters</span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="filterDropdown" id="category-dropdown">
                    <li><a class="dropdown-item category-item" data-category="">Semua Kategori</a></li>
                </ul>
            </div>
            <div class="dropdown">
                <button class="btn btn-outline-secondary rounded-pill d-flex align-items-center gap-1 dropdown-toggle"
                    type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                    style="height:38px;">
                    <i class="fa fa-sort"></i> <span id="sort-label">sort by</span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="sortDropdown" id="sort-dropdown">
                    <li><a class="dropdown-item sort-item" data-sort="">Default</a></li>
                    <li><a class="dropdown-item sort-item" data-sort="newest">Produk Terbaru</a></li>
                    <li><a class="dropdown-item sort-item" data-sort="oldest">Produk Terlama</a></li>
                    <li><a class="dropdown-item sort-item" data-sort="cheapest">Produk Termurah</a></li>
                    <li><a class="dropdown-item sort-item" data-sort="priciest">Produk Termahal</a></li>
                </ul>
            </div>
        </div>
        <div id="products-grid" class="products-grid-parent">
            {{-- Produk akan dimunculkan via JS --}}
        </div>
        <div class="text-center mt-4">
            <button id="load-more" class="btn btn-primary">Load More</button>
        </div>
    </div>

    @push('scripts')
    <script>
    let currentBatch = 1;
    let isLoading = false;
    let currentSearch = "";
    let currentCategory = "";
    let currentSort = "";

    // Fetch and populate lelang categories
    function fetchCategories() {
        fetch("{{ route('lelang.categories') }}")
            .then(res => res.json())
            .then(data => {
                const dropdown = document.getElementById('category-dropdown');
                dropdown.innerHTML = `<li><a class=\"dropdown-item category-item\" data-category=\"\">Semua Kategori</a></li>`;
                if (data.categories && data.categories.length) {
                    data.categories.forEach(cat => {
                        dropdown.innerHTML += `<li><a class=\"dropdown-item category-item\" data-category=\"${cat.slug}\">${cat.name}</a></li>`;
                    });
                }
            });
    }

    function renderProduct(product, idx) {
        if (!product) return '';
        
        let batchIdx = idx % 21;
        let cellClass = "cell";
        if (batchIdx === 0 || batchIdx === 8 || batchIdx === 16) cellClass += " span-2";

        let imageUrl = product.image ? `/${product.image}` : 'assets/img/default.jpg';

        // Jika ada waktu lelang, tampilkan badge waktu (opsional)
        let badgeWaktu = product.end_time
            ? `<div class="lelang-timer-badge">${product.end_time}</div>`
            : '';

        return `
        <a href="/lelang/${product.slug}" class="${cellClass}" style="text-decoration:none; color:inherit;">
            <div class="card product-card h-100">
                ${badgeWaktu}
                <img src="${imageUrl}" class="card-img-top" alt="${product.title}">
                <div class="card-body text-left p-2">
                    <div class="product-title">${product.title}</div>
                    <div class="text-muted small mb-1">Bidding Tertinggi:</div>
                    <div class="product-price">Rp. ${product.price_str}</div>
                </div>
            </div>
        </a>
        `;
    }

    function fetchProducts(batch = 1, search = "", category = "", sort = "") {
        if (isLoading) return;
        isLoading = true;
        let url = "{{ route('lelang.products.json') }}?batch=" + batch;
        if (search) url += "&search=" + encodeURIComponent(search);
        if (category) url += "&category=" + encodeURIComponent(category);
        if (sort) url += "&sort=" + encodeURIComponent(sort);

        // Disable tombol submit saat loading
        document.getElementById('search-btn').disabled = true;

        fetch(url)
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(data => {
                const grid = document.getElementById('products-grid');
                if (batch === 1) grid.innerHTML = "";
                if (data.products && data.products.length > 0) {
                    data.products.forEach((product, idx) => {
                        if (product) {
                            grid.insertAdjacentHTML('beforeend', renderProduct(product, idx));
                        }
                    });
                } else {
                    if (batch === 1) {
                        grid.innerHTML = '<div class="col-12 text-center text-muted">Tidak ada produk ditemukan.</div>';
                    }
                }
                if (!data.has_more_featured && !data.has_more_normal) {
                    document.getElementById('load-more').style.display = 'none';
                } else {
                    document.getElementById('load-more').style.display = '';
                }
                isLoading = false;
                document.getElementById('search-btn').disabled = false;
            })
            .catch((err) => {
                const grid = document.getElementById('products-grid');
                if (batch === 1) {
                    grid.innerHTML = '<div class="col-12 text-center text-danger">Gagal memuat produk: ' + err.message + '</div>';
                }
                isLoading = false;
                document.getElementById('search-btn').disabled = false;
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        fetchCategories(); // <-- Tambahkan ini
        fetchProducts(currentBatch, currentSearch, currentCategory, currentSort);

        document.getElementById('load-more').addEventListener('click', function() {
            currentBatch++;
            fetchProducts(currentBatch, currentSearch, currentCategory, currentSort);
        });

        const searchInput = document.querySelector('.search-input');
        const searchError = document.createElement('div');
        searchError.className = 'text-danger small mt-1';
        searchInput.parentNode.appendChild(searchError);

        document.getElementById('search-form').addEventListener('submit', function(e) {
            e.preventDefault();
            let val = searchInput.value.trim();
            searchError.textContent = '';
            if (val.length > 50) {
                searchError.textContent = 'Pencarian maksimal 50 karakter.';
                searchInput.classList.add('is-invalid');
                return;
            }
            if (val.length === 0) {
                searchError.textContent = 'Kata kunci pencarian tidak boleh kosong.';
                searchInput.classList.add('is-invalid');
                return;
            }
            searchInput.classList.remove('is-invalid');
            currentSearch = val;
            currentBatch = 1;
            fetchProducts(currentBatch, currentSearch, currentCategory, currentSort);
        });

        document.getElementById('category-dropdown').addEventListener('click', function(e) {
            if (e.target.classList.contains('category-item')) {
                currentCategory = e.target.getAttribute('data-category');
                document.getElementById('filter-label').textContent = e.target.textContent;
                currentBatch = 1;
                fetchProducts(currentBatch, currentSearch, currentCategory, currentSort);
            }
        });

        document.getElementById('sort-dropdown').addEventListener('click', function(e) {
            if (e.target.classList.contains('sort-item')) {
                currentSort = e.target.getAttribute('data-sort');
                document.getElementById('sort-label').textContent = e.target.textContent;
                currentBatch = 1;
                fetchProducts(currentBatch, currentSearch, currentCategory, currentSort);
            }
        });
    });
    </script>
    @endpush

</section>
@endsection