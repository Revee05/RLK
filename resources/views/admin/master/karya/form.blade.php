<div class="row justify-content-between">
    <div class="col-sm-8">
        
        <div class="form-group">
            {{ Form::label('name', 'Nama Seniman') }}
            <span class="text-danger">*</span>
            {{ Form::text('name', null, array('class' => 'form-control form-control-sm '.($errors->has('name') ? 'is-invalid' : ''),'placeholder' => 'Nama Seniman', 'required' => true)) }}
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            {{ Form::label('julukan', 'Julukan / Alias') }}
            {{ Form::text('julukan', null, array('class' => 'form-control form-control-sm '.($errors->has('julukan') ? 'is-invalid' : ''),'placeholder' => 'Contoh: Si Pelukis Handal')) }}
            <small class="form-text text-muted">Julukan atau nama panggilan seniman (opsional)</small>
            @error('julukan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            {{ Form::label('profession', 'Profesi / Subtitle') }}
            <span class="text-danger">*</span>
            {{ Form::text('profession', null, array('class' => 'form-control form-control-sm '.($errors->has('profession') ? 'is-invalid' : ''),'placeholder' => 'Contoh: I\'m a Visual Artist, Illustrator, and Mural Painter', 'required' => true)) }}
            <small class="form-text text-muted">Akan ditampilkan sebagai subtitle di halaman detail seniman</small>
            @error('profession')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            {{ Form::label('province_id', 'Provinsi') }}
            <select name="province_id" id="province_id" class="form-control form-control-sm {{ $errors->has('province_id') ? 'is-invalid' : '' }}">
                <option value="">-- Pilih Provinsi --</option>
                @foreach($provinces as $province)
                    <option value="{{ $province->id }}" {{ old('province_id', isset($karya) ? $karya->province_id : '') == $province->id ? 'selected' : '' }}>
                        {{ $province->name }}
                    </option>
                @endforeach
            </select>
            @error('province_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            {{ Form::label('city_id', 'Kota/Kabupaten') }}
            <select name="city_id" id="city_id" class="form-control form-control-sm {{ $errors->has('city_id') ? 'is-invalid' : '' }}">
                <option value="">-- Pilih Kota/Kabupaten --</option>
                @if(isset($cities))
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}" {{ old('city_id', isset($karya) ? $karya->city_id : '') == $city->id ? 'selected' : '' }}>
                            {{ $city->name }}
                        </option>
                    @endforeach
                @endif
            </select>
            @error('city_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            {{ Form::label('district_id', 'Kecamatan') }}
            <select name="district_id" id="district_id" class="form-control form-control-sm {{ $errors->has('district_id') ? 'is-invalid' : '' }}">
                <option value="">-- Pilih Kecamatan --</option>
                @if(isset($districts))
                    @foreach($districts as $district)
                        <option value="{{ $district->id }}" {{ old('district_id', isset($karya) ? $karya->district_id : '') == $district->id ? 'selected' : '' }}>
                            {{ $district->name }}
                        </option>
                    @endforeach
                @endif
            </select>
            @error('district_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            {{ Form::label('address', 'Alamat Detail (Jalan, Nomor, RT/RW, dll)') }}
            {{ Form::text('address', null, array('class' => 'form-control form-control-sm '.($errors->has('address') ? 'is-invalid' : ''),'placeholder' => 'Contoh: Jl. Malioboro No. 123, RT 02/RW 05')) }}
            <small class="form-text text-muted">Masukkan detail alamat seperti nama jalan, nomor, RT/RW, dll</small>
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>     

        <div class="form-group row align-items-center">
            <div class="col-sm-12 d-flex align-items-center" style="gap: 10px;">
                {{ Form::label('bio', 'Bio Singkat',['class'=>'col-form-label mb-0']) }}
                <small style="color: #e3342f;">Efektif 4 baris (tergantung ukuran text), sesuaikan di preview card.</small>
            </div>
            <div class="col-sm-12">
                {{ Form::textarea('bio', null, array('class' => 'form-control form-control-sm '.($errors->has('bio') ? 'is-invalid' : ''),'id'=> 'bio-singkat', 'rows' => 3)) }}
                @error('bio')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        {{-- Preview Card Seniman --}}
        <label class="mb-1" style="font-weight: 600; color: #444; font-size: 1.05rem;">
            <i class="fas fa-eye"></i> Preview Card Seniman
        </label>
        <small class="d-block mb-2 text-muted">Tampilan card ini akan muncul di halaman daftar seniman</small>
        <div class="card mb-3 border shadow-sm" id="preview-card" style="width: 620px; border-radius: 16px; overflow: hidden; transition: all 0.3s ease;">
            <div class="card-body p-0">
                <div style="display: flex; align-items: flex-start; padding: 15px;">
                    <div style="width: 140px; min-width: 140px; height: 165px; border-radius: 12px; overflow: hidden; background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%); margin-right: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <img id="preview-image" src="{{ isset($karya) && $karya->image ? asset('uploads/senimans/'.$karya->image) : asset('assets/img/default.jpg') }}" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; padding-top: 5px;">
                        <h5 id="preview-name" style="font-size: 1.1rem; font-weight: 700; color: #222; margin-bottom: 4px;">
                            {{ old('name', isset($karya) ? $karya->name : 'Nama Seniman') }}
                        </h5>
                        @php
                            $address = old('address', isset($karya) ? $karya->address : '');
                            $city = trim(Str::afterLast($address, ',')) ?: '';
                        @endphp
                        <div id="preview-location" style= "font-size: 0.9rem; font-style: italic; color: #667eea; margin-bottom: 8px; font-weight: 500;">
                            <i class="fas fa-map-marker-alt" style="font-size: 0.8rem;"></i>
                            {{ $city ? $city : 'Nama kota muncul di sini...' }}
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
            {{ Form::label('description', 'Biografi - Full',['class'=>'col-sm-12 col-form-label']) }}
            <div class="col-sm-12">
                <small class="text-muted d-block mb-2">Deskripsi lengkap tentang seniman yang akan ditampilkan di halaman detail</small>
                {{ Form::textarea('description', null, array('class' => 'form-control form-control-sm '.($errors->has('description') ? 'is-invalid' : ''),'id'=> 'biografi', 'rows' => 4)) }}
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group row">
            {{ Form::label('art_projects', 'Art Projects',['class'=>'col-sm-12 col-form-label']) }}
            <div class="col-sm-12">
                <small class="text-muted d-block mb-2">Proyek seni yang pernah dikerjakan oleh seniman</small>
                {{ Form::textarea('art_projects', null, array('class' => 'form-control form-control-sm summernote '.($errors->has('art_projects') ? 'is-invalid' : ''),'id'=> 'art_projects', 'rows' => 4)) }}
                @error('art_projects')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group row">
            {{ Form::label('achievement', 'Achievement',['class'=>'col-sm-12 col-form-label']) }}
            <div class="col-sm-12">
                <small class="text-muted d-block mb-2">Pencapaian atau penghargaan yang diterima seniman</small>
                {{ Form::textarea('achievement', null, array('class' => 'form-control form-control-sm summernote '.($errors->has('achievement') ? 'is-invalid' : ''),'id'=> 'achievement', 'rows' => 4)) }}
                @error('achievement')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group row">
            {{ Form::label('exhibition', 'Exhibition',['class'=>'col-sm-12 col-form-label']) }}
            <div class="col-sm-12">
                <small class="text-muted d-block mb-2">Pameran yang pernah diikuti oleh seniman</small>
                {{ Form::textarea('exhibition', null, array('class' => 'form-control form-control-sm summernote '.($errors->has('exhibition') ? 'is-invalid' : ''),'id'=> 'exhibition', 'rows' => 4)) }}
                @error('exhibition')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <hr class="my-4">
        <h6 class="mb-3 font-weight-bold"><i class="fab fa-facebook"></i> Media Sosial (Opsional)</h6>
        <small class="text-muted d-block mb-3">Masukkan URL lengkap profil media sosial seniman</small>

        <div class="form-group">
            {{ Form::label('facebook', 'Facebook') }}
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fab fa-facebook" style="color: #1877f3;"></i></span>
                </div>
                <input type="url" name="social[facebook]" value="{{ old('social.facebook', isset($karya) && $karya->social ? ($karya->social['facebook'] ?? '') : '') }}" class="form-control form-control-sm" placeholder="https://facebook.com/username">
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('twitter', 'Twitter / X') }}
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fab fa-twitter" style="color: #1da1f2;"></i></span>
                </div>
                <input type="url" name="social[twitter]" value="{{ old('social.twitter', isset($karya) && $karya->social ? ($karya->social['twitter'] ?? '') : '') }}" class="form-control form-control-sm" placeholder="https://twitter.com/username">
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('instagram', 'Instagram') }}
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fab fa-instagram" style="color: #e4405f;"></i></span>
                </div>
                <input type="url" name="social[instagram]" value="{{ old('social.instagram', isset($karya) && $karya->social ? ($karya->social['instagram'] ?? '') : '') }}" class="form-control form-control-sm" placeholder="https://instagram.com/username">
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('youtube', 'Youtube') }}
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fab fa-youtube" style="color: #ff0000;"></i></span>
                </div>
                <input type="url" name="social[youtube]" value="{{ old('social.youtube', isset($karya) && $karya->social ? ($karya->social['youtube'] ?? '') : '') }}" class="form-control form-control-sm" placeholder="https://youtube.com/@username">
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('tiktok', 'Tiktok') }}
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fab fa-tiktok" style="color: #000000;"></i></span>
                </div>
                <input type="url" name="social[tiktok]" value="{{ old('social.tiktok', isset($karya) && $karya->social ? ($karya->social['tiktok'] ?? '') : '') }}" class="form-control form-control-sm" placeholder="https://tiktok.com/@username">
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('email', 'Email') }}
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-envelope" style="color: #6c757d;"></i></span>
                </div>
                <input type="email" name="social[email]" value="{{ old('social.email', isset($karya) && $karya->social ? ($karya->social['email'] ?? '') : '') }}" class="form-control form-control-sm {{ $errors->has('social.email') ? 'is-invalid' : '' }}" placeholder="email@domain.com">
                @error('social.email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card rounded-0 border-0 shadow-sm" style="position: sticky; top: 20px;">
            <div class="card-header bg-gradient-primary text-white">
                <h6 class="mb-0"><i class="fa fa-image"></i> Foto Seniman</h6>
            </div>
            <div class="card-body">
                <div class="preview-cover">
                    <img class="border-1" @if(isset($karya) && $karya->image) src="{{asset('uploads/senimans/'.$karya->image)}}" @else src="{{asset('assets/img/default.jpg')}}" @endif id="foto-seniman">
                </div>
                <div class="media-body m-auto">
                    <input id="input-foto-seniman" type="file" name="fotoseniman" class="d-none @error('fotoseniman') is-invalid @enderror" accept="image/jpeg,image/jpg,image/png,image/webp"/>
                    <label for="input-foto-seniman" class="btn btn-sm btn-dark rounded-0 btn-block mt-3">
                        <i class="fa fa-folder-open"></i> Pilih Foto Seniman
                    </label>
                    <small class="text-muted d-block mt-2" style="color:#2b6cb0!important;">
                        Ukuran ideal: <b>400x500 (px)</b> &nbsp;|&nbsp; Format: <b>webp, jpg, jpeg, png</b>
                    </small>
                    <small class="text-muted d-block">
                        Max size: <b>2MB</b>
                    </small>
                    @error('fotoseniman')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
        </div>
    </div>
    
</div>