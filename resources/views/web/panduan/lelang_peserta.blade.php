@extends('web.partials.layout')

@section('title', 'Panduan Peserta Lelang')

@section('css')
    {{-- Memanggil file CSS eksternal --}}
    <link href="{{ asset('css/panduan_style.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container panduan-container">
    <div class="row">
        
        {{-- PANGGIL SIDEBAR --}}
        @include('web.panduan.sidebar')

        {{-- BAGIAN KANAN: KONTEN PDF --}}
        <div class="col-md-9">
            
            {{-- Mulai Struktur Card Baru --}}
            <div class="panduan-card">
                
                {{-- 1. Bagian Header (Judul dengan garis bawah) --}}
                <div class="panduan-card-header">
                    {{-- class mb-0 agar margin tidak double dengan padding header --}}
                    <h2 class="panduan-content-title mb-0">Panduan Peserta Lelang</h2>
                </div>
                
                {{-- 2. Bagian Body (Area PDF dengan padding) --}}
                <div class="panduan-card-body">
                    <div class="pdf-viewer-container">
                        {{-- Pastikan file PDF ada di public/assets/pdf/ --}}
                        <iframe 
                            src="{{ asset('assets/test.pdf') }}" 
                            class="pdf-frame"
                            title="Panduan Peserta Lelang PDF">
                        </iframe>
                    </div>
                </div>

            </div>
            {{-- Akhir Struktur Card --}}

        </div>

    </div>
</div>
@endsection