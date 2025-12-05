<div class="col-md-3 mb-4">
    <div class="panduan-sidebar">
        {{-- Menu 1: Peserta Lelang --}}
        <a href="{{ route('panduan.show', 'peserta-lelang') }}"
           class="nav-link {{ request()->is('panduan/peserta-lelang') ? 'active' : '' }}">
            Panduan Peserta Lelang
        </a>
        
        {{-- Menu 2: Penjualan Karya --}}
        <a href="{{ route('panduan.show', 'penjualan-karya-lelang') }}"
           class="nav-link {{ request()->is('panduan/penjualan-karya-lelang') ? 'active' : '' }}">
            Panduan Penjualan Karya Lelang
        </a>

        {{-- Menu 3: Pembelian Produk --}}
        <a href="{{ route('panduan.show', 'pembelian-produk') }}"
           class="nav-link {{ request()->is('panduan/pembelian-produk') ? 'active' : '' }}">
            Panduan Pembelian Produk
        </a>

        {{-- Menu 4: Penjualan Produk --}}
        <a href="{{ route('panduan.show', 'penjualan-produk') }}"
           class="nav-link {{ request()->is('panduan/penjualan-produk') ? 'active' : '' }}">
            Panduan Penjualan Produk
        </a>
    </div>
</div>