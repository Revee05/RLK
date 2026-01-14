@extends('admin.partials._layout')
@section('title','Detail transaksi masuk')
@section('collapseTransaksi','show')
@section('masuk','active')
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="row justify-content-md-center">
        <div class="col-sm-10">
            <!-- Page Heading -->
            <h1 class="h5 mb-4 text-gray-800">
                <i class="fa fa-shopping-cart"></i> Transaksi
            <small>Masuk</small>
            <a href="{{route('transaksi.masuk.index')}}" class="btn btn-primary btn-sm float-right">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>
            </h1>
            <!-- DataTales Example -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    
                    <div class="d-flex border-bottom pb-2">
                        <div class="d-block mr-auto px-2 "><b>Date :</b> {{$masuk->date_of_entry}}</div>
                        <div class="d-block">
                            <div class="d-flex">
                                <div class="d-block px-2 border-left">
                                    <a href="{{route('print.masuk',$masuk->id)}}" target="_blank" class="text-decoration-none">
                                        <i class="fa fa-print"></i>
                                    </a>
                                </div>
                                <div class="d-block px-2 border-left">
                                    <a href="{{route('transaksi.masuk.edit',$masuk->id)}}" class="text-decoration-none">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-block pt-5 p-2 pb-3 text-gray-800">
                        <b>Supplier :</b> {{ucwords($masuk->supplier)}}
                    </div>
                    <div class="d-block border-bottom pb-4">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Jumlah</th>
                                    <th>Satuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $no=1;
                                @endphp
                                @foreach($masuk->transaksis as $m)
                                    <tr>
                                        <td>{{$no++}}</td>
                                        <td>{{$m->barang->code}}</td>
                                        <td>{{ucfirst($m->barang->name)}}</td>
                                        <td>{{$m->amount}}</td>
                                        <td>{{ucfirst($m->barang->uom->name)}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right pt-2">Total jenis barang : <span class="text-primary">{{count($masuk->transaksis)}} item</span></div>
                    <div class="d-block pt-3">
                        <div class="alert alert-secondary rounded-0" role="alert">
                            <span class="d-block text-sm">Catatan :</span>
                            {{$masuk->note}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->
@endsection