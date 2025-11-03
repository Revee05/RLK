<div class="form-group">
    {{ Form::label('name', 'Nama Kecamatan') }}
    {{ Form::select('kecamatan_id', $kecamatans, null,array('class' => 'form-control form-control-sm','placeholder' => 'Pilih Kecamatan')) }}
</div>
<div class="form-group">
    {{ Form::label('name', 'Nama Desa') }}
    {{ Form::text('nama_desa', null, array('class' => 'form-control form-control-sm ','placeholder' => 'Nama Desa')) }}
</div>
<div class="form-group">
    {{ Form::label('name', 'Kode Pos') }}
    {{ Form::text('kodepos', null, array('class' => 'form-control form-control-sm ','placeholder' => 'Kodepos')) }}
</div>