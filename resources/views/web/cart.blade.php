{{-- filepath: resources/views/web/cart.blade.php --}}
@extends('web.partials.layout')

@section('content')
<div class="container my-5">
    <h2 class="fw-bold mb-5">Keranjang</h2> 

    <form action="/checkout" method="POST">
        @csrf 

        {{-- HEADER TABEL --}}
        {{-- Saya tambahkan border-bottom agar garisnya rapi dan text-center pada kolom yang perlu di tengah --}}
        <div class="row g-3 text-muted mb-3 pb-2 border-bottom d-none d-md-flex">
            <div class="col-1 text-center">
                {{-- Header Checkbox --}}
            </div>
            <div class="col-md-4">Produk</div>
            <div class="col-md-2">Harga</div>
            <div class="col-md-3 text-center">Jumlah</div> {{-- Judul Jumlah rata tengah --}}
            <div class="col-md-2 text-end">Total</div>
        </div>

        @if($cartItems && !$cartItems->isEmpty())

            @foreach($cartItems as $item)
                @php 
                    // Pengambilan Data
                    $data = $item->merchProduct;
                    if (!$data) continue; 

                    $price = $item->price; 
                    $quantity = $item->quantity;
                    $total = $price * $quantity; 
                    
                    $nama_produk = $data->name ?? 'Nama Produk Tidak Ditemukan';
                    $firstImage = $data->images->first(); 
                    $gambar_produk = $firstImage ? asset($firstImage->image_path) : 'https://via.placeholder.com/100';

                    $kategori = $data->categories->first();
                    $link_kategori = '#'; 
                @endphp

                {{-- ITEM ROW --}}
                {{-- align-items-center membuat semua elemen vertikal di tengah --}}
                <div class="row g-3 align-items-center my-3 py-2 border-bottom">
                    
                    {{-- 1. Checkbox (Rata Tengah) --}}
                    <div class="col-1 d-flex justify-content-center">
                        <input class="form-check-input item-checkbox" 
                               type="checkbox" 
                               name="cart_item_ids[]" 
                               value="{{ $item->id }}" 
                               id="checkbox-{{ $item->id }}"
                               data-item-total="{{ $total }}"
                               style="transform: scale(1.2);"> {{-- Sedikit diperbesar agar mudah diklik --}}
                    </div>

                    {{-- 2. Info Produk --}}
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <img src="{{ $gambar_produk }}" alt="{{ $nama_produk }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-right: 15px;">
                            <div>
                                <h6 class="mb-1 fw-bold">{{ $nama_produk }}</h6>
                                
                                @if($kategori)
                                    <p class="text-muted small mb-1">
                                        <a class="text-decoration-none text-secondary" href="{{ $link_kategori }}">
                                            {{ $kategori->name }}
                                        </a>
                                    </p>
                                @else
                                    <p class="text-muted small mb-1">Merchandise</p>
                                @endif
                                
                                <form action="{{ route('cart.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0 m-0 small text-decoration-none" onclick="return confirm('Yakin ingin menghapus item ini?')">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Harga --}}
                    <div class="col-md-2">
                        <span id="price-per-item-{{ $item->id }}" data-price="{{ $price }}">
                            Rp{{ number_format($price, 0, ',', '.') }}
                        </span>
                    </div>

                    {{-- 4. Jumlah (Rata Tengah) --}}
                    <div class="col-md-3 d-flex justify-content-center">
                        <div class="input-group" style="width: 120px;">
                            <button class="btn btn-outline-secondary btn-quantity" type="button" data-action="decrease" data-id="{{ $item->id }}">-</button>
                            <input type="text" class="form-control text-center quantity-input" id="quantity-input-{{ $item->id }}" value="{{ str_pad($quantity, 2, '0', STR_PAD_LEFT) }}" readonly>
                            <button class="btn btn-outline-secondary btn-quantity" type="button" data-action="increase" data-id="{{ $item->id }}">+</button>
                        </div>
                    </div>

                    {{-- 5. Total (Rata Kanan) --}}
                    <div class="col-md-2 text-end">
                        <strong class="fs-6" id="row-total-{{ $item->id }}">
                            Rp{{ number_format($total, 0, ',', '.') }}
                        </strong>
                    </div>
                
                </div>
            @endforeach
        @else
            <div class="row">
                <div class="col-12 my-5 text-center">
                    <p class="text-muted">Keranjang belanja Anda kosong.</p>
                    <a href="/" class="btn btn-primary btn-sm">Mulai Belanja</a>
                </div>
            </div>
        @endif

        {{-- SUMMARY SECTION --}}
        @if($cartItems && !$cartItems->isEmpty())
        <div class="row mt-5">
            <div class="col-md-5 offset-md-7">
                <div class="card border-0 bg-light p-4 rounded-3">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="wrap_product" value="10000" id="wrapProductCheckbox">
                        <label class="form-check-label" for="wrapProductCheckbox">
                            For Rp. 10.000 please wrap the product
                        </label>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold">Subtotal</span>
                        <span class="fw-bold " id="subtotalDisplay">Rp0</span>
                    </div>
                    
                    <button type="submit" class="btn btn-dark w-100 py-3 fw-bold" style="border-radius: 8px;">
                        Checkout 
                    </button>
                </div>
            </div>
        </div>
        @endif
    </form>
</div>
@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.addEventListener('DOMContentLoaded', function () {
        var itemCheckboxes = document.querySelectorAll('.item-checkbox');
        var wrapCheckbox = document.getElementById('wrapProductCheckbox');
        var subtotalDisplay = document.getElementById('subtotalDisplay');

        function updateSubtotal() {
            var newSubtotal = 0;
            itemCheckboxes.forEach(function (checkbox) {
                if (checkbox.checked) {
                    newSubtotal += parseFloat(checkbox.getAttribute('data-item-total'));
                }
            });
            if (wrapCheckbox && wrapCheckbox.checked) {
                newSubtotal += parseFloat(wrapCheckbox.value);
            }
            if(subtotalDisplay) {
                subtotalDisplay.innerText = 'Rp' + newSubtotal.toLocaleString('id-ID');
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

            inputElement.value = newQuantity.toString().padStart(2, '0');
            rowTotalElement.innerText = 'Rp' + newRowTotal.toLocaleString('id-ID');
            checkboxElement.setAttribute('data-item-total', newRowTotal);
            
            // Update subtotal jika checkbox item tersebut sedang dicentang
            updateSubtotal();
        }

        function saveQuantityToBackend(id, quantity, inputElement) {
            // Opsional: Tampilkan loading state kecil jika mau
            // inputElement.style.opacity = '0.5';

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
                // inputElement.style.opacity = '1';
                if (data.success) {
                    updateRow(id, data.newQuantity);
                } else {
                    alert(data.message);
                    updateRow(id, data.quantity || 1);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan koneksi.');
                // inputElement.style.opacity = '1';
                // Kembalikan ke nilai sebelumnya (agak tricky tanpa simpan state, 
                // tapi minimal jangan biarkan UI blank)
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
                if (newQuantity < 1) newQuantity = 1;
                if (newQuantity === currentQuantity) return;

                saveQuantityToBackend(id, newQuantity, inputElement);
            });
        });
    });
</script>
@endpush