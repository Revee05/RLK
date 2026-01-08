@extends('account.partials.layout')
@section('css')

@endsection
@section('content')
<section class="py-4" id="customer-account">
    <div class="container mt-6 mb-7">
    <div class="row justify-content-center">
      <div class="col-lg-12 col-xl-7">
        <div class="card">
          <div class="card-body p-5">
            <h2 class="text-center">
              INVOICE
            </h2>
            <p class="fs-sm text-center">
              Invoice Pembayaran Lelang
            </p>

            <div class="border-top border-gray-200 pt-4 mt-4">
              <div class="row">
                <div class="col-md-7">
                  <div class="text-muted mb-2">No Invoice.</div>
                  <strong>#{{$order->order_invoice}}</strong>
                </div>
                <div class="col-md-5 text-md-end">
                  <div class="text-muted mb-2">Tanggal Bayar</div>
                  <strong>{{$order->tanggal_order}}</strong>
                </div>
              </div>
            </div>

            <div class="border-top border-gray-200 mt-4 py-4">
              <div class="row">
                <div class="col-md-12">
                  <div class="text-muted mb-2">Alamat Pengiriman</div>
                  <strong>
                    {{Auth::user()->name}}
                  </strong>
                  <p class="fs-sm">
                    {{ucfirst($order->label_address)}} - {{ucfirst($order->address)}}
                    @if(optional($order->kecamatan)->nama_kecamatan)
                      , {{ucfirst(optional($order->kecamatan)->nama_kecamatan)}}
                    @endif
                    @if(optional($order->kabupaten)->nama_kabupaten)
                      , {{ucfirst(optional($order->kabupaten)->nama_kabupaten)}}
                    @endif
                    @if(optional($order->provinsi)->nama_provinsi)
                      , {{ucfirst(optional($order->provinsi)->nama_provinsi)}}
                    @endif
                    <br>
                  </p>
                </div>
              </div>
            </div>

            <table class="table border-bottom border-gray-200 mt-3">
              <thead>
                <tr>
                  <th scope="col" class="fs-sm text-dark text-uppercase-bold-sm px-0">Deskripsi</th>
                  <th scope="col" class="fs-sm text-dark text-uppercase-bold-sm text-end px-0">Harga</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="px-0">{{$order->product->title}}</td>
                  <td class="text-end px-0">{{number_format($order->bid_terakhir)}}</td>
                </tr>
              </tbody>
            </table>

            <div class="mt-5">
              <div class="d-flex justify-content-end">
                <p class="text-muted me-3">Total Ongkos Kirim:</p>
                <span>{{number_format($order->total_ongkir)}}</span>
              </div>
              <div class="d-flex justify-content-end">
                <p class="text-muted me-3">Asuransi:</p>
                @if($order->asuransi_pengiriman > 0)
                <span>10%</span>
                @else
                <span>0</span>
                @endif
              </div>
              <div class="d-flex justify-content-end mt-3">
                <h5 class="me-3">Total Pembayaran:</h5>
                <h5 class="text-success">{{number_format($order->total_tagihan)}}</h5>
              </div>
            </div>
          </div>
           @if ($order->payment_status == 1)
            <button class="btn btn-dark btn-lg card-footer-btn justify-content-center text-uppercase-bold-sm hover-lift-light" id="pay-button">Bayar Sekarang</button>
            @else
                <div class="btn btn-success btn-lg card-footer-btn justify-content-center text-uppercase-bold-sm hover-lift-light">
                    Pembayaran berhasil
                </div>
            @endif
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
@section('js')
    <script type="text/javascript" src='//ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js'></script>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
        const payButton = document.querySelector('#pay-button');
        payButton.addEventListener('click', function(e) {
            e.preventDefault();
 
            snap.pay('{{ $snapToken }}', {
                // Optional
                onSuccess: function(result) {
                    /* You may add your own js here, this is just example */
                    // document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                    console.log(result)
                },
                // Optional
                onPending: function(result) {
                    /* You may add your own js here, this is just example */
                    // document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                    console.log(result)
                },
                // Optional
                onError: function(result) {
                    /* You may add your own js here, this is just example */
                    // document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                    console.log(result)
                }
            });
        });
    </script>
@endsection