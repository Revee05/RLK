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

                        {{-- 🔽 BID UNTUK MOBILE (DI BAWAH IMAGE + THUMBS) --}}
                        <div class="d-block d-md-none mt-3">
                            @include('web.detail_lelang.bid_lelang')
                        </div>

                        <div class="details-section">
                            <h4>Deskripsi Produk</h4>
                            <div class="detail-desc">{!! $product->description !!}</div>

                            <div class="details-grid">
                                <div>
                                    <!-- <p class="label-teal">Material</p>
                                            <p>{{ $product->material ?? '-' }}</p> -->
                                    <!-- <p class="label-teal">Dimensi</p>
                                            <p>{{ $product->dimension ?? '-' }}</p> -->
                                    <p class="label-teal">Berat</p>
                                    <p>{{ $product->weight ?? '-' }} gr</p>
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
                        <div class="history-box"
                            style="background:#f8f9fa; border:1px solid #ddd; border-radius:8px; padding:16px;">
                            <div class="history-head"
                                style="background:#20c997; color:#fff; padding:8px 12px; border-radius:6px 6px 0 0; font-weight:bold;">
                                Riwayat Bidding
                            </div>

                            {{-- LOGIKA BARU: Cek Status Dulu --}}
                            @if ($product->status == 1)
                                {{-- KONDISI 1: LELANG MASIH JALAN (STATUS 1) --}}

                                @if (Auth::check())
                                    {{-- User Login: Tampilkan Chat & Form Bid --}}
                                    <div id="chat-container" class="history-body"
                                        style="overflow-y:auto; max-height:400px; min-height:120px; background:#fff; border:1px solid #eee; border-radius:6px; margin-bottom:12px; padding:8px;">
                                        <chat-messages :messages="messages"></chat-messages>
                                    </div>
                                    <chat-form ref="bidForm" :user='@json(Auth::user())'
                                        :produk="{{ intval($product->id) }}"
                                        :kelipatan="{{ intval($product->kelipatan ?? 10000) }}"
                                        :price="{{ intval($product->price) }}" v-on:messagesent="addMessage">
                                    </chat-form>
                                @else
                                    {{-- User Guest: Tampilkan List Bid Saja --}}
                                    <div class="history-body"
                                        style="overflow-y:auto; max-height:400px; min-height:120px; background:#fff; border:1px solid #eee; border-radius:6px; margin-bottom:12px; padding:8px;">
                                        @foreach ($bids as $b)
                                            <div class="history-item">
                                                <strong>{{ $b->user->name }}</strong>
                                                <span>Rp {{ number_format($b->price, 0, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    <a href="{{ url('/login') }}" class="btn btn-outline-secondary mt-2 w-100">Login untuk
                                        ikut bidding</a>
                                @endif
                            @elseif($product->status == 2)
                                {{-- KONDISI 2: SOLD / TERJUAL --}}
                                <div class="alert alert-success mt-3 text-center">
                                    <h4><i class="fa fa-trophy"></i> TERJUAL!</h4>
                                    <p>Lelang ini telah dimenangkan.</p>
                                    {{-- Menampilkan Pemenang Terakhir --}}
                                    @if ($bids->count() > 0)
                                        <div class="mt-2 p-2 bg-white rounded border">
                                            Pemenang: <strong>{{ $bids->first()->user->name ?? 'User' }}</strong><br>
                                            Harga Akhir: <strong>Rp
                                                {{ number_format($bids->first()->price, 0, ',', '.') }}</strong>
                                        </div>
                                    @endif
                                </div>

                                {{-- Tetap tampilkan riwayat chat/bid tapi read-only (tanpa form) --}}
                                <div class="history-body mt-2"
                                    style="overflow-y:auto; max-height:200px; background:#fff; border:1px solid #eee; border-radius:6px; padding:8px; opacity: 0.7;">
                                    @foreach ($bids as $b)
                                        <div class="history-item text-muted">
                                            <small>{{ $b->created_at->format('d M H:i') }}</small> -
                                            <strong>{{ $b->user->name }}</strong>: Rp
                                            {{ number_format($b->price, 0, ',', '.') }}
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                {{-- KONDISI 3: EXPIRED / GAGAL --}}
                                <div class="alert alert-danger mt-3 text-center">
                                    <h4><i class="fa fa-times-circle"></i> WAKTU HABIS</h4>
                                    <p>Lelang ini berakhir tanpa pemenang.</p>
                                </div>
                            @endif
                        </div>

                        {{-- SHIPPING --}}
                        <div class="shipping-section">
                            <h4 class="shipping-title">Pengiriman Produk</h4>
                            <p class="shipping-label">Pengiriman</p>
                            <p class="shipping-value">Dikirim dari Semarang</p>

                            <p class="shipping-label">Proteksi Kerusakan</p>
                            <p class="shipping-value">Melindungi produkmu dari risiko rusak maupun kerugian selama 6 bulan.
                            </p>
                        </div>

                        {{-- RELATED --}}
                        @if (isset($related) && count($related) > 0)
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
                                                <div class="related-timer">
                                                    {{ $r->remaining_time ?? '00:01:09:32' }}
                                                </div>
                                            </div>

                                            {{-- CONTENT --}}
                                            <div class="related-content">
                                                <div class="related-name">{{ $r->title }}</div>
                                                <div class="related-sub">Bidding Tertinggi:</div>
                                                <div class="related-price">Rp
                                                    {{ number_format($r->highest_bid ?? $r->price, 0, ',', '.') }}</div>
                                            </div>

                                        </div>
                                    </a>
                                @endforeach
                            </div>

                            {{-- BUTTON --}}
                            <div class="related-more-wrap">
                                <a href="{{ route('lelang.products.json') }}" class="btn-related-more">
                                    See More Product
                                </a>
                            </div>
                        @endif

                    </div>

                    {{-- RIGHT COLUMN (Dipisah ke file bid_lelang.blade.php) --}}
                    <div class="auction-right d-none d-md-block ">
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
        /**
         * Inisialisasi variabel global untuk digunakan di JS dan Vue.
         * - productId: ID produk yang sedang ditampilkan
         * - productSlug: slug produk
         * - initialHighest: harga bid tertinggi saat ini
         * - serverStep: kelipatan dari database
         * - serverNominals: daftar nominal bid dari server
         */
        window.productId = {{ intval($product->id) }};
        window.productSlug = "{{ $product->slug }}";
        window.initialHighest = {{ intval($highestBid) }};
        window.serverStep = {{ intval($step ?? 10000) }};
        window.serverNominals = @json($nominals ?? []);

        console.log('[INIT] Global vars:', {
            productId: window.productId,
            slug: window.productSlug,
            highest: window.initialHighest,
            step: window.serverStep,
            nominals: window.serverNominals
        });
    </script>
    <script src="{{ asset('js/app.js') }}"></script>

    {{-- Script untuk update dropdown & fungsi bidding --}}
    <script>
        // Hanya menyimpan behaviour tombol bid dan inisialisasi; helper (formatRp, updateNominalDropdown)
        // sekarang dipasang oleh bundled helper yang di-import di `app.js`.

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
                if (typeof window.app !== 'undefined' && window.app.$refs && window.app.$refs.bidForm &&
                    typeof window.app.$refs.bidForm.sendBidFromButton === 'function') {
                    console.log('[BID] Kirim bid via Vue:', val);
                    window.app.$refs.bidForm.sendBidFromButton(val);
                } else {
                    alert('Bid gagal, Vue belum siap!');
                    console.error('[BID] Vue instance atau ref bidForm belum siap', window.app);
                }
            });
        }

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

        // Event listener untuk inisialisasi dropdown dan tombol bid setelah DOM siap
        document.addEventListener('DOMContentLoaded', waitForVueAndSetupBidBtn);
        document.addEventListener('DOMContentLoaded', function() {
            const initialHighest = {{ intval($highestBid) }};
            const serverStep = window.serverStep || {{ intval($step ?? 10000) }};
            const serverNominals = window.serverNominals || @json($nominals ?? []);

            console.log('[Init] Setting initial dropdown', {
                highest: initialHighest,
                step: serverStep,
                nominals: serverNominals
            });

            if (typeof window.updateNominalDropdown === 'function') {
                // Kirim nominals dan step dari server agar sinkron dengan database
                window.updateNominalDropdown(initialHighest, serverNominals, serverStep);
            } else {
                console.warn('[Init] updateNominalDropdown not available yet');
            }
        });
    </script>

    {{-- Listener realtime bidding menggunakan Laravel Echo --}}
    <script>
        /**
         * Jika user sudah login, aktifkan listener realtime untuk update UI bidding.
         * - Mendengarkan event BidSent dan MessageSent dari channel Echo.
         * - Update harga tertinggi, dropdown nominal bid, dan nilai bid di Vue ChatForm.
         */
        @if (Auth::check())
            if (typeof Echo !== 'undefined') {
                console.log('[Echo] Mendengarkan channel product.{{ $product->id }}');
                const channel = Echo.private(`product.{{ $product->id }}`);

                Echo.connector.pusher.connection.bind('connected', () => {
                    console.log('[Pusher] Connected to WebSocket');
                });
                Echo.connector.pusher.connection.bind('disconnected', () => {
                    console.warn('[Pusher] Disconnected from WebSocket');
                });
                Echo.connector.pusher.connection.bind('error', (err) => {
                    console.error('[Pusher] Connection error:', err);
                });

                channel.listen('BidSent', (e) => {
                    // update highest (guarded formatter)
                    const highestEl = document.getElementById('highestPrice');
                    if (highestEl && typeof e.price !== 'undefined') {
                        const fmt = (typeof window.formatRp === 'function') ? window.formatRp : (v => String(v));
                        highestEl.innerText = 'Rp ' + fmt(Number(e.price));
                    }

                    // kalau server kirim nominals, rebuild select langsung — prefer helper if present
                    const select = document.getElementById('bidSelect');
                    if (select) {
                        if (typeof window.updateNominalDropdown === 'function') {
                            try {
                                window.updateNominalDropdown(Number(e.price) || 0, e.nominals || null, e.step ||
                                    null);
                            } catch (err) {
                                console.error('[Echo] updateNominalDropdown threw', err);
                            }
                        } else if (Array.isArray(e.nominals)) {
                            // fallback manual build using guarded formatter
                            const fmt = (typeof window.formatRp === 'function') ? window.formatRp : (v => String(
                                v));
                            select.innerHTML = '<option value="">Pilih Nominal Bid</option>';
                            e.nominals.forEach(v => {
                                const opt = document.createElement('option');
                                opt.value = Number(v);
                                opt.textContent = 'Rp ' + fmt(Number(v));
                                select.appendChild(opt);
                            });
                        }
                    }
                });

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
        /**
         * Script image slider:
         * - Memastikan data-src pada gambar utama dan thumbnail sudah terisi.
         * - Menukar gambar utama dengan thumbnail saat thumbnail diklik.
         * - Memberi efek animasi pada pergantian gambar.
         */
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
        /**
         * Script countdown:
         * - Menampilkan waktu mundur sampai lelang berakhir.
         * - Mengupdate tampilan setiap detik.
         * - Otomatis berhenti jika waktu habis atau element hilang.
         */
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

                // --- BAGIAN INI YANG DIUBAH (LOGIKA WAKTU HABIS) ---
                if (s <= 0) {
                    elNow.innerText = '00:00:00:00';
                    clearInterval(interval);

                    // 1. Matikan Tombol Bid
                    const btnBid = document.getElementById('btnBidNow');
                    if (btnBid) {
                        btnBid.disabled = true; // Kunci tombol
                        btnBid.innerText = "Waktu Habis"; // Ubah tulisan
                        btnBid.style.backgroundColor = "#6c757d"; // Ubah warna jadi abu-abu
                        btnBid.style.borderColor = "#6c757d";
                        btnBid.style.cursor = "not-allowed";
                    }

                    // 2. Matikan Dropdown Pilihan Harga
                    const selectBid = document.getElementById('bidSelect');
                    if (selectBid) {
                        selectBid.disabled = true; // Kunci dropdown
                    }

                    return;
                }
                // --- SELESAI PERUBAHAN ---

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
        /**
         * Menginisialisasi data bid awal dari backend ke variabel global JS.
         * Data ini digunakan untuk mengisi pesan bidding pada Vue Chat.
         */
        window.existingBids = @json($initialMessages ?? []);
    </script>

    {{-- Script Tambahan untuk meng-inject data ke Vue Instance --}}
    <script>
        /**
         * Setelah DOM siap, inject data existingBids ke instance Vue jika tersedia.
         * Memastikan data riwayat bid langsung muncul di chat tanpa reload.
         */
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof app !== 'undefined') {
                if (window.existingBids && window.existingBids.length > 0) {
                    if (app.messages) {
                        app.messages = window.existingBids;
                    }
                }
            }
        });
    </script>
@endsection
