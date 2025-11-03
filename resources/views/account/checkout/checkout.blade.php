@extends('account.partials.layout')
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style type="text/css">
    .checkout-figure {
    position: relative;
    overflow: hidden;
    height: 100px;
    width: 100px;
    }
    .checkout-figure img{
    object-fit: cover;
    object-position: center;
    height: 100%;
    width: 100%;
    }
    #customer-account {
    background-color: #eef0f8;
    padding: 36px 0 64px;
    }
    .select2-container .select2-selection--single {
        height: 35px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
     line-height: 25px;
     padding: 0.375rem 0.75rem;
    }
    [type=search] {
        outline: none;
    }
    .border-transparant {
        border-color: transparent;
    }
</style>
 <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')
<section class="py-1" id="customer-account">
    <div class="container">
            {{ Form::open(array('route' => 'account.orders.store')) }}
        <div class="row py-4 justify-content-center" style="border-radius: 10px;">
            {{-- <div class="col-sm-3 border-end">
                @include('account.partials.nav')
            </div> --}}
            <div class="col-sm-7 bg-white shadow py-4">
                <h2 style="font-weight:bold;font-size: 20px;" class="text-center">Checkout</h2>
                <div class="d-flex">
                    <div class="d-block" style="width:20%">
                        <div class="checkout-figure">
                            <img src="{{asset($product->imageUtama->path ?? 'assets/img/default.jpg')}}" alt="Generic placeholder image">
                        </div>
                    </div>
                    <div class="d-block" style="width:80%">
                        <h5 class="mt-0">{{$product->title}}</h5>
                        <p>{{$bid->price ?? $product->price}}</p>
                        <p>{{$product->weight}}</p>
                    </div>
                </div>
                <div class="col-sm-12 py-1">
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
                         <select id='kabupaten' class="form-control select2 kabupatens" name="kabupaten_id">
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
                        {{ Form::label('name', 'Alamat Pengiriman') }}
                        <textarea class="form-control" name="address" required></textarea>
                    </div>
                     <div class="form-group py-2">
                    <input type="hidden" name="weight" id="weight" value="{{ $product->weight }}">
                    <input type="hidden" name="pengirim" value="jne">
                    <input type="hidden" name="product_id" value="{{$product->id}}">
                    <input type="hidden" name="bid_terakhir" value="{{$bid->price ?? $product->price}}">
                    <input type="hidden" name="total_ongkir" id="total_ongkos_kirim">
                    <input type="hidden" name="asuransi_pengiriman" id="asuransi_pengiriman">
                    <input type="hidden" name="total_tagihan" id="total_tagihan">
                    {{ Form::label('name', 'Pilih Ongkir') }}
                     <select class="form-control" name="jenis_ongkir" id="courier" required>
                        <option value="">Pilih Ongkir</option>
                    </select>
                    <div class="form-group py-2">
                        {{ Form::label('name', 'Proteksi Kerusakan') }}<br>
                        <input type="checkbox" name="proteksi_kerusakan" id="asuransi" onclick="onChecked()">
                        <span class="text-dark text-xs">Melindungi produkmu dari risiko rusak maupun kerugian selama 6 bulan</span>
                    </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 bg-white shadow py-4">
                <table class="table">
                    <tbody>
                        <tr class="border-transparant">
                            <td><b>Ringkasan belanja</b></td>
                            <td></td>
                        </tr>
                        <tr class="border-transparant">
                            <td>Total Harga</td>
                            <td>RP. {{$bid->price ?? $product->price}}</td>
                        </tr>
                        <tr class="border-transparant">
                            <td>Total Ongkos Kirim</td>
                            <td> 
                                <span id="ongkir">Rp 0</span>
                            </td>
                        </tr>
                        <tr class="border-transparant">
                            <td>Asuransi Pengiriman</td>
                            <td>
                                <span id="setasuransi">Rp 0</span>
                            </td>
                        </tr>
                        
                        <tr>
                            <td><b>Total Tagihan</b></td>
                            <td>Rp. 
                                <span id="total"></span>
                            </td>
                        </tr>
                        <tr class="border-transparant">
                            <td colspan="2">
                                <button class="btn btn-danger" style="width:100%" type="submit">CHECKOUT</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
          
            </div>
        </div>
             {{ Form::close() }}
    </div>
