@extends('admin.partials._layout')
@section('title', $panduan->title)

@section('content')
<div class="container-fluid mt-3">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
        <h4 class="h5 mb-2 mb-md-0 text-truncate" style="max-width: 80%;">{{ $panduan->title }}</h4>
        <a href="{{ route('admin.panduan.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body p-0">
            <iframe src="{{ asset($panduan->file_path) }}" 
                    style="width: 100%; height: 85vh; border: none;"
                    allowfullscreen>
            </iframe>
        </div>
    </div>

</div>
@endsection