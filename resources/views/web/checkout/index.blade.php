@extends('web.partials.layout')

@section('content')

<div class="container py-5">

    <h2 class="fw-bold mb-4">Checkout</h2>

    <form action="{{ route('checkout.process') }}" method="POST">
        @csrf

        <div class="row">

            <!-- LEFT SIDE -->
            <div class="col-md-7">

                {{-- ADDRESS --}}
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between">
                        <div>
                            <h6 class="fw-bold mb-1">Alamat Pengiriman</h6>
                            @if($addresses->count())
                                <div class="text-muted small">
                                    {{ $addresses[0]->name }} <br>
                                    {{ $addresses[0]->address }} <br>
                                    {{ $addresses[0]->provinsi->name ?? '' }},
                                    {{ $addresses[0]->kabupaten->name ?? '' }} <br>
                                    {{ $addresses[0]->phone }}
                                </div>
                            @else
                                <span class="text-muted">Kamu belum punya alamat.</span>
                            @endif
                        </div>

                        <button type="button" class="btn btn-dark px-3" data-bs-toggle="modal" data-bs-target="#addressModal">
                            Ganti
                        </button>
                    </div>
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
@include('web.checkout.modals')

@endsection

@section('scripts')
<script>
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
</script>
@endsection