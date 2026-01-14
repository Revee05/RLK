@extends('web.partials.layout')
@section('sampleproduct','aktiv')

{{-- =========================
    1. PAGE CSS
========================= --}}
@section('css')
<link href="{{ asset('css/merch/MerchDetailProducts.css') }}" rel="stylesheet">
@endsection

@section('content')

{{-- =========================
    2. PREPROCESS DATA
========================= --}}
@php
// 2.1 Main Variant
$mainVariant = $product->variants->firstWhere('is_default', 1) ?? $product->variants->first();

// 2.2 Main Image
$mainImage = ($mainVariant && $mainVariant->images->count())
? asset($mainVariant->images->first()->image_path)
: 'https://placehold.co/500x400?text=No+Image';

// 2.3 All Images
$allImages = $product->variants->flatMap(fn($v) => ($v->images && $v->images->count()) ? $v->images : collect());

// 2.4 Variant Data for JS
$variantsArray = $product->variants->map(function($v) {
return [
'id' => $v->id,
'display_stock' => $v->display_stock,
'price' => $v->price,
'discount' => $v->discount,
'sizes' => $v->sizes->map(fn($s) => [
'id' => $s->id,
'size' => $s->size,
'stock' => $s->stock,
'price' => $s->price,
'discount' => $s->discount,
])->toArray(),
'image' => $v->images->first() ? asset($v->images->first()->image_path) : 'https://placehold.co/500x400?text=No+Image',
];
})->values()->toArray();

// 2.5 Total Stock
$totalStock = $product->variants->sum(fn($variant) => ($variant->sizes && $variant->sizes->count())
? $variant->sizes->sum('stock')
: ($variant->display_stock ?? 0));
@endphp


{{-- =========================
    3. PAGE MAIN CONTAINER
========================= --}}

