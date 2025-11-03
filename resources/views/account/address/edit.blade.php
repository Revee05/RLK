@extends('account.partials.layout')
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style type="text/css">
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
    </style>
@endsection
@section('content')
<section class="py-4" id="customer-account">
    <div class="container">
        <div class="row bg-white py-4" style="border-radius: 10px;">
            <div class="col-sm-3 border-end">
                @include('account.partials.nav')
            </div>
            <div class="col-sm-9">
                <div class="card">
                    <div class="card-header bg-transparant">
                        <div class="d-flex justify-content-between">
                            <div class="d-block">
                                FORM ALAMAT
                            </div>
                            <a href="#" class="d-block text-decoration-none btn btn-danger btn-sm">
                                < Daftar Alamat
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- Erors notification --}}
                        @include('admin.partials._errors')
                        {{ Form::model($user_address, array('route' => array('account.address.update', $user_address->id), 'method' => 'PUT')) }}
                            @include('account.address.form')
                        
                        {{ Form::submit('Simpan', array('class' => 'btn btn-danger btn-sm rounded-0')) }}
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('js')
    <script type="text/javascript" src='//ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js'></script>
    <!--<![endif]-->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> 
    <script type="text/javascript" src="{{asset('js/jquery.chained.min.js')}}"></script>
    <script type="text/javascript">
      $(document).ready(function() {
            $('.select2').select2();
            $("#kabupaten").chained("#provinsi");
            $("#kecamatan").chained("#kabupaten");
            $('#kecamatan').on('change',function(){

                var pid = $(this).val(); //$(this).val();
                if(pid){
                    $('#desa_id').prop('disabled', false);
                    $("#desa_id").select2({
                        placeholder: 'Cari Desa...',
                        // width: '350px',
                        allowClear: true,
                        ajax: {
                            url: '/account/address/get/desa/'+pid,
                            dataType: 'json',
                            delay: 250,
                            data: function(params) {
                                 console.log('params',params);
                                return {
                                    term: params.term || '',
                                    id: params.pid || '',
                                    page: params.page || 1
                                }
                            },
                            cache: true
                        }
                    });
                }else {
                    console.log('disabled')
                }
            })
        });
    </script>
@endpush