@extends('admin.partials._layout')
@section('title','Detail Transaksi')
@section('transaksi','active')
@section('content')
<div class="container-fluid">
    <h1 class="h5 mb-4 text-gray-800">Detail Transaksi</h1>

    <a href="{{ route('admin.transaksi.index') }}" class="btn btn-sm btn-secondary mb-3">&larr; Kembali</a>

    @php
        $model = $order ?? $data ?? null;
        $items = [];
        if ($model) {
            if (is_array($model->items)) {
                $items = $model->items;
            } elseif (!empty($model->items)) {
                $items = json_decode($model->items, true) ?: [];
            }
        }
        function fmtRp($v){ return number_format($v ?? 0,0,',','.'); }

        function formatAddress($m){
            if(!$m) return '-';
            // If relation object exists and has address property
            if(is_object($m->address) && !empty($m->address->address)){
                return $m->address->address;
            }
            $addrAttr = $m->address ?? null;
            if(is_array($addrAttr)){
                if(!empty($addrAttr['address'])) return $addrAttr['address'];
                return implode(', ', array_filter($addrAttr));
            }
            if(is_string($addrAttr) && trim($addrAttr) !== ''){
                $decoded = json_decode($addrAttr, true);
                if(json_last_error() === JSON_ERROR_NONE && is_array($decoded)){
                    if(!empty($decoded['address'])) return $decoded['address'];
                    // try common keys
                    $parts = [];
                    foreach(['label_address','address','provinsi','kabupaten','kecamatan','city','district','postal_code'] as $k){
                        if(!empty($decoded[$k])) $parts[] = $decoded[$k];
                    }
                    if(!empty($decoded['name'])) $parts[] = '('.$decoded['name'].')';
                    return $parts ? implode(', ', $parts) : $addrAttr;
                }
                // plain text address
                return $addrAttr;
            }
            return '-';
        }
    @endphp

    <div class="row mb-3">
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header py-2">
                    <strong>Ringkasan</strong>
                    <span class="float-right text-muted small">{{ $model->created_at ?? '-' }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-2">
                                <small class="text-muted">Invoice</small>
                                <div class="h6 text-primary font-weight-bold mb-0">{{ $model->invoice ?? $model->order_invoice ?? '-' }}</div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Pelanggan</small>
                                <div class="text-dark">{{ optional($model->user)->name ?? $model->name ?? '-' }}</div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Kontak</small>
                                <div class="text-dark">{{ $model->phone ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            @php
                                $rawStatus = strtolower($model->status ?? ($model->payment_status ?? 'pending'));
                                $statusMap = ['pending' => 'badge-warning', 'success' => 'badge-success', 'expired' => 'badge-danger', 'cancelled' => 'badge-danger'];
                                $statusClass = $statusMap[$rawStatus] ?? 'badge-secondary';
                            @endphp
                            <div class="mb-2">
                                <small class="text-muted">Status</small>
                                <div class="mb-0">
                                    @if(method_exists($model,'statusTxt'))
                                        {!! $model->statusTxt !!}
                                    @else
                                        <span class="badge {{ $statusClass }}">{{ ucfirst($rawStatus) }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Metode Pembayaran</small>
                                <div class="text-dark">{{ $model->payment_method ?? '-' }}</div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Alamat</small>
                                <div class="text-dark">{!! nl2br(e(formatAddress($model))) !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header py-2"><strong>Ringkasan Pembayaran</strong></div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr><th class="p-1">Total Ongkir</th><td class="p-1 text-right">Rp {{ fmtRp($model->total_ongkir ?? $model->total_ongkir ?? 0) }}</td></tr>
                        <tr><th class="p-1">Total Tagihan</th><td class="p-1 text-right">Rp {{ fmtRp($model->total_tagihan ?? $model->total_tagihan ?? 0) }}</td></tr>
                        <tr><th class="p-1">Paid At</th><td class="p-1 text-right">{{ $model->paid_at ?? '-' }}</td></tr>
                        <tr><th class="p-1">Note</th><td class="p-1 text-right">{{ $model->note ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header py-2"><strong>Items</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:5%">#</th>
                            <th style="width:10%">Gambar</th>
                            <th>Nama</th>
                            <th style="width:8%">Qty</th>
                            <th style="width:15%">Harga</th>
                            <th style="width:15%">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i = 1; @endphp
                        @forelse($items as $it)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>
                                @if(!empty($it['image']))
                                    <img src="{{ asset($it['image']) }}" alt="" style="width:48px;height:48px;object-fit:cover;border-radius:4px">
                                @else
                                    <img src="{{ asset('assets/images/no-image.png') }}" alt="" style="width:48px;height:48px;object-fit:cover;border-radius:4px">
                                @endif
                            </td>
                            <td>{{ $it['name'] ?? $it['title'] ?? '-' }}</td>
                            <td>{{ $it['qty'] ?? $it['quantity'] ?? 1 }}</td>
                            <td>Rp {{ fmtRp($it['price'] ?? 0) }}</td>
                            <td>Rp {{ fmtRp( ($it['price'] ?? 0) * ($it['qty'] ?? ($it['quantity'] ?? 1)) ) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-3">Tidak ada item.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
