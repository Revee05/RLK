@extends('web.partials.layout')

@section('css')
<link rel="stylesheet" href="{{ asset('css/blog/blog-detail.css') }}">
@endsection

@section('content')
<section class="py-3">
  <div class="container blog-container">

    {{-- Breadcrumb 
    <nav arial-label="breadcrumb">
      <ol class="breadcrumb justify-content-left">
        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ url('/blogs') }}">Blog</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $blog->title }}</li>
      </ol>
    </nav> --}}

    {{-- Judul --}}
    <h1 class="blog-title">{{ $blog->title }}</h1>

    {{-- Metadata tanggal & waktu --}}
    <div class="blog-meta">
      <i class="bi bi-calendar-event"></i>
      {{ $blog->created_at->translatedFormat('l, d F Y H:i:s') }}
    </div>

    {{-- Cover --}}
    @if($blog->image)
      <div class="blog-cover">
        <img src="{{ asset('uploads/blogs/'.$blog->image) }}" alt="{{ $blog->title }}">
      </div>
    @endif

    {{-- BODY --}}
    @php
      $blocks = json_decode($blog->body, true);
      if (!is_array($blocks)) {
        $blocks = [];
      }
    @endphp

    <article class="blog-body">
      @foreach($blocks as $block)

        {{-- TEXT --}}
        @if(($block['type'] ?? '') === 'text')
          <div class="blog-text">
            {!! $block['html'] ?? '' !!}
          </div>
        @endif

        {{-- IMAGE --}}
        @if(($block['type'] ?? '') === 'image')
          @php
            $img = DB::table('blog_images')->find($block['image_id'] ?? 0);
          @endphp

          @if($img)
            <figure class="blog-image-inline">
              <img src="{{ asset('uploads/blogs/'.$img->filename) }}">
            </figure>
          @endif
        @endif

      @endforeach
    </article>

    {{-- Penulis --}}
    @if(!empty($blog->author))
    <div class="blog-author">
      <img src="{{ asset('uploads/authors/'.$blog->author->photo ?? 'default.jpg') }}" alt="{{ $blog->author->name }}">
      <div class="author-info">
        <h5>Written by {{ ucwords($blog->author->name) }}</h5>
        <p>{{ $blog->author->bio ?? 'Penulis di platform Rasanya Lelang Karya yang berfokus pada seni dan budaya.' }}</p>
      </div>
    </div>
    @endif

    {{-- Artikel terkait --}}
    @if(!empty($relatedBlogs) && $relatedBlogs->count() > 0)
    <div class="related-posts mt-5">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Artikel atau postingan terkait</h4>
      </div>

      <div class="related-scroll">
        @foreach($relatedBlogs as $rel)
          <div class="related-card">
            <a href="{{ url('blog/'.$rel->slug) }}" class="text-decoration-none text-dark">
              <img src="{{ asset('uploads/blogs/'.$rel->image) }}" alt="{{ $rel->title }}">
              <h6>{{ $rel->title }}</h6>
              <p>{{ $rel->author->name ?? 'Anonim' }}</p>
            </a>
          </div>
        @endforeach

        {{-- Card "See More" di akhir --}}
        <div class="related-card see-more-card">
          <a href="{{ url('/blogs') }}" class="see-more-link">
            <div class="see-more-circle">
              <span>Lihat Lebih<br> Banyak</span>
            </div>
          </a>
        </div>
      </div>

    </div>
    @endif

  </div>
</section>
@endsection
