@extends('web.partials.layout')
@section('home','aktiv')

{{-- CSS --}}
@section('css')
    <link href="{{ asset('css/home_new.css') }}" rel="stylesheet">
    <link href="{{asset('theme/owlcarousel/assets/owl.carousel.min.css')}}" rel="stylesheet" />
    <link href="{{asset('theme/owlcarousel/assets/owl.theme.default.min.css')}}" rel="stylesheet" />
    
    {{-- CSS Tambahan untuk transisi Lazy Load agar halus --}}
    <style>
        .owl-carousel .owl-item img.owl-lazy {
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .owl-carousel .owl-item img {
            opacity: 1;
        }
    </style>
@endsection

{{-- KONTEN --}}
@section('content')

{{-- HERO SECTION --}}
<section class="hero-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h1>Koleksi Karya Seniman Terbaik dari Indonesia</h1>
                <p>Temukan Nilai Sesungguhnya dari Sebuah Karya Melalui Lelang Terbuka di Rasanya Lelang Karya</p>
                <form action="{{route('web.search')}}" method="GET" class="hero-search">
                    {{-- 1. Placeholder WAJIB diisi spasi kosong " " agar CSS selector berfungsi --}}
                    <input type="text" 
                        class="form-control search-input-none" 
                        placeholder=" " 
                        name="q">
                    
                    {{-- 2. Tambahkan SPAN ini tepat di bawah input --}}
                    <span class="responsive-placeholder"></span>

                    <button type="submit"><i class="fa fa-search"></i></button>
                </form>
            </div>
        </div>
    </div>
</section>

{{-- SLIDER SECTION --}}
<section>
    <div class="container"> 
        <div class="row">
            <div class="col-md-12"> 
                
                {{-- LOGIKA: Jika ada EVENT AKTIF --}}
                @if($eventSliders->count() > 0)
                
                    <div id="eventCarousel" class="carousel slide slider-container" data-bs-ride="carousel">
                        
                        {{-- 1. BAGIAN GAMBAR (CAROUSEL) --}}
                        <div class="carousel-inner">
                            @foreach($eventSliders as $key => $event)
                            
                            {{-- PENTING: Kita simpan data teks di 'data attributes' agar bisa dibaca JS --}}
                            <div class="carousel-item {{ $key == 0 ? 'active' : '' }}"
                                 data-title="{{ $event->title }}"
                                 data-online="{{ $event->online_period }}"
                                 data-offline="{{ $event->offline_date }}"
                                 data-location="{{ $event->location }}"
                                 data-link="{{ $event->link ?? '#' }}">
                                
                                {{-- Link Pembungkus Gambar --}}
                                <a href="{{ $event->link ?? '#' }}">
                                    <picture>
                                        {{-- Gambar Mobile --}}
                                        @if($event->image_mobile)
                                            <source media="(max-width: 768px)" srcset="{{ asset($event->image_mobile) }}">
                                        @endif
                                        {{-- Gambar Desktop --}}
                                        <img src="{{ asset($event->image) }}" class="d-block w-100" alt="{{ $event->title }}">
                                    </picture>
                                </a>

                                {{-- INFO KHUSUS MOBILE (Tetap di bawah gambar, tidak melayang) --}}
                                <div class="d-block d-md-none bg-dark text-white p-3 mt-1 rounded text-start">
                                    <h5 class="fw-bold mb-2">{{ $event->title }}</h5>
                                    @if($event->online_period)
                                        <small class="d-block mb-1"><i class="fa fa-globe text-info me-1"></i> {{ $event->online_period }}</small>
                                    @endif
                                    @if($event->offline_date)
                                        <small class="d-block"><i class="fa fa-gavel text-warning me-1"></i> {{ $event->offline_date }}</small>
                                    @endif
                                </div>

                            </div>
                            @endforeach
                        </div>

                        {{-- 2. KOTAK NAVY STATIS (Hanya Desktop) --}}
                        {{-- Letaknya DI LUAR carousel-inner, tapi DI DALAM eventCarousel --}}
                        <div class="static-navy-box d-none d-md-block text-start">
                            {{-- Isi awal diambil dari slide pertama ($key 0) --}}
                            @php $first = $eventSliders->first(); @endphp
                            
                            <h3 id="navyTitle">{{ $first->title }}</h3>
                            
                            <div id="navyContent">
                                @if($first->online_period)
                                <p class="navy-online">
                                    <i class="fa fa-globe text-info me-2"></i> Lelang Online: <br>
                                    <span class="ms-4 text-white-50">{{ $first->online_period }}</span>
                                </p>
                                @endif

                                @if($first->offline_date)
                                <p class="navy-offline">
                                    <i class="fa fa-gavel text-warning me-2"></i> Lelang Offline: <br>
                                    <span class="ms-4 text-white-50">{{ $first->offline_date }}</span>
                                </p>
                                @endif

                                @if($first->location)
                                <p class="navy-location mt-3 text-white-50" style="font-size: 0.85rem; line-height: 1.4;">
                                    <i class="fa fa-map-marker text-danger me-2"></i> {{ $first->location }}
                                </p>
                                @endif
                            </div>
                        </div>

                        {{-- Indikator & Navigasi --}}
                        @if($eventSliders->count() > 1)
                        <div class="carousel-indicators">
                            @foreach($eventSliders as $key => $event)
                                <button type="button" data-bs-target="#eventCarousel" data-bs-slide-to="{{ $key }}" class="{{ $key == 0 ? 'active' : '' }}"></button>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#eventCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#eventCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        </button>
                        @endif

                    </div>

                {{-- LOGIKA FALLBACK (Slider Lama) --}}
                @else
                    {{-- ... (Code slider default tetap sama seperti sebelumnya) ... --}}
                    <div class="carousel slide slider-container" id="defaultCarousel" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($defaultSliders as $idx => $slide)
                            <div class="carousel-item @if($idx == 0) active @endif">
                                <img class="d-block w-100" src="{{asset('uploads/sliders/'.$slide->image)}}" alt="Slider Image">
                            </div>
                            @endforeach
                        </div>
                        {{-- Controls... --}}
                    </div>
                @endif

            </div>
        </div>
    </div>
</section>

{{-- LELANG SECTION --}}
<section class="section-padding auction-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 d-flex justify-content-between align-items-center mb-4 header-flex">
                <h2 class="section-title mb-0">Karya Lelang</h2>
                <a href="{{ route('lelang') }}" class="see-all-text">Lihat Semua</a>
            </div>
        </div>

        <div class="main-content">
            <div id="auction-carousel" class="owl-carousel owl-theme clickable-carousel">
                @if(isset($products) && $products->isNotEmpty())
                    @foreach($products as $produk) 
                    <div class="item">
                        <a href="{{route('lelang.detail',$produk->slug)}}" class="card-auction d-block text-decoration-none">
                            {{-- IMPLEMENTASI LAZY LOAD --}}
                            {{-- 1. Tambah class 'owl-lazy' --}}
                            {{-- 2. Ganti 'src' jadi 'data-src' --}}
                            <img class="owl-lazy" 
                                 data-src="{{asset($produk->imageUtama->path ?? 'assets/img/default.jpg')}}" 
                                 alt="{{$produk->title}}">
                            
                            <div class="card-body">
                                <h5>{{ $produk->title }}</h5> 
                                <p class="text-muted">{{$produk->kategori->name}}</p>
                                <span class="btn-visual">Bid Sekarang</span>
                            </div>
                        </a>
                    </div> 
                    @endforeach
                @else
                    <div class="item">
                        <div class="card-auction">
                            <div style="height: 300px; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-gavel fa-4x" style="color: #dee2e6;"></i>
                            </div>
                            <div class="card-body">
                                <h5>Belum Ada Lelang</h5> 
                                <p>Saat ini belum ada lelang yang berlangsung.</p>
                                <span class="btn-visual disabled" style="opacity: 0.6;">Nantikan Segera</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div> 
            <div class="owl-theme"><div class="owl-controls"></div></div>
        </div> 
    </div>
</section>

{{-- MERCHANDISE SECTION --}}
<section class="section-padding merch-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 d-flex justify-content-between align-items-center mb-4 header-flex">
                <h2 class="section-title mb-0">Produk Merchandise</h2>
                <a href="{{ route('all-other-product') }}" class="see-all-text">Lihat Semua</a>
            </div>
        </div>

        @php $favIds = Auth::check() ? \App\Favorite::where('user_id', Auth::id())->pluck('product_id')->toArray() : []; @endphp
        <script>const userFavorites = @json($favIds);</script>

        <div class="main-content">
            <div id="merch-carousel" class="owl-carousel owl-theme">
                @if(isset($merchProducts) && $merchProducts->isNotEmpty())
                    @foreach($merchProducts as $merch)
                        @php
                            $variant = $merch->defaultVariant;
                            $image = ($variant && $variant->images->count()) ? asset($variant->images->first()->image_path) : 'https://placehold.co/300x250';
                            // Logic harga disederhanakan untuk tampilan (idealnya dipindah ke Controller/Model)
                            $price = 0; $discount = 0;
                            if ($variant) {
                                if ($variant->sizes && $variant->sizes->count() > 0) {
                                    $price = $variant->sizes->min('price');
                                    $discount = $variant->sizes->max('discount');
                                } else {
                                    $price = $variant->price;
                                    $discount = $variant->discount;
                                }
                            }
                            $isFavorite = in_array($merch->id, $favIds);
                            $favIcon = $isFavorite ? "/icons/heart_fill.svg" : "/icons/heart_outline.svg";
                            $originalPrice = ($discount > 0 && $price > 0) ? ($price / (1 - ($discount/100))) : 0;
                        @endphp
                        <div class="item">
                            <div class="card-merch">
                                <div class="merch-img-wrapper">
                                    @if($discount > 0)<div class="discount-badge">-{{ intval($discount) }}%</div>@endif
                                    
                                    {{-- Icon Favorit JANGAN di lazy load karena kecil & interaktif --}}
                                    <img src="{{ asset($favIcon) }}" data-id="{{ $merch->id }}" class="favorite-icon-home favorite-action" alt="favorite">
                                    
                                    <a href="{{ url('/merch/'.$merch->slug) }}" style="width:100%; height:100%;">
                                        {{-- IMPLEMENTASI LAZY LOAD --}}
                                        <img class="owl-lazy" 
                                             data-src="{{ $image }}" 
                                             alt="{{ $merch->name }}">
                                    </a>
                                </div>
                                <div class="card-body">
                                    <a href="{{ url('/merch/'.$merch->slug) }}" class="merch-title">{{ $merch->name }}</a>
                                    <div class="merch-price-box">
                                        @if($price > 0)
                                            <span class="merch-price">Rp {{ number_format($price, 0, ',', '.') }}</span>
                                            @if($discount > 0)<span class="merch-price-slash">Rp {{ number_format($originalPrice, 0, ',', '.') }}</span>@endif
                                        @else
                                            <span class="merch-price">Cek Detail</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="item">
                        <div class="card-merch p-4 text-center d-flex align-items-center justify-content-center" style="height: 250px;">
                            <p class="text-muted">Belum ada merchandise.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- BLOG SECTION --}}
