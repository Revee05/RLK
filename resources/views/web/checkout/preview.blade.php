@extends('web.partials.layout')

@section('css')
<link rel="stylesheet" href="{{ asset('css/checkout/preview.css') }}">
@endsection

@section('content')

<div class="preview-page">

    {{-- ================= HEADER ================= --}}
    <div class="preview-header">
        <h2>Invoice Pembayaran</h2>
        <p>Mohon periksa detail pesanan sebelum melanjutkan ke pembayaran</p>
    </div>

    {{-- ================= ORDER INFO ================= --}}
    <h3 class="section-title">Informasi Pesanan</h3>

    <div class="order-info-box">
        <div class="info-item">
            <h6>Invoice</h6>
            <p>{{ $order->invoice }}</p>
        </div>
        <div class="info-item">
            <h6>Status</h6>
            <p>{{ $order->status }}</p>
        </div>
        <div class="info-item">
            <h6>Tanggal</h6>
            <p>{{ $order->created_at->format('d M Y') }}</p>
        </div>
        <div class="info-item">
            <h6>Total</h6>
            <p>Rp {{ number_format($order->total_tagihan,0,',','.') }}</p>
        </div>
    </div>

    {{-- ================= ITEM DETAIL ================= --}}
    <h3 class="section-title">Detail Produk</h3>

    <div class="items-box">

        @foreach($items as $item)
        <div class="product-row">
            <div class="product-left">
                <img src="{{ asset($item['image']) }}" class="product-img" alt="">
                <div>
                    <div class="product-name">{{ $item['name'] }}</div>
                    <div class="product-variant">{{ $item['variant'] ?? '-' }}</div>
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
            <span>Bungkus Kado</span>
            <span>Rp {{ number_format($giftWrapCost,0,',','.') }}</span>
        </div>
        @endif

        @if(!$isPickup)
        <div class="summary-row">
            <span>Pengiriman</span>
            <span>Rp {{ number_format($shippingCost,0,',','.') }}</span>
        </div>
        @endif

        <div class="total-row">
            <span class="label">Total</span>
            <span class="amount">Rp {{ number_format($order->total_tagihan,0,',','.') }}</span>
        </div>
    </div>

    {{-- ================= ACTION BUTTON ================= --}}
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
