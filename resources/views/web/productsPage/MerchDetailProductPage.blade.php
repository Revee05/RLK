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
                <img src="{{ $product->images->first() ? asset($product->images->first()->image_path) : 'https://placehold.co/500x400?text=No+Image' }}" alt="{{ $product->name }}" class="img-fluid rounded">
            </div>
            <div class="thumb-images d-flex gap-2">
                @foreach($product->images->skip(1) as $img)
                    <img src="{{ asset($img->image_path) }}" class="img-thumbnail" alt="thumb">
                @endforeach
            </div>
        </div>
        <!-- Detail produk -->
        <div class="col-lg-6">
            <h2 class="product-title mb-2">{{ $product->name }}</h2>
            <div class="product-category mb-2">
                {{ $product->categories->pluck('name')->join(', ') }}
            </div>
            <div class="product-price mb-3">
                @if($product->discount)
                    Rp. {{ number_format($product->price * (1 - $product->discount/100), 0, ',', '.') }}
                    <span class="text-muted text-decoration-line-through ms-2">Rp. {{ number_format($product->price, 0, ',', '.') }}</span>
                @else
                    Rp. {{ number_format($product->price, 0, ',', '.') }}
                @endif
            </div>
            <p class="product-desc mb-3">
                {!! $product->description !!}
            </p>
            <div class="mb-3">
                <strong>Stok:</strong> {{ $product->stock }}
            </div>
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
        @foreach($relatedProducts as $related)
        <div class="col-6 col-md-3">
            <a href="{{ route('merch.products.detail', $related->slug) }}" style="text-decoration:none; color:inherit;">
                <div class="card related-product-card h-100">
                    <img src="{{ $related->images->first() ? asset($related->images->first()->image_path) : 'https://placehold.co/300x140?text=No+Image' }}" class="card-img-top" alt="{{ $related->name }}">
                    <div class="card-body text-center">
                        <div class="related-product-title mb-1 fw-semibold">{{ $related->name }}</div>
                        @if($related->discount)
                            <div class="related-product-price text-danger fw-bold">
                                Rp. {{ number_format($related->price * (1 - $related->discount/100), 0, ',', '.') }}
                                <span class="text-muted text-decoration-line-through ms-2 small">Rp. {{ number_format($related->price, 0, ',', '.') }}</span>
                            </div>
                            <div class="badge bg-danger mt-2">-{{ $related->discount }}%</div>
                        @else
                            <div class="related-product-price fw-bold">
                                Rp. {{ number_format($related->price, 0, ',', '.') }}
                            </div>
                        @endif
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection

@section('js')
    <!-- Tambahkan JS khusus halaman ini jika diperlukan -->
@endsection