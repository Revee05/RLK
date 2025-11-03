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
                    {{ Form::label('name', 'Page Status') }}
                    {{ Form::select('status', ['DRAFT'=>'DRAFT','PENDING'=>'PENDING','PUBLISHED'=>'PUBLISHED'],null, array('class' => 'form-control form-control-sm ')) }}
                </div>
                <a href="{{ route('admin.posts.index') }}" class="btn btn-primary btn-sm rounded-0">Kembali</a>
                {{ Form::submit('Simpan', array('class' => 'btn btn-danger btn-sm rounded-0')) }}
            </div>
        </div>
    </div>
</div>