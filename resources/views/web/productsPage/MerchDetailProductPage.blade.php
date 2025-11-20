@extends('web.partials.layout')
@section('sampleproduct','aktiv')

@section('css')
    <link href="{{ asset('css/MerchDetailProducts.css') }}" rel="stylesheet">
@endsection

@section('content')
@php
    $mainVariant = $product->variants->where('is_default', 1)->first() ?? $product->variants->first();
    $mainImage = $mainVariant && $mainVariant->images->count()
        ? asset($mainVariant->images->first()->image_path)
        : 'https://placehold.co/500x400?text=No+Image';
@endphp

<div class="merch-product-detail container py-5">
    <div class="row">
        <!-- Gambar utama & thumbnail -->
        <div class="col-lg-6">
            <div class="main-image mb-3">
                <img src="{{ $mainImage }}" alt="{{ $product->name }}" class="img-fluid rounded">
            </div>
            <div class="thumb-images d-flex gap-2">
                @php
                    $allImages = collect();
                    foreach ($product->variants as $variant) {
                        if ($variant->images && $variant->images->count()) {
                            $allImages = $allImages->merge($variant->images);
                        }
                    }
                @endphp
                @if($allImages->count())
                    @foreach($allImages as $img)
                        <img src="{{ asset($img->image_path) }}" class="img-thumbnail" alt="thumb">
                    @endforeach
                @endif
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
            
            <form action="{{ route('cart.addMerch', $product->id) }}" method="POST">
                @csrf
                <input type="number" name="quantity" value="1" min="1">
                <button class="btn btn-primary btn-lg w-100 mb-3">Tambahkan ke keranjang</button>
            </form>
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
        <div class="col-12 col-md-4">
            <a href="{{ route('merch.products.detail', $related->slug) }}" style="text-decoration:none; color:inherit;">
                <div class="card related-product-card h-100">
                    @php
                        // Gabungkan semua gambar dari semua varian
                        $relAllImages = collect();
                        foreach ($related->variants as $variant) {
                            if ($variant->images && $variant->images->count()) {
                                $relAllImages = $relAllImages->merge($variant->images);
                            }
                        }
                        $relMainImage = $relAllImages->count()
                            ? asset($relAllImages->first()->image_path)
                            : 'https://placehold.co/300x140?text=No+Image';
                    @endphp
                    <img src="{{ $relMainImage }}" class="card-img-top" alt="{{ $related->name }}">
                    <div class="d-flex gap-1 justify-content-center mt-2 mb-1">
                        @foreach($relAllImages as $img)
                            <img src="{{ asset($img->image_path) }}" style="width:40px;height:40px;object-fit:cover;border-radius:6px;" alt="thumb">
                        @endforeach
                    </div>
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

<div class="mb-3">
    <strong>Varian Produk:</strong>
    @if($product->variants && $product->variants->count())
        <div class="accordion" id="variantAccordion">
            @foreach($product->variants as $variant)
                <div class="accordion-item mb-2">
                    <h2 class="accordion-header" id="heading-{{ $variant->id }}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $variant->id }}" aria-expanded="false" aria-controls="collapse-{{ $variant->id }}">
                            {{ $variant->name }} @if($variant->is_default) <span class="badge bg-primary ms-2">Default</span> @endif
                        </button>
                    </h2>
                    <div id="collapse-{{ $variant->id }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $variant->id }}" data-bs-parent="#variantAccordion">
                        <div class="accordion-body">
                            {{-- Gambar varian --}}
                            @if($variant->images && $variant->images->count())
                                <div class="d-flex gap-2 mb-2">
                                    @foreach($variant->images as $vimg)
                                        <img src="{{ asset($vimg->image_path) }}" alt="varian-img" style="width:60px; height:60px; object-fit:cover; border-radius:6px;">
                                    @endforeach
                                </div>
                            @endif
                            {{-- Size dan harga --}}
                            @if($variant->sizes && $variant->sizes->count())
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Ukuran</th>
                                            <th>Stok</th>
                                            <th>Harga</th>
                                            <th>Diskon</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($variant->sizes as $size)
                                            <tr>
                                                <td>{{ $size->size }}</td>
                                                <td>{{ $size->stock }}</td>
                                                <td>
                                                    Rp. {{ number_format($size->price, 0, ',', '.') }}
                                                </td>
                                                <td>
                                                    @if($size->discount)
                                                        <span class="badge bg-danger">-{{ $size->discount }}%</span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="text-muted">Tidak ada data ukuran.</div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-muted">Tidak ada varian.</div>
    @endif
</div>
@endsection

@section('js')
    <!-- Tambahkan JS khusus halaman ini jika diperlukan -->
@endsection