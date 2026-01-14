@extends('admin.partials._layout')
@section('title','Transaksi')
@section('transaksi','active')
@section('css')
<!-- Custom styles for this page -->
<link href="{{asset('assets/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endsection

@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <h1 class="h5 mb-4 text-gray-800">Transaksi <small>Daftar Order</small></h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            @include('admin.partials._success')
            <div class="table-responsive">
                <table class="table table-bordered table-sm" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Invoice</th>
                            <th>User</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Type</th>
                            <th>Tanggal</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                            function normalizeStatusLabel($raw){
                                if(is_null($raw) || $raw === '') return ['label'=>'Menunggu Pembayaran','class'=>'badge-info'];
                                $s = strtolower((string)$raw);
                                // numeric mapping
                                if(preg_match('/^\d+$/',$s)){
                                    if($s === '1') $n = 'pending';
                                    elseif($s === '2') $n = 'success';
                                    elseif($s === '3') $n = 'expired';
                                    else $n = 'cancelled';
                                } else {
                                    if(preg_match('/pending|menunggu|wait|waiting|menunggu pembayaran/',$s)) $n = 'pending';
                                    elseif(preg_match('/success|paid|lunas|sudah dibayar/',$s)) $n = 'success';
                                    elseif(preg_match('/expired|kadaluarsa|kadaluwarsa/',$s)) $n = 'expired';
                                    elseif(preg_match('/cancel|batal|cancelled|canceled/',$s)) $n = 'cancelled';
                                    else $n = $s;
                                }
                                $mapLabel = ['pending'=>'Menunggu Pembayaran','success'=>'Sudah dibayar','expired'=>'Kadaluarsa','cancelled'=>'Batal'];
                                $mapClass = ['pending'=>'badge-info','success'=>'badge-success','expired'=>'badge-danger','cancelled'=>'badge-dark'];
                                $label = $mapLabel[$n] ?? ucfirst($n);
                                $class = $mapClass[$n] ?? 'badge-secondary';
                                return ['label'=>$label,'class'=>$class];
                            }
                        @endphp
                        @if(!empty($orders))
                            @foreach($orders as $order)
                            <tr>
                                <td>{{$no++}}</td>
                                <td>{{ $order->order_invoice ?? $order->invoice ?? '-' }}</td>
                                <td>{{ optional($order->user)->name ?? ($order->name ?? '-') }}</td>
                                <td>{{ number_format($order->total_tagihan ?? 0,0,',','.') }}</td>
                                @php $s = normalizeStatusLabel($order->status ?? $order->payment_status ?? null); @endphp
                                <td><span class="badge {{ $s['class'] }}">{{ $s['label'] }}</span></td>
                                <td>Produk</td>
                                <td>{{ $order->created_at }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.transaksi.show', $order->id) }}" class="btn btn-sm btn-info">Detail</a>
                                </td>
                            </tr>
                            @endforeach
                        @endif

                        @if(!empty($ordersMerch))
                            @foreach($ordersMerch as $om)
                            <tr>
                                <td>{{$no++}}</td>
                                <td>{{ $om->invoice ?? '-' }}</td>
                                <td>{{ optional($om->user)->name ?? '-' }}</td>
                                <td>{{ number_format($om->total_tagihan ?? 0,0,',','.') }}</td>
                                @php $sm = normalizeStatusLabel($om->status ?? null); @endphp
                                <td><span class="badge {{ $sm['class'] }}">{{ $sm['label'] }}</span></td>
                                <td>Merch</td>
                                <td>{{ $om->created_at }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.transaksi.show', ['id' => $om->id, 'type' => 'merch']) }}" class="btn btn-sm btn-info">Detail</a>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->
@endsection

@section('js')
    <script src="{{asset('assets/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
          $('#dataTable').DataTable();
        });
    </script>
@endsection
