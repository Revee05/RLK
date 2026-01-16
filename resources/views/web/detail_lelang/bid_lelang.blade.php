{{-- File: resources/views/web/detail_lelang/bid_lelang.blade.php --}}

<div class="bid-sticky-container">
    <div class="bid-card-wrapper">
        <div class="bid-card-blur"></div>

        <div class="bid-card">
            <div class="bid-title">{{ $product->title }}</div>
            <div class="bid-sub">{{ $product->kategori->name ?? 'Kategori' }}</div>

            {{-- LOGIKA TAMPILAN BERDASARKAN STATUS --}}

            @if ($product->status == 1)
                {{-- KONDISI 1: LELANG MASIH JALAN --}}

                {{-- TIMER --}}
                <div class="timer-wrap">
                    <div class="timer-top">Waktu tersisa</div>
                    <div class="timer-body" data-role="countdown"
                        data-end="{{ $product->end_date ? $product->end_date->toIso8601String() : '' }}">
                        --:--:--:--
                    </div>
                </div>

                {{-- HIGHEST --}}
                <div class="highest-label">Bidding Tertinggi Saat Ini:</div>
                <div class="highest-price" data-role="highest-price">
                    Rp {{ number_format($highestBid, 0, ',', '.') }}
                </div>

                {{-- FORM BIDDING --}}
                @if (Auth::check())
                    <label class="form-label mt-3"><strong>Masukkan Bid Anda</strong></label>
                    <select data-role="bid-select" class="form-select">
                        <option value="">Pilih Nominal Bid</option>
                        @foreach ($nominals as $n)
                            <option value="{{ intval($n) }}">Rp {{ number_format(intval($n), 0, ',', '.') }}</option>
                        @endforeach
                    </select>

                    <button class="btn-bid-now" data-role="btn-bid">Bid Sekarang</button>
                @else
                    <div class="mt-4 text-center">
                        <p class="text-muted small">Login untuk mengikuti lelang ini</p>
                        <a href="{{ url('/login') }}" class="btn btn-outline-primary w-100">Login Sekarang</a>
                    </div>
                @endif
            @elseif($product->status == 2)
                {{-- KONDISI 2: SOLD / TERJUAL --}}

                <div
                    style="background: #d1e7dd; color: #0f5132; padding: 20px; border-radius: 8px; text-align: center; margin: 20px 0;">
                    <h2 style="font-weight: bold; margin:0;">TERJUAL</h2>
                    <small>Lelang Selesai</small>
                </div>

                <div class="highest-label">Terjual dengan Harga:</div>
                <div class="highest-price" style="color: #198754;">
                    Rp {{ number_format($highestBid, 0, ',', '.') }}
                </div>

                <button class="btn btn-success w-100 mt-3" disabled>Lelang Sudah Ditutup</button>
            @else
                {{-- KONDISI 3: EXPIRED / HANGUS --}}

                <div
                    style="background: #f8d7da; color: #842029; padding: 20px; border-radius: 8px; text-align: center; margin: 20px 0;">
                    <h2 style="font-weight: bold; margin:0;">CLOSED</h2>
                    <small>Waktu Habis (Tanpa Pemenang)</small>
                </div>

                <div class="highest-label">Harga Terakhir:</div>
                <div class="highest-price" style="color: #6c757d;">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </div>

                <button class="btn btn-secondary w-100 mt-3" disabled>Lelang Berakhir</button>
            @endif

        </div>
    </div>
</div>
