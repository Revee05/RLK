@extends('account.partials.layout')
@section('css')
<!-- <link rel="stylesheet" href="{{ asset('css/account/purchase_history.css') }}"> -->
@endsection

@section('content')
<div class="container purchase-history-container">
    <div class="row">
        @include('account.partials.nav_new')

        <div class="col-md-9">
            <div class="card content-border">
                <div class="card-head border-bottom border-darkblue align-baseline ps-4">
                    <h3 class="mb-0 fw-bolder align-bottom">Riwayat Pembelian</h3>
                </div>
                <div class="card-body ps-4 pe-4">
                    <!-- Transaction Statistics -->
                    <div class="transaction-stats">
                        <div class="stat-box">
                            <div class="stat-label">Total Pembelian</div>
                            <div class="stat-value">{{ $orders->count() }}</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">Total Pengeluaran</div>
                            <div class="stat-value">Rp.
                                {{ number_format($orders->where('status', 'paid')->sum('total_tagihan'), 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">Pesanan Pending</div>
                            <div class="stat-value">{{ $orders->where('status', 'pending')->count() }}</div>
                        </div>
                    </div>

                    <!-- Transaction Log -->
                    <div class="transaction-log-section">
                        <div class="section-title">Log Transaksi Terbaru</div>
                        <div class="transaction-list-container">
                        @if($orders->count() > 0)
                        @foreach($orders->sortByDesc('created_at') as $order)
                        <div class="transaction-item">
                            <div class="transaction-id">
                                NO. TRANSAKSI:
                                <br>
                                {{ $order->invoice }}
                            </div>
                            <div class="transaction-status">
                                @if($order->status == 'pending')
                                <span class="badge-status badge-menunggu">Menunggu Pembayaran</span>
                                @elseif($order->status == 'paid')
                                <span class="badge-status badge-proses">Sudah Dibayar</span>
                                @elseif($order->status == 'shipped')
                                <span class="badge-status badge-dikirim">Sedang Dikirim</span>
                                @elseif($order->status == 'completed')
                                <span class="badge-status badge-selesai">Selesai</span>
                                @else
                                <span class="badge-status"
                                    style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c2c7;">Dibatalkan</span>
                                @endif
                            </div>
                            <div class="transaction-date">
                                {{ \Carbon\Carbon::parse($order->created_at)->format('d-m-Y H:i') }}
                            </div>
                            <div class="transaction-amount">
                                Rp. {{ number_format($order->total_tagihan, 0, ',', '.') }}
                            </div>
                            <div class="transaction-actions">
                                @if($order->status == 'pending')
                                <a href="{{ route('checkout.success', $order->invoice) }}"
                                    class="btn-action btn-bayar-sekarang">
                                    Bayar Sekarang
                                </a>
                                @elseif($order->status == 'paid')
                                <button class="btn-action btn-paid">
                                    Paid
                                </button>
                                @elseif($order->status == 'shipped')
                                <button class="btn-action btn-shipped">
                                    Shipped
                                </button>
                                @elseif($order->status == 'completed')
                                <button class="btn-action btn-selesai" disabled>
                                    Selesai
                                </button>
                                @endif
                                <a href="{{ route('account.merch.order.show', $order->id) }}"
                                    class="btn-action btn-lihat-detail ajax-link">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <div class="empty-state">
                            <i class="bi bi-cart-x"></i>
                            <p>Belum ada riwayat pembelian</p>
                        </div>
                        @endif
                        </div>
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