@extends('web.partials.layout')

@section('title', 'Checkout Success')

@section('content')
<div class="container my-5">

    <div class="text-center mb-4">
        <h2 class="fw-bold">Terima kasih telah berbelanja!</h2>
        <p>Pesanan Anda berhasil dibuat. Berikut ringkasan pesanan:</p>
        <span class="badge bg-success">Invoice: {{ $order->invoice }}</span>
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
                    @php
                        $items = json_decode($order->items, true);
                    @endphp

                    @foreach($items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item['name'] }}</strong>
                                @if(isset($item['variant_name']))
                                    <br><small>Varian: {{ $item['variant_name'] }}</small>
                                @endif
                                @if(isset($item['size_name']))
                                    <br><small>Size: {{ $item['size_name'] }}</small>
                                @endif
                            </td>
                            <td>Rp {{ number_format($item['price'],0,'','.') }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>Rp {{ number_format($item['price'] * $item['quantity'],0,'','.') }}</td>
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

    <!-- LANGKAH PEMBAYARAN -->
    <div class="card mb-4">
        <div class="card-header fw-bold">Langkah Pembayaran</div>
        <div class="card-body">
            <ol>
                <li>Pilih metode pembayaran sesuai yang sudah Anda pilih sebelumnya.</li>
                <li>Lakukan pembayaran sesuai jumlah total tagihan di atas.</li>
                <li>Simpan bukti pembayaran jika menggunakan transfer bank atau QRIS.</li>
                <li>Pesanan akan diproses setelah pembayaran terkonfirmasi.</li>
            </ol>
        </div>
    </div>

    <div class="text-center">
        <a href="{{ route('home') }}" class="btn btn-primary">Kembali ke Beranda</a>
    </div>

</div>
@endsection
