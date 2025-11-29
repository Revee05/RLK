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

                        <form id="notificationSettingsForm" method="POST"
                            action="{{ route('account.notifications.update') }}">
                            @csrf

                            <h5 class="mt-4">Email</h5>
                            <div class="form-group">
                                <input type="hidden" name="email_enabled" value="0">
                                <div class="form-check form-switch form-switch-right">
                                    <input type="checkbox" class="form-check-input" id="email_enabled" name="email_enabled"
                                        value="1" {{ $settings->email_enabled ? 'checked' : '' }}>
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

                                <input type="hidden" name="email_promo" value="0">
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
                                        value="1" {{ $settings->wa_enabled ? 'checked' : '' }}>
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
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-cyan rounded-3 text-dark"
                                    id="saveNotifications">Simpan</button>
                                <span id="notifSaved" class="text-success ms-3" style="display:none">Tersimpan.</span>
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
        document.getElementById('notificationSettingsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var form = e.target;
            var data = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content'),
                    'Accept': 'application/json'
                },
                body: data
            }).then(function(res) {
                return res.json();
            }).then(function(json) {
                var el = document.getElementById('notifSaved');
                el.style.display = 'inline';
                setTimeout(function() {
                    el.style.display = 'none';
                }, 2500);
            }).catch(function(err) {
                alert('Terjadi kesalahan saat menyimpan pengaturan.');
                console.error(err);
            });
        });
    </script>
@endsection
