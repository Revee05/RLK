@extends('web.partials.layout')

@section('css')
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/seniman/seniman.detail.css') }}">
@endsection

@section('content')
<div class="container">
    {{-- Section: Profile Seniman --}}
    <div class="seniman-profile-section">
        <div class="seniman-profile-layout">
            {{-- Left Column: Image + Contact --}}
            <div class="seniman-left-column">
                {{-- Profile Image --}}
                <div class="profile-image">
                    @if($seniman->image)
                        <img src="{{ asset('uploads/senimans/' . $seniman->image) }}" alt="{{ $seniman->name }}">
                    @else
                        <div class="default-avatar">
                            <i class="fas fa-user fa-5x"></i>
                        </div>
                    @endif
                </div>

                {{-- Contact Profile Box --}}
                @php
                    $socials = [];
                    if (!empty($seniman->social)) {
                        if (is_string($seniman->social)) {
                            $decoded = json_decode($seniman->social, true);
                            $socials = is_array($decoded) ? $decoded : [];
                        } elseif (is_array($seniman->social)) {
                            $socials = $seniman->social;
                        } elseif (is_object($seniman->social)) {
                            $socials = (array) $seniman->social;
                        }
                    }
                @endphp
                @if(!empty($socials))
                <div class="contact-profile-box">
                    <h6><i class="fas fa-address-card"></i> Contact:</h6>
                    @foreach($socials as $key => $url)
                        @if($url)
                            @php
                                $path = parse_url($url, PHP_URL_PATH);
                                $host = parse_url($url, PHP_URL_HOST);
                                $username = $path ? trim($path, '/') : ($host ? preg_replace('/^www\\./', '', $host) : $url);
                            @endphp
                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="contact-item">
                                <i class="fab fa-{{ $key }} social-icon social-{{ $key }}"></i>
                                <span>{{ '@' . $seniman->name }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Right Column: Name + Bio + Info + Accordions --}}
            <div class="seniman-right-column">
                {{-- Name & Subtitle --}}
                <h1 class="seniman-name">
                    Hi, I'm {{ $seniman->name }}
                    @if(!empty($seniman->julukan))
                        <small class="seniman-aka" style="color:#666; font-style:italic; margin-left:8px;">aka {{ $seniman->julukan }}</small>
                    @endif
                </h1>
                <p class="seniman-subtitle">{{ $seniman->profession ?? 'Visual Artist' }}</p>

                {{-- Bio Box --}}
                @if($seniman->bio)
                <div class="bio-box">
                    {!! $seniman->bio !!}
                </div>
                @endif

                {{-- Info Stats --}}
                <div class="seniman-info-stats">
                    @php
                        // Prefer explicit city from relation if available, otherwise extract last segment from address
                        $cityName = null;
                        if(isset($seniman->city) && $seniman->city) {
                            $cityName = is_object($seniman->city) && isset($seniman->city->name) ? $seniman->city->name : (is_string($seniman->city) ? $seniman->city : null);
                        }
                        if(empty($cityName) && !empty($seniman->address)) {
                            $parts = explode(',', $seniman->address);
                            $cityName = trim(end($parts));
                        }
                        $joinedLabel = null;
                        if(!empty($seniman->created_at)) {
                            $joinedLabel = \Carbon\Carbon::parse($seniman->created_at)->translatedFormat('F Y');
                        }
                    @endphp

                    @if($cityName)
                    <div class="info-stat info-stat--box">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>{{ $cityName }}</span>
                    </div>
                    @endif

                    @if($joinedLabel)
                    <div class="info-stat info-stat--box">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Bergabung sejak {{ $joinedLabel }}</span>
                    </div>
                    @endif
                </div>

                {{-- Accordion Sections --}}
                <div class="accordion-sections">
                    @if($seniman->description)
                    <div class="accordion-item">
                        <button class="accordion-header" type="button" data-toggle="collapse" data-target="#experienceSection">
                            <span>Experience</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div id="experienceSection" class="accordion-content collapse">
                            <div class="accordion-body">
                                {!! $seniman->description !!}
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($seniman->art_projects)
                    <div class="accordion-item">
                        <button class="accordion-header" type="button" data-toggle="collapse" data-target="#artProjectsSection">
                            <span>Art Projects</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div id="artProjectsSection" class="accordion-content collapse">
                            <div class="accordion-body">
                                {!! $seniman->art_projects !!}
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($seniman->achievement)
                    <div class="accordion-item">
                        <button class="accordion-header" type="button" data-toggle="collapse" data-target="#achievementSection">
                            <span>Achievement</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div id="achievementSection" class="accordion-content collapse">
                            <div class="accordion-body">
                                {!! $seniman->achievement !!}
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($seniman->exhibition)
                    <div class="accordion-item">
                        <button class="accordion-header" type="button" data-toggle="collapse" data-target="#exhibitionSection">
                            <span>Exhibition</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div id="exhibitionSection" class="accordion-content collapse">
                            <div class="accordion-body">
                                {!! $seniman->exhibition !!}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
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
        
        @if($productsData->count() > 0)
            <div class="row">
                @foreach($productsData as $product)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100">
                            @php
                                $imagePath = $product['image_utama'] ?? 'assets/img/default.jpg';
                            @endphp
                            <a href="{{ route('detail', ['slug' => $product['slug']]) }}" class="text-decoration-none text-reset position-relative">
                                <img src="{{ asset($imagePath) }}" class="card-img-top" alt="{{ $product['title'] }}" onerror="this.src='{{ asset('assets/img/default.jpg') }}'">
                                <div class="product-overlay">
                                    <i class="fas fa-eye"></i>
                                </div>
                            </a>
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="{{ route('detail', ['slug' => $product['slug']]) }}" class="text-decoration-none text-dark">{{ $product['title'] }}</a>
                                </h5>
                                <!--<p class="card-text">Rp {{ number_format($product['price'], 0, ',', '.') }}</p>-->
                                <a href="{{ route('detail', ['slug' => $product['slug']]) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-shopping-cart"></i> Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @php $paginator = $products ?? $productsData ?? null; @endphp
            @if($paginator && method_exists($paginator, 'links'))
                <div class="d-flex justify-content-center mt-4">
                    {{ $paginator->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-palette"></i>
                </div>
                <h5 class="empty-state-title">Belum Ada Karya</h5>
                <p class="empty-state-text">Seniman ini belum mengunggah karya atau produk.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Accordion functionality
    const accordionHeaders = document.querySelectorAll('.accordion-header');
    
    accordionHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const targetContent = document.querySelector(targetId);
            const icon = this.querySelector('i');
            
            // Toggle collapse
            if (targetContent.classList.contains('show')) {
                targetContent.classList.remove('show');
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            } else {
                // Close all other accordions
                document.querySelectorAll('.accordion-content.show').forEach(content => {
                    content.classList.remove('show');
                });
                document.querySelectorAll('.accordion-header i').forEach(i => {
                    i.classList.remove('fa-chevron-up');
                    i.classList.add('fa-chevron-down');
                });
                
                // Open clicked accordion
                targetContent.classList.add('show');
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            }
        });
    });
});
</script>
@endpush