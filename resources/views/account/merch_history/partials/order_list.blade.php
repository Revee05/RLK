<style>
.purchase-history-container .nav-tabs .nav-link {
    color: #6c757d;
    border: none;
    border-bottom: 2px solid transparent;
    padding: 0.5rem 1rem;
}
.purchase-history-container .nav-tabs .nav-link.active, .purchase-history-container .nav-tabs .nav-link:hover {
    color: #0d6efd;
    border-bottom-color: #0d6efd;
    font-weight: bold;
}
.order-item-card {
    border: 1.5px solid #d1d5db;
    border-radius: .375rem;
    padding: 1.25rem;
    background-color: #fff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}
.order-item-header {
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #e9ecef;
    margin-bottom: 1rem;
}
.order-id {
    font-weight: 600;
    font-size: 0.9rem;
    color: #495057;
}
.order-status-badge {
    padding: 0.4rem 1rem;
    border-radius: 5px;
    font-size: 0.8rem;
    font-weight: 500;
    color: #fff;
}
.status-pending { background-color: #ffc107; } /* Belum Bayar - Orange/Yellow */
.status-processing { background-color: #6c757d; } /* Diproses - Grey */
.status-completed { background-color: #28a745; } /* Selesai - Green */
.status-cancelled { background-color: #dc3545; } /* Dibatalkan - Red */

.product-name {
    font-weight: 600;
    color: #58bcc2; /* Teal color from image */
    font-size: 1.5rem;
}
.order-item-footer {
    margin-top: 1rem;
    gap: 0.5rem;
}
.empty-state {
    color: #6c757d;
}
.btn-beli-lagi {
    background-color: #58bcc2;
    color: #fff;
    padding: 0.375rem 1rem;
    border-radius: 0.25rem;
    border: none;
}
.btn-beli-lagi:hover {
    background-color: #4aa8ae;
}
.btn-bayar-sekarang {
    background-color: #28a745;
    color: #fff;
    padding: 0.375rem 1rem;
    border-radius: 0.25rem;
    border: none;
    text-decoration: none;
    display: inline-block;
}
.btn-bayar-sekarang:hover {
    background-color: #218838;
    color: #fff;
}
.btn-batalkan {
    border: 1.5px solid #dc3545;
    color: #dc3545;
    background-color: #fff;
    padding: 0.375rem 1rem;
    border-radius: 0.25rem;
    text-decoration: none;
    display: inline-block;
}
.btn-batalkan:hover {
    background-color: #dc3545;
    color: #fff;
}
.btn-lihat-detail {
    border: 1.5px solid #58bcc2;
    color: #58bcc2;
    background-color: #fff;
    padding: 0.375rem 1rem;
    border-radius: 0.25rem;
    text-decoration: none;
}
.btn-lihat-detail:hover {
    background-color: #58bcc2;
    color: #fff;
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
                            $totalProducts = collect($items)->sum('quantity');
                        }
                    } elseif ($orderType === 'lelang' && isset($order->product) && isset($order->product->karya)) {
                        $productName = $order->product->karya->nama_karya;
                        $totalProducts = 1;
                    }
                @endphp
                <h4 class="product-name mt-1">{{ $productName }}</h4>
            </div>
            <div>
                @if($order->status == 'pending')
                    <span class="order-status-badge status-pending">Belum Bayar</span>
                @elseif($order->status == 'paid' || $order->status == 'shipped')
                    <span class="order-status-badge status-processing">Diproses</span>
                @elseif($order->status == 'completed')
                    <span class="order-status-badge status-completed">Selesai</span>
                @elseif($order->status == 'cancelled' || $order->status == 'failed')
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
            @if($order->status == 'pending')
                <a href="{{ $orderType === 'merch' ? route('checkout.success', $order->invoice) : route('lelang.payment.checkout', ['invoice' => $order->invoice]) }}" class="btn-bayar-sekarang">Bayar Sekarang</a>
                <a href="#" class="btn-batalkan">Batalkan Pesanan</a>
            @elseif($order->status == 'paid' || $order->status == 'shipped')
                {{-- No button for processing status based on image --}}
            @elseif($order->status == 'completed')
                <a href="#" class="btn btn-beli-lagi me-2">Beli Lagi</a>
            @elseif($order->status == 'cancelled' || $order->status == 'failed')
                 <a href="#" class="btn btn-beli-lagi me-2">Beli Lagi</a>
            @endif
            
            <a href="{{ $orderType === 'merch' ? route('account.merch.order.show', $order->id) : route('account.lelang.order.show', $order->id) }}" class="btn-lihat-detail ajax-link ms-2">
                Lihat Detail
            </a>
        </div>
    </div>
    @endforeach
@else
    <div class="empty-state text-center py-5">
        <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
        <p>Belum ada riwayat pembelian</p>
    </div>
@endif
