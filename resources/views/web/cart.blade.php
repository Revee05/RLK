{{-- filepath: resources/views/web/cart.blade.php --}}
@extends('web.partials.layout')

@section('content')
<div class="container my-5">
    <h2 class="fw-bold mb-5">Keranjang</h2> 

    @php $grandTotal = 0; @endphp

    <form action="/checkout" method="POST">
        @csrf 

        <div class="row g-3 text-muted mb-2 pb-2">
            <div class="col-1"></div>
            <div class="col-md-4">Produk</div>
            <div class="col-md-2">Harga</div>
            <div class="col-md-3">Jumlah</div>
            <div class="col-md-2 text-end">Total</div>
        </div>
        <hr class="mt-0">
            @if($cartItems && !$cartItems->isEmpty())

            @foreach($cartItems as $item)
                @php 
                    // Variabel kosong untuk diisi
                    $data = null;
                    $nama_produk = 'Produk Tidak Tersedia';
                    $gambar_produk = 'https://via.placeholder.com/100';
                    $kategori = null;
                    $link_kategori = '#';

                    // 1. Cek apakah ini PRODUK LELANG (dari 'products')
                    if ($item->product) {
                        $data = $item->product;
                        $nama_produk = $data->title ?? 'Nama Produk Tidak Ditemukan';
                        $firstImage = $data->images->first(); // Pakai relasi 'images'
                        if ($firstImage) $gambar_produk = asset($firstImage->path);

                        $kategori = $data->kategori; // Ambil relasi 'kategori'
                        if ($kategori) $link_kategori = route('products.category', $kategori->slug);

                    // 2. Cek apakah ini PRODUK MERCH (dari 'merch_products')
                    } elseif ($item->merchProduct) {
                        $data = $item->merchProduct;
                        $nama_produk = $data->name ?? 'Nama Merch Tidak Ditemukan';
                        $firstImage = $data->images->first(); // Pakai relasi 'images' dari merch
                        if ($firstImage) $gambar_produk = asset($firstImage->image_path); // Sesuai controller Anda

                        $kategori = $data->categories->first(); // Ambil kategori pertama
                        if ($kategori) $link_kategori = '#'; // Ganti '#' dengan route kategori merch jika ada
                    }

                    // 3. Harga & Total selalu diambil dari 'cart_items'
                    $price = $item->price; 
                    $quantity = $item->quantity;
                    $total = $price * $quantity; 
                @endphp

                {{-- BARIS ITEM (HTML) --}}
                <div class="row g-3 align-items-center my-3">

                    {{-- Kolom Checkbox --}}
                    <div class="col-1 d-flex justify-content-center">
                        <input class="form-check-input item-checkbox" 
                               type="checkbox" 
                               name="cart_item_ids[]" 
                               value="{{ $item->id }}" 
                               {{-- ID UNTUK JS --}}
                               id="checkbox-{{ $item->id }}"
                               data-item-total="{{ $total }}"> 
                    </div>

                    {{-- Kolom Info Produk (Sekarang dinamis) --}}
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <img src="{{ $gambar_produk }}" alt="{{ $nama_produk }}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; margin-right: 15px;">
                            <div>
                                <h5 class="mb-1">{{ $nama_produk }}</h5>

                                {{-- Kategori (Dinamis) --}}
                                @if($kategori)
                                    <p class="text-muted small mb-1">
                                        <a class="text-decoration-none text-dark" href="{{ $link_kategori }}">
                                            {{ $kategori->name }}
                                        </a>
                                    </p>
                                @else
                                    <p class="text-muted small mb-1">
                                        @if($item->product) Barang Menang Lelang @else Merchandise @endif
                                    </p>
                                @endif

                                {{-- Form Hapus (Tidak berubah) --}}
                                <form action="{{ route('cart.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0 m-0 small" style="text-decoration: none; vertical-align: baseline;" onclick="return confirm('Yakin ingin menghapus item ini?')">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Kolom Harga --}}
                    <div class="col-md-2">
                        {{-- ID DAN DATA-ATTRIBUTE UNTUK JS --}}
                        <strong id="price-per-item-{{ $item->id }}" data-price="{{ $price }}">
                            Rp{{ number_format($price, 0, ',', '.') }}
                        </strong>
                    </div>

                    {{-- Kolom Jumlah --}}
                    <div class="col-md-3">
                        <div class="input-group" style="max-width: 130px;">
                            {{-- DATA-ATTRIBUTE UNTUK JS --}}
                            <button class="btn btn-outline-secondary btn-quantity" 
                                    type="button" 
                                    data-action="decrease"
                                    data-id="{{ $item->id }}">
                                -
                            </button>
                            {{-- ID UNTUK JS --}}
                            <input type="text" 
                                   class="form-control text-center quantity-input" 
                                   id="quantity-input-{{ $item->id }}"
                                   value="{{ str_pad($quantity, 2, '0', STR_PAD_LEFT) }}" 
                                   readonly>
                            {{-- DATA-ATTRIBUTE UNTUK JS --}}
                            <button class="btn btn-outline-secondary btn-quantity" 
                                    type="button" 
                                    data-action="increase"
                                    data-id="{{ $item->id }}">
                                +
                            </button>
                        </div>
                    </div>

                    {{-- Kolom Total Item --}}
                    <div class="col-md-2 text-end">
                        {{-- ID UNTUK JS --}}
                        <strong class="fs-5" id="row-total-{{ $item->id }}">
                            Rp{{ number_format($total, 0, ',', '.') }}
                        </strong>
                    </div>

                </div> {{-- Akhir dari .row --}}
            @endforeach
            @else
                <div class="row">
                    <div class="col-12 my-4">
                        <p>Keranjang belanja kosong.</p>
                    </div>
                </div>
            @endif

            {{-- Bagian Summary --}}
            <hr class="my-4">
            <div class="row">
                <div class="col-md-6 offset-md-6">
                    <div class="form-check mb-4">
                        <input class="form-check-input" 
                               type="checkbox" 
                               name="wrap_product"
                               value="10000" 
                               id="wrapProductCheckbox">
                        <label class="form-check-label ms-2" for="wrapProductCheckbox">
                            For <strong>10.000</strong> Please Wrap The Product
                        </label>
                    </div>
                    <hr class="my-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span>Subtotal</span>
                        <span id="subtotalDisplay">Rp0</span>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-lg py-3 w-100" style="background-color: #1a2b48; color: white; border-radius: 0.5rem;">
                            Checkout
                        </button>
                    </div>
                </div>
            </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Ambil CSRF token dari tag meta di <head>
    // Pastikan tag <meta name="csrf-token" content="{{ csrf_token() }}"> ada di layout.blade.php
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Jalankan skrip setelah halaman dimuat
    document.addEventListener('DOMContentLoaded', function () {

        // --- SKRIP 1: FUNGSI UNTUK SUBTOTAL CHECKBOX ---
        
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

            if (wrapCheckbox.checked) {
                newSubtotal += parseFloat(wrapCheckbox.value);
            }

            var formattedSubtotal = 'Rp' + newSubtotal.toLocaleString('id-ID');

            subtotalDisplay.innerText = formattedSubtotal;
        }

        itemCheckboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', updateSubtotal);
        });

        wrapCheckbox.addEventListener('change', updateSubtotal);
        
        // Panggil fungsi sekali saat halaman dimuat
        updateSubtotal();


        // --- SKRIP 2: FUNGSI UNTUK TOMBOL KUANTITAS (+/-) ---

        // Fungsi helper untuk update semua elemen di baris
        function updateRow(id, newQuantity) {
            var inputElement = document.getElementById('quantity-input-' + id);
            var priceElement = document.getElementById('price-per-item-' + id);
            var rowTotalElement = document.getElementById('row-total-' + id);
            var checkboxElement = document.getElementById('checkbox-' + id);

            var pricePerItem = parseFloat(priceElement.getAttribute('data-price'));
            var newRowTotal = pricePerItem * newQuantity;

            // 1. Update input kuantitas
            inputElement.value = newQuantity.toString().padStart(2, '0');

            // 2. Update total baris
            rowTotalElement.innerText = 'Rp' + newRowTotal.toLocaleString('id-ID');

            // 3. Update data-item-total di checkbox (SANGAT PENTING!)
            checkboxElement.setAttribute('data-item-total', newRowTotal);

            // 4. Hitung ulang Subtotal keseluruhan
            updateSubtotal();
        }

        // Fungsi untuk mengirim data ke backend
        function saveQuantityToBackend(id, quantity, inputElement) {
            inputElement.value = '...'; // Loading

            fetch('/cart/update/' + id, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken, // Kirim CSRF token
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    quantity: quantity
                })
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.success) {
                    // Backend sukses, update tampilan
                    updateRow(id, data.newQuantity);
                } else {
                    // Backend gagal (misal: stok habis)
                    alert(data.message); 
                    // Kembalikan ke angka yang disarankan backend (stok maks, atau 1)
                    updateRow(id, data.quantity || 1); 
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
                // Kembalikan ke angka semula jika gagal total
                updateRow(id, parseInt(inputElement.value) || 1);
            });
        }

        // Tambahkan event listener ke SEMUA tombol kuantitas
        document.querySelectorAll('.btn-quantity').forEach(function(button) {
            button.addEventListener('click', function() {
                
                var action = this.getAttribute('data-action');
                var id = this.getAttribute('data-id');
                var inputElement = document.getElementById('quantity-input-' + id);
                
                var currentQuantity = parseInt(inputElement.value);
                var newQuantity = currentQuantity;

                if (action === 'increase') {
                    newQuantity = currentQuantity + 1;
                } else if (action === 'decrease') {
                    newQuantity = currentQuantity - 1;
                }

                if (newQuantity < 1) {
                    newQuantity = 1;
                }

                if (newQuantity === currentQuantity) {
                    return; 
                }

                // Kirim kuantitas baru ke backend
                saveQuantityToBackend(id, newQuantity, inputElement);
            });
        });

    });
</script>
@endpush