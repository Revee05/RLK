<div class="row justify-content-between">
    <div class="col-sm-8">
        
        <div class="form-group">
            {{ Form::label('name', 'Nama Seniman') }}
            {{ Form::text('name', null, array('class' => 'form-control form-control-sm ','placeholder' => 'Nama Seniman')) }}
        </div>
        <div class="form-group row">
            {{ Form::label('name', 'Bio Singkat',['class'=>'col-sm-12 col-form-label']) }}
            <div class="col-sm-12">
                {{ Form::textarea('bio', null, array('class' => 'form-control form-control-sm ','id'=> 'bio-singkat')) }}
            </div>
        </div>        
        <div class="form-group row">
            {{ Form::label('name', 'Biografi',['class'=>'col-sm-12 col-form-label']) }}
            <div class="col-sm-12">
                {{ Form::textarea('description', null, array('class' => 'form-control form-control-sm ','id'=> 'biografi')) }}
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('name', 'Address') }}
            {{ Form::text('address', null, array('class' => 'form-control form-control-sm ','placeholder' => '')) }}
        </div>
        <div class="form-group">
            {{ Form::label('name', 'Facebook') }}
            <input type="text" name="social[facebook]" value="{{ old('social.facebook', isset($karya) && $karya->social ? ($karya->social['facebook'] ?? '') : '') }}" class="form-control form-control-sm" placeholder="https://facebook.com/...">
        </div>
        <div class="form-group">
            {{ Form::label('name', 'Twitter') }}
            <input type="text" name="social[twitter]" value="{{ old('social.twitter', isset($karya) && $karya->social ? ($karya->social['twitter'] ?? '') : '') }}" class="form-control form-control-sm" placeholder="https://twitter.com/...">
        </div>
        <div class="form-group">
            {{ Form::label('name', 'Instagram') }}
            <input type="text" name="social[instagram]" value="{{ old('social.instagram', isset($karya) && $karya->social ? ($karya->social['instagram'] ?? '') : '') }}" class="form-control form-control-sm" placeholder="https://instagram.com/...">
        </div>
        <div class="form-group">
            {{ Form::label('name', 'Youtube') }}
            <input type="text" name="social[youtube]" value="{{ old('social.youtube', isset($karya) && $karya->social ? ($karya->social['youtube'] ?? '') : '') }}" class="form-control form-control-sm" placeholder="https://youtube.com/...">
        </div>
        <div class="form-group">
            {{ Form::label('name', 'Tiktok') }}
            <input type="text" name="social[tiktok]" value="{{ old('social.tiktok', isset($karya) && $karya->social ? ($karya->social['tiktok'] ?? '') : '') }}" class="form-control form-control-sm" placeholder="https://tiktok.com/...">
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card rounded-0 border-0">
            <div class="card-body">
                <div class="preview-cover">
                    <img class="border-1" @if(isset($karya) && $karya->image) src="{{asset('uploads/senimans/'.$karya->image)}}" @else src="{{asset('assets/img/default.jpg')}}" @endif id="foto-seniman">
                </div>
                <div class="media-body m-auto">
                    <input id="input-foto-seniman" type="file" name="fotoseniman" class="d-none @error('fotoseniman') is-invalid @enderror" accept="image/*"/>
                    <label for="input-foto-seniman" class="btn btn-sm btn-dark rounded-0 btn-block">
                        <i class="fa fa-folder-open"></i> Pilih Foto Seniman
                    </label>
                    @error('fotoseniman')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
        </div>
    </div>
    
</div>