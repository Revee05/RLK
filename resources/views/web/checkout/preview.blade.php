@extends('web.partials.layout')

@section('css')
<link rel="stylesheet" href="{{ asset('css/checkout/preview.css') }}">
@endsection

@php
    $items    = json_decode($order->items, true);
    $shipping = $order->shipping ? json_decode($order->shipping, true) : null;
    
    // Untuk OrderMerch: alamat ada di relasi address
    // Untuk Order (lelang): alamat ada di field langsung (name, phone, address, dll)
    $isOrderMerch = get_class($order) === 'App\OrderMerch';
    $address = $isOrderMerch ? $order->address : null;
@endphp


@section('content')

<div class="preview-page">

    {{-- ================= HEADER ================= --}}
    <div class="preview-header">
        <h2>Invoice Pembayaran</h2>
        <p>Mohon periksa detail pesanan sebelum melanjutkan ke pembayaran</p>
    </div>

    {{-- ================= INFORMASI PEMBELI ================= --}}
    <h3 class="section-title">Informasi Pesanan</h3>

    <div class="order-info-box">
        <div class="info-item">
            <h6>Tanggal</h6>
            <p>{{ $order->created_at->format('d M Y') }}</p>
        </div>
        <div class="info-item">
            <h6>No Pesanan</h6>
            <p>{{ $order->invoice }}</p>
        </div>

        {{-- ========================= --}}
        {{-- ALAMAT / PICKUP INFO --}}
        {{-- ========================= --}}
        <div class="info-item">
            @if(($shipping['type'] ?? null) === 'pickup')

                <h6>Pengambilan Pesanan</h6>
                <p>
                    <strong>Rasa Lelang Karya</strong><br>
                    Griya Jl. Sekargading blok C 19, <br>
                    RT.04/RW.03, Kel. Kalisegoro, <br>
                    Gunung Pati, Kota Semarang <br>
                    Jam Operasional: 09.00 – 21.00
                </p>

            @else

                <h6>Alamat Pengiriman</h6>
                <p>
                    @if($isOrderMerch && $address)
                        {{-- OrderMerch: ambil dari relasi address --}}
                        {{ Str::title($address->name ?? '-') }} -
                        {{ $address->phone ?? '-' }} <br>
                        {{ Str::title($address->address ?? '-') }},
                        {{ Str::title($address->district->name ?? '-') }} <br>
                        {{ Str::title($address->city->name ?? '-') }},
                        {{ Str::title($address->province->name ?? '-') }}
                        @if(!empty($address->kodepos))
                            , ID {{ $address->kodepos }}
                        @endif
                    @else
                        {{-- Order (lelang): ambil langsung dari field order --}}
                        {{ Str::title($order->name ?? '-') }} -
                        {{ $order->phone ?? '-' }} <br>
                        {{ Str::title($order->address ?? '-') }},
                        {{ Str::title(optional($order->kecamatan)->nama_kecamatan ?? '-') }} <br>
                        {{ Str::title(optional($order->kabupaten)->nama_kabupaten ?? '-') }},
                        {{ Str::title(optional($order->provinsi)->nama_provinsi ?? '-') }}
                    @endif
                </p>

            @endif
        </div>

        {{-- ========================= --}}
        {{-- METODE PENGIRIMAN --}}
        {{-- ========================= --}}
        <div class="info-item">
            <h6>Metode Pengiriman</h6>

            @if(($shipping['type'] ?? null) === 'pickup')
                <p>Ambil di Toko</p>
            @else
                <p>
                    {{ $shipping['name'] ?? '-' }}
                    – {{ $shipping['service'] ?? '-' }}
                </p>
            @endif
        </div>
    </div>
    <div class="note-box">
        <div class="order-note">
            <strong>Catatan Pembeli:</strong>
            {{ !empty($order->note) ? $order->note : '-' }}
        </div>
    </div>

    {{-- ================= RINCIAN PESANAN ================= --}}
    <h3 class="section-title">Rincian Pesanan</h3>

    <div class="items-box">

        @foreach($items as $item)
            @php
                $image = $item['image'] ?? 'img/default.png';
                $name = $item['name'] ?? 'Unknown Product';
                $variant = $item['variant_name'] ?? '';
                $size = $item['size_name'] ?? '';
                $quantity = $item['qty'] ?? 1;
                $price = $item['price'] ?? 0;
            @endphp

            <div class="product-row">
                <div class="product-left">
                    <img src="{{ asset($image) }}" class="product-img" alt="{{ $name }}">
                    <div>
                        <div class="product-name">{{ $name }}</div>
                        <div class="product-variant">
                            {{ $variant }}
                            @if($size)
                                , {{ $size }}
                            @endif
                        </div>
                        <div class="product-qty">Qty: {{ $quantity }}</div>
                    </div>
                </div>
                <div class="product-price">
                    Rp {{ number_format($price,0,',','.') }}
                </div>
            </div>
            <div class="line"></div>
        @endforeach


        {{-- SUMMARY --}}
        <div class="summary-row">
            <span>Subtotal</span>
            <span>Rp {{ number_format($subtotal,0,',','.') }}</span>
        </div>

        @if($giftWrapCost > 0)
        <div class="summary-row">
            <span>Biaya Pengemasan Ekstra</span>
            <span>Rp {{ number_format($giftWrapCost,0,',','.') }}</span>
        </div>
        @endif

        @if(!$isPickup)
        <div class="summary-row">
            <span>Biaya Pengiriman</span>
            <span>Rp {{ number_format($order->total_ongkir,0,',','.') }}</span>
        </div>
        @endif

        <div class="total-row">
            <span class="label">Total</span>
            <span class="amount">Rp {{ number_format($order->total_tagihan,0,',','.') }}</span>
        </div>
    </div>

    {{-- ================= ACTION ================= --}}
    <div class="preview-action">

        <form action="{{ route('checkout.pay.xendit', $order->invoice) }}" method="POST">
            @csrf
            <button type="submit" class="btn-primary">
                Bayar Sekarang
            </button>
        </form>

        <form action="{{ route('payment.cancel', $order->invoice) }}" method="POST"
              onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">
            @csrf
            <button type="submit" class="btn-primary btn-cancel">
                Batalkan
            </button>
        </form>

    </div>

</div>

@endsection
