@extends('web.partials.layout')
@section('all-other-products','aktiv')

@section('css')
    <link href="{{ asset('css/MerchProductPage.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-4">
    <h2 class="text-center fw-bold mb-4">Products Design</h2>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <input type="text" class="form-control w-auto" style="min-width:1000px; border-radius:50px;" placeholder="Search product...">
        <div>
            <button class="btn btn-outline-secondary btn-sm me-2">Filter</button>
            <button class="btn btn-outline-secondary btn-sm">Sort</button>
        </div>
    </div>
    <div class="products-grid-parent mb-4">
        <div class="div1">
            <a href="{{ url('detail-products') }}" class="text-decoration-none text-dark">
                <div class="card product-card h-100">
                    <img src="https://placehold.co/500x400?text=Main+Back+Design" class="card-img-top" alt="Main Product">
                    <div class="card-body">
                        <div class="product-title mb-1">Dragon</div>
                        <div class="product-desc mb-1">Streetwear Kaos</div>
                        <div class="product-price mb-2">Rp 85.000</div>
                        <div class="product-status">Stok: Available</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="div2">
            <div class="card product-card h-100">
                <img src="https://placehold.co/250x250?text=Front+Model" class="card-img-top" alt="Model Front">
                <div class="card-body">
                    <div class="product-title mb-1">Dragon</div>
                    <div class="product-desc mb-1">Streetwear Kaos</div>
                    <div class="product-status">Stok: Available</div>
                </div>
            </div>
        </div>
        <div class="div3">
            <div class="card product-card h-100">
                <img src="https://placehold.co/250x250?text=Front+Logo" class="card-img-top" alt="Front Logo">
                <div class="card-body">
                    <div class="product-title mb-1">Dragon</div>
                    <div class="product-desc mb-1">Streetwear Kaos</div>
                    <div class="product-status">Stok: Available</div>
                </div>
            </div>
        </div>
        <div class="div4">
            <div class="card product-card h-100">
                <img src="https://placehold.co/250x250?text=Back+Design" class="card-img-top" alt="Back Design">
                <div class="card-body">
                    <div class="product-title mb-1">Dragon</div>
                    <div class="product-desc mb-1">Streetwear Kaos</div>
                    <div class="product-status">Stok: Available</div>
                </div>
            </div>
        </div>
        <div class="div5">
            <div class="card product-card h-100">
                <img src="https://placehold.co/250x250?text=Side+Model" class="card-img-top" alt="Side Model">
                <div class="card-body">
                    <div class="product-title mb-1">Dragon</div>
                    <div class="product-desc mb-1">Streetwear Kaos</div>
                    <div class="product-status">Stok: Available</div>
                </div>
            </div>
        </div>
        <div class="div6">
            <div class="card product-card h-100">
                <img src="https://placehold.co/250x250?text=Front+Flat" class="card-img-top" alt="Front Flat">
                <div class="card-body">
                    <div class="product-title mb-1">Dragon</div>
                    <div class="product-desc mb-1">Streetwear Kaos</div>
                    <div class="product-status">Stok: Available</div>
                </div>
            </div>
        </div>
        <div class="div7">
            <div class="card product-card h-100">
                <img src="https://placehold.co/250x250?text=Back+Flat" class="card-img-top" alt="Back Flat">
                <div class="card-body">
                    <div class="product-title mb-1">Dragon</div>
                    <div class="product-desc mb-1">Streetwear Kaos</div>
                    <div class="product-status">Stok: Available</div>
                </div>
            </div>
        </div>
        <div class="div8">
            <div class="card product-card h-100">
                <img src="https://placehold.co/250x250?text=Model+1" class="card-img-top" alt="Model 1">
                <div class="card-body">
                    <div class="product-title mb-1">Dragon</div>
                    <div class="product-desc mb-1">Streetwear Kaos</div>
                    <div class="product-status">Stok: Available</div>
                </div>
            </div>
        </div>
        <div class="div9">
            <div class="card product-card h-100">
                <img src="https://placehold.co/500x250?text=Model+2" class="card-img-top" alt="Model 2">
                <div class="card-body">
                    <div class="product-title mb-1">Dragon</div>
                    <div class="product-desc mb-1">Streetwear Kaos</div>
                    <div class="product-status">Stok: Available</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <!-- Tambahkan JS khusus halaman ini jika diperlukan -->
@endsection