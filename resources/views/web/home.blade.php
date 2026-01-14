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
            <div class="col-lg-12 d-flex justify-content-between align-items-center">
                <h2 class="section-title mb-0">General Auction</h2>
                <div class="owl-custom-nav-auction">
                    <span class="custom-next" id="auction-next">
                        <i class="fa fa-arrow-right"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="main-content">
            <div id="auction-carousel" class="owl-carousel owl-theme">
                
                @if($products->isNotEmpty())
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
                    </div> @endforeach
                @endif
                
                <div class="item">
                    <a href="{{ route('lelang') }}" class="card-see-more-link">
                        <div class="card-auction card-see-more">
                            <div class="card-body">
                                <h5>See for More</h5>
                                <small class="see-more-caption">Click here</small>
                            </div>
                        </div>
                    </a>
                </div>

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
        <div class="row align-items-center mb-2">
            <div class="col-lg-12 d-flex justify-content-between align-items-center">
                <h2 class="section-title mb-0">Online Blog</h2>
                <div class="owl-custom-nav-blog">
                    <span class="custom-next" id="blog-next">
                        <i class="fa fa-arrow-right"></i>
                    </span>
                </div>
            </div>
        </div>
        
        <div class="blog-slider owl-carousel owl-theme">

            @foreach($blogs->take(3) as $blog) 
            <div class="item d-flex">
                <div class="card-blog">
                    <img src="{{asset('uploads/blogs/'.$blog->image)}}" alt="{{$blog->title}}">
                    <div class="card-body">
                        <h5>{{ $blog->title }}</h5>
                        
                        <p>{!! Str::limit(strip_tags($blog->body), 150) !!}</p>
                        
                        <a href="{{route('web.blog.detail',$blog->slug)}}" class="btn-blog-responsive">
                            <span class="text-desktop">Lihat..</span>
                            
                            <span class="text-mobile">Read more . . .</span>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
            <div class="item d-flex">
                <a href="{{ route('blogs') }}" class="card-blog-link">
                    <div class="card-blog card-see-more-blog">
                        <div class="card-body">
                            <h5>See for More</h5>
                            <small class="see-more-caption">Click here</small>
                        </div>
                    </div>
                </a>
            </div>
        </div> 
    </div>
</section>
@endif

@endsection 
{{-- 
================================
3. JAVASCRIPT (Menggunakan Dots)
================================
--}}
@section('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    
    <script src="{{asset('theme/owlcarousel/owl.carousel.min.js')}}"></script>
    
    <script type="text/javascript">
    $(document).ready(function(){
    
        // --- 1. BOOTSTRAP CAROUSEL (Hero Slider) ---
        // Assuming 'myCarousel' is the Bootstrap component in the hero section
        $('#myCarousel').carousel({
            interval: 2000 // Sets auto-slide interval
        });

        // --- 2. AUCTION CAROUSEL (Owl Carousel) ---
        var auctionCarousel = $('.main-content .owl-carousel');

        auctionCarousel.owlCarousel({
            // HAPUS "loop: true" dari sini (Global settings)
            margin: 15, 
            nav: false, 
            dots: true, 
            
            responsive:{
                // === TAMPILAN MOBILE / TABLET (<= 991px) ===
                0:{ 
                    items: 1, 
                    loop: false,      // <--- LOOP DIMATIKAN DI SINI (MOBILE) - agar "See for More" tidak berulang
                    mouseDrag: true, 
                    touchDrag: true, 
                    
                    // Opsional: stagePadding membuat user melihat potongan slide kiri/kanan
                    // Hapus baris stagePadding di bawah jika ingin slide penuh satu kotak
                    stagePadding: 40, 
                    margin: 10 
                },
                
                // === TAMPILAN DESKTOP (>= 992px) ===
                992:{ 
                    items: 3,
                    loop: false,      // <--- LOOP MATI DI SINI (DESKTOP)
                    mouseDrag: true, 
                    touchDrag: true,
                    stagePadding: 0   // Pastikan 0 agar rapi di desktop
                }
            }
        });

        // Connect Custom Auction Button (Only Next/Right is active)
        $('#auction-next').click(function() {
            auctionCarousel.trigger('next.owl.carousel');
        });

        // --- 3. BLOG CAROUSEL (Owl Carousel) ---
        var blogCarousel = $('.blog-slider'); 

        blogCarousel.owlCarousel({
            loop: false, 
            margin: 30, 
            nav: false, 
            dots: true, 
            
            responsive:{
                // MOBILE (<= 767px)
                0:{
                    items: 1, 
                    margin: 15,
                    // ENSURE DRAG IS ACTIVE FOR MOBILE/TOUCH
                    mouseDrag: true, 
                    touchDrag: true
                },
                // TABLET (>= 768px)
                768:{
                    items: 2 
                },
                // DESKTOP (>= 992px)
                992:{
                    items: 2 
                }
            }
        });

        // Connect Custom Blog Button (Only Next/Right is active)
        $('#blog-next').click(function() {
            blogCarousel.trigger('next.owl.carousel');
        });
    });
    </script>
@endsection