@extends('web.partials.layout')

@section('css')
<link rel="stylesheet" href="{{ asset('css/checkout/preview.css') }}">
@endsection

@php
    $items    = json_decode($order->items, true);
    $shipping = $order->shipping ? json_decode($order->shipping, true) : null;
    $address  = $order->address;
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
        <div class="info-item">
            <h6>Alamat Pengiriman</h6>
            <p>
                {{ Str::title($order->address->name) ?? '-' }} - 
                {{ $order->address->phone ?? '-' }} <br>
                {{ Str::title($address->address) ?? '-' }},
                {{ Str::title($address->district->name) }} <br>
                {{ Str::title($address->city->name) }},
                {{ Str::title($address->province->name) }}
                @if($address->kodepos)
                    , ID {{ $address->kodepos }}
                @endif
            </p>
        </div>
        <div class="info-item">
            <h6>Metode Pengiriman</h6>
            <p>{{ $shipping['name'] ?? '-' }} </p>
        </div>

        
    </div>
    <div class="note-box">
        {{-- ================= CATATAN ================= --}}
        @if($order->note)
            <div class="order-note">
                <strong>Catatan Pembeli:</strong> {{ $order->note }}
            </div>
        @endif
    </div>

    {{-- ================= RINCIAN PESANAN ================= --}}
    <h3 class="section-title">Rincian Pesanan</h3>

    <div class="items-box">

        @foreach($items as $item)
        <div class="product-row">
            <div class="product-left">
                <img src="{{ asset($item['image']) }}" class="product-img" alt="">
                <div>
                    <div class="product-name">{{ $item['name'] }}</div>
                    <div class="product-variant">
                        {{ $item['variant_name'] ?? '-' }}
                        @if(!empty($item['size_name']))
                            , {{ $item['size_name'] }}
                        @endif
                    </div>
                    <div class="product-qty">Qty: {{ $item['qty'] }}</div>
                </div>
            </div>
            <div class="product-price">
                Rp {{ number_format($item['price'],0,',','.') }}
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
