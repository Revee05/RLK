@extends('web.partials.layout')

@section('css')
    <style>
        /* Setting Global Style */
        html body,
        html body.auction-detail,
        body.auction-detail {
            background: #ffffff !important;
        }

        .auction-detail * {
            font-family: 'Helvetica', sans-serif !important;
        }

        /* Wrapper Luar */
        .auction-detail {
            padding-top: 40px;
            padding-bottom: 60px;
        }

        .auction-wrapper {
            width: 92%;
            max-width: 1280px;
            margin: auto;
        }

        /* Layout grid 2 kolom */
        .auction-grid {
            display: grid;
            grid-template-columns: 55% 1fr;
            gap: 32px;
            margin-top: 25px;
        }

        .auction-left {
            padding-right: 1.5em;
        }

        .auction-right {
            position: relative;
        }

        /* Image Utama dan Thumbs Style */
        .main-box img {
            border-radius: 12px;
            height: 560px;
            width: 100%;
            object-fit: cover;
        }

        .thumb-row {
            margin-top: 16px;
            display: flex;
            gap: 14px;
        }

        .thumb-item {
            width: 220px;
            height: 250px;
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
            border: 3px solid transparent;
        }

        .thumb-item:hover,
        .thumb-item.active {
            border-color: #63c6c9;
        }

        .thumb-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            cursor: pointer;
        }

        /* transisi saat swap */
        .main-box img,
        .thumb-item img {
            transition: opacity 220ms ease, transform 220ms ease;
            display: block;
        }

        .fade-out {
            opacity: 0;
            transform: scale(0.98);
        }

        .fade-in {
            opacity: 1;
            transform: scale(1);
        }

        .thumb-item.active {
            outline: 2px solid #63c6c9;
        }

        /* Bidding Style Kanan */
        .bid-sticky-container {
            position: relative;
            height: auto;
        }

        .bid-card-wrapper {
            position: sticky;
            top: 110px;
            margin-left: auto;
            z-index: 20;
        }

        .bid-card-blur {
            position: absolute;
            inset: 0;
            background: #fff;
            border-radius: 12px;
            filter: blur(7.6px);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.25);
            z-index: 1;
            margin-top: -10px;
        }

        .bid-card {
            position: relative;
            z-index: 2;
            width: 441px;
            margin: 30px auto 0;
            background: white;
            padding: 22px 0;
            border-radius: 12px;
        }

        .bid-title {
            font-size: 40px;
            font-weight: 700;
        }

        .bid-sub {
            font-size: 24px;
            color: #777;
        }

        .timer-wrap {
            border: 1px solid #dce7e7;
            border-radius: 10px;
            margin-top: 14px;
            overflow: hidden;
        }

        .timer-top {
            background: #63c6c9;
            color: white;
            padding: 10px;
            text-align: center;
        }

        .timer-body {
            text-align: center;
            padding: 18px;
            font-size: 36px;
            font-weight: 700;
        }

        .highest-label {
            font-size: 16px;
            margin-top: 20px;
            color: #777;
        }

        .highest-price {
            font-size: 36px;
            font-weight: 800;
            color: #0da7a0;
        }

        .btn-bid-now {
            width: 100%;
            margin-top: 14px;
            background: #0d223f;
            color: white;
            border-radius: 8px;
            padding: 12px;
        }

        /* Deskripsi Lelang */
        .details-section h4,
        .shipping-title {
            color: black;
            font-size: 24px;
            font-family: Helvetica;
            font-weight: 700;
            line-height: 36px;
            word-wrap: break-word
        }

        .shipping-label {
            color: #828282;
            font-size: 20px;
            font-family: Helvetica;
            font-weight: 700;
            line-height: 30px;
            word-wrap: break-word
        }

        .shipping-value {
            color: #828282;
            font-size: 20px;
            font-family: Helvetica;
            font-weight: 400;
            line-height: 30px;
            word-wrap: break-word
        }

        .details-section {
            margin-top: 60px;
            padding-left: 32px;
        }

        .detail-desc {
            font-size: 20px;
            text-align: justify;
            color: #828282;
        }

        .details-grid {
            display: flex;
            gap: 40px;
            margin-top: 12px;
            font-size: 20px;
            color: #828282;
        }

        .label-teal {
            color: #0da7a0;
            font-weight: 700;
        }

        .history-box {
            margin-top: 38px;
            margin-left: 30px;
        }

        .history-head {
            background: #63c6c9;
            color: white;
            padding: 10px;
            border-radius: 8px 8px 0 0;
            text-align: center;
            font-size: 24px;
        }

        .history-body {
            background: white;
            height: 300px;
            overflow-y: auto;
            padding: 10px;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 5px 14px rgba(0, 0, 0, 0.06);
        }

        /* Related Lelang */
        .related-title {
            margin-top: 40px;
            font-size: 32px;
            font-weight: 700;
        }

        .related-grid {
            display: flex;
            gap: 22px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .related-card {
            width: 230px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 16px rgba(0, 0, 0, 0.06);
            padding-bottom: 12px;
        }

        .related-card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 10px;
        }

        .related-name {
            padding: 10px;
            font-size: 18px;
            font-weight: 600;
        }

        .related-price {
            padding: 0 10px;
            font-size: 20px;
            font-weight: 700;
            color: #0da7a0;
        }
    </style>
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