<div class="main-container">

    {{-- =========================
        4. PRODUCT DETAIL SECTION
    ========================= --}}
    <div class="row g-3 p-3">

        {{-- =========================
            4A. PRODUCT IMAGE LEFT SIDE
        ========================= --}}
        <div class="col-lg-6 col-md-12 mb-3 mb-lg-0 px-3 px-lg-2">

                {{-- 4A.1 Main Image --}}
                <div class="main-image">
                    <img src="{{ $mainImage }}" alt="{{ $product->name }}" class="img-fluid rounded"
                        id="main-product-image">
                </div>

                {{-- 4A.2 Thumbnail Carousel --}}
                <div class="thumb-carousel">
                    <button type="button" class="thumb-nav prev" aria-label="Prev">
                        <span>&lsaquo;</span>
                    </button>

                    <div class="thumb-track-wrapper">
                        <div class="thumb-track" id="thumbTrack">
                            @foreach($allImages as $img)
                            <img src="{{ asset($img->image_path) }}" alt="thumb"
                                class="thumb-item{{ $loop->first ? ' active-thumb' : '' }}">
                            @endforeach
                        </div>
                    </div>

                    <button type="button" class="thumb-nav next" aria-label="Next">
                        <span>&rsaquo;</span>
                    </button>
                </div>
            </div>

        {{-- =========================
            4B. PRODUCT INFO RIGHT SIDE
        ========================= --}}
        <div class="col-lg-6 col-md-12 px-3 px-lg-2">

            {{-- 4B.1 Title --}}
                <h2 class="product-title mb-2">{{ $product->name }}</h2>

                {{-- 4B.2 Categories --}}
                <div class="product-category mb-2">
                    {{ $product->categories->pluck('name')->join(', ') }}
                </div>

                {{-- 4B.3 Price --}}
                <div class="product-price mb-3" id="price-display">
                    @if($mainVariant && $mainVariant->display_discount)
                    <span class="badge bg-danger me-2"
                        id="discount-badge">-{{ rtrim(rtrim(number_format($mainVariant->display_discount, 2, '.', ''), '0'), '.') }}%</span>
                    <span id="current-price">Rp. {{ number_format($mainVariant->display_price, 0, ',', '.') }}</span>
                    @elseif($mainVariant)
                    <span id="current-price">Rp. {{ number_format($mainVariant->display_price, 0, ',', '.') }}</span>
                    @else
                    <span class="text-muted">-</span>
                    @endif
                </div>

                {{-- 4B.4 Total Stock --}}
                <div class="mb-3">
                    <strong>Total Stok:</strong> {{ $totalStock }}
                </div>

                {{-- =========================
                    4B.5 Variant Selector
                ========================= --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Variant</label>
                    <div class="variant-grid">
                        @foreach($product->variants as $variant)
                        <label class="variant-btn"
                            data-image="{{ $variant->images->first() ? asset($variant->images->first()->image_path) : 'https://placehold.co/500x400?text=No+Image' }}">

                            @php $img = $variant->images->first(); @endphp

                            @if($img)
                            <img src="{{ asset($img->image_path) }}" alt="{{ $variant->name }}">
                            @else
                            <img src="https://placehold.co/40x40?text=?" alt="no-img">
                            @endif

                            <input type="radio" name="variant_id" value="{{ $variant->id }}" class="d-none"
                                autocomplete="off" {{ $variant->id == $mainVariant->id ? 'checked' : '' }}>
                            <span>{{ $variant->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- =========================
                    4B.6 Size Selector
                ========================= --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Sizes</label>
                    <div class="size-grid">
                        @if($mainVariant && $mainVariant->sizes->count())
                            @foreach($mainVariant->sizes as $size)
                            <label class="size-btn">
                                <input type="radio" name="size_id" value="{{ $size->id }}" class="d-none" autocomplete="off">
                                <span>{{ $size->size }}</span>
                            </label>
                            @endforeach
                        @else
                            <span class="text-muted">Tidak ada ukuran.</span>
                        @endif
                    </div>
                    {{-- Tombol Panduan Produk (dinamis) muncul jika ada konten/gambar panduan --}}
                    @if(!empty($product->size_guide_content) || !empty($product->size_guide_image))
                    <div class="mt-2">
                        <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#productGuideModal">
                            {{ $product->guide_button_label ?? 'Panduan Produk' }}
                        </button>
                    </div>
                    @endif
                </div>

                {{-- =========================
                    4B.7 Add To Cart Form
                ========================= --}}
                <div id="form-messages" class="mb-2"></div> {{-- Tambahan: Wadah pesan error/sukses --}}

                <form action="{{ route('cart.addMerch') }}" method="POST" id="add-to-cart-form">
                    {{-- Tambahan: id="add-to-cart-form" --}}
                    @csrf

                    {{-- PERUBAHAN 1: Ganti name="product_id" jadi "merch_product_id" --}}
                    <input type="hidden" name="merch_product_id" value="{{ $product->id }}">

                    {{-- Input hidden variant & size biarkan tetap sama --}}
                    <input type="hidden" name="selected_variant_id" id="selected_variant_id"
                        value="{{ $mainVariant->id }}">
                    <input type="hidden" name="selected_size_id" id="selected_size_id"
                        value="{{ $mainVariant->sizes->first()->id ?? '' }}">

                    {{-- Bagian Quantity biarkan tetap sama --}}
                    <label class="form-label fw-bold">Quantity</label>
                    <div class="d-flex align-items-center mb-3">
                        <div class="qty-group">
                            <button type="button" class="qty-btn minus" tabindex="-1">-</button>
                            <input type="number" id="qty-input" name="quantity" value="1" min="1" class="qty-input"
                                autocomplete="off">
                            <button type="button" class="qty-btn plus" tabindex="-1">+</button>
                        </div>
                        <span id="stock-info" class="text-muted ms-3">
                            Tersedia
                            {{ $mainVariant->sizes->count() ? ($mainVariant->sizes->first()->stock ?? 0) : ($mainVariant->display_stock ?? 0) }}
                        </span>
                    </div>

                    {{-- PERUBAHAN 2: Tambahkan ID pada tombol dan Spinner loading --}}
                    <button type="submit" class="btn btn-primary btn-lg w-100 mb-3" id="btn-submit">
                        <span id="btn-text">Tambahkan ke keranjang</span>
                        <span id="btn-spinner" class="spinner-border spinner-border-sm d-none" role="status"
                            aria-hidden="true"></span>
                    </button>
                </form>

                {{-- 4B.8 Shipping Info --}}
                <div class="product-shipping-info">
                    <strong>Pengiriman:</strong> Pengiriman dilakukan setiap hari kerja.
                </div>
            </div>
        </div>
    </div>


    {{-- =========================
        5. PRODUCT DESCRIPTION
    ========================= --}}
    <div class="container pb-3">
        <h4 class="mb-2">
            Deskripsi Produk
            <button id="toggle-desc-btn" class="btn btn-link btn-sm" type="button" style="text-decoration:none;">
                <span id="toggle-desc-icon">▼</span> Tampilkan
            </button>
        </h4>
        <div class="product-desc mb-4" id="product-desc-content" style="display:none;">
            {!! $product->description !!}
        </div>
    </div>


    {{-- =========================
        6. RELATED PRODUCTS
    ========================= --}}
    <div class="related-products-section container pb-5">
        <h4 class="mb-4">Related products</h4>
        <div class="row g-3">

            @forelse($relatedProducts as $rel)
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card h-100 shadow-sm border-0">
                    <a href="{{ route('merch.products.detail', $rel['slug']) }}" class="text-decoration-none text-dark">

                        <img src="{{ $rel['image'] }}" class="card-img-top related-img" alt="{{ $rel['name'] }}">

                        <div class="card-body p-2">
                            <div class="fw-bold mb-1 related-title">{{ $rel['name'] }}</div>

                            @if($rel['display_discount'])
                            <span class="badge bg-danger mb-1">-{{ $rel['display_discount'] }}%</span>
                            @endif

                            <div class="related-price">
                                Rp. {{ number_format($rel['display_price'], 0, ',', '.') }}
                            </div>
                        </div>

                    </a>
                </div>
            </div>
            @empty
            <div class="col-12 text-muted">Tidak ada produk terkait.</div>
            @endforelse

        </div>
    </div>

</div>
{{-- Product Guide Modal --}}
@if(!empty($product->size_guide_content) || !empty($product->size_guide_image))
<div class="modal fade product-guide-modal" id="productGuideModal" tabindex="-1" aria-labelledby="productGuideLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productGuideLabel">
                    {{ $product->guide_button_label ?? 'Panduan Produk' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if(!empty($product->size_guide_image))
                <div class="text-center">
                    <img src="{{ asset($product->size_guide_image) }}" alt="Panduan Produk" class="guide-image">
                </div>
                @endif
                @if(!empty($product->size_guide_content))
                <div class="product-guide-content">{!! $product->size_guide_content !!}</div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection



{{-- =========================
    7. JAVASCRIPT INTERACTIONS
========================= --}}
@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Product:', @json($product));
    console.log('Related Products:', @json($relatedProducts));

    // 7.1 Data & Element References
    const variants = @json($variantsArray);
    const variantInputs = Array.from(document.querySelectorAll('.variant-btn input[type="radio"]'));
    const sizeGrid = document.querySelector('.size-grid');
    const mainImageEl = document.getElementById('main-product-image');
    const stockInfoEl = document.getElementById('stock-info');
    const qtyInputEl = document.getElementById('qty-input');
    const submitBtnEl = document.querySelector('button[type="submit"]');
    const hiddenVariantEl = document.getElementById('selected_variant_id');
    const hiddenSizeEl = document.getElementById('selected_size_id');

    // 7.2 Helper: Get Variant By Input
    function getVariantByInput(input) {
        const idx = variantInputs.indexOf(input);
        return variants[idx];
    }

    // 7.3 Render Sizes for Variant
    function renderSizes(variant) {
        if (!variant || variant.sizes.length === 0) {
            sizeGrid.innerHTML = '<span class="text-muted">Tidak ada ukuran.</span>';
            return;
        }

        sizeGrid.innerHTML = variant.sizes.map((sz, i) => `
            <label class="size-btn${i === 0 ? ' active' : ''}">
                <input type="radio" name="size_id" value="${sz.id}" class="d-none"
                    autocomplete="off" ${i === 0 ? 'checked' : ''}>
                <span>${sz.size}</span>
            </label>
        `).join('');
    }

    // 7.4 Get Current Variant
    function currentVariant() {
        const checked = document.querySelector('.variant-btn input[type="radio"]:checked');
        return checked ? getVariantByInput(checked) : null;
    }

    // 7.5 Compute Stock
    function currentSizeIndex(variant) {
        if (!variant || variant.sizes.length === 0) return -1;
        const checkedSize = document.querySelector('.size-btn input[type="radio"]:checked');
        if (!checkedSize) return -1;
        const sizeInputs = Array.from(document.querySelectorAll('.size-btn input[type="radio"]'));
        return sizeInputs.indexOf(checkedSize);
    }

    function computeStock(variant, sizeIdx) {
        if (!variant) return 0;
        if (variant.sizes.length > 0) {
            return sizeIdx >= 0 ? (variant.sizes[sizeIdx]?.stock ?? 0) : 0;
        }
        return variant.display_stock ?? 0;
    }

    // 7.6 Update Stock UI
    function updateStockInfo() {
        const variant = currentVariant();
        const sizeIdx = currentSizeIndex(variant);
        const stock = computeStock(variant, sizeIdx);

        stockInfoEl.innerHTML = stock < 1 ?
            '<span class="text-danger">Habis</span>' :
            `Tersedia ${stock}`;

        if (qtyInputEl) {
            qtyInputEl.disabled = stock < 1;
            qtyInputEl.max = stock;
        }

        if (submitBtnEl) submitBtnEl.disabled = stock < 1;
    }

    // 7.6b Update Price Display
    function updatePriceDisplay() {
        const variant = currentVariant();
        if (!variant) return;

        const sizeIdx = currentSizeIndex(variant);
        let price, discount;

        // Jika ada size dan size dipilih, ambil dari size
        if (variant.sizes.length > 0 && sizeIdx >= 0) {
            price = variant.sizes[sizeIdx].price;
            discount = variant.sizes[sizeIdx].discount;
        } else {
            // Jika tidak ada size, ambil dari variant
            price = variant.price;
            discount = variant.discount;
        }

        const priceDisplay = document.getElementById('price-display');
        if (!priceDisplay) return;

        // Format harga
        const formatPrice = (num) => 'Rp. ' + parseFloat(num).toLocaleString('id-ID');

        // Format discount: hilangkan trailing zero
        const formatDiscount = (num) => {
            const formatted = parseFloat(num).toString();
            return formatted;
        };

        if (discount > 0) {
            priceDisplay.innerHTML = `
                <span class="badge bg-danger me-2" id="discount-badge">-${formatDiscount(discount)}%</span>
                <span id="current-price">${formatPrice(price)}</span>
            `;
        } else {
            priceDisplay.innerHTML = `<span id="current-price">${formatPrice(price)}</span>`;
        }
    }

    // 7.7 Update Hidden Inputs
    function updateHiddenInputs() {
        const v = currentVariant();
        hiddenVariantEl.value = v ? v.id : '';

        const checkedSize = document.querySelector('.size-btn input[type="radio"]:checked');
        hiddenSizeEl.value = checkedSize ? checkedSize.value : '';
    }

    // 7.8 Set Active Variant
    function setActiveVariant(input) {
        document.querySelectorAll('.variant-btn').forEach(l => l.classList.remove('active'));
        input.closest('.variant-btn').classList.add('active');

        const variant = getVariantByInput(input);

        renderSizes(variant);
        updateStockInfo();
        updatePriceDisplay();
        updateHiddenInputs();
    }

    // 7.9 Variant Events
    variantInputs.forEach(input => {
        input.addEventListener('change', () => setActiveVariant(input));

        const label = input.closest('.variant-btn');

        // Hover Effect
        label.addEventListener('mouseenter', () => {
            const variant = getVariantByInput(input);
            mainImageEl.src = label.getAttribute('data-image');

            const tempStock = computeStock(variant, variant.sizes.length ? 0 : -1);
            stockInfoEl.innerHTML = tempStock < 1 ? '<span class="text-danger">Habis</span>' :
                `Tersedia ${tempStock}`;
        });

        label.addEventListener('mouseleave', () => {
            const checkedVariant = document.querySelector(
                '.variant-btn input[type="radio"]:checked');
            if (checkedVariant) {
                mainImageEl.src = checkedVariant.closest('.variant-btn').getAttribute(
                    'data-image');
            }
            updateStockInfo();
        });
    });

    // 7.10 Size Change Event
    document.addEventListener('change', e => {
        if (e.target.name === 'size_id') {
            document.querySelectorAll('.size-btn').forEach(l => l.classList.remove('active'));
            e.target.closest('.size-btn').classList.add('active');

            updateStockInfo();
            updatePriceDisplay();
            updateHiddenInputs();
        }
    });

    // 7.11 Init State
    const initVariant = document.querySelector('.variant-btn input[type="radio"]:checked');
    if (initVariant) setActiveVariant(initVariant);
    else {
        updateStockInfo();
        updatePriceDisplay();
    }


    // =========================
    // 7.12 Thumbnail Carousel
    // =========================
    const track = document.getElementById('thumbTrack');
    const items = Array.from(document.querySelectorAll('.thumb-item'));
    const prevBtn = document.querySelector('.thumb-nav.prev');
    const nextBtn = document.querySelector('.thumb-nav.next');

    let offset = 0;
    const gap = 10;
    const visibleCount = 5;
    const itemWidth = 80;
    const step = itemWidth + gap;

    function maxOffset() {
        const totalWidth = items.length * (itemWidth + gap);
        const wrapperWidth = visibleCount * (itemWidth + gap);
        return Math.max(0, totalWidth - wrapperWidth);
    }

    function updateButtons() {
        prevBtn.disabled = offset <= 0;
        nextBtn.disabled = offset >= maxOffset();
    }

    function applyTransform() {
        track.style.transform = `translateX(-${offset}px)`;
        updateButtons();
    }

    prevBtn.addEventListener('click', () => {
        offset = Math.max(0, offset - step);
        applyTransform();
    });

    nextBtn.addEventListener('click', () => {
        offset = Math.min(maxOffset(), offset + step);
        applyTransform();
    });

    items.forEach(img => {
        img.addEventListener('click', () => {
            items.forEach(i => i.classList.remove('active-thumb'));
            img.classList.add('active-thumb');
            mainImageEl.src = img.src;
        });
    });

    applyTransform();



    const addToCartForm = document.getElementById('add-to-cart-form');
    const submitBtn = document.getElementById('btn-submit');
    const btnText = document.getElementById('btn-text');
    const btnSpinner = document.getElementById('btn-spinner');
    const msgContainer = document.getElementById('form-messages');

    if (addToCartForm) {
        addToCartForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Mencegah reload halaman

            // Efek Loading
            submitBtn.disabled = true;
            btnText.innerText = 'Menambahkan...';
            btnSpinner.classList.remove('d-none');
            msgContainer.innerHTML = '';

            const formData = new FormData(this);

            fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content'),
                        'Accept': 'application/json' // Minta respon JSON
                    },
                    body: formData
                })
                .then(async response => {
                    // Tangani 401 khusus (belum login)
                    if (response.status === 401) {
                        msgContainer.innerHTML =
                            `<div class="alert alert-danger py-2">Silahkan login supaya bisa menyimpan produk ke keranjang. <a href="${"{{ route('login') }}"}" class="alert-link">Log in</a></div>`;
                        return null; // hentikan chain
                    }

                    // Coba parse JSON, jika gagal tampilkan pesan umum
                    let data = null;
                    try {
                        data = await response.json();
                    } catch (e) {
                        msgContainer.innerHTML = `<div class="alert alert-danger py-2">Terjadi kesalahan sistem.</div>`;
                        return null;
                    }

                    return { ok: response.ok, data };
                })
                .then(result => {
                    if (!result) return; // sudah ditangani di atas

                    const { ok, data } = result;
                    if (ok && data && data.success) {
                        msgContainer.innerHTML = `<div class="alert alert-success py-2">${data.message}</div>`;
                    } else if (data && data.message) {
                        msgContainer.innerHTML = `<div class="alert alert-danger py-2">${data.message}</div>`;
                    } else {
                        msgContainer.innerHTML = `<div class="alert alert-danger py-2">Terjadi kesalahan sistem.</div>`;
                    }
                })
                .catch(err => {
                    msgContainer.innerHTML = `<div class="alert alert-danger py-2">Terjadi kesalahan sistem.</div>`;
                })
                .finally(() => {
                    // Kembalikan tombol seperti semula
                    submitBtn.disabled = false;
                    btnText.innerText = 'Tambahkan ke keranjang';
                    btnSpinner.classList.add('d-none');
                });
        });
    }

    // Toggle deskripsi produk
    const descBtn = document.getElementById('toggle-desc-btn');
    const descContent = document.getElementById('product-desc-content');
    const descIcon = document.getElementById('toggle-desc-icon');
    let descVisible = false;

    if (descBtn && descContent) {
        descBtn.addEventListener('click', function() {
            descVisible = !descVisible;
            descContent.style.display = descVisible ? 'block' : 'none';
            descBtn.innerHTML =
                `<span id="toggle-desc-icon">${descVisible ? '▲' : '▼'}</span> ${descVisible ? 'Sembunyikan' : 'Tampilkan'}`;
        });
    }

    // =========================
    // Image preview modal (open when main image clicked)
    // =========================
    const imgModalEl = document.getElementById('imageModal');
    const modalImageEl = document.getElementById('modal-image-el');
    let bsImgModal = null;
    if (imgModalEl && typeof bootstrap !== 'undefined') {
        bsImgModal = new bootstrap.Modal(imgModalEl);
    }

    if (mainImageEl && bsImgModal && modalImageEl) {
        mainImageEl.style.cursor = 'zoom-in';
        mainImageEl.addEventListener('click', () => {
            modalImageEl.src = mainImageEl.src;
            modalImageEl.classList.remove('zoomed');
            bsImgModal.show();
        });
    }

    // Ensure close button hides modal even if data-bs-dismiss doesn't work
    if (imgModalEl && bsImgModal) {
        const imgCloseBtn = imgModalEl.querySelector('.btn-close');
        if (imgCloseBtn) imgCloseBtn.addEventListener('click', () => bsImgModal.hide());
    }
});
document.querySelectorAll('.qty-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const input = this.parentElement.querySelector('.qty-input');
        let val = parseInt(input.value) || 1;
        const min = parseInt(input.min) || 1;
        const max = parseInt(input.max) || 9999;

        if (this.classList.contains('minus')) {
            if (val > min) input.value = val - 1;
        } else {
            if (val < max) input.value = val + 1;
        }
        input.dispatchEvent(new Event('input'));
    });
});
</script>

@endsection

<!-- Image Preview Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-fullscreen-sm-down">
        <div class="modal-content bg-transparent border-0 position-relative">
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body text-center">
                <img id="modal-image-el" src="" alt="Preview" class="img-fluid">
            </div>
        </div>
    </div>
</div>
