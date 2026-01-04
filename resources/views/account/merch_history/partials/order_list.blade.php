<style>
/* Purchase history list - cleaned and consolidated */
.purchase-history-container .nav-tabs .nav-link {
    color: #6c757d;
    border: none;
    border-bottom: 2px solid transparent;
    padding: 0.5rem 1rem;
}
.purchase-history-container .nav-tabs .nav-link.active,
.purchase-history-container .nav-tabs .nav-link:hover {
    color: #0d6efd;
    border-bottom-color: #0d6efd;
    font-weight: 600;
}

.order-item-card {
    border: 1.5px solid #d1d5db;
    border-radius: .375rem;
    padding: 1.25rem;
    background-color: #fff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.order-item-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #e9ecef;
    margin-bottom: 1rem;
}
.order-id { font-weight: 600; font-size: 0.9rem; color: #495057; }
.order-status-badge { padding: 0.4rem 1rem; border-radius: 5px; font-size: 0.8rem; font-weight: 500; color: #fff; }

/* Status colors (user-specified rgba with white font) */
.status-pending { background-color: rgba(240, 89, 44, 0.5); color: #fff; } /* Belum Bayar */
.status-processing { background-color: rgba(5, 26, 54, 0.5); color: #fff; } /* Diproses */
.status-completed { background-color: rgba(0, 79, 56, 0.5); color: #fff; } /* Selesai */
.status-cancelled { background-color: rgba(236, 31, 48, 0.5); color: #fff; } /* Dibatalkan */

.product-name { font-weight: 600; color: #58bcc2; font-size: 1.4rem; margin: 0; }

.order-product-item { display: flex; gap: 1rem; align-items: center; }
.order-product-thumb { width: 72px; height: 72px; background: #f1f5f9; border-radius: .5rem; flex-shrink: 0; }

.action-btn-group { display: flex; gap: .5rem; align-items: center; flex-wrap: wrap; }
.order-item-footer { margin-top: 1rem; gap: 0.5rem; }

/* Buttons */
.btn-base {
    padding: .45rem .9rem;
    border-radius: .4rem;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
    box-sizing: border-box;
    min-width: 140px;
    text-align: center;
}
/* Buttons: user-specified colors */
.btn-bayar-sekarang { background-color: rgba(88, 188, 194, 1); color: #fff; border: none; }
.btn-bayar-sekarang:hover { background-color: rgba(63, 149, 151, 1); }
.btn-beli-lagi { background-color: rgba(88, 188, 194, 1); color: #fff; border: none; }
.btn-beli-lagi:hover { background-color: rgba(63, 149, 151, 1); }
.btn-lihat-detail { background-color: rgba(255,255,255,1); color: rgba(88, 188, 194, 1); border: 1.5px solid rgba(88, 188, 194, 1); }
.btn-lihat-detail:hover { background-color: rgba(88, 188, 194, 1); color: #fff; }

/* Cancel button used only in detail view but style kept here for consistency */
.btn-batalkan { background: rgba(236, 31, 48, 1); color: #fff; border: 1.5px solid rgba(236, 31, 48, 1); }
.btn-batalkan:hover { background: rgba(187, 24, 37, 1); color: #fff; }

.empty-state { color: #6c757d; text-align: center; padding: 3rem 1rem; }

/* Responsive adjustments */
@media (max-width: 576px) {
    .order-item-card { padding: 0.75rem; }
    .order-item-header { flex-direction: column; align-items: flex-start; gap: .5rem; }
    .order-id { font-size: 0.78rem; }
    .product-name { font-size: 1.05rem; }
    .order-status-badge { padding: 0.25rem .6rem; font-size: 0.75rem; }
    .order-item-body p, .order-item-body h5 { font-size: 0.9rem; }
    .order-product-thumb { width: 48px; height: 48px; }
    .action-btn-group { flex-direction: column; align-items: stretch; width: 100%; }
    .btn-base, .btn-bayar-sekarang, .btn-beli-lagi, .btn-batalkan, .btn-lihat-detail { width: 100%; text-align: center; padding: .55rem .6rem; font-size: .95rem; min-width: unset; }
}
</style>

@if($orders->count() > 0)
    @foreach($orders->sortByDesc('created_at') as $order)
    <div class="order-item-card mb-3">
        <div class="order-item-header d-flex justify-content-between align-items-start">
            <div>
                <span class="order-id d-block">NO. PESANAN: {{ $order->invoice }}</span>
                @php
                    $productName = 'Nama Produk tidak tersedia';
                    $totalProducts = 0;
                    $orderType = isset($order->order_type) ? $order->order_type : 'merch'; // Default to merch if not set

                    if ($orderType === 'merch') {
                        $items = is_string($order->items) ? json_decode($order->items, true) : $order->items;
                        if (!empty($items)) {
                            $productName = $items[0]['name'];
                            $totalProducts = collect($items)->sum('qty');
                        }
                    } elseif ($orderType === 'lelang') {
                        // Gunakan product_title yang sudah disiapkan di controller (dengan fallback)
                        $productName = $order->product_title ?? 'Produk Lelang';
                        $totalProducts = 1;
                    }
                @endphp
                <h4 class="product-name mt-1">{{ $productName }}</h4>
            </div>
            <div>
                @if($order->status == 'pending')
                    <span class="order-status-badge status-pending">Belum Bayar</span>
                @elseif($order->status == 'success')
                    <span class="order-status-badge status-completed">Selesai</span>
                @elseif($order->status == 'expired')
                    <span class="order-status-badge status-cancelled">Kadaluarsa</span>
                @elseif($order->status == 'cancelled')
                    <span class="order-status-badge status-cancelled">Dibatalkan</span>
                @endif
            </div>
        </div>
        
        <div class="order-item-body">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0 text-muted"><small>Tanggal Pesanan:</small></p>
                    <p class="mb-0">{{ \Carbon\Carbon::parse($order->created_at)->format('d F Y') }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-0 text-muted"><small>Jumlah Pesanan:</small></p>
                    <p class="mb-0">{{ $totalProducts }} Produk</p>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <p class="mb-0 text-muted"><small>TOTAL PEMBAYARAN</small></p>
                    <h5 class="mb-0 fw-bold">Rp. {{ number_format($order->total_tagihan, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>
        <div class="order-item-footer d-flex justify-content-end align-items-center">
            <div class="action-btn-group">
                @if($order->status == 'pending')
                    <a href="{{ $orderType === 'merch' 
                        ? route('checkout.preview', $order->invoice) 
                        : route('account.invoice', $order->orderid_uuid) }}" 
                       class="btn-base btn-bayar-sekarang">Bayar Sekarang</a>
                @elseif($order->status == 'success')
                    <a href="#" class="btn-base btn-beli-lagi">Beli Lagi</a>
                @elseif($order->status == 'cancelled' || $order->status == 'expired')
                    <a href="#" class="btn-base btn-beli-lagi">Beli Lagi</a>
                @endif
                
                <a href="{{ $orderType === 'merch' ? route('account.merch.order.show', $order->id) : route('account.lelang.order.show', $order->id) }}" class="btn-base btn-lihat-detail">
                    Lihat Detail
                </a>
            </div>
        </div>
    </div>
    @endforeach
@else
    <div class="empty-state text-center py-5">
        <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
        <p>Belum ada riwayat pembelian</p>
    </div>
@endif
