<a class="text-decoration-none text-dark d-block" style="font-weight: 500;" data-bs-toggle="collapse" href="#informasi" role="button" aria-expanded="false" aria-controls="informasi">
    Informasi Akun
    <i class="fa fa-angle-down float-end"></i> 
</a>
<div class="collapse show" id="informasi">
    <ul class="list-group list-group-flush">
        <li class="list-group-item">
           <a href="{{route('account.dashboard')}}" class="text-decoration-none text-dark">Profil</a>
        </li>
        {{-- <li class="list-group-item">
           <a href="{{route('account.address.index')}}" class="text-decoration-none text-dark">Alamat</a>
        </li> --}}
        
        <li class="list-group-item">
              <a href="{{route('account.katasandi')}}" class="text-decoration-none text-dark">Ubah Kata Sandi</a>
        </li>
        
    </ul>
</div>
<a class="text-decoration-none text-dark d-block" style="font-weight: 500;" data-bs-toggle="collapse" href="#pembelian" role="button" aria-expanded="false" aria-controls="pembelian">
    Pembelian
    <i class="fa fa-angle-down float-end"></i> 
</a>
<div class="collapse show" id="pembelian">
    <ul class="list-group list-group-flush">
        {{-- <li class="list-group-item"> --}}
            {{-- <a href="#" class="text-decoration-none text-dark">Status Pesanan</a> --}}
            {{-- <a href="{{route('account.orders.index')}}" class="text-decoration-none text-dark">Status Pesanan</a> --}}
        {{-- </li> --}}
        <li class="list-group-item">
              <a href="{{route('account.orders.index')}}" class="text-decoration-none text-dark">Riwayat Pesanan</a>
        </li>
        
    </ul>
</div>
<a class="text-decoration-none text-dark d-block" href="{{ route('logout') }}" 
    onclick="event.preventDefault();
    document.getElementById('logout-form').submit();">
    Logout
</a>
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>