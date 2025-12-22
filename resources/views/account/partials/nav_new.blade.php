<!-- Mobile-only profile quick view -->
<div class="col-12 d-block d-md-none text-center mb-4">
    <img src="{{ Auth::user()->foto ? asset(Auth::user()->foto) : asset('assets/img/default-profile-picture.webp') }}"
        alt="avatar" class="rounded-circle img-fluid mb-3" style="width:120px; height:120px; object-fit:cover;">
    <h1 class="profile-name mb-0">{{ Auth::user()->name }}</h1>
    <a href="#" class="profile-view-link" data-bs-toggle="modal" data-bs-target="#modalProfilePicture">Lihat
        Profil</a>
</div>

<!-- Left sidebar -->
<div class="col-md-3 d-none d-md-block">
    <div class="text-center mb-4">
        <img src="{{ Auth::user()->foto ? asset(Auth::user()->foto) : asset('assets/img/default-profile-picture.webp') }}"
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
        <a href="{{ route('account.favorites') }}"
        class="list-group-item list-group-item-action border-nav-middle py-2 {{ request()->routeIs('account.favorites') ? 'active' : '' }}">
        Favorit
        </a>

        <a href="{{ route('account.auction_history') }}"
            class="list-group-item list-group-item-action border-nav-middle py-2 {{ request()->routeIs('account.auction_history') ? 'active' : '' }}">
            Riwayat Lelang
        </a>
        <a href="{{ route('account.purchase.history') }}"
            class="list-group-item list-group-item-action border-nav-middle py-2 {{ request()->routeIs('account.purchase.history') ? 'active' : '' }}">Riwayat
            Pembelian</a>
        <a href="{{ route('account.notifications') }}"
            class="list-group-item list-group-item-action border-nav-middle  py-2 {{ request()->routeIs('account.notifications') ? 'active' : '' }}">Pengaturan
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
