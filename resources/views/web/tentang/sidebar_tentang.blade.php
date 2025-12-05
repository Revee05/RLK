<!-- Left sidebar -->
<div class="col-md-3">
    <div class="list-group mb-4 text-center">
        <a href="{{ route('perusahaan') }}"
            class="list-group-item list-group-item-action border-nav-top py-2 {{ request()->routeIs('perusahaan') ? 'active' : '' }}"
            aria-current="true">
            Perusahaan
        </a>
        <a href="{{ route('tim') }}"
            class="list-group-item list-group-item-action border-nav-bottom py-2 {{ request()->routeIs('tim') ? 'active' : '' }}"
            aria-current="true">
            Tim Kami</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</div>
