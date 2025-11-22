<link href="{{ asset('css/header.css') }}" rel="stylesheet">

<header id="header" class="sticky-top">
    <div class="header-nav container-fluid">
        <!-- Logo -->
        <div class="logo-area">
            <a href="{{ route('home') }}">
                <img src="{{ asset('uploads/logos/'.$setting->logo) }}" class="logo-img"
                    alt="Logo Rasanya Lelang Karya">
            </a>
        </div>
        <!-- Menu -->
        <nav class="menu-area">
            <a href="{{ route('home') }}" class="@yield('home')">Beranda</a>
            
            <div class="dropdown-menu-nav">
                <a href="#" class="dropdown-toggle @yield('lelang')" id="koleksiKaryaDropdown">
                    Koleksi Karya <span class="fa fa-caret-down caret-icon"></span>
                </a>
                <div class="dropdown-menu" id="koleksiKaryaDropdownMenu">
                    <a class="dropdown-item" href="{{ route('lelang') }}">All Auction Products</a>
                    <a class="dropdown-item" href="{{ route('all-other-product') }}">Other Products</a>
                </div>
            </div>

            <div class="dropdown-menu-nav">
                <a href="#" class="dropdown-toggle" id="tentangDropdown">
                    Tentang <span class="fa fa-caret-down caret-icon"></span>
                </a>
                <div class="dropdown-menu" id="tentangDropdownMenu">
                    <a class="dropdown-item" href="{{ route('galeri.kami') }}">Perusahaan</a>
                    <a class="dropdown-item" href="#">Tim</a>
                </div>
            </div>

            <a>Seniman</a>

            <div class="dropdown-menu-nav">
                <a href="#" class="dropdown-toggle" id="panduanDropdown">
                    Panduan <span class="fa fa-caret-down caret-icon"></span>
                </a>
                <div class="dropdown-menu" id="panduanDropdownMenu">
                    <a class="dropdown-item" href="#">Panduan Beli</a>
                    <a class="dropdown-item" href="#">Panduan Jual</a>
                </div>
            </div>        
        </nav>
        <!-- Action & Hamburger (sejajar kanan) -->
        <div class="header-action">
            @guest
            <a href="{{ route('login') }}" class="btn-login @yield('login')">Masuk</a>
            @else
            <div class="profile-dropdown">
                <a href="#" class="profile-icon" id="profileDropdown" title="Profil">
                    <i class="fa fa-user-circle"></i>
                </a>
                <div class="dropdown-menu" id="profileDropdownMenu">
                    @if(Auth::user()->access == 'admin')
                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">Dashboard</a>
                    @else
                    <a class="dropdown-item" href="{{ route('account.dashboard') }}">Profile</a>
                    @endif
                    <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Log Out
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
            @endguest
            <a href="{{ url('/cart') }}">
                <img src="{{ asset('assets/img/shopping-cart.svg') }}" alt="Cart" class="cart-svg" style="width:28px; height:28px; vertical-align:middle;">
            </a>
            <!-- Hamburger Toggle -->
            <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</header>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Hamburger menu
    var toggle = document.getElementById('menuToggle');
    var menu = document.querySelector('.menu-area');
    if (toggle && menu) {
        toggle.addEventListener('click', function() {
            menu.classList.toggle('active');
            toggle.classList.toggle('active');
        });
    }

    // Profile dropdown
    var profileBtn = document.getElementById('profileDropdown');
    var profileMenu = document.getElementById('profileDropdownMenu');
    if (profileBtn && profileMenu) {
        profileBtn.addEventListener('click', function(e) {
            e.preventDefault();
            profileMenu.classList.toggle('show');
        });
        document.addEventListener('click', function(e) {
            if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                profileMenu.classList.remove('show');
            }
        });
    }
    // Helper for dropdown menu with caret animation
    function setupDropdown(btnId, menuId) {
        var btn = document.getElementById(btnId);
        var menu = document.getElementById(menuId);
        var parent = btn ? btn.closest('.dropdown-menu-nav') : null;
        if (btn && menu && parent) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                menu.classList.toggle('show');
                parent.classList.toggle('open');
            });
            document.addEventListener('click', function(e) {
                if (!btn.contains(e.target) && !menu.contains(e.target)) {
                    menu.classList.remove('show');
                    parent.classList.remove('open');
                }
            });
        }
    }

    // Tentang & Panduan dropdown
    setupDropdown('tentangDropdown', 'tentangDropdownMenu');
    setupDropdown('panduanDropdown', 'panduanDropdownMenu');
    setupDropdown('koleksiKaryaDropdown', 'koleksiKaryaDropdownMenu');
});
</script>