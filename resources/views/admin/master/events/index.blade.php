@extends('admin.partials._layout')

@section('title', 'Manajemen Event')
@section('events', 'active')
@section('collapseEvents', 'show')
@section('allEvents', 'active')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Event</h1>
        <a href="{{ route('admin.events.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Event Baru
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success border-left-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-primary d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-white">Daftar Event & Lelang</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 5%">No</th>
                            <th style="width: 20%">Preview Gambar</th>
                            <th style="width: 30%">Detail Event (Kotak Navy)</th>
                            <th style="width: 20%">Status Publikasi</th>
                            <th style="width: 10%">Link</th>
                            <th style="width: 15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $index => $event)
                        <tr>
                            <td class="align-middle">{{ $index + 1 }}</td>
                            
                            <td class="align-middle">
                                <div class="mb-2">
                                    <span class="badge badge-light border mb-1">Desktop</span>
                                    @if($event->image)
                                        {{-- PERBAIKAN: Hapus 'storage/' karena path database sudah lengkap --}}
                                        <img src="{{ asset($event->image) }}" class="img-fluid rounded border shadow-sm d-block" style="height: 50px; object-fit: cover;" alt="Desktop">
                                    @else
                                        <span class="text-danger small d-block font-italic"><i class="fas fa-times"></i> Belum ada</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="badge badge-light border mb-1">Mobile</span>
                                    @if($event->image_mobile)
                                        {{-- PERBAIKAN: Hapus 'storage/' --}}
                                        <img src="{{ asset($event->image_mobile) }}" class="img-fluid rounded border shadow-sm d-block" style="height: 60px; width: auto;" alt="Mobile">
                                    @else
                                        <span class="text-danger small d-block font-italic"><i class="fas fa-times"></i> Belum ada</span>
                                    @endif
                                </div>
                            </td>

                            <td class="align-middle">
                                <h6 class="font-weight-bold text-primary mb-2">{{ $event->title }}</h6>
                                
                                <div class="small text-secondary space-y-1">
                                    <div class="d-flex align-items-start mb-1">
                                        <i class="fas fa-globe fa-fw mr-2 mt-1 text-info"></i>
                                        <span><strong>Online:</strong> {{ $event->online_period ?? '-' }}</span>
                                    </div>
                                    <div class="d-flex align-items-start mb-1">
                                        <i class="fas fa-gavel fa-fw mr-2 mt-1 text-warning"></i>
                                        <span><strong>Offline:</strong> {{ $event->offline_date ?? '-' }}</span>
                                    </div>
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-map-marker-alt fa-fw mr-2 mt-1 text-danger"></i>
                                        <span class="text-truncate" style="max-width: 200px;">{{ $event->location ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="align-middle">
                                <form action="{{ route('admin.events.status', $event->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="custom-select custom-select-sm 
                                        @if($event->status == 'active') border-success text-success font-weight-bold
                                        @elseif($event->status == 'coming_soon') border-warning text-warning font-weight-bold
                                        @else border-secondary text-secondary
                                        @endif" 
                                        onchange="this.form.submit()">
                                        
                                        <option value="active" {{ $event->status == 'active' ? 'selected' : '' }}>Active (Tayang)</option>
                                        <option value="coming_soon" {{ $event->status == 'coming_soon' ? 'selected' : '' }}>Coming Soon</option>
                                        <option value="inactive" {{ $event->status == 'inactive' ? 'selected' : '' }}>Inactive (Draft)</option>
                                    </select>
                                </form>
                                <small class="text-muted d-block mt-1">
                                    Terakhir update: {{ $event->updated_at->diffForHumans() }}
                                </small>
                            </td>

                            <td class="align-middle text-center">
                                @if($event->link)
                                    <a href="{{ $event->link }}" target="_blank" class="btn btn-sm btn-circle btn-info" title="Buka Link">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td class="align-middle text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus event ini? Data gambar juga akan dihapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <img src="{{ asset('img/undraw_empty.svg') }}" style="height: 150px; opacity: 0.5;" class="mb-3" alt="Empty">
                                    <h5 class="text-gray-600">Belum ada event yang ditambahkan</h5>
                                    <p class="text-gray-500 mb-3">Silakan buat event lelang pertama Anda.</p>
                                    <a href="{{ route('admin.events.create') }}" class="btn btn-primary btn-sm px-4">Buat Sekarang</a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .space-y-1 > div { margin-bottom: 0.25rem; }
    .table td { vertical-align: middle !important; }
</style>
@endsection