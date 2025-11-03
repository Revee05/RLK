@extends('admin.partials._layout')
@section('title','Dashboard')
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Dashboard Page</h1>
    <div class="row">
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Produk</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{$dashboard['product']}}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Seniman
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{$dashboard['seniman']}}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Pengguna
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{$dashboard['users']}}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pending Requests Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Member
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{$dashboard['member']}}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Produk di Lelang</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{$lelang['aktiv']}}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Produk Expired
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{$lelang['expired']}}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Produk di Bid
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{$dashboard['users']}}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pending Requests Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Produk Terjual
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{$dashboard['member']}}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white text-center">
                    5 Produk baru terakhir yang ditambahkan
                </div>
                {{-- <div class="card-body"> --}}
                    <table class="table table-striped table-sm">
                        <thead>
                                <th>Tanggal Post</th>
                                <th>Nama Produk</th>
                                <th>Stok</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($daftar['produk'] as $d)
                            <tr>
                                <td> {{ Carbon\Carbon::parse($d->created_at)->format('d/m/Y') }}</td>
                                <td>{{$d->title}}</td>
                                <td>{{$d->stock}}</td>
                                <td>Rp.{{$d->price}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                {{-- </div> --}}
            </div>
        </div>
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-warning text-white text-center">
                    5 Produk Transaksi Terakhir
                </div>
                {{-- <div class="card-body"> --}}
                    <table class="table table-striped table-sm">
                        <thead>
                                <th>Tanggal Transaksi</th>
                                <th>Nama Pemesan</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($daftar['transaksi'] as $trans)
                            <tr>
                                <td> {{ Carbon\Carbon::parse($trans->created_at)->format('d/m/Y') }}</td>
                                <td>{{$trans->name}}</td>
                                <td>Rp. {{number_format($trans->total_tagihan)}}</td>
                                <td>{!!$trans->status_txt!!}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                {{-- </div> --}}
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->
@endsection