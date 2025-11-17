@extends('web.partials.layout')

@section('content')
<div class="container my-5 text-center">

    <h2>Pemesanan Berhasil!</h2>
    <p>Invoice: <strong>{{ $order->invoice }}</strong></p>

    <a href="/" class="btn btn-primary mt-3">Kembali ke Beranda</a>

</div>
@endsection
