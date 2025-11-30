@extends('account.partials.layout')
@section('css')
<style>
    .order-detail-container {
        max-width: 1200px;
        margin: 40px auto 80px;
    }

    .order-header {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 30px;
        margin-bottom: 20px;
    }

    .order-number {
        font-size: 20px;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
    }

    .order-date {
        font-size: 14px;
        color: #666;
    }

    .order-status-badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
        margin-top: 10px;
    }

    .order-content {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 30px;
        margin-bottom: 20px;
    }

    .section-title {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #58bcc2;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-size: 14px;
        color: #666;
        font-weight: 500;
    }

    .info-value {
        font-size: 14px;
        color: #333;
        text-align: right;
    }

    .product-item {
        display: flex;
        gap: 20px;
        padding: 20px;
        background: #f9f9f9;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .product-image {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
    }

    .product-details {
        flex: 1;
    }

    .product-name {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }

    .product-price {
        font-size: 18px;
        font-weight: 600;
        color: #58bcc2;
    }

    .total-section {
        background: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        margin-top: 20px;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        font-size: 14px;
    }

    .total-final {
        display: flex;
        justify-content: space-between;
        padding: 15px 0;
        border-top: 2px solid #58bcc2;
        margin-top: 10px;
        font-size: 18px;
        font-weight: 600;
        color: #333;
    }

    .btn-back {
        background-color: #58bcc2;
        color: white;
        padding: 10px 30px;
        border-radius: 5px;
        text-decoration: none;
        display: inline-block;
        margin-top: 20px;
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        background-color: #4aa9af;
        color: white;
    }
</style>
@endsection

@section('content')
<div class="container order-detail-container">
    <div class="row">
        @include('account.partials.nav_new')

        <div class="col-md-9">
            <div class="card content-border">
                <div class="card-head border-bottom border-darkblue align-baseline ps-4">
                    <h3 class="mb-0 fw-bolder align-bottom">Detail Pesanan</h3>
                </div>
                <div class="card-body ps-4 pe-4">
                    <!-- Order Header -->
                    <div class="order-header">
                        <div class="order-number">NO. TRANSAKSI: {{ $order->order_invoice }}</div>
                        <div class="order-date">Tanggal Pemesanan: {{ \Carbon\Carbon::parse($order->created_at)->format('d F Y, H:i') }}</div>
                        @if($order->payment_status == '1')
                            <span class="order-status-badge badge-menunggu">Menunggu Pembayaran</span>
                        @elseif($order->payment_status == '2' && $order->status_pesanan == '3')
                            <span class="order-status-badge badge-selesai">Pesanan Selesai</span>
                        @elseif($order->payment_status == '2')
                            <span class="order-status-badge badge-proses">Sedang Diproses</span>
                        @elseif($order->payment_status == '3')
                            <span class="order-status-badge" style="background-color: #f8d7da; color: #721c24;">Kadaluarsa</span>
                        @else
                            <span class="order-status-badge" style="background-color: #f8d7da; color: #721c24;">Dibatalkan</span>
                        @endif
                    </div>

                    <!-- Product Information -->
                    <div class="order-content">
                        <div class="section-title">Informasi Produk</div>
                        <div class="product-item">
                            @if($order->product && $order->product->imageUtama)
                                <img src="{{ asset($order->product->imageUtama->full_path) }}" 
                                     alt="{{ $order->product->title }}" 
                                     class="product-image">
                            @else
                                <div class="product-image" style="background-color: #e0e0e0; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-image" style="font-size: 32px; color: #999;"></i>
                                </div>
                            @endif
                            <div class="product-details">
                                <div class="product-name">{{ $order->product->title ?? 'Produk tidak tersedia' }}</div>
                                <div class="product-price">Rp. {{ number_format($order->bid_terakhir, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Information -->
                    <div class="order-content">
                        <div class="section-title">Informasi Pengiriman</div>
                        <div class="info-row">
                            <span class="info-label">Nama Penerima</span>
                            <span class="info-value">{{ $order->name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">No. Telepon</span>
                            <span class="info-value">{{ $order->phone }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Label Alamat</span>
                            <span class="info-value">{{ ucfirst($order->label_address) }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Alamat Lengkap</span>
                            <span class="info-value" style="max-width: 60%; text-align: right;">
                                {{ $order->address }}, 
                                {{ $order->kecamatan->nama_kecamatan ?? '' }}, 
                                {{ $order->kabupaten->nama_kabupaten ?? '' }}, 
                                {{ $order->provinsi->nama_provinsi ?? '' }}
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Kurir</span>
                            <span class="info-value">{{ strtoupper($order->pengirim) }} - {{ $order->jenis_ongkir }}</span>
                        </div>
                        @if($order->nomor_resi)
                        <div class="info-row">
                            <span class="info-label">No. Resi</span>
                            <span class="info-value">{{ $order->nomor_resi }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Payment Summary -->
                    <div class="order-content">
                        <div class="section-title">Ringkasan Pembayaran</div>
                        <div class="total-section">
                            <div class="total-row">
                                <span>Harga Produk</span>
                                <span>Rp. {{ number_format($order->bid_terakhir, 0, ',', '.') }}</span>
                            </div>
                            <div class="total-row">
                                <span>Ongkos Kirim</span>
                                <span>Rp. {{ number_format($order->total_ongkir, 0, ',', '.') }}</span>
                            </div>
                            @if($order->asuransi_pengiriman > 0)
                            <div class="total-row">
                                <span>Asuransi Pengiriman (10%)</span>
                                <span>Rp. {{ number_format($order->asuransi_pengiriman, 0, ',', '.') }}</span>
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
                        @if($order->payment_status == '1')
                            <a href="{{ route('account.invoice', $order->orderid_uuid) }}" 
                               class="btn-back" style="background-color: #333; margin-right: 10px;">
                                Bayar Sekarang
                            </a>
                        @endif
                        <a href="{{ route('account.purchase.history') }}" class="btn-back">
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
@endsection
