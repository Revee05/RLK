<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('admin.dashboard')}}">
        <div class="sidebar-brand-icon">
            {{-- <i class="fas fa-laugh-wink"></i> --}}
            <i class="fas fa-store"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Lelang</sup></div>
    </a>
    <!-- Divider -->
    <hr class="sidebar-divider my-0">
    <!-- Nav Item - Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="{{route('admin.dashboard')}}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
        </li>
        <!-- Divider -->
        <hr class="sidebar-divider">
        <!-- Heading -->
        <div class="sidebar-heading">
            Master
        </div>
        <!-- Nav Item - Pages Collapse Menu -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                aria-expanded="true" aria-controls="collapseTwo">
                <i class="fas fa-cart-plus"></i>
                <span>Products</span>
            </a>
            <div id="collapseTwo" class="collapse @yield('collapseMaster')" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item @yield('addproduct')" href="{{route('master.product.create')}}">Add Product</a>
                    <a class="collapse-item @yield('product')" href="{{route('master.product.index')}}">All Products</a>
                    <a class="collapse-item @yield('kategoriproduct')" href="{{route('master.kategori.index',['cat_type'=>'product'])}}">Categories</a>
                   {{--  <a class="collapse-item @yield('provinsi')" href="{{route('master.provinsi.index')}}">Provinsi</a>
                    <a class="collapse-item @yield('kabupaten')" href="{{route('master.kabupaten.index')}}">Kabupaten</a>
                    <a class="collapse-item @yield('kecamatan')" href="{{route('master.kecamatan.index')}}">Kecamatan</a> --}}
                    {{-- <a class="collapse-item @yield('desa')" href="{{route('master.desa.index')}}">Desa</a> --}}
                    {{-- <a class="collapse-item @yield('shipper')" href="{{route('master.shipper.index')}}">Shipper</a> --}}
                    <a class="collapse-item @yield('karya')" href="{{route('master.karya.index')}}">Seniman</a>
                    <a class="collapse-item @yield('kelengkapan')" href="{{route('master.kelengkapan.index')}}">Kelengkapan Karya</a>
                    
                </div>
            </div>
        </li>
        <!-- Nav Item - Utilities Collapse Menu -->
        
          <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBlog"
                aria-expanded="true" aria-controls="collapseBlog">
                <i class="fas fa-book"></i>
                <span>Blog</span>
            </a>
            <div id="collapseBlog" class="collapse @yield('collapseBlog')" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item @yield('addblog')" href="{{route('admin.blogs.create')}}">Add Blogs</a>
                    <a class="collapse-item @yield('blogs')" href="{{route('admin.blogs.index')}}">All Blogs</a>
                    <a class="collapse-item @yield('kategoriblog')" href="{{route('master.kategori.index',['cat_type'=>'blog'])}}">Categories</a>
                    {{-- <a class="collapse-item @yield('tags')" href="{{route('master.tags.index')}}">Tags</a> --}}
                    
                </div>
            </div>
        </li>
        <li class="nav-item @yield('post')">
            <a class="nav-link collapsed" href="{{route('admin.posts.index')}}">
                <i class="fas fa-list"></i>
                <span>Pages</span>
            </a>
        </li>
        <li class="nav-item @yield('slider')">
            <a class="nav-link collapsed" href="{{route('master.sliders.index')}}">
                <i class="fas fa-image"></i>
                <span>Sliders</span>
            </a>
        </li>
        <hr class="sidebar-divider">
        <li class="nav-item @yield('post')">
            <a class="nav-link collapsed" href="{{route('admin.daftar-penawaran.index')}}">
                <i class="fas fa-cart-plus"></i>
                <span>Daftar Penawaran</span>
            </a>
        </li>
        <li class="nav-item @yield('slider')">
            <a class="nav-link collapsed" href="{{route('admin.daftar-pemenang.index')}}">
                <i class="fas fa-users"></i>
                <span>Pemenang Lelang</span>
            </a>
        </li>
        <!-- Divider -->
        <hr class="sidebar-divider">
        <!-- Nav Item - Pages Collapse Menu -->
        {{-- <li class="nav-item">
            <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="true"
                aria-controls="collapsePages">
                <i class="fas fa-exchange-alt"></i>
                <span>Transaksi</span>
            </a>
            <div id="collapsePages" class="collapse @yield('collapseTransaksi')" aria-labelledby="headingPages"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item @yield('masuk')" href="{{route('transaksi.masuk.index')}}">Barang Masuk</a>
                    <a class="collapse-item @yield('keluar')" href="{{route('transaksi.keluar.index')}}">Barang Keluar</a>
                </div>
            </div>
        </li> --}}
        <!-- Nav Item - Pages Collapse Menu -->
        {{-- <li class="nav-item">
            <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseReport" aria-expanded="true"
                aria-controls="collapseReport">
                <i class="fas fa-fw fa-print"></i>
                <span>Laporan</span>
            </a>
            <div id="collapseReport" class="collapse @yield('collapseLaporan')" aria-labelledby="headingPages"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                   {{--  <a class="collapse-item @yield('lapstok')" href="{{route('report.stok')}}">Stok</a>
                    <a class="collapse-item @yield('lapmasuk')" href="{{url('/admin/laporan/masuk')}}">Barang Masuk</a>
                    <a class="collapse-item @yield('lapkeluar')" href="{{url('/admin/laporan/keluar')}}">Barang Keluar</a> --}}
                {{-- </div>
            </div>
        </li> --}}
        @if(Auth::user()->access == 'admin')
         <!-- Nav Item - Utilities Collapse Menu -->
        <li class="nav-item @yield('setting')">
            <a class="nav-link collapsed" href="{{route('setting.data')}}">
                <i class="fas fa-fw fa-cog"></i>
                <span>Setting</span>
            </a>
        </li>
        <li class="nav-item @yield('social')">
            <a class="nav-link collapsed" href="{{route('setting.social')}}">
                <i class="fas fa-fw fa-share-alt"></i>
                <span>Social</span>
            </a>
        </li>
        
        <li class="nav-item @yield('user')">
            <a class="nav-link collapsed" href="{{route('admin.user.index')}}">
                <i class="fas fa-fw fa-users"></i>
                <span>Users</span>
            </a>
        </li>
        @endif
    </ul>