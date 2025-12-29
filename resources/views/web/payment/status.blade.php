@extends('web.partials.layout')

@section('title', 'Status Pembayaran')

@section('css')
<link rel="stylesheet" href="{{ asset('css/checkout/status.css') }}">
@endsection

@php
    $items = json_decode($order->items, true);
    $shipping = $order->shipping ? json_decode($order->shipping, true) : null;
    $shippingType = $shipping['type'] ?? 'delivery';
    $giftWrapCost = $order->gift_wrap == 1 ? 10000 : 0;
    $shippingData = json_decode($order->shipping, true);
@endphp

@section('content')
<div class="status-page">

    {{-- ================= ICON & TITLE ================= --}}
    <div class="status-header">
        @switch($order->status)
            @case('success')
                <div class="status-icon">✔</div>
                <h3 class="status-title">Terima Kasih Sudah Order!</h3>
                <p class="status-desc">
                    Pesanan Anda Dengan Nomor
                    <strong>{{ $order->invoice }}</strong>
                    Telah Berhasil Diproses. Anda Akan Menerima Email Konfirmasi Dalam Waktu Singkat.
                </p>
                @break
            @case('pending')
                <div class="status-icon">!</div>
                <h3 class="status-title">Menunggu Pembayaran</h3>
                <p class="status-desc">
                    Pesanan Anda Dengan Nomor
                    <strong>{{ $order->invoice }}</strong>
                    Belum Dibayar. Silakan Lakukan Pembayaran.
                </p>
                @break

            @case('expired')
                <div class="status-icon">×</div>
                <h3 class="status-title">Pembayaran Kedaluwarsa</h3>
                <p class="status-desc">
                    Waktu Pembayaran Untuk Pesanan Dengan Nomor
                    <strong>{{ $order->invoice }}</strong>
                    Telah Habis. Silakan Lakukan Pemesanan Ulang.
                </p>
                @break

            @case('cancelled')
                <div class="status-icon">×</div>
                <h3 class="status-title">Pesanan Dibatalkan</h3>
                <p class="status-desc">
                    Pesanan Anda Dengan Nomor
                    <strong>{{ $order->invoice }}</strong>
                    Telah Dibatalkan.
                </p>
                @break

            @default
                <div class="status-icon">?</div>
                <h3 class="status-title">Status Tidak Diketahui</h3>
                <p class="status-desc">
                    Status pesanan dengan nomor
                    <strong>{{ $order->invoice }}</strong>
                    tidak dapat diproses.
                </p>

        @endswitch

    </div>

    {{-- ================= INFO BOX ================= --}}
    <div class="info-box">
        <div class="info-item">
            <div class="info-label">No Pesanan</div>
            <div class="info-value">{{ $order->invoice }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Metode Pembayaran</div>
            <div class="info-value">{{ $order->payment_channel ? strtoupper($order->payment_channel) : '-' }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">
                @if($shippingType === 'pickup')
                    Alamat Pengambilan
                @else
                    Alamat Pengiriman
                @endif
            </div>
            <div class="info-value">
                @if($shippingType === 'pickup')
                    Griya Jl. Sekargading blok C 19, <br>
                    RT.04/RW.03, Kel. Kalisegoro, <br>
                    Gunung Pati, Kota Semarang <br>
                    Jawa Tengah 50228
                @else
                    {{ $order->address->name ?? '-' }} - {{ $order->address->phone ?? '-' }} <br>
                    {{ $order->address->address ?? '-' }},
                    {{ $order->address->district->name ?? '-' }}<br>
                    {{ $order->address->city->name ?? '-' }},
                    {{ $order->address->province->name ?? '-' }}
                @endif
            </div>
        </div>
        <div class="info-item">
            <div class="info-label">Metode Pengiriman</div>
            <div class="info-value">
                @if($shippingType === 'pickup')
                    Ambil di Toko
                @else
                    {{ $shippingData['name'] ?? '-' }}
                    @if(!empty($shippingData['service']))
                        – {{ $shippingData['service'] }}
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- ================= Note ================= --}}
    <div class="note-box">
        <div class="order-note">
            <strong>Catatan Pembeli:</strong>
            {{ !empty($order->note) ? $order->note : '-' }}
        </div>
    </div>

    {{-- ================= RINCIAN PESANAN ================= --}}
    <h2 class="section-title">Rincian Pesanan</h2>

    <div class="product-box">
        @foreach($items as $item)
            @php
                $image = $item['image'] ?? 'img/default.png';
                $name = $item['name'] ?? 'Unknown Product';
                $variant = $item['variant_name'] ?? '';
                $size = $item['size_name'] ?? '';
                $qty = $item['qty'] ?? 1;
                $price = $item['price'] ?? 0;
            @endphp

            <div class="product-row">
                <img src="{{ asset($image) }}" class="product-img" alt="{{ $name }}">
                <div class="product-info">
                    <div class="product-name">{{ $name }}</div>
                    <div class="product-variant">
                        {{ $variant }}
                        @if($size)
                            , {{ $size }}
                        @endif
                    </div>
                    <div class="product-qty">Qty: {{ $qty }}</div>
                </div>
                <div class="product-price">
                    Rp {{ number_format($item['price'],0,',','.') }}
                </div>
            </div>
            <div class="divider"></div>
        @endforeach

        {{-- SUMMARY --}}
        <div class="summary-row">
            <span>Subtotal</span>
            <span class="summary-price">Rp. {{ number_format($order->subtotal,0,',','.') }}</span>
        </div>
        <div class="summary-row">
            <span>Biaya Pengiriman</span>
            <span class="summary-price">Rp. {{ number_format($order->total_ongkir,0,',','.') }}</span>
        </div>
        
        @if($giftWrapCost > 0)
        <div class="summary-row">
            <span>Biaya Pengemasan Ekstra</span>
            <span>Rp {{ number_format($giftWrapCost,0,',','.') }}</span>
        </div>
        @endif

        <div class="divider"></div>

        <div class="total-row">
            <span>Total</span>
            <span class="total-price">
                Rp. {{ number_format($order->total_tagihan,0,',','.') }}
            </span>
        </div>
    </div>

    {{-- ================= ACTION ================= --}}
    <div class="action-box">
        @switch($order->status)
            @case('success')
                <button type="button" class="btn-main"
                    onclick="window.location='{{ route('home') }}'">
                    Lanjut Belanja
                </button>
                <button type="button" class="btn-text"
                    onclick="window.location='{{ route('account.purchase.history') }}'">
                    Lihat Riwayat Pembelian
                </button>
                @break

            @case('pending')
                <button type="button" class="btn-main"
                    onclick="window.location='{{ route('checkout.pay.xendit', $order->id) }}'">
                    Bayar Sekarang
                </button>
                <button type="button" class="btn-text"
                    onclick="window.location='{{ route('account.purchase.history') }}'">
                    Lihat Riwayat Pembelian
                </button>
                @break

            @case('expired')
                <button type="button" class="btn-main"
                    onclick="window.location='{{ route('cart.index', $order->id) }}'">
                    Pesan Ulang
                </button>
                <button type="button" class="btn-text"
                    onclick="window.location='{{ route('account.purchase.history') }}'">
                    Lihat Riwayat Pembelian
                </button>
                @break

            @case('cancelled')
                <button type="button" class="btn-main"
                    onclick="window.location='{{ route('cart.index', $order->id) }}'">
                    Pesan Ulang
                </button>
                <button type="button" class="btn-text"
                    onclick="window.location='{{ route('account.purchase.history') }}'">
                    Lihat Riwayat Pembelian
                </button>
                @break

            @default
                <button type="button" class="btn-main"
                    onclick="window.location='{{ route('home') }}'">
                    Lanjut Belanja
                </button>
        @endswitch
    </div>
</div>
@endsection