</section>
@endsection
@section('js')
    <script type="text/javascript" src='//ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js'></script>
    <!--<![endif]-->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> 
    <script type="text/javascript" src="{{asset('js/jquery.chained.min.js')}}"></script>
    <script type="text/javascript">
    function onChecked() {
        // check if checkbox is checked
        if (document.querySelector('#asuransi').checked) {
          // if checked
          return true;
        } else {
          
          return false;
          // console.log('unchecked');
        }
      }
    $(document).ready(function() {
            $('.select2').select2();
            $(".kabupatens").chained("#provinsi");
            $("#kecamatan").chained("#kabupaten");
    //JIKA KECAMATAN DIPILIH
    $("#kabupaten").on('change', function() {
        //MEMBUAT EFEK LOADING SELAMA PROSES REQUEST BERLANGSUNG
        console.log("masuk",$(this).val(),$('#weight').val());
          var destination = $(this).val();
          $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#courier').empty()
            $('#courier').append('<option value="">Loading...</option>')
            // MENGIRIM PERMINTAAN KE SERVER UNTUK MENGAMBIL DATA API
            if (destination) {

                $.ajax({
                    url: "{{ url('account/checkout/get/ongkir') }}",
                    type: "POST",
                    data: { destination: $(this).val(), weight: $('#weight').val() },
                    success: function(html){
                        console.log(html.rajaongkir.results[0].costs);
                        
                        $('#courier').empty()
                        $('#courier').append('<option value="">Pilih Kurir</option>')
                        
                        $.each(html.rajaongkir.results[0].costs, function(key, item) {
                            let courier = item.service + ' - ' + item.cost[0].etd + ' (Rp '+ item.cost[0].value +')'
                            let value = item.service + '-'+ item.cost[0].value
                            //DAN MASUKKAN KE DALAM OPTION SELECT BOX
                            $('#courier').append('<option value="'+value+'">' + courier + '</option>')
                        })
                
                    },
                    error:function(e){
                        console.log(e);
                    }
                });
            } else {
                console.log("Pilih destination dulu!")
            }
        })
    $('#courier').on('change', function() {
        //UPDATE INFORMASI BIAYA PENGIRIMAN
        let split = $(this).val().split('-');
        $('#ongkir').text('Rp ' + split[1]);

        //UPDATE INFORMASI TOTAL (SUBTOTAL + ONGKIR)
        let subtotal = "{{$bid->price ?? $product->price}}";
        // let total = 0;
        let total = parseInt(subtotal) + parseInt(split['1']);
        document.getElementById('total_ongkos_kirim').value= split[1];
        // let asuransi = $('#asuransi');
        $('#asuransi').on('change', function() {
            console.log($(this).checked,$(this));
            if (document.querySelector('#asuransi').checked) {
              // if checked
                $('#setasuransi').text('Rp 10%');
                let asuransi = 0.1 * total;
                let totali = parseInt(total) + parseInt(asuransi);
                $('#total').text(totali);

                document.getElementById('asuransi').value = '1';
                document.getElementById('total_tagihan').value = totali;
                // console.log('checked');
            } else {
                $('#setasuransi').text('Rp 0');
                let asuransi = 0;
                let totali = parseInt(total) + parseInt(asuransi);
                $('#total').text(totali);
                document.getElementById('asuransi').value = '0';
                document.getElementById('total_tagihan').value = totali;
                // console.log('unchecked',total);
            }
        });
         $('#total').text(total);





        })
    });
    </script>
@endsection