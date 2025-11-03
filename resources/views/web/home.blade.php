@extends('web.partials.layout')
@section('home','aktiv')
@section('css')
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
 </style>
@endsection
@section('content')
<section>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 px-0">
                <div class="carousel slide" id="myCarousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($sliders as $idx => $slide)
                        <div class="carousel-item carausel-figure @if($idx == 0) active @endif">
                            <img class="d-block" src="{{asset('uploads/sliders/'.$slide->image)}}">
                        </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#myCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#myCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row py-3 px-3">
            <div class="col-md-12 text-center">
                <h2 class="lelang-terbaru">Lelang Terbaru</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 px-0">
                <div class="main-content">
                    <div class="owl-carousel owl-theme">
                         @foreach($products as $produk)
                        <div class="item">
                              <a href="{{route('detail',$produk->slug)}}" class="text-decoration-none">
                    
                                <div class="card h-100 card-produk">
                                    <!-- Product image-->
                                    <div class="card-figure">
                                    <img class="card-img-top card-image" src="{{asset($produk->imageUtama->path ?? 'assets/img/default.jpg')}}" alt="{{$produk->title}}" />
                                    <div class="mark-lelang btn btn-light rounded-0 border">
                                        LELANG
                                    </div>
                                    </div>
                                    <!-- Product details-->
                                    <div class="card-body p-2">
                                        <div class="text-left">
                                            <h5 class="fw-bolder produk-title">{{$produk->title}}</h5>
                                            <div class="kategori-produk">
                                                <a class="text-decoration-none text-dark" href="{{route('products.category',$produk->kategori->slug)}}">
                                                    {{$produk->kategori->name}}
                                                </a>
                                            </div>
                                            <!-- Product name-->
                                            {{-- <span class="span-lelang">Lelang saat ini</span> --}}
                                            <!-- Product price-->
                                            <div class="price-produk">
                                                @if($produk->diskon > 0)
                                                <s>{{$produk->price_str}}</s> 
                                                @php
                                                $getDiskon = $produk->diskon / 100;
                                                $neWPrice = $getDiskon * $produk->price;
                                                @endphp
                                                <span class="text-danger">{{$neWPrice}}</span>
                                                @else
                                                {{$produk->price_str}}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer text-center">
                                        <a href="{{route('detail',$produk->slug)}}" class="btn btn-block w-100">BID</a>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                    <div class="owl-theme">
                        <div class="owl-controls">
                            <div class="custom-nav owl-nav"></div>
                        </div>
                    </div>
                </div>
                <div class="d-block text-center py-3">
                    <a href="{{route('lelang')}}" class="btn btn-danger rounded-0">Lihat Semua Lelang</a>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="section-about">
    <div class="container-fluid py-4">
        <div class="row justify-content-md-center">
            <div class="col-md-9 text-center">
                <h2 class="text-white">About Us</h2>
                <p class="text-white">Wadah pengkarya terus berkarya dari karya</p>
            </div>
        </div>
    </div>
</section>
<section>
@if(isset($blogs) && !empty($blogs) && count($blogs) > 0)
    <section>
        <div class="container">
            <div class="row py-2 justify-content-md-center pb-5">
                <div class="text-center">
                    <h2>Featured Article</h2>
                </div>
                <div class="col-md-12 scrolling-pagination">
                    @foreach($blogs as $blog)
                    <div class="d-flex justify-content-between border my-3">
                        <div class="d-block w-50 p-4 p-4-mobile">
                          <div class="d-block text-left text-mb">{{ucwords($blog->author->name)}}</div>
                          <div class="d-block text-left text-mb">{{$blog->date_indo}}</div>
                          <a href="{{route('web.blog.detail',$blog->slug)}}" class="d-block text-decoration-none fw-500 text-dark pt-5 fs-5 blog-title-mobile">
                              {{$blog->title}}
                          </a>
                          <div class="py-3 desc-mobile">
                              {{-- {!!$blog->body!!} --}}
                              {!!Str::limit($blog->body, 250)!!}
                          </div>
                        </div>
                        <div class="d-block w-50">
                            <div class="blog-figure">
                                <img src="{{asset('uploads/blogs/'.$blog->image)}}">
                            </div>
                        </div>
                    </div>
                    @endforeach
                    {{$blogs->links()}}
                </div>
                    <div class="d-block text-center py-3">
                        <a href="{{route('blogs')}}" class="btn btn-danger rounded-0">Lihat Semua Artikel</a>
                    </div>
            </div>
        </div>
    </section>
@endif
<section style="background:#f1f1f1;" class="py-3">
    <div class="container">
        <div class="row">
            <div class="col-md-2">
                <h2 class="ikuti-kami-h2">Ikuti Kami</h2>
            </div>
            <div class="col-md-10">
                 <div class="d-inline-flex text-center w-100 ikuti-kami">
                         
                        <a href="https://www.instagram.com/{{$social['instagram'] ?? '#'}}" class="d-block text-decoration-none follow-icon-bottom">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="{{$social['website'] ?? '#'}}" class="d-block text-decoration-none follow-icon-bottom">
                            <i class="fas fa-globe"></i>
                        </a>
                        <a href="https://twitter.com/{{$social['twitter'] ?? '#'}}" class="d-block text-decoration-none follow-icon-bottom">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://tiktok.com/{{$social['tiktok'] ?? '#'}}" class="d-block text-decoration-none follow-icon-bottom">
                            <i class="fab fa-tiktok"></i>
                        </a>
                        <a href="https://youtube.com/{{$social['youtube'] ?? '#'}}" class="d-block text-decoration-none follow-icon-bottom">
                            <i class="fab fa-youtube"></i>
                        </a>
                        
                        
                     </div>
            </div>
        </div>
    </div>
</section>

@endsection
@section('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.4.1/jquery.jscroll.min.js"></script>
    <script src="{{asset('theme/owlcarousel/owl.carousel.min.js')}}"></script>
    <script type="text/javascript">
    $(document).ready(function(){
        $('#myCarousel').carousel({
            interval: 2000
        });

        $('.main-content .owl-carousel').owlCarousel({
            loop: true,
            margin: 10,
            nav: true,
            navText: [
                '<i class="fa fa-angle-left" aria-hidden="true"></i>',
                '<i class="fa fa-angle-right" aria-hidden="true"></i>'
            ],
            navContainer: '.main-content .custom-nav',
            dots:false,
            responsive:{
                0:{
                    items: 2
                },
                600:{
                    items: 4
                },
                1000:{
                    items: 4
                }
            }
        });
        $('ul.pagination').hide();
            $(function() {
                $('.scrolling-pagination').jscroll({
                    autoTrigger: true,
                    padding: 0,
                    nextSelector: '.pagination li.active + li a',
                    contentSelector: 'div.scrolling-pagination',
                    callback: function() {
                        $('ul.pagination').remove();
                    }
                });
            });
    })
    </script>
@endsection
