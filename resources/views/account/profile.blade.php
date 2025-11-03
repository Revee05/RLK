@extends('account.partials.layout')
@push('css')
<style type="text/css">
#customer-account {
    background-color: #eef0f8;
    padding: 36px 0 64px;
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
@endpush
@section('content')
<section class="py-4" id="customer-account">
    <div class="container">
        <div class="row bg-white py-4" style="border-radius: 10px;">
            <div class="col-sm-3 border-end">
                @include('account.partials.nav')
            </div>
            <div class="col-sm-9">
                <div class="card">
                    <div class="card-header bg-transparant">
                        PROFIL
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-8">
                                
                                {{ Form::model($user, array('route' => array('update.profil'), 'method' => 'POST')) }}
                                <div class="form-group">
                                    {{ Form::label('name', 'Nama Lengkap') }}
                                    {{ Form::text('name', $user->name ?? '', array('class' => 'form-control','placeholder' => 'Nama')) }}
                                </div>
                                <div class="form-group py-2">
                                    {{ Form::label('name', 'Username') }}
                                    {{ Form::text('username', $user->username ?? '', array('class' => 'form-control','placeholder' => 'Username','disabled')) }}
                                </div>
                                
                                <div class="form-group py-2">
                                    {{ Form::label('name', 'Email') }}
                                    {{ Form::text('email', $user->email ?? '', array('class' => 'form-control','placeholder' => 'Email')) }}
                                </div>
                                <div class="form-group py-2">
                                    {{ Form::label('name', 'Jenis Kelamin') }}
                                    {{ Form::select('jenis_kelamin',['perempuan'=>'Perempuan','laki_laki'=>'Laki laki'],$user->jenis_kelamin,array('class' => 'form-control','placeholder' => 'Pilih Jenis Kelamin')) }}
                                </div>
                            {{--     <div class="form-group py-2">
                                    {{ Form::label('name', 'Email') }}
                                    {{ Form::text('ttl', $user->ttl ?? '', array('class' => 'form-control','placeholder' => 'Email')) }}
                                </div>
                                <div class="form-group py-2">
                                    {{ Form::label('name', 'Hp') }}
                                    {{ Form::text('hp', $user->hp ?? '', array('class' => 'form-control','placeholder' => 'Email')) }}
                                </div> --}}
                                
                               {{--  <div class="form-group py-2">
                                    {{ Form::label('name', 'Foto') }}
                                </div> --}}
                                
                                <input type="hidden" name="id" value="{{Auth::user()->id}}">
                                <br>
                                {{ Form::submit('Simpan', array('class' => 'btn btn-danger btn-sm rounded-0')) }}
                                {{ Form::close() }}
                            </div>
                            <div class="col-sm-4">
                                
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection