<link href="{{ asset('css/header.css') }}" rel="stylesheet">
<header id="header" class="sticky-top">
    <div class="header-nav container-fluid">
        <!-- Logo -->
        <div class="logo-area">
            <a href="{{ route('home') }}">
                <img src="{{ asset('uploads/logos/' . $setting->logo) }}" class="logo-img" alt="Logo Rasanya Lelang Karya">
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
                    <a class="dropdown-item" href="{{ route('all-other-product') }}">Merchandise Products</a>
                </div>
            </div>

            {{-- <div class="dropdown-menu-nav">
                <a href="#" class="dropdown-toggle" id="tentangDropdown">
                    Tentang <span class="fa fa-caret-down caret-icon"></span>
                </a>
                <div class="dropdown-menu" id="tentangDropdownMenu">
                    <a class="dropdown-item" href="{{ route('perusahaan') }}">Perusahaan</a>
                    <a class="dropdown-item" href="{{ route('tim') }}">Tim</a>
                </div>
            </div> --}}
            <a class="@yield('tentang')" href="{{ route('perusahaan') }}">Tentang</a>
            <a href="{{ route('seniman.index') }}" class="@yield('seniman')">Seniman</a>

            <a href="{{ route('panduan.index') }}" class="@yield('panduan')">Panduan</a>
        </nav>
        <!-- Action & Hamburger (sejajar kanan) -->
        <div class="header-action">
            @guest
                <a href="{{ route('login') }}" class="btn-login @yield('login')">Masuk</a>
            @else
                <div class="profile-dropdown">
                    <a href="#" class="profile-icon" id="profileDropdown" title="Profil">
                        @if (Auth::user()->foto)
                            <img src="{{ asset(Auth::user()->foto) }}" alt="profile picture" class="rounded-circle"
                                style="width:28px; height:28px; object-fit:cover;">
                        @else
                            <i class="fa fa-user-circle"></i>
                        @endif
                    </a>
                    <div class="dropdown-menu" id="profileDropdownMenu">

                        @if (Auth::user()->access == 'admin')
                            <a class="dropdown-item" href="{{ route('admin.dashboard') }}">Dashboard</a>
                            {{-- <a class="dropdown-item" href="{{ route('account.dashboard') }}">Profile</a> --}}
                        @else
                            <a class="dropdown-item" href="{{ route('account.dashboard') }}">Profile</a>
                            <div class="d-md-none"> <!-- show nav list only on mobile -->
                                <a class="dropdown-item" href="{{ route('account.katasandi') }}">Ubah Password</a>
                                <a class="dropdown-item" href="{{ route('account.address.index') }}">Alamat</a>
                                <a class="dropdown-item" href="#">Favorit</a>
                                <a class="dropdown-item" href="{{ route('account.auction_history') }}">Riwayat Lelang</a>
                                <a class="dropdown-item" href="{{ route('account.purchase.history') }}">Riwayat
                                    Pembelian</a>
                                <a class="dropdown-item" href="{{ route('account.notifications') }}">Pengaturan
                                    Notifikasi</a>
                            </div>
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
                <img src="{{ asset('assets/img/shopping-cart.svg') }}" alt="Cart" class="cart-svg"
                    style="width:28px; height:28px; vertical-align:middle;">
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
        // Elements
        var toggle = document.getElementById('menuToggle');
        var menu = document.querySelector('.menu-area');
        var profileBtn = document.getElementById('profileDropdown');
        var profileMenu = document.getElementById('profileDropdownMenu');

        // Helpers to close menus
        function closeProfileMenu() {
            if (profileMenu && profileMenu.classList.contains('show')) profileMenu.classList.remove('show');
        }
        function closeMobileMenu() {
            if (menu && menu.classList.contains('active')) {
                menu.classList.remove('active');
                if (toggle) toggle.classList.remove('active');
            }
        }
        function closeOtherDropdowns(exceptMenu) {
            document.querySelectorAll('.menu-area .dropdown-menu').forEach(function(m) {
                if (m !== exceptMenu) {
                    m.classList.remove('show');
                    var p = m.closest('.dropdown-menu-nav');
                    if (p) p.classList.remove('open');
                }
            });
        }

        // Hamburger menu - when opening, close profile and other dropdowns
        if (toggle && menu) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                var opening = !menu.classList.contains('active');
                menu.classList.toggle('active');
                toggle.classList.toggle('active');
                if (opening) {
                    closeProfileMenu();
                    closeOtherDropdowns();
                }
            });
        }

        // Profile dropdown - when opening, close mobile menu and other dropdowns
        if (profileBtn && profileMenu) {
            profileBtn.addEventListener('click', function(e) {
                e.preventDefault();
                var opening = !profileMenu.classList.contains('show');
                profileMenu.classList.toggle('show');
                if (opening) {
                    closeMobileMenu();
                    closeOtherDropdowns();
                }
            });
            document.addEventListener('click', function(e) {
                if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                    profileMenu.classList.remove('show');
                }
            });
        }

        // Helper for other dropdown menus (koleksi, tentang, panduan)
        function setupDropdown(btnId, menuId) {
            var btn = document.getElementById(btnId);
            var menuEl = document.getElementById(menuId);
            var parent = btn ? btn.closest('.dropdown-menu-nav') : null;
            if (btn && menuEl && parent) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var opening = !menuEl.classList.contains('show');
                    menuEl.classList.toggle('show');
                    parent.classList.toggle('open');
                    if (opening) {
                        closeProfileMenu();
                        closeMobileMenu();
                        closeOtherDropdowns(menuEl);
                    }
                });
                document.addEventListener('click', function(e) {
                    if (!btn.contains(e.target) && !menuEl.contains(e.target)) {
                        menuEl.classList.remove('show');
                        parent.classList.remove('open');
                    }
                });
            }
        }

        // Initialize dropdowns
        setupDropdown('tentangDropdown', 'tentangDropdownMenu');
        setupDropdown('panduanDropdown', 'panduanDropdownMenu');
        setupDropdown('koleksiKaryaDropdown', 'koleksiKaryaDropdownMenu');

        // Ensure mobile menu is closed when resizing to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 991) {
                closeMobileMenu();
                closeOtherDropdowns();
            }
        });
    });
</script>
