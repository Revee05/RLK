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
            {{ Form::text('social[facebook]', null, array('class' => 'form-control form-control-sm ','placeholder' => '')) }}
        </div>
        <div class="form-group">
            {{ Form::label('name', 'Twitter') }}
            {{ Form::text('social[twitter]', null, array('class' => 'form-control form-control-sm ','placeholder' => '')) }}
        </div>
        <div class="form-group">
            {{ Form::label('name', 'Instagram') }}
            {{ Form::text('social[instagram]', null, array('class' => 'form-control form-control-sm ','placeholder' => '')) }}
        </div>
        <div class="form-group">
            {{ Form::label('name', 'Youtube') }}
            {{ Form::text('social[youtube]', null, array('class' => 'form-control form-control-sm ','placeholder' => '')) }}
        </div>
        <div class="form-group">
            {{ Form::label('name', 'Tiktok') }}
            {{ Form::text('social[tiktok]', null, array('class' => 'form-control form-control-sm ','placeholder' => '')) }}
            {{-- <input type="text" name="" id="tiktok" onkeyup="getVal()"> --}}
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