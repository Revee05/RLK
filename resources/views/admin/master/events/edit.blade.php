@extends('admin.partials._layout')

@section('title', 'Edit Event')
@section('events', 'active')
@section('collapseEvents', 'show')
@section('allEvents', 'active')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Event</h1>
        <a href="{{ route('admin.events.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <form action="{{ route('admin.events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            
            <div class="col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Informasi Event (Kotak Navy)</h6>
                    </div>
                    <div class="card-body">
                        
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">Judul Event (Title) <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control form-control-lg" 
                                   value="{{ old('title', $event->title) }}" required>
                        </div>

                        <hr>
                        
                        <label class="font-weight-bold text-info">Detail Jadwal & Lokasi</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small text-secondary">Jadwal Lelang Online</label>
                                    <input type="text" name="online_period" class="form-control" 
                                           value="{{ old('online_period', $event->online_period) }}" 
                                           placeholder="Cth: 15 Nov - 31 Des 2025">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small text-secondary">Jadwal Lelang Offline</label>
                                    <input type="text" name="offline_date" class="form-control" 
                                           value="{{ old('offline_date', $event->offline_date) }}" 
                                           placeholder="Cth: 01 Januari 2026">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="small text-secondary">Lokasi (Alamat)</label>
                            <textarea name="location" class="form-control" rows="2" 
                                      placeholder="Cth: Jl. Sekargading blok C no.9...">{{ old('location', $event->location) }}</textarea>
                        </div>
                        
                        <hr>

                        <div class="form-group">
                            <label class="font-weight-bold">Link Tombol "Detail"</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-link"></i></span>
                                </div>
                                <input type="url" name="link" class="form-control" 
                                       value="{{ old('link', $event->link) }}" placeholder="https://...">
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Media & Publikasi</h6>
                    </div>
                    <div class="card-body">
                        
                        <div class="form-group">
                            <label class="font-weight-bold">Status Publikasi</label>
                            <select name="status" class="form-control">
                                <option value="active" {{ $event->status == 'active' ? 'selected' : '' }} class="text-success font-weight-bold">Active (Tayang)</option>
                                <option value="coming_soon" {{ $event->status == 'coming_soon' ? 'selected' : '' }} class="text-warning font-weight-bold">Coming Soon</option>
                                <option value="inactive" {{ $event->status == 'inactive' ? 'selected' : '' }} class="text-secondary">Inactive (Draft)</option>
                            </select>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label class="font-weight-bold text-dark">Gambar Desktop (Landscape)</label>
                            <small class="d-block text-muted mb-2">Biarkan kosong jika tidak ingin mengubah gambar.</small>
                            
                            <div class="custom-file mb-2">
                                <input type="file" name="image" class="custom-file-input" id="inputDesktop" 
                                       accept="image/*" onchange="previewImage(this, 'previewDesktop')">
                                <label class="custom-file-label" for="inputDesktop">Pilih file baru...</label>
                            </div>

                            <div class="text-center p-2 border rounded bg-light">
                                @if($event->image)
                                    {{-- PERBAIKAN: Hapus 'storage/' --}}
                                    <img id="previewDesktop" src="{{ asset($event->image) }}" 
                                         class="img-fluid rounded" style="max-height: 150px;">
                                    <p class="small text-muted mt-1 mb-0">Gambar Saat Ini</p>
                                @else
                                    <img id="previewDesktop" src="#" class="img-fluid rounded" style="display:none; max-height: 150px;">
                                    <p class="small text-muted mt-1 mb-0">Belum ada gambar</p>
                                @endif
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label class="font-weight-bold text-dark">Gambar Mobile (Portrait)</label>
                            <small class="d-block text-muted mb-2">Khusus tampilan HP (Rasio 4:5).</small>
                            
                            <div class="custom-file mb-2">
                                <input type="file" name="image_mobile" class="custom-file-input" id="inputMobile" 
                                       accept="image/*" onchange="previewImage(this, 'previewMobile')">
                                <label class="custom-file-label" for="inputMobile">Pilih file baru...</label>
                            </div>

                             <div class="text-center p-2 border rounded bg-light">
                                @if($event->image_mobile)
                                    {{-- PERBAIKAN: Hapus 'storage/' --}}
                                    <img id="previewMobile" src="{{ asset($event->image_mobile) }}" 
                                         class="img-fluid rounded" style="max-height: 200px;">
                                    <p class="small text-muted mt-1 mb-0">Gambar Saat Ini</p>
                                @else
                                    <img id="previewMobile" src="#" class="img-fluid rounded" style="display:none; max-height: 200px;">
                                    <p class="small text-muted mt-1 mb-0">Belum ada gambar</p>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-block btn-lg">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('admin.events.index') }}" class="btn btn-light btn-block text-secondary">
                                Batal
                            </a>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection

@section('js')
<script>
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
            let fileName = input.files[0].name;
            let label = input.nextElementSibling;
            label.innerText = fileName;
        }
    }
</script>
@endsection