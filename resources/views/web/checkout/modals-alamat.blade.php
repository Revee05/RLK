<div class="modal fade modal-alamat" id="addressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header border-0 pb-0">
                <h5 class="address-title fw-bold">Daftar Alamat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="address-item mb-3">
                    <button type="button" class="btn btn-dark w-100 py-2" id="btn-add-address">
                        + Tambah Alamat Baru
                    </button>
                </div>

                <div id="address-list">
                    @if ($addresses->count())
                        @foreach ($addresses as $address)
                            <label class="address-card d-flex justify-content-between align-items-start mb-3 p-3 border rounded pointer"
                                data-id="{{ $address->id }}" 
                                data-name="{{ $address->name }}"
                                data-phone="{{ $address->phone }}" 
                                data-label="{{ $address->label_address }}"
                                data-address="{{ $address->address }}"
                                data-district-id="{{ $address->district_id }}">
                                
                                <div>
                                    @if ($address->is_primary || $address->is_main)
                                        <span class="label-utama badge bg-primary mb-1">Utama</span><br>
                                    @endif

                                    <span class="address-name fw-bold"> {{ $address->name }} </span>
                                    <span class="address-separator">|</span>
                                    <span class="address-phone text-muted"> {{ $address->phone }} </span> <br>
                                    
                                    <div class="address-detail mt-1 small text-secondary">
                                        {{ $address->address }},
                                        {{ $address->district->name ?? '-' }}<br>
                                        {{ $address->city->name ?? '-' }},
                                        {{ $address->province->name ?? '-' }}
                                    </div>
                                </div>
                                
                                <input type="radio" name="selected_address_radio" class="form-check-input mt-1"
                                    value="{{ $address->id }}"
                                    @if ($selectedAddress && $selectedAddress->id == $address->id) checked @endif>
                            </label>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">Kamu belum memiliki alamat pengiriman.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="modal-footer border-0">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>

        </div>
    </div>
</div>

@push('js')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const addressList = document.getElementById("address-list");

            function bindAddressCardEvents() {
                if (!addressList) return;

                addressList.querySelectorAll(".address-card").forEach(card => {
                    card.addEventListener("click", function() {
                        const addressId = this.dataset.id;
                        const districtId = this.dataset.districtId;
                        const radio = this.querySelector('input[type="radio"]');
                        
                        if (radio) radio.checked = true;

                        // Kirim ke server untuk set session alamat
                        fetch("{{ route('checkout.set-address') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                address_id: addressId
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === "success") {
                                // 1. Update Tampilan Alamat di Halaman Checkout Utama
                                const checkoutContainer = document.getElementById("checkout-selected-address");
                                if (checkoutContainer) {
                                    checkoutContainer.innerHTML = `
                                        <div class="address-row">
                                            ${data.address.is_primary ? '<span class="label-utama">Utama</span><br>' : ''}
                                            <div class="address-name">${data.address.name}</div>
                                            <span class="address-separator">|</span>
                                            <div class="address-phone mt-1">${data.address.phone}</div>
                                        </div>
                                        <div class="address-detail mt-1">
                                            ${data.address.address}, ${data.address.district ?? '-'}, ${data.address.city ?? '-'}, ${data.address.province ?? '-'}
                                        </div>
                                    `;
                                }

                                // 2. Update Hidden Input di Form
                                const inputDistrict = document.getElementById('selected_district_id');
                                const inputAddressId = document.querySelector('input[name="address_id"]');
                                
                                if (inputDistrict) inputDistrict.value = data.address.district_id;
                                if (inputAddressId) inputAddressId.value = addressId;

                                // 3. Update Variabel Global Checkout
                                if (window.checkout) {
                                    window.checkout.destination = data.address.district_id;
                                }

                                // 4. Reset Kurir (Karena alamat berubah, ongkir harus dihitung ulang)
                                const selectedShipperDiv = document.getElementById('selected-shipper');
                                const shippingPriceEl = document.getElementById('shipping_price');
                                const inputOngkir = document.getElementById('input_total_ongkir');

                                if (selectedShipperDiv) {
                                    selectedShipperDiv.style.display = 'none';
                                    selectedShipperDiv.innerHTML = '';
                                }
                                if (shippingPriceEl) shippingPriceEl.innerText = 'Rp 0';
                                if (inputOngkir) inputOngkir.value = 0;

                                // 5. Jalankan Event Alamat Dipilih untuk refresh total biaya
                                document.dispatchEvent(new CustomEvent('alamatDipilih', {
                                    detail: {
                                        id: addressId,
                                        districtId: data.address.district_id
                                    }
                                }));

                                // 6. Tutup Modal
                                const modalInstance = bootstrap.Modal.getInstance(document.getElementById("addressModal"));
                                if (modalInstance) modalInstance.hide();
                            }
                        })
                        .catch(err => console.error("Error setting address:", err));
                    });
                });
            }

            // Inisialisasi event listener
            bindAddressCardEvents();

            // Handler Tambah Alamat (Tutup modal ini, buka modal tambah)
            document.getElementById("btn-add-address")?.addEventListener("click", function() {
                const currentModal = bootstrap.Modal.getInstance(document.getElementById("addressModal"));
                if (currentModal) currentModal.hide();
                
                const addModalEl = document.getElementById("addAddressModal");
                if (addModalEl) {
                    const addModal = new bootstrap.Modal(addModalEl);
                    addModal.show();
                }
            });
        });
    </script>
@endpush