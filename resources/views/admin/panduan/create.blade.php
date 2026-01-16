@extends('admin.partials._layout')
@section('title', 'Tambah Panduan')

@section('content')
    <div class="container-fluid">

        <h4 class="mb-4">Tambah Panduan Baru</h4>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 pl-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('admin.panduan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label class="font-weight-bold">Judul Panduan</label>
                        <input type="text" name="title" id="title" class="form-control" placeholder="Masukkan judul..." required>
                    </div>

                    {{-- SLUG (HIDDEN) --}}
                    <input type="hidden" name="slug" id="slug">

                    <div class="form-group">
                        <label class="font-weight-bold">Upload File PDF</label>
                        <div class="p-3 border rounded bg-light">
                            <input type="file" name="pdf" class="form-control-file" accept="application/pdf">
                            <small class="text-muted mt-2 d-block">Format PDF, Maks 20MB.</small>
                        </div>
                    </div>

                    <div class="mt-4 d-flex flex-column flex-md-row">
                        <button class="btn btn-primary mb-2 mb-md-0 mr-md-2 px-4">Simpan</button>
                        <a href="{{ route('admin.panduan.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function createSlug(text) {
            return text.toString().toLowerCase().trim()
                .replace(/\s+/g, '-')
                .replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, '-');
        }
        document.getElementById('title').addEventListener('keyup', function() {
            document.getElementById('slug').value = createSlug(this.value);
        });
    </script>
@endsection