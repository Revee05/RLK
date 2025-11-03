@extends('web.partials.layout')
@section('css')
<style type="text/css">
      .blog-figure-detail {
        position: relative;
        overflow: hidden;
        height:450px;
        width: 100%;
    }
    .blog-figure-detail img{
        object-fit: cover;
        object-position: center;
        height:100%;
        width: 100%;
    }
</style>
@endsection
@section('content')
<section class="py-3">
  <div class="container">
      <div class="row mt-2">
          <div class="col-md-12 text-danger">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb justify-content-center text-mb">
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Home</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Blog</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{$blog->title}}</li>
              </ol>
            </nav>
          </div>
        </div>
        <div class="row justify-content-center">
          <div class="col-md-9">
            <h3 class="text-center title-detail-mobile">{{$blog->title}}</h3>
            <div class="d-flex justify-content-around justify-beetween">
               <div class="d-block text-mb">{{ucwords($blog->author->name)}}</div>
                <div class="d-block text-mb">{{$blog->date_indo}}</div>
            </div>
            <div class="blog-figure-detail">
                <img src="{{asset('uploads/blogs/'.$blog->image)}}">
            </div>
            <div class="single-desc py-3">{!!$blog->body!!}</div>
          </div>
        </div>
  </div>
</section>
@endsection
