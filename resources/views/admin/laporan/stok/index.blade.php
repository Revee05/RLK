@extends('admin.partials._layout')
@section('title','Laporan stok barang')
@section('collapseLaporan','show')
@section('lapstok','active')
@section('css')
<!-- Custom styles for this page -->
<link href="{{asset('assets/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endsection
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h5 mb-4 text-gray-800">Laporan 
        <small>Stok</small>
        <a href="{{route('print.stok')}}" target="_blank" class="btn btn-primary btn-sm float-right rounded-0">
            <i class="fa fa-print"></i> Print
        </a>
    </h1>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            {{-- Success notification --}}
            @include('admin.partials._success')
            <div class="table-responsive">
                <table class="table table-bordered table-sm" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $no=1;
                        @endphp
                        @foreach($stoks as $stok)
                        <tr>
                            <td>{{$no++}}</td>
                            <td>{{$stok->code}}</td>
                            <td>{{ucfirst($stok->name)}}</td>
                            <td>{{ucfirst($stok->kategori->name)}}</td>
                            <td>Rp. {{number_format($stok->price)}}</td>
                            <td>{{$stok->stock}} _{{ucfirst($stok->uom->name)}}</td>
                        </tr>
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
          $('#dataTable').DataTable();
        });
    </script>
@endsection