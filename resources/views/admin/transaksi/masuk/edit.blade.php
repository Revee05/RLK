@extends('admin.partials._layout')
@section('title','Edit transaksi masuk')
@section('collapseTransaksi','show')
@section('masuk','active')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style type="text/css">
    select option {
      text-transform: capitalize;
    }
    .select2-container--default .select2-selection--single {
        border-radius: 0px !important;
    }
    /*.select2-container--default .select2-selection--single .select2-selection__clear {
        display: none !important;
    }*/
    .btn-circle-sm {
        height: 20px !important;
        width: 20px !important;
        font-size: 10px !important;
    }
    .select2-container--default .select2-selection--single {
        background-color: #fff;
        border: 1px solid #d1d3e2 !important;
        border-radius: 4px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #6e707e;
        line-height: 28px;
    }
    .select2-container--default .select2-selection--single .select2-selection__clear {
        color: #888888;
    }
</style>
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h5 mb-4 text-gray-800">Transaksi
    <small>Masuk</small>
    {{-- <a href="" class="btn btn-primary btn-sm float-right"><i class="fa fa-plus-circle"></i> Create</a> --}}
    </h1>
    <div class="row">

        {{-- Erors notification --}}
        @include('admin.partials._errors')
        <div class="col-sm-5">
            
            {{ Form::open(array('route' => 'transaksi.transaksi.store')) }}
            <div class="card shadow mb-4 rounded-0">
                <div class="card-header py-1">
                <div class="d-flex">
                    <div class="d-block mr-auto text-gray-800">Cari Barang</div>
                    <div class="d-block">Stok</div>
                </div>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-sm-10">
                        <select id="barang_id" name="barang_id" class="form-control form-control-sm rounded-0">
                            <option value='0'>- Search Barang -</option>
                        </select>
                            
                        </div>
                        <div class="col-sm-2">
                            <input type="text" name="stok" class="form-control form-control-sm rounded-0 shadow-none" size="1" id="stok" disabled>
                        </div>
                    </div>
                       
                </div>
                <div class="card-footer">
                    <div class="float-right d-flex">
                        <div class="d-block m-auto">Jumlah : </div>
                        <div class="d-block pl-2"> 
                            <input type="number" name="amount" class="form-control form-control-sm rounded-0 pl-2 shadow-none" size="1" style="width:70px" id="jumlah" disabled>
                            <input type="text" name="edit" value="true" hidden>
                            <input type="text" name="kode_transaksi" value="{{$masuk->kode_transaksi}}" hidden>
                        </div>
                        <div class="d-block pl-2">
                            <input type="hidden" name="type" value="masuk">
                            <button type="submit" class="btn btn-sm btn-primary rounded-0">Tambah</button>
                        </div>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
        <div class="col-sm-7">
            {{ Form::model($masuk, array('route' => array('transaksi.masuk.update', $masuk->id), 'method' => 'PUT')) }}
            <div class="card">
                <div class="card-header">
                    <div class="d-flex">
                        <div class="d-block mr-auto">Barang Masuk</div>
                        <div class="d-block">Tanggal : {{date('m/d/Y')}}</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row pb-3">
                        <div class="col-sm-6">
                            <input type="date" name="date_of_entry" class="form-control form-control-sm rounded-0" value="{{$masuk->date_of_entry}}">
                        </div>
                        <div class="col-sm-6">
                            <input type="text" name="supplier" class="form-control form-control-sm rounded-0" value="{{$masuk->supplier}}">
                        </div>
                        <div class="col-sm-12 mt-2">
                            <div class="text-xs">Catatan :</div>
                            <textarea class="form-control form-control-sm" name="note">{{$masuk->note}}</textarea>
                             <input type="text" name="kode_transaksi" value="{{$masuk->kode_transaksi}}" hidden>
                        </div>
                        
                    </div>
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Kode Barang</th>
                                <th>Deskripsi</th>
                                <th>Jumlah</th>
                                <th>Satuan</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($masuk->transaksis as $trans)
                            <tr class="delete_mem{{$trans->id}}">
                                <td>{{$trans->barang->code}}</td>
                                <td>{{$trans->barang->name}}</td>
                                <td>{{$trans->amount}}</td>
                                <td>{{ucfirst($trans->barang->uom->description)}}</td>
                                  <td class="text-center">
                                    <a data-id="{{$trans->id}}" class="delete d-block text-decoration-none text-xs">
                                        <div class="btn btn-circle btn-circle-sm btn-sm bg-danger">
                                            <i class="fa fa-minus text-white"></i>
                                        </div>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-sm btn-primary rounded-0">Update</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
<!-- /.container-fluid -->
@endsection
@section('js')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script >

(function() {
 
    $("#barang_id").select2({
        placeholder: 'Search Barang',
        allowClear: true,
        ajax: {
            url: '/admin/transaksi/cari/barang',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                console.log(params);
                return {
                    term: params.term || '',
                    page: params.page || 1
                }
            },
            cache: true
        }
    });
})();

$('#barang_id').change(function(){
    var id = $('#barang_id option:selected').val();
    if (id) {

        $('#jumlah').prop('disabled', false);
        
        $.ajax({
            url: '/admin/transaksi/stok/barang/'+id,
            type:'GET',
            dataType: 'json',
            delay: 250,
            data: {
                id:id
            },
            cache: true,
            success:function(data){
                if (data['stock'] == undefined || data['stock'] == null) {
                    $('#stok').value("0");
                }
                $('#stok').val(data['stock']);
            }
        })
    }
});
$(".delete").click(function(){
    var id = $(this).data("id");
    var token = $("meta[name='csrf-token']").attr("content");
    if (confirm("Are you sure you want to delete this transaction ?")) {
        $.ajax({
            url: "/admin/transaksi/transaksi/"+id,
            type: "DELETE",
            data: {
                "id": id,
                "_token": token,
                "type": "editmasuk",
            },
            cache: false,
            success: function(html) {
                $(".delete_mem" + id).fadeOut('slow');
            }
        });
    } else {
        return false;
    }
});
</script>
@endsection