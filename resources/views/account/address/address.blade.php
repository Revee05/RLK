@extends('account.partials.layout')
@push('css')
<style type="text/css">
#customer-account {
    background-color: #eef0f8;
    padding: 36px 0 64px;
}
.preview-cover {
    height:160px;
    width: 100%;
    overflow: hidden;
    position: relative;
    border: 1px solid  #5a5c69;
}
.preview-cover img{
    height:100%;
    width: 100%;
    object-fit: cover;
    object-position: center;
}
</style>
@endpush
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
                                DAFTAR ALAMAT
                            </div>
                            <a href="{{route('account.address.create')}}" class="d-block text-decoration-none btn btn-danger btn-sm">
                                + Tambah Alamat
                            </a>
                        </div>
                    </div>
                        <ul class="list-group">                            
                        @foreach($userAddress as $ua)
                            <li class="list-group-item">
                                <strong>{{$ua->name}} - {{ucwords($ua->label_address)}}</strong>
                                <div class="d-block">{{$ua->phone}}</div>
                                <div class="d-block">{{$ua->address}}, {{$ua->provinsi->nama_provinsi}}, {{$ua->kabupaten->nama_kabupaten}}, {{$ua->kecamatan->nama_kecamatan}}</div>
                                <a href="{{route('account.address.edit',$ua->id)}}" class="text-decoration-none text-danger small">Ubah Alamat </a>
                                 <form action="{{route('account.address.destroy',[$ua->id])}}" method="post" class="d-inline">
                                    @method('delete')
                                    @csrf
                                    <button onclick="return confirm('Are you sure want to delete this record?')"
                                        type="submit" class="btn">
                                        <span class="text-danger small">Hapus</span>
                                    </button>
                                </form>
                                
                            </li>
                        @endforeach
                        </ul>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('js')
<script type="text/javascript">

</script>
@endsection