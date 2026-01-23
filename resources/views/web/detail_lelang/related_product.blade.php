{{-- RELATED PRODUCTS --}}
@if (!empty($related) && count($related) > 0)
    <div class="related-title">Related products</div>

    <div class="related-grid-modern">
        @foreach ($related->take(2) as $r)
            <a href="{{ route('lelang.detail', $r->slug) }}" class="related-link">

                <div class="related-card-modern">

                    {{-- IMAGE --}}
                    <div class="related-image-wrap">
                        <img src="{{ asset($r->imageUtama ? $r->imageUtama->path : 'assets/img/default.jpg') }}"
                            alt="{{ $r->title }}">

                        {{-- TIMER BADGE --}}
                        <div class="related-timer" data-end="{{ $r->end_date->toIso8601String() }}">
                            --:--:--:--
                        </div>

                    </div>

                    {{-- CONTENT --}}
                    <div class="related-content">
                        <div class="related-name">{{ $r->title }}</div>
                        <div class="related-sub">Bidding Tertinggi:</div>
                        <div class="related-price">
                            Rp {{ number_format($r->highest_bid ?? $r->price, 0, ',', '.') }}
                        </div>
                    </div>

                </div>
            </a>
        @endforeach
    </div>

    {{-- BUTTON --}}
    <div class="related-more-wrap">
        <a href="{{ route('lelang') }}" class="btn-related-more">
            See More Product
        </a>
    </div>
@endif
