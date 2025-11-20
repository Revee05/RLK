@php
$destinationAddress = $selectedAddress ? [
    'provinsi_id'  => $selectedAddress->provinsi_id,
    'kabupaten_id' => $selectedAddress->kabupaten_id,
    'kecamatan_id' => $selectedAddress->kecamatan_id,
    'desa_id'      => $selectedAddress->desa_id ?? null,
] : null;
@endphp

<!-- MODAL PILIH KURIR -->
<div class="modal fade" id="shipperModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="fw-bold mb-0">Pilih Kurir</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div id="shipper-list" style="display:none"></div>
        <div id="shipper-loading" class="text-center text-muted">
          Memuat kurir...
        </div>
      </div>

    </div>
  </div>
</div>

@push('js')
<script>
document.addEventListener("DOMContentLoaded", function () {

    const btnPilihKurir = document.getElementById('btnPilihKurir');
    const shipperList = document.getElementById('shipper-list');
    const shipperLoading = document.getElementById('shipper-loading');
    const destinationAddress = @json($destinationAddress ?? null);
    const subtotal = {{ $subtotal ?? 0 }};

    if (!btnPilihKurir) return;

    btnPilihKurir.addEventListener('click', function () {

        if (!destinationAddress) {
            alert('Pilih alamat tujuan dulu!');
            return;
        }

        shipperList.style.display = 'none';
        shipperLoading.style.display = 'block';
        shipperList.innerHTML = '';

        fetch('{{ route("checkout.shipping-cost") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                destination: destinationAddress
            })
        })
        .then(res => res.json())
        .then(data => {

            shipperList.innerHTML = '';

            if (!data.length) {
                shipperList.innerHTML = `
                    <div class="text-center text-muted">Tidak ada kurir tersedia.</div>
                `;
            } else {
                data.forEach(ship => {
                    const harga = ship.price ?? 0;

                    const div = document.createElement('div');
                    div.classList.add('border','rounded','p-3','mb-2','pointer');

                    div.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${ship.name}</strong><br>
                                <small class="text-muted">Estimasi: -</small>
                            </div>
                            <div class="fw-bold">Rp ${harga.toLocaleString('id-ID')}</div>
                        </div>
                    `;

                    div.onclick = function () {
                        selectShipper(ship.name, harga, ship.id, subtotal);
                    };

                    shipperList.appendChild(div);
                });
            }

            shipperLoading.style.display = 'none';
            shipperList.style.display = 'block';
        })
        .catch(() => {
            shipperList.innerHTML = `
                <div class="text-center text-danger">Gagal memuat kurir.</div>
            `;
            shipperLoading.style.display = 'none';
            shipperList.style.display = 'block';
        });
    });
});


// ========================
// FUNGSI PILIH KURIR
// ========================
function selectShipper(name, price, id, subtotal) {

    document.getElementById('selected-shipper').innerHTML =
        `${name} – Rp ${price.toLocaleString('id-ID')}`;

    document.getElementById('shipping_price').innerText =
        'Rp ' + price.toLocaleString('id-ID');

    document.getElementById('total_price').innerText =
        'Rp ' + (Number(subtotal) + Number(price)).toLocaleString('id-ID');

    document.getElementById('radioDelivery').checked = true;

    // hidden input
    let input = document.getElementById('selected_shipper_id');
    if (!input) {
        input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'shipper_id';
        input.id = 'selected_shipper_id';
        document.querySelector('form').appendChild(input);
    }
    input.value = id;

    // tutup modal
    const modalEl = document.getElementById('shipperModal');
    const modal = bootstrap.Modal.getInstance(modalEl);
    modal.hide();
}

</script>
@endpush
