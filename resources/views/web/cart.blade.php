@extends('web.partials.layout')

@section('css')
    {{-- Memanggil CSS Eksternal --}}
    <link href="{{ asset('css/cart_style.css') }}" rel="stylesheet">
@endsection

@section('content')

<div class="container my-3 my-md-5">
    <h2 class="fw-bold mb-4 mb-md-5">Keranjang</h2> 

    <div id="ajax-alert-container"></div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @php
        $hasItems = $cartItems && !$cartItems->isEmpty();
        // Deteksi apakah ada item lelang / merch di keranjang
        $hasLelang = $hasItems ? $cartItems->contains(fn($it) => method_exists($it, 'isAuction') ? $it->isAuction() : false) : false;
        $hasMerch = $hasItems ? $cartItems->contains(fn($it) => method_exists($it, 'isAuction') ? !$it->isAuction() : false) : false;
    @endphp

    {{-- 1. KERANJANG KOSONG --}}
    <div id="empty-cart-view" class="{{ $hasItems ? 'd-none' : '' }}">
        <div class="row">
            <div class="col-12 my-3 my-md-5 text-center py-5 bg-light rounded-3">
                <h4 class="text-muted mb-3">Keranjang belanja Anda kosong.</h4>
                <a href="/all-other-product" class="btn btn-dark px-4">Mulai Belanja</a>
            </div>
        </div>
    </div>

    {{-- 2. LIST ITEM --}}
    <div id="cart-content-view" class="{{ $hasItems ? '' : 'd-none' }}">
        @if($hasLelang && $hasMerch)
            <div class="alert alert-warning">
                <strong>Perhatian:</strong> Keranjang Anda berisi produk <em>lelang</em> dan <em>merchandise</em>.
                Checkout untuk produk lelang (yang memiliki masa kadaluarsa) dan merchandise harus dilakukan secara terpisah. Silakan pilih dan checkout satu jenis produk saja (merch atau lelang).
            </div>
        @endif
        <form action="{{ route('checkout.index') }}" method="GET" id="checkout-form">
            @csrf 

            <div class="row g-3 fw-bold text-black mb-3 pb-2 border-bottom border-black d-none d-md-flex">
                <div class="col-1 text-center"></div>
                <div class="col-md-4">Produk</div>
                <div class="col-md-2">Harga</div>
                <div class="col-md-3 text-center">Jumlah</div>
                <div class="col-md-2 text-end">Total</div>
            </div>

            <div id="cart-items-list">
            @if($hasItems)
                @foreach($cartItems as $item)
                    @php 
                        // --- LOGIKA PENENTUAN TIPE ---
                        $isAuction = $item->isAuction(); // Pakai helper dari Model
                        $product = null;
                        $imgUrl = 'https://via.placeholder.com/100';
                        $price = $item->price;
                        $quantity = $item->quantity;
                        $total = $price * $quantity;

                        // Variabel khusus Merch
                        $availableVariants = collect([]);
                        $availableSizes = collect([]);
                        $currentVariantId = null;
                        $currentSizeId = null;

                        if ($isAuction) {
                            // --- KONDISI LELANG ---
                            $product = $item->auctionProduct;
                            if (!$product) continue; // Skip jika produk terhapus

                            // LOGIKA GAMBAR LELANG (DIPERBAIKI)
                            // 1. Cek apakah ada relasi images dan datanya ada
                            if ($product->images && $product->images->count() > 0) {
                                // Gunakan ->path sesuai yang ada di detail.blade.php
                                $imgUrl = asset($product->images->first()->path); 
                            } 
                            // Fallback jika tidak punya relasi images, cek kolom path langsung di tabel produk
                            elseif (!empty($product->path)) { 
                                $imgUrl = asset($product->path);
                            }
                            else {
                                $imgUrl = 'https://via.placeholder.com/100?text=No+Image';
                            }

                            $title = $product->title ?? 'Produk Lelang';
                        } else {
                            // --- KONDISI MERCHANDISE (Kode Lama) ---
                            $product = $item->merchProduct;
                            if (!$product) continue;

                            $title = $product->name;
                            $availableVariants = $product->variants ?? collect([]); 
                            $currentVariantId = $item->merch_product_variant_id;
                            $currentVariant = $currentVariantId ? \App\models\MerchProductVariant::find($currentVariantId) : null;
                            $availableSizes = ($currentVariant) ? $currentVariant->sizes : collect([]);
                            $currentSizeId = $item->merch_product_variant_size_id;

                            // Gambar Merch
                            if ($currentVariant && $currentVariant->images->count() > 0) {
                                $imgUrl = asset($currentVariant->images->first()->image_path);
                            } elseif ($product->images->count() > 0) {
                                $imgUrl = asset($product->images->first()->image_path);
                            }
                        }
                    @endphp

                    {{-- A. DESKTOP VIEW --}}
                    <div class="row g-3 align-items-center my-3 py-2 border-bottom border-black cart-item-row desktop-view d-none d-md-flex" id="item-row-desktop-{{ $item->id }}">
                        <div class="col-1 text-center">
                            <input class="form-check-input item-checkbox" type="checkbox" name="cart_item_ids[]" value="{{ $item->id }}" data-item-total="{{ $total }}" data-type="{{ $isAuction ? 'lelang' : 'merch' }}" onclick="syncCheckbox(this, {{ $item->id }})">
                        </div>
                        <div class="col-md-4 d-flex align-items-center">
                            <img src="{{ $imgUrl }}" class="rounded me-3 img-product-{{ $item->id }}" style="width: 70px; height: 70px; object-fit: cover; border: 1px solid #f8f9fa;">
                            <div class="w-100 pe-3">
                                <h6 class="mb-1 fw-bold text-dark text-truncate">{{ $title }}</h6>
                                
                                @if($isAuction)
                                    {{-- TAMPILAN KHUSUS LELANG --}}
                                    <span class="badge bg-warning text-dark mb-1" style="font-size: 10px;">Lelang Winner</span>
                                    @if($item->expires_at)
                                        <div class="text-danger small fw-bold mt-1" style="font-size: 11px;">
                                            <i class="bi bi-clock"></i> Hangus: {{ \Carbon\Carbon::parse($item->expires_at)->format('d M H:i') }}
                                        </div>
                                    @endif
                                @else
                                    {{-- TAMPILAN KHUSUS MERCH --}}
                                    <span class="badge bg-primary text-white mb-1" style="font-size: 10px;">Merchandise</span>
                                    {{-- TAMPILAN KHUSUS MERCH (Dropdown) --}}
                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        @if($availableVariants->count() > 0)
                                            <select class="custom-select-compact option-selector" data-id="{{ $item->id }}" data-type="variant_id" title="Pilih Varian">
                                                @foreach($availableVariants as $v)
                                                    @php $variantSizeCount = method_exists($v, 'sizes') ? $v->sizes->count() : (data_get($v, 'sizes') ? count($v->sizes) : 0); @endphp
                                                    <option value="{{ $v->id }}" {{ $currentVariantId == $v->id ? 'selected' : '' }} {{ $variantSizeCount == 0 ? 'disabled' : '' }}>{{ $v->name }}{{ $variantSizeCount == 0 ? ' (Display)' : '' }}</option>
                                                @endforeach
                                            </select>
                                        @endif

                                        <select class="custom-select-compact option-selector size-selector-{{ $item->id }}" 
                                                data-id="{{ $item->id }}" data-type="size_id" 
                                                {{ $availableSizes->isEmpty() ? 'disabled' : '' }} title="Pilih Ukuran" style="min-width: 60px;">
                                            @if($availableSizes->count() > 0)
                                                @foreach($availableSizes as $s)
                                                    @php $sStock = data_get($s, 'stock', $s->stock ?? 0); @endphp
                                                    <option value="{{ $s->id }}" {{ $currentSizeId == $s->id ? 'selected' : '' }} {{ ($sStock !== null && $sStock <= 0) ? 'disabled' : '' }}>{{ $s->size }}{{ ($sStock !== null && $sStock <= 0) ? ' (Habis)' : '' }}</option>
                                                @endforeach
                                            @else
                                                <option value="">-</option>
                                            @endif
                                        </select>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-2">
                            <span class="fw-semibold price-display-{{ $item->id }}" data-price="{{ $price }}">Rp{{ number_format($price, 0, ',', '.') }}</span>
                        </div>
                        <div class="col-md-3 text-center">
                            @if($isAuction)
                                {{-- QTY LELANG FIX 1 --}}
                                <input type="text" class="form-control text-center bg-light mx-auto" style="width: 60px;" value="1" readonly title="Item lelang hanya bisa 1">
                            @else
                                {{-- QTY MERCH --}}
                                <div class="input-group input-group-sm mx-auto" style="width: 100px;">
                                    <button class="btn btn-outline-secondary btn-quantity" type="button" data-action="decrease" data-id="{{ $item->id }}">-</button>
                                    <input type="text" class="form-control text-center bg-white quantity-display-{{ $item->id }}" value="{{ $quantity }}" readonly>
                                    <button class="btn btn-outline-secondary btn-quantity" type="button" data-action="increase" data-id="{{ $item->id }}">+</button>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-2 text-end">
                            <strong class="fs-6 text-primary d-block mb-2 text-dark total-display-{{ $item->id }}">Rp{{ number_format($total, 0, ',', '.') }}</strong>
                            
                            {{-- LOGIC: Tombol Hapus hanya muncul jika BUKAN lelang --}}
                            @if(!$isAuction)
                                <button type="button" class="btn btn-link text-danger p-0 small text-decoration-none btn-delete-item" 
                                        data-id="{{ $item->id }}" data-url="{{ route('cart.destroy', $item->id) }}">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- B. MOBILE VIEW --}}
                    <div class="d-flex d-md-none align-items-start my-3 pb-3 border-bottom cart-item-row mobile-view" id="item-row-mobile-{{ $item->id }}">
                        <div class="me-3 d-flex align-items-center align-self-center">
                            <input class="form-check-input mobile-checkbox item-checkbox" type="checkbox" name="cart_item_ids[]" value="{{ $item->id }}" data-item-total="{{ $total }}" data-type="{{ $isAuction ? 'lelang' : 'merch' }}" onclick="syncCheckbox(this, {{ $item->id }})">
                        </div>
                        <div class="flex-grow-1 pe-2" style="min-width: 0;">
                            <div class="d-flex align-items-start">
                                <img src="{{ $imgUrl }}" class="rounded border me-3 flex-shrink-0 img-product-{{ $item->id }}" style="width: 75px; height: 75px; object-fit: cover;">
                                <div class="flex-grow-1" style="min-width: 0;">
                                    <h6 class="mobile-product-title text-truncate">{{ $title }}</h6>
                                    
                                    @if($isAuction)
                                         <span class="badge bg-warning text-dark" style="font-size: 9px;">Lelang</span>
                                         @if($item->expires_at)
                                            <div class="text-danger fw-bold mt-1" style="font-size: 10px;">
                                                Hangus: {{ \Carbon\Carbon::parse($item->expires_at)->format('d/m H:i') }}
                                            </div>
                                         @endif
                                    @else
                                        <div class="d-flex flex-wrap gap-2 mt-2">
                                            {{-- Dropdown Mobile Merch (Sama seperti sebelumnya) --}}
                                            @if($availableVariants->count() > 0)
                                                <select class="custom-select-compact option-selector" data-id="{{ $item->id }}" data-type="variant_id">
                                                    @foreach($availableVariants as $v)
                                                        @php $variantSizeCount = method_exists($v, 'sizes') ? $v->sizes->count() : (data_get($v, 'sizes') ? count($v->sizes) : 0); @endphp
                                                        <option value="{{ $v->id }}" {{ $currentVariantId == $v->id ? 'selected' : '' }} {{ $variantSizeCount == 0 ? 'disabled' : '' }}>{{ $v->name }}{{ $variantSizeCount == 0 ? ' (Display)' : '' }}</option>
                                                    @endforeach
                                                </select>
                                            @endif
                                            <select class="custom-select-compact option-selector size-selector-{{ $item->id }}" 
                                                    data-id="{{ $item->id }}" data-type="size_id" {{ $availableSizes->isEmpty() ? 'disabled' : '' }} style="min-width: 50px;">
                                                @if($availableSizes->count() > 0)
                                                    @foreach($availableSizes as $s)
                                                        @php $sStock = data_get($s, 'stock', $s->stock ?? 0); @endphp
                                                        <option value="{{ $s->id }}" {{ $currentSizeId == $s->id ? 'selected' : '' }} {{ ($sStock !== null && $sStock <= 0) ? 'disabled' : '' }}>{{ $s->size }}{{ ($sStock !== null && $sStock <= 0) ? ' (Habis)' : '' }}</option>
                                                    @endforeach
                                                @else
                                                    <option value="">-</option>
                                                @endif
                                            </select>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column justify-content-between align-items-end ms-2" style="height: 75px;">
                            <div class="mobile-qty-pill">
                                @if($isAuction)
                                    <input type="text" class="mobile-qty-input bg-light" value="1" readonly style="width: 100%; text-align: center;">
                                @else
                                    <button class="mobile-qty-btn btn-quantity" type="button" data-action="decrease" data-id="{{ $item->id }}">-</button>
                                    <input type="text" class="mobile-qty-input quantity-display-{{ $item->id }}" value="{{ $quantity }}" readonly>
                                    <button class="mobile-qty-btn btn-quantity" type="button" data-action="increase" data-id="{{ $item->id }}">+</button>
                                @endif
                            </div>
                            <div class="fw-bold text-dark" style="font-size: 13px;">
                                 <span id="price-per-item-{{ $item->id }}" data-price="{{ $price }}" class="d-none"></span> 
                                 <span class="total-display-{{ $item->id }}">Rp{{ number_format($total, 0, ',', '.') }}</span>
                            </div>

                            {{-- LOGIC: Tombol Hapus Mobile hanya muncul jika BUKAN lelang --}}
                            @if(!$isAuction)
                                <a href="#" class="text-danger small text-decoration-none fw-bold btn-delete-item" 
                                   data-id="{{ $item->id }}" data-url="{{ route('cart.destroy', $item->id) }}" style="font-size: 11px;">
                                    Hapus
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif
            </div>

            <div class="row mt-4 mt-md-5 mb-5">
                <div class="col-12 col-md-5 ms-auto">
                    <div class="card border-0 shadow-sm bg-white p-3 p-md-4 rounded-3">
                        <div class="form-check mb-3">
                            <input class="form-check-input mt-1" type="checkbox" name="wrap_product" value="10000" id="wrapProductCheckbox">
                            <label class="form-check-label ms-2 lh-sm" for="wrapProductCheckbox">
                                <span class="fw-bold d-block">For Rp. 10.000 please wrap the product</span>
                            </label>
                        </div>
                        <hr class="my-3 my-md-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fw-bold text-secondary">Subtotal</span>
                            <span class="fw-bold fs-4 text-dark" id="subtotalDisplay">Rp0</span>
                        </div>
                        <div id="mixed-warning-placeholder" class="mb-2 d-none">
                            <div id="mixedWarning" class="alert alert-warning py-2 mb-2" role="alert" style="font-size:13px;">
                                <strong>Perhatian:</strong> Anda memilih produk <em>lelang</em> dan <em>merchandise</em> bersamaan. Silakan pilih satu jenis produk untuk checkout.
                            </div>
                        </div>
                        <button type="submit" id="checkoutButton" class="btn btn-dark w-100 py-3 fw-bold shadow-sm transition-btn">
                            Checkout
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const formatRupiah = (number) => 'Rp' + new Intl.NumberFormat('id-ID').format(number);

    function syncCheckbox(el, id) {
        const checkboxes = document.querySelectorAll(`input[value="${id}"].item-checkbox`);
        checkboxes.forEach(box => box.checked = el.checked);
        updateSubtotal();
        updateCheckoutState();
    }

    function updateSubtotal() {
        const wrapCheckbox = document.getElementById('wrapProductCheckbox');
        const subtotalDisplay = document.getElementById('subtotalDisplay');
        let newSubtotal = 0;
        let checkedIds = new Set(); 

        document.querySelectorAll('.item-checkbox:checked').forEach(function (checkbox) {
            const itemId = checkbox.value;
            if (!checkedIds.has(itemId)) {
                newSubtotal += parseFloat(checkbox.getAttribute('data-item-total'));
                checkedIds.add(itemId);
            }
        });
        if (wrapCheckbox && wrapCheckbox.checked && checkedIds.size > 0) {
            newSubtotal += parseFloat(wrapCheckbox.value);
        }
        if(subtotalDisplay) subtotalDisplay.innerText = formatRupiah(newSubtotal);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const wrapCheckbox = document.getElementById('wrapProductCheckbox');
        if(wrapCheckbox) wrapCheckbox.addEventListener('change', updateSubtotal);
        updateSubtotal();

        const checkoutForm = document.getElementById('checkout-form');
        if(checkoutForm) {
            checkoutForm.addEventListener('submit', function(e) {
                const checkedBoxes = Array.from(document.querySelectorAll('.item-checkbox:checked'));
                if (checkedBoxes.length === 0) {
                    e.preventDefault();
                    alert('Harap pilih minimal satu produk untuk checkout!');
                    return;
                }

                // Cek apakah user memilih campuran antara lelang dan merch
                const types = new Set(checkedBoxes.map(cb => cb.getAttribute('data-type') || 'merch'));
                if (types.size > 1) {
                    e.preventDefault();
                    alert('Tidak dapat checkout produk lelang dan merchandise bersamaan. Silakan pilih dan checkout satu jenis produk saja.');
                    return;
                }
            });
        }

        // Update checkout button state and show mixed warning
        function updateCheckoutState() {
            const checkedBoxes = Array.from(document.querySelectorAll('.item-checkbox:checked'));
            const checkoutBtn = document.getElementById('checkoutButton');
            const mixedPlaceholder = document.getElementById('mixed-warning-placeholder');

            if (!checkoutBtn) return;

            if (checkedBoxes.length === 0) {
                checkoutBtn.disabled = false; // allow user to open checkout page but validation will prevent empty selection
                if (mixedPlaceholder) mixedPlaceholder.classList.add('d-none');
                return;
            }

            const types = new Set(checkedBoxes.map(cb => cb.getAttribute('data-type') || 'merch'));
            if (types.size > 1) {
                // Mixed types selected -> disable button and show warning
                checkoutBtn.disabled = true;
                if (mixedPlaceholder) mixedPlaceholder.classList.remove('d-none');
            } else {
                checkoutBtn.disabled = false;
                if (mixedPlaceholder) mixedPlaceholder.classList.add('d-none');
            }
        }

        // Attach change listeners to checkboxes to update state dynamically
        document.querySelectorAll('.item-checkbox').forEach(cb => cb.addEventListener('change', updateCheckoutState));

        // Initial state
        updateCheckoutState();

        // --- 1. LOGIC AJAX UPDATE OPTION (HARGA & GAMBAR BERUBAH) ---
        document.querySelectorAll('.option-selector').forEach(select => {
            select.addEventListener('change', function() {
                const itemId = this.getAttribute('data-id');
                const type = this.getAttribute('data-type'); 
                const value = this.value;

                // Sync UI Desktop & Mobile
                document.querySelectorAll(`.option-selector[data-id="${itemId}"][data-type="${type}"]`).forEach(s => s.value = value);

                fetch('/cart/update-option/' + itemId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ [type]: value })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        // Update Harga & Total (Tanpa Refresh)
                        document.querySelectorAll(`.price-display-${itemId}`).forEach(el => {
                            el.innerText = formatRupiah(data.new_price);
                            el.setAttribute('data-price', data.new_price);
                        });
                        const priceRef = document.getElementById(`price-per-item-${itemId}`);
                        if(priceRef) priceRef.setAttribute('data-price', data.new_price);

                        // Jika backend menyesuaikan quantity karena stok, sinkronkan tampilan
                        if (typeof data.newQuantity !== 'undefined') {
                            document.querySelectorAll(`.quantity-display-${itemId}`).forEach(el => el.value = data.newQuantity);
                            // mobile inputs use same class in templates; ensure mobile qty displays also update
                            document.querySelectorAll(`.mobile-qty-input.quantity-display-${itemId}`).forEach(el => el.value = data.newQuantity);
                        }

                        document.querySelectorAll(`.total-display-${itemId}`).forEach(el => {
                            el.innerText = formatRupiah(data.new_total);
                        });
                        document.querySelectorAll(`input[value="${itemId}"].item-checkbox`).forEach(box => {
                            box.setAttribute('data-item-total', data.new_total);
                        });
                        // Update Gambar
                        document.querySelectorAll(`.img-product-${itemId}`).forEach(img => {
                            img.src = data.new_image;
                        });

                        // Rebuild Size Dropdown Jika Ganti Varian
                        if (type === 'variant_id') {
                            const sizeSelectors = document.querySelectorAll(`.size-selector-${itemId}`);
                            sizeSelectors.forEach(sizeSelect => {
                                sizeSelect.innerHTML = '';
                                if (data.available_sizes && data.available_sizes.length > 0) {
                                    sizeSelect.disabled = false;
                                    data.available_sizes.forEach(sizeObj => {
                                        let option = document.createElement('option');
                                        option.value = sizeObj.id;
                                        // Disable option if stock is zero or missing
                                        const stock = typeof sizeObj.stock !== 'undefined' ? parseInt(sizeObj.stock) : null;
                                        option.disabled = (stock !== null && stock <= 0);
                                        option.text = sizeObj.size + ((stock !== null && stock <= 0) ? ' (Habis)' : '');
                                        if (sizeObj.id == data.new_size_id && !option.disabled) option.selected = true;
                                        sizeSelect.appendChild(option);
                                    });
                                } else {
                                    let option = document.createElement('option');
                                    option.text = "-";
                                    sizeSelect.appendChild(option);
                                    sizeSelect.disabled = true;
                                }
                            });
                        }

                        // Update Subtotal Global
                        const checkbox = document.querySelector(`input[value="${itemId}"].item-checkbox`);
                        if(checkbox && checkbox.checked) updateSubtotal();

                    } else {
                        alert('Gagal update opsi. ' + (data.message || ''));
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });

        // --- 2. LOGIC UPDATE QUANTITY (debounced per item, optimistic UI) ---
        const quantityTimers = {};
        const pendingDesired = {};
        const DEBOUNCE_MS = 700;

        document.querySelectorAll('.btn-quantity').forEach(function(button) {
            button.addEventListener('click', function() {
                const action = this.getAttribute('data-action');
                const id = this.getAttribute('data-id');
                const inputElements = document.querySelectorAll('.quantity-display-' + id);
                if (!inputElements || inputElements.length === 0) return;

                const currentQuantity = parseInt(inputElements[0].value) || 0;
                let desired = currentQuantity + (action === 'increase' ? 1 : -1);
                if (desired < 1) return;

                // Optimistic UI update
                inputElements.forEach(el => el.value = desired);
                const price = parseFloat(document.getElementById('price-per-item-' + id).getAttribute('data-price')) || 0;
                const optimisticTotal = price * desired;
                document.querySelectorAll('.total-display-' + id).forEach(el => el.innerText = formatRupiah(optimisticTotal));
                document.querySelectorAll(`input[value="${id}"].item-checkbox`).forEach(el => el.setAttribute('data-item-total', optimisticTotal));
                if(document.querySelector(`input[value="${id}"].item-checkbox`).checked) updateSubtotal();

                // store desired quantity and debounce API call
                pendingDesired[id] = desired;
                if (quantityTimers[id]) clearTimeout(quantityTimers[id]);

                quantityTimers[id] = setTimeout(() => {
                    // disable buttons for this item while request in-flight
                    document.querySelectorAll(`.btn-quantity[data-id="${id}"]`).forEach(b => b.disabled = true);

                    fetch('/cart/update/' + id, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ quantity: pendingDesired[id] })
                    })
                    .then(response => response.json())
                    .then(data => {
                        const priceLocal = parseFloat(document.getElementById('price-per-item-' + id).getAttribute('data-price')) || 0;
                        if (data && data.newQuantity !== undefined) {
                            const finalQty = data.newQuantity;
                            document.querySelectorAll('.quantity-display-' + id).forEach(el => el.value = finalQty);
                            const finalTotal = priceLocal * finalQty;
                            document.querySelectorAll('.total-display-' + id).forEach(el => el.innerText = formatRupiah(finalTotal));
                            document.querySelectorAll(`input[value="${id}"].item-checkbox`).forEach(el => el.setAttribute('data-item-total', finalTotal));
                            if(document.querySelector(`input[value="${id}"].item-checkbox`).checked) updateSubtotal();
                        }
                        if (data && data.success === false && data.message) {
                            // Show message if server rejected the update
                            alert(data.message);
                        }
                    })
                    .catch(err => {
                        console.error('Error updating quantity:', err);
                    })
                    .finally(() => {
                        // re-enable buttons and clear pending state
                        document.querySelectorAll(`.btn-quantity[data-id="${id}"]`).forEach(b => b.disabled = false);
                        if (quantityTimers[id]) { clearTimeout(quantityTimers[id]); delete quantityTimers[id]; }
                        if (pendingDesired[id]) delete pendingDesired[id];
                    });
                }, DEBOUNCE_MS);
            });
        });

        // --- 3. LOGIC DELETE ITEM (DIKEMBALIKAN KE VERSI SEBELUMNYA) ---
        document.querySelectorAll('.btn-delete-item').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                if(!confirm('Yakin ingin menghapus item ini?')) return;

                const url = this.getAttribute('data-url');
                const id = this.getAttribute('data-id');
                
                // Ambil kedua row (Mobile & Desktop)
                const desktopRow = document.getElementById('item-row-desktop-' + id);
                const mobileRow = document.getElementById('item-row-mobile-' + id);

                this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                
                fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        // Hapus Desktop Row
                        if(desktopRow) {
                            desktopRow.style.transition = "opacity 0.3s";
                            desktopRow.style.opacity = "0";
                            setTimeout(() => desktopRow.remove(), 300);
                        }
                        // Hapus Mobile Row
                        if(mobileRow) {
                            mobileRow.style.transition = "opacity 0.3s";
                            mobileRow.style.opacity = "0";
                            setTimeout(() => mobileRow.remove(), 300);
                        }

                        setTimeout(() => {
                            updateSubtotal();
                            const remainingItems = document.querySelectorAll('.cart-item-row');
                            if(remainingItems.length === 0) {
                                document.getElementById('cart-content-view').classList.add('d-none');
                                document.getElementById('empty-cart-view').classList.remove('d-none');
                            }
                        }, 350);

                    } else {
                        alert('Gagal menghapus item.');
                        this.innerHTML = 'Hapus';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan sistem.');
                    this.innerHTML = 'Hapus';
                });
            });
        });
    });
</script>
@endpush