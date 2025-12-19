@extends('web.partials.layout')

@section('title', 'Status Pembayaran')

@section('content')
<div class="container my-5">

    @php
        $status = $order->status;
        $statusText = '';
        $statusClass = '';

        switch($status) {
            case 'pending':
                $statusText = 'Menunggu Pembayaran';
                $statusClass = 'bg-warning text-dark';
                break;
            case 'success':
                $statusText = 'Pembayaran Berhasil';
                $statusClass = 'bg-success';
                break;
            case 'cancelled':
                $statusText = 'Pesanan Dibatalkan';
                $statusClass = 'bg-danger';
                break;
            case 'expired':
                $statusText = 'Pembayaran Kadaluarsa';
                $statusClass = 'bg-secondary';
                break;
            default:
                $statusText = 'Status Tidak Diketahui';
                $statusClass = 'bg-dark';
                break;
        }
    @endphp

    <div class="text-center mb-4">
        <h2 class="fw-bold">Status Pesanan Anda</h2>
        <span class="badge {{ $statusClass }} fs-5">{{ $statusText }}</span>
        <p class="mt-2">Invoice: <strong>{{ $order->invoice }}</strong></p>
    </div>

    <!-- RINGKASAN ITEM -->
    <div class="card mb-4">
        <div class="card-header fw-bold">Detail Pesanan</div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @php $items = json_decode($order->items, true); @endphp
                    @foreach($items as $item)
                        <tr>
                            <td>
                                <img src="{{ asset($item['image']) }}" class="product-img" alt="">
                                <strong>{{ $item['name'] }}</strong>
                                @if(!empty($item['variant_name']))
                                    <br><small>Varian: {{ $item['variant_name'] }}</small>
                                @endif
                                @if(!empty($item['size_name']))
                                    <br><small>Size: {{ $item['size_name'] }}</small>
                                @endif
                            </td>
                            <td>Rp {{ number_format($item['price'],0,'','.') }}</td>
                            <td>{{ $item['qty'] }}</td>
                            <td>Rp {{ number_format($item['price'] * $item['qty'],0,'','.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- ONGKIR & TOTAL -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <span>Ongkos Kirim ({{ $order->jenis_ongkir ?? 'Reguler' }})</span>
                <span>Rp {{ number_format($order->total_ongkir,0,'','.') }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Catatan</span>
                <span>{{ $order->note ?? '-' }}</span>
            </div>
            <hr>
            <div class="d-flex justify-content-between fw-bold">
                <span>Total Tagihan</span>
                <span>Rp {{ number_format($order->total_tagihan,0,'','.') }}</span>
            </div>
        </div>
    </div>

    <!-- ALAMAT -->
    <div class="card mb-4">
        <div class="card-header fw-bold">Alamat Pengiriman</div>
        <div class="card-body">
            @if($order->address)
                <div>{{ $order->address->name }} | {{ $order->address->phone }}</div>
                <div>{{ $order->address->address }}</div>
                <div>{{ $order->address->district->name ?? '-' }},
                     {{ $order->address->city->name ?? '-' }},
                     {{ $order->address->province->name ?? '-' }}</div>
            @else
                <div>-</div>
            @endif
        </div>
    </div>

    <!-- ACTION BUTTON / PESANAN BERHASIL -->
    <div class="text-center mb-5">
        @if($status === 'pending')
            <p class="mb-3">Silakan lakukan pembayaran untuk memproses pesanan Anda.</p>
            <form action="{{ route('checkout.pay.xendit', $order->invoice) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary">Bayar Sekarang</button>
            </form>
            <form action="{{ route('payment.cancel', $order->invoice) }}" method="POST" class="d-inline ms-2" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">
                @csrf
                <button type="submit" class="btn btn-danger">Batalkan Pesanan</button>
            </form>
        @elseif($status === 'success')
            <p class="mb-3 text-success fw-bold">Pembayaran Anda telah diterima. Pesanan sedang diproses.</p>
            <a href="{{ route('home') }}" class="btn btn-primary">Kembali ke Beranda</a>
        @elseif($status === 'cancelled')
            <p class="mb-3 text-danger fw-bold">Pesanan Anda dibatalkan.</p>
            <a href="{{ route('home') }}" class="btn btn-primary">Kembali ke Beranda</a>
        @elseif($status === 'expired')
            <p class="mb-3 text-secondary fw-bold">Pembayaran Anda telah kadaluarsa. Silakan lakukan pemesanan ulang.</p>
            <a href="{{ route('home') }}" class="btn btn-primary">Kembali ke Beranda</a>
        @endif
    </div>

</div>
@endsection
