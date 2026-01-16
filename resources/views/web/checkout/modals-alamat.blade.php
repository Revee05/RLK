<div class="modal fade modal-alamat" id="addressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header border-0 pb-0">
                <h5 class="address-title fw-bold">Daftar Alamat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <!-- Tambah Alamat -->
                <div class="address-item">
                    <button type="button" class="btn-add-address w-100 py-2" id="btn-add-address">
                        Tambah Alamat
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
document.addEventListener("DOMContentLoaded", function () {

    const modalAdd = new bootstrap.Modal(document.getElementById("addAddressModal"));
    const addressList = document.getElementById("address-list");

    function bindAddressCardEvents() {
        addressList.querySelectorAll(".address-card").forEach(card => {
            card.addEventListener("click", function () {

                const radio = this.querySelector('input[type="radio"]');
                if (radio) radio.checked = true;

                fetch("{{ route('checkout.set-address') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        address_id: this.dataset.id
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === "success") {

                        const checkoutContainer =
                            document.getElementById("checkout-selected-address");

                        checkoutContainer.innerHTML = `
                            <div class="address-row">
                                ${data.address.is_primary ? '<span class="label-utama">Utama</span><br>' : ''}
                                <div class="address-name">${data.address.name}</div>
                                <span class="address-separator">|</span>
                                <div class="address-phone mt-1">${data.address.phone}</div>
                            </div>
                            <div class="address-detail mt-1">
                                ${data.address.address},
                                ${data.address.district ?? '-'},
                                ${data.address.city ?? '-'},
                                ${data.address.province ?? '-'}
                            </div>
                        `;

                        document.getElementById('selected_district_id').value =
                            data.address.district_id || '';

                        window.checkout.destination =
                            data.address.district_id || '';

                        window.SHIPPER_DATA = null;

                        document.dispatchEvent(new CustomEvent('alamatDipilih', {
                            detail: {
                                districtId: data.address.district_id
                            }
                        }));

                        const selectedShipperDiv =
                            document.getElementById('selected-shipper');

                        selectedShipperDiv.style.display = 'none';
                        selectedShipperDiv.innerHTML = `
                            <input type="hidden" name="selected_shipper_id" id="selected_shipper_id" value="">
                            <input type="hidden" name="total_ongkir" id="input_total_ongkir" value="0">
                            <input type="hidden" name="jenis_ongkir" id="input_jenis_ongkir" value="Reguler">
                        `;

                        window.updateTotal(0);

                        bootstrap.Modal
                            .getInstance(document.getElementById("addressModal"))
                            ?.hide();
                    }
                });
            });
        });
    }

    bindAddressCardEvents();

    // Cleanup backdrop saat modal ditutup
    document.getElementById('addressModal').addEventListener('hidden.bs.modal', function () {
        // Hapus semua backdrop yang mungkin tertinggal
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(backdrop => {
            if (backdrop.classList.contains('show')) {
                return; // Jangan hapus backdrop yang masih aktif
            }
            backdrop.remove();
        });
    });

    document.getElementById("btn-add-address")
        ?.addEventListener("click", function () {
            const addressModal = bootstrap.Modal.getInstance(document.getElementById("addressModal"));
            
            if (addressModal) {
                addressModal.hide();
                
                // Tunggu modal pertama tertutup sepenuhnya
                document.getElementById("addressModal").addEventListener('hidden.bs.modal', function openAddModal() {
                    // FORCE hapus SEMUA backdrop lama
                    const allBackdrops = document.querySelectorAll('.modal-backdrop');
                    allBackdrops.forEach(backdrop => backdrop.remove());
                    
                    // Reset body state
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                    
                    // Tunggu sebentar lalu buka modal baru
                    setTimeout(() => {
                        modalAdd.show();
                    }, 200);
                    
                    // Remove listener setelah digunakan
                    this.removeEventListener('hidden.bs.modal', openAddModal);
                }, { once: true });
            } else {
                modalAdd.show();
            }
    });

});
</script>
@endpush
