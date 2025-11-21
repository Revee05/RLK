@extends('web.partials.layout')

@section('content')
<div class="container my-5">
    <h2 class="fw-bold mb-5">Keranjang</h2> 

    {{-- Alert Section --}}
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

    <form action="/checkout" method="POST">
        @csrf 

        {{-- HEADER TABEL --}}
        <div class="row g-3 text-muted mb-3 pb-2 border-bottom d-none d-md-flex">
            <div class="col-1 text-center">
                {{-- Header Checkbox --}}
            </div>
            <div class="col-md-4">Produk</div>
            <div class="col-md-2">Harga</div>
            <div class="col-md-3 text-center">Jumlah</div>
            <div class="col-md-2 text-end">Total</div>
        </div>

        @if($cartItems && !$cartItems->isEmpty())

            @foreach($cartItems as $item)
                @php 
                    // 1. Ambil Data Relasi
                    $product = $item->merchProduct;
                    $variant = $item->merchVariant; // Data Varian (Warna/Model)
                    $size    = $item->merchSize;    // Data Size (Ukuran)

                    if (!$product) continue; 

                    // 2. Logika Gambar: Prioritas Gambar Varian > Gambar Produk Utama
                    $imgUrl = 'https://via.placeholder.com/100';
                    
                    // Cek apakah varian punya gambar spesifik?
                    if ($variant && $variant->images && $variant->images->isNotEmpty()) {
                        $imgUrl = asset($variant->images->first()->image_path);
                    } 
                    // Jika tidak, pakai gambar default produk
                    elseif ($product->images && $product->images->isNotEmpty()) {
                        $imgUrl = asset($product->images->first()->image_path);
                    }

                    // 3. Data Teks
                    $nama_produk = $product->name;
                    $variantName = $variant ? $variant->name : ''; // Contoh: "Hitam"
                    $sizeName    = $size ? $size->size : '';       // Contoh: "XL"

                    $price = $item->price; 
                    $quantity = $item->quantity;
                    $total = $price * $quantity; 
                    
                    $kategori = $product->categories->first();
                @endphp

                {{-- ITEM ROW --}}
                <div class="row g-3 align-items-center my-3 py-2 border-bottom">
                    
                    {{-- 1. Checkbox (Rata Tengah) --}}
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
                            {{-- Gambar --}}
                            <img src="{{ $imgUrl }}" alt="{{ $nama_produk }}" 
                                 style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-right: 15px; border: 1px solid #f0f0f0;">
                            
                            <div>
                                <h6 class="mb-1 fw-bold text-dark">{{ $nama_produk }}</h6>
                                
                                {{-- Tampilkan Varian & Size --}}
                                <div class="mb-2">
                                    @if($variantName)
                                        <span class="badge bg-light text-dark border fw-normal me-1">{{ $variantName }}</span>
                                    @endif
                                    @if($sizeName)
                                        <span class="badge bg-light text-dark border fw-normal">Size: {{ $sizeName }}</span>
                                    @endif
                                </div>

                                {{-- Kategori (Opsional) --}}
                                @if($kategori)
                                    <small class="text-muted d-block mb-1">{{ $kategori->name }}</small>
                                @endif
                                
                                {{-- Tombol Hapus --}}
                                {{-- Tombol Hapus yang Benar --}}
                                <button type="submit" 
                                        form="delete-form-{{ $item->id }}" 
                                        class="btn btn-link text-danger p-0 m-0 small text-decoration-none" 
                                        onclick="return confirm('Yakin ingin menghapus item ini?')">
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

                    {{-- 4. Jumlah (Rata Tengah) --}}
                    <div class="col-md-3 d-flex justify-content-center">
                        <div class="input-group input-group-sm" style="width: 110px;">
                            <button class="btn btn-outline-secondary btn-quantity" type="button" data-action="decrease" data-id="{{ $item->id }}">-</button>
                            <input type="text" class="form-control text-center quantity-input bg-white" id="quantity-input-{{ $item->id }}" value="{{ $quantity }}" readonly>
                            <button class="btn btn-outline-secondary btn-quantity" type="button" data-action="increase" data-id="{{ $item->id }}">+</button>
                        </div>
                    </div>

                    {{-- 5. Total (Rata Kanan) --}}
                    <div class="col-md-2 text-end">
                        <strong class="fs-6 text-primary" id="row-total-{{ $item->id }}">
                            Rp{{ number_format($total, 0, ',', '.') }}
                        </strong>
                    </div>
                
                </div>
            @endforeach
        @else
            {{-- KOSONG --}}
            <div class="row">
                <div class="col-12 my-5 text-center py-5 bg-light rounded-3">
                    <h4 class="text-muted mb-3">Keranjang belanja Anda kosong.</h4>
                    <a href="/" class="btn btn-dark px-4">Mulai Belanja</a>
                </div>
            </div>
        @endif

        {{-- SUMMARY SECTION --}}
        @if($cartItems && !$cartItems->isEmpty())
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
        @endif
    </form>
