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
                        @endphp

                        @if($selectedAddress)
                            <div class="address-card border border-primary rounded p-3 mb-3">
                                <h6 class="fw-bold mb-1">{{ $selectedAddress->label }}</h6>
                                <div class="small text-muted">
                                    {{ $selectedAddress->name }} • {{ $selectedAddress->phone }} <br>
                                    {{ $selectedAddress->address }} <br>
                                    {{ $selectedAddress->kabupaten->nama_kabupaten ?? '-' }},
                                    {{ $selectedAddress->provinsi->nama_provinsi ?? '-' }}
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
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Opsi Pengiriman</h6>
                        @foreach ($shippers as $ship)
                            <label class="d-flex justify-content-between align-items-start mb-3 p-3 border rounded pointer">
                                <div>
                                    <div class="fw-bold">{{ $ship->name }}</div>
                                    <div class="text-muted small">Biaya: Rp {{ number_format($ship->price,0,',','.') }}</div>
                                </div>
                                <input type="radio" name="shipper_id" value="{{ $ship->id }}" class="shipper-radio" data-price="{{ $ship->price }}">
                            </label>
                        @endforeach
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
@include('web.checkout.modals-alamat', ['addresses' => $addresses, 'provinsi' => $provinsi])
@include('web.checkout.modals-tambah-alamat', ['provinsi' => $provinsi])

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
                            <h6 class="fw-bold mb-1">${data.address.label}</h6>
                            <div class="small text-muted">
                                ${data.address.name} • ${data.address.phone} <br>
                                ${data.address.address} <br>
                                ${data.address.kabupaten?.nama_kabupaten ?? '-'},
                                ${data.address.provinsi?.nama_provinsi ?? '-'}
                            </div>
                        </div>
                    `;
                }
            });
        });
    });

    // Shipping option
    const radios = document.querySelectorAll('.shipper-radio');
    const shippingPriceEl = document.getElementById('shipping_price');
    const totalPriceEl = document.getElementById('total_price');
    const subtotal = {{ $subtotal }};
    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            const price = parseInt(this.dataset.price);
            shippingPriceEl.innerText = 'Rp ' + price.toLocaleString('id-ID');
            totalPriceEl.innerText = 'Rp ' + (subtotal + price).toLocaleString('id-ID');
        });
    });

});
</script>
@endsection
