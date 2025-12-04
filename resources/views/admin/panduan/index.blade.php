@extends('admin.partials._layout')
@section('title','Kelola Panduan Pengguna')

@section('content')

<div class="container-fluid">
    <h1 class="h5 mb-4 text-gray-800">Kelola Panduan Pengguna</h1>

    @include('admin.partials._success')

    <div class="card shadow mb-4">
        <div class="card-body">

            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th width="25%">Judul Panduan</th>
                        <th width="25%">File Saat Ini</th>
                        <th width="25%">Aksi</th>
                        <th width="25%">Upload Baru</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($panduan as $p)
                    <tr>
                        <td>{{ $p->title }}</td>

                        <!-- FILE SAAT INI -->
                        <td>
                            @if($p->file_path)
                                <a href="{{ asset($p->file_path) }}" 
                                   class="btn btn-info btn-sm" 
                                   target="_blank">
                                    Lihat PDF
                                </a>

                                <!-- Tombol Preview Modal -->
                                <button class="btn btn-secondary btn-sm"
                                    data-toggle="modal" data-target="#previewModal{{ $p->id }}">
                                    Preview
                                </button>
                            @else
                                <span class="text-danger">Belum ada file</span>
                            @endif
                        </td>

                        <!-- AKSI (PREVIEW, HAPUS) -->
                        <td>
                            @if($p->file_path)
                            <form method="POST" action="{{ route('admin.panduan.hapus', $p->id) }}"
                                  style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin ingin menghapus file ini?')">
                                    Hapus
                                </button>
                            </form>
                            @endif
                        </td>

                        <!-- UPLOAD BARU -->
                        <td>
                            <form method="POST" action="{{ route('admin.panduan.upload', $p->id) }}"
                                  enctype="multipart/form-data">
                                @csrf

                                <input type="file" 
                                       name="pdf" 
                                       accept="application/pdf" 
                                       class="form-control-file"
                                       required>

                                <button class="btn btn-primary btn-sm mt-2">
                                    Upload
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- MODAL PREVIEW -->
                    @if($p->file_path)
                    <div class="modal fade" id="previewModal{{ $p->id }}" tabindex="-1" role="dialog">
                      <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">{{ $p->title }}</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                          </div>
                          <div class="modal-body" style="height:80vh;">
                            <iframe src="{{ asset($p->file_path) }}" width="100%" height="100%"></iframe>
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

@endsection
