@extends('web.partials.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/search.css') }}">
@endsection

@section('content')
<section class="py-5">
    <div class="container">
        
        {{-- Hitung Total Hasil --}}
        @php
            $totalResults = $lelang->count() + $merchandise->count() + $blogs->count() + $seniman->count();
        @endphp

        {{-- HEADER & FORM PENCARIAN --}}
        <div class="search-header">
            <div class="row align-items-center">
                {{-- KOLOM KIRI --}}
                <div class="col-md-7 text-center text-md-start mb-3 mb-md-0">
                    <h1>Hasil pencarian untuk <strong>"{{$q}}"</strong></h1>
                    <p class="m-0">{{ $totalResults }} hasil ditemukan</p>
                </div>
                {{-- KOLOM KANAN --}}
                <div class="col-md-5">
                    <form action="{{ route('web.search') }}" method="GET" class="internal-search-form ms-auto">
                        <div class="input-group input-group-search">
                            <input type="text" name="q" class="form-control" placeholder="Cari lagi..." value="{{ $q }}" required>
                            <button class="btn btn-search" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if($totalResults == 0)
            <div class="col-12">
                <div class="search-empty">
                    <i class="fas fa-search" style="font-size: 4rem; color: #dee2e6;"></i> 
                    <h3 class="mt-3">Tidak ada hasil ditemukan</h3>
                    <p>Coba gunakan kata kunci yang lebih umum atau periksa ejaan Anda.</p>
                </div>
            </div>
        @else

            {{-- 1. BAGIAN SENIMAN --}}
            @if($seniman->count() > 0)
            <div class="section-title">
                <span>Seniman & Kreator</span>
                <span class="badge-count">{{ $seniman->count() }}</span>
            </div>
            {{-- UPDATE: row-cols-1 (HP) --}}
            <div class="row gx-4 gx-lg-5 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 mb-5">
                @foreach($seniman as $item)
                <div class="col mb-4">
                    <a href="{{ route('seniman.detail', $item->slug) }}" class="card-search-result card-seniman">
                        @php
                            $defaultAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($item->name) . '&background=random&color=fff&size=150&bold=true';
                            $imageSource = $item->image ? asset('uploads/senimans/' . $item->image) : $defaultAvatar;
                        @endphp
                        <img src="{{ $imageSource }}" alt="{{ $item->name }}" onerror="this.onerror=null; this.src='{{ $defaultAvatar }}';"/>
                        <div class="card-body">
                            <h5 title="{{ $item->name }}">{{ $item->name }}</h5>
                            @if($item->julukan)
                                <p class="sub-text" style="margin-bottom: 5px;">"{{ $item->julukan }}"</p>
                            @endif
                            <p class="sub-text">
                                <i class="fas fa-map-marker-alt me-1"></i> {{ $item->city ? $item->city->name : 'Indonesia' }}
                            </p>
                            <span class="btn btn-outline-custom">Lihat Profil</span>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
            @endif

            {{-- 2. BAGIAN LELANG --}}
            @if($lelang->count() > 0)
            <div class="section-title">
                <span>Lelang Karya</span>
                <span class="badge-count">{{ $lelang->count() }}</span>
            </div>
            {{-- UPDATE: row-cols-1 (HP) --}}
            <div class="row gx-4 gx-lg-5 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 mb-5">
                @foreach($lelang as $item)
                <div class="col mb-4">
                    <a href="{{ route('detail', $item->slug) }}" class="card-search-result">
                        <img src="{{ asset($item->imageUtama->path ?? 'assets/img/default.jpg') }}" alt="{{ $item->title }}" />
                        <div class="card-body">
                            <h5 title="{{ $item->title }}">{{ $item->title }}</h5>
                            <p class="sub-text">{{ $item->kategori->name ?? 'Umum' }}</p>
                            <div class="price-tag" style="font-weight: 700; color: #00b8a9; margin-bottom: 15px;">
                                {{ $item->price_str ?? 'Rp '.number_format($item->price,0,',','.') }}
                            </div>
                            <span class="btn btn-outline-custom">Ikut Lelang</span>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
            @endif

            {{-- 3. BAGIAN MERCHANDISE --}}
            @if($merchandise->count() > 0)
            <div class="section-title">
                <span>Merchandise</span>
                <span class="badge-count">{{ $merchandise->count() }}</span>
            </div>
            {{-- UPDATE: row-cols-1 (HP) --}}
            <div class="row gx-4 gx-lg-5 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 mb-5">
                @foreach($merchandise as $item)
                @php
                    $imgRaw = $item->defaultVariant->images->first(); 
                    $imgPath = $imgRaw ? $imgRaw->image_path : 'assets/img/default_merch.jpg';
                    $price = $item->defaultVariant->price;
                    if($item->defaultVariant->sizes && $item->defaultVariant->sizes->count() > 0) {
                        $price = $item->defaultVariant->sizes->min('price');
                    }
                @endphp
                <div class="col mb-4">
                    <a href="{{ route('merch.products.detail', $item->slug) }}" class="card-search-result">
                        <img src="{{ asset($imgPath) }}" alt="{{ $item->name }}" />
                        <div class="card-body">
                            <h5 title="{{ $item->name }}">{{ $item->name }}</h5>
                            <p class="sub-text">Official Merch</p>
                            <div class="price-tag" style="font-weight: 700; color: #00b8a9; margin-bottom: 15px;">
                                Rp {{ number_format($price, 0, ',', '.') }}
                            </div>
                            <span class="btn btn-outline-custom">Beli Sekarang</span>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
            @endif

            {{-- 4. BAGIAN BLOG (TETAP SAMA) --}}
            @if($blogs->count() > 0)
            <div class="section-title">
                <span>Artikel & Berita</span>
                <span class="badge-count">{{ $blogs->count() }}</span>
            </div>
            <div class="row gx-4 gx-lg-5 row-cols-1 row-cols-md-3 mb-5">
                @foreach($blogs as $item)
                <div class="col mb-4">
                    <a href="{{ route('web.blog.detail', $item->slug) }}" class="card-search-result">
                        <img src="{{ asset('uploads/blogs/' . $item->image) }}" onerror="this.src='{{ asset('assets/img/blog_default.jpg') }}'" alt="{{ $item->title }}" />
                        <div class="card-body">
                            <h5 title="{{ $item->title }}">{{ $item->title }}</h5>
                            <p class="sub-text">
                                <i class="far fa-calendar me-1"></i> {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                            </p>
                            <span class="btn btn-outline-custom mt-auto">Baca Selengkapnya</span>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
            @endif

        @endif
    </div>
</section>
@endsection