@extends('web.partials.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/checkout/checkout.css') }}">
@endsection

@section('content')

    <div class="container py-5">

        <h2 class="fw-bold mb-4">Checkout</h2>

        <form action="{{ route('checkout.process') }}" method="POST">
            @csrf

            {{-- ID item yang dipilih --}}
            <input type="hidden" name="selected_item_ids" value="{{ json_encode($selectedItemIds) }}">
            
            {{-- Gift wrap --}}
            <input type="hidden" name="is_gift_wrap" value="{{ $isGiftWrap ? '1' : '0' }}">
        
            {{-- District tujuan --}}
            <input type="hidden" name="selected_district_id" id="selected_district_id" value="{{ $selectedAddress->district_id ?? '' }}">
            <input type="hidden" name="address_id" value="{{ $selectedAddress->id ?? '' }}">

            {{-- Shipping --}}
            <input type="hidden" name="selected_shipper_id" id="selected_shipper_id" value="">
            <input type="hidden" name="shipping_name" id="shipping_name">
            <input type="hidden" name="shipping_service" id="shipping_service">
            <input type="hidden" name="shipping_etd" id="shipping_etd">
            <input type="hidden" name="shipping_code" id="shipping_code">
            <input type="hidden" name="shipping_description" id="shipping_description">
            <input type="hidden" name="total_ongkir" id="input_total_ongkir">
        
            <div class="row">
                {{-- 1. Mengirim ID barang mana saja yang dicentang user --}}
                <input type="hidden" name="selected_item_ids" value="{{ json_encode($selectedItemIds) }}">

                {{-- 2. Mengirim status apakah user memilih bungkus kado --}}
                <input type="hidden" name="is_gift_wrap" value="{{ $isGiftWrap ? '1' : '0' }}">

                <input type="hidden" name="selected_district_id" id="selected_district_id" value="{{ $selectedAddress->district_id ?? '' }}">

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
                                            <div class="checkout-item-name">{{ $item['name'] }}</div>
                                            <div class="checkout-item-variant">
                                                @if (!empty($item['variant_name'])) {{ $item['variant_name'] }} @endif
                                                @if (!empty($item['size_name'])) ,{{ $item['size_name'] }} @endif
                                            </div>
                                            <div class="checkout-item-row">
                                                <div class="checkout-item-sub">Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
                                                <div class="checkout-item-sub">x{{ $item['quantity'] }}</div>
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

                                    @if ($isGiftWrap)
                                        <div class="checkout-row" id="giftWrapRow">
                                            <span class="label">Gift Wrap</span>
                                            <span class="value" id="gift_wrap_price">
                                                Rp {{ number_format(10000, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    @endif

                                    <div class="checkout-row total">
                                        <span class="label">Total</span>
                                        <span class="value" id="total_price">Rp {{ number_format($subtotal + ($isGiftWrap ? 10000 : 0), 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="paynow-btn">
                                Pay Now
                            </button>
                        </div>
                    </div>
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
@endsection

@section('js')

<script>
window.checkout = window.checkout || {};
window.checkout.destination = {{ $selectedAddress->district_id ?? 'null' }};
window.checkout.totalWeight = {{ collect($cart ?? [])->sum(fn($item)=>data_get($item,'weight',1000)*data_get($item,'quantity',1)) }};
window.checkout.subtotal = {{ $subtotal }};

window.checkout.giftWrap = {{ $isGiftWrap ? 'true' : 'false' }};
window.checkout.giftWrapCost = {{ $isGiftWrap ? 10000 : 0 }};

window.updateTotal = function(shippingCost, options = {}) {
    const shippingEl = options.shippingEl || document.getElementById('shipping_price');
    const giftWrapEl = options.giftWrapEl || document.getElementById('gift_wrap_price');
    const totalEl = options.totalEl || document.getElementById('total_price');

    const subtotal = window.checkout.subtotal;
    const giftWrap = window.checkout.giftWrap ? window.checkout.giftWrapCost : 0;

    if(shippingEl) shippingEl.innerText = 'Rp ' + Number(shippingCost).toLocaleString('id-ID');
    if(giftWrapEl) giftWrapEl.innerText = 'Rp ' + giftWrap.toLocaleString('id-ID');

    const total = subtotal + giftWrap + Number(shippingCost);
    if(totalEl) totalEl.innerText = 'Rp ' + total.toLocaleString('id-ID');
};

// --- JS untuk toggle shipping, note, pilih kurir ---
document.addEventListener("DOMContentLoaded", function () {
    const radioPickup = document.getElementById('radioPickup');
    const radioDelivery = document.getElementById('radioDelivery');
    const pickupInfo = document.getElementById('pickup-info');
    const btnPilihKurir = document.getElementById('btnPilihKurir');
    const selectedShipperDiv = document.getElementById('selected-shipper');
    const shippingPriceEl = document.getElementById('shipping_price');
    const totalPriceEl = document.getElementById('total_price');
    const subtotal = window.checkout.subtotal;
    const giftWrapEl = document.getElementById('gift_wrap_price');

    radioPickup.addEventListener('change', function() {
        pickupInfo.style.display = 'block';
        btnPilihKurir.style.display = 'none';
        selectedShipperDiv.style.display = 'none';
        shippingPriceEl.innerText = 'Rp 0';
        window.updateTotal(0);
    });
    radioDelivery.addEventListener('change', function() {
        pickupInfo.style.display = 'none';
        btnPilihKurir.style.display = 'inline-block';
    });

    btnPilihKurir?.addEventListener("click", function () {
        if (!window.checkout.destination) return;
        window.SHIPPER_DATA = {
            origin: 5592,
            destination: window.checkout.destination,
            weight: window.checkout.totalWeight
        };
    });

    window.selectShipper = function(name, code, service, description, cost, etd, id) {
        shippingPriceEl.innerText = 'Rp ' + cost.toLocaleString('id-ID');
        window.updateTotal(cost);

        selectedShipperDiv.innerText = `${name} – Rp ${cost.toLocaleString('id-ID')}`;
        selectedShipperDiv.style.display = "block";

        document.getElementById('radioDelivery').checked = true;

        document.getElementById("selected_shipper_id").value = id;         // tiki-ECO, jne-REG, etc
        document.getElementById('input_total_ongkir').value = cost;
        document.getElementById('shipping_name').value = name;
        document.getElementById('shipping_code').value = code;
        document.getElementById('shipping_service').value = service;
        document.getElementById('shipping_etd').value = etd;
        document.getElementById('shipping_description').value = description;

        const modalEl = document.getElementById('shipperModal');
        bootstrap.Modal.getInstance(modalEl)?.hide();
    };

    const giftWrapCheckbox = document.getElementById('giftWrapCheckbox');
    giftWrapCheckbox?.addEventListener('change', function() {
        const hiddenInput = document.querySelector('input[name="is_gift_wrap"]');
        hiddenInput.value = this.checked ? '1' : '0';
        window.checkout.giftWrap = this.checked;
        window.checkout.giftWrapCost = this.checked ? 10000 : 0;
        window.updateTotal(Number(document.getElementById('shipping_price').innerText.replace(/\D/g,'')));
    });


    // NOTE textarea toggle
    const wrapper = document.getElementById('noteWrapper');
    const textarea = document.getElementById('noteArea');
    const counter = wrapper.querySelector('.note-count');
    
    wrapper.addEventListener('click', function(e){
        e.stopPropagation();
        wrapper.classList.add('open');
        textarea.classList.remove('hidden');
        setTimeout(()=>textarea.classList.add('show'),10);
        wrapper.style.justifyContent = "flex-start";
        textarea.focus();
    });

    document.addEventListener('click', e => {
        if(!wrapper.contains(e.target)){
            textarea.classList.remove('show');
            setTimeout(()=>textarea.classList.add('hidden'),150);
            wrapper.classList.remove('open');
        }
    });

    textarea.addEventListener('input', function(){
        counter.textContent=`${textarea.value.length}/200`;
    });

    // Event alamat dipilih
    document.addEventListener('alamatDipilih', function(e){
        window.checkout.destination = e.detail.districtId;
        document.getElementById('selected_district_id').value = e.detail.districtId || '';
        
        document.getElementById('selected_shipper_id').value = '';
        document.getElementById('input_total_ongkir').value = 0;
        document.getElementById('shipping_name').value = '';
        document.getElementById('shipping_code').value = '';
        document.getElementById('shipping_service').value = '';
        document.getElementById('shipping_etd').value = '';
        document.getElementById('shipping_description').value = '';

        selectedShipperDiv.style.display = 'none';
        shippingPriceEl.innerText = 'Rp 0';
        window.updateTotal(0);
    });
});
</script>
@endsection
