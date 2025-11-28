<!-- Modal: Update Alamat -->
<div class="modal fade" id="updateAddressModal" tabindex="-1" aria-hidden="true" style="display:none;" data-lazy="1">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">

            <form id="formUpdateAddress" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="user_id" value="{{ Auth::id() }}">

                <div class="modal-header text-center">
                    <h5 class="modal-title fw-bold text-center">Ubah Alamat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body px-4">

                    <div class="form-group mb-3">
                        <input type="text" id="update_name" class="form-control input-cyan" name="name"
                            placeholder="Nama Penerima" required>
                    </div>

                    <div class="form-group mb-3">
                        <input type="text" id="update_phone" class="form-control input-cyan" name="phone"
                            placeholder="Nomor HP" required>
                    </div>

                    <div class="form-group mb-3">
                        <input type="text" id="update_label" class="form-control input-cyan" name="label_address"
                            placeholder="Label Alamat (rumah / kantor / apartemen)" required>
                    </div>

                    <div class="form-group mb-3">
                        <select class="form-control input-cyan" id="update_provinsi" name="provinsi_id" required>
                            <option value="">Pilih Provinsi</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <select class="form-control input-cyan" id="update_kabupaten" name="kabupaten_id" disabled
                            required>
                            <option value="">Pilih Kabupaten/Kota</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <select class="form-control input-cyan" id="update_kecamatan" name="kecamatan_id" disabled
                            required>
                            <option value="">Pilih Kecamatan</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <textarea id="update_address" class="form-control input-cyan" name="address" rows="2" placeholder="Alamat Lengkap"
                            required></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="update_is_primary" name="is_primary"
                                value="1">
                            <label class="form-check-label" for="update_is_primary">Jadikan alamat utama</label>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-darkblue btn-wide btn-rounded"
                        id="saveUpdateAddress">Simpan</button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalEl = document.getElementById('updateAddressModal');
        const modal = new bootstrap.Modal(modalEl);

        const prov = document.getElementById('update_provinsi');
        const kab = document.getElementById('update_kabupaten');
        const kec = document.getElementById('update_kecamatan');

        // Populate provinsi and fetch address data when modal opens
        modalEl.addEventListener('show.bs.modal', function(e) {
            prov.innerHTML = '<option value="">Pilih Provinsi</option>';
            kab.innerHTML = '<option value="">Pilih Kabupaten</option>';
            kec.innerHTML = '<option value="">Pilih Kecamatan</option>';
            kab.disabled = true;
            kec.disabled = true;

            let provUrl = "{{ route('lokasi.provinsi') }}" || "/lokasi/provinsi";
            // load provinsi options first
            fetch(provUrl).then(r => r.json()).then(data => {
                data.forEach(p => prov.innerHTML +=
                    `<option value="${p.id}">${p.nama_provinsi}</option>`);
            }).catch(() => {});

            // If the opener stored an edit URL on the modal, fetch the address data and populate fields
            const editUrl = modalEl.dataset.editUrl;
            if (!editUrl) return;

            fetch(editUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.json())
                .then(obj => {
                    // populate fields
                    document.getElementById('update_name').value = obj.name || '';
                    document.getElementById('update_phone').value = obj.phone || '';
                    document.getElementById('update_label').value = obj.label_address || '';
                    document.getElementById('update_address').value = obj.address || '';
                    // set primary checkbox
                    const updPrimary = document.getElementById('update_is_primary');
                    if (updPrimary) updPrimary.checked = obj.is_primary ? true : false;

                    // set form action to resource update URL
                    const form = document.getElementById('formUpdateAddress');
                    // Build update URL using modal's data-update-base if available
                    const base = modalEl.dataset.updateBase || '/account/address';
                    if (form) form.action = base.replace(/\/$/, '') + '/' + obj.id;

                    // populate kabupaten/kecamatan based on selected provinsi
                    if (obj.provinsi_id) {
                        prov.value = obj.provinsi_id;
                        fetch('/lokasi/kabupaten/' + obj.provinsi_id)
                            .then(r => r.json())
                            .then(kabs => {
                                kab.innerHTML = '<option value="">Pilih Kabupaten</option>';
                                kabs.forEach(k => kab.innerHTML +=
                                    `<option value="${k.id}">${k.nama_kabupaten}</option>`);
                                kab.disabled = false;
                                if (obj.kabupaten_id) kab.value = obj.kabupaten_id;

                                if (obj.kabupaten_id) {
                                    fetch('/lokasi/kecamatan/' + obj.kabupaten_id)
                                        .then(r => r.json())
                                        .then(kecs => {
                                            kec.innerHTML =
                                                '<option value="">Pilih Kecamatan</option>';
                                            kecs.forEach(kc => kec.innerHTML +=
                                                `<option value="${kc.id}">${kc.nama_kecamatan}</option>`
                                            );
                                            kec.disabled = false;
                                            if (obj.kecamatan_id) kec.value = obj
                                                .kecamatan_id;
                                        }).catch(() => {});
                                }
                            }).catch(() => {});
                    }
                }).catch(() => {});
        });

        prov.addEventListener('change', function() {
            kab.innerHTML = '<option value="">Pilih Kabupaten</option>';
            kec.innerHTML = '<option value="">Pilih Kecamatan</option>';
            kab.disabled = true;
            kec.disabled = true;
            if (!this.value) return;
            fetch('/lokasi/kabupaten/' + this.value).then(r => r.json()).then(data => {
                kab.disabled = false;
                data.forEach(k => kab.innerHTML +=
                    `<option value="${k.id}">${k.nama_kabupaten}</option>`);
            }).catch(() => {});
        });
        kab.addEventListener('change', function() {
            kec.innerHTML = '<option value="">Pilih Kecamatan</option>';
            kec.disabled = true;
            if (!this.value) return;
            fetch('/lokasi/kecamatan/' + this.value).then(r => r.json()).then(data => {
                kec.disabled = false;
                data.forEach(k => kec.innerHTML +=
                    `<option value="${k.id}">${k.nama_kecamatan}</option>`);
            }).catch(() => {});
        });
    });
</script>
