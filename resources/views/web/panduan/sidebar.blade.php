<div class="col-md-3 mb-4">
    <div class="panduan-sidebar">

        @foreach ($semuaPanduan as $item)
            <a href="javascript:void(0)" class="nav-link panduan-item {{ $loop->first ? 'active' : '' }}"
                data-slug="{{ $item->slug }}">
                {{ $item->title }}
            </a>
        @endforeach

    </div>
</div>
