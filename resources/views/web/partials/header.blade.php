<link href="{{ asset('css/header.css') }}" rel="stylesheet">
<header id="header" class="sticky-top">
    <div class="header-nav container-fluid">
        <!-- Logo -->
        <div class="logo-area">
            <a href="{{route('home')}}">
                <img src="{{asset('uploads/logos/'.$setting->logo)}}" class="logo-img" alt="Logo Rasanya Lelang Karya">
            </a>
        </div>
        <!-- Menu -->
        <nav class="menu-area">
            <a href="{{route('home')}}" class="@yield('home')">Beranda</a>
            <a href="{{route('lelang')}}" class="@yield('lelang')">Lelang</a>
            <a>Tentang</a>
            <a>Seniman</a>
            <a>Panduan</a>
        </nav>
        <!-- Action & Hamburger (sejajar kanan) -->
        <div class="header-action">
            @guest
                <a href="{{route('login')}}" class="btn-login @yield('login')">Masuk</a>
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
            <a><i class="fa fa-shopping-cart cart-icon"></i></a>
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
    if(toggle && menu){
        toggle.addEventListener('click', function() {
            menu.classList.toggle('active');
            toggle.classList.toggle('active');
        });
    }

    // Profile dropdown
    var profileBtn = document.getElementById('profileDropdown');
    var profileMenu = document.getElementById('profileDropdownMenu');
    if(profileBtn && profileMenu){
        profileBtn.addEventListener('click', function(e){
            e.preventDefault();
            profileMenu.style.display = (profileMenu.style.display === 'block') ? 'none' : 'block';
        });
        // Klik di luar dropdown untuk menutup
        document.addEventListener('click', function(e){
            if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                profileMenu.style.display = 'none';
            }
        });
    }
});
</script>
