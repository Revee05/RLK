{{-- Sidebar --}}
<ul class="navbar-nav bg-gradient-dark sidebar sidebar-dark accordion toggled" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon">
            {{-- <i class="fas fa-mosque"></i> --}}
            <i class="fas fa-envelope"></i>
        </div>
        <div class="sidebar-brand-text mx-3">UNDANGAN <sup>D</sup></div>
    </a>
    <!-- Divider -->
    <hr class="sidebar-divider my-0">
    <!-- Nav Item - Dashboard -->
    <li class="nav-item @yield('dashboard')">
        <a class="nav-link" href="{{route('admin.index')}}">
            <i class="fas fa-fw fa-home"></i>
            <span>Beranda</span>
        </a>
    </li>
    <!-- Nav Item - Panitian -->
    <li class="nav-item @yield('wedding')">
        <a class="nav-link" href="{{route('wedding.index')}}">
            <i class="fas fa-fw fa-life-ring"></i>
            <span>Pernikahan</span>
        </a>
    </li>
   {{--  <li class="nav-item @yield('birthday')">
        <a class="nav-link" href="{{route('birthday.index')}}">
            <i class="fas fa-fw fa-birthday-cake"></i>
            <span>Ulang Tahun</span>
        </a>
    </li> --}}
    {{-- <li class="nav-item @yield('khitan')">
        <a class="nav-link" href="{{route('khitan.index')}}">
            <i class="fas fa-fw fa-dove"></i>
            <span>Khitanan</span>
        </a>
    </li> --}}
    {{-- <li class="nav-item @yield('panel')">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-baby"></i>
            <span>Aqiqah</span>
        </a>
    </li> --}}
    <!-- Divider -->
    <hr class="sidebar-divider my-0">
    <!-- Nav Item - Dashboard -->
    <li class="nav-item @yield('pembayaran')">
        <a class="nav-link" href="{{route('admin.index')}}">
            <i class="fas fa-fw fa-shopping-cart"></i>
            <span>Pembayaran</span>
        </a>
    </li>
    <!-- Heading -->
    @if(Auth::user()->access == 'admin')
    <hr class="sidebar-divider">
    <div class="sidebar-heading">
        Settings
    </div>
    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item @yield('bank') @yield('zona') @yield('paket') @yield('fitur') @yield('paket-fitur') @yield('desain') @yield('musik')">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMaster"
            aria-expanded="true" aria-controls="collapseMaster">
            <i class="fas fa-fw fa-list"></i>
            <span>Master</span>
        </a>
        <div id="collapseMaster" class="collapse @yield('collapseMaster')" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Master Data</h6>
                <a class="collapse-item @yield('bank')" href="{{route('master.bank.index')}}">Bank</a>
                <a class="collapse-item @yield('zona')" href="{{route('master.zona.index')}}">Zona</a>
                <a class="collapse-item @yield('paket')" href="{{route('master.paket.index')}}">Paket</a>
                <a class="collapse-item @yield('fitur')" href="{{route('master.fitur.index')}}">Fitur</a>
                <a class="collapse-item @yield('paket-fitur')" href="{{route('master.paket-fitur.index')}}">Paket Fitur</a>
                <a class="collapse-item @yield('desain')" href="{{route('master.desain.index')}}">Desain</a>
                <a class="collapse-item @yield('musik')" href="{{route('master.musik.index')}}">Musik</a>
            </div>
        </div>
    </li>
    @endif
    @if(Auth::user()->access == 'admin' or Auth::user()->access == 'reseller')
    <!-- Nav Item - User -->
    <li class="nav-item @yield('users')">
        <a class="nav-link" href="{{route('master.users.index')}}">
            <i class="fas fa-fw fa-users"></i>
            <span>Anggota</span>
        </a>
    </li>
    @endif
    @if(Auth::user()->access == 'admin')
    <!-- Nav Item - User -->
    <li class="nav-item @yield('setting')">
        <a class="nav-link" href="{{route('master.setting.index')}}">
            <i class="fas fa-fw fa-cog"></i>
            <span>Setting</span>
        </a>
    </li>
    <!-- Nav Item - User -->
     <li class="nav-item @yield('activity') @yield('log')">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseActivity"
            aria-expanded="true" aria-controls="collapseActivity">
            <i class="fas fa-fw fa-list"></i>
            <span>Management</span>
        </a>
        <div id="collapseActivity" class="collapse @yield('collapseActivity')" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Management</h6>
                <a class="collapse-item @yield('activity')" href="{{route('master.activity.index')}}">Activity</a>
                <a class="collapse-item @yield('log')" href="{{route('master.log-viewer.index')}}">Log</a>
            </div>
        </div>
    </li>
    @endif
    
    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">
    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
<!-- End of Sidebar