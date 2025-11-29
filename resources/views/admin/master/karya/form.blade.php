<div class="row justify-content-between">
    <div class="col-sm-8">
        
        <div class="form-group">
            {{ Form::label('name', 'Nama Seniman') }}
            {{ Form::text('name', null, array('class' => 'form-control form-control-sm ','placeholder' => 'Nama Seniman')) }}
        </div>
        <div class="form-group">
            {{ Form::label('name', 'Address') }}
            <small style="margin-top:4px; color: #e3342f;">tulis nama kota di paling akhir alamat setelah koma ( , ) (contoh: isi alamat lengkap, nama kota) </small>
            {{ Form::text('address', null, array('class' => 'form-control form-control-sm ','placeholder' => '')) }}
        </div>       
        <div class="form-group row">
            {{ Form::label('name', 'Bio Singkat',['class'=>'col-sm-12 col-form-label']) }}
            <div class="col-sm-12">
                {{ Form::textarea('bio', null, array('class' => 'form-control form-control-sm ','id'=> 'bio-singkat')) }}
            </div>
        </div>
        
        {{-- Preview Card Seniman --}}
        <label class="mb-1" style="font-weight: 600; color: #444;">Preview Card</label>
        <div class="card mb-3 border" id="preview-card" style="border-radius: 16px; overflow: hidden;">
            <div class="card-body p-0">
                <div style="display: flex; align-items: flex-start; padding: 15px;">
                    <div style="width: 100px; min-width: 100px; height: 120px; border-radius: 12px; overflow: hidden; background-color: #f5f5f5; margin-right: 15px;">
                        <img id="preview-image" src="{{ isset($karya) && $karya->image ? asset('uploads/senimans/'.$karya->image) : asset('assets/img/default.jpg') }}" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; padding-top: 5px;">
                        <h5 id="preview-name" style="font-size: 1.1rem; font-weight: 700; color: #222; margin-bottom: 4px;">
                            {{ old('name', isset($karya) ? $karya->name : 'Nama Seniman') }}
                        </h5>
                        @php
                            $address = old('address', isset($karya) ? $karya->address : ' ');
                            $city = trim(Str::afterLast($address, ',')) ?: $address;
                        @endphp
                        <div id="preview-location" style="font-size: 0.9rem; font-style: italic; color: #444; margin-bottom: 8px;">
                            {{ $city }}
                        </div>
                        <div id="preview-bio"
                            style="
                                color: #555;
                                font-size: 0.85rem;
                                line-height: 1.6;
                                display: -webkit-box;
                                -webkit-line-clamp: 5;
                                -webkit-box-orient: vertical;
                                overflow: hidden;
                                word-break: break-word;
                                max-height: calc(1.6em * 5);
                            ">
                            {!! old('bio', isset($karya) ? $karya->bio : 'Bio singkat akan muncul di sini...') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-group row">
            {{ Form::label('name', 'Biografi',['class'=>'col-sm-12 col-form-label']) }}
            <div class="col-sm-12">
                {{ Form::textarea('description', null, array('class' => 'form-control form-control-sm ','id'=> 'biografi')) }}
            </div>
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