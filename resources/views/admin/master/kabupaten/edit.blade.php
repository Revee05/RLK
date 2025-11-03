@extends('admin.partials._layout')
@section('title','Edit Satuan')
@section('collapseMaster','show')
@section('kabupaten','active')
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h5 mb-4 text-gray-800">Master
    <small>kabupaten</small>
    {{-- <a href="" class="btn btn-primary btn-sm float-right"><i class="fa fa-plus-circle"></i> Create</a> --}}
    </h1>
    <div class="row">
        <div class="col-sm-5">
            
            <div class="card shadow mb-4 rounded-0">
                <div class="card-body">
                    {{ Form::model($kabupaten, array('route' => array('master.kabupaten.update', $kabupaten->id), 'method' => 'PUT')) }}
                    @include('admin.master.kabupaten.form')
                    <a href="{{ route('master.kabupaten.index') }}" class="btn btn-primary btn-sm rounded-0">Kembali</a>
                    {{ Form::submit('Simpan', array('class' => 'btn btn-primary btn-sm rounded-0')) }}
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->
@endsection