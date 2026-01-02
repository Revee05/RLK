@extends('account.partials.layout')
@section('css')
<!-- <link rel="stylesheet" href="{{ asset('css/account/merch_order_detail.css') }}"> -->
@endsection

@section('content')
<div class="container order-detail-container">
    <div class="row">
        @include('account.partials.nav_new')

        <div class="col-md-9">
            <div class="card content-border">
                <div class="card-head border-bottom border-darkblue align-baseline ps-4">
                    <h3 class="mb-0 fw-bolder align-bottom">Detail Pesanan Merchandise</h3>
                </div>
                <div class="card-body ps-4 pe-4">
                    <!-- Order Header -->
                    <div class="order-header">
                        <div class="order-number">NO. INVOICE: {{ $order->invoice }}</div>
                        <div class="order-date">Tanggal Pemesanan: {{ \Carbon\Carbon::parse($order->created_at)->format('d F Y, H:i') }}</div>
                        @if($order->status == 'pending')
                            <span class="order-status-badge badge-menunggu">Menunggu Pembayaran</span>
                        @elseif($order->status == 'success')
                            <span class="order-status-badge badge-selesai">Pesanan Selesai</span>
                        @elseif($order->status == 'expired')
                            <span class="order-status-badge" style="background-color: #f8d7da; color: #721c24;">Kadaluarsa</span>
                        @elseif($order->status == 'cancelled')
                            <span class="order-status-badge" style="background-color: #f8d7da; color: #721c24;">Dibatalkan</span>
                        @endif
                    </div>

                    <!-- Product Information -->
                    <div class="order-content">
                        <div class="section-title">Produk yang Dibeli</div>
                        @php
                            $items = is_string($order->items) ? json_decode($order->items, true) : $order->items;
                        @endphp
                        
                        @if($items && count($items) > 0)
                            @foreach($items as $item)
                            <div class="product-item">
                                @if(isset($item['image']) && $item['image'])
                                    <img src="{{ asset($item['image']) }}" 
                                         alt="{{ $item['name'] ?? 'Produk' }}" 
                                         class="product-image">
                                @else
                                    <div class="product-image" style="background-color: #e0e0e0; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-image" style="font-size: 32px; color: #999;"></i>
                                    </div>
                                @endif
                                <div class="product-details">
                                    <div class="product-name">{{ $item['name'] ?? 'Produk Merchandise' }}</div>
                                    @if(isset($item['variant_name']) || isset($item['size_name']))
                                        <div style="font-size: 13px; color: #666; margin-top: 3px;">
                                            @if(isset($item['variant_name']))
                                                Varian: {{ $item['variant_name'] }}
                                            @endif
                                            @if(isset($item['size_name']))
                                                @if(isset($item['variant_name'])) | @endif
                                                Ukuran: {{ $item['size_name'] }}
                                            @endif
                                        </div>
                                    @endif
                                    <div class="product-price">Rp. {{ number_format($item['price'] ?? 0, 0, ',', '.') }}</div>
                                    <div class="product-qty">Jumlah: {{ $item['qty'] ?? 1 }} pcs</div>
                                </div>
                                <div class="text-end" style="min-width: 150px;">
                                    <div style="font-size: 14px; color: #666; margin-bottom: 5px;">Subtotal</div>
                                    <div style="font-size: 18px; font-weight: 600; color: #333;">
                                        Rp. {{ number_format(($item['price'] ?? 0) * ($item['qty'] ?? 1), 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <p class="text-muted">Tidak ada produk</p>
                        @endif
                    </div>

                    <!-- Shipping Information -->
                    <div class="order-content">
                        <div class="section-title">Informasi Pengiriman</div>
                        @if($order->address)
                        <div class="info-row">
                            <span class="info-label">Nama Penerima</span>
                            <span class="info-value">{{ $order->address->name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">No. Telepon</span>
                            <span class="info-value">{{ $order->address->phone }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Label Alamat</span>
                            <span class="info-value">{{ ucfirst($order->address->label_address ?? 'Rumah') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Alamat Lengkap</span>
                            <span class="info-value" style="max-width: 60%; text-align: right;">
                                {{ $order->address->address }}, 
                                {{ $order->address->district->name ?? '' }}, 
                                {{ $order->address->city->name ?? '' }}, 
                                {{ $order->address->province->name ?? '' }}
                            </span>
                        </div>
                        @else
                            <p class="text-muted">Alamat tidak tersedia</p>
                        @endif
                        
                        @if($order->shipper)
                        <div class="info-row">
                            <span class="info-label">Kurir</span>
                            <span class="info-value">{{ strtoupper($order->shipper->name ?? 'N/A') }} - {{ $order->jenis_ongkir ?? 'Regular' }}</span>
                        </div>
                        @endif
                        
                        @php
                            $shipping = is_array($order->shipping) ? $order->shipping : [];
                        @endphp
                        
                        @if(!empty($shipping))
                            @if(isset($shipping['type']))
                            <div class="info-row">
                                <span class="info-label">Jenis Pengiriman</span>
                                <span class="info-value">{{ ucfirst($shipping['type']) == 'Delivery' ? 'Dikirim ke Alamat' : 'Ambil Sendiri' }}</span>
                            </div>
                            @endif
                            
                            @if(isset($shipping['service']))
                            <div class="info-row">
                                <span class="info-label">Layanan</span>
                                <span class="info-value">{{ $shipping['service'] }} @if(isset($shipping['description'])) - {{ $shipping['description'] }} @endif</span>
                            </div>
                            @endif
                            
                            @if(isset($shipping['etd']))
                            <div class="info-row">
                                <span class="info-label">Estimasi Pengiriman</span>
                                <span class="info-value">{{ $shipping['etd'] }}</span>
                            </div>
                            @endif
                        @endif
                        
                        @if($order->note)
                        <div class="info-row">
                            <span class="info-label">Catatan</span>
                            <span class="info-value" style="max-width: 60%; text-align: right;">{{ $order->note }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Payment Information -->
                    @if($order->status == 'success' && ($order->payment_method || $order->paid_at))
                    <div class="order-content">
                        <div class="section-title">Informasi Pembayaran</div>
                        
                        @if($order->paid_at)
                        <div class="info-row">
                            <span class="info-label">Tanggal Pembayaran</span>
                            <span class="info-value">{{ \Carbon\Carbon::parse($order->paid_at)->format('d F Y, H:i') }}</span>
                        </div>
                        @endif
                        
                        @if($order->payment_method)
                        <div class="info-row">
                            <span class="info-label">Metode Pembayaran</span>
                            <span class="info-value">{{ strtoupper($order->payment_method) }}</span>
                        </div>
                        @endif
                        
                        @if($order->payment_channel)
                        <div class="info-row">
                            <span class="info-label">Channel Pembayaran</span>
                            <span class="info-value">{{ strtoupper($order->payment_channel) }}</span>
                        </div>
                        @endif
                        
                        @if($order->payment_destination)
                        <div class="info-row">
                            <span class="info-label">Nomor Virtual Account / ID Pembayaran</span>
                            <span class="info-value">{{ $order->payment_destination }}</span>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Payment Summary -->
                    <div class="order-content">
                        <div class="section-title">Ringkasan Pembayaran</div>
                        <div class="total-section">
                            @php
                                $subtotalItems = 0;
                                if($items && count($items) > 0) {
                                    foreach($items as $item) {
                                        $subtotalItems += ($item['price'] ?? 0) * ($item['qty'] ?? 1);
                                    }
                                }
                            @endphp
                            <div class="total-row">
                                <span>Subtotal Produk</span>
                                <span>Rp. {{ number_format($subtotalItems, 0, ',', '.') }}</span>
                            </div>
                            <div class="total-row">
                                <span>Ongkos Kirim</span>
                                <span>Rp. {{ number_format($order->total_ongkir ?? 0, 0, ',', '.') }}</span>
                            </div>
                            @if($order->gift_wrap)
                            <div class="total-row">
                                <span>Gift Wrap (Bungkus Kado)</span>
                                <span>Rp. 10.000</span>
                            </div>
                            @endif
                            <div class="total-final">
                                <span>Total Pembayaran</span>
                                <span style="color: #58bcc2;">Rp. {{ number_format($order->total_tagihan, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="text-center">
                        @if($order->status == 'pending')
                            <a href="{{ route('checkout.preview', $order->invoice) }}" 
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
<script>
    $(document).ready(function() {
        // Add any interactive features here if needed
    });
</script>
<script src="{{ asset('js/account/tabs.js') }}"></script>
@endsection
