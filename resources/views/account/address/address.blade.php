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
                        <a href="{{ route('account.address.create') }}" class="btn btn-danger btn-sm">+ Tambah Alamat</a>
                    </div>
                    <div class="card-body">
                        @if ($userAddress->isEmpty())
                            <div class="address-empty text-center py-4">Belum ada alamat. Tambahkan alamat baru.</div>
                        @else
                            <div class="row g-3">
                                @foreach ($userAddress as $ua)
                                    <div class="col-12">
                                        <div class="address-item p-3">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <div class="d-flex align-items-center mb-2">
                                                        <strong class="me-2">{{ $ua->name }}</strong>
                                                        <span
                                                            class="address-badge badge bg-light border text-dark">{{ ucwords($ua->label_address) }}</span>
                                                    </div>
                                                    <div class="text-muted small">{{ $ua->phone }}</div>
                                                    <div class="mt-2">{{ $ua->address }},
                                                        {{ $ua->provinsi->nama_provinsi }},
                                                        {{ $ua->kabupaten->nama_kabupaten }},
                                                        {{ $ua->kecamatan->nama_kecamatan }}</div>
                                                </div>
                                                <div class="address-actions text-end">
                                                    <a href="{{ route('account.address.edit', $ua->id) }}"
                                                        class="btn btn-outline-primary btn-sm mb-2">Ubah</a>
                                                    <form action="{{ route('account.address.destroy', [$ua->id]) }}"
                                                        method="post" class="d-inline-block">
                                                        @method('delete')
                                                        @csrf
                                                        <button onclick="return confirm('Hapus alamat ini?')" type="submit"
                                                            class="btn btn-outline-danger btn-sm">Hapus</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript"></script>
@endsection
