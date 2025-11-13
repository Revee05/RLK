@extends('web.partials.layout')
@section('sampleproduct','aktiv')

@section('css')
    <link href="{{ asset('css/MerchDetailProduct.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="merch-product-detail container py-5">
    <div class="row">
        <!-- Gambar utama & thumbnail -->
        <div class="col-lg-6">
            <div class="main-image mb-3">
                <img src="https://placehold.co/500x400?text=Main+Image" alt="Cyborg 6 T-shirt" class="img-fluid rounded">
            </div>
            <div class="thumb-images d-flex gap-2">
                <img src="https://placehold.co/70x70?text=1" class="img-thumbnail" alt="thumb1">
                <img src="https://placehold.co/70x70?text=2" class="img-thumbnail" alt="thumb2">
                <img src="https://placehold.co/70x70?text=3" class="img-thumbnail" alt="thumb3">
                <img src="https://placehold.co/70x70?text=4" class="img-thumbnail" alt="thumb4">
            </div>
        </div>
        <!-- Detail produk -->
        <div class="col-lg-6">
            <h2 class="product-title mb-2">Cyborg 6 from Dippolar</h2>
            <div class="product-category mb-2">Merchandise Kaos</div>
            <div class="product-price mb-3">Rp. 85.000</div>
            <p class="product-desc mb-3">
                Kaos dengan desain ilustrasi bertema cyborg kaiju ini menghadirkan nuansa keunikan dan gaya streetwear. 
                Menggunakan bahan katun combed 30s, nyaman dipakai sehari-hari, cocok untuk koleksi dan dipadukan dengan berbagai outfit.
            </p>
            <ul class="product-detail-list mb-3">
                <li>Bahan: 100% Cotton Combed 30s</li>
                <li>Ukuran: M-L-XL</li>
                <li>Sablon: Plastisol (digital print high detail)</li>
                <li>Unisex</li>
                <li>Limited edition</li>
            </ul>
            <button class="btn btn-primary btn-lg w-100 mb-3">Tambahkan ke keranjang</button>
            <div class="product-shipping-info">
                <strong>Pengiriman:</strong> Pengiriman dilakukan setiap hari kerja.
            </div>
        </div>
    </div>
</div>

<div class="related-products-section container pb-5">
    <h4 class="mb-4">Related products</h4>
    <div class="row g-4">
        @for($i=1; $i<=8; $i++)
        <div class="col-6 col-md-3">
            <div class="card related-product-card h-100">
                <img src="https://placehold.co/300x140?text=Related+{{ $i }}" class="card-img-top" alt="Related Product {{ $i }}">
                <div class="card-body">
                    <div class="related-product-title">Product</div>
                    <div class="related-product-desc">Description of {{ $i == 1 ? 'first' : ($i == 2 ? 'second' : ($i == 3 ? 'third' : ($i == 4 ? 'fourth' : ($i == 5 ? 'fifth' : ($i == 6 ? 'sixth' : ($i == 7 ? 'seventh' : 'eighth')))))) }} product</div>
                    <div class="related-product-price">$19.99</div>
                </div>
            </div>
        </div>
        @endfor
    </div>
</div>
@endsection

@section('js')
    <!-- Tambahkan JS khusus halaman ini jika diperlukan -->
@endsection