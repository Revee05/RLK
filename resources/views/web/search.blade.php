@extends('web.partials.layout')
@section('content')
<section class="py-4">
    <div class="container">
        <p class="search-title">Hasil pencarian <strong>"{{$q}}" </strong>{{$products->total()}} hasil ditemukan</p>
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
                                <div class="kategori-produk">{{$produk->kategori->name}}</div>
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