@extends('web.partials.layout')

@section('title', 'Panduan Pembelian')

@section('css')
    {{-- Memanggil file CSS eksternal yang baru dibuat --}}
    <link href="{{ asset('css/panduan_style.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container panduan-container">
    <div class="row">
        
        {{-- PANGGIL SIDEBAR --}}
        @include('web.panduan.sidebar')

        {{-- BAGIAN KANAN: KONTEN PDF --}}
        <div class="col-md-9">
            
            {{-- Mulai Struktur Card --}}
            <div class="panduan-card">
                
                {{-- 1. Bagian Header --}}
                <div class="panduan-card-header">
                    <h2 class="panduan-content-title mb-0">Panduan Pembelian</h2>
                </div>
                
                {{-- 2. Bagian Body --}}
                <div class="panduan-card-body">
                    <div class="pdf-viewer-container">
                        {{-- 
                            Pastikan file PDF ada di: public/assets/pdf/panduan-pembelian.pdf
                        --}}
                        <iframe 
                            src="{{ asset('assets/pdf/panduan-pembelian.pdf') }}" 
                            class="pdf-frame"
                            title="Panduan Pembelian PDF">
                        </iframe>
                    </div>
                </div>

            </div>
            {{-- Akhir Struktur Card --}}

        </div>

    </div>
</div>
@endsection