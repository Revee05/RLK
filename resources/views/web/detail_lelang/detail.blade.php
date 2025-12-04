@extends('web.partials.layout')

@section('css')
{{-- Memanggil CSS Eksternal --}}
<link href="{{ asset('css/lelang/detail.css') }}" rel="stylesheet">
@endsection

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<div id="app">
    <div class="auction-detail">
        <div class="auction-wrapper">
            <div class="auction-grid">
                {{-- LEFT --}}
                <div class="auction-left">
                    @php
                    $images = $product->images ?: collect([]);
                    $count = $images->count();
                    $mainIndex = 0;
                    $thumbLimit = 3;
                    $desiredThumbs = [3, 4, 5];
                    if ($count === 0) {
                    $main = null;
                    $thumbs = collect([]);
                    } else {
                    $mainIndex = $mainIndex >= 0 && $mainIndex < $count ? $mainIndex : 0; $main=$images->
                        get($mainIndex);
                        $thumbsArr = [];
                        foreach ($desiredThumbs as $d) {
                        if (count($thumbsArr) >= $thumbLimit) {
                        break;
                        }
                        $pos = ($d - 1) % $count;
                        if ($pos < 0) { $pos +=$count; } if ($pos===$mainIndex) { continue; } if (!in_array($pos,
                            array_keys($thumbsArr))) { $thumbsArr[$pos]=$images->get($pos);
                            }
                            }
                            if (count($thumbsArr) < $thumbLimit) { for ($i=1; $i <=$count && count($thumbsArr) <
                                $thumbLimit; $i++) { $pos=($mainIndex + $i) % $count; if ($pos===$mainIndex) { continue;
                                } if (!array_key_exists($pos, $thumbsArr)) { $thumbsArr[$pos]=$images->get($pos);
                                }
                                }
                                }
                                $thumbs = collect(array_values($thumbsArr))->slice(0, $thumbLimit);
                                }
                                @endphp

                                {{-- Render main --}}
                                <div class="main-box">
                                    @if ($main)
                                    <img id="mainDisplay" src="{{ asset($main->path) }}"
                                        data-src="{{ asset($main->path) }}" alt="Main image" data-index="0" />
                                    @else
                                    <img id="mainDisplay" src="{{ asset('assets/default.jpg') }}"
                                        data-src="{{ asset('assets/default.jpg') }}" alt="Main image" data-index="0" />
                                    @endif
                                </div>

                                {{-- Render thumbs --}}
                                <div class="thumb-row" id="thumbRow">
                                    @foreach ($thumbs as $i => $img)
                                    <div class="thumb-item" data-index="{{ $i + 1 }}"
                                        data-src="{{ asset($img->path) }}">
                                        <img src="{{ asset($img->path) }}" alt="thumb {{ $i + 1 }}">
                                    </div>
                                    @endforeach
                                </div>


                                <div class="details-section">
                                    <h4>Deskripsi Produk</h4>
                                    <div class="detail-desc">{!! $product->description !!}</div>

                                    <div class="details-grid">
                                        <div>
                                            <p class="label-teal">Material</p>
                                            <p>{{ $product->material ?? '-' }}</p>
                                            <p class="label-teal">Dimensi</p>
                                            <p>{{ $product->dimension ?? '-' }}</p>
                                            <p class="label-teal">Berat</p>
                                            <p>{{ $product->weight ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="label-teal">Tahun Karya</p>
                                            <p>{{ $product->year ?? '-' }}</p>
                                            <p class="label-teal">Seniman</p>
                                            <p>{{ $product->karya->name ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- HISTORY / REALTIME --}}
                                    <div class="history-box" style="background:#f8f9fa; border:1px solid #ddd; border-radius:8px; padding:16px;">
                                        <div class="history-head" style="background:#20c997; color:#fff; padding:8px 12px; border-radius:6px 6px 0 0; font-weight:bold;">Riwayat Bidding</div>

                                        @if (Auth::check())
                                        <div id="chat-container" class="history-body" style="overflow-y:auto; max-height:400px; min-height:120px; background:#fff; border:1px solid #eee; border-radius:6px; margin-bottom:12px; padding:8px;">
                                            <chat-messages :messages="messages"></chat-messages>
                                        </div>
                                        <chat-form ref="bidForm" :user='@json(Auth::user())' :produk="{{ intval($product->id) }}" :kelipatan="{{ intval($product->kelipatan_bid ?? $product->kelipatan) }}" :price="{{ intval($product->price) }}" v-on:messagesent="addMessage"></chat-form>
                                        @else
                                        <div class="history-body" style="overflow-y:auto; max-height:400px; min-height:120px; background:#fff; border:1px solid #eee; border-radius:6px; margin-bottom:12px; padding:8px;">
                                            @foreach ($bids as $b)
                                            <div class="history-item">
                                                <strong>{{ $b->user->name }}</strong>
                                                <span>Rp {{ number_format($b->price, 0, ',', '.') }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                        <a href="{{ url('/login') }}" class="btn btn-outline-secondary mt-2 w-100">Login untuk ikut bidding</a>
                                        @endif
                                    </div>

                                {{-- SHIPPING --}}
                                <div class="shipping-section" style="margin-top:30px; margin-left:30px;">
                                    <h4 class="shipping-title">Pengiriman Produk</h4>
                                    <p class="shipping-label">Pengiriman</p>
                                    <p class="shipping-value">Dikirim dari Semarang</p>

                                    <p class="shipping-label">Proteksi Kerusakan</p>
                                    <p class="shipping-value">Melindungi produkmu dari risiko rusak maupun kerugian
                                        selama 6 bulan.
                                    </p>
                                </div>

                                {{-- RELATED --}}
                                @if (isset($related) && count($related) > 0)
                                <div class="related-title">Related Products</div>
                                <div class="related-grid">
                                    @foreach ($related as $r)
                                    <a href="{{ route('lelang.detail', $r->slug) }}"
                                        class="text-dark text-decoration-none">
                                        <div class="related-card">
                                            {{-- Safe Image Check --}}
                                            <img src="{{ asset($r->imageUtama ? $r->imageUtama->path : 'assets/img/default.jpg') }}"
                                                alt="related" style="object-fit: cover;">

                                            <div class="related-name">{{ $r->title }}</div>
                                            <div class="related-price"> {{ $r->price_str }} </div>
                                        </div>
                                    </a>
                                    @endforeach
                                </div>
                                @endif
                </div>

                {{-- RIGHT COLUMN (Dipisah ke file bid_lelang.blade.php) --}}
                <div class="auction-right">
                    @include('web.detail_lelang.bid_lelang')
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
{{-- Pastikan variabel global di-define SEBELUM app.js --}}
<script>
    // PENTING: Variabel global HARUS didefinisikan SEBELUM app.js di-load
    window.productId = {{ intval($product->id) }};
    window.productSlug = "{{ $product->slug }}";
    window.initialHighest = {{ intval($highestBid) }};
</script>
<script src="{{ asset('js/app.js') }}"></script>

{{-- Script untuk update dropdown & fungsi bidding --}}
<script>
// Global functions agar bisa diakses dari Vue dan Echo listeners
window.formatRp = function(n) {
    return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

window.updateNominalDropdown = function(highest) {
    // Ambil kelipatan dari produk
    const rawStep = {{ intval($product->kelipatan) }};
    const step = Number(rawStep) || 10000;
    const h = Number(highest);
    const select = document.getElementById('bidSelect');
    if (!select) return;
    if (isNaN(h)) return;

    select.innerHTML = '<option value="">Pilih Nominal Bid</option>';
    for (let i = 1; i <= 5; i++) {
        const val = h + (step * i);
        const opt = document.createElement('option');
        opt.value = val;
        opt.textContent = 'Rp ' + window.formatRp(val);
        select.appendChild(opt);
    }
}

// Integrasi tombol bid kanan (di area produk) dengan Vue
function setupBidButtonListener() {
    var btn = document.getElementById('btnBidNow');
    var select = document.getElementById('bidSelect');
    if (!btn || !select) return;
    btn.disabled = false;
    btn.addEventListener('click', function() {
        var val = select.value;
        if (!val || isNaN(val) || Number(val) < 1) {
            alert('Pilih nominal bidding terlebih dahulu');
            return;
        }
        if (typeof window.app !== 'undefined' && window.app.$refs && window.app.$refs.bidForm && typeof window.app.$refs.bidForm.sendBidFromButton === 'function') {
            console.log('[BID] Kirim bid via Vue:', val);
            window.app.$refs.bidForm.sendBidFromButton(val);
        } else {
            alert('Bid gagal, Vue belum siap!');
            console.error('[BID] Vue instance atau ref bidForm belum siap', window.app);
        }
    });
}

// Tunggu sampai window.app (Vue) sudah siap
function waitForVueAndSetupBidBtn() {
    var btn = document.getElementById('btnBidNow');
    if (btn) btn.disabled = true;
    var tries = 0;
    var maxTries = 50;
    var timer = setInterval(function() {
        tries++;
        if (typeof window.app !== 'undefined' && window.app.$refs && window.app.$refs.bidForm) {
            clearInterval(timer);
            setupBidButtonListener();
        }
        if (tries > maxTries) {
            clearInterval(timer);
            if (btn) btn.disabled = false;
            console.error('[BID] Gagal menemukan Vue instance/ref bidForm setelah', maxTries, 'kali cek');
        }
    }, 100);
}

document.addEventListener('DOMContentLoaded', waitForVueAndSetupBidBtn);

// Initialize dropdown dengan harga tertinggi saat ini
document.addEventListener('DOMContentLoaded', function() {
    const initialHighest = {{ intval($highestBid) }};
    console.log('[Init] Setting initial dropdown with highest:', initialHighest);
    if (typeof window.updateNominalDropdown === 'function') {
        window.updateNominalDropdown(initialHighest);
    }
});


@if(Auth::check())
// Listener untuk update UI realtime tanpa reload
if (typeof Echo !== 'undefined') {
    console.log('[Echo] Mendengarkan channel product.{{ $product->id }}');
    
    const channel = Echo.private(`product.{{ $product->id }}`);
    
    // Monitor connection status
    Echo.connector.pusher.connection.bind('connected', () => {
        console.log('[Pusher] Connected to WebSocket');
    });
    
    Echo.connector.pusher.connection.bind('disconnected', () => {
        console.warn('[Pusher] Disconnected from WebSocket');
    });
    
    Echo.connector.pusher.connection.bind('error', (err) => {
        console.error('[Pusher] Connection error:', err);
    });
    
    // Listen to BidSent event for UI updates
    channel.listen('BidSent', (e) => {
        console.log('[BidSent] Event diterima:', e);
        const price = Number(e.price || e);
        if (isNaN(price)) {
            console.error('[BidSent] Invalid price:', e);
            return;
        }

        // 1. Update label harga tertinggi di sidebar kanan
        const highestEl = document.getElementById('highestPrice');
        if (highestEl) {
            console.log('[BidSent] Updating highestPrice to:', price);
            highestEl.innerText = 'Rp ' + window.formatRp(price);
            // Animasi highlight
            highestEl.style.transition = 'all 0.3s ease';
            highestEl.style.backgroundColor = '#fef3c7';
            setTimeout(() => {
                highestEl.style.backgroundColor = 'transparent';
            }, 800);
        } else {
            console.warn('[BidSent] Element #highestPrice tidak ditemukan');
        }

        // 2. Update dropdown nominal bid dengan harga terbaru
        console.log('[BidSent] Updating dropdown options');
        window.updateNominalDropdown(price);

        // 3. Update nilai next bid di Vue ChatForm
        if (window.app && window.app.$refs && window.app.$refs.bidForm) {
            const kelipatan = {{ intval($product->kelipatan_bid ?? $product->kelipatan) }};
            window.app.$refs.bidForm.newMessage = price + kelipatan;
            console.log('[BidSent] Updated ChatForm newMessage to:', price + kelipatan);
        }

        console.log('[BidSent] ✓ UI berhasil diupdate dengan harga:', price);
    });
    
    // Also listen to MessageSent for additional logging
    channel.listen('MessageSent', (e) => {
        console.log('[MessageSent] Event diterima di detail.blade.php:', e);
    });
    
    console.log('[Echo] ✓ Listener berhasil didaftarkan');
} else {
    console.error('[Echo] Echo is not defined! WebSocket tidak aktif.');
}
@endif
</script>

{{-- Script untuk Image Slider --}}
<script>
(function() {
    function $(sel) {
        return document.querySelector(sel);
    }

    function ensureDatas() {
        const main = $('#mainDisplay');
        if (main && !main.dataset.src) main.dataset.src = main.src;
        document.querySelectorAll('.thumb-item').forEach(t => {
            const img = t.querySelector('img');
            if (!t.dataset.src && img && img.src) {
                t.dataset.src = img.src;
            }
        });
    }

    function doSwap(mainImg, thumbElem) {
        const thumbImg = thumbElem.querySelector('img');
        if (!thumbImg) return;
        const mainSrc = mainImg.dataset.src || mainImg.src;
        const thumbSrc = thumbElem.dataset.src || thumbImg.src;
        if (mainSrc === thumbSrc) {
            document.querySelectorAll('.thumb-item').forEach(t => t.classList.remove('active'));
            thumbElem.classList.add('active');
            return;
        }
        mainImg.classList.add('fade-out');
        thumbImg.classList.add('fade-out');
        setTimeout(() => {
            mainImg.src = thumbSrc;
            mainImg.dataset.src = thumbSrc;
            thumbImg.src = mainSrc;
            thumbElem.dataset.src = mainSrc;
            document.querySelectorAll('.thumb-item').forEach(t => t.classList.remove('active'));
            thumbElem.classList.add('active');
            mainImg.classList.remove('fade-out');
            thumbImg.classList.remove('fade-out');
            mainImg.classList.add('fade-in');
            thumbImg.classList.add('fade-in');
            setTimeout(() => {
                mainImg.classList.remove('fade-in');
                thumbImg.classList.remove('fade-in');
            }, 240);
        }, 140);
    }
    document.addEventListener('click', function(e) {
        const thumbElem = e.target.closest('.thumb-item');
        if (!thumbElem) return;
        const mainImg = $('#mainDisplay');
        if (!mainImg) return;
        ensureDatas();
        doSwap(mainImg, thumbElem);
    });
})();
</script>

{{-- Script Countdown --}}
<script>
(function() {
    console.info('[countdown] init');
    try {
        if (window.__auction_countdown && window.__auction_countdown.interval) {
            clearInterval(window.__auction_countdown.interval);
        }
    } catch (e) {
        console.warn('[countdown] clear prev failed', e);
    }

    const pad = n => (n < 10 ? '0' + n : n);

    function findEl() {
        return document.getElementById('mainCountdown');
    }

    function readRaw() {
        const el = findEl();
        return el ? el.dataset.end : null;
    }

    function tryParseIso(s) {
        if (!s) return null;
        let t = String(s).trim();
        t = t.replace(' ', 'T');
        t = t.replace(/([+-]\d{2}):?(\d{2})$/, (m, hh, mm) => hh + mm);
        const d1 = new Date(t);
        if (!isNaN(d1.getTime())) return d1;
        const t2 = t.replace(/Z$/, '');
        const d2 = new Date(t2);
        if (!isNaN(d2.getTime())) return d2;
        return null;
    }

    let attempts = 0;
    const maxAttempts = 30;
    let endDate = null;
    let lastRaw = null;
    let interval = null;

    function startTicking() {
        if (interval) return;
        interval = setInterval(tick, 1000);
        window.__auction_countdown = {
            endDate,
            interval,
            tick,
            destroy() {
                clearInterval(interval);
            }
        };
    }

    function tick() {
        const elNow = findEl();
        if (!elNow) {
            attempts++;
            if (attempts > maxAttempts) clearInterval(interval);
            return;
        }
        const rawNow = elNow.dataset.end;
        if (rawNow && rawNow !== lastRaw) {
            lastRaw = rawNow;
            const parsed = tryParseIso(rawNow);
            if (parsed && !isNaN(parsed.getTime())) endDate = parsed;
        }
        if (!endDate) {
            attempts++;
            if (attempts <= maxAttempts) elNow.innerText = 'Memuat...';
            else {
                elNow.innerText = '--:--:--:--';
                clearInterval(interval);
            }
            return;
        }
        const now = new Date();
        let s = Math.floor((endDate - now) / 1000);
        if (s <= 0) {
            elNow.innerText = '00:00:00:00';
            clearInterval(interval);
            return;
        }
        const d = Math.floor(s / 86400);
        s %= 86400;
        const h = Math.floor(s / 3600);
        s %= 3600;
        const m = Math.floor(s / 60);
        const sec = Math.floor(s % 60);
        elNow.innerText = `${pad(d)}:${pad(h)}:${pad(m)}:${pad(sec)}`;
    }

    const initialRaw = readRaw();
    lastRaw = initialRaw;
    if (initialRaw) {
        const p = tryParseIso(initialRaw);
        if (p && !isNaN(p.getTime())) {
            endDate = p;
            startTicking();
        } else {
            startTicking();
        }
    } else {
        startTicking();
    }

    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible' && window.__auction_countdown && window
            .__auction_countdown.tick) {
            window.__auction_countdown.tick();
        }
    }, false);
})();
</script>

{{-- Init Data dari Controller ke JS Global Variable --}}
<script>
// INI SOLUSI AGAR RIWAYAT MUNCUL:
// Kita oper data JSON dari controller ke variabel window
window.existingBids = @json($initialMessages ?? []);
</script>

{{-- Script Tambahan untuk meng-inject data ke Vue Instance --}}
<script>
document.addEventListener("DOMContentLoaded", function() {
    if (typeof app !== 'undefined') {
        if (window.existingBids && window.existingBids.length > 0) {
            // Gunakan data apa adanya dari backend (sudah urut terbaru di atas)
            if (app.messages) {
                app.messages = window.existingBids;
            }
        }
    }
});
</script>

@endsection