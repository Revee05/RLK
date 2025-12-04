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
                                $mainIndex = $mainIndex >= 0 && $mainIndex < $count ? $mainIndex : 0;
                                $main = $images->get($mainIndex);
                                $thumbsArr = [];
                                foreach ($desiredThumbs as $d) {
                                    if (count($thumbsArr) >= $thumbLimit) {
                                        break;
                                    }
                                    $pos = ($d - 1) % $count;
                                    if ($pos < 0) {
                                        $pos += $count;
                                    }
                                    if ($pos === $mainIndex) {
                                        continue;
                                    }
                                    if (!in_array($pos, array_keys($thumbsArr))) {
                                        $thumbsArr[$pos] = $images->get($pos);
                                    }
                                }
                                if (count($thumbsArr) < $thumbLimit) {
                                    for ($i = 1; $i <= $count && count($thumbsArr) < $thumbLimit; $i++) {
                                        $pos = ($mainIndex + $i) % $count;
                                        if ($pos === $mainIndex) {
                                            continue;
                                        }
                                        if (!array_key_exists($pos, $thumbsArr)) {
                                            $thumbsArr[$pos] = $images->get($pos);
                                        }
                                    }
                                }
                                $thumbs = collect(array_values($thumbsArr))->slice(0, $thumbLimit);
                            }
                        @endphp

                        {{-- Render main --}}
                        <div class="main-box">
                            @if ($main)
                                <img id="mainDisplay" src="{{ asset($main->path) }}" data-src="{{ asset($main->path) }}"
                                    alt="Main image" data-index="0" />
                            @else
                                <img id="mainDisplay" src="{{ asset('assets/default.jpg') }}"
                                    data-src="{{ asset('assets/default.jpg') }}" alt="Main image" data-index="0" />
                            @endif
                        </div>

                        {{-- Render thumbs --}}
                        <div class="thumb-row" id="thumbRow">
                            @foreach ($thumbs as $i => $img)
                                <div class="thumb-item" data-index="{{ $i + 1 }}" data-src="{{ asset($img->path) }}">
                                    <img src="{{ asset($img->path) }}" alt="thumb {{ $i + 1 }}">
                                </div>
                            @endforeach
                        </div>


                        <div class="details-section">
                            <h4>Deskripsi Produk</h4>
                            <div class="detail-desc">{!! $product->description !!}</div>

                            <div class="details-grid">
                                <div>
                                    <p class="label-teal">Stock</p>
                                    <p>{{ $product->stock ?? '-' }}</p>
                                    <p class="label-teal">Berat</p>
                                    <p>{{ $product->weight ?? '-' }}</p>
                                    <p class="label-teal">Ukuran</p>
                                    <p>{{ $product->long ?? '-' }} X {{ $product->width ?? '-' }} X
                                        {{ $product->height ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="label-teal">Kondisi</p>
                                    <p>{{ $product->kondisi ?? '-' }}</p>
                                    <p class="label-teal">Seniman</p>
                                    <p>{{ $product->karya->name ?? '-' }}</p>
                                </div>
                            </div>

                            <h4>Kelengkapan Produk</h4>
                            <div></div>
                        </div>


                        {{-- HISTORY / REALTIME --}}
                        <div class="history-box">
                            <div class="history-head">Riwayat Bidding</div>

                            @if (Auth::check())
                                {{-- Area Login: Menggunakan Vue --}}
                                {{-- Kita bungkus dengan ID agar bisa dimanipulasi CSS jika perlu --}}
                                <div id="chat-container" class="history-body" style="overflow-y: auto; max-height: 400px;">
                                    {{-- PERBAIKAN: pastikan props messages diisi dari variabel Vue 'messages' --}}
                                    <chat-messages :messages="messages"></chat-messages>
                                </div>

                                {{-- Component Form --}}
                                <chat-form ref="bidForm" 
                                    :user='@json(Auth::user())'
                                    :produk="{{ intval($product->id) }}"
                                    :kelipatan="{{ intval($product->kelipatan_bid ?? $product->kelipatan) }}"
                                    :price="{{ intval($product->price) }}" 
                                    v-on:messagesent="addMessage">
                                </chat-form>
                            @else
                                {{-- Area Guest: Menggunakan Blade biasa (Sudah Benar) --}}
                                <div class="history-body">
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
                            <p class="shipping-value">Melindungi produkmu dari risiko rusak maupun kerugian selama 6 bulan.
                            </p>
                        </div>

                        {{-- RELATED --}}
                        @if (isset($related) && count($related) > 0)
                            <div class="related-title">Related Products</div>
                            <div class="related-grid">
                                @foreach ($related as $r)
                                    <a href="{{ route('lelang.detail', $r->slug) }}" class="text-dark text-decoration-none">
                                        <div class="related-card">
                                            {{-- Safe Image Check --}}
                                            <img src="{{ asset($r->imageUtama ? $r->imageUtama->path : 'assets/img/default.jpg') }}" 
                                                 alt="related"
                                                 style="object-fit: cover;">
                                            
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
    {{-- ... (kode JS tetap sama seperti sebelumnya, tidak perlu diubah) ... --}}
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        window.productId = {{ intval($product->id) }};
        window.productSlug = "{{ $product->slug }}";
        window.initialHighest = {{ intval($highestBid) }};

        if (!window.productSlug || window.productSlug === "undefined") {
            console.warn("Product slug kosong! (dari Blade)");
        }
    </script>

    {{-- Script untuk update dropdown & fungsi bidding --}}
    <script>
        function formatRp(n) {
            return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function updateNominalDropdown(highest) {
            const step = {{ intval($product->kelipatan_bid) }};
            const select = document.getElementById('bidSelect');
            if (!select) return;
            select.innerHTML = '<option value="">Pilih Nominal Bid</option>';
            for (let i = 1; i <= 5; i++) {
                const val = highest + (step * i);
                const opt = document.createElement('option');
                opt.value = val;
                opt.textContent = 'Rp ' + formatRp(val);
                select.appendChild(opt);
            }
        }

        document.getElementById('btnBidNow').addEventListener('click', function() {
            const val = document.getElementById('bidSelect').value;
            if (!val) {
                alert('Pilih nominal bidding terlebih dahulu');
                return;
            }

            if (typeof app !== 'undefined' && app.$refs && app.$refs.bidForm) {
                const comp = app.$refs.bidForm;
                if (typeof comp.sendMessageFromButton === 'function') {
                    comp.sendMessageFromButton(val);
                    return;
                }
                if (typeof comp.sendBidFromButton === 'function') {
                    comp.sendBidFromButton(val);
                    return;
                }
                if (typeof comp.sendMessage === 'function') {
                    comp.newMessage = val;
                    comp.sendMessage();
                    return;
                }
            }

            // fallback
            axios.post('/bid/messages', {
                user: null,
                message: val,
                produk: window.productId
            }).then(function() {
                location.reload();
            }).catch(function(e) {
                console.error(e);
                alert('Gagal mengirim bid (fallback). Cek console.');
            });
        });

        @if (Auth::check())
            if (typeof Echo !== 'undefined') {
                Echo.private(`product.{{ $product->id }}`)
                    .listen('MessageSent', (e) => {
                        try {
                            if (typeof app !== 'undefined' && app.messages) {
                                console.log('MessageSent event', e);
                                let payload = null;
                                if (e.bid !== undefined) payload = e;
                                else if (e.message !== undefined) payload = e;
                                const pushData = {
                                    user: (e.user ? e.user : {
                                        name: (e.user_name || 'Unknown')
                                    }),
                                    message: (e.bid || e.message || e.price || ''),
                                    tanggal: (e.tanggal || e.created_at || (new Date()).toISOString())
                                };
                                app.messages.push(pushData);
                                const chat = document.getElementById('chat-container');
                                if (chat) chat.scrollTop = chat.scrollHeight;
                            }
                        } catch (err) {
                            console.error(err);
                        }
                    })
                    .listen('BidSent', (e) => {
                        console.log('BidSent event', e);
                        const price = Number(e.price || e);
                        if (!isNaN(price)) {
                            const highestEl = document.getElementById('highestPrice');
                            if (highestEl) highestEl.innerText = 'Rp ' + formatRp(price);
                            updateNominalDropdown(price);
                        }
                    });
            }
        @endif
    </script>

    {{-- Script untuk Image Slider --}}
    <script>
        (function() {
            function $(sel) { return document.querySelector(sel); }
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
            } catch (e) { console.warn('[countdown] clear prev failed', e); }

            const pad = n => (n < 10 ? '0' + n : n);
            function findEl() { return document.getElementById('mainCountdown'); }
            function readRaw() { const el = findEl(); return el ? el.dataset.end : null; }
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
                window.__auction_countdown = { endDate, interval, tick, destroy() { clearInterval(interval); } };
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
                    else { elNow.innerText = '--:--:--:--'; clearInterval(interval); }
                    return;
                }
                const now = new Date();
                let s = Math.floor((endDate - now) / 1000);
                if (s <= 0) {
                    elNow.innerText = '00:00:00:00';
                    clearInterval(interval);
                    return;
                }
                const d = Math.floor(s / 86400); s %= 86400;
                const h = Math.floor(s / 3600); s %= 3600;
                const m = Math.floor(s / 60);
                const sec = Math.floor(s % 60);
                elNow.innerText = `${pad(d)}:${pad(h)}:${pad(m)}:${pad(sec)}`;
            }

            const initialRaw = readRaw();
            lastRaw = initialRaw;
            if (initialRaw) {
                const p = tryParseIso(initialRaw);
                if (p && !isNaN(p.getTime())) { endDate = p; startTicking(); }
                else { startTicking(); }
            } else { startTicking(); }

            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'visible' && window.__auction_countdown && window.__auction_countdown.tick) {
                    window.__auction_countdown.tick();
                }
            }, false);
        })();
    </script>

    {{-- Init Data dari Controller ke JS Global Variable --}}
    <script>
        window.productId = {{ intval($product->id) }};
        window.productSlug = "{{ $product->slug }}";
        window.initialHighest = {{ intval($highestBid) }};
        
        // INI SOLUSI AGAR RIWAYAT MUNCUL:
        // Kita oper data JSON dari controller ke variabel window
        window.existingBids = @json($initialMessages ?? []); 
    </script>

    {{-- Load App JS --}}
    <script src="{{ asset('js/app.js') }}"></script>

    {{-- Script Tambahan untuk meng-inject data ke Vue Instance --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Cek apakah instance Vue 'app' sudah ada
            if (typeof app !== 'undefined') {
                // Masukkan data riwayat lama ke variable messages di Vue
                // Kita perlu me-reverse agar chat muncul dari atas ke bawah (opsional, tergantung CSS)
                // Biasanya chat/log itu array push, jadi data lama ada di index awal.
                
                // Pastikan format window.existingBids sesuai dengan struktur yang diharapkan Vue
                if(window.existingBids && window.existingBids.length > 0) {
                    // Karena di controller kita order desc (terbaru diatas), 
                    // tapi biasanya chat widget nambah ke bawah, kita reverse biar urut waktu
                    const sortedBids = window.existingBids.reverse(); 
                    
                    if(app.messages) {
                        app.messages = sortedBids;
                    } else {
                        // Jika app.messages belum terdefinisi (tergantung setup app.js Anda)
                        // Anda mungkin perlu setup manual atau menggunakan props di component root
                        console.warn('Property app.messages tidak ditemukan');
                    }
                }
            }
        });
    </script>
@endsection

