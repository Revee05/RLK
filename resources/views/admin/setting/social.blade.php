@extends('admin.partials._layout')
@section('title','Social Form')
@section('social','active')
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h5 mb-4 text-gray-800">Setting
    <small>Media Social</small>
    {{-- <a href="" class="btn btn-primary btn-sm float-right"><i class="fa fa-plus-circle"></i> Create</a> --}}
    </h1>
    {{ Form::open(array('route' => 'setting.update.social')) }}
    <div class="row">
        <div class="col-sm-6">
            
            <div class="card shadow mb-4 rounded-0">
                <div class="card-body">
                    <div class="form-group">
                        {{ Form::label('name', 'Facebook') }}
                        {{ Form::text('social[facebook]', $setting->social['facebook'] ?? '', array('class' => 'form-control form-control-sm ','placeholder' => 'Link Facebook')) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('name', 'Instagram') }}
                        {{ Form::text('social[instagram]', $setting->social['instagram'] ?? '', array('class' => 'form-control form-control-sm ','placeholder' => 'Link instagram')) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('name', 'Youtube') }}
                        {{ Form::text('social[youtube]', $setting->social['youtube'] ?? '', array('class' => 'form-control form-control-sm ','placeholder' => 'Link youtube')) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('name', 'Twitter') }}
                        {{ Form::text('social[twitter]', $setting->social['twitter'] ?? '', array('class' => 'form-control form-control-sm ','placeholder' => 'Link twitter')) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('name', 'Tiktok') }}
                        {{ Form::text('social[tiktok]', $setting->social['tiktok'] ?? '', array('class' => 'form-control form-control-sm ','placeholder' => 'Link tiktok')) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('name', 'Threads') }}
                        {{ Form::text('social[threads]', $setting->social['threads'] ?? '', array('class' => 'form-control form-control-sm ','placeholder' => 'Link Threads')) }}
                    </div>

                    <input type="hidden" name="id" value="{{$setting->id}}">
                    {{ Form::submit('Simpan', array('class' => 'btn btn-primary btn-sm rounded-0')) }}
                    
                </div>
            </div>
            {{-- <p>
            <div class="badge badge-danger"><i class="fa fa-exclamation"></i></div>
                Simpan nama usernamenya saja !</p> --}}
        </div>
    </div>
    {{ Form::close() }}
    {{-- Erors notification --}}
    @include('admin.partials._errors')
</div>
<!-- /.container-fluid -->
@endsection