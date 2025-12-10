@extends('admin.partials._layout')

@section('title', 'Edit Panduan')
@section('panduan', 'active')

@section('css')
    <style>
        .form-label {
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">

        <h4 class="mb-4">Edit Panduan</h4>

        {{-- SUCCESS MESSAGE --}}
        @include('admin.partials._success')

        {{-- ERROR MESSAGE --}}
        @include('admin.partials._errors')

        <div class="card shadow mb-4">
            <div class="card-body">

                <form action="{{ route('admin.panduan.update', $panduan->id) }}" method="POST">
                    @csrf

                    {{-- JUDUL --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Judul Panduan</label>
                        <input type="text" name="title" 
                               value="{{ old('title', $panduan->title) }}" 
                               class="form-control" required>
                    </div>

                    {{-- FILE PDF --}}
                    <div class="form-group mb-3">
                        <label class="form-label">File PDF Saat Ini</label><br>

                        @if ($panduan->file_path)
                            <a href="{{ asset($panduan->file_path) }}" target="_blank" class="btn btn-info btn-sm">
                                Lihat PDF
                            </a>
                        @else
                            <span class="text-danger">Tidak ada file PDF</span>
                        @endif

                        <p class="mt-2 text-muted">
                            Untuk mengganti file PDF, gunakan tombol upload di halaman daftar panduan.
                        </p>
                    </div>

                    <hr>

                    {{-- BUTTON --}}
                    <div class="form-group mt-4">
                        <button class="btn btn-primary">Simpan Perubahan</button>
                        <a href="{{ route('admin.panduan.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>

                </form>

            </div>
        </div>

    </div>
@endsection
