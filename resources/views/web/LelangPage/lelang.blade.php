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
<section class="py-5">
    <h2 class="text-center fw-bold mb-4">General Auction</h2>
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
                    <li><a class="dropdown-item sort-item" data-sort="running">Sedang Berlangsung</a></li>
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
    const currentUserId = @json(auth()->id()); // <-- tetap ada
    let currentBatch = 1;
    let isLoading = false;
    let currentSearch = "";
    let currentCategory = "";
    let currentSort = "";

    let globalIndex = 0;

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

        // --- TIMER BADGE (top) ---
        let badgeWaktu = '';
        if (product.end_date_iso) {
            badgeWaktu = `<div class="lelang-timer-badge lelang-countdown" data-end="${product.end_date_iso}">
                            --:--:--:--
                          </div>`;
        }

        // --- RESULT BADGE (tampil ketika status = 2 AND lelang sudah selesai) ---
        let resultBadge = '';
        if (!currentUserId) {
            // tidak menampilkan badge untuk user yang tidak login
        } else if (product.status === 2) {
            // pastikan lelang sudah selesai (jika ada end_date)
            let ended = true;
            if (product.end_date_iso) {
                ended = (new Date(product.end_date_iso).getTime() <= Date.now());
            }
            if (ended) {
                // Jika backend memberikan is_winner dan has_bid, gunakan keduanya.
                if (typeof product.is_winner !== 'undefined') {
                    if (product.is_winner) {
                        resultBadge = `<div class="lelang-result-badge" style="background:#28a745; color:#fff;">MENANG</div>`;
                    } else if (product.has_bid) {
                        // tampilkan "KALAH" hanya jika user memang pernah melakukan bid
                        resultBadge = `<div class="lelang-result-badge" style="background:#6c757d; color:#fff;">KALAH</div>`;
                    } else {
                        resultBadge = '';
                    }
                } else {
                    // Fallback lama: gunakan winner_id dan has_bid jika tersedia
                    if (product.winner_id && currentUserId && product.winner_id == currentUserId) {
                        resultBadge = `<div class="lelang-result-badge" style="background:#28a745; color:#fff;">MENANG</div>`;
                    } else if (product.has_bid) {
                        resultBadge = `<div class="lelang-result-badge" style="background:#6c757d; color:#fff;">KALAH</div>`;
                    } else {
                        resultBadge = '';
                    }
                }
            }
        }

        // --- Logika Harga ---
        let displayPrice = '';
        let labelHarga = '';
        let priceColor = '';

        if (product.highest_bid && product.highest_bid > 0) {
            let formattedPrice = new Intl.NumberFormat('id-ID').format(product.highest_bid);
            displayPrice = `Rp. ${formattedPrice}`;
            labelHarga = 'Bidding Tertinggi:';
            priceColor = '#0d6efd'; 
        } else {
            displayPrice = product.price_str; 
            labelHarga = 'Harga Awal:';
            priceColor = '#6c757d'; 
        }

        return `
        <a href="/lelang/${product.slug}" class="${cellClass}" style="text-decoration:none; color:inherit;">
            <div class="card product-card h-100">
                ${badgeWaktu}
                ${resultBadge}
                <img src="${imageUrl}" class="card-img-top" alt="${product.title}">
                <div class="card-body text-left p-2">
                    <div class="product-title">${product.title}</div>
                    <div class="text-muted small mb-1">${labelHarga}</div>
                    <div class="product-price fw-bold" style="color: ${priceColor};">${displayPrice}</div>
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
                if (batch === 1) {
                grid.innerHTML = "";
                globalIndex = 0; 
            }

            if (data.products && data.products.length > 0) {
                data.products.forEach(product => {
                    if (product) {
                        grid.insertAdjacentHTML(
                            'beforeend',
                            renderProduct(product, globalIndex)
                        );
                        globalIndex++; 
                    }
                });
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

        // Clear validation UI while typing or when input becomes empty
        searchInput.addEventListener('input', function() {
            if (this.value.trim().length === 0) {
                searchError.textContent = '';
                searchInput.classList.remove('is-invalid');
            } else {
                // remove previous error once user starts typing
                searchError.textContent = '';
                searchInput.classList.remove('is-invalid');
            }
        });

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
                // If search is empty, reset to default and re-fetch products
                searchError.textContent = '';
                searchInput.classList.remove('is-invalid');
                currentSearch = '';
                currentBatch = 1;
                fetchProducts(currentBatch, currentSearch, currentCategory, currentSort);
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

    // --- SCRIPT COUNTDOWN GLOBAL (Letakkan di bawah fetchProducts) ---
    
    // Fungsi untuk memformat angka jadi 2 digit (09, 10, etc)
    const pad = n => (n < 10 ? '0' + n : n);

    function startGlobalCountdown() {
        // Cek apakah interval sudah jalan biar tidak dobel
        if (window.lelangInterval) return;

        window.lelangInterval = setInterval(() => {
            const now = new Date().getTime();
            
            // Ambil semua elemen timer yang ada di layar
            const timers = document.querySelectorAll('.lelang-countdown');
            
            timers.forEach(el => {
                const endStr = el.getAttribute('data-end');
                if (!endStr) return;

                const endDate = new Date(endStr).getTime();
                const distance = endDate - now;

                if (distance < 0) {
                    el.innerHTML = "SELESAI";
                    el.style.backgroundColor = "#dc3545"; // Merah kalau selesai
                    return;
                }

                // Hitung hari, jam, menit, detik
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Update teks badge
                // Format: Hari:Jam:Menit:Detik
                el.innerHTML = `${pad(days)}:${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
            });

        }, 1000);
    }

    // Panggil fungsi ini sekali saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        startGlobalCountdown();
    });
    </script>
    @endpush

</section>
@endsection