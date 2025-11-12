@extends('admin.partials._layout')
@section('title','Social Form')
@section('social','active')
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="h5 text-gray-800 mb-0">Setting
            <small>Media Social</small>
        </h1>
    </div>
    {{ Form::open(['route' => 'setting.update.social', 'onsubmit' => 'return showSuccess()']) }}
    <div class="row">
        <div class="col-sm-6">
            <div class="card shadow mb-4 rounded-0">
                <div class="card-body">
                    @if(session('message'))
                        <div id="notif-success" class="alert alert-success py-2 px-3 mb-3" style="font-size: 0.95rem;">
                            {{ session('message') }}
                        </div>
                    @endif
                    <div class="form-group">
                        {{ Form::label('name', 'Facebook') }}
                        {{ Form::text('social[facebook]', $setting->social['facebook'] ?? '', ['class' => 'form-control form-control-sm','placeholder' => 'Link Facebook']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('name', 'Instagram') }}
                        {{ Form::text('social[instagram]', $setting->social['instagram'] ?? '', ['class' => 'form-control form-control-sm','placeholder' => 'Link instagram']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('name', 'Youtube') }}
                        {{ Form::text('social[youtube]', $setting->social['youtube'] ?? '', ['class' => 'form-control form-control-sm','placeholder' => 'Link youtube']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('name', 'Twitter') }}
                        {{ Form::text('social[twitter]', $setting->social['twitter'] ?? '', ['class' => 'form-control form-control-sm','placeholder' => 'Link twitter']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('name', 'Tiktok') }}
                        {{ Form::text('social[tiktok]', $setting->social['tiktok'] ?? '', ['class' => 'form-control form-control-sm','placeholder' => 'Link tiktok']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('name', 'Threads') }}
                        {{ Form::text('social[threads]', $setting->social['threads'] ?? '', ['class' => 'form-control form-control-sm','placeholder' => 'Link Threads']) }}
                    </div>
                    <input type="hidden" name="id" value="{{$setting->id}}">
                    {{ Form::submit('Simpan', ['class' => 'btn btn-primary btn-sm rounded-0']) }}
                </div>
            </div>
        </div>
    </div>
    {{ Form::close() }}
    {{-- Erors notification --}}
    @include('admin.partials._errors')
</div>
<!-- /.container-fluid -->

@push('scripts')
<script>
function showSuccess() {
    document.getElementById('notif-success').innerText = 'Data media sosial berhasil disimpan!';
    setTimeout(function() {
        document.getElementById('notif-success').innerText = '';
    }, 2000);
    return true; // lanjutkan submit
}
</script>
@endpush
@endsection