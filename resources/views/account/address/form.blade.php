<div class="form-group">
    {{ Form::label('name', 'Nama Penerima') }}
    {{ Form::text('name', null, array('class' => 'form-control','placeholder' => 'Nama')) }}
</div>
<div class="form-group py-2">
    {{ Form::label('name', 'Nomer Hp') }}
    {{ Form::text('phone', null, array('class' => 'form-control','placeholder' => 'Phone')) }}
</div>
<div class="form-group py-2">
    {{ Form::label('name', 'Label Alamat') }}
    {{ Form::select('label_address',['rumah'=>'Rumah','apartemen'=>'Apartemen','kantor'=>'Kantor','kos'=>'Kos'],null,array('class' => 'form-control','placeholder' => 'Pilih Label Alamat')) }}
</div>

<div class="form-group py-2">
    {{ Form::label('name', 'Provinsi') }}
    {{ Form::select('provinsi_id',$provinsis,null,array('class' => 'form-control select2','placeholder' => 'Pilih Provinsi','id'=>'provinsi')) }}
</div>
<div class="form-group py-2">
    {{ Form::label('name', 'Kabupaten') }}
     <select id='kabupaten' class="form-control select2" name="kabupaten_id">
        <option value=''>- Pilih Kabupaten -</option>
        @foreach($kabupatens as $kab)
        <option value='{{$kab->id}}' class="{{$kab->provinsi_id}}" @if(isset($user_address) && $user_address->kabupaten->id == $kab->id) selected @endif>{{$kab->nama_kabupaten}}</option>
        @endforeach
    </select>
</div>
<div class="form-group py-2">
    {{ Form::label('name', 'Kecamatan') }}
     <select id='kecamatan' class="form-control select2" name="kecamatan_id">
        <option value=''>- Pilih Kecamatan -</option>
        @foreach($kecamatans as $kec)
        <option value='{{$kec->id}}' class="{{$kec->kabupaten_id}}" @if(isset($user_address) && $user_address->kecamatan->id == $kec->id) selected @endif>{{$kec->nama_kecamatan}}</option>
        @endforeach
    </select>
</div>
<div class="form-group py-2">
    {{ Form::label('name', 'Address') }}
    {{ Form::textarea('address', null, array('class' => 'form-control','placeholder' => 'Alamat lengkap ...','rows'=>'4')) }}
</div>
{{-- <div class="form-group py-2">
    {{ Form::label('name', 'Desa') }}
    <select id='desa_id' class="form-control select2" name="desa_id">
    @if(empty($user_address->desa_id))
        <option value='0'>- Pilih Desa -</option>
    @else
        <option value="{{ $user_address->desa_id }}" selected>
            {{ $user_address->desa->nama_desa }}
        </option>
    @endif
    </select>
</div> --}}
{{-- <div class="form-group py-2">
    {{ Form::label('name', 'Kode pos') }}
    {{ Form::select('kodepos',$kodepos,null,array('class' => 'form-control','placeholder' => 'Pilih Desa')) }}
</div> --}}


<input type="hidden" name="user_id" value="{{Auth::user()->id}}">
<br>