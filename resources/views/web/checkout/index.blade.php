@extends('web.partials.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/checkout/checkout.css') }}">
@endsection

@section('content')

    <div class="container py-5">
        <h2 class="fw-bold mb-4">Checkout</h2>

        <form action="{{ route('checkout.process') }}" method="POST">
            @csrf

            {{-- Hidden Inputs untuk Data Checkout --}}
            <input type="hidden" name="selected_item_ids" value="{{ json_encode($selectedItemIds) }}">
            <input type="hidden" name="is_gift_wrap" value="{{ $isGiftWrap ? '1' : '0' }}">
            <input type="hidden" name="address_id" value="{{ $selectedAddress->id ?? '' }}">
            <input type="hidden" name="selected_district_id" id="selected_district_id" value="{{ $selectedAddress->district_id ?? '' }}">

            {{-- Hidden Inputs untuk Shipping Detail --}}
            <input type="hidden" name="selected_shipper_id" id="selected_shipper_id" value="">
            <input type="hidden" name="shipping_name" id="shipping_name">
            <input type="hidden" name="shipping_service" id="shipping_service">
            <input type="hidden" name="shipping_etd" id="shipping_etd">
            <input type="hidden" name="shipping_code" id="shipping_code">
            <input type="hidden" name="total_ongkir" id="input_total_ongkir" value="0">
            <input type="hidden" name="jenis_ongkir" id="input_jenis_ongkir" value="Regular">

            <div class="row">
                <div class="col-md-7">
                    {{-- ADDRESS SECTION --}}
                    <div class="address-wrapper">
                        <div class="address-left">
                            <h6 class="title-address">Alamat Pengiriman</h6>
                            <div id="checkout-selected-address">
                                @if ($selectedAddress)
                                    <div class="address-row">
                                        @if ($selectedAddress->is_primary)
                                            <span class="label-utama">Utama</span><br>
                                        @endif
                                        <div class="address-name">{{ $selectedAddress->name }}</div>
                                        <span class="address-separator">|</span>
                                        <div class="address-phone mt-1">{{ $selectedAddress->phone }}</div>
                                    </div>
                                    <div class="address-detail mt-1">
                                        {{ $selectedAddress->address }},
                                        {{ $selectedAddress->district->name ?? '-' }},
                                        {{ $selectedAddress->city->name ?? '-' }},
                                        {{ $selectedAddress->province->name ?? '-' }}
                                    </div>
                                @else
                                    <span class="text-muted">Kamu belum punya alamat. Silakan tambah alamat.</span>
                                @endif
                            </div>
                        </div>
                        <div class="address-right">
                            <button type="button" class="btn-change-address mt-3" data-bs-toggle="modal" data-bs-target="#addressModal">
                                Ganti
                            </button>
                        </div>
                    </div>

                    {{-- SHIPPING OPTIONS --}}
                    <div class="shipping-box mt-4">
                        <div class="shipping-title">Opsi Pengiriman</div>

                        {{-- Self Pick-Up --}}
                        <label class="shipping-item">
                            <div class="shipping-left">
                                <div class="shipping-subtitle">Self Pick-Up</div>
                                <div class="shipping-desc">Ambil langsung di toko kami. Konfirmasi akan dikirim saat siap.</div>
                            </div>
                            <input type="radio" name="shipping_method" value="pickup" class="shipper-radio-custom" id="radioPickup">
                        </label>

                        <div id="pickup-info" class="pickup-info" style="display:none;">
                            <strong>Alamat Toko:</strong><br>
                            GRIYA SEKAR GADING BLOK C NOMOR 19<br>
                            Jam Operasional: 09.00–21.00
                        </div>

                        {{-- Delivery Service --}}
                        <label class="shipping-item mt-2">
                            <div class="shipping-left">
                                <div class="shipping-subtitle">Delivery Service</div>
                                <div class="shipping-desc">Dikirim ke alamat tujuan menggunakan kurir (JNE, TIKI, POS).</div>
                                
                                <button type="button" class="btn btn-kurir mt-2" id="btnPilihKurir" style="display:none;" data-bs-toggle="modal" data-bs-target="#shipperModal">
                                    Pilih Kurir
                                </button>

                                <div id="selected-shipper" class="selected-shipper mt-2" style="display:none; font-weight: bold; color: #28a745;">
                                    {{-- Diisi via JS --}}
                                </div>
                            </div>
                            <input type="radio" name="shipping_method" value="delivery" class="shipper-radio-custom" id="radioDelivery">
                        </label>
                    </div>

                    {{-- NOTE SECTION --}}
                    <div class="note-wrapper mb-4 mt-4" id="noteWrapper">
                        <div class="note-header">
                            <div class="note-title">
                                <img src="/icons/edit.svg" class="note-icon" alt="note icon">
                                <span>Catatan Pesanan</span>
                            </div>
                            <span class="note-count">0/200</span>
                        </div>
                        <textarea name="note" id="noteArea" class="note-input hidden" maxlength="200" placeholder="Tulis catatan..."></textarea>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="checkout-card">
                        <div class="checkout-body">
                            @foreach ($cart as $item)
                                <div class="checkout-item">
                                    <img src="{{ $item['image'] ?? '/img/default.png' }}" width="80" height="80" class="rounded border me-3">
                                    <div class="checkout-item-info">
                                        <div class="checkout-item-name">{{ $item['name'] }}</div>
                                        <div class="checkout-item-variant text-muted small">
                                            {{ $item['variant_name'] ?? '' }} {{ $item['size_name'] ? '- '.$item['size_name'] : '' }}
                                        </div>
                                        <div class="checkout-item-row mt-1">
                                            <div class="checkout-item-sub">Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
                                            <div class="checkout-item-sub">x{{ $item['quantity'] }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <hr class="checkout-divider">

                            <div class="checkout-row">
                                <span class="label">Subtotal Produk</span>
                                <span class="value">Rp {{ number_format($subtotalBarang, 0, ',', '.') }}</span>
                            </div>

                            <div class="checkout-row gift-wrap-row" style="{{ $isGiftWrap ? '' : 'display:none;' }}">
                                <span class="label">Bungkus Kado</span>
                                <span class="value" id="gift_wrap_price">Rp {{ number_format($giftWrapPrice, 0, ',', '.') }}</span>
                            </div>

                            <div class="checkout-row">
                                <span class="label">Biaya Pengiriman</span>
                                <span class="value" id="shipping_price">Rp 0</span>
                            </div>

                            <div class="checkout-row total">
                                <span class="label">Total Pembayaran</span>
                                <span class="value" id="total_price">Rp {{ number_format($subtotal + $giftWrapPrice, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- PAYMENT BOX --}}
                    <div class="payment-box mt-3">
                        <div class="payment-left" id="selectedPayment">
                            <img id="paymentIcon" src="/uploads/logos/qris.png" width="40" class="payment-logo me-2">
                            <div class="payment-text">
                                <span id="paymentName" class="payment-title fw-bold">Pilih Pembayaran</span>
                                <div id="paymentFee" class="payment-fee small text-muted">Biaya: Rp -</div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#paymentModal">Ganti</button>
                    </div>

                    <button type="submit" class="paynow-btn mt-4 w-100">
                        Buat Pesanan
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Modals --}}
    @include('web.checkout.modals-alamat', ['addresses' => $addresses, 'province' => $province])
    @include('web.checkout.modals-tambah-alamat', ['province' => $province])
    @include('web.checkout.modals-shipper', [
        'selectedAddress' => $selectedAddress,
        'cart' => $cart,
    ])
    @include('web.checkout.modals-pembayaran')

