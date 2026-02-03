@extends('admin.partials._layout')
@section('title','Daftar Product')
@section('collapseMaster','show')
@section('product','active')
@section('master-product','active')
@section('css')
<!-- Custom styles for this page -->
<link href="{{asset('assets/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
<style type="text/css">
    .figure {
        height: 80px;
        width: 80px;
        overflow: hidden;
        position: relative;
          border: 1px solid  #0425ffff;
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
    <h1 class="h5 mb-4 text-gray-800">Master 
        <small>Product</small>
        <a href="{{route('master.product.create')}}" class="btn btn-primary btn-sm float-right">
            <i class="fa fa-plus-circle"></i> Tambah product
        </a>
    </h1>

    {{-- Success notification --}}
    @include('admin.partials._success')
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center">Foto</th>
                            <th>Nama Produk</th>
                            <th>Seniman</th>
                            <th>Harga</th>
                            <th>Tanggal Berakhir</th>
                            <th>Tanggal</th>
                            <th width="12%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $produk)
                        <tr>
                            <td class="text-center">
                                <div class="figure">
                                    <img src="{{asset($produk->imageUtama->path ?? 'assets/img/default.jpg')}}">
                                </div>
                            </td>
                            <td>{{ucfirst($produk->title)}}
                                <br>
                                <span class="badge bg-primary text-white rounded-0">
                                    {{ucfirst($produk->kategori->name)}}
                                </span>
                                {!!$produk->status_txt!!}
                                
                            </td>
                            <td>{{ucfirst($produk->karya->name)}}</td>
                            <td>{{$produk->price_str}}</td>
                            <td>{{$produk->end_date}}</td>
                            <td>{{$produk->created_at}}</td>
                            <td class="text-center">
                                @if($produk->status == '1')
                                 <a href="{{route('master.product.status',$produk->id)}}" class="btn btn-sm btn-primary rounded-0" title="PUBLISH">
                                    <i class="fa fa-xs fa-check"></i>
                                </a>
                                @elseif($produk->status == '2')
                                 <a href="{{route('master.product.status',$produk->id)}}" class="btn btn-sm btn-success rounded-0" title="EXPIRED">
                                    <i class="fas fa-calendar-times"></i>
                                </a>
                                @else
                                <a href="{{route('master.product.status',$produk->id)}}" class="btn btn-sm btn-warning rounded-0" title="DRAFT">
                                    <i class="fa fa-xs fa-times"></i>
                                </a>
                                @endif
                                <a href="{{route('master.product.edit',$produk->id)}}" class="btn btn-sm btn-info rounded-0" title="EDIT">
                                    <i class="fa fa-xs fa-pencil-alt"></i>
                                </a>
                                <form action="{{route('master.product.destroy',[$produk->id])}}" method="post" class="d-inline">
                                    @method('delete')
                                    @csrf
                                    <button onclick="return confirm('Are you sure want to delete this record?')"
                                        type="submit" class="btn btn-danger btn-sm rounded-0">
                                        <i class="fas fa-xs fa-trash"></i>
                                    </button>
                                </form>

                                <a href="{{route('master.product.reset.bid',$produk->id)}}" class="btn btn-sm btn-warning rounded-0" title="RESET BID">
                                    <i class="fa fa-xs fa-sync-alt"></i>
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