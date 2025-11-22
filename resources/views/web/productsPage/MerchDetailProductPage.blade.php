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

// Gabungan semua gambar dari semua variant
$allImages = collect();
foreach ($product->variants as $variant) {
    if ($variant->images && $variant->images->count()) {
        $allImages = $allImages->merge($variant->images);
    }
}

$variantsArray = $product->variants->map(function($v) {
    return [
        'id' => $v->id,
        'display_stock' => $v->display_stock,
        'sizes' => $v->sizes->map(function($s) {
            return [
                'id' => $s->id,
                'size' => $s->size,
                'stock' => $s->stock,
            ];
        })->toArray(),
        'image' => $v->images->first() ? asset($v->images->first()->image_path) : 'https://placehold.co/500x400?text=No+Image',
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
<div class="main-container">

    <div class="merch-product-detail">
        <div class="row">
            <div class="col-lg-6">
                <div class="main-image">
                    <img src="{{ $mainImage }}" alt="{{ $product->name }}" class="img-fluid rounded" id="main-product-image">
                </div>
                <div class="thumb-images">
                    @foreach($allImages as $img)
                    <img src="{{ asset($img->image_path) }}" alt="thumb">
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
                    <span class="badge bg-danger me-2">-{{ $mainVariant->display_discount }}%</span>
                    Rp. {{ number_format($mainVariant->display_price, 0, ',', '.') }}
                    <span class="text-muted text-decoration-line-through ms-2 original-strike">
                        Rp. {{ number_format($mainVariant->display_price, 0, ',', '.') }}</span>
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
                        <label class="variant-btn"
                            data-image="{{ $variant->images->first() ? asset($variant->images->first()->image_path) : 'https://placehold.co/500x400?text=No+Image' }}">
                            @php
                            $img = $variant->images->first();
                            @endphp
                            @if($img)
                            <img src="{{ asset($img->image_path) }}" alt="{{ $variant->name }}">
                            @else
                            <img src="https://placehold.co/40x40?text=?" alt="no-img">
                            @endif
                            <input type="radio" name="variant_id" value="{{ $variant->id }}" class="d-none"
                                autocomplete="off" {{ $variant->id == $mainVariant->id ? 'checked' : '' }}>
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
                            <span>{{ $size->size }}</span>
                        </label>
                        @endforeach
                        @else
                        <span class="text-muted">Tidak ada ukuran.</span>
                        @endif
                    </div>
                </div>
                <form action="{{ route('cart.addMerch', $product->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="selected_variant_id" id="selected_variant_id" value="{{ $mainVariant->id }}">
                    <input type="hidden" name="selected_size_id" id="selected_size_id"
                        value="{{ $mainVariant->sizes->first()->id ?? '' }}">
                    <div class="d-flex align-items-center mb-3">
                        <input type="number" id="qty-input" name="quantity" value="1" min="1" class="qty-input">
                        <span id="stock-info" class="text-muted">
                            Tersedia
                            {{ $mainVariant->sizes->count() ? ($mainVariant->sizes->first()->stock ?? 0) : ($mainVariant->display_stock ?? 0) }}
                        </span>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">Tambahkan ke keranjang</button>
                </form>
                <div class="product-shipping-info">
                    <strong>Pengiriman:</strong> Pengiriman dilakukan setiap hari kerja.
                </div>
            </div>
        </div>
    </div>

    {{-- Deskripsi produk--}}
    <div class="container pb-3">
        <h4 class="mb-2">Deskripsi Produk</h4>
        <div class="product-desc mb-4">
            {!! $product->description !!}
        </div>
    </div>

    {{-- related products --}}
    <div class="related-products-section container pb-5">
        <h4 class="mb-4">Related products</h4>
        <div class="row g-3">
            @forelse($relatedProducts as $rel)
                @php
                    $variant = $rel->variants->where('is_default', 1)->first() ?: $rel->variants->first();
                    $img = $variant && $variant->images->count() ? asset($variant->images->first()->image_path) : 'https://placehold.co/300x250?text=No+Image';
                @endphp
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card h-100 shadow-sm border-0">
                        <a href="{{ route('merch.products.detail', $rel->slug) }}" class="text-decoration-none text-dark">
                            <img src="{{ $img }}" class="card-img-top related-img" alt="{{ $rel->name }}">
                            <div class="card-body p-2">
                                <div class="fw-bold mb-1 related-title">{{ $rel->name }}</div>
                                @if($rel->display_discount)
                                    <span class="badge bg-danger mb-1">-{{ $rel->display_discount }}%</span>
                                @endif
                                <div class="related-price">
                                    Rp. {{ number_format($rel->display_price, 0, ',', '.') }}
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-12 text-muted">Tidak ada produk terkait.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const variants = @json($variantsArray);
    console.log('VARIANTS ARRAY:', variants);

    // Jika ingin lihat data produk utama juga:
    console.log('PRODUCT DATA:', @json($product));

    // Highlight active variant
    document.querySelectorAll('.variant-btn input[type="radio"]').forEach((input, idx) => {
        input.addEventListener('change', function() {
            document.querySelectorAll('.variant-btn').forEach(l => l.classList.remove('active'));
            input.closest('.variant-btn').classList.add('active');

            const variant = variants[idx];
            const sizeGrid = document.querySelector('.size-grid');
            if (variant.sizes.length > 0) {
                sizeGrid.innerHTML = '';
                variant.sizes.forEach((sz, sidx) => {
                    sizeGrid.innerHTML += `
                        <label class="size-btn">
                            <input type="radio" name="size_id" value="${sz.id}" class="d-none" autocomplete="off" ${sidx === 0 ? 'checked' : ''}>
                            <span>${sz.size}</span>
                        </label>
                    `;
                });
            } else {
                sizeGrid.innerHTML = '<span class="text-muted">Tidak ada ukuran.</span>';
            }

            // Set active pada size pertama (jika ada)
            const sizeLabels = document.querySelectorAll('.size-grid .size-btn');
            if (sizeLabels.length > 0) {
                sizeLabels[0].classList.add('active');
                sizeLabels[0].querySelector('input').checked = true;
            }

            updateStockInfo();
        });

        // --- Hover effect: change main image and stock on hover ---
        input.closest('.variant-btn').addEventListener('mouseenter', function() {
            // Ganti gambar utama
            const imgUrl = this.getAttribute('data-image');
            document.getElementById('main-product-image').src = imgUrl;

            // Update stock info sesuai variant yang dihover
            const variant = variants[idx];
            let stock = 0;
            if (variant.sizes.length > 0) {
                stock = variant.sizes[0]?.stock ?? 0;
            } else {
                stock = variant.display_stock ?? 0;
            }
            const stockInfo = document.getElementById('stock-info');
            if (stock < 1) {
                stockInfo.innerHTML = `<span class="text-danger">Habis</span>`;
            } else {
                stockInfo.textContent = `Tersedia ${stock}`;
            }
        });

        input.closest('.variant-btn').addEventListener('mouseleave', function() {
            // Kembalikan gambar & stok ke variant terpilih saat mouse keluar
            const checkedVariant = document.querySelector('.variant-btn input[type="radio"]:checked');
            if (checkedVariant) {
                const checkedLabel = checkedVariant.closest('.variant-btn');
                const imgUrl = checkedLabel.getAttribute('data-image');
                document.getElementById('main-product-image').src = imgUrl;
            }
            updateStockInfo();
        });
        // --- End hover effect ---
    });

    // Highlight active size
    document.addEventListener('change', function(e) {
        if (e.target.name === 'size_id') {
            document.querySelectorAll('.size-btn').forEach(l => l.classList.remove('active'));
            e.target.closest('.size-btn').classList.add('active');
            updateStockInfo();
        }
    });

    // Thumbnail click
    document.querySelectorAll('.thumb-images img').forEach(function(img) {
        img.addEventListener('click', function() {
            document.getElementById('main-product-image').src = img.src;
        });
    });

    // Inisialisasi: set active pada variant dan size yang terpilih saat load
    const checkedVariant = document.querySelector('.variant-btn input[type="radio"]:checked');
    if (checkedVariant) checkedVariant.closest('.variant-btn').classList.add('active');
    const checkedSize = document.querySelector('.size-btn input[type="radio"]:checked');
    if (checkedSize) checkedSize.closest('.size-btn').classList.add('active');

    // Update stok info sesuai pilihan
    window.updateStockInfo = function() {
        const checkedVariant = document.querySelector('.variant-btn input[type="radio"]:checked');
        const checkedSize = document.querySelector('.size-btn input[type="radio"]:checked');
        let stock = 0;

        if (checkedVariant) {
            const variantIdx = Array.from(document.querySelectorAll('.variant-btn input[type="radio"]'))
                .indexOf(checkedVariant);
            const variant = variants[variantIdx];

            if (checkedSize && variant.sizes.length > 0) {
                const sizeIdx = Array.from(document.querySelectorAll('.size-btn input[type="radio"]')).indexOf(
                    checkedSize);
                stock = variant.sizes[sizeIdx]?.stock ?? 0;
            } else {
                stock = variant.display_stock ?? 0;
            }
        }

        const stockInfo = document.getElementById('stock-info');
        if (stock < 1) {
            stockInfo.innerHTML = `<span class="text-danger">Habis</span>`;
        } else {
            stockInfo.textContent = `Tersedia ${stock}`;
        }
        const qtyInput = document.getElementById('qty-input');
        const submitBtn = document.querySelector('button[type="submit"]');
        if (qtyInput) qtyInput.disabled = stock < 1;
        if (qtyInput) qtyInput.max = stock;
        if (submitBtn) submitBtn.disabled = stock < 1;
    }

    function updateHiddenInputs() {
        // Set variant
        const checkedVariant = document.querySelector('.variant-btn input[type="radio"]:checked');
        if (checkedVariant) {
            document.getElementById('selected_variant_id').value = checkedVariant.value;
        }
        // Set size
        const checkedSize = document.querySelector('.size-btn input[type="radio"]:checked');
        document.getElementById('selected_size_id').value = checkedSize ? checkedSize.value : '';
    }

    // Panggil setelah setiap update pilihan
    document.addEventListener('change', function(e) {
        if (e.target.name === 'size_id' || e.target.name === 'variant_id') {
            updateStockInfo();
            updateHiddenInputs();
        }
    });
    updateStockInfo();
    updateHiddenInputs();
});
</script>
@endsection
