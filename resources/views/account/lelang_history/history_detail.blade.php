@extends('account.partials.layout')
@section('css')
{{-- <link rel="stylesheet" href="{{ asset('css/account/order_detail.css') }}"> --}}
@endsection

@section('content')
<div class="container order-detail-container">
    <div class="row">
        @include('account.partials.nav_new')

        <div class="col-md-9">
            <div class="card content-border">
                <div class="card-head border-bottom border-darkblue align-baseline ps-4">
                    <h3 class="mb-0 fw-bolder align-bottom">Detail Pesanan Lelang</h3>
                </div>
                <div class="card-body ps-4 pe-4">
                    <!-- Order Header -->
                    <div class="order-header">
                        <div class="order-number">NO. INVOICE: {{ $order->invoice }}</div>
                        <div class="order-date">Tanggal Pemesanan: {{ \Carbon\Carbon::parse($order->created_at)->format('d F Y, H:i') }}</div>
                        @if($order->status == 'pending')
                            <span class="order-status-badge badge-menunggu">Menunggu Pembayaran</span>
                        @elseif($order->status == 'success')
                            <span class="order-status-badge badge-selesai">Sudah Dibayar</span>
                        @elseif($order->status == 'expired')
                            <span class="order-status-badge" style="background-color: #fff3cd; color: #856404;">Kadaluarsa</span>
                        @elseif($order->status == 'cancelled')
                            <span class="order-status-badge" style="background-color: #f8d7da; color: #721c24;">Dibatalkan</span>
                        @else
                            <span class="order-status-badge" style="background-color: #e2e3e5; color: #383d41;">Status Tidak Dikenal</span>
                        @endif
                    </div>

                    <!-- Product Information -->
                    <div class="order-content">
                        <div class="section-title">Produk yang Dimenangkan</div>
                        <div class="product-item">
                            @php
                                // Gunakan data yang sudah disiapkan di controller dengan fallback
                                $productName = 'Produk Lelang';
                                $productImage = null;
                                
                                if ($order->product && $order->product_exists) {
                                    $productName = $order->product->title ?? 'Produk Lelang';
                                    
                                    // Ambil gambar utama dari relasi imageUtama
                                    if ($order->product->imageUtama && $order->product->imageUtama->path) {
                                        $productImage = $order->product->imageUtama->path;
                                    }
                                    // Fallback ke images pertama
                                    elseif ($order->product->images && $order->product->images->count() > 0) {
                                        $productImage = $order->product->images->first()->path ?? null;
                                    }
                                    // Fallback ke karya
                                    elseif ($order->product->karya && $order->product->karya->image) {
                                        $productImage = $order->product->karya->image;
                                    }
                                    
                                    // Fallback nama produk dari karya jika title kosong
                                    if (empty($productName) && $order->product->karya) {
                                        $productName = $order->product->karya->nama_karya ?? $order->product->karya->name ?? 'Produk Lelang';
                                    }
                                } else {
                                    // Product tidak ada atau dihapus, gunakan fallback dari controller
                                    $productName = $order->product_title ?? 'Produk tidak tersedia';
                                    $productImage = null;
                                }
                            @endphp
                            
                            @if($productImage)
                                <img src="{{ asset($productImage) }}" 
                                     alt="{{ $productName }}" 
                                     class="product-image"
                                     onerror="this.onerror=null; this.src='{{ asset('assets/img/default.jpg') }}'">
                            @else
                                <div class="product-image" style="background-color: #e0e0e0; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-image" style="font-size: 32px; color: #999;"></i>
                                </div>
                            @endif
                            <div class="product-details">
                                <div class="product-name">{{ $productName }}</div>
                                <div class="product-price">Harga Menang: Rp. {{ number_format($order->total_tagihan, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div class="order-content">
                        <div class="section-title">Ringkasan Pembayaran</div>
                        <div class="total-section">
                            <div class="total-final">
                                <span>Total Pembayaran</span>
                                <span style="color: #58bcc2;">Rp. {{ number_format($order->total_tagihan, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="text-center">
                        @if($order->status == 'pending')
                            <a href="{{ route('account.invoice', $order->orderid_uuid) }}" 
                               class="btn-back ajax-link" style="background-color: #333; margin-right: 10px;">
                                Bayar Sekarang
                            </a>
                        @endif
                        <a href="{{ route('account.purchase.history') }}" class="btn-back ajax-link">
                            Kembali ke Riwayat Pembelian
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('js/account/tabs.js') }}"></script>
@endsection
