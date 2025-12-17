@extends('web.partials.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/checkout/checkout.css') }}">
@endsection

@section('content')

    <div class="container py-5">

        <h2 class="fw-bold mb-4">Checkout</h2>

        <form action="{{ route('checkout.process') }}" method="POST">
            @csrf

            {{-- 1. Mengirim ID barang mana saja yang dicentang user --}}
            <input type="hidden" name="selected_item_ids" value="{{ json_encode($selectedItemIds) }}">

            {{-- 2. Mengirim status apakah user memilih bungkus kado --}}
            <input type="hidden" name="is_gift_wrap" value="{{ $isGiftWrap ? '1' : '0' }}">

            <input type="hidden" name="selected_district_id" id="selected_district_id"
                value="{{ $selectedAddress->district_id ?? '' }}">

            <div class="row">

                <!-- LEFT SIDE -->
                <div class="col-md-7">

                    {{-- ADDRESS --}}
                    <div class="address-wrapper">
                        <div class="address-left">
                            <h6 class="title-address">Alamat Pengiriman</h6>

                            @php
                                $selectedAddress = session('checkout_address', $addresses->first());
                                $destination = $selectedAddress
                                    ? ['district_id' => $selectedAddress->district_id]
                                    : null;
                                $totalWeight = collect($cart ?? [])->sum(function ($item) {
                                    $weight = data_get($item, 'weight', 1000);
                                    $qty = data_get($item, 'quantity', 1);
                                    return $weight * $qty;
                                });
                            @endphp

                            <div id="checkout-selected-address">
                                @if ($selectedAddress)
                                    <div class="address-row">
                                        @if ($selectedAddress->is_primary ?? false)
                                            <span class="label-utama">Utama</span><br>
                                        @endif
                                        <div class="address-name">
                                            {{ $selectedAddress->name }}
                                        </div>
                                        <span class="address-separator">|</span>
                                        <div class="address-phone mt-1">
                                            {{ $selectedAddress->phone }}
                                        </div>
                                    </div>
                                    <div class="address-detail mt-1">
                                        {{ $selectedAddress->address }},
                                        {{ $selectedAddress->district->name ?? '-' }},
                                        {{ $selectedAddress->city->name ?? '-' }},
                                        {{ $selectedAddress->province->name ?? '-' }}
                                    </div>
                                @else
                                    <span class="text-muted">Kamu belum punya alamat.</span>
                                @endif
                            </div>
                        </div>
                        <div class="address-right">
                            <button type="button" class="btn-change-address mt-3" data-bs-toggle="modal"
                                data-bs-target="#addressModal">
                                Ganti
                            </button>
                        </div>
                    </div>

                    {{-- SHIPPING OPTION --}}
                    <div class="shipping-box">
                        <div class="shipping-title">Opsi Pengiriman</div>

                        {{-- Self Pick-Up --}}
                        <label class="shipping-item">
                            <div class="shipping-left">
                                <div class="shipping-subtitle">Self Pick-Up</div>
                                <div class="shipping-desc">
                                    Pilih opsi Self Pick Up jika ingin mengambil pesanan langsung di toko kami.
                                    Anda akan mendapat konfirmasi ketika pesanan siap diambil. Harap tunjukkan bukti
                                    pemesanan saat pengambilan.
                                </div>
                            </div>

                            <input type="radio" name="shipping_method" value="pickup" class="shipper-radio-custom"
                                id="radioPickup">
                        </label>

                        <div id="pickup-info" class="pickup-info" style="display:none;">
                            <strong>Alamat Rasa Lelang Karya</strong><br>
                            GRIYA SEKAR GADING BLOK C NOMOR 19<br>
                            Jam Operasional: 09.00–21.00
                        </div>

                        {{-- Delivery Service --}}
                        <label class="shipping-item">
                            <div class="shipping-left">
                                <div class="shipping-subtitle">Delivery Service</div>
                                <div class="shipping-desc">
                                    Pesanan akan dikirim ke alamat tujuan menggunakan jasa kurir pilihan kami.
                                    Estimasi waktu pengiriman mengikuti area masing-masing.
                                </div>

                                <button type="button" class="btn btn-kurir" id="btnPilihKurir" style="display:none;"
                                    data-bs-toggle="modal" data-bs-target="#shipperModal">
                                    Pilih Kurir
                                </button>

                                <div id="selected-shipper" class="selected-shipper">
                                    {{-- Input Hidden untuk menyimpan ID Kurir yang dipilih --}}
                                    <input type="hidden" name="selected_shipper_id" id="selected_shipper_id">
                                    {{-- Input Hidden untuk menyimpan harga ongkir (agar bisa ditangkap request) --}}
                                    <input type="hidden" name="total_ongkir" id="input_total_ongkir" value="0">
                                    <input type="hidden" name="jenis_ongkir" id="input_jenis_ongkir" value="Reguler">
                                </div>
                            </div>

                            <input type="radio" name="shipping_method" value="delivery" class="shipper-radio-custom"
                                id="radioDelivery">
                        </label>
                    </div>

                    {{-- NOTE --}}
                    <div class="note-wrapper mb-4" id="noteWrapper">
                        <div class="note-header">
                            <div class="note-title">
                                <img src="/icons/edit.svg" class="note-icon" alt="note icon">
                                <span>Catatan</span>
                            </div>
                            <span class="note-count">0/200</span>
                        </div>
                        <textarea name="note" id="noteArea" class="note-input hidden" maxlength="200" placeholder="Tulis catatan..."></textarea>
                    </div>
                </div>

                <!-- RIGHT SIDE -->
                <div class="col-md-5">

                    {{-- PRODUCT SUMMARY --}}
                    <div class="checkout-card">
                        <div class="checkout-body">
                            @foreach ($cart as $item)
                                <div class="checkout-item">
                                    <img src="{{ $item['image'] ?? '/img/default.png' }}" width="90" height="90"
                                        class="rounded border me-3">
                                    <div class="checkout-item-info">
                                        <div class="checkout-item-name">
                                            {{ $item['name'] }}
                                        </div>
                                        <div class="checkout-item-variant">
                                            @if (!empty($item['variant_name']))
                                                {{ $item['variant_name'] }},
                                            @endif

                                            @if (!empty($item['size_name']))
                                                {{ $item['size_name'] }}
                                            @endif
                                        </div>
                                        <div class="checkout-item-row">
                                            <div class="checkout-item-sub">
                                                Rp {{ number_format($item['price'], 0, ',', '.') }}
                                            </div>
                                            <div class="checkout-item-sub">
                                                x{{ $item['quantity'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <hr class="checkout-divider">

                            <div class="checkout-row">
                                <span class="label">Subtotal</span>
                                <span class="value">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>

                            <div class="checkout-row">
                                <span class="label">Pengiriman</span>
                                <span class="value" id="shipping_price">Rp 0</span>
                            </div>

                            <div class="checkout-row total">
                                <span class="label">Total</span>
                                <span class="value" id="total_price">Rp
                                    {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- PAYMENT METHOD --}}
                    <div class="payment-box">
                        <div class="payment-left" id="selectedPayment">
                            <img id="paymentIcon" src="/uploads/logos/qris.png" width="55" class="payment-logo">
                            <div class="payment-text">
                                <span id="paymentName" class="payment-title">Pilih Metode Pembayaran</span>
                                <div id="paymentFee" class="payment-fee">Biaya: Rp -</div>
                            </div>
                        </div>
                        <button type="button" class="payment-btn" data-bs-toggle="modal"
                            data-bs-target="#paymentModal">Ganti</button>
                    </div>

                    <button type="submit" class="paynow-btn" formaction="{{ route('checkout.pay') }}">
                        Pay Now
                    </button>
                </div>

            </div>
        </form>
    </div>

    {{-- Include modal --}}
    @include('web.checkout.modals-alamat', ['addresses' => $addresses, 'province' => $province])
    @include('web.checkout.modals-tambah-alamat', ['province' => $province])
    @include('web.checkout.modals-shipper', [
        'selectedAddress' => $selectedAddress,
        'cart' => $cart,
        'subtotal' => $subtotal,
    ])
    @include('web.checkout.modals-pembayaran')

@endsection

@section('js')
    <script>
        window.checkout = window.checkout || {};
        window.checkout.destination = {{ $selectedAddress->district_id ?? 'null' }};
        window.checkout.totalWeight =
            {{ collect($cart ?? [])->sum(fn($item) => data_get($item, 'weight', 1000) * data_get($item, 'quantity', 1)) }};
        window.checkout.subtotal = {{ $subtotal }};

        // --- JS untuk toggle shipping, note, pilih kurir ---
        document.addEventListener("DOMContentLoaded", function() {
            const radioPickup = document.getElementById('radioPickup');
            const radioDelivery = document.getElementById('radioDelivery');
            const pickupInfo = document.getElementById('pickup-info');
            const btnPilihKurir = document.getElementById('btnPilihKurir');
            const selectedShipperDiv = document.getElementById('selected-shipper');
            const shippingPriceEl = document.getElementById('shipping_price');
            const totalPriceEl = document.getElementById('total_price');
            const subtotal = window.checkout.subtotal;

            radioPickup.addEventListener('change', function() {
                pickupInfo.style.display = 'block';
                btnPilihKurir.style.display = 'none';
                selectedShipperDiv.style.display = 'none';
                shippingPriceEl.innerText = 'Rp 0';
                totalPriceEl.innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
            });
            radioDelivery.addEventListener('change', function() {
                pickupInfo.style.display = 'none';
                btnPilihKurir.style.display = 'inline-block';
            });

            btnPilihKurir?.addEventListener("click", function() {
                if (!window.checkout.destination) return;
                window.SHIPPER_DATA = {
                    origin: 5592,
                    destination: window.checkout.destination,
                    weight: window.checkout.totalWeight
                };
            });

            window.selectShipper = function(name, price, id) {
                const selectedShipperDiv = document.getElementById('selected-shipper');
                const shippingPriceEl = document.getElementById('shipping_price');
                const totalPriceEl = document.getElementById('total_price');
                const subtotal = window.checkout?.subtotal || 0;

                if (selectedShipperDiv) {
                    selectedShipperDiv.innerHTML = `${name} – Rp ${price.toLocaleString('id-ID')}`;
                    selectedShipperDiv.style.display = "block";
                }

                if (shippingPriceEl) shippingPriceEl.innerText = 'Rp ' + price.toLocaleString('id-ID');
                if (totalPriceEl) totalPriceEl.innerText = 'Rp ' + (subtotal + Number(price)).toLocaleString(
                    'id-ID');

                const radioDelivery = document.getElementById('radioDelivery');
                if (radioDelivery) radioDelivery.checked = true;

                const input = document.getElementById("selected_shipper_id");
                if (input) input.value = id;

                const inputOngkir = document.getElementById('input_total_ongkir');
                if (inputOngkir) inputOngkir.value = price;

                const modalEl = document.getElementById('shipperModal');
                bootstrap.Modal.getInstance(modalEl)?.hide();
            };

            // NOTE textarea toggle
            const wrapper = document.getElementById('noteWrapper');
            const textarea = document.getElementById('noteArea');
            const counter = wrapper.querySelector('.note-count');

            wrapper.addEventListener('click', function(e) {
                e.stopPropagation();
                wrapper.classList.add('open');
                textarea.classList.remove('hidden');
                setTimeout(() => textarea.classList.add('show'), 10);
                wrapper.style.justifyContent = "flex-start";
                textarea.focus();
            });

            document.addEventListener('click', e => {
                if (!wrapper.contains(e.target)) {
                    textarea.classList.remove('show');
                    setTimeout(() => textarea.classList.add('hidden'), 150);
                    wrapper.classList.remove('open');
                }
            });

            textarea.addEventListener('input', function() {
                counter.textContent = `${textarea.value.length}/200`;
            });

            // Event alamat dipilih
            document.addEventListener('alamatDipilih', function(e) {
                window.checkout.destination = e.detail.districtId;
                document.getElementById('selected_district_id').value = e.detail.districtId || '';
                selectedShipperDiv.style.display = 'none';
                selectedShipperDiv.innerHTML =
                    `<input type="hidden" name="selected_shipper_id" id="selected_shipper_id" value="">
                                        <input type="hidden" name="total_ongkir" id="input_total_ongkir" value="0">
                                        <input type="hidden" name="jenis_ongkir" id="input_jenis_ongkir" value="Reguler">`;
                shippingPriceEl.innerText = 'Rp 0';
                totalPriceEl.innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
            });

            document.querySelectorAll('.payment-option').forEach(option => {
                option.addEventListener('click', function() {
                    const name = this.dataset.name;
                    const icon = this.dataset.icon;
                    const fee = this.dataset.fee;

                    const paymentIcon = document.getElementById('paymentIcon');
                    const paymentName = document.getElementById('paymentName');
                    const paymentFee = document.getElementById('paymentFee');

                    if (paymentIcon) paymentIcon.src = icon;
                    if (paymentName) paymentName.innerText = name;
                    if (paymentFee) paymentFee.innerText =
                        `Biaya: Rp ${Number(fee).toLocaleString('id-ID')}`;

                    const modal = bootstrap.Modal.getInstance(document.getElementById(
                        'paymentModal'));
                    if (modal) modal.hide();
                });
            });
        });

        /* document.addEventListener("DOMContentLoaded", function () {
            let destinationAddress = {{ $defaultAddress->district_id ?? 'null' }};
            console.log("INIT DESTINATION:", destinationAddress);

            // pilih alamat → kirim ke session via AJAX
            const addressCards = document.querySelectorAll('.address-card input[type="radio"]');
            addressCards.forEach(card => {
                card.addEventListener("change", function() {
                    const parent = this.closest(".address-card");
                    const addressId = parent.dataset.id;

                    fetch("{{ route('checkout.set-address') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ address_id: addressId })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.status === "success") {
                            // UPDATE VARIABEL GLOBAL DESTINATION
                            destinationAddress = data.address.district_id;

                            console.log("DESTINATION DI SET:", destinationAddress);

                            console.log("ALAMAT DIPILIH:", destinationAddress);

                            // TRIGGER EVENT (optional)
                            document.dispatchEvent(new CustomEvent('alamatDipilih', {
                                detail: { districtId: data.address.district_id }
                            }));

                            // update tampilan checkout
                            const checkoutEl = document.getElementById("checkout-selected-address");
                            checkoutEl.innerHTML = `
                        <div class="address-card border border-primary rounded p-3">
                            <div class="address-row">
                                <div class="address-name">
                                    ${data.address.name}
                                </div>
                                <span class="address-separator">|</span>
                                <div class="address-phone mt-1">
                                    ${data.address.phone}
                                </div>
                            </div>
                            <div class="address-detail mt-1">
                                ${data.address.address},
                                ${data.address.district ?? '-'},
                                ${data.address.city ?? '-'},
                                ${data.address.province ?? '-'}
                            </div>
                        </div>
                    `;

                            document.querySelectorAll('.address-card input[type="radio"]').forEach(r => {
                                r.checked = (r.closest('.address-card').dataset.id == addressId);
                            });
                        }
                    });
                });
            });

            const radioPickup = document.getElementById('radioPickup');
            const radioDelivery = document.getElementById('radioDelivery');

            const pickupInfo = document.getElementById('pickup-info');
            const btnPilihKurir = document.getElementById('btnPilihKurir');
            const selectedShipperDiv = document.getElementById('selected-shipper');
            const shippingPriceEl = document.getElementById('shipping_price');
            const totalPriceEl = document.getElementById('total_price');

            const subtotal = {{ $subtotal }};

            const totalWeight = {{ $totalWeight }};

            // Toggle Pickup / Delivery
            radioPickup.addEventListener('change', function () {
                pickupInfo.style.display = 'block';
                btnPilihKurir.style.display = 'none';
                selectedShipperDiv.style.display = 'none';

                shippingPriceEl.innerText = 'Rp 0';
                totalPriceEl.innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
            });

            radioDelivery.addEventListener('change', function () {
                pickupInfo.style.display = 'none';
                btnPilihKurir.style.display = 'inline-block';

            });

            // Tombol Pilih Kurir → fetch daftar kurir
            btnPilihKurir?.addEventListener("click", function () {
                console.log("DESTINATION ADDRESS SAAT KLIK:", destinationAddress);

                if (!destinationAddress) {
                    alert("Pilih alamat tujuan dulu!");
                    return;
                }

                // Kirim data ke modal (window global)
                window.SHIPPER_DATA = {
                    origin: 5592,
                    destination: destinationAddress,
                    weight: totalWeight
                };
            });

            function loadShippers(destination, weight) {
                const shipperList = document.getElementById('shipper-list');
                const shipperLoading = document.getElementById('shipper-loading');

                shipperList.style.display = 'none';
                shipperLoading.style.display = 'block';
                shipperList.innerHTML = '';

                fetch('{{ route('checkout.shipping-cost') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        origin: 5592,
                        destination: destinationAddress,
                        weight: totalWeight,
                        price: "lowest"
                    })
                })
                .then(res => res.json())
                .then(data => {
                    shipperList.innerHTML = '';
                    shipperLoading.style.display = 'none';
                    shipperList.style.display = 'block';

                    if (!Array.isArray(data) || !data.length) {
                        shipperList.innerHTML = `<div class="text-center text-muted">Tidak ada kurir tersedia.</div>`;
                        return;
                    }

                    data.forEach(ship => {
                        const harga = ship.price ?? 0;
                        const div = document.createElement('div');
                        div.classList.add('border','rounded','p-3','mb-2','pointer');
                        div.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${ship.name}</strong><br>
                            <small class="text-muted">Estimasi: ${ship.eta ?? '-'}</small>
                        </div>
                        <div class="fw-bold">Rp ${harga.toLocaleString('id-ID')}</div>
                    </div>
                `;
                        div.onclick = function () {
                            selectShipper(ship.name, harga, ship.id);
                        };
                        shipperList.appendChild(div);
                    });
                })
                .catch(err => {
                    console.error(err);
                    shipperList.innerHTML = `<div class="text-center text-danger">Gagal memuat kurir.</div>`;
                    shipperLoading.style.display = 'none';
                    shipperList.style.display = 'block';
                });
            }
            // FUNGSI PILIH KURIR
            window.selectShipper = function (name, price, id) {

                selectedShipperDiv.innerHTML = `${name} – Rp ${price.toLocaleString('id-ID')}`;
                selectedShipperDiv.style.display = "block";

                shippingPriceEl.innerText = 'Rp ' + price.toLocaleString('id-ID');
                totalPriceEl.innerText = 'Rp ' + (subtotal + Number(price)).toLocaleString('id-ID');

                document.getElementById('radioDelivery').checked = true;

                let input = document.getElementById("selected_shipper_id");
                if (!input) {
                    input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'shipper_id';
                    input.id = 'selected_shipper_id';
                    document.querySelector('form').appendChild(input);
                }
                input.value = id;

                const modalEl = document.getElementById('shipperModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            }

            const wrapper = document.getElementById('noteWrapper');
            const textarea = document.getElementById('noteArea');
            const counter = wrapper.querySelector('.note-count');

            // KLIK WRAPPER → MUNCUL
            wrapper.addEventListener('click', function(e) {
                e.stopPropagation();

                wrapper.classList.add('open');
                textarea.classList.remove('hidden');
                setTimeout(() => textarea.classList.add('show'), 10);

                wrapper.style.justifyContent = "flex-start";
                textarea.focus();
            });

            // KLIK LUAR → TUTUP
            document.addEventListener('click', function(e) {
                if (!wrapper.contains(e.target)) {
                    textarea.classList.remove('show');
                    setTimeout(() => textarea.classList.add('hidden'), 150);
                    wrapper.classList.remove('open');
                }
            });

            // COUNTER
            textarea.addEventListener('input', function () {
                counter.textContent = `${textarea.value.length}/200`;
            });

            document.querySelectorAll('.payment-option').forEach(option => {
                option.addEventListener('click', function () {

                    const name = this.dataset.name;
                    const icon = this.dataset.icon;
                    const fee  = this.dataset.fee;

                    document.getElementById('paymentIcon').src = icon;
                    document.getElementById('paymentName').innerText = name;
                    document.getElementById('paymentFee').innerText = `Biaya: Rp ${fee}`;

                    const modal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
                    modal.hide();
                });
            });

        });

        // LISTENER: Kalau alamat dipilih di modal alamat
        document.addEventListener('alamatDipilih', function(e) {
            destinationAddress = e.detail.districtId;

            // Reset shipper jika sebelumnya sudah dipilih
            const selectedShipperDiv = document.getElementById('selected-shipper');
            selectedShipperDiv.style.display = 'none';
            selectedShipperDiv.innerHTML = '';

            document.getElementById('shipping_price').innerText = 'Rp 0';

            const subtotal = {{ $subtotal }};
            document.getElementById('total_price').innerText =
                'Rp ' + subtotal.toLocaleString('id-ID');
        }); */
    </script>
@endsection
