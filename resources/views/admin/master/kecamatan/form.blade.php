<div class="form-group">
    {{ Form::label('name', 'Nama Kabupaten') }}
    {{ Form::select('kabupaten_id', $kabupatens, null,array('class' => 'form-control form-control-sm','placeholder' => 'Pilih Kabupaten')) }}
</div>
<div class="form-group">
    {{ Form::label('name', 'Nama Kecamatan') }}
    {{ Form::text('nama_kecamatan', null, array('class' => 'form-control form-control-sm ','placeholder' => 'Nama Kecamatan')) }}
</div>