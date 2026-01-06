@extends('account.partials.layout')

@section('content')
<style>
/* Buttons and container for order detail actions */
.order-detail-container .action-buttons {
    display: flex;
    gap: .75rem;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
}

/* Base button */
.order-detail-container .btn-base {
    padding: .55rem 1rem;
    border-radius: .45rem;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
    min-width: 140px;
    text-align: center;
    box-sizing: border-box;
}

/* Primary (teal) - used for Bayar Sekarang and Kembali */
.order-detail-container .btn-primary {
    background-color: rgba(88, 188, 194, 1);
    color: #fff;
    border: none;
}
.order-detail-container .btn-primary:hover { background-color: rgba(63,149,151,1); }

/* Danger (red) - cancel action */
.order-detail-container .btn-danger {
    background-color: rgba(236, 31, 48, 1);
    color: #fff;
    border: 1.5px solid rgba(236, 31, 48, 1);
}
.order-detail-container .btn-danger:hover { background-color: rgba(187,24,37,1); }

/* Bayar Sekarang (teal) - payment action */
.order-detail-container .btn-bayar-sekarang {
    background-color: rgba(88, 188, 194, 1);
    color: #fff;
    border: none;
}
.order-detail-container .btn-bayar-sekarang:hover { background-color: rgba(63,149,151,1); }

/* Mobile: stack actions and make them touch-friendly */
@media (max-width: 576px) {
    .order-detail-container .action-buttons { flex-direction: column; align-items: stretch; }
    .order-detail-container .action-buttons .btn-base { width: 100%; min-width: unset; }
}

