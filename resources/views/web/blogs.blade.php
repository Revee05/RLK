@extends('web.partials.layout')
@section('blogs','aktiv')
@section('css')
<style type="text/css">
    .blog-figure {
        position: relative;
        overflow: hidden;
        height: 220px;
        width: 100%;
        border-radius: 12px;
    }

    .blog-figure img {
        object-fit: cover;
        object-position: center;
        height: 100%;
        width: 100%;
        transition: transform 0.3s ease;
    }
 
    .blog-figure:hover img {
        transform: scale(1.05);
    }

    .blog-search-form input::placeholder {
        color: #aaa;
    }

    .blog-search-form .btn-outline-secondary:hover {
        background-color: #f0f0f0;
    }

    .blog-search-form select {
        cursor: pointer;
    }

    .blog-card {
        border: none;
        transition: all 0.3s ease;
        border-radius: 16px;
    }

    .blog-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .blog-title {
        font-weight: 600;
        font-size: 15px;
        color: #000;
    }

    .blog-date {
        color: #888;
        font-size: 13px;
    }

    @media (max-width: 768px) {
        .blog-figure {
            height: 180px;
        }
    }
    .blog-figure {
        position: relative;
        overflow: hidden;
        height: 220px; /* lebih kecil dari sebelumnya */
        width: 100%;
        border-radius: 16px;
    }
    .blog-figure img {
        object-fit: cover;
        object-position: center;
        width: 100%;
        height: 100%;
        transition: transform 0.3s ease;
    }
    .blog-figure:hover img {
        transform: scale(1.05);
    }
    .card-body h6 {
        font-size: 1rem;
    }
    .card-body small {
        font-size: 0.875rem;
    }
    .container {
        margin-top: 40px; /* jarak dari atas */
    }
</style>
@endsection 
@section('content')
<section>
    <div class="container">
        {{-- Judul dan Form Pencarian selalu tampil --}}
        <div class="row py-2 justify-content-md-center pb-5 mt-3">
            <div class="text-center">
                <h2 class="mb-4">Blogs</h2>
                <form action="{{ route('blogs') }}" method="GET" 
                    class="d-flex align-items-center justify-content-center gap-2 flex-wrap blog-search-form">
                    <div class="position-relative flex-grow-1" style="max-width:600px;">
                        <input 
                            type="text" 
                            name="search" 
                            class="form-control rounded-pill ps-4 pe-5 shadow-sm border-secondary-subtle"
                            placeholder="Search Art Project"
                            value="{{ request('search') }}"
                        />
                        <button type="submit" 
                                class="btn position-absolute end-0 top-50 translate-middle-y text-secondary">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                    <select name="filter" class="form-select rounded-pill border-secondary-subtle shadow-sm" style="width: 160px;" onchange="this.form.submit()">
                        <option value="">Filter</option>
                        <option value="seni" {{ request('filter') == 'seni' ? 'selected' : '' }}>Seni</option>
                        <option value="lelang" {{ request('filter') == 'lelang' ? 'selected' : '' }}>Lelang</option>
                        <option value="lifestyle" {{ request('filter') == 'lifestyle' ? 'selected' : '' }}>Lifestyle</option>
                        
                        {{-- <option value="publikasi" {{ request('filter') == 'publikasi' ? 'selected' : '' }}>Publikasi</option> --}}
                        {{-- <option value="pendanaan" {{ request('filter') == 'pendanaan' ? 'selected' : '' }}>Pendanaan</option> --}}
                        {{-- <option value="lomba" {{ request('filter') == 'lomba' ? 'selected' : '' }}>Lomba</option> --}}
                        
                    </select>
                    <select name="sort" class="form-select rounded-pill border-secondary-subtle shadow-sm" 
                            style="width: 160px;" onchange="this.form.submit()">
                        <option value="">Sort By</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                        <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Judul A-Z</option>
                        <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Judul Z-A</option>
                        <option value="author_asc" {{ request('sort') == 'author_asc' ? 'selected' : '' }}>Author A-Z</option>
                        <option value="author_desc" {{ request('sort') == 'author_desc' ? 'selected' : '' }}>Author Z-A</option>
                    </select>
                </form>
            </div>

            {{-- Bagian daftar blog --}}
            <div class="col-md-12 scrolling-pagination mt-5">
                @if(isset($blogs) && $blogs->count() > 0)
                    <div class="row g-4">
                        @foreach($blogs as $blog)
                        <div class="col-md-4 col-sm-6 col-12">
                            <a href="{{ route('web.blog.detail', $blog->slug) }}" class="text-decoration-none text-dark">
                                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                                    <div class="blog-figure">
                                        <img src="{{ asset('uploads/blogs/'.$blog->image) }}" alt="{{ $blog->title }}">
                                    </div>
                                    <div class="card-body text-center">
                                        <h6 class="fw-semibold mt-2 mb-1">{{ $blog->title }}</h6>
                                        <small class="text-muted d-block mb-2">{{ $blog->date_indo }}</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        {{ $blogs->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <h5 class="text-muted">Belum ada blog yang tersedia untuk kategori atau pencarian ini.</h5>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
@section('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="{{asset('theme/owlcarousel/owl.carousel.min.js')}}"></script>
    <script type="text/javascript">
    $(document).ready(function(){
 
        $('ul.pagination').hide();
        $(function() {
            $('.scrolling-pagination').jscroll({
                autoTrigger: true,
                padding: 0,
                nextSelector: '.pagination li.active + li a',
                contentSelector: 'div.scrolling-pagination',
                callback: function() {
                    $('ul.pagination').remove();
                }
            });
        });
    })
    </script>
@endsection