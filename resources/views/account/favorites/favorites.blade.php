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

                                    // Ambil varian default, jika tidak ada pakai varian pertama
                                    $variant = $product->variants()
                                        ->where('is_default', 1)
                                        ->first() ?? $product->variants()->first();

                                    // Ambil gambar varian pertama jika ada
                                    $imagePath = ($variant && $variant->images()->first())
                                        ? $variant->images()->first()->image_path
                                        : null;
                                @endphp

                                <div class="col-md-4 mb-3">
                                    <div class="card">

                                        {{-- Gambar produk --}}
                                        @if ($imagePath)
                                            <img src="{{ asset('storage/' . $imagePath) }}"
                                                 class="card-img-top"
                                                 alt="{{ $product->name }}" />
                                        @else
                                            <img src="{{ asset('images/no-image.png') }}"
                                                 class="card-img-top"
                                                 alt="{{ $product->name }}" />
                                        @endif

                                        <div class="card-body">
                                            <h5 class="card-title">{{ $product->name }}</h5>

                                            <p>Rp {{ number_format($product->price, 0, ',', '.') }}</p>

                                            <a href="{{ url('/product/' . $product->id) }}"
                                               class="btn btn-primary btn-sm">
                                                Detail
                                            </a>
                                        </div>

                                    </div>
                                </div>

                            @empty
                                <p class="text-center">Belum ada produk favorit.</p>
                            @endforelse

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection
