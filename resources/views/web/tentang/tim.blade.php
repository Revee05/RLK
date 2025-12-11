@extends('web.partials.layout')
@section('content')
    <div class="container" style="max-width:1200px; margin-top:40px; margin-bottom:80px;">
        <div class="row">
            @include('web.tentang.sidebar_tentang')

            <!-- Right content: form -->
            <div class="col-md-9">
                <div class="card content-border">
                    <div class="card-head border-bottom border-darkblue align-baseline ps-4">
                        <h3 class="mb-0 fw-bolder align-bottom">Tim</h3>
                    </div>
                    <div class="card-body ps-4">
                        Tim
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
