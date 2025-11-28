@extends('web.partials.layout')
@section('css')
<style type="text/css">
      .seniman-figure {
        position: relative;
        overflow: hidden;
        height:150px;
        width:150px;
        margin: auto;
        border-radius: 50%;
    }
    .seniman-figure img{
        object-fit: cover;
        object-position: center;
        height:100%;
        width: 100%;
    }
    #profil-limit {
        text-overflow: ellipsis;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 8;
        -webkit-box-orient: vertical;
    }
    #profil-full{
        display: none
    }
    #btn-limit, #btn-full {
        cursor: pointer;
    }
</style>
@endsection
@section('content')
<section class="py-1">
    <div class="container">
        <div class="row mt-2">
          <div class="col-md-12 text-danger">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-danger">Home</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-danger">Seniman</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-danger">{{$products->first()->karya->name ?? ''}}</a></li>
              </ol>
            </nav>
          </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="card text-center pt-3">
                    <div class="seniman-figure">
                        <img src="{{asset('uploads/senimans/'.$products->first()->karya->image ?? '')}}" alt="{{$products->first()->karya->name ?? ''}}">
                    </div>
                    <div class="py-3">
                        <button type="button" class="btn btn-sm btn-primary">
                             <span class="badge badge-light">{{$products->count()}}</span> Karya
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="row profile-seniman">
                    <div class="col-md-12">
                        <h3>{{$products->first()->karya->name ?? ''}}</h3>
                        <div id="profil-limit">
                            {!! $products->first()->karya->description ?? ''!!}
                            
                        </div>
                        <a class="text-danger px-3 text-decoration-none" id="btn-limit">Baca Selengkapnya . . .</a>
                        <div id="profil-full">
                            {!! $products->first()->karya->description ?? ''!!}
                            <p><i class="fas fa-map-marker-alt"></i> {!!$products->first()->karya->address ?? ''!!}</p>
                            <a class="text-danger px-3 text-decoration-none" id="btn-full">Sembunyikan</a>
                        </div>

                    </div>
                    <div class="col-md-12 pt-2">
                        <div class="d-inline-flex text-center">
                         
                            <a href="{{$products->first()->karya->social['instagram'] ?? '#'}}" class="d-block text-decoration-none footer-icon-bottom ig">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="{{$products->first()->karya->social['facebook'] ?? '#'}}" class="d-block text-decoration-none footer-icon-bottom fb">
                                <i class="fab fa-facebook"></i>
                            </a>
                            <a href="{{$products->first()->karya->social['twitter'] ?? '#'}}" class="d-block text-decoration-none footer-icon-bottom twit">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="{{$products->first()->karya->social['tiktok'] ?? '#'}}" class="d-block text-decoration-none footer-icon-bottom">
                                <i class="fab fa-tiktok"></i>
                            </a>
                            <a href="{{$products->first()->karya->social['youtube'] ?? '#'}}" class="d-block text-decoration-none footer-icon-bottom yt">
                                <i class="fab fa-youtube"></i>
                            </a>
                            
                            
                         </div>
                    </div>
                </div>
            </div>

        </div>
        <hr>
        <div class="row gx-4 gx-lg-4 row-cols-2 row-cols-md-3 row-cols-xl-4">
            @foreach($products as $produk)
            <div class="col-md-3 mb-4">
                <a href="{{route('detail',$produk->slug)}}" class="text-decoration-none">
                    
                    <div class="card h-100 card-produk">
                        <!-- Product image-->
                        <div class="card-figure">
                        <img class="card-img-top card-image" src="{{asset($produk->imageUtama->path ?? 'assets/img/default.jpg')}}" alt="{{$produk->title}}" />
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
                                    {{$produk->price_str}}
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        <div class="row justify-content-center" style="display:grid;">        
            <div class="col-md-12">
                {{$products->links()}}
            </div>
        </div>
    </div>
</section>
@endsection
@section('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function(){
        // var limit = document.getElementById("profil-limit");
        // var btn = document.getElementById("btn-limit");
        // var full = document.getElementById("profil-full");
        $('#btn-limit').click(function(){
            $("#profil-limit").hide();
            $("#btn-limit").hide();
            $("#profil-full").show();
        })
        $('#btn-full').click(function(){
            $("#profil-limit").show();
            $("#btn-limit").show();
            $("#profil-full").hide();
        })
    })
    </script>
@endsection