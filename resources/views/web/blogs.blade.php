@extends('web.partials.layout')
@section('blogs','aktiv')
@section('css')
<style type="text/css">
      .blog-figure {
        position: relative;
        overflow: hidden;
        height:385px;
        width: 100%;
    }
    .blog-figure img{
        object-fit: cover;
        object-position: center;
        height:100%;
        width: 100%;
    }
</style>
@endsection
@section('content')
<section>
@if(isset($blogs) && !empty($blogs) && count($blogs) > 0)
    <section>
        <div class="container">
            <div class="row py-2 justify-content-md-center pb-5 mt-3">
                <div class="text-center">
                    <h2>Blogs</h2>
                </div>
                <div class="col-md-12 scrolling-pagination">
                    @foreach($blogs as $blog)
                    <div class="d-flex justify-content-between border my-3 flex-row-reverse">
                        <div class="d-block w-50 p-4 p-4-mobile">
                          <div class="d-block text-left text-mb">{{ucwords($blog->author->name)}}</div>
                          <div class="d-block text-left text-mb">{{$blog->date_indo}}</div>
                          <a href="{{route('web.blog.detail',$blog->slug)}}" class="d-block text-decoration-none fw-500 text-dark pt-5 fs-5 blog-title-mobile">
                              {{$blog->title}}
                          </a>
                          <div class="py-3 desc-mobile">
                              {{-- {!!$blog->body!!} --}}
                              {!!Str::limit($blog->body, 250)!!}
                          </div>
                        </div>
                        <div class="d-block w-50">
                            <div class="blog-figure">
                                <img src="{{asset('uploads/blogs/'.$blog->image)}}">
                            </div>
                        </div>
                    </div>
                    @endforeach
                    {{$blogs->links()}}
                </div>
            </div>
        </div>
    </section>
@endif

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