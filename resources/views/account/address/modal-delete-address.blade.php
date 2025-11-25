<!-- Modal: Delete Alamat -->
<div class="modal fade" id="deleteAddressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">

            <form id="formDeleteAddress" method="POST" action="">
                @csrf
                @method('DELETE')

                <div class="modal-header">
                    <h5 class="modal-title">Hapus Alamat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p class="mb-0">Apakah Anda yakin ingin menghapus alamat ini?</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const delModalEl = document.getElementById('deleteAddressModal');
        if (!delModalEl) return;
        const form = document.getElementById('formDeleteAddress');

        delModalEl.addEventListener('show.bs.modal', function(e) {
            const url = delModalEl.dataset.deleteUrl || '';
            if (form && url) {
                form.action = url;
            }
        });

        // Clear action when hidden
        delModalEl.addEventListener('hidden.bs.modal', function(e) {
            if (form) form.action = '';
            delete delModalEl.dataset.deleteUrl;
        });
    });
</script>
