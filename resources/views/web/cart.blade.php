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
        <form action="{{ route('checkout.index') }}" method="GET" id="checkout-form">
            @csrf 

            <div class="row g-3 text-muted mb-3 pb-2 border-bottom d-none d-md-flex">
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
                        $product = $item->merchProduct;
                        if (!$product) continue; 

                        // --- DATA LOGIC ---
                        $availableVariants = $product->variants ?? collect([]); 
                        $currentVariantId = $item->merch_product_variant_id;
                        $currentVariant = $currentVariantId ? \App\models\MerchProductVariant::find($currentVariantId) : null;

                        $availableSizes = ($currentVariant) ? $currentVariant->sizes : collect([]);
                        $currentSizeId = $item->merch_product_variant_size_id;

                        // Gambar Logic
                        $imgUrl = 'https://via.placeholder.com/100';
                        if ($currentVariant && $currentVariant->images->count() > 0) {
                            $imgUrl = asset($currentVariant->images->first()->image_path);
                        } elseif ($product->images->count() > 0) {
                            $imgUrl = asset($product->images->first()->image_path);
                        }

                        $price = $item->price; 
                        $quantity = $item->quantity;
                        $total = $price * $quantity; 
                    @endphp

                    {{-- A. DESKTOP VIEW --}}
                    <div class="row g-3 align-items-center my-3 py-2 border-bottom cart-item-row desktop-view d-none d-md-flex" id="item-row-desktop-{{ $item->id }}">
                        <div class="col-1 text-center">
                            <input class="form-check-input item-checkbox" type="checkbox" name="cart_item_ids[]" value="{{ $item->id }}" data-item-total="{{ $total }}" onclick="syncCheckbox(this, {{ $item->id }})">
                        </div>
                        <div class="col-md-4 d-flex align-items-center">
                            <img src="{{ $imgUrl }}" class="rounded me-3 img-product-{{ $item->id }}" style="width: 70px; height: 70px; object-fit: cover; border: 1px solid #f8f9fa;">
                            <div class="w-100 pe-3">
                                <h6 class="mb-1 fw-bold text-dark text-truncate">{{ $product->name }}</h6>
                                
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    {{-- Dropdown Varian Compact --}}
                                    @if($availableVariants->count() > 0)
                                        <select class="custom-select-compact option-selector" 
                                                data-id="{{ $item->id }}" data-type="variant_id" title="Pilih Varian">
                                            @foreach($availableVariants as $v)
                                                <option value="{{ $v->id }}" {{ $currentVariantId == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                                            @endforeach
                                        </select>
                                    @endif

                                    {{-- Dropdown Size Compact --}}
                                    <select class="custom-select-compact option-selector size-selector-{{ $item->id }}" 
                                            data-id="{{ $item->id }}" data-type="size_id" 
                                            {{ $availableSizes->isEmpty() ? 'disabled' : '' }} 
                                            title="Pilih Ukuran" style="min-width: 60px;">
                                        @if($availableSizes->count() > 0)
                                            @foreach($availableSizes as $s)
                                                <option value="{{ $s->id }}" {{ $currentSizeId == $s->id ? 'selected' : '' }}>{{ $s->size }}</option>
                                            @endforeach
                                        @else
                                            <option value="">-</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <span class="fw-semibold price-display-{{ $item->id }}" data-price="{{ $price }}">Rp{{ number_format($price, 0, ',', '.') }}</span>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="input-group input-group-sm mx-auto" style="width: 100px;">
                                <button class="btn btn-outline-secondary btn-quantity" type="button" data-action="decrease" data-id="{{ $item->id }}">-</button>
                                <input type="text" class="form-control text-center bg-white quantity-display-{{ $item->id }}" value="{{ $quantity }}" readonly>
                                <button class="btn btn-outline-secondary btn-quantity" type="button" data-action="increase" data-id="{{ $item->id }}">+</button>
                            </div>
                        </div>
                        <div class="col-md-2 text-end">
                            <strong class="fs-6 text-primary d-block mb-2 text-dark total-display-{{ $item->id }}">Rp{{ number_format($total, 0, ',', '.') }}</strong>
                            <button type="button" class="btn btn-link text-danger p-0 small text-decoration-none btn-delete-item" 
                                    data-id="{{ $item->id }}" data-url="{{ route('cart.destroy', $item->id) }}">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </div>
                    </div>

                    {{-- B. MOBILE VIEW --}}
                    <div class="d-flex d-md-none align-items-start my-3 pb-3 border-bottom cart-item-row mobile-view" id="item-row-mobile-{{ $item->id }}">
                        <div class="me-3 d-flex align-items-center align-self-center">
                            <input class="form-check-input mobile-checkbox item-checkbox" type="checkbox" name="cart_item_ids[]" value="{{ $item->id }}" data-item-total="{{ $total }}" onclick="syncCheckbox(this, {{ $item->id }})">
                        </div>
                        <div class="flex-grow-1 pe-2" style="min-width: 0;">
                            <div class="d-flex align-items-start">
                                <img src="{{ $imgUrl }}" class="rounded border me-3 flex-shrink-0 img-product-{{ $item->id }}" style="width: 75px; height: 75px; object-fit: cover;">
                                <div class="flex-grow-1" style="min-width: 0;">
                                    <h6 class="mobile-product-title text-truncate">{{ $product->name }}</h6>
                                    
                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        @if($availableVariants->count() > 0)
                                            <select class="custom-select-compact option-selector" data-id="{{ $item->id }}" data-type="variant_id">
                                                @foreach($availableVariants as $v)
                                                    <option value="{{ $v->id }}" {{ $currentVariantId == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                                                @endforeach
                                            </select>
                                        @endif

                                        <select class="custom-select-compact option-selector size-selector-{{ $item->id }}" 
                                                data-id="{{ $item->id }}" data-type="size_id" 
                                                {{ $availableSizes->isEmpty() ? 'disabled' : '' }} style="min-width: 50px;">
                                            @if($availableSizes->count() > 0)
                                                @foreach($availableSizes as $s)
                                                    <option value="{{ $s->id }}" {{ $currentSizeId == $s->id ? 'selected' : '' }}>{{ $s->size }}</option>
                                                @endforeach
                                            @else
                                                <option value="">-</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column justify-content-between align-items-end ms-2" style="height: 75px;">
                            <div class="mobile-qty-pill">
                                <button class="mobile-qty-btn btn-quantity" type="button" data-action="decrease" data-id="{{ $item->id }}">-</button>
                                <input type="text" class="mobile-qty-input quantity-display-{{ $item->id }}" value="{{ $quantity }}" readonly>
                                <button class="mobile-qty-btn btn-quantity" type="button" data-action="increase" data-id="{{ $item->id }}">+</button>
                            </div>
                            <div class="fw-bold text-dark" style="font-size: 13px;">
                                 <span id="price-per-item-{{ $item->id }}" data-price="{{ $price }}" class="d-none"></span> 
                                 <span class="total-display-{{ $item->id }}">Rp{{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                            <a href="#" class="text-danger small text-decoration-none fw-bold btn-delete-item" 
                               data-id="{{ $item->id }}" data-url="{{ route('cart.destroy', $item->id) }}" style="font-size: 11px;">
                                Hapus
                            </a>
                        </div>
                    </div>
                @endforeach
            @endif
            </div>

            <div class="row mt-4 mt-md-5 mb-5">
                <div class="col-12 col-md-5 ms-auto">
                    <div class="card border-0 shadow-sm bg-white p-3 p-md-4 rounded-3">
                        <div class="form-check mb-3 p-3 bg-light rounded">
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
                        <button type="submit" class="btn btn-dark w-100 py-3 fw-bold shadow-sm transition-btn">
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
                if (document.querySelectorAll('.item-checkbox:checked').length === 0) {
                    e.preventDefault();
                    alert('Harap pilih minimal satu produk untuk checkout!');
                }
            });
        }

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
                                        option.text = sizeObj.size;
                                        if (sizeObj.id == data.new_size_id) option.selected = true;
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

        // --- 2. LOGIC UPDATE QUANTITY ---
        document.querySelectorAll('.btn-quantity').forEach(function(button) {
            button.addEventListener('click', function() {
                const action = this.getAttribute('data-action');
                const id = this.getAttribute('data-id');
                const inputElement = document.querySelector('.quantity-display-' + id);
                
                let currentQuantity = parseInt(inputElement.value);
                let newQuantity = currentQuantity + (action === 'increase' ? 1 : -1);
                
                if (newQuantity < 1 || newQuantity === currentQuantity) return;

                this.disabled = true; 
                fetch('/cart/update/' + id, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ quantity: newQuantity })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const price = parseFloat(document.getElementById('price-per-item-' + id).getAttribute('data-price'));
                        const total = price * data.newQuantity;
                        
                        document.querySelectorAll('.quantity-display-' + id).forEach(el => el.value = data.newQuantity);
                        document.querySelectorAll('.total-display-' + id).forEach(el => el.innerText = formatRupiah(total));
                        document.querySelectorAll(`input[value="${id}"].item-checkbox`).forEach(el => el.setAttribute('data-item-total', total));
                        
                        if(document.querySelector(`input[value="${id}"].item-checkbox`).checked) updateSubtotal();
                    } else {
                        alert(data.message);
                    }
                })
                .finally(() => { this.disabled = false; });
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