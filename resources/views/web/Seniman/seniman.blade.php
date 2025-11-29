@extends('web.partials.layout')

{{-- CSS eksternal --}}
@section('css')
<link rel="stylesheet" href="{{ asset('css/seniman/seniman.css') }}">
@endsection

@section('content')
<div class="seniman-main-container">
    {{-- Header Section --}}
    <section class="seniman-page-header">
        <div class="container">
            <h2>Seniman</h2>
            <p>Seniman adalah kreator di balik setiap karya yang ditawarkan di platform ini. Mereka menggabungkan bic, eksperimen, dan inspirasi kreatif untuk menghasilkan karya yang unik dan bernilai seni penuh ikhlas.</p>
        </div>
    </section>

    {{-- Search Bar Section --}}
    <section class="seniman-search-bar">
        <div class="container">
            <form action="{{ route('seniman.index') }}" method="GET" class="seniman-search-form">
                <div class="search-wrapper">
                    <input 
                        type="text" 
                        name="search" 
                        class="search-input"
                        placeholder="Search Seniman"
                        value="{{ request('search') }}"
                    />
                    <button type="submit" class="search-icon-btn">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
                
                <div class="filter-sort-wrapper">
                    <button type="button" class="filter-btn">
                        <i class="fa fa-filter"></i>
                        <span>filter</span>
                    </button>
                    
                    <select name="sort" class="sort-select" onchange="this.form.submit()">
                        <option value="">sort by</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama A-Z</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama Z-A</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                        <option value="" {{ request('sort') == '' ? 'selected' : '' }}>Terbaru</option>
                    </select>
                </div>
            </form>
        </div>
    </section>

    {{-- Seniman Grid --}}
    <section class="pb-5">
        <div class="container">
            @if($senimans->count() > 0)
                <div class="row g-3">
                    @foreach($senimans as $seniman)
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <a href="{{ route('seniman.detail', $seniman->slug) }}" class="text-decoration-none">
                                <div class="seniman-card">
                                    <div class="seniman-image-wrapper">
                                        @if($seniman->image)
                                            <img src="{{ asset('uploads/senimans/' . $seniman->image) }}" 
                                                 alt="{{ $seniman->name }}" 
                                                 class="seniman-image">
                                        @else
                                            <div class="seniman-image d-flex align-items-center justify-content-center bg-light">
                                                <i class="fas fa-user fa-3x text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="seniman-info">
                                        <div class="seniman-name">{{ $seniman->name }}</div>
                                        <div class="seniman-location">
                                            {{ Str::contains($seniman->address, ',') ? trim(Str::afterLast($seniman->address, ',')) : $seniman->address }}
                                        </div>
                                        <div class="seniman-bio bio-clamp">{!! $seniman->bio !!}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
                
                {{-- Pagination --}}
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="d-flex justify-content-center">
                            {{ $senimans->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-user-friends fa-4x text-muted mb-3"></i>
                    <h4>Seniman tidak ditemukan</h4>
                    <p class="text-muted">Coba ubah kata kunci pencarian Anda</p>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection

@php use Illuminate\Support\Str; @endphp
