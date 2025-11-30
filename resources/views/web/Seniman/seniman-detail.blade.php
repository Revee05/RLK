@extends('web.partials.layout')

@section('css')
<link rel="stylesheet" href="{{ asset('css/seniman/seniman.detail.css') }}">
@endsection

@section('content')
<div class="container">
    {{-- Section: Profile Seniman --}}
    <div class="seniman-profile-section">
        <div class="seniman-profile">
            <div class="seniman-image-container">
                @if($seniman->image)
                    <img src="{{ asset('uploads/senimans/' . $seniman->image) }}" alt="{{ $seniman->name }}">
                @else
                    <div class="default-avatar">
                        <i class="fas fa-user fa-5x"></i>
                    </div>
                @endif
            </div>
            <div class="seniman-info-container">
                <h1 class="seniman-name">{{ $seniman->name }}</h1>
                
                <div class="seniman-stats">
                    <div class="stat-item">
                        <i class="fas fa-palette"></i>
                        <span>{{ $seniman->total_products ?? 0 }} Karya</span>
                    </div>
                    @if($seniman->created_at)
                    <div class="stat-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Bergabung sejak {{ \Carbon\Carbon::parse($seniman->created_at)->translatedFormat('d F Y') }}</span>
                    </div>
                    @endif
                </div>

                @if($seniman->address)
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span class="info-label">Alamat:</span>
                    <span class="info-value">{{ $seniman->address }}</span>
                </div>
                @endif

                @if($seniman->bio)
                <div class="info-item">
                    <i class="fas fa-info-circle"></i>
                    <span class="info-label">Bio:</span>
                    <div class="info-bio">{!! $seniman->bio !!}</div>
                </div>
                @endif

                @if($seniman->description)
                <div class="info-item">
                    <i class="fas fa-align-left"></i>
                    <span class="info-label">Deskripsi:</span>
                    <div class="info-description" id="desc-short" style="display: inline;">
                        {!! Str::limit(strip_tags($seniman->description, '<br><ul><ol><li><b><strong><i><em>'), 400) !!}
                        @if(Str::length(strip_tags($seniman->description)) > 400)
                            <a href="javascript:void(0)" id="show-desc" style="color:#3182ce;">Lihat Selengkapnya</a>
                        @endif
                    </div>
                    <div class="info-description" id="desc-full" style="display: none;">
                        {!! $seniman->description !!}
                        <a href="javascript:void(0)" id="hide-desc" style="color:#3182ce;">Tutup</a>
                    </div>
                </div>
                @endif

                @php
                    $socmedColors = [
                        'facebook' => '#1877f3',
                        'twitter' => '#1da1f2',
                        'instagram' => '#e4405f',
                        'youtube' => '#ff0000',
                        'tiktok' => '#000000',
                    ];
                @endphp

                @if($seniman->social && is_array($seniman->social))
                <div class="info-item">
                    <i class="fas fa-share-alt"></i>
                    <span class="info-label">Media Sosial:</span>
                    <span class="info-value d-flex flex-wrap" style="gap:8px;">
                        @foreach($seniman->social as $key => $url)
                            @if($url)
                                <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-light" style="margin-right:4px;">
                                    <i class="fab fa-{{ $key }}" style="color:{{ $socmedColors[$key] ?? '#555' }}"></i> {{ ucfirst($key) }}
                                </a>
                            @endif
                        @endforeach
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <hr>

    {{-- Section: Karya/Produk --}}
    <div class="products-section">
        <div class="section-header">
            <h4><i class="fas fa-box-open"></i> Karya & Produk Seniman</h4>
            <p class="section-subtitle">Koleksi karya terbaik dari {{ $seniman->name }}</p>
        </div>
        <div class="row">
            @forelse($productsData as $product)
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        @php
                            $imagePath = $product['image_utama'] ?? 'assets/img/default.jpg';
                        @endphp
                        <img src="{{ asset($imagePath) }}" class="card-img-top" alt="{{ $product['title'] }}" onerror="this.src='{{ asset('assets/img/default.jpg') }}'">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product['title'] }}</h5>
                            <p class="card-text">Rp {{ number_format($product['price'], 0, ',', '.') }}</p>
                            <a href="{{ route('detail', $product['slug']) }}" class="btn btn-primary btn-sm">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p>Belum ada karya/produk dari seniman ini.</p>
                </div>
            @endforelse
        </div>
        <div class="d-flex justify-content-center mt-4">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const showBtn = document.getElementById('show-desc');
    const hideBtn = document.getElementById('hide-desc');
    if(showBtn && hideBtn) {
        showBtn.addEventListener('click', function() {
            document.getElementById('desc-short').style.display = 'none';
            document.getElementById('desc-full').style.display = 'inline';
        });
        hideBtn.addEventListener('click', function() {
            document.getElementById('desc-short').style.display = 'inline';
            document.getElementById('desc-full').style.display = 'none';
        });
    }
});
</script>
@endpush