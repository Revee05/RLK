@extends('account.partials.layout')

@section('content')
    <div class="container" style="max-width:1200px; margin-top:40px; margin-bottom:80px;">
    <div class="row g-4" style="display:flex; align-items:stretch; height:100%;">
<!-- gunakan bootstrap align-items-stretch -->

        @include('account.partials.nav_new')

        <div class="col-sm-9 account-right d-flex flex-column "style="height:100%;">
            <div class="card content-border h-100 d-flex flex-column"><!-- card mengisi tinggi kolom -->
                <div class="card-head border-bottom border-darkblue ps-4 d-flex align-items-center justify-content-between">
                    <h3 class="mb-0 fw-bolder">Favorit</h3>
                </div>

                <!-- card-body jadi area scrollable -->
                <div class="card-body overflow-auto mt-3 mb-3" style="max-height: calc(100vh - 240px); overflow-y:auto;">
                  <div class="row">
                            @forelse($favorites as $fav)
@php
    $product = $fav->product;
    $variant = $product->variants->where('is_default', 1)->first() ?? $product->variants->first();

    $imagePath = null;
    if ($variant && $variant->images && $variant->images->first()) {
        $imagePath = $variant->images->first()->image_path;
    }

    // gunakan harga & stok dari variant jika tersedia, fallback ke product
    $displayStock = $variant ? ($variant->stock ?? $product->stock ?? 0) : ($product->stock ?? 0);
    $displayPrice = $variant ? ($variant->price ?? $product->price ?? 0) : ($product->price ?? 0);
    
    // route ke detail/checkout produk
    $checkoutUrl = route('merch.products.detail', $product->slug);
@endphp

<div class="col-md-4 mb-4">
    <a href="{{ $checkoutUrl }}" style="text-decoration:none; color:inherit;">
        <div class="card shadow-sm border-0" style="border-radius:10px;">
            {{-- WRAPPER GAMBAR + ICON FAVORIT --}}
            <div class="position-relative">
                {{-- ICON UNLIKE --}}
                <form action="{{ route('account.favorites.remove', $fav->id) }}"
                      method="POST"
                      class="position-absolute"
                      style="bottom:10px; right:10px; z-index:10;"
                      onclick="event.stopPropagation();">
                    @csrf
                    @method('DELETE')

                    <button type="submit" style="background:none; border:none; cursor:pointer; padding:0;">
                        <img src="{{ asset('icons/heart_fill.svg') }}"
                             alt="unlike favorite"
                             style="width:20.9px; height:18.2px;">
                    </button>
                </form>

                {{-- GAMBAR --}}
                <img src="{{ asset($imagePath ?? 'images/no-image.png') }}"
                     class="card-img-top"
                     style="height:230px; object-fit:cover; border-radius:8px;"
                     alt="{{ $product->name }}">
            </div>

            {{-- PRODUCT TEXT --}}
            <div class="card-body" style="font-family: Helvetica, sans-serif;">

                <h5 style="font-size:18px; font-weight:400; line-height:150%; margin:0 0 4px 0;">
                    {{ $product->name }}
                </h5>

                <p style="font-size:14px; font-weight:400; line-height:150%; margin:0;">
                    Stok: {{ $displayStock }}
                </p>

                <p style="font-size:20px; font-weight:700; line-height:150%; margin:4px 0 0 0;">
                    Rp {{ number_format($displayPrice, 0, ',', '.') }}
                </p>

            </div>
        </div>
    </a>
</div>

                            @empty
                                <p class="text-center">Anda belum memiliki produk favorit.</p>
                            @endforelse

                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection