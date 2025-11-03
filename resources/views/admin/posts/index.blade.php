@extends('admin.partials._layout')
@section('title','Daftar post')
@section('post','active')
@section('css')
<!-- Custom styles for this post -->
<link href="{{asset('assets/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endsection
@section('content')
<!-- Begin post Content -->
<div class="container-fluid">
    <!-- post Heading -->
    <h1 class="h5 mb-4 text-gray-800">Daftar 
        <small>Pages</small>
        <a href="{{route('admin.posts.create')}}" class="btn btn-primary btn-sm float-right"><i class="fa fa-plus-circle"></i> Tambah page</a>
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
                            <th>Title</th>
                            <th width="10%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $no=1;
                        @endphp
                        @foreach($posts as $post)
                        <tr>
                            <td>{{$no++}}</td>
                            <td>
                                {{ucfirst($post->title)}}<br>
                                <span class="badge bg-danger text-white rounded-0">
                                    {{$post->status}}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{route('admin.posts.edit',$post->id)}}" class="btn btn-sm btn-info rounded-0">
                                    <i class="fa fa-pencil-alt"></i>
                                </a>
                                <form action="{{route('admin.posts.destroy',[$post->id])}}" method="post" class="d-inline">
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
    <!-- post level plugins -->
    <script src="{{asset('assets/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script type="text/javascript">
        // Call the dataTables jQuery plugin
        $(document).ready(function() {
          $('#dataTable').DataTable();
        });
    </script>
@endsection