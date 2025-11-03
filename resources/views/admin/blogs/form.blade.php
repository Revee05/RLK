<div class="row">
    <div class="col-sm-8">
        <div class="card shadow mb-4 rounded-0">
            <div class="card-body">
                <div class="form-group">
                    {{ Form::label('name', 'Title') }}
                    {{ Form::text('title', null, array('class' => 'form-control form-control-sm ','placeholder' => 'Title')) }}
                </div>
                <div class="form-group row">
                    {{ Form::label('name', 'Deskripsi',['class'=>'col-sm-12 col-form-label']) }}
                    <div class="col-sm-12">
                        {{ Form::textarea('body', null, array('class' => 'form-control form-control-sm ','id'=> 'page')) }}
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card">
            <div class="card-body">
              {{--   <div class="form-group">
                    {{ Form::label('name', 'URL Slug') }}
                    {{ Form::text('slug', null, array('class' => 'form-control form-control-sm ')) }}
                </div> --}}
                <div class="form-group">
                    {{ Form::label('name', 'Status') }}
                    {{ Form::select('status', ['DRAFT'=>'DRAFT','PENDING'=>'PENDING','PUBLISHED'=>'PUBLISHED'],null, array('class' => 'form-control form-control-sm ')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('name', 'Category') }}
                    {{ Form::select('kategori_id',$cats,null, array('class' => 'form-control form-control-sm ')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('name', 'Tags') }}
                    {{-- {{ Form::select('tagger[]',null, array('class' => 'form-control form-control-sm','id'=>'selTag','multiple'=>'multiple')) }} --}}
                    <select name="tagger[]" class="form-control form-control-sm" id="selTag" multiple>
                        <option>--Select Tags--</option>
                        @if(isset($blog) && $blog->tags)
                            @foreach($blog->tags as $tag)
                                <option value="{{$tag->name}}" selected>{{$tag->name}}</option>
                            @endforeach
                        @endif
                    </select>
                    {{-- <input type="text" name="tagger[]" class="form-control form-control-sm" id="selTag" multiple> --}}
                </div>
                
                <div class="preview-cover">
                    <img class="border-1" @if(isset($blog) && $blog->image) src="{{asset('uploads/blogs/'.$blog->image)}}" @else src="{{asset('assets/img/default.jpg')}}" @endif id="foto-blog">
                </div>
                <div class="media-body m-auto">
                    <input id="input-foto-blog" type="file" name="fotoblog" class="d-none @error('fotoblog') is-invalid @enderror" accept="image/*"/>
                    <label for="input-foto-blog" class="btn btn-sm btn-dark rounded-0 btn-block">
                        <i class="fa fa-folder-open"></i> Pilih Foto
                    </label>
                    @error('fotoblog')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                 <a href="{{ route('admin.posts.index') }}" class="btn btn-primary btn-sm rounded-0">Kembali</a>
                {{ Form::submit('Simpan', array('class' => 'btn btn-danger btn-sm rounded-0')) }}
            </div>
        </div>
    </div>
</div>