@extends('web.partials.layout')

@section('content')
<div class="container my-5">
    <h2 class="fw-bold mb-5">Keranjang</h2> 

    {{-- Alert Section (Untuk Feedback AJAX) --}}
    <div id="ajax-alert-container"></div>

    {{-- Alert Session (Untuk Redirect biasa) --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- LOGIKA TAMPILAN KOSONG VS ISI --}}
    @php
        $hasItems = $cartItems && !$cartItems->isEmpty();
    @endphp

    {{-- 1. TAMPILAN KERANJANG KOSONG (Hidden by default jika ada item) --}}
    <div id="empty-cart-view" class="{{ $hasItems ? 'd-none' : '' }}">
        <div class="row">
            <div class="col-12 my-5 text-center py-5 bg-light rounded-3">
                <h4 class="text-muted mb-3">Keranjang belanja Anda kosong.</h4>
                <a href="/" class="btn btn-dark px-4">Mulai Belanja</a>
            </div>
        </div>
    </div>

    {{-- 2. TAMPILAN LIST ITEM (Hidden jika kosong) --}}
    <div id="cart-content-view" class="{{ $hasItems ? '' : 'd-none' }}">
        <form action="{{ route('checkout.index') }}" method="GET" id="checkout-form">
            @csrf 

            {{-- HEADER TABEL --}}
            <div class="row g-3 text-muted mb-3 pb-2 border-bottom d-none d-md-flex">
                <div class="col-1 text-center"></div>
                <div class="col-md-4">Produk</div>
                <div class="col-md-2">Harga</div>
                <div class="col-md-3 text-center">Jumlah</div>
                <div class="col-md-2 text-end">Total</div>
            </div>

            {{-- CONTAINER ITEM --}}
            <div id="cart-items-list">
            @if($hasItems)
                @foreach($cartItems as $item)
                    @php 
                        $product = $item->merchProduct;
                        $variant = $item->merchVariant; 
                        $size    = $item->merchSize;

                        if (!$product) continue; 

                        // Logika Gambar
                        $imgUrl = 'https://via.placeholder.com/100';
                        if ($variant && $variant->images && $variant->images->isNotEmpty()) {
                            $imgUrl = asset($variant->images->first()->image_path);
                        } elseif ($product->images && $product->images->isNotEmpty()) {
                            $imgUrl = asset($product->images->first()->image_path);
                        }

                        $nama_produk = $product->name;
                        $variantName = $variant ? $variant->name : ''; 
                        $sizeName    = $size ? $size->size : ''; 

                        $price = $item->price; 
                        $quantity = $item->quantity;
                        $total = $price * $quantity; 
                        $kategori = $product->categories->first();
                    @endphp

                    {{-- ITEM ROW (Tambahkan ID unik untuk manipulasi DOM) --}}
                    <div class="row g-3 align-items-center my-3 py-2 border-bottom cart-item-row" id="item-row-{{ $item->id }}">
                        
                        {{-- 1. Checkbox --}}
                        <div class="col-1 d-flex justify-content-center">
                            <input class="form-check-input item-checkbox" 
                                   type="checkbox" 
                                   name="cart_item_ids[]" 
                                   value="{{ $item->id }}" 
                                   id="checkbox-{{ $item->id }}"
                                   data-item-total="{{ $total }}"
                                   style="transform: scale(1.2);">
                        </div>

                        {{-- 2. Info Produk --}}
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <img src="{{ $imgUrl }}" alt="{{ $nama_produk }}" 
                                     style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-right: 15px; border: 1px solid #f0f0f0;">
                                
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">{{ $nama_produk }}</h6>
                                    <div class="mb-2">
                                        @if($variantName) <span class="badge bg-light text-dark border fw-normal me-1">{{ $variantName }}</span> @endif
                                        @if($sizeName) <span class="badge bg-light text-dark border fw-normal">Size: {{ $sizeName }}</span> @endif
                                    </div>
                                    @if($kategori) <small class="text-muted d-block mb-1">{{ $kategori->name }}</small> @endif
                                    
                                    {{-- TOMBOL HAPUS AJAX (Type Button, bukan Submit) --}}
                                    <button type="button" 
                                            class="btn btn-link text-danger p-0 m-0 small text-decoration-none btn-delete-item" 
                                            data-id="{{ $item->id }}"
                                            data-url="{{ route('cart.destroy', $item->id) }}">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- 3. Harga --}}
                        <div class="col-md-2">
                            <span id="price-per-item-{{ $item->id }}" data-price="{{ $price }}" class="fw-semibold">
                                Rp{{ number_format($price, 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- 4. Jumlah --}}
                        <div class="col-md-3 d-flex justify-content-center">
                            <div class="input-group input-group-sm" style="width: 110px;">
                                <button class="btn btn-outline-secondary btn-quantity" type="button" data-action="decrease" data-id="{{ $item->id }}">-</button>
                                <input type="text" class="form-control text-center quantity-input bg-white" id="quantity-input-{{ $item->id }}" value="{{ $quantity }}" readonly>
                                <button class="btn btn-outline-secondary btn-quantity" type="button" data-action="increase" data-id="{{ $item->id }}">+</button>
                            </div>
                        </div>

                        {{-- 5. Total --}}
                        <div class="col-md-2 text-end">
                            <strong class="fs-6 text-primary" id="row-total-{{ $item->id }}">
                                Rp{{ number_format($total, 0, ',', '.') }}
                            </strong>
                        </div>
                    </div>
                @endforeach
            @endif
            </div>

            {{-- SUMMARY SECTION --}}
            <div class="row mt-5">
                <div class="col-md-5 offset-md-7">
                    <div class="card border-0 shadow-sm bg-white p-4 rounded-3">
                        <div class="form-check mb-3 p-3 bg-light rounded">
                            <input class="form-check-input mt-1" type="checkbox" name="wrap_product" value="10000" id="wrapProductCheckbox">
                            <label class="form-check-label ms-2" for="wrapProductCheckbox">
                                <span class="fw-bold d-block">For Rp. 10.000 please wrap the product</span>
                            </label>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fw-bold text-secondary">Subtotal</span>
                            <span class="fw-bold fs-5 text-dark" id="subtotalDisplay">Rp0</span>
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

    // Helper: Format Rupiah
    const formatRupiah = (number) => {
        return 'Rp' + new Intl.NumberFormat('id-ID').format(number);
    }

    document.addEventListener('DOMContentLoaded', function () {
        // --- 1. Logic Subtotal ---
        const wrapCheckbox = document.getElementById('wrapProductCheckbox');
        const subtotalDisplay = document.getElementById('subtotalDisplay');
        
        function updateSubtotal() {
            // Ambil ulang semua checkbox yang masih ada di DOM
            const itemCheckboxes = document.querySelectorAll('.item-checkbox');
            let newSubtotal = 0;
            let checkedCount = 0;

            itemCheckboxes.forEach(function (checkbox) {
                if (checkbox.checked) {
                    newSubtotal += parseFloat(checkbox.getAttribute('data-item-total'));
                    checkedCount++;
                }
            });

            if (wrapCheckbox && wrapCheckbox.checked && checkedCount > 0) {
                newSubtotal += parseFloat(wrapCheckbox.value);
            }

            if(subtotalDisplay) {
                subtotalDisplay.innerText = formatRupiah(newSubtotal);
            }
        }

        // Event listener global untuk checkbox (delegation lebih aman jika ada perubahan DOM ekstrem, tapi langsung juga oke)
        document.body.addEventListener('change', function(e){
            if(e.target.classList.contains('item-checkbox') || e.target.id === 'wrapProductCheckbox'){
                updateSubtotal();
            }
        });
        
        // Init awal
        updateSubtotal();


        // --- 2. Logic Update Quantity (AJAX) ---
        function updateRow(id, newQuantity) {
            const inputElement = document.getElementById('quantity-input-' + id);
            const priceElement = document.getElementById('price-per-item-' + id);
            const rowTotalElement = document.getElementById('row-total-' + id);
            const checkboxElement = document.getElementById('checkbox-' + id);

            if(!inputElement) return; // Guard clause jika elemen sudah dihapus

            const pricePerItem = parseFloat(priceElement.getAttribute('data-price'));
            const newRowTotal = pricePerItem * newQuantity;

            inputElement.value = newQuantity;
            rowTotalElement.innerText = formatRupiah(newRowTotal);
            checkboxElement.setAttribute('data-item-total', newRowTotal);
            
            // Update subtotal jika checkbox item tersebut sedang dicentang
            if(checkboxElement.checked) {
                updateSubtotal();
            }
        }

        document.querySelectorAll('.btn-quantity').forEach(function(button) {
            button.addEventListener('click', function() {
                const action = this.getAttribute('data-action');
                const id = this.getAttribute('data-id');
                const inputElement = document.getElementById('quantity-input-' + id);
                
                let currentQuantity = parseInt(inputElement.value);
                let newQuantity = currentQuantity;

                if (action === 'increase') newQuantity += 1;
                if (action === 'decrease') newQuantity -= 1;
                
                if (newQuantity < 1) return;
                if (newQuantity === currentQuantity) return;

                // UI Optimistic Update (Biar cepat)
                // updateRow(id, newQuantity); 
                // Kita pakai backend confirmation saja agar aman stok
                
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
                        updateRow(id, data.newQuantity);
                    } else {
                        alert(data.message);
                        updateRow(id, data.quantity || 1);
                    }
                })
                .catch(error => console.error('Error:', error))
                .finally(() => {
                    this.disabled = false;
                });
            });
        });


        // --- 3. Logic Delete Item (AJAX) ---
        document.querySelectorAll('.btn-delete-item').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                if(!confirm('Yakin ingin menghapus item ini?')) return;

                const url = this.getAttribute('data-url');
                const id = this.getAttribute('data-id');
                const row = document.getElementById('item-row-' + id);

                // Visual feedback loading
                this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Hapus...';
                this.disabled = true;

                fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json' // Meminta respon JSON
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        // 1. Hapus baris dari DOM dengan efek fade
                        row.style.transition = "all 0.3s ease";
                        row.style.opacity = "0";
                        
                        setTimeout(() => {
                            row.remove();
                            
                            // 2. Update Subtotal
                            updateSubtotal();

                            // 3. Cek apakah keranjang kosong?
                            const remainingItems = document.querySelectorAll('.cart-item-row');
                            if(remainingItems.length === 0) {
                                document.getElementById('cart-content-view').classList.add('d-none');
                                document.getElementById('empty-cart-view').classList.remove('d-none');
                            }
                        }, 300);

                    } else {
                        alert('Gagal menghapus item.');
                        this.disabled = false;
                        this.innerHTML = '<i class="bi bi-trash"></i> Hapus';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan sistem.');
                    this.disabled = false;
                    this.innerHTML = '<i class="bi bi-trash"></i> Hapus';
                });
            });
        });
    });
</script>
@endpush