@extends('admin.partials._layout')
@section('title', 'Panduan Pengguna')
@section('panduan', 'active')

@section('css')
    <link href="{{ asset('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">

    <style>
        .btn-sm {
            padding: 4px 8px !important;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">

        <h1 class="h5 mb-4 text-gray-800">
            Panduan <small>Pengguna</small>
            <a href="{{ route('admin.panduan.create') }}" class="btn btn-primary btn-sm float-right">
                <i class="fa fa-plus-circle"></i> Tambah Panduan Baru
            </a>
        </h1>

        <div class="card shadow mb-4">
            <div class="card-body">

                @include('admin.partials._success')

                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="dataTable" width="100%" cellspacing="0">
                        <thead class="text-center">
                            <tr>
                                <th width="25%">Judul Panduan</th>
                                <th width="25%">File Saat Ini</th>
                                <th width="30%">Upload File Baru</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($panduan as $p)
                                <tr>
                                    <!-- JUDUL -->
                                    <td>{{ $p->title }}</td>

                                    <!-- FILE SAAT INI -->
                                    <td class="text-center">
                                        @if ($p->file_path)
                                            <a href="{{ asset($p->file_path) }}" class="btn btn-info btn-sm"
                                                target="_blank">
                                                Lihat PDF
                                            </a>
                                            <button class="btn btn-secondary btn-sm" data-toggle="modal"
                                                data-target="#previewModal{{ $p->id }}">
                                                Preview
                                            </button>
                                        @else
                                            <span class="text-danger">Belum ada file</span>
                                        @endif
                                    </td>

                                    <!-- UPLOAD BARU -->
                                    <td>
                                        <form method="POST" action="{{ route('admin.panduan.upload', $p->id) }}"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <input type="file" name="pdf" accept="application/pdf"
                                                class="form-control-file" required>
                                            <button class="btn btn-primary btn-sm mt-2">Upload</button>
                                        </form>
                                    </td>

                                    <!-- AKSI -->
                                    <td class="text-center">
                                        <a href="{{ route('admin.panduan.edit', $p->id) }}" class="btn btn-warning btn-sm"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <button class="btn btn-danger btn-sm open-delete-modal"
                                            data-id="{{ $p->id }}" data-title="{{ $p->title }}"
                                            title="Hapus Panduan">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- MODAL PREVIEW -->
                                @if ($p->file_path)
                                    <div class="modal fade" id="previewModal{{ $p->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-xl">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ $p->title }}</h5>
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body" style="height:80vh;">
                                                    <iframe src="{{ asset($p->file_path) }}" width="100%"
                                                        height="100%"></iframe>
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

    <!-- ===========================
        MODAL KONFIRMASI HAPUS
    ============================= -->
    <div class="modal fade" id="deletePanduanModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <p>Anda yakin ingin menghapus panduan berikut?</p>
                        <h5 id="deletePanduanTitle" class="font-weight-bold"></h5>
                        <p class="text-danger mt-2">Tindakan ini tidak bisa dibatalkan.</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus Sekarang</button>
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

        /* HANDLE DELETE MODAL */
        $(document).on('click', '.open-delete-modal', function() {
            let id = $(this).data('id');
            let title = $(this).data('title');

            $('#deletePanduanTitle').text(title);

            // set form action
            $('#deleteForm').attr('action', '/admin/panduan/hapus/' + id);

            $('#deletePanduanModal').modal('show');
        });
    </script>
@endsection
