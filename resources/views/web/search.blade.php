@extends('web.partials.layout')

{{-- 
================================
CSS UNTUK HALAMAN PENCARIAN
================================
--}}
@section('css')
<style type_content="text/css">
    .search-header {
        padding-bottom: 30px;
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 40px;
    }
    .search-header h1 {
        font-weight: 700;
        font-size: 2.2rem;
        margin: 0;
    }
    .search-header h1 strong {
        color: #00b8a9; /* Warna aksen (hijau toska) */
    }
    .search-header p {
        font-size: 1.1rem;
        color: #6c757d;
        margin: 0;
        padding-top: 5px;
    }

    /* Menggunakan style yang SAMA dengan 'card-auction' di home
      untuk konsistensi desain.
    */
    .card-search-result {
        border: 1px solid #dee2e6;
        border-radius: 10px;
        background-color: white;
        height: 100%;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        text-decoration: none;
        color: #212529;
        transition: box-shadow 0.2s ease-in-out;
    }
    .card-search-result:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .card-search-result img {
        width: 100%;
        height: 250px; 
        object-fit: cover;
    }
    .card-search-result .card-body {
        padding: 20px;
        display: flex;
        flex-direction: column;
        flex-grow: 1; /* Penting agar tombol menempel di bawah */
    }
    .card-search-result h5 {
        font-weight: 600;
        font-size: 1.1rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis; 
    }
    .card-search-result .kategori {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 15px;
    }
    .card-search-result .price-tag {
        font-size: 1.1rem;
        font-weight: 600;
        color: #212529;
        margin-bottom: 20px;
    }
    
    /* Tombol (sama seperti di home) */
    .btn-outline-custom {
        border: 1px solid #212529;
        color: #212529;
        border-radius: 50px;
        padding: 8px 30px;
        font-weight: 600;
        text-decoration: none;
        margin-top: auto; /* KUNCI: Mendorong tombol ke bawah */
        width: 100%; /* Tombol jadi full width di kartu */
        text-align: center;
    }
    .btn-outline-custom:hover {
        background-color: #212529;
        color: white;
    }

    /* Tampilan jika hasil kosong */
    .search-empty {
        text-align: center;
        padding: 80px 0;
    }
    .search-empty i {
        font-size: 4rem;
        color: #dee2e6; /* Ikon abu-abu muda */
    }
    .search-empty h3 {
        margin-top: 20px;
        font-weight: 600;
    }
    .search-empty p {
        font-size: 1.1rem;
        color: #6c757d;
    }

    /* Style Paginasi Bootstrap agar rapi */
    .pagination {
        --bs-pagination-color: #212529;
        --bs-pagination-hover-color: #00b8a9;
        --bs-pagination-active-bg: #00b8a9;
        --bs-pagination-active-border-color: #00b8a9;
    }
</style>
@endsection

{{-- 
================================
KONTEN (Desain Ulang)
================================
--}}
@section('content')
<section class="py-5">
    <div class="container">
        
        <div class="search-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1>Hasil pencarian untuk <strong>"{{$q}}"</strong></h1>
                </div>
                <div class="col-md-4 text-md-end">
                    <p>{{$products->total()}} hasil ditemukan</p>
                </div>
            </div>
        </div>

        <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4">
            
            @forelse($products as $produk)
            <div class="col mb-5">
                <a href="{{route('detail',$produk->slug)}}" class="card-search-result">
                    <img src="{{asset($produk->imageUtama->path ?? 'assets/img/default.jpg')}}" alt="{{$produk->title}}" />
                    <div class="card-body">
                        <h5 class="fw-bolder">{{$produk->title}}</h5>
                        <p class="kategori">{{$produk->kategori->name}}</p>
                        <div class="price-tag">{{$produk->price_str}}</div>
                        <span class="btn btn-outline-custom">Lihat Lelang</span>
                    </div>
                </a>
            </div>
            
            @empty
            <div class="col-12">
                <div class="search-empty">
                    <i class="fas fa-search"></i> 
                    <h3>Tidak ada hasil</h3>
                    <p>Kami tidak dapat menemukan apa pun untuk "<strong>{{$q}}</strong>".<br>Coba gunakan kata kunci yang lain.</p>
                </div>
            </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{-- Ini akan otomatis menampilkan link paginasi jika ada lebih dari 1 halaman --}}
            {{$products->links()}}
        </div>
    </div>
</section>
@endsection