@if(isset($blogs) && $blogs->isNotEmpty())
<section class="section-padding blog-section">
    <div class="container">
        <div class="row align-items-center mb-4">
            <div class="col-lg-12 d-flex justify-content-between align-items-center header-flex">
                <h2 class="section-title mb-0">Artikel dan Berita</h2>
                <a href="{{ route('blogs') }}" class="see-all-text">Lihat Semua</a>
            </div>
        </div>
        
        <div class="blog-slider owl-carousel owl-theme">
            @foreach($blogs->take(5) as $blog) 
            <div class="item d-flex">
                <a href="{{route('web.blog.detail',$blog->slug)}}" class="card-blog d-block text-decoration-none w-100">
                    {{-- IMPLEMENTASI LAZY LOAD --}}
                    <img class="owl-lazy" 
                         data-src="{{asset('uploads/blogs/'.$blog->image)}}" 
                         alt="{{$blog->title}}">
                    
                    <div class="card-body">
                        <h5>{{ $blog->title }}</h5>
                        <p class="text-muted">{{ Str::limit($blog->excerpt, 100) }}</p>
                        <span class="btn-visual mt-auto text-center">Baca Selengkapnya</span>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection 

{{-- JAVASCRIPT --}}
@section('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="{{asset('theme/owlcarousel/owl.carousel.min.js')}}"></script>
    
    <script type="text/javascript">
    $(document).ready(function(){
    
        // 1. Setup Hero Slider (Bootstrap)
        $('#myCarousel').carousel({ interval: 3000 });

        // 2. Setup Owl Carousel Configuration
        var commonSettings = {
            nav: false,
            dots: true,
            loop: false,
            margin: 15,
            smartSpeed: 450,
            fluidSpeed: true,
            mouseDrag: true,
            touchDrag: true,
            
            // --- FITUR LAZY LOAD DIAKTIFKAN ---
            lazyLoad: true, 
            lazyLoadEager: 1 // (Opsional) Load 1 gambar sebelah kanan sebelum terlihat agar mulus
        };

        // Init Carousel Lelang
        $('#auction-carousel').owlCarousel($.extend({}, commonSettings, {
            responsive:{
                0:{ items: 1, stagePadding: 40, margin: 10 },
                992:{ items: 3, stagePadding: 0 }
            }
        }));

        // Init Carousel Merchandise
        $('#merch-carousel').owlCarousel($.extend({}, commonSettings, {
            responsive:{
                0:{ items: 1, margin: 10 }, 
                768:{ items: 3, margin: 15 },
                992:{ items: 4, margin: 20 }
            }
        }));

        // Init Blog Slider
        $('.blog-slider').owlCarousel($.extend({}, commonSettings, {
            responsive:{
                0:{ items: 1, margin: 15 }, 
                768:{ items: 2 }, 
                992:{ items: 2 }
            }
        }));

        // LOGIKA FAVORITE (Tetap sama)
        $(document).on('click', '.favorite-action', function(e) {
            e.preventDefault(); e.stopPropagation();
            @if(!Auth::check())
                alert("Silakan login untuk menyimpan favorit."); return;
            @endif
            const icon = $(this);
            const productId = icon.data('id');
            const currentSrc = icon.attr('src');
            
            if (currentSrc.includes('heart_fill')) {
                icon.attr('src', "{{ asset('/icons/heart_outline.svg') }}");
            } else {
                icon.attr('src', "{{ asset('/icons/heart_fill.svg') }}");
            }

            fetch("{{ route('favorite.toggle') }}", {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                body: JSON.stringify({ product_id: productId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === "added") {
                    icon.attr('src', "{{ asset('/icons/heart_fill.svg') }}");
                    if(typeof userFavorites !== 'undefined' && !userFavorites.includes(productId)) userFavorites.push(productId);
                } else {
                    icon.attr('src', "{{ asset('/icons/heart_outline.svg') }}");
                    if(typeof userFavorites !== 'undefined') {
                        const idx = userFavorites.indexOf(productId);
                        if (idx > -1) userFavorites.splice(idx, 1);
                    }
                }
            })
            .catch(err => {
                console.error('Error:', err); icon.attr('src', currentSrc); 
            });
        });

        // --- LOGIKA UPDATE KOTAK NAVY ---
        // Event listener saat slide Bootstrap Carousel berganti
        $('#eventCarousel').on('slide.bs.carousel', function (e) {
            
            // 'e.relatedTarget' adalah elemen slide (.carousel-item) yang akan muncul
            var nextSlide = $(e.relatedTarget);
            
            // Ambil data dari atribut data- yang sudah kita pasang di HTML
            var title    = nextSlide.data('title');
            var online   = nextSlide.data('online');
            var offline  = nextSlide.data('offline');
            var location = nextSlide.data('location');

            // Efek Fade Out sebentar agar transisi teks halus
            $('.static-navy-box').css('opacity', '0.5');

            setTimeout(function() {
                // Update Judul
                $('#navyTitle').text(title);

                // Bangun ulang HTML untuk konten (karena isinya bisa beda-beda tiap event)
                var htmlContent = '';

                if(online) {
                    htmlContent += '<p class="navy-online"><i class="fa fa-globe text-info me-2"></i> Lelang Online: <br><span class="ms-4 text-white-50">' + online + '</span></p>';
                }
                if(offline) {
                    htmlContent += '<p class="navy-offline"><i class="fa fa-gavel text-warning me-2"></i> Lelang Offline: <br><span class="ms-4 text-white-50">' + offline + '</span></p>';
                }
                if(location) {
                    htmlContent += '<p class="navy-location mt-3 text-white-50" style="font-size: 0.85rem; line-height: 1.4;"><i class="fa fa-map-marker text-danger me-2"></i> ' + location + '</p>';
                }

                // Masukkan HTML baru
                $('#navyContent').html(htmlContent);

                // Fade In kembali
                $('.static-navy-box').css('opacity', '1');
            }, 150); // Delay sedikit (150ms)
        });

    });
    </script>
@endsection