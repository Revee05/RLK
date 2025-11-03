@extends('admin.partials._layout')
@section('title','Edit User')
@section('user','active')
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h5 mb-4 text-gray-800">@if(Auth::user()->access == 'operator') Profil @else User @endif
        <small>Edit</small>
    </h1>
    <div class="row">
        <div class="col-sm-5">
            
            <div class="card shadow mb-4 rounded-0">
                <div class="card-body">
                    {{ Form::model($user, array('route' => array('admin.user.update', $user->id), 'method' => 'PUT')) }}
                    @include('admin.users.form')
                    <a href="{{ route('admin.user.index') }}" class="btn btn-primary btn-sm rounded-0">Kembali</a>
                    {{ Form::submit('Simpan', array('class' => 'btn btn-primary btn-sm rounded-0')) }}
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->
@endsection