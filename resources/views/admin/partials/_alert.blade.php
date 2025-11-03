@if(session()->has('alert'))
<div class="alert alert-warning rounded-0">
    {{ session()->get('alert') }}
</div>
@endif