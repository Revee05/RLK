@extends('account.partials.layout')

@section('content')
    <div class="container" style="max-width:1200px; margin-top:40px; margin-bottom:80px;">
        <div class="row">

            @include('account.partials.nav_new')

            <div class="col-sm-9">
                <div class="card content-border">
                    <div class="card-head border-bottom border-darkblue ps-4 d-flex align-items-center justify-content-between">
                        <h3 class="mb-0 fw-bolder">Favorit</h3>
                    </div>

                    <div class="card-body">

                        <div class="row">

                            @forelse($favorites as $fav)

                                @php
                                    $product = $fav->product;
                                    $variant = $product->variants->where('is_default', 1)->first() ?? $product->variants->first();

                                    $imagePath = null;
                                    if ($variant && $variant->images && $variant->images->first()) {
                                        $imagePath = $variant->images->first()->image_path;
                                    }
                                @endphp

                                <div class="col-md-4 mb-4">
                                    <div class="card shadow-sm border-0" style="border-radius:10px;">

                                        {{-- WRAPPER GAMBAR + ICON FAVORIT --}}
                                        <div class="position-relative">
                                            
                                            {{-- ICON UNLIKE --}}
                                            <form action="{{ route('account.favorites.remove', $fav->id) }}"
                                                  method="POST"
                                                  class="position-absolute"
                                                  style="bottom:10px; right:10px; z-index:10;">
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
                                                Stok: {{ $product->stock }}
                                            </p>

                                            <p style="font-size:20px; font-weight:700; line-height:150%; margin:4px 0 0 0;">
                                                Rp {{ number_format($product->price, 0, ',', '.') }}
                                            </p>

                                        </div>

                                    </div>
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
