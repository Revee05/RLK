@extends('web.partials.layout')

@section('css')
<link rel="stylesheet" href="{{ asset('css/checkout/preview.css') }}">
@endsection

@section('content')
<div class="container py-5">

    <h3>Ringkasan Pesanan</h3>

    <p><strong>Invoice:</strong> {{ $order->invoice }}</p>
    <p><strong>Alamat:</strong> {{ $order->address->address }}, {{ $order->address->district->name }}, {{ $order->address->city->name }}, {{ $order->address->province->name }}</p>
    <p><strong>Catatan:</strong> {{ $order->note ?? '-' }}</p>

    <table class="table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Qty</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach(json_decode($order->items) as $item)
            <tr>
                <td>{{ $item->name }} {{ $item->variant_name ?? '' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>Rp {{ number_format($item->price,0,'','.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>Total Ongkir:</strong> Rp {{ number_format($order->total_ongkir,0,'','.') }}</p>
    <p><strong>Total Tagihan:</strong> Rp {{ number_format($order->total_tagihan,0,'','.') }}</p>

    <h5>Pilih Metode Pembayaran</h5>
    <div class="payment-option mb-2 border rounded p-2" data-name="QRIS">
        QRIS
    </div>
    <div class="payment-option mb-2 border rounded p-2" data-name="Bank Transfer">
        Bank Transfer
    </div>

    <form action="{{ route('checkout.pay.xendit', $order->invoice) }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary mt-3">Bayar Sekarang</button>
    </form>

</div>
@endsection
