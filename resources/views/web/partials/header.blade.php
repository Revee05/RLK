<header id="header" class="sticky-top sticky-box">
    <div class="container-fluid bg-white py-md-1 border-bottom px-4">
        <div class="row">
            <div class="col-md-12 text-end">
                <a href="#" class="text-decoration-none text-dark">TENTANG</a>
                @guest
                    <a href="{{route('login')}}" class="text-decoration-none text-dark px-2 @yield('login')">MASUK</a>
                @else
                    @if(Auth::user()->access == 'admin')
                    <a href="{{route('admin.dashboard')}}" class="text-decoration-none text-dark">{{strtoupper(Auth::user()->name)}}</a>
                    @else
                    <a href="{{route('account.dashboard')}}" class="text-decoration-none text-dark">{{strtoupper(Auth::user()->name)}}</a>
                    @endif
                    {{-- <span class="separator"> | </span> --}}
                    <a class="text-decoration-none text-dark" href="{{ route('logout') }}" 
                        onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
                        <i class="fa fa-sign-out-alt"></i>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @endif
            </div>
        </div>
    </div>
    <div class="container-fluid bg-white border-bottom px-4">
        <div class="row">
            <div class="col-md-2 m-auto logo-mobile">
                <div class="logo">
                    <a href="{{route('home')}}">
                        {{-- <img src="https://themewagon.github.io/electro/img/logo.png"> --}}
                        <img src="{{asset('uploads/logos/'.$setting->logo)}}">
                        {{-- <img src="{{asset('assets/img/logo-lelang.png')}}"> --}}
                    </a>
                </div>
            </div>
            <div class="col-md-6 m-auto">
                <div class="d-flex justify-content-evenly menu-mobile">
                    <a href="{{route('home')}}" class="text-decoration-none text-dark px-2 fw-500 @yield('home')">BERANDA</a>
                    <a href="{{route('lelang')}}" class="text-decoration-none text-dark px-2 fw-500 @yield('lelang')">LELANG</a>
                    <a href="{{route('galeri.kami')}}" class="text-decoration-none text-dark px-2 fw-500 @yield('galeri-kami')">GALERI KAMI</a>
                    <a href="{{route('blogs')}}" class="text-decoration-none text-dark px-2 fw-500 @yield('blogs')">BLOG</a>
                @guest
                    {{-- <a href="{{route('login')}}" class="text-decoration-none text-white px-2 @yield('login')">MASUK</a> --}}
                @else
                    {{-- @if(Auth::user()->access == 'admin')
                    <a href="{{route('admin.dashboard')}}" class="text-decoration-none text-white">{{strtoupper(Auth::user()->name)}}</a>
                    @else
                    <a href="{{route('account.dashboard')}}" class="text-decoration-none text-white">{{strtoupper(Auth::user()->name)}}</a>
                    @endif
                    <span class="separator"> </span>
                    <a class="text-decoration-none text-white" href="{{ route('logout') }}" 
                        onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
                        <i class="fa fa-sign-out-alt"></i>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form> --}}
                @endif
                </div>
            </div>
            <div class="col-md-4">
                <form action="{{route('web.search')}}" method="GET">
                    <div class="input-group my-3 search-input">
                        <input type="text" class="form-control search-input-none" placeholder="Searh here" aria-label="Recipient's username" aria-describedby="basic-addon2" name="q">
                        <button class="search-btn" type="submit">
                            <i class="fa fa-search text-dark" style="padding:10px"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</header>