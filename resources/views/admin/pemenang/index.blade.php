@extends('admin.partials._layout')
{{-- @section('title','Daftar Product') --}}
{{-- @section('collapseMaster','show') --}}
{{-- @section('product','active') --}}
@section('css')
<!-- Custom styles for this page -->
<link href="{{asset('assets/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
<style type="text/css">
    .figure {
        height: 80px;
        width: 80px;
        overflow: hidden;
        position: relative;
          border: 1px solid  #5a5c69;
    }
    .figure img {
        height: 100%;
        width: 100%;
        object-fit: cover;
        object-position: center;
    }
</style>
@endsection
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h5 mb-4 text-gray-800">Daftar 
        <small>Pemenang</small>
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
                            <th class="text-center">Foto</th>
                            <th>Product</th>
                            <th>Nama Pemenang</th>
                            <th>Bid Terakhir</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($daftarPemenang as $bid)
                        <tr>
                            <td class="text-center">
                                <div class="figure">
                                    <img src="{{asset($bid->product->imageUtama->path ?? 'assets/img/default.jpg')}}">
                                </div>
                            </td>
                            <td>{{ucfirst($bid->product->title)}}</td>
                            <td>{{$bid->user->name}}</td>
                            <td>{{$bid->price}}</td>
                            
                            <td class="text-center">
                                <a href="{{route('admin.daftar-pemenang.show',$bid->id)}}" class="btn btn-sm btn-warning rounded-0" title="RESET BID">
                                    <i class="fa fa-xs fa-eye"></i>
                                </a>
                            </td>
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