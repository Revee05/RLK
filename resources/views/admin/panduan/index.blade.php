@extends('admin.partials._layout')
@section('title', 'Panduan Pengguna')
@section('panduan', 'active')

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">

    <style>
        .btn-sm {
            padding: 4px 8px !important;
        }
    </style>
@endsection

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h5 mb-4 text-gray-800">
            Panduan <small>Pengguna</small>
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
                                <th width="30%">Upload Baru</th>
                                <th width="20%">Hapus</th> {{-- pindah ke kolom terakhir --}}
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

                                            <button class="btn btn-primary btn-sm mt-2">
                                                Upload
                                            </button>
                                        </form>
                                    </td>

                                    <!-- HAPUS -->
                                    <td class="text-center">
                                        @if ($p->file_path)
                                            <form method="POST" action="{{ route('admin.panduan.hapus', $p->id) }}"
                                                style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')

                                                <button class="btn btn-danger btn-sm" title="Hapus File"
                                                    onclick="return confirm('Yakin ingin menghapus file ini?')">

                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </td>
                                </tr>

                                <!-- ==============================
                                                 MODAL PREVIEW PDF
                                            =============================== -->
                                @if ($p->file_path)
                                    <div class="modal fade" id="previewModal{{ $p->id }}" tabindex="-1"
                                        role="dialog">
                                        <div class="modal-dialog modal-xl" role="document">
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
@endsection

@section('js')
    <!-- DataTables -->
    <script src="{{ asset('assets/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });
    </script>
@endsection
