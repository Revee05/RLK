@extends('web.partials.layout')
@section('content')
<section class="py-3">
  <div class="container">
    
        <div class="row">
          <h3>{{$page->title}}</h3>
            <div class="single-desc">{!!$page->body!!}</div>
        </div>
  </div>
</section>
@endsection
