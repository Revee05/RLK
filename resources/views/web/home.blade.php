@extends('web.partials.layout')
@section('home','aktiv')

{{-- 
================================
1. CSS (Menggunakan Owl Carousel)
================================
--}}
@section('css')
    <link href="{{ asset('css/home_new.css') }}" rel="stylesheet">
    
    <link href="{{asset('theme/owlcarousel/assets/owl.carousel.min.css')}}" rel="stylesheet" />
    <link href="{{asset('theme/owlcarousel/assets/owl.theme.default.min.css')}}" rel="stylesheet" />
@endsection


{{-- 
================================
2. KONTEN
================================
--}}
@section('content')

<section class="hero-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h1>Koleksi Karya Seniman Terbaik dari Indonesia</h1>
                <p>Temukan Nilai Sesungguhnya dari Sebuah Karya Melalui Lelang Terbuka di Rasanya Lelang Karya</p>
                <form action="{{route('web.search')}}" method="GET" class="hero-search">
                    <input type="text" class="form-control search-input-none" placeholder="Cari" aria-label="Recipient's username" aria-describedby="basic-addon2" name="q">
                    <button type="submit">
                        <i class="fa fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="container"> 
        <div class="row">
            <div class="col-md-12"> 
                <div class="carousel slide slider-container" id="myCarousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($sliders as $idx => $slide)
                        <div class="carousel-item @if($idx == 0) active @endif">
                            <img class="d-block w-100" src="{{asset('uploads/sliders/'.$slide->image)}}">
                        </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#myCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#myCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>
                    
                    @if($featuredEvent) 
                    <div class="hero-overlay-box">
                        <h4>{{ $featuredEvent->title }}</h4>
                        <p>{{ $featuredEvent->subtitle }}</p>
                        @if($featuredEvent->status == 'coming_soon')
                            <span class="btn btn-detail" style="opacity: 1; cursor: default;">Segera Hadir</span>
                        @else
                            <a href="{{ $featuredEvent->link ?? '#' }}" class="btn btn-detail">Detail</a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-padding auction-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 d-flex justify-content-between align-items-center mb-4 header-flex">
                <h2 class="section-title mb-0">Karya Lelang</h2>
                <a href="{{ route('lelang') }}" class="see-all-text">Lihat Semua</a>
            </div>
        </div>

        <div class="main-content">
            <div id="auction-carousel" class="owl-carousel owl-theme">
                
                @if(isset($products) && $products->isNotEmpty())
                    @foreach($products as $produk) 
                    <div class="item">
                        <div class="card-auction">
                            <img src="{{asset($produk->imageUtama->path ?? 'assets/img/default.jpg')}}" alt="{{$produk->title}}">
                            
                            <div class="card-body">
                                <h5>{{ $produk->title }}</h5> 
                                <p class="text-decoration-none text-dark" href="{{route('products.category',$produk->kategori->slug)}}">{{$produk->kategori->name}}</p>
                                <a href="{{route('lelang.detail',$produk->slug)}}" class="btn btn-outline-custom">Bid Sekarang</a>
                            </div>

                        </div>
                    </div> 
                    @endforeach
                @else
                    {{-- CARD INFORMASI JIKA KOSONG --}}
                    <div class="item">
                        <div class="card-auction">
                            <div style="height: 300px; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-gavel fa-4x" style="color: #dee2e6;"></i>
                            </div>
                            <div class="card-body">
                                <h5>Belum Ada Lelang</h5> 
                                <p>Saat ini belum ada lelang yang berlangsung.</p>
                                <span class="btn btn-outline-custom disabled" style="opacity: 0.6; cursor: default; background-color: #e9ecef; border-color: #dee2e6; color: #6c757d;">Nantikan Segera</span>
                            </div>
                        </div>
                    </div>
                @endif
                
            </div> 
            <div class="owl-theme">
                <div class="owl-controls">
                </div>
            </div>
        </div> 
    </div>
</section>

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
                <div class="card-blog">
                    <img src="{{asset('uploads/blogs/'.$blog->image)}}" alt="{{$blog->title}}">
                    <div class="card-body">
                        <h5>{{ $blog->title }}</h5>
                        
                        <p>{{ Str::limit($blog->excerpt, 150) }}</p>
                        
                        {{-- UPDATE: TEXT BUTTON UNIFIED --}}
                        <a href="{{route('web.blog.detail',$blog->slug)}}" class="btn-blog-responsive">
                            Baca Selengkapnya
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
            
        </div> 
    </div>
</section>
@endif

@endsection 

{{-- 
================================
3. JAVASCRIPT
================================
--}}
@section('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="{{asset('theme/owlcarousel/owl.carousel.min.js')}}"></script>
    
    <script type="text/javascript">
    $(document).ready(function(){
    
        // --- 1. BOOTSTRAP CAROUSEL (Hero Slider) ---
        $('#myCarousel').carousel({
            interval: 2000 
        });

        // --- 2. AUCTION CAROUSEL (Owl Carousel) ---
        var auctionCarousel = $('.main-content .owl-carousel');

        auctionCarousel.owlCarousel({
            margin: 15, 
            nav: false, 
            dots: true, 
            
            responsive:{
                // === MOBILE ===
                0:{ 
                    items: 1, 
                    loop: false,      // <--- LOOP DIMATIKAN DI SINI (MOBILE) - agar "See for More" tidak berulang
                    mouseDrag: true, 
                    touchDrag: true, 
                    stagePadding: 40, 
                    margin: 10 
                },
                // === DESKTOP ===
                992:{ 
                    items: 3,
                    loop: false,      
                    mouseDrag: true, 
                    touchDrag: true,
                    stagePadding: 0   
                }
            }
        });

        // --- 3. BLOG CAROUSEL (Owl Carousel) ---
        var blogCarousel = $('.blog-slider'); 

        blogCarousel.owlCarousel({
            loop: false, 
            margin: 30, 
            nav: false, 
            dots: true, 
            
            responsive:{
                0:{
                    items: 1, 
                    margin: 15,
                    mouseDrag: true, 
                    touchDrag: true
                },
                768:{
                    items: 2 
                },
                992:{
                    items: 2 
                }
            }
        });
    });
    </script>
@endsection