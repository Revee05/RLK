@extends('web.partials.layout')

@section('title', $panduan->title)

@section('css')
    <link href="{{ asset('css/panduan_style.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="container panduan-container">
        <div class="row">

            {{-- Sidebar --}}
            @include('web.panduan.sidebar')

            {{-- Konten Utama --}}
            <div class="col-md-9">

                <div class="panduan-card">

                    <div class="panduan-card-header">
                        <h2 class="panduan-content-title mb-0">{{ $panduan->title }}</h2>
                    </div>

                    <div class="panduan-card-body">
                        <div class="pdf-viewer-container">

                            @if ($panduan->file_path)
                                <iframe src="{{ asset($panduan->file_path) }}" class="pdf-frame"
                                    title="{{ $panduan->title }}">
                                </iframe>
                            @else
                                <div class="alert alert-warning text-center">
                                    <i class="fas fa-file-pdf fa-3x mb-3 text-warning"></i>
                                    <h5 class="font-weight-bold">Panduan Belum Tersedia</h5>
                                    <p>Dokumen ini sedang diperbarui. Silakan cek kembali nanti.</p>
                                </div>
                            @endif

                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
@endsection