/* Stronger rules for anchors to override global link styles */
.order-detail-container .action-buttons a.btn-base,
.order-detail-container .action-buttons a.btn-primary,
.order-detail-container .action-buttons a.btn-bayar-sekarang,
.order-detail-container .action-buttons a.btn-danger,
.order-detail-container .action-buttons button.btn-base,
.order-detail-container .action-buttons button.btn-primary,
.order-detail-container .action-buttons button.btn-bayar-sekarang,
.order-detail-container .action-buttons button.btn-danger {
    display: inline-block !important;
    padding: .55rem 1rem !important;
    border-radius: .45rem !important;
    text-decoration: none !important;
    color: inherit !important;
    box-sizing: border-box !important;
}
.order-detail-container .action-buttons a.btn-primary,
.order-detail-container .action-buttons button.btn-primary {
    background-color: rgba(88, 188, 194, 1) !important;
    color: #fff !important;
    border: none !important;
}
.order-detail-container .action-buttons a.btn-bayar-sekarang,
.order-detail-container .action-buttons button.btn-bayar-sekarang {
    background-color: rgba(88, 188, 194, 1) !important;
    color: #fff !important;
    border: none !important;
}
.order-detail-container .action-buttons a.btn-danger,
.order-detail-container .action-buttons button.btn-danger {
    background-color: rgba(236, 31, 48, 1) !important;
    color: #fff !important;
    border: 1.5px solid rgba(236, 31, 48, 1) !important;
}
</style>

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
                                <div class="product-price">Harga Menang: Rp. {{ number_format($order->winning_bid ?? $order->total_tagihan, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Auction Winner Information -->
                    <div class="order-content">
                        <div class="section-title">Informasi Pemenang Lelang</div>
                        <div class="info-row">
                            <span class="info-label">Nama Pemenang</span>
                            <span class="info-value">{{ $order->winner_name ?? $order->name ?? Auth::user()->name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Bid Menang</span>
                            <span class="info-value">Rp. {{ number_format($order->winning_bid ?? $order->total_tagihan, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Shipping Information (if available) -->
                    @if($order->address || $order->provinsi_id || $order->phone || $order->name)
                    <div class="order-content">
                        <div class="section-title">Informasi Pengiriman</div>
                        @if($order->name)
                        <div class="info-row">
                            <span class="info-label">Nama Penerima</span>
                            <span class="info-value">{{ $order->name }}</span>
                        </div>
                        @endif
                        @if($order->phone)
                        <div class="info-row">
                            <span class="info-label">No. Telepon</span>
                            <span class="info-value">{{ $order->phone }}</span>
                        </div>
                        @endif
                        @if($order->label_address)
                        <div class="info-row">
                            <span class="info-label">Label Alamat</span>
                            <span class="info-value">{{ ucfirst($order->label_address) }}</span>
                        </div>
                        @endif
                        @php
                            // Check if address is an object (UserAddress model) or a string
                            $isAddressObject = is_object($order->address);
                        @endphp
                        @if($order->address || $order->provinsi_id)
                        <div class="info-row">
                            <span class="info-label">Alamat Lengkap</span>
                            <span class="info-value" style="max-width: 60%; text-align: right;">
                                @if($isAddressObject && $order->address)
                                    {{-- Address is UserAddress object --}}
                                    {{ $order->address->address ?? '' }}
                                    @if($order->address->district), {{ $order->address->district->name ?? '' }}@endif
                                    @if($order->address->city), {{ $order->address->city->name ?? '' }}@endif
                                    @if($order->address->province), {{ $order->address->province->name ?? '' }}@endif
                                    @if($order->address->postal_code) {{ $order->address->postal_code }}@endif
                                @else
                                    {{-- Address is string or use old fields --}}
                                    {{ $order->address ?? '' }}
                                    @if($order->kecamatan), {{ $order->kecamatan->name ?? '' }}@endif
                                    @if($order->kabupaten), {{ $order->kabupaten->name ?? '' }}@endif
                                    @if($order->provinsi), {{ $order->provinsi->name ?? '' }}@endif
                                @endif
                            </span>
                        </div>
                        @endif
                        
                        @if($order->shipper || $order->pengirim)
                        <div class="info-row">
                            <span class="info-label">Kurir</span>
                            <span class="info-value">{{ strtoupper($order->shipper->name ?? $order->pengirim ?? 'N/A') }} @if($order->jenis_ongkir)- {{ $order->jenis_ongkir }}@endif</span>
                        </div>
                        @endif
                        
                        @if($order->nomor_resi)
                        <div class="info-row">
                            <span class="info-label">Nomor Resi</span>
                            <span class="info-value">{{ $order->nomor_resi }}</span>
                        </div>
                        @endif
                        
                        @if($order->note)
                        <div class="info-row">
                            <span class="info-label">Catatan</span>
                            <span class="info-value" style="max-width: 60%; text-align: right;">{{ $order->note }}</span>
                        </div>
                        @endif
                    </div>
                    @endif

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
                            <div class="info-row mb-3">
                                <span class="info-label">Tanggal Pesanan</span>
                                <span class="info-value">{{ \Carbon\Carbon::parse($order->created_at)->format('d F Y, H:i') }}</span>
                            </div>
                            <hr style="margin: 1rem 0; border-color: #e9ecef;">
                            <div class="total-row">
                                <span>Harga Final</span>
                                <span>Rp. {{ number_format($order->winning_bid ?? $order->total_tagihan, 0, ',', '.') }}</span>
                            </div>
                            @if($order->total_ongkir && $order->total_ongkir > 0)
                            <div class="total-row">
                                <span>Ongkos Kirim</span>
                                <span>Rp. {{ number_format($order->total_ongkir, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            @if($order->asuransi_pengiriman && $order->asuransi_pengiriman > 0)
                            <div class="total-row">
                                <span>Asuransi Pengiriman</span>
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
                    <div class="action-buttons">
                        @if($order->status == 'pending')
                            {{-- Tombol cancel TIDAK ada untuk lelang/auction karena tidak bisa dibatalkan --}}
                            <a href="{{ route('checkout.preview', $order->invoice) }}" class="btn-base btn-bayar-sekarang">Bayar Sekarang</a>
                        @endif
                        <a href="{{ route('account.purchase.history') }}" class="btn-base btn-primary">Kembali ke Riwayat Pembelian</a>
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
