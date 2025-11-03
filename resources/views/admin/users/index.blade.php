@extends('admin.partials._layout')
@section('title','Daftar User')
@section('user','active')
@section('css')
<!-- Custom styles for this page -->
<link href="{{asset('assets/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endsection
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h5 mb-4 text-gray-800">Daftar 
        <small>Users</small>
        <a href="{{route('admin.user.create')}}" class="btn btn-primary btn-sm float-right"><i class="fa fa-plus-circle"></i> Tambah User</a>
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
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Access</th>
                            <th>Status</th>
                            <th>Register</th>
                            <th width="10%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $no=1;
                        @endphp
                        @foreach($users as $user)
                        <tr>
                            <td>{{$no++}}</td>
                            <td>{{ucfirst($user->name)}}</td>
                            <td>{{$user->email}}</td>
                            <td>{{ucfirst($user->access)}}</td>
                            <td>
                                @if($user->email_verified_at)
                                    <span class="badge bg-primary text-white rounded-0">
                                        ACTIVE
                                    </span>
                                @else 
                                    <span class="badge bg-danger text-white rounded-0">
                                        NON ACTIVE
                                    </span>
                                @endif
                            </td>
                            <td>{{$user->created_at}}</td>
                            <td class="text-center">
                                 @if($user->email_verified_at)
                                 <a href="{{route('admin.user.status',$user->id)}}" class="btn btn-sm btn-primary rounded-0">
                                    <i class="fa fa-xs fa-check"></i>
                                </a>
                                @else
                                <a href="{{route('admin.user.status',$user->id)}}" class="btn btn-sm btn-warning rounded-0">
                                    <i class="fa fa-xs fa-times"></i>
                                </a>
                                @endif
                                <a href="{{route('admin.user.edit',$user->id)}}" class="btn btn-sm btn-info rounded-0">
                                    <i class="fa fa-pencil-alt"></i>
                                </a>
                                <form action="{{route('admin.user.destroy',[$user->id])}}" method="post" class="d-inline">
                                    @method('delete')
                                    @csrf
                                    <button onclick="return confirm('Are you sure want to delete this record?')"
                                        type="submit" class="btn btn-danger btn-sm rounded-0">
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