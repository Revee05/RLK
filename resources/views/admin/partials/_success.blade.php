@if(session()->has('message'))
<div class="alert alert-info rounded-0">
    {{ session()->get('message') }}
</div>
@endif