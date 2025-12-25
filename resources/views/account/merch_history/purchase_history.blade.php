@extends('account.partials.layout')
@section('css')
<style>
.purchase-history-container .nav-tabs {
    border-bottom: 1px solid #dee2e6;
}
.purchase-history-container .nav-tabs .nav-item {
    margin-bottom: -1px;
}
.purchase-history-container .nav-tabs .nav-link {
    border: 1px solid transparent;
    border-top-left-radius: .25rem;
    border-top-right-radius: .25rem;
    color: #6c757d;
}
.purchase-history-container .nav-tabs .nav-link.active, 
.purchase-history-container .nav-tabs .nav-link:hover, 
.purchase-history-container .nav-tabs .nav-link:focus {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
    font-weight: 600;
}
.tab-content {
    padding-top: 1rem;
}
</style>
@endsection

@section('content')
<div class="container purchase-history-container">
    <div class="row">
        @include('account.partials.nav_new')

        <div class="col-md-9">
            <div class="card content-border">
                <div class="card-body ps-4 pe-4">
                    <h3 class="mb-4 fw-bolder">Riwayat Pembelian</h3>
                    
                    <!-- Status Filter Tabs -->
                    <ul class="nav nav-tabs" id="statusTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="semua-tab" data-bs-toggle="tab" data-bs-target="#semua" type="button" role="tab" aria-controls="semua" aria-selected="true">Semua Status</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="belum-bayar-tab" data-bs-toggle="tab" data-bs-target="#belum-bayar" type="button" role="tab" aria-controls="belum-bayar" aria-selected="false">Belum Bayar</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="diproses-tab" data-bs-toggle="tab" data-bs-target="#diproses" type="button" role="tab" aria-controls="diproses" aria-selected="false">Diproses</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="selesai-tab" data-bs-toggle="tab" data-bs-target="#selesai" type="button" role="tab" aria-controls="selesai" aria-selected="false">Selesai</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="dibatalkan-tab" data-bs-toggle="tab" data-bs-target="#dibatalkan" type="button" role="tab" aria-controls="dibatalkan" aria-selected="false">Dibatalkan</button>
                        </li>
                    </ul>

                    <!-- Transaction Log -->
                    <div class="tab-content" id="statusTabsContent">
                        <div class="tab-pane fade show active" id="semua" role="tabpanel" aria-labelledby="semua-tab">
                            @include('account.merch_history.partials.order_list', ['orders' => $orders])
                        </div>
                        <div class="tab-pane fade" id="belum-bayar" role="tabpanel" aria-labelledby="belum-bayar-tab">
                            @include('account.merch_history.partials.order_list', ['orders' => $orders->where('status', 'pending')])
                        </div>
                        <div class="tab-pane fade" id="diproses" role="tabpanel" aria-labelledby="diproses-tab">
                            @include('account.merch_history.partials.order_list', ['orders' => $orders->whereIn('status', ['paid', 'shipped'])])
                        </div>
                        <div class="tab-pane fade" id="selesai" role="tabpanel" aria-labelledby="selesai-tab">
                            @include('account.merch_history.partials.order_list', ['orders' => $orders->where('status', 'completed')])
                        </div>
                        <div class="tab-pane fade" id="dibatalkan" role="tabpanel" aria-labelledby="dibatalkan-tab">
                            @include('account.merch_history.partials.order_list', ['orders' => $orders->whereIn('status', ['cancelled', 'failed'])])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('js/account/tabs.js') }}"></script>
@endsection