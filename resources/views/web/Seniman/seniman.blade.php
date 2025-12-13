@extends('web.partials.layout')

{{-- CSS eksternal --}}
@section('css')
<link rel="stylesheet" href="{{ asset('css/seniman/seniman.css') }}">
@endsection



@section('content')
<div class="seniman-main-container">
    {{-- Header Section --}}
    <section class="seniman-page-header">
        <div class="container">
            <h2>Seniman</h2>
            <p>Seniman adalah kreator di balik setiap karya yang ditawarkan di platform ini. Mereka menggabungkan bic, eksperimen, dan inspirasi kreatif untuk menghasilkan karya yang unik dan bernilai seni penuh ikhlas.</p>
        </div>
    </section>

    {{-- Search Bar Section --}}
    <section class="seniman-search-bar">
        <div class="container">
            <form action="{{ route('seniman.index') }}" method="GET" class="seniman-search-form">
                <div class="search-wrapper">
                    <input 
                        type="text" 
                        name="search" 
                        class="search-input"
                        placeholder="Search Seniman"
                        value="{{ request('search') }}"
                    />
                    <button type="submit" class="search-icon-btn">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
                
                <div class="filter-sort-wrapper">
                    <input type="hidden" name="city" id="city-id" value="{{ request('city') }}">
                    @php
                        $selectedCityName = '';
                        if(request('city') && isset($cities)){
                            $found = $cities->firstWhere('id', (int) request('city'));
                            $selectedCityName = $found ? $found->name : '';
                        }
                    @endphp

                    <div class="pill-group">
                        <div class="filter-dropdown">
                            <button type="button" class="pill pill-filter-toggle" id="filter-toggle">
                                <span class="pill-icon"><i class="fa fa-filter"></i></span>
                                <span class="pill-text">Filters</span>
                            </button>

                            <div class="filter-panel" id="filter-panel" aria-hidden="true">
                            <div class="filter-panel-header">
                                <h4>Filters</h4>
                                <a href="#" id="filter-reset" class="filter-reset">Reset</a>
                            </div>

                            <div class="filter-panel-body">
                                <label class="filter-available"><input type="checkbox" name="available" id="filter-available" {{ request('available') ? 'checked' : '' }}> Item yang tersedia</label>

                                <div class="filter-section">
                                    <div class="filter-title">Kota</div>

                                    <div class="custom-select" id="city-custom-select">
                                        <div class="custom-select-trigger" id="custom-trigger">
                                            <input type="text" id="custom-search" class="custom-search" placeholder="Cari nama kota..." value="{{ $selectedCityName ?: '' }}" autocomplete="off" />
                                            <span class="caret">▾</span>
                                        </div>
                                        <div class="custom-options" id="custom-options">
                                            @if(isset($cities))
                                                @foreach($cities as $city)
                                                    <div class="custom-option" data-id="{{ $city->id }}" data-value="{{ $city->name }}">{{ $city->name }}</div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <label class="pill pill-sort">
                            <span class="pill-icon"><i class="fa fa-sort"></i></span>
                            <select name="sort" class="pill-select" onchange="this.form.submit()">
                                <option value="" {{ request('sort') == '' ? 'selected' : '' }}>Terbaru</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama A-Z</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama Z-A</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                            </select>
                            <span class="pill-caret"><i class="fa fa-caret-down"></i></span>
                        </label>
                    </div>
                </div>
            </form>
        </div>
    </section>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const filterToggle = document.getElementById('filter-toggle');
        const panel = document.getElementById('filter-panel');
        const customTrigger = document.getElementById('custom-trigger');
        const customOptions = document.getElementById('custom-options');
        const cityId = document.getElementById('city-id');
        const filterReset = document.getElementById('filter-reset');
        const availCheckbox = document.getElementById('filter-available');
        const form = document.querySelector('form.seniman-search-form');

        if(filterToggle && panel){
            filterToggle.addEventListener('click', function(e){
                const open = panel.getAttribute('aria-hidden') === 'false';
                panel.setAttribute('aria-hidden', open ? 'true' : 'false');
            });

            document.addEventListener('click', function(e){
                if(!panel) return;
                if(panel.getAttribute('aria-hidden') === 'false'){
                    if(!panel.contains(e.target) && e.target !== filterToggle && !filterToggle.contains(e.target)){
                        panel.setAttribute('aria-hidden', 'true');
                    }
                }
            });
        }

        if(customTrigger && customOptions){
            const customSearch = document.getElementById('custom-search');

            function openOptions(){ customOptions.classList.add('open'); }
            function closeOptions(){ customOptions.classList.remove('open'); }

            // clicking the trigger caret or container should toggle, but clicking the input shouldn't toggle
            customTrigger.addEventListener('click', function(e){
                if(e.target && e.target.tagName && e.target.tagName.toLowerCase() === 'input') return;
                customOptions.classList.toggle('open');
                if(customSearch) customSearch.focus();
            });

            if(customSearch){
                customSearch.addEventListener('focus', openOptions);
                customSearch.addEventListener('input', function(){
                    const q = (this.value || '').trim().toLowerCase();
                    const opts = Array.from(customOptions.querySelectorAll('.custom-option'));
                    let firstVisible = null;
                    opts.forEach(function(o){
                        const txt = (o.textContent || '').trim().toLowerCase();
                        const match = q === '' || txt.indexOf(q) !== -1;
                        o.style.display = match ? '' : 'none';
                        if(match && !firstVisible) firstVisible = o;
                    });
                    // if user presses Enter later, we will select firstVisible
                });

                customSearch.addEventListener('keydown', function(ev){
                    if(ev.key === 'Enter'){
                        ev.preventDefault();
                        const opts = Array.from(customOptions.querySelectorAll('.custom-option')).filter(o => o.style.display !== 'none');
                        if(opts.length){ opts[0].click(); }
                    }
                });
            }

            customOptions.querySelectorAll('.custom-option').forEach(function(opt){
                opt.addEventListener('click', function(){
                    const val = this.dataset.value || this.textContent.trim();
                    const id = this.dataset.id || '';
                    if(cityId) cityId.value = id;
                    if(customSearch) customSearch.value = val;
                    closeOptions();
                    if(form){
                        form.submit();
                    }
                });
            });
        }

        if(filterReset){
            filterReset.addEventListener('click', function(e){
                e.preventDefault();
                if(cityId) cityId.value = '';
                if(availCheckbox) availCheckbox.checked = false;
                const sort = document.querySelector('select[name="sort"]');
                if(sort) sort.value = '';
                if(form) form.submit();
            });
        }
    });
    </script>
    @endpush

    {{-- Seniman Grid --}}
    <section class="pb-5">
        <div class="container">
            @if($senimans->count() > 0)
                <div class="row g-3">
                    @foreach($senimans as $seniman)
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <a href="{{ route('seniman.detail', $seniman->slug) }}" class="text-decoration-none">
                                <div class="seniman-card">
                                    <div class="seniman-image-wrapper">
                                        @if($seniman->image)
                                            <img src="{{ asset('uploads/senimans/' . $seniman->image) }}" 
                                                 alt="{{ $seniman->name }}" 
                                                 class="seniman-image">
                                        @else
                                            <div class="seniman-image d-flex align-items-center justify-content-center bg-light">
                                                <i class="fas fa-user fa-3x text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="seniman-info">
                                        <div class="seniman-name">
                                            {{ $seniman->name }}
                                            @if($seniman->julukan)
                                                <span style="font-size: 0.85em; color: #888; font-style: italic; font-weight: normal;">
                                                    "{{ $seniman->julukan }}"
                                                </span>
                                            @endif
                                        </div>
                                        <div class="seniman-location">
                                            {{ Str::contains($seniman->address, ',') ? trim(Str::afterLast($seniman->address, ',')) : $seniman->address }}
                                        </div>
                                        <div class="seniman-bio bio-clamp">{!! $seniman->bio !!}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
                
                {{-- Pagination --}}
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="d-flex justify-content-center">
                            {{ $senimans->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-user-friends fa-4x text-muted mb-3"></i>
                    <h4>Seniman tidak ditemukan</h4>
                    <p class="text-muted">Coba ubah kata kunci pencarian Anda</p>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