</div>
@endsection

@foreach($cartItems as $item)
    <form id="delete-form-{{ $item->id }}" 
          action="{{ route('cart.destroy', $item->id) }}" 
          method="POST" 
          style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endforeach

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.addEventListener('DOMContentLoaded', function () {
        var itemCheckboxes = document.querySelectorAll('.item-checkbox');
        var wrapCheckbox = document.getElementById('wrapProductCheckbox');
        var subtotalDisplay = document.getElementById('subtotalDisplay');

        // Helper: Format Rupiah
        const formatRupiah = (number) => {
            return 'Rp' + new Intl.NumberFormat('id-ID').format(number);
        }

        function updateSubtotal() {
            var newSubtotal = 0;
            itemCheckboxes.forEach(function (checkbox) {
                if (checkbox.checked) {
                    newSubtotal += parseFloat(checkbox.getAttribute('data-item-total'));
                }
            });
            if (wrapCheckbox && wrapCheckbox.checked && newSubtotal > 0) {
                newSubtotal += parseFloat(wrapCheckbox.value);
            }
            if(subtotalDisplay) {
                subtotalDisplay.innerText = formatRupiah(newSubtotal);
            }
        }

        itemCheckboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', updateSubtotal);
        });
        
        if(wrapCheckbox){
            wrapCheckbox.addEventListener('change', updateSubtotal);
        }
        
        // Panggil sekali di awal
        updateSubtotal();

        // Logic Kuantitas
        function updateRow(id, newQuantity) {
            var inputElement = document.getElementById('quantity-input-' + id);
            var priceElement = document.getElementById('price-per-item-' + id);
            var rowTotalElement = document.getElementById('row-total-' + id);
            var checkboxElement = document.getElementById('checkbox-' + id);

            var pricePerItem = parseFloat(priceElement.getAttribute('data-price'));
            var newRowTotal = pricePerItem * newQuantity;

            // Update UI Row
            inputElement.value = newQuantity; // Tidak perlu padding 0 di frontend modern biasanya, tapi kalau mau pakai .toString().padStart(2, '0')
            rowTotalElement.innerText = formatRupiah(newRowTotal);
            checkboxElement.setAttribute('data-item-total', newRowTotal);
            
            // Update subtotal jika checkbox item tersebut sedang dicentang
            if(checkboxElement.checked) {
                updateSubtotal();
            }
        }

        function saveQuantityToBackend(id, quantity, btnElement) {
            // Disable button sementara loading
            const originalText = btnElement.innerText;
            btnElement.disabled = true;
            document.body.style.cursor = 'wait';

            fetch('/cart/update/' + id, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ quantity: quantity })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateRow(id, data.newQuantity);
                } else {
                    // Error handler (misal stok habis)
                    alert(data.message);
                    updateRow(id, data.quantity || 1);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan koneksi.');
            })
            .finally(() => {
                btnElement.disabled = false;
                document.body.style.cursor = 'default';
            });
        }

        document.querySelectorAll('.btn-quantity').forEach(function(button) {
            button.addEventListener('click', function() {
                var action = this.getAttribute('data-action');
                var id = this.getAttribute('data-id');
                var inputElement = document.getElementById('quantity-input-' + id);
                
                var currentQuantity = parseInt(inputElement.value);
                var newQuantity = currentQuantity;

                if (action === 'increase') newQuantity += 1;
                if (action === 'decrease') newQuantity -= 1;
                
                if (newQuantity < 1) return; // Min 1
                if (newQuantity === currentQuantity) return;

                saveQuantityToBackend(id, newQuantity, this);
            });
        });
    });
</script>
@endpush