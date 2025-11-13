@extends('admin.partials._layout')
@section('title','Create product')
@section('collapseMaster','show')
@section('addproduct','active')
@section('content')
<style type="text/css">
select option {
text-transform: capitalize;
}
.preview-cover {
    height:160px;
    width: 100%;
    overflow: hidden;
    position: relative;
    border: 1px solid  #5a5c69;
}
.preview-cover img{
    height:100%;
    width: 100%;
    object-fit: cover;
    object-position: center;
}

</style>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h5 mb-4 text-gray-800">Master
    <small>Product</small>
    {{-- <a href="" class="btn btn-primary btn-sm float-right"><i class="fa fa-plus-circle"></i> Create</a> --}}
    </h1>
    {{ Form::open(array('route' => 'master.product.store','files'=>true)) }}
    <div class="row">
        <div class="col-sm-12">
            <div class="row mb-4 d-flex">
                <div class="col-md-3">
                    <div class="card rounded-0 shadow">
                        <div class="card-body">
                            <div class="preview-cover">
                                <img class="border-1" src="{{asset('assets/img/default.jpg')}}" id="foto-barang-satu">
                            </div>
                            <div class="media-body m-auto">
                                <input id="input-foto-satu" type="file" name="fotosatu" class="d-none @error('fotosatu') is-invalid @enderror" accept="image/*"/>
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
                <div class="col-md-3">
                    <div class="card rounded-0 shadow">
                        <div class="card-body">
                            <div class="preview-cover">
                                <img class="border-1" src="{{asset('assets/img/default.jpg')}}" id="foto-barang-dua">
                            </div>
                            <div class="media-body m-auto">
                                <input id="input-foto-dua" type="file" name="fotodua" class="d-none @error('fotodua') is-invalid @enderror" accept="image/*"/>
                                <label for="input-foto-dua" class="btn btn-sm btn-dark rounded-0 btn-block">
                                    <i class="fa fa-folder-open"></i> Pilih Foto Depan
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
                <div class="col-md-3">
                    <div class="card rounded-0 shadow">
                        <div class="card-body">
                            <div class="preview-cover">
                                <img class="border-1" src="{{asset('assets/img/default.jpg')}}" id="foto-barang-tiga">
                            </div>
                            <div class="media-body m-auto">
                                <input id="input-foto-tiga" type="file" name="fototiga" class="d-none @error('fototiga') is-invalid @enderror" accept="image/*"/>
                                <label for="input-foto-tiga" class="btn btn-sm btn-dark rounded-0 btn-block">
                                    <i class="fa fa-folder-open"></i> Pilih Foto Samping
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
                <div class="col-md-3">
                    <div class="card rounded-0 shadow">
                        <div class="card-body">
                            <div class="preview-cover">
                                <img class="border-1" src="{{asset('assets/img/default.jpg')}}" id="foto-barang-empat">
                            </div>
                            <div class="media-body m-auto">
                                <input id="input-foto-empat" type="file" name="fotoempat" class="d-none @error('fotoempat') is-invalid @enderror" accept="image/*"/>
                                <label for="input-foto-empat" class="btn btn-sm btn-dark rounded-0 btn-block">
                                    <i class="fa fa-folder-open"></i> Pilih Foto Atas
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
                
            </div>
            <div class="card shadow mb-4 rounded-0">
                <div class="card-body">
                    @include('admin.master.product.form')
                    <a href="{{ route('master.product.index') }}" class="btn btn-primary btn-sm rounded-0">Kembali</a>
                    {{ Form::submit('Simpan', array('class' => 'btn btn-primary btn-sm rounded-0')) }}
                    {{-- Erors notification --}}
                    @include('admin.partials._errors')
                    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <hr>
        <strong>Data yang dikirim:</strong>
        <pre>{{ print_r(old(), true) }}</pre>
    </div>
@endif
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
    $(document).ready(function () {
      'use strict';

    //foto1
    var previewFotoSatu = $('#foto-barang-satu'),
        fotoSatu = $('#input-foto-satu');

    //preview 1
    if (fotoSatu) {
        fotoSatu.on('change', function (e) {
        var reader = new FileReader(),
        files = e.target.files;
        var fsize = files[0].size;
        if(fsize > 2000000) {
            alert("Ukuran foto terlalu besar, maximal 2mb");
          } else {
            reader.onload = function () {
              if (previewFotoSatu) {
                  previewFotoSatu.attr('src', reader.result);
              }
          };
            reader.readAsDataURL(files[0]);
        }
          });
    }    

    //foto2
    var previewFotoDua = $('#foto-barang-dua'),
        fotoDua = $('#input-foto-dua');

    //preview 2
    if (fotoDua) {
        fotoDua.on('change', function (e) {
        var reader = new FileReader(),
        files = e.target.files;
        var fsize = files[0].size;
        if(fsize > 2000000) {
            alert("Ukuran foto terlalu besar, maximal 2mb");
          } else {
            reader.onload = function () {
              if (previewFotoDua) {
                  previewFotoDua.attr('src', reader.result);
              }
          };
            reader.readAsDataURL(files[0]);
        }
          });
    }    

 //foto2
    var previewFotoTiga = $('#foto-barang-tiga'),
        fotoTiga = $('#input-foto-tiga');

    //preview 2
    if (fotoTiga) {
        fotoTiga.on('change', function (e) {
        var reader = new FileReader(),
        files = e.target.files;
        var fsize = files[0].size;
        if(fsize > 2000000) {
            alert("Ukuran foto terlalu besar, maximal 2mb");
          } else {
            reader.onload = function () {
              if (previewFotoTiga) {
                  previewFotoTiga.attr('src', reader.result);
              }
          };
            reader.readAsDataURL(files[0]);
        }
          });
    }  

     //foto2
    var previewFotoEmpat = $('#foto-barang-empat'),
        fotoEmpat = $('#input-foto-empat');

    //preview 2
    if (fotoEmpat) {
        fotoEmpat.on('change', function (e) {
        var reader = new FileReader(),
        files = e.target.files;
        var fsize = files[0].size;
        if(fsize > 2000000) {
            alert("Ukuran foto terlalu besar, maximal 2mb");
          } else {
            reader.onload = function () {
              if (previewFotoEmpat) {
                  previewFotoEmpat.attr('src', reader.result);
              }
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
                  .replace(/\B(?=(\d{10})+(?!\d))/g, ",")
                  ;
            });
              
            Number($('#harga').val().replace(/,/g,''));
        });
       $('#endate').flatpickr({
            allowInput: true,
            enableTime: true,
            noCalendar: false,
            dateFormat: 'Y-m-d H:i:S',
            time_24hr: true,
            disableMobile:true
        });
    </script>
@endsection