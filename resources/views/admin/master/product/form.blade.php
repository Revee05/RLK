<div class="form-group row">
    {{ Form::label('name', 'Nama Product *',['class'=>'col-sm-2 col-form-label']) }}
    <div class="col-sm-10">
        {{ Form::text('title', null, array('class' => 'form-control form-control-sm ','placeholder' => 'Nama barang')) }}
    </div>
</div>
<div class="form-group row">
    {{ Form::label('name', 'Deskripsi Product *',['class'=>'col-sm-2 col-form-label']) }}
    <div class="col-sm-10">
        {{ Form::textarea('description', null, array('class' => 'form-control form-control-sm ','id'=> 'deskripsi')) }}
    </div>
</div>
<div class="form-group row">
    {{ Form::label('name', 'Harga Product *',['class'=>'col-sm-2 col-form-label']) }}
    <div class="col-sm-10">
        {{ Form::number('price', null, array('class' => 'form-control form-control-sm ','placeholder' => 'Harga barang','id'=>'harga')) }}
        <span class="text-danger text-xs">Penulisan harga tanpa titik</span>
    </div>
</div>
<div class="form-group row">
    {{ Form::label('name', 'Diskon *',['class'=>'col-sm-2 col-form-label']) }}
    <div class="col-sm-3">
        <div class="input-group input-group-sm mb-3">
        {{ Form::number('diskon', null, array('class' => 'form-control form-control-sm ')) }}
          <div class="input-group-append">
            <span class="input-group-text" id="basic-addon2">.%</span>
          </div>
        </div>
    </div>
</div>

<div class="form-group row">
    {{ Form::label('name', 'Kategori Product *',['class'=>'col-sm-2 col-form-label']) }}
    <div class="col-sm-10">
        {{ Form::select('kategori_id', $kategoris, null, array('class' => 'form-control form-control-sm ','placeholder' => 'Pilih Kategori')) }}
    </div>
</div>
<div class="form-group row">
    {{ Form::label('name', 'Seniman *',['class'=>'col-sm-2 col-form-label']) }}
    <div class="col-sm-10">
        {{ Form::select('karya_id', $karyas,null, array('class' => 'form-control form-control-sm ')) }}
    </div>
</div>
<div class="form-group row">
    {{ Form::label('name', 'Stock *',['class'=>'col-sm-2 col-form-label']) }}
    <div class="col-sm-3">
        <div class="input-group input-group-sm mb-3">
        {{ Form::text('stock', null, array('class' => 'form-control form-control-sm ')) }}
          <div class="input-group-append">
            <span class="input-group-text" id="basic-addon2">.buah</span>
          </div>
        </div>
    </div>
</div>
<div class="form-group row">
    {{ Form::label('name', 'Sku',['class'=>'col-sm-2 col-form-label']) }}
    <div class="col-sm-2">
        {{ Form::text('sku', null, array('class' => 'form-control form-control-sm ')) }}
        <span class="text-xs">Kode Unik Produk</span>
    </div>
</div>
<div class="form-group row">
    {{ Form::label('name', 'Berat *',['class'=>'col-sm-2 col-form-label']) }}
    <div class="col-sm-3">
        <div class="input-group input-group-sm mb-3">
        {{ Form::text('weight', null, array('class' => 'form-control form-control-sm ')) }}
          <div class="input-group-append">
            <span class="input-group-text" id="basic-addon2">.gram</span>
          </div>
        </div>
    </div>
</div>
<div class="form-group row">
    {{ Form::label('name', 'Proteksi Kerusakan',['class'=>'col-sm-2 col-form-label']) }}
    <div class="col-sm-2">
        {{ Form::checkbox('asuransi', null,$product ?? '', array('class' => '')) }}
    </div>
</div>
<div class="form-group row">
    {{ Form::label('name', 'Panjang',['class'=>'col-sm-2 col-form-label']) }}
    <div class="col-sm-2">
        <div class="input-group input-group-sm mb-3">
        {{ Form::text('long', null, array('class' => 'form-control form-control-sm ')) }}
          <div class="input-group-append">
            <span class="input-group-text" id="basic-addon2">.mm</span>
          </div>
        </div>
    </div>
    {{ Form::label('name', 'Lebar',['class'=>'col-sm-1 col-form-label']) }}
    <div class="col-sm-2">
        <div class="input-group input-group-sm mb-3">
        {{ Form::text('width', null, array('class' => 'form-control form-control-sm ')) }}
          <div class="input-group-append">
            <span class="input-group-text" id="basic-addon2">.mm</span>
          </div>
        </div>
    </div>
    {{ Form::label('name', 'Tinggi',['class'=>'col-sm-1 col-form-label']) }}
    <div class="col-sm-2">
        <div class="input-group input-group-sm mb-3">
        {{ Form::text('height', null, array('class' => 'form-control form-control-sm ')) }}
          <div class="input-group-append">
            <span class="input-group-text" id="basic-addon2">.mm</span>
          </div>
        </div>
    </div>
</div>
<div class="form-group row">
    {{ Form::label('name', 'Kondisi',['class'=>'col-sm-2 col-form-label']) }}
    <div class="col-sm-2">
        {{ Form::select('kondisi', ['baru'=>'Baru','bekas'=>'Bekas'],null, array('class' => 'form-control form-control-sm ')) }}
    </div>
</div>
<div class="form-group row">
    {{ Form::label('name', 'Kelipatan',['class'=>'col-sm-2 col-form-label']) }}
    <div class="col-sm-2">
        {{ Form::number('kelipatan', null, array('class' => 'form-control form-control-sm ')) }}
         <span class="text-danger text-xs">Penulisan kelipatan tanpa titik</span>
    </div>
</div>
<div class="form-group row">
    {{ Form::label('name', 'Waktu selesai',['class'=>'col-sm-2 col-form-label']) }}
    <div class="col-sm-3">
        <div class="input-group input-group-sm mb-3">
        {{ Form::text('end_date', null, array('class' => 'form-control form-control-sm','id'=>'endate')) }}
          <div class="input-group-append">
            <span class="input-group-text" id="basic-addon2">.24 Jam</span>
          </div>
        </div>
    </div>
</div>
<div class="form-group row">
    {{ Form::label('name', 'Kelengkapan Karya',['class'=>'col-sm-2 col-form-label']) }}
    <div class="col-sm-3">
        {{-- {{dd($product->kelengkapans)}} --}}
        @foreach($kelengkapans as $kel)
        <div class="d-block">
            <input type="checkbox" name="kelengkapan_id[]" value="{{$kel->id}}" @if(isset($product) && $product->kelengkapans->contains($kel->id)) checked @endif> {{ucfirst($kel->name)}}
            {{-- <input type="checkbox" name="kelengkapan_id[]" value="{{$kel->id}}" @if(isset($kelengkapan_products) && in_array($kel->id, $kelengkapan_products)) checked @endif> {{ucfirst($kel->name)}} --}}
        </div>
        @endforeach
    </div>
</div>
<div class="form-group row">
    {{ Form::label('type', 'Tipe Produk *',['class'=>'col-sm-2 col-form-label']) }}
    <div class="col-sm-3">
        {{ Form::select(
            'type',
            ['normal' => 'Normal', 'featured' => 'Featured'],
            old('type', $product->type ?? null),
            ['class' => 'form-control form-control-sm', 'placeholder' => 'Pilih Tipe']
        ) }}
        @if ($errors->has('type'))
            <span class="text-danger">{{ $errors->first('type') }}</span>
        @endif
    </div>
</div>

