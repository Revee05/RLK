@extends('admin.partials._layout')
@section('title','Daftar Seniman')
@section('collapseMaster','show')
@section('karya','active')
@section('css')
<!-- Custom styles for this page -->
<link href="{{asset('assets/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
<style>
    .table-hover tbody tr:hover {
        background-color: #f8f9fc;
        transition: background-color 0.2s ease;
    }
    .btn-action {
        margin: 0 2px;
    }
</style>
@endsection
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-friends"></i> Master Seniman
        </h1>
        <a href="{{route('master.karya.create')}}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus-circle fa-sm text-white-50"></i> Tambah Seniman
        </a>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Daftar Seniman
            </h6>
        </div>
        <div class="card-body">
            {{-- Success notification --}}
            @include('admin.partials._success')
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Seniman</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $no=1;
                        @endphp
                        @foreach($karyas as $karya)
                        <tr>
                            <td class="text-center">{{$no++}}</td>
                            <td>
                                <strong>{{ucfirst($karya->name)}}</strong>
                                @if($karya->address)
                                    <br><small class="text-muted"><i class="fas fa-map-marker-alt"></i> {{ Str::limit($karya->address, 50) }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{route('master.karya.edit',$karya->id)}}" class="btn btn-sm btn-info btn-action" title="Edit">
                                    <i class="fa fa-pencil-alt"></i>
                                </a>
                                <form action="{{route('master.karya.destroy',[$karya->id])}}" method="post" class="d-inline">
                                    @method('delete')
                                    @csrf
                                    <button onclick="return confirm('Apakah Anda yakin ingin menghapus seniman ini?')"
                                        type="submit" class="btn btn-danger btn-sm btn-action" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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