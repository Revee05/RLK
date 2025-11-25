@extends('web.partials.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/checkout/checkout.css') }}">
<link rel="stylesheet" href="{{ asset('css/checkout/alamat.css') }}">
@endsection

@section('content')

<div class="container py-5">

    <h2 class="fw-bold mb-4">Checkout</h2>

    <form action="{{ route('checkout.process') }}" method="POST">
        @csrf

        <div class="row">

            <!-- LEFT SIDE -->
            <div class="col-md-7">

                {{-- ADDRESS --}}
                <div class="mb-4">
                    <h6 class="fw-bold mb-2">Alamat Pengiriman</h6>

                    <div id="checkout-selected-address">
                        @php
                            $selectedAddress = session('checkout_address') ?? ($addresses->first() ?? null);
                            $destination = $selectedAddress ? ['district_id' => $selectedAddress->district_id] : null;
                            $totalWeight = array_sum(array_map(fn($item) => ($item['weight'] ?? 1000) * $item['quantity'], $cart ?? []));
                        @endphp

                        @if($selectedAddress)
                            <div class="address-card border border-primary rounded p-3 mb-3">
                                <h6 class="fw-bold mb-1">{{ $selectedAddress->label_address }}</h6>
                                <div class="small text-muted">
                                    {{ $selectedAddress->name }} <br>
                                    {{ $selectedAddress->phone }} <br>
                                    {{ $selectedAddress->address }},
                                    {{ $selectedAddress->district->name ?? '-' }}<br> 
                                    {{ $selectedAddress->city->name ?? '-' }},
                                    {{ $selectedAddress->province->name ?? '-' }}
                                </div>
                            </div>
                        @else
                            <span class="text-muted">Kamu belum punya alamat.</span>
                        @endif
                    </div>

                    <button type="button" class="btn btn-dark px-3" data-bs-toggle="modal" data-bs-target="#addressModal">
                        Ganti Alamat
                    </button>
                </div>

                {{-- SHIPPING OPTION --}}
                <div class="card mb-4 border rounded">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Opsi Pengiriman</h6>

                        {{-- Self Pick-Up --}}
                        <label class="d-flex justify-content-between align-items-start mb-3 p-3 border rounded pointer">
                            <div>
                                <div class="fw-bold">Self Pick-Up</div>
                                <div class="text-muted small">
                                    Pilih opsi Self Pick Up jika ingin mengambil pesanan langsung di toko kami.
                                </div>

                                <div id="pickup-info" class="mt-2 text-primary small" style="display:none;">
                                    <strong>Alamat Rasa Lelang Karya</strong><br>
                                    GRIYA SEKAR GADING BLOK C NOMOR 19<br>
                                    Jam Operasional: 09.00–21.00
                                </div>
                            </div>

                            <input type="radio" name="shipping_method" value="pickup" class="shipper-radio" id="radioPickup">
                        </label>

                        {{-- Delivery Service --}}
                        <label class="d-flex justify-content-between align-items-start mb-3 p-3 border rounded pointer">
                            <div>
                                <div class="fw-bold">Delivery</div>
                                <div class="text-muted small">
                                    Pesanan akan dikirim ke alamat tujuan menggunakan jasa kurir.
                                </div>

                                <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                                        id="btnPilihKurir" style="display:none;"
                                        data-bs-toggle="modal" data-bs-target="#shipperModal">
                                    Pilih Kurir
                                </button>

                                <div id="selected-shipper" class="mt-2 fw-bold text-primary small" style="display:none;"></div>
                            </div>

                            <input type="radio" name="shipping_method" value="delivery" class="shipper-radio" id="radioDelivery">
                        </label>
                    </div>
                </div>

                {{-- NOTE --}}
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-body">
                        <label class="fw-bold mb-2">Catatan</label>
                        <textarea name="note" class="form-control" rows="3" maxlength="200" placeholder="Tulis catatan..."></textarea>
                    </div>
                </div>

            </div>

            <!-- RIGHT SIDE -->
            <div class="col-md-5">

                {{-- PRODUCT SUMMARY --}}
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-body">
                        @foreach ($cart as $item)
                            <div class="d-flex mb-3">
                                <img src="{{ $item['image'] ?? '/img/default.png' }}" width="90" height="90" class="rounded border me-3">
                                <div>
                                    <div class="fw-bold">{{ $item['name'] }}</div>
                                    <div class="text-muted small">Merchandise</div>
                                    <div class="fw-bold mt-1">x{{ $item['quantity'] }}</div>
                                    <div class="fw-bold mt-1">x{{ $item['price'] }}</div>
                                    
                                </div>
                            </div>
                        @endforeach

                        <hr>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span>Rp {{ number_format($subtotal,0,',','.') }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Pengiriman</span>
                            <span id="shipping_price">Rp 0</span>
                        </div>

                        <div class="d-flex justify-content-between fw-bold fs-5 mt-3">
                            <span>Total</span>
                            <span id="total_price">Rp {{ number_format($subtotal,0,',','.') }}</span>
                        </div>

                    </div>
                </div>

                {{-- PAYMENT METHOD --}}
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <img src="/img/qr.png" width="55" class="me-2">
                            <span class="fw-bold">QRIS - QR Code</span>
                            <div class="text-muted small">Biaya: Rp 500</div>
                        </div>

                        <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#paymentModal">Ganti</button>
                    </div>
                </div>

                <button type="submit" class="btn btn-dark w-100 py-3 fw-bold fs-6">
                    Pay Now
                </button>

            </div>

        </div>
    </form>
</div>

{{-- Include modal --}}
@include('web.checkout.modals-alamat', ['addresses' => $addresses, 'province' => $province])
@include('web.checkout.modals-tambah-alamat', ['province' => $province])
@include('web.checkout.modals-shipper', ['selectedAddress' => $selectedAddress, 'cart' => $cart, 'subtotal' => $subtotal])

@endsection

@section('js')
<script>
document.addEventListener("DOMContentLoaded", function () {

    // pilih alamat → kirim ke session via AJAX
    const addressCards = document.querySelectorAll(".address-card input[type='radio']");
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
                    // update tampilan checkout
                    const checkoutEl = document.getElementById("checkout-selected-address");
                    checkoutEl.innerHTML = `
                        <div class="address-card border border-primary rounded p-3 mb-3">
                            <h6 class="fw-bold mb-1">${data.address.label_address}</h6>
                            <div class="small text-muted">
                                ${data.address.name} <br>
                                ${data.address.phone} <br>
                                ${data.address.address},
                                ${data.address.district?.name ?? '-'} <br> 
                                ${data.address.city?.name ?? '-'},
                                ${data.address.provinsi?.name ?? '-'}
                            </div>
                        </div>
                    `;
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
    const destinationDistrictId = {{ $selectedAddress->district_id ?? 'null' }};
    const totalWeight = {{ $totalWeight }};

    // Toggle Pickup / Delivery
    radioPickup?.addEventListener('change', function () {
        pickupInfo.style.display = 'block';
        btnPilihKurir.style.display = 'none';
        selectedShipperDiv.style.display = 'none';

        shippingPriceEl.innerText = 'Rp 0';
        totalPriceEl.innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
    });

    radioDelivery?.addEventListener('change', function () {
        pickupInfo.style.display = 'none';
        btnPilihKurir.style.display = 'inline-block';
    });

    // Tombol Pilih Kurir → fetch daftar kurir
    btnPilihKurir?.addEventListener("click", function () {
        if (!destinationDistrictId) {
            alert("Pilih alamat tujuan dulu!");
            return;
        }

        // Kirim data ke modal (window global)
        window.SHIPPER_DATA = {
            origin: 5592,
            destination: destinationDistrictId,
            weight: totalWeight
        };
    });

    // FUNGSI PILIH KURIR
    window.selectShipper = function (name, price, id) {

        selectedShipperDiv.innerHTML = `${name} – Rp ${price.toLocaleString('id-ID')}`;
        selectedShipperDiv.style.display = "block";

        shippingPriceEl.innerText = 'Rp ' + price.toLocaleString('id-ID');
        totalPriceEl.innerText = 'Rp ' + (subtotal + Number(price)).toLocaleString('id-ID');

        radioDelivery.checked = true;

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

});
</script>
@endsection
