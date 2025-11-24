@extends('account.partials.layout')
@section('content')
    <div class="container" style="max-width:1200px; margin-top:40px; margin-bottom:80px;">
        <div class="row">
            @include('account.partials.nav_new')

            <div class="col-sm-9">
                <div class="card content-border">
                    <div
                        class="card-head border-bottom border-darkblue ps-4 d-flex align-items-center justify-content-between">
                        <h3 class="mb-0 fw-bolder">Daftar Alamat</h3>
                    </div>
                    <div class="card-body">
                        @if ($userAddress->isEmpty())
                            <div class="address-empty text-center py-4">Belum ada alamat. Tambahkan alamat baru.</div>
                        @else
                            <div class="row g-3">
                                @foreach ($userAddress as $ua)
                                    <div class="col-12">
                                        <div class="address-item p-3 content-border">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div class="mb-1">
                                                        <span
                                                            class="address-badge mb-1">{{ ucwords($ua->label_address) }}</span>
                                                        <div class="d-flex align-items-center">
                                                            <strong class="me-2 mb-0">{{ $ua->name }}</strong>
                                                            <span class="address-divider text-muted">|</span>
                                                            <span class="text-muted small ms-2">{{ $ua->phone }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="address-street text-muted">{{ $ua->address }}</div>
                                                    <div class="address-location text-muted small">
                                                        {{ $ua->provinsi->nama_provinsi }},
                                                        {{ $ua->kabupaten->nama_kabupaten }},
                                                        {{ $ua->kecamatan->nama_kecamatan }}</div>
                                                </div>
                                                <div class="address-actions d-flex align-items-center">
                                                    <a href="{{ route('account.address.edit', $ua->id) }}"
                                                        class="btn-icon icon-edit" aria-label="Ubah alamat">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <form action="{{ route('account.address.destroy', [$ua->id]) }}"
                                                        method="post" class="d-inline-block ms-2">
                                                        @method('delete')
                                                        @csrf
                                                        <button onclick="return confirm('Hapus alamat ini?')" type="submit"
                                                            class="btn-icon icon-delete" aria-label="Hapus alamat">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-3 text-start ps-0">
                            <a href="{{ route('account.address.create') }}" class="btn btn-cyan btn-add-address">
                                <span class="btn-add-icon"><i class="bi bi-plus-circle"></i></span>
                                Tambah Alamat
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript"></script>
@endsection
