<header id="header" class="sticky-top sticky-box">
    <div class="container-fluid bg-white border-bottom px-4">
        <div class="row align-items-center">
            <div class="col-md-2 m-auto logo-mobile">
                <div class="logo">
                    <a href="{{route('home')}}">
                        <img src="{{asset('uploads/logos/'.$setting->logo)}}">
                    </a>
                </div>
            </div>
            <div class="col-md-7 m-auto">
                <div class="d-flex justify-content-evenly align-items-center menu-mobile">
                    <a href="{{route('home')}}" class="text-decoration-none text-dark px-2 fw-500 @yield('home')">BERANDA</a>
                    <a href="{{route('lelang')}}" class="text-decoration-none text-dark px-2 fw-500 @yield('lelang')">LELANG</a>
                    <a href="{{route('galeri.kami')}}" class="text-decoration-none text-dark px-2 fw-500 @yield('galeri-kami')">GALERI KAMI</a>
                    <a href="{{route('blogs')}}" class="text-decoration-none text-dark px-2 fw-500 @yield('blogs')">BLOG</a>
                </div>
            </div>
            <div class="col-md-3 d-flex align-items-center justify-content-end">
                <form action="{{route('web.search')}}" method="GET" class="flex-grow-1 me-2">
                    <div class="input-group my-3 search-input">
                        <input type="text" class="form-control search-input-none" placeholder="Search here" aria-label="Recipient's username" aria-describedby="basic-addon2" name="q">
                        <button class="search-btn" type="submit">
                            <i class="fa fa-search text-dark" style="padding:10px"></i>
                        </button>
                    </div>
                </form>
                <!-- Menu kanan -->
                <div class="d-flex align-items-center">
                    <a href="#" class="text-decoration-none text-dark px-2">TENTANG</a>
                    @guest
                        <a href="{{route('login')}}" class="text-decoration-none text-dark px-2 @yield('login')">MASUK</a>
                    @else
                        @if(Auth::user()->access == 'admin')
                            <a href="{{route('admin.dashboard')}}" class="text-decoration-none text-dark px-2">{{strtoupper(Auth::user()->name)}}</a>
                        @else
                            <a href="{{route('account.dashboard')}}" class="text-decoration-none text-dark px-2">{{strtoupper(Auth::user()->name)}}</a>
                        @endif
                        <a class="text-decoration-none text-dark px-2" href="{{ route('logout') }}" 
                            onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
                            <i class="fa fa-sign-out-alt"></i>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</header>