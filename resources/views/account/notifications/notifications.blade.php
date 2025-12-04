@extends('account.partials.layout')

@section('content')
    <div class="container" style="max-width:1200px; margin-top:40px; margin-bottom:80px;">
        <div class="row">
            @include('account.partials.nav_new')

            <div class="col-md-9">
                <div class="card content-border">
                    <div class="card-head border-bottom border-darkblue align-baseline ps-4">
                        <h3 class="mb-0 fw-bolder align-bottom">Pengaturan Notifikasi</h3>
                    </div>
                    <div class="card-body mx-2">
                        <p class="text-muted">Kelola channel notifikasi yang ingin Anda terima.</p>

                        <!-- Toast container (used for success/error toasts) -->
                        <div aria-live="polite" aria-atomic="true" class="position-relative">
                            <div id="accountToastContainer"
                                style="position: fixed; right: 1rem; top: 1rem; z-index: 10800;
                                display: flex; flex-direction: column; gap: .5rem;">
                            </div>
                        </div>

                        <form id="notificationSettingsForm" method="POST"
                            action="{{ route('account.notifications.update') }}">
                            @csrf

                            <h5 class="mt-4">Email</h5>
                            <div class="form-group">
                                <input type="hidden" name="email_enabled" value="0">
                                <div class="form-check form-switch form-switch-right">
                                    <input type="checkbox" class="form-check-input" id="email_enabled" name="email_enabled"
                                        value="1" {{ $settings->email_enabled ? 'checked' : '' }} disabled>
                                    <label class="form-check-label" for="email_enabled">Aktifkan email notifikasi</label>
                                </div>

                                <input type="hidden" name="email_order_status" value="0">
                                <div class="form-check form-switch form-switch-right">
                                    <input type="checkbox" class="form-check-input" id="email_order_status"
                                        name="email_order_status" value="1"
                                        {{ $settings->email_order_status ? 'checked' : '' }}>
                                    <label class="form-check-label" for="email_order_status">Perubahan status
                                        pesanan</label>
                                </div>

                                {{-- <input type="hidden" name="email_promo" value="0">
                                <div class="form-check form-switch form-switch-right">
                                    <input type="checkbox" class="form-check-input" id="email_promo" name="email_promo"
                                        value="1" {{ $settings->email_promo ? 'checked' : '' }}>
                                    <label class="form-check-label" for="email_promo">Promosi dan penawaran</label>
                                </div>
                            </div>

                            <h5 class="mt-4">WhatsApp</h5>
                            <div class="form-group">
                                <input type="hidden" name="wa_enabled" value="0">
                                <div class="form-check form-switch form-switch-right">
                                    <input type="checkbox" class="form-check-input" id="wa_enabled" name="wa_enabled"
                                        value="1" {{ $settings->wa_enabled ? 'checked' : '' }} disabled>
                                    <label class="form-check-label" for="wa_enabled">Aktifkan WhatsApp notifikasi</label>
                                </div>

                                <input type="hidden" name="wa_order_status" value="0">
                                <div class="form-check form-switch form-switch-right">
                                    <input type="checkbox" class="form-check-input" id="wa_order_status"
                                        name="wa_order_status" value="1"
                                        {{ $settings->wa_order_status ? 'checked' : '' }}>
                                    <label class="form-check-label" for="wa_order_status">Perubahan status pesanan</label>
                                </div>

                                <input type="hidden" name="wa_promo" value="0">
                                <div class="form-check form-switch form-switch-right">
                                    <input type="checkbox" class="form-check-input" id="wa_promo" name="wa_promo"
                                        value="1" {{ $settings->wa_promo ? 'checked' : '' }}>
                                    <label class="form-check-label" for="wa_promo">Promosi dan penawaran</label>
                                </div>
                            </div>

                            <h5 class="mt-4">Banner</h5>
                            <div class="form-group">
                                <input type="hidden" name="banner_enabled" value="0">
                                <div class="form-check form-switch form-switch-right">
                                    <input type="checkbox" class="form-check-input" id="banner_enabled"
                                        name="banner_enabled" value="1"
                                        {{ $settings->banner_enabled ? 'checked' : '' }}>
                                    <label class="form-check-label" for="banner_enabled">Tampilkan banner notifikasi di
                                        akun</label>
                                </div>

                                <input type="hidden" name="banner_order_status" value="0">
                                <div class="form-check form-switch form-switch-right">
                                    <input type="checkbox" class="form-check-input" id="banner_order_status"
                                        name="banner_order_status" value="1"
                                        {{ $settings->banner_order_status ? 'checked' : '' }}>
                                    <label class="form-check-label" for="banner_order_status">Perubahan status
                                        pesanan</label>
                                </div> --}}
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var form = document.getElementById('notificationSettingsForm');
            var savedEl = document.getElementById('notifSaved');
            var inputs = form.querySelectorAll('.form-check-input');
            var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            var toastContainer = document.getElementById('accountToastContainer') || document.body;

            function makeToast(message, type) {
                // type: 'success' | 'danger' | 'info'
                var bgClass = type === 'success' ? 'bg-success' : (type === 'danger' ? 'bg-danger' : 'bg-info');

                // If Bootstrap Toast API exists, use it. Otherwise fallback to a simple manual toast.
                if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
                    var toast = document.createElement('div');
                    toast.className = 'toast align-items-center text-white ' + bgClass + ' border-0';
                    toast.setAttribute('role', 'alert');
                    toast.setAttribute('aria-live', 'assertive');
                    toast.setAttribute('aria-atomic', 'true');

                    toast.innerHTML = `
                        <div class="d-flex">
                            <div class="toast-body">${message}</div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>`;

                    toastContainer.appendChild(toast);
                    var bsToast = new bootstrap.Toast(toast, {
                        delay: 3500
                    });
                    bsToast.show();
                    toast.addEventListener('hidden.bs.toast', function() {
                        toast.remove();
                    });
                    return;
                }

                // Fallback manual toast
                var manual = document.createElement('div');
                manual.style.background = (type === 'success') ? '#198754' : ((type === 'danger') ? '#dc3545' :
                    '#0dcaf0');
                manual.style.color = '#fff';
                manual.style.padding = '0.6rem 0.9rem';
                manual.style.borderRadius = '0.4rem';
                manual.style.boxShadow = '0 6px 18px rgba(0,0,0,0.08)';
                manual.style.marginBottom = '0.5rem';
                manual.style.maxWidth = '320px';
                manual.style.fontSize = '0.95rem';
                manual.textContent = message;

                // If container is body, position fixed top-right
                if (toastContainer === document.body) {
                    manual.style.position = 'fixed';
                    manual.style.right = '1rem';
                    manual.style.top = (1 + (toastContainer._manualToastOffset || 0)) + 'rem';
                    toastContainer._manualToastOffset = (toastContainer._manualToastOffset || 0) +
                        3.2; // stack spacing
                }

                toastContainer.appendChild(manual);
                setTimeout(function() {
                    manual.remove();
                    if (toastContainer === document.body) {
                        toastContainer._manualToastOffset = Math.max(0, (toastContainer
                            ._manualToastOffset || 0) - 3.2);
                    }
                }, 3500);
            }

            function showSavedQuick() {
                if (savedEl) {
                    savedEl.style.display = 'inline';
                    setTimeout(function() {
                        savedEl.style.display = 'none';
                    }, 1400);
                }
            }

            // Attach per-switch change handlers
            inputs.forEach(function(input) {
                input.addEventListener('change', function(e) {
                    var name = this.name;
                    var value = this.checked ? 1 : 0;
                    var formCheck = this.closest('.form-check');

                    // small spinner element
                    var spinner = document.createElement('span');
                    spinner.className = 'spinner-border spinner-border-sm ms-2';
                    spinner.setAttribute('role', 'status');
                    spinner.setAttribute('aria-hidden', 'true');

                    // disable input while saving
                    this.disabled = true;
                    if (formCheck) formCheck.appendChild(spinner);

                    var data = new FormData();
                    data.append('_token', csrf);
                    data.append(name, value);

                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json'
                        },
                        body: data
                    }).then(function(res) {
                        if (!res.ok) {
                            // server error: show toast with status
                            if (res.status >= 500) {
                                makeToast(
                                    'Terjadi kesalahan server. Silakan coba lagi nanti.',
                                    'danger');
                            } else {
                                makeToast('Gagal menyimpan. Periksa koneksi Anda.',
                                    'danger');
                            }
                            throw new Error('HTTP ' + res.status);
                        }
                        return res.json();
                    }).then(function(json) {
                        if (json && json.success) {
                            showSavedQuick();
                        } else {
                            makeToast((json && json.message) ? json.message :
                                'Gagal menyimpan.', 'danger');
                        }
                    }).catch(function(err) {
                        console.error(err);
                    }).finally(() => {
                        // remove spinner and re-enable
                        input.disabled = false;
                        if (spinner && spinner.parentNode) spinner.parentNode.removeChild(
                            spinner);
                    });
                });
            });

            // Prevent form submit (we update per-switch)
            form.addEventListener('submit', function(e) {
                e.preventDefault();
            });
        });
    </script>
@endsection