@endsection

@section('js')
<script>
    // Konfigurasi Global dari PHP
    window.checkout = {
        destination: {{ $selectedAddress->district_id ?? 'null' }},
        totalWeight: {{ collect($cart)->sum(fn($item) => ($item['weight'] ?? 1000) * $item['quantity']) }},
        subtotal: {{ $subtotalBarang }},
        giftWrapCost: {{ $isGiftWrap ? 10000 : 0 }},
        currentShipping: 0
    };

    function formatIDR(number) {
        return 'Rp ' + Number(number).toLocaleString('id-ID');
    }

    function refreshTotal() {
        const total = window.checkout.subtotal + window.checkout.giftWrapCost + window.checkout.currentShipping;
        document.getElementById('total_price').innerText = formatIDR(total);
    }

    document.addEventListener("DOMContentLoaded", function () {
        const radioPickup = document.getElementById('radioPickup');
        const radioDelivery = document.getElementById('radioDelivery');
        const pickupInfo = document.getElementById('pickup-info');
        const btnPilihKurir = document.getElementById('btnPilihKurir');
        const selectedShipperDiv = document.getElementById('selected-shipper');
        const shippingPriceEl = document.getElementById('shipping_price');

        // Toggle Pickup
        radioPickup.addEventListener('change', function() {
            pickupInfo.style.display = 'block';
            btnPilihKurir.style.display = 'none';
            selectedShipperDiv.style.display = 'none';
            window.checkout.currentShipping = 0;
            shippingPriceEl.innerText = formatIDR(0);
            refreshTotal();
        });

        // Toggle Delivery
        radioDelivery.addEventListener('change', function() {
            pickupInfo.style.display = 'none';
            btnPilihKurir.style.display = 'inline-block';
            if(window.checkout.currentShipping > 0) selectedShipperDiv.style.display = 'block';
        });

        // Handler saat Kurir dipilih dari Modal
        window.selectShipper = function(name, code, service, description, cost, etd, id) {
            window.checkout.currentShipping = cost;
            shippingPriceEl.innerText = formatIDR(cost);
            
            selectedShipperDiv.innerText = `${name} (${service}) - ${formatIDR(cost)}`;
            selectedShipperDiv.style.display = "block";
            
            // Update Hidden Inputs
            document.getElementById('selected_shipper_id').value = id;
            document.getElementById('input_total_ongkir').value = cost;
            document.getElementById('shipping_name').value = name;
            document.getElementById('shipping_code').value = code;
            document.getElementById('shipping_service').value = service;
            document.getElementById('shipping_etd').value = etd;

            refreshTotal();
            bootstrap.Modal.getInstance(document.getElementById('shipperModal'))?.hide();
        };

        // Alamat dipilih listener
        document.addEventListener('alamatDipilih', function(e){
            window.checkout.destination = e.detail.districtId;
            document.getElementById('selected_district_id').value = e.detail.districtId;
            document.getElementById('address_id').value = e.detail.id; // Pastikan modal alamat kirim ID
            
            // Reset Ongkir
            window.checkout.currentShipping = 0;
            shippingPriceEl.innerText = formatIDR(0);
            selectedShipperDiv.style.display = 'none';
            refreshTotal();
        });

        // Note UI logic
        const wrapper = document.getElementById('noteWrapper');
        const textarea = document.getElementById('noteArea');
        wrapper.addEventListener('click', function(e){
            wrapper.classList.add('open');
            textarea.classList.remove('hidden');
            textarea.focus();
        });

        // Payment Option listener
        document.querySelectorAll('.payment-option').forEach(option => {
            option.addEventListener('click', function () {
                document.getElementById('paymentIcon').src = this.dataset.icon;
                document.getElementById('paymentName').innerText = this.dataset.name;
                document.getElementById('paymentFee').innerText = `Biaya: ${formatIDR(this.dataset.fee)}`;
                bootstrap.Modal.getInstance(document.getElementById('paymentModal'))?.hide();
            });
        });
    });
</script>
@endsection