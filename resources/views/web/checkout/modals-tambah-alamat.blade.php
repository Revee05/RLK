<!-- MODAL 2: TAMBAH ALAMAT --> 
<div class="modal fade" id="addAddressModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">

      <form id="formAddAddress">
      @csrf

        <div class="modal-header">
          <h5 class="modal-title fw-bold">Tambah Alamat Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <div class="form-group mb-2">
            <input type="text" class="form-control" name="name" placeholder="Nama Penerima" required>
          </div>

          <div class="form-group mb-2">
            <input type="text" class="form-control" name="phone" placeholder="Nomor HP" required>
          </div>

          <div class="form-group mb-2">
            <input type="text" class="form-control" name="label_address" placeholder="Label Alamat">
          </div>

          <div class="form-group mb-2">
            <select class="form-control" id="province" name="province_id" required>
              <option value="">Pilih Provinsi</option>
            </select>
          </div>

          <div class="form-group mb-2">
            <select class="form-control" id="city" name="city_id" disabled required>
              <option value="">Pilih Kabupaten/Kota</option>
            </select>
          </div>

          <div class="form-group mb-2">
            <select class="form-control" id="district" name="district_id" disabled required>
              <option value="">Pilih Kecamatan</option>
            </select>
          </div>

          <div class="form-group mb-2">
            <textarea class="form-control" name="address" rows="2" placeholder="Alamat Lengkap" required></textarea>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary" id="saveNewAddress">Simpan</button>
        </div>

      </form>

    </div>
  </div>
</div>

@push('js')
<script>
document.addEventListener("DOMContentLoaded", function () {

    const modalAdd = new bootstrap.Modal(document.getElementById("addAddressModal"));

    let province = document.getElementById('province');
    let city     = document.getElementById('city');
    let district = document.getElementById('district');

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

    // ================= LOAD PROVINSI =================
    document.getElementById("addAddressModal").addEventListener("show.bs.modal", function () {

        province.innerHTML = '<option value="">Pilih Provinsi</option>';
        city.innerHTML     = '<option value="">Pilih Kabupaten/Kota</option>';
        district.innerHTML = '<option value="">Pilih Kecamatan</option>';

        city.disabled = true;
        district.disabled = true;

        fetch("/lokasi/province")
            .then(res => res.json())
            .then(data => {
                data.sort((a, b) => a.name.localeCompare(b.name));
                data.forEach(p => {
                    province.innerHTML += `<option value="${p.id}">${p.name}</option>`;
                });
            });
    });

    // ================= PROVINSI → KOTA =================
    province.addEventListener("change", function () {

        city.innerHTML     = '<option value="">Pilih Kabupaten/Kota</option>';
        district.innerHTML = '<option value="">Pilih Kecamatan</option>';

        city.disabled = true;
        district.disabled = true;

        if (!this.value) return;

        fetch("/lokasi/city/" + this.value)
            .then(res => res.json())
            .then(data => {
                data.sort((a, b) => a.name.localeCompare(b.name));
                city.disabled = false;
                data.forEach(k => {
                    city.innerHTML += `<option value="${k.id}">${k.name}</option>`;
                });
            });
    });

    // ================= KOTA → KECAMATAN =================
    city.addEventListener("change", function () {

        district.innerHTML = '<option value="">Pilih Kecamatan</option>';
        district.disabled = true;

        if (!this.value) return;

        fetch("/lokasi/district/" + this.value)
            .then(res => res.json())
            .then(data => {
                data.sort((a, b) => a.name.localeCompare(b.name));
                district.disabled = false;
                data.forEach(k => {
                    district.innerHTML += `<option value="${k.id}">${k.name}</option>`;
                });
            });
    });

    // ================= SUBMIT FORM =================
    document.getElementById("formAddAddress").addEventListener("submit", function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        fetch("{{ route('alamat.store') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('input[name=_token]').value,
            },
            body: formData
        })
        .then(async response => {
            if (!response.ok) {
                let error = await response.json();
                alert(error.message || "Validasi gagal");
                return;
            }
            return response.json();
        })
        .then(res => {
            if (!res) return;

            alert("Alamat berhasil ditambahkan!");

            // ===== FLAG UNTUK BUKA MODAL ALAMAT SETELAH RELOAD =====
            sessionStorage.setItem("openAddressModal", "1");

            // ===== RELOAD HALAMAN =====
            location.reload();
        })
        .catch(err => {
            console.error(err);
            alert("Terjadi kesalahan");
        });
    });

});
</script>


@endpush
