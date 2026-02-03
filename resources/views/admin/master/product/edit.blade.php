@extends('admin.partials._layout')
@section('title','Create product')
@section('collapseMaster','show')
@section('product','active')
@section('master-product','active')
@section('content')
<style type="text/css">
select option {
    text-transform: capitalize;
}

.preview-cover {
    /* Hapus height dan width fixed */
    overflow: hidden;
    position: relative;
    border: 1px solid #5a5c69;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fc;
    /* Optional: min-height: 160px; */
}

.preview-cover img.img-featured {
    width: 800px;
    height: 300px;
    object-fit: cover;
}

.preview-cover img.img-normal {
    width: 300px;
    height: 400px;
    object-fit: cover;
}
</style>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h5 mb-4 text-gray-800">Master
        <small>Brand</small>
        {{-- <a href="" class="btn btn-primary btn-sm float-right"><i class="fa fa-plus-circle"></i> Create</a> --}}
    </h1>

    {{-- Erors notification --}}
    @include('admin.partials._errors')

    {{ Form::model($product, array('route' => array('master.product.update', $product->id), 'method' => 'PUT','files' => true)) }}
    <div class="row">
        <div class="col-sm-12">
            <div class="row mb-4 d-flex">
                <div class="col-12">
                    <!-- Input file hanya satu per gambar, preview dua (normal & featured) -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card rounded-0 shadow">
                                <div class="card-body">
                                    <div class="preview-cover mb-2">
                                        <img class="border-1 @if($product->type == 'featured') img-featured d-none @else img-normal @endif" @if($product->imageUtama) src="{{asset($product->imageUtama->path)}}" @else src="{{asset('assets/img/default.jpg')}}" @endif id="foto-barang-satu-normal">
                                        <img class="border-1 img-featured @if($product->type != 'featured') d-none @endif" @if($product->imageUtama) src="{{asset($product->imageUtama->path)}}" @else src="{{asset('assets/img/default.jpg')}}" @endif id="foto-barang-satu-featured">
                                    </div>
                                    <div class="media-body m-auto">
                                        <input id="input-foto-satu" type="file" name="fotosatu" class="d-none @error('fotosatu') is-invalid @enderror" accept="image/*" />
                                        <label for="input-foto-satu" class="btn btn-sm btn-dark rounded-0 btn-block">
                                            <i class="fa fa-folder-open"></i> Pilih Foto Utama
                                        </label>
                                        @error('fotosatu')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card rounded-0 shadow">
                                <div class="card-body">
                                    <div class="preview-cover mb-2">
                                        <img class="border-1 @if($product->type == 'featured') img-featured d-none @else img-normal @endif" @if($product->imageDepan) src="{{asset($product->imageDepan->path)}}" @else src="{{asset('assets/img/default.jpg')}}" @endif id="foto-barang-dua-normal">
                                        <img class="border-1 img-featured @if($product->type != 'featured') d-none @endif" @if($product->imageDepan) src="{{asset($product->imageDepan->path)}}" @else src="{{asset('assets/img/default.jpg')}}" @endif id="foto-barang-dua-featured">
                                    </div>
                                    <div class="media-body m-auto">
                                        <input id="input-foto-dua" type="file" name="fotodua" class="d-none @error('fotodua') is-invalid @enderror" accept="image/*" />
                                        <label for="input-foto-dua" class="btn btn-sm btn-dark rounded-0 btn-block">
                                            <i class="fa fa-folder-open"></i> Pilih Foto Depan
                                        </label>
                                        @error('fotodua')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card rounded-0 shadow">
                                <div class="card-body">
                                    <div class="preview-cover mb-2">
                                        <img class="border-1 @if($product->type == 'featured') img-featured d-none @else img-normal @endif" @if($product->imageSamping) src="{{asset($product->imageSamping->path)}}" @else src="{{asset('assets/img/default.jpg')}}" @endif id="foto-barang-tiga-normal">
                                        <img class="border-1 img-featured @if($product->type != 'featured') d-none @endif" @if($product->imageSamping) src="{{asset($product->imageSamping->path)}}" @else src="{{asset('assets/img/default.jpg')}}" @endif id="foto-barang-tiga-featured">
                                    </div>
                                    <div class="media-body m-auto">
                                        <input id="input-foto-tiga" type="file" name="fototiga" class="d-none @error('fototiga') is-invalid @enderror" accept="image/*" />
                                        <label for="input-foto-tiga" class="btn btn-sm btn-dark rounded-0 btn-block">
                                            <i class="fa fa-folder-open"></i> Pilih Foto Samping
                                        </label>
                                        @error('fototiga')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card rounded-0 shadow">
                                <div class="card-body">
                                    <div class="preview-cover mb-2">
                                        <img class="border-1 @if($product->type == 'featured') img-featured d-none @else img-normal @endif" @if($product->imageAtas) src="{{asset($product->imageAtas->path)}}" @else src="{{asset('assets/img/default.jpg')}}" @endif id="foto-barang-empat-normal">
                                        <img class="border-1 img-featured @if($product->type != 'featured') d-none @endif" @if($product->imageAtas) src="{{asset($product->imageAtas->path)}}" @else src="{{asset('assets/img/default.jpg')}}" @endif id="foto-barang-empat-featured">
                                    </div>
                                    <div class="media-body m-auto">
                                        <input id="input-foto-empat" type="file" name="fotoempat" class="d-none @error('fotoempat') is-invalid @enderror" accept="image/*" />
                                        <label for="input-foto-empat" class="btn btn-sm btn-dark rounded-0 btn-block">
                                            <i class="fa fa-folder-open"></i> Pilih Foto Atas
                                        </label>
                                        @error('fotoempat')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card shadow mb-4 rounded-0">
                <div class="card-body">
                    @include('admin.master.product.form')
                    <a href="{{ route('master.product.index') }}" class="btn btn-primary btn-sm rounded-0">Kembali</a>
                    {{ Form::submit('Simpan', array('class' => 'btn btn-primary btn-sm rounded-0')) }}
                </div>
            </div>
        </div>
    </div>
    {{ Form::close() }}
