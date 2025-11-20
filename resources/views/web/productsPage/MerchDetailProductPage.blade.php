@extends('web.partials.layout')
@section('sampleproduct','aktiv')

@section('css')
    <link href="{{ asset('css/MerchDetailProducts.css') }}" rel="stylesheet">
@endsection

@section('content')

@php
    $mainVariant = $product->variants->where('is_default', 1)->first() ?? $product->variants->first();
    $mainImage = ($mainVariant && $mainVariant->images->count())
        ? asset($mainVariant->images->first()->image_path)
        : 'https://placehold.co/500x400?text=No+Image';

    // Gabungkan semua gambar dari semua variant
    $allImages = collect();
    foreach ($product->variants as $variant) {
        if ($variant->images && $variant->images->count()) {
            $allImages = $allImages->merge($variant->images);
        }
    }

    $variantsArray = $product->variants->map(function($v) {
        return [
            'id' => $v->id,
            'sizes' => $v->sizes->map(function($s) {
                return [
                    'id' => $s->id,
                    'size' => $s->size,
                    'stock' => $s->stock,
                ];
            })->toArray(),
        ];
    })->values()->toArray();

    // Hitung total stock semua variant (termasuk size jika ada)
    $totalStock = 0;
    foreach ($product->variants as $variant) {
        if ($variant->sizes && $variant->sizes->count()) {
            $totalStock += $variant->sizes->sum('stock');
        } else {
            $totalStock += $variant->display_stock ?? 0;
        }
    }
@endphp

<div class="merch-product-detail container py-5">
    <div class="row">
        <div class="col-lg-6">
            <div class="main-image mb-3">
                <img src="{{ $mainImage }}" alt="{{ $product->name }}" class="img-fluid rounded">
            </div>
            <div class="thumb-images d-flex gap-2">
                @foreach($allImages as $img)
                    <img src="{{ asset($img->image_path) }}" class="img-thumbnail" alt="thumb" style="width:60px; height:60px; object-fit:cover; cursor:pointer;">
                @endforeach
            </div>
        </div>
        <div class="col-lg-6">
            <h2 class="product-title mb-2">{{ $product->name }}</h2>
            <div class="product-category mb-2">
                {{ $product->categories->pluck('name')->join(', ') }}
            </div>
            <div class="product-price mb-3">
                @if($mainVariant && $mainVariant->display_discount)
                    Rp. {{ number_format($mainVariant->display_price * (1 - $mainVariant->display_discount/100), 0, ',', '.') }}
                    <span class="text-muted text-decoration-line-through ms-2">Rp. {{ number_format($mainVariant->display_price, 0, ',', '.') }}</span>
                @elseif($mainVariant)
                    Rp. {{ number_format($mainVariant->display_price, 0, ',', '.') }}
                @else
                    <span class="text-muted">-</span>
                @endif
            </div>
            <div class="mb-3">
                <strong>Total Stok:</strong> {{ $totalStock }}
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Variant</label>
                <div class="variant-grid">
                    @foreach($product->variants as $variant)
                        <label class="variant-btn">
                            @php
                                $img = $variant->images->first();
                            @endphp
                            @if($img)
                                <img src="{{ asset($img->image_path) }}" alt="{{ $variant->name }}">
                            @else
                                <img src="https://placehold.co/40x40?text=?" alt="no-img">
                            @endif
                            <input type="radio" name="variant_id" value="{{ $variant->id }}" class="d-none" autocomplete="off" {{ $variant->id == $mainVariant->id ? 'checked' : '' }}>
                            <span>{{ $variant->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Sizes</label>
                <div class="size-grid">
                    @if($mainVariant && $mainVariant->sizes->count())
                        @foreach($mainVariant->sizes as $size)
                            <label class="size-btn">
                                <input type="radio" name="size_id" value="{{ $size->id }}" class="d-none" autocomplete="off">
                                <span>{{ $size->size }} 
                                    @if($size->stock > 0)
                                        <small class="text-muted">({{ $size->stock }} stok)</small>
                                    @else
                                        <small class="text-danger">(Habis)</small>
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    @else
                        <span class="text-muted">Tidak ada ukuran.</span>
                    @endif
                </div>
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

{{-- Deskripsi produk di luar box utama, di atas related products --}}
<div class="container pb-3">
    <h4 class="mb-2">Deskripsi Produk</h4>
    <div class="product-desc mb-4">
        {!! $product->description !!}
    </div>
</div>

<div class="related-products-section container pb-5">
    <h4 class="mb-4">Related products</h4>
    <!-- Related products code here -->
</div>

@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ambil data variant dan size dari blade ke JS
    const variants = @json($variantsArray);

    // Saat variant dipilih, update size-grid
    document.querySelectorAll('.variant-btn input[type="radio"]').forEach((input, idx) => {
        input.addEventListener('change', function() {
            document.querySelectorAll('.variant-btn').forEach(l => l.classList.remove('active'));
            input.closest('.variant-btn').classList.add('active');

            const variant = variants[idx];
            const sizeGrid = document.querySelector('.size-grid');
            if (variant.sizes.length > 0) {
                sizeGrid.innerHTML = '';
                variant.sizes.forEach(sz => {
                    sizeGrid.innerHTML += `
                        <label class="size-btn">
                            <input type="radio" name="size_id" value="${sz.id}" class="d-none" autocomplete="off">
                            <span>${sz.size} 
                                ${sz.stock > 0 ? `<small class="text-muted">(${sz.stock} stok)</small>` : `<small class="text-danger">(Habis)</small>`}
                            </span>
                        </label>
                    `;
                });
            } else {
                sizeGrid.innerHTML = '<span class="text-muted">Tidak ada ukuran.</span>';
            }

            // Setelah update size, set active pada size pertama (jika ada)
            const sizeLabels = document.querySelectorAll('.size-grid .size-btn');
            if (sizeLabels.length > 0) {
                sizeLabels[0].classList.add('active');
                sizeLabels[0].querySelector('input').checked = true;
            }
        });
    });

    // Thumbnail tetap
    document.querySelectorAll('.thumb-images img').forEach(function(img) {
        img.addEventListener('click', function() {
            document.querySelector('.main-image img').src = img.src;
        });
    });
});

// Highlight active size
document.addEventListener('change', function(e) {
    if (e.target.name === 'size_id') {
        document.querySelectorAll('.size-btn').forEach(l => l.classList.remove('active'));
        e.target.closest('.size-btn').classList.add('active');
    }
});

// Inisialisasi: set active pada variant dan size yang terpilih saat load
document.addEventListener('DOMContentLoaded', function() {
    const checkedVariant = document.querySelector('.variant-btn input[type="radio"]:checked');
    if (checkedVariant) checkedVariant.closest('.variant-btn').classList.add('active');
    const checkedSize = document.querySelector('.size-btn input[type="radio"]:checked');
    if (checkedSize) checkedSize.closest('.size-btn').classList.add('active');
});
</script>
@endsection