<<<<<<< HEAD
                        {{-- Render thumbs --}}
                        <div class="thumb-row" id="thumbRow">
                            @foreach ($thumbs as $i => $img)
                                <div class="thumb-item" data-index="{{ $i + 1 }}" data-src="{{ asset($img->path) }}">
                                    <img src="{{ asset($img->path) }}" alt="thumb {{ $i + 1 }}">
=======
            {{-- Kolom tengah: detail produk --}}
            <div class="col col-mobile">
                {{-- Judul produk --}}
                <h1 class="single-title">{{$product->title}}</h1>
                {{-- Harga produk --}}
                <div class="text-danger single-price">{{$product->price_str}}</div>
                {{-- Kelipatan bid --}}
                <div class="d-flex caption-produk">
                  <div class="flex-grow1">Kelipatan Bid</div>
                  <div class="flex-grow2">{{$product->kelipatan_bid}}</div>
                </div>
                {{-- Informasi produk --}}
                <div class="pt-2 fw-bold">Informasi Produk</div>
                <div class="d-flex caption-produk">
                  <div class="flex-grow1">Berat</div>
                  <div class="flex-grow2">{{$product->weight}}</div>
                </div>
                <div class="d-flex caption-produk">
                  <div class="flex-grow1">Kondisi</div>
                  <div class="flex-grow2">{{$product->kondisi}}</div>
                </div>
                <div class="d-flex caption-produk">
                  <div class="flex-grow1">Kategori</div>
                  <div class="flex-grow2">
                    {{-- Link ke kategori --}}
                    <a href="{{route('products.category',$product->kategori->slug)}}" class="text-decoration-none text-dark">
                        {{$product->kategori->name}}
                    </a>
                  </div>
                </div>
                <div class="d-flex caption-produk">
                  <div class="flex-grow1">Seniman</div>
                  <div class="flex-grow2">
                    {{-- Link ke seniman --}}
                    <a href="{{route('products.seniman',$product->karya->slug)}}" class="text-decoration-none text-dark">
                        {{$product->karya->name}}
                    </a>
                  </div>
                </div>
                <div class="d-flex caption-produk">
                  <div class="flex-grow1">Tanggal berakhir</div>
                  <div class="flex-grow2">{{$product->end_date_indo}}</div>
                </div>
                {{-- Info pengiriman --}}
                <div class="row mt-4">
                  <div class="col-md-12 single-send fw-bold">
                    Pengiriman
                  </div>
                  <div class="col-md-12 single-city">
                    Dikirim dari <span>Semarang</span>
                  </div>
                </div>
                {{-- Info asuransi jika ada --}}
                @if($product->asuransi)
                <div class="row mt-2">
                  <div class="col-md-12 single-send fw-bold">
                    Proteksi Kerusakan
                  </div>
                  <div class="col-md-12 single-city">
                    melindungi produkmu dari risiko rusak maupun kerugian selama 6 bulan
                  </div>
                </div>
                @endif
                {{-- Tombol bid --}}
                <div class="d-block w-100 bid-button py-2">
                    <button type="button" class="btn btn-danger btn-block w-100" id="bid-button">Bid</button>
                </div>
            </div>

            {{-- Kolom kanan: panel bid/chat --}}
            <div class="col" id="bid-mobile">
                <div class="panel panel-default rounded-4">
                    {{-- Header panel bid/chat --}}
                    <div class="panel-heading panel-heading-mobile">
                        <div class="bid-foto">
                            <div class="d-flex p-2">
                                <div class="d-block px-2 text-white" id="bid-off">
                                    <i class="fa fa-times"></i>
