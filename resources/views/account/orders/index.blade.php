@extends('account.partials.layout')
@section('css')
<!-- Custom styles for this page -->
<link href="{{asset('assets/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endsection

@section('content')
<section class="py-4" id="customer-account">
    <div class="container">
        <div class="row bg-white py-4" style="border-radius: 10px;">
            <div class="col-sm-3 border-end">
                @include('account.partials.nav')
            </div>
            <div class="col-sm-9">
                <div class="card">
                    <div class="card-header bg-transparant">
                        RIWAYAT PESANAN
                    </div>
                    <div class="card-body">
                   
                        <table class="table table-bordered table-sm" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No Invoice</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Payment Status</th>
                            <th>Status Pesanan</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $no=1;
                        @endphp
                        @foreach($orders as $order)
                        <tr>
                            <td>{{$order->order_invoice}}</td>
                            <td>{{ucfirst($order->product->title)}}</td>
                            <td>Rp. {{number_format($order->total_tagihan)}}</td>
                            <td>
                                @if($order->payment_status == '1')
                                 <span class="badge bg-primary text-white rounded-0">
                                    Menunggu Pembayaran
                                </span>
                                @elseif($order->payment_status == '2')
                                <span class="badge bg-success text-white rounded-0">
                                    Sudah dibayar
                                </span>
                                @elseif($order->payment_status == '3')
                                <span class="badge bg-warning text-white rounded-0">
                                    Kadaluarsa
                                </span>
                                
                                @else()
                                <span class="badge bg-info text-white rounded-0">
                                    Batal
                                </span>
                                @endif
                            </td>
                            <td>
                                @if($order->status_pesanan == '1')
                                 <span class="badge bg-primary text-white rounded-0">
                                    Belum diproses
                                </span>
                                @elseif($order->status_pesanan == '2')
                                <span class="badge bg-success text-white rounded-0">
                                    diproses
                                </span>
                                @elseif($order->status_pesanan == '3')
                                <span class="badge bg-warning text-white rounded-0">
                                    dikirim
                                </span>
                                
                                @else()
                                <span class="badge bg-info text-white rounded-0">
                                    dikembalikan
                                </span>
                                @endif
                            </td>
                            <td></td>
                            <td class="text-center">
                               {{--  <a href="{{route('account.orders.edit',$order->id)}}" class="btn btn-sm btn-info rounded-0">
                                    <i class="fa fa-pencil-alt"></i>
                                </a> --}}
                                <a href="{{route('account.orders.show',$order->id)}}" class="btn btn-sm btn-info rounded-0">
                                    <i class="fa fa-eye"></i>
                                </a>
                                
                               {{--  <form action="{{route('account.orders.destroy',[$order->id])}}" method="post" class="d-inline">
                                    @method('delete')
                                    @csrf
                                    <button onclick="return confirm('Are you sure want to delete this record?')"
                                        type="submit" class="btn btn-danger btn-sm rounded-0">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form> --}}

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('js')
    <!-- Page level plugins -->
    <script src="{{asset('assets/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script type="text/javascript">
        // Call the dataTables jQuery plugin
        $(document).ready(function() {
          $('#dataTable').DataTable();
        });
    </script>
@endsection