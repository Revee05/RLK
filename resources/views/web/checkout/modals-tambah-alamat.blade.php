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
            <select class="form-control" id="provinsi" name="provinsi_id" required>
              <option value="">Pilih Provinsi</option>
            </select>
          </div>

          <div class="form-group mb-2">
            <select class="form-control" id="kabupaten" name="kabupaten_id" disabled required>
              <option value="">Pilih Kabupaten/Kota</option>
            </select>
          </div>

          <div class="form-group mb-2">
            <select class="form-control" id="kecamatan" name="kecamatan_id" disabled required>
              <option value="">Pilih Kecamatan</option>
            </select>
          </div>

          <div class="form-group mb-2">
            <textarea class="form-control" name="address" rows="2" placeholder="Alamat Lengkap" required></textarea>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-dark" id="saveNewAddress">Simpan</button>
        </div>

      </form>

    </div>
  </div>
</div>

@push('js')
<script> 
document.addEventListener("DOMContentLoaded", function () {

    // Hanya modal add saja (modal list dihapus)
    const modalAdd = new bootstrap.Modal(document.getElementById('addAddressModal'));

    let prov = document.getElementById('provinsi');
    let kab = document.getElementById('kabupaten');
    let kec = document.getElementById('kecamatan');

    // === Jika ada tombol open ===
    if (document.getElementById("openAddAddress")) {
        document.getElementById("openAddAddress").addEventListener("click", function () {
            modalAdd.show();
        });
    }

    // === Saat Modal Dibuka ===
    document.getElementById("addAddressModal").addEventListener("show.bs.modal", function () {

        prov.innerHTML = '<option value="">Pilih Provinsi</option>';
        kab.innerHTML = '<option value="">Pilih Kabupaten</option>';
        kec.innerHTML = '<option value="">Pilih Kecamatan</option>';
        kab.disabled = true;
        kec.disabled = true;

        fetch("{{ route('lokasi.provinsi') }}")
            .then(res => res.json())
            .then(data => {
                data.forEach(p => {
                    prov.innerHTML += `<option value="${p.id}">${p.nama_provinsi}</option>`;
                });
            });
    });

    // === Provinsi → Kabupaten ===
    prov.addEventListener("change", function () {
        kab.innerHTML = '<option value="">Pilih Kabupaten</option>';
        kec.innerHTML = '<option value="">Pilih Kecamatan</option>';
        kab.disabled = true;
        kec.disabled = true;

        if (!this.value) return;

        fetch("/lokasi/kabupaten/" + this.value)
            .then(res => res.json())
            .then(data => {
                kab.disabled = false;
                data.forEach(k => {
                    kab.innerHTML += `<option value="${k.id}">${k.nama_kabupaten}</option>`;
                });
            });
    });

    // === Kabupaten → Kecamatan ===
    kab.addEventListener("change", function () {
        kec.innerHTML = '<option value="">Pilih Kecamatan</option>';
        kec.disabled = true;

        if (!this.value) return;

        fetch("/lokasi/kecamatan/" + this.value)
            .then(res => res.json())
            .then(data => {
                kec.disabled = false;
                data.forEach(k => {
                    kec.innerHTML += `<option value="${k.id}">${k.nama_kecamatan}</option>`;
                });
            });
    });

    // === SUBMIT FORM ===
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
          
          // tutup modal 2
          modalAdd.hide();
          this.reset();

          // buka modal 1
          const modal1El = document.getElementById("addressModal");
          if (modal1El) {
              const modal1 = new bootstrap.Modal(modal1El);

              // lakukan refresh saat modal 1 sudah terbuka sepenuhnya
              modal1El.addEventListener("shown.bs.modal", function () {
                  reloadAddressList();
              }, { once: true });

              modal1.show();
          }

          // Reset form
          document.getElementById("formAddAddress").reset();
      })
      .catch(err => {
          console.error(err);
          alert("Terjadi kesalahan");
      });
  });
});
</script>
@endpush
