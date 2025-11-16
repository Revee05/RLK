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
                    $data = $item->product; 
                    $price = $item->price; 
                    $quantity = $item->quantity;
                    $total = $price * $quantity; 
                    
                    $nama_produk = $data->title ?? 'Nama Produk Tidak Ditemukan';
                    $firstImage = $data->images->first(); 
                    $gambar_produk = $firstImage ? asset($firstImage->path) : 'https://via.placeholder.com/100';
                @endphp

                {{-- Baris ini HARUS cocok dengan header --}}
                <div class="row g-3 align-items-center my-3">
                    
                    {{-- 1 kolom --}}
                    <div class="col-1 d-flex justify-content-center">
                        <input class="form-check-input item-checkbox" 
                               type="checkbox" 
                               name="cart_item_ids[]" 
                               value="{{ $item->id }}" 
                               data-item-total="{{ $total }}"> 
                    </div>

                    {{-- 4 kolom --}}
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <img src="{{ $gambar_produk }}" 
                                 alt="{{ $nama_produk }}" 
                                 style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; margin-right: 15px;">
                            <div>
                                <h5 class="mb-1">{{ $nama_produk }}</h5>
                                
                                {{-- PERUBAHAN 2: Kategori dibungkus <p> agar 'Hapus' di bawahnya --}}
                                @if($data && $data->kategori)
                                    <p class="text-muted small mb-1">
                                        <a class="text-decoration-none text-dark" 
                                           href="{{ route('products.category', $data->kategori->slug) }}">
                                            {{ $data->kategori->name }}
                                        </a>
                                    </p>
                                @else
                                    <p class="text-muted small mb-1">Barang Menang Lelang</p>
                                @endif
                                
                                {{-- Form Hapus (posisinya sekarang sudah benar di bawah kategori) --}}
                                <form action="{{ route('cart.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-link text-danger p-0 m-0 small" 
                                            style="text-decoration: none; vertical-align: baseline;"
                                            onclick="return confirm('Yakin ingin menghapus item ini dari keranjang?')">
                                        Hapus
                                    </button>
                                </form>

                            </div>
                        </div>
                    </div>
                    
                    {{-- 2 kolom --}}
                    <div class="col-md-2">
                        <strong>Rp{{ number_format($price, 0, ',', '.') }}</strong>
                    </div>

                    {{-- 3 kolom --}}
                    <div class="col-md-3">
                        <div class="input-group" style="max-width: 130px;">
                            <button class="btn btn-outline-secondary" type="button">-</button>
                            <input type="text" class="form-control text-center" value="{{ str_pad($quantity, 2, '0', STR_PAD_LEFT) }}" readonly>
                            <button class="btn btn-outline-secondary" type="button">+</button>
                        </div>
                    </div>

                    {{-- 2 kolom --}}
                    <div class="col-md-2 text-end">
                        <strong class="fs-5">Rp{{ number_format($total, 0, ',', '.') }}</strong>
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
{{-- JavaScript Anda sudah benar dan tidak perlu diubah --}}
<script>
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
    });
</script>
@endpush