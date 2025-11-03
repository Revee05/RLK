<div class="form-group">
    {{ Form::label('name', 'Nama Provinsi') }}
    {{ Form::select('provinsi_id', $provinsis, null,array('class' => 'form-control form-control-sm','placeholder' => 'Pilih Provinsi')) }}
</div>
<div class="form-group">
    {{ Form::label('name', 'Nama Kabupaten') }}
    {{ Form::text('nama_kabupaten', null, array('class' => 'form-control form-control-sm ','placeholder' => 'Nama Kabupaten')) }}
</div>