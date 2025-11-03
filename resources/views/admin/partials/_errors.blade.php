@if ($errors->any())
<div class="alert alert-secondary mt-3 rounded-0" role="alert">
    <div class="alert-text">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif