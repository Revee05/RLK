@extends('web.partials.layout')

@section('title', 'Panduan')

@section('css')
    <link href="{{ asset('css/panduan_style.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="container panduan-container">
        <div class="row">

            <!-- SIDEBAR DIPISAH -->
            @include('web.panduan.sidebar', ['semuaPanduan' => $semuaPanduan])

            <!-- KONTEN UTAMA -->
            <div class="col-md-9">
                <div class="panduan-card">

                    <!-- Jika tidak ada panduan sama sekali -->
                    @if (!$panduan)
                        <div class="panduan-card-body text-center py-5">
                            <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Belum Ada Panduan</h4>
                            <p class="text-muted">Panduan akan muncul di sini setelah ditambahkan oleh admin.</p>
                        </div>
                    @else
                        <!-- Judul -->
                        <div class="panduan-card-header">
                            <h2 id="panduanTitle">{{ $panduan->title }}</h2>
                        </div>

                        <!-- Konten PDF / Alert -->
                        <div class="panduan-card-body">
                            <div id="pdfContainer">

                                @if ($panduan->file_path)
                                    <iframe id="panduanFrame" src="{{ asset($panduan->file_path) }}"
                                        class="pdf-frame"></iframe>
                                @else
                                    <div id="noPdfMessage" class="alert alert-warning text-center">
                                        <i class="fas fa-file-pdf fa-3x mb-3 text-warning"></i>
                                        <h5 class="font-weight-bold">Panduan Belum Tersedia</h5>
                                        <p>Dokumen ini sedang diperbarui. Silakan cek kembali nanti.</p>
                                    </div>
                                @endif

                            </div>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
@endsection



@section('js')
    @if ($panduan)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const items = document.querySelectorAll('.panduan-item');
                const titleEl = document.getElementById('panduanTitle');
                const pdfContainer = document.getElementById('pdfContainer');
                const loadUrlBase = "{{ url('/panduan/load') }}";

                items.forEach(item => {
                    item.addEventListener('click', function() {

                        items.forEach(i => i.classList.remove('active'));
                        this.classList.add('active');

                        const slug = this.dataset.slug;

                        fetch(`${loadUrlBase}/${slug}`)
                            .then(res => res.json())
                            .then(data => {

                                titleEl.textContent = data.title || 'Panduan';

                                if (data.file_path) {
                                    pdfContainer.innerHTML = `
                            <iframe id="panduanFrame"
                                    src="${data.file_path}"
                                    class="pdf-frame"></iframe>
                        `;
                                } else {
                                    pdfContainer.innerHTML = `
                            <div id="noPdfMessage" class="alert alert-warning text-center">
                                <i class="fas fa-file-pdf fa-3x mb-3 text-warning"></i>
                                <h5 class="font-weight-bold">Panduan Belum Tersedia</h5>
                                <p>Dokumen ini sedang diperbarui. Silakan cek kembali nanti.</p>
                            </div>
                        `;
                                }
                            });
                    });
                });
            });
        </script>
    @endif
@endsection
