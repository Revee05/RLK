@extends('web.partials.layout')
@section('home','aktiv')

{{-- 
================================
CSS SEKARANG MEMANGGIL FILE BARU
================================
--}}
@section('css')
    <link href="{{ asset('css/home_new.css') }}" rel="stylesheet">
@endsection

{{-- 
================================
KONTEN (Lengkap dengan Perbaikan)
================================
--}}
@section('content')

<section class="hero-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h1>Koleksi Karya Seniman Terbaik di Semarang</h1>
                <p>Temukan Nilai Sesungguhnya dari Sebuah Karya Melalui Lelang Terbuka di Rasanya Lelang Karya</p>
                
                <form action="{{route('web.search')}}" method="GET" class="hero-search">
                    <input type="text" class="form-control search-input-none" placeholder="cari karya seni..." aria-label="Recipient's username" aria-describedby="basic-addon2" name="q">
                    <button type="submit">
                        <i class="fa fa-search"></i>
                        Cari
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

                    <div class="hero-overlay-box">
                        <h4>Semarang Art Festival</h4>
                        <p>Lelang Online 15 November - 31 Desember 2025</p>
                        <a href="#" class="btn btn-detail">Detail</a>
                    </div>
                </div>
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
                <h2 class="section-title mb-0">Blog</h2>
            </div>
            <div class="col-6 text-end">
                <a href="{{ route('blogs') }}" class="blog-see-all-link">Semua postingan >></a>
            </div>
        </div>
    </section>

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
