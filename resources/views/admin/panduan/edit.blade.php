@extends('admin.partials._layout')
@section('title', 'Edit Panduan')
@section('panduan', 'active')

@section('content')
    <div class="container-fluid">
        <h4 class="mb-4">Edit Panduan</h4>

        <form action="{{ route('admin.panduan.update', $panduan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="card shadow mb-4">
                <div class="card-body">
                    
                    {{-- JUDUL --}}
                    <div class="form-group mb-4">
                        <label class="font-weight-bold">Judul Panduan</label>
                        <input type="text" name="title" 
                               value="{{ old('title', $panduan->title) }}" 
                               class="form-control @error('title') is-invalid @enderror" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        {{-- KIRI: PREVIEW FILE LAMA --}}
                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="font-weight-bold">File Saat Ini:</label>
                                <div class="mt-2">
                                    @if ($panduan->file_path)
                                        <p class="mb-2 text-success font-weight-bold">
                                            <i class="fas fa-check-circle"></i> File Tersedia
                                        </p>
                                        <a href="{{ asset($panduan->file_path) }}" target="_blank" class="btn btn-info btn-block btn-sm">
                                            <i class="fas fa-file-pdf"></i> Lihat / Download PDF
                                        </a>
                                    @else
                                        <p class="text-danger mb-0">
                                            <i class="fas fa-times-circle"></i> Belum ada file.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- KANAN: UPLOAD FILE BARU --}}
                        <div class="col-12 col-lg-6">
                            <div class="p-3 border rounded h-100">
                                <label class="font-weight-bold">Ganti File (Opsional)</label>
                                <input type="file" name="pdf" class="form-control-file mt-2 @error('pdf') is-invalid @enderror" accept="application/pdf">
                                <small class="text-muted d-block mt-2">
                                    *Kosongkan jika tidak ingin mengubah.<br>
                                    *Maksimal 20MB.
                                </small>
                                @error('pdf')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- FOOTER TOMBOL --}}
                <div class="card-footer bg-white d-flex flex-column flex-md-row">
                    <button type="submit" class="btn btn-primary px-4 mb-2 mb-md-0 mr-md-2">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.panduan.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </form>
    </div>
@endsection