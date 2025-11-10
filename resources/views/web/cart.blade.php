{{-- filepath: resources/views/web/cart.blade.php --}}
@extends('web.partials.layout')

@section('content')
<div class="container my-5">
    <h2>Keranjang Belanja</h2>
    @if(session('cart') && count(session('cart')) > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Harga</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp
                @foreach(session('cart') as $item)
                    @php $total = $item['price'] * $item['quantity']; $grandTotal += $total; @endphp
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>Rp{{ number_format($item['price'], 0, ',', '.') }}</td>
                        <td>Rp{{ number_format($total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <h4>Total: Rp{{ number_format($grandTotal, 0, ',', '.') }}</h4>
        <a href="#" class="btn btn-primary">Checkout</a>
    @else
        <p>Keranjang belanja kosong.</p>
    @endif
</div>
@endsection