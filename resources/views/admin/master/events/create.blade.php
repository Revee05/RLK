@extends('admin.partials._layout')

@section('title', 'Buat Event Baru')
@section('events', 'active')
@section('collapseEvents', 'show')
@section('addEvent', 'active')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Buat Event Baru</h1>
        <a href="{{ route('admin.events.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row">

            <div class="col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 border-left-primary">
                        <h6 class="m-0 font-weight-bold text-primary">Informasi Event (Kotak Navy)</h6>
                    </div>
                    <div class="card-body">
                        
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">Judul Event (Title) <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control form-control-lg" 
                                   value="{{ old('title') }}" required 
                                   placeholder="Contoh: Semarang Art Festival">
                        </div>

                        <hr>
                        
                        <div class="alert alert-light border border-left-info" role="alert">
                            <i class="fas fa-info-circle text-info mr-1"></i> 
                            <small class="text-dark">Bagian ini akan muncul di dalam <strong>Kotak Navy</strong> pada slider beranda.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold small text-uppercase text-secondary">Jadwal Lelang Online</label>
                                    <input type="text" name="online_period" class="form-control" 
                                           value="{{ old('online_period') }}" 
                                           placeholder="Cth: 15 Nov - 31 Des 2025">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold small text-uppercase text-secondary">Jadwal Lelang Offline</label>
                                    <input type="text" name="offline_date" class="form-control" 
                                           value="{{ old('offline_date') }}" 
                                           placeholder="Cth: 01 Januari 2026">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold small text-uppercase text-secondary">Lokasi (Alamat)</label>
                            <textarea name="location" class="form-control" rows="2" 
                                      placeholder="Cth: Jl. Sekargading blok C no.9, Kalisegoro...">{{ old('location') }}</textarea>
                        </div>
                        
                        <hr>

                        <div class="form-group">
                            <label class="font-weight-bold">Link Tombol "Detail" (URL)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-link"></i></span>
                                </div>
                                <input type="url" name="link" class="form-control" 
                                       value="{{ old('link') }}" placeholder="https://rasanyalelangkarya.com/...">
                            </div>
                            <small class="text-muted">Kosongkan jika belum ada link tujuan.</small>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 border-left-warning">
                        <h6 class="m-0 font-weight-bold text-primary">Media & Publikasi</h6>
                    </div>
                    <div class="card-body">
                        
                        <div class="form-group">
                            <label class="font-weight-bold">Status Publikasi <span class="text-danger">*</span></label>
                            <select name="status" class="form-control">
                                <option value="active" class="text-success font-weight-bold">Active (Tayang)</option>
                                <option value="coming_soon" class="text-warning font-weight-bold">Coming Soon</option>
                                <option value="inactive" class="text-secondary" selected>Inactive (Draft)</option>
                            </select>
                            <small class="text-muted">Pilih "Active" untuk menampilkan di slider beranda.</small>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label class="font-weight-bold text-dark">Gambar Desktop (Landscape)</label>
                            <span class="badge badge-secondary float-right">Wajib Landscape</span>
                            <small class="d-block text-muted mb-2">Rekomendasi: <strong>1440 x 600 px</strong></small>
                            
                            <div class="custom-file mb-2">
                                <input type="file" name="image" class="custom-file-input" id="inputDesktop" 
                                       accept="image/*" onchange="previewImage(this, 'previewDesktop')">
                                <label class="custom-file-label" for="inputDesktop">Pilih file...</label>
                            </div>
                            
                            <div class="text-center p-3 border rounded bg-light" style="min-height: 100px; display: flex; align-items: center; justify-content: center;">
                                <img id="previewDesktop" src="#" class="img-fluid rounded shadow-sm" style="display:none; max-height: 150px;">
                                <span id="textDesktop" class="text-muted small">Preview gambar desktop</span>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label class="font-weight-bold text-dark">Gambar Mobile (Portrait)</label>
                            <span class="badge badge-info float-right">Wajib Portrait/Square</span>
                            <small class="d-block text-muted mb-2">Khusus tampilan HP. Rekomendasi: <strong>800 x 1000 px</strong></small>
                            
                            <div class="custom-file mb-2">
                                <input type="file" name="image_mobile" class="custom-file-input" id="inputMobile" 
                                       accept="image/*" onchange="previewImage(this, 'previewMobile')">
                                <label class="custom-file-label" for="inputMobile">Pilih file...</label>
                            </div>

                            <div class="text-center p-3 border rounded bg-light" style="min-height: 100px; display: flex; align-items: center; justify-content: center;">
                                <img id="previewMobile" src="#" class="img-fluid rounded shadow-sm" style="display:none; max-height: 200px;">
                                <span id="textMobile" class="text-muted small">Preview gambar mobile</span>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-block btn-lg">
                                <i class="fas fa-save"></i> Simpan Event
                            </button>
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
        const textPlaceholder = input.parentElement.nextElementSibling.querySelector('span'); 
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block'; 
                if(textPlaceholder) textPlaceholder.style.display = 'none'; 
            }
            reader.readAsDataURL(input.files[0]);
            let fileName = input.files[0].name;
            let label = input.nextElementSibling;
            label.innerText = fileName;
        }
    }
</script>
@endsection