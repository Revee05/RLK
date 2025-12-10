@extends('admin.partials._layout')

@section('content')
    <div class="container">

        <h4 class="mb-4">Tambah Panduan Baru</h4>

        {{-- ALERT ERROR JIKA ADA --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.panduan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- JUDUL PANDUAN --}}
            <div class="form-group">
                <label>Judul Panduan</label>
                <input type="text" name="title" id="title" class="form-control" required>
            </div>

            {{-- SLUG (HIDDEN) --}}
            <input type="hidden" name="slug" id="slug">

            {{-- UPLOAD FILE --}}
            <div class="form-group">
                <label>Upload File PDF</label>
                <input type="file" name="pdf" class="form-control-file" accept="application/pdf">
            </div>

            <button class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.panduan.index') }}" class="btn btn-secondary">Kembali</a>

        </form>
    </div>
@endsection


@section('js')
    <script>
        function createSlug(text) {
            return text
                .toString()
                .toLowerCase()
                .trim()
                .replace(/\s+/g, '-') // ubah spasi jadi "-"
                .replace(/[^\w\-]+/g, '') // hapus karakter aneh
                .replace(/\-\-+/g, '-'); // rapikan --
        }

        document.getElementById('title').addEventListener('keyup', function() {
            let slug = createSlug(this.value);
            document.getElementById('slug').value = slug;
        });
    </script>
@endsection
