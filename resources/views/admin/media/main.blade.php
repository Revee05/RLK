@extends('admin.partials._layout')
{{-- Active menu header blade --}}
@section('media','active')
@section('css')
<style type="text/css">
  .nav-pills > .nav-item > .nav-link {
    height: 2rem !important;
    display: flex;
    align-items: center;
}
</style>
@endsection
@section('content')
<div class="wrapper">
	<div class="container">
		<div class="row">
			<div class="col-lg-12  col-sm-12">
            <iframe src="/filemanager?type={{$type}}" style="width: 100%; height: 750px; overflow: hidden; border: none;"></iframe>
        </div>
		</div>
	</div>
</div>
@endsection