>>>>>>> d7ee93efee9b0fa4e4b5ea5a6fab712e004c3718
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
                        <div class="history-box">
                            <div class="history-head">Riwayat Bidding</div>

                            @if (Auth::check())
                                <div id="chat-container" class="history-body">
                                    <!-- chat-messages component will render messages -->
                                    <chat-messages :messages="messages"></chat-messages>
                                </div>

                                <!-- chat-form receives numeric kelipatan & price (no formatted strings) -->
                                <chat-form ref="bidForm" :user='@json(Auth::user())'
                                    :produk="{{ intval($product->id) }}"
                                    :kelipatan="{{ intval($product->kelipatan_bid) }}"
                                    :price="{{ intval($product->price) }}" v-on:messagesent="addMessage"></chat-form>
                            @else
                                <div class="history-body">
                                    @foreach ($bids as $b)
                                        <div class="history-item">
                                            <strong>{{ $b->user->name }}</strong>
                                            <span>Rp {{ number_format($b->price, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                <a href="{{ url('/login') }}" class="btn btn-outline-secondary mt-2 w-100">Login untuk ikut
                                    bidding</a>
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
                        @if (count($related))
                            <div class="related-title">Related Products</div>
                            <div class="related-grid">
                                @foreach ($related as $r)
                                    <a href="{{ route('detail', $r->slug) }}" class="text-dark text-decoration-none">
                                        <div class="related-card">
                                            <img src="{{ asset($r->imageUtama->path) }}" alt="related">
                                            <div class="related-name">{{ $r->title }}</div>
                                            <div class="related-price"> {{ $r->price_str }} </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- RIGHT --}}
                    <div class="auction-right">
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

                                    {{-- SELECT --}}
                                    <label class="form-label mt-3"><strong>Masukkan Bid Anda</strong></label>
                                    <select id="bidSelect" class="form-select">
                                        <option value="">Pilih Nominal Bid</option>
                                        @foreach ($nominals as $n)
                                            <option value="{{ intval($n) }}">Rp
                                                {{ number_format(intval($n), 0, ',', '.') }}</option>
                                        @endforeach
                                    </select>

                                    {{-- BUTTON --}}
                                    <button class="btn-bid-now" id="btnBidNow">Bid Sekarang</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <<script src="{{ asset('js/app.js') }}"></script>
    {{-- expose some server values to JS --}}
    <script>
        window.productId = {{ intval($product->id) }};
        window.productSlug = "{{ $product->slug }}";
        window.initialHighest = {{ intval($highestBid) }};

        // Tambahan fix: cegah undefined
        if (!window.productSlug || window.productSlug === "undefined") {
            console.warn("Product slug kosong! (dari Blade)");
        }
    </script>

    <script>
        /* ======= helper: number formatter (ID) ======= */
        function formatRp(n) {
            return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        /* ======= update nominal dropdown using highest price ======= */
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

        /* ======= Bid button: call Vue component method (with fallback) ======= */
        document.getElementById('btnBidNow').addEventListener('click', function() {
            const val = document.getElementById('bidSelect').value;
            if (!val) {
                alert('Pilih nominal bidding terlebih dahulu');
                return;
            }

            // app should be the Vue root created in resources/js/app.js
            if (typeof app !== 'undefined' && app.$refs && app.$refs.bidForm) {
                const comp = app.$refs.bidForm;
                // preferred method names: sendMessageFromButton, sendBidFromButton, sendMessage
                if (typeof comp.sendMessageFromButton === 'function') {
                    comp.sendMessageFromButton(val);
                    return;
                }
                if (typeof comp.sendBidFromButton === 'function') {
                    comp.sendBidFromButton(val);
                    return;
                }
                if (typeof comp.sendMessage === 'function') {
                    // some chat-form uses sendMessage(): we set its newMessage then call sendMessage
                    comp.newMessage = val;
                    comp.sendMessage();
                    return;
                }
            }

            // fallback: submit via axios post to /bid/messages
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

        /* ======= Echo listeners ======= */
        @if (Auth::check())
            if (typeof Echo !== 'undefined') {
                Echo.private(`product.{{ $product->id }}`)
                    .listen('MessageSent', (e) => {
                        try {
                            if (typeof app !== 'undefined' && app.messages) {
                                console.log('MessageSent event', e);
                                // try multiple keys
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
                                // scroll chat container to bottom
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

            // Event Delegation (1 listener untuk semua thumbnails)
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



    <!-- Script countdown -->
    <script>
        (function() {
            // pastikan script ini dijalankan setelah app.js
            console.info('[countdown] init');

            // clear previous
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

                // common normalizations
                t = t.replace(' ', 'T'); // space -> T
                t = t.replace(/([+-]\d{2}):?(\d{2})$/, (m, hh, mm) => hh + mm); // +07:00 -> +0700

                // try native
                const d1 = new Date(t);
                if (!isNaN(d1.getTime())) return d1;

                // try if trailing Z missing and server time same TZ (assume local)
                const t2 = t.replace(/Z$/, '');
                const d2 = new Date(t2);
                if (!isNaN(d2.getTime())) return d2;

                // manual parse basic YYYY-MM-DDTHH:mm:ss
                const parts = t.match(/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):?(\d{2})?/);
                if (parts) {
                    return new Date(Number(parts[1]), Number(parts[2]) - 1, Number(parts[3]),
                        Number(parts[4] || 0), Number(parts[5] || 0), Number(parts[6] || 0));
                }

                return null;
            }

            // Retry finding element / dataset for up to N attempts (in case Vue renders late)
            let attempts = 0;
            const maxAttempts = 30; // ~3s
            let endDate = null;
            let lastRaw = null;
            let interval = null;

            function startTicking() {
                if (interval) return; // already running
                interval = setInterval(tick, 1000);
                window.__auction_countdown = {
                    endDate,
                    interval,
                    tick,
                    destroy() {
                        clearInterval(interval);
                    }
                };
                console.info('[countdown] started. endDate=', endDate && endDate.toString());
            }

            function tick() {
                const elNow = findEl();
                if (!elNow) {
                    // If element removed, keep trying to find it but don't crash
                    console.warn('[countdown] element #mainCountdown not present, waiting...');
                    attempts++;
                    if (attempts > maxAttempts) {
                        clearInterval(interval); // stop trying after some time
                        console.error('[countdown] giving up finding element after attempts=', attempts);
                    }
                    return;
                }

                // If dataset changed, reparse
                const rawNow = elNow.dataset.end;
                if (rawNow && rawNow !== lastRaw) {
                    lastRaw = rawNow;
                    const parsed = tryParseIso(rawNow);
                    if (parsed && !isNaN(parsed.getTime())) {
                        endDate = parsed;
                        console.info('[countdown] parsed endDate from dataset:', endDate.toISOString());
                    } else {
                        console.error('[countdown] failed to parse dataset.end:', rawNow);
                        // show a helpful message in UI so it's obvious
                        elNow.innerText = 'Error parsing end date';
                        return;
                    }
                }

                if (!endDate) {
                    // still no valid endDate yet
                    attempts++;
                    if (attempts <= maxAttempts) {
                        // try again next tick; show a waiting indicator
                        if (elNow) elNow.innerText = 'Memuat...';
                        return;
                    } else {
                        if (elNow) elNow.innerText = '--:--:--:--';
                        console.error('[countdown] no endDate after retries. lastRaw=', lastRaw);
                        clearInterval(interval);
                        return;
                    }
                }

                // normal counting
                const now = new Date();
                let s = Math.floor((endDate - now) / 1000);

                if (s <= 0) {
                    elNow.innerText = '00:00:00:00';
                    clearInterval(interval);
                    console.info('[countdown] reached zero, cleared interval');
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

            // Kick-off: try to read immediately. If not found, create a short probing interval to wait for Vue.
            const initialRaw = readRaw();
            lastRaw = initialRaw;
            if (initialRaw) {
                const p = tryParseIso(initialRaw);
                if (p && !isNaN(p.getTime())) {
                    endDate = p;
                    startTicking();
                } else {
                    console.warn('[countdown] initial parse failed for', initialRaw);
                    // still start ticking to allow retries which display helpful messages
                    startTicking();
                }
            } else {
                // element not present immediately — start interval to wait for it
                console.warn('[countdown] initial element/dataset not found; will retry for a short while');
                startTicking();
            }

            // Also respond to visibilitychange to immediately refresh when tab becomes active
            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'visible' && window.__auction_countdown && window
                    .__auction_countdown.tick) {
                    window.__auction_countdown.tick();
                }
            }, false);

        })();
    </script>
@endsection
