<div class="col-md-3 mb-4">
    <div class="panduan-sidebar">
        {{-- Menu 1: Peserta Lelang --}}
        <a href="{{ route('panduan.lelang.peserta') }}" 
           class="nav-link {{ Route::is('panduan.lelang.peserta') ? 'active' : '' }}">
            Panduan Peserta Lelang
        </a>
        
        {{-- Menu 2: Penjualan Karya --}}
        <a href="{{ route('panduan.penjualan.karya') }}" 
           class="nav-link {{ Route::is('panduan.penjualan.karya') ? 'active' : '' }}">
            Panduan Penjualan Karya Lelang
        </a>

        {{-- Menu 3: Pembelian Produk --}}
        <a href="{{ route('panduan.beli') }}" 
           class="nav-link {{ Route::is('panduan.beli') ? 'active' : '' }}">
            Panduan Pembelian Produk
        </a>

        {{-- Menu 4: Penjualan Produk --}}
        <a href="{{ route('panduan.penjualan.produk') }}" 
           class="nav-link {{ Route::is('panduan.penjualan.produk') ? 'active' : '' }}">
            Panduan Penjualan Produk
        </a>
    </div>
</div>