</div>
<!-- /.container-fluid -->
@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script type="text/javascript">
$(document).ready(function() {
    'use strict';

    // Ganti class dan visibilitas preview saat tipe produk diubah
    $('select[name="type"]').on('change', function() {
        var tipe = $(this).val();
        // Untuk setiap gambar, ganti class dan tampilkan preview sesuai tipe
        ['satu','dua','tiga','empat'].forEach(function(nomor) {
            var normal = $('#foto-barang-' + nomor + '-normal');
            var featured = $('#foto-barang-' + nomor + '-featured');
            if (tipe === 'featured') {
                normal.addClass('d-none');
                featured.removeClass('d-none');
            } else {
                normal.removeClass('d-none');
                featured.addClass('d-none');
            }
        });
    });

    // Helper: update both normal & featured preview
    function updatePreviewImage(nomor, src) {
        $('#foto-barang-' + nomor + '-normal').attr('src', src);
        $('#foto-barang-' + nomor + '-featured').attr('src', src);
    }

    //foto1
    var fotoSatu = $('#input-foto-satu');
    if (fotoSatu.length) {
        fotoSatu.on('change', function(e) {
            var reader = new FileReader(),
                files = e.target.files;
            var fsize = files[0].size;
            if (fsize > 2000000) {
                alert("Ukuran foto terlalu besar, maximal 2mb");
            } else {
                reader.onload = function() {
                    updatePreviewImage('satu', reader.result);
                };
                reader.readAsDataURL(files[0]);
            }
        });
    }

    //foto2
    var fotoDua = $('#input-foto-dua');
    if (fotoDua.length) {
        fotoDua.on('change', function(e) {
            var reader = new FileReader(),
                files = e.target.files;
            var fsize = files[0].size;
            if (fsize > 2000000) {
                alert("Ukuran foto terlalu besar, maximal 2mb");
            } else {
                reader.onload = function() {
                    updatePreviewImage('dua', reader.result);
                };
                reader.readAsDataURL(files[0]);
            }
        });
    }

    //foto3
    var fotoTiga = $('#input-foto-tiga');
    if (fotoTiga.length) {
        fotoTiga.on('change', function(e) {
            var reader = new FileReader(),
                files = e.target.files;
            var fsize = files[0].size;
            if (fsize > 2000000) {
                alert("Ukuran foto terlalu besar, maximal 2mb");
            } else {
                reader.onload = function() {
                    updatePreviewImage('tiga', reader.result);
                };
                reader.readAsDataURL(files[0]);
            }
        });
    }

    //foto4
    var fotoEmpat = $('#input-foto-empat');
    if (fotoEmpat.length) {
        fotoEmpat.on('change', function(e) {
            var reader = new FileReader(),
                files = e.target.files;
            var fsize = files[0].size;
            if (fsize > 2000000) {
                alert("Ukuran foto terlalu besar, maximal 2mb");
            } else {
                reader.onload = function() {
                    updatePreviewImage('empat', reader.result);
                };
                reader.readAsDataURL(files[0]);
            }
        });
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script type="text/javascript">
$('#deskripsi').summernote({
    placeholder: 'Tulis deskripsi produk disini...',
    toolbar: [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough']],
        ['fontsize', ['fontsize']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']],
    ],
    height: 150
});
$("#harga").keyup(function(event) {

    // format number, maximum 10 digit
    $(this).val(function(index, value) {
        return value
            .replace(/\D/g, "")
            .replace(/\B(?=(\d{10})+(?!\d))/g, ",");
    });

    Number($('#harga').val().replace(/,/g, ''));
});
$('#endate').flatpickr({
    allowInput: true,
    enableTime: true,
    noCalendar: false,
    dateFormat: 'Y-m-d H:i:S',
    time_24hr: true,
    disableMobile: true
});
</script>
@endsection