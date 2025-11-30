@php
$destinationAddress = $selectedAddress->district_id ?? null;
$totalWeight = collect($cart ?? [])->sum(function ($item) {
    $weight = data_get($item, 'weight', 1000);
    $qty    = data_get($item, 'quantity', 1);
    return $weight * $qty;
});
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
document.addEventListener("DOMContentLoaded", function(){
    const shipperList = document.getElementById('shipper-list');
    const shipperLoading = document.getElementById('shipper-loading');

    document.getElementById('shipperModal').addEventListener('show.bs.modal', function(){
        const destination = window.SHIPPER_DATA?.destination || window.checkout?.destination || document.getElementById('selected_district_id').value;
        const weight = window.SHIPPER_DATA?.weight || window.checkout?.totalWeight || {{ $totalWeight }};

        if(!destination){
            shipperList.innerHTML = `<div class="text-center text-muted">Pilih alamat terlebih dahulu.</div>`;
            shipperList.style.display='block';
            shipperLoading.style.display='none';
            return;
        }

        loadShippers(destination, weight);
    });

    function loadShippers(destination, weight){
        shipperList.style.display='none';
        shipperLoading.style.display='block';
        shipperList.innerHTML='';

        fetch('{{ route("checkout.shipping-cost") }}', {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: JSON.stringify({origin:5592,destination,weight,price:"lowest"})
        })
        .then(res=>res.json())
        .then(data=>{
            shipperList.innerHTML=''; shipperLoading.style.display='none'; shipperList.style.display='block';
            if(!Array.isArray(data) || !data.length){
                shipperList.innerHTML=`<div class="text-center text-muted">Tidak ada kurir tersedia.</div>`;
                return;
            }
            data.forEach(ship=>{
                const harga = ship.price ?? 0;
                const div = document.createElement('div');
                div.classList.add('border','rounded','p-3','mb-2','pointer');
                div.innerHTML=`<div class="d-flex justify-content-between align-items-center">
                                <div><strong>${ship.name}</strong><br><small class="text-muted">Estimasi: ${ship.eta ?? '-'}</small></div>
                                <div class="fw-bold">Rp ${harga.toLocaleString('id-ID')}</div>
                               </div>`;
                div.onclick=function(){window.selectShipper(ship.name,harga,ship.id)};
                shipperList.appendChild(div);
            });
        })
        .catch(err=>{console.error(err); shipperList.innerHTML=`<div class="text-center text-danger">Gagal memuat kurir.</div>`; shipperLoading.style.display='none'; shipperList.style.display='block';});
    }
});

/* document.addEventListener("DOMContentLoaded", function () {

    const btnPilihKurir = document.getElementById('btnPilihKurir');
    const shipperList = document.getElementById('shipper-list');
    const shipperLoading = document.getElementById('shipper-loading');
    let destinationAddress = {{ $destinationAddress ?? 'null' }};
    const totalWeight = {{ $totalWeight }};

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
                origin: 5592,
                destination: destinationAddress,
                weight: totalWeight,
                price: "lowest"
            })
        })
        .then(res => res.json())
        .then(data => {

            shipperList.innerHTML = '';
            shipperLoading.style.display = 'none';
            shipperList.style.display = 'block';

            if (!Array.isArray(data) || !data.length) {
                shipperList.innerHTML = `<div class="text-center text-muted">Tidak ada kurir tersedia.</div>`;
                return;
            }

            data.forEach(ship => {
                const harga = ship.price ?? 0;

                const div = document.createElement('div');
                div.classList.add('border','rounded','p-3','mb-2','pointer');
                div.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${ship.name}</strong><br>
                            <small class="text-muted">Estimasi: ${ship.eta ?? '-'}</small>
                        </div>
                        <div class="fw-bold">Rp ${harga.toLocaleString('id-ID')}</div>
                    </div>
                `;

                div.onclick = function () {
                    selectShipper(ship.name, harga, ship.id);
                };

                shipperList.appendChild(div);
            });

        })
        .catch(err => {
            console.error(err);
            shipperList.innerHTML = `<div class="text-center text-danger">Gagal memuat kurir.</div>`;
            shipperLoading.style.display = 'none';
            shipperList.style.display = 'block';
        });
    });
});

// ========================
// FUNGSI PILIH KURIR
// ========================
function selectShipper(name, price, id) {

    // Ambil subtotal (bukan total!)
    let subtotalText = document.getElementById('subtotal_price').innerText;
    let subtotal = Number(subtotalText.replace(/[^0-9]/g, ""));

    // Update UI
    document.getElementById('selected-shipper').innerHTML =
        `${name} – Rp ${price.toLocaleString('id-ID')}`;

    document.getElementById('shipping_price').innerText =
        'Rp ' + price.toLocaleString('id-ID');

    document.getElementById('total_price').innerText =
        'Rp ' + (subtotal + Number(price)).toLocaleString('id-ID');

    // Set radio Delivery
    document.getElementById('radioDelivery').checked = true;

    // Hidden input untuk form
    let input = document.getElementById('selected_shipper_id');
    if (!input) {
        input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'shipper_id';
        input.id = 'selected_shipper_id';
        document.querySelector('form').appendChild(input);
    }
    input.value = id;

    // Tutup modal
    const modalEl = document.getElementById('shipperModal');
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();
} */

</script>
@endpush
