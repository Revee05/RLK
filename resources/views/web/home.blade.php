@extends('web.partials.layout')
@section('home','aktiv')

{{-- 
================================
CSS SEKARANG MEMANGGIL FILE BARU
================================
--}}
@section('css')
    <link href="{{ asset('css/home_new.css') }}" rel="stylesheet">
<<<<<<< HEAD
 <link href="{{asset('theme/owlcarousel/assets/owl.carousel.min.css')}}" rel="stylesheet" />
 <link href="{{asset('theme/owlcarousel/assets/owl.theme.default.min.css')}}" rel="stylesheet" />
 <style type="text/css">
    .main-content {
        position: relative;
    }
    .custom-nav {
        position: absolute;
        top: 30%;
        left: 0;
        right: 0;
    }
    .custom-nav .owl-prev, .owl-next {
        position: absolute;
        height: 50px;
        width: 50px;
        color: inherit;
        background: none;
        border: none;
        border-radius: 50% !important;
        z-index: 100;
        background-color: #ffffff70 !important;
    }
    .custom-nav .owl-prev i {
        font-size: 20px;
        margin: 10px;
        color: black;
    }
    .custom-nav .owl-next i {
        font-size: 20px;
        margin: 10px;
        color: black;
    }
    
    .owl-prev {
        left: 0;
    }
    .owl-next {
        right: 0;
    }
    .follow-icon-bottom {
        height: 35px;
        width: 35px;
        color: black;
        margin: 0px 40px;
        font-size: 25px;
    }
    s{
       text-decoration : line-through;
    }
    .blog-figure {
        position: relative;
        overflow: hidden;
        height:385px;
        width: 100%;
    }
    .blog-figure img{
        object-fit: cover;
        object-position: center;
        height:100%;
        width: 100%;
    }
    .section-about {
        background-color: #343a40;
    }
    .mark-lelang {
        position: absolute;
        z-index: 10;
        top: 10px;
        right: 10px;
    }
    .hero-koleksi {
        background-color: #fff;
        font-family: 'Inter', sans-serif;
    }

    .hero-koleksi h1 {
        font-weight: 700;
        color: #111;
        font-size: 2rem;
        line-height: 1.4;
    }

    .hero-koleksi .subtitle {
        color: #666;
        font-size: 0.95rem;
        white-space: nowrap; /* biar satu baris aja */
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }

    .search-bar {
        max-width: 420px;
        margin: 0 auto;
        width: 100%;
    }

    .search-wrapper {
        position: relative;
        border-radius: 50px;
        overflow: hidden;
        background-color: #22b6b6;
    }

    .search-input {
        border: none;
        border-radius: 50px;
        padding: 12px 50px 12px 20px;
        width: 100%;
        background-color: #22b6b6;
        color: #fff;
        font-size: 0.95rem;
    }

    .search-input::placeholder {
        color: rgba(255, 255, 255, 0.8);
    }

    .search-input:focus {
        outline: none;
        background-color: #1fa1a1;
    }

    .btn-search {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: white;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .btn-search i {
        color: #fff;
    }
    .carausel-figure {
        position: relative;
        height: 420px; /* lebih pendek dan modern */
        overflow: hidden;
    }

    .carausel-figure img {
        object-fit: cover;
        width: 100%;
        height: 100%;
        filter: brightness(65%); /* biar teksnya lebih kontras */
        transition: transform 0.8s ease;
    }

    .carausel-figure:hover img {
        transform: scale(1.05); /* efek zoom halus */
    }

    .carousel-caption-custom {
        position: absolute;
        top: 50%;
        left: 10%;
        transform: translateY(-50%);
        color: #fff;
        max-width: 500px;
        font-family: 'Inter', sans-serif;
    }

    .carousel-caption-custom h2 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 10px;
        text-shadow: 1px 1px 8px rgba(0, 0, 0, 0.3);
    }

    .carousel-caption-custom p {
        font-size: 1rem;
        line-height: 1.5;
        margin-bottom: 20px;
        color: #eaeaea;
    }

    .carousel-caption-custom .btn {
        background-color: #22b6b6;
        border: none;
        color: #fff;
        transition: 0.3s;
        font-weight: 500;
    }

    .carousel-caption-custom .btn:hover {
        background-color: #1b9a9a;
    }

    .custom-slider {
        position: relative;
        width: 90%;
        margin: 40px auto;
        overflow: hidden;
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .carousel-item {
        height: 600px;
        background-size: cover;
        background-position: center;
        transition: transform 1s ease-in-out;
    }

    /* box teks di pojok kiri bawah */
    .slider-text-box {
        position: absolute;
        bottom: 40px;
        left: 50px;
        background-color: #001b44; /* navy gelap */
        color: #fff;
        padding: 22px 30px;
        border-radius: 12px;
        max-width: 450px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.4);
    }

    .slider-text-box h2 {
        font-family: 'Inter', sans-serif;
        font-weight: 600;
        font-size: 22px;
        margin-bottom: 8px;
    }

    .slider-text-box p {
        font-family: 'Inter', sans-serif;
        font-weight: 400;
        font-size: 15px;
        margin-bottom: 14px;
        opacity: 0.95;
    }

    /* tombol kecil putih */
    .btn-slider {
        background-color: #fff !important;
        color: #001b44 !important;
        font-size: 14px;
        padding: 6px 16px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-slider:hover {
        background-color: #f0f0f0 !important;
    }

    /* indikator titik di bawah */
    .carousel-indicators {
        bottom: 10px;
    }

    .carousel-indicators [data-bs-target] {
        background-color: #fff;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        opacity: 0.6;
    }

    .carousel-indicators .active {
        opacity: 1;
    }

    /* sembunyikan tombol next/prev */
    .carousel-control-prev,
    .carousel-control-next {
        display: none !important;
    }

</style>
=======
>>>>>>> Az-Zauqy
@endsection

{{-- 
================================
KONTEN (Lengkap dengan Perbaikan)
================================
--}}
@section('content')
<<<<<<< HEAD
=======

>>>>>>> Az-Zauqy
<section class="hero-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
<<<<<<< HEAD
                <h1>Koleksi Karya Seniman Terbaik di Semarang</h1>
                <p>Temukan Nilai Sesungguhnya dari Sebuah Karya Melalui Lelang Terbuka di Rasanya Lelang Karya</p>
                
                <form action="{{route('web.search')}}" method="GET" class="hero-search">
                    <input type="text" class="form-control search-input-none" placeholder="cari karya seni..." aria-label="Recipient's username" aria-describedby="basic-addon2" name="q">
                    <button type="submit">
                        <i class="fa fa-search"></i>
                        Cari
=======
                <h1>Koleksi Karya Seniman Terbaik dari Indonesia</h1>
                <p>Temukan Nilai Sesungguhnya dari Sebuah Karya Melalui Lelang Terbuka di Rasanya Lelang Karya</p>
                
                <form action="{{route('web.search')}}" method="GET" class="hero-search">
                    <input type="text" class="form-control search-input-none" placeholder="Cari" aria-label="Recipient's username" aria-describedby="basic-addon2" name="q">
                    <button type="submit">
                        <i class="fa fa-search"></i>
>>>>>>> Az-Zauqy
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
<<<<<<< HEAD
                            <div class="carousel-item @if($idx == 0) active @endif"
                                style="background-image: url('{{ asset('uploads/sliders/'.$slide->image) }}');">
                            </div>
=======
>>>>>>> Az-Zauqy
                        <div class="carousel-item @if($idx == 0) active @endif">
                            <img class="d-block w-100" src="{{asset('uploads/sliders/'.$slide->image)}}">
                        </div>
                        @endforeach
                    </div>
<<<<<<< HEAD
=======
                    
>>>>>>> Az-Zauqy
                    <button class="carousel-control-prev" type="button" data-bs-target="#myCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#myCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>

<<<<<<< HEAD
=======
                    {{-- 
>>>>>>> Az-Zauqy
                    <div class="hero-overlay-box">
                        <h4>Semarang Art Festival</h4>
                        <p>Lelang Online 15 November - 31 Desember 2025</p>
                        <a href="#" class="btn btn-detail">Detail</a>
<<<<<<< HEAD
                    </div>
                </div>

=======
                    </div> 
                    --}}

                    @if($featuredEvent) <div class="hero-overlay-box">
                        <h4>{{ $featuredEvent->title }}</h4>
                        <p>{{ $featuredEvent->subtitle }}</p>
                        <a href="{{ $featuredEvent->link ?? '#' }}" class="btn btn-detail">Detail</a>
                    </div>
                    @endif
                    </div>
>>>>>>> Az-Zauqy
            </div>
        </div>
    </div>
</section>

<section class="section-padding auction-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="section-title">General Auction</h2>
            </div>
        </div>

        <div class="row">

            @if($products->isNotEmpty())
                @foreach($products->take(3) as $produk)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card-auction">
                        <img src="{{asset($produk->imageUtama->path ?? 'assets/img/default.jpg')}}" alt="{{$produk->title}}">
                        <div class="card-body">
                            <h5>{{ $produk->title }}</h5> 
                            <p class="text-decoration-none text-dark" href="{{route('products.category',$produk->kategori->slug)}}">{{$produk->kategori->name}}</p>
                            
                            <a href="{{route('detail',$produk->slug)}}" class="btn btn-outline-custom">Bid Sekarang</a>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="col-lg-8 text-center py-5">
                    <p style="color: #6c757d; font-size: 1.1rem;">Belum ada karya lelang aktif yang tersedia saat ini.</p>
                </div>
            @endif

        </div>
    </div>
</section>

@if(isset($blogs) && $blogs->isNotEmpty())
<section class="section-padding blog-section">
    <div class="container">
        <div class="row align-items-center mb-4">
            <div class="col-6">
<<<<<<< HEAD
                <h2 class="section-title mb-0">Blog</h2>
=======
                <h2 class="section-title mb-0">Online Blog</h2>
>>>>>>> Az-Zauqy
            </div>
            <div class="col-6 text-end">
                <a href="{{ route('blogs') }}" class="blog-see-all-link">Semua postingan >></a>
            </div>
        </div>
<<<<<<< HEAD
    </section>
=======
>>>>>>> Az-Zauqy

        <div class="row">
            @foreach($blogs->take(2) as $blog)
            
            <div class="col-lg-6 mb-4 d-flex"> 
                
                <div class="card-blog">
                    <img src="{{asset('uploads/blogs/'.$blog->image)}}" alt="{{$blog->title}}">
                    <div class="card-body">
                        <h5>{{ $blog->title }}</h5>
                        <p>{!! Str::limit(strip_tags($blog->body), 150) !!}</p>
                        
                        <a href="{{route('web.blog.detail',$blog->slug)}}" class="btn btn-outline-custom">Lihat lebih banyak</a>
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
JAVASCRIPT (Hanya untuk Slider)
================================
--}}
@section('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    
    <script type="text/javascript">
    $(document).ready(function(){
        $('#myCarousel').carousel({
            interval: 2000
        });
    })
    </script>
@endsection