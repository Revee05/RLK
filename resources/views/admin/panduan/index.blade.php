@extends('admin.partials._layout')
@section('title', 'Panduan Pengguna')
@section('panduan', 'active')

@section('css')
    <link href="{{ asset('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <style>
        .pdf-frame { width: 100%; height: 80vh; border: none; }
    </style>
@endsection

@section('content')
    <div class="container-fluid">

        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="h5 mb-0 text-gray-800">
                Panduan <small>Pengguna</small>
            </h1>
            <a href="{{ route('admin.panduan.create') }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="fa fa-plus-circle"></i> Tambah Panduan Baru
            </a>
        </div>
        <div class="card shadow mb-4">
            <div class="card-body">
                @include('admin.partials._success')

                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead class="text-center bg-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Judul Panduan</th>
                                <th style="width: 150px;">File PDF</th>
                                <th style="width: 160px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($panduan as $p)
                                <tr>
                                    <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                    <td class="align-middle">{{ $p->title }}</td>
                                    
                                    <td class="text-center align-middle">
                                        @if ($p->file_path)
                                            <button class="btn btn-info btn-sm btn-block" data-toggle="modal"
                                                data-target="#previewModal{{ $p->id }}">
                                                <i class="fas fa-eye"></i> Preview
                                            </button>
                                        @else
                                            <span class="badge badge-secondary">Kosong</span>
                                        @endif
                                    </td>

                                    <td class="text-center align-middle">
                                        <a href="{{ route('admin.panduan.edit', $p->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-danger btn-sm open-delete-modal"
                                            data-id="{{ $p->id }}" data-title="{{ $p->title }}" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                @if ($p->file_path)
                                    <div class="modal fade" id="previewModal{{ $p->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-xl">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ $p->title }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body p-0">
                                                    <iframe src="{{ asset($p->file_path) }}" class="pdf-frame"></iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deletePanduanModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deleteForm" method="POST">
                    @csrf @method('DELETE')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>Yakin ingin menghapus panduan:</p>
                        <h5 id="deletePanduanTitle" class="font-weight-bold text-break"></h5>
                        <small class="text-danger">File PDF akan terhapus permanen.</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });

        $(document).on('click', '.open-delete-modal', function() {
            let id = $(this).data('id');
            let title = $(this).data('title');
            $('#deletePanduanTitle').text(title);
            $('#deleteForm').attr('action', '/admin/panduan/hapus/' + id);
            $('#deletePanduanModal').modal('show');
        });
    </script>
@endsection