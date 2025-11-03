@extends('admin.partials._layout')
@section('title','Detail Pemenang Product')
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
    .bid-detail ul{
        height:300px;
        width:100%;
        padding: 10px;
    }
    .bid-detail ul{overflow:hidden; overflow-y:scroll;}
</style>
@endsection
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h5 mb-4 text-gray-800">Detail 
        <small>Penawaran</small>
    </h1>
    <div class="row">
        <div class="col-md-12">
            <!-- DataTales Example -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    Data Pemenang Lelang & Bidding
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row mb-0">
                                <label class="col-sm-4 font-weight-bold">Nama Pemenang</label>
                                <div class="col-sm-8">
                                  : {{$detailBid->user->name}}
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <label class="col-sm-4 font-weight-bold">Alamat</label>
                                <div class="col-sm-8">
                                  : {{$order->address ?? ''}}
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <label class="col-sm-4 font-weight-bold">Telephon</label>
                                <div class="col-sm-8">
                                  : {{$order->phone ?? ''}}
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <label class="col-sm-4 font-weight-bold">Email</label>
                                <div class="col-sm-8">
                                  : Rp {{$detailBid->user->email}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row mb-0">
                                <label class="col-sm-4 font-weight-bold">Nominal Penawaran</label>
                                <div class="col-sm-8">
                                  : {{$detailBid->price ?? ''}}
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <label class="col-sm-4 font-weight-bold">Tanggal Penawaran</label>
                                <div class="col-sm-8">
                                  : {{$detailBid->created_at ?? ''}}
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <label class="col-sm-4 font-weight-bold">Status Pembayaran</label>
                                <div class="col-sm-8">
                                  : {{$order->payment_status ?? ''}}
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <label class="col-sm-4 font-weight-bold">Email</label>
                                <div class="col-sm-8">
                                  : Rp {{$detailBid->user->email}}
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            a
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-block">
                <a href="{{route('admin.daftar-pemenang.index')}}" class="btn btn-primary btn-sm rounded-0">
                    <i class="fa fa-arrow-left"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>
    
</div>
<!-- /.container-fluid -->
@endsection