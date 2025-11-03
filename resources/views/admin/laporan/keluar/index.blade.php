@extends('admin.partials._layout')
@section('title','Laporan barang keluar')
@section('collapseLaporan','show')
@section('lapkeluar','active')
@section('css')
<!-- Custom styles for this page -->
<link href="{{asset('assets/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endsection
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h5 mb-4 text-gray-800">
        Laporan 
        <small>Keluar</small>
        <a href="{{route('print.laporan.keluar',['start_date'=> $start_date ?? '','end_date'=> $end_date ?? ''])}}" class="btn btn-primary btn-sm float-right" target="_blank">
            <i class="fa fa-print"></i> 
            Print
        </a>
    </h1>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <form action="{{route('report.keluar')}}" method="get">
               <div class="row">
                  <div class="col">
                      <input type="date" class="form-control form-control-sm" name="start_date" value="{{$start_date ?? ''}}">
                  </div>
                  <div class="col">
                      <input type="date" class="form-control form-control-sm" name="end_date" value="{{$end_date ?? ''}}">
                  </div>
                  <div class="col">
                    <button type="submit" class="btn btn-sm btn-primary rounded-0">Cari</button>
                  </div>
                  
                </div>
                
            </form>
        </div>
        <div class="card-body">
            {{-- Success notification --}}
            @include('admin.partials._success')
            <div class="table-responsive">
                <table class="table table-bordered table-sm" id="dataTable">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Pembeli</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Satuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($keluars as $keluar)
                            <tr style="background: #efefef;">
                                <td>{{$keluar->out_date}}</td>
                                <td>{{ucwords($keluar->buyer)}}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @php
                            $no=1;
                            @endphp
                            @foreach($keluar->transaksis as $trans)
                            <tr class="event">
                                <td></td>
                                <td align="right">{{$no++}}.</td>
                                <td>{{$trans->barang->code}}</td>
                                <td>{{ucfirst($trans->barang->name)}}</td>
                                <td>{{$trans->amount}}</td>
                                <td>1</td>
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->
@endsection
@section('js')
    <!-- Page level plugins -->
    <script src="{{asset('assets/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script type="text/javascript">
        // Call the dataTables jQuery plugin
        $(document).ready(function() {
          $('#dataTable').DataTable({
                    bAutoWidth: false,
                    "aaSorting": []
                    } );
        });
    </script>
@endsection