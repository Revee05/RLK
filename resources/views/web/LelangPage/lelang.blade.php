@extends('web.partials.layout')
@section('lelang','aktiv')
@section('css')
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
        <div id="products-grid" class="row gx-4 gx-lg-4 row-cols-2 row-cols-md-3 row-cols-xl-4">
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

    function renderProduct(product) {
        const priceHtml = (product.diskon && product.diskon > 0) ?
            `<s>${product.price_str}</s> <span class="text-danger">${product.price}</span>` :
            `${product.price_str}`;

        return `
            <div class="col-md-3 mb-4">
                <a href="/lelang/${product.slug}" class="text-decoration-none">
                    <div class="card h-100 card-produk">
                        <div class="card-figure">
                            <img class="card-img-top card-image" src="/${product.image}" alt="${product.title}" />
                            <div class="mark-lelang btn btn-light rounded-0 border">LELANG</div>
                        </div>
                        <div class="card-body p-2">
                            <div class="text-left">
                                <h5 class="fw-bolder produk-title">${product.title}</h5>
                                <div class="kategori-produk">
                                    <a class="text-decoration-none text-dark" href="/category/${product.category_slug}">${product.category}</a>
                                </div>
                                <div class="price-produk">${priceHtml}</div>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <a href="/lelang/${product.slug}" class="btn btn-block w-100">BID</a>
                        </div>
                    </div>
                </a>
            </div>`;
    }

    function fetchProducts(batch = 1, search = "", category = "", sort = "") {
        if (isLoading) return;
        isLoading = true;
        let url = "{{ route('lelang.products.json') }}?batch=" + batch;
        if (search) url += "&search=" + encodeURIComponent(search);
        if (category) url += "&category=" + encodeURIComponent(category);
        if (sort) url += "&sort=" + encodeURIComponent(sort);

        fetch(url)
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(data => {
                const grid = document.getElementById('products-grid');
                if (batch === 1) grid.innerHTML = "";
                if (data.products && data.products.length > 0) {
                    data.products.forEach((product) => {
                        grid.insertAdjacentHTML('beforeend', renderProduct(product));
                    });
                } else {
                    if (batch === 1) {
                        grid.innerHTML = '<div class="col-12 text-center text-muted">Tidak ada produk ditemukan.</div>';
                    }
                }
                document.getElementById('load-more').style.display = data.has_more ? '' : 'none';
                isLoading = false;
            })
            .catch((err) => {
                const grid = document.getElementById('products-grid');
                if (batch === 1) {
                    grid.innerHTML = '<div class="col-12 text-center text-danger">Gagal memuat produk: ' + err.message + '</div>';
                }
                isLoading = false;
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
        document.getElementById('search-form').addEventListener('submit', function(e) {
            e.preventDefault();
            currentSearch = searchInput.value;
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