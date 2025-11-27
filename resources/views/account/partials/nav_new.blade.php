<!-- Left sidebar -->
<div class="col-md-3">
    <div class="text-center mb-4">
        <img src="{{ asset(Auth::user()->foto) ?? 'https://www.figma.com/api/mcp/asset/1bcfd75e-90c9-43bf-8586-79d92d395def' }}"
            alt="avatar" class="rounded-circle img-fluid mb-3" style="width:200px; height:200px; object-fit:cover;">
        <h1 class="profile-name mb-0">{{ Auth::user()->name }}</h1>
        <a href="#" class="profile-view-link" data-bs-toggle="modal" data-bs-target="#modalProfilePicture">Lihat
            Profil</a>
    </div>


    <div class="list-group mb-4 text-center">
        <a href="{{ route('account.dashboard') }}"
            class="list-group-item list-group-item-action border-nav-top py-2 {{ request()->routeIs('account.dashboard') ? 'active' : '' }}"
            aria-current="true">
            Akun
        </a>
        <a href="{{ route('account.katasandi') }}"
            class="list-group-item list-group-item-action border-nav-middle py-2 {{ request()->routeIs('account.katasandi') ? 'active' : '' }}">Ubah
            Password</a>
        <a href="{{ route('account.address.index') }}"
            class="list-group-item list-group-item-action border-nav-middle py-2 {{ request()->routeIs('account.address.*') ? 'active' : '' }}">Alamat</a>
        <a href="#" class="list-group-item list-group-item-action border-nav-middle py-2">Favorit</a>
        <a href="{{ route('account.auction_history') }}" 
            class="list-group-item list-group-item-action border-nav-middle py-2 {{ request()->routeIs('account.auction_history') ? 'active' : '' }}">
            Riwayat Lelang
        </a>
        <a href="{{ route('account.purchase.history') }}" 
            class="list-group-item list-group-item-action border-nav-middle py-2 {{ request()->routeIs('account.purchase.history') ? 'active' : '' }}">Riwayat
            Pembelian</a>
        <a href="#" class="list-group-item list-group-item-action border-nav-middle  py-2">Pengaturan
            Notifikasi</a>
        <a class="list-group-item list-group-item-action border-nav-bottom text-danger py-2"
            href="{{ route('logout') }}"
            onclick="event.preventDefault();
    document.getElementById('logout-form').submit();">
            Logout</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</div>
