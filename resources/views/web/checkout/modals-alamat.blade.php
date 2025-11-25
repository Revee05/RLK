<!-- MODAL PILIH ALAMAT -->
<div class="modal fade" id="addressModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content address-modal">

      <div class="modal-header border-0 pb-0">
        <h5 class="fw-bold">Daftar Alamat</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <!-- Tambah Alamat -->
        <div class="address-item">
            <button type="button" class="btn btn-dark w-100 py-2" id="btn-add-address">
                Tambah Alamat
            </button>
        </div>

        <!-- LIST ALAMAT -->
        <div id="address-list">
            @if($addresses->count())
                @foreach($addresses as $address)
                    <label class="address-card border border-primary rounded p-3 mb-3 d-flex justify-content-between align-items-start"
                           data-id="{{ $address->id }}"
                           data-name="{{ $address->name }}"
                           data-phone="{{ $address->phone }}"
                           data-label="{{ $address->label_address   }}"
                           data-address="{{ $address->address }}"
                           data-provinsi="{{ $address->province->name ?? '' }}"
                           data-kabupaten="{{ $address->city->name ?? '' }}">
                        <div>
                            <h6 class="fw-bold mb-1">{{ $address->label_address }}</h6>
                            <div class="small text-muted">
                                {{ $address->name }} <br>
                                {{ $address->phone }} <br>
                                {{ $address->address }},
                                {{ $address->city->name ?? '-' }},
                                {{ $address->province->name ?? '-' }}
                            </div>
                        </div>
                        <input type="radio" name="selected_address" class="form-check-input mt-1" @if($loop->first) checked @endif>
                    </label>
                @endforeach
            @else
                <p class="text-muted">Belum ada alamat.</p>
            @endif
        </div>

      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>

    </div>
  </div>
</div>

@push('js')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const modalAdd = new bootstrap.Modal(document.getElementById("addAddressModal"));
    
    const btnAddAddress = document.getElementById("btn-add-address");
    const addressList = document.getElementById("address-list");

    // Fungsi untuk pilih alamat
    function bindAddressCardEvents() {
        const cards = addressList.querySelectorAll(".address-card");
        cards.forEach(card => {
            card.addEventListener("click", function () {
                const radio = this.querySelector('input[type="radio"]');
                if (radio) radio.checked = true;

                fetch("{{ route('checkout.set-address') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ address_id: this.dataset.id })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === "success") {
                        const checkoutContainer = document.getElementById("checkout-selected-address");
                        if (checkoutContainer) {
                            checkoutContainer.innerHTML = `
                                <div class="address-card border border-primary rounded p-3 mb-3">
                                    <h6 class="fw-bold mb-1">${data.address.label_address}</h6>
                                    <div class="small text-muted">
                                        ${data.address.name} • ${data.address.phone} <br>
                                        ${data.address.address} <br>
                                        ${data.address.city ?? '-'}, ${data.address.province ?? '-'}
                                    </div>
                                </div>
                            `;
                        }

                        // tutup modal 1
                        const modal1 = bootstrap.Modal.getInstance(document.getElementById("addressModal"));
                        if (modal1) modal1.hide();
                    }
                });
            });
        });
    }

    bindAddressCardEvents();

    // Tombol Tambah Alamat
    if (btnAddAddress) {
        btnAddAddress.addEventListener("click", function () {
            // tutup modal 1
            const modal1 = bootstrap.Modal.getInstance(document.getElementById("addressModal"));
            if (modal1) modal1.hide();

            // buka modal 2
            const modal2El = document.getElementById("addAddressModal");
            if (modal2El) {
                const modal2 = bootstrap.Modal.getInstance(modal2El) || new bootstrap.Modal(modal2El);
                modal2.show();
            } else {
                console.error("Modal 2 belum ada di halaman!");
            }
        });
    }

    const modalList = new bootstrap.Modal(document.getElementById('addressModal'));

    let province = document.getElementById('province');
    let city = document.getElementById('city');
    let district = document.getElementById('district');

    // ==== LOAD PROVINSI SAAT MODAL 2 DIBUKA ====
    document.getElementById("addAddressModal").addEventListener("show.bs.modal", function () {

        province.innerHTML = '<option value="">Pilih Provinsi</option>';
        city.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
        district.innerHTML = '<option value="">Pilih Kecamatan</option>';
        
        city.disabled = true;
        district.disabled = true;

        fetch("/lokasi/province")
            .then(res => res.json())
            .then(data => {

                // URUTKAN A-Z
                data.sort((a, b) => a.name.localeCompare(b.name));

                data.forEach(p => {
                    province.innerHTML += `<option value="${p.id}">${p.name}</option>`;
                });
            });
    });

    // ==== PROVINSI -> KABUPATEN ====
    province.addEventListener("change", function () {

        city.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
        district.innerHTML = '<option value="">Pilih Kecamatan</option>';
        
        city.disabled = true;
        district.disabled = true;

        if (!this.value) return;

        fetch("/lokasi/city/" + this.value)
            .then(res => res.json())
            .then(data => {

                // URUT A-Z
                data.sort((a, b) => a.name.localeCompare(b.name));

                city.disabled = false;
                data.forEach(k => {
                    city.innerHTML += `<option value="${k.id}">${k.name}</option>`;
                });
            });
    });

    // ==== KABUPATEN -> KECAMATAN ====
    city.addEventListener("change", function () {

        district.innerHTML = '<option value="">Pilih Kecamatan</option>';
        district.disabled = true;

        if (!this.value) return;

        fetch("/lokasi/district/" + this.value)
            .then(res => res.json())
            .then(data => {

                // URUT A-Z
                data.sort((a, b) => a.name.localeCompare(b.name));

                district.disabled = false;
                data.forEach(k => {
                    district.innerHTML += `<option value="${k.id}">${k.name}</option>`;
                });
            });
    });

    window.reloadAddressList = function () {
    fetch("{{ route('alamat.refresh') }}")
        .then(res => res.json())
        .then(data => {

            const list = document.getElementById("address-list");
            list.innerHTML = data.html;

            // re-bind click event pada card
            bindAddressCardEvents();
        });
    }

});

</script>
@endpush
