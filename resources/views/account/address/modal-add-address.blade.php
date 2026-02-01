<!-- Modal: Tambah Alamat (reuse modal-tambah-address implementation) -->
<!-- This modal is opened from the account address page 'Tambah Alamat' button -->
<div class="modal fade" id="addAddressModal" tabindex="-1" aria-hidden="true" style="display:none;" data-lazy="1">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">

            <form id="formAddAddress" action="{{ route('account.address.store') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" value="{{ Auth::id() }}">

                <div class="modal-header text-center">
                    <h5 class="modal-title fw-bold text-center">Tambah Alamat Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body px-4">

                    <div class="form-group mb-3">
                        <input type="text" class="form-control input-cyan" name="name" placeholder="Nama Penerima"
                            required>
                    </div>

                    <div class="form-group mb-3">
                        <input type="number" class="form-control input-cyan" name="phone" placeholder="Nomor HP"
                            required>
                    </div>

                    <div class="form-group mb-3">
                        <input type="text" class="form-control input-cyan" name="label_address"
                            placeholder="Label Alamat (rumah / kantor / apartemen)" required>
                    </div>

                    <div class="form-group mb-3">
                        <select class="form-control input-cyan" id="provinsi" name="province_id" required>
                            <option value="">Pilih Provinsi</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <select class="form-control input-cyan" id="kabupaten" name="city_id" disabled required>
                            <option value="">Pilih Kabupaten/Kota</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <select class="form-control input-cyan" id="kecamatan" name="district_id" disabled required>
                            <option value="">Pilih Kecamatan</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <textarea class="form-control input-cyan" name="address" rows="2" placeholder="Alamat Lengkap" required></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_primary" name="is_primary"
                                value="1">
                            <label class="form-check-label" for="is_primary">Jadikan alamat utama</label>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-darkblue btn-wide btn-rounded"
                        id="saveNewAddress">Simpan</button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Note: toast UI removed — form will submit via Laravel and controller should set flash messages.
        const modalAdd = new bootstrap.Modal(document.getElementById('addAddressModal'));

        let prov = document.getElementById('provinsi');
        let kab = document.getElementById('kabupaten');
        let kec = document.getElementById('kecamatan');

        // === Saat Modal Dibuka ===
        const populateSelect = (select, placeholder) => {
            select.innerHTML = `<option value="">${placeholder}</option>`;
        };

        const fetchOptions = (url) => fetch(url).then(res => res.json());

        document.getElementById("addAddressModal").addEventListener("show.bs.modal", function() {
            populateSelect(prov, 'Pilih Provinsi');
            populateSelect(kab, 'Pilih Kabupaten');
            populateSelect(kec, 'Pilih Kecamatan');
            kab.disabled = true;
            kec.disabled = true;

            fetchOptions("{{ route('lokasi.province') }}")
                .then(data => {
                    data.forEach(p => prov.innerHTML +=
                        `<option value="${p.id}">${p.name}</option>`);
                })
                .catch(() => populateSelect(prov, 'Pilih Provinsi'));
        });

        const loadCityOptions = (provinceId) => {
            populateSelect(kab, 'Pilih Kabupaten');
            populateSelect(kec, 'Pilih Kecamatan');
            kab.disabled = true;
            kec.disabled = true;
            if (!provinceId) return;

            fetchOptions("/lokasi/city/" + provinceId)
                .then(data => {
                    kab.disabled = false;
                    data.forEach(k => kab.innerHTML +=
                        `<option value="${k.id}">${k.name}</option>`);
                });
        };

        const loadDistrictOptions = (cityId) => {
            populateSelect(kec, 'Pilih Kecamatan');
            kec.disabled = true;
            if (!cityId) return;

            fetchOptions("/lokasi/district/" + cityId)
                .then(data => {
                    kec.disabled = false;
                    data.forEach(k => kec.innerHTML +=
                        `<option value="${k.id}">${k.name}</option>`);
                });
        };

        prov.addEventListener("change", function() {
            loadCityOptions(this.value);
        });

        kab.addEventListener("change", function() {
            loadDistrictOptions(this.value);
        });

        // Submission will be handled by the server via normal form POST.
    });
</script>
