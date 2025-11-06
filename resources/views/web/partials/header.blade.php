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
                @if(Auth::user()->access == 'admin')
                    <a href="{{route('admin.dashboard')}}" class="btn-login">{{strtoupper(Auth::user()->name)}}</a>
                @else
                    <a href="{{route('account.dashboard')}}" class="btn-login">{{strtoupper(Auth::user()->name)}}</a>
                @endif
                <a class="btn-login" href="{{ route('logout') }}" 
                    onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();">
                    <i class="fa fa-sign-out-alt"></i>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
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
    var toggle = document.getElementById('menuToggle');
    var menu = document.querySelector('.menu-area');
    if(toggle && menu){
        toggle.addEventListener('click', function() {
            menu.classList.toggle('active');
            toggle.classList.toggle('active'); // untuk animasi hamburger
        });
    }
});
</script>
