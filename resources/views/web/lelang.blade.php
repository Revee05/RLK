@extends('web.partials.layout')
@section('lelang','aktiv')
@section('css')
 <style type="text/css">
    .mark-lelang {
        position: absolute;
        z-index: 10;
        top: 10px;
        right: 10px;
    }
 </style>
@endsection
@section('content')
<section class="py-4">
    <div class="container">
        <div class="row gx-4 gx-lg-4 row-cols-2 row-cols-md-3 row-cols-xl-4">
            @foreach($products as $produk)
            <div class="col-md-3 mb-4">
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
        <div class="row justify-content-center" style="display:grid;">        
            <div class="col-md-12">
                {{$products->links()}}
            </div>
        </div>
    </div>
</section>
@endsection