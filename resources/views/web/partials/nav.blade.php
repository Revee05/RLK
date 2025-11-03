<nav class="navbar navbar-expand-lg navbar-light ok-navbar" id="navigation">
    <div class="container">{{--
        <a class="navbar-brand" href="#!">Start Bootstrap</a> --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link ok-nav-link dropdown-toggle" style="padding-left: 0px;" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">CATEGORIES</a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        @foreach($kategori as $kat)
                        <li><a class="dropdown-item" href="{{route('products.category',$kat->slug)}}">{{ucwords($kat->name)}}</a></li>
                        @endforeach
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link ok-nav-link" href="{{route('web.page',['slug'=>'about-us'])}}">ABOUT US</a></li>
                {{-- <li class="nav-item"><a class="nav-link ok-nav-link" href="#!">BLOG</a></li> --}}
                {{-- <li class="nav-item"><a class="nav-link ok-nav-link" href="{{route('web.page',['slug'=>'help'])}}">BANTUAN</a></li> --}}
            </ul>
            {{-- <form class="d-flex">
                <button class="btn btn-outline-dark" type="submit">
                <i class="bi-cart-fill me-1"></i>
                Cart
                <span class="badge bg-dark text-white ms-1 rounded-pill">0</span>
                </button>
            </form> --}}
        </div>
    </div>
</nav>