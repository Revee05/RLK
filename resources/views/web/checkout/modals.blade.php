{{-- ADDRESS MODAL --}}
<div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="addressModalLabel">Alamat Pengiriman</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        {{-- DAFTAR ALAMAT --}}
        <div id="address-list">
            @if($addresses->count())
                @foreach($addresses as $address)
                    <label class="d-flex justify-content-between align-items-start mb-3 p-3 border rounded pointer">
                        <div>
                            <div class="fw-bold">{{ $address->name }}</div>
                            <div class="text-muted small">
                                {{ $address->address }} <br>
                                {{ $address->provinsi->name ?? '' }},
                                {{ $address->kabupaten->name ?? '' }} <br>
                                {{ $address->phone }}
                            </div>
                        </div>
                        <input type="radio" name="selected_address" value="{{ $address->id }}" @if($loop->first) checked @endif>
                    </label>
                @endforeach
            @else
                <p class="text-muted">Kamu belum punya alamat.</p>
            @endif
        </div>

        <hr>

        {{-- TOMBOL TAMBAH ALAMAT --}}
        <div class="d-flex justify-content-center mb-3">
            <button type="button" class="btn btn-outline-dark" id="btnAddAddress">Tambah Alamat</button>
        </div>

        {{-- FORM TAMBAH ALAMAT --}}
        <div id="formAddAddress" style="display:none;">
            <h6 class="fw-bold mb-3">Tambah Alamat Baru</h6>

            <div class="mb-2">
                <input type="text" class="form-control" placeholder="Nama Penerima" id="new_name">
            </div>
            <div class="mb-2">
                <input type="text" class="form-control" placeholder="Nomor HP" id="new_phone">
            </div>
            <div class="mb-2">
                <input type="text" class="form-control" placeholder="Label Alamat" id="new_label">
            </div>

            {{-- PROVINSI --}}
            <div class="mb-2">
                <select class="form-control" id="provinsi">
                    <option value="">Pilih Provinsi</option>
                    @foreach($provinsi as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- KABUPATEN --}}
            <div class="mb-2">
                <select class="form-control" id="kabupaten" disabled>
                    <option value="">Pilih Kabupaten</option>
                </select>
            </div>

            {{-- KECAMATAN --}}
            <div class="mb-2">
                <select class="form-control" id="kecamatan" disabled>
                    <option value="">Pilih Kecamatan</option>
                </select>
            </div>

            {{-- DESA --}}
            <div class="mb-2">
                <select class="form-control" id="desa" disabled>
                    <option value="">Pilih Desa</option>
                </select>
            </div>

            <div class="mb-2">
                <textarea class="form-control" placeholder="Alamat Lengkap" rows="2" id="new_address"></textarea>
            </div>
            <div class="mb-2">
                <input type="text" class="form-control" placeholder="Catatan" id="new_note">
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" placeholder="Tambah Pinpoint (Opsional)" id="new_pinpoint">
            </div>

            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-dark" id="saveNewAddress">Simpan</button>
            </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

{{-- SCRIPT TAMBAH ALAMAT --}}
@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // Tampilkan / sembunyikan form tambah alamat
    const btnAdd = document.getElementById('btnAddAddress');
    const formAdd = document.getElementById('formAddAddress');

    btnAdd.addEventListener('click', () => {
        formAdd.style.display = formAdd.style.display === 'none' ? 'block' : 'none';
        btnAdd.innerText = formAdd.style.display === 'block' ? 'Batal' : 'Tambah Alamat';
    });

    // ============================
    //      AJAX PROV → KAB
    // ============================
    $('#provinsi').change(function() {
        let id = $(this).val();
        $('#kabupaten').prop('disabled', true).html('<option>Loading...</option>');

        $.get('/get-kabupaten/' + id, (res) => {
            $('#kabupaten').prop('disabled', false).html('<option value="">Pilih Kabupaten</option>');
            res.forEach(x => $('#kabupaten').append(`<option value="${x.id}">${x.name}</option>`));
        });
    });

    // KAB → KEC
    $('#kabupaten').change(function() {
        let id = $(this).val();
        $('#kecamatan').prop('disabled', true).html('<option>Loading...</option>');

        $.get('/get-kecamatan/' + id, (res) => {
            $('#kecamatan').prop('disabled', false).html('<option value="">Pilih Kecamatan</option>');
            res.forEach(x => $('#kecamatan').append(`<option value="${x.id}">${x.name}</option>`));
        });
    });

    // KEC → DESA
    $('#kecamatan').change(function() {
        let id = $(this).val();
        $('#desa').prop('disabled', true).html('<option>Loading...</option>');

        $.get('/get-desa/' + id, (res) => {
            $('#desa').prop('disabled', false).html('<option value="">Pilih Desa</option>');
            res.forEach(x => $('#desa').append(`<option value="${x.id}">${x.name}</option>`));
        });
    });

    // =============================================
    //      SIMPAN ALAMAT KE DATABASE (AJAX)
    // =============================================
    $('#saveNewAddress').click(function() {

        const data = {
            _token: "{{ csrf_token() }}",
            name: $('#new_name').val(),
            phone: $('#new_phone').val(),
            address: $('#new_address').val(),
            provinsi_id: $('#provinsi').val(),
            kabupaten_id: $('#kabupaten').val(),
            kecamatan_id: $('#kecamatan').val(),
            desa_id: $('#desa').val(),
            kodepos: 0,
            label_address: $('#new_label').val(),
        };

        if(!data.name || !data.phone || !data.address || !data.provinsi_id){
            alert('Harap lengkapi field wajib!');
            return;
        }

        $.post("{{ route('address.store') }}", data, function(res) {
            // Tambahkan ke daftar alamat di modal
            const addressList = document.getElementById('address-list');

            const labelEl = document.createElement('label');
            labelEl.className = 'd-flex justify-content-between align-items-start mb-3 p-3 border rounded pointer';
            labelEl.innerHTML = `
                <div>
                    <div class="fw-bold">${data.name}</div>
                    <div class="text-muted small">
                        ${data.address}<br>
                        ${$('#provinsi option:selected').text()}, 
                        ${$('#kabupaten option:selected').text()}<br>
                        ${data.phone}
                    </div>
                </div>
                <input type="radio" name="selected_address" value="${res.id}" checked>
            `;

            // Uncheck radio lama
            const radios = addressList.querySelectorAll('input[name="selected_address"]');
            radios.forEach(r => r.checked = false);

            addressList.appendChild(labelEl);

            // Reset form
            formAdd.style.display = 'none';
            btnAdd.innerText = 'Tambah Alamat';
            $('#formAddAddress input').val('');
            $('#provinsi').val('');
            $('#kabupaten').html('<option value="">Pilih Kabupaten</option>').prop('disabled', true);
            $('#kecamatan').html('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
            $('#desa').html('<option value="">Pilih Desa</option>').prop('disabled', true);

            alert('Alamat berhasil ditambahkan!');
        });
    });
</script>
@endsection
