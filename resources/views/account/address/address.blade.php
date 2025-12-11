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
                                                            class="small text-cyan fw-bold">{{ ucwords($ua->label_address) }}</span>
                                                        @if (!empty($ua->is_primary) && $ua->is_primary)
                                                            <span class="address-badge mb-1">Utama</span>
                                                        @endif
                                                        <div class="d-flex align-items-center">
                                                            <strong class="me-2 mb-0">{{ $ua->name }}</strong>
                                                            <span class="address-divider text-muted">|</span>
                                                            <span class="text-muted small ms-2">{{ $ua->phone }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="address-street text-muted">{{ $ua->address }}</div>
                                                    <div class="address-location text-muted small">
                                                        {{ $ua->province->name }},
                                                        {{ $ua->city->name }},
                                                        {{ $ua->district->name }}</div>
                                                </div>
                                                <div class="address-actions d-flex align-items-center">
                                                    <a href="#" class="btn-icon icon-edit open-update-address"
                                                        aria-label="Ubah alamat"
                                                        data-url="{{ route('account.address.edit', $ua->id) }}"
                                                        data-id="{{ $ua->id }}">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <button type="button"
                                                        class="btn-icon icon-delete open-delete-address ms-2"
                                                        aria-label="Hapus alamat"
                                                        data-url="{{ route('account.address.destroy', [$ua->id]) }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-3 text-start ps-0">
                            <a href="#" class="btn btn-cyan btn-add-address" data-bs-toggle="modal"
                                data-bs-target="#addAddressModal" id="openAddAddress">
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
    <!-- Toasts for server-side flash messages -->
    <div aria-live="polite" aria-atomic="true" class="position-relative">
        <div id="accountToastContainer"
            style="position: fixed; right: 1rem; top: 1rem; z-index: 10800;
            display: flex; flex-direction: column; gap: .5rem;
        ">
        </div>
    </div>

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('accountToastContainer');

            // Success toast
            @if (session('success'))
                (function() {
                    const toastSuccess = document.createElement('div');
                    toastSuccess.className = 'toast align-items-center text-white bg-success border-0';
                    toastSuccess.setAttribute('role', 'alert');
                    toastSuccess.setAttribute('aria-live', 'assertive');
                    toastSuccess.setAttribute('aria-atomic', 'true');
                    toastSuccess.innerHTML = `
                        <div class="d-flex">
                            <div class="toast-body">{!! session('success') !!}</div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>`;
                    container.appendChild(toastSuccess);
                    new bootstrap.Toast(toastSuccess, {
                        delay: 4000
                    }).show();
                })();
            @endif

            // Error toast
            @if (session('error'))
                (function() {
                    const toastError = document.createElement('div');
                    toastError.className = 'toast align-items-center text-white bg-danger border-0';
                    toastError.setAttribute('role', 'alert');
                    toastError.setAttribute('aria-live', 'assertive');
                    toastError.setAttribute('aria-atomic', 'true');
                    toastError.innerHTML = `
                        <div class="d-flex">
                            <div class="toast-body">{!! session('error') !!}</div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>`;
                    container.appendChild(toastError);
                    new bootstrap.Toast(toastError, {
                        delay: 4000
                    }).show();
                })();
            @endif

            // Wire edit buttons: only set modal edit URL and show modal; modal will fetch/populate
            document.querySelectorAll('.open-update-address').forEach(function(el) {
                el.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('data-url');
                    if (!url) return;

                    const updModalEl = document.getElementById('updateAddressModal');
                    if (!updModalEl) return;

                    // attach the edit URL to modal for its own show handler
                    updModalEl.dataset.editUrl = url;
                    const updModal = new bootstrap.Modal(updModalEl);
                    updModal.show();
                });
            });

            // Wire delete buttons to open confirmation modal
            document.querySelectorAll('.open-delete-address').forEach(function(el) {
                el.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('data-url');
                    if (!url) return;

                    const delModalEl = document.getElementById('deleteAddressModal');
                    if (!delModalEl) return;
                    delModalEl.dataset.deleteUrl = url;
                    const delModal = new bootstrap.Modal(delModalEl);
                    delModal.show();
                });
            });
        });
    </script>
@endsection

@include('account.address.modal-add-address')
@include('account.address.modal-update-address')
@include('account.address.modal-delete-address')
