@extends('admin.partials._layout')
@section('title','Daftar slider')
@section('slider','active')
@section('css')
<link href="{{asset('assets/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
<style>
    /* Tambahan CSS khusus agar gambar rapi di mobile */
    .img-slider {
        max-width: 150px; /* Maksimal lebar di desktop */
        width: 100%;      /* Di mobile dia akan menyesuaikan lebar kolom */
        height: auto;
        object-fit: cover;
        border-radius: 5px;
    }
    /* Di layar sangat kecil (HP), batasi ukuran gambar */
    @media (max-width: 576px) {
        .img-slider {
            max-width: 100px; 
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <h1 class="h5 mb-4 text-gray-800">Master 
        <small>slider</small>
        <a href="{{route('master.sliders.create')}}" class="btn btn-primary btn-sm float-right">
            <i class="fa fa-plus-circle"></i> <span class="d-none d-sm-inline">Tambah slider</span>
        </a>
    </h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            {{-- Success notification --}}
            @include('admin.partials._success')
            
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            {{-- Hide kolom No di layar kecil (mobile) --}}
                            <th width="5%" class="d-none d-md-table-cell text-center">No</th>
                            
                            <th width="20%" class="text-center">Gambar</th> 
                            
                            <th>Nama</th>
                            
                            {{-- Mencegah tombol turun ke bawah (wrapping) --}}
                            <th width="10%" class="text-center text-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $no=1;
                        @endphp
                        @foreach($sliders as $slider)
                        <tr>
                            {{-- Hide kolom No di layar kecil (mobile) --}}
                            <td class="align-middle text-center d-none d-md-table-cell">{{$no++}}</td>
                            
                            <td class="text-center align-middle">
                                @if($slider->image)
                                    <img src="{{ asset('uploads/sliders/' . $slider->image) }}" 
                                         alt="{{ $slider->name }}" 
                                         class="img-fluid img-slider">
                                @else
                                    <span class="badge badge-secondary">No Image</span>
                                @endif
                            </td>

                            {{-- text-break mencegah tabel melebar jika nama sangat panjang --}}
                            <td class="align-middle text-break">{{ucfirst($slider->name)}}</td>
                            
                            <td class="text-center align-middle text-nowrap">
                                <a href="{{route('master.sliders.edit',$slider->id)}}" class="btn btn-sm btn-info rounded-0" title="Edit">
                                    <i class="fa fa-pencil-alt"></i>
                                </a>
                                <form action="{{route('master.sliders.destroy',[$slider->id])}}" method="post" class="d-inline">
                                    @method('delete')
                                    @csrf
                                    <button onclick="return confirm('Apakah Anda yakin ingin menghapus slider ini?')"
                                        type="submit" class="btn btn-danger btn-sm rounded-0" title="Hapus">
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
@endsection

@section('js')
    <script src="{{asset('assets/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
          $('#dataTable').DataTable({
              // Memastikan tabel tetap responsif dalam fitur DataTables
              "responsive": true,
              "autoWidth": false,
              "columnDefs": [
                { "orderable": false, "targets": [1, 3] } // Matikan sorting di kolom Gambar (index 1) dan Aksi (index 3)
              ]
          });
        });
    </script>
@endsection