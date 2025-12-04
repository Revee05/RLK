{{-- File: resources/views/web/detail_lelang/bid_lelang.blade.php --}}

<div class="bid-sticky-container">
    <div class="bid-card-wrapper">
        <div class="bid-card-blur"></div>

        <div class="bid-card">
            <div class="bid-title">{{ $product->title }}</div>
            <div class="bid-sub">{{ $product->kategori->name }}</div>

            {{-- TIMER --}}
            <div class="timer-wrap">
                <div class="timer-top">Waktu tersisa</div>
                <div id="mainCountdown" class="timer-body"
                    data-end="{{ $product->end_date->toIso8601String() }}">
                    --:--:--:--
                </div>
            </div>

            {{-- HIGHEST --}}
            <div class="highest-label">Bidding Tertinggi Saat Ini:</div>
            <div class="highest-price" id="highestPrice">
                Rp {{ number_format($highestBid, 0, ',', '.') }}
            </div>

            {{-- Jika User Login: Tampilkan Form Bid --}}
            @if (Auth::check())
                <label class="form-label mt-3"><strong>Masukkan Bid Anda</strong></label>
                <select id="bidSelect" class="form-select">
                    <option value="">Pilih Nominal Bid</option>
                    @foreach ($nominals as $n)
                        <option value="{{ intval($n) }}">Rp
                            {{ number_format(intval($n), 0, ',', '.') }}</option>
                    @endforeach
                </select>

                <button class="btn-bid-now" id="btnBidNow">Bid Sekarang</button>
            {{-- Jika Belum Login: Tampilkan Tombol Login --}}
            @else
                <div class="mt-4 text-center">
                    <p class="text-muted small">Login untuk mengikuti lelang ini</p>
                    <a href="{{ url('/login') }}" class="btn btn-outline-primary w-100">Login Sekarang</a>
                </div>
            @endif
        </div>
    </div>
</div>